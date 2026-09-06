<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // MySQL: ALTER COLUMN enum langsung
            DB::statement("
                ALTER TABLE bill_payments
                MODIFY COLUMN payment_method ENUM('cash', 'transfer', 'gateway_duitku') NOT NULL
            ");
        } elseif ($driver === 'sqlite') {
            // SQLite: tidak support ALTER COLUMN enum, pakai string biasa
            // (SQLite sudah menggunakan string di rebuild migration sebelumnya,
            //  jadi tidak ada masalah — nilai 'gateway_duitku' sudah bisa disimpan)
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TYPE bill_payments_payment_method_enum ADD VALUE IF NOT EXISTS 'gateway_duitku'");
        }
    }

    public function down(): void
    {
        // Rollback enum: hapus 'gateway_duitku' (MySQL tidak support DROP ENUM VALUE langsung)
        // Dibiarkan kosong — tidak worth rollback karena bisa break data existing
    }
};
