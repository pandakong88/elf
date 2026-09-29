<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FcmToken;
use App\Services\FcmNotificationService;

class TestFcmNotification extends Command
{
    protected $signature = 'fcm:test';
    protected $description = 'Cek token FCM dan kirim notifikasi uji coba langsung dari console';

    public function handle(FcmNotificationService $fcm)
    {
        $count = FcmToken::count();
        $this->info("Jumlah Token FCM terdaftar di database: {$count}");

        if ($count === 0) {
            $this->warn("PERHATIAN: Belum ada token tersimpan di tabel fcm_tokens!");
            $this->line("Buka browser, login sebagai admin di https://dev.elvith.id/login dan klik 'Izinkan' notifikasi.");
            return 1;
        }

        $tokens = FcmToken::with('user')->get();
        foreach ($tokens as $t) {
            $userEmail = $t->user?->email ?? 'Unknown User';
            $this->line("- User: {$userEmail} | Diupdate: {$t->last_active_at} | Token: " . substr($t->token, 0, 25) . "...");
        }

        $this->info("Mengirim notifikasi uji coba ke semua user terdaftar...");
        foreach ($tokens as $t) {
            $fcm->sendToUser(
                userId: $t->user_id,
                title: '🔔 Tes Notifikasi Elvith',
                body: 'Halo Admin! Push notification Firebase berhasil terhubung ke perangkat ini.',
                clickUrl: url('/dashboard')
            );
        }

        $this->info("Selesai diproses! Periksa log dan layar browser/HP Anda.");
        return 0;
    }
}