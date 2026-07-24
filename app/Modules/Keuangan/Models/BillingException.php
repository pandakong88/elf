<?php

namespace App\Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Core\Models\Person;
use App\Models\User;

class BillingException extends Model
{
    use HasUuids;

    protected $table = 'billing_exceptions';

    protected $fillable = [
        'billing_config_id',
        'person_id',
        'exception_type',
        'amount',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function configuration(): BelongsTo
    {
        return $this->belongsTo(BillingConfiguration::class, 'billing_config_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
