<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_instance_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('instance_id')->constrained('workflow_instances')->cascadeOnDelete();
            $table->integer('step_order');
            $table->enum('action', ['submitted', 'approved', 'rejected', 'noted']);
            $table->foreignUuid('actor_id')->constrained('persons');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('instance_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_instance_logs');
    }
};
