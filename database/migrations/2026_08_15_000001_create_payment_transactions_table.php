<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Identitas transaksi
            $table->string('merchant_order_id')->unique(); // Format: ELF-{timestamp}-{random6}
            $table->string('duitku_reference')->nullable(); // ID dari Duitku setelah createTransaction

            // Pemilik tagihan
            $table->foreignUuid('person_id')->constrained('persons')->cascadeOnDelete();

            // Tagihan yang dibayar (bisa multi-bill)
            $table->json('bill_ids');       // ["uuid1", "uuid2", ...]
            $table->json('bill_breakdown'); // [{bill_id, bill_amount, mdr_portion, net_portion}, ...]

            // Nominal
            $table->decimal('bill_amount', 12, 2);  // Total tagihan sebelum MDR
            $table->string('mdr_channel', 10)->nullable(); // Kode channel untuk MDR
            $table->decimal('mdr_rate', 6, 4)->default(0); // Contoh: 0.0070 untuk 0.7%
            $table->decimal('mdr_fixed', 10, 2)->default(0); // Contoh: 4000 (Rp 4.000)
            $table->decimal('mdr_amount', 10, 2)->default(0); // Hasil kalkulasi MDR
            $table->decimal('total_amount', 12, 2);   // Yang dibayar wali (bill + mdr)
            $table->decimal('net_amount', 12, 2);     // Yang masuk kas (total - mdr)

            // Channel pembayaran
            $table->string('payment_channel', 10)->nullable(); // SP, BR, BT, I1, M2, dll

            // Status & URL
            $table->enum('status', ['pending', 'success', 'failed', 'expired'])->default('pending');
            $table->text('payment_url')->nullable();     // URL halaman bayar dari Duitku
            $table->string('va_number')->nullable();     // Nomor VA (jika channel VA)
            $table->string('qr_string')->nullable();     // QR string (jika QRIS)

            // Waktu penting
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('callback_received_at')->nullable();
            $table->timestamp('return_url_accessed_at')->nullable();

            // Audit trail
            $table->json('raw_duitku_response')->nullable(); // Simpan seluruh response API Duitku
            $table->json('raw_callback_payload')->nullable(); // Simpan payload callback dari Duitku
            $table->foreignUuid('initiated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('failure_reason')->nullable();

            $table->timestamps();

            // Index untuk performa
            $table->index(['person_id', 'status']);
            $table->index(['merchant_order_id']);
            $table->index(['duitku_reference']);
            $table->index(['status', 'expires_at']); // Untuk job rekonsiliasi expired
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
