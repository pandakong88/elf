<?php

namespace App\Livewire\Kepengasuhan;

use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\CensusPeriod;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\DormitoryCensus;
use App\Modules\Kepengasuhan\Models\RoomCensusDetail;
use App\Modules\Kepengasuhan\Models\SantriProfile;
use App\Modules\Kepengasuhan\Services\CensusService;
use App\Modules\Kepengasuhan\Services\CensusExcelService;
use Livewire\Component;
use App\Livewire\Concerns\SendsToast;
use Livewire\WithFileUploads;

class CensusManager extends Component
{
    use SendsToast;

    use WithFileUploads;

    // =========================================================================
    // State Umum
    // =========================================================================
    public $view = 'list'; // list | fill | review

    // =========================================================================
    // Modal: Buat Periode Baru
    // =========================================================================
    public $showCreatePeriodModal = false;
    public $periodName            = '';
    public $periodMonth;
    public $periodYear;

    // =========================================================================
    // Modal: Konfirmasi Aksi
    // =========================================================================
    public $showConfirmModal  = false;
    public $confirmTitle      = '';
    public $confirmMessage    = '';
    public $confirmAction     = '';
    public $confirmPayload    = null;
    public $confirmVariant    = 'danger'; // danger | success | warning

    // =========================================================================
    // Modal: Reject / Penolakan
    // =========================================================================
    public $showRejectModal        = false;
    public $rejectCensusId         = null;
    public $rejectNotes            = '';

    // =========================================================================
    // Mode: Pengisian Sensus (View: fill)
    // =========================================================================
    public $activeDormitoryCensusId = null;
    public $censusData              = []; // person_id => ['status', 'notes', 'profile_updates', 'guardian_updates']
    public $expandedPersonId        = null; // untuk collapsible profil panel
    public $excelFile; // untuk file upload
    public $fillTab                 = 'form'; // form | excel
    public $onlyShowExceptions      = false; // filter untuk menyembunyikan yang "Hadir" dan tanpa perubahan

    // =========================================================================
    // Mode: Review Setoran (View: review) - untuk pengurus pusat
    // =========================================================================
    public $reviewCensusId = null;

    // =========================================================================
    // Filter pada halaman list
    // =========================================================================
    public $selectedPeriodId = null;

    // =========================================================================
    // Lifecycle
    // =========================================================================

    public function mount(): void
    {
        $this->periodMonth = now()->month;
        $this->periodYear  = now()->year;

        // Default pilih periode aktif atau terbaru
        $activePeriod = CensusPeriod::where('status', 'active')->latest()->first()
            ?? CensusPeriod::latest()->first();

        if ($activePeriod) {
            $this->selectedPeriodId = $activePeriod->id;
        }
    }

    // =========================================================================
    // Create Period
    // =========================================================================

    public function openCreatePeriodModal(): void
    {
        $this->reset(['periodName', 'periodMonth', 'periodYear']);
        $this->periodMonth         = now()->month;
        $this->periodYear          = now()->year;
        $this->showCreatePeriodModal = true;
    }

