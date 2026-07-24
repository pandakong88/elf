<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('census_responses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('campaign_id')->constrained('census_campaigns')->cascadeOnDelete();
            $table->foreignUuid('dormitory_id')->constrained('dormitories')->cascadeOnDelete();
            $table->foreignUuid('person_id')->constrained('persons')->cascadeOnDelete();
            $table->foreignUuid('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->json('response_data');
            $table->enum('input_method', ['web_admin', 'web_ketua', 'excel_upload'])->default('web_admin');
            $table->foreignUuid('inputted_by')->constrained('users');
            $table->boolean('is_complete')->default(false);
            $table->boolean('has_profile_changes')->default(false);
            $table->json('profile_change_preview')->nullable();
            $table->timestamps();

            $table->unique(['campaign_id', 'person_id']);
            $table->index(['campaign_id', 'dormitory_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('census_responses');
    }
};
