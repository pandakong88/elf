<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Guardian;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Kepengasuhan\Models\SantriGuardian;
use App\Modules\Kepengasuhan\Models\SantriProfile;
use App\Modules\Madrasah\Models\MadrasahEnrollment;
use App\Modules\Madrasah\Models\MadrasahKelas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class RealTestingDataSeeder extends Seeder
{
    /**
     * Run database seeds from 3 Real Excel setup files.
     */
    public function run(): void
    {
        $this->command?->info("🧹 Menghapus data santri, kamar, asrama, & kelas lama...");

        Schema::disableForeignKeyConstraints();
        DB::table('bill_payments')->truncate();
        DB::table('bills')->truncate();
        DB::table('billing_exceptions')->truncate();
        DB::table('billing_configurations')->truncate();
        SantriGuardian::query()->truncate();
        Guardian::query()->truncate();
        RoomAssignment::query()->truncate();
        MadrasahEnrollment::query()->truncate();
        SantriProfile::query()->truncate();
        Room::query()->truncate();
        Dormitory::query()->truncate();
        MadrasahKelas::query()->truncate();

        PersonRole::whereIn('role_type', ['santri', 'wali'])->delete();
        Person::whereDoesntHave('userAccount')
            ->whereDoesntHave('roles', function ($q) {
                $q->where('role_type', 'pengurus');
            })
            ->delete();

        Schema::enableForeignKeyConstraints();

        $this->command?->info("✅ Clear data lama selesai.");

        // Fetch Organizations
        $rootOrg  = Organization::where('slug', 'ponpes-al-fithroh')->first() ?? Organization::first();
        $putraOrg = Organization::where('slug', 'kepengasuhan-putra')->first() ?? $rootOrg;
        $putriOrg = Organization::where('slug', 'kepengasuhan-putri')->first() ?? $rootOrg;

        // 1. Process Asrama & Kamar (File 1)
        $file1Paths = [
            base_path('Setup_1_Asrama_Putra_v2.xlsx'),
            base_path('data testing/Hasil_Setup_Asrama_KomplekCD.xlsx'),
        ];

        foreach ($file1Paths as $filePath) {
            if (!file_exists($filePath)) continue;

            $this->command?->info("📖 Impor Asrama & Kamar dari: " . basename($filePath));
            $ss = IOFactory::load($filePath);
            $sheet = $ss->getActiveSheet();
            $rows = $sheet->toArray();
            array_shift($rows); // Skip header

            foreach ($rows as $row) {
                $dormName = trim((string)($row[0] ?? ''));
                $gender   = strtoupper(trim((string)($row[1] ?? 'L')));
                $roomName = trim((string)($row[2] ?? ''));
                $capacity = (int)($row[3] ?? 10);

                if (!$dormName || !$roomName) continue;

                $dorm = Dormitory::firstOrCreate(
                    ['name' => $dormName],
                    ['gender' => in_array($gender, ['L', 'P']) ? $gender : 'L']
                );

                Room::firstOrCreate(
                    ['dormitory_id' => $dorm->id, 'name' => $roomName],
                    ['capacity' => $capacity > 0 ? $capacity : 10]
                );
            }
        }

        // 2. Process Kelas Madrasah (File 2)
        $file2Paths = [
            base_path('Setup_2_Kelas_Putra_v2.xlsx'),
            base_path('data testing/Hasil_Setup_Kelas_KomplekCD.xlsx'),
        ];

        foreach ($file2Paths as $filePath) {
            if (!file_exists($filePath)) continue;

            $this->command?->info("📖 Impor Kelas Madrasah dari: " . basename($filePath));
            $ss = IOFactory::load($filePath);
            $sheet = $ss->getActiveSheet();
            $rows = $sheet->toArray();
            array_shift($rows); // Skip header

            foreach ($rows as $row) {
                $className = trim((string)($row[0] ?? ''));
                $jenjangRaw = strtolower(trim((string)($row[1] ?? 'ula')));
                
                if (!$className) continue;

                $jenjang = match(true) {
                    str_contains($jenjangRaw, 'wusth') => 'wustho',
                    str_contains($jenjangRaw, 'uly') => 'ulya',
                    default => 'ula'
                };

                MadrasahKelas::firstOrCreate(
                    ['name' => $className],
                    ['jenjang' => $jenjang, 'academic_year' => '2025/2026', 'is_active' => true]
                );
            }
        }

        // 3. Process Santri & Wali (File 3)
        $file3Paths = [
            base_path('Setup_3_Santri_Wali_Putra_v2.xlsx'),
            base_path('data testing/Hasil_Setup_Santri_Wali_KomplekCD.xlsx'),
        ];

        $importedSantri = 0;
        $importedGuardians = 0;
        $nisCounter = 20261001;
        $guardiansCache = [];

        foreach ($file3Paths as $filePath) {
            if (!file_exists($filePath)) continue;

            $this->command?->info("📖 Impor Santri & Wali dari: " . basename($filePath));
            $ss = IOFactory::load($filePath);
            $sheet = $ss->getActiveSheet();
            $rows = $sheet->toArray();
            array_shift($rows); // Skip header

            foreach ($rows as $row) {
                $santriName   = trim((string)($row[0] ?? ''));
                $nik          = trim((string)($row[1] ?? ''));
                $nis          = trim((string)($row[2] ?? ''));
                $genderRaw    = strtoupper(trim((string)($row[3] ?? 'L')));
                $birthPlace   = trim((string)($row[4] ?? ''));
                $birthDate    = trim((string)($row[5] ?? ''));
                $statusRaw    = strtolower(trim((string)($row[6] ?? 'mukim')));
                $dormName     = trim((string)($row[7] ?? ''));
                $roomName     = trim((string)($row[8] ?? ''));
                $kelasName    = trim((string)($row[9] ?? ''));
                $guardianName = trim((string)($row[10] ?? ''));
                $guardianPhone= trim((string)($row[11] ?? ''));
                $relationship = trim((string)($row[12] ?? 'Ayah'));
                $address      = trim((string)($row[13] ?? ''));
                $formalSchool = trim((string)($row[14] ?? ''));

                if (!$santriName) continue;

                $gender = in_array($genderRaw, ['L', 'P']) ? $genderRaw : 'L';
                $residence = str_contains($statusRaw, 'laju') ? 'laju' : 'mukim';
                if (!$nis) {
                    $nis = (string)($nisCounter++);
                }

                $person = Person::create([
                    'id' => Str::uuid(),
                    'name' => $santriName,
                    'gender' => $gender,
                    'birth_place' => $birthPlace ?: null,
                    'birth_date' => (!empty($birthDate) && strtotime($birthDate)) ? date('Y-m-d', strtotime($birthDate)) : null,
                    'address' => $address ?: null,
                ]);

                $orgId = ($gender === 'P' ? $putriOrg?->id : $putraOrg?->id) ?? $rootOrg?->id;

                PersonRole::create([
                    'person_id' => $person->id,
                    'organization_id' => $orgId,
                    'role_type' => 'santri',
                    'enrollment_status' => 'aktif',
                    'presence_status' => $residence,
                    'valid_from' => now()->toDateString(),
                ]);

                SantriProfile::create([
                    'person_id' => $person->id,
                    'additional_info' => [
                        'nis' => $nis,
                        'nik' => $nik ?: null,
                        'residence_status' => $residence,
                        'formal_school' => $formalSchool ?: null,
                    ],
                ]);

                if (!empty($guardianName)) {
                    $cacheKey = strtolower($guardianName . '_' . $guardianPhone);
                    if (isset($guardiansCache[$cacheKey])) {
                        $guardian = $guardiansCache[$cacheKey];
                    } else {
                        $guardian = Guardian::create([
                            'id' => Str::uuid(),
                            'name' => $guardianName,
                            'phone_primary' => $guardianPhone ?: null,
                            'address' => $address ?: null,
                        ]);
                        $guardiansCache[$cacheKey] = $guardian;
                        $importedGuardians++;
                    }

                    SantriGuardian::create([
                        'person_id' => $person->id,
                        'guardian_id' => $guardian->id,
                        'relationship' => $relationship ?: 'Ayah',
                        'is_primary' => true,
                    ]);
                }

                if ($residence === 'mukim' && !empty($dormName) && !empty($roomName)) {
                    $dorm = Dormitory::where('name', 'like', '%' . $dormName . '%')->first();
                    if ($dorm) {
                        $room = Room::where('dormitory_id', $dorm->id)->where('name', 'like', '%' . $roomName . '%')->first();
                        if ($room) {
                            RoomAssignment::create([
                                'person_id' => $person->id,
                                'room_id' => $room->id,
                                'valid_from' => now()->toDateString(),
                                'is_active' => true,
                            ]);
                        }
                    }
                }

                if (!empty($kelasName)) {
                    $kelas = MadrasahKelas::where('name', 'like', '%' . $kelasName . '%')->first();
                    if ($kelas) {
                        MadrasahEnrollment::create([
                            'person_id' => $person->id,
                            'kelas_id' => $kelas->id,
                            'academic_year' => '2025/2026',
                            'is_active' => true,
                        ]);
                    }
                }

                $importedSantri++;
            }
        }

        $this->command?->info("🎉 Impor Data Excel Selesai!");
        $this->command?->line("✔ Total Asrama: " . Dormitory::count());
        $this->command?->line("✔ Total Kamar: " . Room::count());
        $this->command?->line("✔ Total Kelas Madrasah: " . MadrasahKelas::count());
        $this->command?->line("✔ Total Santri Impor: " . $importedSantri);
        $this->command?->line("✔ Total Wali Impor: " . $importedGuardians);
        $this->command?->line("✔ Tarif & Tagihan: KOSONG (Siap dikonfigurasi manual).");
    }
}
