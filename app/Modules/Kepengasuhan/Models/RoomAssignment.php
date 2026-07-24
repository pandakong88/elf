<?php

namespace App\Modules\Kepengasuhan\Models;

use App\Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomAssignment extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'room_assignments';

    protected $fillable = [
        'room_id',
        'person_id',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'valid_from'  => 'date',
        'valid_until' => 'date',
        'is_active'   => 'boolean',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function scopeActive($query)
    {
        $now = now();
        return $query->where('room_assignments.is_active', true)
            ->where('room_assignments.valid_from', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('room_assignments.valid_until')
                  ->orWhere('room_assignments.valid_until', '>=', $now);
            });
    }
}
