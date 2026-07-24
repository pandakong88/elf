<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('template_id')->constrained('workflow_templates')->cascadeOnDelete();
            $table->integer('step_order');
            $table->string('name'); // 'Pengajuan', 'Persetujuan Musyrif', 'Persetujuan Pengasuh'
            // null = tidak perlu approver spesifik (misal: step notifikasi)
            $table->foreignUuid('approver_position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->enum('action_type', ['approve', 'review', 'notify']);
            $table->integer('sla_hours')->nullable(); // batas waktu dalam jam
            $table->timestamps();

            $table->unique(['template_id', 'step_order']);
            $table->index('template_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_steps');
    }
};
