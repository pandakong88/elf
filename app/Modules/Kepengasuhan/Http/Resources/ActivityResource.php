<?php

namespace App\Modules\Kepengasuhan\Http\Resources;

use App\Modules\Core\Http\Resources\MasterDataResource;
use App\Modules\Core\Http\Resources\OrganizationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'organization_id'  => $this->organization_id,
            'organization'     => new OrganizationResource($this->whenLoaded('organization')),
            'activity_type_id' => $this->activity_type_id,
            'activity_type'    => new MasterDataResource($this->whenLoaded('activityType')),
            'name'             => $this->name,
            'date'             => $this->date?->toDateString(),
            'description'      => $this->description,
            'attendances'      => ActivityAttendanceResource::collection($this->whenLoaded('attendances')),
            'created_at'       => $this->created_at?->toIso8601String(),
        ];
    }
}
