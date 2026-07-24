<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_instances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('template_id')->constrained('workflow_templates');
            $table->string('entity_type'); // 'perizinan', 'pengajuan', dll
            $table->uuid('entity_id');     // polymorphic — ID dari tabel entity terkait
            $table->integer('current_step')->default(1);
            $table->enum('status', ['pending', 'in_progress', 'approved', 'rejected', 'cancelled'])
                  ->default('pending');
            $table->foreignUuid('initiated_by')->constrained('persons');
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index(['status', 'template_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_instances');
    }
};
