<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('census_campaign_dormitory_assignees');

        Schema::create('census_campaign_dormitory_assignees', function (Blueprint $table) {
            $table->id();
            $table->uuid('campaign_dormitory_id');
            $table->uuid('user_id');
            $table->timestamps();

            $table->foreign('campaign_dormitory_id', 'fk_ccda_dormitory')
                ->references('id')
                ->on('census_campaign_dormitories')
                ->cascadeOnDelete();

            $table->foreign('user_id', 'fk_ccda_user')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->unique(['campaign_dormitory_id', 'user_id'], 'cd_user_unique');
        });

        // Migrasikan data yang sudah ada dari kolom assigned_to
        if (Schema::hasColumn('census_campaign_dormitories', 'assigned_to')) {
            $existing = DB::table('census_campaign_dormitories')
                ->whereNotNull('assigned_to')
                ->select('id', 'assigned_to', 'created_at', 'updated_at')
                ->get();

            foreach ($existing as $item) {
                // Pastikan user masih ada
                $userExists = DB::table('users')->where('id', $item->assigned_to)->exists();
                if ($userExists) {
                    DB::table('census_campaign_dormitory_assignees')->updateOrInsert(
                        [
                            'campaign_dormitory_id' => $item->id,
                            'user_id' => $item->assigned_to,
                        ],
                        [
                            'created_at' => $item->created_at ?? now(),
                            'updated_at' => $item->updated_at ?? now(),
                        ]
                    );
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('census_campaign_dormitory_assignees');
    }
};
