<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Madrasah\Models\MadrasahEnrollment;
use App\Modules\Madrasah\Models\MadrasahKelas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IsiKamarSeeder extends Seeder
{
    /**
     * Mengisi setiap kamar dengan santri mukim (5–8 orang per kamar).
     * Santri yang sudah ada di kamar tetap dipertahankan.
     * Santri baru yang ditambahkan juga otomatis di-enroll ke kelas madrasah.
     */

    // ── Nama Putra ───────────────────────────────────────────────────
    private array $firstNamePutra = [
        'Ahmad', 'Muhammad', 'Abdullah', 'Umar', 'Zaid', 'Ibrahim', 'Hasan', 'Husain',
        'Yusuf', 'Khalid', 'Bilal', 'Salman', 'Tariq', 'Naufal', 'Dzaky', 'Faris',
        'Ghifari', 'Hamdan', 'Iqbal', 'Jabir', 'Kamil', 'Luqman', 'Mazin', 'Nashir',
        'Omar', 'Qais', 'Rafi', 'Syauqi', 'Tsaqib', 'Ubaid', 'Wahyu', 'Yahya',
        'Zidan', 'Afif', 'Badar', 'Daffa', 'Fadhil', 'Haikal', 'Jakfar', 'Kafil',
        'Lathif', 'Maher', 'Nasr', 'Pandu', 'Qorib', 'Salim', 'Tamam', 'Wafi',
        'Yazid', 'Zikri', 'Alif', 'Baha', 'Dani', 'Esa', 'Gifari', 'Hendra',
        'Irfan', 'Jauhar', 'Khoirul', 'Luthfi', 'Mahrus', 'Nabil', 'Oky', 'Prasetyo',
        'Reza', 'Syahrul', 'Taufiq', 'Ulin', 'Vicky', 'Wahid', 'Yayan', 'Zulfikar',
        'Anas', 'Bagas', 'Cahyo', 'Dimas', 'Erlan', 'Fuad', 'Galih', 'Hafidz',
        'Ilham', 'Jundi', 'Kevin', 'Lukman', 'Munir', 'Nanang', 'Okta', 'Pondra',
    ];

    private array $lastNamePutra = [
        'Fauzi', 'Rizki', 'Hakim', 'Faruq', 'Al-Amin', 'Sholeh', 'Mubarak', 'Barokah',
        'Abdillah', 'Mukhlis', 'Rasyid', 'Zubair', 'Fikri', 'Mubarok', 'Abdurrahman',
        'Salim', 'Tsabit', 'Maulana', 'Saifudin', 'Nasrullah', 'Halim', 'Taufiq',
        'Rifai', 'Firdaus', 'Subakti', 'Syafii', 'Ismail', 'Badawi', 'Basori',
        'Syaifullah', 'Mukhtar', 'Ghufron', 'Hanafi', 'Barakah', 'Hidayat', 'Nugroho',
        'Santoso', 'Prasetyo', 'Wibowo', 'Setiawan', 'Kurniawan', 'Permana', 'Susanto',
        'Ardiansyah', 'Saputra', 'Pratama', 'Ramadhan', 'Purnomo', 'Hartono', 'Wijaya',
    ];

    // ── Nama Putri ───────────────────────────────────────────────────
    private array $firstNamePutri = [
        'Fatimah', 'Aisyah', 'Khadijah', 'Maryam', 'Zainab', 'Ruqayyah', 'Ummu',
        'Hafshah', 'Asma', 'Ramlah', 'Afifah', 'Balqis', 'Chusnul', 'Durrotun',
        'Elfa', 'Faizah', 'Ghina', 'Hanifa', 'Iffah', 'Jihan', 'Kamila', 'Layla',
        'Mazaya', 'Nayla', 'Putri', 'Qonita', 'Rania', 'Shifa', 'Tsuroyya', 'Umayyah',
        'Wahdah', 'Yumna', 'Zulfa', 'Anisah', 'Badriah', 'Chafifah', 'Dina', 'Eca',
        'Fida', 'Hana', 'Inas', 'Jauharoh', 'Khozinah', 'Lutfia', 'Muna', 'Nuha',
        'Pramudita', 'Qurrota', 'Rifa', 'Salamah', 'Ulfah', 'Wardah', 'Yasmin',
        'Zahro', 'Afra', 'Bening', 'Cahya', 'Dewi', 'Erni', 'Fitri', 'Gita',
        'Hesti', 'Indah', 'Julia', 'Kartika', 'Lina', 'Mila', 'Nisa', 'Okta',
        'Puspita', 'Ratna', 'Siti', 'Tuti', 'Ulfa', 'Vina', 'Winda', 'Yuli',
        'Zahra', 'Aini', 'Bunga', 'Cinta', 'Diana', 'Eka', 'Fina', 'Galuh',
    ];

    private array $lastNamePutri = [
        'Az-Zahra', 'Nurhaliza', 'Sari', 'Nabilah', 'Hasanah', 'Putri', 'Kultsum',
        'Salimah', 'Fitriani', 'Azizah', 'Rasyidah', 'Khotimah', 'Nafis', 'Nurina',
        'Ulya', 'Mufidah', 'Zuhda', 'Karimah', 'Lathifah', 'Muniroh', 'Badriyah',
        'Hamidah', 'Sholihah', 'Rahmawati', 'Ilmiyah', 'Fauziyah', 'Najihah',
        'Rosyidah', 'Taslimah', 'Ulwiyah', 'Munawaroh', 'Maulida', 'Khaulah',
        'Maziyyah', 'Fathimah', 'Ilmi', 'Kamaliyah', 'Shofiyah', 'Zakiyah',
        'Rohmah', 'Alya', 'Aini', 'Tsaqifah', 'Barokah', 'Dzikrullah', 'Mawaddah',
        'Hamdiyah', 'Musyarofah', 'Mufliha', 'Nurani', 'Karima', 'Wulandari',
        'Rahayu', 'Susanti', 'Pertiwi', 'Anggraini', 'Kusuma', 'Wahyuni', 'Lestari',
    ];

    private array $tempatLahir = [
        'Surabaya', 'Sidoarjo', 'Gresik', 'Lamongan', 'Malang', 'Mojokerto',
        'Pasuruan', 'Jombang', 'Kediri', 'Blitar', 'Tuban', 'Bojonegoro',
        'Bangkalan', 'Pamekasan', 'Sumenep', 'Lumajang', 'Probolinggo', 'Bondowoso',
        'Jember', 'Banyuwangi', 'Situbondo', 'Nganjuk', 'Ngawi', 'Madiun',
        'Ponorogo', 'Magetan', 'Pacitan', 'Trenggalek', 'Tulungagung',
    ];

    // Counter global agar nama tidak pernah duplikat persis
    private int $counterPutra = 0;
    private int $counterPutri = 0;

    public function run(): void
    {
        $academicYear = '2025/2026';
        $targetIsiMin = 5; // minimal isi per kamar
        $targetIsiMax = 8; // maksimal isi per kamar (tidak harus penuh)

        $orgPutra = Organization::where('slug', 'kepengasuhan-putra')->firstOrFail();
        $orgPutri = Organization::where('slug', 'kepengasuhan-putri')->firstOrFail();

        $kelasPutra = MadrasahKelas::where('academic_year', $academicYear)
            ->where('name', 'like', '%Putra%')
            ->get();

        $kelasPutri = MadrasahKelas::where('academic_year', $academicYear)
            ->where('name', 'like', '%Putri%')
            ->get();

        $totalDitambah = 0;

        // ── Proses setiap kamar putra ────────────────────────────────
        $kamarPutra = Room::whereHas('dormitory', fn($q) => $q->where('gender', 'L'))->get();

        foreach ($kamarPutra as $kamar) {
            $isiSekarang = RoomAssignment::where('room_id', $kamar->id)
                ->where('is_active', true)->count();

            // Target isi: random antara 5–8 (tidak melebihi kapasitas kamar)
            $target = min(rand($targetIsiMin, $targetIsiMax), $kamar->capacity);
            $perluDitambah = max(0, $target - $isiSekarang);

            for ($i = 0; $i < $perluDitambah; $i++) {
                $nama = $this->generateNamaPutra();

                $person = Person::create([
                    'id'          => Str::uuid(),
                    'nik'         => null,
                    'name'        => $nama,
                    'gender'      => 'L',
                    'birth_place' => $this->tempatLahir[array_rand($this->tempatLahir)],
                    'birth_date'  => now()->subYears(rand(12, 22))->subDays(rand(0, 365))->toDateString(),
                    'phone'       => null,
                    'address'     => 'Pondok Pesantren Al-Fithroh, ' . $kamar->dormitory->name,
                    'notes'       => 'Santri mukim ' . $kamar->dormitory->name . ' — ' . $kamar->name,
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
                    'presence_status'      => 'mukim',
                    'presence_status_since'=> now()->startOfYear(),
                ]);

                RoomAssignment::create([
                    'id'         => Str::uuid(),
                    'room_id'    => $kamar->id,
                    'person_id'  => $person->id,
                    'valid_from' => now()->startOfYear()->toDateString(),
                    'valid_until'=> null,
                    'is_active'  => true,
                ]);

                // Enroll ke kelas madrasah putra (acak)
                if ($kelasPutra->isNotEmpty()) {
                    $kelas = $kelasPutra->random();
                    MadrasahEnrollment::firstOrCreate(
                        ['person_id' => $person->id, 'kelas_id' => $kelas->id, 'academic_year' => $academicYear],
                        ['id' => Str::uuid(), 'is_active' => true, 'created_by' => null]
                    );
                }

                $totalDitambah++;
            }
        }

        // ── Proses setiap kamar putri ────────────────────────────────
        $kamarPutri = Room::whereHas('dormitory', fn($q) => $q->where('gender', 'P'))->get();

        foreach ($kamarPutri as $kamar) {
            $isiSekarang = RoomAssignment::where('room_id', $kamar->id)
                ->where('is_active', true)->count();

            $target = min(rand($targetIsiMin, $targetIsiMax), $kamar->capacity);
            $perluDitambah = max(0, $target - $isiSekarang);

            for ($i = 0; $i < $perluDitambah; $i++) {
                $nama = $this->generateNamaPutri();

                $person = Person::create([
                    'id'          => Str::uuid(),
                    'nik'         => null,
                    'name'        => $nama,
                    'gender'      => 'P',
                    'birth_place' => $this->tempatLahir[array_rand($this->tempatLahir)],
                    'birth_date'  => now()->subYears(rand(12, 22))->subDays(rand(0, 365))->toDateString(),
                    'phone'       => null,
                    'address'     => 'Pondok Pesantren Al-Fithroh, ' . $kamar->dormitory->name,
                    'notes'       => 'Santri mukim ' . $kamar->dormitory->name . ' — ' . $kamar->name,
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
                    'presence_status'      => 'mukim',
                    'presence_status_since'=> now()->startOfYear(),
                ]);

                RoomAssignment::create([
                    'id'         => Str::uuid(),
                    'room_id'    => $kamar->id,
                    'person_id'  => $person->id,
                    'valid_from' => now()->startOfYear()->toDateString(),
                    'valid_until'=> null,
                    'is_active'  => true,
                ]);

                // Enroll ke kelas madrasah putri (acak)
                if ($kelasPutri->isNotEmpty()) {
                    $kelas = $kelasPutri->random();
                    MadrasahEnrollment::firstOrCreate(
                        ['person_id' => $person->id, 'kelas_id' => $kelas->id, 'academic_year' => $academicYear],
                        ['id' => Str::uuid(), 'is_active' => true, 'created_by' => null]
                    );
                }

                $totalDitambah++;
            }
        }

        // ── Laporan ──────────────────────────────────────────────────
        $totalMukim = RoomAssignment::where('is_active', true)->count();
        $totalPutraKamar = RoomAssignment::whereHas('room.dormitory', fn($q) => $q->where('gender', 'L'))->count();
        $totalPutriKamar = RoomAssignment::whereHas('room.dormitory', fn($q) => $q->where('gender', 'P'))->count();

        $this->command->info("  ✅ IsiKamarSeeder: {$totalDitambah} santri mukim baru ditambahkan.");
        $this->command->info("  ✅ Total penghuni kamar sekarang: {$totalMukim} orang");
        $this->command->info("     → Putra : {$totalPutraKamar} orang di 17 kamar");
        $this->command->info("     → Putri : {$totalPutriKamar} orang di 17 kamar");
    }

    // ── Generator Nama ───────────────────────────────────────────────

    private function generateNamaPutra(): string
    {
        $first = $this->firstNamePutra[$this->counterPutra % count($this->firstNamePutra)];
        // Geser last name berdasarkan pembagian berbeda agar kombinasi unik
        $last  = $this->lastNamePutra[intdiv($this->counterPutra, count($this->firstNamePutra)) % count($this->lastNamePutra)];

        // Tambahan nama tengah untuk variasi lebih banyak
        $midOptions = ['bin', 'al-', ''];
        $mid = $midOptions[$this->counterPutra % count($midOptions)];

        $this->counterPutra++;

        if ($mid === '') {
            return "{$first} {$last}";
        }

        $extraFirst = $this->firstNamePutra[($this->counterPutra + 7) % count($this->firstNamePutra)];
        return "{$first} {$mid}{$extraFirst}";
    }

    private function generateNamaPutri(): string
    {
        $first = $this->firstNamePutri[$this->counterPutri % count($this->firstNamePutri)];
        $last  = $this->lastNamePutri[intdiv($this->counterPutri, count($this->firstNamePutri)) % count($this->lastNamePutri)];

        $this->counterPutri++;

        return "{$first} {$last}";
    }
}
