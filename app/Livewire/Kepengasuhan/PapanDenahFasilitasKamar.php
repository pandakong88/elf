<?php

namespace App\Livewire\Kepengasuhan;

use Livewire\Component;
use App\Livewire\Concerns\SendsToast;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\Locker;
use App\Modules\Kepengasuhan\Services\RoomFurnitureService;
use App\Modules\Kepengasuhan\Services\CensusV3AccessService;
use App\Modules\Core\Models\Person;
use Illuminate\Support\Str;

class PapanDenahFasilitasKamar extends Component
{
    use SendsToast;

    // Filter gender scope: 'L', 'P', or null
    public ?string $genderScope = null;
    public bool    $isGenderLocked = false;

    // Selected Room navigation
    public ?string $selectedDormitoryId = null;
    public ?string $selectedRoomId      = null;
    public string  $search              = '';
    public bool    $isRoomSelectorOpen  = false; // Accordion toggle for room list

    // Filter Ketersediaan Kamar
    public string $roomFilter           = 'all'; // 'all', 'has_vacancy', 'full'

    // Filter & Pencarian Penghuni Kamar
    public string $occupantFilter       = 'all'; // 'all', 'no_baju', 'no_kitab', 'complete', 'missing_any'
    public string $occupantSearch       = '';

    // Tab Tampilan Perabot Kamar (Lemari vs Rak Kitab vs Fasilitas)
    public string $activeUnitTab        = 'all'; // 'all', 'baju', 'kitab', 'single'

    // Highlight / Indikator Lemari & Rak Kosong Menyala
    public array $highlightVacantUnitIds = [];
    public bool  $highlightAllVacant     = false;

    // Data Perabot & Denah Hidup
    public array $furnitureUnits = [];

    // Modal Quick Inspection / Pencatatan Kondisi
    public bool    $showConditionModal        = false;
    public string  $conditionModalTargetType  = 'slot'; // 'slot' or 'single'
    public ?string $targetUnitId              = null;
    public ?string $targetSlotCode            = null;
    public string  $selectedCondition         = 'baik';
    public string  $conditionNotes            = '';

    // Modal Pasang Santri Pemegang Loker
    public bool    $showAssignModal           = false;
    public ?string $assignSlotCode            = null;
    public ?string $assignSlotType            = 'baju';
    public ?string $assignSelectedPersonId    = null;
    public string  $assignAllocationType      = 'santri'; // 'santri', 'inventaris', 'external'
    public string  $assignCustomHolderName    = '';

    // Modal Visual Block Builder (Desain Bentuk Lemari)
    public bool    $showBuilderModal          = false;
    public ?string $builderEditingUnitId      = null;
    public string  $builderName               = 'Lemari Pakaian Utama';
    public string  $builderType               = 'baju'; // 'baju', 'kitab', 'umum'
    public string  $builderCategory           = 'compartment'; // 'compartment' or 'single'
    public string  $builderPrefix             = 'B-';
    public int     $builderStartingNumber     = 1;
    public array   $builderRows               = []; // [ ['slots_count' => 2, 'col_span' => 2], ['slots_count' => 4, 'col_span' => 1] ]
    public string  $builderSingleCondition    = 'baik';
    public string  $builderSingleNotes        = '';
    public array   $builderTargetRoomIds      = [];
    public bool    $isBuilderRoomAccordionOpen = false;

    // Modal Salin Layout (Clone)
    public bool    $showCloneModal            = false;
    public array   $cloneSelectedUnitIds      = [];
    public string  $cloneMode                 = 'overwrite'; // 'overwrite' or 'append'
    public array   $cloneTargetRoomIds        = [];

    // Modal Konfirmasi Hapus Unit Perabot (Estetik & Impact List)
    public bool    $showDeleteUnitModal       = false;
    public ?string $deleteUnitId              = null;
    public array   $deleteUnitData            = [];

    // Modal Konfirmasi Lepas Santri dari Loker (Estetik Per-Pintu)
    public bool    $showUnassignModal         = false;
    public ?string $unassignSlotCode          = null;
    public array   $unassignSlotData          = [];

    protected RoomFurnitureService  $furnitureService;
    protected CensusV3AccessService $accessService;

    public function boot(
        RoomFurnitureService $furnitureService,
        CensusV3AccessService $accessService
    ): void {
        $this->furnitureService = $furnitureService;
        $this->accessService    = $accessService;
    }

    public function mount(): void
    {
        $user = auth()->user();
        if (!$user || !($user->can('manage-sensus-v3') || $user->can('manage-asrama') || $user->can('input-census-v3'))) {
            abort(403, 'Akses ditolak. Anda tidak berwenang mengakses Papan Denah Fasilitas & Lemari.');
        }

        $detectedScope = $this->accessService->genderScope($user);
        if ($detectedScope) {
            $this->genderScope    = $detectedScope;
            $this->isGenderLocked = true;
        } else {
            $this->genderScope    = 'L';
            $this->isGenderLocked = false;
        }

        $this->selectFirstAvailableRoom();
    }

