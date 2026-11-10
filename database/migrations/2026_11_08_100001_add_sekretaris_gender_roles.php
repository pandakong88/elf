<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pastikan permission yang dibutuhkan sudah ada
        $permissions = [
            'manage-sensus-v3',
            'manage-census-template',
            'input-census-v3',
            'approve-census-v3',
            'change-enrollment-status',
            'change-presence-status',
            'view-any-santri',
            'manage-sensus',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // 2. Buat Role sekretaris-putra dan sekretaris-putri
        $sekretarisPutra = Role::firstOrCreate(['name' => 'sekretaris-putra', 'guard_name' => 'web']);
        $sekretarisPutri = Role::firstOrCreate(['name' => 'sekretaris-putri', 'guard_name' => 'web']);

        // 3. Beri permission ke roles
        $sekretarisPutra->syncPermissions($permissions);
        $sekretarisPutri->syncPermissions($permissions);

        // 4. Assign role ke akun-akun sekretaris yang sudah ada jika ditemukan
        $putraUsernames = ['syafieuddin', 'yasiro', 'alif.khabibul'];
        foreach ($putraUsernames as $uname) {
            $user = User::where('username', $uname)->first();
            if ($user && !$user->hasRole('sekretaris-putra')) {
                $user->assignRole('sekretaris-putra');
            }
        }

        $putriUsernames = ['ulfah.nuryati'];
        foreach ($putriUsernames as $uname) {
            $user = User::where('username', $uname)->first();
            if ($user && !$user->hasRole('sekretaris-putri')) {
                $user->assignRole('sekretaris-putri');
            }
        }
    }

    public function down(): void
    {
        // Hapus role
        Role::whereIn('name', ['sekretaris-putra', 'sekretaris-putri'])->delete();
    }
};
