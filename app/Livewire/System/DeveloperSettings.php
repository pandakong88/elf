<?php

namespace App\Livewire\System;

use Livewire\Component;
use App\Modules\Core\Models\LandingPageContent;
use Illuminate\Support\Facades\Artisan;

class DeveloperSettings extends Component
{
    // Quick Switcher Settings
    public $dev_quick_switcher_enabled = true;
    public $dev_quick_switcher_password = 'rahasia123';

    // DOKU Payment Gateway Settings
    public $doku_enabled = true;
    public $doku_environment = 'sandbox';
    public $doku_client_id = '';
    public $doku_secret_key = '';
    public $doku_expiry_minutes = 1440;

    // Push Notification & WhatsApp Settings
    public $fcm_enabled = true;
    public $fcm_notify_bendahara_pondok = true;
    public $fcm_notify_super_admin = true;
    public $fcm_notify_manajemen = false;
    public $wa_notify_transfers = true;

    // Firebase Diagnostics & Info
    public $firebaseCredentialsExist = false;
    public $firebaseCredentialFile = 'firebase-service-account.json';
    public $firebaseCredentialPath = '';
    public $firebaseProjectId = '';
    public $activeTokensCount = 0;
    public $tokenStats = [];

    // Test Notification Form
    public $test_target = 'me';
    public $test_title = '🔔 Uji Coba dari Developer Mode';
    public $test_body = 'Ini adalah pesan uji coba push notifikasi sistem Elvith.';
    public $testNotificationResult = null;

    public $dokuTestResult = null;
    public $successMessage = '';

    public function mount()
    {
        if (!auth()->check() || !auth()->user()->hasRole('super-admin')) {
            abort(403, 'Akses Ditolak: Halaman Pengaturan Developer khusus Super Admin.');
        }

        $contents = LandingPageContent::all()->pluck('value', 'key')->toArray();

        $this->dev_quick_switcher_enabled  = ($contents['dev_quick_switcher_enabled'] ?? '1') === '1';
        $this->dev_quick_switcher_password = $contents['dev_quick_switcher_password'] ?? 'rahasia123';

        // DOKU Settings
        $this->doku_enabled        = ($contents['doku_enabled'] ?? (config('doku.is_enabled') ? '1' : '0')) === '1';
        $this->doku_environment    = $contents['doku_environment'] ?? config('doku.environment', 'sandbox');
        $this->doku_client_id      = $contents['doku_client_id'] ?? config('doku.client_id', '');
        $this->doku_secret_key     = $contents['doku_secret_key'] ?? config('doku.secret_key', '');
        $this->doku_expiry_minutes = (int) ($contents['doku_expiry_minutes'] ?? config('doku.expiry_minutes', 1440));

        // Notification & WhatsApp Settings
        $this->fcm_enabled                 = ($contents['fcm_enabled'] ?? '1') === '1';
        $this->fcm_notify_bendahara_pondok = ($contents['fcm_notify_bendahara_pondok'] ?? '1') === '1';
        $this->fcm_notify_super_admin      = ($contents['fcm_notify_super_admin'] ?? '1') === '1';
        $this->fcm_notify_manajemen        = ($contents['fcm_notify_manajemen'] ?? '0') === '1';
        $this->wa_notify_transfers         = ($contents['wa_notify_transfers'] ?? '1') === '1';

        $this->checkFirebaseHealth();
    }

