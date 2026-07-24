<?php

namespace App\Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class MajekPeriod extends Model
{
    use HasUuids;

    protected $table = 'majek_periods';

    protected $fillable = [
        'month',
        'year',
        'active_days',
        'tarif_per_hari',
        'tarif_per_hari_putri',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'month'                => 'integer',
        'year'                 => 'integer',
        'active_days'          => 'integer',
        'tarif_per_hari'       => 'decimal:2',
        'tarif_per_hari_putri' => 'decimal:2',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(MajekRegistration::class, 'month', 'month')
                    ->where('year', $this->year);
    }

    // -------------------------------------------------------------------------
    // Computed Helpers
    // -------------------------------------------------------------------------

    /**
     * Dapatkan tarif per hari berdasarkan gender santri ('L' / 'P').
     */
    public function getTarifPerHariForGender(?string $gender): float
    {
        if (strtoupper((string)$gender) === 'P') {
            return (float) ($this->tarif_per_hari_putri ?? 3000.00);
        }
        return (float) ($this->tarif_per_hari ?? 3333.33);
    }

    /**
     * Tarif Putra untuk 2x makan sebulan penuh (pagi + sore).
     */
    public function getTarif2xAttribute(): float
    {
        return (float) $this->tarif_per_hari * 2 * $this->active_days;
    }

    /**
     * Tarif Putra untuk 1x makan sebulan penuh (pagi saja ATAU sore saja).
     */
    public function getTarif1xAttribute(): float
    {
        return (float) $this->tarif_per_hari * $this->active_days;
    }

    /**
     * Tarif Putri untuk 2x makan sebulan penuh (pagi + sore).
     */
    public function getTarif2xPutriAttribute(): float
    {
        return (float) ($this->tarif_per_hari_putri ?? 3000.00) * 2 * $this->active_days;
    }

    /**
     * Tarif Putri untuk 1x makan sebulan penuh (pagi saja ATAU sore saja).
     */
    public function getTarif1xPutriAttribute(): float
    {
        return (float) ($this->tarif_per_hari_putri ?? 3000.00) * $this->active_days;
    }
}
