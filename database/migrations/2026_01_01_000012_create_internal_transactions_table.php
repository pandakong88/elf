<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internal_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('requester_unit_id')->constrained('organizations');
            $table->foreignUuid('provider_unit_id')->constrained('organizations');
            $table->string('reference_type'); // 'internal_request', 'service', dll
            $table->uuid('reference_id');     // ID dari tabel rujukan
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'fulfilled', 'invoiced', 'settled'])->default('pending');
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();

            $table->index(['requester_unit_id', 'status']);
            $table->index(['provider_unit_id', 'status']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internal_transactions');
    }
};
