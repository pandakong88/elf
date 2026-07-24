<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        // Create new roles for Spatie Permission
        Role::firstOrCreate(['name' => 'bendahara-putra', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'bendahara-putri', 'guard_name' => 'web']);
    }

    public function down(): void
    {
        Role::whereIn('name', ['bendahara-putra', 'bendahara-putri'])->delete();
    }
};
