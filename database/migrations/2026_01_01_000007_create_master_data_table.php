<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_data', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // null = global (berlaku untuk semua unit)
            $table->foreignUuid('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('category'); // 'jenis_izin', 'jenis_pelanggaran', 'jenis_kegiatan', dll
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // data tambahan per kategori (misal: poin pelanggaran)
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category', 'is_active']);
            $table->index(['organization_id', 'category']);
            $table->unique(['organization_id', 'category', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_data');
    }
};
