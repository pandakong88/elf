<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lockers', function (Blueprint $table) {
            $table->string('allocation_type', 30)->default('santri')->after('assigned_person_id');
            $table->string('custom_holder_name', 150)->nullable()->after('allocation_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lockers', function (Blueprint $table) {
            $table->dropColumn(['allocation_type', 'custom_holder_name']);
        });
    }
};
