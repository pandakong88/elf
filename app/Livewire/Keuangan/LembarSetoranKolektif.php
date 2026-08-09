<?php

namespace App\Livewire\Keuangan;

use Livewire\Component;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillingConfiguration;
use App\Modules\Keuangan\Services\BillingService;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Madrasah\Models\MadrasahKelas;
use App\Modules\Madrasah\Models\MadrasahEnrollment;
use App\Modules\Core\Models\Person;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use App\Traits\HasGenderScope;

class LembarSetoranKolektif extends Component
{
    use HasGenderScope;

    public string  $search = '';
    public int     $year;
    public string  $payMethod = 'CASH';

    // Active sheet selection context
    public ?string $activeType = null;      // 'komplek' or 'madrasah'
    public ?string $activeTargetId = null;  // dormitoryId or kelasId
    public ?string $activeBillType = null;  // e.g. 'syahriah_pondok', 'kas_komplek', etc.
    public ?string $activeConfigId = null;  // billing_config_id UUID
    public ?string $activeInterval = null;  // 'monthly', 'semester', 'once'
    public ?string $activeLabel = null;
    public string  $filterConfigId = 'all'; // Filter by specific billing config ID

    // Amount-based payment inputs
    public array   $paymentAmounts = [];     // Keyed by bill_id
    public array   $oldArrearsPayments = []; // Keyed by student_id

    // Computed totals
    public float $totalChecked = 0.0;
    public int   $countChecked = 0;

    // Confirmation Modal States
    public bool  $showConfirmModal = false;
    public bool  $confirmCheck = false;
    public bool  $showMobileNavigator = false;
    public string $mobileViewMode = 'cards'; // 'cards' or 'table'

    public function toggleMobileNavigator(): void
    {
        $this->showMobileNavigator = !$this->showMobileNavigator;
    }

    public function setMobileViewMode(string $mode): void
    {
        $this->mobileViewMode = in_array($mode, ['cards', 'table']) ? $mode : 'cards';
    }

    protected $queryString = [
        'activeType'     => ['except' => ''],
        'activeTargetId' => ['except' => ''],
        'activeBillType' => ['except' => ''],
        'activeConfigId' => ['except' => ''],
        'activeInterval' => ['except' => ''],
        'year'           => ['except' => ''],
        'filterConfigId' => ['except' => 'all'],
    ];

    public function mount(): void
    {
        $user = auth()->user();
        if ($user && ! ($user->hasRole('super-admin') || $user->hasRole('pengasuh') || $user->hasRole('manajemen') || $user->hasRole('bendahara-pondok') || $user->hasPermissionTo('manage-setoran-kolektif'))) {
            abort(403, 'Anda tidak memiliki akses ke Lembar Setoran Kolektif.');
        }

        $this->year = (int) now()->format('Y');
    }

    public function updatedSearch(): void
    {
        // Search updating doesn't reset sheet selection but we could clear filter inputs if needed.
    }

    public function updatedYear(): void
    {
        // Do not reset inputs when year changes, to allow cross-year payment checklists.
    }

    public function incrementYear(): void
    {
        $this->year++;
    }

    public function decrementYear(): void
    {
        $this->year--;
    }

    public function selectSheet(string $type, string $targetId, string $billType, string $interval, string $label, ?string $configId = null): void
    {
        $this->activeType = $type;
        $this->activeTargetId = $targetId;
        $this->activeBillType = $billType;
        $this->activeInterval = $interval;
        $this->activeLabel = $label;
        $this->activeConfigId = $configId;
        $this->showMobileNavigator = false;

        $this->resetInputAmounts();
    }

    public function deselectSheet(): void
    {
        $this->activeType = null;
        $this->activeTargetId = null;
        $this->activeBillType = null;
        $this->activeInterval = null;
        $this->activeLabel = null;
        $this->activeConfigId = null;
        $this->showMobileNavigator = true;

        $this->resetInputAmounts();
    }

    public function resetInputAmounts(): void
    {
        $this->paymentAmounts = [];
        $this->oldArrearsPayments = [];
        $this->confirmCheck = false;
        $this->showConfirmModal = false;
        $this->recalculateTotals();
    }

    public function updatedFilterConfigId(): void
    {
        $this->deselectSheet();
    }
 
