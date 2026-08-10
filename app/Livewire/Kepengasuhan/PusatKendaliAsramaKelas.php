<?php

namespace App\Livewire\Kepengasuhan;

use App\Livewire\Concerns\SendsToast;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Kepengasuhan\Models\SantriProfile;
use App\Modules\Kepengasuhan\Services\DormitoryService;
use App\Modules\Kepengasuhan\Services\SantriStatusService;
use App\Modules\Keuangan\Services\BillingService;
use App\Modules\Madrasah\Models\MadrasahEnrollment;
use App\Modules\Madrasah\Models\MadrasahKelas;
use App\Traits\HasGenderScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class PusatKendaliAsramaKelas extends Component
{
    use SendsToast, WithPagination, HasGenderScope;

    // Sub-Tab Navigation
    public string $activeTab = 'komplek'; // 'komplek', 'kamar', 'kelas', 'bagan-komplek', 'bagan-kelas'

    // Common Filters
    public string $search         = '';
    public string $genderFilter   = '';
    public bool   $isGenderLocked = false;
    public string $dormitoryFilter= '';
    public string $kelasFilter    = '';

    // -------------------------------------------------------------------------
    // Registration Form States (Santri Baru Super Lengkap)
    // -------------------------------------------------------------------------
    public bool    $showNewSantriModal     = false;
    public string  $newSantriNik           = '';
    public string  $newSantriName          = '';
    public string  $newSantriGender        = 'L';
    public string  $newSantriPob           = '';
    public string  $newSantriDob           = '';
    public string  $newSantriPhone         = '';
    public string  $newSantriBloodType     = '';
    public string  $newSantriFormalSchool  = '';
    public string  $newSantriEntryPath     = 'reguler'; // 'reguler', 'tes_placement', 'pindahan'

    public string  $newSantriFormalGrade   = ''; // Contoh: Kelas 7 / 1 SMP

    // Data Orang Tua / Wali Kandung
    public string  $newSantriFatherName           = '';
    public string  $newSantriFatherPhone          = '';
    public string  $newSantriFatherJob            = '';
    public string  $newSantriFatherAddress        = '';
    public string  $newSantriMotherName           = '';
    public string  $newSantriMotherPhone          = '';
    public string  $newSantriMotherJob            = '';
    public string  $newSantriMotherAddress        = '';
    public bool    $sameMotherAddress             = true;
    public string  $newSantriParentAddress        = '';
    public string  $newSantriGuardianName         = '';
    public string  $newSantriGuardianPhone        = '';
    public string  $newSantriGuardianRelationship = '';

    // Alokasi Penempatan
    public string  $newSantriPresence      = 'mukim'; // 'mukim', 'laju'
    public ?string $newSantriRoomId        = null;
    public ?string $newSantriKelasId       = null;

    // Dynamic Billing Checklist State
    public bool    $generateBillPackage    = true;
    public array   $billingChecklist       = [];

    public function updatedNewSantriFatherAddress(): void
    {
        if ($this->sameMotherAddress) {
            $this->newSantriMotherAddress = $this->newSantriFatherAddress;
            $this->newSantriParentAddress = $this->newSantriFatherAddress;
        }
    }

    public function updatedSameMotherAddress(): void
    {
        if ($this->sameMotherAddress) {
            $this->newSantriMotherAddress = $this->newSantriFatherAddress;
            $this->newSantriParentAddress = $this->newSantriFatherAddress;
        }
    }

    public function updatedNewSantriGender(): void
    {
        $this->newSantriRoomId  = null;
        $this->newSantriKelasId = null;
        $this->refreshBillingChecklist();
    }
    public function updatedNewSantriPresence(): void { $this->refreshBillingChecklist(); }
    public function updatedNewSantriKelasId(): void  { $this->refreshBillingChecklist(); }

    public function refreshBillingChecklist(): void
    {
        $this->billingChecklist = [];

        if (!$this->generateBillPackage) {
            return;
        }

        // Fetch active pendaftaran items from DB
        $dbItems = \App\Modules\Keuangan\Models\BillingConfiguration::where('type', 'pendaftaran')
            ->where('is_active', true)
            ->get();

        if ($dbItems->count() > 0) {
            foreach ($dbItems as $dbItem) {
                $filters  = $dbItem->target_filters ?? [];
                $gen      = $filters['gender'] ?? 'ALL';
                $res      = $filters['residence'] ?? 'ALL';
                $category = $filters['category'] ?? 'dasar';

                // Gender Filter Match
                if ($gen !== 'ALL' && $gen !== $this->newSantriGender) {
                    continue;
                }

                // Residence Filter Match
                if ($res !== 'ALL' && $res !== $this->newSantriPresence) {
                    continue;
                }

                // Default check state (majek optional/false by default, others true)
                $isMajek = str_contains(strtolower($dbItem->label), 'majek');

                $this->billingChecklist[] = [
                    'key'      => Str::slug($dbItem->label),
                    'label'    => $dbItem->label,
                    'amount'   => (float) $dbItem->amount,
                    'checked'  => !$isMajek,
                    'category' => $category,
                ];
            }
        } else {
            // Default Hardcoded Fallbacks if DB is empty
            $this->billingChecklist[] = [
                'key'      => 'pendaftaran',
                'label'    => 'Pendaftaran Diniyyah & Pondok',
                'amount'   => 150000,
                'checked'  => true,
                'category' => 'dasar',
            ];

            $this->billingChecklist[] = [
                'key'      => 'syahriyah',
                'label'    => 'Syahriyah Bulan Pertama',
                'amount'   => 35000,
                'checked'  => true,
                'category' => 'dasar',
            ];

            if ($this->newSantriPresence === 'mukim') {
                $this->billingChecklist[] = [
                    'key'      => 'almari',
                    'label'    => 'Almari & Fasilitas Asrama Mukim',
                    'amount'   => 250000,
                    'checked'  => true,
                    'category' => 'asrama',
                ];
                $this->billingChecklist[] = [
                    'key'      => 'pembangunan',
                    'label'    => 'Uang Pembangunan Pondok',
                    'amount'   => 100000,
                    'checked'  => true,
                    'category' => 'asrama',
                ];
            }

            if ($this->newSantriGender === 'L') {
                $this->billingChecklist[] = [
                    'key'      => 'seragam_putra',
                    'label'    => 'Seragam Santri Putra (Baju Koko, Sarung, Peci)',
                    'amount'   => 120000,
                    'checked'  => true,
                    'category' => 'seragam',
                ];
                if ($this->newSantriPresence === 'mukim') {
                    $this->billingChecklist[] = [
                        'key'      => 'majek_putra',
                        'label'    => 'Uang Makan Majek Putra (Opsional)',
                        'amount'   => 200000,
                        'checked'  => false,
                        'category' => 'konsumsi',
                    ];
                }
            } else {
                $this->billingChecklist[] = [
                    'key'      => 'seragam_putri',
                    'label'    => 'Seragam Santri Putri (Gamis, Jilbab)',
                    'amount'   => 210000,
                    'checked'  => true,
                    'category' => 'seragam',
                ];
            }
        }

        // Tagihan Kitab (Berdasarkan Kelas yang dipilih)
        if ($this->newSantriKelasId) {
            $kelas = MadrasahKelas::find($this->newSantriKelasId);
            $kelasName = $kelas ? $kelas->name : 'Madrasah';

            // Query configured kitab price from DB
            $dbKitab = \App\Modules\Keuangan\Models\BillingConfiguration::where('type', 'kitab')
                ->where('label', 'like', "%{$kelasName}%")
                ->where('is_active', true)
                ->first();

            $kitabPrice = $dbKitab ? (float) $dbKitab->amount : 136000;
            if (!$dbKitab && $kelas) {
                if (str_contains(strtolower($kelas->name), 'awaliyah 2')) {
                    $kitabPrice = 150000;
                } elseif (str_contains(strtolower($kelas->name), 'awaliyah 3')) {
                    $kitabPrice = 175000;
                } elseif (str_contains(strtolower($kelas->name), 'wustho')) {
                    $kitabPrice = 200000;
                } elseif (str_contains(strtolower($kelas->name), 'ulya')) {
                    $kitabPrice = 225000;
                }
            }

            $this->billingChecklist[] = [
                'key'      => 'kitab_kelas',
                'label'    => "Paket Kitab {$kelasName}",
                'amount'   => $kitabPrice,
                'checked'  => true,
                'category' => 'kitab',
            ];
        }
    }

    public function toggleBillingItem(int $index): void
    {
        if (isset($this->billingChecklist[$index])) {
            $this->billingChecklist[$index]['checked'] = !$this->billingChecklist[$index]['checked'];
        }
    }

    public function getTotalRegistrationBillProperty(): float
    {
        if (!$this->generateBillPackage) {
            return 0.0;
        }

        $total = 0.0;
        foreach ($this->billingChecklist as $item) {
            if (!empty($item['checked'])) {
                $total += (float) $item['amount'];
            }
        }

        return $total;
    }

    // -------------------------------------------------------------------------
    // Status Change Modal States (Ubah Status Mukim ↔ Laju / Boyong)
    // -------------------------------------------------------------------------
    public bool    $showStatusModal        = false;
    public ?string $statusSantriId         = null;
    public string  $statusSantriName       = '';
    public ?string $statusSantriRoleId     = null;
    public string  $targetPresenceStatus   = 'mukim'; // 'mukim', 'laju'
    public string  $targetEnrollmentStatus = 'aktif'; // 'aktif', 'boyong', 'alumni', 'keluar_resmi'
    public string  $statusChangeNotes      = '';

    // -------------------------------------------------------------------------
    // Bulk Selection States
    // -------------------------------------------------------------------------
    public array $selectedSantriIds = [];

    // Modal: Bulk Transfer Room
    public bool    $showBulkTransferRoomModal = false;
    public ?string $bulkTargetRoomId          = null;

    // Modal: Bulk Transfer Kelas
    public bool    $showBulkTransferKelasModal = false;
    public ?string $bulkTargetKelasId          = null;

    // -------------------------------------------------------------------------
    // Form States: Tab 1 (CRUD Komplek)
    // -------------------------------------------------------------------------
    public bool    $showDormitoryModal = false;
    public ?string $editingDormitoryId = null;
    public string  $dormitoryName      = '';
    public string  $dormitoryGender    = 'L';
    public string  $dormitoryDesc      = '';
    public float   $dormitoryKasAmount = 0.0;

    // -------------------------------------------------------------------------
    // Form States: Tab 2 (CRUD Kamar)
    // -------------------------------------------------------------------------
    public bool    $showRoomModal     = false;
    public ?string $editingRoomId     = null;
    public ?string $targetDormitoryId = null;
    public string  $roomName          = '';
    public int     $roomCapacity      = 12;
    public string  $roomDesc          = '';

    // -------------------------------------------------------------------------
    // Form States: Tab 3 (CRUD Kelas)
    // -------------------------------------------------------------------------
    public bool    $showKelasModal  = false;
    public ?string $editingKelasId  = null;
    public string  $formName        = '';
    public string  $formJenjang     = 'ula';
    public string  $formAcademicYear= '';
    public ?string $formWaliKelasId = null;
    public bool    $formIsActive    = true;

    // -------------------------------------------------------------------------
    // Modal States: Transfer Single Kamar & Kelas
    // -------------------------------------------------------------------------
    public bool    $showTransferRoomModal = false;
    public ?string $transferSantriId      = null;
    public string  $transferSantriName    = '';
    public ?string $targetRoomId          = null;

    public bool    $showTransferKelasModal = false;
    public ?string $transferKelasSantriId  = null;
    public string  $transferKelasSantriName= '';
    public ?string $targetKelasId          = null;

    protected $queryString = [
        'activeTab'    => ['except' => 'komplek'],
        'search'       => ['except' => ''],
        'genderFilter' => ['except' => ''],
    ];

    public function updatingSearch(): void          { $this->resetPage(); }
    public function updatingActiveTab(): void       { $this->selectedSantriIds = []; $this->resetPage(); }
    public function updatingGenderFilter(): void    { $this->resetPage(); }
    public function updatingDormitoryFilter(): void { $this->resetPage(); }
    public function updatingKelasFilter(): void     { $this->resetPage(); }

    public function mount(): void
    {
        $currentYear = (int) now()->format('Y');
        $this->formAcademicYear = $currentYear . '/' . ($currentYear + 1);

        // Apply automatic gender scope
        $scope = $this->genderScope();
        if ($scope) {
            $this->genderFilter    = $scope;
            $this->dormitoryGender = $scope;
            $this->newSantriGender = $scope;
            $this->isGenderLocked  = true;
        }
    }

    // =========================================================================
    // Registration Methods (Santri Baru Sewaktu-waktu)
    // =========================================================================

    public function openNewSantriModal(): void
    {
        $this->newSantriNik           = '';
        $this->newSantriName          = '';
        $this->newSantriGender        = $this->genderScope() ?: 'L';
        $this->newSantriPob           = '';
        $this->newSantriDob           = '';
        $this->newSantriPhone         = '';
        $this->newSantriBloodType     = '';
        $this->newSantriFormalSchool  = '';
        $this->newSantriFormalGrade   = '';
        $this->newSantriEntryPath     = 'reguler';
        $this->newSantriFatherName    = '';
        $this->newSantriFatherPhone   = '';
        $this->newSantriFatherJob     = '';
        $this->newSantriFatherAddress = '';
        $this->newSantriMotherName    = '';
        $this->newSantriMotherPhone   = '';
        $this->newSantriMotherJob     = '';
        $this->newSantriMotherAddress        = '';
        $this->sameMotherAddress             = true;
        $this->newSantriParentAddress        = '';
        $this->newSantriGuardianName         = '';
        $this->newSantriGuardianPhone        = '';
        $this->newSantriGuardianRelationship = '';
        $this->newSantriPresence             = 'mukim';
        $this->newSantriRoomId               = null;
        $this->newSantriKelasId              = null;
        $this->generateBillPackage           = true;

        $this->refreshBillingChecklist();
        $this->showNewSantriModal = true;
    }

    public function registerNewSantri(): void
    {
        if (!auth()->user()->can('create-person')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk mendaftarkan santri baru.');
            return;
        }

        $this->validate([
            'newSantriName'   => 'required|string|min:3|max:255',
            'newSantriGender' => 'required|in:L,P',
        ]);

        $autoNis = 'S-' . date('Ym') . rand(100, 999);

        try {
            DB::transaction(function () use ($autoNis) {
                $mainAddress = $this->newSantriFatherAddress ?: ($this->newSantriMotherAddress ?: $this->newSantriParentAddress);

                // 1. Create Person
                $person = Person::create([
                    'id'          => Str::uuid()->toString(),
                    'nik'         => $this->newSantriNik ?: null,
                    'name'        => $this->newSantriName,
                    'gender'      => $this->newSantriGender,
                    'birth_place' => $this->newSantriPob ?: null,
                    'birth_date'  => $this->newSantriDob ?: null,
                    'phone'       => $this->newSantriPhone ?: null,
                    'address'     => $mainAddress ?: null,
                ]);

                // 2. Create SantriProfile
                SantriProfile::create([
                    'id'                => Str::uuid()->toString(),
                    'person_id'         => $person->id,
                    'nis'               => $autoNis,
                    'father_name'       => $this->newSantriFatherName ?: null,
                    'father_phone'      => $this->newSantriFatherPhone ?: null,
                    'father_occupation' => $this->newSantriFatherJob ?: null,
                    'mother_name'       => $this->newSantriMotherName ?: null,
                    'mother_phone'      => $this->newSantriMotherPhone ?: null,
                    'birth_city'        => $this->newSantriPob ?: null,
                    'blood_type'        => $this->newSantriBloodType ?: null,
                    'school_name'       => $this->newSantriFormalSchool ?: null,
                    'school_year'       => $this->newSantriFormalGrade ?: null,
                    'additional_info'   => [
                        'nis'                   => $autoNis,
                        'father_address'        => $this->newSantriFatherAddress ?: null,
                        'mother_job'            => $this->newSantriMotherJob ?: null,
                        'mother_address'        => $this->newSantriMotherAddress ?: null,
                        'guardian_name'         => $this->newSantriGuardianName ?: null,
                        'guardian_phone'        => $this->newSantriGuardianPhone ?: null,
                        'guardian_relationship' => $this->newSantriGuardianRelationship ?: null,
                        'address'               => $mainAddress ?: null,
                        'entry_path'            => $this->newSantriEntryPath ?: 'reguler',
                    ],
                ]);

                // 3. Create PersonRole
                $orgSlug = $this->newSantriGender === 'P' ? 'kepengasuhan-putri' : 'kepengasuhan-putra';
                $org = \App\Modules\Core\Models\Organization::where('slug', $orgSlug)->first()
                    ?? \App\Modules\Core\Models\Organization::where('slug', 'ponpes-al-fithroh')->first()
                    ?? \App\Modules\Core\Models\Organization::first();

                PersonRole::create([
                    'id'                    => Str::uuid()->toString(),
                    'person_id'             => $person->id,
                    'organization_id'       => $org->id,
                    'role_type'             => 'santri',
                    'enrollment_status'     => 'aktif',
                    'presence_status'       => $this->newSantriPresence,
                    'presence_status_since' => now(),
                    'is_active'             => true,
                ]);

                // 4. Assign Room if Mukim & Room selected
                if ($this->newSantriPresence === 'mukim' && $this->newSantriRoomId) {
                    app(DormitoryService::class)->assignRoom($this->newSantriRoomId, $person->id, now()->toDateString());
                }

                // 5. Enroll in Kelas if selected
                if ($this->newSantriKelasId) {
                    MadrasahEnrollment::create([
                        'person_id'     => $person->id,
                        'kelas_id'      => $this->newSantriKelasId,
                        'academic_year' => $this->formAcademicYear,
                        'is_active'     => true,
                        'created_by'    => auth()->id(),
                    ]);
                }

                // 6. Generate Custom Registration Package Bill based on checked items
                if ($this->generateBillPackage && !empty($this->billingChecklist)) {
                    $checkedItems = array_filter($this->billingChecklist, fn($item) => !empty($item['checked']));
                    if (!empty($checkedItems)) {
                        \App\Modules\Keuangan\Models\Bill::create([
                            'id'         => Str::uuid()->toString(),
                            'person_id'  => $person->id,
                            'bill_type'  => 'pendaftaran',
                            'title'      => 'Tagihan Registrasi Santri Baru - ' . $person->name,
                            'amount'     => $this->totalRegistrationBill,
                            'status'     => 'unpaid',
                            'due_date'   => now()->addMonths(3)->toDateString(),
                            'notes'      => 'Paket Pendaftaran Santri Baru (' . strtoupper($this->newSantriEntryPath) . ')',
                            'created_by' => auth()->id(),
                        ]);
                    }
                }
            });

            $this->toastSuccess("Santri baru {$this->newSantriName} (NIS: {$autoNis}) berhasil didaftarkan.");
            $this->showNewSantriModal = false;
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    // =========================================================================
    // Status Change Methods (Mukim ↔ Laju / Boyong)
    // =========================================================================

    public function openStatusModal(string $santriId): void
    {
        $person = Person::with('activeRoles')->findOrFail($santriId);
        $role   = $person->activeRoles->firstWhere('role_type', 'santri');

        if (!$role) {
            $this->toastError('Role santri tidak ditemukan.');
            return;
        }

        $this->statusSantriId         = $santriId;
        $this->statusSantriName       = $person->name;
        $this->statusSantriRoleId     = $role->id;
        $this->targetPresenceStatus   = $role->presence_status ?? 'mukim';
        $this->targetEnrollmentStatus = $role->enrollment_status ?? 'aktif';
        $this->statusChangeNotes      = '';

        $this->showStatusModal = true;
    }

    // Custom Confirmation Dialog Properties
    public bool   $showConfirmModal     = false;
    public string $confirmTitle         = '';
    public string $confirmMessage       = '';
    public string $confirmAction        = '';
    public string $confirmButtonText    = 'Ya, Lanjutkan';
    public string $confirmButtonColor   = 'emerald';

    // Delete & Toggle Target IDs
    public ?string $deletingDormitoryId = null;
    public ?string $deletingRoomId      = null;
    public ?string $deletingKelasId     = null;
    public ?string $togglingDormitoryId = null;
    public ?string $togglingRoomId      = null;

    public function requestStatusChangeConfirm(): void
    {
        $this->confirmAction      = 'executeStatusChange';
        $this->confirmTitle       = 'Konfirmasi Perubahan Status Santri';
        $this->confirmMessage     = "Apakah Anda YAKIN ingin mengubah status santri {$this->statusSantriName}? Perubahan status keanggotaan/keberadaan akan memengaruhi alokasi kamar & kelas aktif santri.";
        $this->confirmButtonText  = 'Ya, Ubah Status Santri';
        $this->confirmButtonColor = 'amber';
        $this->showConfirmModal   = true;
    }

    public function requestBulkRoomTransferConfirm(): void
    {
        $this->validate(['bulkTargetRoomId' => 'required|uuid|exists:rooms,id']);
        $count = count($this->selectedSantriIds);
        $this->confirmAction      = 'executeBulkTransferRoom';
        $this->confirmTitle       = 'Konfirmasi Pemindahan Kamar Massal';
        $this->confirmMessage     = "Apakah Anda YAKIN ingin memindahkan {$count} santri terpilih secara serentak ke kamar baru?";
        $this->confirmButtonText  = 'Ya, Pindahkan Massal';
        $this->confirmButtonColor = 'emerald';
        $this->showConfirmModal   = true;
    }

    public function requestBulkKelasTransferConfirm(): void
    {
        $this->validate(['bulkTargetKelasId' => 'required|uuid|exists:madrasah_kelas,id']);
        $count = count($this->selectedSantriIds);
        $this->confirmAction      = 'executeBulkTransferKelas';
        $this->confirmTitle       = 'Konfirmasi Pemindahan Kelas Massal';
        $this->confirmMessage     = "Apakah Anda YAKIN ingin memindahkan {$count} santri terpilih secara serentak ke kelas madrasah baru?";
        $this->confirmButtonText  = 'Ya, Pindahkan Massal';
        $this->confirmButtonColor = 'indigo';
        $this->showConfirmModal   = true;
    }

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

        if ($this->confirmAction === 'executeStatusChange') {
            $this->executeStatusChange();
        } elseif ($this->confirmAction === 'executeBulkTransferRoom') {
            $this->executeBulkTransferRoom();
        } elseif ($this->confirmAction === 'executeBulkTransferKelas') {
            $this->executeBulkTransferKelas();
        } elseif ($this->confirmAction === 'executeTransferRoom') {
            $this->executeTransferRoom();
        } elseif ($this->confirmAction === 'executeTransferKelas') {
            $this->executeTransferKelas();
        } elseif ($this->confirmAction === 'executeDeleteDormitory') {
            $this->executeDeleteDormitory();
        } elseif ($this->confirmAction === 'executeDeleteRoom') {
            $this->executeDeleteRoom();
        } elseif ($this->confirmAction === 'executeToggleDormitoryStatus') {
            $this->executeToggleDormitoryStatus();
        } elseif ($this->confirmAction === 'executeToggleRoomStatus') {
            $this->executeToggleRoomStatus();
        } elseif ($this->confirmAction === 'executeDeleteKelas') {
            $this->executeDeleteKelas();
        }
    }

    public function executeStatusChange(): void
    {
        if (!auth()->user()->can('change-enrollment-status') && !auth()->user()->can('change-presence-status')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk mengubah status santri.');
            return;
        }

        if (!$this->statusSantriRoleId) return;

        try {
            $statusService = app(SantriStatusService::class);

            // 1. Update Enrollment status if changed
            $role = PersonRole::find($this->statusSantriRoleId);
            if ($role && $role->enrollment_status !== $this->targetEnrollmentStatus) {
                $statusService->changeEnrollmentStatus(
                    $this->statusSantriRoleId,
                    $this->targetEnrollmentStatus,
                    auth()->id(),
                    $this->statusChangeNotes ?: 'Perubahan status via Pusat Kendali'
                );
            }

            // 2. Update Presence status if active and changed
            $role = PersonRole::find($this->statusSantriRoleId);
            if ($role && $role->isActiveEnrollment() && $role->presence_status !== $this->targetPresenceStatus) {
                $statusService->changePresenceStatus(
                    $this->statusSantriRoleId,
                    $this->targetPresenceStatus,
                    auth()->id(),
                    null,
                    $this->statusChangeNotes ?: 'Perubahan status via Pusat Kendali'
                );
            }

            $this->toastSuccess("Status santri {$this->statusSantriName} berhasil diperbarui.");
            $this->showStatusModal = false;
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    // =========================================================================
    // Bulk Selection Methods
    // =========================================================================

    public function clearSelection(): void
    {
        $this->selectedSantriIds = [];
    }

    public function selectAllInRoom(string $roomId): void
    {
        $room = Room::with('currentAssignments')->find($roomId);
        if (!$room) return;

        $roomSantriIds = $room->currentAssignments->pluck('person_id')->toArray();
        $this->selectedSantriIds = array_unique(array_merge($this->selectedSantriIds, $roomSantriIds));
    }

    public function selectAllInKelas(string $kelasId): void
    {
        $kelas = MadrasahKelas::with(['enrollments' => fn($q) => $q->where('is_active', true)])->find($kelasId);
        if (!$kelas) return;

        $kelasSantriIds = $kelas->enrollments->pluck('person_id')->toArray();
        $this->selectedSantriIds = array_unique(array_merge($this->selectedSantriIds, $kelasSantriIds));
    }

    public function openBulkTransferRoomModal(): void
    {
        if (empty($this->selectedSantriIds)) {
            $this->toastError('Pilih minimal satu santri terlebih dahulu.');
            return;
        }
        $this->bulkTargetRoomId = null;
        $this->showBulkTransferRoomModal = true;
    }

    public function executeBulkTransferRoom(): void
    {
        if (!auth()->user()->can('manage-kamar')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk memindahkan santri antar kamar.');
            return;
        }

        $this->validate([
            'bulkTargetRoomId' => 'required|uuid|exists:rooms,id',
        ]);

        $targetRoom = Room::with('dormitory', 'currentAssignments')->findOrFail($this->bulkTargetRoomId);
        $countSelected = count($this->selectedSantriIds);
        $remainingCap = $targetRoom->capacity - $targetRoom->currentAssignments->count();

        if ($countSelected > $remainingCap) {
            $this->toastError("Kapasitas sisa kamar {$targetRoom->name} ({$remainingCap} bed) tidak cukup untuk {$countSelected} santri terpilih.");
            return;
        }

        $service = app(DormitoryService::class);
        $successCount = 0;

        DB::transaction(function () use ($service, &$successCount) {
            foreach ($this->selectedSantriIds as $santriId) {
                try {
                    $service->assignRoom($this->bulkTargetRoomId, $santriId, now()->toDateString());
                    $successCount++;
                } catch (\Exception $e) {
                    // Log or handle individual failure
                }
            }
        });

        if ($successCount > 0) {
            $this->toastSuccess("Berhasil memindahkan {$successCount} santri ke kamar {$targetRoom->name}.");
            $this->clearSelection();
            $this->showBulkTransferRoomModal = false;
        } else {
            $this->toastError('Gagal memindahkan santri terpilih.');
        }
    }

    public function openBulkTransferKelasModal(): void
    {
        if (empty($this->selectedSantriIds)) {
            $this->toastError('Pilih minimal satu santri terlebih dahulu.');
            return;
        }
        $this->bulkTargetKelasId = null;
        $this->showBulkTransferKelasModal = true;
    }

    public function executeBulkTransferKelas(): void
    {
        if (!auth()->user()->can('manage-kelas')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk memindahkan santri antar kelas.');
            return;
        }

        $this->validate([
            'bulkTargetKelasId' => 'required|uuid|exists:madrasah_kelas,id',
        ]);

        $targetKelas = MadrasahKelas::findOrFail($this->bulkTargetKelasId);
        $academicYear = $targetKelas->academic_year ?: $this->formAcademicYear;
        $successCount = 0;

        DB::transaction(function () use ($targetKelas, $academicYear, &$successCount) {
            foreach ($this->selectedSantriIds as $santriId) {
                MadrasahEnrollment::where('person_id', $santriId)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);

                MadrasahEnrollment::updateOrCreate(
                    [
                        'person_id'     => $santriId,
                        'kelas_id'      => $this->bulkTargetKelasId,
                        'academic_year' => $academicYear,
                    ],
                    [
                        'is_active'  => true,
                        'created_by' => auth()->id(),
                    ]
                );
                $successCount++;
            }
        });

        $this->toastSuccess("Berhasil memindahkan {$successCount} santri ke kelas {$targetKelas->name}.");
        $this->clearSelection();
        $this->showBulkTransferKelasModal = false;
    }

    // =========================================================================
    // CRUD Komplek Methods
    // =========================================================================

    public function openCreateDormitoryModal(): void
    {
        $this->reset(['editingDormitoryId', 'dormitoryName', 'dormitoryDesc', 'dormitoryKasAmount']);
        $this->dormitoryGender   = $this->isGenderLocked ? $this->genderFilter : 'L';
        $this->showDormitoryModal = true;
    }

    public function openEditDormitoryModal(string $id): void
    {
        $dormitory = Dormitory::findOrFail($id);
        $this->editingDormitoryId = $id;
        $this->dormitoryName      = $dormitory->name;
        $this->dormitoryGender    = $dormitory->gender;
        $this->dormitoryDesc      = $dormitory->description ?? '';
        $this->dormitoryKasAmount = (float) ($dormitory->kas_komplek_amount ?? 0);
        $this->showDormitoryModal = true;
    }

    public function saveDormitory(): void
    {
        if (!auth()->user()->can('manage-asrama')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk mengelola data komplek/asrama.');
            return;
        }

        $this->validate([
            'dormitoryName'      => 'required|string|max:100',
            'dormitoryGender'    => 'required|in:L,P',
            'dormitoryDesc'      => 'nullable|string|max:500',
            'dormitoryKasAmount' => 'required|numeric|min:0',
        ]);

        $service = app(DormitoryService::class);

        if ($this->editingDormitoryId) {
            $dormitory = Dormitory::findOrFail($this->editingDormitoryId);
            $dormitory->update([
                'name'               => $this->dormitoryName,
                'gender'             => $this->dormitoryGender,
                'description'        => $this->dormitoryDesc ?: null,
                'kas_komplek_amount' => $this->dormitoryKasAmount,
            ]);
            $this->toastSuccess('Data komplek berhasil diperbarui.');
        } else {
            $service->createDormitory([
                'name'               => $this->dormitoryName,
                'gender'             => $this->dormitoryGender,
                'description'        => $this->dormitoryDesc ?: null,
                'kas_komplek_amount' => $this->dormitoryKasAmount,
                'is_active'          => true,
            ]);
            $this->toastSuccess('Komplek baru berhasil ditambahkan.');
        }

        $this->showDormitoryModal = false;
    }

    public function requestToggleDormitoryStatusConfirm(string $id): void
    {
        $dormitory = Dormitory::findOrFail($id);
        $statusNext = $dormitory->is_active ? 'Nonaktifkan' : 'Aktifkan';
        $this->togglingDormitoryId = $id;
        $this->confirmAction       = 'executeToggleDormitoryStatus';
        $this->confirmTitle        = "{$statusNext} Komplek Asrama";
        $this->confirmMessage      = "Apakah Anda yakin ingin me-{$dormitory->is_active ? 'nonaktifkan' : 'ngaktifkan'} komplek \"{$dormitory->name}\"?";
        $this->confirmButtonText   = "Ya, {$statusNext}";
        $this->confirmButtonColor  = $dormitory->is_active ? 'amber' : 'emerald';
        $this->showConfirmModal    = true;
    }

    public function executeToggleDormitoryStatus(): void
    {
        if (!auth()->user()->can('manage-asrama')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk mengelola data komplek/asrama.');
            return;
        }

        if ($this->togglingDormitoryId) {
            app(DormitoryService::class)->toggleDormitoryStatus($this->togglingDormitoryId);
            $this->togglingDormitoryId = null;
            $this->toastSuccess('Status komplek berhasil diperbarui.');
        }
    }

    public function requestDeleteDormitoryConfirm(string $id): void
    {
        $dormitory = Dormitory::withCount('rooms')->findOrFail($id);

        if ($dormitory->rooms_count > 0) {
            $this->toastError("Komplek \"{$dormitory->name}\" tidak bisa dihapus karena masih memiliki {$dormitory->rooms_count} kamar. Hapus semua kamar terlebih dahulu.");
            return;
        }

        $this->deletingDormitoryId = $id;
        $this->confirmAction       = 'executeDeleteDormitory';
        $this->confirmTitle        = 'Hapus Komplek Asrama';
        $this->confirmMessage      = "Anda akan menghapus komplek \"{$dormitory->name}\" secara permanen. Tindakan ini tidak dapat dibatalkan.";
        $this->confirmButtonText   = 'Ya, Hapus Permanen';
        $this->confirmButtonColor  = 'rose';
        $this->showConfirmModal    = true;
    }

    public function executeDeleteDormitory(): void
    {
        if (!auth()->user()->can('manage-asrama')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk menghapus komplek.');
            return;
        }

        $dormitory = Dormitory::withCount('rooms')->findOrFail($this->deletingDormitoryId);

        if ($dormitory->rooms_count > 0) {
            $this->toastError("Komplek \"{$dormitory->name}\" tidak bisa dihapus karena masih memiliki {$dormitory->rooms_count} kamar.");
            return;
        }

        $name = $dormitory->name;
        $dormitory->delete();
        $this->deletingDormitoryId = null;
        $this->toastSuccess("Komplek \"{$name}\" berhasil dihapus.");
    }

    // =========================================================================
    // CRUD Kamar Methods
    // =========================================================================

    public function openCreateRoomModal(?string $dormitoryId = null): void
    {
        $this->reset(['editingRoomId', 'roomName', 'roomCapacity', 'roomDesc']);
        $this->targetDormitoryId = $dormitoryId ?: (Dormitory::first()?->id ?? null);
        $this->roomCapacity      = 12;
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
        if (!auth()->user()->can('manage-kamar')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk mengelola data kamar.');
            return;
        }

        $this->validate([
            'targetDormitoryId' => 'required|uuid|exists:dormitories,id',
            'roomName'          => 'required|string|max:100',
            'roomCapacity'      => 'required|integer|min:1|max:100',
            'roomDesc'          => 'nullable|string|max:500',
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

    public function requestToggleRoomStatusConfirm(string $id): void
    {
        $room = Room::findOrFail($id);
        $statusNext = $room->is_active ? 'Nonaktifkan' : 'Aktifkan';
        $this->togglingRoomId     = $id;
        $this->confirmAction      = 'executeToggleRoomStatus';
        $this->confirmTitle       = "{$statusNext} Kamar";
        $this->confirmMessage     = "Apakah Anda yakin ingin me-{$room->is_active ? 'nonaktifkan' : 'ngaktifkan'} kamar \"{$room->name}\"?";
        $this->confirmButtonText  = "Ya, {$statusNext}";
        $this->confirmButtonColor = $room->is_active ? 'amber' : 'emerald';
        $this->showConfirmModal   = true;
    }

    public function executeToggleRoomStatus(): void
    {
        if (!auth()->user()->can('manage-kamar')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk mengelola data kamar.');
            return;
        }

        if ($this->togglingRoomId) {
            app(DormitoryService::class)->toggleRoomStatus($this->togglingRoomId);
            $this->togglingRoomId = null;
            $this->toastSuccess('Status kamar berhasil diperbarui.');
        }
    }

    public function requestDeleteRoomConfirm(string $id): void
    {
        $room      = Room::with(['currentAssignments', 'dormitory'])->findOrFail($id);
        $occupants = $room->currentAssignments->count();

        if ($occupants > 0) {
            $this->toastError("Kamar \"{$room->name}\" tidak bisa dihapus karena masih dihuni oleh {$occupants} santri aktif.");
            return;
        }

        $this->deletingRoomId     = $id;
        $this->confirmAction      = 'executeDeleteRoom';
        $this->confirmTitle       = 'Hapus Kamar';
        $this->confirmMessage     = "Anda akan menghapus kamar \"{$room->name}\" di komplek {$room->dormitory->name} secara permanen. Tindakan ini tidak dapat dibatalkan.";
        $this->confirmButtonText  = 'Ya, Hapus Permanen';
        $this->confirmButtonColor = 'rose';
        $this->showConfirmModal   = true;
    }

    public function executeDeleteRoom(): void
    {
        if (!auth()->user()->can('manage-kamar')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk menghapus kamar.');
            return;
        }

        $room      = Room::with('currentAssignments')->findOrFail($this->deletingRoomId);
        $occupants = $room->currentAssignments->count();

        if ($occupants > 0) {
            $this->toastError("Kamar \"{$room->name}\" tidak bisa dihapus karena masih dihuni oleh {$occupants} santri aktif.");
            return;
        }

        $name = $room->name;
        $room->delete();
        $this->deletingRoomId = null;
        $this->toastSuccess("Kamar \"{$name}\" berhasil dihapus.");
    }

    // =========================================================================
    // CRUD Kelas Methods
    // =========================================================================

    public function openCreateKelasModal(): void
    {
        $this->reset(['editingKelasId', 'formName', 'formWaliKelasId']);
        $this->formJenjang   = 'ula';
        $this->formIsActive  = true;
        $this->showKelasModal= true;
    }

    public function openEditKelasModal(string $id): void
    {
        $kelas = MadrasahKelas::findOrFail($id);
        $this->editingKelasId   = $id;
        $this->formName         = $kelas->name;
        $this->formJenjang      = $kelas->jenjang;
        $this->formAcademicYear = $kelas->academic_year;
        $this->formWaliKelasId  = $kelas->wali_kelas_id;
        $this->formIsActive     = $kelas->is_active;
        $this->showKelasModal   = true;
    }

    public function saveKelas(): void
    {
        if (!auth()->user()->can('manage-kelas')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk mengelola data kelas.');
            return;
        }

        $this->validate([
            'formName'         => 'required|string|max:100',
            'formJenjang'      => 'required|in:ula,wustho,ulya',
            'formAcademicYear' => 'required|string|max:20',
        ]);

        $data = [
            'name'          => $this->formName,
            'jenjang'       => $this->formJenjang,
            'academic_year' => $this->formAcademicYear,
            'wali_kelas_id' => $this->formWaliKelasId ?: null,
            'is_active'     => $this->formIsActive,
            'created_by'    => auth()->id(),
        ];

        if ($this->editingKelasId) {
            MadrasahKelas::findOrFail($this->editingKelasId)->update($data);
            $this->toastSuccess('Data kelas berhasil diperbarui.');
        } else {
            MadrasahKelas::create($data);
            $this->toastSuccess('Kelas baru berhasil ditambahkan.');
        }

        $this->showKelasModal = false;
    }

    public function requestDeleteKelasConfirm(string $id): void
    {
        $kelas = MadrasahKelas::findOrFail($id);
        if ($kelas->enrollments()->exists()) {
            $this->toastError("Kelas \"{$kelas->name}\" tidak bisa dihapus karena masih ada santri terdaftar.");
            return;
        }

        $this->deletingKelasId    = $id;
        $this->confirmAction      = 'executeDeleteKelas';
        $this->confirmTitle       = 'Hapus Kelas Madrasah';
        $this->confirmMessage     = "Anda akan menghapus kelas \"{$kelas->name}\" secara permanen. Tindakan ini tidak dapat dibatalkan.";
        $this->confirmButtonText  = 'Ya, Hapus Permanen';
        $this->confirmButtonColor = 'rose';
        $this->showConfirmModal   = true;
    }

    public function executeDeleteKelas(): void
    {
        if (!auth()->user()->can('manage-kelas')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk menghapus kelas.');
            return;
        }

        if ($this->deletingKelasId) {
            $kelas = MadrasahKelas::findOrFail($this->deletingKelasId);
            if ($kelas->enrollments()->exists()) {
                $this->toastError("Kelas \"{$kelas->name}\" tidak bisa dihapus karena masih ada santri terdaftar.");
                return;
            }
            $name = $kelas->name;
            $kelas->delete();
            $this->deletingKelasId = null;
            $this->toastSuccess("Kelas \"{$name}\" berhasil dihapus.");
        }
    }

    // =========================================================================
    // Transfer Single Methods
    // =========================================================================

    public function openTransferRoomModal(string $santriId): void
    {
        $person = Person::findOrFail($santriId);
        $this->transferSantriId   = $santriId;
        $this->transferSantriName = $person->name;
        $this->targetRoomId       = null;
        $this->showTransferRoomModal = true;
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
            $this->toastSuccess("Santri {$this->transferSantriName} berhasil dipindahkan ke kamar baru.");
            $this->showTransferRoomModal = false;
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function openTransferKelasModal(string $santriId): void
    {
        $person = Person::findOrFail($santriId);
        $this->transferKelasSantriId   = $santriId;
        $this->transferKelasSantriName = $person->name;
        $this->targetKelasId           = null;
        $this->showTransferKelasModal  = true;
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
            $targetKelas = MadrasahKelas::findOrFail($this->targetKelasId);
            $academicYear = $targetKelas->academic_year ?: $this->formAcademicYear;

            MadrasahEnrollment::where('person_id', $this->transferKelasSantriId)
                ->where('is_active', true)
                ->update(['is_active' => false]);

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

            $this->toastSuccess("Santri {$this->transferKelasSantriName} berhasil mendaftar ke kelas baru.");
            $this->showTransferKelasModal = false;
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    // =========================================================================
    // Render Method
    // =========================================================================

    public function render()
    {
        // 1. Data Tab Komplek (CRUD Komplek)
        $dormitoriesList = Dormitory::query()
            ->when($this->genderFilter, fn($q) => $q->where('gender', $this->genderFilter))
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->withCount('rooms')
            ->paginate(10);

        // 2. Data Tab Kamar (CRUD Kamar)
        $roomsList = Room::query()
            ->with(['dormitory', 'currentAssignments'])
            ->when($this->genderFilter, fn($q) => $q->whereHas('dormitory', fn($dq) => $dq->where('gender', $this->genderFilter)))
            ->when($this->dormitoryFilter, fn($q) => $q->where('dormitory_id', $this->dormitoryFilter))
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->paginate(12);

        // 3. Data Tab Kelas (CRUD Kelas)
        $kelasList = MadrasahKelas::query()
            ->with('waliKelas')
            ->withCount('enrollments')
            ->when($this->genderFilter === 'L', fn($q) => $q->where(fn($sq) => $sq->where('name', 'like', '%(Pa)%')->orWhere('name', 'like', '%Pa%')->orWhere('name', 'like', '%Putra%')->orWhere('name', 'like', '%(L)%')->orWhereHas('enrollments.person', fn($pq) => $pq->where('gender', 'L'))))
            ->when($this->genderFilter === 'P', fn($q) => $q->where(fn($sq) => $sq->where('name', 'like', '%(Pi)%')->orWhere('name', 'like', '%Pi%')->orWhere('name', 'like', '%Putri%')->orWhere('name', 'like', '%(P)%')->orWhereHas('enrollments.person', fn($pq) => $pq->where('gender', 'P'))))
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('jenjang')
            ->orderBy('name')
            ->paginate(12);

        // 4. Data Bagan Komplek & Kamar
        $baganKomplekData = collect();
        if ($this->activeTab === 'bagan-komplek') {
            $baganKomplekData = Dormitory::active()
                ->when($this->genderFilter, fn($q) => $q->where('gender', $this->genderFilter))
                ->when($this->dormitoryFilter, fn($q) => $q->where('id', $this->dormitoryFilter))
                ->with(['rooms' => function ($rq) {
                    $rq->active()->orderByRaw('LENGTH(name) ASC, name ASC')->with(['currentAssignments' => function ($caq) {
                        $caq->with(['person.santriProfile', 'person.activeRoles', 'person.madrasahEnrollments.kelas']);
                    }]);
                }])
                ->get();
        }

        // 5. Data Bagan Kelas Madrasah
        $baganKelasData = collect();
        if ($this->activeTab === 'bagan-kelas') {
            $baganKelasData = MadrasahKelas::where('is_active', true)
                ->when($this->genderFilter === 'L', fn($q) => $q->where(fn($sq) => $sq->where('name', 'like', '%(Pa)%')->orWhere('name', 'like', '%Pa%')->orWhere('name', 'like', '%Putra%')->orWhere('name', 'like', '%(L)%')->orWhereHas('enrollments.person', fn($pq) => $pq->where('gender', 'L'))))
                ->when($this->genderFilter === 'P', fn($q) => $q->where(fn($sq) => $sq->where('name', 'like', '%(Pi)%')->orWhere('name', 'like', '%Pi%')->orWhere('name', 'like', '%Putri%')->orWhere('name', 'like', '%(P)%')->orWhereHas('enrollments.person', fn($pq) => $pq->where('gender', 'P'))))
                ->when($this->kelasFilter, fn($q) => $q->where('id', $this->kelasFilter))
                ->with(['waliKelas', 'enrollments' => function ($eq) {
                    $eq->where('is_active', true)->with(['person.santriProfile', 'person.activeRoles', 'person.roomAssignments.room.dormitory']);
                }])
                ->orderBy('jenjang')
                ->orderBy('name')
                ->get();
        }

        // Shared Options
        $registrationGender = $this->showNewSantriModal ? $this->newSantriGender : ($this->genderFilter ?: null);

        $dormitoryOptions = Dormitory::active()
            ->when($this->genderFilter, fn($q) => $q->where('gender', $this->genderFilter))
            ->get();

        $roomOptions = Room::active()
            ->with('dormitory')
            ->when($registrationGender, fn($q) => $q->whereHas('dormitory', fn($dq) => $dq->where('gender', $registrationGender)))
            ->get();

        $kelasOptions = MadrasahKelas::where('is_active', true)
            ->when($registrationGender === 'L', fn($q) => $q->where(fn($sq) => $sq->where('name', 'like', '%(Pa)%')->orWhere('name', 'like', '%Pa%')->orWhere('name', 'like', '%Putra%')->orWhere('name', 'like', '%(L)%')->orWhereHas('enrollments.person', fn($pq) => $pq->where('gender', 'L'))))
            ->when($registrationGender === 'P', fn($q) => $q->where(fn($sq) => $sq->where('name', 'like', '%(Pi)%')->orWhere('name', 'like', '%Pi%')->orWhere('name', 'like', '%Putri%')->orWhere('name', 'like', '%(P)%')->orWhereHas('enrollments.person', fn($pq) => $pq->where('gender', 'P'))))
            ->orderBy('jenjang')
            ->orderBy('name')
            ->get();
        $guruOptions  = Person::whereHas('activeRoles', fn($q) => $q->where('role_type', 'guru'))->orderBy('name')->get();

        // Selected Santri List for Modal Info
        $selectedSantriList = collect();
        if (!empty($this->selectedSantriIds)) {
            $selectedSantriList = Person::whereIn('id', $this->selectedSantriIds)->get();
        }

        return view('livewire.kepengasuhan.pusat-kendali-asrama-kelas', [
            'dormitoriesList'    => $dormitoriesList,
            'roomsList'          => $roomsList,
            'kelasList'          => $kelasList,
            'baganKomplekData'   => $baganKomplekData,
            'baganKelasData'     => $baganKelasData,
            'dormitoryOptions'   => $dormitoryOptions,
            'roomOptions'        => $roomOptions,
            'kelasOptions'       => $kelasOptions,
            'guruOptions'        => $guruOptions,
            'selectedSantriList' => $selectedSantriList,
        ])->layout('layouts.app');
    }
}
