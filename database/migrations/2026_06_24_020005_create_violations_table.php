<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('violations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('person_id')->constrained('persons')->cascadeOnDelete();
            $table->foreignUuid('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignUuid('violation_type_id')->constrained('master_data')->cascadeOnDelete();
            $table->foreignUuid('reporter_id')->constrained('persons')->cascadeOnDelete();
            $table->dateTime('violation_date');
            $table->text('description');
            $table->string('severity');
            $table->text('punishment')->nullable();
            $table->integer('points')->default(0);
            $table->string('status')->default('reported');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('violations');
    }
};
