<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('santri_leaves', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('person_id')->constrained('persons')->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('start_month');
            $table->unsignedTinyInteger('end_month');
            $table->json('months')->nullable();
            $table->string('scope_type', 20)->default('all'); // 'all' | 'specific'
            $table->json('config_ids')->nullable();
            $table->string('reason')->nullable();
            $table->foreignUuid('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['person_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('santri_leaves');
    }
};
