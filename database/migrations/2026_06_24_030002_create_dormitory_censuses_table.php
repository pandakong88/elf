<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dormitory_censuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('census_period_id')->constrained('census_periods')->cascadeOnDelete();
            $table->foreignUuid('dormitory_id')->constrained('dormitories')->cascadeOnDelete();
            $table->foreignUuid('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->string('status')->default('pending'); // pending, submitted, approved, rejected
            $table->text('notes')->nullable(); // catatan revisi dari pusat
            $table->timestamps();

            $table->unique(['census_period_id', 'dormitory_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dormitory_censuses');
    }
};
