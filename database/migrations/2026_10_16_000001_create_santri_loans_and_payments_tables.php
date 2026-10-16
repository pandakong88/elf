<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('santri_loans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('loan_code', 50)->unique();
            $table->foreignUuid('person_id')->constrained('persons')->cascadeOnDelete();
            $table->char('gender', 1)->default('L')->index(); // L or P for strict scoping and fast query
            $table->decimal('amount', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0.00);
            $table->enum('status', ['unpaid', 'partial', 'paid', 'cancelled'])->default('unpaid')->index();
            $table->date('borrowed_at');
            $table->date('due_date')->nullable();
            $table->string('reason', 255)->nullable();
            $table->boolean('is_visible_to_guardian')->default(false)->index();
            $table->foreignUuid('bill_id')->nullable()->constrained('bills')->nullOnDelete();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['person_id', 'status']);
            $table->index(['gender', 'status']);
        });

        Schema::create('santri_loan_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('loan_id')->constrained('santri_loans')->cascadeOnDelete();
            $table->string('payment_code', 50)->unique();
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['cash', 'transfer', 'portal_wali'])->default('cash');
            $table->date('payment_date');
            $table->foreignUuid('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('receipt_no', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['loan_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('santri_loan_payments');
        Schema::dropIfExists('santri_loans');
    }
};