    /**
     * Enforce sequential payments and recalculate totals when inputs change.
     */
    public function updatedPaymentAmounts($value, $key): void
    {
        $billId = $key;
        $amountPaid = (float)$value;
 
        $bill = Bill::find($billId);
        if ($bill) {
            $remaining = (float)$bill->amount - (float)$bill->amount_paid;
            if ($amountPaid > $remaining) {
                $amountPaid = $remaining;
                $this->paymentAmounts[$billId] = $remaining;
            }

            $interval = $this->activeInterval;
            $periods = $this->getRelevantPeriods();
            $firstPeriodKey = array_key_first($periods);
            [$firstM, $firstY] = explode('-', $firstPeriodKey);
            $configId = $this->activeConfigId ?: $this->config?->id;
 
            if ($amountPaid <= 0) {
                // Deselecting / clearing: auto-deselect all newer unpaid bills of the same santri and billing config
                $subsequentBills = Bill::where('person_id', $bill->person_id)
                    ->when($configId, fn($q) => $q->where('billing_config_id', $configId), fn($q) => $q->where('bill_type', $bill->bill_type))
                    ->whereIn('status', ['unpaid', 'partial'])
                    ->get();
                
                foreach ($subsequentBills as $sb) {
                    $date1 = $sb->due_date ? $sb->due_date->toDateString() : sprintf('%04d-%02d-01', $sb->period_year, $sb->period_month);
                    $date2 = $bill->due_date ? $bill->due_date->toDateString() : sprintf('%04d-%02d-01', $bill->period_year, $bill->period_month);
                    
                    $isNewer = false;
                    if ($date1 !== $date2) {
                        $isNewer = $date1 > $date2;
                    } else {
                        $isNewer = $sb->created_at >= $bill->created_at;
                    }
                    
                    if ($isNewer && isset($this->paymentAmounts[$sb->id])) {
                        $this->paymentAmounts[$sb->id] = 0;
                    }
                }
            } else {
                // Selecting / filling: auto-select/fill all prior unpaid bills of the same santri and billing config
                $oldArrearsQuery = Bill::where('person_id', $bill->person_id)
                    ->when($configId, fn($q) => $q->where('billing_config_id', $configId), fn($q) => $q->where('bill_type', $bill->bill_type))
                    ->whereIn('status', ['unpaid', 'partial']);
 
                if (in_array($interval, ['once', 'insidental', 'event', 'sekali'])) {
                    $oldArrearsQuery->where('period_year', '<', (int)$firstY);
                } else {
                    $oldArrearsQuery->where(function($q) use ($firstM, $firstY) {
                        $q->where('period_year', '<', (int)$firstY)
                          ->orWhere(function($sub) use ($firstM, $firstY) {
                              $sub->where('period_year', (int)$firstY)
                                  ->where('period_month', '<', (int)$firstM);
                          });
                    });
                }
                
                $oldArrearsSum = $oldArrearsQuery->get()->sum(fn($b) => $b->amount - $b->amount_paid);
                
                if ($oldArrearsSum > 0 && (!isset($this->oldArrearsPayments[$bill->person_id]) || (float)$this->oldArrearsPayments[$bill->person_id] < $oldArrearsSum)) {
                    $this->oldArrearsPayments[$bill->person_id] = $oldArrearsSum;
                }
                
                // Auto-fill prior grid bills
                $priorBills = Bill::where('person_id', $bill->person_id)
                    ->when($configId, fn($q) => $q->where('billing_config_id', $configId), fn($q) => $q->where('bill_type', $bill->bill_type))
                    ->whereIn('status', ['unpaid', 'partial'])
                    ->get();
                
                foreach ($priorBills as $pb) {
                    if ($pb->id === $bill->id) continue;
                    
                    $date1 = $pb->due_date ? $pb->due_date->toDateString() : sprintf('%04d-%02d-01', $pb->period_year, $pb->period_month);
                    $date2 = $bill->due_date ? $bill->due_date->toDateString() : sprintf('%04d-%02d-01', $bill->period_year, $bill->period_month);
                    
                    $isOlder = false;
                    if ($date1 !== $date2) {
                        $isOlder = $date1 < $date2;
                    } else {
                        $isOlder = $pb->created_at < $bill->created_at;
                    }
                    
                    if ($isOlder) {
                        $rem = (float)$pb->amount - (float)$pb->amount_paid;
                        $this->paymentAmounts[$pb->id] = $rem;
                    }
                }
            }
        }
 
        $this->recalculateTotals();
    }
 
