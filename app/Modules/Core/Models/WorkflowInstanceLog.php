<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowInstanceLog extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'workflow_instance_logs';

    // Log bersifat immutable — tidak ada update
    public $timestamps = false;

    protected $fillable = [
        'instance_id',
        'step_order',
        'action',
        'actor_id',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'step_order' => 'integer',
        'created_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function instance(): BelongsTo
    {
        return $this->belongsTo(WorkflowInstance::class, 'instance_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'actor_id');
    }
}
