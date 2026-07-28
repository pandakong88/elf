<?php

namespace App\Modules\Madrasah\Models;

use App\Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MadrasahPromotionBatchItem extends Model
{
    use HasFactory;

    protected $table = 'madrasah_promotion_batch_items';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'batch_id',
        'person_id',
        'source_kelas_id',
        'target_kelas_id',
        'status',
        'previous_enrollment_id',
        'new_enrollment_id',
        'previous_person_role_status',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(MadrasahPromotionBatch::class, 'batch_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function sourceKelas(): BelongsTo
    {
        return $this->belongsTo(MadrasahKelas::class, 'source_kelas_id');
    }

    public function targetKelas(): BelongsTo
    {
        return $this->belongsTo(MadrasahKelas::class, 'target_kelas_id');
    }

    public function previousEnrollment(): BelongsTo
    {
        return $this->belongsTo(MadrasahEnrollment::class, 'previous_enrollment_id');
    }

    public function newEnrollment(): BelongsTo
    {
        return $this->belongsTo(MadrasahEnrollment::class, 'new_enrollment_id');
    }
}
