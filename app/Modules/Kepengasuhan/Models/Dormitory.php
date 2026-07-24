<?php

namespace App\Modules\Kepengasuhan\Models;

use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dormitory extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'dormitories';

    protected $fillable = [
        'organization_id',
        'name',
        'gender',
        'description',
        'is_active',
        'kas_komplek_amount',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'kas_komplek_amount' => 'decimal:2',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'dormitory_id')
            ->orderByRaw('LENGTH(name) ASC, name ASC');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeGender($query, string $gender)
    {
        return $query->where('gender', $gender);
    }
}
