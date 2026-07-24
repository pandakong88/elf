<?php

namespace App\Modules\Kepengasuhan\Services;

use App\Modules\Core\Models\MasterData;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\Violation;
use DomainException;
use Illuminate\Support\Str;

class ViolationService
{
    /**
     * Laporkan pelanggaran baru santri.
     */
    public function reportViolation(array $data): Violation
    {
        $santri = Person::findOrFail($data['person_id']);
        $org = Organization::findOrFail($data['organization_id']);
        $reporter = Person::findOrFail($data['reporter_id']);
        $type = MasterData::where('category', 'jenis_pelanggaran')->findOrFail($data['violation_type_id']);

        // Default points dari metadata master_data jika tidak di-override
        $points = $data['points'] ?? 0;
        if ($points === 0 && isset($type->metadata['points'])) {
            $points = (int) $type->metadata['points'];
        }

        return Violation::create([
            'id'                => Str::uuid()->toString(),
            'person_id'         => $santri->id,
            'organization_id'   => $org->id,
            'violation_type_id' => $type->id,
            'reporter_id'       => $reporter->id,
            'violation_date'    => $data['violation_date'] ?? now(),
            'description'       => $data['description'],
            'severity'          => $data['severity'] ?? 'ringan',
            'punishment'        => $data['punishment'] ?? null,
            'points'            => $points,
            'status'            => 'reported',
        ]);
    }

    /**
     * Selesaikan pelanggaran (verifikasi tindakan disiplin/punishment telah dilakukan).
     */
    public function resolveViolation(string $violationId, string $punishmentApplied): Violation
    {
        $violation = Violation::findOrFail($violationId);

        if ($violation->status === 'resolved') {
            throw new DomainException("Pelanggaran ini sudah selesai diproses.");
        }

        $violation->update([
            'punishment' => $punishmentApplied,
            'status'     => 'resolved',
        ]);

        return $violation->fresh();
    }

    /**
     * Hitung total poin pelanggaran aktif (belum diselesaikan).
     */
    public function getCumulativePoints(string $personId): int
    {
        return (int) Violation::where('person_id', $personId)
            ->where('status', '!=', 'resolved')
            ->sum('points');
    }
}
