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

class BillingExceptionCreate extends Component
{
    use HasGenderScope;

    // Filters
    public ?string $filterGender = null;
    public ?string $filterDormitoryId = null;
    public ?string $filterKelasId = null;
    public ?string $filterPresenceStatus = null; // mukim | laju
    public string $filterSearch = '';

    // Confirmation Modal state
    public bool $showConfirmModal = false;
    public int $overwriteCount = 0;
    public array $overwriteSantriNames = [];

    // Copy state
    public ?string $copyFromGroupKey = null;

    protected $queryString = [
        'copyFromGroupKey' => ['except' => ''],
    ];

    public function mount(): void
    {
        $copyConfigId = request()->query('copy_config_id');
        $copyType = request()->query('copy_type');
        $copyAmount = request()->query('copy_amount');
        $copyNotes = request()->query('copy_notes');

        if ($copyConfigId && $copyType) {
            $this->loadSantriFromGroup($copyConfigId, $copyType, (float)$copyAmount, (string)$copyNotes);
        }
    }

    public function loadSantriFromGroup(string $configId, string $type, float $amount, string $notes): void
    {
        $santriIds = BillingException::where('billing_config_id', $configId)
            ->where('exception_type', $type)
            ->where('amount', $amount)
            ->where('notes', $notes)
            ->pluck('person_id')
            ->toArray();

        if (empty($santriIds)) {
            $santriIds = BillingException::where('billing_config_id', $configId)
                ->where('notes', $notes)
                ->pluck('person_id')
                ->toArray();
        }

        foreach ($santriIds as $id) {
            if (!in_array($id, $this->excSantriIds)) {
                $this->excSantriIds[] = $id;
            }
        }

        $this->excType = $type;
        $this->excAmount = $amount;
        if (!empty($notes)) {
            $this->excNotes = $notes;
        }

        session()->flash('message', 'Berhasil menyalin ' . count($santriIds) . ' santri penerima dari kelompok "' . ($notes ?: 'Dispensasi') . '". Silakan pilih Iuran / Tagihan tujuan (Langkah 1).');
    }

    public function updatedCopyFromGroupKey($value): void
    {
        if (empty($value)) return;

        $parts = explode('|', $value, 4);
        if (count($parts) === 4) {
            $this->loadSantriFromGroup($parts[0], $parts[1], (float)$parts[2], $parts[3]);
        }
    }

    // Form fields
    public array $excSantriIds = [];
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

    protected $messages = [
        'excConfigId.required' => 'Silakan pilih jenis iuran / tagihan terlebih dahulu (Langkah 1).',
        'excSantriIds.required' => 'Pilih minimal 1 santri penerima dispensasi dari tabel (Langkah 2).',
        'excSantriIds.min' => 'Pilih minimal 1 santri penerima dispensasi dari tabel (Langkah 2).',
        'excType.required' => 'Pilih tipe dispensasi (Langkah 3).',
        'excAmount.required' => 'Nominal potongan / tarif wajib diisi.',
        'excAmount.min' => 'Nominal potongan tidak boleh bernilai negatif.',
        'excNotes.required' => 'Ketik nama / keterangan kelompok potongan (Langkah 3).',
        'excNotes.min' => 'Keterangan potongan minimal 3 karakter.',
    ];

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
        // 1. Get IDs from confirmed SantriSibling table
        $siblingIdsFromTable = \App\Modules\Kepengasuhan\Models\SantriSibling::where('is_confirmed', true)
            ->where('is_eligible_for_discount', true)
            ->get()
            ->flatMap(fn($sib) => [$sib->person_id, $sib->sibling_person_id])
            ->unique()
            ->toArray();

        // 2. Get IDs from SantriProfile where has_active_sibling = true (Sensus / Form flag)
        $siblingIdsFromProfile = \App\Modules\Kepengasuhan\Models\SantriProfile::where('has_active_sibling', true)
            ->pluck('person_id')
            ->toArray();

        $allSiblingIds = array_unique(array_merge($siblingIdsFromTable, $siblingIdsFromProfile));

        // Get active santri matching these sibling IDs
        $eligibleSiblings = Person::whereIn('id', $allSiblingIds)
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

    public function requestSaveConfirmation(): void
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

        // Check how many selected santris already have an existing exception for this configuration
        $existingExceptions = BillingException::where('billing_config_id', $this->excConfigId)
            ->whereIn('person_id', $this->excSantriIds)
            ->with('person')
            ->get();