    public function switchGender(string $gender): void
    {
        if ($this->isGenderLocked) {
            return;
        }

        if (in_array($gender, ['L', 'P'])) {
            $this->genderScope = $gender;
            $this->selectedDormitoryId = null;
            $this->selectedRoomId = null;
            $this->selectFirstAvailableRoom();
        }
    }

    public function selectRoom(string $roomId): void
    {
        $room = Room::with('dormitory')->find($roomId);
        if ($room) {
            $this->selectedRoomId         = $room->id;
            $this->selectedDormitoryId    = $room->dormitory_id;
            $this->occupantSearch         = '';
            $this->highlightVacantUnitIds = [];
            $this->highlightAllVacant     = false;
            $this->loadRoomFurniture();
        }
    }

    /**
     * Dapatkan daftar seluruh kamar aktif dalam lingkup gender saat ini (terurut per asrama & nama).
     */
    public function getAvailableRoomsList(): array
    {
        $dormitories = Dormitory::where('is_active', true)
            ->when($this->genderScope, fn($q) => $q->where('gender', $this->genderScope))
            ->with(['rooms' => fn($rq) => $rq->where('is_active', true)->withCount('currentAssignments')->orderBy('name')])
            ->orderBy('name')
            ->get();

        $flat = [];
        foreach ($dormitories as $d) {
            foreach ($d->rooms as $r) {
                $flat[] = [
                    'id'             => $r->id,
                    'name'           => $r->name,
                    'dormitory_id'   => $d->id,
                    'dormitory_name' => $d->name,
                    'capacity'       => $r->capacity,
                    'assigned_count' => $r->current_assignments_count ?? 0,
                ];
            }
        }
        return $flat;
    }

    /**
     * Pindah ke kamar berikutnya secara melingkar (looping).
     */
    public function goToNextRoom(): void
    {
        $rooms = $this->getAvailableRoomsList();
        if (empty($rooms)) return;

        $ids = array_column($rooms, 'id');
        $currentIndex = array_search($this->selectedRoomId, $ids);

        if ($currentIndex !== false && isset($ids[$currentIndex + 1])) {
            $nextId = $ids[$currentIndex + 1];
        } else {
            $nextId = $ids[0]; // Balik ke kamar pertama
        }

        $this->selectRoom($nextId);
    }

    /**
     * Pindah ke kamar sebelumnya secara melingkar (looping).
     */
    public function goToPreviousRoom(): void
    {
        $rooms = $this->getAvailableRoomsList();
        if (empty($rooms)) return;

        $ids = array_column($rooms, 'id');
        $currentIndex = array_search($this->selectedRoomId, $ids);

        if ($currentIndex !== false && isset($ids[$currentIndex - 1])) {
            $prevId = $ids[$currentIndex - 1];
        } else {
            $prevId = end($ids); // Balik ke kamar terakhir
        }

        $this->selectRoom($prevId);
    }

    public function toggleHighlightVacant(string $unitId): void
    {
        if (in_array($unitId, $this->highlightVacantUnitIds)) {
            $this->highlightVacantUnitIds = array_values(array_diff($this->highlightVacantUnitIds, [$unitId]));
        } else {
            $this->highlightVacantUnitIds[] = $unitId;
        }
    }

    public function toggleHighlightAllVacant(): void
    {
        $this->highlightAllVacant = !$this->highlightAllVacant;
    }

    public function toggleRoomSelector(): void
    {
        $this->isRoomSelectorOpen = !$this->isRoomSelectorOpen;
    }

    public function setOccupantFilter(string $filter): void
    {
        $this->occupantFilter = $filter;
    }

    public function setRoomFilter(string $filter): void
    {
        $this->roomFilter = $filter;
    }

    public function setActiveUnitTab(string $tab): void
    {
        $this->activeUnitTab = $tab;
    }

    protected function selectFirstAvailableRoom(): void
    {
        $firstRoom = Room::where('is_active', true)
            ->whereHas('dormitory', fn($q) => $q->where('is_active', true)->where('gender', $this->genderScope))
            ->first();

        if ($firstRoom) {
            $this->selectedRoomId      = $firstRoom->id;
            $this->selectedDormitoryId = $firstRoom->dormitory_id;
            $this->loadRoomFurniture();
        } else {
            $this->selectedRoomId      = null;
            $this->selectedDormitoryId = null;
            $this->furnitureUnits      = [];
        }
    }

    public function loadRoomFurniture(): void
    {
        if (!$this->selectedRoomId) {
            $this->furnitureUnits = [];
            return;
        }

        $room = Room::find($this->selectedRoomId);
        if ($room) {
            $this->furnitureUnits = $this->furnitureService->getRoomFurnitureHydrated($room);
        }
    }

    // =========================================================================
    // MODAL QUICK INSPECTION / PENCATATAN KONDISI FISIK
    // =========================================================================

