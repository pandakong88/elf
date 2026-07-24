<?php

namespace App\Modules\Kepengasuhan\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DormitoryCensus extends Model
{
    use HasUuids;

    protected $table = 'dormitory_censuses';

    protected $fillable = [
        'census_period_id',
        'dormitory_id',
        'submitted_by',
        'submitted_at',
        'status',
        'notes',
        'import_source',
        'import_file_path',
        'total_santri',
        'total_confirmed',
        'total_exceptions',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    // Statuses
    const STATUS_PENDING   = 'pending';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED  = 'approved';
    const STATUS_REJECTED  = 'rejected';

    public function period(): BelongsTo
    {
        return $this->belongsTo(CensusPeriod::class, 'census_period_id');
    }

    public function dormitory(): BelongsTo
    {
        return $this->belongsTo(Dormitory::class, 'dormitory_id');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(RoomCensusDetail::class, 'dormitory_census_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'Belum Diisi',
            'submitted' => 'Menunggu Verifikasi',
            'approved'  => 'Disetujui',
            'rejected'  => 'Dikembalikan',
            default     => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'amber',
            'submitted' => 'blue',
            'approved'  => 'emerald',
            'rejected'  => 'red',
            default     => 'gray',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'clock',
            'submitted' => 'paper-airplane',
            'approved'  => 'check-circle',
            'rejected'  => 'x-circle',
            default     => 'question-mark-circle',
        };
    }

    /**
     * Hitung jumlah santri per status di sensus ini.
     */
    public function getStatisticsAttribute(): array
    {
        $counts = $this->details()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'present' => $counts['present'] ?? 0,
            'sick'    => $counts['sick']    ?? 0,
            'leave'   => $counts['leave']   ?? 0,
            'absent'  => $counts['absent']  ?? 0,
            'moved'   => $counts['moved']   ?? 0,
            'total'   => array_sum($counts),
        ];
    }
}
