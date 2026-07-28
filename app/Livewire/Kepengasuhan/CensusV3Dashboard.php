<?php

namespace App\Livewire\Kepengasuhan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\Concerns\SendsToast;
use App\Modules\Kepengasuhan\Models\CensusV3Campaign;
use App\Modules\Kepengasuhan\Models\CensusV3CampaignDormitory;
use App\Modules\Kepengasuhan\Services\CensusV3Service;

class CensusV3Dashboard extends Component
{
    use SendsToast, WithPagination;

    // Filter status: 'active', 'draft', 'closed'
    public string $statusFilter = 'active';

    // Search query for campaigns
    public string $search = '';

    protected CensusV3Service $censusService;

    public function boot(CensusV3Service $censusService): void
    {
        $this->censusService = $censusService;
    }

    public function mount(): void
    {
        $user = auth()->user();
        if (!$user || !($user->hasRole('super-admin') || $user->hasRole('manajemen'))) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Super Admin dan Manajemen.');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function publishCampaign(string $id): void
    {
        try {
            $this->censusService->activateCampaign($id);
            $this->toastSuccess('Kampanye sensus berhasil diterbitkan dan dimulai.');
        } catch (\Exception $e) {
            $this->toastError('Gagal menerbitkan kampanye: ' . $e->getMessage());
        }
    }

    public function closeForReview(string $id): void
    {
        try {
            $this->censusService->closeCampaignForReview($id);
            $this->toastSuccess('Sensus dihentikan, silakan lakukan review terhadap laporan masuk.');
        } catch (\Exception $e) {
            $this->toastError('Gagal memindahkan ke review: ' . $e->getMessage());
        }
    }

    public function finalizeCampaign(string $id): void
    {
        try {
            // Ensure all dormitories are approved
            $campaign = CensusV3Campaign::with('dormitories')->findOrFail($id);
            $unapproved = $campaign->dormitories->where('status', '!=', 'approved')->count();

            if ($unapproved > 0) {
                $this->toastError("Ada {$unapproved} asrama yang belum disetujui. Setujui semua laporan terlebih dahulu.");
                return;
            }

            $this->censusService->closeCampaign($id);
            $this->toastSuccess('Kampanye sensus berhasil diselesaikan secara permanen.');
        } catch (\Exception $e) {
            $this->toastError('Gagal menyelesaikan kampanye: ' . $e->getMessage());
        }
    }

    public function deleteCampaign(string $id): void
    {
        try {
            $campaign = CensusV3Campaign::findOrFail($id);
            if ($campaign->status !== 'draft') {
                $this->toastError('Hanya kampanye berstatus draft yang bisa dihapus.');
                return;
            }

            // Delete dependencies manually to be clean
            $campaign->dormitories()->delete();
            $campaign->responses()->delete();
            $campaign->delete();

            $this->toastSuccess('Draft kampanye sensus berhasil dihapus.');
        } catch (\Exception $e) {
            $this->toastError('Gagal menghapus kampanye: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = CensusV3Campaign::with([
            'template',
            'creator',
            'dormitories.dormitory',
            'dormitories.assignedUser'
        ])->orderBy('created_at', 'desc');

        if ($this->statusFilter === 'active') {
            $query->whereIn('status', ['active', 'collecting', 'review']);
        } elseif ($this->statusFilter === 'draft') {
            $query->where('status', 'draft');
        } else {
            $query->where('status', 'closed');
        }

        if (!empty(trim($this->search))) {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm);
            });
        }

        return view('livewire.kepengasuhan.census-v3-dashboard', [
            'campaigns' => $query->paginate(3),
        ])->layout('layouts.app', ['title' => 'Sensus Fleksibel']);
    }
}
