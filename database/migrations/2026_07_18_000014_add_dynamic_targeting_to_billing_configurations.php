<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('billing_configurations', function (Blueprint $table) {
            $table->string('target_type')->default('all')->after('manager_role'); // 'all', 'dormitory', 'kelas', 'individual'
            $table->json('target_filters')->nullable()->after('target_type'); // JSON array of targeted UUIDs
        });
    }

    public function down(): void
    {
        Schema::table('billing_configurations', function (Blueprint $table) {
            $table->dropColumn(['target_type', 'target_filters']);
        });
    }
};
