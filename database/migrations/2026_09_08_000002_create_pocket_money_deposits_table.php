<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pocket_money_deposits', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relasi ke santri
            $table->foreignUuid('person_id')->constrained('persons')->cascadeOnDelete();

            // Nominal Uang Saku
            $table->decimal('amount', 12, 2);

            // Sumber Transaksi
            $table->enum('source', ['manual_transfer', 'cashier', 'gateway_duitku'])->default('manual_transfer');
            $table->uuid('reference_id')->nullable(); // ID dari manual_transfer_submission atau payment_transaction

            // Status Pencairan / Distribusi ke Santri
            $table->enum('status', ['pending', 'received', 'disbursed'])->default('received');
            $table->text('notes')->nullable();

            // Penerima / Bendahara yang mengelola
            $table->foreignUuid('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('received_at')->nullable();

            // Penyerahan ke Musyrif / Santri
            $table->foreignUuid('disbursed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disbursed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['person_id', 'status']);
            $table->index(['source', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pocket_money_deposits');
    }
};
