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
use App\Modules\Kepengasuhan\Services\SantriStatusService;
use App\Modules\Core\Models\PersonRole;
use App\Traits\HasGenderScope;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Exports\SantriExport;
use App\Imports\SantriUpdateImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class PetaSantriManager extends Component
{
    use SendsToast, WithPagination, HasGenderScope, WithFileUploads;

    // Export & Import Excel
    public $importFile             = null;
    public bool  $showImportModal   = false;
    public bool  $showExportConfirmModal = false;
    public array $importResults    = [];
    public int   $importStep       = 1; // 1: Choose File, 2: Preview Diff Table
    public array $importPreviewData = [];

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

    // Modal: Ubah Status Santri
    public bool    $showStatusModal        = false;
    public ?string $statusSantriId         = null;
    public string  $statusSantriName       = '';
    public ?string $statusSantriRoleId     = null;
    public string  $targetPresenceStatus   = 'mukim';
    public string  $targetEnrollmentStatus = 'aktif';
    public string  $statusChangeNotes      = '';

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

    public ?string $deletingSantriId   = null;
    public string  $deletingSantriName = '';

    public function processConfirmedAction(): void
    {
        $this->showConfirmModal = false;

        if ($this->confirmAction === 'executeStatusChange') {
            $this->executeStatusChange();
        } elseif ($this->confirmAction === 'executeTransferRoom') {
            $this->executeTransferRoom();
        } elseif ($this->confirmAction === 'executeTransferKelas') {
            $this->executeTransferKelas();
        } elseif ($this->confirmAction === 'executeDeleteSantri') {
            $this->executeDeleteSantri();
        }
    }

    public function openDeleteSantriModal(string $personId): void
    {
        if (!auth()->user()->can('delete-person')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk menghapus data santri.');
            return;
        }

        $person = Person::withCount([
            'bills as paid_bills_count' => fn($q) => $q->where(fn($sq) => $sq->where('amount_paid', '>', 0)->orWhereIn('status', ['paid', 'partial'])->orWhereHas('payments'))
        ])->find($personId);

        if (!$person) {
            $this->toastError('Data santri tidak ditemukan.');
            return;
        }

        // Syarat Mutlak: Hanya blokir jika SUDAH PERNAH ADA PEMBAYARAN DI KASIR (kuitansi lunas/cicilan)
        if ($person->paid_bills_count > 0) {
            $this->toastError("Santri '{$person->name}' tidak dapat dihapus karena sudah memiliki {$person->paid_bills_count} kuitansi/transaksi pembayaran lunas di kasir. Gunakan tombol 'Ubah Status' (Boyong/Alumni) untuk menonaktifkan.");
            return;
        }

        $this->deletingSantriId   = $personId;
        $this->deletingSantriName = $person->name;

        $this->confirmAction      = 'executeDeleteSantri';
        $this->confirmTitle       = 'Konfirmasi Hapus Data Santri';
        $this->confirmMessage     = "Apakah Anda YAKIN ingin menghapus data santri {$person->name}? Tagihan belum lunas & penempatan kamar/kelas akan dibersihkan otomatis oleh sistem.";
        $this->confirmButtonText  = 'Ya, Hapus Data Santri';
        $this->confirmButtonColor = 'rose';
        $this->showConfirmModal   = true;
    }

    public function executeDeleteSantri(): void
    {
        if (!auth()->user()->can('delete-person')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk menghapus data santri.');
            return;
        }

        if (!$this->deletingSantriId) return;

        try {
            DB::transaction(function () {
                $person = Person::findOrFail($this->deletingSantriId);

                // Bersihkan tagihan belum lunas, kamar, dan kelas secara otomatis
                $person->bills()->where('status', 'unpaid')->where(fn($q) => $q->whereNull('amount_paid')->orWhere('amount_paid', 0))->delete();
                $person->roomAssignments()->delete();
                $person->madrasahEnrollments()->delete();
                $person->roles()->delete();
                if ($person->santriProfile) {
                    $person->santriProfile()->delete();
                }
                $person->delete();

                activity()
                    ->performedOn($person)
                    ->causedBy(auth()->user())
                    ->log("Hapus data santri '{$this->deletingSantriName}' (tagihan belum lunas, kamar & kelas dibersihkan)");
            });

            $this->toastSuccess("Data santri '{$this->deletingSantriName}' berhasil dihapus dari sistem.");
            $this->deletingSantriId = null;
        } catch (\Exception $e) {
            $this->toastError('Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function openTransferRoomModal(string $santriId): void
    {
        $person = Person::findOrFail($santriId);
        $this->showQuickProfileModal = false;
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
        if (!auth()->user()->can('manage-kamar')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk memindahkan santri antar kamar.');
            return;
        }

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
        $this->showQuickProfileModal = false;
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

    // =========================================================================
    // Ubah Status Santri Methods
    // =========================================================================

    public function openStatusModal(string $santriId): void
    {
        $person = Person::with('activeRoles')->findOrFail($santriId);
        $role   = $person->activeRoles->firstWhere('role_type', 'santri');

        if (!$role) {
            $this->toastError('Role santri tidak ditemukan untuk santri ini.');
            return;
        }

        $this->statusSantriId         = $santriId;
        $this->statusSantriName       = $person->name;
        $this->statusSantriRoleId     = $role->id;
        $this->targetPresenceStatus   = $role->presence_status ?? 'mukim';
        $this->targetEnrollmentStatus = $role->enrollment_status ?? 'aktif';
        $this->statusChangeNotes      = '';
        $this->showStatusModal        = true;
    }

    public function requestStatusChangeConfirm(): void
    {
        $this->confirmAction      = 'executeStatusChange';
        $this->confirmTitle       = 'Konfirmasi Perubahan Status Santri';
        $this->confirmMessage     = "Apakah Anda YAKIN ingin mengubah status santri {$this->statusSantriName}? Perubahan ini akan memengaruhi alokasi kamar & kelas aktif santri.";
        $this->confirmButtonText  = 'Ya, Ubah Status';
        $this->confirmButtonColor = 'amber';
        $this->showConfirmModal   = true;
    }

    public function executeStatusChange(): void
    {
        if (!auth()->user()->can('change-enrollment-status') && !auth()->user()->can('change-presence-status')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk mengubah status santri.');
            return;
        }

        if (!$this->statusSantriRoleId) return;

        try {
            $statusService = app(\App\Modules\Kepengasuhan\Services\SantriStatusService::class);

            $role = \App\Modules\Core\Models\PersonRole::find($this->statusSantriRoleId);
            if ($role && $role->enrollment_status !== $this->targetEnrollmentStatus) {
                $statusService->changeEnrollmentStatus(
                    $this->statusSantriRoleId,
                    $this->targetEnrollmentStatus,
                    auth()->id(),
                    $this->statusChangeNotes ?: 'Perubahan status via Data Santri Master'
                );
            }

            $role = \App\Modules\Core\Models\PersonRole::find($this->statusSantriRoleId);
            if ($role && $role->isActiveEnrollment() && $role->presence_status !== $this->targetPresenceStatus) {
                $statusService->changePresenceStatus(
                    $this->statusSantriRoleId,
                    $this->targetPresenceStatus,
                    auth()->id(),
                    null,
                    $this->statusChangeNotes ?: 'Perubahan status via Data Santri Master'
                );
            }

            $this->toastSuccess("Status santri {$this->statusSantriName} berhasil diperbarui.");
            $this->showStatusModal = false;
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function executeTransferKelas(): void
    {
        if (!auth()->user()->can('manage-kelas')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk memindahkan santri antar kelas.');
            return;
        }

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
    // Query Builder Helper
    // =========================================================================

    private function getSantriBaseQuery(): Builder
    {
        $user = auth()->user();

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
        // Hanya berlaku untuk user yang terhubung ke organisasi bertipe 'unit' atau 'pondok'
        // (yaitu pengurus asrama/komplek). Role yang terhubung ke org tipe lain
        // (madrasah, koperasi, tahfidz, dll.) TIDAK dikunci per-organisasi — mereka
        // tetap bisa melihat semua santri, namun tetap dibatasi oleh gender scope di atas.
        // Ini bersifat future-proof: role baru apapun yg terhubung ke org non-unit/pondok
        // otomatis akan mendapat perilaku yang sama tanpa perlu mengubah kode ini.
        if ($user && !$user->hasRole('super-admin') && !$user->hasRole('pengasuh') && !$user->hasRole('manajemen')) {
            $orgIds = $user->getOrganizationIds();
            if (!empty($orgIds)) {
                // Cek apakah salah satu org user bertipe 'unit' atau 'pondok' (scope per-asrama)
                $asramaOrgIds = \App\Modules\Core\Models\Organization::whereIn('id', $orgIds)
                    ->whereIn('type', ['unit', 'pondok'])
                    ->pluck('id')
                    ->toArray();

                if (!empty($asramaOrgIds)) {
                    // User adalah pengurus asrama/komplek → filter per organisasi
                    $santriQuery->byOrganization($asramaOrgIds[0]);
                }
                // else: user dari org tipe lain (madrasah, koperasi, tahfidz, dll.)
                // → tidak dikunci per-organisasi, cukup gender scope saja
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

        return $santriQuery;
    }

    // =========================================================================
    // Export & Import Actions
    // =========================================================================

    public function openExportConfirmModal(): void
    {
        $this->showExportConfirmModal = true;
    }

    public function getExportSummaryProperty(): array
    {
        $dormName = 'Semua Komplek';
        if ($this->activeTab === 'komplek' && $this->dormitoryFilter) {
            $dorm = Dormitory::find($this->dormitoryFilter);
            $dormName = $dorm ? 'Komplek ' . $dorm->name : 'Komplek Custom';
        }

        $kelasName = 'Semua Kelas';
        if ($this->activeTab === 'kelas' && $this->kelasFilter) {
            $kelas = MadrasahKelas::find($this->kelasFilter);
            $kelasName = $kelas ? 'Kelas ' . $kelas->name : 'Kelas Custom';
        }

        $enrollmentLabel = match ($this->enrollmentFilter) {
            'aktif'  => 'Aktif (Mukim & Laju)',
            'boyong' => 'Boyong / Keluar',
            'alumni' => 'Alumni / Lulus',
            default  => 'Semua Status Keanggotaan',
        };

        $presenceLabel = match ($this->presenceFilter) {
            'mukim'        => 'Mukim (Tinggal di Asrama)',
            'laju'         => 'Laju (Non-Asrama)',
            'izin', 'pulang' => 'Izin / Pulang Sementara',
            default        => 'Semua Status Keberadaan',
        };

        $genderLabel = match ($this->genderFilter) {
            'L'     => 'Putra (L)',
            'P'     => 'Putri (P)',
            default => 'Semua Gender',
        };

        $totalCount = $this->getSantriBaseQuery()->count();
        $filename   = $this->generateExportFilename($dormName, $kelasName);

        return [
            'total_count'      => $totalCount,
            'enrollment_label' => $enrollmentLabel,
            'presence_label'   => $presenceLabel,
            'gender_label'     => $genderLabel,
            'location_label'   => $this->activeTab === 'komplek' ? $dormName : $kelasName,
            'search_label'     => $this->search ? '"' . trim($this->search) . '"' : 'Tidak Ada (Semua Data)',
            'filename'         => $filename,
        ];
    }

    private function generateExportFilename(string $dormName, string $kelasName): string
    {
        $parts = ['Data-Santri'];

        if ($this->enrollmentFilter) {
            $parts[] = ucfirst($this->enrollmentFilter);
        }

        if ($this->genderFilter === 'L') {
            $parts[] = 'Putra';
        } elseif ($this->genderFilter === 'P') {
            $parts[] = 'Putri';
        }

        if ($this->activeTab === 'komplek' && $this->dormitoryFilter) {
            $parts[] = \Illuminate\Support\Str::slug($dormName);
        } elseif ($this->activeTab === 'kelas' && $this->kelasFilter) {
            $parts[] = \Illuminate\Support\Str::slug($kelasName);
        }

        if ($this->presenceFilter) {
            $parts[] = ucfirst($this->presenceFilter);
        }

        $parts[] = now()->format('Y-m-d_His');

        return implode('_', $parts) . '.xlsx';
    }

    public function exportSantri(): mixed
    {
        $this->showExportConfirmModal = false;

        $summary    = $this->exportSummary;
        $collection = $this->getSantriBaseQuery()->orderBy('name')->get();
        $filename   = $summary['filename'];

        return response()->streamDownload(function () use ($collection) {
            echo Excel::raw(new SantriExport($collection), \Maatwebsite\Excel\Excel::XLSX);
        }, $filename);
    }

    public function generateImportPreview(): void
    {
        $this->validate(['importFile' => 'required|file|mimes:xlsx,xls|max:10240']);

        try {
            $path = $this->importFile->getRealPath();
            $this->importPreviewData = SantriUpdateImport::parsePreview($path);
            $this->importStep = 2;
        } catch (\Exception $e) {
            $this->toastError('Gagal membaca file Excel: ' . $e->getMessage());
        }
    }

    public function resetImportModal(): void
    {
        $this->importFile          = null;
        $this->showImportModal     = false;
        $this->importStep          = 1;
        $this->importPreviewData   = [];
    }

    public function processImport(): void
    {
        if (!$this->importFile) {
            $this->toastError('File Excel belum dipilih.');
            return;
        }

        try {
            $import = new SantriUpdateImport();
            Excel::import($import, $this->importFile->getRealPath());

            $this->importResults  = $import->results;
            $updated = $this->importResults['updated'];
            $skipped = count($this->importResults['skipped']);

            $this->resetImportModal();
            $this->toastSuccess("Berhasil! {$updated} data santri telah diperbarui" . ($skipped > 0 ? ", {$skipped} baris dilewati." : '.'));
        } catch (\Exception $e) {
            $this->toastError('Gagal memproses update: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // Render Method
    // =========================================================================

    public function render()
    {
        $user = auth()->user();

        // 1. Base Query Santri dengan Eager Loading & Combined Roles Filter
        $santriQuery = $this->getSantriBaseQuery();

        // Hitung statistik berdasarkan filter aktif saat ini
        $statsQuery = clone $santriQuery;
        $stats = [
            'total'   => (clone $statsQuery)->count(),
            'mukim'   => (clone $statsQuery)->whereHas('roles', fn($q) => $q->where('role_type','santri')->where('presence_status','mukim'))->count(),
            'laju'    => (clone $statsQuery)->whereHas('roles', fn($q) => $q->where('role_type','santri')->where('presence_status','laju'))->count(),
            'izin'    => (clone $statsQuery)->whereHas('roles', fn($q) => $q->where('role_type','santri')->whereIn('presence_status',['izin','pulang']))->count(),
            'boyong'  => (clone $statsQuery)->whereHas('roles', fn($q) => $q->where('role_type','santri')->whereIn('enrollment_status',['boyong','keluar_resmi','dikeluarkan','alumni','tanpa_keterangan']))->count(),
        ];

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

        // Dropdown options (Natural Sorting: 1, 2, 3... 10, 11)
        $dormitoryOptions = Dormitory::active()
            ->when($this->genderFilter, fn($q) => $q->where('gender', $this->genderFilter))
            ->get()
            ->sort(fn($a, $b) => strnatcasecmp($a->name, $b->name));

        $roomOptions = Room::active()
            ->with('dormitory')
            ->when($this->genderFilter, fn($q) => $q->whereHas('dormitory', fn($dq) => $dq->where('gender', $this->genderFilter)))
            ->get()
            ->sort(fn($a, $b) => strnatcasecmp($a->name, $b->name));

        $kelasOptions = MadrasahKelas::where('is_active', true)
            ->when($this->genderFilter === 'L', fn($q) => $q->where(fn($sq) => $sq->where('name', 'like', '%(Pa)%')->orWhere('name', 'like', '%Pa%')->orWhere('name', 'like', '%Putra%')->orWhere('name', 'like', '%(L)%')->orWhereHas('enrollments.person', fn($pq) => $pq->where('gender', 'L'))))
            ->when($this->genderFilter === 'P', fn($q) => $q->where(fn($sq) => $sq->where('name', 'like', '%(Pi)%')->orWhere('name', 'like', '%Pi%')->orWhere('name', 'like', '%Putri%')->orWhere('name', 'like', '%(P)%')->orWhereHas('enrollments.person', fn($pq) => $pq->where('gender', 'P'))))
            ->get()
            ->sort(fn($a, $b) => strnatcasecmp($a->name, $b->name));

        return view('livewire.kepengasuhan.peta-santri-manager', [
            'santriList'       => $santriList,
            'stats'            => $stats,
            'dormitoriesData'  => $dormitoriesData,
            'kelasListData'    => $kelasListData,
            'dormitoryOptions' => $dormitoryOptions,
            'roomOptions'      => $roomOptions,
            'kelasOptions'     => $kelasOptions,
        ])->layout('layouts.app');
    }
}
