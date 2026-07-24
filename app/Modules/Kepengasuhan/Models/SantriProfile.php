<?php

namespace App\Modules\Kepengasuhan\Models;

use App\Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SantriProfile extends Model
{
    use HasUuids;

    protected $table = 'santri_profiles';

    protected $fillable = [
        'person_id',

        // Orang Tua (untuk auto-detect saudara)
        'father_name',
        'father_phone',
        'father_occupation',
        'mother_name',
        'mother_phone',

        // Kesehatan
        'blood_type',
        'medical_history',
        'allergies',
        'special_conditions',

        // Pendidikan
        'school_status',
        'school_name',
        'school_type',
        'major',
        'school_year',

        // Sosial
        'birth_city',
        'hobby',
        'achievement',

        // Saudara (flag auto-update)
        'has_active_sibling',
        'active_sibling_count',

        // Meta sensus
        'last_census_id',
        'last_updated_at',

        // Fleksibel JSON
        'additional_info',
    ];

    protected $casts = [
        'additional_info'     => 'array',
        'has_active_sibling'  => 'boolean',
        'active_sibling_count'=> 'integer',
        'last_updated_at'     => 'datetime',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function guardians(): BelongsToMany
    {
        return $this->person->guardians();
    }

    // =========================================================================
    // Static Option Helpers
    // =========================================================================

    public static function schoolStatusOptions(): array
    {
        return [
            'mondok_full'   => 'Mondok Full (Madrasah / Ngaji)',
            'sekolah_luar'  => 'Sekolah Formal (Luar Pondok)',
            'kuliah'        => 'Kuliah / Perguruan Tinggi',
            'tidak_sekolah' => 'Tidak Sekolah',
            'lulus'         => 'Sudah Lulus / Alumni',
        ];
    }

    public static function schoolTypeOptions(): array
    {
        return ['SD', 'MI', 'SMP', 'MTs', 'SMA', 'SMK', 'MA', 'D3', 'D4', 'S1', 'S2', 'S3', 'Pesantren', 'Lainnya'];
    }

    public static function bloodTypeOptions(): array
    {
        return ['A', 'B', 'AB', 'O', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    }

    // =========================================================================
    // Additional Info JSON Helpers
    // =========================================================================

    /**
     * Ambil nilai dari kolom `additional_info` JSON secara dinamis.
     */
    public function getAdditional(string $key, mixed $default = null): mixed
    {
        $info = $this->additional_info ?? [];
        return $info[$key] ?? $default;
    }

    /**
     * Set nilai dalam `additional_info` secara dinamis.
     */
    public function setAdditional(string $key, mixed $value): void
    {
        $info       = $this->additional_info ?? [];
        $info[$key] = $value;
        $this->additional_info = $info;
    }

    // =========================================================================
    // Census Update Helpers
    // =========================================================================

    /**
     * Update profil dari array data sensus yang sudah disetujui.
     * Field eksplisit langsung di-update, sisanya masuk additional_info.
     */
    public function applyUpdates(array $updates, ?string $censusId = null): void
    {
        $directFields = [
            'father_name', 'mother_name',
            'blood_type', 'medical_history', 'allergies', 'special_conditions',
            'school_status', 'school_name', 'school_type', 'major', 'school_year',
            'birth_city', 'hobby', 'achievement',
        ];

        foreach ($updates as $key => $value) {
            if ($value === null || $value === '') continue;

            if (in_array($key, $directFields)) {
                $this->$key = $value;
            } else {
                $this->setAdditional($key, $value);
            }
        }

        if ($censusId) {
            $this->last_census_id = $censusId;
            $this->last_updated_at = now();
        }

        $this->save();
    }

    /**
     * Apply guardian updates dari data sensus.
     * Return array guardian data yang siap di-upsert via GuardianService.
     */
    public function extractGuardianData(array $guardianUpdates): array
    {
        return array_filter($guardianUpdates, fn($v) => !empty($v));
    }

    // =========================================================================
    // Sibling Helpers
    // =========================================================================

    /**
     * Update flag saudara aktif di pondok.
     * Dipanggil oleh SiblingService saat ada perubahan relasi.
     */
    public function refreshSiblingFlag(): void
    {
        $activeSiblings = SantriSibling::where(function ($q) {
            $q->where('person_id', $this->person_id)
              ->orWhere('sibling_person_id', $this->person_id);
        })
        ->where('is_confirmed', true)
        ->count();

        $this->has_active_sibling   = $activeSiblings > 0;
        $this->active_sibling_count = $activeSiblings;
        $this->save();
    }
}
