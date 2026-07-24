<?php

namespace App\Modules\Kepengasuhan\Http\Resources;

use App\Modules\Core\Http\Resources\OrganizationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DormitoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'organization_id' => $this->organization_id,
            'organization'    => new OrganizationResource($this->whenLoaded('organization')),
            'name'            => $this->name,
            'gender'          => $this->gender,
            'description'     => $this->description,
            'is_active'       => $this->is_active,
            'rooms'           => RoomResource::collection($this->whenLoaded('rooms')),
            'created_at'      => $this->created_at?->toIso8601String(),
        ];
    }
}