    public function openConditionModal(
        string $targetType,
        ?string $unitId = null,
        ?string $slotCode = null,
        string $currentCondition = 'baik',
        string $notes = ''
    ): void {
        $this->conditionModalTargetType = $targetType;
        $this->targetUnitId             = $unitId;
        $this->targetSlotCode           = $slotCode;
        $this->selectedCondition        = $currentCondition;
        $this->conditionNotes           = $notes;
        $this->showConditionModal       = true;
    }

    public function setQuickCondition(string $cond): void
    {
        $this->selectedCondition = $cond;
    }

    public function saveConditionModal(): void
    {
        if (!$this->selectedRoomId) return;

        if ($this->conditionModalTargetType === 'slot' && $this->targetSlotCode) {
            $this->furnitureService->updateSlotCondition(
                $this->selectedRoomId,
                $this->targetSlotCode,
                $this->selectedCondition,
                $this->conditionNotes
            );
            $this->toastSuccess("Kondisi loker {$this->targetSlotCode} diperbarui ke " . ucfirst($this->selectedCondition));
        } elseif ($this->conditionModalTargetType === 'single' && $this->targetUnitId) {
            $room = Room::find($this->selectedRoomId);
            if ($room) {
                $this->furnitureService->updateSingleItemCondition(
                    $room,
                    $this->targetUnitId,
                    $this->selectedCondition,
                    $this->conditionNotes
                );
                $this->toastSuccess("Kondisi fasilitas diperbarui ke " . ucfirst($this->selectedCondition));
            }
        }

        $this->showConditionModal = false;
        $this->loadRoomFurniture();
    }

    // =========================================================================
    // MODAL PASANG / LEPAS SANTRI PEMEGANG LOKER
    // =========================================================================

    public function openAssignModal(
        string $slotCode,
        string $slotType = 'baju',
        ?string $currentPersonId = null,
        string $allocationType = 'santri',
        ?string $customHolderName = null
    ): void {
        $this->assignSlotCode         = $slotCode;
        $this->assignSlotType         = $slotType;
        $this->assignSelectedPersonId = $currentPersonId;
        $this->assignAllocationType   = $allocationType ?: 'santri';
        $this->assignCustomHolderName = $customHolderName ?? '';
        $this->showAssignModal        = true;
    }

    public function setAssignInventarisPreset(string $preset): void
    {
        $this->assignCustomHolderName = $preset;
    }

    public function saveAssignModal(): void
    {
        if (!$this->selectedRoomId || !$this->assignSlotCode) return;

        if ($this->assignAllocationType === 'inventaris') {
            $name = trim($this->assignCustomHolderName) ?: 'Kas / Uang Bendahara';
            $this->furnitureService->assignSlotPerson(
                $this->selectedRoomId,
                $this->assignSlotCode,
                null,
                'inventaris',
                $name
            );
            $this->toastSuccess("Loker {$this->assignSlotCode} berhasil dialokasikan untuk {$name}.");
        } elseif ($this->assignAllocationType === 'external') {
            $name = trim($this->assignCustomHolderName) ?: 'Pengurus / Tamu';
            $this->furnitureService->assignSlotPerson(
                $this->selectedRoomId,
                $this->assignSlotCode,
                null,
                'external',
                $name
            );
            $this->toastSuccess("Loker {$this->assignSlotCode} berhasil dialokasikan untuk {$name}.");
        } else {
            $personId = $this->assignSelectedPersonId ?: null;
            $this->furnitureService->assignSlotPerson(
                $this->selectedRoomId,
                $this->assignSlotCode,
                $personId,
                'santri',
                null
            );
            if ($personId) {
                $this->toastSuccess("Penugasan loker {$this->assignSlotCode} berhasil disimpan.");
            } else {
                $this->toastSuccess("Loker {$this->assignSlotCode} berhasil dikosongkan.");
            }
        }

        $this->showAssignModal = false;
        $this->loadRoomFurniture();
    }

    public function confirmUnassignSlot(string $slotCode): void
    {
        $targetSlot = null;
        $unitName = 'Lemari';
        $unitType = 'baju';

        foreach ($this->furnitureUnits as $unit) {
            if (($unit['category'] ?? '') === 'compartment') {
                foreach ($unit['rows'] ?? [] as $row) {
                    foreach ($row['slots'] ?? [] as $slot) {
                        if (($slot['code'] ?? '') === $slotCode) {
                            $targetSlot = $slot;
                            $unitName = $unit['name'] ?? 'Lemari';
                            $unitType = $unit['type'] ?? 'baju';
                            break 2;
                        }
                    }
                }
            }
        }

        if (!$targetSlot) return;

        $alloc = $targetSlot['allocation_type'] ?? 'santri';
        $isSantri = !empty($targetSlot['assigned_person_name']);
        $displayName = $isSantri ? $targetSlot['assigned_person_name'] : ($targetSlot['custom_holder_name'] ?: ($alloc === 'inventaris' ? 'Kas / Inventaris' : 'Pengurus / Tamu'));

        $this->unassignSlotCode = $slotCode;
        $this->unassignSlotData = [
            'code'          => $slotCode,
            'unit_name'     => $unitName,
            'unit_type'     => $unitType,
            'occupant_name' => $displayName,
            'nis'           => $targetSlot['assigned_person_nis'] ?? null,
            'alloc'         => $alloc,
        ];
        $this->showUnassignModal = true;
    }

