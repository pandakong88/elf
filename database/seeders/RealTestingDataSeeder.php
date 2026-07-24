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
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Illuminate\Support\Facades\Schema;

class RealTestingDataSeeder extends Seeder
{
    /**
     * Run database seeds from Real Excel testing files.
     */
    public function run(): void
    {
        $this->command?->info("🧹 Menghapus data santri, kamar, asrama, & kelas lama...");

        // 1. Clean existing dummy data related to santri, rooms, classes (SQLite & MySQL compatible)
        Schema::disableForeignKeyConstraints();
        SantriGuardian::query()->truncate();
        Guardian::query()->truncate();
        RoomAssignment::query()->truncate();
        MadrasahEnrollment::query()->truncate();
        SantriProfile::query()->truncate();
        Room::query()->truncate();
        Dormitory::query()->truncate();
        MadrasahKelas::query()->truncate();

        // Delete PersonRole for santri & wali
        PersonRole::whereIn('role_type', ['santri', 'wali'])->delete();

        // Delete Person records that belong to santri/wali (not staff/users)
        Person::whereDoesntHave('userAccount')
            ->whereDoesntHave('roles', function ($q) {
                $q->where('role_type', 'pengurus');
            })
            ->delete();

        Schema::enableForeignKeyConstraints();

        $this->command?->info("✅ Clearing data lama selesai.");

        // 2. Fetch Base Organizations
        $rootOrg  = Organization::where('slug', 'ponpes-al-fithroh')->first() ?? Organization::first();
        $putraOrg = Organization::where('slug', 'kepengasuhan-putra')->first() ?? $rootOrg;
        $putriOrg = Organization::where('slug', 'kepengasuhan-putri')->first() ?? $rootOrg;

        // 3. Define Excel file paths
        $excelFiles = [
            base_path('data testing/KOMPLEK C-D PA.xlsx'),
            base_path('data testing/KOMPLEK TAHASUS PI.xlsx'),
        ];

        $totalImported = 0;
        $guardiansCache = [];
        $nisCounter = 1001;

        foreach ($excelFiles as $filePath) {
            if (!file_exists($filePath)) {
                $this->command?->error("File tidak ditemukan: {$filePath}");
                continue;
            }

            $this->command?->info("📖 Membaca file: " . basename($filePath));
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (count($rows) <= 1) {
                continue;
            }

            // Remove header row
            array_shift($rows);

            foreach ($rows as $index => $row) {
                $name        = trim((string)($row[0] ?? ''));
                $nik         = trim((string)($row[1] ?? ''));
                $nis         = trim((string)($row[2] ?? ''));
                $genderRaw   = strtoupper(trim((string)($row[3] ?? '')));
                $birthPlace  = trim((string)($row[4] ?? ''));
                $birthDate   = trim((string)($row[5] ?? ''));
                $statusRaw   = strtolower(trim((string)($row[6] ?? '')));
                $dormName    = trim((string)($row[7] ?? ''));
                $roomName    = trim((string)($row[8] ?? ''));
                $kelasName   = trim((string)($row[9] ?? ''));
                $parentName  = trim((string)($row[10] ?? ''));
                $parentPhone = trim((string)($row[11] ?? ''));
                $parentRel   = trim((string)($row[12] ?? 'Ayah'));
                $address     = trim((string)($row[13] ?? ''));
                $schoolName  = trim((string)($row[14] ?? ''));

                if (empty($name)) {
                    continue;
                }

                // Determine Gender
                $gender = in_array($genderRaw, ['P', 'PEREMPUAN', 'PUTRI']) ? 'P' : 'L';
                $org = $gender === 'L' ? $putraOrg : $putriOrg;

                // Determine Status
                $presenceStatus = in_array($statusRaw, ['laju', 'non-mukim']) ? 'laju' : 'mukim';

                // Generate NIS if empty
                if (empty($nis)) {
                    $nis = date('Y') . sprintf('%04d', $nisCounter++);
                }

                // Generate NIK if empty
                if (empty($nik)) {
                    $nik = ($gender === 'L' ? '3578' : '3579') . sprintf('%012d', mt_rand(100000000000, 999999999999));
                }

                // 4. Create/Get Dormitory & Room if Mukim
                $dorm = null;
                $room = null;
                if ($presenceStatus === 'mukim' && !empty($dormName)) {
                    $dorm = Dormitory::firstOrCreate([
                        'name' => $dormName,
                    ], [
                        'id'              => Str::uuid()->toString(),
                        'organization_id' => $org->id,
                        'gender'          => $gender,
                        'is_active'       => true,
                    ]);

                    if (!empty($roomName)) {
                        $room = Room::firstOrCreate([
                            'dormitory_id' => $dorm->id,
                            'name'         => $roomName,
                        ], [
                            'id'        => Str::uuid()->toString(),
                            'capacity'  => 15,
                            'is_active' => true,
                        ]);
                    }
                }

                // 5. Create/Get Madrasah Kelas
                $kelas = null;
                if (!empty($kelasName)) {
                    $jenjang = 'ula';
                    $lowerName = strtolower($kelasName);
                    if (str_contains($lowerName, 'wustho')) {
                        $jenjang = 'wustho';
                    } elseif (str_contains($lowerName, 'ulya') || str_contains($lowerName, 'tahasus')) {
                        $jenjang = 'ulya';
                    }

                    $kelas = MadrasahKelas::firstOrCreate([
                        'name' => $kelasName,
                    ], [
                        'id'            => Str::uuid()->toString(),
                        'jenjang'       => $jenjang,
                        'academic_year' => '2025/2026',
                        'is_active'     => true,
                    ]);
                }

                // 6. Create/Get Guardian (Wali)
                $guardianKey = strtolower(trim($parentName . '_' . $parentPhone));
                if (isset($guardiansCache[$guardianKey])) {
                    $guardian = $guardiansCache[$guardianKey];
                } else {
                    $guardian = Guardian::create([
                        'id'            => Str::uuid()->toString(),
                        'name'          => !empty($parentName) ? $parentName : 'Wali dari ' . $name,
                        'gender'        => strtolower($parentRel) === 'ibu' ? 'P' : 'L',
                        'phone_primary' => !empty($parentPhone) ? $parentPhone : '081234567890',
                        'address'       => $address ?: 'Pondok Pesantren Al-Fithroh',
                        'is_active'     => true,
                    ]);
                    $guardiansCache[$guardianKey] = $guardian;
                }

                // 7. Create Santri (Person)
                $santriPerson = Person::create([
                    'id'          => Str::uuid()->toString(),
                    'nik'         => $nik,
                    'name'        => $name,
                    'gender'      => $gender,
                    'birth_place' => $birthPlace ?: ($gender === 'L' ? 'Surabaya' : 'Sidoarjo'),
                    'birth_date'  => !empty($birthDate) && strtotime($birthDate) ? date('Y-m-d', strtotime($birthDate)) : '2008-01-01',
                    'phone'       => null,
                    'address'     => $address ?: 'Pondok Pesantren Al-Fithroh',
                    'notes'       => 'Santri Aktif (Impor Real Testing)',
                ]);

                // 8. Create Santri Guardian Pivot
                SantriGuardian::create([
                    'id'           => Str::uuid()->toString(),
                    'person_id'    => $santriPerson->id,
                    'guardian_id'  => $guardian->id,
                    'relationship' => strtolower($parentRel) === 'ibu' ? 'ibu_kandung' : (strtolower($parentRel) === 'ayah' ? 'ayah_kandung' : 'wali_resmi'),
                    'is_primary'   => true,
                ]);

                // 9. Create Santri PersonRole
                PersonRole::create([
                    'id'                => Str::uuid()->toString(),
                    'person_id'         => $santriPerson->id,
                    'organization_id'   => $org->id,
                    'role_type'         => 'santri',
                    'enrollment_status' => 'aktif',
                    'presence_status'   => $presenceStatus,
                    'valid_from'        => now()->startOfYear()->toDateString(),
                    'is_active'         => true,
                ]);

                // 10. Create SantriProfile
                SantriProfile::create([
                    'id'              => Str::uuid()->toString(),
                    'person_id'       => $santriPerson->id,
                    'father_name'     => strtolower($parentRel) === 'ayah' ? $parentName : null,
                    'mother_name'     => strtolower($parentRel) === 'ibu' ? $parentName : null,
                    'father_phone'    => $parentPhone ?: null,
                    'school_name'     => $schoolName ?: null,
                    'additional_info' => ['nis' => $nis],
                ]);

                // 11. Create RoomAssignment if Mukim & Room assigned
                if ($presenceStatus === 'mukim' && $room) {
                    RoomAssignment::create([
                        'id'         => Str::uuid()->toString(),
                        'person_id'  => $santriPerson->id,
                        'room_id'    => $room->id,
                        'valid_from' => now()->startOfYear()->toDateString(),
                        'is_active'  => true,
                    ]);
                }

                // 12. Create MadrasahEnrollment if Kelas assigned
                if ($kelas) {
                    MadrasahEnrollment::create([
                        'id'            => Str::uuid()->toString(),
                        'person_id'     => $santriPerson->id,
                        'kelas_id'      => $kelas->id,
                        'academic_year' => '2025/2026',
                        'is_active'     => true,
                    ]);
                }

                $totalImported++;
            }
        }

        $this->command?->info("🎉 SELESAI! Impor data testing real berhasil:");
        $this->command?->info("   → Total Santri Terimpor : {$totalImported}");
        $this->command?->info("   → Total Komplek Asrama : " . Dormitory::count());
        $this->command?->info("   → Total Kamar Asrama   : " . Room::count());
        $this->command?->info("   → Total Kelas Madrasah : " . MadrasahKelas::count());
    }
}
