<?php

namespace App\Imports;

use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Kepengasuhan\Services\DormitoryService;
use App\Modules\Madrasah\Models\MadrasahEnrollment;
use App\Modules\Madrasah\Models\MadrasahKelas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SantriUpdateImport implements ToCollection, WithHeadingRow
{
    public array $results = [
        'updated' => 0,
        'skipped' => [],
    ];

    public function collection(Collection $rows): void
    {
        $getField = function (array $row, string $pattern, ?string $excludePattern = null): ?string {
            foreach ($row as $k => $v) {
                $cleanK = preg_replace('/[^a-z0-9]/', '', strtolower((string)$k));
                if (preg_match($pattern, $cleanK)) {
                    if ($excludePattern && preg_match($excludePattern, $cleanK)) {
                        continue;
                    }
                    if ($v !== null && trim((string)$v) !== '') {
                        return trim((string)$v);
                    }
                }
            }
            return null;
        };

        $parseDate = function ($dateVal): ?string {
            if (!$dateVal || $dateVal === '-') return null;
            if (is_numeric($dateVal) && (float)$dateVal > 1000) {
                try {
                    return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$dateVal)->format('Y-m-d');
                } catch (\Exception $e) {}
            }
            $dateStr = trim((string)$dateVal);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
                return $dateStr;
            }
            if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $dateStr, $m)) {
                return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
            }
            $ts = strtotime($dateStr);
            return $ts ? date('Y-m-d', $ts) : null;
        };

        foreach ($rows as $rowIndex => $rowItem) {
            $rowNum = $rowIndex + 2; // +2 karena heading di row 1
            $row    = is_array($rowItem) ? $rowItem : $rowItem->toArray();

            // 1. Ambil NIS (Kunci pencocokan utama)
            $nis = $getField($row, '/^nis/');

            if (empty($nis) || $nis === '-') {
                $this->results['skipped'][] = "Baris {$rowNum}: NIS kosong, dilewati.";
                continue;
            }

            // 2. Cari santri berdasarkan NIS
            $profile = \App\Modules\Kepengasuhan\Models\SantriProfile::where('additional_info->nis', $nis)
                ->orWhere('additional_info->nis', (string)$nis)
                ->orWhereJsonContains('additional_info->nis', $nis)
                ->first();

            if (!$profile) {
                $this->results['skipped'][] = "Baris {$rowNum}: NIS '{$nis}' tidak ditemukan di sistem.";
                continue;
            }

            $person = $profile->person;
            if (!$person) {
                $this->results['skipped'][] = "Baris {$rowNum}: NIS '{$nis}' — data santri (person) tidak ditemukan.";
                continue;
            }

            try {
                DB::transaction(function () use ($getField, $parseDate, $row, $person, $profile, $nis) {
                    // =========================================================
                    // A. Update Biodata Santri pada Model Person
                    // =========================================================
                    $personUpdates = [];

                    $name = $getField($row, '/^nama.*santri|^namalengkap|^nama$/', '/wali|kamar|orangtua/');
                    if ($name && $name !== '-' && $person->name !== $name) {
                        $personUpdates['name'] = $name;
                    }

                    $nik = $getField($row, '/^nik/');
                    if ($nik && $nik !== '-' && $person->nik !== $nik) {
                        $personUpdates['nik'] = $nik;
                    }

                    $genderRaw = strtoupper((string)$getField($row, '/jeniskelamin|gender|^jk$/'));
                    $newGender = in_array($genderRaw, ['L', 'LAKI-LAKI', 'PUTRA', 'MALE']) ? 'L' : (in_array($genderRaw, ['P', 'PEREMPUAN', 'PUTRI', 'FEMALE']) ? 'P' : null);
                    if ($newGender && $person->gender !== $newGender) {
                        $personUpdates['gender'] = $newGender;
                    }

                    $birthPlace = $getField($row, '/tempatlahir/');
                    if ($birthPlace && $birthPlace !== '-' && $person->birth_place !== $birthPlace) {
                        $personUpdates['birth_place'] = $birthPlace;
                    }

                    $birthDateRaw = $getField($row, '/tanggallahir|tgl/');
                    $formattedBirthDate = $parseDate($birthDateRaw);
                    if ($formattedBirthDate) {
                        if (!$person->birth_date || $person->birth_date->format('Y-m-d') !== $formattedBirthDate) {
                            $personUpdates['birth_date'] = $formattedBirthDate;
                        }
                    }

                    $address = $getField($row, '/alamat/');
                    if ($address && $address !== '-' && $person->address !== $address) {
                        $personUpdates['address'] = $address;
                    }

                    if (!empty($personUpdates)) {
                        $person->update($personUpdates);
                    }

                    // =========================================================
                    // B. Update Profil Santri & Wali (SantriProfile)
                    // =========================================================
                    $parentName  = $getField($row, '/nama.*orangtua|nama.*wali/');
                    $parentPhone = $getField($row, '/nohp|nowa|phone|telepon|hpwa/', '/nama/');
                    $parentRel   = ucfirst(strtolower((string)$getField($row, '/hubungan/')));

                    if ($parentName && $parentName !== '-') {
                        if ($parentRel === 'Ibu') {
                            $profile->mother_name = $parentName;
                            if ($parentPhone && $parentPhone !== '-') $profile->mother_phone = $parentPhone;
                        } elseif ($parentRel === 'Ayah') {
                            $profile->father_name = $parentName;
                            if ($parentPhone && $parentPhone !== '-') $profile->father_phone = $parentPhone;
                        } else {
                            // Wali (Non-Orang Tua)
                            $profile->setAdditional('guardian_name', $parentName);
                            if ($parentPhone && $parentPhone !== '-') $profile->setAdditional('guardian_phone', $parentPhone);
                            $profile->setAdditional('guardian_relationship', $parentRel ?: 'Wali');

                            if (empty($profile->father_name)) {
                                $profile->father_name  = $parentName;
                                $profile->father_phone = $parentPhone;
                            }
                        }
                    } elseif ($parentPhone && $parentPhone !== '-') {
                        if (empty($profile->father_phone)) {
                            $profile->father_phone = $parentPhone;
                        }
                        $profile->setAdditional('guardian_phone', $parentPhone);
                    }

                    $schoolName = $getField($row, '/sekolah/');
                    if ($schoolName && $schoolName !== '-') {
                        $profile->school_name = $schoolName;
                    }

                    $hasSibRaw = strtoupper((string)$getField($row, '/saudara|kakak|adik/'));
                    if (in_array($hasSibRaw, ['YA', 'TRUE', '1', 'YES'])) {
                        $profile->has_active_sibling = true;
                    } elseif (in_array($hasSibRaw, ['TIDAK', 'FALSE', '0', 'NO'])) {
                        $profile->has_active_sibling = false;
                    }

                    $profile->save();

                    // =========================================================
                    // C. Update Status Keberadaan & Keanggotaan Role
                    // =========================================================
                    $role = $person->roles()->where('role_type', 'santri')->where('is_active', true)->first();
                    if ($role) {
                        $statusRaw = strtolower((string)$getField($row, '/statussantri|statuskeberadaan/'));
                        if (in_array($statusRaw, ['mukim', 'laju', 'izin', 'pulang']) && strtolower($role->presence_status ?? '') !== $statusRaw) {
                            $role->update(['presence_status' => $statusRaw]);
                        }

                        $enrollmentRaw = strtolower((string)$getField($row, '/statuskeanggotaan/'));
                        if (in_array($enrollmentRaw, ['aktif', 'boyong', 'keluar_resmi', 'dikeluarkan', 'alumni', 'tanpa_keterangan']) && strtolower($role->enrollment_status ?? '') !== $enrollmentRaw) {
                            $role->update(['enrollment_status' => $enrollmentRaw]);
                        }
                    }

                    // =========================================================
                    // D. Update Penempatan Kamar Asrama
                    // =========================================================
                    $newKomplek = $getField($row, '/komplek/');
                    $newKamar   = $getField($row, '/kamar/');
                    if (!empty($newKamar) && $newKamar !== '-') {
                        $roomQuery = Room::where('name', 'like', "%{$newKamar}%");
                        if (!empty($newKomplek) && $newKomplek !== '-') {
                            $roomQuery->whereHas('dormitory', fn($q) => $q->where('name', 'like', "%{$newKomplek}%"));
                        }
                        $room = $roomQuery->first();

                        if ($room) {
                            $currentAssignment = $person->roomAssignments()->where('is_active', true)->first();
                            if (!$currentAssignment || $currentAssignment->room_id !== $room->id) {
                                app(DormitoryService::class)->assignRoom($room->id, $person->id, now()->toDateString());
                            }
                        }
                    }

                    // =========================================================
                    // E. Update Kelas Madrasah
                    // =========================================================
                    $newKelas = $getField($row, '/kelas/');
                    if (!empty($newKelas) && $newKelas !== '-') {
                        $kelas = MadrasahKelas::where('is_active', true)->where('name', 'like', "%{$newKelas}%")->first();
                        if ($kelas) {
                            $currentEnrollment = $person->madrasahEnrollments()->where('is_active', true)->first();
                            if (!$currentEnrollment || $currentEnrollment->kelas_id !== $kelas->id) {
                                MadrasahEnrollment::where('person_id', $person->id)->where('is_active', true)->update(['is_active' => false]);
                                MadrasahEnrollment::updateOrCreate(
                                    ['person_id' => $person->id, 'kelas_id' => $kelas->id, 'academic_year' => $kelas->academic_year],
                                    ['is_active' => true, 'created_by' => auth()->id()]
                                );
                            }
                        }
                    }

                    activity()
                        ->performedOn($person)
                        ->causedBy(auth()->user())
                        ->log("Data santri diperbarui via import Excel massal (NIS: {$nis})");
                });

                $this->results['updated']++;
            } catch (\Exception $e) {
                $this->results['skipped'][] = "Baris {$rowNum}: NIS '{$nis}' — error: " . $e->getMessage();
            }
        }
    }

    public static function parsePreview(string $filePath): array
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $worksheet   = $spreadsheet->getActiveSheet();
        $rows        = $worksheet->toArray();

        if (count($rows) <= 1) {
            return [
                'stats' => ['total' => 0, 'changed' => 0, 'unchanged' => 0, 'skipped' => 0],
                'rows'  => [],
            ];
        }

        // Header row
        $headersRaw = array_shift($rows);

        $getField = function (array $rowArray, string $pattern, ?string $excludePattern = null) use ($headersRaw): ?string {
            foreach ($headersRaw as $idx => $headerText) {
                $cleanK = preg_replace('/[^a-z0-9]/', '', strtolower((string)$headerText));
                if (preg_match($pattern, $cleanK)) {
                    if ($excludePattern && preg_match($excludePattern, $cleanK)) {
                        continue;
                    }
                    if (isset($rowArray[$idx]) && trim((string)$rowArray[$idx]) !== '') {
                        return trim((string)$rowArray[$idx]);
                    }
                }
            }
            return null;
        };

        $parseDate = function ($dateVal): ?string {
            if (!$dateVal || $dateVal === '-') return null;
            if (is_numeric($dateVal) && (float)$dateVal > 1000) {
                try {
                    return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$dateVal)->format('Y-m-d');
                } catch (\Exception $e) {}
            }
            $dateStr = trim((string)$dateVal);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
                return $dateStr;
            }
            if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $dateStr, $m)) {
                return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
            }
            $ts = strtotime($dateStr);
            return $ts ? date('Y-m-d', $ts) : null;
        };

        $previewRows = [];
        $stats = ['total' => count($rows), 'changed' => 0, 'unchanged' => 0, 'skipped' => 0];

        foreach ($rows as $index => $rowArray) {
            $rowNum = $index + 2;
            $nis    = $getField($rowArray, '/^nis/');

            if (empty($nis) || $nis === '-') {
                $stats['skipped']++;
                $previewRows[] = [
                    'row_num' => $rowNum,
                    'name'    => $getField($rowArray, '/^nama.*santri|^namalengkap|^nama$/', '/wali|kamar|orangtua/') ?? '-',
                    'nis'     => $nis ?: '-',
                    'status'  => 'skipped',
                    'reason'  => 'NIS Kosong',
                    'diffs'   => [],
                ];
                continue;
            }

            $profile = \App\Modules\Kepengasuhan\Models\SantriProfile::where('additional_info->nis', $nis)
                ->orWhere('additional_info->nis', (string)$nis)
                ->orWhereJsonContains('additional_info->nis', $nis)
                ->first();

            if (!$profile || !$profile->person) {
                $stats['skipped']++;
                $previewRows[] = [
                    'row_num' => $rowNum,
                    'name'    => $getField($rowArray, '/^nama.*santri|^namalengkap|^nama$/', '/wali|kamar|orangtua/') ?? '-',
                    'nis'     => $nis,
                    'status'  => 'skipped',
                    'reason'  => 'NIS Tidak Ditemukan di Sistem',
                    'diffs'   => [],
                ];
                continue;
            }

            $person     = $profile->person;
            $role       = $person->roles->firstWhere('role_type', 'santri') ?? $person->roles->first();
            $assignment = $person->roomAssignments->firstWhere('is_active', true) ?? $person->roomAssignments->first();
            $enrollment = $person->madrasahEnrollments->firstWhere('is_active', true) ?? $person->madrasahEnrollments->first();

            $diffs = [];

            // 1. Nama
            $newName = $getField($rowArray, '/^nama.*santri|^namalengkap|^nama$/', '/wali|kamar|orangtua/');
            if ($newName && $newName !== '-' && $person->name !== $newName) {
                $diffs[] = ['field' => 'Nama Santri', 'old' => $person->name, 'new' => $newName];
            }

            // 2. NIK
            $newNik = $getField($rowArray, '/^nik/');
            if ($newNik && $newNik !== '-' && $person->nik !== $newNik) {
                $diffs[] = ['field' => 'NIK', 'old' => $person->nik ?: '-', 'new' => $newNik];
            }

            // 3. Gender
            $genderRaw = strtoupper((string)$getField($rowArray, '/jeniskelamin|gender|^jk$/'));
            $newGender = in_array($genderRaw, ['L', 'LAKI-LAKI', 'PUTRA', 'MALE']) ? 'L' : (in_array($genderRaw, ['P', 'PEREMPUAN', 'PUTRI', 'FEMALE']) ? 'P' : null);
            if ($newGender && $person->gender !== $newGender) {
                $diffs[] = ['field' => 'Gender', 'old' => $person->gender === 'L' ? 'Putra' : 'Putri', 'new' => $newGender === 'L' ? 'Putra' : 'Putri'];
            }

            // 4. Tempat Lahir
            $birthPlace = $getField($rowArray, '/tempatlahir/');
            if ($birthPlace && $birthPlace !== '-' && $person->birth_place !== $birthPlace) {
                $diffs[] = ['field' => 'Tempat Lahir', 'old' => $person->birth_place ?: '-', 'new' => $birthPlace];
            }

            // 5. Tanggal Lahir
            $birthDateRaw = $getField($rowArray, '/tanggallahir|tgl/');
            $formattedBirthDate = $parseDate($birthDateRaw);
            $currentBirthDate = $person->birth_date ? $person->birth_date->format('Y-m-d') : null;
            if ($formattedBirthDate && $currentBirthDate !== $formattedBirthDate) {
                $diffs[] = ['field' => 'Tanggal Lahir', 'old' => $currentBirthDate ?: '-', 'new' => $formattedBirthDate];
            }

            // 6. Status Keberadaan
            $statusRaw = strtolower((string)$getField($rowArray, '/statussantri|statuskeberadaan/'));
            if (in_array($statusRaw, ['mukim', 'laju', 'izin', 'pulang']) && strtolower($role?->presence_status ?? '') !== $statusRaw) {
                $diffs[] = ['field' => 'Status Keberadaan', 'old' => ucfirst($role?->presence_status ?? '-'), 'new' => ucfirst($statusRaw)];
            }

            // 7. Kamar & Komplek
            $newKamar   = $getField($rowArray, '/kamar/');
            $newKomplek = $getField($rowArray, '/komplek/');
            $currentKamarName = $assignment?->room?->name ?? '-';
            if ($newKamar && $newKamar !== '-' && $currentKamarName !== $newKamar) {
                $diffs[] = ['field' => 'Penempatan Kamar', 'old' => $currentKamarName, 'new' => ($newKomplek ? $newKomplek . ' - ' : '') . $newKamar];
            }

            // 8. Kelas
            $newKelas = $getField($rowArray, '/kelas/');
            $currentKelasName = $enrollment?->kelas?->name ?? '-';
            if ($newKelas && $newKelas !== '-' && $currentKelasName !== $newKelas) {
                $diffs[] = ['field' => 'Kelas Madrasah', 'old' => $currentKelasName, 'new' => $newKelas];
            }

            // 9. Nama Wali
            $parentName = $getField($rowArray, '/nama.*orangtua|nama.*wali/');
            $currentParentName = $profile->father_name ?: ($profile->mother_name ?: ($profile->getAdditional('guardian_name') ?? '-'));
            if ($parentName && $parentName !== '-' && $currentParentName !== $parentName) {
                $diffs[] = ['field' => 'Nama Wali', 'old' => $currentParentName, 'new' => $parentName];
            }

            // 10. HP Wali
            $parentPhone = $getField($rowArray, '/nohp|nowa|phone|telepon|hpwa/', '/nama/');
            $currentParentPhone = $profile->father_phone ?: ($profile->mother_phone ?: ($profile->getAdditional('guardian_phone') ?? '-'));
            if ($parentPhone && $parentPhone !== '-' && $currentParentPhone !== $parentPhone) {
                $diffs[] = ['field' => 'No HP Wali', 'old' => $currentParentPhone, 'new' => $parentPhone];
            }

            // 11. Alamat
            $address = $getField($rowArray, '/alamat/');
            if ($address && $address !== '-' && $person->address !== $address) {
                $diffs[] = ['field' => 'Alamat', 'old' => $person->address ?: '-', 'new' => $address];
            }

            // 12. Sekolah
            $schoolName = $getField($rowArray, '/sekolah/');
            if ($schoolName && $schoolName !== '-' && $profile->school_name !== $schoolName) {
                $diffs[] = ['field' => 'Sekolah Formal', 'old' => $profile->school_name ?: '-', 'new' => $schoolName];
            }

            if (!empty($diffs)) {
                $stats['changed']++;
                $previewRows[] = [
                    'row_num' => $rowNum,
                    'name'    => $person->name,
                    'nis'     => $nis,
                    'status'  => 'changed',
                    'diffs'   => $diffs,
                ];
            } else {
                $stats['unchanged']++;
                $previewRows[] = [
                    'row_num' => $rowNum,
                    'name'    => $person->name,
                    'nis'     => $nis,
                    'status'  => 'unchanged',
                    'diffs'   => [],
                ];
            }
        }

        return [
            'stats' => $stats,
            'rows'  => $previewRows,
        ];
    }
}
