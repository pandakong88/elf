<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('madrasah_enrollments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('person_id');         // FK ke persons (santri)
            $table->uuid('kelas_id');          // FK ke madrasah_kelas
            $table->string('academic_year');   // "2025/2026"
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('persons')->cascadeOnDelete();
            $table->foreign('kelas_id')->references('id')->on('madrasah_kelas')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();

            // Satu santri hanya bisa terdaftar satu kali per kelas per tahun
            $table->unique(['person_id', 'kelas_id', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('madrasah_enrollments');
    }
};
