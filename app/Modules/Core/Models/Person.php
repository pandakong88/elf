<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Person extends Model implements HasMedia
{
    use HasFactory, HasUuids, SoftDeletes, InteractsWithMedia;

    protected $table = 'persons';

    protected $fillable = [
        'nik',
        'name',
        'gender',
        'birth_place',
        'birth_date',
        'phone',
        'address',
        'photo',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'deleted_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Media Library
    // -------------------------------------------------------------------------

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')
             ->singleFile()
             ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function roles(): HasMany
    {
        return $this->hasMany(PersonRole::class);
    }

    public function activeRoles(): HasMany
    {
        return $this->hasMany(PersonRole::class)->where('is_active', true);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(PersonPosition::class);
    }

    public function currentPositions(): HasMany
    {
        return $this->hasMany(PersonPosition::class)
                    ->where(function ($q) {
                        $q->whereNull('valid_until')
                          ->orWhere('valid_until', '>=', now()->toDateString());
                    });
    }

    public function userAccount(): HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'person_id');
    }

    public function santriProfile(): HasOne
    {
        return $this->hasOne(\App\Modules\Kepengasuhan\Models\SantriProfile::class, 'person_id');
    }

    public function bills(): HasMany
    {
        return $this->hasMany(\App\Modules\Keuangan\Models\Bill::class, 'person_id');
    }

    public function majekRegistrations(): HasMany
    {
        return $this->hasMany(\App\Modules\Keuangan\Models\MajekRegistration::class, 'person_id');
    }

    public function eventBillItems(): HasMany
    {
        return $this->hasMany(\App\Modules\Keuangan\Models\EventBillItem::class, 'person_id');
    }

    public function roomAssignments(): HasMany
    {
        return $this->hasMany(\App\Modules\Kepengasuhan\Models\RoomAssignment::class, 'person_id');
    }

    public function madrasahEnrollments(): HasMany
    {
        return $this->hasMany(\App\Modules\Madrasah\Models\MadrasahEnrollment::class, 'person_id');
    }

    /**
     * Ambil nilai dari profil santri (kolom langsung atau additional_info JSON).
     */
    public function getProfileAttribute(string $key, mixed $default = null): mixed
    {
        $profile = $this->santriProfile;
        if (!$profile) {
            return $default;
        }

        $directFields = ['school_status', 'school_name', 'major', 'school_year', 'medical_history', 'blood_type'];

        if (in_array($key, $directFields)) {
            return $profile->$key ?? $default;
        }

        return $profile->getAdditional($key, $default);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereHas('activeRoles');
    }

    public function scopeByRole(Builder $query, string $roleType, ?string $organizationId = null): Builder
    {
        return $query->whereHas('roles', function (Builder $q) use ($roleType, $organizationId) {
            $q->where('role_type', $roleType)->where('is_active', true);
            if ($organizationId) {
                $q->where('organization_id', $organizationId);
            }
        });
    }

    public function scopeByOrganization(Builder $query, string $organizationId): Builder
    {
        return $query->whereHas('roles', function (Builder $q) use ($organizationId) {
            $q->where('organization_id', $organizationId)->where('is_active', true);
        });
    }

    public function scopeGender(Builder $query, string $gender): Builder
    {
        return $query->where('gender', $gender);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date?->age;
    }

    public function getNisAttribute(): ?string
    {
        return $this->getProfileAttribute('nis');
    }

    public function getNisnAttribute(): ?string
    {
        return $this->getProfileAttribute('nisn');
    }

    public function isSantri(?string $organizationId = null): bool
    {
        $q = $this->roles()->where('role_type', 'santri')->where('is_active', true);
        if ($organizationId) {
            $q->where('organization_id', $organizationId);
        }

        return $q->exists();
    }
}