    public function updatedOldArrearsPayments($value, $key): void
    {
        $studentId = $key;
        $amountPaid = (float)$value;
        $configId = $this->activeConfigId ?: $this->config?->id;

        // Calculate max arrears sum
        $oldArrearsQuery = Bill::where('person_id', $studentId)
            ->when($configId, fn($q) => $q->where('billing_config_id', $configId), fn($q) => $q->where('bill_type', $this->activeBillType))
            ->whereIn('status', ['unpaid', 'partial']);

        $periods = $this->getRelevantPeriods();
        $firstPeriodKey = array_key_first($periods);
        [$firstM, $firstY] = explode('-', $firstPeriodKey);
        $interval = $this->activeInterval;

        if (in_array($interval, ['once', 'insidental', 'event', 'sekali'])) {
            $oldArrearsQuery->where('period_year', '<', (int)$firstY);
        } else {
            $oldArrearsQuery->where(function($q) use ($firstM, $firstY) {
                $q->where('period_year', '<', (int)$firstY)
                  ->orWhere(function($sub) use ($firstM, $firstY) {
                      $sub->where('period_year', (int)$firstY)
                          ->where('period_month', '<', (int)$firstM);
                  });
            });
        }

        $oldArrearsSum = $oldArrearsQuery->get()->sum(fn($b) => $b->amount - $b->amount_paid);

        if ($amountPaid > $oldArrearsSum) {
            $amountPaid = $oldArrearsSum;
            $this->oldArrearsPayments[$studentId] = $oldArrearsSum;
        }
        
        if ($amountPaid <= 0) {
            // Clearing old arrears: clear all grid payments for this student too
            $studentBills = Bill::where('person_id', $studentId)
                ->when($configId, fn($q) => $q->where('billing_config_id', $configId), fn($q) => $q->where('bill_type', $this->activeBillType))
                ->pluck('id')
                ->toArray();
            
            foreach ($studentBills as $bId) {
                if (isset($this->paymentAmounts[$bId])) {
                    $this->paymentAmounts[$bId] = 0;
                }
            }
        }
        $this->recalculateTotals();
    }

    public function recalculateTotals(): void
    {
        $this->totalChecked = 0.0;
        $this->countChecked = 0;

        foreach ($this->paymentAmounts as $billId => $amount) {
            $val = (float)$amount;
            if ($val > 0) {
                $this->totalChecked += $val;
                $this->countChecked++;
            }
        }

        foreach ($this->oldArrearsPayments as $studentId => $amount) {
            $val = (float)$amount;
            if ($val > 0) {
                $this->totalChecked += $val;
                $this->countChecked++;
            }
        }
    }

    public function toggleBillFullPayment(string $billId, float $remainingAmount): void
    {
        $currentVal = isset($this->paymentAmounts[$billId]) ? (float)$this->paymentAmounts[$billId] : 0.0;
        $configId = $this->activeConfigId ?: $this->config?->id;
        
        if ($currentVal > 0) {
            $this->paymentAmounts[$billId] = 0;
            
            $bill = Bill::find($billId);
            if ($bill) {
                $subsequentBills = Bill::where('person_id', $bill->person_id)
                    ->when($configId, fn($q) => $q->where('billing_config_id', $configId), fn($q) => $q->where('bill_type', $bill->bill_type))
                    ->whereIn('status', ['unpaid', 'partial'])
                    ->get();
                
                foreach ($subsequentBills as $sb) {
                    $date1 = $sb->due_date ? $sb->due_date->toDateString() : sprintf('%04d-%02d-01', $sb->period_year, $sb->period_month);
                    $date2 = $bill->due_date ? $bill->due_date->toDateString() : sprintf('%04d-%02d-01', $bill->period_year, $bill->period_month);
                    
                    $isNewer = false;
                    if ($date1 !== $date2) {
                        $isNewer = $date1 > $date2;
                    } else {
                        $isNewer = $sb->created_at >= $bill->created_at;
                    }
                    
                    if ($isNewer && isset($this->paymentAmounts[$sb->id])) {
                        $this->paymentAmounts[$sb->id] = 0;
                    }
                }
            }
        } else {
            $this->paymentAmounts[$billId] = $remainingAmount;
            
            $bill = Bill::find($billId);
            if ($bill) {
                $interval = $this->activeInterval;
                $periods = $this->getRelevantPeriods();
                $firstPeriodKey = array_key_first($periods);
                [$firstM, $firstY] = explode('-', $firstPeriodKey);
                
                // Auto-fill Old Arrears (Tunggakan Lama) if any
                $oldArrearsQuery = Bill::where('person_id', $bill->person_id)
                    ->when($configId, fn($q) => $q->where('billing_config_id', $configId), fn($q) => $q->where('bill_type', $bill->bill_type))
                    ->whereIn('status', ['unpaid', 'partial']);
 
                if (in_array($interval, ['once', 'insidental', 'event', 'sekali'])) {
                    $oldArrearsQuery->where('period_year', '<', (int)$firstY);
                } else {
                    $oldArrearsQuery->where(function($q) use ($firstM, $firstY) {
                        $q->where('period_year', '<', (int)$firstY)
                          ->orWhere(function($sub) use ($firstM, $firstY) {
                              $sub->where('period_year', (int)$firstY)
                                  ->where('period_month', '<', (int)$firstM);
                          });
                    });
                }
                
                $oldArrearsSum = $oldArrearsQuery->get()->sum(fn($b) => $b->amount - $b->amount_paid);
                
                if ($oldArrearsSum > 0 && (!isset($this->oldArrearsPayments[$bill->person_id]) || (float)$this->oldArrearsPayments[$bill->person_id] < $oldArrearsSum)) {
                    $this->oldArrearsPayments[$bill->person_id] = $oldArrearsSum;
                }
                
                // Auto-fill prior grid bills
                $priorBills = Bill::where('person_id', $bill->person_id)
                    ->when($configId, fn($q) => $q->where('billing_config_id', $configId), fn($q) => $q->where('bill_type', $bill->bill_type))
                    ->whereIn('status', ['unpaid', 'partial'])
                    ->get();
                
                foreach ($priorBills as $pb) {
                    if ($pb->id === $bill->id) continue;
                    
                    $date1 = $pb->due_date ? $pb->due_date->toDateString() : sprintf('%04d-%02d-01', $pb->period_year, $pb->period_month);
                    $date2 = $bill->due_date ? $bill->due_date->toDateString() : sprintf('%04d-%02d-01', $bill->period_year, $bill->period_month);
                    
                    $isOlder = false;
                    if ($date1 !== $date2) {
                        $isOlder = $date1 < $date2;
                    } else {
                        $isOlder = $pb->created_at < $bill->created_at;
                    }
                    
                    if ($isOlder) {
                        $rem = (float)$pb->amount - (float)$pb->amount_paid;
                        $this->paymentAmounts[$pb->id] = $rem;
                    }
                }
            }
        }
        $this->recalculateTotals();
    }

