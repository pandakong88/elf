<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Illuminate\Support\Facades\Schema::table('billing_configurations', function (Blueprint $table) {
            $table->json('manager_ids')->nullable()->after('manager_role');
        });
    }

    public function down(): void
    {
        Illuminate\Support\Facades\Schema::table('billing_configurations', function (Blueprint $table) {
            $table->dropColumn('manager_ids');
        });
    }
};
