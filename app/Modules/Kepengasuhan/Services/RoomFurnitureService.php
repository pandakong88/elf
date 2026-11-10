<?php

namespace App\Modules\Kepengasuhan\Services;

use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\Locker;
use Illuminate\Support\Str;
use DomainException;

class RoomFurnitureService
{
    /**
     * Ambil layout perabot kamar dan lengkapi (hydrate) dengan data live dari tabel lockers.
     */
    public function getRoomFurnitureHydrated(Room $room): array
    {
        $layout = $room->furniture_layout;

        // Jika layout belum pernah diset (null), buatkan default layout otomatis dari data lockers kamar saat ini
        if (is_null($layout)) {
            $layout = $this->generateInitialDefaultLayout($room);
            $room->update(['furniture_layout' => $layout]);
        } elseif (!empty($layout)) {
            // Sanitasi: jika ada unit yang tercampur B- dan K-, pisahkan menjadi unit terpisah
            $layout = $this->sanitizeAndSplitLayout($layout, $room);
        }

        // Ambil semua lockers aktif di kamar ini sekaligus (eager loading assignedPerson & santriProfile)
        $lockers = Locker::where('room_id', $room->id)
            ->with(['assignedPerson.santriProfile'])
            ->get()
            ->keyBy('code');

        $hydratedLayout = [];

        foreach ($layout as $unit) {
            $category = $unit['category'] ?? 'compartment';

            if ($category === 'compartment') {
                $unitType = $unit['type'] ?? 'baju';
                $hydratedRows = [];

                foreach ($unit['rows'] ?? [] as $rowIdx => $row) {
                    $hydratedSlots = [];

                    foreach ($row['slots'] ?? [] as $slotIdx => $slot) {
                        $code = $slot['code'] ?? ('B-' . str_pad((string)($slotIdx + 1), 2, '0', STR_PAD_LEFT));

                        /** @var Locker|null $locker */
                        $locker = $lockers->get($code);

                        // Jika loker belum ada di DB, buatkan secara otomatis
                        if (!$locker) {
                            $locker = Locker::create([
                                'id'                 => (string) Str::uuid(),
                                'room_id'            => $room->id,
                                'type'               => $unitType,
                                'code'               => $code,
                                'assigned_person_id' => null,
                                'condition'          => 'baik',
                                'is_active'          => true,
                            ]);
                            $lockers->put($code, $locker);
                        }

                        $hydratedSlots[] = [
                            'code'                 => $code,
                            'col_span'             => $slot['col_span'] ?? 1,
                            'label'                => $slot['label'] ?? $code,
                            'locker_id'            => $locker->id,
                            'assigned_person_id'   => $locker->assigned_person_id,
                            'assigned_person_name' => $locker->assignedPerson?->name,
                            'assigned_person_nis'  => $locker->assignedPerson?->santriProfile?->nis ?? '-',
                            'allocation_type'      => $locker->allocation_type ?: 'santri',
                            'custom_holder_name'   => $locker->custom_holder_name ?? '',
                            'condition'            => $locker->condition ?: 'baik',
                            'condition_label'      => $locker->condition_label,
                            'condition_color'      => $locker->condition_color,
                            'condition_badge_class'=> $locker->condition_badge_class,
                            'notes'                => $locker->notes ?? '',
                        ];
                    }

                    $hydratedRows[] = [
                        'row'   => $rowIdx + 1,
                        'slots' => $hydratedSlots,
                    ];
                }

                $unit['rows'] = $hydratedRows;
            }

            $hydratedLayout[] = $unit;
        }

        return $hydratedLayout;
    }

    /**
     * Simpan struktur tata letak perabot kamar dan sinkronkan loker-lokernya ke database.
     */
    public function saveRoomFurnitureLayout(Room $room, array $furnitureLayout): void
    {
        $existingCodes = [];

        foreach ($furnitureLayout as $unit) {
            if (($unit['category'] ?? 'compartment') === 'compartment') {
                $unitType = $unit['type'] ?? 'baju';

                foreach ($unit['rows'] ?? [] as $row) {
                    foreach ($row['slots'] ?? [] as $slot) {
                        if (!empty($slot['code'])) {
                            $code = trim($slot['code']);
                            $existingCodes[] = $code;

                            Locker::firstOrCreate(
                                ['room_id' => $room->id, 'code' => $code],
                                [
                                    'id'                 => (string) Str::uuid(),
                                    'type'               => $unitType,
                                    'condition'          => 'baik',
                                    'assigned_person_id' => null,
                                    'is_active'          => true,
                                ]
                            );
                        }
                    }
                }
            }
        }

        $room->furniture_layout = $furnitureLayout;
        $room->save();
    }

