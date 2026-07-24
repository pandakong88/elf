<?php

namespace App\Modules\Kepengasuhan\Services;

use App\Modules\Core\Models\MasterData;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\Activity;
use App\Modules\Kepengasuhan\Models\ActivityAttendance;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ActivityService
{
    /**
     * Buat kegiatan baru.
     */
    public function createActivity(array $data): Activity
    {
        $org = Organization::findOrFail($data['organization_id']);
        $type = MasterData::where('category', 'jenis_kegiatan')->findOrFail($data['activity_type_id']);

        return Activity::create([
            'id'               => Str::uuid()->toString(),
            'organization_id'  => $org->id,
            'activity_type_id' => $type->id,
            'name'             => $data['name'],
            'date'             => $data['date'] ?? now()->toDateString(),
            'description'      => $data['description'] ?? null,
        ]);
    }

    /**
     * Catat absensi santri secara kolektif (batch input).
     *
     * @param  string  $activityId
     * @param  array<array{ person_id: string, status: string, notes: ?string }>  $attendances
     */
    public function recordAttendanceBatch(string $activityId, array $attendances): array
    {
        $activity = Activity::findOrFail($activityId);

        return DB::transaction(function () use ($activity, $attendances) {
            $results = [];

            foreach ($attendances as $record) {
                $personId = $record['person_id'];
                $status   = $record['status'];
                $notes    = $record['notes'] ?? null;

                // Pastikan person exists
                $person = Person::findOrFail($personId);

                // Update or create absensi
                $attendance = ActivityAttendance::updateOrCreate(
                    [
                        'activity_id' => $activity->id,
                        'person_id'   => $person->id,
                    ],
                    [
                        'id'          => Str::uuid()->toString(), // jika create baru
                        'status'      => $status,
                        'notes'       => $notes,
                    ]
                );

                $results[] = $attendance;
            }

            return $results;
        });
    }
}
