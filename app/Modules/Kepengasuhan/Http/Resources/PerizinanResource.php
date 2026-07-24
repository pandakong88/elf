<?php

namespace App\Modules\Kepengasuhan\Http\Resources;

use App\Modules\Core\Http\Resources\MasterDataResource;
use App\Modules\Core\Http\Resources\OrganizationResource;
use App\Modules\Core\Http\Resources\PersonResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerizinanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'person_id'            => $this->person_id,
            'person'               => new PersonResource($this->whenLoaded('person')),
            'organization_id'      => $this->organization_id,
            'organization'         => new OrganizationResource($this->whenLoaded('organization')),
            'permission_type_id'   => $this->permission_type_id,
            'permission_type'      => new MasterDataResource($this->whenLoaded('permissionType')),
            'reason'               => $this->reason,
            'start_date'           => $this->start_date?->toIso8601String(),
            'end_date'             => $this->end_date?->toIso8601String(),
            'actual_return_date'   => $this->actual_return_date?->toIso8601String(),
            'workflow_instance_id' => $this->workflow_instance_id,
            'workflow_instance'    => $this->whenLoaded('workflowInstance'),
            'status'               => $this->status,
            'created_at'           => $this->created_at?->toIso8601String(),
        ];
    }
}
