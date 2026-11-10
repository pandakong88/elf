<?php

namespace App\Livewire\Kepengasuhan;

use Livewire\Component;
use App\Livewire\Concerns\SendsToast;
use App\Modules\Kepengasuhan\Models\CensusV3Campaign;
use App\Modules\Kepengasuhan\Models\CensusV3CampaignDormitory;
use App\Modules\Kepengasuhan\Models\CensusFacilityItem;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Kepengasuhan\Services\CensusFacilityService;
use App\Modules\Kepengasuhan\Services\LockerService;
use App\Modules\Kepengasuhan\Services\CensusV3Service;
use App\Modules\Kepengasuhan\Services\CensusV3AccessService;

class CensusFacilitySheet extends Component
{
    use SendsToast;

    public string $campaignId;
    public string $dormitoryId;

    public string $activeSection = 'komplek'; // 'komplek' or room id

    public array $komplekItems = [];
    public array $rooms = [];
    public array $roomOccupants = []; // [room_id => [ [id, name, nis] ]]

    // Form entries keyed by unique identifier:
    // Komplek: "kmp_{name}" => ['item_type' => 'komplek', 'item_name' => ..., 'condition' => 'baik', 'notes' => '']
    // Kamar: "kmr_{room_id}_{name}" => ['item_type' => 'kamar', 'room_id' => ..., 'item_name' => ..., 'condition' => 'baik', 'notes' => '']
    // Lemari: "lmr_{locker_id}" => ['item_type' => 'lemari', 'room_id' => ..., 'locker_id' => ..., 'item_name' => ..., 'condition' => 'baik', 'assigned_person_id' => ..., 'notes' => '']
    public array $entries = [];

    protected CensusFacilityService $facilityService;
    protected LockerService $lockerService;
    protected CensusV3Service $censusService;
    protected CensusV3AccessService $accessService;

    public function boot(
        CensusFacilityService $facilityService,
        LockerService $lockerService,
        CensusV3Service $censusService,
        CensusV3AccessService $accessService
    ): void {
        $this->facilityService = $facilityService;
        $this->lockerService   = $lockerService;
        $this->censusService   = $censusService;
        $this->accessService   = $accessService;
    }

    public function mount(string $campaign, string $dormitory): void
    {
        $this->campaignId  = $campaign;
        $this->dormitoryId = $dormitory;

        $cd = $this->getCampaignDormitoryProperty();
        if (!$cd) {
            $this->redirect(route('sensus.campaigns'));
            return;
        }

        if (!$this->accessService->canAccessDormitory(auth()->user(), $cd, 'fill')) {
            abort(403, 'Akses ditolak. Anda tidak berwenang mengisi data sensus fasilitas asrama ini.');
        }

        $this->loadData();
    }

    public function getCampaignProperty(): ?CensusV3Campaign
    {
        return CensusV3Campaign::find($this->campaignId);
    }

    public function getCampaignDormitoryProperty(): ?CensusV3CampaignDormitory
    {
        return CensusV3CampaignDormitory::with('dormitory')
            ->where('campaign_id', $this->campaignId)
            ->where('dormitory_id', $this->dormitoryId)
            ->first();
    }

