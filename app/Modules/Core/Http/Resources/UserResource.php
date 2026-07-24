<?php

namespace App\Modules\Core\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @var bool Jika true, sertakan permissions dalam response (hanya untuk /me endpoint).
     */
    public bool $withPermissions = false;

    public static function withPermissions(mixed $resource): static
    {
        $instance = new static($resource);
        $instance->withPermissions = true;

        return $instance;
    }

    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'email'            => $this->email,
            'username'         => $this->username,
            'is_active'        => $this->is_active,
            'email_verified_at'=> $this->email_verified_at?->toIso8601String(),

            // Person profile — hanya jika di-load
            'person'           => new PersonResource($this->whenLoaded('person')),

            // Roles selalu disertakan (nama saja, efisien)
            'roles'            => $this->whenLoaded('roles', fn() =>
                $this->roles->pluck('name')->values()
            ),

            // Permissions HANYA di endpoint /me — jangan expose di listing
            'permissions'      => $this->when(
                $this->withPermissions,
                fn() => $this->getAllPermissions()->pluck('name')->values()
            ),

            // Organization IDs untuk scope di frontend/Flutter
            'organization_ids' => $this->when(
                $this->withPermissions,
                fn() => $this->getOrganizationIds()
            ),

            'created_at'       => $this->created_at?->toIso8601String(),
        ];
    }
}
