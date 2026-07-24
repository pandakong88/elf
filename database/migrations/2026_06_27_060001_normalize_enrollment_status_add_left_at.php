<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom left_at untuk mencatat tanggal boyong/keluar
        Schema::table('person_roles', function (Blueprint $table) {
            $table->date('left_at')->nullable()->after('presence_status_notes')->comment('Tanggal santri boyong/keluar dari pondok. Berguna untuk kalkulasi masa mondok dan dashboard alumni.');
        });
    }

    public function down(): void
    {
        Schema::table('person_roles', function (Blueprint $table) {
            $table->dropColumn('left_at');
        });
    }
};
