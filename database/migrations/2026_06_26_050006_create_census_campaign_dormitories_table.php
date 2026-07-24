<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('census_campaign_dormitories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('campaign_id')->constrained('census_campaigns')->cascadeOnDelete();
            $table->foreignUuid('dormitory_id')->constrained('dormitories')->cascadeOnDelete();
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'in_progress', 'submitted', 'approved', 'rejected'])->default('pending');
            $table->unsignedInteger('progress_filled')->default(0);
            $table->unsignedInteger('progress_total')->default(0);
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->text('rejection_notes')->nullable();
            $table->timestamps();

            $table->unique(['campaign_id', 'dormitory_id']);
            $table->index(['campaign_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('census_campaign_dormitories');
    }
};
