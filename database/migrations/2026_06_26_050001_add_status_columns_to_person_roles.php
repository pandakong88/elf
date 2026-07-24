<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('person_roles', function (Blueprint $table) {
            $table->enum('enrollment_status', [
                'aktif',
                'alumni',
                'keluar_resmi',
                'dikeluarkan',
                'tanpa_keterangan',
            ])->default('aktif')->after('is_active');

            $table->enum('presence_status', [
                'mukim',
                'laju',
                'izin',
                'alpa',
            ])->default('mukim')->nullable()->after('enrollment_status');

            $table->dateTime('presence_status_since')->nullable()->after('presence_status');
            $table->dateTime('presence_status_until')->nullable()->after('presence_status_since');
            $table->text('presence_status_notes')->nullable()->after('presence_status_until');
        });
    }

    public function down(): void
    {
        Schema::table('person_roles', function (Blueprint $table) {
            $table->dropColumn([
                'enrollment_status',
                'presence_status',
                'presence_status_since',
                'presence_status_until',
                'presence_status_notes',
            ]);
        });
    }
};
