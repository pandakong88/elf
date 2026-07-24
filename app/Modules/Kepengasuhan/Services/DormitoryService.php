<?php

namespace App\Modules\Kepengasuhan\Services;

use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DormitoryService
{
    /**
     * Buat asrama baru.
     */
    public function createDormitory(array $data): Dormitory
    {
        return Dormitory::create([
            'id'              => Str::uuid()->toString(),
            'organization_id' => $data['organization_id'] ?? null,
            'name'            => $data['name'],
            'gender'          => $data['gender'],
            'description'     => $data['description'] ?? null,
            'is_active'       => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Buat kamar baru dalam asrama.
     */
    public function createRoom(string $dormitoryId, array $data): Room
    {
        $dormitory = Dormitory::findOrFail($dormitoryId);

        return Room::create([
            'id'           => Str::uuid()->toString(),
            'dormitory_id' => $dormitory->id,
            'name'         => $data['name'],
            'capacity'     => $data['capacity'],
            'description'  => $data['description'] ?? null,
            'is_active'    => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Assign santri ke kamar.
     * Validasi kapasitas kamar & batasi satu santri di satu kamar aktif saja.
     *
     * @throws DomainException
     */
    public function assignRoom(string $roomId, string $personId, string $validFrom, ?string $validUntil = null): RoomAssignment
    {
        $room = Room::findOrFail($roomId);
        $person = Person::findOrFail($personId);

        if (! $room->is_active) {
            throw new DomainException("Kamar '{$room->name}' tidak aktif.");
        }

        // Cek keselarasan gender santri dan komplek
        if ($person->gender && $room->dormitory && $person->gender !== $room->dormitory->gender) {
            $genderLabel = $person->gender === 'L' ? 'Laki-laki' : 'Perempuan';
            $dormGenderLabel = $room->dormitory->gender === 'L' ? 'Laki-laki (Putra)' : 'Perempuan (Putri)';
            throw new DomainException("Gender santri ({$genderLabel}) tidak sesuai dengan Komplek {$room->dormitory->name} ({$dormGenderLabel}).");
        }

        // Cek kapasitas kamar
        $currentOccupantsCount = $room->currentAssignments()->count();
        if ($currentOccupantsCount >= $room->capacity) {
            throw new DomainException("Kamar '{$room->name}' sudah penuh (kapasitas: {$room->capacity}).");
        }

        return DB::transaction(function () use ($room, $person, $validFrom, $validUntil) {
            // Nonaktifkan semua assignment aktif untuk person ini (santri hanya boleh punya satu kamar aktif)
            RoomAssignment::where('person_id', $person->id)
                ->where('is_active', true)
                ->update([
                    'is_active'   => false,
                    'valid_until' => now()->subDay()->toDateString(),
                ]);

            return RoomAssignment::create([
                'id'          => Str::uuid()->toString(),
                'room_id'     => $room->id,
                'person_id'   => $person->id,
                'valid_from'  => $validFrom,
                'valid_until' => $validUntil,
                'is_active'   => true,
            ]);
        });
    }

    /**
     * Ambil penghuni aktif di suatu kamar.
     */
    public function getCurrentOccupants(string $roomId)
    {
        $room = Room::findOrFail($roomId);
        return Person::whereIn('id', function ($query) use ($room) {
            $query->select('person_id')
                ->from('room_assignments')
                ->where('room_id', $room->id)
                ->where('is_active', true);
        })->get();
    }

    /**
     * Update data asrama.
     */
    public function updateDormitory(string $id, array $data): Dormitory
    {
        $dormitory = Dormitory::findOrFail($id);
        $dormitory->update([
            'name'        => $data['name']        ?? $dormitory->name,
            'gender'      => $data['gender']      ?? $dormitory->gender,
            'description' => $data['description'] ?? $dormitory->description,
        ]);

        return $dormitory->fresh();
    }

    /**
     * Toggle status aktif/nonaktif asrama.
     */
    public function toggleDormitoryStatus(string $id): Dormitory
    {
        $dormitory = Dormitory::findOrFail($id);
        $dormitory->update(['is_active' => !$dormitory->is_active]);
        return $dormitory->fresh();
    }

    /**
     * Update data kamar.
     */
    public function updateRoom(string $id, array $data): Room
    {
        $room = Room::findOrFail($id);

        // Validasi kapasitas baru tidak kurang dari jumlah penghuni aktif
        if (isset($data['capacity'])) {
            $currentOccupants = $room->currentAssignments()->count();
            if ($data['capacity'] < $currentOccupants) {
                throw new DomainException("Kapasitas baru ({$data['capacity']}) tidak boleh kurang dari jumlah penghuni aktif ({$currentOccupants}).");
            }
        }

        $room->update([
            'name'        => $data['name']        ?? $room->name,
            'capacity'    => $data['capacity']    ?? $room->capacity,
            'description' => $data['description'] ?? $room->description,
        ]);

        return $room->fresh();
    }

    /**
     * Toggle status aktif/nonaktif kamar.
     */
    public function toggleRoomStatus(string $id): Room
    {
        $room = Room::findOrFail($id);
        $room->update(['is_active' => !$room->is_active]);
        return $room->fresh();
    }

    /**
     * Lepaskan santri dari kamar (nonaktifkan assignment).
     */
    public function unassignRoom(string $assignmentId): void
    {
        RoomAssignment::where('id', $assignmentId)
            ->where('is_active', true)
            ->update([
                'is_active'   => false,
                'valid_until' => now()->toDateString(),
            ]);
    }

    /**
     * Ambil santri yang belum memiliki kamar aktif berdasarkan gender.
     */
    public function getSantriWithoutRoom(string $gender, ?string $search = null)
    {
        $assignedPersonIds = RoomAssignment::where('is_active', true)->pluck('person_id')->toArray();

        $query = Person::byRole('santri')
            ->where('gender', $gender)
            ->whereNotIn('id', $assignedPersonIds);

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return $query->orderBy('name')->get();
    }
}
