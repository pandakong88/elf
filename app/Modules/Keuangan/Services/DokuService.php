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
    public function generateHeaders(string $targetPath, string $bodyJson, ?string $requestId = null, ?string $timestamp = null): array
    {
        $clientId  = $this->getClientId();
        $secretKey = $this->getSecretKey();
        $requestId = $requestId ?: (string) Str::uuid();
        $timestamp = $timestamp ?: gmdate('Y-m-d\TH:i:s\Z');

        // 1. Digest Body
        $digest = base64_encode(hash('sha256', $bodyJson, true));

        // 2. Component String
        $componentString = "Client-Id:" . $clientId . "\n"
                         . "Request-Id:" . $requestId . "\n"
                         . "Request-Timestamp:" . $timestamp . "\n"
                         . "Request-Target:" . $targetPath . "\n"
                         . "Digest:" . $digest;

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
     * Buat Sesi Pembayaran DOKU Checkout Hosted Page.
     *
     * @param Bill[] $bills Koleksi Bill yang akan dibayar
     * @param string $personId UUID Santri
     * @param float $pocketMoney Titipan uang saku (opsional)
     * @param string|null $userId User yang memulai transaksi (opsional)
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

        $grandTotal = $totalBillAmount + $pocketMoney;

        if ($grandTotal <= 0) {
            throw new \Exception('Total pembayaran harus lebih besar dari 0.');
        }

        $notificationUrl = config('doku.notification_url') ?: route('doku.notification');
        $returnUrl = config('doku.return_url') ?: route('portal-wali.dashboard', ['personId' => $personId]);

        $payload = [
            'order' => [
                'invoice_number' => $invoiceNumber,
                'amount'         => (int) $grandTotal,
                'line_items'     => $lineItems,
                'callback_url'   => $returnUrl,
                'auto_redirect'  => true,
            ],
            'payment' => [
                'payment_due_date' => $expiryMinutes,
            ],
            'customer' => [
                'id'    => (string) $santri->id,
                'name'  => Str::limit($santri->name, 50),
                'email' => 'santri_' . ($santri->nis ?: '00') . '@pesantren.local',
                'phone' => '08123456789',
            ],
            'additional_info' => [
                'person_id'           => $personId,
                'pocket_money_amount' => $pocketMoney,
                'bills_count'         => count($billIds),
            ],
        ];

        $targetPath = '/checkout/v1/payment';
        $jsonPayload = json_encode($payload);
        $headers = $this->generateHeaders($targetPath, $jsonPayload);
        $apiUrl = rtrim($this->getBaseUrl(), '/') . $targetPath;

        Log::info('[DokuService] Request Checkout', [
            'invoice_number' => $invoiceNumber,
            'api_url'        => $apiUrl,
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
            'mdr_amount'           => 0,
            'total_amount'         => $grandTotal,
            'net_amount'           => $grandTotal,
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
}
