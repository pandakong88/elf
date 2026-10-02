<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FcmToken;
use App\Services\FcmNotificationService;

class TestFcmNotification extends Command
{
    protected $signature = 'fcm:test {--send : Kirim notifikasi uji coba} {--gender= : Target gender L atau P} {--role= : Target role spesifik}';
    protected $description = 'Cek token FCM dan kirim notifikasi uji coba langsung dari console';

    public function handle(FcmNotificationService $fcm)
    {
        $count = FcmToken::count();
        $this->info("Jumlah Token FCM terdaftar di database: {$count}");

        $tokens = FcmToken::with(['user.roles', 'user.person'])->get();
        foreach ($tokens as $t) {
            $userEmail = $t->user?->email ?? 'Unknown User';
            $userName  = $t->user?->name ?? '-';
            $userRoles = $t->user?->roles->pluck('name')->join(', ') ?: 'no-role';
            $gender    = $t->user?->person?->gender ?? '-';
            $this->line("- ID: {$t->id} | {$userName} ({$userEmail}) | Roles: [{$userRoles}] | Gender: {$gender}");
        }

        if ($this->option('send')) {
            $gender = $this->option('gender') ? strtoupper($this->option('gender')) : null;
            $role   = $this->option('role');

            $this->info("\nMengirim notifikasi uji coba...");
            if ($role) {
                $this->line("Target Role: {$role} (Gender: " . ($gender ?: 'All') . ")");
                $fcm->sendToRoles(
                    roles: [$role],
                    title: "🔔 Test Role FCM ({$role})",
                    body: "Uji coba notifikasi untuk role {$role} pada " . now()->format('H:i:s'),
                    gender: $gender
                );
            } else {
                $genderLabel = $gender === 'P' ? 'Putri' : ($gender === 'L' ? 'Putra' : 'Global');
                $this->line("Target Bendahara: {$genderLabel}");
                $fcm->sendToFinancialOfficers(
                    santriGender: $gender,
                    title: "💰 Test Transfer Bendahara [{$genderLabel}]",
                    body: "Uji coba notifikasi santri {$genderLabel} pada " . now()->format('H:i:s')
                );
            }
            $this->info("Perintah pengiriman selesai diproses.");
        }

        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $lines = array_slice(file($logFile), -15);
            $this->warn("\n--- 15 Baris Log Terakhir ---");
            foreach ($lines as $line) {
                if (str_contains($line, '[FCM]') || str_contains($line, 'DashboardTagihan')) {
                    $this->line(trim($line));
                }
            }
        }

        return 0;
    }
}