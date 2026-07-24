<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('majek_periods', function (Blueprint $table) {
            $table->decimal('tarif_per_hari_putri', 10, 2)->default(3000.00)->after('tarif_per_hari');
        });
    }

    public function down(): void
    {
        Schema::table('majek_periods', function (Blueprint $table) {
            $table->dropColumn('tarif_per_hari_putri');
        });
    }
};
