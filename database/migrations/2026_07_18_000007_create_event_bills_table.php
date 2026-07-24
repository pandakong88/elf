<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_bills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('event_name');
            $table->date('event_date');
            $table->decimal('default_amount', 10, 2);
            $table->foreignUuid('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_bills');
    }
};