    public function saveSettings()
    {
        if (!auth()->check() || !auth()->user()->hasRole('super-admin')) {
            abort(403, 'Akses Ditolak.');
        }

        // 1. Quick Switcher
        LandingPageContent::updateOrCreate(
            ['key' => 'dev_quick_switcher_enabled'],
            [
                'value'   => $this->dev_quick_switcher_enabled ? '1' : '0',
                'type'    => 'text',
                'section' => 'general',
                'title'   => 'Aktifkan Quick Switcher Mode Login',
            ]
        );

        LandingPageContent::updateOrCreate(
            ['key' => 'dev_quick_switcher_password'],
            [
                'value'   => $this->dev_quick_switcher_password ?: 'rahasia123',
                'type'    => 'text',
                'section' => 'general',
                'title'   => 'Kata Sandi Dev Quick Switcher',
            ]
        );

        // 2. DOKU Payment Gateway
        LandingPageContent::updateOrCreate(
            ['key' => 'doku_enabled'],
            [
                'value'   => $this->doku_enabled ? '1' : '0',
                'type'    => 'text',
                'section' => 'payment_gateway',
                'title'   => 'Aktifkan Gateway DOKU di Portal Wali',
            ]
        );

        LandingPageContent::updateOrCreate(
            ['key' => 'doku_environment'],
            [
                'value'   => $this->doku_environment ?: 'sandbox',
                'type'    => 'text',
                'section' => 'payment_gateway',
                'title'   => 'Environment DOKU (sandbox / production)',
            ]
        );

        LandingPageContent::updateOrCreate(
            ['key' => 'doku_client_id'],
            [
                'value'   => trim($this->doku_client_id),
                'type'    => 'text',
                'section' => 'payment_gateway',
                'title'   => 'DOKU Client ID',
            ]
        );

        LandingPageContent::updateOrCreate(
            ['key' => 'doku_secret_key'],
            [
                'value'   => trim($this->doku_secret_key),
                'type'    => 'text',
                'section' => 'payment_gateway',
                'title'   => 'DOKU Secret Key',
            ]
        );

        LandingPageContent::updateOrCreate(
            ['key' => 'doku_expiry_minutes'],
            [
                'value'   => (string) ($this->doku_expiry_minutes ?: 1440),
                'type'    => 'text',
                'section' => 'payment_gateway',
                'title'   => 'Masa Berlaku Invoice DOKU (Menit)',
            ]
        );

        // 3. Push Notification & WhatsApp Settings
        LandingPageContent::updateOrCreate(
            ['key' => 'fcm_enabled'],
            ['value' => $this->fcm_enabled ? '1' : '0', 'type' => 'text', 'section' => 'notifications', 'title' => 'Aktifkan Master Push Notification']
        );
        LandingPageContent::updateOrCreate(
            ['key' => 'fcm_notify_bendahara_pondok'],
            ['value' => $this->fcm_notify_bendahara_pondok ? '1' : '0', 'type' => 'text', 'section' => 'notifications', 'title' => 'Teruskan Notifikasi ke Bendahara Pondok']
        );
        LandingPageContent::updateOrCreate(
            ['key' => 'fcm_notify_super_admin'],
            ['value' => $this->fcm_notify_super_admin ? '1' : '0', 'type' => 'text', 'section' => 'notifications', 'title' => 'Teruskan Notifikasi ke Super Admin']
        );
        LandingPageContent::updateOrCreate(
            ['key' => 'fcm_notify_manajemen'],
            ['value' => $this->fcm_notify_manajemen ? '1' : '0', 'type' => 'text', 'section' => 'notifications', 'title' => 'Teruskan Notifikasi ke Manajemen']
        );
        LandingPageContent::updateOrCreate(
            ['key' => 'wa_notify_transfers'],
            ['value' => $this->wa_notify_transfers ? '1' : '0', 'type' => 'text', 'section' => 'notifications', 'title' => 'Kirim Alert WA ke Grup Bendahara']
        );

        $this->checkFirebaseHealth();
        $this->successMessage = 'Seluruh pengaturan Developer, Payment Gateway DOKU, & Notifikasi berhasil disimpan!';
    }

