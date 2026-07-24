<?php

namespace App\Modules\Kepengasuhan\Services;

use App\Modules\Kepengasuhan\Models\Guardian;
use App\Modules\Kepengasuhan\Models\SantriGuardian;
use App\Modules\Core\Models\Person;
use Illuminate\Support\Facades\DB;

class GuardianService
{
    /**
     * Cari wali berdasarkan Nama dan No HP, jika tidak ditemukan buat baru.
     * Jika ditemukan, update field yang diisi.
     */
    public function createOrFindGuardian(array $data): Guardian
    {
        $name = trim($data['name'] ?? '');
        $phone = trim($data['phone_primary'] ?? '');

        if (empty($name)) {
            throw new \InvalidArgumentException('Nama wali tidak boleh kosong.');
        }

        // Cari duplikasi case-insensitive nama + phone_primary
        $guardian = Guardian::whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->when(!empty($phone), function ($q) use ($phone) {
                return $q->where('phone_primary', $phone);
            })
            ->first();

        $fillableData = array_intersect_key($data, array_flip((new Guardian())->getFillable()));

        if ($guardian) {
            // Update data yang dikirim jika berbeda / baru
            $guardian->update(array_filter($fillableData, fn($val) => $val !== null && $val !== ''));
        } else {
            $guardian = Guardian::create($fillableData);
        }

        return $guardian;
    }

    /**
     * Hubungkan wali dengan santri.
     */
    public function linkGuardianToSantri(
        string $guardianId,
        string $personId,
        string $relationship,
        bool $isPrimary = false,
        int $priorityOrder = 1,
        ?string $notes = null
    ): SantriGuardian {
        return DB::transaction(function () use ($guardianId, $personId, $relationship, $isPrimary, $priorityOrder, $notes) {
            if ($isPrimary) {
                // Nonaktifkan primary guardian lain untuk santri ini
                SantriGuardian::where('person_id', $personId)
                    ->update(['is_primary' => false]);
            }

            // Gunakan updateOrCreate untuk menghindari duplikat link
            return SantriGuardian::updateOrCreate(
                [
                    'person_id' => $personId,
                    'guardian_id' => $guardianId,
                ],
                [
                    'relationship' => $relationship,
                    'is_primary' => $isPrimary,
                    'priority_order' => $priorityOrder,
                    'notes' => $notes,
                ]
            );
        });
    }

    /**
     * Putuskan hubungan wali dengan santri.
     */
    public function unlinkGuardian(string $guardianId, string $personId): bool
    {
        return SantriGuardian::where('guardian_id', $guardianId)
            ->where('person_id', $personId)
            ->delete() > 0;
    }

    /**
     * Ambil daftar santri untuk wali tertentu.
     */
    public function getSantrisByGuardian(string $guardianId)
    {
        $guardian = Guardian::findOrFail($guardianId);
        return $guardian->santri;
    }
}
