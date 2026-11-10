<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lockers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('room_id');
            $table->string('code', 50);
            $table->uuid('assigned_person_id')->nullable();
            $table->string('condition', 30)->default('baik');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('room_id', 'fk_lck_room')
                ->references('id')->on('rooms')
                ->cascadeOnDelete();

            $table->foreign('assigned_person_id', 'fk_lck_person')
                ->references('id')->on('persons')
                ->nullOnDelete();

            $table->unique(['room_id', 'code'], 'uniq_lck_room_code');
            $table->index(['room_id', 'condition'], 'idx_lck_room_cond');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lockers');
    }
};
