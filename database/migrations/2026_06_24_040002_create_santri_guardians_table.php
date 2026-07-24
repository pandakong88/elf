<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('santri_guardians', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('person_id')
                ->constrained('persons')
                ->cascadeOnDelete();

            $table->foreignUuid('guardian_id')
                ->constrained('guardians')
                ->cascadeOnDelete();

            // Jenis hubungan wali ke santri ini
            $table->string('relationship')->default('wali_resmi')
                ->comment('ayah_kandung|ibu_kandung|wali_resmi|kakek|nenek|paman|bibi|kakak_kandung|kontak_darurat|lainnya');

            // Urutan prioritas kontak (1 = utama)
            $table->unsignedTinyInteger('priority_order')->default(1);

            // Apakah ini wali utama / penanggung jawab resmi
            $table->boolean('is_primary')->default(false);

            // Catatan khusus hubungan ini
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['person_id', 'guardian_id', 'relationship']);
            $table->index('person_id');
            $table->index('guardian_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('santri_guardians');
    }
};
