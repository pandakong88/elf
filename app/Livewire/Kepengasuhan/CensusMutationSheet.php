<?php

namespace App\Livewire\Kepengasuhan;

use Livewire\Component;
use App\Livewire\Concerns\SendsToast;
use App\Modules\Kepengasuhan\Models\CensusV3Campaign;
use App\Modules\Kepengasuhan\Models\CensusV3CampaignDormitory;
use App\Modules\Kepengasuhan\Models\CensusMutationEntry;
use App\Modules\Kepengasuhan\Services\CensusMutationService;
use App\Modules\Kepengasuhan\Services\CensusV3Service;
use App\Modules\Kepengasuhan\Services\CensusV3AccessService;

class CensusMutationSheet extends Component
{
    use SendsToast;

    public string $campaignId;
    public string $dormitoryId;

    public ?string $selectedRoomId = null;

    // Room occupants & unassigned data
    public array $rooms = [];
    public array $unassigned = [];
    public array $availableTargetRooms = [];

    // Form inputs indexed by person_id:
    // [person_id => ['action' => 'tetap', 'to_room_id' => ..., 'new_enrollment_status' => ..., 'notes' => ...]]
    public array $entries = [];

    protected CensusMutationService $mutationService;
    protected CensusV3Service $censusService;
    protected CensusV3AccessService $accessService;

    public function boot(
        CensusMutationService $mutationService,
        CensusV3Service $censusService,
        CensusV3AccessService $accessService
    ): void {
        $this->mutationService = $mutationService;
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
            abort(403, 'Akses ditolak. Anda tidak berwenang mengisi data sensus asrama ini.');
        }

