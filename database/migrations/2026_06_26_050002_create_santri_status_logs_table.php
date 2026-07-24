<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('santri_status_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('person_id')->constrained('persons')->cascadeOnDelete();
            $table->foreignUuid('role_id')->constrained('person_roles')->cascadeOnDelete();
            $table->enum('changed_field', ['enrollment_status', 'presence_status']);
            $table->string('old_value')->nullable();
            $table->string('new_value');
            $table->foreignUuid('changed_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->dateTime('changed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('santri_status_logs');
    }
};
