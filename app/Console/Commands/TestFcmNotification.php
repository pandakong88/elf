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

        $tokens = FcmToken::with('user')->get();
        foreach ($tokens as $t) {
            $userEmail = $t->user?->email ?? 'Unknown User';
            $this->line("- ID: {$t->id} | User: {$userEmail} | Token: " . substr($t->token, 0, 30) . "...");
        }

        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $lines = array_slice(file($logFile), -15);
            $this->warn("--- 15 Baris Log Terakhir ---");
            foreach ($lines as $line) {
                if (str_contains($line, '[FCM]') || str_contains($line, 'DashboardTagihan')) {
                    $this->line(trim($line));
                }
            }
        }

        return 0;
    }
}