<?php

namespace App\Modules\Kepengasuhan\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class CensusV3CampaignDormitory extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'census_campaign_dormitories';

    protected $fillable = [
        'campaign_id', 'dormitory_id', 'assigned_to',
        'status', 'progress_filled', 'progress_total',
        'submitted_at', 'approved_at', 'rejection_notes',
    ];

    protected $casts = [
        'progress_filled' => 'integer',
        'progress_total'  => 'integer',
        'submitted_at'    => 'datetime',
        'approved_at'     => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(CensusV3Campaign::class, 'campaign_id');
    }

    public function dormitory(): BelongsTo
    {
        return $this->belongsTo(Dormitory::class, 'dormitory_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->whereIn('status', ['submitted', 'approved']);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getProgressPercentage(): int
    {
        if ($this->progress_total === 0) return 0;
        return (int) round(($this->progress_filled / $this->progress_total) * 100);
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending'     => 'Belum Dimulai',
            'in_progress' => 'Sedang Diisi',
            'submitted'   => 'Sudah Dikirim',
            'approved'    => 'Disetujui',
            'rejected'    => 'Dikembalikan',
            default       => $this->status,
        };
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            'pending'     => 'gray',
            'in_progress' => 'yellow',
            'submitted'   => 'blue',
            'approved'    => 'green',
            'rejected'    => 'red',
            default       => 'gray',
        };
    }
}
