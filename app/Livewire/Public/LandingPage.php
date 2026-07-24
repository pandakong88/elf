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
            'pondok_name' => 'Pondok Pesantren Al-Fithroh',
            'hero_title' => 'Membentuk Generasi Berakhlakul Karimah & Unggul',
            'hero_subtitle' => 'Pondok Pesantren Al-Fithroh membimbing santri dengan kurikulum salafiyah, tahfidzul qur\'an, dan pendidikan formal modern.',
            'about_profile' => 'Pondok Pesantren Al-Fithroh didirikan dengan komitmen untuk melahirkan generasi yang bertakwa, berwawasan luas, beradab mulia, dan siap berkontribusi positif bagi masyarakat. Kami menyinergikan pendidikan pesantren tradisional (kitab kuning) dengan sistem madrasah formal.',
            'about_vision' => 'Menjadi pusat pendidikan Islam yang unggul dalam mencetak ulama dan cendekiawan muslim yang berakhlak mulia, berpegang teguh pada nilai-nilai ahlussunnah wal jama\'ah.',
            'about_mission' => "1. Menyelenggarakan pendidikan kepesantrenan tradisional dan formal secara terpadu.\n2. Menanamkan adab, kemandirian, dan kedisiplinan berlandaskan akhlak mulia.\n3. Menyelenggarakan program Tahfidzul Qur'an yang terstruktur.\n4. Mendidik santri agar mampu menghadapi tantangan global tanpa kehilangan jati diri muslim.",
            'contact_address' => 'Jl. Kedinding Lor No.99, Kenjeran, Surabaya, Jawa Timur 60129',
            'contact_phone' => '0812-3456-7890 (Humas Putra) / 0812-3456-7891 (Humas Putri)',
            'contact_email' => 'info@elvith.id',
            'pedoman_title' => 'Buku Pedoman Santri 2026/2027',
            'pedoman_description' => 'Unduh buku pedoman resmi untuk mengetahui tata tertib, hak & kewajiban, serta informasi penting lainnya selama mukim di pondok.',
            'pedoman_file_url' => '',
            'registration_info' => 'Penerimaan Santri Baru (PSB) Tahun Ajaran 2026/2027 telah dibuka. Pendaftaran dapat dilakukan langsung di kantor sekretariat pusat Al-Fithroh.',
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
