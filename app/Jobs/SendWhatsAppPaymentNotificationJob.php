<?php

namespace App\Jobs;

use App\Modules\Keuangan\Models\PaymentTransaction;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppPaymentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah percobaan maksimal jika terjadi kegagalan jaringan.
     */
    public int $tries = 3;

    /**
     * Waktu jeda (dalam detik) sebelum mencoba kembali (exponential backoff).
     */
    public array $backoff = [10, 60, 180];

    /**
     * ID transaksi pembayaran.
     */
    public string $transactionId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsAppService): void
    {
        $transaction = PaymentTransaction::with(['person.santriProfile'])->find($this->transactionId);

        if (!$transaction) {
            Log::warning("[SendWhatsAppPaymentNotificationJob] Transaksi {$this->transactionId} tidak ditemukan.");
            return;
        }

        $person       = $transaction->person;
        $santriName   = $person?->name ?? '—';
        $channelLabel = $transaction->channel_label ?? $transaction->payment_channel ?? 'Online Gateway';
        $paidAt       = ($transaction->callback_received_at ?? $transaction->created_at)->locale('id')->translatedFormat('d F Y, H:i') . ' WIB';
        $breakdown    = $transaction->bill_breakdown ?? [];

        // 1. Kirim notifikasi ke Grup WhatsApp Bendahara
        try {
            $whatsAppService->notifyGatewayPayment(
                santriName:   $santriName,
                orderId:      $transaction->merchant_order_id,
                channelLabel: $channelLabel,
                paidAt:       $paidAt,
                billAmount:   (float) $transaction->bill_amount,
                mdrAmount:    (float) $transaction->mdr_amount,
                totalAmount:  (float) $transaction->total_amount,
                breakdown:    $breakdown,
            );
        } catch (\Throwable $e) {
            Log::warning("[SendWhatsAppPaymentNotificationJob] Gagal kirim ke grup: " . $e->getMessage());
        }

        // 2. Kirim kuitansi WhatsApp ke Nomor Pribadi Wali Santri (jika ada)
        try {
            $waliPhone = $person?->santriProfile?->father_phone 
                ?: $person?->santriProfile?->mother_phone 
                ?: $person?->santriProfile?->guardian_phone 
                ?: $person?->phone;

            if ($waliPhone) {
                $whatsAppService->notifyWaliPaymentReceipt(
                    phone:        $waliPhone,
                    santriName:   $santriName,
                    orderId:      $transaction->merchant_order_id,
                    channelLabel: $channelLabel,
                    paidAt:       $paidAt,
                    totalAmount:  (float) $transaction->total_amount,
                    breakdown:    $breakdown,
                );
            }
        } catch (\Throwable $e) {
            Log::warning("[SendWhatsAppPaymentNotificationJob] Gagal kirim kuitansi ke wali: " . $e->getMessage());
        }
    }

    /**
     * Tangani kegagalan permanen setelah semua percobaan (tries) habis.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("[SendWhatsAppPaymentNotificationJob] Gagal permanen mengirim WhatsApp untuk transaksi {$this->transactionId}: " . $exception->getMessage());
    }
}
