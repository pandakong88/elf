<?php

namespace App\Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class EventBill extends Model
{
    use HasUuids;

    protected $table = 'event_bills';

    protected $fillable = [
        'event_name',
        'event_date',
        'default_amount',
        'created_by',
    ];

    protected $casts = [
        'event_date' => 'date',
        'default_amount' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(EventBillItem::class, 'event_bill_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
