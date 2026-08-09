<?php

namespace App\Livewire\Keuangan;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Modules\Keuangan\Models\MajekPeriod;
use App\Modules\Keuangan\Models\MajekRegistration;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillPayment;
use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Traits\HasGenderScope;
use Illuminate\Support\Facades\DB;

class MajekManager extends Component
{
    use WithPagination, HasGenderScope;

    // ─── Navigation ──────────────────────────────────────────────────────────
    public int    $month;
    public int    $year;

    // ─── Period Setup Modal ───────────────────────────────────────────────────
    public bool   $showPeriodModal   = false;
    public int    $periodActiveDays  = 30;
    public float  $periodTarifPerHari = 3333.33;
    public float  $periodTarifPerHariPutri = 3000.00;
    public float  $targetMonthlyPutra = 100000.00;
    public float  $targetMonthlyPutri = 90000.00;
    public string $periodNotes       = '';

    // ─── Copy Period Modal ────────────────────────────────────────────────────
    public bool   $showCopyPeriodModal = false;
    public int    $copySourceMonth     = 1;
    public int    $copySourceYear      = 2026;

    // ─── Add Participant Modal (Shared Tab) ──────────────────────────────────
    public bool   $showAddModal      = false;
    public string $addTab            = 'komplek'; // 'komplek' | 'pencarian'

    // ─── Tab Komplek / Super Bulk ──────────────────────────────────────────────
    public string $selectedDormitoryId   = '';
    public array  $dormitoryStudents    = [];      // Array of student details
    public array  $bulkSelections       = [];      // [person_id => bool]
    public array  $bulkSessions         = [];      // [person_id => '2x'|'pagi'|'sore']
    public array  $bulkDays             = [];      // [person_id => int]
    public array  $bulkNotes            = [];      // [person_id => string]

    // ─── Super Bulk Search & Mass Config ─────────────────────────────────────
    public string $searchBulkQuery       = '';
    public string $filterBulkDormitoryId = '';
    public string $filterBulkStatus      = 'unregistered'; // 'all' | 'unregistered' | 'registered'
    public string $massSesi              = '2x';           // '2x' | 'pagi' | 'sore'
    public int    $massDays              = 30;
    public string $massNotes             = '';

    // ─── Tab Pencarian (Single) ───────────────────────────────────────────────
    public string $searchQuery       = '';
    public array  $searchResults     = [];
    public string $selectedPersonId  = '';
    public string $selectedPersonName = '';
    public string $selectedSesi      = '2x';     // '2x' | 'pagi' | 'sore'
    public int    $selectedPersonDays = 30;
    public string $selectedPersonNotes = '';

    // ─── Edit Participant Modal ───────────────────────────────────────────────
    public bool   $showEditModal     = false;
    public ?string $editRegId        = null;
    public string $editPersonName    = '';
    public string $editSesi          = '2x';
    public int    $editDays          = 30;
    public string $editNotes         = '';

    // ─── Delete Participant Confirmation Modal ─────────────────────────────────
    public bool   $showDeleteModal   = false;
    public ?string $deleteRegId      = null;
    public string $deletePersonName  = '';

    // ─── Payment Checklist ────────────────────────────────────────────────────
    public array  $paymentChecks     = [];      // [registration_id => bool]
    public array  $paymentAmounts    = [];      // [registration_id => float|string]
    public string $payMethod         = 'cash';
    public bool   $showConfirmModal  = false;
    public bool   $confirmCheck      = false;

    // ─── Totals (updated reactively) ─────────────────────────────────────────
    public float  $totalChecked      = 0.0;
    public int    $countChecked      = 0;

    // ─── Flash ───────────────────────────────────────────────────────────────
    public string $flashSuccess      = '';
    public string $flashError        = '';

    // ─── Main Participant Table Filter & Search ────────────────────────────────
    public string $searchParticipant = '';
    public array  $filterDormitoryIds = [];
    public string $filterStatus = 'all'; // 'all' | 'paid' | 'unpaid' | 'partial'

