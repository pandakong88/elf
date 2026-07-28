<?php

namespace App\Livewire\Kepengasuhan;

use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\Guardian;
use App\Modules\Kepengasuhan\Models\SantriGuardian;
use App\Modules\Kepengasuhan\Models\SantriProfile;
use App\Modules\Kepengasuhan\Models\SantriSibling;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Madrasah\Models\MadrasahKelas;
use App\Modules\Kepengasuhan\Services\GuardianService;
use App\Modules\Kepengasuhan\Services\SiblingService;
use Livewire\Component;
use App\Livewire\Concerns\SendsToast;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Traits\HasGenderScope;

class GuardianSiblingManager extends Component
{
    use SendsToast, WithPagination, HasGenderScope;

    // State Umum
    public $activeTab = 'guardians'; // guardians | siblings

    // Tab Wali: Search & Detail
    public $search = '';
    public $selectedGuardianId = null;
    public $showDetailModal = false;

    // Tab Wali: Create/Edit Form
    public $showFormModal = false;
    public $isEditing = false;
    public $guardianId = null;
    public $guardianName = '';
    public $guardianPhone = '';
    public $guardianOccupation = '';
    public $guardianEducation = '';
    public $guardianAddress = '';
    public $guardianCity = '';
    public $guardianNotes = '';

    // Tab Wali: Link Santri Form
    public $showLinkModal = false;
    public $linkSearch = '';
    public $linkRelationship = 'wali_resmi';
    public $linkIsPrimary = false;

    // Tab Wali: Merge Form
    public $showMergeModal = false;
    public $mergeTargetId = null;

    // Tab Sibling: Form, Search & Filters
    public $siblingRelationship = 'saudara';
    public $siblingSearch = '';
    public $siblingStatusFilter = 'all'; // 'all', 'has_sibling', 'no_sibling'
    public ?string $siblingFilterGender = null; // null, 'L', 'P'
    public ?string $siblingFilterDormitoryId = null;
    public ?string $siblingFilterKelasId = null;
    public ?string $siblingFilterPresenceStatus = null; // null, 'mukim', 'laju'

    // Bulk selection state
    public array $selectedSantriIds = [];
    public bool $selectAllInPage = false;

    // Confirmation Modal state
    public bool $showConfirmModal = false;
    public string $confirmTitle = '';
    public string $confirmMessage = '';
    public string $confirmAction = ''; // 'toggle_single', 'bulk_set_sibling', 'bulk_set_single', 'auto_detect'
    public ?string $confirmTargetId = null;
    public ?bool $confirmTargetValue = null;

    public function mount(): void
    {
        $user = auth()->user();
        if (!$user || !($user->can('manage-sensus') || $user->can('view-person') || $user->can('view-any-santri') || $user->hasRole('super-admin') || $user->hasRole('manajemen') || $user->hasRole('pengasuh'))) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengelola data wali & saudara.');
        }
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'siblingSearch' => ['except' => ''],
        'siblingStatusFilter' => ['except' => 'all'],
        'siblingFilterGender' => ['except' => ''],
        'siblingFilterDormitoryId' => ['except' => ''],
        'siblingFilterKelasId' => ['except' => ''],
        'siblingFilterPresenceStatus' => ['except' => ''],
        'activeTab' => ['except' => 'guardians'],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSiblingSearch(): void
    {
        $this->resetPage('siblingPage');
    }

    public function updatedSiblingStatusFilter(): void
    {
        $this->resetPage('siblingPage');
    }

    public function updatedSiblingFilterGender(): void
    {
        $this->resetPage('siblingPage');
    }

    public function updatedSiblingFilterDormitoryId(): void
    {
        $this->resetPage('siblingPage');
    }

    public function updatedSiblingFilterKelasId(): void
    {
        $this->resetPage('siblingPage');
    }

    public function updatedSiblingFilterPresenceStatus(): void
    {
        $this->resetPage('siblingPage');
    }

    public function resetSiblingFilters(): void
    {
        $this->reset([
            'siblingSearch', 'siblingStatusFilter', 'siblingFilterGender',
            'siblingFilterDormitoryId', 'siblingFilterKelasId', 'siblingFilterPresenceStatus'
        ]);
        $this->resetPage('siblingPage');
    }

