<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DummySantriSeeder extends Seeder
{
    private array $namaPutra = [
        'Ahmad Fauzi', 'Muhammad Rizki', 'Abdullah Hakim', 'Umar Faruq', 'Zaid Al-Amin',
        'Ibrahim Sholeh', 'Hasan Mubarak', 'Husain Barokah', 'Yusuf Abdillah', 'Khalid Mukhlis',
    ];

    private array $namaPutri = [
        'Fatimah Az-Zahra', 'Aisyah Nurhaliza', 'Khadijah Sari', 'Maryam Nabilah', 'Zainab Hasanah',
        'Ruqayyah Putri', 'Ummu Kultsum', 'Hafshah Salimah', 'Asma Fitriani', 'Ramlah Azizah',
    ];

    private array $tempatLahir = [
        'Surabaya', 'Sidoarjo', 'Gresik', 'Lamongan', 'Malang',
        'Mojokerto', 'Pasuruan', 'Jombang', 'Kediri', 'Blitar',
    ];

    public function run(): void
    {
        $putraUnit = Organization::where('slug', 'kepengasuhan-putra')->firstOrFail();
        $putriUnit = Organization::where('slug', 'kepengasuhan-putri')->firstOrFail();

        $created = 0;

        // ─── 10 Santri Putra ───
        foreach ($this->namaPutra as $i => $nama) {
            $person = Person::create([
                'id'          => Str::uuid(),
                'nik'         => null, // santri biasanya belum punya NIK
                'name'        => $nama,
                'gender'      => 'L',
                'birth_place' => $this->tempatLahir[$i],
                'birth_date'  => now()->subYears(rand(14, 20))->subDays(rand(0, 365))->toDateString(),
                'phone'       => null,
                'address'     => 'Jl. Pondok Pesantren No. ' . ($i + 1) . ', Surabaya',
                'notes'       => 'Santri dummy putra #' . ($i + 1),
            ]);

            PersonRole::create([
                'id'              => Str::uuid(),
                'person_id'       => $person->id,
                'organization_id' => $putraUnit->id,
                'role_type'       => 'santri',
                'valid_from'      => now()->startOfYear()->toDateString(),
                'valid_until'     => null,
                'is_active'       => true,
            ]);

            $created++;
        }

        // ─── 10 Santri Putri ───
        foreach ($this->namaPutri as $i => $nama) {
            $person = Person::create([
                'id'          => Str::uuid(),
                'nik'         => null,
                'name'        => $nama,
                'gender'      => 'P',
                'birth_place' => $this->tempatLahir[$i],
                'birth_date'  => now()->subYears(rand(13, 19))->subDays(rand(0, 365))->toDateString(),
                'phone'       => null,
                'address'     => 'Jl. Pondok Pesantren No. ' . ($i + 101) . ', Surabaya',
                'notes'       => 'Santri dummy putri #' . ($i + 1),
            ]);

            PersonRole::create([
                'id'              => Str::uuid(),
                'person_id'       => $person->id,
                'organization_id' => $putriUnit->id,
                'role_type'       => 'santri',
                'valid_from'      => now()->startOfYear()->toDateString(),
                'valid_until'     => null,
                'is_active'       => true,
            ]);

            $created++;
        }

        $this->command->info("✅ DummySantriSeeder: {$created} santri dummy berhasil di-seed.");
    }
}
