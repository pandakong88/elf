<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('census_facility_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('campaign_id');
            $table->uuid('dormitory_id');
            $table->uuid('room_id')->nullable();
            $table->uuid('locker_id')->nullable();
            $table->string('item_type', 30); // komplek, kamar, lemari
            $table->string('item_name', 150);
            $table->string('condition', 30)->default('baik'); // baik, rusak_ringan, rusak_berat, hilang
            $table->uuid('assigned_person_id')->nullable();
            $table->text('notes')->nullable();
            $table->uuid('entered_by')->nullable();
            $table->string('apply_status', 30)->default('pending'); // pending, applied, rejected
            $table->text('apply_error')->nullable();
            $table->timestamps();

            $table->foreign('campaign_id', 'fk_cfi_campaign')
                ->references('id')->on('census_campaigns')
                ->cascadeOnDelete();

            $table->foreign('dormitory_id', 'fk_cfi_dorm')
                ->references('id')->on('dormitories')
                ->cascadeOnDelete();

            $table->foreign('room_id', 'fk_cfi_room')
                ->references('id')->on('rooms')
                ->cascadeOnDelete();

            $table->foreign('locker_id', 'fk_cfi_locker')
                ->references('id')->on('lockers')
                ->nullOnDelete();

            $table->foreign('assigned_person_id', 'fk_cfi_person')
                ->references('id')->on('persons')
                ->nullOnDelete();

            $table->foreign('entered_by', 'fk_cfi_entered')
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->index(['campaign_id', 'dormitory_id'], 'idx_cfi_camp_dorm');
            $table->index(['room_id', 'item_type'], 'idx_cfi_room_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('census_facility_items');
    }
};
