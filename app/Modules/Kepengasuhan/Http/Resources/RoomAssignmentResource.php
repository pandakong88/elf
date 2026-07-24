<?php

namespace App\Modules\Kepengasuhan\Http\Resources;

use App\Modules\Core\Http\Resources\PersonResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomAssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'room_id'     => $this->room_id,
            'room'        => new RoomResource($this->whenLoaded('room')),
            'person_id'   => $this->person_id,
            'person'      => new PersonResource($this->whenLoaded('person')),
            'valid_from'  => $this->valid_from?->toDateString(),
            'valid_until' => $this->valid_until?->toDateString(),
            'is_active'   => $this->is_active,
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
