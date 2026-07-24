<?php

namespace App\Modules\Kepengasuhan\Services;

use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\SantriProfile;
use App\Modules\Kepengasuhan\Models\SantriSibling;
use App\Modules\Kepengasuhan\Models\SantriGuardian;
use Illuminate\Support\Facades\DB;

class SiblingService
{
    /**
     * Scan seluruh santri untuk mendeteksi saudara kandung berdasarkan:
     * 1. Kesamaan nama Ayah + Ibu (case-insensitive & trimmed)
     * 2. Kesamaan data Wali (Guardian ID yang ter-link)
     */
    public function detectSiblingsByGuardian(): int
    {
        $detectedCount = 0;

        // Ambil semua santri yang memiliki profile
        $profiles = SantriProfile::whereNotNull('father_name')
            ->whereNotNull('mother_name')
            ->get();

        // 1. Deteksi via kesamaan Nama Orang Tua
        $groupedByName = [];
        foreach ($profiles as $profile) {
            $key = strtolower(trim($profile->father_name)) . '|' . strtolower(trim($profile->mother_name));
            $groupedByName[$key][] = $profile->person_id;
        }

        foreach ($groupedByName as $key => $personIds) {
            if (count($personIds) > 1) {
                // Ada lebih dari 1 santri dengan orang tua yang sama
                $detectedCount += $this->createSiblingRelations($personIds, 'name_match');
            }
        }

        // 2. Deteksi via kesamaan Guardian ID (wali)
        $guardians = SantriGuardian::select('guardian_id')
            ->groupBy('guardian_id')
            ->havingRaw('COUNT(person_id) > 1')
            ->pluck('guardian_id');

        foreach ($guardians as $guardianId) {
            $personIds = SantriGuardian::where('guardian_id', $guardianId)
                ->pluck('person_id')
                ->toArray();
            
            $detectedCount += $this->createSiblingRelations($personIds, 'guardian_match');
        }

        return $detectedCount;
    }

    /**
     * Buat relasi saudara kandung untuk list person IDs.
     */
    private function createSiblingRelations(array $personIds, string $source): int
    {
        $created = 0;
        $count = count($personIds);

        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $p1 = $personIds[$i];
                $p2 = $personIds[$j];

                // Selalu simpan dengan person_id < sibling_person_id untuk konsistensi
                $first = $p1 < $p2 ? $p1 : $p2;
                $second = $p1 < $p2 ? $p2 : $p1;

                // Cek apakah relasi sudah ada
                $exists = SantriSibling::where('person_id', $first)
                    ->where('sibling_person_id', $second)
                    ->exists();

                if (!$exists) {
                    SantriSibling::create([
                        'person_id' => $first,
                        'sibling_person_id' => $second,
                        'relationship' => 'saudara',
                        'auto_detected' => true,
                        'is_confirmed' => false,
                        'is_eligible_for_discount' => false,
                        'notes' => 'Terdeteksi otomatis via ' . ($source === 'name_match' ? 'kesamaan nama orang tua' : 'kesamaan wali'),
                    ]);
                    $created++;
                }
            }
        }

        return $created;
    }

    /**
     * Konfirmasi hubungan saudara kandung.
     */
    public function confirmSibling(string $siblingRelationId, string $relationship, string $confirmedByUserId): bool
    {
        return DB::transaction(function () use ($siblingRelationId, $relationship, $confirmedByUserId) {
            $sibling = SantriSibling::findOrFail($siblingRelationId);
            
            $sibling->update([
                'relationship' => $relationship,
                'is_confirmed' => true,
                'confirmed_by' => $confirmedByUserId,
                'confirmed_at' => now(),
                'is_eligible_for_discount' => true, // Default true jika terkonfirmasi
            ]);

            // Update profil kedua santri
            $this->updateSiblingFlags($sibling->person_id);
            $this->updateSiblingFlags($sibling->sibling_person_id);

            return true;
        });
    }

    /**
     * Tolak/hapus hubungan saudara kandung.
     */
    public function rejectSibling(string $siblingRelationId): bool
    {
        return DB::transaction(function () use ($siblingRelationId) {
            $sibling = SantriSibling::findOrFail($siblingRelationId);
            $p1 = $sibling->person_id;
            $p2 = $sibling->sibling_person_id;
            
            $sibling->delete();

            $this->updateSiblingFlags($p1);
            $this->updateSiblingFlags($p2);

            return true;
        });
    }

    /**
     * Hitung & update flag sibling pada SantriProfile.
     */
    public function updateSiblingFlags(string $personId): void
    {
        $profile = SantriProfile::where('person_id', $personId)->first();
        if (!$profile) {
            return;
        }

        // Cari relasi terkonfirmasi di mana santri ini terlibat
        $siblingCount = SantriSibling::where('is_confirmed', true)
            ->where(function ($q) use ($personId) {
                $q->where('person_id', $personId)
                  ->orWhere('sibling_person_id', $personId);
            })
            ->count();

        $profile->update([
            'has_active_sibling' => $siblingCount > 0,
            'active_sibling_count' => $siblingCount,
        ]);
    }

    /**
     * Otomatis jalankan deteksi dan update dari data sensus yang masuk.
     */
    public function autoLinkFromCensusData(string $personId): void
    {
        // Jalankan pencarian relasi untuk santri ini saja
        $profile = SantriProfile::where('person_id', $personId)->first();
        if (!$profile || empty($profile->father_name) || empty($profile->mother_name)) {
            return;
        }

        $fatherName = strtolower(trim($profile->father_name));
        $motherName = strtolower(trim($profile->mother_name));

        // Cari santri lain dengan nama orang tua yang sama
        $otherProfiles = SantriProfile::where('person_id', '!=', $personId)
            ->whereRaw('LOWER(father_name) = ?', [$fatherName])
            ->whereRaw('LOWER(mother_name) = ?', [$motherName])
            ->pluck('person_id')
            ->toArray();

        if (count($otherProfiles) > 0) {
            $allIds = array_merge([$personId], $otherProfiles);
            $this->createSiblingRelations($allIds, 'name_match');
        }
    }

    /**
     * Ambil daftar santri yang eligible untuk diskon syahriah saudara.
     */
    public function getSiblingDiscountEligible()
    {
        return Person::whereHas('santriProfile', function ($q) {
            $q->where('has_active_sibling', true);
        })->with('santriProfile')->get();
    }
}
