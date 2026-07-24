<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Keuangan\Models\BillingConfiguration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BillingConfigurationSeeder extends Seeder
{
    /**
     * Daftar Konfigurasi Tarif Aktif (T.A. 2025/2026):
     *
     * 1. Syahriah Pondok    → Rp 35.000 / bulan   → target: semua santri mukim
     * 2. Syahriah Madrasah  → Rp 150.000 / semester → target: semua santri (mukim + laju)
     * 3. Kas Komplek        → Rp 5.000 / bulan     → target: per komplek (8 konfigurasi, masing-masing dormitory)
     * 4. Kebersihan         → Rp 20.000 / semester → target: semua santri mukim
     */
    public function run(): void
    {
        // Hapus konfigurasi lama agar tidak duplikat
        BillingConfiguration::query()->delete();

        // Ambil user pertama (admin) sebagai created_by
        $admin = User::first();
        if (!$admin) {
            $this->command->error('❌ Tidak ada user di database! Jalankan RolePermissionSeeder terlebih dahulu.');
            return;
        }

        $effectiveFrom = now()->startOfYear()->toDateString(); // 2026-01-01

        $created = 0;

        // ─────────────────────────────────────────────────────────────────
        // 1. SYAHRIAH PONDOK — Rp 35.000 / bulan
        //    Target: semua santri mukim (presence_status = 'mukim')
        //    Dikelola oleh: bendahara-pusat
        // ─────────────────────────────────────────────────────────────────
        BillingConfiguration::create([
            'id'                 => Str::uuid(),
            'type'               => 'syahriah_pondok',
            'label'              => 'Syahriah Pondok Bulanan',
            'amount'             => 35000,
            'dormitory_id'       => null,       // berlaku semua komplek
            'effective_from'     => $effectiveFrom,
            'interval'           => 'monthly',
            'manager_role'       => 'bendahara-pusat',
            'manager_ids'        => null,
            'target_type'        => 'all',      // semua santri mukim
            'target_filters'     => null,
            'can_be_installment' => false,
            'is_active'          => true,
            'created_by'         => $admin->id,
        ]);
        $created++;

        // ─────────────────────────────────────────────────────────────────
        // 2. SYAHRIAH MADRASAH — Rp 150.000 / semester
        //    Target: semua santri (mukim + laju)
        //    Dikelola oleh: bendahara-madrasah
        // ─────────────────────────────────────────────────────────────────
        BillingConfiguration::create([
            'id'                 => Str::uuid(),
            'type'               => 'syahriah_madrasah',
            'label'              => 'Syahriah Madrasah Per Semester',
            'amount'             => 150000,
            'dormitory_id'       => null,
            'effective_from'     => $effectiveFrom,
            'interval'           => 'semester',
            'manager_role'       => 'bendahara-madrasah',
            'manager_ids'        => null,
            'target_type'        => 'all',      // mukim + laju
            'target_filters'     => null,
            'can_be_installment' => true,       // boleh dicicil
            'is_active'          => true,
            'created_by'         => $admin->id,
        ]);
        $created++;

        // ─────────────────────────────────────────────────────────────────
        // 3. KEBERSIHAN — Rp 20.000 / semester
        //    Target: semua santri mukim
        //    Dikelola oleh: bendahara-pusat
        // ─────────────────────────────────────────────────────────────────
        BillingConfiguration::create([
            'id'                 => Str::uuid(),
            'type'               => 'kebersihan',
            'label'              => 'Iuran Kebersihan Per Semester',
            'amount'             => 20000,
            'dormitory_id'       => null,
            'effective_from'     => $effectiveFrom,
            'interval'           => 'semester',
            'manager_role'       => 'bendahara-pusat',
            'manager_ids'        => null,
            'target_type'        => 'all',
            'target_filters'     => null,
            'can_be_installment' => false,
            'is_active'          => true,
            'created_by'         => $admin->id,
        ]);
        $created++;

        // ─────────────────────────────────────────────────────────────────
        // 4. KAS KOMPLEK — Rp 5.000 / bulan
        //    SATU konfigurasi per dormitory (8 komplek: A-D Putra + A-D Putri)
        //    Target: santri di dormitory tersebut (target_type = 'dormitory')
        //    Dikelola oleh: bendahara-komplek masing-masing
        // ─────────────────────────────────────────────────────────────────
        $dormitories = Dormitory::orderBy('gender')->orderBy('name')->get();

        if ($dormitories->isEmpty()) {
            $this->command->warn('  ⚠  Tidak ada dormitory ditemukan! Pastikan KompleKamarSeeder sudah dijalankan.');
        }

        foreach ($dormitories as $dorm) {
            $genderLabel = $dorm->gender === 'L' ? 'Putra' : 'Putri';
            $managerRole = $dorm->gender === 'L'
                ? 'bendahara-komplek-putra'
                : 'bendahara-komplek-putri';

            BillingConfiguration::create([
                'id'                 => Str::uuid(),
                'type'               => 'kas_komplek',
                'label'              => 'Kas Komplek ' . $dorm->name,
                'amount'             => 5000,
                'dormitory_id'       => $dorm->id,  // spesifik per komplek
                'effective_from'     => $effectiveFrom,
                'interval'           => 'monthly',
                'manager_role'       => $managerRole,
                'manager_ids'        => null,
                'target_type'        => 'dormitory', // hanya santri di komplek ini
                'target_filters'     => [$dorm->id], // ID dormitory sebagai filter
                'can_be_installment' => false,
                'is_active'          => true,
                'created_by'         => $admin->id,
            ]);
            $created++;
        }

        // ─────────────────────────────────────────────────────────────────
        // Laporan
        // ─────────────────────────────────────────────────────────────────
        $this->command->info("  ✅ BillingConfigurationSeeder: {$created} konfigurasi tarif berhasil dibuat.");
        $this->command->info('');
        $this->command->info('  📋 Rincian Tarif Aktif:');
        $this->command->info('  ┌─────────────────────────────┬───────────┬───────────┬──────────────────────┐');
        $this->command->info('  │ Jenis Tarif                 │ Nominal   │ Interval  │ Target               │');
        $this->command->info('  ├─────────────────────────────┼───────────┼───────────┼──────────────────────┤');
        $this->command->info('  │ Syahriah Pondok             │ Rp 35.000 │ Bulanan   │ Semua santri mukim   │');
        $this->command->info('  │ Syahriah Madrasah           │ Rp150.000 │ Semester  │ Semua santri         │');
        $this->command->info('  │ Kebersihan                  │ Rp 20.000 │ Semester  │ Semua santri mukim   │');
        $this->command->info('  │ Kas Komplek (per komplek)   │ Rp  5.000 │ Bulanan   │ Per dormitory        │');
        $this->command->info('  └─────────────────────────────┴───────────┴───────────┴──────────────────────┘');
    }
}
