<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('person_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('person_id')->constrained('persons')->cascadeOnDelete();
            $table->foreignUuid('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->enum('role_type', ['santri', 'wali', 'guru', 'pengurus', 'pegawai', 'umum']);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Satu orang bisa punya role yang sama di organisasi yang sama hanya sekali aktif
            $table->index(['person_id', 'organization_id', 'role_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person_roles');
    }
};
