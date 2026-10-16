<?php

namespace App\Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class SantriLoanPayment extends Model
{
    use HasUuids;

    protected $table = 'santri_loan_payments';

    protected $fillable = [
        'loan_id',
        'payment_code',
        'amount',
        'payment_method',
        'payment_date',
        'received_by',
        'receipt_no',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    protected static function booted()
    {
        static::saved(function ($payment) {
            $payment->loan?->recalculateStatus();
        });

        static::deleted(function ($payment) {
            $payment->loan?->recalculateStatus();
        });
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(SantriLoan::class, 'loan_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public static function generatePaymentCode(): string
    {
        $prefix = 'LPAY-' . now()->format('Ym') . '-';
        $todayCount = self::where('payment_code', 'like', $prefix . '%')->count();
        $seq = str_pad((string)($todayCount + 1), 4, '0', STR_PAD_LEFT);
        return $prefix . $seq;
    }
}
