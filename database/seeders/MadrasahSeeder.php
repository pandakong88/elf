<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Madrasah\Models\MadrasahEnrollment;
use App\Modules\Madrasah\Models\MadrasahKelas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MadrasahSeeder extends Seeder
{
    /**
     * Struktur kelas di pondok ini:
     *
     * PUTRA (7 kelas):
     *   - Awaliyah 1, 2, 3  → jenjang: ula
     *   - Wustho 1, 2        → jenjang: wustho
     *   - Ulya 1, 2          → jenjang: ulya
     *
     * PUTRI (6 kelas):
     *   - Awaliyah 1, 2, 3  → jenjang: ula
     *   - Wustho 1, 2        → jenjang: wustho
     *   - Ulya 1             → jenjang: ulya
     */
    public function run(): void
    {
        $academicYear = '2025/2026';

        // ────────────────────────────────────────────────────
        // 1. BUAT KELAS PUTRA (7 kelas)
        // ────────────────────────────────────────────────────
        $kelasPutra = collect([
            // Jenjang Ula (Awaliyah) — 3 kelas
            ['name' => 'Awaliyah 1 Putra',  'jenjang' => 'ula'],
            ['name' => 'Awaliyah 2 Putra',  'jenjang' => 'ula'],
            ['name' => 'Awaliyah 3 Putra',  'jenjang' => 'ula'],
            // Jenjang Wustho — 2 kelas
            ['name' => 'Wustho 1 Putra',    'jenjang' => 'wustho'],
            ['name' => 'Wustho 2 Putra',    'jenjang' => 'wustho'],
            // Jenjang Ulya — 2 kelas
            ['name' => 'Ulya 1 Putra',      'jenjang' => 'ulya'],
            ['name' => 'Ulya 2 Putra',      'jenjang' => 'ulya'],
        ])->map(fn($k) => MadrasahKelas::firstOrCreate(
            ['name' => $k['name'], 'academic_year' => $academicYear],
            [
                'id'            => Str::uuid(),
                'jenjang'       => $k['jenjang'],
                'academic_year' => $academicYear,
                'wali_kelas_id' => null,
                'is_active'     => true,
                'created_by'    => null,
            ]
        ));

        // ────────────────────────────────────────────────────
        // 2. BUAT KELAS PUTRI (6 kelas)
        // ────────────────────────────────────────────────────
        $kelasPutri = collect([
            // Jenjang Ula (Awaliyah) — 3 kelas
            ['name' => 'Awaliyah 1 Putri',  'jenjang' => 'ula'],
            ['name' => 'Awaliyah 2 Putri',  'jenjang' => 'ula'],
            ['name' => 'Awaliyah 3 Putri',  'jenjang' => 'ula'],
            // Jenjang Wustho — 2 kelas
            ['name' => 'Wustho 1 Putri',    'jenjang' => 'wustho'],
            ['name' => 'Wustho 2 Putri',    'jenjang' => 'wustho'],
            // Jenjang Ulya — 1 kelas (putri hanya sampai Ulya 1)
            ['name' => 'Ulya 1 Putri',      'jenjang' => 'ulya'],
        ])->map(fn($k) => MadrasahKelas::firstOrCreate(
            ['name' => $k['name'], 'academic_year' => $academicYear],
            [
                'id'            => Str::uuid(),
                'jenjang'       => $k['jenjang'],
                'academic_year' => $academicYear,
                'wali_kelas_id' => null,
                'is_active'     => true,
                'created_by'    => null,
            ]
        ));

        $this->command->info('✅ MadrasahSeeder: ' . ($kelasPutra->count() + $kelasPutri->count()) . ' kelas berhasil dibuat.');
        $this->command->info('   → Putra : ' . $kelasPutra->count() . ' kelas (Awaliyah 1-3, Wustho 1-2, Ulya 1-2)');
        $this->command->info('   → Putri : ' . $kelasPutri->count() . ' kelas (Awaliyah 1-3, Wustho 1-2, Ulya 1)');

        // ────────────────────────────────────────────────────
        // 3. ENROLLMENT: Distribusikan santri ke kelas
        //    Santri diambil dari DB berdasarkan role_type = 'santri'
        //    kemudian dibagi merata ke kelas sesuai gender.
        // ────────────────────────────────────────────────────
        $santriPutra = Person::whereHas('roles', fn($q) =>
            $q->where('role_type', 'santri')->where('is_active', true)
        )->where('gender', 'L')->inRandomOrder()->get();

        $santriPutri = Person::whereHas('roles', fn($q) =>
            $q->where('role_type', 'santri')->where('is_active', true)
        )->where('gender', 'P')->inRandomOrder()->get();

        $enrolledCount = 0;

        // Distribusi round-robin: setiap santri mendapat 1 kelas
        foreach ($santriPutra as $index => $santri) {
            // Pilih kelas berdasarkan giliran (modulo jumlah kelas putra)
            $kelas = $kelasPutra[$index % $kelasPutra->count()];

            MadrasahEnrollment::firstOrCreate(
                [
                    'person_id'     => $santri->id,
                    'kelas_id'      => $kelas->id,
                    'academic_year' => $academicYear,
                ],
                [
                    'id'         => Str::uuid(),
                    'is_active'  => true,
                    'created_by' => null,
                ]
            );
            $enrolledCount++;
        }

        foreach ($santriPutri as $index => $santri) {
            $kelas = $kelasPutri[$index % $kelasPutri->count()];

            MadrasahEnrollment::firstOrCreate(
                [
                    'person_id'     => $santri->id,
                    'kelas_id'      => $kelas->id,
                    'academic_year' => $academicYear,
                ],
                [
                    'id'         => Str::uuid(),
                    'is_active'  => true,
                    'created_by' => null,
                ]
            );
            $enrolledCount++;
        }

        $this->command->info("✅ MadrasahSeeder: {$enrolledCount} santri berhasil di-enroll ke kelas madrasah.");
        $this->command->info('   → Putra : ' . $santriPutra->count() . ' santri → ' . $kelasPutra->count() . ' kelas');
        $this->command->info('   → Putri : ' . $santriPutri->count() . ' santri → ' . $kelasPutri->count() . ' kelas');
    }
}
