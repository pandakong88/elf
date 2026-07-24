<?php

namespace App\Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class BillPayment extends Model
{
    use HasUuids;

    protected $table = 'bill_payments';

    protected $fillable = [
        'bill_id',
        'amount_paid',
        'payment_date',
        'payment_method',
        'logged_by',
        'notes',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'payment_date' => 'date',
    ];

    protected static function booted()
    {
        static::saved(function ($payment) {
            $payment->bill->recalculateStatus();
        });

        static::deleted(function ($payment) {
            $payment->bill->recalculateStatus();
        });
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }

    public function logger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by');
    }
}
