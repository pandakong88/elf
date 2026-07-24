<?php

namespace App\Livewire\Kepengasuhan;

use Livewire\Component;
use App\Livewire\Concerns\SendsToast;
use App\Modules\Kepengasuhan\Models\CensusV3Campaign;
use App\Modules\Kepengasuhan\Models\CensusV3CampaignDormitory;
use App\Modules\Kepengasuhan\Models\CensusV3Response;
use App\Modules\Kepengasuhan\Services\CensusV3Service;
use Illuminate\Support\Collection;

class CensusV3Review extends Component
{
    use SendsToast;

    public string $campaignId;
    public string $dormitoryId;

    // Reject Form state
    public bool $showRejectModal = false;
    public string $rejectionNotes = '';

    protected CensusV3Service $censusService;

    public function boot(CensusV3Service $censusService): void
    {
        $this->censusService = $censusService;
    }

    public function mount(string $campaign, string $dormitory): void
    {
        $this->campaignId = $campaign;
        $this->dormitoryId = $dormitory;
    }

    public function getCampaignDormitoryProperty(): ?CensusV3CampaignDormitory
    {
        return CensusV3CampaignDormitory::with('campaign.template.fields', 'dormitory', 'assignedUser')
            ->where('campaign_id', $this->campaignId)
            ->where('dormitory_id', $this->dormitoryId)
            ->first();
    }

    public function getResponsesProperty(): Collection
    {
        return CensusV3Response::with('person.activeRoles', 'room')
            ->where('campaign_id', $this->campaignId)
            ->where('dormitory_id', $this->dormitoryId)
            ->get();
    }

    public function getProfileChangesProperty(): Collection
    {
        return $this->responses->where('has_profile_changes', true);
    }

    public function approve(): void
    {
        try {
            $cd = $this->campaignDormitory;
            
            $this->censusService->approveDormitory($cd->id, auth()->id());
            
            $this->toastSuccess('Laporan sensus asrama berhasil disetujui. Perubahan profil telah disinkronkan.');
            $this->redirect(route('sensus.campaigns'));
        } catch (\Exception $e) {
            $this->toastError('Gagal menyetujui laporan: ' . $e->getMessage());
        }
    }

    public function reject(): void
    {
        $this->validate([
            'rejectionNotes' => 'required|string|max:1000|min:3',
        ], [
            'rejectionNotes.required' => 'Catatan penolakan wajib diisi agar pengisi tahu apa yang perlu diperbaiki.',
        ]);

        try {
            $cd = $this->campaignDormitory;
            
            $this->censusService->rejectDormitory($cd->id, $this->rejectionNotes);

            $this->showRejectModal = false;
            $this->rejectionNotes = '';

            $this->toastSuccess('Laporan sensus dikembalikan ke pengisi.');
            $this->redirect(route('sensus.campaigns'));
        } catch (\Exception $e) {
            $this->toastError('Gagal menolak laporan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.kepengasuhan.census-v3-review', [
            'cd'             => $this->campaignDormitory,
            'responses'      => $this->responses,
            'profileChanges' => $this->profileChanges,
        ])->layout('layouts.app', ['title' => 'Review Sensus']);
    }
}
