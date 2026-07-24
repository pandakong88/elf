<?php

namespace App\Modules\Kepengasuhan\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'dormitory_id' => $this->dormitory_id,
            'dormitory'    => new DormitoryResource($this->whenLoaded('dormitory')),
            'name'         => $this->name,
            'capacity'     => $this->capacity,
            'description'  => $this->description,
            'is_active'    => $this->is_active,
            'assignments'  => RoomAssignmentResource::collection($this->whenLoaded('assignments')),
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
