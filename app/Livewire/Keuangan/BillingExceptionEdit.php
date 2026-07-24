<?php

namespace App\Livewire\Keuangan;

use Livewire\Component;
use App\Modules\Core\Models\Person;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillingConfiguration;
use App\Modules\Keuangan\Models\BillingException;
use App\Modules\Keuangan\Services\BillingService;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Madrasah\Models\MadrasahKelas;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Traits\HasGenderScope;

class BillingExceptionEdit extends Component
{
    use HasGenderScope;

    // Keys of the original group
    public ?string $configId = null;
    public ?string $type = null;
    public float $amount = 0.00;
    public ?string $notes = null;

    // Filters
    public ?string $filterGender = null;
    public ?string $filterDormitoryId = null;
    public ?string $filterKelasId = null;
    public string $filterSearch = '';

    // Form fields
    public array $excSantriIds = []; // Visible selected students matching gender scope
    public array $hiddenSantriIds = []; // Preserved hidden students (different gender)
    public ?string $excConfigId = null;
    public string $excType = 'discount';
    public float $excAmount = 0.00;
    public string $excNotes = '';

    protected $rules = [
        'excSantriIds' => 'required|array|min:1',
        'excConfigId' => 'required',
        'excType' => 'required|in:discount,waived,custom_rate',
        'excAmount' => 'required|numeric|min:0',
        'excNotes' => 'required|string|min:3',
    ];

    public function mount(): void
    {
        $this->configId = request()->query('config_id');
        $this->type = request()->query('type');
        $this->amount = (float) request()->query('amount');
        $this->notes = request()->query('notes');

        // Check if original config exists
        $config = BillingConfiguration::findOrFail($this->configId);

        $this->excConfigId = $this->configId;
        $this->excType = $this->type;
        $this->excAmount = $this->amount;
        $this->excNotes = $this->notes ?? '';

        // Fetch all student IDs currently in this group
        $allGroupStudentIds = BillingException::where('billing_config_id', $this->configId)
            ->where('exception_type', $this->type)
            ->where('amount', $this->amount)
            ->where('notes', $this->notes)
            ->pluck('person_id')
            ->toArray();

        // Split based on gender scope
        $gScope = $this->genderScope();
        if ($gScope) {
            // Find visible students (matching gender)
            $this->excSantriIds = Person::whereIn('id', $allGroupStudentIds)
                ->where('gender', $gScope)
                ->pluck('id')
                ->toArray();

            // Find hidden students (different gender)
            $this->hiddenSantriIds = Person::whereIn('id', $allGroupStudentIds)
                ->where('gender', '!=', $gScope)
                ->pluck('id')
                ->toArray();
        } else {
            // Super Admin / Manajemen see everything
            $this->excSantriIds = $allGroupStudentIds;
            $this->hiddenSantriIds = [];
        }
    }

    public function toggleSantri(string $id): void
    {
        if (in_array($id, $this->excSantriIds)) {
            $this->excSantriIds = array_values(array_diff($this->excSantriIds, [$id]));
        } else {
            $this->excSantriIds[] = $id;
        }
    }

    public function selectAllFiltered(array $ids): void
    {
        $this->excSantriIds = array_values(array_unique(array_merge($this->excSantriIds, $ids)));
    }

    public function deselectAllFiltered(array $ids): void
    {
        $this->excSantriIds = array_values(array_diff($this->excSantriIds, $ids));
    }

    public function autoSelectSiblingDiscountRecipients(): void
    {
        // Get all sibling IDs (both genders)
        $siblingIds = \App\Modules\Kepengasuhan\Models\SantriSibling::where('is_confirmed', true)
            ->where('is_eligible_for_discount', true)
            ->get()
            ->flatMap(fn($sib) => [$sib->person_id, $sib->sibling_person_id])
            ->unique()
            ->toArray();

        // Get active santri profiles matching sibling IDs
        $eligibleSiblings = Person::whereIn('id', $siblingIds)
            ->whereHas('activeRoles', function ($q) {
                $q->where('role_type', 'santri')->where('enrollment_status', 'aktif');
            })
            ->pluck('id')
            ->toArray();

        $addedCount = 0;
        foreach ($eligibleSiblings as $id) {
            if (!in_array($id, $this->excSantriIds)) {
                $this->excSantriIds[] = $id;
                $addedCount++;
            }
        }

        session()->flash('message', 'Berhasil mendeteksi otomatis ' . $addedCount . ' santri bersaudara yang berhak menerima diskon.');
    }

    public function clearSelected(): void
    {
        $this->excSantriIds = [];
    }

