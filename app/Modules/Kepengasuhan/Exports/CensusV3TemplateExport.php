<?php

namespace App\Modules\Kepengasuhan\Exports;

use App\Modules\Kepengasuhan\Models\CensusV3CampaignDormitory;
use App\Modules\Kepengasuhan\Models\CensusV3Response;
use App\Modules\Kepengasuhan\Services\CensusV3Service;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Illuminate\Support\Collection;

class CensusV3TemplateExport implements WithMultipleSheets
{
    protected CensusV3CampaignDormitory $campaignDormitory;

    public function __construct(CensusV3CampaignDormitory $campaignDormitory)
    {
        $this->campaignDormitory = $campaignDormitory;
    }

    public function sheets(): array
    {
        return [
            new SensusV3DataSheet($this->campaignDormitory),
            new SensusV3InstructionSheet($this->campaignDormitory),
        ];
    }
}

// =========================================================================
// Sheet 1: Lembar Isian Data Sensus
// =========================================================================
class SensusV3DataSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected CensusV3CampaignDormitory $campaignDormitory;
    protected Collection $santriList;

    public function __construct(CensusV3CampaignDormitory $campaignDormitory)
    {
        $this->campaignDormitory = $campaignDormitory;
        
        $censusService = app(CensusV3Service::class);
        $this->santriList = $censusService->getSantriForInput($campaignDormitory->id);
    }

    public function title(): string
    {
        return 'Isian Sensus';
    }

    public function headings(): array
    {
        $headers = [
            'ID Santri (Jangan Diubah)',
            'Nama Lengkap',
            'Kamar',
            'Status Anggota',
            'Status Keberadaan',
        ];

        // Tambah dynamic template fields
        $fields = $this->campaignDormitory->campaign->template->fields;
        foreach ($fields as $field) {
            $headers[] = $field->field_label . ' (' . $field->field_key . ')';
        }

        return $headers;
    }

    public function array(): array
    {
        $data = [];
        $fields = $this->campaignDormitory->campaign->template->fields;

        foreach ($this->santriList as $s) {
            $personId = $s->person_id;

            // Load existing responses
            $existingResponse = $s->response_data ? (is_string($s->response_data) ? json_decode($s->response_data, true) : $s->response_data) : [];

            $row = [
                $personId,
                $s->person_name,
                $s->room_name ?? '-',
                $s->enrollment_status ?? 'aktif',
                $s->presence_status ?? 'mukim',
            ];

            // Tambah dynamic values
            foreach ($fields as $field) {
                $val = $existingResponse[$field->field_key] ?? null;
                if ($field->field_type === 'boolean') {
                    $row[] = ($val === true || $val === 'YA' || $val === '1') ? 'YA' : 'TIDAK';
                } else {
                    $row[] = $val ?? '';
                }
            }

            $data[] = $row;
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $fields = $this->campaignDormitory->campaign->template->fields;
        $colCount = 5 + $fields->count();
        $lastLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
        
        // Bold dan abu-abu untuk header
        $sheet->getStyle('A1:' . $lastLetter . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $lastLetter . '1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        $rowCount = $this->santriList->count() + 1;

        // Tambah visual comment/tooltip di header kolom untuk memandu user
        $sheet->getComment('D1')->getText()->createTextRun(
            "Status Anggota (Keaktifan Santri)\n\nPilihan valid:\n- aktif\n- alumni\n- keluar_resmi\n- dikeluarkan\n- tanpa_keterangan"
        );
        
        $sheet->getComment('E1')->getText()->createTextRun(
            "Status Keberadaan (Lokasi Santri)\n\nPilihan valid:\n- mukim\n- laju\n- izin\n- alpa"
        );

        $colIdx = 6;
        foreach ($fields as $field) {
            $cellLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            
            $commentText = $field->field_label;
            if ($field->is_required) {
                $commentText .= " [WAJIB DIISI]";
            }
            
            if ($field->field_type === 'dropdown' && !empty($field->field_options)) {
                $commentText .= "\n\nPilihan opsi:\n- " . implode("\n- ", $field->field_options);
            } elseif ($field->field_type === 'boolean') {
                $commentText .= "\n\nPilihan opsi:\n- YA\n- TIDAK";
            }
            
            if ($field->help_text) {
                $commentText .= "\n\nPetunjuk: " . $field->help_text;
            }
            
            $sheet->getComment($cellLetter . '1')->getText()->createTextRun($commentText);
            $colIdx++;
        }

        // Tambah validasi dropdown dan proteksi data input
        for ($i = 2; $i <= $rowCount; $i++) {
            // Enrollment Status (Column D)
            $valEnroll = $sheet->getCell('D' . $i)->getDataValidation();
            $valEnroll->setType(DataValidation::TYPE_LIST);
            $valEnroll->setFormula1('"aktif,alumni,keluar_resmi,dikeluarkan,tanpa_keterangan"');
            $valEnroll->setShowDropDown(true);
            $valEnroll->setShowErrorMessage(true);
            $valEnroll->setErrorTitle('Pilihan Tidak Valid');
            $valEnroll->setError('Status Anggota harus dipilih dari daftar dropdown.');

            // Presence Status (Column E)
            $valPresence = $sheet->getCell('E' . $i)->getDataValidation();
            $valPresence->setType(DataValidation::TYPE_LIST);
            $valPresence->setFormula1('"mukim,laju,izin,alpa"');
            $valPresence->setShowDropDown(true);
            $valPresence->setShowErrorMessage(true);
            $valPresence->setErrorTitle('Pilihan Tidak Valid');
            $valPresence->setError('Status Keberadaan harus dipilih dari daftar dropdown.');

            // Dropdown untuk kolom template dinamis
            $colIdx = 6;
            foreach ($fields as $field) {
                $cellLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                
                if ($field->field_type === 'dropdown' && !empty($field->field_options)) {
                    $valField = $sheet->getCell($cellLetter . $i)->getDataValidation();
                    $valField->setType(DataValidation::TYPE_LIST);
                    $optionsStr = implode(',', $field->field_options);
                    $valField->setFormula1('"' . $optionsStr . '"');
                    $valField->setShowDropDown(true);
                    $valField->setShowErrorMessage(true);
                    $valField->setErrorTitle('Opsi Salah');
                    $valField->setError('Isian harus dipilih dari opsi dropdown yang disediakan.');
                } elseif ($field->field_type === 'boolean') {
                    $valField = $sheet->getCell($cellLetter . $i)->getDataValidation();
                    $valField->setType(DataValidation::TYPE_LIST);
                    $valField->setFormula1('"YA,TIDAK"');
                    $valField->setShowDropDown(true);
                    $valField->setShowErrorMessage(true);
                    $valField->setErrorTitle('Format Salah');
                    $valField->setError('Isian harus berupa YA atau TIDAK.');
                }
                
                $colIdx++;
            }
        }

        // Sembunyikan kolom ID Santri agar tampilan rapi
        $sheet->getColumnDimension('A')->setVisible(false);
    }
}

// =========================================================================
// Sheet 2: Lembar Panduan Pengisian
// =========================================================================
class SensusV3InstructionSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected CensusV3CampaignDormitory $campaignDormitory;

    public function __construct(CensusV3CampaignDormitory $campaignDormitory)
    {
        $this->campaignDormitory = $campaignDormitory;
    }

    public function title(): string
    {
        return 'Panduan Pengisian';
    }

    public function headings(): array
    {
        return [
            'Nama Kolom di Excel',
            'Tipe Kolom',
            'Status Wajib',
            'Penjelasan Detail & Opsi Pilihan',
            'Contoh Nilai',
        ];
    }

    public function array(): array
    {
        $data = [
            [
                'ID Santri (Jangan Diubah)',
                'ID Kunci Sistem',
                'PENTING (Jangan Diedit)',
                "Kolom ID Santri (Kolom A) sengaja disembunyikan agar rapi.\nSistem menggunakan kolom ini untuk menyinkronkan data. JANGAN menghapus, mengubah, atau memindahkan letak kolom ini.",
                'f4e66810-c637-4d8e-b5f7-f823...',
            ],
            [
                'Nama Lengkap',
                'Teks',
                'Hanya Baca (Read-only)',
                'Nama lengkap santri yang disensus. Perubahan nama di Excel tidak akan memengaruhi data master santri di sistem.',
                'Santri Ahmad',
            ],
            [
                'Kamar',
                'Teks',
                'Hanya Baca (Read-only)',
                'Kamar asrama santri saat ini.',
                'Kamar A-01',
            ],
            [
                'Status Anggota',
                'Pilihan Dropdown',
                'WAJIB DIISI',
                "Status keanggotaan santri di pondok.\nPilihan dropdown:\n- aktif (santri aktif)\n- alumni (lulus)\n- keluar_resmi (berhenti resmi/berkas lengkap)\n- dikeluarkan (sanksi berat)\n- tanpa_keterangan (kabur/lama tidak kembali)",
                'aktif',
            ],
            [
                'Status Keberadaan',
                'Pilihan Dropdown',
                'WAJIB DIISI',
                "Status keberadaan/kehadiran santri di komplek.\nPilihan dropdown:\n- mukim (berada di komplek/asrama)\n- laju (pulang-pergi/tidak menetap di asrama)\n- izin (sakit, izin pulang, kegiatan luar)\n- alpa (tidak ada di asrama/kabur tanpa izin/keterangan)",
                'mukim',
            ],
        ];

        // Tambah dynamic template fields ke dalam panduan
        $fields = $this->campaignDormitory->campaign->template->fields;
        foreach ($fields as $field) {
            $typeLabel = match ($field->field_type) {
                'dropdown' => 'Pilihan Dropdown',
                'boolean'  => 'Pilihan YA/TIDAK',
                'number'   => 'Angka',
                'date'     => 'Tanggal (YYYY-MM-DD)',
                'textarea' => 'Teks Panjang',
                default    => 'Teks Bebas',
            };

            $requiredLabel = $field->is_required ? 'WAJIB DIISI' : 'Opsional';

            $desc = $field->field_label;
            if ($field->field_type === 'dropdown' && !empty($field->field_options)) {
                $desc .= ".\nOpsi pilihan:\n- " . implode("\n- ", $field->field_options);
            } elseif ($field->field_type === 'boolean') {
                $desc .= ".\nOpsi pilihan:\n- YA\n- TIDAK";
            }
            
            if ($field->help_text) {
                $desc .= "\n\nPetunjuk Tambahan: " . $field->help_text;
            }

            $example = '';
            if ($field->field_type === 'dropdown' && !empty($field->field_options)) {
                $example = $field->field_options[0];
            } elseif ($field->field_type === 'boolean') {
                $example = 'YA';
            } elseif ($field->field_type === 'number') {
                $example = '12';
            } elseif ($field->field_type === 'date') {
                $example = date('Y-m-d');
            } else {
                $example = 'Contoh Data';
            }

            $data[] = [
                $field->field_label . ' (' . $field->field_key . ')',
                $typeLabel,
                $requiredLabel,
                $desc,
                $example,
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Set alignment wrap text agar penjelasan yang panjang tidak memotong kolom
        $sheet->getStyle('D2:D100')->getAlignment()->setWrapText(true);
    }
}
