<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('census_template_fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('template_id')->constrained('census_templates')->cascadeOnDelete();
            $table->string('group_name')->default('Umum');
            $table->string('field_key');
            $table->string('field_label');
            $table->enum('field_type', ['text', 'textarea', 'dropdown', 'boolean', 'number', 'date']);
            $table->json('field_options')->nullable(); // untuk dropdown
            $table->string('placeholder_text')->nullable();
            $table->string('help_text')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_system_field')->default(false); // linked ke santri_profiles
            $table->string('profile_field_key')->nullable(); // kolom di santri_profiles
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['template_id', 'field_key']);
            $table->index(['template_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('census_template_fields');
    }
};
