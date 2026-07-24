<?php

namespace App\Livewire\System;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Modules\Core\Models\LandingPageContent;
use App\Livewire\Concerns\SendsToast;

class LandingPageCMS extends Component
{
    use WithFileUploads, SendsToast;

    // Content fields
    public $hero_title = '';
    public $hero_subtitle = '';
    public $about_profile = '';
    public $about_vision = '';
    public $about_mission = '';
    public $contact_address = '';
    public $contact_phone = '';
    public $contact_email = '';
    public $pedoman_title = '';
    public $pedoman_description = '';
    public $registration_info = '';
    
    // File upload
    public $pedoman_file;
    public $existing_pedoman_url = '';
    
    public $logo_file;
    public $existing_logo_url = '';

    public $hero_image_file;
    public $existing_hero_image_url = '';

    public function mount()
    {
        // Enforce Authorization (Super-Admin or Manajemen or manage-roles)
        if (!auth()->check() || (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasRole('manajemen') && !auth()->user()->can('manage-roles'))) {
            abort(403, 'Anda tidak memiliki wewenang untuk mengakses halaman ini.');
        }

        // Load existing values
        $contents = LandingPageContent::all()->pluck('value', 'key')->toArray();

        $this->hero_title = $contents['hero_title'] ?? 'Membentuk Generasi Berakhlakul Karimah & Unggul';
        $this->hero_subtitle = $contents['hero_subtitle'] ?? 'Pondok Pesantren Al-Fithroh membimbing santri dengan kurikulum salafiyah, tahfidzul qur\'an, dan pendidikan formal modern.';
        $this->about_profile = $contents['about_profile'] ?? 'Pondok Pesantren Al-Fithroh didirikan dengan komitmen untuk melahirkan generasi yang bertakwa, berwawasan luas, beradab mulia, dan siap berkontribusi positif bagi masyarakat. Kami menyinergikan pendidikan pesantren tradisional (kitab kuning) dengan sistem madrasah formal.';
        $this->about_vision = $contents['about_vision'] ?? 'Menjadi pusat pendidikan Islam yang unggul dalam mencetak ulama dan cendekiawan muslim yang berakhlak mulia, berpegang teguh pada nilai-nilai ahlussunnah wal jama\'ah.';
        $this->about_mission = $contents['about_mission'] ?? "1. Menyelenggarakan pendidikan kepesantrenan tradisional dan formal secara terpadu.\n2. Menanamkan adab, kemandirian, dan kedisiplinan berlandaskan akhlak mulia.\n3. Menyelenggarakan program Tahfidzul Qur'an yang terstruktur.\n4. Mendidik santri agar mampu menghadapi tantangan global tanpa kehilangan jati diri muslim.";
        $this->contact_address = $contents['contact_address'] ?? 'Jl. Kedinding Lor No.99, Kenjeran, Surabaya, Jawa Timur 60129';
        $this->contact_phone = $contents['contact_phone'] ?? '0812-3456-7890 (Humas Putra) / 0812-3456-7891 (Humas Putri)';
        $this->contact_email = $contents['contact_email'] ?? 'info@alfithroh.pondok';
        $this->pedoman_title = $contents['pedoman_title'] ?? 'Buku Pedoman Santri 2026/2027';
        $this->pedoman_description = $contents['pedoman_description'] ?? 'Unduh buku pedoman resmi untuk mengetahui tata tertib, hak & kewajiban, serta informasi penting lainnya selama mukim di pondok.';
        $this->registration_info = $contents['registration_info'] ?? 'Penerimaan Santri Baru (PSB) Tahun Ajaran 2026/2027 telah dibuka. Pendaftaran dapat dilakukan langsung di kantor sekretariat pusat Al-Fithroh.';
        $this->existing_pedoman_url = $contents['pedoman_file_url'] ?? '';
        $this->existing_logo_url = $contents['logo_url'] ?? '';
        $this->existing_hero_image_url = $contents['hero_image_url'] ?? '';
    }