    public function toggleOldArrearsFullPayment(string $studentId, float $totalArrears): void
    {
        $currentVal = isset($this->oldArrearsPayments[$studentId]) ? (float)$this->oldArrearsPayments[$studentId] : 0.0;
        $configId = $this->activeConfigId ?: $this->config?->id;
 
        if ($currentVal > 0) {
            $this->oldArrearsPayments[$studentId] = 0;
            
            $studentBills = Bill::where('person_id', $studentId)
                ->when($configId, fn($q) => $q->where('billing_config_id', $configId), fn($q) => $q->where('bill_type', $this->activeBillType))
                ->pluck('id')
                ->toArray();
            
            foreach ($studentBills as $bId) {
                if (isset($this->paymentAmounts[$bId])) {
                    $this->paymentAmounts[$bId] = 0;
                }
            }
        } else {
            $this->oldArrearsPayments[$studentId] = $totalArrears;
        }
        $this->recalculateTotals();
    }

    public function getConfigProperty()
    {
        if (!$this->activeBillType) return null;
        return BillingConfiguration::where('type', $this->activeBillType)
            ->where('is_active', true)
            ->first();
    }

    public function getPrintUrlProperty(): ?string
    {
        if (!$this->activeBillType || !$this->activeTargetId) {
            return null;
        }

        if ($this->activeType === 'komplek') {
            $config = $this->config;
            if (!$config) return null;
            return route('print.checklist-config', [
                'id' => $config->id,
                'dormitory_id' => $this->activeTargetId,
                'year' => $this->year
            ]);
        } else {
            return route('print.checklist-kelas', [
                'kelas_id' => $this->activeTargetId,
                'bill_type' => $this->activeBillType,
                'year' => $this->year
            ]);
        }
    }

