<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Core\Models\LandingPageContent;

class LandingPageContentSeeder extends Seeder
{
    public function run(): void
    {
        $contents = [
            'hero_title' => [
                'value' => 'Mencetak Generasi Agamis, Beradab Luhur & Berkualitas',
                'type' => 'text',
                'section' => 'hero',
                'title' => 'Judul Utama Hero'
            ],
            'hero_subtitle' => [
                'value' => 'Pondok Pesantren Al-Fithroh Jejeran Bantul membimbing santri dengan tradisi keilmuan Ahlussunnah wal Jama\'ah sejak tahun 1970, memadukan ketulusan salafiyah dengan kedisiplinan diniyah.',
                'type' => 'text',
                'section' => 'hero',
                'title' => 'Sub-Judul Hero'
            ],
            'about_profile' => [
                'value' => 'Pondok Pesantren Al-Fithroh berdiri sekitar tahun 1970, didirikan oleh al-Maghfurlahu K.H. Abdul Muhith Nawawi didukung oleh para sesepuh. Pada permulaannya, dahulu merupakan pondok pesantren tradisional dengan santri tetap (mukim) berjumlah sekitar 20 orang. Lambat laun semakin bertambah, yang awalnya hanya dari daerah sekitar, pada tahun-tahun berikutnya para santri datang dari berbagai daerah. Sejak akhir tahun 2004 kepengasuhan dilanjutkan oleh KH. Ahmad Mamsyad yang merupakan putra pertama KH. Abdul Muhith sampai sekarang.',
                'type' => 'text',
                'section' => 'about',
                'title' => 'Profil Singkat Pondok'
            ],
            'about_vision' => [
                'value' => 'Menciptakan generasi penerus yang agamis serta berkualitas dengan menegakkan agama Islam yang murni untuk menempuh madzhab ahlussunnah waljama\'ah.',
                'type' => 'text',
                'section' => 'about',
                'title' => 'Visi Pondok'
            ],
            'about_mission' => [
                'value' => "1. Memberikan wadah atau media bagi anak-anak atau generasi muda maupun masyarakat umum yang memerlukan kajian, bertukar pikiran dalam bidang agama maupun keadaan masa kini (teknologi informasi).\n2. Meningkatkan kualitas dan membentuk generasi Islam yang memiliki keimanan dan ketaqwaan kepada Allah SWT.\n3. Menciptakan lulusan yang berkualitas serta dapat memperluas dan mengembangkan syiar Islam dimanapun.",
                'type' => 'text',
                'section' => 'about',
                'title' => 'Misi Pondok'
            ],
            'contact_address' => [
                'value' => 'Jejeran, Wonokromo, Pleret, Bantul, Yogyakarta, 55791',
                'type' => 'text',
                'section' => 'contact',
                'title' => 'Alamat Sekretariat'
            ],
            'contact_phone' => [
                'value' => '0898-6626-009 (Humas Putra) / 0857-1328-5438 (Humas Putri)',
                'type' => 'text',
                'section' => 'contact',
                'title' => 'No Telepon Humas'
            ],
            'contact_email' => [
                'value' => 'info@alfithroh-jejeran.sch.id',
                'type' => 'text',
                'section' => 'contact',
                'title' => 'Email Humas'
            ],
            'pedoman_title' => [
                'value' => 'Buku Pedoman Santri PP. Al-Fithroh',
                'type' => 'text',
                'section' => 'contact',
                'title' => 'Judul Buku Pedoman'
            ],
            'pedoman_description' => [
                'value' => 'Unduh dokumen buku pedoman resmi untuk mengetahui tata tertib, jadwal harian & mingguan kegiatan putra-putri, serta ketentuan administrasi pondok.',
                'type' => 'text',
                'section' => 'contact',
                'title' => 'Deskripsi Buku Pedoman'
            ],
            'registration_info' => [
                'value' => 'Penerimaan Santri Baru (PSB) Pondok Pesantren Al-Fithroh Bantul. Syarat pendaftaran meliputi sowan kepada Pengasuh oleh orang tua/wali, masa training maksimal 10 hari, melengkapi berkas fotokopi KK & pas foto formal berwarna, serta melunasi biaya administrasi masuk. Santri baru wajib menetap selama minimal 40 hari pertama.',
                'type' => 'text',
                'section' => 'contact',
                'title' => 'Info Pendaftaran Baru'
            ],
            'pedoman_file_url' => [
                'value' => '',
                'type' => 'image_url',
                'section' => 'contact',
                'title' => 'Link File Buku Pedoman PDF'
            ],
            'logo_url' => [
                'value' => '/images/logo-alfithroh.png',
                'type' => 'image_url',
                'section' => 'general',
                'title' => 'Logo Pondok Pesantren'
            ],
            'hero_image_url' => [
                'value' => '/images/calligraphy-alfithroh.png',
                'type' => 'image_url',
                'section' => 'hero',
                'title' => 'Gambar Latar/Banner Hero'
            ],
            'about_image_url' => [
                'value' => '/images/foto-pesantren-alfithroh.jpg',
                'type' => 'image_url',
                'section' => 'about',
                'title' => 'Foto Profil Pondok'
            ],
        ];

        foreach ($contents as $key => $data) {
            LandingPageContent::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $data['value'],
                    'type' => $data['type'],
                    'section' => $data['section'],
                    'title' => $data['title'],
                ]
            );
        }
    }
}
