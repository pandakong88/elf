<?php

namespace App\Modules\Core\Services;

use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonPosition;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Core\Models\Position;
use Illuminate\Database\Eloquent\Collection;
use DomainException;

class PersonService
{
    /**
     * Buat person baru.
     */
    public function create(array $data): Person
    {
        if (! empty($data['nik'])) {
            $exists = Person::where('nik', $data['nik'])->exists();
            if ($exists) {
                throw new DomainException("NIK '{$data['nik']}' sudah terdaftar di sistem.");
            }
        }

        return Person::create($data);
    }

    /**
     * Update data person.
     */
    public function update(Person $person, array $data): Person
    {
        if (! empty($data['nik']) && $data['nik'] !== $person->nik) {
            $exists = Person::where('nik', $data['nik'])
                            ->where('id', '!=', $person->id)
                            ->exists();
            if ($exists) {
                throw new DomainException("NIK '{$data['nik']}' sudah terdaftar di sistem.");
            }
        }

        $person->update($data);

        return $person->fresh();
    }

    /**
     * Assign role ke person di sebuah organisasi.
     * Jika sudah ada role yang sama dan aktif, throw DomainException.
     */
    public function assignRole(
        Person $person,
        string $organizationId,
        string $roleType,
        ?string $validFrom = null,
        ?string $validUntil = null
    ): PersonRole {
        $existing = PersonRole::where('person_id', $person->id)
                              ->where('organization_id', $organizationId)
                              ->where('role_type', $roleType)
                              ->where('is_active', true)
                              ->first();

        if ($existing) {
            throw new DomainException(
                "Person '{$person->name}' sudah memiliki role '{$roleType}' di organisasi ini."
            );
        }

        return PersonRole::create([
            'person_id'       => $person->id,
            'organization_id' => $organizationId,
            'role_type'       => $roleType,
            'valid_from'      => $validFrom,
            'valid_until'     => $validUntil,
            'is_active'       => true,
        ]);
    }

    /**
     * Cabut (nonaktifkan) role dari person.
     */
    public function revokeRole(Person $person, string $organizationId, string $roleType): void
    {
        $affected = PersonRole::where('person_id', $person->id)
                              ->where('organization_id', $organizationId)
                              ->where('role_type', $roleType)
                              ->where('is_active', true)
                              ->update([
                                  'is_active'   => false,
                                  'valid_until' => now()->toDateString(),
                              ]);

        if ($affected === 0) {
            throw new DomainException(
                "Tidak ditemukan role '{$roleType}' aktif untuk person '{$person->name}'."
            );
        }
    }

    /**
     * Assign jabatan ke person.
     */
    public function assignPosition(
        Person $person,
        string $positionId,
        ?string $validFrom = null,
        ?string $validUntil = null,
        ?string $notes = null
    ): PersonPosition {
        $position = Position::findOrFail($positionId);

        return PersonPosition::create([
            'person_id'   => $person->id,
            'position_id' => $position->id,
            'valid_from'  => $validFrom,
            'valid_until' => $validUntil,
            'notes'       => $notes,
        ]);
    }

    /**
     * Ambil semua person berdasarkan role di organisasi tertentu.
     */
    public function findByRole(string $roleType, ?string $organizationId = null): Collection
    {
        return Person::byRole($roleType, $organizationId)
                     ->with(['roles' => function ($q) use ($roleType, $organizationId) {
                         $q->where('role_type', $roleType)->where('is_active', true);
                         if ($organizationId) {
                             $q->where('organization_id', $organizationId);
                         }
                     }])
                     ->get();
    }

    /**
     * Ambil semua person yang saat ini memegang sebuah jabatan.
     */
    public function findByPosition(string $positionId): Collection
    {
        return Person::whereHas('positions', function ($q) use ($positionId) {
            $q->where('position_id', $positionId)->current();
        })->get();
    }

    /**
     * Soft-delete person (beserta roles dan positions ikut dinonaktifkan).
     */
    public function deactivate(Person $person): void
    {
        // Nonaktifkan semua role
        $person->roles()->where('is_active', true)->update([
            'is_active'   => false,
            'valid_until' => now()->toDateString(),
        ]);

        // Soft-delete person
        $person->delete();
    }
}
