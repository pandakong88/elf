<?php

namespace App\Modules\Kepengasuhan\Models;

use App\Modules\Core\Models\MasterData;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\WorkflowInstance;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Perizinan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'perizinan';

    protected $fillable = [
        'person_id',
        'organization_id',
        'permission_type_id',
        'reason',
        'start_date',
        'end_date',
        'actual_return_date',
        'workflow_instance_id',
        'status',
    ];

    protected $casts = [
        'start_date'         => 'datetime',
        'end_date'           => 'datetime',
        'actual_return_date' => 'datetime',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function permissionType(): BelongsTo
    {
        return $this->belongsTo(MasterData::class, 'permission_type_id');
    }

    public function workflowInstance(): BelongsTo
    {
        return $this->belongsTo(WorkflowInstance::class, 'workflow_instance_id');
    }

    public function scopeActiveLeave($query)
    {
        return $query->where('status', 'out');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
