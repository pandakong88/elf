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
        Schema::dropIfExists('admin_virtual_pets');

        Schema::create('admin_virtual_pets', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete()->unique();
            $table->string('pet_type', 30); // kibo, bunbun, bambu, pingo
            $table->string('name', 50);
            $table->unsignedInteger('level')->default(1);
            $table->unsignedInteger('xp')->default(0);
            $table->unsignedInteger('coins')->default(100);
            
            // Status kebutuhan pokok (0 - 100)
            $table->unsignedTinyInteger('hunger')->default(80);
            $table->unsignedTinyInteger('hygiene')->default(80);
            $table->unsignedTinyInteger('energy')->default(90);
            $table->unsignedTinyInteger('happiness')->default(85);
            $table->boolean('is_sleeping')->default(false);

            // Timestamps aksi & peluruhan
            $table->timestamp('last_fed_at')->nullable();
            $table->timestamp('last_cleaned_at')->nullable();
            $table->timestamp('last_slept_at')->nullable();
            $table->timestamp('last_played_at')->nullable();
            $table->timestamp('last_decay_calculated_at')->nullable();
            $table->date('last_daily_bonus_at')->nullable();

            // Inventori item & riwayat reward
            $table->json('inventory')->nullable();
            $table->json('recent_rewards')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_virtual_pets');
    }
};
