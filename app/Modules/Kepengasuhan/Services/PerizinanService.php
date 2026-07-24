<?php

namespace App\Modules\Kepengasuhan\Services;

use App\Modules\Core\Models\MasterData;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\Perizinan;
use App\Modules\Kepengasuhan\Models\Violation;
use App\Modules\Shared\Workflow\WorkflowEngine;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PerizinanService
{
    public function __construct(
        private readonly WorkflowEngine $workflowEngine
    ) {}

    /**
     * Ajukan perizinan baru untuk santri.
     *
     * @throws DomainException
     */
    public function initiateLeave(array $data, Person $initiator): Perizinan
    {
        $santri = Person::findOrFail($data['person_id']);
        $org = Organization::findOrFail($data['organization_id']);
        $type = MasterData::where('category', 'jenis_izin')->findOrFail($data['permission_type_id']);

        // 1. Validasi: Apakah ada izin aktif yang statusnya 'out' (sedang di luar pondok)?
        // Cek jika bypass tidak diaktifkan (secara default kita block)
        $preventDuplicate = $data['prevent_duplicate'] ?? true;
        if ($preventDuplicate) {
            $hasActiveLeave = Perizinan::where('person_id', $santri->id)
                ->where('status', 'out')
                ->exists();
            if ($hasActiveLeave) {
                throw new DomainException("Santri '{$santri->name}' sedang berada di luar pondok dengan izin aktif.");
            }
        }

        // 2. Validasi: Poin pelanggaran (fleksibel)
        $maxPointsAllowed = $data['max_points_allowed'] ?? null;
        if ($maxPointsAllowed !== null) {
            $currentPoints = Violation::where('person_id', $santri->id)
                ->where('status', '!=', 'resolved')
                ->sum('points');
            if ($currentPoints > $maxPointsAllowed) {
                throw new DomainException("Santri '{$santri->name}' memiliki akumulasi poin pelanggaran sebesar {$currentPoints} (batas maksimal: {$maxPointsAllowed}). Pengajuan izin ditolak.");
            }
        }

        return DB::transaction(function () use ($santri, $org, $type, $data, $initiator) {
            $perizinanId = Str::uuid()->toString();

            // Mulai workflow
            $workflowInstance = $this->workflowEngine->initiate(
                $data['workflow_template_id'],
                'perizinan',
                $perizinanId,
                $initiator
            );

            return Perizinan::create([
                'id'                   => $perizinanId,
                'person_id'            => $santri->id,
                'organization_id'      => $org->id,
                'permission_type_id'   => $type->id,
                'reason'               => $data['reason'],
                'start_date'           => $data['start_date'],
                'end_date'             => $data['end_date'],
                'workflow_instance_id' => $workflowInstance->id,
                'status'               => 'pending',
            ]);
        });
    }

    /**
     * Lepas santri keluar gerbang pondok (checkout).
     * Hanya bisa dilakukan jika status workflow sudah 'approved'.
     *
     * @throws DomainException
     */
    public function checkout(string $perizinanId): Perizinan
    {
        $perizinan = Perizinan::findOrFail($perizinanId);

        // Sync status dari workflow terlebih dahulu
        $perizinan = $this->syncWorkflowStatus($perizinan->id);

        if ($perizinan->status === 'pending') {
            throw new DomainException("Izin belum disetujui (workflow masih pending/in progress).");
        }

        if (in_array($perizinan->status, ['rejected', 'cancelled'])) {
            throw new DomainException("Izin ditolak atau dibatalkan.");
        }

        if ($perizinan->status === 'out') {
            throw new DomainException("Santri sudah melakukan checkout.");
        }

        if (in_array($perizinan->status, ['returned', 'late'])) {
            throw new DomainException("Izin ini sudah selesai.");
        }

        $perizinan->update(['status' => 'out']);

        return $perizinan->fresh();
    }

    /**
     * Validasi santri kembali ke pondok (checkin).
     * Bandingkan dengan end_date untuk menetapkan status 'returned' atau 'late'.
     */
    public function checkin(string $perizinanId, ?string $notes = null): Perizinan
    {
        $perizinan = Perizinan::findOrFail($perizinanId);

        if ($perizinan->status !== 'out') {
            throw new DomainException("Santri tidak sedang berada di luar pondok berdasarkan izin ini.");
        }

        $returnDate = now();
        $isLate = $returnDate->greaterThan($perizinan->end_date);
        $finalStatus = $isLate ? 'late' : 'returned';

        $perizinan->update([
            'actual_return_date' => $returnDate,
            'status'             => $finalStatus,
        ]);

        return $perizinan->fresh();
    }

    /**
     * Sinkronisasi status perizinan dengan status Workflow Instance.
     */
    public function syncWorkflowStatus(string $perizinanId): Perizinan
    {
        $perizinan = Perizinan::findOrFail($perizinanId);

        if ($perizinan->workflowInstance) {
            $wfStatus = $perizinan->workflowInstance->status;

            // Jika status perizinan masih pending/approved awal, ikuti status workflow
            if (in_array($perizinan->status, ['pending', 'approved', 'rejected'])) {
                if ($wfStatus === 'approved') {
                    $perizinan->update(['status' => 'approved']);
                } elseif ($wfStatus === 'rejected') {
                    $perizinan->update(['status' => 'rejected']);
                } elseif ($wfStatus === 'cancelled') {
                    $perizinan->update(['status' => 'cancelled']);
                }
            }
        }

        return $perizinan->fresh();
    }
}
