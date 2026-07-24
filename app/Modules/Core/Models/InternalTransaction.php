<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternalTransaction extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'internal_transactions';

    protected $fillable = [
        'requester_unit_id',
        'provider_unit_id',
        'reference_type',
        'reference_id',
        'description',
        'amount',
        'status',
        'settled_at',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'settled_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function requesterUnit(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'requester_unit_id');
    }

    public function providerUnit(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'provider_unit_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeByStatus(Builder $query, string|array $status): Builder
    {
        return $query->whereIn('status', (array) $status);
    }

    public function scopeByUnit(Builder $query, string $organizationId): Builder
    {
        return $query->where(function (Builder $q) use ($organizationId) {
            $q->where('requester_unit_id', $organizationId)
              ->orWhere('provider_unit_id', $organizationId);
        });
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeUnsettled(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['settled']);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isSettled(): bool
    {
        return $this->status === 'settled';
    }
}
