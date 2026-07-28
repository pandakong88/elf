<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_configurations', function (Blueprint $table) {
            $table->string('due_day_type')->default('fixed_day')->after('interval');
            $table->unsignedSmallInteger('due_day_value')->default(10)->after('due_day_type');
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->unsignedTinyInteger('period_sub')->nullable()->after('period_year');
        });
    }

    public function down(): void
    {
        Schema::table('billing_configurations', function (Blueprint $table) {
            $table->dropColumn(['due_day_type', 'due_day_value']);
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn('period_sub');
        });
    }
};
