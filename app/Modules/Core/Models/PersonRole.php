<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonRole extends Model
{
    use HasUuids;

    protected $table = 'person_roles';

    protected $fillable = [
        'person_id',
        'organization_id',
        'role_type',
        'valid_from',
        'valid_until',
        'is_active',
        'enrollment_status',
        'presence_status',
        'presence_status_since',
        'presence_status_until',
        'presence_status_notes',
        'left_at',
    ];

    protected $casts = [
        'is_active'            => 'boolean',
        'valid_from'           => 'date',
        'valid_until'          => 'date',
        'left_at'              => 'date',
        'presence_status_since'=> 'datetime',
        'presence_status_until'=> 'datetime',
    ];

    /**
     * Semua nilai enum enrollment_status yang valid.
     */
    public const ENROLLMENT_STATUSES = [
        'aktif'            => 'Aktif',
        'alumni'           => 'Alumni',
        'keluar_resmi'     => 'Keluar Resmi',
        'dikeluarkan'      => 'Dikeluarkan',
        'tanpa_keterangan' => 'Tanpa Keterangan',
    ];

    /**
     * Status enrollment yang dianggap masih aktif/terdaftar.
     */
    public const ACTIVE_ENROLLMENT_STATUSES = ['aktif'];

    /**
     * Status enrollment yang dianggap sudah tidak aktif/alumni/keluar.
     */
    public const INACTIVE_ENROLLMENT_STATUSES = ['alumni', 'keluar_resmi', 'dikeluarkan', 'tanpa_keterangan'];

    /**
     * Semua nilai enum presence_status yang valid.
     */
    public const PRESENCE_STATUSES = [
        'mukim'          => 'Mukim',
        'laju'           => 'Laju',
        'izin'           => 'Izin',
        'alpa'           => 'Alpa',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeEnrollmentActive(Builder $query): Builder
    {
        return $query->whereIn('enrollment_status', self::ACTIVE_ENROLLMENT_STATUSES);
    }

    public function scopeAlumni(Builder $query): Builder
    {
        return $query->where('enrollment_status', 'alumni');
    }

    public function scopeSantri(Builder $query): Builder
    {
        return $query->where('role_type', 'santri');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getEnrollmentStatusLabelAttribute(): string
    {
        return self::ENROLLMENT_STATUSES[$this->enrollment_status] ?? $this->enrollment_status;
    }

    public function getPresenceStatusLabelAttribute(): string
    {
        return self::PRESENCE_STATUSES[$this->presence_status] ?? ($this->presence_status ?? '-');
    }

    public function isActiveEnrollment(): bool
    {
        return in_array($this->enrollment_status, self::ACTIVE_ENROLLMENT_STATUSES);
    }

    public function isLaju(): bool
    {
        return $this->presence_status === 'laju';
    }

    public function isMukim(): bool
    {
        return $this->presence_status === 'mukim';
    }

    /**
     * Hitung masa mondok dalam satuan bulan.
     * Berguna untuk dashboard alumni.
     */
    public function getMasaMondokBulanAttribute(): ?int
    {
        if (!$this->valid_from) return null;

        $end = $this->left_at ?? ($this->valid_until ?? now());

        return (int) \Carbon\Carbon::parse($this->valid_from)->diffInMonths($end);
    }
}
