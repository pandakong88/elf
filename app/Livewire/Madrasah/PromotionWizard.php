<?php

namespace App\Livewire\Madrasah;

use App\Livewire\Concerns\SendsToast;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Madrasah\Models\MadrasahEnrollment;
use App\Modules\Madrasah\Models\MadrasahKelas;
use App\Traits\HasGenderScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class PromotionWizard extends Component
{
    use SendsToast, HasGenderScope;

    public string $fromAcademicYear = '';
    public string $toAcademicYear   = '';
    public string $genderFilter     = '';
    public bool   $isGenderLocked   = false;

    // Promotion Mapping Data
    public array $promotionMap = [];
    public bool  $isLoaded     = false;

    // Undo / Rollback State
    public ?array $lastPromotionBatch   = null;
    public bool   $showUndoConfirmModal = false;

    public function mount(): void
    {
        $currentYear = (int) now()->format('Y');
        $this->fromAcademicYear = ($currentYear - 1) . '/' . $currentYear;
        $this->toAcademicYear   = $currentYear . '/' . ($currentYear + 1);

        $scope = $this->genderScope();
        if ($scope) {
            $this->genderFilter   = $scope;
            $this->isGenderLocked = true;
        }

        // Restore last promotion batch from session if exists
        $this->lastPromotionBatch = session()->get('last_promotion_batch', null);

        $this->loadPromotionData();
    }

    public function getSummaryStatsProperty(): array
    {
        $total     = 0;
        $promoted  = 0;
        $retained  = 0;
        $graduated = 0;

        foreach ($this->promotionMap as $classData) {
            foreach ($classData['students'] as $st) {
                $total++;
                if ($st['status'] === 'promoted') {
                    $promoted++;
                } elseif ($st['status'] === 'retained') {
                    $retained++;
                } elseif ($st['status'] === 'graduated') {
                    $graduated++;
                }
            }
        }

        return [
            'total'     => $total,
            'promoted'  => $promoted,
            'retained'  => $retained,
            'graduated' => $graduated,
            'classes'   => count($this->promotionMap),
        ];
    }

    public function loadPromotionData(): void
    {
        $allClasses = MadrasahKelas::where('is_active', true)
            ->orderBy('jenjang')
            ->orderBy('name')
            ->get();

        $this->promotionMap = [];

        foreach ($allClasses as $kelas) {
            // Fetch active enrollments for this class in fromAcademicYear
            $enrollments = MadrasahEnrollment::with(['person.santriProfile'])
                ->where('kelas_id', $kelas->id)
                ->where('is_active', true)
                ->when($this->genderFilter, fn($q) => $q->whereHas('person', fn($pq) => $pq->where('gender', $this->genderFilter)))
                ->get();

            if ($enrollments->isEmpty()) {
                continue;
            }

            // Determine default target class based on rules:
            $targetClassId = $this->determineDefaultTargetClass($kelas, $allClasses);

            $students = [];
            foreach ($enrollments as $enr) {
                $p = $enr->person;
                if (!$p) continue;

                $defaultStatus = ($targetClassId === 'lulus') ? 'graduated' : 'promoted';

                $students[] = [
                    'person_id' => $p->id,
                    'name'      => $p->name,
                    'gender'    => $p->gender,
                    'nis'       => $p->santriProfile->nis ?? '-',
                    'status'    => $defaultStatus,
                ];
            }

            $this->promotionMap[$kelas->id] = [
                'class_id'        => $kelas->id,
                'class_name'      => $kelas->name,
                'jenjang'         => strtoupper($kelas->jenjang),
                'target_class_id' => $targetClassId,
                'students'        => $students,
            ];
        }

        $this->isLoaded = true;
    }

    private function determineDefaultTargetClass(MadrasahKelas $currentClass, $allClasses): string
    {
        $cName = strtolower($currentClass->name);

        // Check Graduation Rules:
        if (str_contains($cName, 'ulya 1') && str_contains($cName, 'putri')) {
            return 'lulus';
        }
        if (str_contains($cName, 'ulya 2')) {
            return 'lulus';
        }

        $nextClass = null;

        if (str_contains($cName, 'awaliyah 1')) {
            $targetName = str_replace('awaliyah 1', 'awaliyah 2', $cName);
            $nextClass = $allClasses->first(fn($c) => strtolower($c->name) === $targetName);
        } elseif (str_contains($cName, 'awaliyah 2')) {
            $targetName = str_replace('awaliyah 2', 'awaliyah 3', $cName);
            $nextClass = $allClasses->first(fn($c) => strtolower($c->name) === $targetName);
        } elseif (str_contains($cName, 'awaliyah 3')) {
            $targetName = str_contains($cName, 'putri') ? 'wustho 1 putri' : 'wustho 1 putra';
            $nextClass = $allClasses->first(fn($c) => str_contains(strtolower($c->name), $targetName));
        } elseif (str_contains($cName, 'wustho 1')) {
            $targetName = str_replace('wustho 1', 'wustho 2', $cName);
            $nextClass = $allClasses->first(fn($c) => strtolower($c->name) === $targetName);
        } elseif (str_contains($cName, 'wustho 2')) {
            $targetName = str_contains($cName, 'putri') ? 'ulya 1 putri' : 'ulya 1 putra';
            $nextClass = $allClasses->first(fn($c) => str_contains(strtolower($c->name), $targetName));
        } elseif (str_contains($cName, 'ulya 1') && str_contains($cName, 'putra')) {
            $nextClass = $allClasses->first(fn($c) => str_contains(strtolower($c->name), 'ulya 2 putra'));
        }

        return $nextClass ? $nextClass->id : 'lulus';
    }

    public function updateTargetClass(string $classId, string $targetClassId): void
    {
        if (isset($this->promotionMap[$classId])) {
            $this->promotionMap[$classId]['target_class_id'] = $targetClassId;

            $isGraduation = ($targetClassId === 'lulus');
            foreach ($this->promotionMap[$classId]['students'] as &$student) {
                if ($student['status'] !== 'retained') {
                    $student['status'] = $isGraduation ? 'graduated' : 'promoted';
                }
            }
        }
    }

    public function toggleStudentStatus(string $classId, string $personId, string $newStatus): void
    {
        if (isset($this->promotionMap[$classId])) {
            foreach ($this->promotionMap[$classId]['students'] as &$student) {
                if ($student['person_id'] === $personId) {
                    $student['status'] = $newStatus;
                    break;
                }
            }
        }
    }

    public function setAllStudentsStatusInClass(string $classId, string $status): void
    {
        if (isset($this->promotionMap[$classId])) {
            foreach ($this->promotionMap[$classId]['students'] as &$student) {
                $student['status'] = $status;
            }
        }
    }

    // Custom Confirmation Dialog Properties
    public bool   $showConfirmModal     = false;
    public string $confirmTitle         = '';
    public string $confirmMessage       = '';
    public string $confirmAction        = '';
    public string $confirmButtonText    = 'Ya, Lanjutkan';
    public string $confirmButtonColor   = 'emerald';

    public function requestMassPromotionConfirm(): void
    {
        if (empty($this->promotionMap)) {
            $this->toastError('Tidak ada data kenaikan kelas untuk diproses.');
            return;
        }

        $totalSantri = 0;
        foreach ($this->promotionMap as $cData) {
            $totalSantri += count($cData['students']);
        }

        $this->confirmAction      = 'executeMassPromotion';
        $this->confirmTitle       = 'Konfirmasi Kenaikan & Kelulusan Massal';
        $this->confirmMessage     = "Apakah Anda YAKIN ingin memproses kenaikan kelas & kelulusan serentak untuk {$totalSantri} santri dari Tahun Ajaran {$this->fromAcademicYear} ke Tahun Ajaran {$this->toAcademicYear}?";
        $this->confirmButtonText  = 'Ya, Eksekusi Massal';
        $this->confirmButtonColor = 'emerald';
        $this->showConfirmModal   = true;
    }

    public function processConfirmedAction(): void
    {
        $this->showConfirmModal = false;

        if ($this->confirmAction === 'executeMassPromotion') {
            $this->executeMassPromotion();
        }
    }

    public function executeMassPromotion(): void
    {
        if (empty($this->promotionMap)) {
            $this->toastError('Tidak ada data kenaikan kelas untuk diproses.');
            return;
        }

        $totalPromoted  = 0;
        $totalRetained  = 0;
        $totalGraduated = 0;
        $affectedPersons = [];

        DB::transaction(function () use (&$totalPromoted, &$totalRetained, &$totalGraduated, &$affectedPersons) {
            foreach ($this->promotionMap as $classData) {
                $sourceClassId  = $classData['class_id'];
                $targetClassId  = $classData['target_class_id'];

                foreach ($classData['students'] as $st) {
                    $personId = $st['person_id'];
                    $status   = $st['status'];
                    $affectedPersons[] = $personId;

                    // 1. Deactivate old enrollment for this academic year
                    MadrasahEnrollment::where('person_id', $personId)
                        ->where('kelas_id', $sourceClassId)
                        ->where('is_active', true)
                        ->update([
                            'is_active' => false,
                        ]);

                    if ($status === 'promoted' && $targetClassId !== 'lulus') {
                        // Create/Update enrollment in target class for TO academic year
                        MadrasahEnrollment::updateOrCreate(
                            [
                                'person_id'     => $personId,
                                'kelas_id'      => $targetClassId,
                                'academic_year' => $this->toAcademicYear,
                            ],
                            [
                                'is_active'  => true,
                                'created_by' => auth()->id(),
                            ]
                        );
                        $totalPromoted++;
                    } elseif ($status === 'retained') {
                        // Retained: enroll again in SAME class for TO academic year
                        MadrasahEnrollment::updateOrCreate(
                            [
                                'person_id'     => $personId,
                                'kelas_id'      => $sourceClassId,
                                'academic_year' => $this->toAcademicYear,
                            ],
                            [
                                'is_active'  => true,
                                'created_by' => auth()->id(),
                            ]
                        );
                        $totalRetained++;
                    } elseif ($status === 'graduated' || $targetClassId === 'lulus') {
                        // Graduated: Update PersonRole enrollment_status to 'alumni'
                        PersonRole::where('person_id', $personId)
                            ->where('role_type', 'santri')
                            ->update([
                                'enrollment_status' => 'alumni',
                                'is_active'         => false,
                                'presence_status'   => null,
                                'left_at'           => now()->toDateString(),
                            ]);

                        $totalGraduated++;
                    }
                }
            }
        });

        // Save batch audit details for UNDO action
        $batchData = [
            'batch_id'           => Str::uuid()->toString(),
            'from_academic_year' => $this->fromAcademicYear,
            'to_academic_year'   => $this->toAcademicYear,
            'executed_at'        => now()->format('d M Y H:i:s'),
            'total_students'     => count(array_unique($affectedPersons)),
            'total_promoted'     => $totalPromoted,
            'total_retained'     => $totalRetained,
            'total_graduated'    => $totalGraduated,
            'executed_by'        => auth()->user()->name ?? 'Admin',
            'status'             => 'sukses',
            'person_ids'         => array_unique($affectedPersons),
        ];

        $this->lastPromotionBatch = $batchData;
        session()->put('last_promotion_batch', $batchData);

        // Push to overall promotion batch history session
        $batchHistory = session()->get('promotion_batch_history', []);
        array_unshift($batchHistory, $batchData);
        session()->put('promotion_batch_history', array_slice($batchHistory, 0, 20));

        $this->toastSuccess("Proses Massal Selesai: {$totalPromoted} Naik, {$totalRetained} Tinggal, {$totalGraduated} Lulus.");
        $this->loadPromotionData();
    }

    // =========================================================================
    // Log & Audit Trail Properties & Methods
    // =========================================================================

    public bool   $showLogModal = false;
    public string $logFilter    = 'all'; // 'all', 'promoted', 'retained', 'graduated', 'batches'
    public string $logSearch    = '';

    public function openLogModal(): void
    {
        $this->showLogModal = true;
    }

    public function closeLogModal(): void
    {
        $this->showLogModal = false;
    }

    public function getPromotionLogsProperty(): array
    {
        $logs = [];

        $enrollments = MadrasahEnrollment::with(['person.santriProfile', 'kelas'])
            ->orderBy('updated_at', 'desc')
            ->limit(150)
            ->get();

        foreach ($enrollments as $en) {
            if (!$en->person) continue;

            $person = $en->person;

            $otherEnrollments = MadrasahEnrollment::where('person_id', $person->id)
                ->where('id', '!=', $en->id)
                ->with('kelas')
                ->get();

            $status = 'promoted';
            $detailMessage = "Terdokumentasi di kelas {$en->kelas->name} ({$en->academic_year})";

            $isAlumni = $person->roles()->where('role_type', 'santri')->where('enrollment_status', 'alumni')->exists();

            if ($isAlumni && !$en->is_active) {
                $status = 'graduated';
                $detailMessage = "Lulus Madrasah / Alumni pada TA {$en->academic_year}";
            } else {
                $retainedMatch = $otherEnrollments->first(function($oe) use ($en) {
                    return $oe->kelas_id === $en->kelas_id && $oe->academic_year !== $en->academic_year;
                });

                if ($retainedMatch) {
                    $status = 'retained';
                    $detailMessage = "Mengulang di kelas {$en->kelas->name} ({$en->academic_year})";
                } elseif ($en->is_active) {
                    $status = 'promoted';
                    $detailMessage = "Aktif di kelas {$en->kelas->name} ({$en->academic_year})";
                }
            }

            if ($this->logSearch) {
                $kw = strtolower(trim($this->logSearch));
                $matchName  = str_contains(strtolower($person->name), $kw);
                $matchNis   = str_contains(strtolower($person->santriProfile->additional_info['nis'] ?? ''), $kw);
                $matchKelas = str_contains(strtolower($en->kelas->name ?? ''), $kw);
                if (!$matchName && !$matchNis && !$matchKelas) {
                    continue;
                }
            }

            if ($this->logFilter !== 'all' && $this->logFilter !== 'batches' && $this->logFilter !== $status) {
                continue;
            }

            $logs[] = [
                'id'            => $en->id,
                'person_name'   => $person->name,
                'nis'           => $person->santriProfile->additional_info['nis'] ?? '-',
                'gender'        => $person->gender,
                'kelas_name'    => $en->kelas->name ?? '-',
                'jenjang'       => strtoupper($en->kelas->jenjang ?? '-'),
                'academic_year' => $en->academic_year,
                'status'        => $status,
                'detail'        => $detailMessage,
                'date'          => $en->updated_at ? $en->updated_at->format('d M Y H:i') : '-',
                'is_active'     => $en->is_active,
            ];
        }

        return $logs;
    }

    public function getBatchHistoryProperty(): array
    {
        return session()->get('promotion_batch_history', []);
    }

    // =========================================================================
    // UNDO / ROLLBACK FEATURE
    // =========================================================================

    public function openUndoConfirmModal(): void
    {
        if (!$this->lastPromotionBatch) {
            $this->toastError('Tidak ada riwayat kenaikan kelas yang dapat dibatalkan.');
            return;
        }
        $this->showUndoConfirmModal = true;
    }

    public function executeUndoMassPromotion(): void
    {
        if (!$this->lastPromotionBatch) {
            $this->toastError('Tidak ada riwayat kenaikan kelas yang dapat dibatalkan.');
            return;
        }

        $personIds        = $this->lastPromotionBatch['person_ids'] ?? [];
        $fromAcademicYear = $this->lastPromotionBatch['from_academic_year'];
        $toAcademicYear   = $this->lastPromotionBatch['to_academic_year'];

        DB::transaction(function () use ($personIds, $fromAcademicYear, $toAcademicYear) {
            // 1. Deactivate all new enrollments created for TO academic year
            MadrasahEnrollment::whereIn('person_id', $personIds)
                ->where('academic_year', $toAcademicYear)
                ->update(['is_active' => false]);

            // 2. Reactivate previous enrollments in FROM academic year
            MadrasahEnrollment::whereIn('person_id', $personIds)
                ->where('academic_year', $fromAcademicYear)
                ->update(['is_active' => true]);

            // 3. Revert PersonRole status back to 'aktif'
            PersonRole::whereIn('person_id', $personIds)
                ->where('role_type', 'santri')
                ->update([
                    'enrollment_status' => 'aktif',
                    'is_active'         => true,
                    'left_at'           => null,
                ]);
        });

        // Mark last batch as di_undo in session history
        $batchHistory = session()->get('promotion_batch_history', []);
        if (!empty($batchHistory)) {
            $batchHistory[0]['status'] = 'di_undo';
            session()->put('promotion_batch_history', $batchHistory);
        }

        $this->toastSuccess('Kenaikan kelas massal BERHASIL DIBATALKAN. Seluruh data santri dikembalikan ke posisi semula.');
        $this->lastPromotionBatch   = null;
        $this->showUndoConfirmModal = false;
        session()->forget('last_promotion_batch');

        $this->loadPromotionData();
    }

    public function render()
    {
        $allClasses = MadrasahKelas::where('is_active', true)->orderBy('jenjang')->orderBy('name')->get();

        return view('livewire.madrasah.promotion-wizard', [
            'allClasses' => $allClasses,
        ])->layout('layouts.app');
    }
}
