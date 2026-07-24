<?php

namespace App\Modules\Kepengasuhan\Http\Resources;

use App\Modules\Core\Http\Resources\PersonResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityAttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'activity_id' => $this->activity_id,
            'activity'    => new ActivityResource($this->whenLoaded('activity')),
            'person_id'   => $this->person_id,
            'person'      => new PersonResource($this->whenLoaded('person')),
            'status'      => $this->status,
            'notes'       => $this->notes,
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
