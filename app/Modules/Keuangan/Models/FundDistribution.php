<?php

namespace App\Modules\Keuangan\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundDistribution extends Model
{
    use HasUuids;

    protected $table = 'fund_distributions';

    protected $fillable = [
        'period_from',
        'period_to',
        'gender',
        'total_gross',
        'total_mdr',
        'total_net',
        'breakdown',
        'online_amount',
        'manual_amount',
        'online_count',
        'manual_count',
        'status',
        'distributed_at',
        'distributed_by',
        'notes',
    ];

    protected $casts = [
        'period_from'      => 'date',
        'period_to'        => 'date',
        'total_gross'      => 'decimal:2',
        'total_mdr'        => 'decimal:2',
        'total_net'        => 'decimal:2',
        'online_amount'    => 'decimal:2',
        'manual_amount'    => 'decimal:2',
        'breakdown'        => 'array',
        'distributed_at'   => 'datetime',
    ];

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopePutra($query)
    {
        return $query->where('gender', 'L');
    }

    public function scopePutri($query)
    {
        return $query->where('gender', 'P');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeDistributed($query)
    {
        return $query->where('status', 'distributed');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function getGenderLabelAttribute(): string
    {
        return $this->gender === 'L' ? 'Putra' : 'Putri';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Tandai distribusi sebagai sudah selesai.
     */
    public function markAsDistributed(string $userId, ?string $notes = null): void
    {
        $this->update([
            'status'         => 'distributed',
            'distributed_at' => now(),
            'distributed_by' => $userId,
            'notes'          => $notes ?? $this->notes,
        ]);
    }

    // ─── Relasi ──────────────────────────────────────────────────────────────

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'distributed_by');
    }
}
