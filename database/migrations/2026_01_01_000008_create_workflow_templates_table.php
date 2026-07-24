<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('name'); // 'Perizinan Santri Putra', 'Pengajuan Dana Madrasah'
            $table->string('entity_type'); // 'perizinan', 'pengajuan', 'pelanggaran'
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['organization_id', 'entity_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_templates');
    }
};
