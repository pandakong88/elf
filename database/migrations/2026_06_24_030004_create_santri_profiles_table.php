<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('santri_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('person_id')->unique()->constrained('persons')->cascadeOnDelete();
            // Status pendidikan
            $table->string('school_status')->nullable(); // Dalam Pondok, Luar Pondok, Kuliah, Tidak Sekolah
            $table->string('school_name')->nullable();
            $table->string('major')->nullable(); // jurusan / program studi
            $table->string('school_year')->nullable(); // kelas / semester
            // Kesehatan
            $table->text('medical_history')->nullable(); // riwayat penyakit / alergi
            $table->string('blood_type')->nullable();
            // Data tambahan fleksibel
            $table->json('additional_info')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('santri_profiles');
    }
};
