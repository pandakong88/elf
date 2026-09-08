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

        $this->successMessage = 'Seluruh pengaturan Developer & Payment Gateway DOKU berhasil disimpan!';
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
