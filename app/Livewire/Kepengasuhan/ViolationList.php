<?php

namespace App\Livewire\Kepengasuhan;

use App\Modules\Core\Models\MasterData;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\Violation;
use App\Modules\Kepengasuhan\Services\ViolationService;
use App\Traits\HasGenderScope;
use Livewire\Component;
use App\Livewire\Concerns\SendsToast;
use Livewire\WithPagination;

class ViolationList extends Component
{
    use SendsToast, WithPagination, HasGenderScope;

    public $search = '';
    public $severityFilter = '';
    public $sortField = 'violation_date';
    public $sortDirection = 'desc';

    // Report Modal State
    public $showCreateModal = false;
    public $searchSantri = '';
    public $selectedSantriId = null;
    public $selectedSantriName = '';
    public $selectedOrgId = '';
    public $selectedViolationTypeId = '';
    public $violationDate = '';
    public $description = '';
    public $severity = 'ringan';
    public $points = 0;
    public $punishment = '';

    // Resolve Modal State
    public $showResolveModal = false;
    public $selectedViolationId = null;
    public $punishmentApplied = '';

    protected $updatesQueryString = ['search', 'severityFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSeverityFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }

    // Modal Create Action Handlers
    public function openCreateModal()
    {
        $this->resetCreateForm();
        $this->violationDate = now()->format('Y-m-d\TH:i');
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    private function resetCreateForm()
    {
        $this->searchSantri = '';
        $this->selectedSantriId = null;
        $this->selectedSantriName = '';
        $this->selectedOrgId = '';
        $this->selectedViolationTypeId = '';
        $this->violationDate = '';
        $this->description = '';
        $this->severity = 'ringan';
        $this->points = 0;
        $this->punishment = '';
    }

    public function selectSantriForViolation($santriId, $santriName)
    {
        $this->selectedSantriId = $santriId;
        $this->selectedSantriName = $santriName;
        $this->searchSantri = '';

        // Auto-select organization
        $santri = Person::find($santriId);
        if ($santri) {
            $activeRole = $santri->activeRoles()->first();
            if ($activeRole) {
                $this->selectedOrgId = $activeRole->organization_id;
            }
        }
    }

    public function updatedSelectedViolationTypeId($value)
    {
        // Auto fill points from violation type metadata
        if ($value) {
            $type = MasterData::find($value);
            if ($type && isset($type->metadata['points'])) {
                $this->points = (int) $type->metadata['points'];
            }
        }
    }

    public function submitViolation()
    {
        $this->validate([
            'selectedSantriId' => 'required',
            'selectedOrgId' => 'required',
            'selectedViolationTypeId' => 'required',
            'violationDate' => 'required|date',
            'description' => 'required|string|min:5',
            'severity' => 'required|in:ringan,sedang,berat',
            'points' => 'required|integer|min:0',
        ], [
            'selectedSantriId.required' => 'Santri wajib dipilih.',
            'selectedOrgId.required' => 'Organisasi wajib diisi.',
            'selectedViolationTypeId.required' => 'Jenis pelanggaran wajib dipilih.',
            'description.required' => 'Keterangan kejadian wajib diisi.',
            'description.min' => 'Keterangan kejadian minimal 5 karakter.',
            'points.required' => 'Poin pelanggaran wajib diisi.',
        ]);

        $reporter = auth()->user()->person;
        if (!$reporter) {
            $this->toastError('Akun Anda tidak memiliki asosiasi data Person untuk melaporkan pelanggaran.');
            return;
        }

        $service = app(ViolationService::class);
        try {
            $service->reportViolation([
                'person_id' => $this->selectedSantriId,
                'organization_id' => $this->selectedOrgId,
                'violation_type_id' => $this->selectedViolationTypeId,
                'reporter_id' => $reporter->id,
                'violation_date' => $this->violationDate,
                'description' => $this->description,
                'severity' => $this->severity,
                'points' => $this->points,
                'punishment' => $this->punishment,
            ]);

            $this->toastSuccess('Pelanggaran santri berhasil dicatat.');
            $this->closeCreateModal();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    // Modal Resolve Action Handlers
    public function openResolveModal($violationId)
    {
        $this->selectedViolationId = $violationId;
        $this->punishmentApplied = '';
        $this->showResolveModal = true;
    }

    public function closeResolveModal()
    {
        $this->showResolveModal = false;
        $this->selectedViolationId = null;
        $this->punishmentApplied = '';
    }

    public function submitResolve()
    {
        $this->validate([
            'punishmentApplied' => 'required|string|min:3',
        ], [
            'punishmentApplied.required' => 'Tindakan disiplin / sanksi yang diterapkan wajib diisi.',
            'punishmentApplied.min' => 'Tindakan disiplin minimal 3 karakter.',
        ]);

        $service = app(ViolationService::class);
        try {
            $service->resolveViolation($this->selectedViolationId, $this->punishmentApplied);
            $this->toastSuccess('Pelanggaran berhasil diselesaikan (Resolved).');
            $this->closeResolveModal();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function render()
    {
        $query = Violation::query()
            ->with(['person', 'violationType', 'reporter']);

        // Scope to user's assigned organization if they are not super-admin/pengasuh
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('pengasuh')) {
            $orgIds = $user->getOrganizationIds();
            if (!empty($orgIds)) {
                $query->whereIn('organization_id', $orgIds);
            }
        }

        // Gender scope: filter violations berdasarkan gender santri
        if ($this->genderScope()) {
            $query->whereHas('person', fn($q) => $q->where('gender', $this->genderScope()));
        }

        // Search query
        if ($this->search) {
            $query->whereHas('person', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        // Severity filter
        if ($this->severityFilter) {
            $query->where('severity', $this->severityFilter);
        }

        $violations = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        // Fetch violation types from MasterData
        $violationTypes = MasterData::byCategory('jenis_pelanggaran')->active()->get();
        $organizations = Organization::active()->get();

        // Modal santri search
        $modalSantriList = [];
        if ($this->showCreateModal && $this->searchSantri) {
            $santriQuery = Person::query()
                ->byRole('santri')
                ->where('name', 'like', '%' . $this->searchSantri . '%');

            // Limit to user's assigned organization if they are not super-admin/pengasuh
            if (!$user->hasRole('super-admin') && !$user->hasRole('pengasuh')) {
                $orgIds = $user->getOrganizationIds();
                if (!empty($orgIds)) {
                    $santriQuery->whereHas('roles', function($q) use ($orgIds) {
                        $q->whereIn('organization_id', $orgIds)->where('role_type', 'santri')->where('is_active', true);
                    });
                }
            }

            // Gender scope: santri modal hanya muncul sesuai gender user
            if ($this->genderScope()) {
                $santriQuery->where('gender', $this->genderScope());
            }

            $modalSantriList = $santriQuery->limit(5)->get();
        }

        // Top 5 highest accumulated points santri (unresolved)
        $topViolatorsQuery = Person::query()
            ->byRole('santri')
            ->whereHas('roles', function($q) use ($user) {
                if (!$user->hasRole('super-admin') && !$user->hasRole('pengasuh')) {
                    $orgIds = $user->getOrganizationIds();
                    if (!empty($orgIds)) {
                        $q->whereIn('organization_id', $orgIds);
                    }
                }
            });

        // Gender scope: top violators hanya dari gender yang sesuai
        if ($this->genderScope()) {
            $topViolatorsQuery->where('gender', $this->genderScope());
        }

        $topViolators = $topViolatorsQuery
            ->withSum(['roles as unresolved_points' => function($q) {
                // Sum from violations table
            }], 'points') // We can query database directly or do a select raw
            ->select('persons.*')
            ->selectSub(function($q) {
                $q->selectRaw('coalesce(sum(points), 0)')
                  ->from('violations')
                  ->whereColumn('violations.person_id', 'persons.id')
                  ->where('violations.status', '!=', 'resolved');
            }, 'unresolved_points')
            ->orderBy('unresolved_points', 'desc')
            ->limit(5)
            ->get();

        return view('livewire.kepengasuhan.violation-list', compact(
            'violations',
            'violationTypes',
            'organizations',
            'modalSantriList',
            'topViolators'
        ))->layout('layouts.app');
    }
}
