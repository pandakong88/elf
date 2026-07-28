<?php

namespace App\Livewire\Kepengasuhan;

use App\Modules\Core\Models\MasterData;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\WorkflowTemplate;
use App\Modules\Kepengasuhan\Models\Perizinan;
use App\Modules\Kepengasuhan\Services\PerizinanService;
use App\Modules\Shared\Workflow\WorkflowEngine;
use App\Traits\HasGenderScope;
use Livewire\Component;
use App\Livewire\Concerns\SendsToast;
use Livewire\WithPagination;

class PerizinanList extends Component
{
    use SendsToast, WithPagination, HasGenderScope;

    public $activeTab = 'persetujuan'; // 'persetujuan', 'keluar', 'riwayat'
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Create Modal states
    public $showCreateModal = false;
    public $searchSantri = '';
    public $selectedSantriId = null;
    public $selectedSantriName = '';
    public $selectedOrgId = '';
    public $selectedPermissionTypeId = '';
    public $reason = '';
    public $startDate = '';
    public $endDate = '';
    public $selectedWorkflowTemplateId = '';

    // Rejection Modal states
    public $showRejectModal = false;
    public $selectedPerizinanId = null;
    public $rejectReason = '';

    protected $updatesQueryString = ['search', 'activeTab'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingActiveTab()
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

    // Modal Create Handlers
    public function openCreateModal()
    {
        $this->resetCreateForm();
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
        $this->selectedPermissionTypeId = '';
        $this->reason = '';
        $this->startDate = '';
        $this->endDate = '';
        $this->selectedWorkflowTemplateId = '';
    }

    public function selectSantriForLeave($santriId, $santriName)
    {
        $this->selectedSantriId = $santriId;
        $this->selectedSantriName = $santriName;
        $this->searchSantri = '';

        // Auto-select organization berdasarkan organisasi aktif santri
        $santri = Person::find($santriId);
        if ($santri) {
            $activeRole = $santri->activeRoles()->first();
            if ($activeRole) {
                $this->selectedOrgId = $activeRole->organization_id;
            }
        }
    }

    public function submitLeave()
    {
        $this->validate([
            'selectedSantriId' => 'required',
            'selectedOrgId' => 'required',
            'selectedPermissionTypeId' => 'required',
            'reason' => 'required|string|min:5',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
            'selectedWorkflowTemplateId' => 'required',
        ], [
            'selectedSantriId.required' => 'Santri wajib dipilih.',
            'selectedOrgId.required' => 'Organisasi wajib diisi.',
            'selectedPermissionTypeId.required' => 'Jenis izin wajib dipilih.',
            'reason.required' => 'Alasan izin wajib diisi.',
            'reason.min' => 'Alasan minimal 5 karakter.',
            'startDate.required' => 'Tanggal mulai wajib diisi.',
            'endDate.required' => 'Tanggal selesai wajib diisi.',
            'endDate.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'selectedWorkflowTemplateId.required' => 'Workflow Approval wajib dipilih.',
        ]);

        $service = app(PerizinanService::class);
        $initiator = auth()->user()->person; // Aktor yang membuat pengajuan

        if (!$initiator) {
            $this->toastError('Akun Anda tidak memiliki asosiasi data Person untuk menginisiasi workflow.');
            return;
        }

        try {
            $service->initiateLeave([
                'person_id' => $this->selectedSantriId,
                'organization_id' => $this->selectedOrgId,
                'permission_type_id' => $this->selectedPermissionTypeId,
                'reason' => $this->reason,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'workflow_template_id' => $this->selectedWorkflowTemplateId,
            ], $initiator);

            $this->toastSuccess('Pengajuan izin santri berhasil dikirim.');
            $this->closeCreateModal();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    // Workflow Actions
    public function approveLeave($perizinanId)
    {
        $perizinan = Perizinan::findOrFail($perizinanId);
        $actor = auth()->user()->person;

        if (!$actor) {
            $this->toastError('Akun Anda tidak memiliki data Person aktif untuk memproses persetujuan.');
            return;
        }

        try {
            $workflowEngine = app(WorkflowEngine::class);
            $workflowEngine->advance($perizinan->workflowInstance, $actor, 'Persetujuan disetujui melalui sistem.');
            
            // Sync status
            $perizinanService = app(PerizinanService::class);
            $perizinanService->syncWorkflowStatus($perizinan->id);

            $this->toastSuccess('Izin santri disetujui.');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function openRejectModal($perizinanId)
    {
        $this->selectedPerizinanId = $perizinanId;
        $this->rejectReason = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->selectedPerizinanId = null;
        $this->rejectReason = '';
    }

    public function rejectLeave()
    {
        $this->validate([
            'rejectReason' => 'required|string|min:5',
        ], [
            'rejectReason.required' => 'Alasan penolakan wajib diisi.',
            'rejectReason.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $perizinan = Perizinan::findOrFail($this->selectedPerizinanId);
        $actor = auth()->user()->person;

        if (!$actor) {
            $this->toastError('Akun Anda tidak memiliki data Person aktif untuk memproses penolakan.');
            return;
        }

        try {
            $workflowEngine = app(WorkflowEngine::class);
            $workflowEngine->reject($perizinan->workflowInstance, $actor, $this->rejectReason);
            
            // Sync status
            $perizinanService = app(PerizinanService::class);
            $perizinanService->syncWorkflowStatus($perizinan->id);

            $this->toastSuccess('Izin ditolak.');
            $this->closeRejectModal();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    // Checkout/Checkin
    public function checkoutLeave($perizinanId)
    {
        // Guard: pastikan santri sesuai gender scope user
        if ($this->genderScope()) {
            $perizinan = Perizinan::with('person')->find($perizinanId);
            if ($perizinan && $perizinan->person && $perizinan->person->gender !== $this->genderScope()) {
                $this->toastError('Akses ditolak: santri tidak sesuai scope gender Anda.');
                return;
            }
        }

        $service = app(PerizinanService::class);
        try {
            $service->checkout($perizinanId);
            $this->toastSuccess('Santri berhasil checkout (keluar pondok).');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function checkinLeave($perizinanId)
    {
        // Guard: pastikan santri sesuai gender scope user
        if ($this->genderScope()) {
            $perizinan = Perizinan::with('person')->find($perizinanId);
            if ($perizinan && $perizinan->person && $perizinan->person->gender !== $this->genderScope()) {
                $this->toastError('Akses ditolak: santri tidak sesuai scope gender Anda.');
                return;
            }
        }
        $service = app(PerizinanService::class);
        try {
            $service->checkin($perizinanId);
            $this->toastSuccess('Santri berhasil checkin (kembali ke pondok).');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function render()
    {
        $query = Perizinan::query()
            ->with(['person', 'permissionType', 'workflowInstance.logs.actor']);

        // Filter tab-specific
        if ($this->activeTab === 'persetujuan') {
            $query->whereIn('status', ['pending', 'approved']);
        } elseif ($this->activeTab === 'keluar') {
            $query->where('status', 'out');
        } else {
            // riwayat
            $query->whereIn('status', ['returned', 'late', 'rejected', 'cancelled']);
        }

        // Scope to user's assigned organization if they are not super-admin/pengasuh
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('pengasuh')) {
            $orgIds = $user->getOrganizationIds();
            if (!empty($orgIds)) {
                $query->whereIn('organization_id', $orgIds);
            }
        }

        // Gender scope: filter perizinan berdasarkan gender santri
        if ($this->genderScope()) {
            $query->whereHas('person', fn($q) => $q->where('gender', $this->genderScope()));
        }

        // Search query
        if ($this->search) {
            $query->whereHas('person', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        $perizinans = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        // Options for create modal
        $leaveTypes = MasterData::byCategory('jenis_izin')->active()->get();
        $organizations = Organization::active()->get();
        $workflowTemplates = WorkflowTemplate::active()->where('entity_type', 'perizinan')->get();

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

        return view('livewire.kepengasuhan.perizinan-list', compact(
            'perizinans', 
            'leaveTypes', 
            'organizations', 
            'workflowTemplates', 
            'modalSantriList'
        ))->layout('layouts.app');
    }
}
