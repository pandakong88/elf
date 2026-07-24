<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('madrasah_kelas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');                        // "Kelas 1 Ula", "Kelas 2 Wustho", dst.
            $table->enum('jenjang', ['ula', 'wustho', 'ulya']); // Tingkatan
            $table->string('academic_year');               // "2025/2026"
            $table->uuid('wali_kelas_id')->nullable();     // FK ke persons (guru)
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('wali_kelas_id')->references('id')->on('persons')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('madrasah_kelas');
    }
};
