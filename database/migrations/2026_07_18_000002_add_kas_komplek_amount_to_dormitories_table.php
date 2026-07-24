<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dormitories', function (Blueprint $table) {
            $table->decimal('kas_komplek_amount', 10, 2)->default(5000.00)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('dormitories', function (Blueprint $table) {
            $table->dropColumn('kas_komplek_amount');
        });
    }
};
