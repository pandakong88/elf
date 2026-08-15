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

        if ($driver === 'sqlite') {
            DB::statement("PRAGMA foreign_keys=OFF;");
            
            // Recreate table bill_payments without the strict enum check constraint
            DB::statement("CREATE TABLE IF NOT EXISTS bill_payments_new (
                id VARCHAR PRIMARY KEY NOT NULL,
                bill_id VARCHAR NOT NULL,
                amount_paid NUMERIC NOT NULL,
                payment_date DATE NOT NULL,
                payment_method VARCHAR NOT NULL,
                logged_by VARCHAR NOT NULL,
                notes TEXT,
                created_at DATETIME,
                updated_at DATETIME,
                FOREIGN KEY (bill_id) REFERENCES bills(id) ON DELETE CASCADE,
                FOREIGN KEY (logged_by) REFERENCES users(id)
            );");

            // Copy existing data
            DB::statement("INSERT OR IGNORE INTO bill_payments_new SELECT id, bill_id, amount_paid, payment_date, payment_method, logged_by, notes, created_at, updated_at FROM bill_payments;");

            // Drop old and rename
            DB::statement("DROP TABLE bill_payments;");
            DB::statement("ALTER TABLE bill_payments_new RENAME TO bill_payments;");

            DB::statement("PRAGMA foreign_keys=ON;");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE bill_payments MODIFY COLUMN payment_method VARCHAR(50) NOT NULL");
        }
    }

    public function down(): void
    {
        // No down necessary
    }
};