    public function save()
    {
        $this->validate([
            'hero_title' => 'required|string|max:200',
            'hero_subtitle' => 'required|string|max:500',
            'about_profile' => 'required|string',
            'about_vision' => 'required|string|max:500',
            'about_mission' => 'required|string',
            'contact_address' => 'required|string|max:250',
            'contact_phone' => 'required|string|max:100',
            'contact_email' => 'required|email|max:100',
            'pedoman_title' => 'required|string|max:150',
            'pedoman_description' => 'required|string|max:300',
            'registration_info' => 'required|string',
            'pedoman_file' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB PDF
            'logo_file' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048', // Max 2MB Image
            'hero_image_file' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:3072', // Max 3MB Image
        ], [
            'hero_title.required' => 'Judul Hero harus diisi.',
            'contact_email.email' => 'Format email humas tidak valid.',
            'pedoman_file.mimes' => 'File pedoman harus berupa berkas PDF.',
            'pedoman_file.max' => 'Ukuran file pedoman maksimal 10MB.',
            'logo_file.image' => 'Logo harus berupa berkas gambar.',
            'logo_file.max' => 'Ukuran logo maksimal 2MB.',
            'hero_image_file.image' => 'Hero Banner harus berupa berkas gambar.',
            'hero_image_file.max' => 'Ukuran Hero Banner maksimal 3MB.',
        ]);

        $fields = [
            'hero_title' => ['section' => 'hero', 'title' => 'Judul Utama Hero', 'type' => 'text'],
            'hero_subtitle' => ['section' => 'hero', 'title' => 'Sub-Judul Hero', 'type' => 'text'],
            'about_profile' => ['section' => 'about', 'title' => 'Profil Singkat Pondok', 'type' => 'text'],
            'about_vision' => ['section' => 'about', 'title' => 'Visi Pondok', 'type' => 'text'],
            'about_mission' => ['section' => 'about', 'title' => 'Misi Pondok', 'type' => 'text'],
            'contact_address' => ['section' => 'contact', 'title' => 'Alamat Sekretariat', 'type' => 'text'],
            'contact_phone' => ['section' => 'contact', 'title' => 'No Telepon Humas', 'type' => 'text'],
            'contact_email' => ['section' => 'contact', 'title' => 'Email Humas', 'type' => 'text'],
            'pedoman_title' => ['section' => 'contact', 'title' => 'Judul Buku Pedoman', 'type' => 'text'],
            'pedoman_description' => ['section' => 'contact', 'title' => 'Deskripsi Buku Pedoman', 'type' => 'text'],
            'registration_info' => ['section' => 'contact', 'title' => 'Info Pendaftaran Baru', 'type' => 'text'],
        ];

        // Save fields
        foreach ($fields as $key => $meta) {
            LandingPageContent::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $this->$key,
                    'type' => $meta['type'],
                    'section' => $meta['section'],
                    'title' => $meta['title'],
                ]
            );
        }

        // Handle pedoman file upload
        if ($this->pedoman_file) {
            $path = $this->pedoman_file->store('pedoman', 'public');
            $url = '/storage/' . $path;

            LandingPageContent::updateOrCreate(
                ['key' => 'pedoman_file_url'],
                [
                    'value' => $url,
                    'type' => 'image_url',
                    'section' => 'contact',
                    'title' => 'Link File Buku Pedoman PDF',
                ]
            );

            $this->existing_pedoman_url = $url;
            $this->pedoman_file = null;
        }

        // Handle logo file upload
        if ($this->logo_file) {
            $path = $this->logo_file->store('logo', 'public');
            $url = '/storage/' . $path;

            LandingPageContent::updateOrCreate(
                ['key' => 'logo_url'],
                [
                    'value' => $url,
                    'type' => 'image_url',
                    'section' => 'general',
                    'title' => 'Logo Pondok Pesantren',
                ]
            );

            $this->existing_logo_url = $url;
            $this->logo_file = null;
        }

        // Handle hero image file upload
        if ($this->hero_image_file) {
            $path = $this->hero_image_file->store('hero', 'public');
            $url = '/storage/' . $path;

            LandingPageContent::updateOrCreate(
                ['key' => 'hero_image_url'],
                [
                    'value' => $url,
                    'type' => 'image_url',
                    'section' => 'hero',
                    'title' => 'Gambar Latar/Banner Hero',
                ]
            );

            $this->existing_hero_image_url = $url;
            $this->hero_image_file = null;
        }

        activity('security')
            ->causedBy(auth()->user())
            ->log("Telah memperbarui konten landing page via CMS.");

        $this->toastSuccess('Konten landing page berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.system.landing-page-cms')->layout('layouts.app');
    }
}
