<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_bill_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_bill_id')->constrained('event_bills')->cascadeOnDelete();
            $table->foreignUuid('person_id')->constrained('persons')->cascadeOnDelete();
            $table->decimal('original_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->string('discount_reason')->nullable();
            $table->decimal('final_amount', 10, 2);
            $table->enum('status', ['unpaid', 'paid', 'waived'])->default('unpaid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_bill_items');
    }
};
