<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('census_periods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->tinyInteger('month'); // 1-12
            $table->year('year');
            $table->string('status')->default('draft'); // draft, active, closed
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('census_periods');
    }
};
