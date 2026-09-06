<?php

namespace App\Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Core\Models\Person;
use App\Models\User;

class SantriLeave extends Model
{
    use HasUuids;

    protected $table = 'santri_leaves';

    protected $fillable = [
        'person_id',
        'year',
        'start_month',
        'end_month',
        'months',
        'scope_type',
        'config_ids',
        'reason',
        'created_by',
    ];

    protected $casts = [
        'year'        => 'integer',
        'start_month' => 'integer',
        'end_month'   => 'integer',
        'months'      => 'array',
        'config_ids'  => 'array',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if this leave record covers a specific month and optional configId.
     */
    public function coversMonth(int $month, ?string $configId = null): bool
    {
        $months = $this->months ?? range($this->start_month, $this->end_month);
        if (!in_array($month, $months)) {
            return false;
        }

        if ($this->scope_type === 'specific' && $configId) {
            $allowedConfigs = $this->config_ids ?? [];
            return in_array($configId, $allowedConfigs);
        }

        return true;
    }

    /**
     * Get a human-readable period description (e.g. "Januari - Maret 2026").
     */
    public function getPeriodDescriptionAttribute(): string
    {
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        if ($this->start_month === $this->end_month) {
            return ($monthNames[$this->start_month] ?? "Bulan {$this->start_month}") . " {$this->year}";
        }

        return ($monthNames[$this->start_month] ?? "Bulan {$this->start_month}") . " s/d " . ($monthNames[$this->end_month] ?? "Bulan {$this->end_month}") . " {$this->year}";
    }
}
