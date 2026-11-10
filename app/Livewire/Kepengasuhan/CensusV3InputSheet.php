<?php

namespace App\Livewire\Kepengasuhan;

use Livewire\Component;
use App\Livewire\Concerns\SendsToast;
use Livewire\WithFileUploads;
use App\Modules\Kepengasuhan\Models\CensusV3Campaign;
use App\Modules\Kepengasuhan\Models\CensusV3CampaignDormitory;
use App\Modules\Kepengasuhan\Models\CensusV3Response;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Services\CensusV3Service;
use App\Modules\Kepengasuhan\Services\CensusV3AccessService;
use App\Modules\Kepengasuhan\Services\SantriStatusService;
use App\Modules\Kepengasuhan\Exports\CensusV3TemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;

class CensusV3InputSheet extends Component
{
    use SendsToast, WithFileUploads;

    public string $campaignId;
    public string $dormitoryId;

    // Filter by Room
    public string $roomFilter = '';

    // File upload
    public $excelFile;

    // Inputs: arrays mapped by person_id
    public array $responses = [];
    public array $presenceStatuses = [];
    public array $enrollmentStatuses = [];

    // Original statuses & responses loaded from DB to detect changes and avoid concurrent overwrites
    public array $originalResponses = [];
    public array $originalPresenceStatuses = [];
    public array $originalEnrollmentStatuses = [];

    protected CensusV3Service $censusService;
    protected CensusV3AccessService $accessService;
    protected SantriStatusService $statusService;

    public function boot(
        CensusV3Service $censusService,
        CensusV3AccessService $accessService,
        SantriStatusService $statusService
    ): void {
        $this->censusService = $censusService;
        $this->accessService = $accessService;
        $this->statusService = $statusService;
    }

    public function mount(string $campaign, string $dormitory): void
    {
        $this->campaignId = $campaign;
        $this->dormitoryId = $dormitory;

        $cd = $this->getCampaignDormitoryProperty();
        if (!$cd) {
            $this->redirect(route('sensus.campaigns'));
            return;
        }

        // Pengecekan otorisasi
        if (!$this->accessService->canAccessDormitory(auth()->user(), $cd, 'fill')) {
            abort(403, 'Akses ditolak. Anda tidak berwenang mengisi data sensus asrama ini.');
        }

        $this->loadData();
    }

    private function loadData(): void
    {
        $cd = $this->getCampaignDormitoryProperty();
        if (!$cd) {
            $this->redirect(route('sensus.campaigns'));
            return;
        }

        $santri = $this->censusService->getSantriForInput($cd->id);

        foreach ($santri as $s) {
            $personId = $s->person_id;

            // Load existing responses or initialize
            $existingResponse = $s->response_data ? (is_string($s->response_data) ? json_decode($s->response_data, true) : $s->response_data) : [];
            
            // Map template fields to responses
            $templateFields = $cd->campaign->template->fields;
            $rowResponses = [];
            foreach ($templateFields as $field) {
                $rowResponses[$field->field_key] = $existingResponse[$field->field_key] ?? ($field->field_type === 'boolean' ? false : '');
            }

            $this->responses[$personId] = $rowResponses;
            $this->originalResponses[$personId] = $rowResponses;

            // Load statuses
            $this->presenceStatuses[$personId] = $s->presence_status ?? 'mukim';
            $this->enrollmentStatuses[$personId] = $s->enrollment_status ?? 'aktif';

            // Keep originals to check for updates later
            $this->originalPresenceStatuses[$personId] = $s->presence_status ?? 'mukim';
            $this->originalEnrollmentStatuses[$personId] = $s->enrollment_status ?? 'aktif';
        }
    }

    public function getCampaignDormitoryProperty(): ?CensusV3CampaignDormitory
    {
        return CensusV3CampaignDormitory::with('campaign.template.fields', 'dormitory', 'assignees')
            ->where('campaign_id', $this->campaignId)
            ->where('dormitory_id', $this->dormitoryId)
            ->first();
    }

    public function getRoomsProperty(): Collection
    {
        return Room::where('dormitory_id', $this->dormitoryId)->orderBy('name')->get();
    }

    public function getSantriListProperty(): Collection
    {
        $cd = $this->campaignDormitory;
        if (!$cd) return collect();

        $allSantri = $this->censusService->getSantriForInput($cd->id);

        if ($this->roomFilter !== '') {
            $allSantri = $allSantri->where('room_id', $this->roomFilter);
        }

        return $allSantri;
    }

    public function bulkConfirm(): void
    {
        $this->ensureAuthorized();

        foreach ($this->santriList as $s) {
            $personId = $s->person_id;
            
            // Set presence to mukim if active
            if ($this->enrollmentStatuses[$personId] === 'aktif') {
                $this->presenceStatuses[$personId] = 'mukim';
            }
        }

        $this->toastSuccess('Berhasil konfirmasi massal: Semua santri aktif di-set Mukim.');
    }

    public function downloadTemplate()
    {
        $cd = $this->campaignDormitory;
        $fileName = 'Template_Sensus_' . str_replace(' ', '_', $cd->campaign->name) . '_' . str_replace(' ', '_', $cd->dormitory->name) . '.xlsx';
        
        return Excel::download(
            new CensusV3TemplateExport($cd),
            $fileName
        );
    }

    public function updatedExcelFile(): void
    {
        $this->importExcel();
    }

