<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Madrasah\Models\MadrasahEnrollment;
use App\Modules\Madrasah\Models\MadrasahKelas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SantriLajuSeeder extends Seeder
{
    /**
     * Menambahkan santri laju ke setiap kelas madrasah.
     * Santri laju = santri yang mengikuti madrasah tapi tidak mukim di pondok.
     *
     * Jumlah laju per kelas: 3–4 orang (random).
     * Presence status: 'laju' di PersonRole.
     * Tidak ada room_assignment dan tidak ada tagihan pondok.
     */

    // Nama-nama dummy untuk santri laju putra
    private array $namaLajuPutra = [
        'Bilal Hakim',      'Salman Rasyid',     'Tariq Zubair',      'Naufal Fikri',
        'Dzaky Mubarok',    'Faris Abdurrahman', 'Ghifari Salim',     'Hamdan Tsabit',
        'Iqbal Maulana',    'Jabir Saifudin',    'Kamil Nasrullah',   'Luqman Hakim',
        'Mazin Taufiq',     'Nashir Wafi',       'Omar Zaki',         'Qais Sholihuddin',
        'Rafi Hidayat',     'Syauqi Amri',       'Tsaqib Muzzaki',    'Ubaid Karim',
        'Valdi Mujahid',    'Wahyu Sanusi',      'Yahya Nugroho',     'Zidan Qoyyum',
        'Afif Rosyadi',     'Badar Halim',       'Cakra Fathoni',     'Daffa Hilmi',
        'Esa Wicaksono',    'Fadhil Mughni',     'Gifari Ramadhan',   'Haikal Yusron',
        'Idan Pradana',     'Jakfar Mukhlis',    'Kafil Azam',        'Lathif Zamzami',
        'Maher Sulthoni',   'Nasr Rifai',        'Osa Firdaus',       'Pandu Subakti',
        'Qorib Syafii',     'Rafi Ismail',       'Salim Badawi',      'Tamam Basori',
        'Umar Syaifullah',  'Wafi Mukhtar',      'Yazid Ghufron',     'Zikri Hanafi',
        'Alif Barakah',     'Baha Udin',         'Chairul Anwar',     'Dani Miftah',
    ];

    // Nama-nama dummy untuk santri laju putri
    private array $namaLajuPutri = [
        'Afifah Salimah',   'Balqis Rasyidah',   'Chusnul Khotimah',  'Durrotun Nafis',
        'Elfa Nurina',      'Faizah Ulya',       'Ghina Mufidah',     'Hanifa Zuhda',
        'Iffah Karimah',    'Jihan Lathifah',    'Kamila Muniroh',    'Layla Badriyah',
        'Mazaya Nabilah',   'Nayla Wardah',      'Ola Hikmah',        'Putri Salsabila',
        'Qonita Azzahra',   'Rania Fadilah',     'Shifa Wulandari',   'Tsuroyya Hamidah',
        'Umayyah Sholihah', 'Vina Rahmawati',    'Wahdah Ilmiyah',    'Yumna Fauziyah',
        'Zulfa Najihah',    'Anisah Rosyidah',   'Badriah Taslimah',  'Chafifah Ulwiyah',
        'Dina Munawaroh',   'Eca Fitriana',      'Fida Maulida',      'Ghufran Nisa',
        'Hana Khaulah',     'Inas Maziyyah',     'Jauharoh Fathimah', 'Khozinah Ilmi',
        'Lutfia Kamaliyah', 'Muna Shofiyah',     'Nuha Zakiyah',      'Ola Rohmah',
        'Pramudita Alya',   'Qurrota Aini',      'Rifa Tsaqifah',     'Salamah Barokah',
        'Tri Rahmatika',    'Ulfah Dzikrullah',  'Wardah Mawaddah',   'Yasmin Hamdiyah',
        'Zahro Musyarofah', 'Afra Mufliha',      'Bening Nurani',     'Cahya Karima',
    ];

    private array $tempatLahir = [
        'Surabaya', 'Sidoarjo', 'Gresik', 'Lamongan', 'Malang',
        'Mojokerto', 'Pasuruan', 'Jombang', 'Tuban', 'Bojonegoro',
        'Bangkalan', 'Pamekasan', 'Sumenep', 'Lumajang', 'Probolinggo',
    ];

    public function run(): void
    {
        $academicYear = '2025/2026';

        $orgPutra = Organization::where('slug', 'kepengasuhan-putra')->firstOrFail();
        $orgPutri = Organization::where('slug', 'kepengasuhan-putri')->firstOrFail();

        // Ambil semua kelas madrasah tahun ajaran ini
        $kelasPutra = MadrasahKelas::where('academic_year', $academicYear)
            ->where('name', 'like', '%Putra%')
            ->orderBy('name')
            ->get();

        $kelasPutri = MadrasahKelas::where('academic_year', $academicYear)
            ->where('name', 'like', '%Putri%')
            ->orderBy('name')
            ->get();

        $totalLajuDibuat = 0;
        $indexNamaPutra  = 0;
        $indexNamaPutri  = 0;

        // ─── Santri Laju PUTRA ──────────────────────────────────────────
        foreach ($kelasPutra as $kelas) {
            $jumlahLaju = rand(3, 4);

            for ($i = 0; $i < $jumlahLaju; $i++) {
                $nama = $this->namaLajuPutra[$indexNamaPutra % count($this->namaLajuPutra)];
                $indexNamaPutra++;

                $person = Person::create([
                    'id'          => Str::uuid(),
                    'nik'         => null,
                    'name'        => $nama,
                    'gender'      => 'L',
                    'birth_place' => $this->tempatLahir[array_rand($this->tempatLahir)],
                    'birth_date'  => now()->subYears(rand(10, 20))->subDays(rand(0, 365))->toDateString(),
                    'phone'       => null,
                    'address'     => 'Jl. Luar Pondok No. ' . rand(1, 100) . ', ' . $this->tempatLahir[array_rand($this->tempatLahir)],
                    'notes'       => 'Santri laju madrasah — ' . $kelas->name,
                ]);

                PersonRole::create([
                    'id'                   => Str::uuid(),
                    'person_id'            => $person->id,
                    'organization_id'      => $orgPutra->id,
                    'role_type'            => 'santri',
                    'valid_from'           => now()->startOfYear()->toDateString(),
                    'valid_until'          => null,
                    'is_active'            => true,
                    'enrollment_status'    => 'aktif',
                    'presence_status'      => 'laju',
                    'presence_status_since'=> now()->startOfYear(),
                    'presence_status_notes'=> 'Santri laju, tidak mukim di pondok.',
                ]);

                MadrasahEnrollment::create([
                    'id'           => Str::uuid(),
                    'person_id'    => $person->id,
                    'kelas_id'     => $kelas->id,
                    'academic_year'=> $academicYear,
                    'is_active'    => true,
                    'created_by'   => null,
                ]);

                $totalLajuDibuat++;
            }
        }

        // ─── Santri Laju PUTRI ──────────────────────────────────────────
        foreach ($kelasPutri as $kelas) {
            $jumlahLaju = rand(3, 4);

            for ($i = 0; $i < $jumlahLaju; $i++) {
                $nama = $this->namaLajuPutri[$indexNamaPutri % count($this->namaLajuPutri)];
                $indexNamaPutri++;

                $person = Person::create([
                    'id'          => Str::uuid(),
                    'nik'         => null,
                    'name'        => $nama,
                    'gender'      => 'P',
                    'birth_place' => $this->tempatLahir[array_rand($this->tempatLahir)],
                    'birth_date'  => now()->subYears(rand(10, 20))->subDays(rand(0, 365))->toDateString(),
                    'phone'       => null,
                    'address'     => 'Jl. Luar Pondok No. ' . rand(1, 100) . ', ' . $this->tempatLahir[array_rand($this->tempatLahir)],
                    'notes'       => 'Santri laju madrasah — ' . $kelas->name,
                ]);

                PersonRole::create([
                    'id'                   => Str::uuid(),
                    'person_id'            => $person->id,
                    'organization_id'      => $orgPutri->id,
                    'role_type'            => 'santri',
                    'valid_from'           => now()->startOfYear()->toDateString(),
                    'valid_until'          => null,
                    'is_active'            => true,
                    'enrollment_status'    => 'aktif',
                    'presence_status'      => 'laju',
                    'presence_status_since'=> now()->startOfYear(),
                    'presence_status_notes'=> 'Santri laju, tidak mukim di pondok.',
                ]);

                MadrasahEnrollment::create([
                    'id'           => Str::uuid(),
                    'person_id'    => $person->id,
                    'kelas_id'     => $kelas->id,
                    'academic_year'=> $academicYear,
                    'is_active'    => true,
                    'created_by'   => null,
                ]);

                $totalLajuDibuat++;
            }
        }

        $lajuPutra = Person::whereHas('roles', fn($q) =>
            $q->where('role_type', 'santri')
              ->where('presence_status', 'laju')
        )->where('gender', 'L')->count();

        $lajuPutri = Person::whereHas('roles', fn($q) =>
            $q->where('role_type', 'santri')
              ->where('presence_status', 'laju')
        )->where('gender', 'P')->count();

        $this->command->info("  ✅ SantriLajuSeeder: {$totalLajuDibuat} santri laju berhasil dibuat.");
        $this->command->info("     → Putra : {$lajuPutra} santri laju di {$kelasPutra->count()} kelas");
        $this->command->info("     → Putri : {$lajuPutri} santri laju di {$kelasPutri->count()} kelas");
        $this->command->info("     → Semua santri laju: presence_status = 'laju', tanpa kamar, tanpa tagihan pondok.");
    }
}
