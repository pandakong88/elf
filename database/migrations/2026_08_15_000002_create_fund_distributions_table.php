<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fund_distributions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Periode distribusi
            $table->date('period_from');
            $table->date('period_to');

            // Target gender
            $table->string('gender', 1); // 'L' = Putra, 'P' = Putri

            // Nominal
            $table->decimal('total_gross', 14, 2)->default(0); // Total kotor semua pembayaran
            $table->decimal('total_mdr', 12, 2)->default(0);   // Total fee MDR (proporsional)
            $table->decimal('total_net', 14, 2)->default(0);   // Bersih = gross - mdr

            // Rincian per jenis tagihan (JSON)
            // Format: {"syahriah_pondok": {"gross": X, "mdr": Y, "net": Z, "count": N}, ...}
            $table->json('breakdown')->nullable();

            // Sumber pembayaran (untuk rekonsiliasi)
            $table->decimal('online_amount', 14, 2)->default(0);  // Dari gateway Duitku
            $table->decimal('manual_amount', 14, 2)->default(0);  // Dari input manual bendahara
            $table->integer('online_count')->default(0);
            $table->integer('manual_count')->default(0);

            // Status distribusi
            $table->enum('status', ['draft', 'distributed'])->default('draft');
            $table->timestamp('distributed_at')->nullable();
            $table->foreignUuid('distributed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();

            $table->timestamps();

            // Cegah duplikat distribusi untuk periode + gender yang sama
            $table->index(['period_from', 'period_to', 'gender']);
            $table->index(['status', 'gender']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fund_distributions');
    }
};
