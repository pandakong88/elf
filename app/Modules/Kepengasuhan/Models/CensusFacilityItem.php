<?php

namespace App\Modules\Kepengasuhan\Models;

use App\Models\User;
use App\Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CensusFacilityItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'census_facility_items';

    protected $fillable = [
        'campaign_id',
        'dormitory_id',
        'room_id',
        'locker_id',
        'item_type',
        'item_name',
        'condition',
        'assigned_person_id',
        'notes',
        'entered_by',
        'apply_status',
        'apply_error',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(CensusV3Campaign::class, 'campaign_id');
    }

    public function dormitory(): BelongsTo
    {
        return $this->belongsTo(Dormitory::class, 'dormitory_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function locker(): BelongsTo
    {
        return $this->belongsTo(Locker::class, 'locker_id');
    }

    public function assignedPerson(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'assigned_person_id');
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function getItemTypeLabelAttribute(): string
    {
        return match ($this->item_type) {
            'komplek' => 'Fasilitas Komplek',
            'kamar'   => 'Fasilitas Kamar',
            'lemari'  => 'Lemari Santri',
            default   => ucfirst($this->item_type),
        };
    }

    public function getConditionLabelAttribute(): string
    {
        return Locker::CONDITIONS[$this->condition] ?? ucfirst($this->condition);
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
