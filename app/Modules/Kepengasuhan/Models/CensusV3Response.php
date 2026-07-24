<?php

namespace App\Modules\Kepengasuhan\Models;

use App\Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class CensusV3Response extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'census_responses';

    protected $fillable = [
        'campaign_id', 'dormitory_id', 'person_id', 'room_id',
        'response_data', 'input_method', 'inputted_by',
        'is_complete', 'has_profile_changes', 'profile_change_preview',
    ];

    protected $casts = [
        'response_data'          => 'array',
        'is_complete'            => 'boolean',
        'has_profile_changes'    => 'boolean',
        'profile_change_preview' => 'array',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

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

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function inputtedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inputted_by');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getValue(string $fieldKey, mixed $default = null): mixed
    {
        return $this->response_data[$fieldKey] ?? $default;
    }

    public function setValue(string $fieldKey, mixed $value): void
    {
        $data = $this->response_data ?? [];
        $data[$fieldKey] = $value;
        $this->response_data = $data;
    }
}