    public function getPreviewDataProperty(): Collection
    {
        $preview = collect();

        // 1. Gather all student IDs involved
        $checkedBillIds = [];
        foreach ($this->paymentAmounts as $billId => $amount) {
            if ((float)$amount > 0) {
                $checkedBillIds[] = $billId;
            }
        }

        $oldArrearsStudentIds = [];
        foreach ($this->oldArrearsPayments as $studentId => $amount) {
            if ((float)$amount > 0) {
                $oldArrearsStudentIds[] = $studentId;
            }
        }

        $billStudentIds = [];
        if (!empty($checkedBillIds)) {
            $billStudentIds = Bill::whereIn('id', $checkedBillIds)
                ->pluck('person_id')
                ->toArray();
        }

        $studentIds = array_unique(array_merge($oldArrearsStudentIds, $billStudentIds));

        if (empty($studentIds)) {
            return $preview;
        }

        // 2. Fetch persons and their room assignments
        $persons = Person::select('persons.*', 'rooms.name as room_name')
            ->leftJoin('room_assignments', function($join) {
                $join->on('room_assignments.person_id', '=', 'persons.id')
                     ->where('room_assignments.is_active', true);
            })
            ->leftJoin('rooms', 'rooms.id', '=', 'room_assignments.room_id')
            ->whereIn('persons.id', $studentIds)
            ->get()
            ->keyBy('id');

        $bills = collect();
        if (!empty($checkedBillIds)) {
            $bills = Bill::whereIn('id', $checkedBillIds)->get()->keyBy('id');
        }

        foreach ($studentIds as $studentId) {
            $person = $persons->get($studentId);
            if (!$person) continue;

            $items = [];
            $studentTotal = 0.0;

            // Old Arrears
            $oldArrears = isset($this->oldArrearsPayments[$studentId]) ? (float)$this->oldArrearsPayments[$studentId] : 0.0;
            if ($oldArrears > 0) {
                $items[] = "Tunggakan Lama (Rp " . number_format($oldArrears, 0, ',', '.') . ")";
                $studentTotal += $oldArrears;
            }

            // Grid Bills
            $gridBillItems = [];
            foreach ($this->paymentAmounts as $billId => $amount) {
                $payAmt = (float)$amount;
                if ($payAmt <= 0) continue;

                $bill = $bills->get($billId);
                if ($bill && $bill->person_id === $studentId) {
                    $periodLabel = '';
                    if (in_array($this->activeInterval, ['semester', '2x_yearly'])) {
                        $semNum = ($bill->period_month >= 7) ? 2 : 1;
                        $periodLabel = "Sem " . $semNum;
                    } elseif (in_array($this->activeInterval, ['caturwulan', '3x_yearly'])) {
                        $cwNum = (int)ceil($bill->period_month / 4);
                        $periodLabel = "Caturwulan " . $cwNum;
                    } elseif (in_array($this->activeInterval, ['triwulan', '4x_yearly'])) {
                        $twNum = (int)ceil($bill->period_month / 3);
                        $periodLabel = "Triwulan " . $twNum;
                    } elseif (in_array($this->activeInterval, ['bimulanan', '6x_yearly'])) {
                        $bmNum = (int)ceil($bill->period_month / 2);
                        $periodLabel = "Bimulanan " . $bmNum;
                    } elseif (in_array($this->activeInterval, ['once', 'insidental', 'event', 'sekali'])) {
                        $periodLabel = "Event";
                    } else {
                        $date = \Carbon\Carbon::create($bill->period_year, $bill->period_month, 1);
                        $periodLabel = $date->locale('id')->translatedFormat('M');
                    }
                    
                    $periodLabel .= " " . $bill->period_year;
                    $gridBillItems[] = $periodLabel . " (Rp " . number_format($payAmt, 0, ',', '.') . ")";
                    $studentTotal += $payAmt;
                }
            }

            if (!empty($gridBillItems)) {
                $items[] = implode(', ', $gridBillItems);
            }

            if ($studentTotal > 0) {
                $preview->push([
                    'person_name' => $person->name,
                    'room_name' => $person->room_name,
                    'details' => implode('; ', $items),
                    'total' => $studentTotal,
                ]);
            }
        }

        return $preview;
    }

    public function getRelevantPeriods(): array
    {
        if (!$this->activeBillType) return [];
        $interval = $this->activeInterval;
        $periods = [];

        if (in_array($interval, ['semester', '2x_yearly'])) {
            $periods["1-{$this->year}"] = "Semester 1";
            $periods["7-{$this->year}"] = "Semester 2";
        } elseif (in_array($interval, ['caturwulan', '3x_yearly'])) {
            $periods["1-{$this->year}"] = "Caturwulan 1";
            $periods["5-{$this->year}"] = "Caturwulan 2";
            $periods["9-{$this->year}"] = "Caturwulan 3";
        } elseif (in_array($interval, ['triwulan', '4x_yearly'])) {
            $periods["1-{$this->year}"] = "Triwulan 1";
            $periods["4-{$this->year}"] = "Triwulan 2";
            $periods["7-{$this->year}"] = "Triwulan 3";
            $periods["10-{$this->year}"] = "Triwulan 4";
        } elseif (in_array($interval, ['bimulanan', '6x_yearly'])) {
            $periods["1-{$this->year}"] = "Bimulanan 1";
            $periods["3-{$this->year}"] = "Bimulanan 2";
            $periods["5-{$this->year}"] = "Bimulanan 3";
            $periods["7-{$this->year}"] = "Bimulanan 4";
            $periods["9-{$this->year}"] = "Bimulanan 5";
            $periods["11-{$this->year}"] = "Bimulanan 6";
        } elseif (in_array($interval, ['once', 'insidental', 'event', 'sekali', 'yearly'])) {
            $periods["1-{$this->year}"] = $this->config?->label ?? str_replace('_', ' ', $this->activeBillType);
        } else {
            // monthly
            for ($m = 1; $m <= 12; $m++) {
                $key  = $m . '-' . $this->year;
                $periods[$key] = \Carbon\Carbon::create($this->year, $m, 1)
                    ->locale('id')
                    ->translatedFormat('M');
            }
        }

        return $periods;
    }

    protected function applyManagerRoleScope($query)
    {
        $user = auth()->user();
        if (!$user) return $query;

        // Central roles bypass unit scoping
        if ($user->hasRole('super-admin') || $user->hasRole('pengasuh') || $user->hasRole('manajemen') || $user->hasRole('bendahara-pondok')) {
            return $query;
        }

        $userRoles = $user->roles->pluck('name')->toArray();
        $userId = $user->id;

        return $query->where(function($sub) use ($userRoles, $userId) {
            $sub->whereNull('manager_role')->whereNull('manager_ids');
            $sub->orWhereJsonContains('manager_ids', $userId);
            foreach ($userRoles as $role) {
                $sub->orWhere('manager_role', 'like', '%' . $role . '%');
            }
        });
    }

