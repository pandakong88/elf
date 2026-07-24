<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Organization extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'organizations';

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Organization::class, 'parent_id');
    }

    /** Recursive children (semua keturunan) */
    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    public function personRoles(): HasMany
    {
        return $this->hasMany(PersonRole::class);
    }

    public function masterData(): HasMany
    {
        return $this->hasMany(MasterData::class);
    }

    public function workflowTemplates(): HasMany
    {
        return $this->hasMany(WorkflowTemplate::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeByType(Builder $query, string|array $type): Builder
    {
        return $query->whereIn('type', (array) $type);
    }

    public function scopeChildrenOf(Builder $query, string $parentId): Builder
    {
        return $query->where('parent_id', $parentId);
    }
}
