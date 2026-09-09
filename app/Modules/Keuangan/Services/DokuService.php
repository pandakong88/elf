<?php

namespace App\Modules\Keuangan\Services;

use App\Modules\Core\Models\LandingPageContent;
use App\Modules\Core\Models\Person;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DokuService
{
    /**
     * Dapatkan status apakah DOKU aktif atau tidak.
     */
    public static function isEnabled(): bool
    {
        $dbVal = LandingPageContent::where('key', 'doku_enabled')->value('value');
        if ($dbVal !== null) {
            return $dbVal === '1' || $dbVal === 'true';
        }
        return (bool) config('doku.is_enabled', true);
    }

    /**
     * Dapatkan Environment aktif ('sandbox' | 'production').
     */
    public function getEnvironment(): string
    {
        $dbVal = LandingPageContent::where('key', 'doku_environment')->value('value');
        return $dbVal ?: config('doku.environment', 'sandbox');
    }

    /**
     * Dapatkan Client ID DOKU.
     */
    public function getClientId(): string
    {
        $dbVal = LandingPageContent::where('key', 'doku_client_id')->value('value');
        return $dbVal ?: (string) config('doku.client_id', '');
    }

    /**
     * Dapatkan Secret Key DOKU.
     */
    public function getSecretKey(): string
    {
        $dbVal = LandingPageContent::where('key', 'doku_secret_key')->value('value');
        return $dbVal ?: (string) config('doku.secret_key', '');
    }

    /**
     * Dapatkan durasi kadaluarsa invoice (dalam menit).
     */
    public function getExpiryMinutes(): int
    {
        $dbVal = LandingPageContent::where('key', 'doku_expiry_minutes')->value('value');
        return $dbVal ? (int) $dbVal : (int) config('doku.expiry_minutes', 1440);
    }

    /**
     * Dapatkan Base URL API DOKU sesuai environment.
     */
    public function getBaseUrl(): string
    {
        $env = $this->getEnvironment();
        return config("doku.base_url.{$env}", 'https://api-sandbox.doku.com');
    }

    /**
     * Generate Header Signature untuk Request ke DOKU API V2.
     *
     * @param string $targetPath Contoh: '/checkout/v1/payment'
     * @param string $bodyJson
     * @param string $requestId
     * @param string $timestamp
     * @return array Array header DOKU lengkap
     */
    public function generateHeaders(string $targetPath, string $bodyJson = '', ?string $requestId = null, ?string $timestamp = null): array
    {
        $clientId  = $this->getClientId();
        $secretKey = $this->getSecretKey();
        $requestId = $requestId ?: (string) Str::uuid();
        $timestamp = $timestamp ?: gmdate('Y-m-d\TH:i:s\Z');

        $componentString = "Client-Id:" . $clientId . "\n"
                         . "Request-Id:" . $requestId . "\n"
                         . "Request-Timestamp:" . $timestamp . "\n"
                         . "Request-Target:" . $targetPath;

        if ($bodyJson !== '') {
            $digest = base64_encode(hash('sha256', $bodyJson, true));
            $componentString .= "\nDigest:" . $digest;
        }

        // 3. Signature HMAC-SHA256
        $signature = "HMACSHA256=" . base64_encode(hash_hmac('sha256', $componentString, $secretKey, true));

        return [
            'Client-Id'         => $clientId,
            'Request-Id'        => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature'         => $signature,
            'Content-Type'      => 'application/json',
        ];
    }

    /**
     * Verifikasi keaslian Signature webhook/notifikasi yang dikirim DOKU.
     *
     * @param string $clientId
     * @param string $requestId
     * @param string $timestamp
     * @param string $targetPath
     * @param string $rawBody
     * @param string $receivedSignature
     * @return bool
     */
    public function verifyNotificationSignature(
        string $clientId,
        string $requestId,
        string $timestamp,
        string $targetPath,
        string $rawBody,
        string $receivedSignature
    ): bool {
        $secretKey = $this->getSecretKey();

        // 1. Digest Body
        $digest = base64_encode(hash('sha256', $rawBody, true));

        // 2. Component String
        $componentString = "Client-Id:" . $clientId . "\n"
                         . "Request-Id:" . $requestId . "\n"
                         . "Request-Timestamp:" . $timestamp . "\n"
                         . "Request-Target:" . $targetPath . "\n"
                         . "Digest:" . $digest;

        // 3. Expected Signature
        $expectedSignature = "HMACSHA256=" . base64_encode(hash_hmac('sha256', $componentString, $secretKey, true));

        return hash_equals($expectedSignature, $receivedSignature);
    }

    /**
     * Dapatkan nominal Biaya Layanan / Admin Fee DOKU.
     */
    public function getAdminFee(): float
    {
        $dbVal = LandingPageContent::where('key', 'doku_admin_fee')->value('value');
        return $dbVal !== null ? (float) $dbVal : (float) config('doku.admin_fee', 3500);
    }

    /**
     * Buat Sesi Pembayaran DOKU Checkout Hosted Page.
     *
     * @param Bill[] $bills Koleksi Bill yang akan dibayar
     * @param string $personId UUID Santri
     * @param float $pocketMoney Titipan uang saku (opsional)
     * @param string|null $userId User yang memulai transaksi (opsional)
     * @param array $customAmounts Nominal cicilan per tagihan (opsional)
     * @return PaymentTransaction
     * @throws \Exception
     */
    public function createCheckoutSession(
        array $bills,
        string $personId,
        float $pocketMoney = 0,
        ?string $userId = null,
        array $customAmounts = []
    ): PaymentTransaction {
        $santri = Person::with('activeMadrasahEnrollment.kelas', 'activeRoomAssignment.room.dormitory')->findOrFail($personId);
        
        $invoiceNumber = 'DOKU-' . date('YmdHis') . '-' . strtoupper(Str::random(4));
        $expiryMinutes = $this->getExpiryMinutes();
        
        $lineItems = [];
        $totalBillAmount = 0.0;
        $billBreakdown = [];
        $billIds = [];

        foreach ($bills as $bill) {
            $maxRemaining = max(0, (float)($bill->amount ?? 0) - (float)($bill->amount_paid ?? 0));
            $customVal = $customAmounts[$bill->id] ?? null;
            $amount = (isset($customVal) && is_numeric($customVal) && (float)$customVal > 0)
                ? min($maxRemaining, (float)$customVal)
                : $maxRemaining;

            if ($amount <= 0) continue;

            $totalBillAmount += $amount;
            $billIds[] = $bill->id;

            $label = $bill->title ?: ($bill->config?->label ?? 'Tagihan');
            $period = $bill->period_label ?: '-';

            $lineItems[] = [
                'name'     => Str::limit($label . ' (' . $period . ')', 50),
                'price'    => (int) $amount,
                'quantity' => 1,
            ];

            $billBreakdown[] = [
                'bill_id'      => $bill->id,
                'config_label' => $label,
                'period_label' => $period,
                'amount'       => $amount,
                'bill_type'    => $bill->bill_type ?? 'syahriah',
            ];
        }

        if ($pocketMoney > 0) {
            $lineItems[] = [
                'name'     => 'Titipan Uang Saku Santri',
                'price'    => (int) $pocketMoney,
                'quantity' => 1,
            ];
        }

        $baseTotal = $totalBillAmount + $pocketMoney;

        if ($baseTotal <= 0) {
            throw new \Exception('Total pembayaran harus lebih besar dari 0.');
        }

        // Biaya Layanan Gateway Flat
        $mdrFee = $this->getAdminFee();

        if ($mdrFee > 0) {
            $lineItems[] = [
                'name'     => 'Biaya Layanan Gateway',
                'price'    => (int) $mdrFee,
                'quantity' => 1,
            ];
        }

        $grandTotal = $baseTotal + $mdrFee;

        $notificationUrl = config('doku.notification_url') ?: route('doku.notification');
        $returnUrl = config('doku.return_url') ?: route('portal-wali.dashboard', ['personId' => $personId]);

        // Resolve customer phone (DOKU requires calling code format: 628xxxxxxxxxx)
        $rawPhone = $santri->phone ?? $santri->guardian_phone ?? '081234567890';
        $cleanedPhone = preg_replace('/[^0-9]/', '', (string)$rawPhone);
        if (str_starts_with($cleanedPhone, '0')) {
            $formattedPhone = '62' . substr($cleanedPhone, 1);
        } elseif (str_starts_with($cleanedPhone, '62')) {
            $formattedPhone = $cleanedPhone;
        } else {
            $formattedPhone = '6281234567890';
        }
        $formattedPhone = substr($formattedPhone, 0, 16);

        // Resolve customer email (must have valid standard TLD)
        $email = $santri->email ?: ('santri_' . ($santri->nis ?: '00') . '@pesantren.sch.id');

        $paymentTypes = config('doku.allowed_payment_types', []);
        $paymentPayload = [
            'payment_due_date' => $expiryMinutes,
        ];
        if (!empty($paymentTypes)) {
            $paymentPayload['payment_method_types'] = $paymentTypes;
        }

        $payload = [
            'order' => [
                'invoice_number' => $invoiceNumber,
                'amount'         => (int) $grandTotal,
                'line_items'     => $lineItems,
                'callback_url'   => $returnUrl,
                'auto_redirect'  => true,
            ],
            'payment' => $paymentPayload,
            'customer' => [
                'id'    => (string) $santri->id,
                'name'  => Str::limit($santri->name, 50),
                'email' => $email,
                'phone' => $formattedPhone,
            ],
            'additional_info' => [
                'person_id'           => $personId,
                'pocket_money_amount' => $pocketMoney,
                'bills_count'         => count($billIds),
                'mdr_fee'             => $mdrFee,
            ],
        ];

        $targetPath = '/checkout/v1/payment';
        $jsonPayload = json_encode($payload);
        $headers = $this->generateHeaders($targetPath, $jsonPayload);
        $apiUrl = rtrim($this->getBaseUrl(), '/') . $targetPath;

        Log::info('[DokuService] Request Checkout', [
            'invoice_number' => $invoiceNumber,
            'api_url'        => $apiUrl,
            'base_total'     => $baseTotal,
            'mdr_fee'        => $mdrFee,
            'grand_total'    => $grandTotal,
        ]);

        $response = Http::withHeaders($headers)
            ->timeout(20)
            ->post($apiUrl, $payload);

        if (!$response->successful()) {
            Log::error('[DokuService] API Error Response', [
                'status' => $response->status(),
                'body'   => $response->json() ?? $response->body(),
            ]);
            $errMessage = $response->json('error.message') ?? $response->json('message') ?? 'Gagal menghubungi server DOKU (' . $response->status() . ').';
            throw new \Exception('DOKU Gateway: ' . $errMessage);
        }

        $resData = $response->json();
        $paymentUrl = $resData['response']['payment']['url'] ?? null;

        if (!$paymentUrl) {
            throw new \Exception('DOKU Gateway tidak mengembalikan URL pembayaran.');
        }

        // Simpan ke PaymentTransaction
        $transaction = PaymentTransaction::create([
            'id'                   => (string) Str::uuid(),
            'merchant_order_id'    => $invoiceNumber,
            'person_id'            => $personId,
            'user_id'              => $userId,
            'payment_channel'      => 'DOKU_CHECKOUT',
            'channel_label'        => 'DOKU Checkout (Hosted Page)',
            'bill_amount'          => $totalBillAmount,
            'mdr_amount'           => $mdrFee,
            'total_amount'         => $grandTotal,
            'net_amount'           => $baseTotal,
            'bill_ids'             => $billIds,
            'bill_breakdown'       => $billBreakdown,
            'pocket_money_amount'  => $pocketMoney,
            'payment_url'          => $paymentUrl,
            'gateway_provider'     => 'doku',
            'status'               => 'pending',
            'expires_at'           => now()->addMinutes($expiryMinutes),
            'raw_response'         => $resData,
        ]);

        return $transaction;
    }

    /**
     * Uji koneksi ke DOKU API untuk memastikan Client ID & Secret Key valid.
     *
     * @return array ['success' => bool, 'message' => string, 'latency_ms' => int]
     */
    public function testConnection(): array
    {
        $startTime = microtime(true);
        $targetPath = '/checkout/v1/payment';
        
        $testPayload = [
            'order' => [
                'invoice_number' => 'TEST-' . time(),
                'amount'         => 10000,
                'line_items'     => [
                    [
                        'name'     => 'Uji Coba Koneksi DOKU',
                        'price'    => 10000,
                        'quantity' => 1,
                    ]
                ],
            ],
            'payment' => [
                'payment_due_date' => 60,
            ],
            'customer' => [
                'id'    => 'TEST-001',
                'name'  => 'Developer Test',
                'email' => 'dev@example.com',
            ],
        ];

        $jsonPayload = json_encode($testPayload);
        $headers = $this->generateHeaders($targetPath, $jsonPayload);
        $apiUrl = rtrim($this->getBaseUrl(), '/') . $targetPath;

        try {
            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->post($apiUrl, $testPayload);

            $latency = (int) round((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                return [
                    'success'    => true,
                    'message'    => 'Koneksi DOKU API Berhasil! (' . $this->getEnvironment() . ') Response OK dalam ' . $latency . 'ms.',
                    'latency_ms' => $latency,
                ];
            }

            $errMsg = $response->json('error.message') ?? $response->json('message') ?? 'HTTP Status ' . $response->status();
            return [
                'success'    => false,
                'message'    => 'Gagal (' . $response->status() . '): ' . $errMsg,
                'latency_ms' => $latency,
            ];
        } catch (\Exception $e) {
            $latency = (int) round((microtime(true) - $startTime) * 1000);
            return [
                'success'    => false,
                'message'    => 'Error Exception: ' . $e->getMessage(),
                'latency_ms' => $latency,
            ];
        }
    }

    /**
     * Cek status pesanan/transaksi langsung ke DOKU API.
     *
     * @param string $invoiceNumber
     * @return array
     */
    public function checkOrderStatus(string $invoiceNumber): array
    {
        $targetPath = "/orders/v1/status/{$invoiceNumber}";
        $headers = $this->generateHeaders($targetPath, '');
        $apiUrl = rtrim($this->getBaseUrl(), '/') . $targetPath;

        try {
            $response = Http::withHeaders($headers)
                ->timeout(15)
                ->get($apiUrl);

            $resData = $response->json();
            Log::info('[DokuService] checkOrderStatus', [
                'invoice_number' => $invoiceNumber,
                'status_code'    => $response->status(),
                'response'       => $resData,
            ]);

            return $resData ?? [];
        } catch (\Exception $e) {
            Log::error('[DokuService] checkOrderStatus exception', [
                'invoice_number' => $invoiceNumber,
                'error'          => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Proses pelunasan pembayaran sukses DOKU secara tuntas & idempotent.
     */
    public function handleSuccessfulPayment(PaymentTransaction $trx, array $payload = []): void
    {
        if ($trx->status !== 'success') {
            $trx->update([
                'status'               => 'success',
                'callback_received_at' => $trx->callback_received_at ?: now(),
            ]);
        }

        $now = now();
        $receiptNo = \App\Modules\Keuangan\Models\BillPayment::generateReceiptNo();
        $paymentGroupId = (string) Str::uuid();

        // Tentukan user sistem pencatat
        $loggedBy = $trx->user_id;
        if (!$loggedBy || !\App\Models\User::where('id', $loggedBy)->exists()) {
            $loggedBy = \App\Models\User::whereHas('roles', function($q) {
                $q->whereIn('name', ['super-admin', 'admin', 'bendahara-putra', 'bendahara-putri']);
            })->value('id') ?? \App\Models\User::value('id');
        }

        // Proses Pelunasan Tagihan (Bills)
        $breakdown = $trx->bill_breakdown ?? [];
        if (empty($breakdown) && !empty($trx->bill_ids)) {
            foreach ($trx->bill_ids as $bId) {
                $b = Bill::find($bId);
                if ($b) {
                    $breakdown[] = [
                        'bill_id'      => $b->id,
                        'config_label' => $b->title ?: 'Tagihan',
                        'amount'       => (float) ($b->remaining_amount ?? $b->amount),
                    ];
                }
            }
        }

        foreach ($breakdown as $item) {
            if (empty($item['bill_id'])) continue;
            $bill = Bill::find($item['bill_id']);
            if (!$bill) continue;

            $amountPaid = (float) ($item['amount'] ?? $bill->remaining_amount ?? 0);
            if ($amountPaid <= 0) continue;

            $existingPayment = \App\Modules\Keuangan\Models\BillPayment::where('bill_id', $bill->id)
                ->where('notes', 'like', "%{$trx->merchant_order_id}%")
                ->first();

            if (!$existingPayment) {
                \App\Modules\Keuangan\Models\BillPayment::create([
                    'bill_id'          => $bill->id,
                    'receipt_no'       => $receiptNo,
                    'payment_group_id' => $paymentGroupId,
                    'amount_paid'      => $amountPaid,
                    'payment_method'   => 'gateway_duitku',
                    'payment_date'     => $now,
                    'logged_by'        => $loggedBy,
                    'notes'            => 'Pembayaran DOKU Gateway [' . $trx->merchant_order_id . ' · ' . ($trx->payment_channel ?: 'DOKU') . ']',
                ]);

                $bill->recalculateStatus();
            }
        }

        // Titipan Uang Saku Santri (jika ada)
        if ($trx->pocket_money_amount > 0) {
            $existingDeposit = \App\Modules\Keuangan\Models\PocketMoneyDeposit::where('reference_id', $trx->id)->first();
            if (!$existingDeposit) {
                \App\Modules\Keuangan\Models\PocketMoneyDeposit::create([
                    'person_id'    => $trx->person_id,
                    'amount'       => $trx->pocket_money_amount,
                    'source'       => 'gateway_duitku',
                    'reference_id' => $trx->id,
                    'status'       => 'received',
                    'notes'        => 'Titipan uang saku via DOKU [' . $trx->merchant_order_id . ']',
                    'received_at'  => $now,
                    'received_by'  => $loggedBy,
                ]);
            }
        }

        // Kirim WhatsApp jika service aktif
        try {
            if (class_exists(\App\Services\WhatsAppService::class) && $trx->person) {
                $wa = app(\App\Services\WhatsAppService::class);
                if (method_exists($wa, 'sendPaymentSuccessReceipt')) {
                    $wa->sendPaymentSuccessReceipt($trx, $receiptNo);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[DokuService] WhatsApp receipt notification failed: ' . $e->getMessage());
        }
    }
}
