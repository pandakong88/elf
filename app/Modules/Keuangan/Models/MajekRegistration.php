<?php

namespace App\Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Core\Models\Person;
use App\Models\User;

class MajekRegistration extends Model
{
    use HasUuids;

    protected $table = 'majek_registrations';

    protected $fillable = [
        'person_id',
        'month',
        'year',
        'session_pagi',
        'session_sore',
        'active_days',
        'amount_pagi',
        'amount_sore',
        'registered_by',
        'notes',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'session_pagi' => 'boolean',
        'session_sore' => 'boolean',
        'active_days' => 'integer',
        'amount_pagi' => 'decimal:2',
        'amount_sore' => 'decimal:2',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function registrar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function bills(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Bill::class, 'reference_id');
    }
}
