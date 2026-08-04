<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Madrasah\Models\MadrasahKelas;
use App\Modules\Keuangan\Models\BillingConfiguration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BillingConfigurationSeeder extends Seeder
{
    /**
     * Billing & Registration Rates Seeder based on Official Buku Pedoman Santri
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
        // 1. IURAN RUTIN (BULANAN & SEMESTERAN)
        // ─────────────────────────────────────────────────────────────────

        // 1a. SYAHRIAH PONDOK — Rp 35.000 / bulan
        BillingConfiguration::create([
            'id'                 => Str::uuid(),
            'type'               => 'syahriah_pondok',
            'label'              => 'Syahriah Pondok Bulanan',
            'amount'             => 35000,
            'dormitory_id'       => null,
            'effective_from'     => $effectiveFrom,
            'interval'           => 'monthly',
            'manager_role'       => 'bendahara-pusat',
            'manager_ids'        => null,
            'target_type'        => 'all',
            'target_filters'     => null,
            'can_be_installment' => false,
            'is_active'          => true,
            'created_by'         => $admin->id,
        ]);
        $created++;

        // 1b. SYAHRIAH MADRASAH — Rp 150.000 / semester
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
            'target_type'        => 'all',
            'target_filters'     => null,
            'can_be_installment' => true,
            'is_active'          => true,
            'created_by'         => $admin->id,
        ]);
        $created++;

        // 1c. KEBERSIHAN — Rp 20.000 / semester
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

        // 1d. KAS KOMPLEK — Rp 5.000 / bulan (Per Dormitory)
        $dormitories = Dormitory::orderBy('gender')->orderBy('name')->get();
        foreach ($dormitories as $dorm) {
            $managerRole = $dorm->gender === 'L' ? 'bendahara-komplek-putra' : 'bendahara-komplek-putri';
            BillingConfiguration::create([
                'id'                 => Str::uuid(),
                'type'               => 'kas_komplek',
                'label'              => 'Kas Komplek ' . $dorm->name,
                'amount'             => 5000,
                'dormitory_id'       => $dorm->id,
                'effective_from'     => $effectiveFrom,
                'interval'           => 'monthly',
                'manager_role'       => $managerRole,
                'manager_ids'        => null,
                'target_type'        => 'dormitory',
                'target_filters'     => [$dorm->id],
                'can_be_installment' => false,
                'is_active'          => true,
                'created_by'         => $admin->id,
            ]);
            $created++;
        }

        // ─────────────────────────────────────────────────────────────────
        // 2. ITEM REGISTRASI SANTRI BARU (SEKALI BAYAR)
        // ─────────────────────────────────────────────────────────────────
        $regItems = [
            [
                'label'   => 'Pendaftaran Pondok',
                'amount'  => 50000,
                'filters' => ['category' => 'dasar', 'gender' => 'ALL', 'residence' => 'ALL'],
            ],
            [
                'label'   => 'Pendaftaran Madrasah Diniyyah',
                'amount'  => 30000,
                'filters' => ['category' => 'dasar', 'gender' => 'ALL', 'residence' => 'ALL'],
            ],
            [
                'label'   => 'Sumbangan Pembangunan Pondok',
                'amount'  => 200000,
                'filters' => ['category' => 'bangunan', 'gender' => 'ALL', 'residence' => 'ALL'],
            ],
            [
                'label'   => 'Kartu Tanda Santri (KTS)',
                'amount'  => 10000,
                'filters' => ['category' => 'administrasi', 'gender' => 'ALL', 'residence' => 'ALL'],
            ],
            [
                'label'   => 'Kitab Pegangan (Pasholatan, Ratib) & Almari (Putra)',
                'amount'  => 45000,
                'filters' => ['category' => 'fasilitas', 'gender' => 'L', 'residence' => 'mukim'],
            ],
            [
                'label'   => 'Kitab Pegangan (Pasholatan, Ratib) & Almari (Putri)',
                'amount'  => 56000,
                'filters' => ['category' => 'fasilitas', 'gender' => 'P', 'residence' => 'mukim'],
            ],
            [
                'label'   => 'Kitab Madrasah Diniyyah (Paket Dasar Putri)',
                'amount'  => 42000,
                'filters' => ['category' => 'kitab', 'gender' => 'P', 'residence' => 'ALL'],
            ],
            [
                'label'   => 'Seragam Pondok Putra (Koko, Sarung, Peci)',
                'amount'  => 125000,
                'filters' => ['category' => 'seragam', 'gender' => 'L', 'residence' => 'ALL'],
            ],
            [
                'label'   => 'Seragam Pondok Putri (Gamis & Jilbab)',
                'amount'  => 180000,
                'filters' => ['category' => 'seragam', 'gender' => 'P', 'residence' => 'ALL'],
            ],
            [
                'label'   => 'Uang Makan Majek Putra - Pagi (Opsional)',
                'amount'  => 100000,
                'filters' => ['category' => 'katering', 'gender' => 'L', 'residence' => 'ALL'],
            ],
            [
                'label'   => 'Uang Makan Majek Putra - Sore (Opsional)',
                'amount'  => 100000,
                'filters' => ['category' => 'katering', 'gender' => 'L', 'residence' => 'ALL'],
            ],
            [
                'label'   => 'Uang Makan Ndalem Putri (1 Bulan)',
                'amount'  => 180000,
                'filters' => ['category' => 'katering', 'gender' => 'P', 'residence' => 'ALL'],
            ],
        ];

        foreach ($regItems as $ri) {
            BillingConfiguration::create([
                'id'                 => Str::uuid(),
                'type'               => 'pendaftaran',
                'label'              => $ri['label'],
                'amount'             => $ri['amount'],
                'dormitory_id'       => null,
                'effective_from'     => $effectiveFrom,
                'interval'           => 'once',
                'manager_role'       => 'bendahara-pusat',
                'manager_ids'        => null,
                'target_type'        => 'all',
                'target_filters'     => $ri['filters'],
                'can_be_installment' => false,
                'is_active'          => true,
                'created_by'         => $admin->id,
            ]);
            $created++;
        }

        // ─────────────────────────────────────────────────────────────────
        // 3. TARIF PAKET KITAB MADRASAH PER KELAS
        // ─────────────────────────────────────────────────────────────────
        $kelasList = MadrasahKelas::all();
        foreach ($kelasList as $kelas) {
            $nameLower = strtolower($kelas->name);
            $amount = 100000; // default baseline

            if (str_contains($nameLower, '1') && str_contains($nameLower, 'putra')) {
                $amount = 136000;
            } elseif (str_contains($nameLower, '1') && str_contains($nameLower, 'putri')) {
                $amount = 42000;
            } elseif (str_contains($nameLower, '2')) {
                $amount = 150000;
            } elseif (str_contains($nameLower, '3')) {
                $amount = 180000;
            }

            BillingConfiguration::create([
                'id'                 => Str::uuid(),
                'type'               => 'kitab_price',
                'label'              => 'Paket Kitab ' . $kelas->name,
                'amount'             => $amount,
                'dormitory_id'       => null,
                'effective_from'     => $effectiveFrom,
                'interval'           => 'semester',
                'manager_role'       => 'bendahara-madrasah',
                'manager_ids'        => null,
                'target_type'        => 'kelas',
                'target_filters'     => [
                    'kelas_id'   => $kelas->id,
                    'kelas_name' => $kelas->name,
                    'jenjang'    => $kelas->jenjang ?? 'Awaliyah',
                ],
                'can_be_installment' => false,
                'is_active'          => true,
                'created_by'         => $admin->id,
            ]);
            $created++;
        }

        $this->command->info("  ✅ BillingConfigurationSeeder: Total {$created} konfigurasi tarif resmi berhasil di-seed!");
    }
}
