<?php

namespace App\Livewire\Kepengasuhan;

use Livewire\Component;
use App\Livewire\Concerns\SendsToast;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Kepengasuhan\Services\DormitoryService;
use App\Modules\Kepengasuhan\Services\CensusV3AccessService;
use App\Modules\Kepengasuhan\Services\SantriStatusService;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Kepengasuhan\Models\SantriProfile;
use App\Modules\Kepengasuhan\Models\Locker;
use App\Modules\Madrasah\Models\MadrasahEnrollment;
use App\Modules\Madrasah\Models\MadrasahKelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PapanMutasiSantri extends Component
{
    use SendsToast;

    // Filter gender scope: 'L', 'P', or null (admin switchable)
    public ?string $genderScope = null;
    public bool $isGenderLocked = false;

    // Search query for instant student finding
    public string $search = '';

    // Collapsed dormitories: array of dormitory IDs
    public array $collapsedDorms = [];

    // Pengurutan santri di kamar: 'name_asc' (A-Z), 'name_desc' (Z-A), 'class_asc' (Kelas), 'nis_asc' (NIS)
    public string $sortBy = 'name_asc';

    // Modal Review & Konfirmasi sebelum commit permanen
    public bool $showReviewModal = false;

    // Staged moves awaiting apply:
    // [person_id => ['person_id' => ..., 'person_name' => ..., 'person_nis' => ..., 'person_class' => ..., 'from_room_id' => ..., 'from_room_name' => ..., 'target_type' => 'room'|'laju'|'boyong'|'unassigned', 'target_room_id' => ..., 'target_room_name' => ...]]
    public array $stagedMoves = [];

    // -------------------------------------------------------------------------
    // Modal Pendaftaran Santri Baru (Sinkron dengan Pusat Kendali)
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
    public string  $newSantriEntryPath     = 'reguler';
    public string  $newSantriFormalGrade   = '';
    public string  $formAcademicYear       = '';

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

    protected DormitoryService $dormitoryService;
    protected SantriStatusService $statusService;
    protected CensusV3AccessService $accessService;

    public function boot(
        DormitoryService $dormitoryService,
        SantriStatusService $statusService,
        CensusV3AccessService $accessService
    ): void {
        $this->dormitoryService = $dormitoryService;
        $this->statusService    = $statusService;
        $this->accessService    = $accessService;
    }

    public function mount(): void
    {
        $user = auth()->user();
        if (!$user || !($user->can('manage-sensus-v3') || $user->can('manage-asrama') || $user->can('input-census-v3'))) {
            abort(403, 'Akses ditolak. Anda tidak berwenang mengakses Papan Mutasi Santri.');
        }

        $this->formAcademicYear = date('Y') . '/' . (date('Y') + 1);

        $detectedScope = $this->accessService->genderScope($user);
        if ($detectedScope) {
            $this->genderScope    = $detectedScope;
            $this->isGenderLocked = true;
        } else {
            // Admin pusat default ke Putra ('L'), dapat diganti tombol switch
            $this->genderScope    = 'L';
            $this->isGenderLocked = false;
        }
    }

    public function switchGender(string $gender): void
    {
        if ($this->isGenderLocked) {
            return;
        }

        if (in_array($gender, ['L', 'P'])) {
            $this->genderScope = $gender;
            $this->stagedMoves = [];
        }
    }

    public function toggleDormCollapse(string $dormId): void
    {
        if (in_array($dormId, $this->collapsedDorms)) {
            $this->collapsedDorms = array_diff($this->collapsedDorms, [$dormId]);
        } else {
            $this->collapsedDorms[] = $dormId;
        }
    }

    public function collapseAll(): void
    {
        $dorms = Dormitory::where('is_active', true)
            ->where('gender', $this->genderScope)
            ->pluck('id')
            ->toArray();
        $this->collapsedDorms = $dorms;
    }

    public function expandAll(): void
    {
        $this->collapsedDorms = [];
    }

    public function openReviewModal(): void
    {
        if (empty($this->stagedMoves)) {
            $this->toastInfo('Belum ada perubahan mutasi untuk direview.');
            return;
        }
        $this->showReviewModal = true;
    }

    public function closeReviewModal(): void
    {
        $this->showReviewModal = false;
    }

    /**
     * Catat pergeseran santri (stage move) sementara sebelum diterapkan.
     */
    public function stageMove(string $personId, string $targetType, ?string $targetValue = null): void
    {
        $person = Person::with(['santriProfile', 'activeMadrasahEnrollment.kelas'])->find($personId);
        if (!$person) {
            return;
        }

        $personNis   = $person->santriProfile?->nis ?? '-';
        $personClass = $person->activeMadrasahEnrollment?->kelas?->name ?? 'Santri';

        // Cari kamar saat ini dari penugasan aktif
        $currentAssignment = RoomAssignment::with('room.dormitory')
            ->where('person_id', $personId)
            ->where('is_active', true)
            ->first();

        $role = PersonRole::where('person_id', $personId)->where('role_type', 'santri')->first();

        $fromRoomId   = $currentAssignment?->room_id;
        $fromRoomName = $currentAssignment 
            ? ($currentAssignment->room->dormitory->name . ' - ' . $currentAssignment->room->name) 
            : ($role?->presence_status === 'laju' ? 'Santri Laju' : 'Tanpa Kamar');

        if ($targetType === 'room') {
            if ($targetValue === $fromRoomId) {
                // Dikembalikan ke kamar asal -> unstage
                unset($this->stagedMoves[$personId]);
                if (empty($this->stagedMoves)) {
                    $this->showReviewModal = false;
                }
                $this->toastInfo("{$person->name} dikembalikan ke kamar asal.");
                return;
            }

            $targetRoom = Room::with('dormitory')->find($targetValue);
            if (!$targetRoom) {
                $this->toastError('Kamar tujuan tidak ditemukan.');
                return;
            }

            $this->stagedMoves[$personId] = [
                'person_id'        => $personId,
                'person_name'      => $person->name,
                'person_nis'       => $personNis,
                'person_class'     => $personClass,
                'from_room_id'     => $fromRoomId,
                'from_room_name'   => $fromRoomName,
                'target_type'      => 'room',
                'target_room_id'   => $targetValue,
                'target_room_name' => $targetRoom->dormitory->name . ' - ' . $targetRoom->name,
            ];

            $this->toastSuccess("{$person->name} diusulkan pindah ke {$targetRoom->name}.");
        } elseif ($targetType === 'laju') {
            if ($fromRoomId === null && $role?->presence_status === 'laju') {
                unset($this->stagedMoves[$personId]);
                if (empty($this->stagedMoves)) {
                    $this->showReviewModal = false;
                }
                $this->toastInfo("{$person->name} sudah berstatus Santri Laju.");
                return;
            }

            $this->stagedMoves[$personId] = [
                'person_id'        => $personId,
                'person_name'      => $person->name,
                'person_nis'       => $personNis,
                'person_class'     => $personClass,
                'from_room_id'     => $fromRoomId,
                'from_room_name'   => $fromRoomName,
                'target_type'      => 'laju',
                'target_room_id'   => null,
                'target_room_name' => 'Santri Laju (Pulang-Pergi)',
            ];
            $this->toastSuccess("{$person->name} diusulkan menjadi Santri Laju.");
        } elseif ($targetType === 'boyong') {
            $this->stagedMoves[$personId] = [
                'person_id'        => $personId,
                'person_name'      => $person->name,
                'person_nis'       => $personNis,
                'person_class'     => $personClass,
                'from_room_id'     => $fromRoomId,
                'from_room_name'   => $fromRoomName,
                'target_type'      => 'boyong',
                'target_room_id'   => null,
                'target_room_name' => 'Usulan Boyong (Keluar)',
            ];
            $this->toastSuccess("{$person->name} diusulkan Boyong.");
        } elseif ($targetType === 'tanpa_keterangan') {
            $this->stagedMoves[$personId] = [
                'person_id'        => $personId,
                'person_name'      => $person->name,
                'person_nis'       => $personNis,
                'person_class'     => $personClass,
                'from_room_id'     => $fromRoomId,
                'from_room_name'   => $fromRoomName,
                'target_type'      => 'tanpa_keterangan',
                'target_room_id'   => $fromRoomId,
                'target_room_name' => 'Tanpa Keterangan (Kamar Ditahan)',
            ];
            $this->toastSuccess("{$person->name} diusulkan Tanpa Keterangan (kamar tetap ditahan).");
        } elseif ($targetType === 'unassigned') {
            if ($fromRoomId === null && $role?->presence_status !== 'laju') {
                unset($this->stagedMoves[$personId]);
                if (empty($this->stagedMoves)) {
                    $this->showReviewModal = false;
                }
                $this->toastInfo("{$person->name} dikembalikan ke status awal.");
                return;
            }

            $this->stagedMoves[$personId] = [
                'person_id'        => $personId,
                'person_name'      => $person->name,
                'person_nis'       => $personNis,
                'person_class'     => $personClass,
                'from_room_id'     => $fromRoomId,
                'from_room_name'   => $fromRoomName,
                'target_type'      => 'unassigned',
                'target_room_id'   => null,
                'target_room_name' => 'Lepas dari Kamar (Unassigned)',
            ];
            $this->toastSuccess("{$person->name} dilepas dari kamar.");
        }
    }

    /**
     * Batalkan pergeseran satu santri.
     */
    public function unstageMove(string $personId): void
    {
        if (isset($this->stagedMoves[$personId])) {
            $name = $this->stagedMoves[$personId]['person_name'] ?? 'Santri';
            unset($this->stagedMoves[$personId]);
            if (empty($this->stagedMoves)) {
                $this->showReviewModal = false;
            }
            $this->toastInfo("Perubahan untuk {$name} dibatalkan.");
        }
    }

    /**
     * Batalkan seluruh pergeseran yang belum disimpan.
     */
    public function resetAllMoves(): void
    {
        $this->stagedMoves = [];
        $this->showReviewModal = false;
        $this->toastInfo('Seluruh pergeseran sementara dibatalkan.');
    }

    /**
     * Terapkan seluruh mutasi kamar dan status langsung ke database secara transaksional.
     */
    public function applyMutations(): void
    {
        if (empty($this->stagedMoves)) {
            $this->toastInfo('Belum ada perubahan mutasi untuk diterapkan.');
            return;
        }

        $userId   = auth()->id();
        $appliedCount = 0;
        $failedCount  = 0;
        $errors       = [];

        DB::beginTransaction();
        try {
            foreach ($this->stagedMoves as $personId => $move) {
                try {
                    if ($move['target_type'] === 'room' && $move['target_room_id']) {
                        // 1. Eksekusi pindah kamar
                        $this->dormitoryService->assignRoom(
                            $move['target_room_id'],
                            $personId,
                            now()->toDateString()
                        );

                        // Jika santri sebelumnya berstatus boyong / non-aktif, aktifkan kembali
                        $role = PersonRole::where('person_id', $personId)->where('role_type', 'santri')->first();
                        if ($role) {
                            if (!$role->isActiveEnrollment()) {
                                $this->statusService->changeEnrollmentStatus(
                                    $role->id,
                                    'aktif',
                                    $userId,
                                    'Diaktifkan kembali via penempatan kamar Papan Mutasi'
                                );
                                $role->refresh();
                            }

                            // Jika sebelumnya santri laju, ubah status keberadaannya kembali menjadi mukim
                            if ($role->presence_status === 'laju') {
                                $this->statusService->changePresenceStatus(
                                    $role->id,
                                    'mukim',
                                    $userId,
                                    null,
                                    'Beralih menjadi Santri Mukim via penempatan kamar Papan Mutasi'
                                );
                            }
                        }

                        $appliedCount++;
                    } elseif ($move['target_type'] === 'laju') {
                        // 2. Eksekusi ubah ke santri laju (presence_status = laju)
                        $role = PersonRole::where('person_id', $personId)->where('role_type', 'santri')->first();
                        if ($role) {
                            if (!$role->isActiveEnrollment()) {
                                $this->statusService->changeEnrollmentStatus(
                                    $role->id,
                                    'aktif',
                                    $userId,
                                    'Diaktifkan kembali sebagai Santri Laju via Papan Mutasi'
                                );
                                $role->refresh();
                            }

                            $this->statusService->changePresenceStatus(
                                $role->id,
                                'laju',
                                $userId,
                                null,
                                'Mutasi laju via Papan Kendali Santri'
                            );
                        } else {
                            RoomAssignment::where('person_id', $personId)
                                ->where('is_active', true)
                                ->update([
                                    'is_active'   => false,
                                    'valid_until' => now()->toDateString(),
                                ]);
                        }

                        // Lepaskan seluruh loker aktif milik santri laju
                        Locker::where('assigned_person_id', $personId)
                            ->update(['assigned_person_id' => null]);

                        $appliedCount++;
                    } elseif ($move['target_type'] === 'boyong') {
                        // 3. Eksekusi boyong (keluar resmi)
                        $role = PersonRole::where('person_id', $personId)->where('role_type', 'santri')->first();
                        if ($role) {
                            $this->statusService->changeEnrollmentStatus(
                                $role->id,
                                'keluar_resmi',
                                $userId,
                                'Boyong via Papan Kendali Santri'
                            );
                        }
                        // Nonaktifkan penempatan kamar
                        RoomAssignment::where('person_id', $personId)
                            ->where('is_active', true)
                            ->update([
                                'is_active'   => false,
                                'valid_until' => now()->toDateString(),
                            ]);

                        // Lepaskan seluruh loker aktif milik santri boyong
                        Locker::where('assigned_person_id', $personId)
                            ->update(['assigned_person_id' => null]);

                        $appliedCount++;
                    } elseif ($move['target_type'] === 'tanpa_keterangan') {
                        // Eksekusi status tanpa keterangan (kamar & kelas tetap ditahan)
                        $role = PersonRole::where('person_id', $personId)->where('role_type', 'santri')->first();
                        if ($role) {
                            $this->statusService->changeEnrollmentStatus(
                                $role->id,
                                'tanpa_keterangan',
                                $userId,
                                'Status diubah ke Tanpa Keterangan via Papan Mutasi'
                            );
                        }
                        $appliedCount++;
                    } elseif ($move['target_type'] === 'unassigned') {
                        // 4. Lepas dari kamar (kembali ke antrean tanpa kamar)
                        RoomAssignment::where('person_id', $personId)
                            ->where('is_active', true)
                            ->update([
                                'is_active'   => false,
                                'valid_until' => now()->toDateString(),
                            ]);

                        // Lepaskan loker aktif milik santri di kamar lama
                        Locker::where('assigned_person_id', $personId)
                            ->update(['assigned_person_id' => null]);

                        // Jika sebelumnya boyong atau laju, sesuaikan status keaktifan dan jadikan santri mukim antre kamar
                        $role = PersonRole::where('person_id', $personId)->where('role_type', 'santri')->first();
                        if ($role) {
                            if (!$role->isActiveEnrollment()) {
                                $this->statusService->changeEnrollmentStatus(
                                    $role->id,
                                    'aktif',
                                    $userId,
                                    'Diaktifkan kembali (lepas kamar) via Papan Mutasi'
                                );
                                $role->refresh();
                            }
                            if ($role->presence_status === 'laju') {
                                $this->statusService->changePresenceStatus(
                                    $role->id,
                                    'mukim',
                                    $userId,
                                    null,
                                    'Dilepas dari status laju menjadi santri mukim antre kamar'
                                );
                            }
                        }

                        $appliedCount++;
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = "Gagal memproses {$move['person_name']}: " . $e->getMessage();
                }
            }

            DB::commit();

            $this->stagedMoves = [];
            $this->showReviewModal = false;
            $this->toastSuccess("Sukses menerapkan {$appliedCount} mutasi santri ke sistem aktif!");

            if (!empty($errors)) {
                $this->toastWarning("Terdapat {$failedCount} kendala: " . implode(', ', array_slice($errors, 0, 2)));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->toastError('Gagal menerapkan mutasi: ' . $e->getMessage());
        }
    }

    /**
     * Hitung struktur data live untuk papan kanban.
     */
    public function getBoardStructureProperty(): array
    {
        $gender = $this->genderScope ?? 'L';

        // 1. Ambil seluruh asrama aktif sesuai gender
        $dormitories = Dormitory::with(['rooms' => function ($rq) {
            $rq->where('is_active', true)
               ->with(['currentAssignments.person.santriProfile', 'currentAssignments.person.activeMadrasahEnrollment.kelas'])
               ->orderBy('name');
        }])
        ->where('is_active', true)
        ->where('gender', $gender)
        ->orderBy('name')
        ->get();

        // 2. Kumpulkan seluruh santri yang aktif di gender ini
        $allAssignedPersons = [];
        $dormsData = [];

        foreach ($dormitories as $dorm) {
            $dormRooms = [];
            $dormOccupantTotal = 0;
            $dormCapacityTotal = 0;

            foreach ($dorm->rooms as $room) {
                $dormCapacityTotal += $room->capacity;

                // Hitung penghuni asli
                $roomOccupants = [];
                foreach ($room->currentAssignments as $asg) {
                    if (!$asg->person) continue;
                    $pid = $asg->person_id;
                    $allAssignedPersons[$pid] = true;

                    $roomOccupants[$pid] = [
                        'person_id'          => $pid,
                        'name'               => $asg->person->name,
                        'nis'                => $asg->person->santriProfile?->nis ?? '-',
                        'class'              => $asg->person->activeMadrasahEnrollment?->kelas?->name ?? 'Santri',
                        'original_room_id'   => $room->id,
                        'original_room_name' => $dorm->name . ' - ' . $room->name,
                    ];
                }

                $dormRooms[$room->id] = [
                    'id'          => $room->id,
                    'name'        => $room->name,
                    'capacity'    => $room->capacity,
                    'dormitory_id'=> $dorm->id,
                    'dorm_name'   => $dorm->name,
                    'occupants'   => $roomOccupants,
                ];
            }

            $dormsData[$dorm->id] = [
                'id'             => $dorm->id,
                'name'           => $dorm->name,
                'gender'         => $dorm->gender,
                'is_collapsed'   => in_array($dorm->id, $this->collapsedDorms),
                'capacity_total' => $dormCapacityTotal,
                'rooms'          => $dormRooms,
            ];
        }

        // 3. Ambil santri unassigned (aktif di gender ini, mukim tapi belum punya kamar aktif)
        $unassignedQuery = PersonRole::with(['person.santriProfile', 'person.activeMadrasahEnrollment.kelas'])
            ->where('role_type', 'santri')
            ->where('is_active', true)
            ->whereIn('enrollment_status', ['aktif', 'calon'])
            ->where(function ($q) {
                $q->whereNull('presence_status')
                  ->orWhere('presence_status', '!=', 'laju');
            })
            ->whereHas('person', fn($pq) => $pq->where('gender', $gender));

        $unassignedList = [];
        foreach ($unassignedQuery->get() as $role) {
            if (!$role->person) continue;
            $pid = $role->person_id;
            if (!isset($allAssignedPersons[$pid])) {
                $unassignedList[$pid] = [
                    'person_id'          => $pid,
                    'name'               => $role->person->name,
                    'nis'                => $role->person->santriProfile?->nis ?? '-',
                    'class'              => $role->person->activeMadrasahEnrollment?->kelas?->name ?? 'Santri Baru',
                    'original_room_id'   => null,
                    'original_room_name' => 'Tanpa Kamar',
                ];
            }
        }

        // 4. Ambil santri laju (aktif di gender ini dengan status keberadaan laju)
        $lajuQuery = PersonRole::with(['person.santriProfile', 'person.activeMadrasahEnrollment.kelas'])
            ->where('role_type', 'santri')
            ->where('is_active', true)
            ->whereIn('enrollment_status', ['aktif', 'calon', 'aktif_laju'])
            ->where('presence_status', 'laju')
            ->whereHas('person', fn($pq) => $pq->where('gender', $gender));

        $lajuList = [];
        foreach ($lajuQuery->get() as $role) {
            if (!$role->person) continue;
            $pid = $role->person_id;
            if (!isset($allAssignedPersons[$pid])) {
                $lajuList[$pid] = [
                    'person_id'          => $pid,
                    'name'               => $role->person->name,
                    'nis'                => $role->person->santriProfile?->nis ?? '-',
                    'class'              => $role->person->activeMadrasahEnrollment?->kelas?->name ?? 'Santri Laju',
                    'original_room_id'   => null,
                    'original_room_name' => 'Santri Laju',
                ];
            }
        }

        // 5. Terapkan $stagedMoves ke tampilan struktur
        $dockLaju            = $lajuList;
        $dockBoyong          = [];
        $dockTanpaKeterangan = [];
        $dockUnassigned      = $unassignedList;

        // Flatten all occupants to easy lookup
        $allPersonsMaster = [];
        foreach ($dormsData as $d) {
            foreach ($d['rooms'] as $r) {
                foreach ($r['occupants'] as $pid => $p) {
                    $allPersonsMaster[$pid] = $p;
                }
            }
        }
        foreach ($dockUnassigned as $pid => $p) {
            $allPersonsMaster[$pid] = $p;
        }
        foreach ($dockLaju as $pid => $p) {
            $allPersonsMaster[$pid] = $p;
        }

        // Apply moves
        foreach ($this->stagedMoves as $pid => $move) {
            $person = $allPersonsMaster[$pid] ?? null;
            if (!$person) continue;

            $origRoomId = $person['original_room_id'];
            $targetType = $move['target_type'];

            // Hapus dari lokasi semula jika ada
            if ($origRoomId) {
                foreach ($dormsData as $did => $dorm) {
                    if (isset($dormsData[$did]['rooms'][$origRoomId]['occupants'][$pid])) {
                        unset($dormsData[$did]['rooms'][$origRoomId]['occupants'][$pid]);
                        break;
                    }
                }
            } else {
                unset($dockUnassigned[$pid]);
                unset($dockLaju[$pid]);
            }

            // Person payload with staged badge
            $stagedPerson = array_merge($person, [
                'is_staged'        => true,
                'target_type'      => $targetType,
                'target_room_name' => $move['target_room_name'],
            ]);

            // Tambahkan ke target
            if ($targetType === 'room' && $move['target_room_id']) {
                $targetRoomId = $move['target_room_id'];
                foreach ($dormsData as $did => $dorm) {
                    if (isset($dormsData[$did]['rooms'][$targetRoomId])) {
                        $dormsData[$did]['rooms'][$targetRoomId]['occupants'][$pid] = $stagedPerson;
                        break;
                    }
                }
            } elseif ($targetType === 'laju') {
                $dockLaju[$pid] = $stagedPerson;
            } elseif ($targetType === 'boyong') {
                $dockBoyong[$pid] = $stagedPerson;
            } elseif ($targetType === 'tanpa_keterangan') {
                $dockTanpaKeterangan[$pid] = $stagedPerson;
            } elseif ($targetType === 'unassigned') {
                $dockUnassigned[$pid] = $stagedPerson;
            }
        }

        // Flatten list of all rooms for instant client-side search in Quick Transfer modal
        $allRoomsList = [];
        foreach ($dormsData as $dorm) {
            foreach ($dorm['rooms'] as $room) {
                $allRoomsList[] = [
                    'id'              => $room['id'],
                    'name'            => $room['name'],
                    'dormitory_id'    => $dorm['id'],
                    'dorm_name'       => $dorm['name'],
                    'full_label'      => $dorm['name'] . ' - ' . $room['name'],
                    'capacity'        => $room['capacity'],
                    'occupants_count' => count($room['occupants']),
                ];
            }
        }

        // Callback Pengurutan Santri
        $sortCallback = function ($a, $b) {
            if ($this->sortBy === 'class_asc') {
                $rankA = $this->getClassRank($a['class'] ?? '');
                $rankB = $this->getClassRank($b['class'] ?? '');
                if ($rankA !== $rankB) {
                    return $rankA <=> $rankB;
                }
                return strnatcasecmp($a['name'] ?? '', $b['name'] ?? '');
            } elseif ($this->sortBy === 'class_desc') {
                $rankA = $this->getClassRank($a['class'] ?? '');
                $rankB = $this->getClassRank($b['class'] ?? '');
                if ($rankA !== $rankB) {
                    return $rankB <=> $rankA;
                }
                return strnatcasecmp($a['name'] ?? '', $b['name'] ?? '');
            } elseif ($this->sortBy === 'nis_asc') {
                $cmp = strnatcasecmp($a['nis'] ?? '', $b['nis'] ?? '');
                if ($cmp !== 0) return $cmp;
                return strnatcasecmp($a['name'] ?? '', $b['name'] ?? '');
            } elseif ($this->sortBy === 'name_desc') {
                return strnatcasecmp($b['name'] ?? '', $a['name'] ?? '');
            } else { // default 'name_asc'
                return strnatcasecmp($a['name'] ?? '', $b['name'] ?? '');
            }
        };

        // Re-index and sort array for view (safe, no references)
        $cleanDorms = [];
        foreach ($dormsData as $dorm) {
            $cleanRooms = [];
            foreach ($dorm['rooms'] as $room) {
                $occupants = array_values($room['occupants']);
                usort($occupants, $sortCallback);
                $room['occupants'] = $occupants;
                $cleanRooms[] = $room;
            }
            $dorm['rooms'] = $cleanRooms;
            $cleanDorms[] = $dorm;
        }

        $unassignedValues = array_values($dockUnassigned);
        usort($unassignedValues, $sortCallback);

        $lajuValues = array_values($dockLaju);
        usort($lajuValues, $sortCallback);

        $boyongValues = array_values($dockBoyong);
        usort($boyongValues, $sortCallback);

        $tanpaKeteranganValues = array_values($dockTanpaKeterangan);
        usort($tanpaKeteranganValues, $sortCallback);

        // Hitung rincian per kategori mutasi untuk modal review
        $stagedBreakdown = [
            'room'             => 0,
            'laju'             => 0,
            'boyong'           => 0,
            'tanpa_keterangan' => 0,
            'unassigned'       => 0,
        ];
        foreach ($this->stagedMoves as $m) {
            $t = $m['target_type'] ?? 'room';
            $stagedBreakdown[$t] = ($stagedBreakdown[$t] ?? 0) + 1;
        }

        return [
            'dormitories'            => $cleanDorms,
            'dock_unassigned'        => $unassignedValues,
            'dock_laju'              => $lajuValues,
            'dock_boyong'            => $boyongValues,
            'dock_tanpa_keterangan'  => $tanpaKeteranganValues,
            'all_rooms'              => $allRoomsList,
            'staged_count'           => count($this->stagedMoves),
            'staged_list'            => array_values($this->stagedMoves),
            'staged_breakdown'       => $stagedBreakdown,
        ];
    }

    /**
     * Hitung bobot hierarki kelas madrasah pesantren:
     * 1. Awaliyah 1..3 (Tingkat Dasar / Awal) -> 101 - 109
     * 2. Wustho 1..2 (Tingkat Menengah)       -> 201 - 209
     * 3. Ulya 1..2 (Tingkat Tertinggi / Atas) -> 301 - 309
     * 4. Tahasus / Khusus                     -> 401 - 409
     * 5. Santri Baru / Belum Ada Kelas        -> 900
     */
    protected function getClassRank(?string $className): int
    {
        if (!$className) {
            return 900;
        }

        $c = strtolower(trim($className));

        // Ekstrak angka tingkat kelas (1, 2, 3) jika ada
        preg_match('/\d+/', $c, $matches);
        $subLevel = isset($matches[0]) ? (int) $matches[0] : 0;

        // 1. Jenjang Awaliyah / Ula (Tingkat 1)
        if (str_contains($c, 'awaliyah') || str_contains($c, 'ula') || str_contains($c, 'ibtidaiyah')) {
            return 100 + $subLevel;
        }

        // 2. Jenjang Wustho / Tsanawiyah (Tingkat 2)
        if (str_contains($c, 'wustho') || str_contains($c, 'tsanawiyah') || str_contains($c, 'tsanawi')) {
            return 200 + $subLevel;
        }

        // 3. Jenjang Ulya / Aliyah (Tingkat 3 - Paling Tinggi)
        if (str_contains($c, 'ulya') || str_contains($c, 'aliyah')) {
            return 300 + $subLevel;
        }

        // 4. Jenjang Tahasus / Lanjutan
        if (str_contains($c, 'tahasus') || str_contains($c, 'takhossus')) {
            return 400 + $subLevel;
        }

        return 900;
    }

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

    public function updatedNewSantriPresence(): void
    {
        $this->refreshBillingChecklist();
    }

    public function updatedNewSantriKelasId(): void
    {
        $this->refreshBillingChecklist();
    }

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

    public function openNewSantriModal(): void
    {
        $this->newSantriNik           = '';
        $this->newSantriName          = '';
        $this->newSantriGender        = $this->genderScope ?: 'L';
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
                    $this->dormitoryService->assignRoom($this->newSantriRoomId, $person->id, now()->toDateString());
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

    public function render()
    {
        $registrationGender = $this->showNewSantriModal ? $this->newSantriGender : ($this->genderScope ?: null);

        $roomOptions = Room::active()
            ->with(['dormitory', 'currentAssignments'])
            ->when($registrationGender, fn($q) => $q->whereHas('dormitory', fn($dq) => $dq->where('gender', $registrationGender)))
            ->get();

        $kelasOptions = MadrasahKelas::where('is_active', true)
            ->when($registrationGender === 'L', fn($q) => $q->where(fn($sq) => $sq->where('name', 'like', '%(Pa)%')->orWhere('name', 'like', '%Pa%')->orWhere('name', 'like', '%Putra%')->orWhere('name', 'like', '%(L)%')->orWhereHas('enrollments.person', fn($pq) => $pq->where('gender', 'L'))))
            ->when($registrationGender === 'P', fn($q) => $q->where(fn($sq) => $sq->where('name', 'like', '%(Pi)%')->orWhere('name', 'like', '%Pi%')->orWhere('name', 'like', '%Putri%')->orWhere('name', 'like', '%(P)%')->orWhereHas('enrollments.person', fn($pq) => $pq->where('gender', 'P'))))
            ->orderBy('jenjang')
            ->orderBy('name')
            ->get();

        return view('livewire.kepengasuhan.papan-mutasi-santri', [
            'board'        => $this->getBoardStructureProperty(),
            'roomOptions'  => $roomOptions,
            'kelasOptions' => $kelasOptions,
        ])->layout('layouts.app', ['title' => 'Papan Mutasi & Hunian Santri']);
    }
}
