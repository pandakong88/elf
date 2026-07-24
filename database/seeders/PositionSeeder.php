<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $units = Organization::all()->keyBy('slug');

        $positions = [
            // ─── Pondok (root) ───
            'ponpes-al-fithroh' => [
                ['name' => 'Pengasuh', 'level' => 'structural'],
                ['name' => 'Wakil Pengasuh', 'level' => 'structural'],
                ['name' => 'Bendahara Pondok', 'level' => 'structural'],
                ['name' => 'Sekretaris Pondok', 'level' => 'structural'],
            ],
            // ─── Kepengasuhan Putra ───
            'kepengasuhan-putra' => [
                ['name' => 'Kepala Kepengasuhan Putra', 'level' => 'structural'],
                ['name' => 'Musyrif', 'level' => 'fungsional'],
                ['name' => 'Ketua OSIS/OPPSI Putra', 'level' => 'fungsional'],
            ],
            // ─── Kepengasuhan Putri ───
            'kepengasuhan-putri' => [
                ['name' => 'Kepala Kepengasuhan Putri', 'level' => 'structural'],
                ['name' => 'Musyrifah', 'level' => 'fungsional'],
                ['name' => 'Ketua OSIS/OPPSI Putri', 'level' => 'fungsional'],
            ],
            // ─── Madrasah Diniyah ───
            'madrasah-diniyah' => [
                ['name' => 'Kepala Madrasah', 'level' => 'structural'],
                ['name' => 'Wakil Kepala Madrasah', 'level' => 'structural'],
                ['name' => 'Guru', 'level' => 'fungsional'],
                ['name' => 'Wali Kelas', 'level' => 'fungsional'],
            ],
            // ─── Koperasi ───
            'koperasi-al-fithroh' => [
                ['name' => 'Ketua Koperasi', 'level' => 'structural'],
                ['name' => 'Kasir', 'level' => 'fungsional'],
                ['name' => 'Petugas Stok', 'level' => 'fungsional'],
            ],
        ];

        $total = 0;
        foreach ($positions as $slug => $items) {
            $org = $units->get($slug);
            if (! $org) {
                $this->command->warn("⚠️  Organisasi '{$slug}' tidak ditemukan, skip.");
                continue;
            }

            foreach ($items as $item) {
                Position::create([
                    'id'              => Str::uuid(),
                    'organization_id' => $org->id,
                    'name'            => $item['name'],
                    'level'           => $item['level'],
                    'is_active'       => true,
                ]);
                $total++;
            }
        }

        $this->command->info("✅ PositionSeeder: {$total} jabatan berhasil di-seed.");
    }
}
