<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_configurations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['syahriah_pondok', 'kas_komplek', 'majek_pagi', 'majek_sore', 'syahriah_madrasah', 'kebersihan', 'kitab', 'pendaftaran']);
            $table->string('label');
            $table->decimal('amount', 10, 2);
            $table->foreignUuid('dormitory_id')->nullable()->constrained('dormitories')->nullOnDelete();
            $table->date('effective_from');
            $table->boolean('is_active')->default(true);
            $table->foreignUuid('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_configurations');
    }
};
