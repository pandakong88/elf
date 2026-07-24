<?php

namespace App\Modules\Core\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'nik'         => $this->nik,
            'name'        => $this->name,
            'gender'      => $this->gender,
            'gender_label'=> $this->gender === 'L' ? 'Laki-laki' : 'Perempuan',
            'birth_place' => $this->birth_place,
            'birth_date'  => $this->birth_date?->format('Y-m-d'),
            'age'         => $this->age,
            'phone'       => $this->phone,
            'address'     => $this->address,
            'photo_url'   => $this->getFirstMediaUrl('photo') ?: null,
            'notes'       => $this->notes,
            'roles'       => PersonRoleResource::collection($this->whenLoaded('roles')),
            'positions'   => PersonPositionResource::collection($this->whenLoaded('positions')),
            'created_at'  => $this->created_at?->toIso8601String(),
            'updated_at'  => $this->updated_at?->toIso8601String(),
        ];
    }
}
