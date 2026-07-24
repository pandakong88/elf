<?php

namespace App\Modules\Kepengasuhan\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class CensusTemplate extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'census_templates';

    protected $fillable = [
        'name',
        'description',
        'is_default',
        'is_archived',
        'created_by',
    ];

    protected $casts = [
        'is_default'  => 'boolean',
        'is_archived' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function fields(): HasMany
    {
        return $this->hasMany(CensusTemplateField::class, 'template_id')
                    ->orderBy('sort_order');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(CensusV3Campaign::class, 'template_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_archived', false);
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function systemFields(): HasMany
    {
        return $this->fields()->where('is_system_field', true);
    }

    public function customFields(): HasMany
    {
        return $this->fields()->where('is_system_field', false);
    }

    public function fieldCount(): int
    {
        return $this->fields()->count();
    }
}
