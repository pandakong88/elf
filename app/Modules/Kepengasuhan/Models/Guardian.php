<?php

namespace App\Modules\Kepengasuhan\Models;

use App\Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guardian extends Model
{
    use HasUuids;

    protected $table    = 'guardians';
    protected $fillable = [
        'name', 'national_id', 'gender',
        'phone_primary', 'phone_secondary', 'email',
        'occupation', 'education_level', 'income_range',
        'address', 'village', 'district', 'city', 'province', 'postal_code',
        'notes', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function santri(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'santri_guardians', 'guardian_id', 'person_id')
            ->withPivot(['relationship', 'priority_order', 'is_primary', 'notes'])
            ->withTimestamps();
    }

    public function santriGuardians(): HasMany
    {
        return $this->hasMany(SantriGuardian::class, 'guardian_id');
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    public function santriCount(): int
    {
        return $this->santri()->count();
    }

    public function isPrimaryFor(string $personId): bool
    {
        return $this->santri()
            ->where('persons.id', $personId)
            ->wherePivot('is_primary', true)
            ->exists();
    }

    public function getRelationshipLabelAttribute(): string
    {
        $labels = [
            'ayah_kandung'   => 'Ayah Kandung',
            'ibu_kandung'    => 'Ibu Kandung',
            'wali_resmi'     => 'Wali Resmi',
            'kakek'          => 'Kakek',
            'nenek'          => 'Nenek',
            'paman'          => 'Paman',
            'bibi'           => 'Bibi',
            'kakak_kandung'  => 'Kakak Kandung',
            'kontak_darurat' => 'Kontak Darurat',
            'lainnya'        => 'Lainnya',
        ];
        return $labels[$this->relationship ?? ''] ?? 'Wali';
    }

    // =========================================================================
    // Static Helpers
    // =========================================================================

    public static function relationshipOptions(): array
    {
        return [
            'ayah_kandung'   => 'Ayah Kandung',
            'ibu_kandung'    => 'Ibu Kandung',
            'wali_resmi'     => 'Wali Resmi',
            'kakek'          => 'Kakek',
            'nenek'          => 'Nenek',
            'paman'          => 'Paman',
            'bibi'           => 'Bibi',
            'kakak_kandung'  => 'Kakak Kandung',
            'kontak_darurat' => 'Kontak Darurat',
            'lainnya'        => 'Lainnya',
        ];
    }

    public static function educationOptions(): array
    {
        return ['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3', 'Tidak Sekolah'];
    }

    public static function incomeRangeOptions(): array
    {
        return [
            '< 1jt'    => '< Rp 1.000.000',
            '1-2jt'    => 'Rp 1.000.000 – 2.000.000',
            '2-5jt'    => 'Rp 2.000.000 – 5.000.000',
            '5-10jt'   => 'Rp 5.000.000 – 10.000.000',
            '> 10jt'   => '> Rp 10.000.000',
        ];
    }
}
