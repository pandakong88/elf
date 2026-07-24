<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Buat semua permissions
        $permissions = [
            // Core
            'view-any-person',
            'view-person',
            'create-person',
            'update-person',
            'delete-person',

            // Kepengasuhan
            'view-any-santri',
            'manage-asrama',
            'manage-kamar',
            'view-perizinan',
            'create-perizinan',
            'approve-perizinan',
            'view-pelanggaran',
            'create-pelanggaran',
            'manage-kegiatan',
            'manage-sensus',

            // Sensus v3
            'manage-sensus-v3',
            'manage-census-template',
            'input-census-v3',
            'approve-census-v3',
            'change-enrollment-status',   // Hanya manajemen & super-admin
            'change-presence-status',

            // Madrasah
            'view-any-kelas',
            'manage-kelas',
            'input-absensi',
            'input-nilai',
            'view-raport',
            'manage-raport',

            // Keuangan
            'view-tagihan',
            'create-tagihan',
            'record-pembayaran',
            'void-pembayaran',
            'manage-billing-config',
            'manage-setoran-kolektif',
            'view-laporan-keuangan',
            'manage-adjustment',

            // Koperasi
            'view-produk',
            'manage-produk',
            'manage-stok',
            'view-penjualan',
            'create-penjualan',
            'manage-supplier',

            // Workflow
            'initiate-workflow',
            'approve-workflow',
            'reject-workflow',

            // System
            'manage-master-data',
            'manage-users',
            'manage-roles',
            'view-audit-log',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // 2. Buat semua roles
        $roles = [
            'super-admin',
            'pengasuh',
            'manajemen',      // Role baru: bisa ubah enrollment status
            'musyrif',
            'ketua-madrasah',
            'guru',
            'bendahara-pondok',
            'bendahara-putra',
            'bendahara-putri',
            'bendahara-unit',
            'pengurus-koperasi',
            'wali-santri',
            'admin-data',
        ];

        $roleInstances = [];
        foreach ($roles as $roleName) {
            $roleInstances[$roleName] = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 3. Assign permissions ke roles sesuai matrix
        // super-admin: semua permission (bypass via Gate::before, tapi tetap kita sync agar rapi)
        $roleInstances['super-admin']->syncPermissions(Permission::all());

        // pengasuh: semua permission kecuali manage-users, manage-roles
        $pengasuhPermissions = Permission::whereNotIn('name', ['manage-users', 'manage-roles'])->get();
        $roleInstances['pengasuh']->syncPermissions($pengasuhPermissions);

        // manajemen: kepengasuhan + keuangan + bisa ubah enrollment status santri
        $roleInstances['manajemen']->syncPermissions([
            'view-any-santri', 'manage-asrama', 'manage-kamar',
            'view-perizinan', 'approve-perizinan',
            'view-pelanggaran', 'manage-kegiatan',
            'manage-sensus', 'manage-sensus-v3', 'manage-census-template',
            'input-census-v3', 'approve-census-v3',
            'change-enrollment-status', 'change-presence-status',
            'view-audit-log',
            'view-tagihan', 'create-tagihan', 'record-pembayaran', 'void-pembayaran', 'manage-billing-config', 'manage-setoran-kolektif', 'view-laporan-keuangan', 'manage-adjustment',
        ]);

        // musyrif:
        $roleInstances['musyrif']->syncPermissions([
            'view-any-santri', 'manage-kamar', 'view-perizinan', 'create-perizinan', 'approve-perizinan',
            'view-pelanggaran', 'create-pelanggaran', 'manage-kegiatan', 'manage-sensus',
            'input-census-v3', 'change-presence-status',
            'initiate-workflow', 'approve-workflow'
        ]);

        // ketua-madrasah:
        $roleInstances['ketua-madrasah']->syncPermissions([
            'view-any-kelas', 'manage-kelas', 'input-absensi', 'input-nilai', 'view-raport', 'manage-raport',
            'view-tagihan', 'manage-master-data'
        ]);

        // guru:
        $roleInstances['guru']->syncPermissions([
            'view-any-kelas', 'input-absensi', 'input-nilai', 'view-raport'
        ]);

        // bendahara-pondok:
        $roleInstances['bendahara-pondok']->syncPermissions([
            'view-tagihan', 'create-tagihan', 'record-pembayaran', 'void-pembayaran', 'manage-billing-config', 'manage-setoran-kolektif', 'view-laporan-keuangan', 'manage-adjustment'
        ]);

        // bendahara-putra:
        $roleInstances['bendahara-putra']->syncPermissions([
            'view-tagihan', 'create-tagihan', 'record-pembayaran', 'void-pembayaran', 'manage-billing-config', 'manage-setoran-kolektif', 'view-laporan-keuangan', 'manage-adjustment'
        ]);

        // bendahara-putri:
        $roleInstances['bendahara-putri']->syncPermissions([
            'view-tagihan', 'create-tagihan', 'record-pembayaran', 'void-pembayaran', 'manage-billing-config', 'manage-setoran-kolektif', 'view-laporan-keuangan', 'manage-adjustment'
        ]);

        // bendahara-unit:
        $roleInstances['bendahara-unit']->syncPermissions([
            'view-tagihan', 'record-pembayaran', 'view-laporan-keuangan'
        ]);

        // pengurus-koperasi:
        $roleInstances['pengurus-koperasi']->syncPermissions([
            'view-produk', 'manage-produk', 'manage-stok', 'view-penjualan', 'create-penjualan', 'manage-supplier'
        ]);

        // wali-santri:
        $roleInstances['wali-santri']->syncPermissions([
            'view-person', 'view-tagihan', 'view-raport', 'create-perizinan'
        ]);

        // admin-data:
        $roleInstances['admin-data']->syncPermissions([
            'view-any-person', 'create-person', 'update-person', 'manage-master-data',
            'view-any-santri', 'view-any-kelas'
        ]);

        // If base users are already seeded, we can stop here
        if (User::where('email', 'admin@elvith.id')->exists()) {
            return;
        }

        // 4. Buat 1 user super-admin
        $rootOrg = Organization::where('slug', 'ponpes-al-fithroh')->firstOrFail();

        $adminPerson = Person::firstOrCreate(
            ['nik' => '0000000000000000'],
            [
                'id'          => Str::uuid()->toString(),
                'name'        => 'Administrator',
                'gender'      => 'L',
                'birth_place' => 'Surabaya',
                'birth_date'  => '1990-01-01',
                'phone'       => '081234567890',
                'address'     => 'Pondok Pesantren Al-Fithroh, Surabaya',
                'notes'       => 'Sistem Administrator Utama',
            ]
        );

        PersonRole::firstOrCreate([
            'person_id'       => $adminPerson->id,
            'organization_id' => $rootOrg->id,
            'role_type'       => 'pengurus',
        ], [
            'id'              => Str::uuid()->toString(),
            'valid_from'      => now()->startOfYear()->toDateString(),
            'valid_until'     => null,
            'is_active'       => true,
        ]);

        $adminUser = User::firstOrCreate([
            'email' => 'admin@elvith.id',
        ], [
            'id'        => Str::uuid()->toString(),
            'person_id' => $adminPerson->id,
            'name'      => 'Administrator',
            'username'  => 'admin',
            'password'  => 'rahasia123',
            'is_active' => true,
        ]);

        $adminUser->assignRole('super-admin');

        // 5. Buat user Musyrif
        $putraOrg = Organization::where('slug', 'kepengasuhan-putra')->firstOrFail();
        $musyrifPerson = Person::create([
            'id'          => Str::uuid()->toString(),
            'nik'         => '1111111111111111',
            'name'        => 'Musyrif Ahmad',
            'gender'      => 'L',
            'birth_place' => 'Surabaya',
            'birth_date'  => '1992-02-02',
            'phone'       => '081234567891',
            'address'     => 'Pondok Pesantren Al-Fithroh, Surabaya',
            'notes'       => 'Musyrif Putra',
        ]);
        PersonRole::create([
            'id'              => Str::uuid()->toString(),
            'person_id'       => $musyrifPerson->id,
            'organization_id' => $putraOrg->id,
            'role_type'       => 'pengurus',
            'valid_from'      => now()->startOfYear()->toDateString(),
            'is_active'       => true,
        ]);
        $musyrifUser = User::create([
            'id'        => Str::uuid()->toString(),
            'person_id' => $musyrifPerson->id,
            'name'      => 'Musyrif Ahmad',
            'username'  => 'musyrif',
            'email'     => 'musyrif@elvith.id',
            'password'  => 'rahasia123',
            'is_active' => true,
        ]);
        $musyrifUser->assignRole('musyrif');

        // 5b. Buat user Musyrifah
        $putriOrg = Organization::where('slug', 'kepengasuhan-putri')->firstOrFail();
        $musyrifahPerson = Person::create([
            'id'          => Str::uuid()->toString(),
            'nik'         => '1111111111111112',
            'name'        => 'Musyrifah Fatimah',
            'gender'      => 'P',
            'birth_place' => 'Surabaya',
            'birth_date'  => '1994-04-04',
            'phone'       => '081234567894',
            'address'     => 'Pondok Pesantren Al-Fithroh, Surabaya',
            'notes'       => 'Musyrifah Putri',
        ]);
        PersonRole::create([
            'id'              => Str::uuid()->toString(),
            'person_id'       => $musyrifahPerson->id,
            'organization_id' => $putriOrg->id,
            'role_type'       => 'pengurus',
            'valid_from'      => now()->startOfYear()->toDateString(),
            'is_active'       => true,
        ]);
        $musyrifahUser = User::create([
            'id'        => Str::uuid()->toString(),
            'person_id' => $musyrifahPerson->id,
            'name'      => 'Musyrifah Fatimah',
            'username'  => 'musyrifah',
            'email'     => 'musyrifah@elvith.id',
            'password'  => 'rahasia123',
            'is_active' => true,
        ]);
        $musyrifahUser->assignRole('musyrif');

        // 6. Buat user Pengasuh
        $pengasuhPerson = Person::create([
            'id'          => Str::uuid()->toString(),
            'nik'         => '2222222222222222',
            'name'        => 'Kiai Abdullah',
            'gender'      => 'L',
            'birth_place' => 'Gresik',
            'birth_date'  => '1965-05-05',
            'phone'       => '081234567892',
            'address'     => 'Pondok Pesantren Al-Fithroh, Surabaya',
            'notes'       => 'Pengasuh Utama',
        ]);
        PersonRole::create([
            'id'              => Str::uuid()->toString(),
            'person_id'       => $pengasuhPerson->id,
            'organization_id' => $rootOrg->id,
            'role_type'       => 'pengurus',
            'valid_from'      => now()->startOfYear()->toDateString(),
            'is_active'       => true,
        ]);
        $pengasuhUser = User::create([
            'id'        => Str::uuid()->toString(),
            'person_id' => $pengasuhPerson->id,
            'name'      => 'Kiai Abdullah',
            'username'  => 'pengasuh',
            'email'     => 'pengasuh@elvith.id',
            'password'  => 'rahasia123',
            'is_active' => true,
        ]);
        $pengasuhUser->assignRole('pengasuh');

        // 7. Buat user Operator / Admin Data
        $operatorPerson = Person::create([
            'id'          => Str::uuid()->toString(),
            'nik'         => '3333333333333333',
            'name'        => 'Operator Data',
            'gender'      => 'L',
            'birth_place' => 'Sidoarjo',
            'birth_date'  => '1995-10-10',
            'phone'       => '081234567893',
            'address'     => 'Pondok Pesantren Al-Fithroh, Surabaya',
            'notes'       => 'Operator Data & Master Data',
        ]);
        PersonRole::create([
            'id'              => Str::uuid()->toString(),
            'person_id'       => $operatorPerson->id,
            'organization_id' => $rootOrg->id,
            'role_type'       => 'pegawai',
            'valid_from'      => now()->startOfYear()->toDateString(),
            'is_active'       => true,
        ]);
        $operatorUser = User::create([
            'id'        => Str::uuid()->toString(),
            'person_id' => $operatorPerson->id,
            'name'      => 'Operator Data',
            'username'  => 'operator',
            'email'     => 'operator@elvith.id',
            'password'  => 'rahasia123',
            'is_active' => true,
        ]);
        $operatorUser->assignRole('admin-data');

        // 8. Buat akun-akun pengurus asli sesuai struktur kepengurusan pondok
        $boardMembers = [
            // KETUA
            [
                'name' => 'M. Jusam Masykuri',
                'gender' => 'L',
                'phone' => '0895 2920 4060',
                'role' => 'manajemen',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'jusam',
                'email' => 'jusam@elvith.id',
            ],
            [
                'name' => 'Latifatun Nurul Hamidah',
                'gender' => 'P',
                'phone' => '0856 0117 7914',
                'role' => 'manajemen',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'latifatun',
                'email' => 'latifatun@elvith.id',
            ],
            // WAKIL KETUA
            [
                'name' => 'M. Iqbal Arjunanda Rizqi',
                'gender' => 'L',
                'phone' => '0813 2696 3142',
                'role' => 'super-admin',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'iqbal.arjunanda',
                'email' => 'iqbal.arjunanda@elvith.id',
            ],
            [
                'name' => 'Mar’atus Sholikhah Imtikhani',
                'gender' => 'P',
                'phone' => '0858 7807 4169',
                'role' => 'manajemen',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'maratus.sholikhah',
                'email' => 'maratus.sholikhah@elvith.id',
            ],
            // SEKRETARIS PUTRA
            [
                'name' => 'Ahmad Syafieuddin',
                'gender' => 'L',
                'phone' => '081234560001',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'syafieuddin',
                'email' => 'syafieuddin@elvith.id',
            ],
            [
                'name' => 'Ahmad Yasiro',
                'gender' => 'L',
                'phone' => '081234560002',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'yasiro',
                'email' => 'yasiro@elvith.id',
            ],
            [
                'name' => 'Alif Khabibul M',
                'gender' => 'L',
                'phone' => '081234560003',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'alif.khabibul',
                'email' => 'alif.khabibul@elvith.id',
            ],
            // SEKRETARIS PUTRI
            [
                'name' => 'Ulfah Nuryati',
                'gender' => 'P',
                'phone' => '081234560004',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'ulfah.nuryati',
                'email' => 'ulfah.nuryati@elvith.id',
            ],
            // BENDAHARA PUTRA
            [
                'name' => 'Helmi Azhar',
                'gender' => 'L',
                'phone' => '081234560006',
                'role' => 'bendahara-putra',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'helmi.azhar',
                'email' => 'helmi.azhar@elvith.id',
            ],
            [
                'name' => 'M Husain Abdullah',
                'gender' => 'L',
                'phone' => '081234560007',
                'role' => 'bendahara-pondok',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'husain.abdullah',
                'email' => 'husain.abdullah@elvith.id',
            ],
            [
                'name' => 'Muhammad Muadib',
                'gender' => 'L',
                'phone' => '081234560008',
                'role' => 'bendahara-putra',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'muadib',
                'email' => 'muadib@elvith.id',
            ],
            // BENDAHARA PUTRI
            [
                'name' => 'Musyarofah',
                'gender' => 'P',
                'phone' => '081234560009',
                'role' => 'bendahara-pondok',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'musyarofah',
                'email' => 'musyarofah@elvith.id',
            ],
            [
                'name' => 'Fadilah Ayu Qodariyatun',
                'gender' => 'P',
                'phone' => '081234560010',
                'role' => 'bendahara-putri',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'fadilah.ayu',
                'email' => 'fadilah.ayu@elvith.id',
            ],
            [
                'name' => 'Fanni Rahmasari',
                'gender' => 'P',
                'phone' => '081234560011',
                'role' => 'bendahara-putri',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'fanni.rahmasari',
                'email' => 'fanni.rahmasari@elvith.id',
            ],
            // MAJLIS TALIM PUTRA
            [
                'name' => 'Mudrik Alkahfi',
                'gender' => 'L',
                'phone' => '081234560012',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'mudrik.alkahfi',
                'email' => 'mudrik.alkahfi@elvith.id',
            ],
            [
                'name' => 'Muhammad Nuril Izza',
                'gender' => 'L',
                'phone' => '081234560013',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'nuril.izza',
                'email' => 'nuril.izza@elvith.id',
            ],
            // PENDIDIKAN PUTRI
            [
                'name' => 'Fina Alifah',
                'gender' => 'P',
                'phone' => '081234560014',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'fina.alifah',
                'email' => 'fina.alifah@elvith.id',
            ],
            [
                'name' => 'Nilna Zulfa Azizah',
                'gender' => 'P',
                'phone' => '081234560015',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'nilna.zulfa',
                'email' => 'nilna.zulfa@elvith.id',
            ],
            [
                'name' => 'Izzatunnafisah',
                'gender' => 'P',
                'phone' => '081234560016',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'izzatunnafisah',
                'email' => 'izzatunnafisah@elvith.id',
            ],
            [
                'name' => 'Wafiq Muna Azizah',
                'gender' => 'P',
                'phone' => '081234560017',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'wafiq.muna',
                'email' => 'wafiq.muna@elvith.id',
            ],
            [
                'name' => 'Arina Khoirunnisa',
                'gender' => 'P',
                'phone' => '081234560018',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'arina.khoirunnisa',
                'email' => 'arina.khoirunnisa@elvith.id',
            ],
            // KEAMANAN PUTRA
            [
                'name' => 'Rifqi Riftianto',
                'gender' => 'L',
                'phone' => '081234560019',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'rifqi.riftianto',
                'email' => 'rifqi.riftianto@elvith.id',
            ],
            [
                'name' => 'Yogi Setyo Anggoro',
                'gender' => 'L',
                'phone' => '081234560020',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'yogi.setyo',
                'email' => 'yogi.setyo@elvith.id',
            ],
            [
                'name' => 'Nurokhim',
                'gender' => 'L',
                'phone' => '081234560021',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'nurokhim',
                'email' => 'nurokhim@elvith.id',
            ],
            [
                'name' => 'Rifqi Darul Ihsan',
                'gender' => 'L',
                'phone' => '081234560022',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'rifqi.darul',
                'email' => 'rifqi.darul@elvith.id',
            ],
            [
                'name' => 'M Misbakhul Munir',
                'gender' => 'L',
                'phone' => '081234560023',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'misbakhul.munir',
                'email' => 'misbakhul.munir@elvith.id',
            ],
            [
                'name' => 'Ainur Rohman',
                'gender' => 'L',
                'phone' => '081234560024',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'ainur.rohman',
                'email' => 'ainur.rohman@elvith.id',
            ],
            [
                'name' => 'Yusuf Muda',
                'gender' => 'L',
                'phone' => '081234560025',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'yusuf.muda',
                'email' => 'yusuf.muda@elvith.id',
            ],
            // KEAMANAN PUTRI
            [
                'name' => 'Lu’lu’atul Mudhi’ah',
                'gender' => 'P',
                'phone' => '081234560026',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'luluatul',
                'email' => 'luluatul@elvith.id',
            ],
            [
                'name' => 'Nadia Putri Maulida',
                'gender' => 'P',
                'phone' => '081234560027',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'nadia.putri',
                'email' => 'nadia.putri@elvith.id',
            ],
            [
                'name' => 'Wida Anggriana',
                'gender' => 'P',
                'phone' => '081234560028',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'wida.anggriana',
                'email' => 'wida.anggriana@elvith.id',
            ],
            [
                'name' => 'Arina Ma Anjana',
                'gender' => 'P',
                'phone' => '081234560029',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'arina.ma',
                'email' => 'arina.ma@elvith.id',
            ],
            [
                'name' => 'Ilma Andini Nadiroh',
                'gender' => 'P',
                'phone' => '081234560030',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'ilma.andini',
                'email' => 'ilma.andini@elvith.id',
            ],
            // MADRASAH DINIYAH PUTRA
            [
                'name' => 'Khoirul Anam',
                'gender' => 'L',
                'phone' => '081234560031',
                'role' => 'guru',
                'org_slug' => 'madrasah-diniyah',
                'username' => 'khoirul.anam',
                'email' => 'khoirul.anam@elvith.id',
            ],
            [
                'name' => 'Muhammad Shulchan',
                'gender' => 'L',
                'phone' => '081234560032',
                'role' => 'guru',
                'org_slug' => 'madrasah-diniyah',
                'username' => 'shulchan',
                'email' => 'shulchan@elvith.id',
            ],
            [
                'name' => 'Achmad Chatami Al-M.',
                'gender' => 'L',
                'phone' => '081234560033',
                'role' => 'guru',
                'org_slug' => 'madrasah-diniyah',
                'username' => 'chatami',
                'email' => 'chatami@elvith.id',
            ],
            [
                'name' => 'Hanif Masyhuri',
                'gender' => 'L',
                'phone' => '081234560034',
                'role' => 'guru',
                'org_slug' => 'madrasah-diniyah',
                'username' => 'hanif.masyhuri',
                'email' => 'hanif.masyhuri@elvith.id',
            ],
            [
                'name' => 'M. Abdul Malik',
                'gender' => 'L',
                'phone' => '081234560035',
                'role' => 'guru',
                'org_slug' => 'madrasah-diniyah',
                'username' => 'abdul.malik',
                'email' => 'abdul.malik@elvith.id',
            ],
            [
                'name' => 'Riyan Khoirul Mustofa',
                'gender' => 'L',
                'phone' => '081234560036',
                'role' => 'guru',
                'org_slug' => 'madrasah-diniyah',
                'username' => 'riyan.khoirul',
                'email' => 'riyan.khoirul@elvith.id',
            ],
            [
                'name' => 'M. Mujtaba',
                'gender' => 'L',
                'phone' => '081234560037',
                'role' => 'guru',
                'org_slug' => 'madrasah-diniyah',
                'username' => 'mujtaba',
                'email' => 'mujtaba@elvith.id',
            ],
            [
                'name' => 'Winarto',
                'gender' => 'L',
                'phone' => '081234560038',
                'role' => 'guru',
                'org_slug' => 'madrasah-diniyah',
                'username' => 'winarto',
                'email' => 'winarto@elvith.id',
            ],
            // MADRASAH DINIYAH PUTRI
            [
                'name' => 'Halimah Nur Azizah',
                'gender' => 'P',
                'phone' => '081234560039',
                'role' => 'guru',
                'org_slug' => 'madrasah-diniyah',
                'username' => 'halimah.nur',
                'email' => 'halimah.nur@elvith.id',
            ],
            [
                'name' => 'Elvina Sri Wijayanti',
                'gender' => 'P',
                'phone' => '081234560040',
                'role' => 'guru',
                'org_slug' => 'madrasah-diniyah',
                'username' => 'elvina.sri',
                'email' => 'elvina.sri@elvith.id',
            ],
            [
                'name' => 'Shofiyatul Muniroh',
                'gender' => 'P',
                'phone' => '081234560041',
                'role' => 'guru',
                'org_slug' => 'madrasah-diniyah',
                'username' => 'shofiyatul',
                'email' => 'shofiyatul@elvith.id',
            ],
            [
                'name' => 'Siti Mutmainah',
                'gender' => 'P',
                'phone' => '081234560042',
                'role' => 'guru',
                'org_slug' => 'madrasah-diniyah',
                'username' => 'mutmainah',
                'email' => 'mutmainah@elvith.id',
            ],
            [
                'name' => 'Lina Nurul Ashfa',
                'gender' => 'P',
                'phone' => '081234560043',
                'role' => 'guru',
                'org_slug' => 'madrasah-diniyah',
                'username' => 'lina.nurul',
                'email' => 'lina.nurul@elvith.id',
            ],
            [
                'name' => 'Uswatun Khasanah',
                'gender' => 'P',
                'phone' => '081234560044',
                'role' => 'guru',
                'org_slug' => 'madrasah-diniyah',
                'username' => 'uswatun',
                'email' => 'uswatun@elvith.id',
            ],
            // KEBERSIHAN PUTRA
            [
                'name' => 'Fathur Rohman',
                'gender' => 'L',
                'phone' => '081234560045',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'fathur.rohman',
                'email' => 'fathur.rohman@elvith.id',
            ],
            [
                'name' => 'Wisnu Darojat',
                'gender' => 'L',
                'phone' => '081234560046',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'wisnu.darojat',
                'email' => 'wisnu.darojat@elvith.id',
            ],
            // KEBERSIHAN PUTRI
            [
                'name' => 'Erra Fazirra Saravica P.A',
                'gender' => 'P',
                'phone' => '081234560047',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'erra.fazirra',
                'email' => 'erra.fazirra@elvith.id',
            ],
            [
                'name' => 'Badriyatul Munawaroh',
                'gender' => 'P',
                'phone' => '081234560048',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'badriyatul',
                'email' => 'badriyatul@elvith.id',
            ],
            [
                'name' => 'Siti Nur Zahroti Jannah',
                'gender' => 'P',
                'phone' => '081234560049',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'zahroti',
                'email' => 'zahroti@elvith.id',
            ],
            [
                'name' => 'Lialini Ulul Makarimi',
                'gender' => 'P',
                'phone' => '081234560050',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'lialini',
                'email' => 'lialini@elvith.id',
            ],
            [
                'name' => 'Lina As’adah',
                'gender' => 'P',
                'phone' => '081234560051',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'lina.asadah',
                'email' => 'lina.asadah@elvith.id',
            ],
            [
                'name' => 'Tyas Fitri Musfiroh',
                'gender' => 'P',
                'phone' => '081234560052',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'tyas.fitri',
                'email' => 'tyas.fitri@elvith.id',
            ],
            // SARPRAS PUTRA
            [
                'name' => 'Ahmad Ismail',
                'gender' => 'L',
                'phone' => '081234560053',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'ahmad.ismail',
                'email' => 'ahmad.ismail@elvith.id',
            ],
            [
                'name' => 'M Sovwan Farid',
                'gender' => 'L',
                'phone' => '081234560054',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'sovwan.farid',
                'email' => 'sovwan.farid@elvith.id',
            ],
            [
                'name' => 'M Hidayatullah',
                'gender' => 'L',
                'phone' => '081234560055',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'hidayatullah',
                'email' => 'hidayatullah@elvith.id',
            ],
            // SARPRAS PUTRI
            [
                'name' => 'Pratama Nita Kusuma',
                'gender' => 'P',
                'phone' => '081234560056',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'pratama.nita',
                'email' => 'pratama.nita@elvith.id',
            ],
            [
                'name' => 'Hanin Asna Nafisah',
                'gender' => 'P',
                'phone' => '081234560057',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'hanin.asna',
                'email' => 'hanin.asna@elvith.id',
            ],
            // POSKESTREN PUTRI
            [
                'name' => 'Aniiqoh',
                'gender' => 'P',
                'phone' => '081234560058',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'aniiqoh',
                'email' => 'aniiqoh@elvith.id',
            ],
            [
                'name' => 'Nurul Fauziyah',
                'gender' => 'P',
                'phone' => '081234560059',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'nurul.fauziyah',
                'email' => 'nurul.fauziyah@elvith.id',
            ],
            [
                'name' => 'Qorry ‘Aina Nilnal Muna',
                'gender' => 'P',
                'phone' => '081234560060',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'qorry.nilnal',
                'email' => 'qorry.nilnal@elvith.id',
            ],
            // ABDI NDALEM PUTRA
            [
                'name' => 'Muhlisin',
                'gender' => 'L',
                'phone' => '081234560061',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'muhlisin',
                'email' => 'muhlisin@elvith.id',
            ],
            [
                'name' => 'Ahmad Nadhir',
                'gender' => 'L',
                'phone' => '081234560062',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putra',
                'username' => 'nadhir',
                'email' => 'nadhir@elvith.id',
            ],
            // ABDI NDALEM PUTRI
            [
                'name' => 'Shofiyyah',
                'gender' => 'P',
                'phone' => '081234560063',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'shofiyyah',
                'email' => 'shofiyyah@elvith.id',
            ],
            [
                'name' => 'Qorri ‘Aina',
                'gender' => 'P',
                'phone' => '081234560064',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'qorri.aina',
                'email' => 'qorri.aina@elvith.id',
            ],
            [
                'name' => 'Nayli Wardatun Nufus',
                'gender' => 'P',
                'phone' => '081234560065',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'nayli.wardatun',
                'email' => 'nayli.wardatun@elvith.id',
            ],
            [
                'name' => 'Muhimmatun Khasanah',
                'gender' => 'P',
                'phone' => '081234560066',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'muhimmatun',
                'email' => 'muhimmatun@elvith.id',
            ],
            [
                'name' => 'Lailatul Mutmaina',
                'gender' => 'P',
                'phone' => '081234560067',
                'role' => 'musyrif',
                'org_slug' => 'kepengasuhan-putri',
                'username' => 'lailatul.mutmaina',
                'email' => 'lailatul.mutmaina@elvith.id',
            ],
        ];

        foreach ($boardMembers as $index => $member) {
            $org = Organization::where('slug', $member['org_slug'])->firstOrFail();

            $person = Person::create([
                'id'          => Str::uuid()->toString(),
                'nik'         => '99' . str_pad((string)($index + 1), 14, '0', STR_PAD_LEFT),
                'name'        => $member['name'],
                'gender'      => $member['gender'],
                'birth_place' => $member['gender'] === 'L' ? 'Surabaya' : 'Sidoarjo',
                'birth_date'  => '1995-01-01',
                'phone'       => $member['phone'],
                'address'     => 'Pondok Pesantren Al-Fithroh, Surabaya',
                'notes'       => 'Staf Pengurus Aktif',
            ]);

            PersonRole::create([
                'id'              => Str::uuid()->toString(),
                'person_id'       => $person->id,
                'organization_id' => $org->id,
                'role_type'       => 'pengurus',
                'valid_from'      => now()->startOfYear()->toDateString(),
                'is_active'       => true,
            ]);

            $user = User::create([
                'id'        => Str::uuid()->toString(),
                'person_id' => $person->id,
                'name'      => $member['name'],
                'username'  => $member['username'],
                'email'     => $member['email'],
                'password'  => 'rahasia123',
                'is_active' => true,
            ]);

            $user->assignRole($member['role']);
        }

        $this->command->info('✅ RolePermissionSeeder: Roles, permissions, and admin user successfully seeded.');
    }
}
