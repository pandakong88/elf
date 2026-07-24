<?php

namespace App\Modules\Kepengasuhan\Services;

use App\Modules\Core\Models\PersonRole;
use App\Modules\Kepengasuhan\Models\CensusTemplate;
use App\Modules\Kepengasuhan\Models\CensusTemplateField;
use App\Modules\Kepengasuhan\Models\CensusV3Campaign;
use App\Modules\Kepengasuhan\Models\CensusV3CampaignDormitory;
use App\Modules\Kepengasuhan\Models\CensusV3Response;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Kepengasuhan\Models\SantriProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use DomainException;

class CensusV3Service
{
    // =========================================================================
    // Template Management
    // =========================================================================

    /**
     * Buat template baru dengan field-field yang dipilih.
     *
     * @param  array  $data  ['name', 'description', 'is_default']
     * @param  array  $fields  Array of field definitions
     * @param  string  $userId
     */
    public function createTemplate(array $data, array $fields, string $userId): CensusTemplate
    {
        return DB::transaction(function () use ($data, $fields, $userId) {
            // Jika template baru di-set sebagai default, hapus default yang lama
            if (!empty($data['is_default'])) {
                CensusTemplate::where('is_default', true)->update(['is_default' => false]);
            }

            $template = CensusTemplate::create([
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'is_default'  => $data['is_default'] ?? false,
                'is_archived' => false,
                'created_by'  => $userId,
            ]);

            $this->syncTemplateFields($template, $fields);

            return $template->load('fields');
        });
    }

    /**
     * Update template yang ada.
     */
    public function updateTemplate(string $templateId, array $data, array $fields): CensusTemplate
    {
        return DB::transaction(function () use ($templateId, $data, $fields) {
            $template = CensusTemplate::findOrFail($templateId);

            if (!empty($data['is_default'])) {
                CensusTemplate::where('is_default', true)
                    ->where('id', '!=', $templateId)
                    ->update(['is_default' => false]);
            }

            $template->update([
                'name'        => $data['name'],
                'description' => $data['description'] ?? $template->description,
                'is_default'  => $data['is_default'] ?? $template->is_default,
            ]);

            $this->syncTemplateFields($template, $fields);

            return $template->fresh('fields');
        });
    }

    /**
     * Sinkronisasi field-field template (upsert + hapus yang tidak ada).
     */
    private function syncTemplateFields(CensusTemplate $template, array $fields): void
    {
        $existingKeys = $template->fields()->pluck('field_key')->toArray();
        $newKeys      = array_column($fields, 'field_key');

        // Hapus field yang dihilangkan
        $toDelete = array_diff($existingKeys, $newKeys);
        if (!empty($toDelete)) {
            $template->fields()->whereIn('field_key', $toDelete)->delete();
        }

        foreach ($fields as $idx => $fieldData) {
            CensusTemplateField::updateOrCreate(
                ['template_id' => $template->id, 'field_key' => $fieldData['field_key']],
                [
                    'group_name'       => $fieldData['group_name']       ?? 'Umum',
                    'field_label'      => $fieldData['field_label'],
                    'field_type'       => $fieldData['field_type'],
                    'field_options'    => $fieldData['field_options']    ?? null,
                    'placeholder_text' => $fieldData['placeholder_text'] ?? null,
                    'help_text'        => $fieldData['help_text']        ?? null,
                    'is_required'      => $fieldData['is_required']      ?? false,
                    'is_system_field'  => $fieldData['is_system_field']  ?? false,
                    'profile_field_key'=> $fieldData['profile_field_key']?? null,
                    'sort_order'       => $fieldData['sort_order']       ?? ($idx + 1),
                ]
            );
        }
    }

    // =========================================================================
    // Campaign Management
    // =========================================================================

