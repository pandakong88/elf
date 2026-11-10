<?php

namespace App\Livewire\Kepengasuhan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\Concerns\SendsToast;
use App\Modules\Kepengasuhan\Models\CensusV3Campaign;
use App\Modules\Kepengasuhan\Models\CensusV3CampaignDormitory;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Kepengasuhan\Models\Locker;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Kepengasuhan\Models\SantriProfile;
use App\Modules\Kepengasuhan\Services\CensusV3Service;
use App\Modules\Kepengasuhan\Services\CensusV3AccessService;

class CensusV3Dashboard extends Component
{
    use SendsToast, WithPagination;

    // Filter status: 'active', 'draft', 'closed'
    public string $statusFilter = 'active';

    // Filter census_type: 'all', 'formal', 'mutasi', 'fasilitas'
    public string $typeFilter = 'all';

    // Search query for campaigns
    public string $search = '';

    // Modal state untuk pembatalan kampanye
    public bool $showCancelModal = false;
    public ?string $campaignIdToCancel = null;
    public string $campaignNameToCancel = '';
    public string $cancelReason = '';

    protected CensusV3Service $censusService;
    protected CensusV3AccessService $accessService;

    public function boot(CensusV3Service $censusService, CensusV3AccessService $accessService): void
    {
        $this->censusService = $censusService;
        $this->accessService = $accessService;
    }

    public function mount(): void
    {
        $user = auth()->user();
        if (!$user || !($user->can('manage-sensus-v3') || $user->can('input-census-v3'))) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman sensus.');
        }