        $this->overwriteCount = $existingExceptions->count();
        $this->overwriteSantriNames = $existingExceptions->pluck('person.name')->take(5)->toArray();
        $this->showConfirmModal = true;
    }

    public function executeSaveException(BillingService $billingService)
    {
        $this->validate();

        $config = BillingConfiguration::findOrFail($this->excConfigId);
        $count = count($this->excSantriIds);

        DB::transaction(function () use ($config, $billingService) {
            foreach ($this->excSantriIds as $santriId) {
                // 1. Create or update exception
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

                // 2. Retroactively update any unpaid/partial bills of this configuration for this student
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
        });

        session()->flash('message', "Dispensasi / potongan berhasil disimpan untuk {$count} santri.");

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

        // Filter by Status Kehadiran (Mukim vs Laju)
        if ($this->filterPresenceStatus) {
            $query->whereHas('activeRoles', fn($q) => $q->where('presence_status', $this->filterPresenceStatus));
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

        // Filter billing configurations based on user permissions
        $user = auth()->user();
        $isCentral = $user && ($user->hasRole('super-admin') || $user->hasRole('manajemen') || $user->hasRole('bendahara-pondok') || $user->hasRole('bendahara-pusat'));
        $activeConfigs = BillingConfiguration::where('is_active', true)->get();

        if (!$isCentral && $user) {
            $userRoles = $user->roles->pluck('name')->toArray();
            $userId = $user->id;

            $filterFunc = function ($config) use ($userRoles, $userId) {
                if (empty($config->manager_role) && empty($config->manager_ids)) {
                    return true;
                }
                if (!empty($config->manager_ids) && in_array($userId, (array)$config->manager_ids)) {
                    return true;
                }
                $managerRoles = [];
                $rawRole = $config->getRawOriginal('manager_role');
                if ($rawRole) {
                    $decoded = json_decode($rawRole, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $managerRoles = $decoded;
                    } else {
                        $managerRoles = [$rawRole];
                    }
                }
                foreach ($userRoles as $role) {
                    if (in_array($role, $managerRoles)) {
                        return true;
                    }
                }
                return false;
            };

            $activeConfigs = $activeConfigs->filter($filterFunc);
        }

        // Live preview data simulation
        $previewData = null;
        if ($this->excConfigId) {
            $config = BillingConfiguration::find($this->excConfigId);
            if ($config) {
                $originalAmount = $config->amount;
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
                    'label' => $config->label,
                    'original' => $originalAmount,
                    'final' => $finalAmount,
                    'discount_applied' => $originalAmount - $finalAmount,
                ];
            }
        }

        // Selected students filtered by gender scope (for UI display)
        $selectedStudentsQuery = Person::whereIn('id', $this->excSantriIds);
        if ($this->genderScope()) {
            $selectedStudentsQuery->where('gender', $this->genderScope());
        }
        $selectedStudents = $selectedStudentsQuery->orderBy('name')->get();

        // Count hidden selected students (different gender)
        $hiddenSelectedCount = 0;
        if ($this->genderScope()) {
            $hiddenSelectedCount = Person::whereIn('id', $this->excSantriIds)
                ->where('gender', '!=', $this->genderScope())
                ->count();
        }

        // Fetch existing exceptions map for chosen billing config
        $existingExceptionsMap = [];
        if ($this->excConfigId) {
            $existingExceptionsMap = BillingException::where('billing_config_id', $this->excConfigId)
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

        // Fetch existing exception groups for quick copying
        $existingGroupsList = BillingException::with('configuration')
            ->get()
            ->groupBy(function($exc) {
                return $exc->billing_config_id . '|' . $exc->exception_type . '|' . $exc->amount . '|' . $exc->notes;
            })
            ->map(function($group, $key) {
                $first = $group->first();
                $configLabel = $first->configuration->label ?? 'Iuran Terhapus';
                $notesLabel = $first->notes ?: 'Tanpa Keterangan';
                return [
                    'key' => $key,
                    'label' => "📋 {$notesLabel} — {$configLabel} (" . count($group) . " Santri)",
                    'count' => count($group),
                ];
            })
            ->values()
            ->toArray();

        return view('livewire.keuangan.billing-exception-create', [
            'students' => $students,
            'dormitories' => $dormitories,
            'kelasList' => $kelasList,
            'activeConfigs' => $activeConfigs,
            'previewData' => $previewData,
            'selectedStudents' => $selectedStudents,
            'hiddenSelectedCount' => $hiddenSelectedCount,
            'existingExceptionsMap' => $existingExceptionsMap,
            'existingGroupsList' => $existingGroupsList,
        ])->layout('layouts.app');
    }
}
