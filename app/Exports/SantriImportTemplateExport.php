<?php

namespace App\Exports;

use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Madrasah\Models\MadrasahKelas;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class SantriImportTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new SantriDataSheet(),
            new SantriReferenceSheet(),
            new SantriInstructionSheet(),
        ];
    }
}

class SantriDataSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Isian Data Santri & Wali';
    }

    public function headings(): array
    {
        return [
            'Nama Lengkap Santri *',
            'NIK / No. Identitas',
            'NIS (Nomor Induk Santri)',
            'Jenis Kelamin (L/P) *',
            'Tempat Lahir',
            'Tanggal Lahir (YYYY-MM-DD)',
            'Status Santri (Mukim/Laju) *',
            'Komplek Asrama',
            'Nama Kamar',
            'Kelas Madrasah',
            'Nama Orang Tua / Wali *',
            'No. HP / WA Wali *',
            'Hubungan Wali',
            'Alamat Lengkap',
            'Asal Sekolah',
            'Ada Saudara di Pondok? (Ya/Tidak)',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Ahmad Syauqi',
                '3578011234560001',
                '2026001',
                'L',
                'Surabaya',
                '2010-05-15',
                'Mukim',
                'Komplek A - Al-Fithroh Putra',
                'Kamar 101',
                'Kelas 1 Ula Putra',
                'Bapak Syamsuddin',
                '081234567890',
                'Ayah',
                'Jl. Raya Ampel No. 45, Surabaya',
                'SDN Ampel 1',
                'Ya',
            ],
            [
                'Siti Fatimah',
                '3578016543210002',
                '2026002',
                'P',
                'Gresik',
                '2011-08-20',
                'Laju',
                '',
                '',
                'Kelas 1 Ula Putri',
                'Ibu Maryam',
                '085712345678',
                'Ibu',
                'Jl. Veteran No. 12, Gresik',
                'MI Miftahul Ulum',
                'Tidak',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:P1')->getFont()->setBold(true);
        $sheet->getStyle('A1:P1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0');

        // Dropdown lists
        $dormitories = Dormitory::where('is_active', true)->orderBy('name')->pluck('name')->toArray();
        $dormitoriesStr = !empty($dormitories) ? implode(',', $dormitories) : '-';

        $kelasList = MadrasahKelas::where('is_active', true)->orderBy('name')->pluck('name')->toArray();
        $kelasStr = !empty($kelasList) ? implode(',', $kelasList) : '-';

        for ($i = 2; $i <= 200; $i++) {
            // Jenis Kelamin (Column D)
            $valGender = $sheet->getCell('D' . $i)->getDataValidation();
            $valGender->setType(DataValidation::TYPE_LIST);
            $valGender->setFormula1('"L,P"');
            $valGender->setShowDropDown(true);

            // Status Santri (Column G)
            $valStatus = $sheet->getCell('G' . $i)->getDataValidation();
            $valStatus->setType(DataValidation::TYPE_LIST);
            $valStatus->setFormula1('"Mukim,Laju"');
            $valStatus->setShowDropDown(true);

            // Komplek Asrama (Column H)
            if (!empty($dormitories)) {
                $valDorm = $sheet->getCell('H' . $i)->getDataValidation();
                $valDorm->setType(DataValidation::TYPE_LIST);
                $valDorm->setFormula1('"' . $dormitoriesStr . '"');
                $valDorm->setShowDropDown(true);
            }

            // Kelas Madrasah (Column J)
            if (!empty($kelasList)) {
                $valKelas = $sheet->getCell('J' . $i)->getDataValidation();
                $valKelas->setType(DataValidation::TYPE_LIST);
                $valKelas->setFormula1('"' . $kelasStr . '"');
                $valKelas->setShowDropDown(true);
            }

            // Hubungan Wali (Column M)
            $valHub = $sheet->getCell('M' . $i)->getDataValidation();
            $valHub->setType(DataValidation::TYPE_LIST);
            $valHub->setFormula1('"Ayah,Ibu,Wali"');
            $valHub->setShowDropDown(true);

            // Ada Saudara di Pondok? (Column P)
            $valSib = $sheet->getCell('P' . $i)->getDataValidation();
            $valSib->setType(DataValidation::TYPE_LIST);
            $valSib->setFormula1('"Ya,Tidak"');
            $valSib->setShowDropDown(true);
        }
    }
}

class SantriReferenceSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Referensi Komplek & Kelas';
    }

    public function headings(): array
    {
        return [
            'Daftar Komplek Asrama Terdaftar',
            'Gender Komplek',
            'Daftar Kelas Madrasah Terdaftar',
        ];
    }

    public function array(): array
    {
        $dormitories = Dormitory::where('is_active', true)->orderBy('name')->get();
        $kelasList   = MadrasahKelas::where('is_active', true)->orderBy('name')->get();

        $max = max($dormitories->count(), $kelasList->count(), 1);
        $rows = [];

        for ($i = 0; $i < $max; $i++) {
            $dorm = $dormitories->get($i);
            $kelas = $kelasList->get($i);

            $rows[] = [
                $dorm?->name ?? '',
                $dorm ? ($dorm->gender === 'L' ? 'Putra (L)' : 'Putri (P)') : '',
                $kelas?->name ?? '',
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0');
    }
}

class SantriInstructionSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Petunjuk Pengisian';
    }

    public function headings(): array
    {
        return ['Kolom', 'Format / Nilai Valid', 'Keterangan'];
    }

    public function array(): array
    {
        return [
            ['Nama Lengkap Santri *', 'Teks Bebas', 'Wajib diisi. Contoh: Ahmad Syauqi'],
            ['NIK / No. Identitas', '16 Digit Angka', 'Opsional. Jika kosong akan dibuatkan ID internal.'],
            ['NIS (Nomor Induk Santri)', 'Nomor Induk / Angka', 'Opsional. Jika kosong akan digenerate otomatis.'],
            ['Jenis Kelamin *', 'L / P', 'Wajib. L = Putra, P = Putri.'],
            ['Tempat Lahir', 'Teks Bebas', 'Kota/Kabupaten lahir. Contoh: Surabaya'],
            ['Tanggal Lahir', 'YYYY-MM-DD', 'Format tahun-bulan-tanggal. Contoh: 2010-05-15'],
            ['Status Santri *', 'Mukim / Laju', 'Wajib. Mukim = tinggal di pondok, Laju = pulang pergi.'],
            ['Komplek Asrama', 'Pilih dari Dropdown List', 'Wajib untuk santri Mukim. Pilih nama komplek yang sesuai.'],
            ['Nama Kamar', 'Teks Bebas', 'Wajib untuk santri Mukim. Contoh: Kamar 101'],
            ['Kelas Madrasah', 'Pilih dari Dropdown List', 'Wajib untuk santri Mukim & Laju.'],
            ['Nama Orang Tua / Wali *', 'Teks Bebas', 'Wajib diisi nama Ayah/Ibu/Wali.'],
            ['No. HP / WA Wali *', 'Format No. HP (08xxx)', 'Wajib diisi nomor WhatsApp aktif wali.'],
            ['Hubungan Wali', 'Ayah / Ibu / Wali', 'Pilih peran wali.'],
            ['Alamat Lengkap', 'Teks Bebas', 'Alamat rumah wali/santri.'],
            ['Asal Sekolah', 'Teks Bebas', 'Sekolah asal sebelum masuk pondok.'],
            ['Ada Saudara di Pondok?', 'Ya / Tidak', 'Opsional. Pilih Ya jika santri memiliki saudara kandung yang juga mondok di Al-Fithroh.'],
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
