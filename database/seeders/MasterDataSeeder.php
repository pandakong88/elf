<?php

namespace Database\Seeders;

use App\Modules\Core\Models\MasterData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            // ─── Jenis Izin ───────────────────────────────────────────────
            ['category' => 'jenis_izin', 'code' => 'PULANG_BIASA',    'name' => 'Pulang Biasa',         'metadata' => ['maks_hari' => 3]],
            ['category' => 'jenis_izin', 'code' => 'PULANG_DARURAT',  'name' => 'Pulang Darurat',       'metadata' => ['maks_hari' => 7, 'prioritas' => 'tinggi']],
            ['category' => 'jenis_izin', 'code' => 'SAKIT',           'name' => 'Izin Sakit',           'metadata' => ['butuh_surat_dokter' => true]],
            ['category' => 'jenis_izin', 'code' => 'KEGIATAN_LUAR',   'name' => 'Kegiatan Luar Pondok', 'metadata' => ['butuh_surat_tugas' => true]],
            ['category' => 'jenis_izin', 'code' => 'LAINNYA',         'name' => 'Lainnya',              'metadata' => null],

            // ─── Jenis Pelanggaran ────────────────────────────────────────
            ['category' => 'jenis_pelanggaran', 'code' => 'BOLOS_SEKOLAH', 'name' => 'Bolos Sekolah',       'metadata' => ['poin' => 10, 'kategori' => 'berat']],
            ['category' => 'jenis_pelanggaran', 'code' => 'HP_TANPA_IZIN', 'name' => 'Membawa HP Tanpa Izin', 'metadata' => ['poin' => 15, 'kategori' => 'berat']],
            ['category' => 'jenis_pelanggaran', 'code' => 'TIDAK_SHALAT',  'name' => 'Tidak Mengikuti Shalat Berjamaah', 'metadata' => ['poin' => 5, 'kategori' => 'ringan']],
            ['category' => 'jenis_pelanggaran', 'code' => 'TERLAMBAT',     'name' => 'Terlambat Masuk Kelas', 'metadata' => ['poin' => 3, 'kategori' => 'ringan']],
            ['category' => 'jenis_pelanggaran', 'code' => 'LAINNYA',       'name' => 'Pelanggaran Lainnya',   'metadata' => ['poin' => 5, 'kategori' => 'sedang']],

            // ─── Jenis Kegiatan ───────────────────────────────────────────
            ['category' => 'jenis_kegiatan', 'code' => 'NGAJI_PAGI',     'name' => 'Ngaji Pagi',              'metadata' => ['jadwal' => 'harian']],
            ['category' => 'jenis_kegiatan', 'code' => 'MUHADHARAH',      'name' => 'Muhadharah / Latihan Pidato', 'metadata' => ['jadwal' => 'mingguan']],
            ['category' => 'jenis_kegiatan', 'code' => 'OLAHRAGA',        'name' => 'Olahraga Rutin',           'metadata' => ['jadwal' => 'mingguan']],
            ['category' => 'jenis_kegiatan', 'code' => 'KERJA_BAKTI',     'name' => 'Kerja Bakti',              'metadata' => ['jadwal' => 'mingguan']],
            ['category' => 'jenis_kegiatan', 'code' => 'KAJIAN_KITAB',    'name' => 'Kajian Kitab Kuning',      'metadata' => ['jadwal' => 'harian']],

            // ─── Jenis Tagihan ────────────────────────────────────────────
            ['category' => 'jenis_tagihan', 'code' => 'SYAHRIYAH',    'name' => 'Syahriyah Bulanan',      'metadata' => ['periode' => 'bulanan']],
            ['category' => 'jenis_tagihan', 'code' => 'KITAB',         'name' => 'Pembelian Kitab',        'metadata' => ['periode' => 'insidental']],
            ['category' => 'jenis_tagihan', 'code' => 'SERAGAM',       'name' => 'Seragam Madrasah',       'metadata' => ['periode' => 'insidental']],
            ['category' => 'jenis_tagihan', 'code' => 'KESEHATAN',     'name' => 'Dana Kesehatan',         'metadata' => ['periode' => 'tahunan']],
            ['category' => 'jenis_tagihan', 'code' => 'PENDAFTARAN',   'name' => 'Biaya Pendaftaran',      'metadata' => ['periode' => 'sekali']],
        ];

        $sortMap = [];
        foreach ($entries as $entry) {
            $cat = $entry['category'];
            $sortMap[$cat] = ($sortMap[$cat] ?? 0) + 1;

            MasterData::create([
                'id'              => Str::uuid(),
                'organization_id' => null, // global
                'category'        => $entry['category'],
                'code'            => $entry['code'],
                'name'            => $entry['name'],
                'description'     => null,
                'metadata'        => $entry['metadata'],
                'sort_order'      => $sortMap[$cat],
                'is_active'       => true,
            ]);
        }

        $this->command->info('✅ MasterDataSeeder: ' . count($entries) . ' entri berhasil di-seed.');
    }
}
