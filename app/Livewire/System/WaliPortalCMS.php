<?php

namespace App\Livewire\System;

use Livewire\Component;
use App\Modules\Core\Models\LandingPageContent;
use App\Livewire\Concerns\SendsToast;

class WaliPortalCMS extends Component
{
    use SendsToast;

    // Putra Settings
    public string $bank1_name_putra = '';
    public string $rekening_bsi_putra = '';
    public string $rekening_bsi_putra_an = '';

    public string $bank2_name_putra = '';
    public string $rekening_bri_putra = '';
    public string $rekening_bri_putra_an = '';

    public string $wa_bendahara_putra = '';
    public string $wa_bendahara_putra_name = '';

    // Putri Settings
    public string $bank1_name_putri = '';
    public string $rekening_bsi_putri = '';
    public string $rekening_bsi_putri_an = '';

    public string $bank2_name_putri = '';
    public string $rekening_bri_putri = '';
    public string $rekening_bri_putri_an = '';

    public string $wa_bendahara_putri = '';
    public string $wa_bendahara_putri_name = '';

    // General Announcement for Wali
    public string $wali_announcement = '';

    // Info Jadwal Rekap Bendahara (Banner Biru)
    public string $wali_rekap_info   = '';

    public function mount()
    {
        // Enforce Authorization: Hanya Super Admin & Manajemen
        if (!auth()->check() || (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasRole('manajemen') && !auth()->user()->can('manage-roles'))) {
            abort(403, 'Anda tidak memiliki wewenang untuk mengelola CMS Portal Wali.');
        }

        $contents = LandingPageContent::all()->pluck('value', 'key')->toArray();

        // Putra Defaults
        $this->bank1_name_putra        = $contents['wali_bank1_name_putra'] ?? 'Bank Syariah Indonesia (BSI)';
        $this->rekening_bsi_putra      = $contents['wali_bsi_putra'] ?? '7123456789';
        $this->rekening_bsi_putra_an   = $contents['wali_bsi_putra_an'] ?? 'Pesantren Al-Fithroh Putra';

        $this->bank2_name_putra        = $contents['wali_bank2_name_putra'] ?? '';
        $this->rekening_bri_putra      = $contents['wali_bri_putra'] ?? '';
        $this->rekening_bri_putra_an   = $contents['wali_bri_putra_an'] ?? '';

        $this->wa_bendahara_putra      = $contents['wali_wa_putra'] ?? '6281234567890';
        $this->wa_bendahara_putra_name = $contents['wali_wa_putra_name'] ?? 'Bendahara Putra Al-Fithroh';

        // Putri Defaults
        $this->bank1_name_putri        = $contents['wali_bank1_name_putri'] ?? 'Bank Syariah Indonesia (BSI)';
        $this->rekening_bsi_putri      = $contents['wali_bsi_putri'] ?? '7987654321';
        $this->rekening_bsi_putri_an   = $contents['wali_bsi_putri_an'] ?? 'Pesantren Al-Fithroh Putri';

        $this->bank2_name_putri        = $contents['wali_bank2_name_putri'] ?? '';
        $this->rekening_bri_putri      = $contents['wali_bri_putri'] ?? '';
        $this->rekening_bri_putri_an   = $contents['wali_bri_putri_an'] ?? '';

        $this->wa_bendahara_putri      = $contents['wali_wa_putri'] ?? '6281234567891';
        $this->wa_bendahara_putri_name = $contents['wali_wa_putri_name'] ?? 'Bendahara Putri Al-Fithroh';

        // Pengumuman
        $this->wali_announcement       = $contents['wali_announcement'] ?? 'Pembayaran tagihan santri dilakukan sebelum tanggal 10 setiap bulannya.';

        // Info Jadwal Rekap
        $this->wali_rekap_info         = $contents['wali_rekap_info'] ?? 'Data tagihan diperbarui oleh bendahara setiap Tanggal 1 dan 15 setiap bulannya. Jika Bapak/Ibu sudah melakukan transfer namun status tagihan belum berubah, mohon bersabar hingga tanggal pembaruan berikutnya.';
    }

