<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_transfer_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('submission_code')->unique(); // e.g. TRF-20260908-ABC12

            // Relasi ke santri
            $table->foreignUuid('person_id')->constrained('persons')->cascadeOnDelete();

            // Rincian Tagihan & Nominal
            $table->json('bill_ids');                         // Array UUID tagihan
            $table->json('bill_breakdown');                   // Detail breakdown per tagihan
            $table->decimal('total_bills_amount', 12, 2)->default(0);
            $table->decimal('pocket_money_amount', 12, 2)->default(0);
            $table->decimal('total_transfer_amount', 12, 2);

            // Informasi Bank & Rekening Pengirim / Tujuan
            $table->string('bank_destination')->nullable();   // Bank pesantren yang dituju
            $table->string('sender_bank')->nullable();        // Bank pengirim
            $table->string('sender_account_name')->nullable(); // Atas nama pengirim
            $table->text('notes')->nullable();                // Catatan / pesan dari wali

            // Bukti Foto Struk
            $table->string('proof_image_path');               // Path foto struk terkompresi di storage

            // Status Verifikasi
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();

            // Audit Verifikasi oleh Bendahara
            $table->foreignUuid('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->string('receipt_no')->nullable();         // No. Kuitansi setelah disetujui (KSR-...)

            $table->timestamps();
            $table->softDeletes();

            // Index performa
            $table->index(['person_id', 'status']);
            $table->index(['submission_code']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_transfer_submissions');
    }
};
