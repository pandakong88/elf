<?php

namespace App\Modules\Kepengasuhan\Models;

use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class SantriStatusLog extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'santri_status_logs';

    protected $fillable = [
        'person_id', 'role_id', 'changed_field',
        'old_value', 'new_value', 'changed_by',
        'notes', 'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(PersonRole::class, 'role_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getFieldLabel(): string
    {
        return match ($this->changed_field) {
            'enrollment_status' => 'Status Keanggotaan',
            'presence_status'   => 'Status Keberadaan',
            default             => $this->changed_field,
        };
    }
}
