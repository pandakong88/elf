<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Identitas
            $table->string('name');
            $table->string('national_id')->nullable()->comment('NIK wali');

            // Hubungan ke santri (deskriptif, relasi aktual di pivot)
            $table->string('gender')->nullable()->comment('L/P');

            // Kontak
            $table->string('phone_primary')->nullable();
            $table->string('phone_secondary')->nullable();
            $table->string('email')->nullable();

            // Pekerjaan & Ekonomi
            $table->string('occupation')->nullable()->comment('Pekerjaan wali');
            $table->string('education_level')->nullable()->comment('SD/SMP/SMA/S1/S2/S3/dll');
            $table->string('income_range')->nullable()->comment('cth: < 2jt, 2-5jt, > 5jt');

            // Alamat
            $table->text('address')->nullable();
            $table->string('village')->nullable()->comment('Desa/Kelurahan');
            $table->string('district')->nullable()->comment('Kecamatan');
            $table->string('city')->nullable()->comment('Kota/Kabupaten');
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();

            // Catatan
            $table->text('notes')->nullable();

            // Meta
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('name');
            $table->index('phone_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