    /**
     * Salin (Clone) layout perabot dari kamar sumber ke kamar-kamar tujuan.
     */
    /**
     * Salin (Clone) layout perabot dari kamar sumber ke kamar-kamar tujuan.
     * @param array $selectedUnitIds Unit perabot mana saja yang mau disalin (jika kosong = semua)
     * @param string $mode 'overwrite' (timpa seluruh perabot kamar tujuan) atau 'append' (tambahkan ke perabot yang sudah ada)
     */
    public function cloneLayout(Room $sourceRoom, array $targetRoomIds, array $selectedUnitIds = [], string $mode = 'overwrite'): int
    {
        $layout = $sourceRoom->furniture_layout;
        if (empty($layout)) {
            $layout = $this->getRoomFurnitureHydrated($sourceRoom);
        }

        // Filter unit yang dipilih jika ada
        if (!empty($selectedUnitIds)) {
            $layoutToClone = array_values(array_filter($layout, function($u) use ($selectedUnitIds) {
                return in_array($u['id'], $selectedUnitIds);
            }));
        } else {
            $layoutToClone = $layout;
        }

        if (empty($layoutToClone)) {
            return 0;
        }

        $clonedCount = 0;

        foreach ($targetRoomIds as $tId) {
            if ($tId === $sourceRoom->id) {
                continue;
            }

            $targetRoom = Room::find($tId);
            if ($targetRoom) {
                $cleanUnits = $this->stripAssignmentsFromLayout($layoutToClone);

                if ($mode === 'append') {
                    $existingTargetLayout = $targetRoom->furniture_layout ?? [];
                    // Gabungkan dengan perabot yang sudah ada di target
                    $finalLayout = array_merge($existingTargetLayout, $cleanUnits);
                } else {
                    // Mode overwrite: Timpa seluruh layout perabot kamar target
                    $finalLayout = $cleanUnits;
                }

                $this->saveRoomFurnitureLayout($targetRoom, $finalLayout);
                $clonedCount++;
            }
        }

        return $clonedCount;
    }

    /**
     * Perbarui kondisi fisik dan catatan kerusakan loker/slot lemari.
     */
    public function updateSlotCondition(string $roomId, string $code, string $condition, ?string $notes = null): Locker
    {
        if (!array_key_exists($condition, Locker::CONDITIONS)) {
            throw new DomainException("Kondisi lemari '{$condition}' tidak valid.");
        }

        $locker = Locker::where('room_id', $roomId)->where('code', $code)->first();

        if (!$locker) {
            $locker = Locker::create([
                'id'                 => (string) Str::uuid(),
                'room_id'            => $roomId,
                'code'               => $code,
                'condition'          => $condition,
                'notes'              => $notes,
                'assigned_person_id' => null,
                'is_active'          => true,
            ]);
        } else {
            $locker->update([
                'condition' => $condition,
                'notes'     => $notes !== null ? $notes : $locker->notes,
            ]);
        }

        return $locker->fresh(['assignedPerson', 'room']);
    }

    /**
     * Pasang atau lepas santri pemegang slot loker tertentu, atau alokasikan untuk keperluan khusus / pengurus.
     */
    public function assignSlotPerson(
        string $roomId,
        string $code,
        ?string $personId,
        string $allocationType = 'santri',
        ?string $customHolderName = null
    ): Locker {
        $locker = Locker::where('room_id', $roomId)->where('code', $code)->first();

        $data = [
            'assigned_person_id' => ($allocationType === 'santri') ? $personId : null,
            'allocation_type'    => $allocationType,
            'custom_holder_name' => ($allocationType !== 'santri') ? $customHolderName : null,
        ];

        if (!$locker) {
            $locker = Locker::create(array_merge([
                'id'        => (string) Str::uuid(),
                'room_id'   => $roomId,
                'code'      => $code,
                'condition' => 'baik',
                'is_active' => true,
            ], $data));
        } else {
            // Jika santri sudah punya loker bertipe sama di kamar ini, lepaskan loker lamanya
            if ($allocationType === 'santri' && $personId) {
                Locker::where('room_id', $roomId)
                    ->where('type', $locker->type)
                    ->where('assigned_person_id', $personId)
                    ->where('id', '!=', $locker->id)
                    ->update(['assigned_person_id' => null]);
            }

            $locker->update($data);
        }

        return $locker->fresh(['assignedPerson.santriProfile', 'room']);
    }

