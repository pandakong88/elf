<?php

namespace App\Modules\Kepengasuhan\Services;

use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Kepengasuhan\Models\CensusMutationEntry;
use App\Modules\Kepengasuhan\Models\CensusV3Campaign;
use App\Modules\Kepengasuhan\Models\CensusV3CampaignDormitory;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CensusMutationService
{
    public function __construct(
        protected DormitoryService $dormitoryService,
        protected SantriStatusService $statusService,
        protected CensusV3Service $censusV3Service
    ) {}

    /**
     * Ambil data santri asrama untuk lembar input sensus mutasi:
     * - Kamar-kamar beserta santri aktifnya
     * - Santri tanpa kamar (gender yang sama)
     */
    public function getDormitoryOccupants(string $dormitoryId): array
    {
        $dormitory = Dormitory::with(['rooms' => function ($q) {
            $q->where('is_active', true)->orderBy('name');
        }])->findOrFail($dormitoryId);

        $roomsData = [];
        foreach ($dormitory->rooms as $room) {
            $assignments = RoomAssignment::active()
                ->where('room_id', $room->id)
                ->with(['person.activeRoles', 'person.santriProfile'])
                ->get();

            $occupants = $assignments->map(function ($assignment) {
                return [
                    'assignment_id' => $assignment->id,
                    'person_id'     => $assignment->person_id,
                    'name'          => $assignment->person?->name,
                    'nis'           => $assignment->person?->santriProfile?->nis,
                    'gender'        => $assignment->person?->gender,
                    'room_id'       => $assignment->room_id,
                    'status'        => $assignment->person?->activeRoles?->first()?->enrollment_status ?? 'aktif',
                ];
            });

            $roomsData[] = [
                'id'          => $room->id,
                'name'        => $room->name,
                'capacity'    => $room->capacity,
                'occupied'    => $occupants->count(),
                'remaining'   => max(0, $room->capacity - $occupants->count()),
                'occupants'   => $occupants,
            ];
        }

        // Ambil santri tanpa kamar yang segender
        $unassignedSantri = $this->dormitoryService->getSantriWithoutRoom($dormitory->gender)
            ->map(function ($person) {
                return [
                    'person_id' => $person->id,
                    'name'      => $person->name,
                    'nis'       => $person->santriProfile?->nis,
                    'gender'    => $person->gender,
                    'room_id'   => null,
                    'status'    => $person->activeRoles?->first()?->enrollment_status ?? 'aktif',
                ];
            });

        return [
            'dormitory'        => $dormitory,
            'rooms'            => $roomsData,
            'unassigned'       => $unassignedSantri,
        ];
    }

    /**
     * Ambil daftar kamar tujuan yang tersedia (sesuai gender asrama).
     */
    public function getAvailableTargetRooms(string $dormitoryGender): Collection
    {
        return Room::where('is_active', true)
            ->whereHas('dormitory', function ($q) use ($dormitoryGender) {
                $q->where('gender', $dormitoryGender)->where('is_active', true);
            })
            ->with('dormitory')
            ->withCount(['currentAssignments as current_occupied'])
            ->get()
            ->map(function ($room) {
                $rem = max(0, $room->capacity - $room->current_occupied);
                return [
                    'id'             => $room->id,
                    'name'           => $room->dormitory->name . ' - ' . $room->name,
                    'capacity'       => $room->capacity,
                    'occupied'       => $room->current_occupied,
                    'remaining'      => $rem,
                    'is_full'        => $rem <= 0,
                ];
            });
    }

    /**
     * Ambil data mutasi yang tersimpan untuk kampanye & asrama tertentu.
     */
    public function getSavedEntries(string $campaignId, string $dormitoryId): Collection
    {
        return CensusMutationEntry::with(['person', 'fromRoom', 'toRoom.dormitory'])
            ->where('campaign_id', $campaignId)
            ->where('dormitory_id', $dormitoryId)
            ->get()
            ->keyBy('person_id');
    }

    /**
     * Simpan entri mutasi santri per person.
     */
    public function saveEntry(
        string $campaignId,
        string $dormitoryId,
        string $personId,
        array  $data,
        string $userId
    ): CensusMutationEntry {
        $cd = CensusV3CampaignDormitory::where('campaign_id', $campaignId)
            ->where('dormitory_id', $dormitoryId)
            ->firstOrFail();

        if (!in_array($cd->status, ['pending', 'in_progress', 'rejected'])) {
            throw new DomainException('Laporan sensus untuk asrama ini sudah dikirim atau disetujui, dan tidak dapat diubah lagi.');
        }

        $entry = CensusMutationEntry::updateOrCreate(
            [
                'campaign_id'  => $campaignId,
                'dormitory_id' => $dormitoryId,
                'person_id'    => $personId,
            ],
            [
                'from_room_id'          => $data['from_room_id'] ?? null,
                'action'                => $data['action'] ?? 'tetap',
                'to_room_id'            => ($data['action'] ?? '') === 'pindah_kamar' ? ($data['to_room_id'] ?? null) : null,
                'new_enrollment_status' => ($data['action'] ?? '') === 'ubah_status' ? ($data['new_enrollment_status'] ?? null) : null,
                'notes'                 => $data['notes'] ?? null,
                'entered_by'            => $userId,
                'apply_status'          => 'pending',
                'apply_error'           => null,
            ]
        );

        if ($cd->status === 'pending') {
            $cd->update(['status' => 'in_progress']);
        }

        return $entry;
    }

    /**
     * Terapkan seluruh mutasi yang telah disetujui (dijalankan saat approve laporan asrama).
     */
    public function applyApprovedMutations(string $campaignDormitoryId, string $approvedBy): array
    {
        $cd = CensusV3CampaignDormitory::with('campaign', 'dormitory')->findOrFail($campaignDormitoryId);

        $entries = CensusMutationEntry::where('campaign_id', $cd->campaign_id)
            ->where('dormitory_id', $cd->dormitory_id)
            ->get();

        $appliedCount = 0;
        $failedCount  = 0;
        $errors       = [];

        foreach ($entries as $entry) {
            if ($entry->action === 'pindah_kamar') {
                if (!$entry->to_room_id) {
                    $entry->update([
                        'apply_status' => 'failed',
                        'apply_error'  => 'Kamar tujuan belum dipilih.',
                    ]);
                    $failedCount++;
                    $errors[] = "Santri #{$entry->person_id}: Kamar tujuan belum dipilih.";
                    continue;
                }

                try {
                    $this->dormitoryService->assignRoom(
                        $entry->to_room_id,
                        $entry->person_id,
                        now()->toDateString()
                    );
                    $entry->update([
                        'apply_status' => 'applied',
                        'apply_error'  => null,
                    ]);
                    $appliedCount++;
                } catch (\Exception $e) {
                    $entry->update([
                        'apply_status' => 'failed',
                        'apply_error'  => $e->getMessage(),
                    ]);
                    $failedCount++;
                    $errors[] = "Gagal memindahkan santri #{$entry->person_id}: " . $e->getMessage();
                }
            } elseif ($entry->action === 'ubah_status') {
                if (!$entry->new_enrollment_status) {
                    $entry->update([
                        'apply_status' => 'failed',
                        'apply_error'  => 'Status baru belum ditentukan.',
                    ]);
                    $failedCount++;
                    $errors[] = "Santri #{$entry->person_id}: Status baru belum ditentukan.";
                    continue;
                }

                try {
                    $role = PersonRole::where('person_id', $entry->person_id)
                        ->where('is_active', true)
                        ->first();

                    if (!$role) {
                        throw new DomainException('Data role aktif santri tidak ditemukan.');
                    }

                    $this->statusService->changeEnrollmentStatus(
                        $role->id,
                        $entry->new_enrollment_status,
                        $approvedBy,
                        $entry->notes ?? 'Sensus Mutasi Santri'
                    );

                    $entry->update([
                        'apply_status' => 'applied',
                        'apply_error'  => null,
                    ]);
                    $appliedCount++;
                } catch (\Exception $e) {
                    $entry->update([
                        'apply_status' => 'failed',
                        'apply_error'  => $e->getMessage(),
                    ]);
                    $failedCount++;
                    $errors[] = "Gagal mengubah status santri #{$entry->person_id}: " . $e->getMessage();
                }
            } else {
                // 'tetap' atau 'tidak_ditemukan' tidak melakukan mutasi data
                $entry->update([
                    'apply_status' => 'applied',
                    'apply_error'  => null,
                ]);
            }
        }

        // Jalankan persetujuan status laporan asrama
        $this->censusV3Service->approveDormitory($cd->id, $approvedBy);

        return [
            'total'   => $entries->count(),
            'applied' => $appliedCount,
            'failed'  => $failedCount,
            'errors'  => $errors,
        ];
    }
}
