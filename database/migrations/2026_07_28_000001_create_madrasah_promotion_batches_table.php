<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('madrasah_promotion_batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('from_academic_year', 20);
            $table->string('to_academic_year', 20);
            $table->timestamp('executed_at');
            $table->uuid('executed_by')->nullable();
            $table->string('executed_by_name')->nullable();
            $table->integer('total_students')->default(0);
            $table->integer('total_promoted')->default(0);
            $table->integer('total_retained')->default(0);
            $table->integer('total_graduated')->default(0);
            $table->string('status', 20)->default('sukses'); // 'sukses' | 'di_undo'
            $table->timestamp('undone_at')->nullable();
            $table->uuid('undone_by')->nullable();
            $table->string('undone_by_name')->nullable();
            $table->timestamps();
        });

        Schema::create('madrasah_promotion_batch_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('batch_id');
            $table->uuid('person_id');
            $table->uuid('source_kelas_id')->nullable();
            $table->uuid('target_kelas_id')->nullable();
            $table->string('status', 20); // 'promoted', 'retained', 'graduated'
            $table->uuid('previous_enrollment_id')->nullable();
            $table->uuid('new_enrollment_id')->nullable();
            $table->string('previous_person_role_status', 30)->nullable();
            $table->timestamps();

            $table->foreign('batch_id')
                ->references('id')
                ->on('madrasah_promotion_batches')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('madrasah_promotion_batch_items');
        Schema::dropIfExists('madrasah_promotion_batches');
    }
};
