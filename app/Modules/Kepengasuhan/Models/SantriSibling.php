<?php

namespace App\Modules\Kepengasuhan\Models;

use App\Modules\Core\Models\Person;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SantriSibling extends Model
{
    use HasUuids;

    protected $table    = 'santri_siblings';
    protected $fillable = [
        'person_id', 'sibling_person_id', 'relationship',
        'auto_detected', 'is_confirmed', 'confirmed_by', 'confirmed_at',
        'is_eligible_for_discount', 'notes',
    ];

    protected $casts = [
        'auto_detected'            => 'boolean',
        'is_confirmed'             => 'boolean',
        'is_eligible_for_discount' => 'boolean',
        'confirmed_at'             => 'datetime',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function sibling(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'sibling_person_id');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Dari perspektif person tertentu, return saudara yang dimaksud.
     */
    public function getOtherPerson(string $personId): ?Person
    {
        if ($this->person_id === $personId) {
            return $this->sibling;
        }
        return $this->person;
    }

    /**
     * Hubungan kebalikan (dari perspektif saudara).
     */
    public function getReverseRelationship(): string
    {
        return match ($this->relationship) {
            'kakak'  => 'adik',
            'adik'   => 'kakak',
            'kembar' => 'kembar',
            default  => 'saudara',
        };
    }

    public function getRelationshipLabelAttribute(): string
    {
        return match ($this->relationship) {
            'kakak'   => '👑 Kakak',
            'adik'    => '🌱 Adik',
            'kembar'  => '👯 Kembar',
            default   => '🤝 Saudara',
        };
    }

    // =========================================================================
    // Static Helpers
    // =========================================================================

    public static function relationshipOptions(): array
    {
        return [
            'kakak'   => 'Kakak',
            'adik'    => 'Adik',
            'kembar'  => 'Kembar',
            'saudara' => 'Saudara (tidak diketahui urutan)',
        ];
    }
}
