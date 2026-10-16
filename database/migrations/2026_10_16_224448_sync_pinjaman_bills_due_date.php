<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('bills')
            ->join('santri_loans', 'bills.reference_id', '=', 'santri_loans.id')
            ->where('bills.bill_type', 'pinjaman')
            ->whereNull('santri_loans.due_date')
            ->update([
                'bills.due_date' => null,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal needed
    }
};