    public function createPeriod(): void
    {
        $this->validate([
            'periodName'  => 'required|string|max:100',
            'periodMonth' => 'required|integer|min:1|max:12',
            'periodYear'  => 'required|integer|min:2020|max:2099',
        ]);

        $service = app(CensusService::class);
        try {
            $period = $service->createPeriod(
                $this->periodName,
                (int) $this->periodMonth,
                (int) $this->periodYear,
                auth()->id()
            );
            $this->selectedPeriodId    = $period->id;
            $this->showCreatePeriodModal = false;
            $this->toastSuccess("Periode sensus \"{$this->periodName}\" berhasil dibuat. Siap untuk diaktifkan.");
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    // =========================================================================
    // Period Lifecycle
    // =========================================================================

    public function confirmStartPeriod(string $periodId): void
    {
        $period = CensusPeriod::findOrFail($periodId);
        $this->confirmTitle    = 'Aktifkan Periode Sensus';
        $this->confirmMessage  = "Aktifkan periode <strong>{$period->name}</strong>? Musyrif komplek akan mulai dapat mengisi laporan sensus.";
        $this->confirmAction   = 'startPeriod';
        $this->confirmPayload  = $periodId;
        $this->confirmVariant  = 'success';
        $this->showConfirmModal = true;
    }

    public function startPeriod(): void
    {
        try {
            app(CensusService::class)->startPeriod($this->confirmPayload);
            $this->toastSuccess('Periode sensus berhasil diaktifkan. Musyrif komplek sudah dapat mengisi laporan.');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
        $this->closeConfirmModal();
    }

    public function confirmClosePeriod(string $periodId): void
    {
        $period = CensusPeriod::findOrFail($periodId);
        $this->confirmTitle    = 'Tutup Periode Sensus';
        $this->confirmMessage  = "Tutup periode <strong>{$period->name}</strong>? Pengisian laporan akan ditutup.";
        $this->confirmAction   = 'closePeriod';
        $this->confirmPayload  = $periodId;
        $this->confirmVariant  = 'warning';
        $this->showConfirmModal = true;
    }

    public function closePeriod(): void
    {
        try {
            app(CensusService::class)->closePeriod($this->confirmPayload);
            $this->toastSuccess('Periode sensus telah ditutup.');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
        $this->closeConfirmModal();
    }

    // =========================================================================
    // Fill Census (Musyrif Komplek)
    // =========================================================================

    public function openFillView(string $dormitoryCensusId): void
    {
        $dc = DormitoryCensus::with(['dormitory.rooms.currentAssignments.person.santriProfile.guardians'])->findOrFail($dormitoryCensusId);

        $this->activeDormitoryCensusId = $dormitoryCensusId;
        $this->censusData              = [];
        $this->expandedPersonId        = null;
        $this->fillTab                 = 'form';
        $this->excelFile               = null;

        // Pre-populate dari data yang sudah ada, atau buat defaults
        $existingDetails = $dc->details()->get()->keyBy('person_id');

        foreach ($dc->dormitory->rooms as $room) {
            foreach ($room->currentAssignments as $assignment) {
                $personId   = $assignment->person_id;
                $existing   = $existingDetails->get($personId);
                $profile    = $assignment->person->santriProfile;
                
                // Cari wali lainnya
                $otherGuardian = $profile ? $profile->guardians->filter(fn($g) => !in_array($g->pivot->relationship, ['ayah_kandung', 'ibu_kandung']))->first() : null;

                $this->censusData[$personId] = [
                    'room_id'         => $room->id,
                    'room_name'       => $room->name,
                    'person_name'     => $assignment->person->name,
                    'status'          => $existing?->status          ?? 'present',
                    'notes'           => $existing?->notes           ?? '',
                    
                    'profile_updates' => $existing?->profile_updates ?? [
                        'school_status'      => $profile?->school_status      ?? 'mondok_full',
                        'school_name'        => $profile?->school_name        ?? '',
                        'school_type'        => $profile?->school_type        ?? '',
                        'major'              => $profile?->major              ?? '',
                        'school_year'        => $profile?->school_year        ?? '',
                        'medical_history'    => $profile?->medical_history    ?? '',
                        'blood_type'         => $profile?->blood_type         ?? '',
                        'allergies'          => $profile?->allergies          ?? '',
                        'special_conditions' => $profile?->special_conditions ?? '',
                        'father_name'        => $profile?->father_name        ?? '',
                        'father_phone'       => $profile?->father_phone       ?? '',
                        'father_occupation'  => $profile?->father_occupation  ?? '',
                        'mother_name'        => $profile?->mother_name        ?? '',
                        'mother_phone'       => $profile?->mother_phone       ?? '',
                        'sibling' => [
                            'name' => '',
                            'relationship' => 'saudara',
                            'nik_nis' => '',
                        ],
                    ],
                    
                    'guardian_updates' => $existing?->guardian_updates ?? [
                        'name' => $otherGuardian?->name ?? '',
                        'relationship' => $otherGuardian?->pivot->relationship ?? 'wali_resmi',
                        'phone_primary' => $otherGuardian?->phone_primary ?? '',
                        'address' => $otherGuardian?->address ?? '',
                        'city' => $otherGuardian?->city ?? '',
                    ],
                    'has_profile_update' => $existing?->has_profile_update ?? false,
                    'has_guardian_update' => $existing?->has_guardian_update ?? false,
                ];
            }
        }

        $this->view = 'fill';
    }

    public function togglePersonProfile(string $personId): void
    {
        $this->expandedPersonId = $this->expandedPersonId === $personId ? null : $personId;
    }

    public function saveDraftCensus(): void
    {
        $service = app(CensusService::class);
        $dc      = DormitoryCensus::findOrFail($this->activeDormitoryCensusId);

        // Kelompokkan per room
        $byRoom = [];
        foreach ($this->censusData as $personId => $data) {
            $roomId = $data['room_id'];
            if (!isset($byRoom[$roomId])) {
                $byRoom[$roomId] = [];
            }
            
            // Check jika ada profile updates yang diisi
            $hasProfileUpdate = false;
            $profileUpdatesFiltered = array_filter($data['profile_updates'] ?? [], function($v, $k) {
                if ($k === 'sibling') {
                    return !empty($v['name']);
                }
                return $v !== '' && $v !== null;
            }, ARRAY_FILTER_USE_BOTH);

            if (!empty($profileUpdatesFiltered)) {
                $hasProfileUpdate = true;
            }

            // Check jika ada guardian updates yang diisi
            $hasGuardianUpdate = !empty($data['guardian_updates']['name']);

            $byRoom[$roomId][] = [
                'person_id'           => $personId,
                'status'              => $data['status'],
                'notes'               => $data['notes'] ?? null,
                'profile_updates'     => $hasProfileUpdate ? $profileUpdatesFiltered : null,
                'has_profile_update'  => $hasProfileUpdate,
                'has_guardian_update' => $hasGuardianUpdate,
                'guardian_updates'    => $hasGuardianUpdate ? array_filter($data['guardian_updates']) : null,
            ];
        }

        try {
            foreach ($byRoom as $roomId => $details) {
                $service->saveRoomCensus($this->activeDormitoryCensusId, $roomId, $details);
            }
            $this->toastSuccess('Draf sensus berhasil disimpan.');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function submitCensus(): void
    {
        // Simpan draf dulu sebelum submit
        $this->saveDraftCensus();

        $service = app(CensusService::class);
        try {
            $service->submitCensus($this->activeDormitoryCensusId, auth()->id());
            $this->toastSuccess('Laporan sensus berhasil dikirim ke pengurus pusat. Mohon tunggu verifikasi.');
            $this->view = 'list';
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    // =========================================================================
    // Redesain: Exception-Based Bulk Operations
    // =========================================================================

    public function confirmRoomNormal(string $roomId): void
    {
        try {
            app(CensusService::class)->bulkConfirmRoom($this->activeDormitoryCensusId, $roomId);
            
            // Reload view fill
            $this->openFillView($this->activeDormitoryCensusId);
            $this->toastSuccess('Kamar berhasil dikonfirmasi normal (semua hadir).');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function confirmAllNormal(): void
    {
        try {
            app(CensusService::class)->bulkConfirmAll($this->activeDormitoryCensusId);
            
            // Reload view fill
            $this->openFillView($this->activeDormitoryCensusId);
            $this->toastSuccess('Seluruh asrama berhasil dikonfirmasi normal.');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    // =========================================================================
    // Redesain: Excel Import/Export
    // =========================================================================

    public function downloadTemplate()
    {
        $service = app(CensusExcelService::class);
        $filePath = $service->generateTemplate($this->activeDormitoryCensusId);
        $census = DormitoryCensus::find($this->activeDormitoryCensusId);
        $filename = 'template_sensus_' . str_replace(' ', '_', strtolower($census->dormitory->name)) . '.xlsx';
        
        return response()->download($filePath, $filename)->deleteFileAfterSend(true);
    }

    public function uploadExcel(): void
    {
        $this->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        try {
            $service = app(CensusExcelService::class);
            $filePath = $this->excelFile->getRealPath();
            
            // Parse
            $parsed = $service->parseUpload($filePath, $this->activeDormitoryCensusId);
            
            // Simpan
            $storePath = $this->excelFile->store('census_uploads', 'local');
            $service->importFromExcel($this->activeDormitoryCensusId, $parsed, $storePath);
            
            // Refresh
            $this->openFillView($this->activeDormitoryCensusId);
            $this->fillTab = 'form';
            
            $this->toastSuccess("Excel berhasil diproses! {$parsed['total_confirmed']} santri dikonfirmasi, dengan {$parsed['total_exceptions']} pengecualian.");
        } catch (\Exception $e) {
            $this->toastError('Gagal memproses Excel: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // Review Census (Pengurus Pusat)
    // =========================================================================

    public function openReviewView(string $dormitoryCensusId): void
    {
        $this->reviewCensusId = $dormitoryCensusId;
        $this->view           = 'review';
    }

    public function confirmApproveCensus(string $dormitoryCensusId): void
    {
        $dc = DormitoryCensus::with('dormitory')->findOrFail($dormitoryCensusId);
        $this->confirmTitle    = 'Setujui Laporan Sensus';
        $this->confirmMessage  = "Setujui laporan sensus dari <strong>{$dc->dormitory->name}</strong>? Perubahan status kamar, data wali, saudara kandung, dan profil santri akan diterapkan secara otomatis.";
        $this->confirmAction   = 'approveCensus';
        $this->confirmPayload  = $dormitoryCensusId;
        $this->confirmVariant  = 'success';
        $this->showConfirmModal = true;
    }

    public function approveCensus(): void
    {
        try {
            app(CensusService::class)->approveCensus($this->confirmPayload);
            $this->toastSuccess('Sensus berhasil disetujui. Data profil santri, penempatan kamar, wali, dan saudara kandung telah diperbarui.');
            $this->view = 'list';
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
        $this->closeConfirmModal();
    }

    public function openRejectModal(string $dormitoryCensusId): void
    {
        $this->rejectCensusId = $dormitoryCensusId;
        $this->rejectNotes    = '';
        $this->showRejectModal = true;
    }

    public function rejectCensus(): void
    {
        $this->validate(['rejectNotes' => 'required|string|min:10|max:1000'], [
            'rejectNotes.required' => 'Harap masukkan catatan alasan penolakan.',
            'rejectNotes.min'      => 'Catatan penolakan minimal 10 karakter.',
        ]);

        try {
            app(CensusService::class)->rejectCensus($this->rejectCensusId, $this->rejectNotes);
            $this->toastSuccess('Laporan dikembalikan ke musyrif komplek untuk direvisi.');
            $this->showRejectModal = false;
            $this->view            = 'list';
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    // =========================================================================
    // Confirm Modal
    // =========================================================================

    public function closeConfirmModal(): void
    {
        $this->showConfirmModal = false;
        $this->confirmAction    = '';
        $this->confirmPayload   = null;
        $this->confirmTitle     = '';
        $this->confirmMessage   = '';
    }

    public function executeConfirmAction(): void
    {
        if ($this->confirmAction && method_exists($this, $this->confirmAction)) {
            $this->{$this->confirmAction}();
        }
    }

    public function goBack(): void
    {
        $this->view               = 'list';
        $this->activeDormitoryCensusId = null;
        $this->reviewCensusId         = null;
    }

    // =========================================================================
    // Render
    // =========================================================================

    public function render()
    {
        $user         = auth()->user();
        $isPusat      = $user->hasRole('super-admin') || $user->hasRole('pengasuh');
        $isMusyrif    = $user->hasRole('musyrif');

        // Daftar periode
        $periods = CensusPeriod::latest()->limit(12)->get();

        // Pilih periode aktif (default to selectedPeriodId)
        $selectedPeriod = $this->selectedPeriodId
            ? CensusPeriod::find($this->selectedPeriodId)
            : null;

        // Dormitory censuses untuk periode yang dipilih
        $dormitoryCensuses = [];
        if ($selectedPeriod) {
            $query = DormitoryCensus::with(['dormitory', 'submitter'])
                ->where('census_period_id', $selectedPeriod->id);

            if ($isMusyrif && !$isPusat) {
                $orgIds = $user->getOrganizationIds();
                $query->whereHas('dormitory', fn($q) => $q->whereIn('organization_id', $orgIds));
            }

            $dormitoryCensuses = $query->get();
        }

        // Untuk view fill - data detail
        $fillDormitoryCensus = null;
        $fillRooms           = [];
        if ($this->view === 'fill' && $this->activeDormitoryCensusId) {
            $fillDormitoryCensus = DormitoryCensus::with(['dormitory', 'period'])->find($this->activeDormitoryCensusId);
            
            // Filter exceptions jika opsi diaktifkan
            $filteredData = collect($this->censusData);
            if ($this->onlyShowExceptions) {
                $filteredData = $filteredData->filter(function ($item) {
                    $hasProfileUpdate = false;
                    $profileUpdatesFiltered = array_filter($item['profile_updates'] ?? [], function($v, $k) {
                        if ($k === 'sibling') return !empty($v['name']);
                        return $v !== '' && $v !== null;
                    }, ARRAY_FILTER_USE_BOTH);
                    if (!empty($profileUpdatesFiltered)) $hasProfileUpdate = true;
                    
                    $hasGuardianUpdate = !empty($item['guardian_updates']['name']);

                    return $item['status'] !== 'present' || $hasProfileUpdate || $hasGuardianUpdate;
                });
            }

            $fillRooms = $filteredData
                ->groupBy('room_name')
                ->map(fn($items, $roomName) => [
                    'name'    => $roomName,
                    'persons' => $items->keys()->toArray(),
                ]);
        }

        // Untuk view review - data review
        $reviewDormitoryCensus = null;
        if ($this->view === 'review' && $this->reviewCensusId) {
            $reviewDormitoryCensus = DormitoryCensus::with([
                'dormitory',
                'period',
                'submitter',
                'details.person.santriProfile.guardians',
                'details.room',
            ])->find($this->reviewCensusId);
        }

        $statusOptions = RoomCensusDetail::statusOptions();

        return view('livewire.kepengasuhan.census-manager', compact(
            'periods', 'selectedPeriod', 'dormitoryCensuses',
            'isPusat', 'isMusyrif',
            'fillDormitoryCensus', 'fillRooms',
            'reviewDormitoryCensus',
            'statusOptions'
        ))->layout('layouts.app');
    }
}
