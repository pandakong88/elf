<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('census_campaigns', function (Blueprint $table) {
            $table->string('census_type', 30)->default('formal')->after('description');
            $table->uuid('template_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('census_campaigns', function (Blueprint $table) {
            $table->dropColumn('census_type');
        });
    }
};
