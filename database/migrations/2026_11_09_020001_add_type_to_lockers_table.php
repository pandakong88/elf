<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lockers', function (Blueprint $table) {
            $table->string('type', 20)->default('baju')->after('room_id');
            $table->index(['room_id', 'type', 'condition'], 'idx_lck_room_type_cond');
        });
    }

    public function down(): void
    {
        Schema::table('lockers', function (Blueprint $table) {
            $table->dropIndex('idx_lck_room_type_cond');
            $table->dropColumn('type');
        });
    }
};
