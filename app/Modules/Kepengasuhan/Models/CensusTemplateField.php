<?php

namespace App\Modules\Kepengasuhan\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CensusTemplateField extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'census_template_fields';

    protected $fillable = [
        'template_id',
        'group_name',
        'field_key',
        'field_label',
        'field_type',
        'field_options',
        'placeholder_text',
        'help_text',
        'is_required',
        'is_system_field',
        'profile_field_key',
        'sort_order',
    ];

    protected $casts = [
        'field_options'   => 'array',
        'is_required'     => 'boolean',
        'is_system_field' => 'boolean',
        'sort_order'      => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function template(): BelongsTo
    {
        return $this->belongsTo(CensusTemplate::class, 'template_id');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /** Tipe field dalam bahasa yang ramah pengguna */
    public function getTypeLabel(): string
    {
        return match ($this->field_type) {
            'text'      => 'Teks Singkat',
            'textarea'  => 'Teks Panjang',
            'dropdown'  => 'Pilihan Ganda',
            'boolean'   => 'Ya / Tidak',
            'number'    => 'Angka',
            'date'      => 'Tanggal',
            default     => $this->field_type,
        };
    }

    /** Icon untuk tipe field */
    public function getTypeIcon(): string
    {
        return match ($this->field_type) {
            'text'      => '📝',
            'textarea'  => '📄',
            'dropdown'  => '🔘',
            'boolean'   => '✅',
            'number'    => '🔢',
            'date'      => '📅',
            default     => '❓',
        };
    }

    /** Semua sistem field yang tersedia untuk dipilih */
    public static function systemFieldDefinitions(): array
    {
        return [
            ['key' => 'blood_type',           'label' => 'Golongan Darah',           'type' => 'dropdown', 'group' => 'Kesehatan',  'options' => ['A', 'B', 'AB', 'O', 'A+', 'B+', 'AB+', 'O+'],             'profile_key' => 'blood_type',         'required' => false],
            ['key' => 'medical_history',      'label' => 'Riwayat Penyakit',         'type' => 'textarea', 'group' => 'Kesehatan',  'options' => null,                                                         'profile_key' => 'medical_history',    'required' => false],
            ['key' => 'allergies',            'label' => 'Alergi',                   'type' => 'text',     'group' => 'Kesehatan',  'options' => null,                                                         'profile_key' => 'allergies',          'required' => false],
            ['key' => 'special_conditions',   'label' => 'Kondisi Khusus',           'type' => 'textarea', 'group' => 'Kesehatan',  'options' => null,                                                         'profile_key' => 'special_conditions', 'required' => false],
            ['key' => 'school_status',        'label' => 'Status Pendidikan',        'type' => 'dropdown', 'group' => 'Pendidikan', 'options' => ['mondok_full', 'sekolah_luar', 'kuliah', 'tidak_sekolah'],  'profile_key' => 'school_status',      'required' => false],
            ['key' => 'school_name',          'label' => 'Nama Sekolah / Kampus',    'type' => 'text',     'group' => 'Pendidikan', 'options' => null,                                                         'profile_key' => 'school_name',        'required' => false],
            ['key' => 'major',                'label' => 'Jurusan',                  'type' => 'text',     'group' => 'Pendidikan', 'options' => null,                                                         'profile_key' => 'major',              'required' => false],
            ['key' => 'school_year',          'label' => 'Kelas / Semester',         'type' => 'text',     'group' => 'Pendidikan', 'options' => null,                                                         'profile_key' => 'school_year',        'required' => false],
            ['key' => 'father_name',          'label' => 'Nama Ayah Kandung',        'type' => 'text',     'group' => 'Data Wali',  'options' => null,                                                         'profile_key' => 'father_name',        'required' => false],
            ['key' => 'mother_name',          'label' => 'Nama Ibu Kandung',         'type' => 'text',     'group' => 'Data Wali',  'options' => null,                                                         'profile_key' => 'mother_name',        'required' => false],
            ['key' => 'father_phone',         'label' => 'HP Ayah',                  'type' => 'text',     'group' => 'Data Wali',  'options' => null,                                                         'profile_key' => 'father_phone',       'required' => false],
            ['key' => 'mother_phone',         'label' => 'HP Ibu',                   'type' => 'text',     'group' => 'Data Wali',  'options' => null,                                                         'profile_key' => 'mother_phone',       'required' => false],
            ['key' => 'sibling_in_pesantren', 'label' => 'Ada Saudara di Pondok?',   'type' => 'boolean',  'group' => 'Data Wali',  'options' => null,                                                         'profile_key' => null,                 'required' => false],
        ];
    }
}