    protected function isTargetMatched(BillingConfiguration $config, string $targetId, string $targetType): bool
    {
        if ($config->target_type === 'all') {
            return true;
        }

        if ($config->target_type !== $targetType) {
            return false;
        }

        $filters = $config->target_filters;
        if (empty($filters)) {
            return true;
        }

        if (is_array($filters) && isset($filters['ids'])) {
            $ids = (array)$filters['ids'];
            return empty($ids) || in_array($targetId, $ids);
        }

        if (is_array($filters) && !array_is_list($filters)) {
            return true;
        }

        return in_array($targetId, (array)$filters);
    }

    public function getSheetsList(): Collection
    {
        $sheets = collect();
 
        // Fetch filtered active billing configurations directly from DB with Manager Role Scoping
        $query = BillingConfiguration::where('is_active', true)
            ->when($this->filterConfigId !== 'all', fn($q) => $q->where('id', $this->filterConfigId));

        $query = $this->applyManagerRoleScope($query);
        $configs = $query->get();
 
        // 1. Komplek Sheets (for configs that target dormitory or all or santri)
        $dormitories = Dormitory::when($this->genderScope(), fn($q, $g) => $q->where('gender', $g))
            ->orderBy('name')
            ->get();
        $komplekConfigs = $configs->filter(fn($c) => $c->target_type !== 'kelas');
 
        foreach ($dormitories as $d) {
            foreach ($komplekConfigs as $config) {
                if (!$this->isTargetMatched($config, $d->id, 'dormitory')) {
                    continue;
                }

                $sheets->push([
                    'type' => 'komplek',
                    'target_id' => $d->id,
                    'target_name' => $d->name,
                    'bill_type' => $config->type,
                    'config_id' => $config->id,
                    'label' => "{$config->label} — {$d->name}",
                    'interval' => $config->interval ?? 'monthly',
                    'config_label' => $config->label,
                ]);
            }
        }
 
        // 2. Madrasah Sheets (for configs that target kelas)
        $kelasListQuery = MadrasahKelas::where('is_active', true);
        if ($gScope = $this->genderScope()) {
            $keyword = $gScope === 'L' ? 'Putra' : 'Putri';
            $kelasListQuery->where(function($q) use ($gScope, $keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                  ->orWhereHas('activeEnrollments.person', fn($pq) => $pq->where('gender', $gScope));
            });
        }
        $kelasList = $kelasListQuery->orderBy('jenjang')->orderBy('name')->get();
        $madrasahConfigs = $configs->filter(fn($c) => $c->target_type === 'kelas');
 
        foreach ($kelasList as $k) {
            foreach ($madrasahConfigs as $config) {
                if (!$this->isTargetMatched($config, $k->id, 'kelas')) {
                    continue;
                }

                $sheets->push([
                    'type' => 'madrasah',
                    'target_id' => $k->id,
                    'target_name' => "Kelas {$k->jenjang} - {$k->name}",
                    'bill_type' => $config->type,
                    'config_id' => $config->id,
                    'label' => "{$config->label} — Kelas {$k->jenjang} - {$k->name}",
                    'interval' => $config->interval ?? 'semester',
                    'config_label' => $config->label,
                ]);
            }
        }
 
        if (!empty($this->search)) {
            $searchLower = strtolower($this->search);
            $sheets = $sheets->filter(function ($s) use ($searchLower) {
                return str_contains(strtolower($s['label']), $searchLower) ||
                       str_contains(strtolower($s['target_name']), $searchLower) ||
                       str_contains(strtolower($s['config_label']), $searchLower);
            });
        }
 
        return $sheets->values();
    }

