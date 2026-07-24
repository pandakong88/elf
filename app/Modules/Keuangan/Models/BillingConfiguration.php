<?php

namespace App\Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Models\User;

class BillingConfiguration extends Model
{
    use HasUuids;

    protected $table = 'billing_configurations';

    protected $fillable = [
        'type',
        'label',
        'amount',
        'dormitory_id',
        'effective_from',
        'interval',
        'manager_role',
        'manager_ids',
        'target_type',
        'target_filters',
        'can_be_installment',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'effective_from' => 'date',
        'is_active' => 'boolean',
        'target_filters' => 'array',
        'can_be_installment' => 'boolean',
        'manager_ids' => 'array',
    ];

    public function dormitory(): BelongsTo
    {
        return $this->belongsTo(Dormitory::class, 'dormitory_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function exceptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BillingException::class, 'billing_config_id');
    }
}