    /**
     * Buat kampanye sensus baru.
     */
    public function createCampaign(array $data, string $userId): CensusV3Campaign
    {
        return DB::transaction(function () use ($data, $userId) {
            $campaign = CensusV3Campaign::create([
                'name'               => $data['name'],
                'description'        => $data['description'] ?? null,
                'template_id'        => $data['template_id'],
                'month'              => $data['month'],
                'year'               => $data['year'],
                'target_scope'       => $data['target_scope'] ?? 'all',
                'workflow_mode'      => $data['workflow_mode'] ?? 'admin_only',
                'allow_excel'        => $data['allow_excel'] ?? false,
                'allow_direct_input' => $data['allow_direct_input'] ?? true,
                'deadline'           => $data['deadline'] ?? null,
                'status'             => 'draft',
                'created_by'         => $userId,
            ]);

            // Buat target asrama sesuai scope
            $this->createCampaignDormitories($campaign, $data);

            return $campaign->load(['template', 'dormitories.dormitory']);
        });
    }

    /**
     * Buat record CampaignDormitory berdasarkan target_scope kampanye.
     */
    private function createCampaignDormitories(CensusV3Campaign $campaign, array $data): void
    {
        $dormitoryQuery = Dormitory::where('is_active', true);

        switch ($campaign->target_scope) {
            case 'putra':
                $dormitoryQuery->where('gender', 'L');
                break;
            case 'putri':
                $dormitoryQuery->where('gender', 'P');
                break;
            case 'custom_dormitories':
                if (!empty($data['target_dormitory_ids'])) {
                    $dormitoryQuery->whereIn('id', $data['target_dormitory_ids']);
                }
                break;
            // 'all' → tidak ada filter tambahan
        }

        $dormitories = $dormitoryQuery->get();
        $assignedUsers = $data['assigned_users'] ?? []; // [dormitory_id => user_id]

        foreach ($dormitories as $dormitory) {
            // Hitung total santri aktif di asrama ini
            $totalSantri = RoomAssignment::active()
                ->join('rooms', 'room_assignments.room_id', '=', 'rooms.id')
                ->where('rooms.dormitory_id', $dormitory->id)
                ->count();

            CensusV3CampaignDormitory::create([
                'campaign_id'     => $campaign->id,
                'dormitory_id'    => $dormitory->id,
                'assigned_to'     => $assignedUsers[$dormitory->id] ?? null,
                'status'          => 'pending',
                'progress_filled' => 0,
                'progress_total'  => $totalSantri,
            ]);
        }
    }

    /**
     * Aktifkan kampanye (draft → active/collecting).
     */
    public function activateCampaign(string $campaignId): CensusV3Campaign
    {
        $campaign = CensusV3Campaign::findOrFail($campaignId);

        if ($campaign->status !== 'draft') {
            throw new DomainException('Hanya kampanye berstatus draft yang bisa diaktifkan.');
        }

        $campaign->update([
            'status'    => 'collecting',
            'opened_at' => now(),
        ]);

        return $campaign->fresh();
    }

    /**
     * Tutup kampanye dan pindahkan ke status review.
     */
    public function closeCampaignForReview(string $campaignId): CensusV3Campaign
    {
        $campaign = CensusV3Campaign::findOrFail($campaignId);

        if ($campaign->status !== 'collecting') {
            throw new DomainException('Kampanye harus berstatus collecting untuk dipindahkan ke review.');
        }

        $campaign->update(['status' => 'review']);

        return $campaign->fresh();
    }

    // =========================================================================
    // Data Input
    // =========================================================================

