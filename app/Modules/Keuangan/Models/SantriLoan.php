<?php

namespace App\Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Core\Models\Person;
use App\Models\User;

class SantriLoan extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'santri_loans';

    protected $fillable = [
        'loan_code',
        'person_id',
        'gender',
        'amount',
        'amount_paid',
        'status',
        'borrowed_at',
        'due_date',
        'reason',
        'is_visible_to_guardian',
        'bill_id',
        'created_by',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'borrowed_at' => 'date',
        'due_date' => 'date',
        'is_visible_to_guardian' => 'boolean',
        'paid_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SantriLoanPayment::class, 'loan_id')->orderBy('payment_date', 'asc');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float)$this->amount - (float)$this->amount_paid);
    }

    public static function generateLoanCode(): string
    {
        $prefix = 'PINJ-' . now()->format('Ym') . '-';
        $todayCount = self::withTrashed()->where('loan_code', 'like', $prefix . '%')->count();
        $seq = str_pad((string)($todayCount + 1), 4, '0', STR_PAD_LEFT);
        return $prefix . $seq;
    }

    public function recalculateStatus(): void
    {
        $totalPaid = (float) $this->payments()->sum('amount');
        $this->amount_paid = $totalPaid;

        if ($this->status !== 'cancelled') {
            if ($totalPaid >= (float)$this->amount) {
                $this->status = 'paid';
                if (!$this->paid_at) {
                    $this->paid_at = now();
                }
            } elseif ($totalPaid > 0) {
                $this->status = 'partial';
                $this->paid_at = null;
            } else {
                $this->status = 'unpaid';
                $this->paid_at = null;
            }
        }

        $this->saveQuietly();

        // Sync with linked bill if exists
        if ($this->bill_id && $this->bill) {
            $bill = $this->bill;
            $bill->amount_paid = min((float)$bill->amount, $totalPaid);
            if ($this->status === 'paid') {
                $bill->status = 'paid';
            } elseif ($this->status === 'partial') {
                $bill->status = 'partial';
            } elseif ($this->status === 'cancelled') {
                $bill->status = 'cancelled';
            } else {
                $bill->status = 'unpaid';
            }
            $bill->saveQuietly();
        }
    }
}
