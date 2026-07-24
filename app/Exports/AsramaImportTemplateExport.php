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

class AsramaImportTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new AsramaDataSheet(),
            new AsramaInstructionSheet(),
        ];
    }
}

class AsramaDataSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Data Asrama & Kamar';
    }

    public function headings(): array
    {
        return [
            'Nama Komplek *',
            'Gender Komplek (L/P) *',
            'Nama Kamar *',
            'Kapasitas Kamar *',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Komplek A Putra',
                'L',
                'Kamar 01',
                10,
            ],
            [
                'Komplek A Putra',
                'L',
                'Kamar 02',
                12,
            ],
            [
                'Komplek B Putri',
                'P',
                'Kamar Khadijah 1',
                8,
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Styling Header
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF059669'); // Emerald 600
        $sheet->getStyle('A1:D1')->getFont()->getColor()->setARGB('FFFFFFFF');

        // Dropdown validation for Gender column (Column B)
        for ($i = 2; $i <= 100; $i++) {
            $valGender = $sheet->getCell('B' . $i)->getDataValidation();
            $valGender->setType(DataValidation::TYPE_LIST);
            $valGender->setFormula1('"L,P"');
            $valGender->setShowDropDown(true);
            $valGender->setShowErrorMessage(true);
            $valGender->setErrorTitle('Gender Tidak Valid');
            $valGender->setError('Gender harus L (Putra) atau P (Putri).');
        }
    }
}

class AsramaInstructionSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
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
                'Nama Komplek',
                'Wajib (*)',
                'Nama komplek/gedung asrama (contoh: Komplek A Putra, Komplek Abu Bakar). Jika komplek belum ada, sistem akan membuatnya otomatis.',
            ],
            [
                'Gender Komplek',
                'Wajib (*)',
                'Pilih L (untuk Asrama Putra) atau P (untuk Asrama Putri).',
            ],
            [
                'Nama Kamar',
                'Wajib (*)',
                'Nama atau nomor kamar di dalam komplek tersebut (contoh: Kamar 01, Kamar Umar 2).',
            ],
            [
                'Kapasitas Kamar',
                'Wajib (*)',
                'Angka jumlah maksimal santri yang dapat menempati kamar (contoh: 10).',
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
