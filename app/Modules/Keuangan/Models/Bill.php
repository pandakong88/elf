<?php

namespace App\Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Core\Models\Person;
use App\Models\User;

class Bill extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'bills';

    protected $fillable = [
        'person_id',
        'bill_type',
        'billing_config_id',
        'reference_id',
        'parent_bill_id',
        'period_month',
        'period_year',
        'period_sub',
        'amount',
        'amount_paid',
        'status',
        'due_date',
        'managed_by_role',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'due_date' => 'date',
        'period_month' => 'integer',
        'period_year' => 'integer',
        'period_sub' => 'integer',
        'deleted_at' => 'datetime',
    ];

    public function getPeriodFormattedAttribute(): string
    {
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $base = ($this->period_month && isset($monthNames[$this->period_month]))
            ? $monthNames[$this->period_month] . ' ' . $this->period_year
            : ($this->period_year ? (string)$this->period_year : '-');

        if ($this->period_sub) {
            $interval = $this->config?->interval;
            $maxSub = match($interval) {
                'biweekly', '2x_monthly' => 2,
                'trimonthly', '3x_monthly' => 3,
                'weekly', '4x_monthly' => 4,
                default => null,
            };

            if ($maxSub) {
                return $base . " (Periode {$this->period_sub}/{$maxSub})";
            }
            return $base . " (Periode {$this->period_sub})";
        }

        return $base;
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function config(): BelongsTo
    {
        return $this->belongsTo(BillingConfiguration::class, 'billing_config_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(BillPayment::class, 'bill_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Bill::class, 'parent_bill_id');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Bill::class, 'parent_bill_id');
    }

    public function recalculateStatus(): void
    {
        if ($this->installments()->exists()) {
            $totalPaid = $this->installments()->sum('amount_paid');
            $this->amount_paid = $totalPaid;
            
            $allPaid = $this->installments()->where('status', '!=', 'paid')->count() === 0;
            $anyPaidOrPartial = $this->installments()->whereIn('status', ['paid', 'partial'])->exists() || $totalPaid > 0;
            
            if ($this->status !== 'refund_requested' && $this->status !== 'refunded' && $this->status !== 'cancelled') {
                if ($allPaid) {
                    $this->status = 'paid';
                } elseif ($anyPaidOrPartial) {
                    $this->status = 'partial';
                } else {
                    $this->status = 'unpaid';
                }
            }
        } else {
            $totalPaid = $this->payments()->sum('amount_paid');
            $this->amount_paid = $totalPaid;

            if ($this->status !== 'refund_requested' && $this->status !== 'refunded' && $this->status !== 'cancelled') {
                if ($totalPaid >= $this->amount) {
                    $this->status = 'paid';
                } elseif ($totalPaid > 0) {
                    $this->status = 'partial';
                } else {
                    $this->status = 'unpaid';
                }
            }
        }

        $this->save();

        if ($this->parent_bill_id) {
            $parent = $this->parent;
            if ($parent) {
                $parent->recalculateStatus();
            }
        }
    }
}
