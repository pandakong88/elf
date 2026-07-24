<?php

namespace App\Modules\Kepengasuhan\Models;

use App\Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomCensusDetail extends Model
{
    use HasUuids;

    protected $table = 'room_census_details';

    protected $fillable = [
        'dormitory_census_id',
        'room_id',
        'person_id',
        'status',
        'notes',
        'profile_updates',
        'has_profile_update',
        'has_guardian_update',
        'guardian_updates',
    ];

    protected $casts = [
        'profile_updates' => 'array',
        'guardian_updates' => 'array',
        'has_profile_update' => 'boolean',
        'has_guardian_update' => 'boolean',
    ];

    // Status kehadiran santri
    const STATUS_PRESENT = 'present'; // Hadir
    const STATUS_SICK    = 'sick';    // Sakit
    const STATUS_LEAVE   = 'leave';   // Izin Pulang
    const STATUS_ABSENT  = 'absent';  // Alpa / Kabur
    const STATUS_MOVED   = 'moved';   // Pindah Kamar

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PRESENT => ['label' => 'Hadir',       'color' => 'emerald', 'icon' => '✅'],
            self::STATUS_SICK    => ['label' => 'Sakit',        'color' => 'amber',   'icon' => '🤒'],
            self::STATUS_LEAVE   => ['label' => 'Izin Pulang',  'color' => 'blue',    'icon' => '🏠'],
            self::STATUS_ABSENT  => ['label' => 'Alpa/Kabur',   'color' => 'red',     'icon' => '⚠️'],
            self::STATUS_MOVED   => ['label' => 'Pindah Kamar', 'color' => 'purple',  'icon' => '🔄'],
        ];
    }

    public function dormitoryCensus(): BelongsTo
    {
        return $this->belongsTo(DormitoryCensus::class, 'dormitory_census_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusOptions()[$this->status]['label'] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return self::statusOptions()[$this->status]['color'] ?? 'gray';
    }
}
