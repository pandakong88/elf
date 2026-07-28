<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE billing_configurations MODIFY COLUMN type VARCHAR(50) NOT NULL");
            DB::statement("ALTER TABLE bills MODIFY COLUMN bill_type VARCHAR(50) NOT NULL");
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');

            // Re-create billing_configurations in SQLite without enum CHECK constraint
            Schema::dropIfExists('billing_configurations_temp');
            DB::statement('CREATE TABLE billing_configurations_temp AS SELECT * FROM billing_configurations;');
            Schema::dropIfExists('billing_configurations');

            Schema::create('billing_configurations', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type', 50);
                $table->string('label');
                $table->decimal('amount', 10, 2);
                $table->foreignUuid('dormitory_id')->nullable();
                $table->date('effective_from');
                $table->boolean('is_active')->default(true);
                $table->foreignUuid('created_by');
                $table->timestamps();

                $table->string('interval', 20)->default('monthly');
                $table->text('manager_role')->nullable();
                $table->string('target_type', 20)->default('all');
                $table->text('target_filters')->nullable();
                $table->boolean('can_be_installment')->default(false);
                $table->text('manager_ids')->nullable();
                $table->string('sub_cycle', 20)->default('monthly');
                $table->string('due_day_type', 20)->default('end_of_period');
                $table->unsignedSmallInteger('due_day_value')->nullable();
                $table->date('due_date_specific')->nullable();
            });

            if (Schema::hasTable('billing_configurations_temp')) {
                $cols = Schema::getColumnListing('billing_configurations_temp');
                if (!empty($cols)) {
                    $colString = implode(', ', array_map(fn($c) => '"' . $c . '"', $cols));
                    DB::statement("INSERT INTO billing_configurations ({$colString}) SELECT {$colString} FROM billing_configurations_temp;");
                }
                Schema::dropIfExists('billing_configurations_temp');
            }

            // Re-create bills in SQLite without enum CHECK constraint
            Schema::dropIfExists('bills_temp');
            DB::statement('CREATE TABLE bills_temp AS SELECT * FROM bills;');
            Schema::dropIfExists('bills');

            Schema::create('bills', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('person_id');
                $table->string('bill_type', 50);
                $table->foreignUuid('billing_config_id')->nullable();
                $table->uuid('reference_id')->nullable();
                $table->unsignedTinyInteger('period_month')->nullable();
                $table->unsignedSmallInteger('period_year')->nullable();
                $table->decimal('amount', 10, 2);
                $table->decimal('amount_paid', 10, 2)->default(0.00);
                $table->string('status', 30)->default('unpaid');
                $table->date('due_date')->nullable();
                $table->string('managed_by_role', 50)->default('bendahara-pusat');
                $table->text('notes')->nullable();
                $table->foreignUuid('created_by');
                $table->timestamps();
                $table->softDeletes();

                // Advanced & Subcycle columns added in previous migrations
                $table->foreignUuid('parent_bill_id')->nullable();
                $table->unsignedTinyInteger('installment_step')->nullable();
                $table->uuid('installment_plan_id')->nullable();
                $table->string('period_sub', 20)->nullable();
            });

            if (Schema::hasTable('bills_temp')) {
                $cols = Schema::getColumnListing('bills_temp');
                if (!empty($cols)) {
                    $colString = implode(', ', array_map(fn($c) => '"' . $c . '"', $cols));
                    DB::statement("INSERT INTO bills ({$colString}) SELECT {$colString} FROM bills_temp;");
                }
                Schema::dropIfExists('bills_temp');
            }

            DB::statement('PRAGMA foreign_keys = ON;');
        }
    }

    public function down(): void
    {
    }
};