    public function executeUnassignSlot(): void
    {
        if (!$this->selectedRoomId || !$this->unassignSlotCode) {
            $this->showUnassignModal = false;
            return;
        }

        $code = $this->unassignSlotCode;
        $occupantName = $this->unassignSlotData['occupant_name'] ?? 'Penghuni';

        $this->furnitureService->assignSlotPerson($this->selectedRoomId, $code, null);
        $this->showUnassignModal = false;
        $this->unassignSlotCode = null;
        $this->unassignSlotData = [];
        $this->loadRoomFurniture();
        $this->toastSuccess("Slot {$code} ({$occupantName}) berhasil dilepaskan dan kini kosong.");
    }

    public function cancelUnassignSlot(): void
    {
        $this->showUnassignModal = false;
        $this->unassignSlotCode = null;
        $this->unassignSlotData = [];
    }

    public function unassignSlot(string $slotCode): void
    {
        if (!$this->selectedRoomId) return;

        $this->furnitureService->assignSlotPerson($this->selectedRoomId, $slotCode, null);
        $this->loadRoomFurniture();
        $this->toastSuccess("Loker {$slotCode} telah dikosongkan.");
    }

    public function quickAssignFromDrag(string $slotCode, string $personId): void
    {
        if (!$this->selectedRoomId) return;

        $person = Person::find($personId);
        $this->furnitureService->assignSlotPerson($this->selectedRoomId, $slotCode, $personId);
        $this->loadRoomFurniture();
        $this->toastSuccess("Loker {$slotCode} berhasil ditempati oleh " . ($person?->name ?? 'santri') . ".");
    }

    // =========================================================================
    // MODAL VISUAL BLOCK BUILDER (DESAIN SENDIRI BENTUK LEMARI)
    // =========================================================================

    public function openBuilderModal(?string $unitId = null): void
    {
        $this->builderEditingUnitId = $unitId;
        $this->isBuilderRoomAccordionOpen = false;

        if ($unitId) {
            // Edit existing unit (Skenario A: Hanya kamar aktif)
            $this->builderTargetRoomIds = [];
            $unit = collect($this->furnitureUnits)->firstWhere('id', $unitId);
            if ($unit) {
                $this->builderName     = $unit['name'] ?? 'Lemari';
                $this->builderType     = $unit['type'] ?? 'baju';
                $this->builderCategory = $unit['category'] ?? 'compartment';

                if ($this->builderCategory === 'compartment') {
                    $this->builderRows = [];
                    foreach ($unit['rows'] ?? [] as $row) {
                        $count = count($row['slots'] ?? []);
                        $colSpan = $row['slots'][0]['col_span'] ?? 1;
                        $this->builderRows[] = [
                            'slots_count' => max($count, 1),
                            'col_span'    => $colSpan,
                        ];
                    }
                } else {
                    $this->builderSingleCondition = $unit['condition'] ?? 'baik';
                    $this->builderSingleNotes     = $unit['notes'] ?? '';
                }
            }
        } else {
            // New unit default (Targetkan ke kamar aktif secara bawaan)
            $this->builderTargetRoomIds  = $this->selectedRoomId ? [$this->selectedRoomId] : [];
            $this->builderName           = 'Lemari Pakaian';
            $this->builderType           = 'baju';
            $this->builderCategory       = 'compartment';
            $this->builderPrefix         = 'B-';
            $this->builderStartingNumber = $this->calculateNextStartingNumber('B-');
            $this->builderSingleCondition = 'baik';
            $this->builderSingleNotes     = '';

            // Default preset 2 atas + 4 bawah
            $this->applyBuilderPreset('2_atas_4_bawah');
        }

        $this->showBuilderModal = true;
    }

