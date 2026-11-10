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

    public function assignees(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'census_campaign_dormitory_assignees',
            'campaign_dormitory_id',
            'user_id'
        )->withTimestamps();
    }

    public function isAssignedTo(string $userId): bool
    {
        if ($this->assigned_to === $userId) {
            return true;
        }

        if ($this->relationLoaded('assignees')) {
            return $this->assignees->contains('id', $userId);
        }

        return $this->assignees()->where('users.id', $userId)->exists();
    }

    public function getAssigneeNamesAttribute(): string
    {
        $names = collect();
        if ($this->relationLoaded('assignees') && $this->assignees->isNotEmpty()) {
            $names = $this->assignees->pluck('name');
        } elseif ($this->relationLoaded('assignedUser') && $this->assignedUser) {
            $names = collect([$this->assignedUser->name]);
        } elseif ($this->assignees()->exists()) {
            $names = $this->assignees()->pluck('name');
        } elseif ($this->assigned_to && $this->assignedUser) {
            $names = collect([$this->assignedUser->name]);
        }

        return $names->isNotEmpty() ? $names->implode(', ') : 'Belum ditugaskan';
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

    public function getStatusBadgeClass(): string
    {
        return match ($this->getStatusColor()) {
            'yellow' => 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300',
            'blue'   => 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300',
            'green'  => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300',
            'red'    => 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300',
            default  => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300',
        };
    }
}
