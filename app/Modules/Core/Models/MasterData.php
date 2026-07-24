<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterData extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'master_data';

    protected $fillable = [
        'organization_id',
        'category',
        'code',
        'name',
        'description',
        'metadata',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'metadata'   => 'array',
        'sort_order' => 'integer',
        'is_active'  => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Data global (berlaku untuk semua unit, organization_id = null).
     */
    public function scopeGlobal(Builder $query): Builder
    {
        return $query->whereNull('organization_id');
    }

    /**
     * Data untuk organisasi tertentu ATAU data global.
     */
    public function scopeForOrganization(Builder $query, string $organizationId): Builder
    {
        return $query->where(function (Builder $q) use ($organizationId) {
            $q->where('organization_id', $organizationId)
              ->orWhereNull('organization_id');
        });
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