    public function applyBuilderPreset(string $preset): void
    {
        if ($preset === '2_atas_4_bawah') {
            $this->builderCategory = 'compartment';
            $this->builderType     = 'baju';
            $this->builderPrefix   = 'B-';
            $this->builderRows     = [
                ['slots_count' => 2, 'col_span' => 2],
                ['slots_count' => 4, 'col_span' => 1],
            ];
        } elseif ($preset === '3x3') {
            $this->builderCategory = 'compartment';
            $this->builderType     = 'baju';
            $this->builderPrefix   = 'B-';
            $this->builderRows     = [
                ['slots_count' => 3, 'col_span' => 1],
                ['slots_count' => 3, 'col_span' => 1],
                ['slots_count' => 3, 'col_span' => 1],
            ];
        } elseif ($preset === '2x4_standar') {
            $this->builderCategory = 'compartment';
            $this->builderType     = 'baju';
            $this->builderPrefix   = 'B-';
            $this->builderRows     = [
                ['slots_count' => 4, 'col_span' => 1],
                ['slots_count' => 4, 'col_span' => 1],
            ];
        } elseif ($preset === '5x5_kitab') {
            $this->builderCategory = 'compartment';
            $this->builderType     = 'kitab';
            $this->builderPrefix   = 'K-';
            $this->builderName     = 'Rak Kitab & Al-Qur\'an';
            $this->builderStartingNumber = $this->calculateNextStartingNumber('K-');
            $this->builderRows     = array_fill(0, 5, ['slots_count' => 5, 'col_span' => 1]);
        } elseif ($preset === '4x4_kitab') {
            $this->builderCategory = 'compartment';
            $this->builderType     = 'kitab';
            $this->builderPrefix   = 'K-';
            $this->builderName     = 'Rak Kitab & Al-Qur\'an';
            $this->builderStartingNumber = $this->calculateNextStartingNumber('K-');
            $this->builderRows     = array_fill(0, 4, ['slots_count' => 4, 'col_span' => 1]);
        } elseif ($preset === 'fasilitas_umum') {
            $this->builderCategory = 'single';
            $this->builderType     = 'umum';
            $this->builderName     = 'Kipas Angin Dinding';
            $this->builderRows     = [];
        }
    }

    public function addRowToBuilder(int $slotsCount = 4, int $colSpan = 1): void
    {
        $this->builderRows[] = [
            'slots_count' => max(1, min(12, $slotsCount)),
            'col_span'    => max(1, $colSpan),
        ];
    }

    public function removeRowFromBuilder(int $index): void
    {
        unset($this->builderRows[$index]);
        $this->builderRows = array_values($this->builderRows);
    }

    public function toggleBuilderTargetRoom(string $roomId): void
    {
        if (in_array($roomId, $this->builderTargetRoomIds)) {
            $this->builderTargetRoomIds = array_values(array_diff($this->builderTargetRoomIds, [$roomId]));
        } else {
            $this->builderTargetRoomIds[] = $roomId;
        }
    }

    public function selectAllRoomsInDormForBuilder(string $dormitoryId): void
    {
        $dorm = Dormitory::with('rooms')->find($dormitoryId);
        if (!$dorm) return;

        $roomIds = $dorm->rooms->where('is_active', true)->pluck('id')->toArray();
        if (empty($roomIds)) {
            $roomIds = $dorm->rooms->pluck('id')->toArray();
        }

        $allSelected = count(array_intersect($roomIds, $this->builderTargetRoomIds)) === count($roomIds);

        if ($allSelected) {
            $this->builderTargetRoomIds = array_values(array_diff($this->builderTargetRoomIds, $roomIds));
        } else {
            $this->builderTargetRoomIds = array_values(array_unique(array_merge($this->builderTargetRoomIds, $roomIds)));
        }
    }

    public function saveBuilderModal(): void
    {
        if (!$this->selectedRoomId) return;

        $room = Room::find($this->selectedRoomId);
        if (!$room) return;

        if ($this->builderCategory === 'compartment') {
            if (empty($this->builderRows)) {
                $this->toastError('Minimal harus ada 1 baris pintu lemari.');
                return;
            }

            $currentNumber = $this->builderStartingNumber;
            $generatedRows = [];

            foreach ($this->builderRows as $row) {
                $slots = [];
                $count = (int)$row['slots_count'];
                $colSpan = (int)($row['col_span'] ?? 1);

                for ($s = 0; $s < $count; $s++) {
                    $code = $this->builderPrefix . str_pad((string)$currentNumber, 2, '0', STR_PAD_LEFT);
                    $slots[] = [
                        'code'     => $code,
                        'col_span' => $colSpan,
                        'label'    => $code,
                    ];
                    $currentNumber++;
                }

                $generatedRows[] = [
                    'slots' => $slots,
                ];
            }

            $unitData = [
                'id'       => $this->builderEditingUnitId ?: (string) Str::uuid(),
                'name'     => $this->builderName ?: 'Lemari',
                'type'     => $this->builderType,
                'category' => 'compartment',
                'rows'     => $generatedRows,
            ];
        } else {
            // Single facility
            $unitData = [
                'id'        => $this->builderEditingUnitId ?: (string) Str::uuid(),
                'name'      => $this->builderName ?: 'Fasilitas Ruangan',
                'type'      => $this->builderType,
                'category'  => 'single',
                'condition' => $this->builderSingleCondition,
                'notes'     => $this->builderSingleNotes,
            ];
        }

        if ($this->builderEditingUnitId) {
            // Skenario A: Edit existing unit HANYA di kamar aktif
            $layout = $room->furniture_layout ?? [];
            foreach ($layout as &$u) {
                if (($u['id'] ?? '') === $this->builderEditingUnitId) {
                    $u = $unitData;
                }
            }
            $this->furnitureService->saveRoomFurnitureLayout($room, $layout);
            $this->toastSuccess("Perabot {$unitData['name']} berhasil diperbarui.");
        } else {
            // Skenario A: Tambah baru didistribusikan ke kamar-kamar yang dipilih
            $targetRoomIds = !empty($this->builderTargetRoomIds) 
                ? $this->builderTargetRoomIds 
                : [$room->id];

            $targetRooms = Room::whereIn('id', $targetRoomIds)->get();
            $appliedCount = 0;

            foreach ($targetRooms as $targetRoom) {
                $roomUnitData = $unitData;
                $roomUnitData['id'] = (string) Str::uuid(); // UUID independen per kamar
                $this->furnitureService->addFurnitureUnit($targetRoom, $roomUnitData);
                $appliedCount++;
            }

            if ($appliedCount > 1) {
                $this->toastSuccess("Perabot {$unitData['name']} berhasil dibuat dan diterapkan ke {$appliedCount} kamar!");
            } else {
                $this->toastSuccess("Perabot {$unitData['name']} berhasil ditambahkan ke kamar {$room->name}.");
            }
        }

        $this->showBuilderModal = false;
        $this->loadRoomFurniture();
    }

