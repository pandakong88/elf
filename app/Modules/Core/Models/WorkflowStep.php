<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowStep extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'workflow_steps';

    protected $fillable = [
        'template_id',
        'step_order',
        'name',
        'approver_position_id',
        'action_type',
        'sla_hours',
    ];

    protected $casts = [
        'step_order' => 'integer',
        'sla_hours'  => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function template(): BelongsTo
    {
        return $this->belongsTo(WorkflowTemplate::class, 'template_id');
    }

    public function approverPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'approver_position_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('step_order');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isLastStep(): bool
    {
        return ! WorkflowStep::where('template_id', $this->template_id)
                             ->where('step_order', '>', $this->step_order)
                             ->exists();
    }

    public function nextStep(): ?WorkflowStep
    {
        return WorkflowStep::where('template_id', $this->template_id)
                           ->where('step_order', '>', $this->step_order)
                           ->orderBy('step_order')
                           ->first();
    }
}
