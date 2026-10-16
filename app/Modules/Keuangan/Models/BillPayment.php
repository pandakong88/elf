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
            $bill = $payment->bill;
            if ($bill) {
                $bill->recalculateStatus();

                if ($bill->bill_type === 'pinjaman' && $bill->reference_id) {
                    $loan = SantriLoan::find($bill->reference_id);
                    if ($loan) {
                        $receipt = $payment->receipt_no ?: ('PORTAL-' . $payment->id);
                        $exists = SantriLoanPayment::where('loan_id', $loan->id)
                            ->where('receipt_no', $receipt)
                            ->exists();
                        if (!$exists) {
                            SantriLoanPayment::create([
                                'loan_id'        => $loan->id,
                                'payment_code'   => SantriLoanPayment::generatePaymentCode(),
                                'amount'         => $payment->amount_paid,
                                'payment_method' => 'portal_wali',
                                'payment_date'   => $payment->payment_date ?: now()->toDateString(),
                                'received_by'    => $payment->logged_by,
                                'receipt_no'     => $receipt,
                                'notes'          => 'Pembayaran via Portal Wali (' . $receipt . ')',
                            ]);
                            $loan->recalculateStatus();
                        }
                    }
                }
            }
        });

        static::deleted(function ($payment) {
            $payment->bill?->recalculateStatus();
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
