<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bill_payments', function (Blueprint $table) {
            $table->string('receipt_no', 50)->nullable()->index()->after('id');
            $table->uuid('payment_group_id')->nullable()->index()->after('receipt_no');
            $table->decimal('tendered_amount', 12, 2)->nullable()->after('amount_paid');
            $table->decimal('change_amount', 12, 2)->nullable()->default(0)->after('tendered_amount');
        });
    }

    public function down(): void
    {
        Schema::table('bill_payments', function (Blueprint $table) {
            $table->dropColumn(['receipt_no', 'payment_group_id', 'tendered_amount', 'change_amount']);
        });
    }
};
