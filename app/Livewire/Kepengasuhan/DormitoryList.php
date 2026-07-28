<?php

namespace App\Livewire\Kepengasuhan;

use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Kepengasuhan\Services\DormitoryService;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Kepengasuhan\Services\SantriStatusService;
use App\Traits\HasGenderScope;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Livewire\Concerns\SendsToast;
use Livewire\WithPagination;

class DormitoryList extends Component
{
    use SendsToast, WithPagination, HasGenderScope;

    public $search        = '';
    public $genderFilter  = '';
    public bool $isGenderLocked = false;

    // =========================================================================
    // Modal: Assign Santri ke Kamar
    // =========================================================================
    public $showAssignModal  = false;
    public $selectedRoomId   = null;
    public $selectedRoomName = '';
    public $searchSantri     = '';

    // =========================================================================
    // Modal: Tambah / Edit Asrama
    // =========================================================================
    public $showDormitoryModal  = false;
    public $editingDormitoryId  = null;
    public $dormitoryName       = '';
    public $dormitoryGender     = 'L';
    public $dormitoryDesc       = '';

    // =========================================================================
    // Modal: Tambah / Edit Kamar
    // =========================================================================
    public $showRoomModal    = false;
    public $editingRoomId    = null;
    public $targetDormitoryId = null;
    public $roomName         = '';
    public $roomCapacity     = 10;
    public $roomDesc         = '';

    // =========================================================================
    // Modal: Konfirmasi Hapus / Unassign Santri
    // =========================================================================
    public $showConfirmModal   = false;
    public $confirmTitle       = '';
    public $confirmMessage     = '';
    public $confirmAction      = ''; // nama action yang akan dipanggil saat konfirmasi
    public $confirmPayload     = null;

    // Waiting list panel
    public $searchWaiting = '';

    // =========================================================================
    // Modal: Status Santri
    // =========================================================================
    public $showStatusModal = false;
    public $selectedSantriId = null;
    public $selectedRoleId = null;
    public $santriName = '';
    public $currentEnrollmentStatus = 'aktif';
    public $currentPresenceStatus = 'mukim';
    public $statusNotes = '';
    public $presenceUntil = null;
    public $statusHistory = [];

    protected $updatesQueryString = ['search', 'genderFilter'];

    // =========================================================================
    // Lifecycle
    // =========================================================================

