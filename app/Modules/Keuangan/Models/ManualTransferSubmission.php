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

    protected $appends = [
        'proof_url',
        'destination_bank_label',
    ];

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
        return $this->getDestinationBankLabelAttribute() ?: $this->bank_destination;
    }

    public function getDestinationBankLabelAttribute(): ?string
    {
        $santri = $this->person;
        $isPutri = ($santri?->gender === 'P');
        $contents = \App\Modules\Core\Models\LandingPageContent::getContent();

        $dest = strtoupper(trim($this->bank_destination ?? ''));

        if ($isPutri) {
            $bank1Name = $contents['wali_bank1_name_putri'] ?? 'Bank Syariah Indonesia (BSI)';
            $bank1No   = $contents['wali_bsi_putri'] ?? '';
            $bank1An   = $contents['wali_bsi_putri_an'] ?? '';

            $bank2Name = $contents['wali_bank2_name_putri'] ?? 'Bank BRI';
            $bank2No   = $contents['wali_bri_putri'] ?? '';
            $bank2An   = $contents['wali_bri_putri_an'] ?? '';
        } else {
            $bank1Name = $contents['wali_bank1_name_putra'] ?? 'Bank Syariah Indonesia (BSI)';
            $bank1No   = $contents['wali_bsi_putra'] ?? '';
            $bank1An   = $contents['wali_bsi_putra_an'] ?? '';

            $bank2Name = $contents['wali_bank2_name_putra'] ?? 'Bank BRI';
            $bank2No   = $contents['wali_bri_putra'] ?? '';
            $bank2An   = $contents['wali_bri_putra_an'] ?? '';
        }

        // 1. Jika hanya salah satu rekening yang terisi di CMS, prioritaskan rekening aktif tersebut
        if (empty($bank1No) && !empty($bank2No)) {
            return ($bank2Name ?: 'Bank BRI') . ($bank2No ? " ({$bank2No})" : '');
        }
        if (!empty($bank1No) && empty($bank2No)) {
            return ($bank1Name ?: 'Bank BSI') . ($bank1No ? " ({$bank1No})" : '');
        }

        // 2. Jika pengirim memilih Bank 2 (BRI)
        if ($dest === 'BRI' || str_contains($dest, 'BRI') || str_contains($dest, '2')) {
            return ($bank2Name ?: 'Bank BRI') . ($bank2No ? " ({$bank2No})" : '');
        }

        // 3. Jika pengirim memilih Bank 1 (BSI)
        if ($dest === 'BSI' || str_contains($dest, 'BSI') || str_contains($dest, '1')) {
            return ($bank1Name ?: 'Bank BSI') . ($bank1No ? " ({$bank1No})" : '');
        }

        // 4. Jika di DB tersimpan nama bank custom
        if (!empty($this->bank_destination)) {
            return $this->bank_destination;
        }

        return ($bank1Name ?: 'Bank BSI') . ($bank1No ? " ({$bank1No})" : '');
    }

    public function getProofUrlAttribute(): ?string
    {
        if (empty($this->proof_image_path)) {
            return null;
        }
        return route('transfer-proof.view', ['id' => $this->id]);
    }
}
