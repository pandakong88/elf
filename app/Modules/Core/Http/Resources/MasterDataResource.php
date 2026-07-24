<?php

namespace App\Modules\Core\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MasterDataResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'organization_id' => $this->organization_id,
            'category'        => $this->category,
            'code'            => $this->code,
            'name'            => $this->name,
            'description'     => $this->description,
            'metadata'        => $this->metadata,
            'sort_order'      => $this->sort_order,
            'is_active'       => $this->is_active,
        ];
    }
}