    public function confirmDeleteFurnitureUnit(string $unitId): void
    {
        $unit = collect($this->furnitureUnits)->firstWhere('id', $unitId);
        if (!$unit) return;

        $occupants = [];
        if (($unit['category'] ?? '') === 'compartment') {
            foreach ($unit['rows'] ?? [] as $row) {
                foreach ($row['slots'] ?? [] as $slot) {
                    $alloc = $slot['allocation_type'] ?? 'santri';
                    $isSantri = !empty($slot['assigned_person_name']);
                    $isSpecial = in_array($alloc, ['inventaris', 'external']);
                    if ($isSantri || $isSpecial) {
                        $occupants[] = [
                            'code'        => $slot['code'],
                            'name'        => $isSantri ? $slot['assigned_person_name'] : ($slot['custom_holder_name'] ?: ($alloc === 'inventaris' ? 'Kas / Inventaris' : 'Pengurus / Tamu')),
                            'nis'         => $slot['assigned_person_nis'] ?? null,
                            'alloc'       => $alloc,
                            'alloc_label' => $isSantri ? 'Santri' : ($alloc === 'inventaris' ? 'Kas / Inventaris' : 'Pengurus / Tamu'),
                        ];
                    }
                }
            }
        }

        $this->deleteUnitId = $unitId;
        $this->deleteUnitData = [
            'id'         => $unitId,
            'name'       => $unit['name'] ?? 'Unit Perabot',
            'category'   => $unit['category'] ?? 'compartment',
            'type'       => $unit['type'] ?? 'baju',
            'occupants'  => $occupants,
            'slot_count' => ($unit['category'] ?? '') === 'compartment' ? collect($unit['rows'] ?? [])->sum(fn($r) => count($r['slots'] ?? [])) : 1,
        ];
        $this->showDeleteUnitModal = true;
    }

    public function executeDeleteFurnitureUnit(): void
    {
        if (!$this->selectedRoomId || !$this->deleteUnitId) {
            $this->showDeleteUnitModal = false;
            return;
        }

        $room = Room::find($this->selectedRoomId);
        if ($room) {
            $unitName = $this->deleteUnitData['name'] ?? 'Unit perabot';
            $occupantCount = count($this->deleteUnitData['occupants'] ?? []);

            $this->furnitureService->removeFurnitureUnit($room, $this->deleteUnitId);
            $this->showDeleteUnitModal = false;
            $this->deleteUnitId = null;
            $this->deleteUnitData = [];
            $this->loadRoomFurniture();

            if ($occupantCount > 0) {
                $this->toastSuccess("{$unitName} berhasil dihapus. {$occupantCount} penghuni telah dilepaskan dari fasilitas.");
            } else {
                $this->toastSuccess("{$unitName} berhasil dihapus dari kamar.");
            }
        }
    }

    public function cancelDeleteFurnitureUnit(): void
    {
        $this->showDeleteUnitModal = false;
        $this->deleteUnitId = null;
        $this->deleteUnitData = [];
    }

    public function deleteFurnitureUnit(string $unitId): void
    {
        if (!$this->selectedRoomId) return;

        $room = Room::find($this->selectedRoomId);
        if ($room) {
            $this->furnitureService->removeFurnitureUnit($room, $unitId);
            $this->loadRoomFurniture();
            $this->toastSuccess("Unit perabot berhasil dihapus dari kamar.");
        }
    }

    // =========================================================================
    // MODAL SALIN LAYOUT (CLONE) KE KAMAR LAIN
    // =========================================================================

