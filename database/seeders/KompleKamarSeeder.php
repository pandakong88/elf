<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KompleKamarSeeder extends Seeder
{
    /**
     * Struktur kamar pondok:
     *
     * PUTRA & PUTRI (sama):
     *   Komplek A → Kamar A1, A2, A3           (3 kamar)
     *   Komplek B → Kamar B4, B5, B6, B7       (4 kamar)
     *   Komplek C → Kamar C8, C9, C10, C11     (4 kamar)
     *   Komplek D → Kamar D12–D17              (6 kamar)
     *
     * Kapasitas : random 5–10 per kamar
     * Kas Komplek: Rp 10.000 semua komplek
     */

    private array $strukturKomplek = [
        'A' => [1, 2, 3],
        'B' => [4, 5, 6, 7],
        'C' => [8, 9, 10, 11],
        'D' => [12, 13, 14, 15, 16, 17],
    ];

    public function run(): void
    {
        // ── 1. Bersihkan data lama ──────────────────────────────────────
        $this->command->warn('  🗑  Menghapus data dormitory, rooms, room_assignments lama...');

        RoomAssignment::query()->forceDelete();
        Room::query()->forceDelete();
        Dormitory::query()->forceDelete();

        $this->command->info('  ✅ Data lama berhasil dihapus.');

        // ── 2. Ambil organisasi putra & putri ───────────────────────────
        $orgPutra = Organization::where('slug', 'kepengasuhan-putra')->firstOrFail();
        $orgPutri = Organization::where('slug', 'kepengasuhan-putri')->firstOrFail();

        // ── 3. Buat Komplek & Kamar ─────────────────────────────────────
        $dormitoriesPutra = collect();
        $dormitoriesPutri = collect();

        foreach ($this->strukturKomplek as $huruf => $nomorKamar) {
            // --- Putra ---
            $dormPutra = Dormitory::create([
                'id'              => Str::uuid(),
                'organization_id' => $orgPutra->id,
                'name'            => "Komplek {$huruf} Putra",
                'gender'          => 'L',
                'description'     => "Komplek {$huruf} asrama putra",
                'kas_komplek_amount' => 10000,
                'is_active'       => true,
            ]);

            foreach ($nomorKamar as $no) {
                Room::create([
                    'id'           => Str::uuid(),
                    'dormitory_id' => $dormPutra->id,
                    'name'         => "Kamar {$huruf}{$no}",
                    'capacity'     => rand(5, 10),
                    'description'  => "Kamar {$huruf}{$no} Komplek {$huruf} Putra",
                    'is_active'    => true,
                ]);
            }

            $dormitoriesPutra->push($dormPutra);

            // --- Putri ---
            $dormPutri = Dormitory::create([
                'id'              => Str::uuid(),
                'organization_id' => $orgPutri->id,
                'name'            => "Komplek {$huruf} Putri",
                'gender'          => 'P',
                'description'     => "Komplek {$huruf} asrama putri",
                'kas_komplek_amount' => 10000,
                'is_active'       => true,
            ]);

            foreach ($nomorKamar as $no) {
                Room::create([
                    'id'           => Str::uuid(),
                    'dormitory_id' => $dormPutri->id,
                    'name'         => "Kamar {$huruf}{$no}",
                    'capacity'     => rand(5, 10),
                    'description'  => "Kamar {$huruf}{$no} Komplek {$huruf} Putri",
                    'is_active'    => true,
                ]);
            }

            $dormitoriesPutri->push($dormPutri);
        }

        $totalKamarPutra = Room::whereHas('dormitory', fn($q) => $q->where('gender', 'L'))->count();
        $totalKamarPutri = Room::whereHas('dormitory', fn($q) => $q->where('gender', 'P'))->count();

        $this->command->info("  ✅ Struktur asrama berhasil dibuat:");
        $this->command->info("     → Putra : 4 komplek, {$totalKamarPutra} kamar");
        $this->command->info("     → Putri : 4 komplek, {$totalKamarPutri} kamar");

        // ── 4. Assign santri mukim ke kamar ────────────────────────────
        // Ambil santri mukim yang sudah ada (dari DummySantriSeeder)
        $santriPutra = Person::whereHas('roles', fn($q) =>
            $q->where('role_type', 'santri')
              ->where('is_active', true)
              ->where('presence_status', '!=', 'laju')   // exclude laju (kalau ada)
        )->where('gender', 'L')->get();

        $santriPutri = Person::whereHas('roles', fn($q) =>
            $q->where('role_type', 'santri')
              ->where('is_active', true)
              ->where('presence_status', '!=', 'laju')
        )->where('gender', 'P')->get();

        // Kumpulkan semua kamar putra & putri
        $kamarPutra = Room::whereHas('dormitory', fn($q) => $q->where('gender', 'L'))
            ->inRandomOrder()->get();

        $kamarPutri = Room::whereHas('dormitory', fn($q) => $q->where('gender', 'P'))
            ->inRandomOrder()->get();

        $assignedCount = 0;

        // Distribusi round-robin ke kamar — santri mukim
        foreach ($santriPutra as $index => $santri) {
            $kamar = $kamarPutra[$index % $kamarPutra->count()];
            RoomAssignment::create([
                'id'         => Str::uuid(),
                'room_id'    => $kamar->id,
                'person_id'  => $santri->id,
                'valid_from' => now()->startOfYear()->toDateString(),
                'valid_until'=> null,
                'is_active'  => true,
            ]);

            // Update presence_status = mukim
            $santri->roles()
                ->where('role_type', 'santri')
                ->where('is_active', true)
                ->update(['presence_status' => 'mukim']);

            $assignedCount++;
        }

        foreach ($santriPutri as $index => $santri) {
            $kamar = $kamarPutri[$index % $kamarPutri->count()];
            RoomAssignment::create([
                'id'         => Str::uuid(),
                'room_id'    => $kamar->id,
                'person_id'  => $santri->id,
                'valid_from' => now()->startOfYear()->toDateString(),
                'valid_until'=> null,
                'is_active'  => true,
            ]);

            $santri->roles()
                ->where('role_type', 'santri')
                ->where('is_active', true)
                ->update(['presence_status' => 'mukim']);

            $assignedCount++;
        }

        $this->command->info("  ✅ {$assignedCount} santri mukim berhasil di-assign ke kamar baru.");
        $this->command->info("     → Putra : {$santriPutra->count()} santri");
        $this->command->info("     → Putri : {$santriPutri->count()} santri");
    }
}
