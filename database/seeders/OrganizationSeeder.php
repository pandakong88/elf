<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Root: Pondok Pesantren Al-Fithroh ───
        $root = Organization::create([
            'id'          => Str::uuid(),
            'parent_id'   => null,
            'name'        => 'Pondok Pesantren Al-Fithroh',
            'slug'        => 'ponpes-al-fithroh',
            'type'        => 'pondok',
            'description' => 'Lembaga induk Pondok Pesantren Al-Fithroh',
            'is_active'   => true,
        ]);

        // ─── Unit Putra ───
        $putra = Organization::create([
            'id'          => Str::uuid(),
            'parent_id'   => $root->id,
            'name'        => 'Kepengurusan Putra',
            'slug'        => 'kepengasuhan-putra',
            'type'        => 'unit',
            'description' => 'Unit kepengurusan santri putra',
            'is_active'   => true,
        ]);

        // ─── Unit Putri ───
        $putri = Organization::create([
            'id'          => Str::uuid(),
            'parent_id'   => $root->id,
            'name'        => 'Kepengurusan Putri',
            'slug'        => 'kepengasuhan-putri',
            'type'        => 'unit',
            'description' => 'Unit kepengurusan santri putri',
            'is_active'   => true,
        ]);

        // ─── Madrasah Diniyah ───
        Organization::create([
            'id'          => Str::uuid(),
            'parent_id'   => $root->id,
            'name'        => 'Madrasah Diniyah',
            'slug'        => 'madrasah-diniyah',
            'type'        => 'madrasah',
            'description' => 'Lembaga pendidikan diniyah pondok pesantren',
            'is_active'   => true,
        ]);

        // ─── Koperasi Pondok ───
        Organization::create([
            'id'          => Str::uuid(),
            'parent_id'   => $root->id,
            'name'        => 'Koperasi Al-Fithroh',
            'slug'        => 'koperasi-al-fithroh',
            'type'        => 'koperasi',
            'description' => 'Koperasi pesantren untuk kebutuhan santri dan pegawai',
            'is_active'   => true,
        ]);

        // ─── Tahfidz (di bawah Putra dan Putri) ───
        Organization::create([
            'id'          => Str::uuid(),
            'parent_id'   => $putra->id,
            'name'        => 'Program Tahfidz Putra',
            'slug'        => 'tahfidz-putra',
            'type'        => 'tahfidz',
            'description' => 'Program hafalan Al-Quran santri putra',
            'is_active'   => true,
        ]);

        Organization::create([
            'id'          => Str::uuid(),
            'parent_id'   => $putri->id,
            'name'        => 'Program Tahfidz Putri',
            'slug'        => 'tahfidz-putri',
            'type'        => 'tahfidz',
            'description' => 'Program hafalan Al-Quran santri putri',
            'is_active'   => true,
        ]);

        $this->command->info('✅ OrganizationSeeder: ' . Organization::count() . ' unit berhasil di-seed.');
    }
}
