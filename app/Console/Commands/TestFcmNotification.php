<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FcmToken;
use App\Services\FcmNotificationService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            return 1;
        }

        $tokens = FcmToken::with('user')->get();
        foreach ($tokens as $t) {
            $userEmail = $t->user?->email ?? 'Unknown User';
            $this->line("- User: {$userEmail} | Diupdate: {$t->last_active_at} | Token: " . substr($t->token, 0, 30) . "...");
        }

        $this->info("Mengirim notifikasi uji coba ke Google FCM...");
        
        $firstToken = $tokens->first()->token;
        $projectId = config('services.firebase.project_id');
        
        // Panggil service
        $fcm->sendToUser(
            userId: $tokens->first()->user_id,
            title: '🔔 Tes Notifikasi Elvith',
            body: 'Halo Admin! Push notification Firebase berhasil terhubung ke perangkat ini.',
            clickUrl: url('/dashboard')
        );

        $this->info("Selesai dipanggil! Cek storage/logs/laravel.log untuk detail respon dari Google FCM.");
        
        // Baca 5 baris terakhir laravel.log
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $lines = array_slice(file($logFile), -10);
            $this->warn("--- Cuplikan Log Terakhir ---");
            foreach ($lines as $line) {
                if (str_contains($line, '[FCM]')) {
                    $this->line(trim($line));
                }
            }
        }

        return 0;
    }
}