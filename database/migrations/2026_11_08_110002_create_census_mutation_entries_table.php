<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('census_mutation_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignUuid('campaign_id')
                ->constrained('census_campaigns', 'id', 'fk_cme_campaign')
                ->cascadeOnDelete();

            $table->foreignUuid('dormitory_id')
                ->constrained('dormitories', 'id', 'fk_cme_dorm')
                ->cascadeOnDelete();

            $table->foreignUuid('person_id')
                ->constrained('persons', 'id', 'fk_cme_person')
                ->cascadeOnDelete();

            $table->foreignUuid('from_room_id')
                ->nullable()
                ->constrained('rooms', 'id', 'fk_cme_from_room')
                ->nullOnDelete();

            // 'tetap', 'pindah_kamar', 'ubah_status', 'tidak_ditemukan'
            $table->string('action', 30)->default('tetap');

            $table->foreignUuid('to_room_id')
                ->nullable()
                ->constrained('rooms', 'id', 'fk_cme_to_room')
                ->nullOnDelete();

            // Status keanggotaan baru jika ubah_status dipilih
            $table->string('new_enrollment_status', 50)->nullable();

            $table->text('notes')->nullable();

            $table->foreignUuid('entered_by')
                ->nullable()
                ->constrained('users', 'id', 'fk_cme_entered_by')
                ->nullOnDelete();

            // 'pending', 'applied', 'failed'
            $table->string('apply_status', 20)->default('pending');
            $table->text('apply_error')->nullable();

            $table->timestamps();

            $table->unique(['campaign_id', 'dormitory_id', 'person_id'], 'uniq_cme_camp_dorm_person');
            $table->index(['campaign_id', 'dormitory_id', 'apply_status'], 'idx_cme_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('census_mutation_entries');
    }
};
