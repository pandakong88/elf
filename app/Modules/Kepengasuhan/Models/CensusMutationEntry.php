<?php

namespace App\Modules\Kepengasuhan\Models;

use App\Models\User;
use App\Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CensusMutationEntry extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'census_mutation_entries';

    protected $fillable = [
        'campaign_id',
        'dormitory_id',
        'person_id',
        'from_room_id',
        'action',
        'to_room_id',
        'new_enrollment_status',
        'notes',
        'entered_by',
        'apply_status',
        'apply_error',
    ];

    public const ACTIONS = [
        'tetap'           => 'Tetap di Kamar',
        'pindah_kamar'    => 'Pindah Kamar',
        'ubah_status'     => 'Ubah Status Santri',
        'tidak_ditemukan' => 'Tidak Ditemukan',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(CensusV3Campaign::class, 'campaign_id');
    }

    public function dormitory(): BelongsTo
    {
        return $this->belongsTo(Dormitory::class, 'dormitory_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function fromRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'from_room_id');
    }

    public function toRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'to_room_id');
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function getActionLabelAttribute(): string
    {
        return self::ACTIONS[$this->action] ?? ucfirst($this->action);
    }
}
