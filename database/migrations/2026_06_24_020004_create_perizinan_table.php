<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perizinan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('person_id')->constrained('persons')->cascadeOnDelete();
            $table->foreignUuid('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignUuid('permission_type_id')->constrained('master_data')->cascadeOnDelete();
            $table->text('reason');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->dateTime('actual_return_date')->nullable();
            $table->foreignUuid('workflow_instance_id')->nullable()->constrained('workflow_instances')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perizinan');
    }
};
