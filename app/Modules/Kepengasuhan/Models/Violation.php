<?php

namespace App\Modules\Kepengasuhan\Models;

use App\Modules\Core\Models\MasterData;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Violation extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'violations';

    protected $fillable = [
        'person_id',
        'organization_id',
        'violation_type_id',
        'reporter_id',
        'violation_date',
        'description',
        'severity',
        'punishment',
        'points',
        'status',
    ];

    protected $casts = [
        'violation_date' => 'datetime',
        'points'         => 'integer',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function violationType(): BelongsTo
    {
        return $this->belongsTo(MasterData::class, 'violation_type_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'reporter_id');
    }

    public function scopeUnresolved($query)
    {
        return $query->where('status', '!=', 'resolved');
    }
}
