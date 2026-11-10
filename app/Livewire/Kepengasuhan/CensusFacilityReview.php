<?php

namespace App\Livewire\Kepengasuhan;

use Livewire\Component;
use App\Livewire\Concerns\SendsToast;
use App\Modules\Kepengasuhan\Models\CensusV3Campaign;
use App\Modules\Kepengasuhan\Models\CensusV3CampaignDormitory;
use App\Modules\Kepengasuhan\Models\CensusFacilityItem;
use App\Modules\Kepengasuhan\Services\CensusFacilityService;
use App\Modules\Kepengasuhan\Services\CensusV3Service;
use App\Modules\Kepengasuhan\Services\CensusV3AccessService;

class CensusFacilityReview extends Component
{
    use SendsToast;

    public string $campaignId;
    public string $dormitoryId;

    // Filters
    public string $filterCondition = 'all'; // all, damaged (rusak_ringan, rusak_berat, hilang), baik
    public string $filterType = 'all';      // all, komplek, kamar, lemari

    // Reject modal
    public bool $showRejectModal = false;
    public string $rejectionNotes = '';

    protected CensusFacilityService $facilityService;
    protected CensusV3Service $censusService;
    protected CensusV3AccessService $accessService;

    public function boot(
        CensusFacilityService $facilityService,
        CensusV3Service $censusService,
        CensusV3AccessService $accessService
    ): void {
        $this->facilityService = $facilityService;
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

        if (!$this->accessService->canAccessDormitory(auth()->user(), $cd, 'review')) {
            abort(403, 'Akses ditolak. Anda tidak berwenang mereview laporan sensus fasilitas asrama ini.');
        }
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

    public function approveAndApply(): void
    {
        $cd = $this->campaignDormitory;
        if (!$cd || !$this->accessService->canAccessDormitory(auth()->user(), $cd, 'review')) {
            abort(403, 'Akses ditolak.');
        }

        try {
            $result = $this->facilityService->applyApprovedFacilities($cd->id, auth()->id());

            if ($result['failed'] > 0) {
                $this->toastWarning("Laporan disetujui dengan catatan: {$result['applied']} berhasil disinkronkan, {$result['failed']} gagal.");
            } else {
                $this->toastSuccess("Laporan sensus fasilitas disetujui! Data inventaris lemari berhasil disinkronkan.");
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

            $this->toastSuccess('Laporan sensus fasilitas dikembalikan ke pengisi.');
            $this->redirect(route('sensus.campaigns'));
        } catch (\Exception $e) {
            $this->toastError('Gagal menolak laporan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = CensusFacilityItem::with(['room', 'locker', 'assignedPerson.santriProfile'])
            ->where('campaign_id', $this->campaignId)
            ->where('dormitory_id', $this->dormitoryId);

        if ($this->filterType !== 'all') {
            $query->where('item_type', $this->filterType);
        }

        if ($this->filterCondition === 'damaged') {
            $query->whereIn('condition', ['rusak_ringan', 'rusak_berat', 'hilang']);
        } elseif ($this->filterCondition !== 'all') {
            $query->where('condition', $this->filterCondition);
        }

        $items = $query->orderBy('item_type')->orderBy('item_name')->get();

        // Overall stats
        $allItems = CensusFacilityItem::where('campaign_id', $this->campaignId)
            ->where('dormitory_id', $this->dormitoryId)
            ->get();

        $stats = [
            'total'        => $allItems->count(),
            'baik'         => $allItems->where('condition', 'baik')->count(),
            'rusak_ringan' => $allItems->where('condition', 'rusak_ringan')->count(),
            'rusak_berat'  => $allItems->where('condition', 'rusak_berat')->count(),
            'hilang'       => $allItems->where('condition', 'hilang')->count(),
        ];

        return view('livewire.kepengasuhan.census-facility-review', [
            'campaign'  => $this->campaign,
            'cd'        => $this->campaignDormitory,
            'dormitory' => $this->campaignDormitory?->dormitory,
            'items'     => $items,
            'stats'     => $stats,
        ])->layout('layouts.app', ['title' => 'Review Sensus Fasilitas & Lemari']);
    }
}