    public function loadData(): void
    {
        $sheetData = $this->facilityService->getFacilityCensusSheet($this->dormitoryId);
        $this->komplekItems = $sheetData['komplek_items'];
        $this->rooms        = $sheetData['rooms'];

        // Load room occupants for quick locker assignment
        $this->roomOccupants = [];
        foreach ($this->rooms as $room) {
            $assignments = RoomAssignment::active()
                ->where('room_id', $room['id'])
                ->with(['person.santriProfile'])
                ->get();

            $this->roomOccupants[$room['id']] = $assignments->map(function ($a) {
                return [
                    'id'   => $a->person_id,
                    'name' => $a->person?->name,
                    'nis'  => $a->person?->santriProfile?->nis,
                ];
            })->toArray();
        }

        // Initialize entries
        $saved = $this->facilityService->getSavedEntries($this->campaignId, $this->dormitoryId);

        // 1. Komplek items
        foreach ($this->komplekItems as $name) {
            $key = 'kmp_' . md5($name);
            $rec = $saved->firstWhere(fn($s) => $s->item_type === 'komplek' && $s->item_name === $name);
            $this->entries[$key] = [
                'item_type' => 'komplek',
                'item_name' => $name,
                'condition' => $rec?->condition ?? 'baik',
                'notes'     => $rec?->notes ?? '',
            ];
        }

        // 2. Kamar & Lemari items
        foreach ($this->rooms as $room) {
            // Kamar items
            foreach ($room['kamar_items'] as $name) {
                $key = 'kmr_' . $room['id'] . '_' . md5($name);
                $rec = $saved->firstWhere(fn($s) => $s->item_type === 'kamar' && $s->room_id === $room['id'] && $s->item_name === $name);
                $this->entries[$key] = [
                    'item_type' => 'kamar',
                    'room_id'   => $room['id'],
                    'item_name' => $name,
                    'condition' => $rec?->condition ?? 'baik',
                    'notes'     => $rec?->notes ?? '',
                ];
            }

            // Lemari items
            foreach ($room['lockers'] as $locker) {
                $key = 'lmr_' . $locker->id;
                $rec = $saved->firstWhere(fn($s) => $s->item_type === 'lemari' && $s->locker_id === $locker->id);
                $this->entries[$key] = [
                    'item_type'          => 'lemari',
                    'room_id'            => $room['id'],
                    'locker_id'          => $locker->id,
                    'item_name'          => 'Lemari ' . $locker->code,
                    'condition'          => $rec?->condition ?? $locker->condition,
                    'assigned_person_id' => $rec ? $rec->assigned_person_id : $locker->assigned_person_id,
                    'notes'              => $rec?->notes ?? $locker->notes ?? '',
                ];
            }
        }
    }

    public function setCondition(string $key, string $condition): void
    {
        if (isset($this->entries[$key])) {
            $this->entries[$key]['condition'] = $condition;
        }
    }

    public function autoGenerateLockers(string $roomId, int $count = 8, string $type = 'all'): void
    {
        try {
            if ($type === 'all') {
                $this->lockerService->generateRoomSet($roomId, $count);
                $this->toastSuccess("Berhasil membuat {$count} lemari baju dan {$count} rak kitab.");
            } elseif ($type === 'kitab') {
                $this->lockerService->generateLockersForRoom($roomId, $count, 'K-', 'kitab');
                $this->toastSuccess("Berhasil membuat {$count} rak kitab.");
            } else {
                $this->lockerService->generateLockersForRoom($roomId, $count, 'B-', 'baju');
                $this->toastSuccess("Berhasil membuat {$count} lemari pakaian.");
            }
            $this->loadData();
        } catch (\Exception $e) {
            $this->toastError('Gagal membuat perabot: ' . $e->getMessage());
        }
    }

    public function saveDraft(): void
    {
        $userId = auth()->id();

        try {
            foreach ($this->entries as $entry) {
                $this->facilityService->saveFacilityEntry(
                    $this->campaignId,
                    $this->dormitoryId,
                    $entry,
                    $userId
                );
            }
            $this->toastSuccess('Draf isian sensus fasilitas berhasil disimpan.');
        } catch (\Exception $e) {
            $this->toastError('Gagal menyimpan draf: ' . $e->getMessage());
        }
    }

    public function submitForReview(): void
    {
        $cd = $this->campaignDormitory;
        if (!$cd) {
            return;
        }

        $userId = auth()->id();

        try {
            // Save all items first
            foreach ($this->entries as $entry) {
                $this->facilityService->saveFacilityEntry(
                    $this->campaignId,
                    $this->dormitoryId,
                    $entry,
                    $userId
                );
            }

            // Submit dormitory to secretary
            $this->censusService->submitDormitory($cd->id, $userId);

            $this->toastSuccess('Laporan sensus fasilitas berhasil dikirim untuk direview oleh Sekretaris!');
            $this->redirect(route('sensus.campaigns'));
        } catch (\Exception $e) {
            $this->toastError('Gagal mengirim laporan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.kepengasuhan.census-facility-sheet', [
            'campaign'  => $this->campaign,
            'cd'        => $this->campaignDormitory,
            'dormitory' => $this->campaignDormitory?->dormitory,
        ])->layout('layouts.app', ['title' => 'Lembar Sensus Fasilitas & Lemari']);
    }
}
