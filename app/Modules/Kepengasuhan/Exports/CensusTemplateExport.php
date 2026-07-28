<?php

namespace App\Modules\Kepengasuhan\Exports;

use App\Modules\Kepengasuhan\Models\DormitoryCensus;
use App\Modules\Kepengasuhan\Models\RoomCensusDetail;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Kepengasuhan\Models\SantriProfile;
use App\Modules\Kepengasuhan\Models\Guardian;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class CensusTemplateExport implements WithMultipleSheets
{
    protected $dormitoryCensus;
    protected $santris;

    public function __construct(DormitoryCensus $dormitoryCensus)
    {
        $this->dormitoryCensus = $dormitoryCensus;

        // Ambil semua santri aktif di asrama ini
        $this->santris = RoomAssignment::active()
            ->join('rooms', 'room_assignments.room_id', '=', 'rooms.id')
            ->where('rooms.dormitory_id', $dormitoryCensus->dormitory_id)
            ->with(['person.santriProfile', 'room'])
            ->select('room_assignments.*')
            ->get()
            ->map(function ($assignment) {
                return [
                    'person' => $assignment->person,
                    'room' => $assignment->room,
                ];
            });
    }

    public function sheets(): array
    {
        return [
            new SensusSheet($this->dormitoryCensus, $this->santris),
            new ProfilSheet($this->santris),
            new WaliSheet($this->santris),
            new SaudaraSheet($this->santris),
            new PanduanSheet(),
        ];
    }
}