    public function getGridData(): Collection
    {
        if (!$this->activeType || !$this->activeTargetId || !$this->activeBillType) {
            return collect();
        }

        // Query students based on type
        if ($this->activeType === 'komplek') {
            $query = Person::select('persons.*', 'rooms.name as room_name')
                ->whereHas('activeRoles', fn($q) =>
                    $q->where('role_type', 'santri')->where('enrollment_status', 'aktif')
                )
                ->join('room_assignments', 'room_assignments.person_id', '=', 'persons.id')
                ->join('rooms', 'rooms.id', '=', 'room_assignments.room_id')
                ->where('rooms.dormitory_id', $this->activeTargetId)
                ->where('room_assignments.is_active', true)
                ->where('room_assignments.valid_from', '<=', now())
                ->where(function ($sub) {
                    $sub->whereNull('room_assignments.valid_until')
                        ->orWhere('room_assignments.valid_until', '>=', now());
                })
                ->when($this->genderScope(), fn($q, $g) => $q->where('persons.gender', $g))
                ->orderBy('rooms.name')
                ->orderBy('persons.name');
            $santriList = $query->get();
        } else {
            $santriIds = MadrasahEnrollment::where('kelas_id', $this->activeTargetId)
                ->where('is_active', true)
                ->pluck('person_id');

            if ($santriIds->isEmpty()) return collect();

            $query = Person::whereIn('id', $santriIds)
                ->when($this->genderScope(), fn($q, $g) => $q->where('gender', $g))
                ->orderBy('name');
            $santriList = $query->get();
        }

        $periods = $this->getRelevantPeriods();
        $interval = $this->activeInterval;
        $configId = $this->activeConfigId ?: $this->config?->id;
 
        return $santriList->map(function ($santri) use ($periods, $interval, $configId) {
            $bills = [];
            foreach ($periods as $periodKey => $periodLabel) {
                [$m, $y] = explode('-', $periodKey);
                
                $billQuery = Bill::where('person_id', $santri->id)
                    ->when($configId, fn($q) => $q->where('billing_config_id', $configId), fn($q) => $q->where('bill_type', $this->activeBillType))
                    ->where('period_year', (int)$y);
                
                if (!in_array($interval, ['once', 'insidental', 'event', 'sekali'])) {
                    $billQuery->where('period_month', (int)$m);
                }
                
                $bill = $billQuery->first();
                
                $bills[$periodKey] = [
                    'label' => $periodLabel,
                    'bill'  => $bill,
                ];
            }

            // Calculate oldest display boundary
            $firstPeriodKey = array_key_first($periods);
            [$firstM, $firstY] = explode('-', $firstPeriodKey);

            $tunggakanLamaQuery = Bill::where('person_id', $santri->id)
                ->when($configId, fn($q) => $q->where('billing_config_id', $configId), fn($q) => $q->where('bill_type', $this->activeBillType))
                ->whereIn('status', ['unpaid', 'partial']);

            if (in_array($interval, ['once', 'insidental', 'event', 'sekali'])) {
                $tunggakanLamaQuery->where('period_year', '<', (int)$firstY);
            } else {
                $tunggakanLamaQuery->where(function($q) use ($firstM, $firstY) {
                    $q->where('period_year', '<', (int)$firstY)
                      ->orWhere(function($sub) use ($firstM, $firstY) {
                          $sub->where('period_year', (int)$firstY)
                              ->where('period_month', '<', (int)$firstM);
                      });
                });
            }

            $tunggakanLamaSum = $tunggakanLamaQuery->get()->sum(fn($b) => $b->amount - $b->amount_paid);

            // Query prepaid until label (furthest future paid month)
            $furthestPaidQuery = Bill::where('person_id', $santri->id)
                ->when($configId, fn($q) => $q->where('billing_config_id', $configId), fn($q) => $q->where('bill_type', $this->activeBillType))
                ->where('status', 'paid');

            if (in_array($interval, ['once', 'insidental', 'event', 'sekali'])) {
                $furthestPaidQuery->where('period_year', '>', (int)$this->year);
            } else {
                $furthestPaidQuery->where(function($q) {
                    $q->where('period_year', '>', (int)$this->year)
                      ->orWhere(function($sub) {
                          $sub->where('period_year', (int)$this->year)
                              ->where('period_month', '>', (int)now()->month);
                      });
                });
            }

            $furthestPaidBill = $furthestPaidQuery->orderBy('period_year', 'desc')
                ->orderBy('period_month', 'desc')
                ->first();

            $lunasDiMukaLabel = null;
            if ($furthestPaidBill) {
                if (in_array($interval, ['semester', '2x_yearly'])) {
                    $semNum = ($furthestPaidBill->period_month >= 7) ? 2 : 1;
                    $lunasDiMukaLabel = "Sem {$semNum} / {$furthestPaidBill->period_year}";
                } elseif (in_array($interval, ['caturwulan', '3x_yearly'])) {
                    $cwNum = (int)ceil($furthestPaidBill->period_month / 4);
                    $lunasDiMukaLabel = "Caturwulan {$cwNum} / {$furthestPaidBill->period_year}";
                } elseif (in_array($interval, ['triwulan', '4x_yearly'])) {
                    $twNum = (int)ceil($furthestPaidBill->period_month / 3);
                    $lunasDiMukaLabel = "Triwulan {$twNum} / {$furthestPaidBill->period_year}";
                } elseif (in_array($interval, ['bimulanan', '6x_yearly'])) {
                    $bmNum = (int)ceil($furthestPaidBill->period_month / 2);
                    $lunasDiMukaLabel = "Bimulanan {$bmNum} / {$furthestPaidBill->period_year}";
                } elseif (in_array($interval, ['once', 'insidental', 'event', 'sekali'])) {
                    $lunasDiMukaLabel = "Tahun {$furthestPaidBill->period_year}";
                } else {
                    $lunasDiMukaLabel = \Carbon\Carbon::create($furthestPaidBill->period_year, $furthestPaidBill->period_month, 1)
                        ->locale('id')
                        ->translatedFormat('M Y');
                }
            }

            return [
                'person' => $santri,
                'bills' => $bills,
                'tunggakanLamaSum' => $tunggakanLamaSum,
                'lunasDiMukaLabel' => $lunasDiMukaLabel,
            ];
        });
    }

