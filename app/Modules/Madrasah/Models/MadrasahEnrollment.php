<?php

namespace App\Modules\Madrasah\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Core\Models\Person;
use App\Models\User;

class MadrasahEnrollment extends Model
{
    use HasUuids;

    protected $table = 'madrasah_enrollments';

    protected $fillable = [
        'person_id',
        'kelas_id',
        'academic_year',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Santri yang terdaftar.
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    /**
     * Kelas yang diikuti.
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(MadrasahKelas::class, 'kelas_id');
    }

    /**
     * Pembuat data.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