    public function openCloneModal(): void
    {
        $this->cloneSelectedUnitIds = array_column($this->furnitureUnits, 'id');
        $this->cloneMode            = 'overwrite';
        $this->cloneTargetRoomIds   = [];
        $this->showCloneModal       = true;
    }

    public function toggleCloneUnit(string $unitId): void
    {
        if (in_array($unitId, $this->cloneSelectedUnitIds)) {
            $this->cloneSelectedUnitIds = array_values(array_diff($this->cloneSelectedUnitIds, [$unitId]));
        } else {
            $this->cloneSelectedUnitIds[] = $unitId;
        }
    }

    public function selectAllCloneUnits(): void
    {
        $allIds = array_column($this->furnitureUnits, 'id');
        if (count($this->cloneSelectedUnitIds) === count($allIds)) {
            $this->cloneSelectedUnitIds = [];
        } else {
            $this->cloneSelectedUnitIds = $allIds;
        }
    }

    public function toggleCloneTarget(string $roomId): void
    {
        if (in_array($roomId, $this->cloneTargetRoomIds)) {
            $this->cloneTargetRoomIds = array_diff($this->cloneTargetRoomIds, [$roomId]);
        } else {
            $this->cloneTargetRoomIds[] = $roomId;
        }
    }

    public function selectAllRoomsInDormitory(): void
    {
        if (!$this->selectedDormitoryId) return;

        $roomIds = Room::where('dormitory_id', $this->selectedDormitoryId)
            ->where('id', '!=', $this->selectedRoomId)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        $this->cloneTargetRoomIds = $roomIds;
    }

    public function executeCloneLayout(): void
    {
        if (!$this->selectedRoomId) return;

        if (empty($this->cloneSelectedUnitIds)) {
            $this->toastError('Pilih minimal 1 perabot / lemari yang ingin disalin.');
            return;
        }

        if (empty($this->cloneTargetRoomIds)) {
            $this->toastError('Pilih minimal 1 kamar tujuan untuk disalin.');
            return;
        }

        $sourceRoom = Room::find($this->selectedRoomId);
        if ($sourceRoom) {
            $count = $this->furnitureService->cloneLayout(
                $sourceRoom,
                $this->cloneTargetRoomIds,
                $this->cloneSelectedUnitIds,
                $this->cloneMode
            );
            $this->showCloneModal = false;
            $unitCount = count($this->cloneSelectedUnitIds);
            $modeText = $this->cloneMode === 'append' ? 'ditambahkan ke' : 'disalin (mengganti)';
            $this->toastSuccess("{$unitCount} perabot berhasil {$modeText} {$count} kamar tujuan!");
        }
    }

    protected function calculateNextStartingNumber(string $prefix): int
    {
        if (!$this->selectedRoomId) return 1;

        $existingCodes = Locker::where('room_id', $this->selectedRoomId)
            ->where('code', 'like', $prefix . '%')
            ->pluck('code');

        $max = 0;
        foreach ($existingCodes as $c) {
            $numPart = preg_replace('/[^0-9]/', '', $c);
            if (is_numeric($numPart)) {
                $max = max($max, (int)$numPart);
            }
        }

        return $max + 1;
    }

