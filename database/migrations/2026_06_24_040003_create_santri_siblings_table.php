<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('santri_siblings', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Dua sisi relasi saudara (selalu simpan person_id < sibling_person_id secara alfabet untuk menghindari duplikat)
            $table->foreignUuid('person_id')
                ->constrained('persons')
                ->cascadeOnDelete();

            $table->foreignUuid('sibling_person_id')
                ->constrained('persons')
                ->cascadeOnDelete();

            // Hubungan dari perspektif person_id ke sibling_person_id
            // kakak = sibling_person_id adalah kakak dari person_id
            // adik  = sibling_person_id adalah adik dari person_id
            // kembar = kembar
            $table->string('relationship')->default('saudara')
                ->comment('kakak|adik|kembar|saudara');

            // Apakah terdeteksi otomatis dari kesamaan data wali
            $table->boolean('auto_detected')->default(false);

            // Apakah sudah dikonfirmasi secara manual
            $table->boolean('is_confirmed')->default(false);

            $table->foreignUuid('confirmed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('confirmed_at')->nullable();

            // Flag: apakah relasi ini eligible untuk diskon syahriah
            // (bisa di-override manual jika ada kondisi khusus)
            $table->boolean('is_eligible_for_discount')->default(true);

            $table->text('notes')->nullable();

            $table->timestamps();

            // Pastikan tidak ada duplikat relasi bolak-balik
            $table->unique(['person_id', 'sibling_person_id']);
            $table->index('is_confirmed');
            $table->index('auto_detected');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('santri_siblings');
    }
};
