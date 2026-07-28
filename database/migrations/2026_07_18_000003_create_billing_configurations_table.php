<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_configurations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type', 50);
            $table->string('label');
            $table->decimal('amount', 10, 2);
            $table->foreignUuid('dormitory_id')->nullable()->constrained('dormitories')->nullOnDelete();
            $table->date('effective_from');
            $table->boolean('is_active')->default(true);
            $table->foreignUuid('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_configurations');
    }
};
