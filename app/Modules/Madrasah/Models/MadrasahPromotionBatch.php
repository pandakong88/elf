<?php

namespace App\Modules\Madrasah\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MadrasahPromotionBatch extends Model
{
    use HasFactory;

    protected $table = 'madrasah_promotion_batches';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'from_academic_year',
        'to_academic_year',
        'executed_at',
        'executed_by',
        'executed_by_name',
        'total_students',
        'total_promoted',
        'total_retained',
        'total_graduated',
        'status',
        'undone_at',
        'undone_by',
        'undone_by_name',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
        'undone_at'   => 'datetime',
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

    public function items(): HasMany
    {
        return $this->hasMany(MadrasahPromotionBatchItem::class, 'batch_id');
    }

    public function executor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }

    public function undoer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'undone_by');
    }
}
