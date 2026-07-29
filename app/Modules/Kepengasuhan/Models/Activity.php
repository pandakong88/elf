<?php

namespace App\Modules\Kepengasuhan\Models;

use App\Modules\Core\Models\MasterData;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Activity extends Model implements HasMedia
{
    use HasFactory, HasUuids, InteractsWithMedia;

    protected $table = 'activities';

    protected $fillable = [
        'organization_id',
        'activity_type_id',
        'name',
        'date',
        'description',
        'visibility',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos')
             ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function activityType(): BelongsTo
    {
        return $this->belongsTo(MasterData::class, 'activity_type_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(ActivityAttendance::class, 'activity_id');
    }

    public function getPhotoUrls(): array
    {
        return $this->getMedia('photos')->map(function ($media) {
            return route('media.stream', $media->id);
        })->toArray();
    }

    public function getFirstPhotoUrl(): ?string
    {
        $urls = $this->getPhotoUrls();
        return $urls[0] ?? null;
    }
}
