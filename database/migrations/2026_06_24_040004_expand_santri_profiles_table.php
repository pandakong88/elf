<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('santri_profiles', function (Blueprint $table) {
            // --- Data Orang Tua (eksplisit untuk auto-detect saudara) ---
            $table->string('father_name')->nullable()->after('person_id')
                ->comment('Nama ayah kandung — untuk auto-detect saudara');
            $table->string('father_phone')->nullable()->after('father_name')
                ->comment('Nomor HP ayah');
            $table->string('father_occupation')->nullable()->after('father_phone')
                ->comment('Pekerjaan ayah');
            $table->string('mother_name')->nullable()->after('father_occupation')
                ->comment('Nama ibu kandung — untuk auto-detect saudara');
            $table->string('mother_phone')->nullable()->after('mother_name')
                ->comment('Nomor HP ibu');

            // --- Kesehatan tambahan ---
            $table->text('allergies')->nullable()->after('medical_history')
                ->comment('Alergi makanan, obat, dll');
            $table->text('special_conditions')->nullable()->after('allergies')
                ->comment('Kondisi fisik/mental khusus yang perlu diperhatikan');

            // --- Pendidikan tambahan ---
            $table->string('school_type')->nullable()->after('school_name')
                ->comment('SD|SMP|SMA|SMK|D3|S1|S2|S3|Pesantren|dll');

            // --- Sosial ---
            $table->string('birth_city')->nullable()->after('school_year')
                ->comment('Kota kelahiran');
            $table->string('hobby')->nullable()->after('birth_city');
            $table->text('achievement')->nullable()->after('hobby')
                ->comment('Prestasi / pencapaian');

            // --- Data Sistem ---
            // Flag: apakah santri ini memiliki saudara kandung aktif di pondok
            // Di-update otomatis oleh SiblingService
            $table->boolean('has_active_sibling')->default(false)->after('achievement')
                ->comment('Flag: ada saudara kandung aktif di pondok (untuk diskon syahriah)');

            $table->unsignedTinyInteger('active_sibling_count')->default(0)->after('has_active_sibling')
                ->comment('Jumlah saudara kandung aktif di pondok');

            // Last census update
            $table->uuid('last_census_id')->nullable()->after('active_sibling_count')
                ->comment('ID sensus terakhir yang mengupdate profil ini');
            $table->timestamp('last_updated_at')->nullable()->after('last_census_id');
        });
    }

    public function down(): void
    {
        Schema::table('santri_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'father_name', 'father_phone', 'father_occupation',
                'mother_name', 'mother_phone',
                'allergies', 'special_conditions',
                'school_type',
                'birth_city', 'hobby', 'achievement',
                'has_active_sibling', 'active_sibling_count',
                'last_census_id', 'last_updated_at',
            ]);
        });
    }
};