    /**
     * Perbarui kondisi fisik untuk fasilitas ruangan umum (non-loker: kipas, lampu, kasur, dsb).
     */
    public function updateSingleItemCondition(Room $room, string $itemId, string $condition, ?string $notes = null): void
    {
        $layout = $room->furniture_layout ?? [];

        foreach ($layout as &$unit) {
            if (($unit['id'] ?? '') === $itemId && ($unit['category'] ?? '') === 'single') {
                $unit['condition'] = $condition;
                if ($notes !== null) {
                    $unit['notes'] = $notes;
                }
            }
        }

        $room->furniture_layout = $layout;
        $room->save();
    }

    /**
     * Tambah unit perabot baru ke kamar.
     */
    public function addFurnitureUnit(Room $room, array $unitData): void
    {
        $layout = $room->furniture_layout ?? [];
        if (empty($unitData['id'])) {
            $unitData['id'] = (string) Str::uuid();
        }

        $layout[] = $unitData;
        $this->saveRoomFurnitureLayout($room, $layout);
    }

    /**
     * Hapus unit perabot dari kamar dan bersihkan penugasan loker di DB.
     */
    public function removeFurnitureUnit(Room $room, string $unitId): void
    {
        $layout = $room->furniture_layout ?? [];
        $newLayout = [];
        $codesToDelete = [];

        foreach ($layout as $unit) {
            if (($unit['id'] ?? '') === $unitId) {
                if (($unit['category'] ?? '') === 'compartment') {
                    foreach ($unit['rows'] ?? [] as $row) {
                        foreach ($row['slots'] ?? [] as $slot) {
                            if (!empty($slot['code'])) {
                                $codesToDelete[] = $slot['code'];
                            }
                        }
                    }
                }
            } else {
                $newLayout[] = $unit;
            }
        }

        // Hapus loker yang bersangkutan dari database agar santri terlepas dan status kembali Belum Lemari
        if (!empty($codesToDelete)) {
            Locker::where('room_id', $room->id)
                ->whereIn('code', $codesToDelete)
                ->delete();
        }

        $this->saveRoomFurnitureLayout($room, $newLayout);
    }

    /**
     * Bangun default layout jika kamar belum memiliki konfigurasi perabot.
     * Memastikan Lemari Pakaian (baju) dan Rak Kitab (kitab) terpisah menjadi unit tersendiri!
     */
    protected function generateInitialDefaultLayout(Room $room): array
    {
        $capacity = $room->capacity ?: 8;

        // Ambil loker yang sudah ada di DB jika ada
        $existingLockers = Locker::where('room_id', $room->id)->orderBy('code')->get();

        $units = [];

        // Pisahkan loker baju dan loker kitab berdasarkan type atau prefix
        $bajuLockers = $existingLockers->filter(function($l) {
            $c = strtoupper(trim($l->code));
            return $l->type === 'baju' || str_starts_with($c, 'B-') || str_starts_with($c, 'L-') || !str_starts_with($c, 'K-');
        });

        $kitabLockers = $existingLockers->filter(function($l) {
            $c = strtoupper(trim($l->code));
            return $l->type === 'kitab' || str_starts_with($c, 'K-') || str_starts_with($c, 'R-');
        });

        // 1. Lemari Pakaian Unit
        $bajuRows = [];
        if ($bajuLockers->count() > 0) {
            foreach ($bajuLockers->chunk(4) as $chunk) {
                $slots = [];
                foreach ($chunk as $lck) {
                    $slots[] = [
                        'code'     => $lck->code,
                        'col_span' => 1,
                        'label'    => $lck->code,
                    ];
                }
                $bajuRows[] = ['slots' => $slots];
            }
        } else {
            $perRow = 4;
            $totalSlots = max($capacity, 4);
            $currentCodeNum = 1;
            while ($currentCodeNum <= $totalSlots) {
                $slots = [];
                for ($c = 0; $c < $perRow && $currentCodeNum <= $totalSlots; $c++) {
                    $code = 'B-' . str_pad((string)$currentCodeNum, 2, '0', STR_PAD_LEFT);
                    $slots[] = [
                        'code'     => $code,
                        'col_span' => 1,
                        'label'    => $code,
                    ];
                    $currentCodeNum++;
                }
                $bajuRows[] = ['slots' => $slots];
            }
        }

        $units[] = [
            'id'       => (string) Str::uuid(),
            'name'     => 'Lemari Pakaian Santri',
            'type'     => 'baju',
            'category' => 'compartment',
            'rows'     => $bajuRows,
        ];

        // 2. Rak Kitab Unit (Hanya jika ada loker kitab di DB)
        if ($kitabLockers->count() > 0) {
            $kitabRows = [];
            foreach ($kitabLockers->chunk(4) as $chunk) {
                $slots = [];
                foreach ($chunk as $lck) {
                    $slots[] = [
                        'code'     => $lck->code,
                        'col_span' => 1,
                        'label'    => $lck->code,
                    ];
                }
                $kitabRows[] = ['slots' => $slots];
            }

            $units[] = [
                'id'       => (string) Str::uuid(),
                'name'     => 'Rak Kitab & Al-Qur\'an',
                'type'     => 'kitab',
                'category' => 'compartment',
                'rows'     => $kitabRows,
            ];
        }

        return $units;
    }

