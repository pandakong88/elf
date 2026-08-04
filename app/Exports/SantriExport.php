<?php

namespace App\Exports;

use App\Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SantriExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    public function __construct(
        private Collection $santriCollection
    ) {}

    public function title(): string
    {
        return 'Isian Data Santri & Wali';
    }

    public function collection(): Collection
    {
        return $this->santriCollection;
    }

    public function headings(): array
    {
        return [
            'Nama Lengkap Santri *',
            'NIK',
            'NIS',
            'Jenis Kelamin (L/P) *',
            'Tempat Lahir',
            'Tanggal Lahir (YYYY-MM-DD)',
            'Status Santri (Mukim/Laju)',
            'Komplek Asrama (Jika Mukim)',
            'Nama Kamar (Jika Mukim)',
            'Kelas Madrasah',
            'Nama Orang Tua / Wali *',
            'No HP / WA Wali *',
            'Hubungan Wali (Ayah/Ibu/Wali)',
            'Alamat Lengkap',
            'Sekolah Formal',
            'Kakak Adik di Pondok (Ya/Tidak)',
        ];
    }

    public function map($santri): array
    {
        $role       = $santri->roles?->firstWhere('role_type', 'santri') ?? $santri->roles?->first();
        $assignment = $santri->roomAssignments?->firstWhere('is_active', true) ?? $santri->roomAssignments?->first();
        $enrollment = $santri->madrasahEnrollments?->firstWhere('is_active', true) ?? $santri->madrasahEnrollments?->first();
        $prof       = $santri->santriProfile;

        // Presence Status (Mukim / Laju)
        $presenceStatus = match (strtolower($role?->presence_status ?? '')) {
            'mukim' => 'Mukim',
            'laju'  => 'Laju',
            default => $role?->presence_status ? ucfirst($role->presence_status) : 'Mukim',
        };

        // Gender (L / P)
        $gender = $santri->gender === 'L' ? 'L' : ($santri->gender === 'P' ? 'P' : '-');

        // Tanggal Lahir (YYYY-MM-DD)
        $birthDate = $santri->birth_date ? (is_string($santri->birth_date) ? date('Y-m-d', strtotime($santri->birth_date)) : $santri->birth_date->format('Y-m-d')) : '';

        // Nama & No HP Wali
        $parentName  = $prof?->father_name ?: ($prof?->mother_name ?: ($prof?->getAdditional('guardian_name') ?? ''));
        $parentPhone = $prof?->father_phone ?: ($prof?->mother_phone ?: ($prof?->getAdditional('guardian_phone') ?? ''));
        $parentRel   = $prof?->father_name ? 'Ayah' : ($prof?->mother_name ? 'Ibu' : ($prof?->getAdditional('guardian_relationship') ?: 'Wali'));

        // Ada Saudara di Pondok?
        $hasSibling = $prof?->has_active_sibling ? 'Ya' : 'Tidak';

        return [
            $santri->name ?? '',
            $santri->nik ? (string)$santri->nik : '',
            $prof?->nis ? (string)$prof->nis : '',
            $gender,
            $santri->birth_place ?? $prof?->birth_city ?? '',
            $birthDate,
            $presenceStatus,
            $assignment?->room?->dormitory?->name ?? '',
            $assignment?->room?->name ?? '',
            $enrollment?->kelas?->name ?? '',
            $parentName ?: '',
            $parentPhone ?: '',
            $parentRel ?: '',
            $santri->address ?? '',
            $prof?->school_name ?? '',
            $hasSibling,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Style header row (Row 1): Dark Navy Blue (#0F294A), Bold White Font
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F294A']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }
}
