<?php

namespace App\Livewire\Kepengasuhan;

use App\Modules\Core\Models\MasterData;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\Activity;
use App\Modules\Kepengasuhan\Models\ActivityAttendance;
use App\Modules\Kepengasuhan\Services\ActivityService;
use App\Traits\HasGenderScope;
use Livewire\Component;
use App\Livewire\Concerns\SendsToast;

class ActivityAttendanceSheet extends Component
{
    use SendsToast, HasGenderScope;

    public $selectedActivityId = '';
    public $searchSantri = '';

    // Create Activity Modal States
    public $showCreateModal = false;
    public $name = '';
    public $date = '';
    public $selectedOrgId = '';
    public $selectedActivityTypeId = '';
    public $description = '';

    // List of attendances locally cached as: person_id => status
    public $attendanceStatuses = [];
    public $attendanceNotes = [];

    public function mount()
    {
        $this->date = now()->toDateString();
    }

    public function updatedSelectedActivityId($activityId)
    {
        $this->loadAttendanceData($activityId);
    }

    public function loadAttendanceData($activityId)
    {
        if (!$activityId) {
            $this->attendanceStatuses = [];
            $this->attendanceNotes = [];
            return;
        }

        $attendances = ActivityAttendance::where('activity_id', $activityId)->get();
        
        $this->attendanceStatuses = $attendances->pluck('status', 'person_id')->toArray();
        $this->attendanceNotes = $attendances->pluck('notes', 'person_id')->toArray();
    }

    public function setAttendanceStatus($personId, $status)
    {
        if (!$this->selectedActivityId) {
            return;
        }

        $service = app(ActivityService::class);
        try {
            $note = $this->attendanceNotes[$personId] ?? null;
            $service->recordAttendanceBatch($this->selectedActivityId, [
                [
                    'person_id' => $personId,
                    'status' => $status,
                    'notes' => $note
                ]
            ]);

            $this->attendanceStatuses[$personId] = $status;
            session()->flash('status_saved_' . $personId, true);
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function saveNote($personId)
    {
        if (!$this->selectedActivityId) {
            return;
        }

        $status = $this->attendanceStatuses[$personId] ?? 'hadir';
        $note = $this->attendanceNotes[$personId] ?? null;

        $service = app(ActivityService::class);
        try {
            $service->recordAttendanceBatch($this->selectedActivityId, [
                [
                    'person_id' => $personId,
                    'status' => $status,
                    'notes' => $note
                ]
            ]);
            session()->flash('note_saved_' . $personId, true);
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    // Modal Action Handlers
    public function openCreateModal()
    {
        $this->resetCreateForm();
        $this->date = now()->toDateString();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    private function resetCreateForm()
    {
        $this->name = '';
        $this->date = '';
        $this->selectedOrgId = '';
        $this->selectedActivityTypeId = '';
        $this->description = '';
    }

    public function submitActivity()
    {
        $this->validate([
            'name' => 'required|string|min:3',
            'date' => 'required|date',
            'selectedOrgId' => 'required',
            'selectedActivityTypeId' => 'required',
        ], [
            'name.required' => 'Nama kegiatan wajib diisi.',
            'name.min' => 'Nama kegiatan minimal 3 karakter.',
            'selectedOrgId.required' => 'Organisasi wajib diisi.',
            'selectedActivityTypeId.required' => 'Jenis kegiatan wajib dipilih.',
        ]);

        $service = app(ActivityService::class);
        try {
            $activity = $service->createActivity([
                'name' => $this->name,
                'date' => $this->date,
                'organization_id' => $this->selectedOrgId,
                'activity_type_id' => $this->selectedActivityTypeId,
                'description' => $this->description,
            ]);

            $this->toastSuccess('Kegiatan baru berhasil dibuat.');
            $this->selectedActivityId = $activity->id;
            $this->loadAttendanceData($activity->id);
            $this->closeCreateModal();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function render()
    {
        $user = auth()->user();
        
        // Fetch activities
        $activitiesQuery = Activity::query()->with(['activityType', 'organization']);

        // Scope to user's assigned organization if they are not super-admin/pengasuh
        if (!$user->hasRole('super-admin') && !$user->hasRole('pengasuh')) {
            $orgIds = $user->getOrganizationIds();
            if (!empty($orgIds)) {
                $activitiesQuery->whereIn('organization_id', $orgIds);
            }
        }

        $activities = $activitiesQuery->orderBy('date', 'desc')->orderBy('name')->get();

        // Load santri for the selected activity
        $santriList = [];
        $selectedActivity = null;
        if ($this->selectedActivityId) {
            $selectedActivity = Activity::find($this->selectedActivityId);
            if ($selectedActivity) {
                $santriQuery = Person::query()
                    ->byRole('santri', $selectedActivity->organization_id)
                    ->when($this->genderScope(), fn($q, $g) => $q->where('gender', $g));

                if ($this->searchSantri) {
                    $santriQuery->where('name', 'like', '%' . $this->searchSantri . '%');
                }

                $santriList = $santriQuery->orderBy('name')->get();
            }
        }

        // Options for create activity form
        // Gender scope: filter organisasi yang relevan dengan gender user
        $organizations = Organization::active()
            ->when($this->genderScope(), function ($q, $g) {
                $q->whereHas('dormitories', fn($dq) => $dq->where('gender', $g)->where('is_active', true));
            })
            ->get();
        $activityTypes = MasterData::byCategory('jenis_kegiatan')->active()->get();

        return view('livewire.kepengasuhan.activity-attendance-sheet', compact(
            'activities',
            'santriList',
            'selectedActivity',
            'organizations',
            'activityTypes'
        ))->layout('layouts.app');
    }
}
