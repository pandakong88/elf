<?php

namespace App\Livewire\System;

use Livewire\Component;
use App\Modules\Core\Models\LandingPageContent;
use Illuminate\Support\Facades\Artisan;

class DeveloperSettings extends Component
{
    public $dev_quick_switcher_enabled = true;
    public $dev_quick_switcher_password = 'rahasia123';
    public $successMessage = '';

    public function mount()
    {
        if (!auth()->check() || !auth()->user()->hasRole('super-admin')) {
            abort(403, 'Akses Ditolak: Halaman Pengaturan Developer khusus Super Admin.');
        }

        $contents = LandingPageContent::all()->pluck('value', 'key')->toArray();

        $this->dev_quick_switcher_enabled  = ($contents['dev_quick_switcher_enabled'] ?? '1') === '1';
        $this->dev_quick_switcher_password = $contents['dev_quick_switcher_password'] ?? 'rahasia123';
    }

    public function saveSettings()
    {
        if (!auth()->check() || !auth()->user()->hasRole('super-admin')) {
            abort(403, 'Akses Ditolak.');
        }

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

        $this->successMessage = 'Pengaturan Developer Mode berhasil disimpan!';
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
        return view('livewire.system.developer-settings')
            ->layout('layouts.app');
    }
}
