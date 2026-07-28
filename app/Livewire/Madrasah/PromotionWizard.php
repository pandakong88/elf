<?php

namespace App\Livewire\Madrasah;

use App\Livewire\Concerns\SendsToast;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\PersonRole;
use App\Modules\Madrasah\Models\MadrasahEnrollment;
use App\Modules\Madrasah\Models\MadrasahKelas;
use App\Modules\Madrasah\Models\MadrasahPromotionBatch;
use App\Modules\Madrasah\Models\MadrasahPromotionBatchItem;
use App\Traits\HasGenderScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
    public ?MadrasahPromotionBatch $lastPromotionBatch   = null;
    public bool                    $showUndoConfirmModal = false;

    // Password Gate Confirmation State
    public string $confirmPassword = '';

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

        // Load latest active promotion batch from Database
        $this->lastPromotionBatch = MadrasahPromotionBatch::where('status', 'sukses')
            ->orderBy('executed_at', 'desc')
            ->first();

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
        if (!auth()->user()->can('manage-kelas') && !auth()->user()->can('execute-kenaikan-kelas')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk mengelola kenaikan kelas.');
            return;
        }

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
        if (!auth()->user()->can('manage-kelas') && !auth()->user()->can('execute-kenaikan-kelas')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk mengelola kenaikan kelas.');
            return;
        }

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
        if (!auth()->user()->can('manage-kelas') && !auth()->user()->can('execute-kenaikan-kelas')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk mengelola kenaikan kelas.');
            return;
        }

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
        if (!auth()->user()->can('execute-kenaikan-kelas')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk mengeksekusi kenaikan kelas massal.');
            return;
        }

        // Academic Year Lock Check
        $alreadyProcessed = MadrasahPromotionBatch::where('from_academic_year', $this->fromAcademicYear)
            ->where('status', 'sukses')
            ->exists();

        if ($alreadyProcessed) {
            $this->toastError("Tahun Ajaran {$this->fromAcademicYear} sudah pernah diproses Kenaikan Kelas. Batalkan (Undo) batch sebelumnya terlebih dahulu jika ingin memproses ulang.");
            return;
        }

        if (empty($this->promotionMap)) {
            $this->toastError('Tidak ada data kenaikan kelas untuk diproses.');
            return;
        }

        $totalSantri = 0;
        foreach ($this->promotionMap as $cData) {
            $totalSantri += count($cData['students']);
        }

        $this->confirmPassword    = '';
        $this->resetErrorBag();
        $this->confirmAction      = 'executeMassPromotion';
        $this->confirmTitle       = 'Konfirmasi Kenaikan & Kelulusan Massal';
        $this->confirmMessage     = "Apakah Anda YAKIN ingin memproses kenaikan kelas & kelulusan serentak untuk {$totalSantri} santri dari Tahun Ajaran {$this->fromAcademicYear} ke Tahun Ajaran {$this->toAcademicYear}?";
        $this->confirmButtonText  = 'Ya, Eksekusi Massal';
        $this->confirmButtonColor = 'emerald';
        $this->showConfirmModal   = true;
    }

    public function processConfirmedAction(): void
    {
        if ($this->confirmAction === 'executeMassPromotion') {
            $this->executeMassPromotion();
        }
    }

    public function executeMassPromotion(): void
    {
        if (!auth()->user()->can('execute-kenaikan-kelas')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk mengeksekusi kenaikan kelas massal.');
            return;
        }

        if (empty($this->confirmPassword) || !Hash::check($this->confirmPassword, auth()->user()->password)) {
            $this->addError('confirmPassword', 'Password konfirmasi yang Anda masukkan salah.');
            $this->toastError('Konfirmasi Password Salah. Eksekusi Kenaikan Kelas Dibatalkan.');
            return;
        }

        if (empty($this->promotionMap)) {
            $this->toastError('Tidak ada data kenaikan kelas untuk diproses.');
            return;
        }

        $totalPromoted   = 0;
        $totalRetained   = 0;
        $totalGraduated  = 0;
        $affectedPersons = [];

        DB::transaction(function () use (&$totalPromoted, &$totalRetained, &$totalGraduated, &$affectedPersons) {
            $batch = MadrasahPromotionBatch::create([
                'id'                 => Str::uuid()->toString(),
                'from_academic_year' => $this->fromAcademicYear,
                'to_academic_year'   => $this->toAcademicYear,
                'executed_at'        => now(),
                'executed_by'        => auth()->id(),
                'executed_by_name'   => auth()->user()->name ?? 'Admin',
                'status'             => 'sukses',
            ]);

            foreach ($this->promotionMap as $classData) {
                $sourceClassId = $classData['class_id'];
                $targetClassId = $classData['target_class_id'];

                foreach ($classData['students'] as $st) {
                    $personId = $st['person_id'];
                    $status   = $st['status'];
                    $affectedPersons[] = $personId;

                    // Fetch previous active enrollment
                    $oldEnrollment = MadrasahEnrollment::where('person_id', $personId)
                        ->where('kelas_id', $sourceClassId)
                        ->where('is_active', true)
                        ->first();

                    if ($oldEnrollment) {
                        $oldEnrollment->update(['is_active' => false]);
                    }

                    $newEnrollmentId = null;
                    $prevRoleStatus  = null;

                    if ($status === 'promoted' && $targetClassId !== 'lulus') {
                        $newEnr = MadrasahEnrollment::create([
                            'id'            => Str::uuid()->toString(),
                            'person_id'     => $personId,
                            'kelas_id'      => $targetClassId,
                            'academic_year' => $this->toAcademicYear,
                            'is_active'     => true,
                            'created_by'    => auth()->id(),
                        ]);
                        $newEnrollmentId = $newEnr->id;
                        $totalPromoted++;
                    } elseif ($status === 'retained') {
                        $newEnr = MadrasahEnrollment::create([
                            'id'            => Str::uuid()->toString(),
                            'person_id'     => $personId,
                            'kelas_id'      => $sourceClassId,
                            'academic_year' => $this->toAcademicYear,
                            'is_active'     => true,
                            'created_by'    => auth()->id(),
                        ]);
                        $newEnrollmentId = $newEnr->id;
                        $totalRetained++;
                    } elseif ($status === 'graduated' || $targetClassId === 'lulus') {
                        $personRole = PersonRole::where('person_id', $personId)
                            ->where('role_type', 'santri')
                            ->first();

                        if ($personRole) {
                            $prevRoleStatus = $personRole->enrollment_status;
                            $personRole->update([
                                'enrollment_status' => 'alumni',
                                'is_active'         => false,
                                'presence_status'   => null,
                                'left_at'           => now()->toDateString(),
                            ]);
                        }
                        $totalGraduated++;
                    }

                    // Save Batch Item in Database
                    MadrasahPromotionBatchItem::create([
                        'id'                          => Str::uuid()->toString(),
                        'batch_id'                    => $batch->id,
                        'person_id'                   => $personId,
                        'source_kelas_id'             => $sourceClassId,
                        'target_kelas_id'             => $targetClassId,
                        'status'                      => $status,
                        'previous_enrollment_id'      => $oldEnrollment?->id,
                        'new_enrollment_id'           => $newEnrollmentId,
                        'previous_person_role_status' => $prevRoleStatus,
                    ]);
                }
            }

            // Update Batch Summary
            $batch->update([
                'total_students'  => count(array_unique($affectedPersons)),
                'total_promoted'  => $totalPromoted,
                'total_retained'  => $totalRetained,
                'total_graduated' => $totalGraduated,
            ]);

            $this->lastPromotionBatch = $batch;
        });

        $this->showConfirmModal = false;
        $this->confirmPassword  = '';

        $this->toastSuccess("Proses Kenaikan Kelas Massal Berhasil: {$totalPromoted} Naik, {$totalRetained} Tinggal, {$totalGraduated} Lulus.");
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

    public function getBatchHistoryProperty()
    {
        return MadrasahPromotionBatch::orderBy('executed_at', 'desc')->get();
    }

    // =========================================================================
    // UNDO / ROLLBACK FEATURE (Database-Driven & Permanent)
    // =========================================================================

    public function openUndoConfirmModal(): void
    {
        if (!auth()->user()->can('execute-kenaikan-kelas')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk membatalkan kenaikan kelas.');
            return;
        }

        $this->lastPromotionBatch = MadrasahPromotionBatch::where('status', 'sukses')
            ->orderBy('executed_at', 'desc')
            ->first();

        if (!$this->lastPromotionBatch) {
            $this->toastError('Tidak ada riwayat kenaikan kelas aktif yang dapat dibatalkan.');
            return;
        }

        $this->confirmPassword      = '';
        $this->resetErrorBag();
        $this->showUndoConfirmModal = true;
    }

    public function executeUndoMassPromotion(): void
    {
        if (!auth()->user()->can('execute-kenaikan-kelas')) {
            $this->toastError('Akses ditolak: Anda tidak memiliki izin untuk membatalkan kenaikan kelas.');
            return;
        }

        if (empty($this->confirmPassword) || !Hash::check($this->confirmPassword, auth()->user()->password)) {
            $this->addError('confirmPassword', 'Password konfirmasi yang Anda masukkan salah.');
            $this->toastError('Konfirmasi Password Salah. Pembatalan Kenaikan Kelas Dibatalkan.');
            return;
        }

        $targetBatch = MadrasahPromotionBatch::where('status', 'sukses')
            ->orderBy('executed_at', 'desc')
            ->first();

        if (!$targetBatch) {
            $this->toastError('Tidak ada riwayat batch kenaikan kelas yang dapat dibatalkan.');
            return;
        }

        DB::transaction(function () use ($targetBatch) {
            $items = MadrasahPromotionBatchItem::where('batch_id', $targetBatch->id)->get();

            foreach ($items as $item) {
                // 1. Deactivate newly created enrollment
                if ($item->new_enrollment_id) {
                    MadrasahEnrollment::where('id', $item->new_enrollment_id)->update(['is_active' => false]);
                }

                // 2. Reactivate previous enrollment
                if ($item->previous_enrollment_id) {
                    MadrasahEnrollment::where('id', $item->previous_enrollment_id)->update(['is_active' => true]);
                }

                // 3. Revert PersonRole status if changed to alumni
                if ($item->previous_person_role_status) {
                    PersonRole::where('person_id', $item->person_id)
                        ->where('role_type', 'santri')
                        ->update([
                            'enrollment_status' => $item->previous_person_role_status,
                            'is_active'         => true,
                            'left_at'           => null,
                        ]);
                }
            }

            // Mark Batch as 'di_undo'
            $targetBatch->update([
                'status'         => 'di_undo',
                'undone_at'      => now(),
                'undone_by'      => auth()->id(),
                'undone_by_name' => auth()->user()->name ?? 'Admin',
            ]);
        });

        $this->toastSuccess('Kenaikan kelas massal BERHASIL DIBATALKAN. Seluruh data santri dikembalikan ke posisi semula.');
        $this->lastPromotionBatch   = null;
        $this->showUndoConfirmModal = false;
        $this->confirmPassword      = '';

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
