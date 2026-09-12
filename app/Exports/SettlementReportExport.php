<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SettlementReportExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(
        protected array $reportData,
        protected array $allSantriList,
        protected array $checklist = [],
        protected string $appName = 'Pondok Pesantren Al-Fithroh'
    ) {}

    public function sheets(): array
    {
        return [
            new SettlementSummarySheet($this->reportData, $this->checklist, $this->appName),
            new SettlementSantriDetailSheet($this->allSantriList),
        ];
    }
}

class SettlementSummarySheet implements FromArray, WithTitle, ShouldAutoSize, WithStyles
{
    public function __construct(
        protected array $report,
        protected array $checklist,
        protected string $appName
    ) {}

    public function title(): string
    {
        return 'Ringkasan & Alokasi Kas';
    }

    public function array(): array
    {
        $rows = [];

        // Title
        $rows[] = [strtoupper($this->appName)];
        $rows[] = ['LAPORAN REKONSILIASI & TUTUP BUKU KAS (SETTLEMENT)'];
        $rows[] = ['Periode: ' . ($this->report['period_label'] ?? '-') . ' | Dicetak: ' . now()->locale('id')->translatedFormat('d F Y, H:i') . ' WIB'];
        $rows[] = ['']; // Blank row

        // Section 1: KPI Arus Kas
        $rows[] = ['1. RINGKASAN ARUS KAS MASUK (3 SALURAN)'];
        $rows[] = ['Saluran Pembayaran', 'Jumlah Transaksi', 'Kotor (Gross)', 'Potongan MDR', 'Uang Bersih (Net)'];
        $rows[] = [
            'Payment Gateway Online (DOKU)',
            ($this->report['gateway_trx'] ?? 0) . ' Trx',
            (float)($this->report['gateway_gross'] ?? 0),
            (float)($this->report['gateway_mdr'] ?? 0),
            (float)($this->report['gateway_net'] ?? 0),
        ];
        $rows[] = [
            'Transfer Bank Manual (BSI/BRI)',
            ($this->report['transfer_trx'] ?? 0) . ' Trx',
            (float)($this->report['transfer_amount'] ?? 0),
            0,
            (float)($this->report['transfer_amount'] ?? 0),
        ];
        $rows[] = [
            'Setoran Fisik Tunai Kasir / Meja',
            ($this->report['cash_trx'] ?? 0) . ' Trx',
            (float)($this->report['cash_amount'] ?? 0),
            0,
            (float)($this->report['cash_amount'] ?? 0),
        ];
        $rows[] = [
            'TOTAL UANG MASUK BERSIH (KLOP)',
            ($this->report['total_trx'] ?? 0) . ' Trx',
            (float)($this->report['total_gross'] ?? 0),
            (float)($this->report['total_mdr'] ?? 0),
            (float)($this->report['total_net'] ?? 0),
        ];
        $rows[] = ['']; // Blank row

        // Section 2: Alokasi Pos Anggaran
        $rows[] = ['2. LEMBAR ALOKASI & DISTRIBUSI PERUNTUKAN POS ANGGARAN'];
        $rows[] = ['No', 'Pos Anggaran', 'Jumlah Tagihan/Santri', 'Porsi (%)', 'Nominal Alokasi (Rp)', 'Status Penyerahan', 'Catatan Penerima'];

        $categories = $this->report['category_breakdown'] ?? [];
        $totalNet = (float)($this->report['total_net'] ?? 1);

        foreach ($categories as $idx => $cat) {
            $catKey = $cat['key'] ?? '';
            $chk = $this->checklist[$catKey] ?? null;
            $status = !empty($chk['handed_over']) ? 'SUDAH DISERAHKAN' : 'BELUM DISERAHKAN';
            $note = $chk['recipient_note'] ?? '-';
            $percent = $totalNet > 0 ? round(($cat['amount'] / $totalNet) * 100, 1) : 0;

            $rows[] = [
                $idx + 1,
                $cat['label'] ?? '-',
                ($cat['count'] ?? 0) . ' item',
                $percent . '%',
                (float)($cat['amount'] ?? 0),
                $status,
                $note,
            ];
        }
        $rows[] = [
            '',
            'TOTAL SELURUH POS ANGGARAN',
            collect($categories)->sum('count') . ' item',
            '100%',
            (float)$totalNet,
            '',
            '',
        ];
        $rows[] = ['']; // Blank row

        // Section 3: Kas Komplek Asrama
        $rows[] = ['3. RINCIAN ALOKASI KAS KOMPLEK ASRAMA'];
        $rows[] = ['No', 'Nama Komplek Asrama', 'Unit', 'Jumlah Santri', 'Total Kas Terkumpul (Rp)', 'Status Serah Terima'];

        $dormitories = $this->report['dormitory_breakdown'] ?? [];
        foreach ($dormitories as $idx => $dorm) {
            $dormKey = 'dorm_' . ($dorm['dormitory_id'] ?? '');
            $chk = $this->checklist[$dormKey] ?? null;
            $status = !empty($chk['handed_over']) ? 'SUDAH DISERAHKAN' : 'BELUM DISERAHKAN';

            $rows[] = [
                $idx + 1,
                $dorm['dormitory_name'] ?? '-',
                ($dorm['gender'] ?? '') === 'P' ? 'Putri' : 'Putra',
                ($dorm['count_santri'] ?? 0) . ' Santri',
                (float)($dorm['total_amount'] ?? 0),
                $status,
            ];
        }
        $rows[] = [
            '',
            'TOTAL KAS SELURUH KOMPLEK',
            '',
            collect($dormitories)->sum('count_santri') . ' Santri',
            (float)collect($dormitories)->sum('total_amount'),
            '',
        ];

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true, 'size' => 11]],
            3 => ['font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '64748B']]],
            5 => ['font' => ['bold' => true, 'color' => ['rgb' => '0369A1']]],
            6 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']]],
            10 => ['font' => ['bold' => true]],
            12 => ['font' => ['bold' => true, 'color' => ['rgb' => '0369A1']]],
            13 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']]],
        ];
    }
}

class SettlementSantriDetailSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    private int $rowNumber = 0;

    public function __construct(
        protected array $santriList
    ) {}

    public function title(): string
    {
        return 'Rincian Santri Pembayar';
    }

    public function collection()
    {
        return collect($this->santriList);
    }

    public function headings(): array
    {
        return [
            'No',
            'NIS Santri',
            'Nama Lengkap Santri',
            'Gender / Unit',
            'Kamar / Asrama',
            'Pos Anggaran / Kategori',
            'Rincian Periode Tagihan',
            'Waktu Pembayaran',
            'Saluran / Metode Bayar',
            'Nominal Dibayar (Rp)',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $row['nis'] ?? '-',
            $row['name'] ?? '-',
            ($row['gender'] ?? '') === 'P' ? 'Putri' : (($row['gender'] ?? '') === 'L' ? 'Putra' : '-'),
            $row['unit_info'] ?? $row['room_name'] ?? '-',
            $row['category_label'] ?? $row['period_label'] ?? '-',
            $row['period_label'] ?? '-',
            $row['paid_date'] ?? '-',
            $row['method'] ?? 'Online',
            (float)($row['amount'] ?? 0),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0284C7'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}
