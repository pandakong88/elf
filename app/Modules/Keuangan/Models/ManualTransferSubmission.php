<?php

namespace App\Modules\Keuangan\Models;

use App\Models\User;
use App\Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManualTransferSubmission extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'manual_transfer_submissions';

    protected $fillable = [
        'submission_code',
        'person_id',
        'bill_ids',
        'bill_breakdown',
        'total_bills_amount',
        'pocket_money_amount',
        'total_transfer_amount',
        'bank_destination',
        'sender_bank',
        'sender_account_name',
        'notes',
        'proof_image_path',
        'status',
        'rejection_reason',
        'verified_by',
        'verified_at',
        'receipt_no',
    ];

    protected $casts = [
        'bill_ids'              => 'array',
        'bill_breakdown'        => 'array',
        'total_bills_amount'    => 'decimal:2',
        'pocket_money_amount'   => 'decimal:2',
        'total_transfer_amount' => 'decimal:2',
        'verified_at'           => 'datetime',
        'deleted_at'            => 'datetime',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function pocketMoneyDeposit(): BelongsTo
    {
        return $this->belongsTo(PocketMoneyDeposit::class, 'id', 'reference_id');
    }

    public static function generateSubmissionCode(): string
    {
        $prefix = 'TRF-' . date('Ymd') . '-';
        $random = strtoupper(substr(uniqid(), -5));
        return $prefix . $random;
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) ($this->total_transfer_amount ?? 0);
    }

    public function getBillAmountAttribute(): float
    {
        return (float) ($this->total_bills_amount ?? 0);
    }

    public function getDestinationBankAttribute(): ?string
    {
        return $this->bank_destination;
    }
}