    public function confirmProsesSetoran(): void
    {
        $this->recalculateTotals();

        if ($this->countChecked === 0) {
            session()->flash('error', 'Tidak ada nominal setoran yang diinput.');
            return;
        }

        $this->showConfirmModal = true;
    }

    public function cancelConfirm(): void
    {
        $this->showConfirmModal = false;
        $this->confirmCheck = false;
    }

    public function prosesSetoran(): void
    {
        $this->recalculateTotals();

        if ($this->countChecked === 0) {
            session()->flash('error', 'Tidak ada nominal setoran yang diinput.');
            $this->showConfirmModal = false;
            return;
        }

        if (!$this->confirmCheck) {
            session()->flash('error', 'Anda harus menyetujui pernyataan konfirmasi terlebih dahulu.');
            return;
        }

        if ($this->activeType === 'komplek') {
            $dormitory = Dormitory::find($this->activeTargetId);
            $notes = 'Setoran Komplek ' . ($dormitory?->name ?? '') . ' — ' . $this->activeLabel;
        } else {
            $kelas = MadrasahKelas::find($this->activeTargetId);
            $notes = 'Setoran Madrasah ' . ($kelas?->name ?? '') . ' — ' . $this->activeLabel;
        }

        $service = new BillingService();
        $totalAmountProcessed = 0.0;
        $billsProcessedCount = 0;
        $configId = $this->activeConfigId ?: $this->config?->id;

        DB::transaction(function () use ($service, $notes, $configId, &$totalAmountProcessed, &$billsProcessedCount) {
            // 1. Process active month/semester payment cells
            foreach ($this->paymentAmounts as $billId => $amount) {
                $amountPaid = (float)$amount;
                if ($amountPaid <= 0) continue;

                $service->recordPayment($billId, $amountPaid, $this->payMethod, $notes, auth()->id());
                $totalAmountProcessed += $amountPaid;
                $billsProcessedCount++;
            }

            // 2. Process old arrears payments using FIFO
            $periods = $this->getRelevantPeriods();
            $firstPeriodKey = array_key_first($periods);
            [$firstM, $firstY] = explode('-', $firstPeriodKey);
            $interval = $this->activeInterval;

            foreach ($this->oldArrearsPayments as $studentId => $amount) {
                $amountToDistribute = (float)$amount;
                if ($amountToDistribute <= 0) continue;

                $oldBillsQuery = Bill::where('person_id', $studentId)
                    ->when($configId, fn($q) => $q->where('billing_config_id', $configId), fn($q) => $q->where('bill_type', $this->activeBillType))
                    ->whereIn('status', ['unpaid', 'partial']);

                if (in_array($interval, ['once', 'insidental', 'event', 'sekali'])) {
                    $oldBillsQuery->where('period_year', '<', (int)$firstY);
                } else {
                    $oldBillsQuery->where(function($q) use ($firstM, $firstY) {
                        $q->where('period_year', '<', (int)$firstY)
                          ->orWhere(function($sub) use ($firstM, $firstY) {
                              $sub->where('period_year', (int)$firstY)
                                  ->where('period_month', '<', (int)$firstM);
                          });
                    });
                }

                $oldBills = $oldBillsQuery->orderBy('period_year', 'asc')
                    ->orderBy('period_month', 'asc')
                    ->get();

                foreach ($oldBills as $oldBill) {
                    if ($amountToDistribute <= 0) break;
                    $remaining = (float)$oldBill->amount - (float)$oldBill->amount_paid;
                    $payVal = min($amountToDistribute, $remaining);

                    $service->recordPayment($oldBill->id, $payVal, $this->payMethod, $notes, auth()->id());
                    $amountToDistribute -= $payVal;
                    $totalAmountProcessed += $payVal;
                    $billsProcessedCount++;
                }
            }
        });

        // Reset inputs & close modal
        $this->resetInputAmounts();
        $this->showConfirmModal = false;
        $this->confirmCheck = false;

        session()->flash('message',
            "Berhasil mencatat setoran Rp " . number_format($totalAmountProcessed, 0, ',', '.') .
            " untuk {$billsProcessedCount} tagihan."
        );
    }

    public function render()
    {
        $sheetsList = $this->getSheetsList();
        $gridData   = $this->getGridData();
        $periods    = $this->getRelevantPeriods();
        
        $configsQuery = BillingConfiguration::where('is_active', true);
        $configsQuery = $this->applyManagerRoleScope($configsQuery);
        $activeConfigs = $configsQuery->orderBy('label')->get();

        return view('livewire.keuangan.lembar-setoran-kolektif', [
            'sheetsList' => $sheetsList,
            'gridData'   => $gridData,
            'months'     => $periods,
            'activeConfigs' => $activeConfigs,
        ])->layout('layouts.app');
    }
}
