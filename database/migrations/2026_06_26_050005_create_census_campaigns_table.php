<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('census_campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignUuid('template_id')->constrained('census_templates');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');

            // Target
            $table->enum('target_scope', ['all', 'putra', 'putri', 'custom_dormitories'])->default('all');

            // Workflow / Mode Distribusi
            $table->enum('workflow_mode', ['admin_only', 'distributed', 'excel', 'hybrid'])->default('admin_only');
            $table->boolean('allow_excel')->default(false);
            $table->boolean('allow_direct_input')->default(true);

            // Timeline
            $table->date('deadline')->nullable();
            $table->enum('status', ['draft', 'active', 'collecting', 'review', 'closed'])->default('draft');
            $table->dateTime('opened_at')->nullable();
            $table->dateTime('closed_at')->nullable();

            $table->foreignUuid('created_by')->constrained('users');
            $table->foreignUuid('approved_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['status', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('census_campaigns');
    }
};
