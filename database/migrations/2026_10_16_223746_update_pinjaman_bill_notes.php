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
            ->where('bill_type', 'pinjaman')
            ->where('notes', 'like', 'Dana Talangan: %')
            ->get()
            ->each(function ($bill) {
                $updatedNote = preg_replace('/^Dana Talangan:\s*/', 'Keperluan: ', $bill->notes);
                DB::table('bills')->where('id', $bill->id)->update([
                    'notes' => $updatedNote,
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal needed
    }
};
