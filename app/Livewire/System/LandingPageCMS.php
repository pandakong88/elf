<?php

namespace App\Livewire\System;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Modules\Core\Models\LandingPageContent;
use App\Modules\Kepengasuhan\Models\Activity;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\MasterData;
use App\Livewire\Concerns\SendsToast;

class LandingPageCMS extends Component
{
    use WithFileUploads, SendsToast;

    // Active tab: 'content' or 'activities'
    public $activeTab = 'content';

    // Content fields
    public $hero_title = '';
    public $hero_subtitle = '';
    public $about_profile = '';
    public $about_vision = '';
    public $about_mission = '';
    public $contact_address = '';
    public $contact_email = '';
    public $ig_username = '';
    public $gmaps_url = '';
    public $wa_putra1 = '';
    public $wa_putra2 = '';
    public $wa_putri  = '';
    public $pedoman_title = '';
    public $pedoman_description = '';
    public $registration_info = '';
    
    // File uploads for static content
    public $pedoman_file;
    public $existing_pedoman_url = '';
    
    public $logo_file;
    public $existing_logo_url = '';

    public $hero_image_file;
    public $existing_hero_image_url = '';

    public $about_image_file;
    public $existing_about_image_url = '';

    // ==========================================
    // Activity Management Properties
    // ==========================================
    public $showActivityModal = false;
    public $editingActivityId = null;
    public $act_name = '';
    public $act_date = '';
    public $act_description = '';
    public $act_visibility = 'umum';
    public $act_organization_id = null;
    public $act_activity_type_id = null;
    public $new_photos = []; // Livewire multi-file upload array