        if ($type = request()->query('type')) {
            if (in_array($type, ['formal', 'mutasi', 'fasilitas'])) {
                $this->typeFilter = $type;
            }
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

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function canManageCampaign(CensusV3Campaign $campaign): bool
    {
        return $this->accessService->canManageCampaign(auth()->user(), $campaign);
    }

    public function canFillDormitory(CensusV3CampaignDormitory $cd): bool
    {
        return $this->accessService->canAccessDormitory(auth()->user(), $cd, 'fill');
    }

    public function canReviewDormitory(CensusV3CampaignDormitory $cd): bool
    {
        return $this->accessService->canAccessDormitory(auth()->user(), $cd, 'review');
    }

    public function getInputRoute(CensusV3Campaign $campaign, CensusV3CampaignDormitory $cd): string
    {
        if ($campaign->census_type === 'mutasi') {
            return route('sensus.mutasi.input', ['campaign' => $campaign->id, 'dormitory' => $cd->dormitory_id]);
        }
        if ($campaign->census_type === 'fasilitas') {
            return route('sensus.fasilitas.input', ['campaign' => $campaign->id, 'dormitory' => $cd->dormitory_id]);
        }
        return route('sensus.input', ['campaign' => $campaign->id, 'dormitory' => $cd->dormitory_id]);
    }

    public function getReviewRoute(CensusV3Campaign $campaign, CensusV3CampaignDormitory $cd): string
    {
        if ($campaign->census_type === 'mutasi') {
            return route('sensus.mutasi.review', ['campaign' => $campaign->id, 'dormitory' => $cd->dormitory_id]);
        }
        if ($campaign->census_type === 'fasilitas') {
            return route('sensus.fasilitas.review', ['campaign' => $campaign->id, 'dormitory' => $cd->dormitory_id]);
        }
        return route('sensus.review', ['campaign' => $campaign->id, 'dormitory' => $cd->dormitory_id]);
    }

    public function publishCampaign(string $id): void
    {
        try {
            $campaign = CensusV3Campaign::findOrFail($id);
            if (!$this->canManageCampaign($campaign)) {
                abort(403, 'Anda tidak berwenang menerbitkan kampanye ini.');
            }

            $this->censusService->activateCampaign($id);
            $this->toastSuccess('Kampanye sensus berhasil diterbitkan dan dimulai.');
        } catch (\Exception $e) {
            $this->toastError('Gagal menerbitkan kampanye: ' . $e->getMessage());
        }
    }

    public function closeForReview(string $id): void
    {
        try {
            $campaign = CensusV3Campaign::findOrFail($id);
            if (!$this->canManageCampaign($campaign)) {
                abort(403, 'Anda tidak berwenang menghentikan kampanye ini.');
            }

            $this->censusService->closeCampaignForReview($id);
            $this->toastSuccess('Sensus dihentikan, silakan lakukan review terhadap laporan masuk.');
        } catch (\Exception $e) {
            $this->toastError('Gagal memindahkan ke review: ' . $e->getMessage());
        }
    }

    public function finalizeCampaign(string $id): void
    {
        try {
            $campaign = CensusV3Campaign::with('dormitories')->findOrFail($id);
            if (!$this->canManageCampaign($campaign)) {
                abort(403, 'Anda tidak berwenang menyelesaikan kampanye ini.');
            }

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
            if (!$this->canManageCampaign($campaign)) {
                abort(403, 'Anda tidak berwenang menghapus kampanye ini.');
            }

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

    public function openCancelModal(string $id): void
    {
        $campaign = CensusV3Campaign::findOrFail($id);
        if (!$this->canManageCampaign($campaign)) {
            abort(403, 'Anda tidak berwenang membatalkan kampanye ini.');
        }

        $this->campaignIdToCancel = $campaign->id;
        $this->campaignNameToCancel = $campaign->name;
        $this->cancelReason = '';
        $this->showCancelModal = true;
    }

    public function closeCancelModal(): void
    {
        $this->showCancelModal = false;
        $this->campaignIdToCancel = null;
        $this->campaignNameToCancel = '';
        $this->cancelReason = '';
    }

    public function executeCancelCampaign(): void
    {
        if (!$this->campaignIdToCancel) {
            return;
        }

        try {
            $campaign = CensusV3Campaign::findOrFail($this->campaignIdToCancel);
            if (!$this->canManageCampaign($campaign)) {
                abort(403, 'Anda tidak berwenang membatalkan kampanye ini.');
            }

            $this->censusService->cancelCampaign(
                $this->campaignIdToCancel,
                trim($this->cancelReason) ?: null,
                auth()->id()
            );

            $this->closeCancelModal();
            $this->toastSuccess('Kampanye sensus berhasil dibatalkan dan dipindahkan ke arsip.');
        } catch (\Exception $e) {
            $this->toastError('Gagal membatalkan kampanye: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $user = auth()->user();
        $genderScope = $this->accessService->genderScope($user);
        $isCentral = $this->accessService->isCentral($user);

        $query = CensusV3Campaign::with([
            'template',
            'creator',
            'canceller',
            'dormitories' => function ($q) use ($genderScope, $user, $isCentral) {
                $q->with(['dormitory', 'assignedUser', 'assignees']);
                if (!$isCentral) {
                    if ($genderScope) {
                        $q->whereHas('dormitory', fn($dq) => $dq->where('gender', $genderScope));
                    } elseif (!$user->can('manage-sensus-v3')) {
                        // Musyrif tanpa gender scope: hanya asrama yang ditugaskan
                        $q->where(function ($sub) use ($user) {
                            $sub->where('assigned_to', $user->id)
                                ->orWhereHas('assignees', fn($aq) => $aq->where('users.id', $user->id));
                        });
                    }
                }
            }
        ])->orderBy('created_at', 'desc');

        // Filter campaign level
        if (!$isCentral) {
            if ($genderScope) {
                $query->where(function ($q) use ($genderScope) {
                    $q->where('target_scope', $genderScope === 'L' ? 'putra' : 'putri')
                      ->orWhereHas('dormitories.dormitory', fn($dq) => $dq->where('gender', $genderScope));
                });
            } elseif (!$user->can('manage-sensus-v3')) {
                // Musyrif biasa hanya melihat kampanye yang punya asrama tugasnya
                $query->whereHas('dormitories', function ($dq) use ($user) {
                    $dq->where('assigned_to', $user->id)
                       ->orWhereHas('assignees', fn($aq) => $aq->where('users.id', $user->id));
                });
            }
        }

        if ($this->statusFilter === 'active') {
            $query->whereIn('status', ['active', 'collecting', 'review']);
        } elseif ($this->statusFilter === 'draft') {
            $query->where('status', 'draft');
        } else {
            $query->whereIn('status', ['closed', 'cancelled']);
        }

        if ($this->typeFilter !== 'all') {
            $query->where('census_type', $this->typeFilter);
        }

        if (!empty(trim($this->search))) {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm);
            });
        }

        $stats = [
            'active'         => CensusV3Campaign::whereIn('status', ['active', 'collecting', 'review'])->count(),
            'waiting_review' => CensusV3CampaignDormitory::where('status', 'submitted')->count(),
            'closed'         => CensusV3Campaign::whereIn('status', ['closed', 'cancelled'])->count(),
        ];

        // Base query for counting campaigns per category tab (scoped by permission & gender)
        $baseCountQuery = CensusV3Campaign::query();
        if (!$isCentral) {
            if ($genderScope) {
                $baseCountQuery->where(function ($q) use ($genderScope) {
                    $q->where('target_scope', $genderScope === 'L' ? 'putra' : 'putri')
                      ->orWhereHas('dormitories.dormitory', fn($dq) => $dq->where('gender', $genderScope));
                });
            } elseif (!$user->can('manage-sensus-v3')) {
                $baseCountQuery->whereHas('dormitories', function ($dq) use ($user) {
                    $dq->where('assigned_to', $user->id)
                       ->orWhereHas('assignees', fn($aq) => $aq->where('users.id', $user->id));
                });
            }
        }
        if ($this->statusFilter === 'active') {
            $baseCountQuery->whereIn('status', ['active', 'collecting', 'review']);
        } elseif ($this->statusFilter === 'draft') {
            $baseCountQuery->where('status', 'draft');
        } else {
            $baseCountQuery->whereIn('status', ['closed', 'cancelled']);
        }

        $typeCounts = [
            'all'       => (clone $baseCountQuery)->count(),
            'mutasi'    => (clone $baseCountQuery)->where('census_type', 'mutasi')->count(),
            'fasilitas' => (clone $baseCountQuery)->where('census_type', 'fasilitas')->count(),
            'formal'    => (clone $baseCountQuery)->where(function ($q) {
                $q->where('census_type', 'formal')->orWhereNull('census_type');
            })->count(),
        ];

        // ---------------------------------------------------------------------
        // EXECUTIVE KPI METRICS (KAMAR, FASILITAS, DAN KELENGKAPAN SANTRI)
        // ---------------------------------------------------------------------
        // 1. Hunian Kamar & Kasur
        $dormitoriesQuery = Dormitory::where('is_active', true);
        if (!$isCentral && $genderScope) {
            $dormitoriesQuery->where('gender', $genderScope);
        }
        $dormitories = $dormitoriesQuery->with(['rooms' => fn($rq) => $rq->where('is_active', true)->withCount('currentAssignments')])->get();

        $totalCapacity = 0;
        $totalAssignedBeds = 0;
        foreach ($dormitories as $d) {
            foreach ($d->rooms as $r) {
                $totalCapacity += $r->capacity;
                $totalAssignedBeds += ($r->current_assignments_count ?? 0);
            }
        }
        $totalVacantBeds = max(0, $totalCapacity - $totalAssignedBeds);
        $occupancyRate = $totalCapacity > 0 ? round(($totalAssignedBeds / $totalCapacity) * 100) : 0;

        // 2. Fasilitas & Lemari
        $dormIds = $dormitories->pluck('id');
        $totalLockers = Locker::whereHas('room', fn($rq) => $rq->whereIn('dormitory_id', $dormIds))->count();
        $occupiedLockers = Locker::whereHas('room', fn($rq) => $rq->whereIn('dormitory_id', $dormIds))->whereNotNull('assigned_person_id')->count();
        $damagedLockers = Locker::whereHas('room', fn($rq) => $rq->whereIn('dormitory_id', $dormIds))->where('condition', '!=', 'baik')->count();

        // 3. Kelengkapan Biodata Santri
        $activeSantriQuery = Person::whereHas('roles', function($rq) {
            $rq->where('role_type', 'santri')
               ->where('is_active', true)
               ->whereIn('enrollment_status', ['aktif', 'baru', 'mutasi_masuk']);
        });
        if (!$isCentral && $genderScope) {
            $activeSantriQuery->where('gender', $genderScope);
        }
        $totalActiveSantri = (clone $activeSantriQuery)->count();

        $missingNikCount = (clone $activeSantriQuery)->where(function($q) {
            $q->whereNull('nik')->orWhere('nik', '')->orWhere('nik', 'like', '0000%');
        })->count();

        $missingPhoneCount = (clone $activeSantriQuery)->where(function($q) {
            $q->whereNull('phone')->orWhere('phone', '');
        })->whereDoesntHave('santriProfile', function($sq) {
            $sq->whereNotNull('father_phone')->where('father_phone', '!=', '')
               ->orWhere(function($sq2) {
                   $sq2->whereNotNull('mother_phone')->where('mother_phone', '!=', '');
               });
        })->count();

        $missingProfileCount = (clone $activeSantriQuery)->whereDoesntHave('santriProfile')->count();

        $assignedPersonIds = RoomAssignment::where('is_active', true)->pluck('person_id')->toArray();
        $transitMukimCount = (clone $activeSantriQuery)->whereHas('roles', fn($rq) => $rq->where('presence_status', 'mukim'))
            ->whereNotIn('id', $assignedPersonIds)
            ->count();

        $dataCompletenessRate = $totalActiveSantri > 0 
            ? max(0, min(100, round((($totalActiveSantri - max($missingNikCount, $missingPhoneCount)) / $totalActiveSantri) * 100))) 
            : 100;

        $executiveStats = [
            'total_capacity'         => $totalCapacity,
            'total_assigned_beds'    => $totalAssignedBeds,
            'total_vacant_beds'      => $totalVacantBeds,
            'occupancy_rate'         => $occupancyRate,
            'total_lockers'          => $totalLockers,
            'occupied_lockers'       => $occupiedLockers,
            'damaged_lockers'        => $damagedLockers,
            'total_active_santri'    => $totalActiveSantri,
            'missing_nik_count'      => $missingNikCount,
            'missing_phone_count'    => $missingPhoneCount,
            'missing_profile_count'  => $missingProfileCount,
            'transit_mukim_count'    => $transitMukimCount,
            'data_completeness_rate' => $dataCompletenessRate,
        ];

        return view('livewire.kepengasuhan.census-v3-dashboard', [
            'campaigns'       => $query->paginate(10),
            'userGenderScope' => $genderScope,
            'stats'           => $stats,
            'typeCounts'      => $typeCounts,
            'executiveStats'  => $executiveStats,
        ])->layout('layouts.app', ['title' => 'Pusat Kendali Sensus']);
    }
}
