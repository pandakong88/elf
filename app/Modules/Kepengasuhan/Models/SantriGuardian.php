<?php

namespace App\Modules\Kepengasuhan\Models;

use App\Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SantriGuardian extends Model
{
    use HasUuids;

    protected $table    = 'santri_guardians';
    protected $fillable = [
        'person_id', 'guardian_id', 'relationship',
        'priority_order', 'is_primary', 'notes',
    ];

    protected $casts = [
        'is_primary'     => 'boolean',
        'priority_order' => 'integer',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class, 'guardian_id');
    }

    public function getRelationshipLabelAttribute(): string
    {
        return Guardian::relationshipOptions()[$this->relationship] ?? $this->relationship;
    }
}
