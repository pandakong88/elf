<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Modules\Core\Models\LandingPageContent;
use App\Modules\Kepengasuhan\Models\Activity;

class LandingPage extends Component
{
    public function render()
    {
        // Query all landing page contents
        $contents = LandingPageContent::all()->pluck('value', 'key')->toArray();

        // Get general defaults if keys are empty
        $defaults = [
            'pondok_name'        => 'Pondok Pesantren Al-Fithroh',
            'hero_title'         => 'Mencetak Generasi Agamis, Beradab Luhur & Berkualitas',
            'hero_subtitle'      => 'Pondok Pesantren Al-Fithroh Jejeran Bantul membimbing santri dengan tradisi keilmuan Ahlussunnah wal Jama\'ah sejak tahun 1970, memadukan ketulusan salafiyah dengan kedisiplinan diniyah.',
            'about_profile'      => 'Pondok Pesantren Al-Fithroh Jejeran Bantul membimbing santri dengan tradisi keilmuan Ahlussunnah wal Jama\'ah sejak tahun 1970, memadukan ketulusan salafiyah dengan kedisiplinan diniyah. Kami hadir sebagai lembaga pendidikan Islam yang berkomitmen mencetak generasi berakhlakul karimah, berpengetahuan luas, dan siap berkontribusi positif bagi umat.',
            'about_vision'       => 'Mencetak generasi Islam yang agamis, beradab luhur, dan berkualitas — berpegang teguh pada manhaj Ahlussunnah wal Jama\'ah, siap menghadapi tantangan zaman tanpa kehilangan jati diri.',
            'about_mission'      => "1. Memberikan wadah atau media bagi anak-anak atau generasi muda maupun masyarakat umum yang memerlukan kajian, bertukar pikiran dalam bidang agama maupun keadaan masa kini (teknologi informasi).\n2. Meningkatkan kualitas dan membentuk generasi Islam yang memiliki keimanan dan ketaqwaan kepada Allah SWT.\n3. Menciptakan lulusan yang berkualitas serta dapat memperluas dan mengembangkan syiar Islam dimanapun.",
            'contact_address'    => 'Jejeran, Wonokromo, Pleret, Bantul, Daerah Istimewa Yogyakarta 55791',
            'contact_email'      => 'info@alfithroh.ac.id',
            'ig_username'        => 'alfithroh.jejeran',
            'gmaps_url'          => 'https://maps.google.com/?q=Pondok+Pesantren+Al-Fithroh+Jejeran+Bantul',
            'wa_putra1'          => '0812-3456-789',
            'wa_putra2'          => '0812-9876-543',
            'wa_putri'           => '0811-1222-333',
            'pedoman_title'      => 'Buku Pedoman Santri 2025/2026',
            'pedoman_description'=> 'Dokumen resmi berisi tata tertib, hak & kewajiban santri, serta informasi penting lainnya selama mukim di Pondok Pesantren Al-Fithroh.',
            'pedoman_file_url'   => '',
            'registration_info'  => 'Penerimaan Santri Baru (PSB) dibuka setiap tahun. Syarat pendaftaran meliputi sowan kepada Pengasuh oleh orang tua/wali, masa training maksimal 10 hari, melengkapi berkas fotokopi KK & pas foto formal berwarna, serta melunasi biaya administrasi masuk.',
            'hero_image_url'     => '',
            'about_image_url'    => '',
        ];

        // Merge defaults with DB contents
        $data = array_merge($defaults, $contents);

        // Fetch 6 recent public activities
        $activities = Activity::where('visibility', 'umum')
            ->orderBy('date', 'desc')
            ->limit(6)
            ->with(['activityType'])
            ->get();

        return view('livewire.public.landing-page', [
            'data' => $data,
            'activities' => $activities,
        ])->layout('layouts.guest'); // Using guest layout for public page
    }
}