    public function render()
    {
        $user = auth()->user();

        // Dormitories for the active gender
        $dormitories = Dormitory::where('is_active', true)
            ->when($this->genderScope, fn($q) => $q->where('gender', $this->genderScope))
            ->with(['rooms' => function($rq) {
                $rq->where('is_active', true)->withCount('currentAssignments');
                if (!empty(trim($this->search))) {
                    $rq->where('name', 'like', '%' . trim($this->search) . '%');
                }
            }])
            ->get();

        if ($this->roomFilter === 'has_vacancy') {
            foreach ($dormitories as $d) {
                $d->setRelation('rooms', $d->rooms->filter(fn($r) => $r->current_assignments_count < $r->capacity));
            }
        } elseif ($this->roomFilter === 'full') {
            foreach ($dormitories as $d) {
                $d->setRelation('rooms', $d->rooms->filter(fn($r) => $r->current_assignments_count >= $r->capacity));
            }
        }

        $activeRoom = $this->selectedRoomId
            ? Room::with(['dormitory', 'currentAssignments.person.santriProfile'])->find($this->selectedRoomId)
            : null;

        // Occupants of the active room
        $occupantsRaw = collect();
        if ($activeRoom) {
            // Get all assigned lockers in this room to check who has what
            $assignedLockers = Locker::where('room_id', $activeRoom->id)
                ->whereNotNull('assigned_person_id')
                ->get();

            $bajuOwners  = $assignedLockers->where('type', 'baju')->pluck('code', 'assigned_person_id')->toArray();
            $kitabOwners = $assignedLockers->where('type', 'kitab')->pluck('code', 'assigned_person_id')->toArray();

            foreach ($activeRoom->currentAssignments as $asg) {
                if ($asg->person) {
                    $pid       = $asg->person->id;
                    $bajuCode  = $bajuOwners[$pid] ?? null;
                    $kitabCode = $kitabOwners[$pid] ?? null;
                    $hasBaju   = !empty($bajuCode);
                    $hasKitab  = !empty($kitabCode);

                    $occupantsRaw->push([
                        'id'          => $pid,
                        'name'        => $asg->person->name,
                        'nis'         => $asg->person->santriProfile?->nis ?? '-',
                        'baju_code'   => $bajuCode,
                        'kitab_code'  => $kitabCode,
                        'has_baju'    => $hasBaju,
                        'has_kitab'   => $hasKitab,
                        'is_complete' => $hasBaju && $hasKitab,
                    ]);
                }
            }
        }

        $occupantStats = [
            'total'       => $occupantsRaw->count(),
            'no_baju'     => $occupantsRaw->where('has_baju', false)->count(),
            'no_kitab'    => $occupantsRaw->where('has_kitab', false)->count(),
            'complete'    => $occupantsRaw->where('is_complete', true)->count(),
            'missing_any' => $occupantsRaw->filter(fn($s) => !$s['has_baju'] || !$s['has_kitab'])->count(),
        ];

        $filteredOccupants = $occupantsRaw;

        if ($this->occupantFilter === 'no_baju') {
            $filteredOccupants = $filteredOccupants->where('has_baju', false);
        } elseif ($this->occupantFilter === 'no_kitab') {
            $filteredOccupants = $filteredOccupants->where('has_kitab', false);
        } elseif ($this->occupantFilter === 'complete') {
            $filteredOccupants = $filteredOccupants->where('is_complete', true);
        } elseif ($this->occupantFilter === 'missing_any') {
            $filteredOccupants = $filteredOccupants->filter(fn($s) => !$s['has_baju'] || !$s['has_kitab']);
        }

        if (!empty(trim($this->occupantSearch))) {
            $query = strtolower(trim($this->occupantSearch));
            $filteredOccupants = $filteredOccupants->filter(function($s) use ($query) {
                return str_contains(strtolower($s['name']), $query)
                    || str_contains(strtolower($s['nis']), $query)
                    || str_contains(strtolower($s['baju_code'] ?? ''), $query)
                    || str_contains(strtolower($s['kitab_code'] ?? ''), $query);
            });
        }

        // Filter visible units according to activeUnitTab
        $unitCounts = [
            'all'    => count($this->furnitureUnits),
            'baju'   => collect($this->furnitureUnits)->filter(fn($u) => ($u['category'] ?? 'compartment') === 'compartment' && ($u['type'] ?? 'baju') === 'baju')->count(),
            'kitab'  => collect($this->furnitureUnits)->filter(fn($u) => ($u['category'] ?? 'compartment') === 'compartment' && ($u['type'] ?? '') === 'kitab')->count(),
            'single' => collect($this->furnitureUnits)->filter(fn($u) => ($u['category'] ?? '') === 'single')->count(),
        ];

        $visibleUnits = collect($this->furnitureUnits)->filter(function($u) {
            if ($this->activeUnitTab === 'baju') {
                return ($u['category'] ?? 'compartment') === 'compartment' && ($u['type'] ?? 'baju') === 'baju';
            }
            if ($this->activeUnitTab === 'kitab') {
                return ($u['category'] ?? 'compartment') === 'compartment' && ($u['type'] ?? '') === 'kitab';
            }
            if ($this->activeUnitTab === 'single') {
                return ($u['category'] ?? '') === 'single';
            }
            return true;
        })->values()->all();

        $availableRoomsList = $this->getAvailableRoomsList();
        $totalRoomsCount    = count($availableRoomsList);
        $currentRoomIndex   = 0;
        foreach ($availableRoomsList as $idx => $rItem) {
            if ($rItem['id'] === $this->selectedRoomId) {
                $currentRoomIndex = $idx + 1;
                break;
            }
        }

        $dormitoryRooms = collect();
        if ($activeRoom) {
            $dormitoryRooms = Room::where('dormitory_id', $activeRoom->dormitory_id)
                ->where('is_active', true)
                ->withCount('currentAssignments')
                ->orderBy('name')
                ->get();
        }

        return view('livewire.kepengasuhan.papan-denah-fasilitas-kamar', [
            'dormitories'        => $dormitories,
            'activeRoom'         => $activeRoom,
            'occupantsList'      => $filteredOccupants,
            'occupantStats'      => $occupantStats,
            'visibleUnits'       => $visibleUnits,
            'unitCounts'         => $unitCounts,
            'availableRoomsList' => $availableRoomsList,
            'currentRoomIndex'   => $currentRoomIndex,
            'totalRoomsCount'    => $totalRoomsCount,
            'dormitoryRooms'     => $dormitoryRooms,
        ])->layout('layouts.app', ['title' => 'Papan Sensus & Denah Lemari Kamar']);
    }
}
