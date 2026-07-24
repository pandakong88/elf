<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Tambah kolom baru di billing_configurations
        Schema::table('billing_configurations', function (Blueprint $table) {
            $table->string('interval')->default('monthly')->after('effective_from'); // 'monthly', 'semester', 'yearly', 'insidental'
            $table->string('manager_role')->nullable()->after('interval'); // e.g. 'bendahara-madrasah', 'petugas-kebersihan'
        });

        // 2. Tambah self-relation untuk cicilan di bills
        Schema::table('bills', function (Blueprint $table) {
            $table->foreignUuid('parent_bill_id')->nullable()->after('id')->constrained('bills')->cascadeOnDelete();
        });

        // 3. Buat tabel billing_exceptions untuk diskon/custom rate santri
        Schema::create('billing_exceptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('billing_config_id')->constrained('billing_configurations')->cascadeOnDelete();
            $table->foreignUuid('person_id')->constrained('persons')->cascadeOnDelete();
            $table->enum('exception_type', ['discount', 'waived', 'custom_rate']);
            $table->decimal('amount', 10, 2)->default(0.00); // nominal khusus / potongan
            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->constrained('users');
            $table->timestamps();

            $table->unique(['billing_config_id', 'person_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_exceptions');

        Schema::table('bills', function (Blueprint $table) {
            $table->dropForeign(['parent_bill_id']);
            $table->dropColumn('parent_bill_id');
        });

        Schema::table('billing_configurations', function (Blueprint $table) {
            $table->dropColumn(['interval', 'manager_role']);
        });
    }
};
