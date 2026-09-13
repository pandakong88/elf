<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_transactions', 'gateway_provider')) {
                $table->string('gateway_provider', 30)->default('duitku')->after('payment_channel');
            }
            if (!Schema::hasColumn('payment_transactions', 'channel_label')) {
                $table->string('channel_label')->nullable()->after('payment_channel');
            }
            if (!Schema::hasColumn('payment_transactions', 'pocket_money_amount')) {
                $table->decimal('pocket_money_amount', 12, 2)->default(0)->after('net_amount');
            }
            if (!Schema::hasColumn('payment_transactions', 'raw_response')) {
                $table->json('raw_response')->nullable()->after('raw_callback_payload');
            }
            if (!Schema::hasColumn('payment_transactions', 'user_id')) {
                $table->foreignUuid('user_id')->nullable()->after('person_id')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn(['gateway_provider', 'channel_label', 'pocket_money_amount', 'raw_response']);
        });
    }
};
