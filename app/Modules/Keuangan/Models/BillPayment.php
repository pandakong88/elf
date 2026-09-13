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
        'receipt_no',
        'payment_group_id',
        'bill_id',
        'amount_paid',
        'tendered_amount',
        'change_amount',
        'payment_date',
        'payment_method',
        'logged_by',
        'notes',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'tendered_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
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

    public static function generateReceiptNo(): string
    {
        $prefix = 'KSR-' . now()->format('Ymd') . '-';
        $todayCount = self::where('receipt_no', 'like', $prefix . '%')
            ->select('receipt_no')
            ->distinct()
            ->count();

        $seq = str_pad((string)($todayCount + 1), 4, '0', STR_PAD_LEFT);
        return $prefix . $seq;
    }
}
