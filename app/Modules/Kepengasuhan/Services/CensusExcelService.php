<?php

namespace App\Modules\Kepengasuhan\Services;

use App\Modules\Kepengasuhan\Models\DormitoryCensus;
use App\Modules\Kepengasuhan\Models\RoomCensusDetail;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Kepengasuhan\Models\SantriProfile;
use App\Modules\Kepengasuhan\Exports\CensusTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class CensusExcelService
{
    /**
     * Generate template Excel pre-filled dengan data asrama saat ini.
     * Mengembalikan absolute path file Excel yang digenerate.
     */
    public function generateTemplate(string $dormitoryCensusId): string
    {
        $dormitoryCensus = DormitoryCensus::findOrFail($dormitoryCensusId);
        $fileName = 'sensus_' . Str::slug($dormitoryCensus->dormitory->name) . '_' . date('Y_m_d_His') . '.xlsx';
        $tempDir = storage_path('app/private/temp/census');
        
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $filePath = $tempDir . '/' . $fileName;
        
        Excel::store(new CensusTemplateExport($dormitoryCensus), 'temp/census/' . $fileName, 'local');

        return $filePath;
    }

    /**
     * Parse file Excel yang diupload dan hitung statistik serta persiapkan data import.
     */
    public function parseUpload(string $filePath, string $dormitoryCensusId): array
    {
        $dormitoryCensus = DormitoryCensus::findOrFail($dormitoryCensusId);
        
        // Baca semua sheet
        $sheets = Excel::toArray(new class {}, $filePath);

        if (count($sheets) < 4) {
            throw new Exception('Format Excel tidak valid. Pastikan template memiliki sheet: Sensus Kehadiran, Profil Santri, Data Wali, dan Saudara Kandung.');
        }

        $sensusData = $sheets[0];
        $profilData = $sheets[1];
        $waliData   = $sheets[2];
        $saudaraData = $sheets[3];

        // Dapatkan semua santri aktif di asrama tersebut untuk mencocokkan ID
        $validPersonIds = RoomAssignment::active()
            ->join('rooms', 'room_assignments.room_id', '=', 'rooms.id')
            ->where('rooms.dormitory_id', $dormitoryCensus->dormitory_id)
            ->pluck('room_assignments.person_id')
            ->toArray();

        $parsed = [];
        $totalSantri = count($validPersonIds);
        $totalConfirmed = 0;
        $totalExceptions = 0;

        // Map status dari bahasa Indonesia ke database enum
        $statusMapping = [
            'hadir'        => 'present',
            'sakit'        => 'sick',
            'izin pulang'  => 'leave',
            'alpa/kabur'   => 'absent',
            'pindah kamar' => 'moved',
        ];

        // 1. Parse Sheet Sensus Kehadiran
        // Lewati baris 0 (headings)
        for ($i = 1; $i < count($sensusData); $i++) {
            $row = $sensusData[$i];
            if (empty($row[0])) continue; // ID kosong

            $personId = trim($row[0]);
            
            // Validasi apakah santri ini memang terdaftar di asrama tersebut
            if (!in_array($personId, $validPersonIds)) continue;

            $statusLabel = strtolower(trim($row[5] ?? 'Hadir'));
            $status = $statusMapping[$statusLabel] ?? 'present';
            $notes = trim($row[6] ?? '');

            $parsed[$personId] = [
                'person_id'           => $personId,
                'status'              => $status,
                'notes'               => $notes ?: null,
                'profile_updates'     => [],
                'guardian_updates'    => [],
                'has_profile_update'  => false,
                'has_guardian_update' => false,
            ];

            $totalConfirmed++;
            if ($status !== 'present') {
                $totalExceptions++;
            }
        }

        // 2. Parse Sheet Profil Santri
        for ($i = 1; $i < count($profilData); $i++) {
            $row = $profilData[$i];
            if (empty($row[0])) continue;

            $personId = trim($row[0]);
            if (!isset($parsed[$personId])) continue;

            // Cek apakah ada perubahan dibanding profil saat ini
            $profile = SantriProfile::where('person_id', $personId)->first();

            $updates = [];
            $fields = [
                'blood_type'         => 2,
                'medical_history'    => 3,
                'allergies'          => 4,
                'special_conditions' => 5,
                'school_status'      => 6,
                'school_name'        => 7,
                'school_type'        => 8,
                'major'              => 9,
                'school_year'        => 10,
            ];

            foreach ($fields as $field => $colIdx) {
                $newValue = isset($row[$colIdx]) ? trim($row[$colIdx]) : null;
                $oldValue = $profile ? $profile->{$field} : null;

                if ($newValue !== $oldValue && $newValue !== null && $newValue !== '') {
                    $updates[$field] = $newValue;
                }
            }

            if (!empty($updates)) {
                $parsed[$personId]['profile_updates'] = array_merge($parsed[$personId]['profile_updates'], $updates);
                $parsed[$personId]['has_profile_update'] = true;
            }
        }

        // 3. Parse Sheet Data Wali
        for ($i = 1; $i < count($waliData); $i++) {
            $row = $waliData[$i];
            if (empty($row[0])) continue;

            $personId = trim($row[0]);
            if (!isset($parsed[$personId])) continue;

            $profile = SantriProfile::where('person_id', $personId)->first();

            // Cek perubahan data Orang Tua Kandung
            $parentUpdates = [];
            $parentFields = [
                'father_name' => 2,
                'father_phone' => 3,
                'father_occupation' => 4,
                'mother_name' => 5,
                'mother_phone' => 6,
            ];

            foreach ($parentFields as $field => $colIdx) {
                $newValue = isset($row[$colIdx]) ? trim($row[$colIdx]) : null;
                $oldValue = $profile ? $profile->{$field} : null;

                if ($newValue !== $oldValue && $newValue !== null && $newValue !== '') {
                    $parentUpdates[$field] = $newValue;
                }
            }

            if (!empty($parentUpdates)) {
                $parsed[$personId]['profile_updates'] = array_merge($parsed[$personId]['profile_updates'], $parentUpdates);
                $parsed[$personId]['has_profile_update'] = true;
            }

            // Cek data Wali Lain (jika diisi)
            $guardianName = isset($row[7]) ? trim($row[7]) : '';
            if (!empty($guardianName)) {
                $guardianUpdates = [
                    'name' => $guardianName,
                    'relationship' => isset($row[8]) ? trim($row[8]) : 'wali_resmi',
                    'phone_primary' => isset($row[9]) ? trim($row[9]) : '',
                    'address' => isset($row[10]) ? trim($row[10]) : '',
                    'city' => isset($row[11]) ? trim($row[11]) : '',
                ];

                $parsed[$personId]['guardian_updates'] = $guardianUpdates;
                $parsed[$personId]['has_guardian_update'] = true;
            }
        }

        // 4. Parse Sheet Saudara Kandung
        for ($i = 1; $i < count($saudaraData); $i++) {
            $row = $saudaraData[$i];
            if (empty($row[0])) continue;

            $personId = trim($row[0]);
            if (!isset($parsed[$personId])) continue;

            // Kolom C (Index 2): Ada Saudara di Pondok? (Ya/Tidak)
            $hasSiblingRaw = isset($row[2]) ? strtolower(trim($row[2])) : '';
            if ($hasSiblingRaw === 'ya' || $hasSiblingRaw === 'true' || $hasSiblingRaw === '1') {
                $parsed[$personId]['profile_updates']['has_active_sibling'] = true;
                $parsed[$personId]['has_profile_update'] = true;
            } elseif ($hasSiblingRaw === 'tidak' || $hasSiblingRaw === 'false' || $hasSiblingRaw === '0') {
                $parsed[$personId]['profile_updates']['has_active_sibling'] = false;
                $parsed[$personId]['has_profile_update'] = true;
            }

            // Kolom D (Index 3): Nama Saudara, Kolom E (Index 4): Relasi, Kolom F (Index 5): NIK/NIS
            $siblingName = isset($row[3]) ? trim($row[3]) : '';
            $siblingRelation = isset($row[4]) ? trim($row[4]) : '';
            $siblingNik = isset($row[5]) ? trim($row[5]) : '';

            if (!empty($siblingName)) {
                $parsed[$personId]['profile_updates']['sibling'] = [
                    'name' => $siblingName,
                    'relationship' => $siblingRelation ?: 'saudara',
                    'nik_nis' => $siblingNik ?: null,
                ];
                $parsed[$personId]['has_profile_update'] = true;
            }
        }

        // Hitung ulang total pengecualian: jika status present tetapi ada profile/guardian update
        foreach ($parsed as $personId => $data) {
            if ($data['status'] === 'present' && ($data['has_profile_update'] || $data['has_guardian_update'])) {
                $totalExceptions++;
            }
        }

        return [
            'details'          => array_values($parsed),
            'total_santri'     => $totalSantri,
            'total_confirmed'  => $totalConfirmed,
            'total_exceptions' => $totalExceptions,
        ];
    }

    /**
     * Jalankan proses import ke database berdasarkan hasil parsing.
     */
    public function importFromExcel(string $dormitoryCensusId, array $parsedData, string $filePath = null): void
    {
        $dormitoryCensus = DormitoryCensus::findOrFail($dormitoryCensusId);

        DB::transaction(function () use ($dormitoryCensus, $parsedData, $filePath) {
            // Update metadata di DormitoryCensus
            $dormitoryCensus->update([
                'import_source'    => 'excel',
                'import_file_path' => $filePath,
                'total_santri'     => $parsedData['total_santri'],
                'total_confirmed'  => $parsedData['total_confirmed'],
                'total_exceptions' => $parsedData['total_exceptions'],
            ]);

            // Draf ulang data census detail
            foreach ($parsedData['details'] as $detail) {
                RoomCensusDetail::updateOrCreate(
                    [
                        'dormitory_census_id' => $dormitoryCensus->id,
                        'person_id'           => $detail['person_id'],
                    ],
                    [
                        'id'                  => Str::uuid()->toString(),
                        'room_id'             => RoomAssignment::active()->where('person_id', $detail['person_id'])->first()->room_id,
                        'status'              => $detail['status'],
                        'notes'               => $detail['notes'],
                        'profile_updates'     => !empty($detail['profile_updates']) ? $detail['profile_updates'] : null,
                        'has_profile_update'  => $detail['has_profile_update'],
                        'has_guardian_update' => $detail['has_guardian_update'],
                        'guardian_updates'    => !empty($detail['guardian_updates']) ? $detail['guardian_updates'] : null,
                    ]
                );
            }
        });
    }
}
