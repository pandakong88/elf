<?php

namespace App\Modules\Kepengasuhan\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'rooms';

    protected $fillable = [
        'dormitory_id',
        'name',
        'capacity',
        'description',
        'furniture_layout',
        'is_active',
    ];

    protected $casts = [
        'capacity'         => 'integer',
        'furniture_layout' => 'array',
        'is_active'        => 'boolean',
    ];

    public function dormitory(): BelongsTo
    {
        return $this->belongsTo(Dormitory::class, 'dormitory_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(RoomAssignment::class, 'room_id');
    }

    public function currentAssignments(): HasMany
    {
        return $this->hasMany(RoomAssignment::class, 'room_id')->where('is_active', true);
    }

    public function lockers(): HasMany
    {
        return $this->hasMany(Locker::class, 'room_id')->orderBy('code');
    }

    public function activeLockers(): HasMany
    {
        return $this->hasMany(Locker::class, 'room_id')->where('is_active', true)->orderBy('code');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