        $this->loadData();
    }

    public function getCampaignDormitoryProperty(): ?CensusV3CampaignDormitory
    {
        return CensusV3CampaignDormitory::with('campaign', 'dormitory')
            ->where('campaign_id', $this->campaignId)
            ->where('dormitory_id', $this->dormitoryId)
            ->first();
    }

    public function loadData(): void
    {
        $occupantsData = $this->mutationService->getDormitoryOccupants($this->dormitoryId);
        $this->rooms      = $occupantsData['rooms'];
        $this->unassigned = $occupantsData['unassigned']->toArray();

        $dormGender = $occupantsData['dormitory']->gender;
        $this->availableTargetRooms = $this->mutationService->getAvailableTargetRooms($dormGender)->toArray();

        // Select first room by default if available
        if (!$this->selectedRoomId && !empty($this->rooms)) {
            $this->selectedRoomId = $this->rooms[0]['id'];
        } elseif (!$this->selectedRoomId && !empty($this->unassigned)) {
            $this->selectedRoomId = 'unassigned';
        }

        // Load existing saved entries
        $saved = $this->mutationService->getSavedEntries($this->campaignId, $this->dormitoryId);

        // Pre-fill entries for room occupants
        foreach ($this->rooms as $r) {
            foreach ($r['occupants'] as $occ) {
                $pid = $occ['person_id'];
                $s = $saved->get($pid);

                $this->entries[$pid] = [
                    'from_room_id'          => $r['id'],
                    'action'                => $s ? $s->action : 'tetap',
                    'to_room_id'            => $s ? $s->to_room_id : null,
                    'new_enrollment_status' => $s ? $s->new_enrollment_status : 'aktif',
                    'notes'                 => $s ? ($s->notes ?? '') : '',
                ];
            }
        }

        // Pre-fill entries for unassigned santri
        foreach ($this->unassigned as $u) {
            $pid = $u['person_id'];
            $s = $saved->get($pid);

            $this->entries[$pid] = [
                'from_room_id'          => null,
                'action'                => $s ? $s->action : 'pindah_kamar',
                'to_room_id'            => $s ? $s->to_room_id : null,
                'new_enrollment_status' => $s ? $s->new_enrollment_status : 'aktif',
                'notes'                 => $s ? ($s->notes ?? '') : '',
            ];
        }
    }

    public string $viewMode = 'board'; // 'board' (drag-drop kanban) or 'table' (list)

    public function selectRoom(string $roomId): void
    {
        $this->selectedRoomId = $roomId;
    }

    public function moveSantri(string $personId, string $targetType, ?string $targetValue = null): void
    {
        if (!isset($this->entries[$personId])) {
            return;
        }

        $fromRoomId = $this->entries[$personId]['from_room_id'] ?? null;

        if ($targetType === 'room') {
            if ($targetValue === $fromRoomId) {
                // Dikembalikan ke kamar asal
                $this->entries[$personId]['action'] = 'tetap';
                $this->entries[$personId]['to_room_id'] = null;
                $this->entries[$personId]['new_enrollment_status'] = 'aktif';
            } else {
                $this->entries[$personId]['action'] = 'pindah_kamar';
                $this->entries[$personId]['to_room_id'] = $targetValue;
                $this->entries[$personId]['new_enrollment_status'] = 'aktif';
            }
        } elseif ($targetType === 'laju') {
            $this->entries[$personId]['action'] = 'ubah_status';
            $this->entries[$personId]['to_room_id'] = null;
            $this->entries[$personId]['new_enrollment_status'] = 'santri_laju';
        } elseif ($targetType === 'boyong') {
            $this->entries[$personId]['action'] = 'ubah_status';
            $this->entries[$personId]['to_room_id'] = null;
            $this->entries[$personId]['new_enrollment_status'] = 'keluar_resmi';
        } elseif ($targetType === 'unassigned') {
            $this->entries[$personId]['action'] = 'ubah_status';
            $this->entries[$personId]['to_room_id'] = null;
            $this->entries[$personId]['new_enrollment_status'] = 'aktif';
        }

        // Auto save single entry to keep state safe
        $this->mutationService->saveEntry(
            $this->campaignId,
            $this->dormitoryId,
            $personId,
            $this->entries[$personId],
            auth()->id()
        );

        $this->toastSuccess('Draft mutasi santri berhasil diperbarui.');
    }

    public function resetSantri(string $personId): void
    {
        if (!isset($this->entries[$personId])) {
            return;
        }

        $fromRoomId = $this->entries[$personId]['from_room_id'] ?? null;
        $this->entries[$personId]['action'] = $fromRoomId ? 'tetap' : 'pindah_kamar';
        $this->entries[$personId]['to_room_id'] = null;
        $this->entries[$personId]['new_enrollment_status'] = 'aktif';

        $this->mutationService->saveEntry(
            $this->campaignId,
            $this->dormitoryId,
            $personId,
            $this->entries[$personId],
            auth()->id()
        );

        $this->toastInfo('Perubahan mutasi santri dibatalkan.');
    }

    public function setAction(string $personId, string $action): void
    {
        if (isset($this->entries[$personId])) {
            $this->entries[$personId]['action'] = $action;
        }
    }

    public function saveDraft(): void
    {
        $userId = auth()->id();
        $savedCount = 0;

        foreach ($this->entries as $personId => $data) {
            $this->mutationService->saveEntry(
                $this->campaignId,
                $this->dormitoryId,
                $personId,
                $data,
                $userId
            );
            $savedCount++;
        }

        $this->toastSuccess("Draf sensus mutasi berhasil disimpan ({$savedCount} santri).");
    }

    public function submitCensus(): void
    {
        $this->saveDraft();

        try {
            $cd = $this->getCampaignDormitoryProperty();
            $this->censusService->submitDormitory($cd->id, auth()->id());

            $this->toastSuccess('Laporan sensus mutasi berhasil dikirim untuk direview.');
            $this->redirect(route('sensus.campaigns'));
        } catch (\Exception $e) {
            $this->toastError('Gagal mengirim laporan: ' . $e->getMessage());
        }
    }

    public function getSummaryProperty(): array
    {
        $total = count($this->entries);
        $tetap = 0;
        $pindah = 0;
        $status = 0;
        $hilang = 0;

        foreach ($this->entries as $e) {
            match ($e['action'] ?? '') {
                'tetap'           => $tetap++,
                'pindah_kamar'    => $pindah++,
                'ubah_status'     => $status++,
                'tidak_ditemukan' => $hilang++,
                default           => null,
            };
        }

        return [
            'total'           => $total,
            'tetap'           => $tetap,
            'pindah_kamar'    => $pindah,
            'ubah_status'     => $status,
            'tidak_ditemukan' => $hilang,
        ];
    }

    public function getBoardDataProperty(): array
    {
        $allPersons = [];
        foreach ($this->rooms as $r) {
            foreach ($r['occupants'] as $occ) {
                $allPersons[$occ['person_id']] = array_merge($occ, [
                    'original_room_name' => $r['name'],
                    'original_room_id'   => $r['id'],
                ]);
            }
        }
        foreach ($this->unassigned as $u) {
            $allPersons[$u['person_id']] = array_merge($u, [
                'original_room_name' => 'Belum Ada Kamar',
                'original_room_id'   => null,
            ]);
        }

        // Room buckets
        $boardRooms = [];
        foreach ($this->rooms as $r) {
            $boardRooms[$r['id']] = [
                'id'        => $r['id'],
                'name'      => $r['name'],
                'capacity'  => $r['capacity'],
                'occupants' => [],
            ];
        }

        $dockUnassigned = [];
        $dockLaju       = [];
        $dockBoyong     = [];

        foreach ($allPersons as $pid => $person) {
            $entry     = $this->entries[$pid] ?? null;
            $action    = $entry['action'] ?? 'tetap';
            $toRoomId  = $entry['to_room_id'] ?? null;
            $newStatus = $entry['new_enrollment_status'] ?? 'aktif';

            $isMoved = ($action === 'pindah_kamar' && $toRoomId && $toRoomId !== ($person['original_room_id'] ?? null))
                || ($action === 'ubah_status' && in_array($newStatus, ['santri_laju', 'keluar_resmi']));

            $personWithState = array_merge($person, [
                'action'     => $action,
                'to_room_id' => $toRoomId,
                'new_status' => $newStatus,
                'is_moved'   => $isMoved,
            ]);

            if ($action === 'ubah_status' && $newStatus === 'santri_laju') {
                $dockLaju[] = $personWithState;
            } elseif ($action === 'ubah_status' && $newStatus === 'keluar_resmi') {
                $dockBoyong[] = $personWithState;
            } elseif ($action === 'pindah_kamar' && $toRoomId && isset($boardRooms[$toRoomId])) {
                $boardRooms[$toRoomId]['occupants'][] = $personWithState;
            } elseif ($person['original_room_id'] && isset($boardRooms[$person['original_room_id']])) {
                $boardRooms[$person['original_room_id']]['occupants'][] = $personWithState;
            } else {
                $dockUnassigned[] = $personWithState;
            }
        }

        return [
            'rooms'           => array_values($boardRooms),
            'dock_unassigned' => $dockUnassigned,
            'dock_laju'       => $dockLaju,
            'dock_boyong'     => $dockBoyong,
        ];
    }

    public function render()
    {
        $currentRoom = null;
        if ($this->selectedRoomId === 'unassigned') {
            $currentRoom = [
                'id'        => 'unassigned',
                'name'      => 'Santri Belum Memiliki Kamar',
                'capacity'  => count($this->unassigned),
                'occupied'  => count($this->unassigned),
                'occupants' => $this->unassigned,
            ];
        } else {
            foreach ($this->rooms as $r) {
                if ($r['id'] === $this->selectedRoomId) {
                    $currentRoom = $r;
                    break;
                }
            }
        }

        return view('livewire.kepengasuhan.census-mutation-sheet', [
            'cd'          => $this->getCampaignDormitoryProperty(),
            'currentRoom' => $currentRoom,
            'summary'     => $this->getSummaryProperty(),
            'boardData'   => $this->getBoardDataProperty(),
        ])->layout('layouts.app', ['title' => 'Sensus Mutasi Santri']);
    }
}
