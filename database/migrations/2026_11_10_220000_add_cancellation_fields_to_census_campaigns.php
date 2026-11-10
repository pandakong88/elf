<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE census_campaigns MODIFY COLUMN status ENUM('draft', 'active', 'collecting', 'review', 'closed', 'cancelled') NOT NULL DEFAULT 'draft'");
        }

        Schema::table('census_campaigns', function (Blueprint $table) {
            $table->text('cancellation_reason')->nullable()->after('status');
            $table->dateTime('cancelled_at')->nullable()->after('closed_at');
            $table->foreignUuid('cancelled_by')->nullable()->after('approved_by')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('census_campaigns', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn(['cancellation_reason', 'cancelled_at', 'cancelled_by']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE census_campaigns MODIFY COLUMN status ENUM('draft', 'active', 'collecting', 'review', 'closed') NOT NULL DEFAULT 'draft'");
        }
    }
};
