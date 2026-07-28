<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('person_id')->constrained('persons')->cascadeOnDelete();
            $table->string('bill_type', 50);
            $table->foreignUuid('billing_config_id')->nullable()->constrained('billing_configurations')->nullOnDelete();
            $table->uuid('reference_id')->nullable();
            $table->unsignedTinyInteger('period_month')->nullable();
            $table->unsignedSmallInteger('period_year')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0.00);
            $table->enum('status', ['unpaid', 'partial', 'paid', 'refund_requested', 'refunded', 'cancelled'])->default('unpaid');
            $table->date('due_date')->nullable();
            $table->string('managed_by_role')->default('bendahara-pusat');
            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['person_id', 'bill_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
