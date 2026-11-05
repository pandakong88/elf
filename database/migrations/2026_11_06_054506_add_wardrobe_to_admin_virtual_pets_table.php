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
        Schema::table('admin_virtual_pets', function (Blueprint $table) {
            $table->json('wardrobe')->nullable()->after('inventory');
            $table->json('equipped_outfit')->nullable()->after('wardrobe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_virtual_pets', function (Blueprint $table) {
            $table->dropColumn(['wardrobe', 'equipped_outfit']);
        });
    }
};