    public function updatingSearchParticipant(): void { $this->resetPage(); }
    public function updatingFilterDormitoryIds(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

    protected $queryString = [
        'searchParticipant' => ['except' => ''],
        'filterDormitoryIds' => ['except' => []],
        'filterStatus' => ['except' => 'all'],
    ];

    // =========================================================================
    // Lifecycle
    // =========================================================================

    public function mount(): void
    {
        $user = auth()->user();
        if ($user && ! ($user->hasRole('super-admin') || $user->hasRole('manajemen') || $user->hasRole('pengasuh') || $user->can('manage-majek'))) {
            abort(403, 'Anda tidak memiliki akses ke modul Majek (Katering Asrama Pondok).');
        }

        $this->month = (int) now()->format('m');
        $this->year  = (int) now()->format('Y');

        $this->recalculateAllUnpaidRegistrations();
    }

    // =========================================================================
    // Computed Properties
    // =========================================================================

    #[Computed]
    public function activePeriod(): ?MajekPeriod
    {
        return MajekPeriod::where('month', $this->month)
                          ->where('year',  $this->year)
                          ->first();
    }

    #[Computed]
    public function registrations()
    {
        $query = MajekRegistration::with([
                'person',
                'person.roomAssignments' => fn($q) => $q->active()->with('room.dormitory'),
            ])
            ->where('month', $this->month)
            ->where('year',  $this->year)
            ->whereHas('person', fn($q) => $q->when($this->genderScope(), fn($sq, $g) => $sq->where('gender', $g)));

        // Filter: Search Participant
        if (!empty($this->searchParticipant)) {
            $query->whereHas('person', function ($q) {
                $q->where('name', 'like', '%' . $this->searchParticipant . '%')
                  ->orWhere('nik', 'like', '%' . $this->searchParticipant . '%')
                  ->orWhereHas('santriProfile', function ($sp) {
                      $sp->where('additional_info->nis', 'like', '%' . $this->searchParticipant . '%')
                         ->orWhere('additional_info->nisn', 'like', '%' . $this->searchParticipant . '%');
                  });
            });
        }

        // Filter: Dormitories (Multi-select)
        if (!empty($this->filterDormitoryIds)) {
            $query->whereHas('person.roomAssignments', function ($q) {
                $q->active()->whereHas('room', function ($r) {
                    $r->whereIn('dormitory_id', $this->filterDormitoryIds);
                });
            });
        }

        // Filter: Status (Lunas, Belum, Sebagian)
        if ($this->filterStatus !== 'all') {
            if ($this->filterStatus === 'paid') {
                $query->whereHas('bills')
                      ->whereDoesntHave('bills', fn($b) => $b->where('status', '!=', 'paid'));
            } elseif ($this->filterStatus === 'unpaid') {
                $query->where(fn($q) => 
                    $q->whereDoesntHave('bills')
                      ->orWhere(fn($sq) => $sq->whereHas('bills')->whereDoesntHave('bills', fn($b) => $b->where('status', '!=', 'unpaid')))
                );
            } elseif ($this->filterStatus === 'partial') {
                $query->whereHas('bills')
                      ->where(function($q) {
                          $q->whereHas('bills', fn($b) => $b->where('status', 'partial'))
                            ->orWhere(function($sq) {
                                $sq->whereHas('bills', fn($b) => $b->where('status', 'paid'))
                                  ->whereHas('bills', fn($b) => $b->where('status', 'unpaid'));
                            });
                      });
            }
        }

        // Order by person's name using join
        return $query->select('majek_registrations.*')
            ->join('persons', 'majek_registrations.person_id', '=', 'persons.id')
            ->orderBy('persons.name', 'asc')
            ->paginate(15);
    }

    #[Computed]
    public function overallStats(): array
    {
        $total = MajekRegistration::where('month', $this->month)
                                  ->where('year',  $this->year)
                                  ->whereHas('person', fn($q) => $q->when($this->genderScope(), fn($sq, $g) => $sq->where('gender', $g)))
                                  ->count();

        $paid = MajekRegistration::where('month', $this->month)
                                 ->where('year',  $this->year)
                                 ->whereHas('person', fn($q) => $q->when($this->genderScope(), fn($sq, $g) => $sq->where('gender', $g)))
                                 ->whereHas('bills')
                                 ->whereDoesntHave('bills', fn($b) => $b->where('status', '!=', 'paid'))
                                 ->count();

        $partial = MajekRegistration::where('month', $this->month)
                                    ->where('year',  $this->year)
                                    ->whereHas('person', fn($q) => $q->when($this->genderScope(), fn($sq, $g) => $sq->where('gender', $g)))
                                    ->whereHas('bills', fn($b) => $b->where('amount_paid', '>', 0))
                                    ->whereHas('bills', fn($b) => $b->where('status', '!=', 'paid'))
                                    ->count();

        return [
            'total'   => $total,
            'paid'    => $paid,
            'partial' => $partial,
            'unpaid'  => max(0, $total - $paid - $partial),
        ];
    }

    #[Computed]
    public function copyPreviewData(): array
    {
        if (!$this->showCopyPeriodModal) {
            return [
                'students' => [],
                'total_source' => 0,
                'will_copy_count' => 0,
                'already_registered_count' => 0,
            ];
        }

        $existingPersonIdsMap = MajekRegistration::where('month', $this->month)
            ->where('year', $this->year)
            ->pluck('person_id')
            ->flip()
            ->toArray();

        $sourceRegs = MajekRegistration::with('person')
            ->where('month', $this->copySourceMonth)
            ->where('year', $this->copySourceYear)
            ->whereHas('person', fn($q) => $q->when($this->genderScope(), fn($sq, $g) => $sq->where('gender', $g)))
            ->get();

        $willCopyCount = 0;
        $alreadyCount = 0;
        $studentsList = [];

        foreach ($sourceRegs as $reg) {
            $isAlready = isset($existingPersonIdsMap[$reg->person_id]);
            if ($isAlready) {
                $alreadyCount++;
            } else {
                $willCopyCount++;
            }

            $sesiLabel = match(true) {
                $reg->session_pagi && $reg->session_sore => '2x (Pagi+Sore)',
                $reg->session_pagi                       => '1x Pagi',
                $reg->session_sore                       => '1x Sore',
                default                                  => '—',
            };

            $studentsList[] = [
                'id'         => $reg->person_id,
                'name'       => $reg->person->name ?? 'Santri Tidak Ditemukan',
                'gender'     => $reg->person->gender ?? 'L',
                'sesi'       => $sesiLabel,
                'is_already' => $isAlready,
            ];
        }

        usort($studentsList, function($a, $b) {
            if ($a['is_already'] !== $b['is_already']) {
                return $a['is_already'] ? 1 : -1;
            }
            return strcmp($a['name'], $b['name']);
        });

        return [
            'students'                 => $studentsList,
            'total_source'             => count($sourceRegs),
            'will_copy_count'          => $willCopyCount,
            'already_registered_count' => $alreadyCount,
        ];
    }

    #[Computed]
    public function paidDetails(): array
    {
        $regIds = $this->registrations->pluck('id');
        $result = [];
        foreach ($regIds as $id) {
            $bills = Bill::where('reference_id', $id)->get();
            if ($bills->isEmpty()) {
                $reg = MajekRegistration::find($id);
                $total = $reg ? ((float)$reg->amount_pagi + (float)$reg->amount_sore) : 0;
                $result[$id] = [
                    'status'    => 'unpaid',
                    'paid'      => 0.0,
                    'remaining' => $total,
                ];
                continue;
            }

            $totalAmount = $bills->sum('amount');
            $totalPaid   = $bills->sum('amount_paid');
            $remaining   = max(0, $totalAmount - $totalPaid);

            if ($totalPaid >= $totalAmount && $totalAmount > 0) {
                $status = 'paid';
            } elseif ($totalPaid > 0) {
                $status = 'partial';
            } else {
                $status = 'unpaid';
            }

            $result[$id] = [
                'status'    => $status,
                'paid'      => (float)$totalPaid,
                'remaining' => (float)$remaining,
            ];
        }
        return $result;
    }

    #[Computed]
    public function paidStatuses(): array
    {
        $details = $this->paidDetails;
        $statuses = [];
        foreach ($details as $id => $item) {
            $statuses[$id] = $item['status'];
        }
        return $statuses;
    }

    #[Computed]
    public function monthLabel(): string
    {
        return Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('F Y');
    }

    #[Computed]
    public function tarif2x(): float
    {
        return $this->activePeriod ? $this->activePeriod->tarif2x : 0;
    }

    #[Computed]
    public function tarif1x(): float
    {
        return $this->activePeriod ? $this->activePeriod->tarif1x : 0;
    }

    #[Computed]
    public function tarif2xPutri(): float
    {
        return $this->activePeriod ? $this->activePeriod->tarif2x_putri : 0;
    }

    #[Computed]
    public function tarif1xPutri(): float
    {
        return $this->activePeriod ? $this->activePeriod->tarif1x_putri : 0;
    }

    #[Computed]
    public function dormitories()
    {
        return Dormitory::active()
            ->when($this->genderScope(), fn($q, $g) => $q->where('gender', $g))
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function previewData(): array
    {
        $checkedIds = array_keys(array_filter($this->paymentChecks));
        if (empty($checkedIds)) {
            return [];
        }

        $regs = MajekRegistration::with('person')->whereIn('id', $checkedIds)->get();

        $data = [];
        foreach ($regs as $reg) {
            $sesiLabel = match(true) {
                $reg->session_pagi && $reg->session_sore => '2x (Pagi + Sore)',
                $reg->session_pagi                       => '1x Pagi',
                $reg->session_sore                       => '1x Sore',
                default                                  => '—',
            };

            $remaining = $this->getRemainingUnpaidAmount($reg->id);
            $payAmt = isset($this->paymentAmounts[$reg->id]) && $this->paymentAmounts[$reg->id] !== ''
                ? (float)$this->paymentAmounts[$reg->id]
                : $remaining;

            $data[] = [
                'id'        => $reg->id,
                'name'      => $reg->person->name,
                'sesi'      => $sesiLabel,
                'total'     => (float)$reg->amount_pagi + (float)$reg->amount_sore,
                'remaining' => $remaining,
                'pay_amt'   => $payAmt,
            ];
        }
        return $data;
    }

    #[Computed]
    public function selectedStudentsList(): array
    {
        $checkedIds = array_keys(array_filter($this->bulkSelections));
        if (empty($checkedIds)) {
            return [];
        }

        $defaultDays = $this->activePeriod ? $this->activePeriod->active_days : 30;

        $persons = Person::whereIn('id', $checkedIds)
            ->with(['roomAssignments' => fn($q) => $q->active()->with('room.dormitory')])
            ->orderBy('name')
            ->get();

        $result = [];
        foreach ($persons as $p) {
            $dormName = '—';
            $activeAssignment = $p->roomAssignments->first();
            if ($activeAssignment && $activeAssignment->room && $activeAssignment->room->dormitory) {
                $dormName = $activeAssignment->room->dormitory->name;
            }

            if (!isset($this->bulkSessions[$p->id])) {
                $this->bulkSessions[$p->id] = '2x';
            }
            if (!isset($this->bulkDays[$p->id])) {
                $this->bulkDays[$p->id] = $defaultDays;
            }
            if (!isset($this->bulkNotes[$p->id])) {
                $this->bulkNotes[$p->id] = '';
            }

            $result[] = [
                'id'        => $p->id,
                'name'      => $p->name,
                'gender'    => $p->gender,
                'dormitory' => $dormName,
                'session'   => $this->bulkSessions[$p->id] ?? '2x',
                'days'      => $this->bulkDays[$p->id] ?? $defaultDays,
                'notes'     => $this->bulkNotes[$p->id] ?? '',
            ];
        }

        return $result;
    }

    public function setAllSelectedSessions(string $sesi): void
    {
        $selectedIds = array_keys(array_filter($this->bulkSelections));
        foreach ($selectedIds as $personId) {
            $this->bulkSessions[$personId] = $sesi;
        }
    }

    #[Computed]
    public function bulkStudentsList(): array
    {
        $registrationsMap = MajekRegistration::where('month', $this->month)
            ->where('year',  $this->year)
            ->get()
            ->keyBy('person_id');

        $query = Person::active()
            ->whereHas('activeRoles', function ($q) {
                $q->where('role_type', 'santri');
            })
            ->when($this->genderScope(), fn($q, $g) => $q->where('gender', $g))
            ->when($this->filterBulkDormitoryId, function ($q) {
                $q->whereHas('roomAssignments', function ($rq) {
                    $rq->active()->whereHas('room', function ($r) {
                        $r->where('dormitory_id', $this->filterBulkDormitoryId);
                    });
                });
            })
            ->when($this->searchBulkQuery, function ($q) {
                $q->where('name', 'like', '%' . $this->searchBulkQuery . '%');
            })
            ->with(['roomAssignments' => fn($q) => $q->active()->with('room.dormitory')])
            ->orderBy('name');

        $students = $query->limit(300)->get();
        $defaultDays = $this->activePeriod ? $this->activePeriod->active_days : 30;
        $result = [];

        foreach ($students as $student) {
            $reg = $registrationsMap->get($student->id);
            $isReg = !is_null($reg);

            if ($this->filterBulkStatus === 'unregistered' && $isReg) continue;
            if ($this->filterBulkStatus === 'registered' && !$isReg) continue;

            $dormName = '—';
            $activeAssignment = $student->roomAssignments->first();
            if ($activeAssignment && $activeAssignment->room && $activeAssignment->room->dormitory) {
                $dormName = $activeAssignment->room->dormitory->name;
            }

            $sesi = '2x';
            if ($isReg) {
                if ($reg->session_pagi && $reg->session_sore) {
                    $sesi = '2x';
                } elseif ($reg->session_pagi) {
                    $sesi = 'pagi';
                } else {
                    $sesi = 'sore';
                }
            }

            $result[] = [
                'id'            => $student->id,
                'name'          => $student->name,
                'gender'        => $student->gender,
                'dormitory'     => $dormName,
                'is_registered' => $isReg,
                'session'       => $sesi,
                'days'          => $isReg ? $reg->active_days : $defaultDays,
                'notes'         => $isReg ? ($reg->notes ?? '') : '',
            ];
        }

        return $result;
    }

    public function applyMassConfiguration(): void
    {
        $selectedIds = array_keys(array_filter($this->bulkSelections));
        if (empty($selectedIds)) {
            $this->flashError = 'Pilih minimal satu santri untuk menerapkan konfigurasi massal.';
            return;
        }

        foreach ($selectedIds as $personId) {
            $this->bulkSessions[$personId] = $this->massSesi;
            $this->bulkDays[$personId]     = $this->massDays;
            $this->bulkNotes[$personId]    = $this->massNotes;
        }

        $this->flashSuccess = 'Konfigurasi berhasil diterapkan ke ' . count($selectedIds) . ' santri terpilih.';
    }

    public function selectAllFilteredStudents(): void
    {
        $students = $this->bulkStudentsList;
        $defaultDays = $this->activePeriod ? $this->activePeriod->active_days : 30;

        foreach ($students as $std) {
            if (!$std['is_registered']) {
                $this->bulkSelections[$std['id']] = true;
                if (!isset($this->bulkSessions[$std['id']])) {
                    $this->bulkSessions[$std['id']] = $this->massSesi;
                }
                if (!isset($this->bulkDays[$std['id']])) {
                    $this->bulkDays[$std['id']] = $defaultDays;
                }
            }
        }
    }

    public function toggleStudentSelection(string $studentId): void
    {
        $current = $this->bulkSelections[$studentId] ?? false;
        $this->bulkSelections[$studentId] = !$current;

        $defaultDays = $this->activePeriod ? $this->activePeriod->active_days : 30;
        if ($this->bulkSelections[$studentId]) {
            if (!isset($this->bulkSessions[$studentId])) {
                $this->bulkSessions[$studentId] = '2x';
            }
            if (!isset($this->bulkDays[$studentId])) {
                $this->bulkDays[$studentId] = $defaultDays;
            }
        }
    }

    public function clearAllBulkSelections(): void
    {
        $this->bulkSelections = [];
    }

    // =========================================================================
    // Navigation
    // =========================================================================

    public function incrementMonth(): void
    {
        if ($this->month === 12) { $this->month = 1; $this->year++; }
        else $this->month++;
        $this->resetPaymentState();
        unset($this->activePeriod, $this->registrations, $this->paidStatuses, $this->paidDetails);
        $this->recalculateAllUnpaidRegistrations();
    }

    public function decrementMonth(): void
    {
        if ($this->month === 1) { $this->month = 12; $this->year--; }
        else $this->month--;
        $this->resetPaymentState();
        unset($this->activePeriod, $this->registrations, $this->paidStatuses, $this->paidDetails);
        $this->recalculateAllUnpaidRegistrations();
    }

    // =========================================================================
    // Period Setup Modal
    // =========================================================================

    public function openPeriodModal(): void
    {
        $period = $this->activePeriod;
        $this->periodActiveDays        = $period ? $period->active_days               : 30;
        $this->periodTarifPerHari      = $period ? (float) $period->tarif_per_hari    : 3333.33;
        $this->periodTarifPerHariPutri = $period ? (float) ($period->tarif_per_hari_putri ?? 3000.00) : 3000.00;
        $this->periodNotes             = $period ? ($period->notes ?? '')              : '';

        $days = max(1, $this->periodActiveDays);
        $this->targetMonthlyPutra      = round($this->periodTarifPerHari * $days);
        $this->targetMonthlyPutri      = round($this->periodTarifPerHariPutri * $days);

        $this->showPeriodModal         = true;
    }

    public function updatedTargetMonthlyPutra(): void
    {
        $days = max(1, (int)$this->periodActiveDays);
        $val = (float)($this->targetMonthlyPutra ?? 0);
        $this->periodTarifPerHari = round($val / $days, 2);
    }

    public function updatedTargetMonthlyPutri(): void
    {
        $days = max(1, (int)$this->periodActiveDays);
        $val = (float)($this->targetMonthlyPutri ?? 0);
        $this->periodTarifPerHariPutri = round($val / $days, 2);
    }

    public function updatedPeriodActiveDays(): void
    {
        $days = max(1, (int)($this->periodActiveDays ?? 30));
        if ($this->targetMonthlyPutra > 0) {
            $this->periodTarifPerHari = round((float)$this->targetMonthlyPutra / $days, 2);
        }
        if ($this->targetMonthlyPutri > 0) {
            $this->periodTarifPerHariPutri = round((float)$this->targetMonthlyPutri / $days, 2);
        }
    }

    public function updatedPeriodTarifPerHari(): void
    {
        $days = max(1, (int)($this->periodActiveDays ?? 30));
        $this->targetMonthlyPutra = round((float)$this->periodTarifPerHari * $days);
    }

    public function updatedPeriodTarifPerHariPutri(): void
    {
        $days = max(1, (int)($this->periodActiveDays ?? 30));
        $this->targetMonthlyPutri = round((float)$this->periodTarifPerHariPutri * $days);
    }

    public function closePeriodModal(): void
    {
        $this->showPeriodModal = false;
    }

    public function savePeriod(): void
    {
        $this->validate([
            'periodActiveDays'        => 'required|integer|min:1|max:31',
            'periodTarifPerHari'      => 'required|numeric|min:1',
            'periodTarifPerHariPutri' => 'required|numeric|min:1',
        ], [
            'periodActiveDays.required'        => 'Hari aktif wajib diisi.',
            'periodActiveDays.min'             => 'Hari aktif minimal 1.',
            'periodActiveDays.max'             => 'Hari aktif maksimal 31.',
            'periodTarifPerHari.required'      => 'Tarif per hari Putra wajib diisi.',
            'periodTarifPerHari.min'           => 'Tarif Putra harus lebih dari 0.',
            'periodTarifPerHariPutri.required' => 'Tarif per hari Putri wajib diisi.',
            'periodTarifPerHariPutri.min'      => 'Tarif Putri harus lebih dari 0.',
        ]);

        $oldActiveDays = $this->activePeriod ? $this->activePeriod->active_days : null;

        MajekPeriod::updateOrCreate(
            ['month' => $this->month, 'year' => $this->year],
            [
                'active_days'          => $this->periodActiveDays,
                'tarif_per_hari'       => $this->periodTarifPerHari,
                'tarif_per_hari_putri' => $this->periodTarifPerHariPutri,
                'notes'                => $this->periodNotes ?: null,
                'created_by'           => auth()->id(),
            ]
        );

        // CLEAR COMPUTED PROPERTY CACHE FIRST SO RECALCULATION USES FRESH PERIOD DATA
        unset($this->activePeriod, $this->tarif2x, $this->tarif1x, $this->tarif2xPutri, $this->tarif1xPutri, $this->registrations);

        $this->recalculateAllUnpaidRegistrations($oldActiveDays, (int) $this->periodActiveDays);

        $this->showPeriodModal = false;
        $this->flashSuccess    = 'Konfigurasi periode berhasil disimpan dan tagihan belum dibayar otomatis dihitung ulang.';
    }

    // =========================================================================
    // Copy Period Modal & Action
    // =========================================================================

    public function openCopyPeriodModal(): void
    {
        if ($this->month === 1) {
            $this->copySourceMonth = 12;
            $this->copySourceYear  = $this->year - 1;
        } else {
            $this->copySourceMonth = $this->month - 1;
            $this->copySourceYear  = $this->year;
        }
        $this->showCopyPeriodModal = true;
    }

    public function closeCopyPeriodModal(): void
    {
        $this->showCopyPeriodModal = false;
    }

    public function copyRegistrationsFromPeriod(): void
    {
        if ($this->copySourceMonth === $this->month && $this->copySourceYear === $this->year) {
            $this->flashError = 'Bulan asal salinan tidak boleh sama dengan bulan yang sedang aktif.';
            return;
        }

        // Ensure active period exists for target month & year
        $targetPeriod = $this->activePeriod;
        if (!$targetPeriod) {
            $targetMonthName = Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('F Y');
            $this->flashError = "Konfigurasi Periode (Hari Aktif & Tarif) untuk bulan {$targetMonthName} belum dibuat. Silakan atur konfigurasi bulan ini terlebih dahulu.";
            $this->showCopyPeriodModal = false;
            return;
        }

        // Get source registrations
        $sourceRegs = MajekRegistration::where('month', $this->copySourceMonth)
            ->where('year', $this->copySourceYear)
            ->whereHas('person', fn($q) => $q->when($this->genderScope(), fn($sq, $g) => $sq->where('gender', $g)))
            ->with('person')
            ->get();

        if ($sourceRegs->isEmpty()) {
            $sourceMonthName = Carbon::createFromDate($this->copySourceYear, $this->copySourceMonth, 1)->translatedFormat('F Y');
            $this->flashError = "Tidak ditemukan data peserta Majek pada periode {$sourceMonthName}.";
            return;
        }

        // Get existing registered person_ids in target period
        $existingPersonIds = MajekRegistration::where('month', $this->month)
            ->where('year', $this->year)
            ->pluck('person_id')
            ->toArray();

        $addedCount = 0;
        $targetPeriodDays = $targetPeriod->active_days;

        DB::transaction(function () use ($sourceRegs, $existingPersonIds, $targetPeriod, $targetPeriodDays, &$addedCount) {
            foreach ($sourceRegs as $srcReg) {
                if (in_array($srcReg->person_id, $existingPersonIds)) {
                    continue; // Skip already registered in target month
                }

                $gender = $srcReg->person?->gender ?? 'L';
                $dailyRate = $targetPeriod->getTarifPerHariForGender($gender);

                $days = $targetPeriodDays;

                $amountPagi = $srcReg->session_pagi ? ($dailyRate * $days) : 0;
                $amountSore = $srcReg->session_sore ? ($dailyRate * $days) : 0;

                $newReg = MajekRegistration::create([
                    'person_id'     => $srcReg->person_id,
                    'month'         => $this->month,
                    'year'          => $this->year,
                    'session_pagi'  => $srcReg->session_pagi,
                    'session_sore'  => $srcReg->session_sore,
                    'active_days'   => $days,
                    'amount_pagi'   => $amountPagi,
                    'amount_sore'   => $amountSore,
                    'registered_by' => auth()->id(),
                    'notes'         => $srcReg->notes,
                ]);

                $this->createUnpaidBills($newReg);
                $addedCount++;
            }
        });

        unset($this->activePeriod, $this->registrations, $this->paidStatuses, $this->paidDetails);
        $this->showCopyPeriodModal = false;

        $sourceMonthName = Carbon::createFromDate($this->copySourceYear, $this->copySourceMonth, 1)->translatedFormat('F Y');
        $targetMonthName = Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('F Y');

        if ($addedCount > 0) {
            $this->flashSuccess = "Berhasil menyalin {$addedCount} peserta Majek dari periode {$sourceMonthName} ke {$targetMonthName}.";
        } else {
            $this->flashError = "Seluruh peserta Majek dari {$sourceMonthName} sudah terdaftar pada periode {$targetMonthName}.";
        }
    }

    public function recalculateAllUnpaidRegistrations(?int $oldDays = null, ?int $newDays = null): void
    {
        $period = $this->activePeriod;
        if (!$period) return;

        $allRegs = MajekRegistration::where('month', $this->month)
                                    ->where('year',  $this->year)
                                    ->with('person')
                                    ->get();

        foreach ($allRegs as $reg) {
            // Protect registrations that have any payments already recorded (amount_paid > 0)
            $hasPayments = Bill::where('reference_id', $reg->id)->where('amount_paid', '>', 0)->exists();
            if (!$hasPayments) {
                // If period active_days was updated, sync active_days for registrations that were matching old active_days
                if ($oldDays !== null && $newDays !== null && ($reg->active_days == $oldDays || $reg->active_days === null)) {
                    $reg->active_days = $newDays;
                }
                $this->recalculateRegistrationAmount($reg);
            }
        }
    }

    private function recalculateRegistrationAmount(MajekRegistration $reg): void
    {
        $period = $this->activePeriod;
        if (!$period) return;

        if (!$reg->relationLoaded('person')) {
            $reg->load('person');
        }

        // Use active_days if set, otherwise use period default
        $days = $reg->active_days ?? $period->active_days;
        $dailyRate = $period->getTarifPerHariForGender($reg->person?->gender);

        $t1x = $dailyRate * $days;
        $reg->amount_pagi = $reg->session_pagi ? $t1x : 0;
        $reg->amount_sore = $reg->session_sore ? $t1x : 0;
        $reg->save();

        // Sync unpaid bills if present
        $pagiBill = Bill::where('reference_id', $reg->id)->where('bill_type', 'majek_pagi')->first();
        if ($pagiBill && $pagiBill->amount_paid == 0) {
            $pagiBill->amount = $reg->amount_pagi;
            $pagiBill->save();
            $pagiBill->recalculateStatus();
        }

        $soreBill = Bill::where('reference_id', $reg->id)->where('bill_type', 'majek_sore')->first();
        if ($soreBill && $soreBill->amount_paid == 0) {
            $soreBill->amount = $reg->amount_sore;
            $soreBill->save();
            $soreBill->recalculateStatus();
        }
    }

    // =========================================================================
    // Add Participant Modal (Tabs & Bulk Logic)
    // =========================================================================

    public function openAddModal(): void
    {
        if (!$this->activePeriod) {
            $this->flashError = 'Buat konfigurasi periode terlebih dahulu sebelum mendaftarkan peserta.';
            return;
        }
        $this->addTab              = 'komplek';
        $this->selectedDormitoryId = '';
        $this->dormitoryStudents   = [];
        $this->searchBulkQuery     = '';
        $this->filterBulkDormitoryId = '';
        $this->filterBulkStatus    = 'unregistered';
        $this->massSesi            = '2x';
        $this->massDays            = $this->activePeriod->active_days;
        $this->massNotes           = '';
        $this->bulkSelections       = [];
        $this->bulkSessions         = [];
        $this->bulkDays             = [];
        $this->bulkNotes            = [];
        $this->searchQuery         = '';
        $this->searchResults       = [];
        $this->selectedPersonId    = '';
        $this->selectedPersonName  = '';
        $this->selectedSesi        = '2x';
        $this->selectedPersonDays  = $this->activePeriod->active_days;
        $this->selectedPersonNotes = '';
        $this->showAddModal        = true;
    }

    public function closeAddModal(): void
    {
        $this->showAddModal = false;
    }

    public function updatedSelectedDormitoryId(): void
    {
        $this->loadDormitoryStudents();
    }

    public function loadDormitoryStudents(): void
    {
        if (!$this->selectedDormitoryId) {
            $this->dormitoryStudents = [];
            return;
        }

        $registrationsMap = MajekRegistration::where('month', $this->month)
                                             ->where('year',  $this->year)
                                             ->get()
                                             ->keyBy('person_id');

        $students = Person::active()
            ->whereHas('activeRoles', function ($q) {
                $q->where('role_type', 'santri');
            })
            ->whereHas('roomAssignments', function ($q) {
                $q->active()->whereHas('room', function ($r) {
                    $r->where('dormitory_id', $this->selectedDormitoryId);
                });
            })
            ->when($this->genderScope(), fn($q, $g) => $q->where('gender', $g))
            ->orderBy('name')
            ->get();

        $this->dormitoryStudents = [];
        $defaultDays = $this->activePeriod ? $this->activePeriod->active_days : 30;

        foreach ($students as $student) {
            $reg = $registrationsMap->get($student->id);
            $isReg = !is_null($reg);

            $sesi = '2x';
            if ($isReg) {
                if ($reg->session_pagi && $reg->session_sore) {
                    $sesi = '2x';
                } elseif ($reg->session_pagi) {
                    $sesi = 'pagi';
                } else {
                    $sesi = 'sore';
                }
            }

            $this->dormitoryStudents[] = [
                'id'            => $student->id,
                'name'          => $student->name,
                'is_registered' => $isReg,
                'session'       => $sesi,
                'days'          => $isReg ? $reg->active_days : $defaultDays,
                'notes'         => $isReg ? ($reg->notes ?? '') : '',
            ];
            
            // Only initialize defaults if not already selected, preventing wiping selections when shifting complexes
            if (!$isReg && !isset($this->bulkSelections[$student->id])) {
                $this->bulkSelections[$student->id] = false;
                $this->bulkSessions[$student->id]   = '2x';
                $this->bulkDays[$student->id]       = $defaultDays;
                $this->bulkNotes[$student->id]      = '';
            }
        }
    }

    public function uncheckStudent(string $studentId): void
    {
        $this->bulkSelections[$studentId] = false;
    }

    public function addPesertaBulk(): void
    {
        $period = $this->activePeriod;
        if (!$period) return;

        $addedCount = 0;

        $registeredIds = MajekRegistration::where('month', $this->month)
                                          ->where('year',  $this->year)
                                          ->pluck('person_id')
                                          ->toArray();

        $selectedPersonIds = array_keys(array_filter($this->bulkSelections));
        $personsGenderMap = Person::whereIn('id', $selectedPersonIds)->pluck('gender', 'id')->toArray();

        DB::transaction(function () use (&$addedCount, $period, $registeredIds, $personsGenderMap) {
            foreach ($this->bulkSelections as $personId => $selected) {
                if (!$selected) continue;
                if (in_array($personId, $registeredIds)) continue; // skip already registered

                $gender = $personsGenderMap[$personId] ?? 'L';
                $dailyRate = $period->getTarifPerHariForGender($gender);

                $sesi = $this->bulkSessions[$personId] ?? '2x';
                $days = (int) ($this->bulkDays[$personId] ?? $period->active_days);
                $notes = $this->bulkNotes[$personId] ?? '';

                $t1x = $dailyRate * $days;

                $reg = MajekRegistration::create([
                    'person_id'    => $personId,
                    'month'        => $this->month,
                    'year'         => $this->year,
                    'session_pagi' => in_array($sesi, ['pagi', '2x']),
                    'session_sore' => in_array($sesi, ['sore', '2x']),
                    'active_days'  => $days,
                    'amount_pagi'  => in_array($sesi, ['pagi', '2x']) ? $t1x : 0,
                    'amount_sore'  => in_array($sesi, ['sore', '2x']) ? $t1x : 0,
                    'registered_by' => auth()->id(),
                    'notes'        => $notes ?: null,
                ]);

                $this->createUnpaidBills($reg);

                $addedCount++;
            }
        });

        unset($this->registrations, $this->paidStatuses);
        $this->showAddModal = false;
        $this->selectedDormitoryId = '';
        $this->dormitoryStudents = [];
        $this->bulkSelections = [];
        
        if ($addedCount > 0) {
            $this->flashSuccess = "$addedCount peserta berhasil didaftarkan.";
        } else {
            $this->flashError = "Tidak ada peserta terpilih untuk didaftarkan.";
        }
    }

    public function switchTab(string $tab): void
    {
        $this->addTab = $tab;

        if ($tab === 'pencarian') {
            // Reset bulk complexes state
            $this->selectedDormitoryId = '';
            $this->dormitoryStudents   = [];
            $this->bulkSelections      = [];
            $this->bulkSessions        = [];
            $this->bulkDays            = [];
            $this->bulkNotes           = [];
        } else {
            // Reset single search state
            $this->searchQuery         = '';
            $this->searchResults       = [];
            $this->selectedPersonId    = '';
            $this->selectedPersonName  = '';
            $this->selectedPersonNotes = '';
            $this->selectedSesi        = '2x';
            if ($this->activePeriod) {
                $this->selectedPersonDays = $this->activePeriod->active_days;
            }
        }
        $this->flashError = '';
        $this->flashSuccess = '';
    }

    public function resetFilters(): void
    {
        $this->searchParticipant  = '';
        $this->filterDormitoryIds = [];
        $this->filterStatus       = 'all';
        $this->resetPage();
    }

    // =========================================================================
    // Single Participant Logic
    // =========================================================================

    public function updatedSearchQuery(): void
    {
        $this->flashError = '';
        if (strlen(trim($this->searchQuery)) < 2) {
            $this->searchResults = [];
            return;
        }

        $registeredIds = MajekRegistration::where('month', $this->month)
                                          ->where('year',  $this->year)
                                          ->pluck('person_id')
                                          ->toArray();

        $results = Person::active()
            ->whereHas('activeRoles', function ($q) {
                $q->where('role_type', 'santri');
            })
            ->where('name', 'LIKE', '%' . trim($this->searchQuery) . '%')
            ->when($this->genderScope(), fn($q, $g) => $q->where('gender', $g))
            ->with(['roomAssignments' => fn($q) => $q->active()->with('room.dormitory')])
            ->orderBy('name')
            ->limit(8)
            ->get();

        $this->searchResults = $results->map(fn($p) => [
            'id'            => $p->id,
            'name'          => $p->name,
            'dormitory'     => $p->roomAssignments->first()?->room?->dormitory?->name ?? '—',
            'is_registered' => in_array($p->id, $registeredIds),
        ])->toArray();
    }

    public function selectPerson(string $personId, string $personName): void
    {
        $this->selectedPersonId   = $personId;
        $this->selectedPersonName = $personName;
        $this->searchQuery        = $personName;
        $this->searchResults      = [];
        $this->selectedPersonDays = $this->activePeriod ? $this->activePeriod->active_days : 30;
    }

    public function addPeserta(): void
    {
        if (!$this->selectedPersonId) {
            $this->flashError = 'Pilih santri terlebih dahulu dari hasil pencarian.';
            return;
        }

        $period = $this->activePeriod;
        if (!$period) return;

        // Check if already registered
        $exists = MajekRegistration::where('person_id', $this->selectedPersonId)
                                   ->where('month', $this->month)
                                   ->where('year',  $this->year)
                                   ->exists();
        if ($exists) {
            $this->flashError = 'Santri ini sudah terdaftar untuk periode ini.';
            return;
        }

        $days = (int)$this->selectedPersonDays;
        if ($days < 1 || $days > 31) {
            $this->flashError = 'Hari aktif khusus tidak valid (1-31).';
            return;
        }

        $person = Person::find($this->selectedPersonId);
        $dailyRate = $period->getTarifPerHariForGender($person?->gender);
        $t1x = $dailyRate * $days;

        DB::transaction(function () use ($days, $t1x) {
            $reg = MajekRegistration::create([
                'person_id'    => $this->selectedPersonId,
                'month'        => $this->month,
                'year'         => $this->year,
                'session_pagi' => in_array($this->selectedSesi, ['pagi', '2x']),
                'session_sore' => in_array($this->selectedSesi, ['sore', '2x']),
                'active_days'  => $days,
                'amount_pagi'  => in_array($this->selectedSesi, ['pagi', '2x']) ? $t1x : 0,
                'amount_sore'  => in_array($this->selectedSesi, ['sore', '2x']) ? $t1x : 0,
                'registered_by' => auth()->id(),
                'notes'        => $this->selectedPersonNotes ?: null,
            ]);

            $this->createUnpaidBills($reg);
        });

        unset($this->registrations, $this->paidStatuses);
        $this->showAddModal       = false;
        $this->selectedPersonId   = '';
        $this->selectedPersonName = '';
        $this->searchQuery        = '';
        $this->selectedPersonNotes = '';
        $this->flashSuccess       = 'Peserta berhasil didaftarkan.';
    }

    // =========================================================================
    // Edit Participant Modal
    // =========================================================================

    public function openEditModal(string $regId): void
    {
        $hasPaid = Bill::where('reference_id', $regId)
                       ->where('status', 'paid')
                       ->exists();

        if ($hasPaid) {
            $this->flashError = 'Pembayaran untuk peserta ini sudah lunas, tidak dapat diubah.';
            return;
        }

        $reg = MajekRegistration::with('person')->find($regId);
        if (!$reg) return;

        $this->editRegId      = $regId;
        $this->editPersonName = $reg->person->name;
        
        if ($reg->session_pagi && $reg->session_sore) {
            $this->editSesi = '2x';
        } elseif ($reg->session_pagi) {
            $this->editSesi = 'pagi';
        } else {
            $this->editSesi = 'sore';
        }

        $this->editDays       = $reg->active_days ?? ($this->activePeriod ? $this->activePeriod->active_days : 30);
        $this->editNotes      = $reg->notes ?? '';
        $this->showEditModal  = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
    }

    public function saveEdit(): void
    {
        $reg = MajekRegistration::with('person')->find($this->editRegId);
        if (!$reg) return;

        $period = $this->activePeriod;
        if (!$period) return;

        $dailyRate = $period->getTarifPerHariForGender($reg->person?->gender);
        $t1x = $dailyRate * $this->editDays;

        $pagiBill = Bill::where('reference_id', $reg->id)->where('bill_type', 'majek_pagi')->first();
        $soreBill = Bill::where('reference_id', $reg->id)->where('bill_type', 'majek_sore')->first();

        $pagiPaid = $pagiBill ? (float)$pagiBill->amount_paid : 0.0;
        $sorePaid = $soreBill ? (float)$soreBill->amount_paid : 0.0;

        $newSessionPagi = in_array($this->editSesi, ['pagi', '2x']);
        $newSessionSore = in_array($this->editSesi, ['sore', '2x']);

        $newPagiAmount = $newSessionPagi ? $t1x : 0.0;
        $newSoreAmount = $newSessionSore ? $t1x : 0.0;

        // Validasi Keuangan: Mencegah penghapusan atau penurunan tarif di bawah nominal yang sudah dibayarkan kasir
        if (!$newSessionPagi && $pagiPaid > 0) {
            $this->flashError = "Tidak dapat menghapus Sesi Pagi karena santri sudah mencicil Sesi Pagi sebesar Rp " . number_format($pagiPaid, 0, ',', '.') . ".";
            return;
        }

        if (!$newSessionSore && $sorePaid > 0) {
            $this->flashError = "Tidak dapat menghapus Sesi Sore karena santri sudah mencicil Sesi Sore sebesar Rp " . number_format($sorePaid, 0, ',', '.') . ".";
            return;
        }

        if ($newSessionPagi && $newPagiAmount < ($pagiPaid - 0.01)) {
            $this->flashError = "Total tagihan Pagi baru (Rp " . number_format($newPagiAmount, 0, ',', '.') . ") tidak boleh lebih kecil dari uang yang sudah dibayarkan santri (Rp " . number_format($pagiPaid, 0, ',', '.') . ").";
            return;
        }

        if ($newSessionSore && $newSoreAmount < ($sorePaid - 0.01)) {
            $this->flashError = "Total tagihan Sore baru (Rp " . number_format($newSoreAmount, 0, ',', '.') . ") tidak boleh lebih kecil dari uang yang sudah dibayarkan santri (Rp " . number_format($sorePaid, 0, ',', '.') . ").";
            return;
        }

        DB::transaction(function () use ($reg, $t1x, $pagiBill, $soreBill) {
            $reg->session_pagi = in_array($this->editSesi, ['pagi', '2x']);
            $reg->session_sore = in_array($this->editSesi, ['sore', '2x']);
            $reg->active_days  = $this->editDays;
            $reg->amount_pagi  = in_array($this->editSesi, ['pagi', '2x']) ? $t1x : 0;
            $reg->amount_sore  = in_array($this->editSesi, ['sore', '2x']) ? $t1x : 0;
            $reg->notes        = $this->editNotes ?: null;
            $reg->save();

            // Pagi Bill
            if ($reg->session_pagi) {
                if ($pagiBill) {
                    if ($pagiBill->status !== 'paid') {
                        $pagiBill->amount = $t1x;
                        $pagiBill->save();
                        $pagiBill->recalculateStatus();
                    }
                } else {
                    Bill::create([
                        'person_id'       => $reg->person_id,
                        'bill_type'       => 'majek_pagi',
                        'reference_id'    => $reg->id,
                        'period_month'    => $reg->month,
                        'period_year'     => $reg->year,
                        'title'           => 'Katering Pagi ' . $this->monthLabel,
                        'amount'          => $t1x,
                        'amount_paid'     => 0,
                        'status'          => 'unpaid',
                        'managed_by_role' => 'bendahara-pusat',
                        'created_by'      => auth()->id(),
                    ]);
                }
            } else {
                if ($pagiBill && $pagiBill->status !== 'paid') {
                    $pagiBill->delete();
                }
            }

            // Sore Bill
            $soreBill = Bill::where('reference_id', $reg->id)->where('bill_type', 'majek_sore')->first();
            if ($reg->session_sore) {
                if ($soreBill) {
                    if ($soreBill->status !== 'paid') {
                        $soreBill->amount = $t1x;
                        $soreBill->save();
                        $soreBill->recalculateStatus();
                    }
                } else {
                    Bill::create([
                        'person_id'       => $reg->person_id,
                        'bill_type'       => 'majek_sore',
                        'reference_id'    => $reg->id,
                        'period_month'    => $reg->month,
                        'period_year'     => $reg->year,
                        'title'           => 'Katering Sore ' . $this->monthLabel,
                        'amount'          => $t1x,
                        'amount_paid'     => 0,
                        'status'          => 'unpaid',
                        'managed_by_role' => 'bendahara-pusat',
                        'created_by'      => auth()->id(),
                    ]);
                }
            } else {
                if ($soreBill && $soreBill->status !== 'paid') {
                    $soreBill->delete();
                }
            }
        });

        unset($this->registrations, $this->paidStatuses);
        $this->showEditModal = false;
        $this->flashSuccess  = "Detail katering santri '{$reg->person->name}' berhasil diperbarui.";
    }

    // =========================================================================
    // Payment Checklist & Installment Logic
    // =========================================================================

    public function updatedPaymentChecks(): void
    {
        $this->initializePaymentAmounts();
        $this->recalculateTotals();
    }

    public function updatedPaymentAmounts(): void
    {
        $this->recalculateTotals();
    }

    private function initializePaymentAmounts(): void
    {
        foreach ($this->paymentChecks as $regId => $checked) {
            if ($checked) {
                if (!isset($this->paymentAmounts[$regId]) || $this->paymentAmounts[$regId] === '' || $this->paymentAmounts[$regId] === null) {
                    $this->paymentAmounts[$regId] = $this->getRemainingUnpaidAmount($regId);
                }
            } else {
                unset($this->paymentAmounts[$regId]);
            }
        }
    }

    public function getRemainingUnpaidAmount(string $regId): float
    {
        $bills = Bill::where('reference_id', $regId)->where('status', '!=', 'paid')->get();
        if ($bills->isNotEmpty()) {
            return (float) $bills->sum(fn($b) => max(0, $b->amount - $b->amount_paid));
        }

        $reg = MajekRegistration::find($regId);
        if (!$reg) return 0.0;
        return (float)$reg->amount_pagi + (float)$reg->amount_sore;
    }

    private function recalculateTotals(): void
    {
        $checkedIds = array_keys(array_filter($this->paymentChecks));
        if (empty($checkedIds)) {
            $this->totalChecked = 0.0;
            $this->countChecked = 0;
            return;
        }

        $total = 0.0;
        foreach ($checkedIds as $regId) {
            $remaining = $this->getRemainingUnpaidAmount($regId);
            $amt = isset($this->paymentAmounts[$regId]) && $this->paymentAmounts[$regId] !== ''
                ? (float)$this->paymentAmounts[$regId]
                : $remaining;
            $total += max(0, $amt);
        }
        $this->totalChecked = $total;
        $this->countChecked = count($checkedIds);
    }

    public function confirmSetoran(): void
    {
        if ($this->countChecked === 0) return;
        $this->initializePaymentAmounts();
        $this->recalculateTotals();
        $this->confirmCheck     = false;
        $this->showConfirmModal = true;
    }

    public function cancelConfirm(): void
    {
        $this->showConfirmModal = false;
        $this->confirmCheck     = false;
    }

    public function prosesSetoran(): void
    {
        if (!$this->confirmCheck) return;

        // Validation for overpayments
        foreach ($this->paymentChecks as $regId => $checked) {
            if (!$checked) continue;

            $reg = MajekRegistration::with('person')->find($regId);
            if (!$reg) continue;

            $remaining = $this->getRemainingUnpaidAmount($reg->id);
            $payAmount = isset($this->paymentAmounts[$regId]) && $this->paymentAmounts[$regId] !== ''
                ? (float)$this->paymentAmounts[$regId]
                : $remaining;

            if ($payAmount > $remaining + 0.01) {
                $this->flashError = 'Nominal setoran untuk ' . $reg->person->name . ' (Rp ' . number_format($payAmount, 0, ',', '.') . ') melebihi sisa tagihan (Maksimal Rp ' . number_format($remaining, 0, ',', '.') . ').';
                return;
            }
        }

        DB::transaction(function () {
            foreach ($this->paymentChecks as $regId => $checked) {
                if (!$checked) continue;

                $reg = MajekRegistration::find($regId);
                if (!$reg) continue;

                $remaining = $this->getRemainingUnpaidAmount($reg->id);
                $payAmount = isset($this->paymentAmounts[$regId]) && $this->paymentAmounts[$regId] !== ''
                    ? (float)$this->paymentAmounts[$regId]
                    : $remaining;

                $payAmount = min($payAmount, $remaining);

                if ($payAmount <= 0) continue;

                $this->applyCustomPayment($reg, $payAmount);
            }
        });

        $this->resetPaymentState();
        unset($this->paidStatuses, $this->paidDetails);
        $this->flashSuccess = 'Setoran Majek berhasil disimpan.';
    }

    private function applyCustomPayment(MajekRegistration $reg, float $payAmount): void
    {
        $bills = Bill::where('reference_id', $reg->id)->orderBy('bill_type', 'asc')->get();
        if ($bills->isEmpty()) {
            $this->createUnpaidBills($reg);
            $bills = Bill::where('reference_id', $reg->id)->orderBy('bill_type', 'asc')->get();
        }

        $remainingToPay = $payAmount;

        foreach ($bills as $bill) {
            if ($remainingToPay <= 0) break;
            if ($bill->status === 'paid') continue;

            $billRemaining = (float)($bill->amount - $bill->amount_paid);
            if ($billRemaining <= 0) continue;

            $allocate = min($remainingToPay, $billRemaining);

            BillPayment::create([
                'bill_id'        => $bill->id,
                'amount_paid'    => $allocate,
                'payment_date'   => now()->toDateString(),
                'payment_method' => $this->payMethod,
                'logged_by'      => auth()->id(),
                'notes'          => 'Setoran Majek ' . $this->monthLabel . ($allocate < $billRemaining ? ' (Cicilan)' : ''),
            ]);

            $bill->recalculateStatus();
            $remainingToPay -= $allocate;
        }
    }

    private function createUnpaidBills(MajekRegistration $reg): void
    {
        $period = $this->activePeriod;
        if (!$period) return;

        if (!$reg->relationLoaded('person')) {
            $reg->load('person');
        }

        $dailyRate = $period->getTarifPerHariForGender($reg->person?->gender);
        $t1x = $dailyRate * $reg->active_days;

        if ($reg->session_pagi) {
            Bill::create([
                'person_id'       => $reg->person_id,
                'bill_type'       => 'majek_pagi',
                'reference_id'    => $reg->id,
                'period_month'    => $reg->month,
                'period_year'     => $reg->year,
                'title'           => 'Katering Pagi ' . $this->monthLabel,
                'amount'          => $t1x,
                'amount_paid'     => 0,
                'status'          => 'unpaid',
                'managed_by_role' => 'bendahara-pusat',
                'created_by'      => auth()->id(),
            ]);
        }

        if ($reg->session_sore) {
            Bill::create([
                'person_id'       => $reg->person_id,
                'bill_type'       => 'majek_sore',
                'reference_id'    => $reg->id,
                'period_month'    => $reg->month,
                'period_year'     => $reg->year,
                'title'           => 'Katering Sore ' . $this->monthLabel,
                'amount'          => $t1x,
                'amount_paid'     => 0,
                'status'          => 'unpaid',
                'managed_by_role' => 'bendahara-pusat',
                'created_by'      => auth()->id(),
            ]);
        }
    }

    public function confirmRemovePeserta(string $regId, string $personName): void
    {
        $this->deleteRegId      = $regId;
        $this->deletePersonName = $personName;
        $this->showDeleteModal  = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal  = false;
        $this->deleteRegId      = null;
        $this->deletePersonName = '';
    }

    public function removePeserta(): void
    {
        if (!$this->deleteRegId) return;

        $regId = $this->deleteRegId;

        $hasPaid = Bill::where('reference_id', $regId)
                       ->where('status', 'paid')
                       ->exists();

        if ($hasPaid) {
            $this->flashError = 'Peserta tidak bisa dihapus karena tagihan katering sudah dibayar/lunas.';
            $this->closeDeleteModal();
            return;
        }

        DB::transaction(function () use ($regId) {
            // Delete associated unpaid bills
            Bill::where('reference_id', $regId)->delete();

            // Delete registration record
            MajekRegistration::where('id', $regId)->delete();
        });

        unset($this->registrations, $this->paidStatuses);
        $this->resetPaymentState();
        $this->flashSuccess = "Santri '{$this->deletePersonName}' berhasil dihapus dari pendaftaran Majek.";
        $this->closeDeleteModal();
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    public function resetPaymentState(): void
    {
        $this->paymentChecks    = [];
        $this->paymentAmounts   = [];
        $this->totalChecked     = 0.0;
        $this->countChecked     = 0;
        $this->showConfirmModal = false;
        $this->confirmCheck     = false;
    }

    // =========================================================================
    // Render
    // =========================================================================

    public function render()
    {
        return view('livewire.keuangan.majek-manager');
    }
}