    public function importExcel()
    {
        $this->ensureAuthorized();

        $this->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls|max:5120', // 5MB max
        ]);

        try {
            $path = $this->excelFile->getRealPath();
            $data = Excel::toArray(new \stdClass(), $path);
            
            if (empty($data) || empty($data[0])) {
                $this->toastError('File Excel kosong atau tidak terbaca.');
                return;
            }

            $rows = $data[0];
            unset($rows[0]); // remove header row

            $cd = $this->campaignDormitory;
            $fields = $cd->campaign->template->fields;

            $importedCount = 0;

            foreach ($rows as $row) {
                $personId = $row[0] ?? null;
                if (!$personId || !isset($this->responses[$personId])) {
                    continue;
                }

                // Column D: Enrollment Status (index 3)
                $enrollment = trim($row[3] ?? 'aktif');
                if (in_array($enrollment, ['aktif', 'alumni', 'keluar_resmi', 'dikeluarkan', 'tanpa_keterangan'])) {
                    $this->enrollmentStatuses[$personId] = $enrollment;
                }

                // Column E: Presence Status (index 4)
                $presence = trim($row[4] ?? 'mukim');
                if (in_array($presence, ['mukim', 'laju', 'izin', 'alpa'])) {
                    $this->presenceStatuses[$personId] = $presence;
                }

                // Dynamic fields starting from index 5
                $colIdx = 5;
                foreach ($fields as $field) {
                    $val = $row[$colIdx] ?? '';
                    if ($field->field_type === 'boolean') {
                        $this->responses[$personId][$field->field_key] = (strtoupper(trim($val)) === 'YA' || $val === '1' || $val === true);
                    } else {
                        $this->responses[$personId][$field->field_key] = $val;
                    }
                    $colIdx++;
                }

                $importedCount++;
            }

            $this->toastSuccess("Berhasil mengimpor {$importedCount} baris data dari Excel. Periksa kembali dan simpan draft / kirim laporan.");
        } catch (\Exception $e) {
            $this->toastError('Gagal mengimpor file Excel: ' . $e->getMessage());
        } finally {
            $this->excelFile = null;
        }
    }

    public function saveDraft(): void
    {
        $this->ensureAuthorized();
        $this->saveData();
        $this->toastSuccess('Draft sensus berhasil disimpan.');
    }

    public function submit(): void
    {
        $this->ensureAuthorized();
        try {
            $this->saveData();
            
            $cd = $this->campaignDormitory;
            $this->censusService->submitDormitory($cd->id, auth()->id());

            $this->toastSuccess('Laporan sensus berhasil dikirim ke pusat.');
            $this->redirect(route('sensus.campaigns'));
        } catch (\Exception $e) {
            $this->toastError('Gagal mengirim sensus: ' . $e->getMessage());
        }
    }

    protected function ensureAuthorized(): void
    {
        $cd = $this->campaignDormitory;
        if (!$cd || !$this->accessService->canAccessDormitory(auth()->user(), $cd, 'fill')) {
            abort(403, 'Anda tidak memiliki hak untuk mengubah data sensus asrama ini.');
        }

        if (!in_array($cd->status, ['pending', 'in_progress', 'rejected'])) {
            abort(403, 'Sensus untuk asrama ini sudah disubmit atau disetujui, perubahan tidak diizinkan.');
        }
    }

    private function saveData(): void
    {
        $cd = $this->campaignDormitory;
        $userId = auth()->id();

        foreach ($this->santriList as $s) {
            $personId = $s->person_id;
            $roleId = $s->role_id;

            $hasResponseChange = ($this->responses[$personId] !== ($this->originalResponses[$personId] ?? null));
            $hasPresenceChange = ($this->presenceStatuses[$personId] !== ($this->originalPresenceStatuses[$personId] ?? null));
            $hasEnrollmentChange = ($this->enrollmentStatuses[$personId] !== ($this->originalEnrollmentStatuses[$personId] ?? null));

            // Jika belum ada response sama sekali di DB, tetap simpan
            $isNotYetInDb = empty($s->response_id);

            // Simpan HANYA baris yang mengalami perubahan atau belum ada respons (mencegah overwrite antar sesama pengisi)
            if ($hasResponseChange || $isNotYetInDb) {
                $this->censusService->saveResponse(
                    $cd->id,
                    $personId,
                    $this->responses[$personId],
                    $userId,
                    'web_ketua'
                );
                $this->originalResponses[$personId] = $this->responses[$personId];
            }

            // Simpan status presence jika ada perubahan
            if ($hasPresenceChange) {
                if (auth()->user()->can('change-presence-status')) {
                    $this->statusService->changePresenceStatus(
                        $roleId,
                        $this->presenceStatuses[$personId],
                        $userId,
                        null,
                        'Diubah via lembar input sensus.'
                    );
                    $this->originalPresenceStatuses[$personId] = $this->presenceStatuses[$personId];
                }
            }

            // Simpan status enrollment jika ada perubahan
            if ($hasEnrollmentChange) {
                if (auth()->user()->can('change-enrollment-status')) {
                    $this->statusService->changeEnrollmentStatus(
                        $roleId,
                        $this->enrollmentStatuses[$personId],
                        $userId,
                        'Diubah via lembar input sensus.'
                    );
                    $this->originalEnrollmentStatuses[$personId] = $this->enrollmentStatuses[$personId];
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.kepengasuhan.census-v3-input-sheet', [
            'cd'         => $this->campaignDormitory,
            'rooms'      => $this->rooms,
            'santriList' => $this->santriList,
        ])->layout('layouts.app', ['title' => 'Input Sensus']);
    }
}