    /**
     * Sanitasi: Jika suatu unit perabot tercampur antara kode B- (baju) dan K- (kitab),
     * pisahkan secara otomatis menjadi unit Lemari Pakaian dan Rak Kitab tersendiri.
     */
    public function sanitizeAndSplitLayout(array $layout, Room $room): array
    {
        $hasMixed = false;
        $cleanUnits = [];

        foreach ($layout as $unit) {
            if (empty($unit['rows']) || !is_array($unit['rows'])) {
                $cleanUnits[] = $unit;
                continue;
            }

            $slots = [];
            foreach ($unit['rows'] ?? [] as $r) {
                foreach ($r['slots'] ?? [] as $s) {
                    $slots[] = $s;
                }
            }

            $bajuSlots = [];
            $kitabSlots = [];

            foreach ($slots as $slot) {
                $code = strtoupper(trim($slot['code'] ?? ''));
                if (str_starts_with($code, 'K-') || str_starts_with($code, 'R-')) {
                    $kitabSlots[] = $slot;
                } else {
                    $bajuSlots[] = $slot;
                }
            }

            // Jika di dalam satu unit ini ada percampuran B- dan K-
            if (!empty($bajuSlots) && !empty($kitabSlots)) {
                $hasMixed = true;

                // Unit 1: Lemari Baju (4 slots per row)
                $bajuRows = [];
                foreach (array_chunk($bajuSlots, 4) as $chunk) {
                    $bajuRows[] = ['slots' => $chunk];
                }
                $cleanUnits[] = [
                    'id'       => $unit['id'] ?: (string) Str::uuid(),
                    'name'     => 'Lemari Pakaian Santri',
                    'type'     => 'baju',
                    'category' => 'compartment',
                    'rows'     => $bajuRows,
                ];

                // Unit 2: Rak Kitab (4 slots per row)
                $kitabRows = [];
                foreach (array_chunk($kitabSlots, 4) as $chunk) {
                    $kitabRows[] = ['slots' => $chunk];
                }
                $cleanUnits[] = [
                    'id'       => (string) Str::uuid(),
                    'name'     => 'Rak Kitab & Al-Qur\'an',
                    'type'     => 'kitab',
                    'category' => 'compartment',
                    'rows'     => $kitabRows,
                ];
            } else {
                $cleanUnits[] = $unit;
            }
        }

        if ($hasMixed) {
            $room->furniture_layout = $cleanUnits;
            $room->save();
        }

        return $cleanUnits;
    }

    /**
     * Hapus data pengenal santri saat mengkloning layout ke kamar lain.
     */
    protected function stripAssignmentsFromLayout(array $layout): array
    {
        $clean = [];

        foreach ($layout as $unit) {
            $u = $unit;
            if (!empty($u['rows']) && is_array($u['rows'])) {
                foreach ($u['rows'] as $rIdx => $r) {
                    foreach ($r['slots'] ?? [] as $sIdx => $s) {
                        unset($u['rows'][$rIdx]['slots'][$sIdx]['assigned_person_id']);
                        unset($u['rows'][$rIdx]['slots'][$sIdx]['assigned_person_name']);
                        unset($u['rows'][$rIdx]['slots'][$sIdx]['assigned_person_nis']);
                        unset($u['rows'][$rIdx]['slots'][$sIdx]['condition']);
                        unset($u['rows'][$rIdx]['slots'][$sIdx]['notes']);
                    }
                }
            } else {
                $u['condition'] = 'baik';
                $u['notes']     = '';
            }
            $u['id'] = (string) Str::uuid();
            $clean[] = $u;
        }

        return $clean;
    }
}