    /**
     * Simpan atau update response santri dalam kampanye.
     *
     * @param  string  $campaignDormitoryId
     * @param  string  $personId
     * @param  array   $responseData   ['field_key' => value, ...]
     * @param  string  $inputtedBy
     * @param  string  $inputMethod
     */
    public function saveResponse(
        string $campaignDormitoryId,
        string $personId,
        array  $responseData,
        string $inputtedBy,
        string $inputMethod = 'web_admin'
    ): CensusV3Response {
        return DB::transaction(function () use ($campaignDormitoryId, $personId, $responseData, $inputtedBy, $inputMethod) {
            $cd = CensusV3CampaignDormitory::findOrFail($campaignDormitoryId);

            // Ambil field template untuk validasi & profil sync
            $templateFields = $cd->campaign->template->fields()->get()->keyBy('field_key');

            // Ambil room_id santri saat ini
            $assignment = RoomAssignment::active()
                ->join('rooms', 'room_assignments.room_id', '=', 'rooms.id')
                ->where('rooms.dormitory_id', $cd->dormitory_id)
                ->where('room_assignments.person_id', $personId)
                ->select('room_assignments.*')
                ->first();

            // Cek perubahan profil (system fields)
            $profilePreview = [];
            $hasProfileChanges = false;
            $profile = SantriProfile::where('person_id', $personId)->first();

            foreach ($responseData as $key => $value) {
                $field = $templateFields->get($key);
                if ($field && $field->is_system_field && $field->profile_field_key) {
                    $currentValue = $profile?->{$field->profile_field_key};
                    if ($currentValue !== $value && $value !== null && $value !== '') {
                        $profilePreview[$field->profile_field_key] = [
                            'label'     => $field->field_label,
                            'old'       => $currentValue,
                            'new'       => $value,
                        ];
                        $hasProfileChanges = true;
                    }
                }
            }

            // Validasi required fields
            $isComplete = true;
            foreach ($templateFields->where('is_required', true) as $field) {
                if (empty($responseData[$field->field_key]) && $responseData[$field->field_key] !== false) {
                    $isComplete = false;
                    break;
                }
            }

            $response = CensusV3Response::updateOrCreate(
                ['campaign_id' => $cd->campaign_id, 'person_id' => $personId],
                [
                    'dormitory_id'           => $cd->dormitory_id,
                    'room_id'                => $assignment?->room_id,
                    'response_data'          => $responseData,
                    'input_method'           => $inputMethod,
                    'inputted_by'            => $inputtedBy,
                    'is_complete'            => $isComplete,
                    'has_profile_changes'    => $hasProfileChanges,
                    'profile_change_preview' => $hasProfileChanges ? $profilePreview : null,
                ]
            );

            // Update progress di CampaignDormitory
            $filled = CensusV3Response::where('campaign_id', $cd->campaign_id)
                ->where('dormitory_id', $cd->dormitory_id)
                ->where('is_complete', true)
                ->count();

            $cd->update([
                'progress_filled' => $filled,
                'status'          => 'in_progress',
            ]);

            return $response;
        });
    }

    /**
     * Submit laporan asrama ke pusat.
     */
    public function submitDormitory(string $campaignDormitoryId, string $submittedBy): CensusV3CampaignDormitory
    {
        $cd = CensusV3CampaignDormitory::findOrFail($campaignDormitoryId);

        if (!in_array($cd->status, ['pending', 'in_progress', 'rejected'])) {
            throw new DomainException('Laporan sudah pernah dikirim atau tidak bisa disubmit saat ini.');
        }

        // Default hadir: santri yang belum diisi → tambah response dengan status hadir
        $this->defaultUnfilledToHadir($cd);

        $cd->update([
            'status'       => 'submitted',
            'submitted_at' => now(),
        ]);

        return $cd->fresh();
    }

    /**
     * Untuk santri yang belum diisi sama sekali → buat response default "Hadir".
     */
    private function defaultUnfilledToHadir(CensusV3CampaignDormitory $cd): void
    {
        $allPersonIds = RoomAssignment::active()
            ->join('rooms', 'room_assignments.room_id', '=', 'rooms.id')
            ->where('rooms.dormitory_id', $cd->dormitory_id)
            ->pluck('room_assignments.person_id')
            ->toArray();

        $filledPersonIds = CensusV3Response::where('campaign_id', $cd->campaign_id)
            ->where('dormitory_id', $cd->dormitory_id)
            ->pluck('person_id')
            ->toArray();

        $unfilledIds = array_diff($allPersonIds, $filledPersonIds);

        foreach ($unfilledIds as $personId) {
            $assignment = RoomAssignment::active()
                ->join('rooms', 'room_assignments.room_id', '=', 'rooms.id')
                ->where('rooms.dormitory_id', $cd->dormitory_id)
                ->where('room_assignments.person_id', $personId)
                ->select('room_assignments.*')
                ->first();

            CensusV3Response::create([
                'campaign_id'  => $cd->campaign_id,
                'dormitory_id' => $cd->dormitory_id,
                'person_id'    => $personId,
                'room_id'      => $assignment?->room_id,
                'response_data'=> [],
                'input_method' => 'web_admin',
                'inputted_by'  => auth()->id() ?? $cd->assigned_to ?? $cd->campaign->created_by,
                'is_complete'  => true,
            ]);
        }

        // Update total progress
        $cd->update([
            'progress_filled' => $cd->progress_total,
        ]);
    }

