<?php

namespace App\Livewire\System;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Livewire\Concerns\SendsToast;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Kepengasuhan\Models\SantriProfile;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Madrasah\Models\MadrasahKelas;
use App\Modules\Madrasah\Models\MadrasahEnrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SantriImportManager extends Component
{
    use WithFileUploads, SendsToast;

    public $excelFile;
    public bool $showImportModal = false;
    public array $tempValidSantri = [];
    public array $tempInvalidSantri = [];

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || (! $user->hasRole('super-admin') && ! $user->hasRole('manajemen'))) {
            abort(403, 'Anda tidak memiliki wewenang untuk mengakses Halaman Setup Data Santri.');
        }
    }

    public function openImportModal(): void
    {
        $this->reset(['excelFile', 'tempValidSantri', 'tempInvalidSantri']);
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->reset(['excelFile', 'tempValidSantri', 'tempInvalidSantri']);
        $this->showImportModal = false;
    }

    public function processImport(): void
    {
        $this->validate([
            'excelFile' => 'required|mimes:xlsx,xls|max:10240',
        ], [
            'excelFile.required' => 'File Excel wajib dipilih.',
            'excelFile.mimes' => 'Format file harus berupa Excel (.xlsx atau .xls).',
            'excelFile.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        try {
            $path = $this->excelFile->getRealPath();
            $spreadsheet = IOFactory::load($path);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (count($rows) <= 1) {
                $this->toastError('File Excel kosong atau tidak memiliki baris data.');
                return;
            }

            // Remove header row
            array_shift($rows);

            $valid = [];
            $invalid = [];

            $dormitories = Dormitory::where('is_active', true)->get()->keyBy(fn($d) => strtolower(trim($d->name)));
            $kelasList = MadrasahKelas::where('is_active', true)->get()->keyBy(fn($k) => strtolower(trim($k->name)));

            foreach ($rows as $index => $row) {
                $rowNum = $index + 2;

                $name        = trim((string)($row[0] ?? ''));
                $nik         = trim((string)($row[1] ?? ''));
                $nis         = trim((string)($row[2] ?? ''));
                $genderRaw   = strtoupper(trim((string)($row[3] ?? '')));
                $birthPlace  = trim((string)($row[4] ?? ''));
                $birthDate   = trim((string)($row[5] ?? ''));
                $statusRaw   = strtolower(trim((string)($row[6] ?? '')));
                $dormName    = trim((string)($row[7] ?? ''));
                $roomName    = trim((string)($row[8] ?? ''));
                $kelasName   = trim((string)($row[9] ?? ''));
                $parentName  = trim((string)($row[10] ?? ''));
                $parentPhone = trim((string)($row[11] ?? ''));
                $parentRel   = trim((string)($row[12] ?? 'Ayah'));
                $address     = trim((string)($row[13] ?? ''));
                $schoolName  = trim((string)($row[14] ?? ''));

                // Ignore empty rows
                if (empty($name) && empty($nik) && empty($genderRaw)) {
                    continue;
                }

                $errors = [];

                if (empty($name)) {
                    $errors[] = 'Nama Lengkap Santri wajib diisi.';
                }

                // Gender validation
                $gender = null;
                if (in_array($genderRaw, ['L', 'LAKI-LAKI', 'LAKI LAKI', 'PUTRA', 'MALE'])) {
                    $gender = 'L';
                } elseif (in_array($genderRaw, ['P', 'PEREMPUAN', 'PUTRI', 'FEMALE'])) {
                    $gender = 'P';
                } else {
                    $errors[] = 'Jenis Kelamin harus L (Putra) atau P (Putri).';
                }

                // Presence status validation
                $presenceStatus = null;
                if (in_array($statusRaw, ['mukim', 'tinggal', 'pondok', 'asrama'])) {
                    $presenceStatus = 'mukim';
                } elseif (in_array($statusRaw, ['laju', 'pulang', 'pulang pergi', 'non-mukim'])) {
                    $presenceStatus = 'laju';
                } else {
                    $errors[] = 'Status Santri harus "Mukim" atau "Laju".';
                }

                // Dormitory validation if Mukim
                $matchedDorm = null;
                if ($presenceStatus === 'mukim') {
                    if (empty($dormName)) {
                        $errors[] = 'Santri Mukim wajib mengisi nama Komplek Asrama.';
                    } else {
                        $dormKey = strtolower($dormName);
                        if (!isset($dormitories[$dormKey])) {
                            $errors[] = "Komplek Asrama \"{$dormName}\" tidak ditemukan di sistem.";
                        } else {
                            $matchedDorm = $dormitories[$dormKey];
                            if ($gender && $matchedDorm->gender !== $gender) {
                                $errors[] = "Gender Santri ({$gender}) tidak sesuai dengan Gender Komplek \"{$dormName}\" ({$matchedDorm->gender}).";
                            }
                        }
                    }

                    if (empty($roomName)) {
                        $errors[] = 'Santri Mukim wajib mengisi nama Kamar.';
                    }
                }

                // Kelas validation
                $matchedKelas = null;
                if (!empty($kelasName)) {
                    $kelasKey = strtolower($kelasName);
                    if (isset($kelasList[$kelasKey])) {
                        $matchedKelas = $kelasList[$kelasKey];
                    } else {
                        $errors[] = "Kelas Madrasah \"{$kelasName}\" tidak ditemukan di sistem.";
                    }
                }

                // Parent validation
                if (empty($parentName)) {
                    $errors[] = 'Nama Orang Tua / Wali wajib diisi.';
                }
                if (empty($parentPhone)) {
                    $errors[] = 'No. HP / WA Wali wajib diisi.';
                }

                if (!empty($errors)) {
                    $invalid[] = [
                        'row' => $rowNum,
                        'name' => $name ?: 'Tanpa Nama',
                        'reasons' => $errors,
                    ];
                } else {
                    $valid[] = [
                        'row' => $rowNum,
                        'name' => $name,
                        'nik' => $nik ?: null,
                        'nis' => $nis ?: null,
                        'gender' => $gender,
                        'birth_place' => $birthPlace ?: null,
                        'birth_date' => !empty($birthDate) && strtotime($birthDate) ? date('Y-m-d', strtotime($birthDate)) : null,
                        'presence_status' => $presenceStatus,
                        'dorm_id' => $matchedDorm?->id,
                        'dorm_name' => $matchedDorm?->name,
                        'room_name' => $roomName,
                        'kelas_id' => $matchedKelas?->id,
                        'kelas_name' => $matchedKelas?->name,
                        'parent_name' => $parentName,
                        'parent_phone' => $parentPhone,
                        'parent_rel' => $parentRel ?: 'Ayah',
                        'address' => $address ?: null,
                        'school_name' => $schoolName ?: null,
                    ];
                }
            }

            $this->tempValidSantri = $valid;
            $this->tempInvalidSantri = $invalid;

            if (empty($valid) && empty($invalid)) {
                $this->toastError('Tidak ditemukan data santri yang dapat diproses dari file Excel.');
            }

        } catch (\Exception $e) {
            $this->toastError('Gagal membaca file Excel: ' . $e->getMessage());
        }
    }

    public function confirmAndSaveImport(): void
    {
        if (empty($this->tempValidSantri)) {
            $this->toastError('Tidak ada data valid yang bisa disimpan.');
            return;
        }

        try {
            $savedCount = 0;
            $currentYear = (int)now()->format('Y');

            DB::transaction(function () use (&$savedCount, $currentYear) {
                foreach ($this->tempValidSantri as $vs) {
                    // 1. Create or Update Person
                    $person = Person::create([
                        'id'          => Str::uuid()->toString(),
                        'nik'         => $vs['nik'] ?: null,
                        'name'        => $vs['name'],
                        'gender'      => $vs['gender'],
                        'birth_place' => $vs['birth_place'],
                        'birth_date'  => $vs['birth_date'],
                        'phone'       => $vs['parent_phone'],
                        'address'     => $vs['address'],
                    ]);

                    // Auto-generate NIS if empty
                    $nisNumber = $vs['nis'];
                    if (empty($nisNumber)) {
                        $nisNumber = $currentYear . sprintf('%04d', rand(1000, 9999));
                    }

                    // 2. Create SantriProfile
                    SantriProfile::create([
                        'id'              => Str::uuid()->toString(),
                        'person_id'       => $person->id,
                        'father_name'     => strtolower($vs['parent_rel']) === 'ayah' ? $vs['parent_name'] : null,
                        'mother_name'     => strtolower($vs['parent_rel']) === 'ibu' ? $vs['parent_name'] : null,
                        'school_name'     => $vs['school_name'],
                        'additional_info' => ['nis' => $nisNumber],
                    ]);

                    // 3. Create PersonRole (Santri)
                    $rootOrg = \App\Modules\Core\Models\Organization::where('slug', 'ponpes-al-fithroh')->first()
                        ?? \App\Modules\Core\Models\Organization::first();

                    PersonRole::create([
                        'id'              => Str::uuid()->toString(),
                        'person_id'       => $person->id,
                        'organization_id' => $rootOrg->id,
                        'role_type'       => 'santri',
                        'enrollment_status' => 'aktif',
                        'presence_status' => $vs['presence_status'],
                        'valid_from'      => now()->toDateString(),
                        'is_active'       => true,
                    ]);

                    // 4. If Mukim, assign to Room
                    if ($vs['presence_status'] === 'mukim' && $vs['dorm_id'] && $vs['room_name']) {
                        $room = Room::firstOrCreate(
                            [
                                'dormitory_id' => $vs['dorm_id'],
                                'name'         => $vs['room_name'],
                            ],
                            [
                                'id'          => Str::uuid()->toString(),
                                'capacity'    => 10,
                                'description' => 'Diimpor dari Setup Excel Massal',
                            ]
                        );

                        RoomAssignment::create([
                            'id'         => Str::uuid()->toString(),
                            'person_id'  => $person->id,
                            'room_id'    => $room->id,
                            'valid_from' => now()->toDateString(),
                            'is_active'  => true,
                        ]);
                    }

                    // 5. If Kelas specified, enroll in MadrasahKelas
                    if ($vs['kelas_id']) {
                        MadrasahEnrollment::create([
                            'id'         => Str::uuid()->toString(),
                            'person_id'  => $person->id,
                            'kelas_id'   => $vs['kelas_id'],
                            'year'       => $currentYear,
                            'is_active'  => true,
                        ]);
                    }

                    $savedCount++;
                }
            });

            activity('santri')
                ->causedBy(auth()->user())
                ->log("Telah melakukan setup masal data santri baru. Berhasil diimpor: {$savedCount} santri.");

            $this->toastSuccess("Berhasil mengimpor dan melakukan setup {$savedCount} data santri baru!");
            $this->closeImportModal();

        } catch (\Exception $e) {
            $this->toastError('Gagal menyimpan data setup santri: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $santriCount = Person::whereHas('activeRoles', fn($q) => $q->where('role_type', 'santri'))->count();
        $mukimCount  = PersonRole::where('role_type', 'santri')->where('presence_status', 'mukim')->where('is_active', true)->count();
        $lajuCount   = PersonRole::where('role_type', 'santri')->where('presence_status', 'laju')->where('is_active', true)->count();

        $recentSantri = Person::whereHas('activeRoles', fn($q) => $q->where('role_type', 'santri'))
            ->with(['activeRoles', 'santriProfile'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.system.santri-import-manager', [
            'santriCount'  => $santriCount,
            'mukimCount'   => $mukimCount,
            'lajuCount'    => $lajuCount,
            'recentSantri' => $recentSantri,
        ])->layout('layouts.app');
    }
}
