<?php

namespace App\Modules\Core\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'parent_id'   => $this->parent_id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'type'        => $this->type,
            'description' => $this->description,
            'is_active'   => $this->is_active,
            'parent'      => new OrganizationResource($this->whenLoaded('parent')),
            'children'    => OrganizationResource::collection($this->whenLoaded('children')),
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
