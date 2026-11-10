<?php

namespace App\Modules\Kepengasuhan\Services;

use App\Modules\Kepengasuhan\Models\Locker;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\Dormitory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use DomainException;

class LockerService
{
    /**
     * Generate lemari / rak massal untuk sebuah kamar (misal: L-01 s/d L-08, B-01, atau K-01).
     */
    public function generateLockersForRoom(string $roomId, int $count, string $prefix = 'L-', string $type = 'baju'): array
    {
        $room = Room::findOrFail($roomId);
        $created = [];

        if ($prefix === 'L-' && $type === 'kitab') {
            $prefix = 'K-';
        }

        for ($i = 1; $i <= $count; $i++) {
            $code = $prefix . str_pad((string) $i, 2, '0', STR_PAD_LEFT);

            // Cek apakah sudah ada kode yang sama di kamar ini
            $existing = Locker::where('room_id', $roomId)->where('code', $code)->first();
            if (!$existing) {
                $locker = Locker::create([
                    'id'                 => (string) Str::uuid(),
                    'room_id'            => $roomId,
                    'type'               => $type,
                    'code'               => $code,
                    'assigned_person_id' => null,
                    'condition'          => 'baik',
                    'is_active'          => true,
                ]);
                $created[] = $locker;
            }
        }

        return $created;
    }

    /**
     * Generate satu set lengkap perabot kamar: Lemari Baju (B-) & Rak Kitab (K-).
     */
    public function generateRoomSet(string $roomId, ?int $count = null): array
    {
        $room = Room::findOrFail($roomId);
        $capacity = $count ?? ($room->capacity ?: 8);

        $baju = $this->generateLockersForRoom($roomId, $capacity, 'B-', 'baju');
        $kitab = $this->generateLockersForRoom($roomId, $capacity, 'K-', 'kitab');

        return array_merge($baju, $kitab);
    }

    /**
     * Pasang / lepas santri pemilik lemari atau rak kitab.
     */
    public function assignLocker(string $lockerId, ?string $personId): Locker
    {
        $locker = Locker::findOrFail($lockerId);

        if ($personId) {
            // Lepaskan kepemilikan lemari setipe di kamar yang sama jika sudah punya
            Locker::where('room_id', $locker->room_id)
                ->where('type', $locker->type)
                ->where('assigned_person_id', $personId)
                ->where('id', '!=', $lockerId)
                ->update(['assigned_person_id' => null]);
        }

        $locker->update([
            'assigned_person_id' => $personId,
        ]);

        return $locker->fresh(['assignedPerson', 'room']);
    }

    /**
     * Update kondisi fisik lemari.
     */
    public function updateCondition(string $lockerId, string $condition, ?string $notes = null): Locker
    {
        $locker = Locker::findOrFail($lockerId);

        if (!array_key_exists($condition, Locker::CONDITIONS)) {
            throw new DomainException("Kondisi lemari '{$condition}' tidak valid.");
        }

        $locker->update([
            'condition' => $condition,
            'notes'     => $notes ?? $locker->notes,
        ]);

        return $locker->fresh();
    }

    /**
     * Ambil seluruh lemari dalam satu kamar beserta santri pemegangnya.
     */
    public function getRoomLockers(string $roomId): Collection
    {
        return Locker::with(['assignedPerson.santriProfile'])
            ->where('room_id', $roomId)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();
    }

    /**
     * Ringkasan lemari per asrama / komplek.
     */
    public function getDormitoryLockersSummary(string $dormitoryId): array
    {
        $dormitory = Dormitory::with(['rooms' => function ($q) {
            $q->where('is_active', true)->with(['lockers.assignedPerson']);
        }])->findOrFail($dormitoryId);

        $totalLockers = 0;
        $occupied     = 0;
        $byCondition  = [
            'baik'         => 0,
            'rusak_ringan' => 0,
            'rusak_berat'  => 0,
            'hilang'       => 0,
        ];

        $roomsData = [];

        foreach ($dormitory->rooms as $room) {
            $roomTotal = $room->lockers->count();
            $roomOccupied = $room->lockers->whereNotNull('assigned_person_id')->count();

            $totalLockers += $roomTotal;
            $occupied     += $roomOccupied;

            foreach ($room->lockers as $lck) {
                if (isset($byCondition[$lck->condition])) {
                    $byCondition[$lck->condition]++;
                }
            }

            $roomsData[] = [
                'id'       => $room->id,
                'name'     => $room->name,
                'capacity' => $room->capacity,
                'lockers'  => $room->lockers,
                'total'    => $roomTotal,
                'occupied' => $roomOccupied,
                'empty'    => max(0, $roomTotal - $roomOccupied),
            ];
        }

        return [
            'dormitory'    => $dormitory,
            'total'        => $totalLockers,
            'occupied'     => $occupied,
            'empty'        => max(0, $totalLockers - $occupied),
            'by_condition' => $byCondition,
            'rooms'        => $roomsData,
        ];
    }
}
