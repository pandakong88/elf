<?php

namespace App\Modules\Kepengasuhan\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CensusPeriod extends Model
{
    use HasUuids;

    protected $table = 'census_periods';

    protected $fillable = [
        'name',
        'month',
        'year',
        'status',
        'created_by',
    ];

    protected $casts = [
        'month' => 'integer',
        'year'  => 'integer',
    ];

    // Statuses
    const STATUS_DRAFT  = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_CLOSED = 'closed';

    public function dormitoryCensuses(): HasMany
    {
        return $this->hasMany(DormitoryCensus::class, 'census_period_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function getMonthNameAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $months[$this->month] ?? '-';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft'  => 'Draf',
            'active' => 'Aktif',
            'closed' => 'Ditutup',
            default  => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft'  => 'gray',
            'active' => 'emerald',
            'closed' => 'slate',
            default  => 'gray',
        };
    }

    /**
     * Hitung progress setoran sensus (berapa yang sudah submitted/approved).
     */
    public function getSubmissionProgressAttribute(): array
    {
        $all       = $this->dormitoryCensuses()->count();
        $submitted = $this->dormitoryCensuses()->whereIn('status', ['submitted', 'approved'])->count();
        $approved  = $this->dormitoryCensuses()->where('status', 'approved')->count();

        return [
            'total'     => $all,
            'submitted' => $submitted,
            'approved'  => $approved,
            'pending'   => $all - $submitted,
            'percent'   => $all > 0 ? round(($submitted / $all) * 100) : 0,
        ];
    }
}
