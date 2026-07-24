<?php

namespace App\Modules\Core\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonRoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'organization_id' => $this->organization_id,
            'organization'    => $this->whenLoaded('organization', fn() => [
                'id'   => $this->organization->id,
                'name' => $this->organization->name,
                'slug' => $this->organization->slug,
            ]),
            'role_type'       => $this->role_type,
            'valid_from'      => $this->valid_from?->format('Y-m-d'),
            'valid_until'     => $this->valid_until?->format('Y-m-d'),
            'is_active'       => $this->is_active,
        ];
    }
}
