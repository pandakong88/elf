<?php

namespace App\Exports;

use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserImportTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new UserDataSheet(),
            new ValidRolesSheet(),
            new InstructionSheet(),
        ];
    }
}

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class UserDataSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Isian Data User';
    }

    public function headings(): array
    {
        return [
            'Nama Lengkap *',
            'Email *',
            'Username',
            'Password *',
            'Role 1 *',
            'Role 2',
        ];
    }

    public function array(): array
    {
        // Provide example rows
        return [
            [
                'Ahmad Musyrif',
                'ahmad.musyrif@ponpes.id',
                'ahmad.musyrif',
                'Password123',
                'musyrif',
                '',
            ],
            [
                'Fatimah Zahra',
                'fatimah.zahra@ponpes.id',
                '',
                'FatimahSecure456',
                'guru',
                'manajemen',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0'); // Light grey

        // Add dropdown validation for roles columns E and F for rows 2 to 100
        $roles = Role::where('guard_name', 'web')->orderBy('name')->pluck('name')->toArray();
        $rolesStr = implode(',', $roles);

        for ($i = 2; $i <= 100; $i++) {
            // Role 1 (Column E)
            $valRole1 = $sheet->getCell('E' . $i)->getDataValidation();
            $valRole1->setType(DataValidation::TYPE_LIST);
            $valRole1->setFormula1('"' . $rolesStr . '"');
            $valRole1->setShowDropDown(true);
            $valRole1->setShowErrorMessage(true);
            $valRole1->setErrorTitle('Peran Tidak Valid');
            $valRole1->setError('Peran harus dipilih dari daftar peran yang terdaftar di sistem.');

            // Role 2 (Column F)
            $valRole2 = $sheet->getCell('F' . $i)->getDataValidation();
            $valRole2->setType(DataValidation::TYPE_LIST);
            $valRole2->setFormula1('"' . $rolesStr . '"');
            $valRole2->setShowDropDown(true);
            $valRole2->setShowErrorMessage(true);
            $valRole2->setErrorTitle('Peran Tidak Valid');
            $valRole2->setError('Peran harus dipilih dari daftar peran yang terdaftar di sistem.');
        }
    }
}

class ValidRolesSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Daftar Role Valid';
    }

    public function headings(): array
    {
        return [
            'Nama Role (Gunakan nama ini)',
            'Deskripsi',
        ];
    }

    public function array(): array
    {
        $roles = Role::where('guard_name', 'web')->orderBy('name')->get();
        $data = [];

        foreach ($roles as $role) {
            $desc = match ($role->name) {
                'super-admin' => 'Akses penuh ke seluruh fitur sistem dan keamanan.',
                'pengasuh' => 'Akses pengawasan tingkat tinggi kepengasuhan.',
                'manajemen' => 'Mengelola status pendaftaran (enrollment) santri.',
                'musyrif' => 'Musyrif pembimbing asrama putra (sensus & perizinan).',
                'musyrifah' => 'Musyrifah pembimbing asrama putri (sensus & perizinan).',
                'guru' => 'Input absensi, nilai, dan raport madrasah.',
                'ketua-madrasah' => 'Mengelola rombel kelas madrasah dan raport.',
                'bendahara-pondok' => 'Mengelola keuangan pusat, iuran, dan tagihan.',
                'bendahara-unit' => 'Mencatat setoran dan pembayaran santri tingkat unit.',
                default => 'Akses peran di dalam sistem ELF.',
            };

            $data[] = [
                $role->name,
                $desc
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);
        $sheet->getStyle('A1:B1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0');
    }
}

class InstructionSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Petunjuk Pengisian';
    }

    public function headings(): array
    {
        return [
            'Kolom',
            'Keharusan',
            'Ketentuan Pengisian',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Nama Lengkap',
                'WAJIB',
                'Tulis nama lengkap user pengurus baru.'
            ],
            [
                'Email',
                'WAJIB',
                'Format email valid dan belum terdaftar di sistem. Digunakan untuk login.'
            ],
            [
                'Username',
                'OPSIONAL',
                'Boleh diisi untuk alternatif login, boleh dikosongkan.'
            ],
            [
                'Password',
                'WAJIB',
                'Password minimal 8 karakter. Sandi ini akan langsung di-hash aman di sistem.'
            ],
            [
                'Role 1',
                'WAJIB',
                'Harus diisi dengan salah satu "Nama Role" yang ada di sheet "Daftar Role Valid" (misal: musyrif).'
            ],
            [
                'Role 2',
                'OPSIONAL',
                'Boleh diisi jika user mengemban peran ganda (misal: guru dan bendahara-unit).'
            ]
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
