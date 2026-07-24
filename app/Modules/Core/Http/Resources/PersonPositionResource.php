<?php

namespace App\Modules\Core\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonPositionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'position'   => $this->whenLoaded('position', fn() => [
                'id'    => $this->position->id,
                'name'  => $this->position->name,
                'level' => $this->position->level,
            ]),
            'valid_from'  => $this->valid_from?->format('Y-m-d'),
            'valid_until' => $this->valid_until?->format('Y-m-d'),
            'notes'       => $this->notes,
        ];
    }
}
