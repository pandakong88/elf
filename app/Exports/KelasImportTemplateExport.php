<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class KelasImportTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new KelasDataSheet(),
            new KelasInstructionSheet(),
        ];
    }
}

class KelasDataSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Data Kelas Madrasah';
    }

    public function headings(): array
    {
        return [
            'Nama Kelas *',
            'Tingkat (Ula/Wustho/Ulya/Umum) *',
            'Gender (L/P/Campur)',
            'Kapasitas Santri',
        ];
    }

    public function array(): array
    {
        return [
            [
                '1 Ula A',
                'Ula',
                'L',
                30,
            ],
            [
                '1 Ula B',
                'Ula',
                'P',
                30,
            ],
            [
                '2 Wustho 1',
                'Wustho',
                'Campur',
                25,
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header Styling
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF4F46E5'); // Indigo 600
        $sheet->getStyle('A1:D1')->getFont()->getColor()->setARGB('FFFFFFFF');

        // Dropdown validation for Tingkat (Column B) and Gender (Column C)
        for ($i = 2; $i <= 100; $i++) {
            // Tingkat
            $valTingkat = $sheet->getCell('B' . $i)->getDataValidation();
            $valTingkat->setType(DataValidation::TYPE_LIST);
            $valTingkat->setFormula1('"Ula,Wustho,Ulya,Umum"');
            $valTingkat->setShowDropDown(true);
            $valTingkat->setShowErrorMessage(true);
            $valTingkat->setErrorTitle('Tingkat Tidak Valid');
            $valTingkat->setError('Tingkat harus salah satu dari: Ula, Wustho, Ulya, atau Umum.');

            // Gender
            $valGender = $sheet->getCell('C' . $i)->getDataValidation();
            $valGender->setType(DataValidation::TYPE_LIST);
            $valGender->setFormula1('"L,P,Campur"');
            $valGender->setShowDropDown(true);
            $valGender->setShowErrorMessage(true);
            $valGender->setErrorTitle('Gender Tidak Valid');
            $valGender->setError('Gender harus L (Putra), P (Putri), atau Campur.');
        }
    }
}

class KelasInstructionSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Petunjuk Pengisian';
    }

    public function headings(): array
    {
        return [
            'Kolom',
            'Wajib / Opsional',
            'Format & Aturan Pengisian',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Nama Kelas',
                'Wajib (*)',
                'Nama ruang/rombel kelas madrasah (contoh: 1 Ula A, 2 Wustho 1, Class A).',
            ],
            [
                'Tingkat',
                'Wajib (*)',
                'Pilih tingkat jenjang pendidikan: Ula, Wustho, Ulya, atau Umum.',
            ],
            [
                'Gender',
                'Opsional',
                'Pilih L (Khusus Putra), P (Khusus Putri), atau Campur (Putra & Putri). Default: Campur.',
            ],
            [
                'Kapasitas Santri',
                'Opsional',
                'Jumlah maksimal kuota santri dalam kelas tersebut (contoh: 30). Default: 40.',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0');
    }
}
