<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_census_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('dormitory_census_id')->constrained('dormitory_censuses')->cascadeOnDelete();
            $table->foreignUuid('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignUuid('person_id')->constrained('persons')->cascadeOnDelete();
            // present, sick, leave, absent, moved
            $table->string('status')->default('present');
            $table->text('notes')->nullable();
            // Usulan perubahan profil santri (disimpan sementara, diapply saat approved)
            $table->json('profile_updates')->nullable();
            $table->timestamps();

            $table->unique(['dormitory_census_id', 'person_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_census_details');
    }
};
