<?php

namespace Database\Seeders;

use App\Modules\Kepengasuhan\Models\CensusTemplate;
use App\Modules\Kepengasuhan\Models\CensusTemplateField;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Kepengasuhan\Models\CensusV3Campaign;
use App\Modules\Kepengasuhan\Models\CensusV3CampaignDormitory;
use App\Modules\Kepengasuhan\Models\CensusV3Response;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CensusTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@elvith.id')->first() ?? User::first();
        $musyrifUser = User::where('email', 'musyrif@elvith.id')->first() ?? $adminUser;
        $musyrifahUser = User::where('email', 'musyrifah@elvith.id')->first() ?? $adminUser;

        $asramaPutra = Dormitory::where('gender', 'L')->first();
        $asramaPutri = Dormitory::where('gender', 'P')->first() ?? $asramaPutra;

        if (!$adminUser || !$asramaPutra) {
            return;
        }

        // =====================================================================
        // Template 1: Sensus Standar Bulanan (Default)
        // =====================================================================
        $standar = CensusTemplate::create([
            'id'          => Str::uuid()->toString(),
            'name'        => 'Sensus Standar Bulanan',
            'description' => 'Template standar untuk sensus rutin bulanan. Mencakup kehadiran, data kesehatan dasar, dan status pendidikan.',
            'is_default'  => true,
            'is_archived' => false,
            'created_by'  => $adminUser->id,
        ]);

        // Opsi attendance_status yang bersih & tidak tumpang tindih dengan enrollment_status
        $attendanceOptions = [
            'hadir'        => 'Hadir & Aktif di Komplek',
            'tidak_terlihat' => 'Tidak Terlihat / Alpa (belum konfirmasi)',
            'boyong_keluar'  => 'Boyong / Keluar (perlu diproses admin)',
        ];

        $standarFields = [
            // -- Kesehatan --
            [
                'key' => 'blood_type', 'label' => 'Golongan Darah',
                'type' => 'dropdown', 'group' => 'Kesehatan',
                'options' => ['A', 'B', 'AB', 'O', 'A+', 'B+', 'AB+', 'O+', 'A-', 'B-', 'AB-', 'O-'],
                'required' => false, 'system' => true, 'profile_key' => 'blood_type',
                'placeholder' => 'Pilih golongan darah', 'help' => null,
            ],
            [
                'key' => 'medical_history', 'label' => 'Riwayat Penyakit',
                'type' => 'textarea', 'group' => 'Kesehatan',
                'options' => null, 'required' => false, 'system' => true, 'profile_key' => 'medical_history',
                'placeholder' => 'Contoh: Asma, Diabetes, Alergi Seafood', 'help' => 'Isi jika santri memiliki riwayat penyakit atau kondisi kesehatan tertentu yang perlu diketahui.',
            ],
            // -- Pendidikan --
            [
                'key' => 'school_status', 'label' => 'Status Pendidikan',
                'type' => 'dropdown', 'group' => 'Pendidikan',
                'options' => ['mondok_full', 'sekolah_luar', 'kuliah', 'tidak_sekolah'],
                'required' => false, 'system' => true, 'profile_key' => 'school_status',
                'placeholder' => 'Pilih status pendidikan',
                'help' => '"Mondok Full" = hanya belajar di dalam pondok. "Sekolah Luar" = sekolah formal di luar pondok. "Kuliah" = sedang kuliah.',
            ],
            [
                'key' => 'school_name', 'label' => 'Nama Sekolah/Kampus',
                'type' => 'text', 'group' => 'Pendidikan',
                'options' => null, 'required' => false, 'system' => true, 'profile_key' => 'school_name',
                'placeholder' => 'Contoh: MA Al-Fithroh, UIN Sunan Ampel', 'help' => null,
            ],
        ];

        foreach ($standarFields as $idx => $f) {
            CensusTemplateField::create([
                'id'                => Str::uuid()->toString(),
                'template_id'       => $standar->id,
                'group_name'        => $f['group'],
                'field_key'         => $f['key'],
                'field_label'       => $f['label'],
                'field_type'        => $f['type'],
                'field_options'     => $f['options'],
                'placeholder_text'  => $f['placeholder'] ?? null,
                'help_text'         => $f['help'] ?? null,
                'is_required'       => $f['required'],
                'is_system_field'   => $f['system'],
                'profile_field_key' => $f['profile_key'],
                'sort_order'        => $idx + 1,
            ]);
        }

        // =====================================================================
        // Template 2: Sensus Data Wali
        // =====================================================================
        $wali = CensusTemplate::create([
            'id'          => Str::uuid()->toString(),
            'name'        => 'Sensus Data Wali',
            'description' => 'Template khusus untuk memperbarui data kontak orang tua dan wali santri.',
            'is_default'  => false,
            'is_archived' => false,
            'created_by'  => $adminUser->id,
        ]);

        $waliFields = [
            ['key' => 'father_name',  'label' => 'Nama Ayah Kandung', 'type' => 'text', 'group' => 'Data Wali', 'options' => null, 'required' => false, 'system' => true, 'profile_key' => 'father_name', 'placeholder' => 'Nama lengkap ayah kandung', 'help' => null],
            ['key' => 'father_phone', 'label' => 'HP Ayah',           'type' => 'text', 'group' => 'Data Wali', 'options' => null, 'required' => false, 'system' => true, 'profile_key' => 'father_phone', 'placeholder' => 'Contoh: 081234567890', 'help' => null],
            ['key' => 'mother_name',  'label' => 'Nama Ibu Kandung',  'type' => 'text', 'group' => 'Data Wali', 'options' => null, 'required' => false, 'system' => true, 'profile_key' => 'mother_name', 'placeholder' => 'Nama lengkap ibu kandung', 'help' => null],
            ['key' => 'mother_phone', 'label' => 'HP Ibu',            'type' => 'text', 'group' => 'Data Wali', 'options' => null, 'required' => false, 'system' => true, 'profile_key' => 'mother_phone', 'placeholder' => 'Contoh: 081234567890', 'help' => null],
            [
                'key' => 'sibling_in_pesantren', 'label' => 'Ada Saudara di Pondok?',
                'type' => 'boolean', 'group' => 'Data Wali', 'options' => null,
                'required' => false, 'system' => false, 'profile_key' => null,
                'placeholder' => null, 'help' => 'Centang jika santri memiliki saudara kandung yang juga mondok di Al-Fithroh.',
            ],
        ];

        foreach ($waliFields as $idx => $f) {
            CensusTemplateField::create([
                'id'                => Str::uuid()->toString(),
                'template_id'       => $wali->id,
                'group_name'        => $f['group'],
                'field_key'         => $f['key'],
                'field_label'       => $f['label'],
                'field_type'        => $f['type'],
                'field_options'     => $f['options'],
                'placeholder_text'  => $f['placeholder'] ?? null,
                'help_text'         => $f['help'] ?? null,
                'is_required'       => $f['required'],
                'is_system_field'   => $f['system'],
                'profile_field_key' => $f['profile_key'],
                'sort_order'        => $idx + 1,
            ]);
        }

        // =====================================================================
        // Template 3: Sensus Khataman (Contoh Template Kustom)
        // =====================================================================
        $khataman = CensusTemplate::create([
            'id'          => Str::uuid()->toString(),
            'name'        => 'Sensus Khataman',
            'description' => 'Template khusus untuk event khataman. Mencatat progress hafalan/bacaan Al-Quran santri.',
            'is_default'  => false,
            'is_archived' => false,
            'created_by'  => $adminUser->id,
        ]);

        $khatamanFields = [
            [
                'key' => 'juz_terakhir', 'label' => 'Sudah hafal/baca sampai Juz ke-',
                'type' => 'number', 'group' => 'Data Khataman', 'options' => null,
                'required' => false, 'system' => false, 'profile_key' => null,
                'placeholder' => 'Masukkan angka 1–30', 'help' => 'Isi juz terakhir yang sudah dikhatamkan atau sedang dihafal.',
            ],
            [
                'key' => 'ikut_khataman', 'label' => 'Ikut acara khataman?',
                'type' => 'boolean', 'group' => 'Data Khataman', 'options' => null,
                'required' => false, 'system' => false, 'profile_key' => null,
                'placeholder' => null, 'help' => 'Centang jika santri akan ikut serta dalam acara khataman.',
            ],
            [
                'key' => 'kitab_dipelajari', 'label' => 'Kitab yang sedang dipelajari',
                'type' => 'text', 'group' => 'Data Khataman', 'options' => null,
                'required' => false, 'system' => false, 'profile_key' => null,
                'placeholder' => 'Contoh: Fathul Qorib, Bulughul Maram', 'help' => null,
            ],
            [
                'key' => 'catatan_pengajar', 'label' => 'Catatan dari pengajar/musyrif',
                'type' => 'textarea', 'group' => 'Data Khataman', 'options' => null,
                'required' => false, 'system' => false, 'profile_key' => null,
                'placeholder' => 'Tuliskan catatan perkembangan atau kondisi khusus santri', 'help' => null,
            ],
        ];

        foreach ($khatamanFields as $idx => $f) {
            CensusTemplateField::create([
                'id'                => Str::uuid()->toString(),
                'template_id'       => $khataman->id,
                'group_name'        => $f['group'],
                'field_key'         => $f['key'],
                'field_label'       => $f['label'],
                'field_type'        => $f['type'],
                'field_options'     => $f['options'],
                'placeholder_text'  => $f['placeholder'] ?? null,
                'help_text'         => $f['help'] ?? null,
                'is_required'       => $f['required'],
                'is_system_field'   => $f['system'],
                'profile_field_key' => $f['profile_key'],
                'sort_order'        => $idx + 1,
            ]);
        }

        // =====================================================================
        // 4. Seed Kampanye Sensus 1: Sensus Bulanan Juli 2026 (Status: Collecting, Dormitory: Pending)
        // =====================================================================
        $campaign1 = CensusV3Campaign::create([
            'id'                 => Str::uuid()->toString(),
            'name'               => 'Sensus Bulanan Juli 2026',
            'description'        => 'Kampanye sensus aktif bulan Juli untuk mendata kehadiran dan status sekolah santri.',
            'template_id'        => $standar->id,
            'month'              => 7,
            'year'               => 2026,
            'target_scope'       => 'all',
            'workflow_mode'      => 'distributed',
            'allow_excel'        => true,
            'allow_direct_input' => true,
            'deadline'           => now()->addDays(14)->toDateString(),
            'status'             => 'collecting',
            'opened_at'          => now(),
            'created_by'         => $adminUser->id,
        ]);

        $totalPutra = RoomAssignment::active()
            ->join('rooms', 'room_assignments.room_id', '=', 'rooms.id')
            ->where('rooms.dormitory_id', $asramaPutra->id)
            ->count();

        CensusV3CampaignDormitory::firstOrCreate(
            ['campaign_id' => $campaign1->id, 'dormitory_id' => $asramaPutra->id],
            [
                'id'              => Str::uuid()->toString(),
                'assigned_to'     => $musyrifUser->id,
                'status'          => 'pending',
                'progress_filled' => 0,
                'progress_total'  => $totalPutra,
            ]
        );

        $totalPutri = RoomAssignment::active()
            ->join('rooms', 'room_assignments.room_id', '=', 'rooms.id')
            ->where('rooms.dormitory_id', $asramaPutri->id)
            ->count();

        CensusV3CampaignDormitory::firstOrCreate(
            ['campaign_id' => $campaign1->id, 'dormitory_id' => $asramaPutri->id],
            [
                'id'              => Str::uuid()->toString(),
                'assigned_to'     => $musyrifahUser->id,
                'status'          => 'pending',
                'progress_filled' => 0,
                'progress_total'  => $totalPutri,
            ]
        );

        // =====================================================================
        // 5. Seed Kampanye Sensus 2: Sensus Wali Juni 2026 (Dormitory: Submitted & Approved)
        // =====================================================================
        $campaign2 = CensusV3Campaign::create([
            'id'                 => Str::uuid()->toString(),
            'name'               => 'Sensus Wali Juni 2026',
            'description'        => 'Kampanye sensus bulan Juni untuk memperbarui data wali santri.',
            'template_id'        => $wali->id,
            'month'              => 6,
            'year'               => 2026,
            'target_scope'       => 'all',
            'workflow_mode'      => 'distributed',
            'allow_excel'        => true,
            'allow_direct_input' => true,
            'deadline'           => now()->subDays(5)->toDateString(),
            'status'             => 'collecting',
            'opened_at'          => now()->subDays(15),
            'created_by'         => $adminUser->id,
        ]);

        // Asrama Putra (Submitted)
        $cdPutra = CensusV3CampaignDormitory::firstOrCreate(
            ['campaign_id' => $campaign2->id, 'dormitory_id' => $asramaPutra->id],
            [
                'id'              => Str::uuid()->toString(),
                'assigned_to'     => $musyrifUser->id,
                'status'          => 'submitted',
                'progress_filled' => $totalPutra,
                'progress_total'  => $totalPutra,
                'submitted_at'    => now()->subDays(1),
            ]
        );

        // Seed responses untuk asrama putra (salah satu santri punya perubahan data wali)
        $putraSantriAssignments = RoomAssignment::active()
            ->join('rooms', 'room_assignments.room_id', '=', 'rooms.id')
            ->where('rooms.dormitory_id', $asramaPutra->id)
            ->select('room_assignments.*')
            ->get();

        foreach ($putraSantriAssignments as $idx => $assignment) {
            $hasChanges = ($idx === 0); // Hanya santri pertama yang beda datanya untuk demo sync preview

            $phone = $hasChanges ? '081299998888' : '081234567890';
            $fatherName = $hasChanges ? 'Bapak Ahmad Jaelani' : 'Ahmad';

            $responseData = [
                'father_name'       => $fatherName,
                'father_phone'      => $phone,
                'mother_name'       => 'Ibu Fatimah',
                'mother_phone'      => '081234567891',
                'sibling_in_pesantren' => false,
            ];

            $profilePreview = null;
            if ($hasChanges) {
                $profilePreview = [
                    'father_name' => [
                        'label' => 'Nama Ayah Kandung',
                        'old'   => null,
                        'new'   => $fatherName,
                    ],
                    'father_phone' => [
                        'label' => 'HP Ayah',
                        'old'   => null,
                        'new'   => $phone,
                    ]
                ];
            }

            CensusV3Response::create([
                'id'                     => Str::uuid()->toString(),
                'campaign_id'            => $campaign2->id,
                'dormitory_id'           => $asramaPutra->id,
                'person_id'              => $assignment->person_id,
                'room_id'                => $assignment->room_id,
                'response_data'          => $responseData,
                'input_method'           => 'web_ketua',
                'inputted_by'            => $musyrifUser->id,
                'is_complete'            => true,
                'has_profile_changes'    => $hasChanges,
                'profile_change_preview' => $profilePreview,
            ]);
        }

        // Asrama Putri (Approved)
        $cdPutri = CensusV3CampaignDormitory::firstOrCreate(
            ['campaign_id' => $campaign2->id, 'dormitory_id' => $asramaPutri->id],
            [
                'id'              => Str::uuid()->toString(),
                'assigned_to'     => $musyrifahUser->id,
                'status'          => 'approved',
                'progress_filled' => $totalPutri,
                'progress_total'  => $totalPutri,
                'submitted_at'    => now()->subDays(2),
                'approved_at'     => now()->subDays(1),
            ]
        );

        // Seed responses untuk asrama putri (hanya jika asrama putri terpisah)
        if ($asramaPutri && $asramaPutri->id !== $asramaPutra->id) {
            $putriSantriAssignments = RoomAssignment::active()
                ->join('rooms', 'room_assignments.room_id', '=', 'rooms.id')
                ->where('rooms.dormitory_id', $asramaPutri->id)
                ->select('room_assignments.*')
                ->get();

            foreach ($putriSantriAssignments as $assignment) {
                $responseData = [
                    'father_name'       => 'Bapak Yusuf',
                    'father_phone'      => '081234567800',
                    'mother_name'       => 'Ibu Aminah',
                    'mother_phone'      => '081234567801',
                    'sibling_in_pesantren' => false,
                ];

                CensusV3Response::create([
                    'id'                     => Str::uuid()->toString(),
                    'campaign_id'            => $campaign2->id,
                    'dormitory_id'           => $asramaPutri->id,
                    'person_id'              => $assignment->person_id,
                    'room_id'                => $assignment->room_id,
                    'response_data'          => $responseData,
                    'input_method'           => 'web_ketua',
                    'inputted_by'            => $musyrifahUser->id,
                    'is_complete'            => true,
                    'has_profile_changes'    => false,
                    'profile_change_preview' => null,
                ]);
            }
        }

        $this->command->info('✅ CensusTemplateSeeder: 3 template & 2 kampanye sensus (dengan data isian dummy) berhasil dibuat.');
    }
}
