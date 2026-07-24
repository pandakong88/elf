<?php

namespace App\Modules\Kepengasuhan\Services;

use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\CensusPeriod;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\DormitoryCensus;
use App\Modules\Kepengasuhan\Models\RoomCensusDetail;
use App\Modules\Kepengasuhan\Models\SantriProfile;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Kepengasuhan\Services\GuardianService;
use App\Modules\Kepengasuhan\Services\SiblingService;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CensusService
{
    /**
     * Buat periode sensus baru (status: draft).
     * Sekaligus otomatis membuat record DormitoryCensus berstatus `pending`
     * untuk setiap asrama aktif yang ada di sistem.
     */
    public function createPeriod(string $name, int $month, int $year, string $userId): CensusPeriod
    {
        return DB::transaction(function () use ($name, $month, $year, $userId) {
            $period = CensusPeriod::create([
                'id'         => Str::uuid()->toString(),
                'name'       => $name,
                'month'      => $month,
                'year'       => $year,
                'status'     => CensusPeriod::STATUS_DRAFT,
                'created_by' => $userId,
            ]);

            // Buat record dormitory_census untuk setiap asrama aktif
            $dormitories = Dormitory::where('is_active', true)->get();
            foreach ($dormitories as $dormitory) {
                DormitoryCensus::create([
                    'id'               => Str::uuid()->toString(),
                    'census_period_id' => $period->id,
                    'dormitory_id'     => $dormitory->id,
                    'status'           => DormitoryCensus::STATUS_PENDING,
                ]);
            }

            return $period;
        });
    }

    /**
     * Aktifkan periode sensus (ubah dari draft ke active).
     */
    public function startPeriod(string $periodId): void
    {
        $period = CensusPeriod::findOrFail($periodId);

        if ($period->status !== CensusPeriod::STATUS_DRAFT) {
            throw new DomainException("Hanya periode berstatus 'draft' yang dapat diaktifkan.");
        }

        $period->update(['status' => CensusPeriod::STATUS_ACTIVE]);
    }

    /**
     * Tutup periode sensus.
     */
    public function closePeriod(string $periodId): void
    {
        $period = CensusPeriod::findOrFail($periodId);

        if ($period->status !== CensusPeriod::STATUS_ACTIVE) {
            throw new DomainException("Hanya periode berstatus 'active' yang dapat ditutup.");
        }

        $period->update(['status' => CensusPeriod::STATUS_CLOSED]);
    }

    /**
     * Simpan draf cek fisik santri per kamar.
     * $details berisi array: [['person_id' => ..., 'status' => ..., 'notes' => ..., 'profile_updates' => [...]], ...]
     */
    public function saveRoomCensus(string $dormitoryCensusId, string $roomId, array $details): void
    {
        $dormitoryCensus = DormitoryCensus::findOrFail($dormitoryCensusId);

        if ($dormitoryCensus->status === DormitoryCensus::STATUS_APPROVED) {
            throw new DomainException("Sensus ini sudah disetujui dan tidak dapat diubah lagi.");
        }

        DB::transaction(function () use ($dormitoryCensus, $roomId, $details) {
            foreach ($details as $detail) {
                RoomCensusDetail::updateOrCreate(
                    [
                        'dormitory_census_id' => $dormitoryCensus->id,
                        'person_id'           => $detail['person_id'],
                    ],
                    [
                        'id'                  => Str::uuid()->toString(),
                        'room_id'             => $roomId,
                        'status'              => $detail['status'] ?? RoomCensusDetail::STATUS_PRESENT,
                        'notes'               => $detail['notes'] ?? null,
                        'profile_updates'     => $detail['profile_updates'] ?? null,
                        'has_profile_update'  => $detail['has_profile_update'] ?? false,
                        'has_guardian_update' => $detail['has_guardian_update'] ?? false,
                        'guardian_updates'    => $detail['guardian_updates'] ?? null,
                    ]
                );
            }
        });
    }

    /**
     * Kirim laporan sensus asrama ke pusat (ubah status menjadi submitted).
     * Jika ada santri yang belum diisi statusnya, otomatis diisi "Hadir" (smart default).
     */
    public function submitCensus(string $dormitoryCensusId, string $userId): void
    {
        $dormitoryCensus = DormitoryCensus::with('dormitory.rooms')->findOrFail($dormitoryCensusId);

        if ($dormitoryCensus->status !== DormitoryCensus::STATUS_PENDING) {
            throw new DomainException("Hanya sensus berstatus 'Belum Diisi' yang dapat dikirim.");
        }

        // Auto-fill status present untuk santri yang belum disensus (smart default)
        $occupiedAssignments = DB::table('room_assignments')
            ->join('rooms', 'room_assignments.room_id', '=', 'rooms.id')
            ->where('rooms.dormitory_id', $dormitoryCensus->dormitory_id)
            ->where('room_assignments.is_active', true)
            ->select('room_assignments.person_id', 'room_assignments.room_id')
            ->get();

        foreach ($occupiedAssignments as $assignment) {
            RoomCensusDetail::firstOrCreate(
                [
                    'dormitory_census_id' => $dormitoryCensus->id,
                    'person_id'           => $assignment->person_id,
                ],
                [
                    'id'                  => Str::uuid()->toString(),
                    'room_id'             => $assignment->room_id,
                    'status'              => RoomCensusDetail::STATUS_PRESENT,
                ]
            );
        }

        $dormitoryCensus->update([
            'status'       => DormitoryCensus::STATUS_SUBMITTED,
            'submitted_by' => $userId,
            'submitted_at' => now(),
        ]);
    }

    /**
     * Konfirmasi sekaligus seluruh santri dalam satu kamar sebagai "Hadir".
     */
    public function bulkConfirmRoom(string $dormitoryCensusId, string $roomId): void
    {
        $dormitoryCensus = DormitoryCensus::findOrFail($dormitoryCensusId);
        
        if ($dormitoryCensus->status === DormitoryCensus::STATUS_APPROVED) {
            throw new DomainException("Sensus ini sudah disetujui.");
        }

        $activeAssignments = RoomAssignment::active()
            ->where('room_id', $roomId)
            ->get();

        DB::transaction(function () use ($dormitoryCensus, $roomId, $activeAssignments) {
            foreach ($activeAssignments as $assignment) {
                RoomCensusDetail::updateOrCreate(
                    [
                        'dormitory_census_id' => $dormitoryCensus->id,
                        'person_id'           => $assignment->person_id,
                    ],
                    [
                        'id'      => Str::uuid()->toString(),
                        'room_id' => $roomId,
                        'status'  => RoomCensusDetail::STATUS_PRESENT,
                    ]
                );
            }
        });
    }

    /**
     * Konfirmasi sekaligus seluruh santri dalam satu komplek/asrama sebagai "Hadir".
     */
    public function bulkConfirmAll(string $dormitoryCensusId): void
    {
        $dormitoryCensus = DormitoryCensus::findOrFail($dormitoryCensusId);
        
        if ($dormitoryCensus->status === DormitoryCensus::STATUS_APPROVED) {
            throw new DomainException("Sensus ini sudah disetujui.");
        }

        $activeAssignments = RoomAssignment::active()
            ->join('rooms', 'room_assignments.room_id', '=', 'rooms.id')
            ->where('rooms.dormitory_id', $dormitoryCensus->dormitory_id)
            ->select('room_assignments.*', 'rooms.id as room_id')
            ->get();

        DB::transaction(function () use ($dormitoryCensus, $activeAssignments) {
            foreach ($activeAssignments as $assignment) {
                RoomCensusDetail::updateOrCreate(
                    [
                        'dormitory_census_id' => $dormitoryCensus->id,
                        'person_id'           => $assignment->person_id,
                    ],
                    [
                        'id'      => Str::uuid()->toString(),
                        'room_id' => $assignment->room_id,
                        'status'  => RoomCensusDetail::STATUS_PRESENT,
                    ]
                );
            }
        });
    }

    /**
     * Setujui sensus (approved).
     * - Melakukan mutasi kamar otomatis jika ada santri berstatus 'moved'.
     * - Menyinkronkan usulan perubahan profil santri ke tabel santri_profiles.
     * - Sinkronisasi data wali dan mendeteksi relasi saudara kandung.
     */
    public function approveCensus(string $dormitoryCensusId): void
    {
        $dormitoryCensus = DormitoryCensus::with('details.person')->findOrFail($dormitoryCensusId);

        if ($dormitoryCensus->status !== DormitoryCensus::STATUS_SUBMITTED) {
            throw new DomainException("Hanya sensus berstatus 'Menunggu Verifikasi' yang dapat disetujui.");
        }

        DB::transaction(function () use ($dormitoryCensus) {
            $dormitoryService = app(DormitoryService::class);
            $guardianService  = app(GuardianService::class);
            $siblingService   = app(SiblingService::class);

            foreach ($dormitoryCensus->details as $detail) {
                // 1. Jika santri pindah kamar, lepaskan dari kamar lama
                if ($detail->status === RoomCensusDetail::STATUS_MOVED) {
                    $activeAssignment = DB::table('room_assignments')
                        ->where('person_id', $detail->person_id)
                        ->where('is_active', true)
                        ->first();

                    if ($activeAssignment) {
                        $dormitoryService->unassignRoom($activeAssignment->id);
                    }
                }

                // 2. Sinkronisasi profil santri (jika ada usulan perubahan)
                if (!empty($detail->profile_updates)) {
                    $profile = SantriProfile::firstOrCreate(
                        ['person_id' => $detail->person_id],
                        ['id' => Str::uuid()->toString()]
                    );
                    $profile->applyUpdates($detail->profile_updates);

                    // Deteksi saudara dari data orang tua baru
                    $siblingService->autoLinkFromCensusData($detail->person_id);

                    // Check jika ada usulan saudara kandung manual
                    if (isset($detail->profile_updates['sibling']) && !empty($detail->profile_updates['sibling']['name'])) {
                        $sibData = $detail->profile_updates['sibling'];
                        $siblingPerson = Person::where('name', 'LIKE', '%' . $sibData['name'] . '%')
                            ->when(!empty($sibData['nik_nis']), function ($q) use ($sibData) {
                                return $q->orWhere(fn($sub) =>
                                    $sub->where('nik', $sibData['nik_nis'])
                                        ->orWhereHas('santriProfile', fn($sp) =>
                                            $sp->where('additional_info->nis', $sibData['nik_nis'])
                                        )
                                );
                            })
                            ->where('id', '!=', $detail->person_id)
                            ->first();

                        if ($siblingPerson) {
                            $first = $detail->person_id < $siblingPerson->id ? $detail->person_id : $siblingPerson->id;
                            $second = $detail->person_id < $siblingPerson->id ? $siblingPerson->id : $detail->person_id;

                            \App\Modules\Kepengasuhan\Models\SantriSibling::updateOrCreate(
                                [
                                    'person_id' => $first,
                                    'sibling_person_id' => $second,
                                ],
                                [
                                    'id'                       => Str::uuid()->toString(),
                                    'relationship'             => $sibData['relationship'] ?? 'saudara',
                                    'auto_detected'            => false,
                                    'is_confirmed'             => true,
                                    'confirmed_by'             => $dormitoryCensus->submitted_by,
                                    'confirmed_at'             => now(),
                                    'is_eligible_for_discount' => true,
                                ]
                            );

                            $siblingService->updateSiblingFlags($first);
                            $siblingService->updateSiblingFlags($second);
                        }
                    }
                }

                // 3. Sinkronisasi data wali (jika diusulkan)
                if ($detail->has_guardian_update && !empty($detail->guardian_updates)) {
                    $guardian = $guardianService->createOrFindGuardian([
                        'name'          => $detail->guardian_updates['name'],
                        'phone_primary' => $detail->guardian_updates['phone_primary'] ?? '',
                        'address'       => $detail->guardian_updates['address'] ?? '',
                        'city'          => $detail->guardian_updates['city'] ?? '',
                    ]);

                    $guardianService->linkGuardianToSantri(
                        $guardian->id,
                        $detail->person_id,
                        $detail->guardian_updates['relationship'] ?? 'wali_resmi',
                        true // Set as primary
                    );
                }
            }

            $dormitoryCensus->update(['status' => DormitoryCensus::STATUS_APPROVED]);
        });
    }

    /**
     * Tolak/kembalikan sensus untuk direvisi oleh musyrif.
     */
    public function rejectCensus(string $dormitoryCensusId, string $notes): void
    {
        $dormitoryCensus = DormitoryCensus::findOrFail($dormitoryCensusId);

        if ($dormitoryCensus->status !== DormitoryCensus::STATUS_SUBMITTED) {
            throw new DomainException("Hanya sensus berstatus 'Menunggu Verifikasi' yang dapat dikembalikan.");
        }

        $dormitoryCensus->update([
            'status' => DormitoryCensus::STATUS_PENDING,
            'notes'  => $notes,
        ]);
    }

    /**
     * Ambil periode sensus yang sedang aktif.
     */
    public function getActivePeriod(): ?CensusPeriod
    {
        return CensusPeriod::where('status', CensusPeriod::STATUS_ACTIVE)->latest()->first();
    }

    /**
     * Cari DormitoryCensus untuk asrama tertentu dalam periode aktif.
     */
    public function getDormitoryCensusForDormitory(string $dormitoryId, ?string $periodId = null): ?DormitoryCensus
    {
        $period = $periodId
            ? CensusPeriod::find($periodId)
            : $this->getActivePeriod();

        if (!$period) {
            return null;
        }

        return DormitoryCensus::where('census_period_id', $period->id)
            ->where('dormitory_id', $dormitoryId)
            ->first();
    }
}