    public function mount(): void
    {
        $scope = $this->genderScope();
        if ($scope) {
            // User memiliki gender scope — kunci filter ke gender mereka
            $this->genderFilter   = $scope;
            $this->isGenderLocked = true;
        }
        // super-admin / manajemen: genderFilter tetap '' (bebas pilih semua)
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingGenderFilter()
    {
        $this->resetPage();
    }

    // =========================================================================
    // CRUD Asrama
    // =========================================================================

    public function openCreateDormitoryModal(): void
    {
        $this->reset(['editingDormitoryId', 'dormitoryName', 'dormitoryGender', 'dormitoryDesc']);
        $this->dormitoryGender  = 'L';
        $this->showDormitoryModal = true;
    }

    public function openEditDormitoryModal(string $id): void
    {
        $dormitory = Dormitory::findOrFail($id);
        $this->editingDormitoryId = $id;
        $this->dormitoryName      = $dormitory->name;
        $this->dormitoryGender    = $dormitory->gender;
        $this->dormitoryDesc      = $dormitory->description ?? '';
        $this->showDormitoryModal = true;
    }

    public function saveDormitory(): void
    {
        // Guard: user ber-gender scope tidak boleh menyimpan asrama lawan jenis
        if ($this->genderScope() && $this->dormitoryGender !== $this->genderScope()) {
            $this->toastError('Anda hanya dapat mengelola asrama sesuai gender yang diizinkan untuk akun Anda.');
            return;
        }

        $this->validate([
            'dormitoryName'   => 'required|string|max:100',
            'dormitoryGender' => 'required|in:L,P',
            'dormitoryDesc'   => 'nullable|string|max:500',
        ]);

        $service = app(DormitoryService::class);

        if ($this->editingDormitoryId) {
            $service->updateDormitory($this->editingDormitoryId, [
                'name'        => $this->dormitoryName,
                'gender'      => $this->dormitoryGender,
                'description' => $this->dormitoryDesc ?: null,
            ]);
            $this->toastSuccess('Data asrama berhasil diperbarui.');
        } else {
            $service->createDormitory([
                'name'        => $this->dormitoryName,
                'gender'      => $this->dormitoryGender,
                'description' => $this->dormitoryDesc ?: null,
                'is_active'   => true,
            ]);
            $this->toastSuccess('Asrama baru berhasil ditambahkan.');
        }

        $this->showDormitoryModal = false;
    }

    public function confirmToggleDormitoryStatus(string $id): void
    {
        $dormitory           = Dormitory::findOrFail($id);
        $action              = $dormitory->is_active ? 'Nonaktifkan' : 'Aktifkan';
        $this->confirmTitle  = "{$action} Asrama";
        $this->confirmMessage = "Apakah Anda yakin ingin {$action} asrama <strong>{$dormitory->name}</strong>?";
        $this->confirmAction  = 'toggleDormitoryStatus';
        $this->confirmPayload = $id;
        $this->showConfirmModal = true;
    }

    public function toggleDormitoryStatus(): void
    {
        app(DormitoryService::class)->toggleDormitoryStatus($this->confirmPayload);
        $this->toastSuccess('Status asrama berhasil diubah.');
        $this->closeConfirmModal();
    }

    // =========================================================================
    // CRUD Kamar
    // =========================================================================

    public function openCreateRoomModal(string $dormitoryId): void
    {
        $this->reset(['editingRoomId', 'roomName', 'roomCapacity', 'roomDesc']);
        $this->targetDormitoryId = $dormitoryId;
        $this->roomCapacity      = 10;
        $this->showRoomModal     = true;
    }

    public function openEditRoomModal(string $id): void
    {
        $room                    = Room::findOrFail($id);
        $this->editingRoomId     = $id;
        $this->targetDormitoryId = $room->dormitory_id;
        $this->roomName          = $room->name;
        $this->roomCapacity      = $room->capacity;
        $this->roomDesc          = $room->description ?? '';
        $this->showRoomModal     = true;
    }

    public function saveRoom(): void
    {
        $this->validate([
            'roomName'     => 'required|string|max:100',
            'roomCapacity' => 'required|integer|min:1|max:100',
            'roomDesc'     => 'nullable|string|max:500',
        ]);

        $service = app(DormitoryService::class);

        try {
            if ($this->editingRoomId) {
                $service->updateRoom($this->editingRoomId, [
                    'name'        => $this->roomName,
                    'capacity'    => (int) $this->roomCapacity,
                    'description' => $this->roomDesc ?: null,
                ]);
                $this->toastSuccess('Data kamar berhasil diperbarui.');
            } else {
                $service->createRoom($this->targetDormitoryId, [
                    'name'        => $this->roomName,
                    'capacity'    => (int) $this->roomCapacity,
                    'description' => $this->roomDesc ?: null,
                    'is_active'   => true,
                ]);
                $this->toastSuccess('Kamar baru berhasil ditambahkan.');
            }
            $this->showRoomModal = false;
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    // =========================================================================
    // Assign / Unassign Santri
    // =========================================================================

    public function openAssignModal($roomId): void
    {
        $room                  = Room::findOrFail($roomId);
        $this->selectedRoomId  = $roomId;
        $this->selectedRoomName = $room->dormitory->name . ' - ' . $room->name;
        $this->searchSantri    = '';
        $this->showAssignModal = true;
    }

    public function closeAssignModal(): void
    {
        $this->showAssignModal = false;
        $this->selectedRoomId  = null;
        $this->searchSantri    = '';
    }

    public function assignSantri(string $santriId): void
    {
        if (!$this->selectedRoomId) {
            return;
        }

        $service = app(DormitoryService::class);
        try {
            $service->assignRoom($this->selectedRoomId, $santriId, now()->toDateString());
            $this->toastSuccess('Santri berhasil ditempatkan di kamar.');
            $this->closeAssignModal();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    /**
     * Tempatkan santri dari waiting list langsung ke kamar yang dipilih.
     */
    public function assignFromWaitingList(string $santriId, string $roomId): void
    {
        $service = app(DormitoryService::class);
        try {
            $service->assignRoom($roomId, $santriId, now()->toDateString());
            $this->toastSuccess('Santri berhasil ditempatkan di kamar.');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function confirmUnassignSantri(string $assignmentId): void
    {
        $assignment = RoomAssignment::with(['person', 'room'])->findOrFail($assignmentId);
        $this->confirmTitle   = 'Lepaskan Santri dari Kamar';
        $this->confirmMessage = "Apakah Anda yakin ingin mengeluarkan <strong>{$assignment->person->name}</strong> dari kamar <strong>{$assignment->room->name}</strong>? Santri akan masuk ke daftar waiting list.";
        $this->confirmAction  = 'unassignSantri';
        $this->confirmPayload = $assignmentId;
        $this->showConfirmModal = true;
    }

    public function unassignSantri(): void
    {
        try {
            app(DormitoryService::class)->unassignRoom($this->confirmPayload);
            $this->toastSuccess('Santri berhasil dilepaskan dari kamar.');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
        $this->closeConfirmModal();
    }

    // =========================================================================
    // Status Santri Management
    // =========================================================================

    public function openStatusModal(string $personId): void
    {
        $person = Person::with(['roles' => function ($q) {
            $q->where('role_type', 'santri')->where('is_active', true);
        }])->findOrFail($personId);

        $role = $person->roles->first();
        if (!$role) {
            $this->toastError('Santri tidak memiliki role aktif.');
            return;
        }

        $this->selectedSantriId = $personId;
        $this->selectedRoleId = $role->id;
        $this->santriName = $person->name;
        $this->currentEnrollmentStatus = $role->enrollment_status ?? 'aktif';
        $this->currentPresenceStatus = $role->presence_status ?? 'mukim';
        $this->presenceUntil = $role->presence_status_until ? $role->presence_status_until->toDateString() : null;
        $this->statusNotes = '';

        $statusService = app(SantriStatusService::class);
        $this->statusHistory = $statusService->getStatusHistory($personId)->toArray();
        $this->showStatusModal = true;
    }

    public function closeStatusModal(): void
    {
        $this->showStatusModal = false;
        $this->reset(['selectedSantriId', 'selectedRoleId', 'santriName', 'currentEnrollmentStatus', 'currentPresenceStatus', 'statusNotes', 'presenceUntil', 'statusHistory']);
    }

    public function updateStatus(): void
    {
        $role = PersonRole::findOrFail($this->selectedRoleId);
        $statusService = app(SantriStatusService::class);
        $userId = auth()->id();
        $hasChanges = false;

        // 1. Enrollment status change
        if ($this->currentEnrollmentStatus !== $role->enrollment_status) {
            if (!auth()->user()->can('change-enrollment-status')) {
                $this->toastError('Anda tidak memiliki hak akses untuk mengubah status keanggotaan.');
                return;
            }

            try {
                $statusService->changeEnrollmentStatus(
                    $this->selectedRoleId,
                    $this->currentEnrollmentStatus,
                    $userId,
                    $this->statusNotes
                );
                $hasChanges = true;
            } catch (\Exception $e) {
                $this->toastError($e->getMessage());
                return;
            }
        }

        // 2. Presence status change (only relevant if active)
        if ($this->currentEnrollmentStatus === 'aktif' && $this->currentPresenceStatus !== $role->presence_status) {
            if (!auth()->user()->can('change-presence-status')) {
                $this->toastError('Anda tidak memiliki hak akses untuk mengubah status keberadaan.');
                return;
            }

            try {
                $statusService->changePresenceStatus(
                    $this->selectedRoleId,
                    $this->currentPresenceStatus,
                    $userId,
                    $this->presenceUntil ?: null,
                    $this->statusNotes
                );
                $hasChanges = true;
            } catch (\Exception $e) {
                $this->toastError($e->getMessage());
                return;
            }
        }

        if ($hasChanges) {
            $this->toastSuccess('Status santri berhasil diperbarui.');
            $this->closeStatusModal();
        } else {
            $this->toastInfo('Tidak ada perubahan status.');
        }
    }

    // =========================================================================
    // Confirm Modal
    // =========================================================================

    public function closeConfirmModal(): void
    {
        $this->showConfirmModal  = false;
        $this->confirmAction     = '';
        $this->confirmPayload    = null;
        $this->confirmTitle      = '';
        $this->confirmMessage    = '';
    }

    public function executeConfirmAction(): void
    {
        if ($this->confirmAction && method_exists($this, $this->confirmAction)) {
            $this->{$this->confirmAction}();
        }
    }

    // =========================================================================
    // Render
    // =========================================================================

    public function render()
    {
        $query = Dormitory::query()->with(['rooms.currentAssignments.person']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('rooms', function ($rq) {
                      $rq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Gender scope: super-admin/manajemen boleh filter manual, role lain di-enforce server-side
        $enforced = $this->genderScope();
        if ($enforced) {
            // Selalu enforce berdasarkan scope user, abaikan URL input
            $query->where('gender', $enforced);
        } elseif ($this->genderFilter) {
            // super-admin / manajemen: boleh pakai filter manual
            $query->where('gender', $this->genderFilter);
        }

        // Scope untuk musyrif (hanya asrama milik organisasinya)
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('pengasuh') && !$user->hasRole('manajemen')) {
            $orgIds = $user->getOrganizationIds();
            if (!empty($orgIds)) {
                $query->whereIn('organization_id', $orgIds);
            }
        }

        $dormitories = $query->paginate(6);

        // Santri untuk modal assign
        $santriList = [];
        if ($this->showAssignModal && $this->selectedRoomId) {
            $room   = Room::find($this->selectedRoomId);
            $gender = $room->dormitory->gender;

            $santriQuery = Person::query()
                ->byRole('santri')
                ->where('gender', $gender);

            if ($this->searchSantri) {
                $santriQuery->where('name', 'like', '%' . $this->searchSantri . '%');
            }

            $currentOccupantIds = $room->currentAssignments()->pluck('person_id')->toArray();
            $santriQuery->whereNotIn('id', $currentOccupantIds);

            $santriList = $santriQuery->limit(8)->get();
        }

        // Waiting list (santri tanpa kamar) — gunakan scope gender yg di-enforce
        $waitingListGender = $this->genderScope() ?? ($this->genderFilter ?: null);
        $waitingList = [];
        if ($waitingListGender) {
            $service = app(DormitoryService::class);
            $waitingList = $service->getSantriWithoutRoom($waitingListGender, $this->searchWaiting ?: null);
        }

        return view('livewire.kepengasuhan.dormitory-list', compact('dormitories', 'santriList', 'waitingList'))
            ->layout('layouts.app');
    }
}
