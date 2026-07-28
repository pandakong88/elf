<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_configurations', function (Blueprint $table) {
            $table->date('due_date_specific')->nullable()->after('due_day_value');
        });
    }

    public function down(): void
    {
        Schema::table('billing_configurations', function (Blueprint $table) {
            $table->dropColumn('due_date_specific');
        });
    }
};
