<?php

namespace App\Modules\Madrasah\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Modules\Core\Models\Person;
use App\Models\User;

class MadrasahKelas extends Model
{
    use HasUuids;

    protected $table = 'madrasah_kelas';

    protected $fillable = [
        'name',
        'jenjang',
        'academic_year',
        'wali_kelas_id',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Wali kelas (guru).
     */
    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'wali_kelas_id');
    }

    /**
     * Semua enrollment santri di kelas ini.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(MadrasahEnrollment::class, 'kelas_id');
    }

    /**
     * Enrollment yang masih aktif.
     */
    public function activeEnrollments(): HasMany
    {
        return $this->hasMany(MadrasahEnrollment::class, 'kelas_id')
            ->where('is_active', true);
    }

    /**
     * Pembuat data.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Label jenjang yang mudah dibaca.
     */
    public function getJenjangLabelAttribute(): string
    {
        return match ($this->jenjang) {
            'ula'    => 'Ula (Ibtidaiyah)',
            'wustho' => 'Wustho (Tsanawiyah)',
            'ulya'   => 'Ulya (Aliyah)',
            default  => $this->jenjang,
        };
    }
}
