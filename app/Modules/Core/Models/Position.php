<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'positions';

    protected $fillable = [
        'organization_id',
        'name',
        'level',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function personPositions(): HasMany
    {
        return $this->hasMany(PersonPosition::class);
    }

    public function workflowSteps(): HasMany
    {
        return $this->hasMany(WorkflowStep::class, 'approver_position_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByOrganization(Builder $query, string $organizationId): Builder
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeStructural(Builder $query): Builder
    {
        return $query->where('level', 'structural');
    }

    public function scopeFungsional(Builder $query): Builder
    {
        return $query->where('level', 'fungsional');
    }
}
