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
     * Kirim kuitansi pelunasan digital ke WhatsApp Wali Santri (Fonnte).
     */
    public function notifyWaliPaymentReceipt(
        string  $phone,
        string  $santriName,
        string  $orderId,
        string  $channelLabel,
        string  $paidAt,
        float   $totalAmount,
        array   $breakdown = [],
        ?string $roomLocation = null,
        ?string $receiptUrl = null
    ): bool {
        if (!config('whatsapp.notify_wali', true)) {
            return false;
        }

        $appName = config('app.name', 'Pondok Pesantren Al-Fithroh');
        $rincian = '';

        foreach ($breakdown as $item) {
            $label   = $item['config_label'] ?? $item['bill_label'] ?? ucwords(str_replace('_', ' ', $item['bill_type'] ?? ''));
            $period  = $item['period_label'] ?? '';
            $amount  = number_format($item['pay_portion'] ?? $item['net_amount'] ?? $item['amount'] ?? 0, 0, ',', '.');
            
            $isPartial = !empty($item['is_partial']);
            $remaining = max(0, ((float)($item['bill_remaining'] ?? $item['remaining'] ?? 0)) - ((float)($item['pay_portion'] ?? $item['net_amount'] ?? $item['amount'] ?? 0)));

            if ($isPartial && $remaining > 0) {
                $remFmt = number_format($remaining, 0, ',', '.');
                $statusTag = " ⏳ *(Cicilan - Sisa: Rp {$remFmt})*";
            } else {
                $statusTag = " 🟢 *(Lunas)*";
            }

            $rincian .= "• {$label}" . ($period ? " ({$period})" : "") . " : Rp {$amount}{$statusTag}\n";
        }

        $totalFmt = number_format($totalAmount, 0, ',', '.');
        $locationLine = !empty($roomLocation) ? "\n🏠 *Komplek/Kamar:* {$roomLocation}" : '';
        $receiptLine  = !empty($receiptUrl) ? "\n📄 *Kuitansi Digital:* {$receiptUrl}\n" : '';

        $message = "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\n"
            . "Alhamdulillah, pembayaran administrasi pesantren telah berhasil dicatat:\n"
            . "👤 *Santri:* {$santriName}"
            . $locationLine . "\n"
            . "🧾 *No. Transaksi:* {$orderId}\n"
            . "💳 *Metode:* {$channelLabel}\n"
            . "📅 *Waktu:* {$paidAt}\n\n"
            . "📦 *Rincian Pembayaran:*\n"
            . ($rincian ?: "• (rincian umum)\n")
            . "\n💰 *Total Diterima:* *Rp {$totalFmt}*\n"
            . $receiptLine . "\n"
            . "Semoga barokah dan bermanfaat bagi kelancaran tholabul 'ilmi ananda. Aamiin.\n\n"
            . "— *Pengurus Keuangan {$appName}*";

        return $this->sendToNumber($phone, $message);
    }

    /**
     * Buat URL direct WhatsApp (wa.me) kuitansi gratis untuk wali santri.
     */
    public function buildWaliDirectWaUrl(
        string  $phone,
        string  $santriName,
        string  $receiptNo,
        string  $method,
        string  $paidAt,
        float   $totalAmount,
        array   $items = [],
        ?string $roomLocation = null,
        ?string $receiptUrl = null
    ): string {
        $formattedPhone = $this->formatPhoneNumber($phone);
        if (empty($formattedPhone)) {
            return '';
        }

        $appName = config('app.name', 'Pondok Pesantren Al-Fithroh');
        $methodLabel = match (strtoupper($method)) {
            'CASH'     => '💵 Tunai (Kasir)',
            'TRANSFER' => '🏦 Transfer Bank',
            'EWALLET'  => '📱 E-Wallet',
            default    => strtoupper($method),
        };

        $rincian = '';
        foreach ($items as $item) {
            $label   = $item['bill_label'] ?? $item['config_label'] ?? ucwords(str_replace('_', ' ', $item['bill_type'] ?? ''));
            $period  = $item['period_label'] ?? '';
            $amount  = number_format($item['amount'] ?? $item['pay_portion'] ?? $item['net_amount'] ?? 0, 0, ',', '.');
            $isPartial = !empty($item['is_partial']);
            $remaining = (float)($item['remaining'] ?? 0);

            if ($isPartial && $remaining > 0) {
                $remFmt = number_format($remaining, 0, ',', '.');
                $statusTag = " ⏳ (Cicilan - Sisa: Rp {$remFmt})";
            } else {
                $statusTag = " 🟢 (Lunas)";
            }

            $rincian .= "• {$label}" . ($period ? " ({$period})" : "") . " : Rp {$amount}{$statusTag}\n";
        }

        $totalFmt = number_format($totalAmount, 0, ',', '.');
        $locationLine = !empty($roomLocation) ? "\n🏠 *Komplek/Kamar:* {$roomLocation}" : '';
        $receiptLine  = !empty($receiptUrl) ? "\n📄 *Unduh Kuitansi Digital:*\n{$receiptUrl}\n" : '';

        $message = "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\n"
            . "Alhamdulillah, pembayaran administrasi pesantren untuk ananda tercatat:\n"
            . "👤 *Santri:* {$santriName}"
            . $locationLine . "\n"
            . "🧾 *No. Kuitansi:* {$receiptNo}\n"
            . "💳 *Metode:* {$methodLabel}\n"
            . "📅 *Waktu:* {$paidAt}\n\n"
            . "📦 *Rincian Pelunasan:*\n"
            . ($rincian ?: "• (pembayaran tagihan)\n")
            . "\n💰 *Total Diterima:* *Rp {$totalFmt}*\n"
            . $receiptLine . "\n"
            . "Jazakumullahu khairan katsiran. Semoga barokah dan melancarkan proses tholabul 'ilmi ananda. Aamiin.\n\n"
            . "— *Pengurus Keuangan {$appName}*";

        return "https://wa.me/{$formattedPhone}?text=" . rawurlencode($message);
    }

    /**
     * Notifikasi pembayaran gateway (Duitku / DOKU) ke Grup Bendahara.
     */
    public function notifyGatewayPayment(
        string  $santriName,
        string  $orderId,
        string  $channelLabel,
        string  $paidAt,
        float   $billAmount,
        float   $mdrAmount,
        float   $totalAmount,
        array   $breakdown = [],
        ?string $roomLocation = null
    ): bool {
        if (!config('whatsapp.notify_gateway', true)) {
            return false;
        }

        $appName = config('app.name', 'Elvith.id');
        $rincian = '';

        foreach ($breakdown as $item) {
            $label   = $item['config_label'] ?? ucwords(str_replace('_', ' ', $item['bill_type'] ?? ''));
            $period  = $item['period_label'] ?? '';
            $amount  = number_format($item['pay_portion'] ?? $item['net_amount'] ?? 0, 0, ',', '.');

            $isPartial = !empty($item['is_partial']);
            $remaining = max(0, ((float)($item['bill_remaining'] ?? 0)) - ((float)($item['pay_portion'] ?? $item['net_amount'] ?? 0)));

            if ($isPartial && $remaining > 0) {
                $remFmt = number_format($remaining, 0, ',', '.');
                $statusTag = " ⏳ *(Cicilan - Sisa: Rp {$remFmt})*";
            } else {
                $statusTag = " 🟢";
            }

            $rincian .= "• {$label}" . ($period ? " – {$period}" : "") . " → Rp {$amount}{$statusTag}\n";
        }

        $totalFmt = number_format($totalAmount, 0, ',', '.');
        $locationLine = !empty($roomLocation) ? "\n🏠 *Komplek/Kamar:* {$roomLocation}" : '';
        $mdrInfo  = $mdrAmount > 0
            ? "\n💸 *Biaya Layanan:* Rp " . number_format($mdrAmount, 0, ',', '.') . " (ditanggung wali)"
            : '';

        $message = "🟢 *[PAYMENT GATEWAY - UANG MASUK]*\n"
            . "━━━━━━━━━━━━━━━━━━━━━━\n\n"
            . "📋 *Santri:* {$santriName}"
            . $locationLine . "\n"
            . "🏷 *No. Order:* `{$orderId}`\n"
            . "💳 *Metode:* {$channelLabel} (Online)\n"
            . "📅 *Waktu:* {$paidAt}"
            . $mdrInfo . "\n\n"
            . "📦 *Rincian Tagihan:*\n"
            . ($rincian ?: "• (tidak ada rincian)\n")
            . "\n💰 *Total Diterima:* *Rp {$totalFmt}*\n\n"
            . "━━━━━━━━━━━━━━━━━━━━━━\n"
            . "_Sistem Keuangan {$appName}_";

        return $this->sendToGroup($message);
    }

    /**
     * Notifikasi pembayaran kasir (manual multi-tagihan) ke Grup Bendahara.
     */
    public function notifyKasirMultiPayment(
        string  $santriName,
        string  $receiptNo,
        string  $method,
        string  $paidAt,
        float   $totalAmount,
        array   $items,
        string  $loggedByName,
        ?string $roomLocation = null,
        ?string $notes = null
    ): bool {
        if (!config('whatsapp.notify_kasir', true)) {
            return false;
        }

        $appName     = config('app.name', 'Elvith.id');
        $totalFmt    = number_format($totalAmount, 0, ',', '.');
        $methodLabel = match (strtoupper($method)) {
            'CASH'     => '💵 Tunai (Kasir)',
            'TRANSFER' => '🏦 Transfer Bank',
            'EWALLET'  => '📱 E-Wallet',
            default    => strtoupper($method),
        };

        $rincian = '';
        foreach ($items as $item) {
            $label     = $item['bill_label'] ?? $item['config_label'] ?? ucwords(str_replace('_', ' ', $item['bill_type'] ?? ''));
            $period    = $item['period_label'] ?? '';
            $amount    = number_format($item['amount'] ?? $item['pay_portion'] ?? 0, 0, ',', '.');
            $isPartial = !empty($item['is_partial']);
            $remaining = (float)($item['remaining'] ?? 0);

            if ($isPartial && $remaining > 0) {
                $remFmt = number_format($remaining, 0, ',', '.');
                $statusTag = " ⏳ *(Cicilan - Sisa: Rp {$remFmt})*";
            } else {
                $statusTag = " 🟢";
            }

            $rincian .= "• {$label}" . ($period ? " – {$period}" : "") . " → Rp {$amount}{$statusTag}\n";
        }

        $locationLine = !empty($roomLocation) ? "\n🏠 *Komplek/Kamar:* {$roomLocation}" : '';
        $notesLine    = !empty($notes) && $notes !== 'Pembayaran Kasir' ? "\n📝 *Catatan:* {$notes}" : '';

        $message = "💰 *[KASIR - PEMBAYARAN DICATAT]*\n"
            . "━━━━━━━━━━━━━━━━━━━━━━\n\n"
            . "📋 *Santri:* {$santriName}"
            . $locationLine . "\n"
            . "🧾 *No. Kuitansi:* `{$receiptNo}`\n"
            . "💳 *Metode:* {$methodLabel}\n"
            . "📅 *Waktu:* {$paidAt}\n"
            . "👤 *Kasir Bertugas:* {$loggedByName}"
            . $notesLine . "\n\n"
            . "📦 *Rincian Tagihan Dibayar:*\n"
            . ($rincian ?: "• (rincian pembayaran)\n")
            . "\n💰 *Total Uang Diterima:* *Rp {$totalFmt}*\n\n"
            . "━━━━━━━━━━━━━━━━━━━━━━\n"
            . "_Sistem Kasir {$appName}_";

        return $this->sendToGroup($message);
    }

    /**
     * Notifikasi pembayaran kasir (single tagihan legacy).
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
        return $this->notifyKasirMultiPayment(
            santriName:   $santriName,
            receiptNo:    '-',
            method:       $method,
            paidAt:       $paidAt,
            totalAmount:  $amount,
            items:        [[
                'bill_label'   => $billLabel,
                'period_label' => $periodLabel,
                'amount'       => $amount,
                'is_partial'   => false,
                'remaining'    => 0,
            ]],
            loggedByName: $loggedByName,
            notes:        $notes
        );
    }
}

