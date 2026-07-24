<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('majek_periods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedTinyInteger('month');              // 1–12
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('active_days');        // Hari aktif makan (mis. 25 untuk Agustus karena 26-31 libur)
            $table->decimal('tarif_per_hari', 10, 2)->default(7000.00); // Tarif 2x/hari (1x = setengahnya)
            $table->text('notes')->nullable();                 // Catatan bendahara (mis. alasan pengurangan hari)
            $table->foreignUuid('created_by')->constrained('users');
            $table->timestamps();

            $table->unique(['month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('majek_periods');
    }
};