    /**
     * Approve laporan asrama + sinkronisasi perubahan profil.
     */
    public function approveDormitory(string $campaignDormitoryId, string $approvedBy): CensusV3CampaignDormitory
    {
        return DB::transaction(function () use ($campaignDormitoryId, $approvedBy) {
            $cd = CensusV3CampaignDormitory::findOrFail($campaignDormitoryId);

            if ($cd->status !== 'submitted') {
                throw new DomainException('Hanya laporan berstatus submitted yang bisa disetujui.');
            }

            // Sync semua profile changes
            $responses = CensusV3Response::where('campaign_id', $cd->campaign_id)
                ->where('dormitory_id', $cd->dormitory_id)
                ->where('has_profile_changes', true)
                ->get();

            $templateFields = $cd->campaign->template->fields()
                ->where('is_system_field', true)
                ->whereNotNull('profile_field_key')
                ->get()
                ->keyBy('field_key');

            foreach ($responses as $response) {
                $updates = [];
                foreach ($response->response_data as $key => $value) {
                    $field = $templateFields->get($key);
                    if ($field && !empty($value)) {
                        $updates[$field->profile_field_key] = $value;
                    }
                }

                if (!empty($updates)) {
                    SantriProfile::updateOrCreate(
                        ['person_id' => $response->person_id],
                        array_merge(['person_id' => $response->person_id], $updates)
                    );
                }
            }

            $cd->update([
                'status'      => 'approved',
                'approved_at' => now(),
            ]);

            return $cd->fresh();
        });
    }

    /**
     * Tolak/kembalikan laporan asrama ke pengisi.
     */
    public function rejectDormitory(string $campaignDormitoryId, string $notes): CensusV3CampaignDormitory
    {
        $cd = CensusV3CampaignDormitory::findOrFail($campaignDormitoryId);

        if ($cd->status !== 'submitted') {
            throw new DomainException('Hanya laporan berstatus submitted yang bisa dikembalikan.');
        }

        $cd->update([
            'status'          => 'rejected',
            'rejection_notes' => $notes,
        ]);

        return $cd->fresh();
    }

    /**
     * Tutup kampanye setelah semua asrama approved.
     */
    public function closeCampaign(string $campaignId): CensusV3Campaign
    {
        $campaign = CensusV3Campaign::findOrFail($campaignId);

        $campaign->update([
            'status'    => 'closed',
            'closed_at' => now(),
        ]);

        return $campaign->fresh();
    }

    /**
     * Ambil daftar santri untuk form input (dengan status santri).
     */
    public function getSantriForInput(string $campaignDormitoryId): \Illuminate\Support\Collection
    {
        $cd = CensusV3CampaignDormitory::with('dormitory')->findOrFail($campaignDormitoryId);

        return RoomAssignment::active()
            ->join('rooms', 'room_assignments.room_id', '=', 'rooms.id')
            ->join('persons', 'room_assignments.person_id', '=', 'persons.id')
            ->leftJoin('person_roles', function ($join) {
                $join->on('person_roles.person_id', '=', 'persons.id')
                     ->where('person_roles.role_type', 'santri')
                     ->where('person_roles.is_active', true);
            })
            ->leftJoin('census_responses', function ($join) use ($cd) {
                $join->on('census_responses.person_id', '=', 'persons.id')
                     ->where('census_responses.campaign_id', $cd->campaign_id);
            })
            ->where('rooms.dormitory_id', $cd->dormitory_id)
            ->select([
                'persons.id as person_id',
                'persons.name as person_name',
                'persons.nik',
                'rooms.id as room_id',
                'rooms.name as room_name',
                'person_roles.enrollment_status',
                'person_roles.presence_status',
                'person_roles.id as role_id',
                'census_responses.id as response_id',
                'census_responses.response_data',
                'census_responses.is_complete',
                'census_responses.has_profile_changes',
            ])
            ->orderBy('rooms.name')
            ->orderBy('persons.name')
            ->get();
    }
}
