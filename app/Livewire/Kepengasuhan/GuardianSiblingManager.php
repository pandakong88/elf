<?php

namespace App\Livewire\Kepengasuhan;

use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\Guardian;
use App\Modules\Kepengasuhan\Models\SantriGuardian;
use App\Modules\Kepengasuhan\Models\SantriSibling;
use App\Modules\Kepengasuhan\Services\GuardianService;
use App\Modules\Kepengasuhan\Services\SiblingService;
use Livewire\Component;
use App\Livewire\Concerns\SendsToast;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GuardianSiblingManager extends Component
{
    use SendsToast;

    use WithPagination;

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

    // Tab Sibling: Form
    public $siblingRelationship = 'saudara';

    protected $queryString = [
        'search' => ['except' => ''],
        'activeTab' => ['except' => 'guardians'],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
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
        try {
            app(SiblingService::class)->rejectSibling($relationId);
            $this->toastSuccess('Hubungan saudara ditolak & dihapus.');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function runAutoDetection(): void
    {
        try {
            $detected = app(SiblingService::class)->detectSiblingsByGuardian();
            $this->toastSuccess("Selesai memindai! Berhasil mendeteksi {$detected} relasi saudara kandung baru.");
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    // =========================================================================
    // Render
    // =========================================================================

    public function render()
    {
        // 1. Ambil wali
        $guardians = Guardian::query()
            ->when(!empty($this->search), function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone_primary', 'like', '%' . $this->search . '%')
                  ->orWhere('city', 'like', '%' . $this->search . '%');
            })
            ->withCount('santri')
            ->paginate(10);

        // Untuk detail modal
        $selectedGuardian = $this->selectedGuardianId
            ? Guardian::with('santri')->find($this->selectedGuardianId)
            : null;

        // Untuk link santri modal (santri list)
        $linkSantriList = [];
        if ($this->showLinkModal && !empty($this->linkSearch)) {
            $linkSantriList = Person::role('santri')
                ->where('name', 'like', '%' . $this->linkSearch . '%')
                ->limit(5)
                ->get();
        }

        // Untuk merge modal (other guardians list)
        $mergeCandidates = [];
        if ($this->showMergeModal) {
            $mergeCandidates = Guardian::where('id', '!=', $this->guardianId)
                ->orderBy('name')
                ->get();
        }

        // 2. Ambil Sibling data
        $unconfirmedSiblings = SantriSibling::where('is_confirmed', false)
            ->with(['person', 'sibling'])
            ->get();

        $confirmedSiblings = SantriSibling::where('is_confirmed', true)
            ->with(['person', 'sibling', 'confirmedBy'])
            ->get();

        $discountEligible = app(SiblingService::class)->getSiblingDiscountEligible();

        $relationshipOptions = SantriSibling::relationshipOptions();
        $guardianRelationOptions = Guardian::relationshipOptions();

        return view('livewire.kepengasuhan.guardian-sibling-manager', compact(
            'guardians', 'selectedGuardian', 'linkSantriList', 'mergeCandidates',
            'unconfirmedSiblings', 'confirmedSiblings', 'discountEligible',
            'relationshipOptions', 'guardianRelationOptions'
        ))->layout('layouts.app');
    }
}
