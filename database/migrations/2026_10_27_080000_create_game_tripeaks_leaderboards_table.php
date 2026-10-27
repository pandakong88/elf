<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_tripeaks_leaderboards', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('high_score')->default(0)->index();
            $table->unsignedInteger('max_combo')->default(0)->index();
            $table->unsignedInteger('total_games')->default(0);
            $table->unsignedInteger('total_cleared_peaks')->default(0);
            $table->unsignedInteger('total_victories')->default(0);
            $table->timestamp('last_played_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_tripeaks_leaderboards');
    }
};
