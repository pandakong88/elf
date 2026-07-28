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
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE billing_configurations MODIFY COLUMN type VARCHAR(50) NOT NULL");
            DB::statement("ALTER TABLE bills MODIFY COLUMN bill_type VARCHAR(50) NOT NULL");
        }
    }

    public function down(): void
    {
    }
};
