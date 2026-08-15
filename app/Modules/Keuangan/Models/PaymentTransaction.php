<?php

namespace App\Modules\Keuangan\Models;

use App\Models\User;
use App\Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasUuids;

    protected $table = 'payment_transactions';

    protected $fillable = [
        'merchant_order_id',
        'duitku_reference',
        'person_id',
        'bill_ids',
        'bill_breakdown',
        'bill_amount',
        'mdr_channel',
        'mdr_rate',
        'mdr_fixed',
        'mdr_amount',
        'total_amount',
        'net_amount',
        'payment_channel',
        'status',
        'payment_url',
        'va_number',
        'qr_string',
        'expires_at',
        'callback_received_at',
        'return_url_accessed_at',
        'raw_duitku_response',
        'raw_callback_payload',
        'initiated_by',
        'failure_reason',
    ];

    protected $casts = [
        'bill_ids'               => 'array',
        'bill_breakdown'         => 'array',
        'raw_duitku_response'    => 'array',
        'raw_callback_payload'   => 'array',
        'bill_amount'            => 'decimal:2',
        'mdr_rate'               => 'decimal:4',
        'mdr_fixed'              => 'decimal:2',
        'mdr_amount'             => 'decimal:2',
        'total_amount'           => 'decimal:2',
        'net_amount'             => 'decimal:2',
        'expires_at'             => 'datetime',
        'callback_received_at'   => 'datetime',
        'return_url_accessed_at' => 'datetime',
    ];

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed', 'expired']);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'pending')
                     ->where('expires_at', '<', now());
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Apakah transaksi ini sudah berhasil diproses?
     */
    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Apakah transaksi ini masih aktif / menunggu pembayaran?
     */
    public function isPending(): bool
    {
        return $this->status === 'pending' && $this->expires_at?->isFuture();
    }

    /**
     * Label channel pembayaran yang ramah pengguna.
     */
    public function getChannelLabelAttribute(): string
    {
        $channels = config('duitku.enabled_channels', []);
        return $channels[$this->payment_channel]['name'] ?? $this->payment_channel ?? '-';
    }

    /**
     * Ambil semua Bill yang terkait dengan transaksi ini.
     */
    public function bills(): \Illuminate\Database\Eloquent\Collection
    {
        return Bill::whereIn('id', $this->bill_ids ?? [])->get();
    }

    // ─── Relasi ──────────────────────────────────────────────────────────────

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }
}
