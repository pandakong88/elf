<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowInstance extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'workflow_instances';

    protected $fillable = [
        'template_id',
        'entity_type',
        'entity_id',
        'current_step',
        'status',
        'initiated_by',
    ];

    protected $casts = [
        'current_step' => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function template(): BelongsTo
    {
        return $this->belongsTo(WorkflowTemplate::class, 'template_id');
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'initiated_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WorkflowInstanceLog::class, 'instance_id')->orderBy('created_at');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeByStatus(Builder $query, string|array $status): Builder
    {
        return $query->whereIn('status', (array) $status);
    }

    public function scopeByEntity(Builder $query, string $entityType, string $entityId): Builder
    {
        return $query->where('entity_type', $entityType)->where('entity_id', $entityId);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', ['pending', 'in_progress']);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isFinished(): bool
    {
        return in_array($this->status, ['approved', 'rejected', 'cancelled']);
    }

    public function getCurrentStepModel(): ?WorkflowStep
    {
        return $this->template
                    ->steps()
                    ->where('step_order', $this->current_step)
                    ->first();
    }
}
