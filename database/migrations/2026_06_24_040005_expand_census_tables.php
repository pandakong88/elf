<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan kolom tracking import ke dormitory_censuses
        Schema::table('dormitory_censuses', function (Blueprint $table) {
            $table->string('import_source')->default('manual')
                ->after('notes')
                ->comment('manual|excel|csv');
            $table->string('import_file_path')->nullable()->after('import_source');
            $table->unsignedSmallInteger('total_santri')->default(0)->after('import_file_path');
            $table->unsignedSmallInteger('total_confirmed')->default(0)->after('total_santri');
            $table->unsignedSmallInteger('total_exceptions')->default(0)->after('total_confirmed');
        });

        // Perluas status room_census_details
        // Status baru yang lebih lengkap dari sebelumnya
        // (present, sick, leave, absent, moved, graduated, resigned)
        // Kolom status sudah ada, hanya tambahkan kolom baru
        Schema::table('room_census_details', function (Blueprint $table) {
            $table->boolean('has_profile_update')->default(false)->after('profile_updates')
                ->comment('Flag: apakah ada usulan update profil di record ini');
            $table->boolean('has_guardian_update')->default(false)->after('has_profile_update')
                ->comment('Flag: apakah ada usulan update data wali di record ini');
            // Data wali yang diusulkan via sensus (JSON)
            $table->json('guardian_updates')->nullable()->after('has_guardian_update')
                ->comment('JSON usulan perubahan data wali dari musyrif');
        });
    }

    public function down(): void
    {
        Schema::table('dormitory_censuses', function (Blueprint $table) {
            $table->dropColumn(['import_source', 'import_file_path', 'total_santri', 'total_confirmed', 'total_exceptions']);
        });
        Schema::table('room_census_details', function (Blueprint $table) {
            $table->dropColumn(['has_profile_update', 'has_guardian_update', 'guardian_updates']);
        });
    }
};
