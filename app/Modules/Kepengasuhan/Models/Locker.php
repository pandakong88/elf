<?php

namespace App\Modules\Kepengasuhan\Models;

use App\Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Locker extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'lockers';

    protected $fillable = [
        'room_id',
        'type',
        'code',
        'assigned_person_id',
        'allocation_type',
        'custom_holder_name',
        'condition',
        'notes',
        'is_active',
    ];

    public const TYPES = [
        'baju'  => 'Lemari Pakaian',
        'kitab' => 'Rak Kitab & Buku',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const CONDITIONS = [
        'baik'         => 'Baik',
        'rusak_ringan' => 'Rusak Ringan',
        'rusak_berat'  => 'Rusak Berat',
        'hilang'       => 'Hilang',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function assignedPerson(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'assigned_person_id');
    }

    public function getConditionLabelAttribute(): string
    {
        return self::CONDITIONS[$this->condition] ?? ucfirst($this->condition);
    }

    public function getConditionColorAttribute(): string
    {
        return match ($this->condition) {
            'baik'         => 'green',
            'rusak_ringan' => 'yellow',
            'rusak_berat'  => 'red',
            'hilang'       => 'purple',
            default        => 'gray',
        };
    }

    public function scopeBaju($query)
    {
        return $query->where('type', 'baju');
    }

    public function scopeKitab($query)
    {
        return $query->where('type', 'kitab');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? 'Lemari Pakaian';
    }

    public function getTypeIconAttribute(): string
    {
        return $this->type === 'kitab' ? '📚' : '🚪';
    }

    public function getConditionBadgeClassAttribute(): string
    {
        return match ($this->condition) {
            'baik'         => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-300',
            'rusak_ringan' => 'bg-amber-100 text-amber-850 dark:bg-amber-950/60 dark:text-amber-300 border-amber-300',
            'rusak_berat'  => 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border-rose-300',
            'hilang'       => 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border-purple-300',
            default        => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 border-slate-300',
        };
    }
}
