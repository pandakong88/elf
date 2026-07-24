<?php

namespace App\Models;

use App\Modules\Core\Models\Person;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, HasRoles, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'person_id',
        'name',
        'username',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // JWT Contract
    // -------------------------------------------------------------------------

    /**
     * JWT identifier — gunakan UUID user.
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Custom claims yang disertakan dalam JWT payload.
     * Dibiarkan kosong — identifier sudah cukup, data detail di-fetch via /me.
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    // -------------------------------------------------------------------------
    // Organization Scope Helpers (dipakai oleh Policy)
    // -------------------------------------------------------------------------

    /**
     * Ambil semua organization_id tempat user ini bertugas (via person_roles).
     * Dipakai Policy untuk scope data berdasarkan unit.
     *
     * @return array<string>
     */
    public function getOrganizationIds(): array
    {
        if (! $this->person_id) {
            return [];
        }

        return \App\Modules\Core\Models\PersonRole::where('person_id', $this->person_id)
            ->where('is_active', true)
            ->pluck('organization_id')
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Apakah user ini bertugas di organisasi tertentu?
     */
    public function isInOrganization(string $organizationId): bool
    {
        return in_array($organizationId, $this->getOrganizationIds());
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_active', true);
    }
}