    public function saveException(BillingService $billingService)
    {
        $this->validate();

        $config = BillingConfiguration::findOrFail($this->excConfigId);

        // Safety check: Discount / Custom rate amount shouldn't exceed original configuration amount
        if ($this->excType === 'discount' && $this->excAmount > $config->amount) {
            $this->addError('excAmount', 'Nominal potongan tidak boleh melebihi tarif asli iuran (Rp ' . number_format($config->amount, 0, ',', '.') . ').');
            return;
        }
        if ($this->excType === 'custom_rate' && $this->excAmount > $config->amount) {
            $this->addError('excAmount', 'Tarif khusus tidak boleh melebihi tarif asli iuran (Rp ' . number_format($config->amount, 0, ',', '.') . ').');
            return;
        }

        $gScope = $this->genderScope();

        DB::transaction(function () use ($config, $billingService, $gScope) {
            // A. Process visible / targeted students
            // 1. Get original targeted student IDs of the same gender in this group
            $originalTargetedQuery = BillingException::where('billing_config_id', $this->configId)
                ->where('exception_type', $this->type)
                ->where('amount', $this->amount)
                ->where('notes', $this->notes);
            
            if ($gScope) {
                $originalTargetedQuery->whereHas('person', fn($pq) => $pq->where('gender', $gScope));
            }
            $originalTargetedStudentIds = $originalTargetedQuery->pluck('person_id')->toArray();

            // 2. Identify students who were removed from this group
            $removedStudentIds = array_diff($originalTargetedStudentIds, $this->excSantriIds);

            // 3. Delete exceptions for removed students and revert their bills retroactively
            if (!empty($removedStudentIds)) {
                BillingException::where('billing_config_id', $this->configId)
                    ->whereIn('person_id', $removedStudentIds)
                    ->delete();

                $revertBills = Bill::whereIn('person_id', $removedStudentIds)
                    ->where('billing_config_id', $this->configId)
                    ->whereIn('status', ['unpaid', 'partial'])
                    ->get();

                foreach ($revertBills as $bill) {
                    $newAmount = $billingService->calculateFinalAmount($config, $bill->person_id, $config->amount);
                    $bill->amount = $newAmount;
                    $bill->status = ($bill->amount_paid >= $newAmount) ? 'paid' : ($bill->amount_paid > 0 ? 'partial' : 'unpaid');
                    $bill->save();
                }
            }

            // 4. Update/create exceptions for all currently selected visible students
            // To prevent key conflicts, we delete the original targeted exception records first
            if (!empty($originalTargetedStudentIds)) {
                BillingException::where('billing_config_id', $this->configId)
                    ->whereIn('person_id', $originalTargetedStudentIds)
                    ->delete();
            }

            foreach ($this->excSantriIds as $santriId) {
                BillingException::updateOrCreate(
                    [
                        'billing_config_id' => $config->id,
                        'person_id' => $santriId,
                    ],
                    [
                        'id' => Str::uuid()->toString(),
                        'exception_type' => $this->excType,
                        'amount' => $this->excAmount,
                        'notes' => $this->excNotes,
                        'created_by' => auth()->id() ?: User::first()?->id,
                    ]
                );

                // Recalculate bill
                $unpaidBills = Bill::where('person_id', $santriId)
                    ->where('billing_config_id', $config->id)
                    ->whereIn('status', ['unpaid', 'partial'])
                    ->get();

                foreach ($unpaidBills as $bill) {
                    $newAmount = $billingService->calculateFinalAmount($config, $santriId, $config->amount);
                    $bill->amount = $newAmount;
                    $bill->status = ($bill->amount_paid >= $newAmount) ? 'paid' : ($bill->amount_paid > 0 ? 'partial' : 'unpaid');
                    $bill->save();
                }
            }

            // B. Process preserved hidden students (different gender)
            if ($gScope && !empty($this->hiddenSantriIds)) {
                // To prevent key conflicts, we delete their original targeted exception records first
                BillingException::where('billing_config_id', $this->configId)
                    ->whereIn('person_id', $this->hiddenSantriIds)
                    ->delete();

                foreach ($this->hiddenSantriIds as $hiddenId) {
                    BillingException::updateOrCreate(
                        [
                            'billing_config_id' => $config->id,
                            'person_id' => $hiddenId,
                        ],
                        [
                            'id' => Str::uuid()->toString(),
                            'exception_type' => $this->excType,
                            'amount' => $this->excAmount,
                            'notes' => $this->excNotes,
                            'created_by' => auth()->id() ?: User::first()?->id,
                        ]
                    );

                    // Recalculate bill for hidden student
                    $unpaidBills = Bill::where('person_id', $hiddenId)
                        ->where('billing_config_id', $config->id)
                        ->whereIn('status', ['unpaid', 'partial'])
                        ->get();

                    foreach ($unpaidBills as $bill) {
                        $newAmount = $billingService->calculateFinalAmount($config, $hiddenId, $config->amount);
                        $bill->amount = $newAmount;
                        $bill->status = ($bill->amount_paid >= $newAmount) ? 'paid' : ($bill->amount_paid > 0 ? 'partial' : 'unpaid');
                        $bill->save();
                    }
                }
            }
        });

        $totalCount = count($this->excSantriIds) + count($this->hiddenSantriIds);
        session()->flash('message', "Kelompok dispensasi berhasil diperbarui untuk {$totalCount} santri.");

        return $this->redirect(route('keuangan.billing', ['tab' => 'exceptions']), navigate: true);
    }

