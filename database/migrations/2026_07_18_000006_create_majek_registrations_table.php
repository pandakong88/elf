<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('majek_registrations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('person_id')->constrained('persons')->cascadeOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->boolean('session_pagi')->default(false);
            $table->boolean('session_sore')->default(false);
            $table->decimal('amount_pagi', 10, 2)->default(0.00);
            $table->decimal('amount_sore', 10, 2)->default(0.00);
            $table->foreignUuid('registered_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['person_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('majek_registrations');
    }
};
