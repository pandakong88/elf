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
use App\Modules\Kepengasuhan\Models\Room;
use App\Traits\HasGenderScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
    public string $periodNotes       = '';

    // ─── Copy Period Modal ────────────────────────────────────────────────────
    public bool   $showCopyPeriodModal = false;
    public int    $copySourceMonth     = 1;
    public int    $copySourceYear      = 2026;

    // ─── Add Participant Modal (Modes: 'bulk' | 'single') ─────────────────────
    public bool   $showAddModal          = false;
    public string $addTab                = 'bulk'; // 'bulk' | 'single'

    // ─── Preset Defaults for Adding ──────────────────────────────────────────
    public string $presetSesi            = '2x';   // '2x' | 'pagi' | 'sore'
    public int    $presetDays            = 30;

    // ─── Bulk Mode Properties ────────────────────────────────────────────────
    public string $searchBulkQuery       = '';
    public string $filterBulkDormitoryId = '';
    public string $filterBulkRoomId      = '';
    public string $filterBulkStatus      = 'unregistered'; // 'all' | 'unregistered' | 'registered'
    public array  $bulkSelections        = []; // [person_id => bool]
    public array  $bulkSessions          = []; // [person_id => '2x'|'pagi'|'sore']
    public array  $bulkDays              = []; // [person_id => int]
    public array  $bulkNotes             = []; // [person_id => string]

    // ─── Single Mode (Input Cepat 1 Santri) ───────────────────────────────────
    public string $singleSearchQuery     = '';
    public array  $singleSearchResults   = [];
    public ?string $singleSelectedPersonId = null;
    public ?array  $singleSelectedPerson = null;
    public string $singleSesi            = '2x';
    public int    $singleDays            = 30;
    public string $singleNotes           = '';

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
    public int    $perPage      = 15;

    public function updatingSearchParticipant(): void { $this->resetPage(); }
    public function updatingFilterDormitoryIds(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    protected $queryString = [
        'searchParticipant' => ['except' => ''],
        'filterDormitoryIds' => ['except' => []],
        'filterStatus' => ['except' => 'all'],
        'perPage' => ['except' => 15],
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
            ->paginate($this->perPage);
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

    public function updatedFilterBulkDormitoryId(): void
    {
        $this->filterBulkRoomId = '';
    }

    #[Computed]
    public function availableRooms()
    {
        if (empty($this->filterBulkDormitoryId)) {
            return collect();
        }

        return Room::active()
            ->where('dormitory_id', $this->filterBulkDormitoryId)
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

        $defaultDays = $this->presetDays ?: ($this->activePeriod ? $this->activePeriod->active_days : 30);
        $defaultSesi = $this->presetSesi ?: '2x';

        $persons = Person::whereIn('id', $checkedIds)
            ->when($this->genderScope(), fn($q, $g) => $q->where('gender', $g))
            ->with(['roomAssignments' => fn($q) => $q->active()->with('room.dormitory')])
            ->orderBy('name')
            ->get();

        $result = [];
        foreach ($persons as $p) {
            $dormName = '—';
            $roomName = '—';
            $activeAssignment = $p->roomAssignments->first();
            if ($activeAssignment && $activeAssignment->room) {
                $roomName = $activeAssignment->room->name;
                if ($activeAssignment->room->dormitory) {
                    $dormName = $activeAssignment->room->dormitory->name;
                }
            }

            if (!isset($this->bulkSessions[$p->id])) {
                $this->bulkSessions[$p->id] = $defaultSesi;
            }
            if (!isset($this->bulkDays[$p->id])) {
                $this->bulkDays[$p->id] = $defaultDays;
            }
            if (!isset($this->bulkNotes[$p->id])) {
                $this->bulkNotes[$p->id] = '';
            }

            $currentSession = $this->bulkSessions[$p->id] ?? $defaultSesi;
            $currentDays    = (int) ($this->bulkDays[$p->id] ?? $defaultDays);
            $dailyRate      = $this->activePeriod ? $this->activePeriod->getTarifPerHariForGender($p->gender) : 0;
            $mult           = match($currentSession) {
                '2x' => 2,
                default => 1,
            };
            $estimatedTotal = $dailyRate * $currentDays * $mult;

            $result[] = [
                'id'              => $p->id,
                'name'            => $p->name,
                'gender'          => $p->gender,
                'dormitory'       => $dormName,
                'room'            => $roomName,
                'location'        => $dormName !== '—' ? ($roomName !== '—' ? "{$dormName} - {$roomName}" : $dormName) : '—',
                'session'         => $currentSession,
                'days'            => $currentDays,
                'notes'           => $this->bulkNotes[$p->id] ?? '',
                'estimated_total' => $estimatedTotal,
            ];
        }

        return $result;
    }

    public function setAllSelectedSessions(string $sesi): void
    {
        $this->presetSesi = $sesi;
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
            ->when($this->filterBulkRoomId, function ($q) {
                $q->whereHas('roomAssignments', function ($rq) {
                    $rq->active()->where('room_id', $this->filterBulkRoomId);
                });
            })
            ->when($this->searchBulkQuery, function ($q) {
                $search = trim($this->searchBulkQuery);
                $q->where(function ($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%')
                       ->orWhere('nik', 'like', '%' . $search . '%')
                       ->orWhereHas('santriProfile', function ($sp) use ($search) {
                           $sp->where('additional_info->nis', 'like', '%' . $search . '%')
                              ->orWhere('additional_info->nisn', 'like', '%' . $search . '%');
                       });
                });
            })
            ->with(['roomAssignments' => fn($q) => $q->active()->with('room.dormitory')])
            ->orderBy('name');

        $students = $query->limit(500)->get();
        $defaultDays = $this->presetDays ?: ($this->activePeriod ? $this->activePeriod->active_days : 30);
        $result = [];

        foreach ($students as $student) {
            $reg = $registrationsMap->get($student->id);
            $isReg = !is_null($reg);

            if ($this->filterBulkStatus === 'unregistered' && $isReg) continue;
            if ($this->filterBulkStatus === 'registered' && !$isReg) continue;

            $dormName = '—';
            $roomName = '—';
            $activeAssignment = $student->roomAssignments->first();
            if ($activeAssignment && $activeAssignment->room) {
                $roomName = $activeAssignment->room->name;
                if ($activeAssignment->room->dormitory) {
                    $dormName = $activeAssignment->room->dormitory->name;
                }
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
                'room'          => $roomName,
                'location'      => $dormName !== '—' ? ($roomName !== '—' ? "{$dormName} - {$roomName}" : $dormName) : '—',
                'is_registered' => $isReg,
                'session'       => $sesi,
                'days'          => $isReg ? $reg->active_days : $defaultDays,
                'notes'         => $isReg ? ($reg->notes ?? '') : '',
            ];
        }

        return $result;
    }

    public function selectAllFilteredStudents(): void
    {
        $students = $this->bulkStudentsList;
        $defaultDays = $this->presetDays ?: ($this->activePeriod ? $this->activePeriod->active_days : 30);
        $defaultSesi = $this->presetSesi ?: '2x';

        foreach ($students as $std) {
            if (!$std['is_registered']) {
                $this->bulkSelections[$std['id']] = true;
                if (!isset($this->bulkSessions[$std['id']])) {
                    $this->bulkSessions[$std['id']] = $defaultSesi;
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

        $defaultDays = $this->presetDays ?: ($this->activePeriod ? $this->activePeriod->active_days : 30);
        $defaultSesi = $this->presetSesi ?: '2x';

        if ($this->bulkSelections[$studentId]) {
            if (!isset($this->bulkSessions[$studentId])) {
                $this->bulkSessions[$studentId] = $defaultSesi;
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
        $this->showPeriodModal         = true;
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
    // Add Participant Modal (Modes: 'bulk' | 'single')
    // =========================================================================

    public function openAddModal(): void
    {
        if (!$this->activePeriod) {
            $this->flashError = 'Buat konfigurasi periode terlebih dahulu sebelum mendaftarkan peserta.';
            return;
        }

        $this->addTab                = 'bulk';
        $this->presetSesi            = '2x';
        $this->presetDays            = $this->activePeriod->active_days;

        // Reset Bulk State
        $this->searchBulkQuery       = '';
        $this->filterBulkDormitoryId = '';
        $this->filterBulkRoomId      = '';
        $this->filterBulkStatus      = 'unregistered';
        $this->bulkSelections        = [];
        $this->bulkSessions          = [];
        $this->bulkDays              = [];
        $this->bulkNotes             = [];

        // Reset Single State
        $this->clearSingleSelectedStudent();

        $this->showAddModal          = true;
    }

    public function closeAddModal(): void
    {
        $this->showAddModal = false;
    }

    public function switchTab(string $tab): void
    {
        $this->addTab = in_array($tab, ['bulk', 'single']) ? $tab : 'bulk';
        $this->flashError = '';
        $this->flashSuccess = '';
    }

    public function uncheckStudent(string $studentId): void
    {
        $this->bulkSelections[$studentId] = false;
    }

    public function addPesertaBulk(): void
    {
        $period = $this->activePeriod;
        if (!$period) {
            $this->flashError = 'Periode Majek belum dikonfigurasi.';
            return;
        }

        $selectedPersonIds = array_keys(array_filter($this->bulkSelections));
        if (empty($selectedPersonIds)) {
            $this->flashError = 'Pilih minimal satu santri untuk didaftarkan.';
            return;
        }

        $registeredIds = MajekRegistration::where('month', $this->month)
            ->where('year',  $this->year)
            ->pluck('person_id')
            ->toArray();

        // Enforce gender scope when querying persons
        $persons = Person::whereIn('id', $selectedPersonIds)
            ->when($this->genderScope(), fn($q, $g) => $q->where('gender', $g))
            ->get()
            ->keyBy('id');

        $addedCount = 0;

        DB::transaction(function () use (&$addedCount, $period, $registeredIds, $persons) {
            foreach ($this->bulkSelections as $personId => $selected) {
                if (!$selected) continue;
                if (in_array($personId, $registeredIds)) continue; // skip already registered
                if (!isset($persons[$personId])) continue; // skip if not matching gender scope

                $person = $persons[$personId];
                $dailyRate = $period->getTarifPerHariForGender($person->gender);

                $sesi = $this->bulkSessions[$personId] ?? $this->presetSesi ?? '2x';
                $days = (int) ($this->bulkDays[$personId] ?? $this->presetDays ?? $period->active_days);
                $notes = $this->bulkNotes[$personId] ?? '';

                $t1x = $dailyRate * $days;

                $reg = MajekRegistration::create([
                    'person_id'     => $personId,
                    'month'         => $this->month,
                    'year'          => $this->year,
                    'session_pagi'  => in_array($sesi, ['pagi', '2x']),
                    'session_sore'  => in_array($sesi, ['sore', '2x']),
                    'active_days'   => $days,
                    'amount_pagi'   => in_array($sesi, ['pagi', '2x']) ? $t1x : 0,
                    'amount_sore'   => in_array($sesi, ['sore', '2x']) ? $t1x : 0,
                    'registered_by' => auth()->id(),
                    'notes'         => $notes ?: null,
                ]);

                $this->createUnpaidBills($reg);
                $addedCount++;
            }
        });

        unset($this->registrations, $this->paidStatuses, $this->overallStats);
        $this->showAddModal = false;
        $this->bulkSelections = [];
        $this->bulkSessions = [];
        $this->bulkDays = [];
        $this->bulkNotes = [];

        if ($addedCount > 0) {
            $this->flashSuccess = "{$addedCount} peserta berhasil didaftarkan ke Majek {$this->monthLabel}.";
        } else {
            $this->flashError = "Seluruh santri terpilih sudah terdaftar sebelumnya.";
        }
    }

    public function resetFilters(): void
    {
        $this->searchParticipant  = '';
        $this->filterDormitoryIds = [];
        $this->filterStatus       = 'all';
        $this->resetPage();
    }

    // =========================================================================
    // Single Participant Logic (Mode Cepat 1 Santri)
    // =========================================================================

    public function updatedSingleSearchQuery(): void
    {
        $this->flashError = '';
        $query = trim($this->singleSearchQuery);
        if (strlen($query) < 2) {
            $this->singleSearchResults = [];
            return;
        }

        $registeredIds = MajekRegistration::where('month', $this->month)
            ->where('year',  $this->year)
            ->pluck('person_id')
            ->toArray();

        $results = Person::active()
            ->whereHas('activeRoles', fn($q) => $q->where('role_type', 'santri'))
            ->when($this->genderScope(), fn($q, $g) => $q->where('gender', $g))
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', '%' . $query . '%')
                  ->orWhere('nik', 'LIKE', '%' . $query . '%')
                  ->orWhereHas('santriProfile', function ($sp) use ($query) {
                      $sp->where('additional_info->nis', 'like', '%' . $query . '%')
                         ->orWhere('additional_info->nisn', 'like', '%' . $query . '%');
                  });
            })
            ->with(['roomAssignments' => fn($q) => $q->active()->with('room.dormitory')])
            ->orderBy('name')
            ->limit(10)
            ->get();

        $this->singleSearchResults = $results->map(function ($p) use ($registeredIds) {
            $dormName = '—';
            $roomName = '—';
            $activeAssignment = $p->roomAssignments->first();
            if ($activeAssignment && $activeAssignment->room) {
                $roomName = $activeAssignment->room->name;
                if ($activeAssignment->room->dormitory) {
                    $dormName = $activeAssignment->room->dormitory->name;
                }
            }
            return [
                'id'            => $p->id,
                'name'          => $p->name,
                'gender'        => $p->gender,
                'dormitory'     => $dormName,
                'room'          => $roomName,
                'location'      => $dormName !== '—' ? ($roomName !== '—' ? "{$dormName} - {$roomName}" : $dormName) : '—',
                'is_registered' => in_array($p->id, $registeredIds),
            ];
        })->toArray();
    }

    public function selectSingleStudent(string $personId): void
    {
        $person = Person::with(['roomAssignments' => fn($q) => $q->active()->with('room.dormitory')])
            ->find($personId);

        if (!$person) return;

        $registered = MajekRegistration::where('month', $this->month)
            ->where('year',  $this->year)
            ->where('person_id', $personId)
            ->exists();

        $dormName = '—';
        $roomName = '—';
        $activeAssignment = $person->roomAssignments->first();
        if ($activeAssignment && $activeAssignment->room) {
            $roomName = $activeAssignment->room->name;
            if ($activeAssignment->room->dormitory) {
                $dormName = $activeAssignment->room->dormitory->name;
            }
        }

        $this->singleSelectedPersonId = $personId;
        $this->singleSelectedPerson = [
            'id'            => $person->id,
            'name'          => $person->name,
            'gender'        => $person->gender,
            'dormitory'     => $dormName,
            'room'          => $roomName,
            'location'      => $dormName !== '—' ? ($roomName !== '—' ? "{$dormName} - {$roomName}" : $dormName) : '—',
            'is_registered' => $registered,
        ];

        $this->singleSearchQuery   = $person->name;
        $this->singleSearchResults = [];
        $this->singleSesi          = '2x';
        $this->singleDays          = $this->activePeriod ? $this->activePeriod->active_days : 30;
        $this->singleNotes         = '';
    }

    public function clearSingleSelectedStudent(): void
    {
        $this->singleSelectedPersonId = null;
        $this->singleSelectedPerson   = null;
        $this->singleSearchQuery      = '';
        $this->singleSearchResults    = [];
    }

    public function addSinglePeserta(): void
    {
        if (!$this->singleSelectedPersonId) {
            $this->flashError = 'Pilih santri terlebih dahulu dari pencarian.';
            return;
        }

        $period = $this->activePeriod;
        if (!$period) {
            $this->flashError = 'Periode Majek belum dikonfigurasi.';
            return;
        }

        $exists = MajekRegistration::where('person_id', $this->singleSelectedPersonId)
            ->where('month', $this->month)
            ->where('year',  $this->year)
            ->exists();

        if ($exists) {
            $this->flashError = 'Santri ini sudah terdaftar untuk periode ini.';
            return;
        }

        $days = (int) $this->singleDays;
        if ($days < 1 || $days > 31) {
            $this->flashError = 'Hari aktif katering tidak valid (1-31).';
            return;
        }

        $person = Person::find($this->singleSelectedPersonId);
        if (!$person) {
            $this->flashError = 'Data santri tidak ditemukan.';
            return;
        }

        // Strict gender scope validation:
        if ($this->genderScope() && $person->gender !== $this->genderScope()) {
            $this->flashError = 'Anda tidak memiliki akses mendaftarkan santri dengan gender berbeda dari scope Anda.';
            return;
        }

        $dailyRate = $period->getTarifPerHariForGender($person->gender);
        $t1x = $dailyRate * $days;

        DB::transaction(function () use ($person, $days, $t1x) {
            $reg = MajekRegistration::create([
                'person_id'     => $person->id,
                'month'         => $this->month,
                'year'          => $this->year,
                'session_pagi'  => in_array($this->singleSesi, ['pagi', '2x']),
                'session_sore'  => in_array($this->singleSesi, ['sore', '2x']),
                'active_days'   => $days,
                'amount_pagi'   => in_array($this->singleSesi, ['pagi', '2x']) ? $t1x : 0,
                'amount_sore'   => in_array($this->singleSesi, ['sore', '2x']) ? $t1x : 0,
                'registered_by' => auth()->id(),
                'notes'         => $this->singleNotes ?: null,
            ]);

            $this->createUnpaidBills($reg);
        });

        unset($this->registrations, $this->paidStatuses, $this->overallStats);
        $savedName = $person->name;
        $this->clearSingleSelectedStudent();
        $this->showAddModal = false;
        $this->flashSuccess = "Santri {$savedName} berhasil didaftarkan ke Majek {$this->monthLabel}.";
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

        $billingService = app(\App\Modules\Keuangan\Services\BillingService::class);
        $receiptNo      = $billingService->generateReceiptNumber();
        $paymentGroupId = (string) Str::uuid();
        $paidItems      = [];
        $totalAllocated = 0.0;
        $remainingToPay = $payAmount;

        foreach ($bills as $bill) {
            if ($remainingToPay <= 0) break;
            if ($bill->status === 'paid') continue;

            $billRemaining = (float)($bill->amount - $bill->amount_paid);
            if ($billRemaining <= 0) continue;

            $allocate = min($remainingToPay, $billRemaining);

            $billingService->recordPayment(
                billId:                    $bill->id,
                amount:                    $allocate,
                method:                    $this->payMethod,
                notes:                     'Setoran Majek ' . $this->monthLabel . ($allocate < $billRemaining ? ' (Cicilan)' : ''),
                loggedByUserId:            (string) auth()->id(),
                receiptNo:                 $receiptNo,
                paymentGroupId:            $paymentGroupId,
                tenderedAmount:            $payAmount,
                changeAmount:              0.0,
                triggerGroupNotification: false,
            );

            $remainingAfterPay = max(0, $billRemaining - $allocate);
            $paidItems[] = [
                'bill_label'   => $bill->config?->label ?? ucwords(str_replace('_', ' ', $bill->bill_type)),
                'period_label' => $this->monthLabel . ' ' . ($bill->period_year ?? $reg->year),
                'amount'       => $allocate,
                'is_partial'   => $remainingAfterPay > 0,
                'remaining'    => $remainingAfterPay,
            ];

            $totalAllocated += $allocate;
            $remainingToPay -= $allocate;
        }

        if (!empty($paidItems)) {
            $reg->loadMissing(['person.roomAssignments.room.dormitory']);
            $person = $reg->person;
            $activeAssignment = $person?->roomAssignments?->where('status', 'active')->first() ?? $person?->roomAssignments?->first();
            $dormName         = $activeAssignment?->room?->dormitory?->name;
            $roomName         = $activeAssignment?->room?->name;
            $roomLocation     = ($dormName && $roomName) ? "{$dormName} – {$roomName}" : ($dormName ?: ($roomName ?: null));

            try {
                app(\App\Services\WhatsAppService::class)->notifyKasirMultiPayment(
                    santriName:   $person?->name ?? '—',
                    receiptNo:    $receiptNo,
                    method:       $this->payMethod,
                    paidAt:       now()->locale('id')->translatedFormat('d F Y, H:i') . ' WIB',
                    totalAmount:  $totalAllocated,
                    items:        $paidItems,
                    loggedByName: auth()->user()?->name ?? 'Kasir',
                    roomLocation: $roomLocation,
                    notes:        'Setoran Majek ' . $this->monthLabel,
                    receiptUrl:   route('bukti-bayar.kuitansi', $receiptNo),
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('[MajekManager] Gagal kirim WA grup: ' . $e->getMessage());
            }
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
    // Export Excel Report
    // =========================================================================

    public function exportExcel()
    {
        $query = MajekRegistration::where('month', $this->month)
            ->where('year', $this->year)
            ->when($this->genderScope(), function ($q, $g) {
                $q->whereHas('person', fn($pq) => $pq->where('gender', $g));
            })
            ->when($this->filterDormitoryIds, function ($q) {
                $q->whereHas('person.roomAssignments', function ($rq) {
                    $rq->active()->whereHas('room', function ($r) {
                        $r->whereIn('dormitory_id', $this->filterDormitoryIds);
                    });
                });
            })
            ->when($this->searchParticipant, function ($q) {
                $q->whereHas('person', fn($pq) => $pq->where('name', 'like', '%' . $this->searchParticipant . '%'));
            })
            ->with([
                'person.roomAssignments' => fn($q) => $q->active()->with('room.dormitory'),
            ]);

        $registrations = $query->get()->sortBy(fn($r) => $r->person?->name ?? '');

        if ($registrations->isEmpty()) {
            $this->flashError = 'Tidak ada data peserta untuk di-export pada bulan ini.';
            return;
        }

        $monthName = $this->monthLabel;
        $fileName = "Laporan_Katering_Majek_{$monthName}_{$this->year}.xlsx";

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\MajekReportExport($registrations, $monthName, $this->year, $this->paidDetails),
            $fileName
        );
    }

    // =========================================================================
    // Render
    // =========================================================================

    public function render()
    {
        return view('livewire.keuangan.majek-manager');
    }
}
