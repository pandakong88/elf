<?php

namespace App\Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Core\Models\Person;

class EventBillItem extends Model
{
    use HasUuids;

    protected $table = 'event_bill_items';

    protected $fillable = [
        'event_bill_id',
        'person_id',
        'original_amount',
        'discount_amount',
        'discount_reason',
        'final_amount',
        'status',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->final_amount = max(0, $item->original_amount - $item->discount_amount);
        });
    }

    public function eventBill(): BelongsTo
    {
        return $this->belongsTo(EventBill::class, 'event_bill_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }
}
