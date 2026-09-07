<?php

namespace App\Modules\Keuangan\Models;

use App\Models\User;
use App\Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PocketMoneyDeposit extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'pocket_money_deposits';

    protected $fillable = [
        'person_id',
        'amount',
        'source',
        'reference_id',
        'status',
        'notes',
        'received_by',
        'received_at',
        'disbursed_by',
        'disbursed_at',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'received_at'  => 'datetime',
        'disbursed_at' => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function disburser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disbursed_by');
    }

    public function manualTransferSubmission(): BelongsTo
    {
        return $this->belongsTo(ManualTransferSubmission::class, 'reference_id', 'id');
    }
}
