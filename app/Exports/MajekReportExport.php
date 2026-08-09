<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MajekReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    private int $rowNumber = 0;

    public function __construct(
        private Collection $registrations,
        private string $monthLabel,
        private int $year,
        private array $paidDetails = []
    ) {}

    public function title(): string
    {
        return 'Laporan Majek ' . $this->monthLabel . ' ' . $this->year;
    }

    public function collection(): Collection
    {
        return $this->registrations;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Santri',
            'L/P',
            'Komplek Asrama',
            'Sesi Makan',
            'Hari Aktif',
            'Total Tagihan (Rp)',
            'Uang Masuk (Rp)',
            'Sisa Tagihan (Rp)',
            'Status Pembayaran',
            'Catatan',
        ];
    }

    public function map($reg): array
    {
        $this->rowNumber++;

        $detail  = $this->paidDetails[$reg->id] ?? ['status' => 'unpaid', 'paid' => 0, 'remaining' => (float)$reg->amount_pagi + (float)$reg->amount_sore];
        $statusStr = match ($detail['status']) {
            'paid'    => 'LUNAS',
            'partial' => 'SEBAGIAN (CICILAN)',
            default   => 'BELUM BAYAR',
        };

        $total     = (float)$reg->amount_pagi + (float)$reg->amount_sore;
        $paid      = (float)$detail['paid'];
        $remaining = (float)$detail['remaining'];

        $gender   = $reg->person?->gender === 'P' ? 'Putri' : 'Putra';
        $dormName = $reg->person?->roomAssignments?->first()?->room?->dormitory?->name ?? '—';
        $customDays = $reg->active_days ?? 30;

        $sessionStr = '2x (Pagi & Sore)';
        if ($reg->session_pagi && !$reg->session_sore) {
            $sessionStr = 'Pagi Saja';
        } elseif (!$reg->session_pagi && $reg->session_sore) {
            $sessionStr = 'Sore Saja';
        }

        return [
            $this->rowNumber,
            $reg->person?->name ?? '—',
            $gender,
            $dormName,
            $sessionStr,
            $customDays . ' Hari',
            $total,
            $paid,
            $remaining,
            $statusStr,
            $reg->notes ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->rowNumber + 1; // 1 heading row + total mapped rows

        // Header Styling
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'], // Emerald 600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        if ($lastRow > 1) {
            // Format Currency Columns (G, H, I)
            $sheet->getStyle("G2:I{$lastRow}")
                ->getNumberFormat()
                ->setFormatCode('#,##0');

            // Alignments
            $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C2:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E2:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J2:J{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Grid Borders
            $sheet->getStyle("A1:K{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);
        }

        return [];
    }
}
