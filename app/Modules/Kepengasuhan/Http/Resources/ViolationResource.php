<?php

namespace App\Modules\Kepengasuhan\Http\Resources;

use App\Modules\Core\Http\Resources\MasterDataResource;
use App\Modules\Core\Http\Resources\OrganizationResource;
use App\Modules\Core\Http\Resources\PersonResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ViolationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'person_id'         => $this->person_id,
            'person'            => new PersonResource($this->whenLoaded('person')),
            'organization_id'   => $this->organization_id,
            'organization'      => new OrganizationResource($this->whenLoaded('organization')),
            'violation_type_id' => $this->violation_type_id,
            'violation_type'    => new MasterDataResource($this->whenLoaded('violationType')),
            'reporter_id'       => $this->reporter_id,
            'reporter'          => new PersonResource($this->whenLoaded('reporter')),
            'violation_date'    => $this->violation_date?->toIso8601String(),
            'description'       => $this->description,
            'severity'          => $this->severity,
            'punishment'        => $this->punishment,
            'points'            => $this->points,
            'status'            => $this->status,
            'created_at'        => $this->created_at?->toIso8601String(),
        ];
    }
}