    // =========================================================================
    // Guardian CRUD
    // =========================================================================

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showFormModal = true;
    }

    public function openEditModal(string $id): void
    {
        $this->resetForm();
        $this->isEditing = true;
        $this->guardianId = $id;

        $g = Guardian::findOrFail($id);
        $this->guardianName = $g->name;
        $this->guardianPhone = $g->phone_primary;
        $this->guardianOccupation = $g->occupation;
        $this->guardianEducation = $g->education_level;
        $this->guardianAddress = $g->address;
        $this->guardianCity = $g->city;
        $this->guardianNotes = $g->notes;

        $this->showFormModal = true;
    }

    public function saveGuardian(): void
    {
        if (!auth()->user()->can('manage-sensus') && !auth()->user()->can('update-person') && !auth()->user()->can('create-person')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk mengelola data wali.');
            return;
        }

        $this->validate([
            'guardianName' => 'required|string|max:100',
            'guardianPhone' => 'required|string|max:20',
            'guardianEducation' => 'nullable|string',
            'guardianOccupation' => 'nullable|string',
            'guardianAddress' => 'nullable|string',
            'guardianCity' => 'nullable|string',
            'guardianNotes' => 'nullable|string',
        ]);

        try {
            $service = app(GuardianService::class);
            $data = [
                'name' => $this->guardianName,
                'phone_primary' => $this->guardianPhone,
                'education_level' => $this->guardianEducation,
                'occupation' => $this->guardianOccupation,
                'address' => $this->guardianAddress,
                'city' => $this->guardianCity,
                'notes' => $this->guardianNotes,
            ];

            if ($this->isEditing) {
                $g = Guardian::findOrFail($this->guardianId);
                $g->update($data);
                $this->toastSuccess('Data wali berhasil diperbarui.');
            } else {
                $service->createOrFindGuardian($data);
                $this->toastSuccess('Wali baru berhasil ditambahkan.');
            }

            $this->showFormModal = false;
            $this->resetForm();
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    private function resetForm(): void
    {
        $this->reset([
            'guardianId', 'guardianName', 'guardianPhone',
            'guardianOccupation', 'guardianEducation', 'guardianAddress',
            'guardianCity', 'guardianNotes'
        ]);
    }

    // =========================================================================
    // Guardian Linking
    // =========================================================================

    public function openLinkModal(string $guardianId): void
    {
        $this->guardianId = $guardianId;
        $this->reset(['linkSearch', 'linkRelationship', 'linkIsPrimary']);
        $this->showLinkModal = true;
    }

    public function linkSantri(string $personId): void
    {
        if (!auth()->user()->can('manage-sensus') && !auth()->user()->can('update-person')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk menghubungkan santri ke wali.');
            return;
        }

        try {
            $service = app(GuardianService::class);
            $service->linkGuardianToSantri(
                $this->guardianId,
                $personId,
                $this->linkRelationship,
                $this->linkIsPrimary
            );

            // Trigger sibling auto-detection
            app(SiblingService::class)->autoLinkFromCensusData($personId);

            $this->toastSuccess('Santri berhasil dihubungkan ke wali.');
            $this->showLinkModal = false;
            
            if ($this->showDetailModal) {
                // Refresh detail modal
                $this->openDetail($this->guardianId);
            }
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function unlinkSantri(string $personId): void
    {
        if (!auth()->user()->can('manage-sensus') && !auth()->user()->can('update-person')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk melepas hubungan wali.');
            return;
        }

        try {
            app(GuardianService::class)->unlinkGuardian($this->selectedGuardianId, $personId);
            $this->toastSuccess('Hubungan santri & wali berhasil dilepas.');
            
            // Refresh detail modal
            $this->openDetail($this->selectedGuardianId);
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    // =========================================================================
    // Guardian Detail
    // =========================================================================

    public function openDetail(string $id): void
    {
        $this->selectedGuardianId = $id;
        $this->showDetailModal = true;
    }

    // =========================================================================
    // Guardian Merging
    // =========================================================================

    public function openMergeModal(string $id): void
    {
        $this->guardianId = $id;
        $this->reset(['mergeTargetId']);
        $this->showMergeModal = true;
    }

    public function mergeGuardians(): void
    {
        if (!auth()->user()->can('manage-sensus') && !auth()->user()->can('update-person')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk menggabungkan data wali.');
            return;
        }

        $this->validate([
            'mergeTargetId' => 'required|different:guardianId',
        ]);

        DB::transaction(function () {
            $source = Guardian::findOrFail($this->guardianId);
            $target = Guardian::findOrFail($this->mergeTargetId);

            // Pindahkan semua santri di pivot
            $linkedSantris = SantriGuardian::where('guardian_id', $source->id)->get();
            foreach ($linkedSantris as $link) {
                // Cek jika target sudah ter-link
                $exists = SantriGuardian::where('guardian_id', $target->id)
                    ->where('person_id', $link->person_id)
                    ->exists();

                if (!$exists) {
                    $link->update(['guardian_id' => $target->id]);
                } else {
                    $link->delete(); // Hapus duplikat
                }
            }

            // Hapus source guardian yang digabungkan
            $source->delete();

            $this->toastSuccess("Wali \"{$source->name}\" berhasil digabungkan ke \"{$target->name}\".");
            $this->showMergeModal = false;
        });
    }

    // =========================================================================
    // Sibling Operations
    // =========================================================================

    public function confirmSibling(string $relationId): void
    {
        if (!auth()->user()->can('manage-sensus') && !auth()->user()->can('update-person')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk mengonfirmasi relasi saudara.');
            return;
        }

        try {
            app(SiblingService::class)->confirmSibling(
                $relationId,
                $this->siblingRelationship,
                auth()->id()
            );
            $this->toastSuccess('Hubungan saudara kandung berhasil dikonfirmasi.');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function rejectSibling(string $relationId): void
    {
        if (!auth()->user()->can('manage-sensus') && !auth()->user()->can('update-person')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk menolak relasi saudara.');
            return;
        }

        try {
            app(SiblingService::class)->rejectSibling($relationId);
            $this->toastSuccess('Hubungan saudara ditolak & dihapus.');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function requestRunAutoDetection(): void
    {
        if (!auth()->user()->can('manage-sensus') && !auth()->user()->can('update-person')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk menjalankan deteksi saudara.');
            return;
        }

        $this->confirmTitle = 'Pemindaian Otomatis Saudara';
        $this->confirmMessage = 'Sistem akan memindai seluruh data santri dan mencocokkan relasi saudara berdasarkan kesamaan nama orang tua dan kontak wali. Lanjutkan?';
        $this->confirmAction = 'auto_detect';
        $this->showConfirmModal = true;
    }

    public function requestToggleSingle(string $personId): void
    {
        if (!auth()->user()->can('manage-sensus') && !auth()->user()->can('update-person')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk mengubah status saudara.');
            return;
        }

        $person = Person::find($personId);
        if (!$person) return;

        $currentHasSibling = $person->santriProfile?->has_active_sibling ?? false;
        $targetValue = !$currentHasSibling;
        $targetLabel = $targetValue ? 'Bersaudara (Aktif Diskon)' : 'Santri Tunggal (Non-Diskon)';

        $this->confirmTitle = 'Konfirmasi Perubahan Status Saudara';
        $this->confirmMessage = "Apakah Anda yakin ingin mengubah status saudara santri \"{$person->name}\" menjadi \"{$targetLabel}\"?";
        $this->confirmAction = 'toggle_single';
        $this->confirmTargetId = $personId;
        $this->confirmTargetValue = $targetValue;
        $this->showConfirmModal = true;
    }

    public function requestBulkSetSibling(bool $hasSibling): void
    {
        if (!auth()->user()->can('manage-sensus') && !auth()->user()->can('update-person')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk melakukan perubahan status saudara massal.');
            return;
        }

        $count = count($this->selectedSantriIds);
        if ($count === 0) {
            $this->toastError('Pilih minimal 1 santri dari tabel terlebih dahulu.');
            return;
        }

        $targetLabel = $hasSibling ? 'Bersaudara (Aktif Diskon)' : 'Santri Tunggal (Non-Diskon)';

        $this->confirmTitle = 'Konfirmasi Perubahan Massal (Bulk Update)';
        $this->confirmMessage = "Apakah Anda yakin ingin mengubah status {$count} santri terpilih menjadi \"{$targetLabel}\"?";
        $this->confirmAction = $hasSibling ? 'bulk_set_sibling' : 'bulk_set_single';
        $this->confirmTargetValue = $hasSibling;
        $this->showConfirmModal = true;
    }

    public function executeConfirmedAction(): void
    {
        if (!auth()->user()->can('manage-sensus') && !auth()->user()->can('update-person')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk mengeksekusi aksi ini.');
            return;
        }
        try {
            if ($this->confirmAction === 'toggle_single' && $this->confirmTargetId) {
                $this->executeToggleSingle($this->confirmTargetId, $this->confirmTargetValue);
            } elseif (in_array($this->confirmAction, ['bulk_set_sibling', 'bulk_set_single'])) {
                $this->executeBulkSetSibling($this->confirmTargetValue);
            } elseif ($this->confirmAction === 'auto_detect') {
                $detected = app(SiblingService::class)->detectSiblingsByGuardian();
                $this->toastSuccess("Selesai memindai! Berhasil mendeteksi {$detected} relasi saudara kandung baru.");
            }
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        } finally {
            $this->showConfirmModal = false;
            $this->reset(['confirmTitle', 'confirmMessage', 'confirmAction', 'confirmTargetId', 'confirmTargetValue']);
        }
    }

    private function executeToggleSingle(string $personId, bool $newValue): void
    {
        $profile = SantriProfile::firstOrCreate(
            ['person_id' => $personId],
            ['id' => Str::uuid()->toString()]
        );
        $profile->has_active_sibling = $newValue;
        $profile->save();

        $statusLabel = $newValue ? 'Bersaudara (Aktif Diskon)' : 'Santri Tunggal (Non-Diskon)';
        $this->toastSuccess("Status saudara santri berhasil diubah menjadi: {$statusLabel}.");
    }

    private function executeBulkSetSibling(bool $newValue): void
    {
        $count = count($this->selectedSantriIds);
        DB::transaction(function () use ($newValue) {
            foreach ($this->selectedSantriIds as $personId) {
                $profile = SantriProfile::firstOrCreate(
                    ['person_id' => $personId],
                    ['id' => Str::uuid()->toString()]
                );
                $profile->has_active_sibling = $newValue;
                $profile->save();
            }
        });

        $statusLabel = $newValue ? 'Bersaudara' : 'Santri Tunggal';
        $this->toastSuccess("Berhasil mengubah status {$count} santri menjadi {$statusLabel}.");
        $this->selectedSantriIds = [];
        $this->selectAllInPage = false;
    }

    public function toggleSantriSelection(string $id): void
    {
        if (in_array($id, $this->selectedSantriIds)) {
            $this->selectedSantriIds = array_values(array_diff($this->selectedSantriIds, [$id]));
        } else {
            $this->selectedSantriIds[] = $id;
        }
    }

    public function selectAllOnPage(array $pagePersonIds): void
    {
        $this->selectedSantriIds = array_values(array_unique(array_merge($this->selectedSantriIds, $pagePersonIds)));
        $this->selectAllInPage = true;
    }

    public function deselectAllOnPage(array $pagePersonIds): void
    {
        $this->selectedSantriIds = array_values(array_diff($this->selectedSantriIds, $pagePersonIds));
        $this->selectAllInPage = false;
    }

    // =========================================================================
    // Render
    // =========================================================================

    public function render()
    {
        $userGender = $this->genderScope();

        // 1. Ambil wali
        $guardiansQuery = Guardian::query()
            ->when(!empty($this->search), function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone_primary', 'like', '%' . $this->search . '%')
                  ->orWhere('city', 'like', '%' . $this->search . '%');
            });

        if ($userGender) {
            $guardiansQuery->whereHas('santri', fn($sq) => $sq->where('gender', $userGender));
        }

        $guardians = $guardiansQuery->withCount('santri')->paginate(10);

        // Detail & Modals data
        $selectedGuardian = $this->selectedGuardianId
            ? Guardian::with('santri')->find($this->selectedGuardianId)
            : null;

        $linkSantriList = [];
        if ($this->showLinkModal && !empty($this->linkSearch)) {
            $linkSantriQuery = Person::role('santri')
                ->where('name', 'like', '%' . $this->linkSearch . '%');
            if ($userGender) {
                $linkSantriQuery->where('gender', $userGender);
            }
            $linkSantriList = $linkSantriQuery->limit(5)->get();
        }

        $mergeCandidates = [];
        if ($this->showMergeModal) {
            $mergeCandidates = Guardian::where('id', '!=', $this->guardianId)
                ->orderBy('name')
                ->get();
        }

        // 2. Ambil Sibling data (unconfirmed & confirmed)
        $unconfirmedSiblings = SantriSibling::where('is_confirmed', false)
            ->where(function($q) use ($userGender) {
                if ($userGender) {
                    $q->whereHas('person', fn($p) => $p->where('gender', $userGender))
                      ->orWhereHas('sibling', fn($s) => $s->where('gender', $userGender));
                }
            })
            ->with(['person', 'sibling'])
            ->get();

        $confirmedSiblings = SantriSibling::where('is_confirmed', true)
            ->where(function($q) use ($userGender) {
                if ($userGender) {
                    $q->whereHas('person', fn($p) => $p->where('gender', $userGender))
                      ->orWhereHas('sibling', fn($s) => $s->where('gender', $userGender));
                }
            })
            ->with(['person', 'sibling', 'confirmedBy'])
            ->get();

        // 3. Ambil Santri Sibling Management List (dengan Filter Lengkap & Gender Scope)
        $siblingSantriQuery = Person::whereHas('activeRoles', function ($q) {
            $q->where('role_type', 'santri')->where('enrollment_status', 'aktif');
        });

        // Gender scope / filter
        if ($userGender) {
            $siblingSantriQuery->where('gender', $userGender);
        } elseif (!empty($this->siblingFilterGender)) {
            $siblingSantriQuery->where('gender', $this->siblingFilterGender);
        }

        // Presence Status filter (Mukim vs Laju)
        if (!empty($this->siblingFilterPresenceStatus)) {
            $siblingSantriQuery->whereHas('activeRoles', fn($q) => $q->where('presence_status', $this->siblingFilterPresenceStatus));
        }

        // Dormitory filter
        if (!empty($this->siblingFilterDormitoryId)) {
            $siblingSantriQuery->whereHas('roomAssignments', function ($q) {
                $q->where('is_active', true)
                  ->whereHas('room', fn($rq) => $rq->where('dormitory_id', $this->siblingFilterDormitoryId));
            });
        }

        // Kelas filter
        if (!empty($this->siblingFilterKelasId)) {
            $siblingSantriQuery->whereHas('madrasahEnrollments', function ($q) {
                $q->where('is_active', true)->where('kelas_id', $this->siblingFilterKelasId);
            });
        }

        // Search Name
        if (!empty($this->siblingSearch)) {
            $siblingSantriQuery->where('name', 'like', '%' . $this->siblingSearch . '%');
        }

        // Sibling Status filter
        if ($this->siblingStatusFilter === 'has_sibling') {
            $siblingSantriQuery->whereHas('santriProfile', fn($sp) => $sp->where('has_active_sibling', true));
        } elseif ($this->siblingStatusFilter === 'no_sibling') {
            $siblingSantriQuery->where(function($sub) {
                $sub->whereDoesntHave('santriProfile')
                   ->orWhereHas('santriProfile', fn($sp) => $sp->where('has_active_sibling', false));
            });
        }

        $siblingSantriList = $siblingSantriQuery
            ->with(['santriProfile', 'roomAssignments.room.dormitory', 'madrasahEnrollments.kelas', 'activeRoles'])
            ->orderBy('name')
            ->paginate(15, ['*'], 'siblingPage');

        // Dynamic Filter Options
        $dormitories = Dormitory::where('is_active', true)
            ->when($userGender, fn($q) => $q->where('gender', $userGender))
            ->orderBy('name')
            ->get();

        $kelasList = MadrasahKelas::where('is_active', true)->orderBy('name')->get();

        $relationshipOptions = SantriSibling::relationshipOptions();
        $guardianRelationOptions = Guardian::relationshipOptions();

        return view('livewire.kepengasuhan.guardian-sibling-manager', compact(
            'guardians', 'selectedGuardian', 'linkSantriList', 'mergeCandidates',
            'unconfirmedSiblings', 'confirmedSiblings', 'siblingSantriList',
            'dormitories', 'kelasList', 'userGender',
            'relationshipOptions', 'guardianRelationOptions'
        ))->layout('layouts.app');
    }
}