    public function render()
    {
        // Load active santri query with filters
        $query = Person::whereHas('activeRoles', function ($q) {
            $q->where('role_type', 'santri')->where('enrollment_status', 'aktif');
        });

        // Apply gender scope (by user role)
        if ($this->genderScope()) {
            $query->where('gender', $this->genderScope());
        } elseif ($this->filterGender) {
            $query->where('gender', $this->filterGender);
        }

        // Filter by Dormitory (via active roomAssignments -> room -> dormitory)
        if ($this->filterDormitoryId) {
            $query->whereHas('roomAssignments', function ($q) {
                $q->where('is_active', true)
                  ->whereHas('room', fn($rq) => $rq->where('dormitory_id', $this->filterDormitoryId));
            });
        }

        // Filter by Kelas Madrasah (via active madrasahEnrollments)
        if ($this->filterKelasId) {
            $query->whereHas('madrasahEnrollments', function ($q) {
                $q->where('is_active', true)->where('kelas_id', $this->filterKelasId);
            });
        }

        // Filter by search name query
        if (strlen($this->filterSearch) >= 2) {
            $query->where('name', 'like', '%' . $this->filterSearch . '%');
        }

        // Retrieve filtered list of students
        $students = $query->orderBy('name')->limit(100)->get();

        // Get filter options based on genderScope
        $dormitories = Dormitory::when($this->genderScope(), fn($q, $g) => $q->where('gender', $g))
            ->orderBy('name')
            ->get();

        // Get Kelas list which has active enrollments for this gender
        $kelasListQuery = MadrasahKelas::where('is_active', true);
        if ($this->genderScope()) {
            $kelasListQuery->whereHas('enrollments', function ($q) {
                $q->where('is_active', true)
                  ->whereHas('person', function ($pq) {
                      $pq->where('gender', $this->genderScope());
                  });
            });
        }
        $kelasList = $kelasListQuery->orderBy('jenjang')->orderBy('name')->get();

        // Load targeted config
        $targetConfig = BillingConfiguration::find($this->excConfigId);

        // Live preview data simulation
        $previewData = null;
        if ($targetConfig) {
            $originalAmount = $targetConfig->amount;
            $exceptionAmount = $this->excAmount;
            $finalAmount = $originalAmount;

            if ($this->excType === 'waived') {
                $finalAmount = 0.00;
            } elseif ($this->excType === 'discount') {
                $finalAmount = max(0.00, $originalAmount - $exceptionAmount);
            } elseif ($this->excType === 'custom_rate') {
                $finalAmount = $exceptionAmount;
            }

            $previewData = [
                'label' => $targetConfig->label,
                'original' => $originalAmount,
                'final' => $finalAmount,
                'discount_applied' => $originalAmount - $finalAmount,
            ];
        }

        // Selected students filtered by gender scope (for UI display)
        $selectedStudentsQuery = Person::whereIn('id', $this->excSantriIds);
        if ($this->genderScope()) {
            $selectedStudentsQuery->where('gender', $this->genderScope());
        }
        $selectedStudents = $selectedStudentsQuery->orderBy('name')->get();

        // Count hidden selected students (different gender)
        $hiddenSelectedCount = count($this->hiddenSantriIds);

        // Fetch existing exceptions map for chosen billing config (excluding current group being edited to avoid warnings about overwriting its own group members)
        $existingExceptionsMap = [];
        if ($this->excConfigId) {
            $existingExceptionsMap = BillingException::where('billing_config_id', $this->excConfigId)
                ->where(function($q) {
                    $q->where('exception_type', '!=', $this->type)
                      ->orWhere('amount', '!=', $this->amount)
                      ->orWhere('notes', '!=', $this->notes);
                })
                ->get()
                ->mapWithKeys(function ($exc) {
                    $desc = '';
                    if ($exc->exception_type === 'waived') {
                        $desc = 'Bebas Biaya';
                    } elseif ($exc->exception_type === 'discount') {
                        $desc = 'Potongan Rp ' . number_format($exc->amount, 0, ',', '.');
                    } elseif ($exc->exception_type === 'custom_rate') {
                        $desc = 'Tarif Rp ' . number_format($exc->amount, 0, ',', '.');
                    }
                    if ($exc->notes) {
                        $desc .= ' (' . $exc->notes . ')';
                    }
                    return [$exc->person_id => $desc];
                })
                ->toArray();
        }

        return view('livewire.keuangan.billing-exception-edit', [
            'students' => $students,
            'dormitories' => $dormitories,
            'kelasList' => $kelasList,
            'targetConfig' => $targetConfig,
            'previewData' => $previewData,
            'selectedStudents' => $selectedStudents,
            'hiddenSelectedCount' => $hiddenSelectedCount,
            'existingExceptionsMap' => $existingExceptionsMap,
        ])->layout('layouts.app');
    }
}
