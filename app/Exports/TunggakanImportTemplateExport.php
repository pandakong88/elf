<?php

namespace App\Exports;

use App\Modules\Core\Models\Person;
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

class TunggakanImportTemplateExport implements WithMultipleSheets
{
    public function __construct(
        protected array|string|null $dormitoryIds = null,
        protected ?string $kelasId = null,
        protected ?string $gender = null,
        protected ?string $presenceStatus = null,
        protected string $orderBy = 'komplek',
        protected bool $prefill = true,
        protected string $defaultBillType = 'kebersihan',
        protected int $defaultYear = 2025
    ) {}

    public function sheets(): array
    {
        return [
            new TunggakanDataSheet(
                $this->dormitoryIds,
                $this->kelasId,
                $this->gender,
                $this->presenceStatus,
                $this->orderBy,
                $this->prefill,
                $this->defaultBillType,
                $this->defaultYear
            ),
            new TunggakanSantriReferenceSheet(
                $this->dormitoryIds,
                $this->kelasId,
                $this->gender,
                $this->presenceStatus,
                $this->orderBy
            ),
            new TunggakanInstructionSheet(),
        ];
    }
}

class TunggakanDataSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function __construct(
        protected array|string|null $dormitoryIds = null,
        protected ?string $kelasId = null,
        protected ?string $gender = null,
        protected ?string $presenceStatus = null,
        protected string $orderBy = 'komplek',
        protected bool $prefill = true,
        protected string $defaultBillType = 'kebersihan',
        protected int $defaultYear = 2025
    ) {}

    public function title(): string
    {
        return 'Isian Data Tunggakan';
    }

    public function headings(): array
    {
        return [
            'NIS Santri (Wajib) *',
            'Nama Santri (Referensi)',
            'Jenis Tagihan (Pilih) *',
            'Tahun Periode (YYYY) *',
            'Bulan / Semester (1-12/Kosong)',
            'Nominal Tunggakan (Rp) *',
            'Keterangan / Catatan Bukti',
        ];
    }

    public function array(): array
    {
        if (!$this->prefill) {
            return [
                [
                    '2024001',
                    'Ahmad Fauzi',
                    $this->defaultBillType,
                    (string)$this->defaultYear,
                    '',
                    '14000',
                    'Tunggakan Kas Sampah 7 bln (Jun-Des ' . $this->defaultYear . ')',
                ],
            ];
        }

        $query = Person::whereHas('activeRoles', function ($q) {
            $q->where('role_type', 'santri')
              ->where('enrollment_status', 'aktif');

            if (!empty($this->presenceStatus)) {
                if ($this->presenceStatus === 'mukim') {
                    $q->where(function ($sq) {
                        $sq->where('presence_status', 'mukim')->orWhereNull('presence_status');
                    });
                } else {
                    $q->where('presence_status', $this->presenceStatus);
                }
            }
        })
        ->with([
            'activeRoomAssignment.room.dormitory',
            'activeMadrasahEnrollment.kelas',
            'activeRoles'
        ]);

        if (!empty($this->gender)) {
            $query->where('gender', $this->gender);
        }

        if (!empty($this->dormitoryIds)) {
            $dormIds = is_array($this->dormitoryIds) ? array_filter($this->dormitoryIds) : [$this->dormitoryIds];
            if (!empty($dormIds)) {
                $query->whereHas('activeRoomAssignment.room', function ($q) use ($dormIds) {
                    $q->whereIn('dormitory_id', $dormIds);
                });
            }
        }

        if (!empty($this->kelasId)) {
            $query->whereHas('activeMadrasahEnrollment', function ($q) {
                $q->where('kelas_id', $this->kelasId);
            });
        }

        $santriList = $query->get();

        // Terapkan Pengurutan Berdasarkan Pilihan Pengurus (Komplek/Kamar/Kelas/Nama/NIS)
        $santriList = match ($this->orderBy) {
            'komplek' => $santriList->sortBy([
                fn ($a, $b) => strcmp($a->activeRoomAssignment?->room?->dormitory?->name ?? 'ZZZ', $b->activeRoomAssignment?->room?->dormitory?->name ?? 'ZZZ'),
                fn ($a, $b) => strcmp($a->activeRoomAssignment?->room?->name ?? 'ZZZ', $b->activeRoomAssignment?->room?->name ?? 'ZZZ'),
                fn ($a, $b) => strcmp($a->name, $b->name),
            ]),
            'kamar' => $santriList->sortBy([
                fn ($a, $b) => strcmp($a->activeRoomAssignment?->room?->name ?? 'ZZZ', $b->activeRoomAssignment?->room?->name ?? 'ZZZ'),
                fn ($a, $b) => strcmp($a->name, $b->name),
            ]),
            'kelas' => $santriList->sortBy([
                fn ($a, $b) => strcmp($a->activeMadrasahEnrollment?->kelas?->name ?? 'ZZZ', $b->activeMadrasahEnrollment?->kelas?->name ?? 'ZZZ'),
                fn ($a, $b) => strcmp($a->name, $b->name),
            ]),
            'nis' => $santriList->sortBy(fn ($s) => $s->nis ?? $s->nik ?? $s->name),
            default => $santriList->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE),
        };

        $rows = [];
        $noteLabel = match($this->defaultBillType) {
            'kebersihan'        => 'Tunggakan Kas Sampah / Kebersihan s/d ' . $this->defaultYear,
            'syahriah_pondok'   => 'Tunggakan Syahriah Pondok s/d ' . $this->defaultYear,
            'syahriah_madrasah' => 'Tunggakan Syahriah Madrasah s/d ' . $this->defaultYear,
            'kas_komplek'       => 'Tunggakan Kas Komplek s/d ' . $this->defaultYear,
            default             => 'Tunggakan saldo awal s/d ' . $this->defaultYear,
        };

        foreach ($santriList as $santri) {
            $rows[] = [
                $santri->nis ?? $santri->nik ?? (string)$santri->id,
                $santri->name,
                $this->defaultBillType,
                (string)$this->defaultYear,
                '',
                '', // Biarkan kosong agar diisi pengurus jika nunggak
                $noteLabel,
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = max($sheet->getHighestRow(), 500);

        // Header styling
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0');

        // Enable worksheet protection so locked cells cannot be modified
        $sheet->getProtection()->setPassword('');
        $sheet->getProtection()->setSheet(true);
        $sheet->getProtection()->setSort(true);
        $sheet->getProtection()->setAutoFilter(true);
        $sheet->getProtection()->setFormatCells(true);
        $sheet->getProtection()->setSelectLockedCells(true);
        $sheet->getProtection()->setSelectUnlockedCells(true);

        if ($this->prefill) {
            // Beri warna latar belakang abu-abu sangat muda pada NIS & Nama Santri untuk menandakan kolom referensi terkunci
            $sheet->getStyle('A2:B' . $highestRow)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFF8FAFC');

            // Kunci kolom A (NIS) dan B (Nama), dan buka kolom C s/d G agar dapat diisi oleh pengurus
            $sheet->getStyle('A2:B' . $highestRow)->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED);
            $sheet->getStyle('C2:G' . $highestRow)->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
        } else {
            // Jika template kosong tanpa prefill, buka seluruh kolom A s/d G agar pengurus bisa mengetik NIS sendiri
            $sheet->getStyle('A2:G' . $highestRow)->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
        }

        // Dropdown for Jenis Tagihan (Column C)
        $billTypes = 'kebersihan,syahriah_pondok,syahriah_madrasah,kas_komplek,lainnya';

        for ($i = 2; $i <= min($highestRow, 500); $i++) {
            $valType = $sheet->getCell('C' . $i)->getDataValidation();
            $valType->setType(DataValidation::TYPE_LIST);
            $valType->setFormula1('"' . $billTypes . '"');
            $valType->setShowDropDown(true);
            $valType->setShowInputMessage(true);
            $valType->setPromptTitle('Jenis Tagihan');
            $valType->setPrompt('Pilih jenis tagihan dari dropdown list.');
        }
    }
}

class TunggakanSantriReferenceSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function __construct(
        protected array|string|null $dormitoryIds = null,
        protected ?string $kelasId = null,
        protected ?string $gender = null,
        protected ?string $presenceStatus = null,
        protected string $orderBy = 'komplek'
    ) {}

    public function title(): string
    {
        return 'Referensi Santri & NIS';
    }

    public function headings(): array
    {
        return [
            'NIS (Nomor Induk)',
            'Nama Lengkap Santri',
            'Gender (L/P)',
            'Status Keberadaan',
            'Komplek Asrama',
            'Kamar Asrama',
            'Kelas Madrasah',
        ];
    }

    public function array(): array
    {
        $query = Person::whereHas('activeRoles', function ($q) {
            $q->where('role_type', 'santri')
              ->where('enrollment_status', 'aktif');

            if (!empty($this->presenceStatus)) {
                if ($this->presenceStatus === 'mukim') {
                    $q->where(function ($sq) {
                        $sq->where('presence_status', 'mukim')->orWhereNull('presence_status');
                    });
                } else {
                    $q->where('presence_status', $this->presenceStatus);
                }
            }
        })
        ->with([
            'activeRoomAssignment.room.dormitory',
            'activeMadrasahEnrollment.kelas',
            'activeRoles'
        ]);

        if (!empty($this->gender)) {
            $query->where('gender', $this->gender);
        }

        if (!empty($this->dormitoryIds)) {
            $dormIds = is_array($this->dormitoryIds) ? array_filter($this->dormitoryIds) : [$this->dormitoryIds];
            if (!empty($dormIds)) {
                $query->whereHas('activeRoomAssignment.room', function ($q) use ($dormIds) {
                    $q->whereIn('dormitory_id', $dormIds);
                });
            }
        }

        if (!empty($this->kelasId)) {
            $query->whereHas('activeMadrasahEnrollment', function ($q) {
                $q->where('kelas_id', $this->kelasId);
            });
        }

        $santriList = $query->get();

        // Terapkan Pengurutan yang Sama
        $santriList = match ($this->orderBy) {
            'komplek' => $santriList->sortBy([
                fn ($a, $b) => strcmp($a->activeRoomAssignment?->room?->dormitory?->name ?? 'ZZZ', $b->activeRoomAssignment?->room?->dormitory?->name ?? 'ZZZ'),
                fn ($a, $b) => strcmp($a->activeRoomAssignment?->room?->name ?? 'ZZZ', $b->activeRoomAssignment?->room?->name ?? 'ZZZ'),
                fn ($a, $b) => strcmp($a->name, $b->name),
            ]),
            'kamar' => $santriList->sortBy([
                fn ($a, $b) => strcmp($a->activeRoomAssignment?->room?->name ?? 'ZZZ', $b->activeRoomAssignment?->room?->name ?? 'ZZZ'),
                fn ($a, $b) => strcmp($a->name, $b->name),
            ]),
            'kelas' => $santriList->sortBy([
                fn ($a, $b) => strcmp($a->activeMadrasahEnrollment?->kelas?->name ?? 'ZZZ', $b->activeMadrasahEnrollment?->kelas?->name ?? 'ZZZ'),
                fn ($a, $b) => strcmp($a->name, $b->name),
            ]),
            'nis' => $santriList->sortBy(fn ($s) => $s->nis ?? $s->nik ?? $s->name),
            default => $santriList->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE),
        };

        $rows = [];
        foreach ($santriList as $santri) {
            $role = $santri->activeRoles->firstWhere('role_type', 'santri');
            $dorm = $santri->activeRoomAssignment?->room?->dormitory?->name ?? '-';
            $room = $santri->activeRoomAssignment?->room?->name ?? '-';
            $kelas = $santri->activeMadrasahEnrollment?->kelas?->name ?? '-';

            $rows[] = [
                $santri->nis ?? $santri->nik ?? (string)$santri->id,
                $santri->name,
                $santri->gender ?? '-',
                ucfirst($role?->presence_status ?? 'mukim'),
                $dorm,
                $room,
                $kelas,
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0');

        $sheet->getProtection()->setPassword('');
        $sheet->getProtection()->setSheet(true);
        $sheet->getProtection()->setSort(true);
        $sheet->getProtection()->setAutoFilter(true);
        $sheet->getProtection()->setSelectLockedCells(true);
        $sheet->getProtection()->setSelectUnlockedCells(true);
    }
}

class TunggakanInstructionSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Petunjuk Pengisian';
    }

    public function headings(): array
    {
        return ['Nama Kolom', 'Format / Tipe', 'Keterangan & Aturan'];
    }

    public function array(): array
    {
        return [
            ['NIS Santri *', 'Teks / Angka', 'Wajib diisi. Harus sesuai dengan NIS santri di sheet Referensi Santri & NIS.'],
            ['Nama Santri', 'Teks Bebas', 'Opsional / Sebagai catatan pembantu. Sistem akan memvalidasi berdasarkan NIS.'],
            ['Jenis Tagihan *', 'Pilih dari Dropdown List', 'Pilihan: kebersihan, syahriah_pondok, syahriah_madrasah, kas_komplek, lainnya.'],
            ['Tahun Periode *', 'Angka 4 Digit (YYYY)', 'Wajib diisi tahun tagihan asal (contoh: 2025, 2024).'],
            ['Bulan / Semester', 'Angka 1-12 atau KOSONG', 'Isi angka 1-12 jika tagihan spesifik 1 bulan. KOSONGKAN jika berupa akumulasi saldo awal tahunan.'],
            ['Nominal Tunggakan *', 'Angka Murni (Tanpa titik/Rp)', 'Isi nominal bagi santri yang nunggak. Santri yang TIDAK nunggak / sudah lunas cukup KOSONGKAN kolom ini.'],
            ['Keterangan / Catatan', 'Teks Bebas', 'Disarankan diisi rincian bulan/alasan untuk transparansi kepada wali santri.'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0');

        $sheet->getProtection()->setPassword('');
        $sheet->getProtection()->setSheet(true);
        $sheet->getProtection()->setSort(true);
        $sheet->getProtection()->setAutoFilter(true);
        $sheet->getProtection()->setSelectLockedCells(true);
        $sheet->getProtection()->setSelectUnlockedCells(true);
    }
}