    public function save()
    {
        // Enforce Authorization lagi saat menyimpan
        if (!auth()->check() || (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasRole('manajemen') && !auth()->user()->can('manage-roles'))) {
            abort(403, 'Anda tidak memiliki wewenang untuk mengelola CMS Portal Wali.');
        }

        $this->validate([
            'bank1_name_putra'        => 'required|string|max:100',
            'rekening_bsi_putra'      => 'required|string|max:50',
            'rekening_bsi_putra_an'   => 'required|string|max:100',

            'bank2_name_putra'        => 'nullable|string|max:100',
            'rekening_bri_putra'      => 'nullable|string|max:50',
            'rekening_bri_putra_an'   => 'nullable|string|max:100',

            'wa_bendahara_putra'      => 'required|string|max:20',
            'wa_bendahara_putra_name' => 'required|string|max:100',

            'bank1_name_putri'        => 'required|string|max:100',
            'rekening_bsi_putri'      => 'required|string|max:50',
            'rekening_bsi_putri_an'   => 'required|string|max:100',

            'bank2_name_putri'        => 'nullable|string|max:100',
            'rekening_bri_putri'      => 'nullable|string|max:50',
            'rekening_bri_putri_an'   => 'nullable|string|max:100',

            'wa_bendahara_putri'      => 'required|string|max:20',
            'wa_bendahara_putri_name' => 'required|string|max:100',

            'wali_announcement'       => 'nullable|string|max:500',
            'wali_rekap_info'         => 'nullable|string|max:800',
        ], [
            'bank1_name_putra.required'   => 'Nama Bank 1 Putra wajib diisi.',
            'rekening_bsi_putra.required' => 'Nomor Rekening Bank 1 Putra wajib diisi.',
            'bank1_name_putri.required'   => 'Nama Bank 1 Putri wajib diisi.',
            'rekening_bsi_putri.required' => 'Nomor Rekening Bank 1 Putri wajib diisi.',
        ]);

        $fields = [
            'wali_bank1_name_putra'  => ['section' => 'wali_portal', 'title' => 'Nama Bank 1 Putra', 'type' => 'text', 'value' => $this->bank1_name_putra],
            'wali_bsi_putra'         => ['section' => 'wali_portal', 'title' => 'No Rekening Bank 1 Putra', 'type' => 'text', 'value' => $this->rekening_bsi_putra],
            'wali_bsi_putra_an'      => ['section' => 'wali_portal', 'title' => 'Atas Nama Bank 1 Putra', 'type' => 'text', 'value' => $this->rekening_bsi_putra_an],

            'wali_bank2_name_putra'  => ['section' => 'wali_portal', 'title' => 'Nama Bank 2 Putra', 'type' => 'text', 'value' => $this->bank2_name_putra],
            'wali_bri_putra'         => ['section' => 'wali_portal', 'title' => 'No Rekening Bank 2 Putra', 'type' => 'text', 'value' => $this->rekening_bri_putra],
            'wali_bri_putra_an'      => ['section' => 'wali_portal', 'title' => 'Atas Nama Bank 2 Putra', 'type' => 'text', 'value' => $this->rekening_bri_putra_an],

            'wali_wa_putra'          => ['section' => 'wali_portal', 'title' => 'WA Bendahara Putra', 'type' => 'text', 'value' => $this->wa_bendahara_putra],
            'wali_wa_putra_name'     => ['section' => 'wali_portal', 'title' => 'Nama Bendahara Putra', 'type' => 'text', 'value' => $this->wa_bendahara_putra_name],

            'wali_bank1_name_putri'  => ['section' => 'wali_portal', 'title' => 'Nama Bank 1 Putri', 'type' => 'text', 'value' => $this->bank1_name_putri],
            'wali_bsi_putri'         => ['section' => 'wali_portal', 'title' => 'No Rekening Bank 1 Putri', 'type' => 'text', 'value' => $this->rekening_bsi_putri],
            'wali_bsi_putri_an'      => ['section' => 'wali_portal', 'title' => 'Atas Nama Bank 1 Putri', 'type' => 'text', 'value' => $this->rekening_bsi_putri_an],

            'wali_bank2_name_putri'  => ['section' => 'wali_portal', 'title' => 'Nama Bank 2 Putri', 'type' => 'text', 'value' => $this->bank2_name_putri],
            'wali_bri_putri'         => ['section' => 'wali_portal', 'title' => 'No Rekening Bank 2 Putri', 'type' => 'text', 'value' => $this->rekening_bri_putri],
            'wali_bri_putri_an'      => ['section' => 'wali_portal', 'title' => 'Atas Nama Bank 2 Putri', 'type' => 'text', 'value' => $this->rekening_bri_putri_an],

            'wali_wa_putri'          => ['section' => 'wali_portal', 'title' => 'WA Bendahara Putri', 'type' => 'text', 'value' => $this->wa_bendahara_putri],
            'wali_wa_putri_name'     => ['section' => 'wali_portal', 'title' => 'Nama Bendahara Putri', 'type' => 'text', 'value' => $this->wa_bendahara_putri_name],

            'wali_announcement'      => ['section' => 'wali_portal', 'title' => 'Pengumuman Portal Wali', 'type' => 'text', 'value' => $this->wali_announcement],
            'wali_rekap_info'        => ['section' => 'wali_portal', 'title' => 'Info Jadwal Rekap Bendahara', 'type' => 'text', 'value' => $this->wali_rekap_info],
        ];

        foreach ($fields as $key => $data) {
            LandingPageContent::updateOrCreate(
                ['key' => $key],
                [
                    'value'   => $data['value'],
                    'type'    => $data['type'],
                    'section' => $data['section'],
                    'title'   => $data['title'],
                ]
            );
        }

        activity('security')
            ->causedBy(auth()->user())
            ->log("Telah memperbarui konfigurasi Nama Bank, Rekening & WA Bendahara CMS Portal Wali.");

        $this->toastSuccess('Pengaturan Nama Bank, Rekening & WhatsApp Bendahara berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.system.wali-portal-cms')->layout('layouts.app');
    }
}
