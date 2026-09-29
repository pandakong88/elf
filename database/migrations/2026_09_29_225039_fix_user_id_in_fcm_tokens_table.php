<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('fcm_tokens')) {
            Schema::create('fcm_tokens', function (Blueprint $table) {
                $table->id();
                $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
                $table->text('token');
                $table->string('device_info')->nullable();
                $table->timestamp('last_active_at')->nullable();
                $table->timestamps();

                $table->index('user_id');
            });
            return;
        }

        try {
            $columnType = Schema::getColumnType('fcm_tokens', 'user_id');
            if (in_array(strtolower($columnType), ['bigint', 'integer', 'int'])) {
                Schema::dropIfExists('fcm_tokens');

                Schema::create('fcm_tokens', function (Blueprint $table) {
                    $table->id();
                    $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
                    $table->text('token');
                    $table->string('device_info')->nullable();
                    $table->timestamp('last_active_at')->nullable();
                    $table->timestamps();

                    $table->index('user_id');
                });
            }
        } catch (\Throwable $e) {
            // Abaikan jika tipe kolom sudah sesuai atau driver tidak mendukung pengecekan
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fcm_tokens');
    }
};
