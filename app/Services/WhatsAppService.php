<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private bool   $enabled;
    private string $token;
    private string $target;
    private string $apiUrl;

    public function __construct()
    {
        $this->enabled = (bool) config('whatsapp.enabled', false);
        $this->token   = config('whatsapp.token', '');
        $this->target  = config('whatsapp.target', '');
        $this->apiUrl  = config('whatsapp.api_url', 'https://api.fonnte.com/send');
    }

    /**
     * Kirim notifikasi ke grup WhatsApp admin/bendahara.
     */
    public function sendToGroup(string $message): bool
    {
        if (!$this->enabled) {
            Log::info('[WhatsApp] Notifikasi dinonaktifkan (FONNTE_ENABLED=false)');
            return false;
        }

        if (empty($this->token) || empty($this->target)) {
            Log::warning('[WhatsApp] Token atau target grup belum dikonfigurasi di .env');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, [
                'target'  => $this->target,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $body = $response->json();
                if ($body['status'] ?? false) {
                    Log::info('[WhatsApp] Notifikasi terkirim ke grup', ['target' => $this->target]);
                    return true;
                }
                Log::warning('[WhatsApp] Fonnte gagal', ['response' => $body]);
                return false;
            }

            Log::warning('[WhatsApp] HTTP error', ['status' => $response->status(), 'body' => $response->body()]);
            return false;
        } catch (\Throwable $e) {
            Log::error('[WhatsApp] Exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Kirim notifikasi ke nomor WhatsApp tertentu (pribadi/wali santri).
     */
    public function sendToNumber(string $phone, string $message): bool
    {
        if (!$this->enabled) {
            Log::info('[WhatsApp] Notifikasi dinonaktifkan (FONNTE_ENABLED=false)');
            return false;
        }

        if (empty($this->token)) {
            Log::warning('[WhatsApp] Token WhatsApp belum dikonfigurasi di .env');
            return false;
        }

        $formattedTarget = $this->formatPhoneNumber($phone);
        if (empty($formattedTarget)) {
            Log::warning('[WhatsApp] Nomor tujuan tidak valid: ' . $phone);
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, [
                'target'  => $formattedTarget,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $body = $response->json();
                if ($body['status'] ?? false) {
                    Log::info('[WhatsApp] Notifikasi terkirim ke nomor pribadi', ['target' => $formattedTarget]);
                    return true;
                }
                Log::warning('[WhatsApp] Fonnte gagal kirim ke nomor', ['target' => $formattedTarget, 'response' => $body]);
                return false;
            }

            Log::warning('[WhatsApp] HTTP error kirim ke nomor', ['status' => $response->status(), 'body' => $response->body()]);
            return false;
        } catch (\Throwable $e) {
            Log::error('[WhatsApp] Exception kirim ke nomor', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Format nomor HP menjadi standar internasional Indonesia (628xxx).
     */
    public function formatPhoneNumber(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        if (empty($cleaned)) {
            return '';
        }

        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62' . substr($cleaned, 1);
        } elseif (str_starts_with($cleaned, '8')) {
            $cleaned = '62' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Kirim kuitansi pelunasan digital ke WhatsApp Wali Santri.
     */
    public function notifyWaliPaymentReceipt(
        string $phone,
        string $santriName,
        string $orderId,
        string $channelLabel,
        string $paidAt,
        float  $totalAmount,
        array  $breakdown = []
    ): bool {
        if (!config('whatsapp.notify_wali', true)) {
            return false;
        }

        $appName = config('app.name', 'Pondok Pesantren Al-Fithroh');
        $rincian = '';

        foreach ($breakdown as $item) {
            $label   = $item['config_label'] ?? ucwords(str_replace('_', ' ', $item['bill_type'] ?? ''));
            $period  = $item['period_label'] ?? '';
            $amount  = number_format($item['pay_portion'] ?? $item['net_amount'] ?? 0, 0, ',', '.');
            $rincian .= "• {$label} ({$period}) : Rp {$amount}\n";
        }

        $totalFmt = number_format($totalAmount, 0, ',', '.');

        $message = "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\n"
            . "Terima kasih, pembayaran administrasi pesantren untuk santri:\n"
            . "👤 *Nama:* {$santriName}\n"
            . "🧾 *No. Transaksi:* {$orderId}\n"
            . "💳 *Metode:* {$channelLabel}\n"
            . "📅 *Waktu:* {$paidAt}\n\n"
            . "📦 *Rincian Pelunasan:*\n"
            . ($rincian ?: "• (rincian umum)\n")
            . "\n💰 *Total Diterima:* *Rp {$totalFmt}* (LUNAS)\n\n"
            . "Semoga barokah dan bermanfaat bagi kelancaran tholabul 'ilmi ananda. Aamiin.\n\n"
            . "— *Pengurus Keuangan {$appName}*";

        return $this->sendToNumber($phone, $message);
    }

    /**
     * Notifikasi pembayaran gateway (Duitku).
     */
    public function notifyGatewayPayment(
        string $santriName,
        string $orderId,
        string $channelLabel,
        string $paidAt,
        float  $billAmount,
        float  $mdrAmount,
        float  $totalAmount,
        array  $breakdown = []
    ): bool {
        if (!config('whatsapp.notify_gateway', true)) {
            return false;
        }

        $appName = config('app.name', 'Elvith');
        $rincian = '';

        foreach ($breakdown as $item) {
            $label   = $item['config_label'] ?? ucwords(str_replace('_', ' ', $item['bill_type'] ?? ''));
            $period  = $item['period_label'] ?? '';
            $amount  = number_format($item['pay_portion'] ?? $item['net_amount'] ?? 0, 0, ',', '.');
            $rincian .= "• {$label} – {$period} → Rp {$amount}\n";
        }

        $totalFmt = number_format($totalAmount, 0, ',', '.');
        $mdrInfo  = $mdrAmount > 0
            ? "\n💸 *Biaya Layanan:* Rp " . number_format($mdrAmount, 0, ',', '.') . " (ditanggung wali)"
            : '';

        $message = "✅ *PEMBAYARAN DITERIMA*\n"
            . "━━━━━━━━━━━━━━━━━\n\n"
            . "📋 *Santri:* {$santriName}\n"
            . "🏷 *No. Order:* {$orderId}\n"
            . "💳 *Metode:* {$channelLabel} (Online)\n"
            . "📅 *Waktu:* {$paidAt}"
            . $mdrInfo . "\n\n"
            . "📦 *Rincian Tagihan:*\n"
            . ($rincian ?: "• (tidak ada rincian)\n")
            . "\n💰 *Total Dibayar:* Rp {$totalFmt}\n\n"
            . "_Via {$appName} Billing System_";

        return $this->sendToGroup($message);
    }

    /**
     * Notifikasi pembayaran kasir (manual).
     */
    public function notifyKasirPayment(
        string  $santriName,
        string  $billLabel,
        string  $periodLabel,
        string  $method,
        string  $paidAt,
        float   $amount,
        string  $loggedByName,
        ?string $notes = null
    ): bool {
        if (!config('whatsapp.notify_kasir', true)) {
            return false;
        }

        $appName     = config('app.name', 'Elvith');
        $amountFmt   = number_format($amount, 0, ',', '.');
        $methodLabel = match (strtolower($method)) {
            'cash'     => '💵 Tunai',
            'transfer' => '🏦 Transfer Bank',
            default    => strtoupper($method),
        };

        $notesLine = $notes ? "\n📝 *Catatan:* {$notes}" : '';

        $message = "💰 *PEMBAYARAN KASIR DICATAT*\n"
            . "━━━━━━━━━━━━━━━━━\n\n"
            . "📋 *Santri:* {$santriName}\n"
            . "📦 *Tagihan:* {$billLabel} – {$periodLabel}\n"
            . "💳 *Metode:* {$methodLabel}\n"
            . "📅 *Waktu:* {$paidAt}\n"
            . "👤 *Kasir:* {$loggedByName}"
            . $notesLine . "\n\n"
            . "💰 *Jumlah:* Rp {$amountFmt}\n\n"
            . "_Via {$appName} Billing System_";

        return $this->sendToGroup($message);
    }
}

