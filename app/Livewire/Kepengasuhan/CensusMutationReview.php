<?php

namespace App\Livewire\Kepengasuhan;

use Livewire\Component;
use App\Livewire\Concerns\SendsToast;
use App\Modules\Kepengasuhan\Models\CensusV3CampaignDormitory;
use App\Modules\Kepengasuhan\Models\CensusMutationEntry;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Services\CensusMutationService;
use App\Modules\Kepengasuhan\Services\CensusV3Service;
use App\Modules\Kepengasuhan\Services\CensusV3AccessService;
use Illuminate\Support\Collection;

class CensusMutationReview extends Component
{
    use SendsToast;

    public string $campaignId;
    public string $dormitoryId;

    public bool $showRejectModal = false;
    public string $rejectionNotes = '';

    // Inline fix for target rooms
    public array $overrideTargetRooms = [];

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
            abort(404, 'Data asrama kampanye tidak ditemukan.');
        }

        $user = auth()->user();
        if (!$user || !$this->accessService->canAccessDormitory($user, $cd, 'review')) {
            abort(403, 'Akses ditolak. Anda tidak berwenang meninjau sensus asrama ini.');
        }
    }

    public function getCampaignDormitoryProperty(): ?CensusV3CampaignDormitory
    {
        return CensusV3CampaignDormitory::with('campaign', 'dormitory')
            ->where('campaign_id', $this->campaignId)
            ->where('dormitory_id', $this->dormitoryId)
            ->first();
    }

    public function getEntriesProperty(): Collection
    {
        return CensusMutationEntry::with(['person', 'fromRoom', 'toRoom.dormitory', 'enteredBy'])
            ->where('campaign_id', $this->campaignId)
            ->where('dormitory_id', $this->dormitoryId)
            ->get();
    }

    public function getTargetRoomsProperty(): Collection
    {
        $cd = $this->campaignDormitory;
        $gender = $cd?->dormitory?->gender ?? 'L';
        return $this->mutationService->getAvailableTargetRooms($gender);
    }

    public function updateTargetRoom(string $entryId, string $newRoomId): void
    {
        $entry = CensusMutationEntry::find($entryId);
        if ($entry) {
            $entry->update([
                'to_room_id'   => $newRoomId ?: null,
                'apply_status' => 'pending',
                'apply_error'  => null,
            ]);
            $this->toastSuccess('Kamar tujuan santri berhasil disesuaikan.');
        }
    }

    public function approveAndApply(): void
    {
        $cd = $this->campaignDormitory;
        if (!$cd || !$this->accessService->canAccessDormitory(auth()->user(), $cd, 'review')) {
            abort(403, 'Akses ditolak.');
        }

        try {
            $result = $this->mutationService->applyApprovedMutations($cd->id, auth()->id());

            if ($result['failed'] > 0) {
                $this->toastWarning("Laporan disetujui dengan catatan: {$result['applied']} berhasil diterapkan, {$result['failed']} gagal (kamar penuh/error).");
            } else {
                $this->toastSuccess("Laporan sensus mutasi disetujui! Seluruh {$result['applied']} mutasi santri berhasil diterapkan.");
            }

            $this->redirect(route('sensus.campaigns'));
        } catch (\Exception $e) {
            $this->toastError('Gagal menyetujui laporan: ' . $e->getMessage());
        }
    }

    public function reject(): void
    {
        $cd = $this->campaignDormitory;
        if (!$cd || !$this->accessService->canAccessDormitory(auth()->user(), $cd, 'review')) {
            abort(403, 'Akses ditolak.');
        }

        $this->validate([
            'rejectionNotes' => 'required|string|max:1000|min:3',
        ], [
            'rejectionNotes.required' => 'Catatan penolakan wajib diisi agar pengisi tahu apa yang perlu diperbaiki.',
        ]);

        try {
            $this->censusService->rejectDormitory($cd->id, $this->rejectionNotes);

            $this->showRejectModal = false;
            $this->rejectionNotes = '';

            $this->toastSuccess('Laporan sensus mutasi dikembalikan ke pengisi.');
            $this->redirect(route('sensus.campaigns'));
        } catch (\Exception $e) {
            $this->toastError('Gagal menolak laporan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $entries = $this->entries;

        $pindahCount = $entries->where('action', 'pindah_kamar')->count();
        $statusCount = $entries->where('action', 'ubah_status')->count();
        $hilangCount = $entries->where('action', 'tidak_ditemukan')->count();
        $failedCount = $entries->where('apply_status', 'failed')->count();

        return view('livewire.kepengasuhan.census-mutation-review', [
            'cd'          => $this->campaignDormitory,
            'entries'     => $entries,
            'targetRooms' => $this->targetRooms,
            'stats'       => [
                'pindah' => $pindahCount,
                'status' => $statusCount,
                'hilang' => $hilangCount,
                'failed' => $failedCount,
            ],
        ])->layout('layouts.app', ['title' => 'Review Sensus Mutasi Santri']);
    }
}
