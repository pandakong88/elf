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
        $root = Organization::firstOrCreate(
            ['slug' => 'ponpes-al-fithroh'],
            [
                'id'          => Str::uuid(),
                'parent_id'   => null,
                'name'        => 'Pondok Pesantren Al-Fithroh',
                'type'        => 'pondok',
                'description' => 'Lembaga induk Pondok Pesantren Al-Fithroh',
                'is_active'   => true,
            ]
        );

        // ─── Unit Putra ───
        $putra = Organization::firstOrCreate(
            ['slug' => 'kepengasuhan-putra'],
            [
                'id'          => Str::uuid(),
                'parent_id'   => $root->id,
                'name'        => 'Kepengurusan Putra',
                'type'        => 'unit',
                'description' => 'Unit kepengurusan santri putra',
                'is_active'   => true,
            ]
        );

        // ─── Unit Putri ───
        $putri = Organization::firstOrCreate(
            ['slug' => 'kepengasuhan-putri'],
            [
                'id'          => Str::uuid(),
                'parent_id'   => $root->id,
                'name'        => 'Kepengurusan Putri',
                'type'        => 'unit',
                'description' => 'Unit kepengurusan santri putri',
                'is_active'   => true,
            ]
        );

        // ─── Madrasah Diniyah ───
        Organization::firstOrCreate(
            ['slug' => 'madrasah-diniyah'],
            [
                'id'          => Str::uuid(),
                'parent_id'   => $root->id,
                'name'        => 'Madrasah Diniyah',
                'type'        => 'madrasah',
                'description' => 'Lembaga pendidikan diniyah pondok pesantren',
                'is_active'   => true,
            ]
        );

        // ─── Koperasi Pondok ───
        Organization::firstOrCreate(
            ['slug' => 'koperasi-pondok'],
            [
                'id'          => Str::uuid(),
                'parent_id'   => $root->id,
                'name'        => 'Koperasi Pondok',
                'type'        => 'koperasi',
                'description' => 'Unit usaha dan mart pondok',
                'is_active'   => true,
            ]
        );

        // ─── Tahfidz Al-Qur'an ───
        Organization::firstOrCreate(
            ['slug' => 'tahfidz-al-quran'],
            [
                'id'          => Str::uuid(),
                'parent_id'   => $root->id,
                'name'        => "Tahfidz Al-Qur'an",
                'type'        => 'tahfidz',
                'description' => "Program khusus hafalan Al-Qur'an",
                'is_active'   => true,
            ]
        );
    }
}