// =========================================================================
// Sensus Sheet
// =========================================================================
class SensusSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $census;
    protected $santris;

    public function __construct($census, $santris)
    {
        $this->census = $census;
        $this->santris = $santris;
    }

    public function title(): string
    {
        return 'Sensus Kehadiran';
    }

    public function headings(): array
    {
        return [
            'ID Santri (Jangan Diubah)',
            'Nama Lengkap',
            'NIK/NIS',
            'Komplek',
            'Kamar',
            'Status Kehadiran',
            'Catatan Sensus',
        ];
    }

    public function array(): array
    {
        $data = [];
        $existingDetails = RoomCensusDetail::where('dormitory_census_id', $this->census->id)
            ->pluck('status', 'person_id')
            ->toArray();
        $existingNotes = RoomCensusDetail::where('dormitory_census_id', $this->census->id)
            ->pluck('notes', 'person_id')
            ->toArray();

        foreach ($this->santris as $s) {
            $person = $s['person'];
            $room = $s['room'];

            $status = $existingDetails[$person->id] ?? 'Hadir';
            $statusLabel = match ($status) {
                'present' => 'Hadir',
                'sick' => 'Sakit',
                'leave' => 'Izin Pulang',
                'absent' => 'Alpa/Kabur',
                'moved' => 'Pindah Kamar',
                default => $status,
            };

            $data[] = [
                $person->id,
                $person->name,
                $person->nik ?? $person->nis ?? '-',
                $this->census->dormitory->name,
                $room->name,
                $statusLabel,
                $existingNotes[$person->id] ?? '',
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        // Bold header
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Add validations to Status column (F)
        $rowCount = count($this->santris) + 1;
        for ($i = 2; $i <= $rowCount; $i++) {
            $validation = $sheet->getCell('F' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Input Error');
            $validation->setError('Pilih status dari daftar yang tersedia.');
            $validation->setPromptTitle('Status Kehadiran');
            $validation->setPrompt('Pilih salah satu: Hadir, Sakit, Izin Pulang, Alpa/Kabur, Pindah Kamar');
            $validation->setFormula1('"Hadir,Sakit,Izin Pulang,Alpa/Kabur,Pindah Kamar"');
        }

        // Hide ID Santri column for aesthetic reasons
        $sheet->getColumnDimension('A')->setVisible(false);
    }
}

// =========================================================================
// Profil Sheet
// =========================================================================
class ProfilSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $santris;

    public function __construct($santris)
    {
        $this->santris = $santris;
    }

    public function title(): string
    {
        return 'Profil Santri';
    }

    public function headings(): array
    {
        return [
            'ID Santri (Jangan Diubah)',
            'Nama Santri',
            'Gol. Darah',
            'Riwayat Penyakit',
            'Alergi',
            'Kondisi Khusus',
            'Status Pendidikan',
            'Nama Sekolah/Kampus',
            'Tipe Sekolah (SD/SMP/SMA/S1)',
            'Jurusan',
            'Kelas/Semester',
        ];
    }

    public function array(): array
    {
        $data = [];
        foreach ($this->santris as $s) {
            $person = $s['person'];
            $profile = $person->santriProfile;

            $data[] = [
                $person->id,
                $person->name,
                $profile->blood_type ?? '',
                $profile->medical_history ?? '',
                $profile->allergies ?? '',
                $profile->special_conditions ?? '',
                $profile->school_status ?? 'mondok_full',
                $profile->school_name ?? '',
                $profile->school_type ?? '',
                $profile->major ?? '',
                $profile->school_year ?? '',
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        $rowCount = count($this->santris) + 1;
        for ($i = 2; $i <= $rowCount; $i++) {
            // Blood Type Validation
            $valBlood = $sheet->getCell('C' . $i)->getDataValidation();
            $valBlood->setType(DataValidation::TYPE_LIST);
            $valBlood->setFormula1('"A,B,AB,O,A+,B+,AB+,O+,A-,B-,AB-,O-"');

            // School Status Validation
            $valSchool = $sheet->getCell('G' . $i)->getDataValidation();
            $valSchool->setType(DataValidation::TYPE_LIST);
            $valSchool->setFormula1('"mondok_full,sekolah_luar,kuliah,tidak_sekolah"');
        }

        $sheet->getColumnDimension('A')->setVisible(false);
    }
}

// =========================================================================
// Wali Sheet
// =========================================================================
class WaliSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $santris;

    public function __construct($santris)
    {
        $this->santris = $santris;
    }

    public function title(): string
    {
        return 'Data Wali';
    }

    public function headings(): array
    {
        return [
            'ID Santri (Jangan Diubah)',
            'Nama Santri',
            'Nama Ayah Kandung',
            'HP Ayah',
            'Pekerjaan Ayah',
            'Nama Ibu Kandung',
            'HP Ibu',
            'Nama Wali Lain (jika ada)',
            'Hubungan Wali Lain',
            'HP Wali Lain',
            'Alamat Lengkap Wali',
            'Kota/Kabupaten Wali',
        ];
    }

    public function array(): array
    {
        $data = [];
        foreach ($this->santris as $s) {
            $person = $s['person'];
            $profile = $person->santriProfile;

            // Cari wali lainnya
            $otherGuardian = null;
            if ($profile) {
                $guardians = $profile->guardians;
                foreach ($guardians as $g) {
                    if (!in_array($g->pivot->relationship, ['ayah_kandung', 'ibu_kandung'])) {
                        $otherGuardian = $g;
                        break;
                    }
                }
            }

            $data[] = [
                $person->id,
                $person->name,
                $profile->father_name ?? '',
                $profile->father_phone ?? '', // HP Ayah
                $profile->father_occupation ?? '',
                $profile->mother_name ?? '',
                $profile->mother_phone ?? '', // HP Ibu
                $otherGuardian ? $otherGuardian->name : '',
                $otherGuardian ? $otherGuardian->pivot->relationship : '',
                $otherGuardian ? $otherGuardian->phone_primary : '',
                $otherGuardian ? $otherGuardian->address : ($profile->address ?? ''),
                $otherGuardian ? $otherGuardian->city : ($profile->city ?? ''),
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:L1')->getFont()->setBold(true);
        $sheet->getStyle('A1:L1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        $rowCount = count($this->santris) + 1;
        for ($i = 2; $i <= $rowCount; $i++) {
            $valRelation = $sheet->getCell('I' . $i)->getDataValidation();
            $valRelation->setType(DataValidation::TYPE_LIST);
            $valRelation->setFormula1('"wali_resmi,kakek,nenek,paman,bibi,kakak_kandung,lainnya"');
        }

        $sheet->getColumnDimension('A')->setVisible(false);
    }
}

// =========================================================================
// Saudara Sheet
// =========================================================================
class SaudaraSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $santris;

    public function __construct($santris)
    {
        $this->santris = $santris;
    }

    public function title(): string
    {
        return 'Saudara Kandung';
    }

    public function headings(): array
    {
        return [
            'ID Santri (Jangan Diubah)',
            'Nama Santri',
            'Ada Saudara di Pondok? (Ya/Tidak)',
            'Nama Lengkap Saudara di Pondok',
            'Status Hubungan',
            'NIK/NIS Saudara (jika tahu)',
        ];
    }

    public function array(): array
    {
        $data = [];
        foreach ($this->santris as $s) {
            $person = $s['person'];
            $profile = $person->santriProfile;
            $hasSiblingLabel = ($profile && $profile->has_active_sibling) ? 'Ya' : 'Tidak';

            $data[] = [
                $person->id,
                $person->name,
                $hasSiblingLabel,
                '', // Diisi manual oleh user
                '', // Kakak/Adik/Kembar
                '', // NIK/NIS
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        $rowCount = count($this->santris) + 1;
        for ($i = 2; $i <= $rowCount; $i++) {
            // Validation for Ada Saudara (Column C)
            $valSibling = $sheet->getCell('C' . $i)->getDataValidation();
            $valSibling->setType(DataValidation::TYPE_LIST);
            $valSibling->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $valSibling->setAllowBlank(false);
            $valSibling->setShowDropDown(true);
            $valSibling->setFormula1('"Ya,Tidak"');

            // Validation for Status Hubungan (Column E)
            $valRel = $sheet->getCell('E' . $i)->getDataValidation();
            $valRel->setType(DataValidation::TYPE_LIST);
            $valRel->setFormula1('"kakak,adik,kembar"');
        }

        $sheet->getColumnDimension('A')->setVisible(false);
    }
}

// =========================================================================
// Panduan Sheet
// =========================================================================
class PanduanSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Panduan Pengisian';
    }

    public function headings(): array
    {
        return [
            'Judul Panduan',
            'Deskripsi Ketentuan',
        ];
    }

    public function array(): array
    {
        return [
            ['[PENTING] Kolom ID Santri', 'Kolom ID Santri (Kolom A) sengaja disembunyikan. JANGAN MENGHAPUS, MENGUBAH, atau MEMINDAHKAN kolom ini. Kolom ini digunakan sistem untuk mencocokkan data santri.'],
            ['[Sheet 1] Sensus Kehadiran', 'Gunakan kolom "Status Kehadiran" untuk memperbarui status santri. Pilihan yang valid adalah: Hadir, Sakit, Izin Pulang, Alpa/Kabur, Pindah Kamar. JANGAN mengetik selain opsi tersebut.'],
            ['[Sheet 2] Profil Santri', 'Isi data medis (golongan darah, penyakit, alergi) dan status sekolah santri saat ini. Status Pendidikan yang valid: mondok_full, sekolah_luar, kuliah, tidak_sekolah.'],
            ['[Sheet 3] Data Wali', 'Masukkan nama dan kontak Ayah / Ibu kandung. Jika ada wali lain, isi di bagian "Wali Lain" dan pilih hubungannya (wali_resmi, kakek, nenek, paman, bibi, kakak_kandung, lainnya). No HP harus diawali dengan angka 08 / +62.'],
            ['[Sheet 4] Saudara Kandung', 'Jika santri memiliki saudara kandung (kakak/adik) yang juga mondok di Pesantren Al-Fithroh, isi baris santri tersebut dengan nama lengkap saudara, status hubungan (kakak, adik, kembar), dan NIK/NIS saudara jika tahu. Ini berguna untuk pengajuan diskon syahriah saudara.'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);
        $sheet->getStyle('A1:B1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');
    }
}