    public function checkFirebaseHealth()
    {
        $possiblePaths = array_filter([
            config('services.firebase.credentials_path'),
            storage_path('app/firebase-service-account.json'),
            storage_path('app/firebase/firebase_credentials.json'),
            storage_path('app/firebase/firebase-service-account.json'),
            base_path('firebase-service-account.json'),
        ]);

        $this->firebaseCredentialsExist = false;
        $this->firebaseCredentialFile = 'firebase-service-account.json';
        $this->firebaseCredentialPath = '';
        $this->firebaseProjectId = config('services.firebase.project_id', '-');

        foreach ($possiblePaths as $path) {
            if ($path && file_exists($path)) {
                $this->firebaseCredentialsExist = true;
                $this->firebaseCredentialFile = basename($path);
                $this->firebaseCredentialPath = $path;
                try {
                    $json = json_decode(file_get_contents($path), true);
                    if (!empty($json['project_id'])) {
                        $this->firebaseProjectId = $json['project_id'];
                    }
                } catch (\Throwable $e) {}
                break;
            }
        }

        $tokens = \App\Models\FcmToken::with(['user.roles', 'user.person'])
            ->where('last_active_at', '>', now()->subDays(60))
            ->get();

        $this->activeTokensCount = $tokens->count();

        $stats = [
            'super_admin'      => 0,
            'bendahara_putra'  => 0,
            'bendahara_putri'  => 0,
            'bendahara_pondok' => 0,
            'others'           => 0,
        ];

        foreach ($tokens as $t) {
            $roles = $t->user?->roles->pluck('name')->toArray() ?? [];
            if (in_array('super-admin', $roles)) $stats['super_admin']++;
            elseif (in_array('bendahara-putra', $roles)) $stats['bendahara_putra']++;
            elseif (in_array('bendahara-putri', $roles)) $stats['bendahara_putri']++;
            elseif (in_array('bendahara-pondok', $roles)) $stats['bendahara_pondok']++;
            else $stats['others']++;
        }

        $this->tokenStats = $stats;
    }

    public function sendTestPushNotification()
    {
        if (!auth()->check() || !auth()->user()->hasRole('super-admin')) {
            abort(403, 'Akses Ditolak.');
        }

        $fcm = app(\App\Services\FcmNotificationService::class);
        $title = $this->test_title ?: '🔔 Test Notifikasi Developer';
        $body = $this->test_body ?: ('Uji coba push notification pada ' . now()->format('d M Y, H:i:s'));

        try {
            if ($this->test_target === 'me') {
                $fcm->sendToUser(auth()->id(), $title, $body, clickUrl: route('system.dev-settings'));
                $this->testNotificationResult = [
                    'success' => true,
                    'message' => 'Notifikasi uji coba berhasil dikirim ke perangkat Anda sendiri!'
                ];
            } elseif ($this->test_target === 'putra') {
                $fcm->sendToFinancialOfficers('L', $title, $body, clickUrl: url('/keuangan/billing?tab=transfers'));
                $this->testNotificationResult = [
                    'success' => true,
                    'message' => 'Notifikasi uji coba berhasil dikirim ke channel Bendahara Putra!'
                ];
            } elseif ($this->test_target === 'putri') {
                $fcm->sendToFinancialOfficers('P', $title, $body, clickUrl: url('/keuangan/billing?tab=transfers'));
                $this->testNotificationResult = [
                    'success' => true,
                    'message' => 'Notifikasi uji coba berhasil dikirim ke channel Bendahara Putri!'
                ];
            } elseif ($this->test_target === 'all_bendahara') {
                $fcm->sendToFinancialOfficers(null, $title, $body, clickUrl: url('/keuangan/billing?tab=transfers'));
                $this->testNotificationResult = [
                    'success' => true,
                    'message' => 'Notifikasi uji coba berhasil dikirim ke Seluruh Bendahara (Putra & Putri)!'
                ];
            }
        } catch (\Throwable $e) {
            $this->testNotificationResult = [
                'success' => false,
                'message' => 'Gagal mengirim notifikasi: ' . $e->getMessage()
            ];
        }

        $this->checkFirebaseHealth();
    }

    public function testDokuConnection()
    {
        if (!auth()->check() || !auth()->user()->hasRole('super-admin')) {
            abort(403, 'Akses Ditolak.');
        }

        // Simpan sementara nilai input saat ini sebelum testing
        $this->saveSettings();

        $dokuService = app(\App\Modules\Keuangan\Services\DokuService::class);
        $this->dokuTestResult = $dokuService->testConnection();
    }

    public function clearCache()
    {
        if (!auth()->check() || !auth()->user()->hasRole('super-admin')) {
            abort(403, 'Akses Ditolak.');
        }

        try {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            $this->successMessage = 'Cache sistem (View & Cache) berhasil dibersihkan tuntas!';
        } catch (\Exception $e) {
            $this->successMessage = 'Gagal membersihkan cache: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.system.developer-settings', [
            'notificationUrl' => route('doku.notification'),
            'returnUrl'       => route('portal-wali.payment.return'),
        ])->layout('layouts.app');
    }
}
