<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonPosition extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'person_positions';

    protected $fillable = [
        'person_id',
        'position_id',
        'valid_from',
        'valid_until',
        'notes',
    ];

    protected $casts = [
        'valid_from'  => 'date',
        'valid_until' => 'date',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Jabatan yang sedang aktif berdasarkan tanggal.
     */
    public function scopeCurrent(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query->where(function (Builder $q) use ($today) {
                         $q->whereNull('valid_from')->orWhere('valid_from', '<=', $today);
                     })
                     ->where(function (Builder $q) use ($today) {
                         $q->whereNull('valid_until')->orWhere('valid_until', '>=', $today);
                     });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $this->scopeCurrent($query);
    }
}
