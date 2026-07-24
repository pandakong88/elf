<?php

namespace App\Livewire\Kepengasuhan;

use App\Livewire\Concerns\SendsToast;
use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Kepengasuhan\Services\DormitoryService;
use App\Modules\Madrasah\Models\MadrasahEnrollment;
use App\Modules\Madrasah\Models\MadrasahKelas;
use App\Traits\HasGenderScope;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class PetaSantriManager extends Component
{
    use SendsToast, WithPagination, HasGenderScope;

    // Navigation & View Mode
    public string $activeTab = 'komplek'; // 'komplek' | 'kelas'
    public string $viewMode  = 'table';   // 'table' (Default) | 'card' (Secondary View)

    // Filter Controls
    public string $search          = '';
    public string $genderFilter    = '';
    public bool   $isGenderLocked  = false;
    public string $dormitoryFilter = '';
    public string $kelasFilter     = '';
    public string $presenceFilter  = ''; // 'mukim', 'izin', 'pulang', 'laju'
    public string $enrollmentFilter= 'aktif'; // 'aktif' (Default), 'boyong', 'alumni', '' (semua)

    // Modal: Quick Profile View
    public bool    $showQuickProfileModal = false;
    public ?string $selectedSantriId      = null;
    public ?Person $selectedSantri        = null;

    // Modal: Quick Transfer Kamar
    public bool    $showTransferRoomModal = false;
    public ?string $transferSantriId      = null;
    public string  $transferSantriName    = '';
    public ?string $targetRoomId          = null;

    // Modal: Quick Transfer Kelas
    public bool    $showTransferKelasModal = false;
    public ?string $transferKelasSantriId  = null;
    public string  $transferKelasSantriName= '';
    public ?string $targetKelasId          = null;
    public string  $academicYear           = '';

    protected $queryString = [
        'activeTab'        => ['except' => 'komplek'],
        'viewMode'         => ['except' => 'table'],
        'search'           => ['except' => ''],
        'genderFilter'     => ['except' => ''],
        'presenceFilter'   => ['except' => ''],
        'enrollmentFilter' => ['except' => 'aktif'],
    ];

    public function mount(): void
    {
        $currentYear = (int) now()->format('Y');
        $this->academicYear = $currentYear . '/' . ($currentYear + 1);

        // Apply Automatic Gender Scoping via HasGenderScope trait
        $scope = $this->genderScope();
        if ($scope) {
            $this->genderFilter   = $scope;
            $this->isGenderLocked = true;
        }
    }

    // Lifecycle hooks to reset pagination on any filter update
    public function updatingSearch(): void           { $this->resetPage(); }
    public function updatedSearch(): void            { $this->resetPage(); }

    public function updatingActiveTab(): void        { $this->resetPage(); }
    public function updatedActiveTab(): void         { $this->resetPage(); }

    public function updatingViewMode(): void         { $this->resetPage(); }
    public function updatedViewMode(): void          { $this->resetPage(); }

    public function updatingGenderFilter(): void       { $this->resetPage(); }
    public function updatedGenderFilter(): void        { $this->resetPage(); }

    public function updatingPresenceFilter(): void     { $this->resetPage(); }
    public function updatedPresenceFilter(): void      { $this->resetPage(); }

    public function updatingDormitoryFilter(): void    { $this->resetPage(); }
    public function updatedDormitoryFilter(): void     { $this->resetPage(); }

    public function updatingKelasFilter(): void        { $this->resetPage(); }
    public function updatedKelasFilter(): void         { $this->resetPage(); }

    public function updatingEnrollmentFilter(): void   { $this->resetPage(); }
    public function updatedEnrollmentFilter(): void    { $this->resetPage(); }

    // =========================================================================
    // Quick Profile Actions
    // =========================================================================

    public function openQuickProfile(string $santriId): void
    {
        $this->selectedSantriId = $santriId;
        $this->selectedSantri   = Person::with([
            'santriProfile',
            'roles',
            'activeRoles',
            'roomAssignments' => fn($q) => $q->orderBy('is_active', 'desc')->with('room.dormitory'),
            'madrasahEnrollments' => fn($q) => $q->orderBy('is_active', 'desc')->with('kelas.waliKelas'),
            'bills' => fn($q) => $q->where('status', 'unpaid')->limit(10),
        ])->find($santriId);

        if ($this->selectedSantri) {
            $this->showQuickProfileModal = true;
        } else {
            $this->toastError('Data santri tidak ditemukan.');
        }
    }

    public function closeQuickProfile(): void
    {
        $this->showQuickProfileModal = false;
        $this->selectedSantriId      = null;
        $this->selectedSantri        = null;
    }

    // =========================================================================
    // Transfer Kamar Actions
    // =========================================================================

    // Custom Confirmation Dialog Properties
    public bool   $showConfirmModal     = false;
    public string $confirmTitle         = '';
    public string $confirmMessage       = '';
    public string $confirmAction        = '';
    public string $confirmButtonText    = 'Ya, Lanjutkan';
    public string $confirmButtonColor   = 'emerald';

    public function requestTransferRoomConfirm(): void
    {
        $this->validate(['targetRoomId' => 'required|uuid|exists:rooms,id']);
        $this->confirmAction      = 'executeTransferRoom';
        $this->confirmTitle       = 'Konfirmasi Pemindahan Kamar';
        $this->confirmMessage     = "Apakah Anda YAKIN ingin memindahkan santri {$this->transferSantriName} ke kamar tujuan baru?";
        $this->confirmButtonText  = 'Ya, Pindahkan Santri';
        $this->confirmButtonColor = 'emerald';
        $this->showConfirmModal   = true;
    }

    public function requestTransferKelasConfirm(): void
    {
        $this->validate(['targetKelasId' => 'required|uuid|exists:madrasah_kelas,id']);
        $this->confirmAction      = 'executeTransferKelas';
        $this->confirmTitle       = 'Konfirmasi Pemindahan Kelas';
        $this->confirmMessage     = "Apakah Anda YAKIN ingin memindahkan santri {$this->transferKelasSantriName} ke kelas madrasah baru?";
        $this->confirmButtonText  = 'Ya, Pindahkan Santri';
        $this->confirmButtonColor = 'indigo';
        $this->showConfirmModal   = true;
    }

    public function processConfirmedAction(): void
    {
        $this->showConfirmModal = false;

        if ($this->confirmAction === 'executeTransferRoom') {
            $this->executeTransferRoom();
        } elseif ($this->confirmAction === 'executeTransferKelas') {
            $this->executeTransferKelas();
        }
    }

    public function openTransferRoomModal(string $santriId): void
    {
        $person = Person::findOrFail($santriId);
        $this->transferSantriId   = $santriId;
        $this->transferSantriName = $person->name;
        $this->targetRoomId       = null;
        $this->showTransferRoomModal = true;
    }

    public function closeTransferRoomModal(): void
    {
        $this->showTransferRoomModal = false;
        $this->transferSantriId      = null;
        $this->transferSantriName    = '';
        $this->targetRoomId          = null;
    }

    public function executeTransferRoom(): void
    {
        $this->validate([
            'targetRoomId' => 'required|uuid|exists:rooms,id',
        ]);

        try {
            app(DormitoryService::class)->assignRoom(
                $this->targetRoomId,
                $this->transferSantriId,
                now()->toDateString()
            );
            $this->toastSuccess("Berhasil memindahkan {$this->transferSantriName} ke kamar baru.");
            $this->closeTransferRoomModal();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    // =========================================================================
    // Transfer Kelas Actions
    // =========================================================================

    public function openTransferKelasModal(string $santriId): void
    {
        $person = Person::findOrFail($santriId);
        $this->transferKelasSantriId   = $santriId;
        $this->transferKelasSantriName = $person->name;
        $this->targetKelasId           = null;
        $this->showTransferKelasModal  = true;
    }

    public function closeTransferKelasModal(): void
    {
        $this->showTransferKelasModal  = false;
        $this->transferKelasSantriId   = null;
        $this->transferKelasSantriName = '';
        $this->targetKelasId           = null;
    }

    public function executeTransferKelas(): void
    {
        $this->validate([
            'targetKelasId' => 'required|uuid|exists:madrasah_kelas,id',
        ]);

        try {
            $targetKelas  = MadrasahKelas::findOrFail($this->targetKelasId);
            $academicYear = $targetKelas->academic_year ?: $this->academicYear;

            // Nonaktifkan enrollment lama
            MadrasahEnrollment::where('person_id', $this->transferKelasSantriId)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // Enrol ke kelas baru
            MadrasahEnrollment::updateOrCreate(
                [
                    'person_id'     => $this->transferKelasSantriId,
                    'kelas_id'      => $this->targetKelasId,
                    'academic_year' => $academicYear,
                ],
                [
                    'is_active'  => true,
                    'created_by' => auth()->id(),
                ]
            );

            $this->toastSuccess("Berhasil mendaftarkan {$this->transferKelasSantriName} ke kelas baru.");
            $this->closeTransferKelasModal();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    // =========================================================================
    // Render Method
    // =========================================================================

    public function render()
    {
        $user = auth()->user();

        // 1. Base Query Santri dengan Eager Loading & Combined Roles Filter
        $santriQuery = Person::query()
            ->whereHas('roles', function (Builder $rq) {
                $rq->where('role_type', 'santri');

                // Filter Status Keanggotaan (Enrollment Status)
                if ($this->enrollmentFilter === 'aktif') {
                    $rq->where('enrollment_status', 'aktif');
                } elseif ($this->enrollmentFilter === 'boyong') {
                    $rq->whereIn('enrollment_status', ['boyong', 'keluar_resmi', 'dikeluarkan', 'tanpa_keterangan']);
                } elseif ($this->enrollmentFilter === 'alumni') {
                    $rq->where('enrollment_status', 'alumni');
                }

                // Filter Status Keberadaan (Presence Status)
                if ($this->presenceFilter) {
                    if ($this->presenceFilter === 'izin' || $this->presenceFilter === 'pulang') {
                        $rq->whereIn('presence_status', ['izin', 'pulang']);
                    } else {
                        $rq->where('presence_status', $this->presenceFilter);
                    }
                }
            })
            ->with([
                'santriProfile',
                'roles' => fn($q) => $q->where('role_type', 'santri'),
                'activeRoles',
                'roomAssignments' => fn($q) => $q->where('is_active', true)->with('room.dormitory'),
                'madrasahEnrollments' => fn($q) => $q->where('is_active', true)->with('kelas'),
            ]);

        // Filter Gender Scope
        if ($this->genderFilter) {
            $santriQuery->where('gender', $this->genderFilter);
        }

        // Filter Organization Scope
        if (!$user->hasRole('super-admin') && !$user->hasRole('pengasuh') && !$user->hasRole('manajemen')) {
            $orgIds = $user->getOrganizationIds();
            if (!empty($orgIds)) {
                $santriQuery->byOrganization($orgIds[0]);
            }
        }

        // Filter Search (Nama, NIK, NIS)
        if ($this->search) {
            $searchKeyword = '%' . trim($this->search) . '%';
            $santriQuery->where(function (Builder $q) use ($searchKeyword) {
                $q->where('name', 'like', $searchKeyword)
                  ->orWhere('nik', 'like', $searchKeyword)
                  ->orWhereHas('santriProfile', function ($sq) use ($searchKeyword) {
                      $sq->where('additional_info->nis', 'like', $searchKeyword)
                        ->orWhere('additional_info->nisn', 'like', $searchKeyword);
                  });
            });
        }

        // Filter spesifik Tab Komplek vs Tab Kelas
        if ($this->activeTab === 'komplek' && $this->dormitoryFilter) {
            $santriQuery->whereHas('roomAssignments', function (Builder $raq) {
                $raq->where('is_active', true)->whereHas('room', function (Builder $rq) {
                    $rq->where('dormitory_id', $this->dormitoryFilter);
                });
            });
        } elseif ($this->activeTab === 'kelas' && $this->kelasFilter) {
            $santriQuery->whereHas('madrasahEnrollments', function (Builder $meq) {
                $meq->where('is_active', true)->where('kelas_id', $this->kelasFilter);
            });
        }

        // Output untuk Mode Tabel (Paginated)
        $santriList = (clone $santriQuery)->orderBy('name')->paginate(15);

        // Output Data Hirarki untuk Mode Kartu / Bagan
        $dormitoriesData = collect();
        $kelasListData   = collect();

        if ($this->viewMode === 'card') {
            if ($this->activeTab === 'komplek') {
                $dormQuery = Dormitory::active()
                    ->with(['rooms' => function ($rq) {
                        $rq->active()->with(['currentAssignments' => function ($caq) {
                            $caq->with(['person.santriProfile', 'person.activeRoles', 'person.madrasahEnrollments.kelas']);
                        }]);
                    }]);

                if ($this->genderFilter) {
                    $dormQuery->where('gender', $this->genderFilter);
                }
                if ($this->dormitoryFilter) {
                    $dormQuery->where('id', $this->dormitoryFilter);
                }
                $dormitoriesData = $dormQuery->get();
            } else {
                $kelasQuery = MadrasahKelas::where('is_active', true)
                    ->with(['waliKelas', 'enrollments' => function ($eq) {
                        $eq->where('is_active', true)->with(['person.santriProfile', 'person.activeRoles', 'person.roomAssignments.room.dormitory']);
                    }]);

                if ($this->genderFilter === 'L') {
                    $kelasQuery->where(fn($sq) => $sq->where('name', 'like', '%(Pa)%')->orWhere('name', 'like', '%Pa%')->orWhere('name', 'like', '%Putra%')->orWhere('name', 'like', '%(L)%')->orWhereHas('enrollments.person', fn($pq) => $pq->where('gender', 'L')));
                } elseif ($this->genderFilter === 'P') {
                    $kelasQuery->where(fn($sq) => $sq->where('name', 'like', '%(Pi)%')->orWhere('name', 'like', '%Pi%')->orWhere('name', 'like', '%Putri%')->orWhere('name', 'like', '%(P)%')->orWhereHas('enrollments.person', fn($pq) => $pq->where('gender', 'P')));
                }

                if ($this->kelasFilter) {
                    $kelasQuery->where('id', $this->kelasFilter);
                }
                $kelasListData = $kelasQuery->orderBy('jenjang')->orderBy('name')->get();
            }
        }

        // Dropdown options
        $dormitoryOptions = Dormitory::active()
            ->when($this->genderFilter, fn($q) => $q->where('gender', $this->genderFilter))
            ->get();

        $roomOptions = Room::active()
            ->with('dormitory')
            ->when($this->genderFilter, fn($q) => $q->whereHas('dormitory', fn($dq) => $dq->where('gender', $this->genderFilter)))
            ->get();

        $kelasOptions = MadrasahKelas::where('is_active', true)
            ->when($this->genderFilter === 'L', fn($q) => $q->where(fn($sq) => $sq->where('name', 'like', '%(Pa)%')->orWhere('name', 'like', '%Pa%')->orWhere('name', 'like', '%Putra%')->orWhere('name', 'like', '%(L)%')->orWhereHas('enrollments.person', fn($pq) => $pq->where('gender', 'L'))))
            ->when($this->genderFilter === 'P', fn($q) => $q->where(fn($sq) => $sq->where('name', 'like', '%(Pi)%')->orWhere('name', 'like', '%Pi%')->orWhere('name', 'like', '%Putri%')->orWhere('name', 'like', '%(P)%')->orWhereHas('enrollments.person', fn($pq) => $pq->where('gender', 'P'))))
            ->orderBy('jenjang')
            ->orderBy('name')
            ->get();

        return view('livewire.kepengasuhan.peta-santri-manager', [
            'santriList'       => $santriList,
            'dormitoriesData'  => $dormitoriesData,
            'kelasListData'    => $kelasListData,
            'dormitoryOptions' => $dormitoryOptions,
            'roomOptions'      => $roomOptions,
            'kelasOptions'     => $kelasOptions,
        ])->layout('layouts.app');
    }
}