    public function mount()
    {
        // Enforce Authorization (Super-Admin or Manajemen or manage-roles)
        if (!auth()->check() || (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasRole('manajemen') && !auth()->user()->can('manage-roles'))) {
            abort(403, 'Anda tidak memiliki wewenang untuk mengakses halaman ini.');
        }

        // Load existing values
        $contents = LandingPageContent::all()->pluck('value', 'key')->toArray();

        $this->hero_title          = $contents['hero_title']          ?? 'Mencetak Generasi Agamis, Beradab Luhur & Berkualitas';
        $this->hero_subtitle       = $contents['hero_subtitle']        ?? 'Pondok Pesantren Al-Fithroh Jejeran Bantul membimbing santri dengan tradisi keilmuan Ahlussunnah wal Jama\'ah sejak tahun 1970.';
        $this->about_profile       = $contents['about_profile']        ?? '';
        $this->about_vision        = $contents['about_vision']         ?? '';
        $this->about_mission       = $contents['about_mission']        ?? '';
        $this->contact_address     = $contents['contact_address']      ?? 'Jejeran, Wonokromo, Pleret, Bantul, DIY';
        $this->contact_email       = $contents['contact_email']        ?? 'info@alfithroh.ac.id';
        $this->ig_username         = $contents['ig_username']          ?? 'alfithroh.jejeran';
        $this->gmaps_url           = $contents['gmaps_url']            ?? 'https://maps.google.com/?q=Pondok+Pesantren+Al-Fithroh+Jejeran+Bantul';
        $this->wa_putra1           = $contents['wa_putra1']            ?? '0812-3456-789';
        $this->wa_putra2           = $contents['wa_putra2']            ?? '0812-9876-543';
        $this->wa_putri            = $contents['wa_putri']             ?? '0811-1222-333';
        $this->pedoman_title       = $contents['pedoman_title']       ?? 'Buku Pedoman Santri 2025/2026';
        $this->pedoman_description = $contents['pedoman_description'] ?? 'Dokumen resmi berisi tata tertib, hak & kewajiban santri.';
        $this->registration_info   = $contents['registration_info']   ?? '';
        $this->existing_pedoman_url     = $contents['pedoman_file_url']    ?? '';
        $this->existing_logo_url        = $contents['logo_url']            ?? '';
        $this->existing_hero_image_url  = $contents['hero_image_url']      ?? '';
        $this->existing_about_image_url = $contents['about_image_url']     ?? '';

        $this->act_date = date('Y-m-d');
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function save()
    {
        $this->validate([
            'hero_title'          => 'required|string|max:200',
            'hero_subtitle'       => 'required|string|max:500',
            'about_profile'       => 'required|string',
            'about_vision'        => 'required|string|max:500',
            'about_mission'       => 'required|string',
            'contact_address'     => 'required|string|max:250',
            'contact_email'       => 'required|email|max:100',
            'ig_username'         => 'nullable|string|max:60',
            'gmaps_url'           => 'nullable|url|max:500',
            'wa_putra1'           => 'nullable|string|max:25',
            'wa_putra2'           => 'nullable|string|max:25',
            'wa_putri'            => 'nullable|string|max:25',
            'pedoman_title'       => 'required|string|max:150',
            'pedoman_description' => 'required|string|max:300',
            'registration_info'   => 'required|string',
            'pedoman_file'        => 'nullable|file|mimes:pdf|max:10240',
            'logo_file'           => 'nullable|file|mimes:png,jpg,jpeg,svg,webp,ico|max:5120',
            'hero_image_file'     => 'nullable|file|mimes:png,jpg,jpeg,webp,svg|max:5120',
            'about_image_file'    => 'nullable|file|mimes:png,jpg,jpeg,webp,svg|max:5120',
        ], [
            'hero_title.required'    => 'Judul Hero harus diisi.',
            'contact_email.email'    => 'Format email humas tidak valid.',
            'gmaps_url.url'          => 'Format link Google Maps tidak valid (harus diawali https://).',
            'pedoman_file.mimes'     => 'File pedoman harus berupa berkas PDF.',
            'pedoman_file.max'       => 'Ukuran file pedoman maksimal 10MB.',
            'logo_file.mimes'        => 'Logo harus berformat PNG, JPG, JPEG, WEBP, atau SVG.',
            'logo_file.max'          => 'Ukuran logo maksimal 5MB.',
            'hero_image_file.mimes'  => 'Banner Hero harus berformat PNG, JPG, JPEG, WEBP, atau SVG.',
            'hero_image_file.max'    => 'Ukuran Banner Hero maksimal 5MB.',
            'about_image_file.mimes' => 'Foto Profil Pondok harus berformat PNG, JPG, JPEG, WEBP, atau SVG.',
            'about_image_file.max'  => 'Ukuran Foto Profil maksimal 5MB.',
        ]);

        $fields = [
            'hero_title'          => ['section' => 'hero',    'title' => 'Judul Utama Hero',         'type' => 'text'],
            'hero_subtitle'       => ['section' => 'hero',    'title' => 'Sub-Judul Hero',           'type' => 'text'],
            'about_profile'       => ['section' => 'about',   'title' => 'Profil Singkat Pondok',    'type' => 'text'],
            'about_vision'        => ['section' => 'about',   'title' => 'Visi Pondok',              'type' => 'text'],
            'about_mission'       => ['section' => 'about',   'title' => 'Misi Pondok',              'type' => 'text'],
            'contact_address'     => ['section' => 'contact', 'title' => 'Alamat Sekretariat',       'type' => 'text'],
            'contact_email'       => ['section' => 'contact', 'title' => 'Email Humas',              'type' => 'text'],
            'ig_username'         => ['section' => 'contact', 'title' => 'Username Instagram',       'type' => 'text'],
            'gmaps_url'           => ['section' => 'contact', 'title' => 'Link Google Maps',         'type' => 'text'],
            'wa_putra1'           => ['section' => 'contact', 'title' => 'WA Admin Putra 1',         'type' => 'text'],
            'wa_putra2'           => ['section' => 'contact', 'title' => 'WA Admin Putra 2',         'type' => 'text'],
            'wa_putri'            => ['section' => 'contact', 'title' => 'WA Admin Putri',           'type' => 'text'],
            'pedoman_title'       => ['section' => 'pedoman', 'title' => 'Judul Buku Pedoman',       'type' => 'text'],
            'pedoman_description' => ['section' => 'pedoman', 'title' => 'Deskripsi Buku Pedoman',   'type' => 'text'],
            'registration_info'   => ['section' => 'pedoman', 'title' => 'Info Penerimaan Santri',   'type' => 'text'],
        ];

        // Save fields
        foreach ($fields as $key => $meta) {
            LandingPageContent::updateOrCreate(
                ['key' => $key],
                [
                    'value'   => $this->$key,
                    'type'    => $meta['type'],
                    'section' => $meta['section'],
                    'title'   => $meta['title'],
                ]
            );
        }

        // Handle pedoman file upload
        if ($this->pedoman_file) {
            $path = $this->pedoman_file->store('pedoman', 'public');
            $url  = '/storage/' . $path;

            LandingPageContent::updateOrCreate(
                ['key' => 'pedoman_file_url'],
                [
                    'value'   => $url,
                    'type'    => 'image_url',
                    'section' => 'contact',
                    'title'   => 'Link File Buku Pedoman PDF',
                ]
            );

            $this->existing_pedoman_url = $url;
            $this->pedoman_file = null;
        }

        // Handle logo file upload
        if ($this->logo_file) {
            $path = $this->logo_file->store('logo', 'public');
            $url  = '/storage/' . $path;

            LandingPageContent::updateOrCreate(
                ['key' => 'logo_url'],
                [
                    'value'   => $url,
                    'type'    => 'image_url',
                    'section' => 'general',
                    'title'   => 'Logo Pondok Pesantren',
                ]
            );

            $this->existing_logo_url = $url;
            $this->logo_file = null;
        }

        // Handle hero image file upload
        if ($this->hero_image_file) {
            $path = $this->hero_image_file->store('hero', 'public');
            $url  = '/storage/' . $path;

            LandingPageContent::updateOrCreate(
                ['key' => 'hero_image_url'],
                [
                    'value'   => $url,
                    'type'    => 'image_url',
                    'section' => 'hero',
                    'title'   => 'Gambar Latar/Banner Hero',
                ]
            );

            $this->existing_hero_image_url = $url;
            $this->hero_image_file = null;
        }

        // Handle about image file upload
        if ($this->about_image_file) {
            $path = $this->about_image_file->store('about', 'public');
            $url  = '/storage/' . $path;
            LandingPageContent::updateOrCreate(
                ['key' => 'about_image_url'],
                ['value' => $url, 'type' => 'image_url', 'section' => 'about', 'title' => 'Foto Profil Pondok']
            );
            $this->existing_about_image_url = $url;
            $this->about_image_file = null;
        }

        activity('security')
            ->causedBy(auth()->user())
            ->log("Telah memperbarui konten landing page via CMS.");

        $this->toastSuccess('Konten landing page berhasil diperbarui.');
    }

    // ==========================================
    // Activity Management Methods
    // ==========================================
    public function openCreateActivityModal()
    {
        $this->resetActivityForm();
        $this->showActivityModal = true;
    }

    public function resetActivityForm()
    {
        $this->editingActivityId = null;
        $this->act_name = '';
        $this->act_date = date('Y-m-d');
        $this->act_description = '';
        $this->act_visibility = 'umum';
        $this->act_organization_id = Organization::first()?->id;
        $this->act_activity_type_id = MasterData::where('category', 'activity_type')->first()?->id;
        $this->new_photos = [];
        $this->resetValidation();
    }

    public function editActivity($id)
    {
        $activity = Activity::findOrFail($id);
        $this->editingActivityId = $activity->id;
        $this->act_name = $activity->name;
        $this->act_date = $activity->date ? $activity->date->format('Y-m-d') : date('Y-m-d');
        $this->act_description = $activity->description ?? '';
        $this->act_visibility = $activity->visibility ?? 'umum';
        $this->act_organization_id = $activity->organization_id;
        $this->act_activity_type_id = $activity->activity_type_id;
        $this->new_photos = [];
        $this->showActivityModal = true;
    }

    public function removeNewPhoto($index)
    {
        if (isset($this->new_photos[$index])) {
            unset($this->new_photos[$index]);
            $this->new_photos = array_values($this->new_photos);
        }
    }

    public function saveActivity()
    {
        $this->validate([
            'act_name' => 'required|string|max:200',
            'act_date' => 'required|date',
            'act_description' => 'required|string',
            'act_visibility' => 'required|in:umum,internal',
            'new_photos.*' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:5120',
        ], [
            'act_name.required' => 'Judul kegiatan harus diisi.',
            'act_date.required' => 'Tanggal kegiatan harus diisi.',
            'act_description.required' => 'Deskripsi kegiatan harus diisi.',
            'new_photos.*.mimes' => 'Foto harus berformat PNG, JPG, JPEG, atau WEBP.',
            'new_photos.*.max' => 'Ukuran setiap foto maksimal 5MB.',
        ]);

        $defaultOrgId = $this->act_organization_id ?: Organization::first()?->id;

        $activity = Activity::updateOrCreate(
            ['id' => $this->editingActivityId],
            [
                'organization_id' => $defaultOrgId,
                'activity_type_id' => $this->act_activity_type_id,
                'name' => $this->act_name,
                'date' => $this->act_date,
                'description' => $this->act_description,
                'visibility' => $this->act_visibility,
            ]
        );

        // Upload multi-photos
        if (!empty($this->new_photos)) {
            foreach ($this->new_photos as $photoFile) {
                $activity->addMedia($photoFile->getRealPath())
                         ->usingFileName($photoFile->getClientOriginalName())
                         ->toMediaCollection('photos');
            }
        }

        $this->showActivityModal = false;
        $this->resetActivityForm();

        $this->toastSuccess('Kegiatan & dokumentasi foto berhasil disimpan.');
    }

    public function deletePhoto($activityId, $mediaId)
    {
        $activity = Activity::findOrFail($activityId);
        $media = $activity->media()->where('id', $mediaId)->first();
        if ($media) {
            $media->delete();
            $this->toastSuccess('Foto dokumentasi berhasil dihapus.');
        }
    }

    public function deleteActivity($id)
    {
        $activity = Activity::findOrFail($id);
        $activity->clearMediaCollection('photos');
        $activity->delete();

        $this->toastSuccess('Kegiatan berhasil dihapus.');
    }

    public function render()
    {
        $activities = Activity::orderBy('date', 'desc')
            ->with(['activityType', 'media'])
            ->get();

        $activityTypes = MasterData::where('category', 'activity_type')->get();
        $organizations = Organization::all();

        return view('livewire.system.landing-page-cms', [
            'activities' => $activities,
            'activityTypes' => $activityTypes,
            'organizations' => $organizations,
        ])->layout('layouts.app');
    }
}
