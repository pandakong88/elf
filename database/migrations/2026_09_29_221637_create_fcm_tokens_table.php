<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('token');                          // FCM device token
            $table->string('device_info')->nullable();      // browser/OS info
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();

            // One user can have multiple devices
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcm_tokens');
    }
};
