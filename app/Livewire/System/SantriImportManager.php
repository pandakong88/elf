<?php

namespace App\Livewire\System;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Livewire\Concerns\SendsToast;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Core\Models\Organization;
use App\Modules\Kepengasuhan\Models\Guardian;
use App\Modules\Kepengasuhan\Models\SantriGuardian;
use App\Modules\Kepengasuhan\Models\SantriProfile;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Kepengasuhan\Services\SiblingService;
use App\Modules\Madrasah\Models\MadrasahKelas;
use App\Modules\Madrasah\Models\MadrasahEnrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SantriImportManager extends Component
{
    use WithFileUploads, SendsToast;

    // Active Tab Navigation
    public string $activeTab = 'santri'; // 'santri', 'asrama', 'kelas'

    // Excel Upload File
    public $excelFile;

    // Santri Setup Modal & State
    public bool $showImportModal = false;
    public array $tempValidSantri = [];
    public array $tempInvalidSantri = [];

    // Asrama Setup Modal & State
    public bool $showAsramaImportModal = false;
    public array $tempValidAsrama = [];
    public array $tempInvalidAsrama = [];

    // Kelas Setup Modal & State
    public bool $showKelasImportModal = false;
    public array $tempValidKelas = [];
    public array $tempInvalidKelas = [];

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || (! $user->hasRole('super-admin') && ! $user->hasRole('manajemen'))) {
            abort(403, 'Anda tidak memiliki wewenang untuk mengakses Halaman Setup Data Master.');
        }
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['santri', 'asrama', 'kelas'])) {
            $this->activeTab = $tab;
        }
    }

    // =========================================================================
    // 1. SETUP SANTRI & WALI (EXCEL IMPORT)
    // =========================================================================

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
                $hasSibRaw   = strtoupper(trim((string)($row[15] ?? '')));
                $hasActiveSibling = in_array($hasSibRaw, ['YA', 'TRUE', '1', 'YES']);

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
                            $errors[] = "Komplek Asrama \"{$dormName}\" tidak ditemukan di sistem. Harap setup di Tab Setup Asrama terlebih dahulu.";
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
                        $errors[] = "Kelas Madrasah \"{$kelasName}\" tidak ditemukan di sistem. Harap setup di Tab Setup Kelas terlebih dahulu.";
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
                        'has_active_sibling' => $hasActiveSibling,
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
            $savedCount    = 0;
            $currentYear   = (int)now()->format('Y');
            $guardianCache = []; // Cache deduplikasi guardian by name+phone

            // Ambil organisasi berdasarkan gender
            $rootOrg  = Organization::where('slug', 'ponpes-al-fithroh')->first() ?? Organization::first();
            $putraOrg = Organization::where('slug', 'kepengasuhan-putra')->first() ?? $rootOrg;
            $putriOrg = Organization::where('slug', 'kepengasuhan-putri')->first() ?? $rootOrg;

            DB::transaction(function () use (&$savedCount, &$guardianCache, $currentYear, $rootOrg, $putraOrg, $putriOrg) {
                foreach ($this->tempValidSantri as $vs) {

                    // 1. Buat data Person (tanpa phone — phone adalah milik wali)
                    $person = Person::create([
                        'id'          => Str::uuid()->toString(),
                        'nik'         => $vs['nik'] ?: null,
                        'name'        => $vs['name'],
                        'gender'      => $vs['gender'],
                        'birth_place' => $vs['birth_place'],
                        'birth_date'  => $vs['birth_date'],
                        'address'     => $vs['address'],
                    ]);

                    // 2. Buat NIS otomatis jika kosong
                    $nisNumber = $vs['nis'];
                    if (empty($nisNumber)) {
                        $nisNumber = $currentYear . sprintf('%04d', rand(1000, 9999));
                    }

                    // 3. Map hubungan wali ke format sistem
                    $relRaw = strtolower(trim($vs['parent_rel'] ?? ''));
                    $relMapped = match(true) {
                        str_contains($relRaw, 'ayah') || $relRaw === 'bapak' || $relRaw === 'father' => 'ayah_kandung',
                        str_contains($relRaw, 'ibu')  || $relRaw === 'mama'  || $relRaw === 'mother' => 'ibu_kandung',
                        str_contains($relRaw, 'kakek')                                               => 'kakek',
                        str_contains($relRaw, 'nenek')                                               => 'nenek',
                        str_contains($relRaw, 'paman') || $relRaw === 'om'                           => 'paman',
                        str_contains($relRaw, 'bibi')  || $relRaw === 'tante'                        => 'bibi',
                        str_contains($relRaw, 'kakak')                                               => 'kakak_kandung',
                        default                                                                      => 'wali_resmi',
                    };

                    // 4. Buat SantriProfile
                    SantriProfile::create([
                        'id'                 => Str::uuid()->toString(),
                        'person_id'          => $person->id,
                        'father_name'        => $relMapped === 'ayah_kandung' ? $vs['parent_name'] : null,
                        'mother_name'        => $relMapped === 'ibu_kandung'  ? $vs['parent_name'] : null,
                        'school_name'        => $vs['school_name'],
                        'has_active_sibling' => $vs['has_active_sibling'] ?? false,
                        'additional_info'    => ['nis' => $nisNumber],
                    ]);

                    // 5. Tentukan organisasi berdasarkan gender santri
                    $orgId = ($vs['gender'] === 'P' ? $putriOrg?->id : $putraOrg?->id) ?? $rootOrg->id;

                    PersonRole::create([
                        'id'                => Str::uuid()->toString(),
                        'person_id'         => $person->id,
                        'organization_id'   => $orgId,
                        'role_type'         => 'santri',
                        'enrollment_status' => 'aktif',
                        'presence_status'   => $vs['presence_status'],
                        'valid_from'        => now()->toDateString(),
                        'is_active'         => true,
                    ]);

                    // 6. Simpan Guardian ke tabel guardians (deduplikasi by nama+HP)
                    if (!empty($vs['parent_name'])) {
                        $cacheKey = strtolower(trim($vs['parent_name'])) . '_' . trim($vs['parent_phone'] ?? '');

                        if (isset($guardianCache[$cacheKey])) {
                            $guardian = $guardianCache[$cacheKey];
                        } else {
                            $guardian = Guardian::firstOrCreate(
                                [
                                    'name'          => $vs['parent_name'],
                                    'phone_primary' => $vs['parent_phone'] ?: null,
                                ],
                                [
                                    'address'   => $vs['address'] ?: null,
                                    'is_active' => true,
                                ]
                            );
                            $guardianCache[$cacheKey] = $guardian;
                        }

                        // 7. Buat relasi santri_guardians
                        SantriGuardian::firstOrCreate(
                            [
                                'person_id'   => $person->id,
                                'guardian_id' => $guardian->id,
                            ],
                            [
                                'relationship' => $relMapped,
                                'is_primary'   => true,
                            ]
                        );
                    }

                    // 8. Penempatan Kamar (jika Mukim)
                    if ($vs['presence_status'] === 'mukim' && $vs['dorm_id'] && $vs['room_name']) {
                        $room = Room::firstOrCreate(
                            [
                                'dormitory_id' => $vs['dorm_id'],
                                'name'         => $vs['room_name'],
                            ],
                            [
                                'capacity'    => 10,
                                'description' => 'Diimpor dari Setup Excel Massal',
                            ]
                        );

                        RoomAssignment::create([
                            'person_id'  => $person->id,
                            'room_id'    => $room->id,
                            'valid_from' => now()->toDateString(),
                            'is_active'  => true,
                        ]);
                    }

                    // 9. Pendaftaran Kelas Madrasah
                    if ($vs['kelas_id']) {
                        MadrasahEnrollment::create([
                            'person_id'     => $person->id,
                            'kelas_id'      => $vs['kelas_id'],
                            'academic_year' => $currentYear . '/' . ($currentYear + 1),
                            'is_active'     => true,
                            'created_by'    => auth()->id(),
                        ]);
                    }

                    $savedCount++;
                }
            });

            // 10. Auto-detect relasi Kakak-Adik dari guardian yang sama
            $detectedSiblings = app(SiblingService::class)->detectSiblingsByGuardian();

            activity('santri')
                ->causedBy(auth()->user())
                ->log("Import massal {$savedCount} santri berhasil. Guardian: " . count($guardianCache) . ". Relasi kakak-adik terdeteksi: {$detectedSiblings}.");

            $siblingMsg = $detectedSiblings > 0 ? " ({$detectedSiblings} relasi kakak-adik terdeteksi otomatis)" : '';
            $this->toastSuccess("Berhasil mengimpor {$savedCount} santri & " . count($guardianCache) . " wali!{$siblingMsg}");
            $this->closeImportModal();

        } catch (\Exception $e) {
            $this->toastError('Gagal menyimpan data setup santri: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // 2. SETUP ASRAMA & KAMAR (EXCEL IMPORT)
    // =========================================================================

    public function openAsramaImportModal(): void
    {
        $this->reset(['excelFile', 'tempValidAsrama', 'tempInvalidAsrama']);
        $this->showAsramaImportModal = true;
    }

    public function closeAsramaImportModal(): void
    {
        $this->reset(['excelFile', 'tempValidAsrama', 'tempInvalidAsrama']);
        $this->showAsramaImportModal = false;
    }

    public function processAsramaImport(): void
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

            array_shift($rows);

            $valid = [];
            $invalid = [];

            foreach ($rows as $index => $row) {
                $rowNum = $index + 2;

                $dormName  = trim((string)($row[0] ?? ''));
                $genderRaw = strtoupper(trim((string)($row[1] ?? '')));
                $roomName  = trim((string)($row[2] ?? ''));
                $capacity  = (int)trim((string)($row[3] ?? 10));

                if (empty($dormName) && empty($roomName)) {
                    continue;
                }

                $errors = [];

                if (empty($dormName)) {
                    $errors[] = 'Nama Komplek Asrama wajib diisi.';
                }

                $gender = null;
                if (in_array($genderRaw, ['L', 'LAKI-LAKI', 'LAKI LAKI', 'PUTRA', 'MALE'])) {
                    $gender = 'L';
                } elseif (in_array($genderRaw, ['P', 'PEREMPUAN', 'PUTRI', 'FEMALE'])) {
                    $gender = 'P';
                } else {
                    $errors[] = 'Gender Komplek harus L (Putra) atau P (Putri).';
                }

                if (empty($roomName)) {
                    $errors[] = 'Nama Kamar wajib diisi.';
                }

                if ($capacity <= 0) {
                    $capacity = 10;
                }

                if (!empty($errors)) {
                    $invalid[] = [
                        'row' => $rowNum,
                        'name' => "{$dormName} - {$roomName}",
                        'reasons' => $errors,
                    ];
                } else {
                    $valid[] = [
                        'row' => $rowNum,
                        'dorm_name' => $dormName,
                        'gender' => $gender,
                        'room_name' => $roomName,
                        'capacity' => $capacity,
                    ];
                }
            }

            $this->tempValidAsrama = $valid;
            $this->tempInvalidAsrama = $invalid;

            if (empty($valid) && empty($invalid)) {
                $this->toastError('Tidak ditemukan data asrama yang dapat diproses dari file Excel.');
            }

        } catch (\Exception $e) {
            $this->toastError('Gagal membaca file Excel Asrama: ' . $e->getMessage());
        }
    }

    public function confirmAndSaveAsramaImport(): void
    {
        if (empty($this->tempValidAsrama)) {
            $this->toastError('Tidak ada data valid asrama yang bisa disimpan.');
            return;
        }

        try {
            $createdCount = 0;
            $rootOrg = Organization::where('slug', 'ponpes-al-fithroh')->first() ?? Organization::first();

            DB::transaction(function () use (&$createdCount, $rootOrg) {
                foreach ($this->tempValidAsrama as $va) {
                    // Find or create Dormitory
                    $dorm = Dormitory::firstOrCreate(
                        ['name' => $va['dorm_name']],
                        [
                            'id'              => Str::uuid()->toString(),
                            'gender'          => $va['gender'],
                            'is_active'       => true,
                            'organization_id' => $rootOrg->id,
                        ]
                    );

                    // Update gender if specified differently
                    if ($dorm->gender !== $va['gender']) {
                        $dorm->update(['gender' => $va['gender']]);
                    }

                    // Find or create Room
                    Room::firstOrCreate(
                        [
                            'dormitory_id' => $dorm->id,
                            'name'         => $va['room_name'],
                        ],
                        [
                            'id'          => Str::uuid()->toString(),
                            'capacity'    => $va['capacity'],
                            'is_active'   => true,
                            'description' => 'Diimpor dari Setup Excel Massal Asrama',
                        ]
                    );

                    $createdCount++;
                }
            });

            activity('asrama')
                ->causedBy(auth()->user())
                ->log("Telah mengimpor massal {$createdCount} kamar/komplek asrama baru.");

            $this->toastSuccess("Berhasil mengimpor & menyiapkan {$createdCount} unit komplek/kamar asrama!");
            $this->closeAsramaImportModal();

        } catch (\Exception $e) {
            $this->toastError('Gagal menyimpan data asrama: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // 3. SETUP KELAS MADRASAH (EXCEL IMPORT)
    // =========================================================================

    public function openKelasImportModal(): void
    {
        $this->reset(['excelFile', 'tempValidKelas', 'tempInvalidKelas']);
        $this->showKelasImportModal = true;
    }

    public function closeKelasImportModal(): void
    {
        $this->reset(['excelFile', 'tempValidKelas', 'tempInvalidKelas']);
        $this->showKelasImportModal = false;
    }

    public function processKelasImport(): void
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

            array_shift($rows);

            $valid = [];
            $invalid = [];

            foreach ($rows as $index => $row) {
                $rowNum = $index + 2;

                $kelasName = trim((string)($row[0] ?? ''));
                $level     = trim((string)($row[1] ?? 'Ula'));
                $genderRaw = strtoupper(trim((string)($row[2] ?? 'Campur')));
                $capacity  = (int)trim((string)($row[3] ?? 40));

                if (empty($kelasName)) {
                    continue;
                }

                $errors = [];

                if (empty($kelasName)) {
                    $errors[] = 'Nama Kelas Madrasah wajib diisi.';
                }

                // Gender validation
                $gender = 'Campur';
                if (in_array($genderRaw, ['L', 'LAKI-LAKI', 'PUTRA'])) {
                    $gender = 'L';
                } elseif (in_array($genderRaw, ['P', 'PEREMPUAN', 'PUTRI'])) {
                    $gender = 'P';
                }

                if ($capacity <= 0) {
                    $capacity = 40;
                }

                if (!empty($errors)) {
                    $invalid[] = [
                        'row' => $rowNum,
                        'name' => $kelasName,
                        'reasons' => $errors,
                    ];
                } else {
                    $valid[] = [
                        'row' => $rowNum,
                        'name' => $kelasName,
                        'level' => $level ?: 'Ula',
                        'gender' => $gender,
                        'capacity' => $capacity,
                    ];
                }
            }

            $this->tempValidKelas = $valid;
            $this->tempInvalidKelas = $invalid;

            if (empty($valid) && empty($invalid)) {
                $this->toastError('Tidak ditemukan data kelas yang dapat diproses dari file Excel.');
            }

        } catch (\Exception $e) {
            $this->toastError('Gagal membaca file Excel Kelas: ' . $e->getMessage());
        }
    }

    public function confirmAndSaveKelasImport(): void
    {
        if (empty($this->tempValidKelas)) {
            $this->toastError('Tidak ada data valid kelas yang bisa disimpan.');
            return;
        }

        try {
            $createdCount = 0;

            DB::transaction(function () use (&$createdCount) {
                $currentYear = (int)now()->format('Y');
                $academicYear = $currentYear . '/' . ($currentYear + 1);

                foreach ($this->tempValidKelas as $vk) {
                    $jenjang = strtolower($vk['level'] ?? 'ula');
                    if (!in_array($jenjang, ['ula', 'wustho', 'ulya'])) {
                        $jenjang = 'ula';
                    }

                    MadrasahKelas::firstOrCreate(
                        ['name' => $vk['name']],
                        [
                            'id'            => Str::uuid()->toString(),
                            'jenjang'       => $jenjang,
                            'academic_year' => $academicYear,
                            'is_active'     => true,
                            'created_by'    => auth()->id(),
                        ]
                    );

                    $createdCount++;
                }
            });

            activity('madrasah')
                ->causedBy(auth()->user())
                ->log("Telah mengimpor massal {$createdCount} kelas madrasah baru.");

            $this->toastSuccess("Berhasil mengimpor & menyiapkan {$createdCount} kelas madrasah!");
            $this->closeKelasImportModal();

        } catch (\Exception $e) {
            $this->toastError('Gagal menyimpan data kelas: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Stats
        $santriCount = Person::whereHas('activeRoles', fn($q) => $q->where('role_type', 'santri'))->count();
        $mukimCount  = PersonRole::where('role_type', 'santri')->where('presence_status', 'mukim')->where('is_active', true)->count();
        $lajuCount   = PersonRole::where('role_type', 'santri')->where('presence_status', 'laju')->where('is_active', true)->count();
        $dormCount   = Dormitory::where('is_active', true)->count();
        $roomCount   = Room::where('is_active', true)->count();
        $kelasCount  = MadrasahKelas::where('is_active', true)->count();

        // Lists
        $recentSantri = Person::whereHas('activeRoles', fn($q) => $q->where('role_type', 'santri'))
            ->with(['activeRoles', 'santriProfile'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $recentDormitories = Dormitory::withCount('rooms')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $recentKelas = MadrasahKelas::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.system.santri-import-manager', [
            'santriCount'       => $santriCount,
            'mukimCount'        => $mukimCount,
            'lajuCount'         => $lajuCount,
            'dormCount'         => $dormCount,
            'roomCount'         => $roomCount,
            'kelasCount'        => $kelasCount,
            'recentSantri'      => $recentSantri,
            'recentDormitories' => $recentDormitories,
            'recentKelas'       => $recentKelas,
        ])->layout('layouts.app');
    }
}
