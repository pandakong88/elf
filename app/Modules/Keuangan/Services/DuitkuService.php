<?php

namespace App\Modules\Keuangan\Services;

use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillPayment;
use App\Modules\Keuangan\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DuitkuService
{
    protected string $merchantCode;
    protected string $apiKey;
    protected string $baseUrl;
    protected int $expiryMinutes;
    protected string $callbackUrl;
    protected string $returnUrl;

    public function __construct()
    {
        $this->merchantCode  = config('duitku.merchant_code');
        $this->apiKey        = config('duitku.api_key');
        $this->expiryMinutes = config('duitku.expiry_minutes', 1440);
        $this->callbackUrl   = config('duitku.callback_url');
        $this->returnUrl     = config('duitku.return_url');

        $env = config('duitku.env', 'sandbox');
        $this->baseUrl = config("duitku.base_url.{$env}");
    }

    // ─── Public API Methods ───────────────────────────────────────────────────

    /**
     * Ambil daftar channel pembayaran yang aktif beserta MDR-nya.
     * Sumber dari config/duitku.php (tidak hit API Duitku).
     */
    public function getEnabledChannels(): array
    {
        return config('duitku.enabled_channels', []);
    }

    /**
     * Hitung MDR untuk nominal tertentu dan channel tertentu.
     *
     * @return array ['bill_amount' => float, 'mdr_amount' => float, 'total_amount' => float]
     */
    public function calculateMdr(float $billAmount, string $channel): array
    {
        $channels = $this->getEnabledChannels();

        if (!isset($channels[$channel])) {
            return [
                'bill_amount'  => $billAmount,
                'mdr_rate'     => 0,
                'mdr_fixed'    => 0,
                'mdr_amount'   => 0,
                'total_amount' => $billAmount,
                'net_amount'   => $billAmount,
            ];
        }

        $cfg       = $channels[$channel];
        $mdrRate   = (float) ($cfg['mdr_rate'] ?? 0);
        $mdrFixed  = (float) ($cfg['mdr_fixed'] ?? 0);

        // MDR ditanggung customer: total = bill + mdr
        // Rumus: total = bill / (1 - rate) + fixed   → agar net = bill
        // Tapi karena sederhananya: mdr = bill * rate + fixed
        $mdrAmount   = round($billAmount * $mdrRate + $mdrFixed, 2);
        $totalAmount = round($billAmount + $mdrAmount, 2);
        $netAmount   = round($totalAmount - $mdrAmount, 2); // = billAmount

        return [
            'bill_amount'  => $billAmount,
            'mdr_rate'     => $mdrRate,
            'mdr_fixed'    => $mdrFixed,
            'mdr_amount'   => $mdrAmount,
            'total_amount' => $totalAmount,
            'net_amount'   => $netAmount,
        ];
    }

    /**
     * Buat transaksi baru ke Duitku.
     *
     * @param  Bill[]  $bills          Koleksi Bill yang akan dibayar
     * @param  string  $channel        Kode channel (SP, BR, BT, I1, M2, ...)
     * @param  string  $personId       UUID santri
     * @param  string|null $userId     UUID user yang memulai (null = portal wali)
     * @param  array   $customAmounts  Nominal custom per bill [bill_id => amount] (untuk cicilan)
     * @return PaymentTransaction
     * @throws \Exception
     */
    public function createTransaction(
        array $bills,
        string $channel,
        string $personId,
        ?string $userId = null,
        array $customAmounts = []
    ): PaymentTransaction {
        // 1. Hitung total tagihan (dukung nominal custom / parsial)
        $billAmount = collect($bills)->sum(function($b) use ($customAmounts) {
            $maxRemaining = max(0, (float) $b->amount - (float) $b->amount_paid);
            if (isset($customAmounts[$b->id]) && is_numeric($customAmounts[$b->id]) && (float)$customAmounts[$b->id] > 0) {
                return min($maxRemaining, (float)$customAmounts[$b->id]);
            }
            return $maxRemaining;
        });

        $mdrData = $this->calculateMdr($billAmount, $channel);

        // 2. Buat bill_breakdown (alokasi MDR per bill secara proporsional)
        $breakdown = $this->buildBillBreakdown($bills, $mdrData, $customAmounts);

        // 3. Generate merchant order ID yang unik
        $merchantOrderId = 'ELF-' . now()->format('ymdHis') . '-' . strtoupper(Str::random(6));

        // 4. Ambil info santri untuk detail produk
        $person        = $bills[0]->person ?? null;
        $santriName    = $person?->name ?? 'Santri';
        $billCount     = count($bills);
        $productDetail = Str::limit("Tagihan {$santriName} ({$billCount} item)", 240, '...');
        $customerName  = Str::limit(preg_replace('/[^a-zA-Z0-9\s]/', '', (string)$santriName), 30, '');
        if (empty($customerName)) {
            $customerName = 'Wali Santri';
        }
        $customerEmail = $person?->user?->email ?? 'wali@elvith.id';

        // 5. Signature: MD5(merchantCode + merchantOrderId + amount + apiKey)
        $amount    = (int) $mdrData['total_amount'];
        $signature = md5($this->merchantCode . $merchantOrderId . $amount . $this->apiKey);

        // 6. Payload ke Duitku
        $payload = [
            'merchantCode'   => $this->merchantCode,
            'paymentAmount'  => $amount,
            'paymentMethod'  => $channel,
            'merchantOrderId'=> $merchantOrderId,
            'productDetails' => $productDetail,
            'email'          => $customerEmail,
            'customerVaName' => $customerName,
            'callbackUrl'    => $this->callbackUrl,
            'returnUrl'      => $this->returnUrl . '?orderId=' . $merchantOrderId,
            'expiryPeriod'   => $this->expiryMinutes,
            'signature'      => $signature,
        ];

        // 7. Simpan record dulu dengan status pending
        $transaction = PaymentTransaction::create([
            'merchant_order_id' => $merchantOrderId,
            'duitku_reference'  => null,
            'person_id'         => $personId,
            'bill_ids'          => collect($bills)->pluck('id')->toArray(),
            'bill_breakdown'    => $breakdown,
            'bill_amount'       => $mdrData['bill_amount'],
            'mdr_channel'       => $channel,
            'mdr_rate'          => $mdrData['mdr_rate'],
            'mdr_fixed'         => $mdrData['mdr_fixed'],
            'mdr_amount'        => $mdrData['mdr_amount'],
            'total_amount'      => $mdrData['total_amount'],
            'net_amount'        => $mdrData['net_amount'],
            'payment_channel'   => $channel,
            'status'            => 'pending',
            'expires_at'        => now()->addMinutes($this->expiryMinutes),
            'initiated_by'      => $userId,
        ]);

        // 8. Hit API Duitku
        try {
            $response = Http::timeout(30)
                ->post("{$this->baseUrl}/merchant/v2/inquiry", $payload);

            $responseData = $response->json();

            Log::info('[Duitku] createTransaction response', [
                'merchant_order_id' => $merchantOrderId,
                'status_code'       => $response->status(),
                'response'          => $responseData,
            ]);

            if ($response->successful() && isset($responseData['paymentUrl'])) {
                $transaction->update([
                    'duitku_reference'   => $responseData['reference'] ?? null,
                    'payment_url'        => $responseData['paymentUrl'],
                    'va_number'          => $responseData['vaNumber'] ?? null,
                    'qr_string'          => $responseData['qrString'] ?? null,
                    'raw_duitku_response'=> $responseData,
                ]);

                return $transaction->refresh();
            }

            // Jika gagal dari Duitku
            $errorMessage = $responseData['Message'] ?? $responseData['message'] ?? 'Unknown error from Duitku';
            $transaction->update([
                'status'             => 'failed',
                'failure_reason'     => $errorMessage,
                'raw_duitku_response'=> $responseData,
            ]);

            throw new \Exception("Duitku createTransaction failed: {$errorMessage}");

        } catch (\Exception $e) {
            Log::error('[Duitku] createTransaction exception', [
                'merchant_order_id' => $merchantOrderId,
                'error'             => $e->getMessage(),
            ]);

            if ($transaction->status === 'pending') {
                $transaction->update([
                    'status'         => 'failed',
                    'failure_reason' => $e->getMessage(),
                ]);
            }

            throw $e;
        }
    }

    /**
     * Cek status transaksi ke Duitku (untuk polling / rekonsiliasi).
     *
     * @return array ['status' => 'SUCCESS'|'PENDING'|'CANCELLED', ...]
     */
    public function checkTransactionStatus(string $merchantOrderId): array
    {
        $signature = md5($this->merchantCode . $merchantOrderId . $this->apiKey);

        $payload = [
            'merchantCode'    => $this->merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'signature'       => $signature,
        ];

        try {
            $response     = Http::timeout(15)->post("{$this->baseUrl}/merchant/transactionStatus", $payload);
            $responseData = $response->json();

            Log::info('[Duitku] checkTransactionStatus', [
                'merchant_order_id' => $merchantOrderId,
                'response'          => $responseData,
            ]);

            return $responseData ?? [];

        } catch (\Exception $e) {
            Log::error('[Duitku] checkTransactionStatus exception', [
                'merchant_order_id' => $merchantOrderId,
                'error'             => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Verifikasi signature dari callback Duitku.
     * Signature Duitku: MD5(merchantCode + amount + merchantOrderId + apiKey)
     *
     * @param  array  $payload  Data POST dari Duitku callback
     * @return bool
     */
    public function verifyCallback(array $payload): bool
    {
        if (empty($payload['merchantCode']) || empty($payload['amount']) ||
            empty($payload['merchantOrderId']) || empty($payload['signature'])) {
            Log::warning('[Duitku] verifyCallback: Missing required fields', $payload);
            return false;
        }

        $expectedSignature = md5(
            $payload['merchantCode'] .
            $payload['amount'] .
            $payload['merchantOrderId'] .
            $this->apiKey
        );

        $isValid = hash_equals($expectedSignature, $payload['signature']);

        if (!$isValid) {
            Log::warning('[Duitku] verifyCallback: Signature mismatch', [
                'expected'  => $expectedSignature,
                'received'  => $payload['signature'],
                'order_id'  => $payload['merchantOrderId'],
            ]);
        }

        return $isValid;
    }

    /**
     * Proses callback dari Duitku yang sudah terverifikasi.
     * Method ini idempotent — aman dipanggil berkali-kali untuk order yang sama.
     *
     * @param  array  $payload  Data POST dari Duitku (sudah diverifikasi)
     */
    public function processCallback(array $payload): void
    {
        $merchantOrderId = $payload['merchantOrderId'];
        $resultCode      = $payload['resultCode'] ?? null; // '00' = sukses

        $transaction = PaymentTransaction::where('merchant_order_id', $merchantOrderId)->first();

        if (!$transaction) {
            Log::error('[Duitku] processCallback: Transaction not found', ['order_id' => $merchantOrderId]);
            return;
        }

        // Idempotency check: jika sudah diproses dan BillPayment sudah ada, skip
        if ($transaction->status === 'success') {
            // Pastikan BillPayment benar-benar sudah ada
            $this->handleSuccessfulPayment($transaction, $payload);
            return;
        }

        // Simpan raw payload callback
        $transaction->update([
            'raw_callback_payload'  => $payload,
            'callback_received_at'  => now(),
            'duitku_reference'      => $payload['reference'] ?? $transaction->duitku_reference,
        ]);

        // Proses berdasarkan result code
        if ($resultCode === '00') {
            // ✅ SUKSES — buat BillPayment records
            $this->handleSuccessfulPayment($transaction, $payload);
        } else {
            // ❌ GAGAL / DIBATALKAN
            $transaction->update([
                'status'         => 'failed',
                'failure_reason' => "Duitku resultCode: {$resultCode}",
            ]);

            Log::warning('[Duitku] processCallback: Payment failed', [
                'order_id'    => $merchantOrderId,
                'result_code' => $resultCode,
            ]);
        }
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────

    /**
     * Re-sync semua transaksi berstatus success yang mungkin belum tercatat di bill_payments.
     */
    public function resyncAllSuccessfulTransactions(): int
    {
        $transactions = PaymentTransaction::where('status', 'success')->get();
        $count = 0;

        foreach ($transactions as $transaction) {
            $this->handleSuccessfulPayment($transaction, $transaction->raw_callback_payload ?? []);
            $count++;
        }

        return $count;
    }

    /**
     * Tangani pembayaran sukses: update transaksi + buat BillPayment records.
     */
    public function handleSuccessfulPayment(PaymentTransaction $transaction, array $payload = []): void
    {
        // Update status transaksi
        if ($transaction->status !== 'success') {
            $transaction->update(['status' => 'success']);
        }

        // Ekstrak timestamp aktual pembayaran dari payload Duitku
        // Duitku mengirim 'publishedDate' atau 'settledDate' di beberapa format
        $paidAt = $payload['publishedDate']
            ?? $payload['settledDate']
            ?? $payload['transactionDate']
            ?? $transaction->callback_received_at?->toDateTimeString()
            ?? null;

        // Buat BillPayment untuk setiap bill dalam transaksi
        $billingService = app(BillingService::class);
        $breakdown      = $transaction->bill_breakdown ?? [];

        foreach ($breakdown as $item) {
            $billId    = $item['bill_id'];
            $netAmount = (float) ($item['net_amount'] ?? $item['pay_portion'] ?? $item['bill_remaining'] ?? 0);

            if ($netAmount <= 0) continue;

            try {
                $billingService->recordGatewayPayment(
                    billId:        $billId,
                    amount:        $netAmount,
                    transactionId: $transaction->id,
                    paidAt:        $paidAt,
                );
            } catch (\Exception $e) {
                Log::error('[Duitku] handleSuccessfulPayment: Failed to record payment for bill', [
                    'bill_id'        => $billId,
                    'transaction_id' => $transaction->id,
                    'error'          => $e->getMessage(),
                ]);
            }
        }

        Log::info('[Duitku] Payment successfully processed', [
            'merchant_order_id' => $transaction->merchant_order_id,
            'total_amount'      => $transaction->total_amount,
            'bill_count'        => count($breakdown),
            'paid_at'           => $paidAt,
        ]);
    }


    /**
     * Bangun bill_breakdown: alokasi MDR per bill secara proporsional.
     *
     * @param  Bill[]  $bills
     * @param  array   $mdrData
     * @param  array   $customAmounts
     * @return array
     */
    private function buildBillBreakdown(array $bills, array $mdrData, array $customAmounts = []): array
    {
        $totalBillAmount = $mdrData['bill_amount'];
        $totalMdr        = $mdrData['mdr_amount'];
        $breakdown       = [];

        $months = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                   7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];

        foreach ($bills as $bill) {
            $maxRemaining = max(0, (float) $bill->amount - (float) $bill->amount_paid);
            $payPortion   = isset($customAmounts[$bill->id]) && is_numeric($customAmounts[$bill->id]) && (float)$customAmounts[$bill->id] > 0
                ? min($maxRemaining, (float)$customAmounts[$bill->id])
                : $maxRemaining;

            // Alokasi MDR proporsional terhadap nominal yang dibayarkan
            $mdrPortion = $totalBillAmount > 0
                ? round(($payPortion / $totalBillAmount) * $totalMdr, 2)
                : 0;

            // --- Build human-readable label & period ---
            $configLabel = $bill->config?->label ?? ucwords(str_replace('_', ' ', $bill->bill_type ?? ''));
            $interval    = $bill->config?->interval ?? '';
            if ($interval === 'semester') {
                $periodLabel = 'Semester ' . $bill->period_month . '/' . $bill->period_year;
            } elseif (in_array($interval, ['once', 'insidental', 'event', 'sekali'])) {
                $periodLabel = 'Event ' . $bill->period_year;
            } else {
                $periodLabel = ($months[$bill->period_month] ?? $bill->period_month) . ' ' . $bill->period_year
                    . ($bill->period_sub ? ' (Gel.' . $bill->period_sub . ')' : '');
            }

            $breakdown[] = [
                'bill_id'        => $bill->id,
                'bill_type'      => $bill->bill_type,
                'config_label'   => $configLabel,
                'period_label'   => $periodLabel,
                'bill_remaining' => $maxRemaining,
                'pay_portion'    => $payPortion,
                'mdr_portion'    => $mdrPortion,
                'net_amount'     => $payPortion,
                'total_charged'  => $payPortion + $mdrPortion,
                'is_partial'     => ($payPortion < $maxRemaining),
            ];
        }

        return $breakdown;
    }
}

