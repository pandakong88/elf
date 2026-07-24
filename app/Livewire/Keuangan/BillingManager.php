<?php

namespace App\Livewire\Keuangan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Modules\Core\Models\Person;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillingConfiguration;
use App\Modules\Keuangan\Models\BillingException;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Keuangan\Services\BillingService;
use App\Modules\Madrasah\Models\MadrasahKelas;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Modules\Keuangan\Models\BillPayment;
use App\Traits\HasGenderScope;
use App\Livewire\Concerns\SendsToast;

class BillingManager extends Component
{
    use WithPagination, HasGenderScope, SendsToast;

    // Tabs
    public string $activeTab = 'generate';

    // Tab: Dynamic Billing Generator
    public ?string $genConfigId = null;
    public int $genMonth;
    public int $genYear;

    // Tab: Dispensasi / Potongan (Exceptions)
    public bool $showMembersModal = false;
    public array $modalMembers = [];
    public string $modalGroupName = '';

    // Tab: Cicilan Event (Installments)
    public string $instSearchQuery = '';
    public ?string $instSantriId = null;
    public ?string $instConfigId = null;
    public float $instTotalAmount = 0.00;
    public int $instTermCount = 3;
    public ?string $selectedParentBillId = null;
    public bool $showInstallmentDetailsModal = false;
    public string $instFilterSearch = '';

    // Tab: Kasir Pembayaran
    public string  $searchQuery     = '';
    public string  $filterKomplek   = '';
    public string  $filterKamar     = '';
    public string  $filterKelas     = '';
    public ?string $selectedSantriId = null;
    public array   $selectedBillIds  = [];
    public float   $payAmount        = 0.00;
    public string  $payMethod        = 'CASH';
    public string  $payNotes         = '';
    public int     $cashierYear;
    public array   $recentSantriIds  = [];
    public bool    $showPaymentConfirmModal = false;
    public array   $previousSelectedBillIds = [];

    // Tab: Tarif Pendaftaran Santri Baru & Kitab
    public string  $activeRegSubTab = 'items'; // 'items' | 'kitab'
    public bool    $showItemModal   = false;
    public ?string $editingItemId   = null;
    public string  $itemLabel       = '';
    public float   $itemAmount      = 0.0;
    public string  $itemCategory       = 'dasar'; // 'dasar', 'asrama', 'seragam', 'konsumsi', 'kitab'
    public string  $itemGender         = 'ALL';   // 'ALL', 'L', 'P'
    public string  $itemResidence      = 'ALL';   // 'ALL', 'mukim', 'laju'
    public bool    $itemIsActive       = true;
    public array   $kitabPrices        = [];
    public string  $kitabSearch        = '';
    public string  $kitabJenjangFilter = '';

    private function getKelasSortWeight(MadrasahKelas $kelas): int
    {
        $name = strtolower($kelas->name);
        $jenjang = strtolower($kelas->jenjang);

        if (str_contains($name, 'awaliyah 1') || ($jenjang === 'awaliyah' && str_contains($name, '1'))) return 10;
        if (str_contains($name, 'awaliyah 2') || ($jenjang === 'awaliyah' && str_contains($name, '2'))) return 11;
        if (str_contains($name, 'awaliyah 3') || ($jenjang === 'awaliyah' && str_contains($name, '3'))) return 12;
        if (str_contains($name, 'wustho 1') || ($jenjang === 'wustho' && str_contains($name, '1'))) return 20;
        if (str_contains($name, 'wustho 2') || ($jenjang === 'wustho' && str_contains($name, '2'))) return 21;
        if (str_contains($name, 'ulya 1') || ($jenjang === 'ulya' && str_contains($name, '1'))) return 30;
        if (str_contains($name, 'ulya 2') || ($jenjang === 'ulya' && str_contains($name, '2'))) return 31;

        if ($jenjang === 'awaliyah') return 15;
        if ($jenjang === 'wustho') return 25;
        if ($jenjang === 'ulya') return 35;

        return 99;
    }

    public function loadKitabPrices(): void
    {
        $kelasList = MadrasahKelas::where('is_active', true)->get()->sortBy(function($kelas) {
            return $this->getKelasSortWeight($kelas);
        });

        $this->kitabPrices = [];

        foreach ($kelasList as $kelas) {
            $config = BillingConfiguration::where('type', 'kitab')
                ->where('label', 'like', "%{$kelas->name}%")
                ->first();

            $defaultPrice = 136000;
            if (str_contains(strtolower($kelas->name), 'awaliyah 2')) {
                $defaultPrice = 150000;
            } elseif (str_contains(strtolower($kelas->name), 'awaliyah 3')) {
                $defaultPrice = 175000;
            } elseif (str_contains(strtolower($kelas->name), 'wustho')) {
                $defaultPrice = 200000;
            } elseif (str_contains(strtolower($kelas->name), 'ulya')) {
                $defaultPrice = 225000;
            }

            $this->kitabPrices[$kelas->id] = [
                'kelas_id'   => $kelas->id,
                'kelas_name' => $kelas->name,
                'jenjang'    => strtoupper($kelas->jenjang),
                'amount'     => $config ? (float) $config->amount : $defaultPrice,
                'config_id'  => $config ? $config->id : null,
            ];
        }
    }

    public function saveKitabPrice(string $kelasId): void
    {
        if (!isset($this->kitabPrices[$kelasId])) return;

        $itemData = $this->kitabPrices[$kelasId];
        $kelas    = MadrasahKelas::find($kelasId);

        if (!$kelas) return;

        try {
            BillingConfiguration::updateOrCreate(
                [
                    'type'  => 'kitab',
                    'label' => "Paket Kitab {$kelas->name}",
                ],
                [
                    'id'             => $itemData['config_id'] ?: Str::uuid()->toString(),
                    'amount'         => (float) $itemData['amount'],
                    'effective_from' => now()->startOfYear(),
                    'interval'       => 'insidental',
                    'target_type'    => 'kelas',
                    'target_filters' => ['kelas_id' => $kelasId],
                    'is_active'      => true,
                    'created_by'     => auth()->id(),
                ]
            );

            $msg = "Tarif Paket Kitab {$kelas->name} berhasil diperbarui menjadi Rp " . number_format($itemData['amount'], 0, ',', '.');
            session()->flash('message', $msg);
            $this->toastSuccess($msg);
            $this->loadKitabPrices();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            $this->toastError($e->getMessage());
        }
    }

    public function openItemModal(?string $itemId = null): void
    {
        $this->editingItemId = $itemId;

        if ($itemId) {
            $config = BillingConfiguration::findOrFail($itemId);
            $filters = $config->target_filters ?? [];

            $this->itemLabel     = $config->label;
            $this->itemAmount    = (float) $config->amount;
            $this->itemCategory  = $filters['category'] ?? 'dasar';
            $this->itemGender    = $filters['gender'] ?? 'ALL';
            $this->itemResidence = $filters['residence'] ?? 'ALL';
            $this->itemIsActive  = (bool) $config->is_active;
        } else {
            $this->itemLabel     = '';
            $this->itemAmount    = 0.0;
            $this->itemCategory  = 'dasar';
            $this->itemGender    = 'ALL';
            $this->itemResidence = 'ALL';
            $this->itemIsActive  = true;
        }

        $this->showItemModal = true;
    }

    public function saveItem(): void
    {
        $this->validate([
            'itemLabel'  => 'required|string|max:255',
            'itemAmount' => 'required|numeric|min:0',
        ]);

        try {
            BillingConfiguration::updateOrCreate(
                ['id' => $this->editingItemId ?: Str::uuid()->toString()],
                [
                    'type'           => 'pendaftaran',
                    'label'          => $this->itemLabel,
                    'amount'         => $this->itemAmount,
                    'effective_from' => now()->startOfYear(),
                    'interval'       => 'insidental',
                    'target_type'    => 'all',
                    'target_filters' => [
                        'category'  => $this->itemCategory,
                        'gender'    => $this->itemGender,
                        'residence' => $this->itemResidence,
                    ],
                    'is_active'      => $this->itemIsActive,
                    'created_by'     => auth()->id(),
                ]
            );

            $msg = "Tarif item {$this->itemLabel} berhasil disimpan.";
            session()->flash('message', $msg);
            $this->toastSuccess($msg);
            $this->showItemModal = false;
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            $this->toastError($e->getMessage());
        }
    }

    public function toggleItemActive(string $configId): void
    {
        $config = BillingConfiguration::findOrFail($configId);
        $config->update(['is_active' => !$config->is_active]);
        $msg = "Status keaktifan item {$config->label} diperbarui.";
        session()->flash('message', $msg);
        $this->toastSuccess($msg);
    }

    // Reset kamar when komplek changes
    public function updatedFilterKomplek(): void
    {
        $this->filterKamar = '';
    }

    // Filters for lists tab
    public string $filterSearch = '';
    public string $filterType = '';
    public string $filterStatus = '';
    public ?int $filterMonth = null;
    public ?int $filterYear = null;

    // Filters for history table
    public string $histSearch = '';
    public string $histInterval = '';
    public ?int $histYear = null;
    public ?int $histMonth = null;
    public string $histGender = '';
    public string $histType = '';

    public function updatingHistSearch(): void { $this->resetPage('historyPage'); }
    public function updatingHistInterval(): void { $this->resetPage('historyPage'); }
    public function updatingHistYear(): void { $this->resetPage('historyPage'); }
    public function updatingHistMonth(): void { $this->resetPage('historyPage'); }
    public function updatingHistGender(): void { $this->resetPage('historyPage'); }
    public function updatingHistType(): void { $this->resetPage('historyPage'); }

    // Filters for payments log (Riwayat Setoran)
    public string $payLogSearch = '';
    public string $payLogMethod = '';
    public string $payLogDate   = '';

    public function updatingPayLogSearch(): void { $this->resetPage('payLogPage'); }
    public function updatingPayLogMethod(): void { $this->resetPage('payLogPage'); }
    public function updatingPayLogDate(): void { $this->resetPage('payLogPage'); }

    protected $queryString = [
        'activeTab' => ['as' => 'tab', 'except' => 'generate'],
        'filterSearch' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function mount(): void
    {
        if (request()->has('tab')) {
            $this->activeTab = (string) request()->query('tab');
        } elseif (request()->has('activeTab')) {
            $this->activeTab = (string) request()->query('activeTab');
        }

        $this->genMonth = (int) now()->format('m');
        $this->genYear = (int) now()->format('Y');

        $this->newConfigEffectiveFrom = now()->toDateString();
        $this->cashierYear = (int) now()->format('Y');

        $this->loadKitabPrices();
    }

    public function generateDynamicBills(BillingService $billingService): void
    {
        $this->validate([
            'genConfigId' => 'required',
            'genMonth' => 'required|integer|min:1|max:12',
            'genYear' => 'required|integer|min:2020|max:2030',
        ]);

        $config = BillingConfiguration::findOrFail($this->genConfigId);
        $periodMonth = $this->genMonth;
        if ($config->interval === 'semester' && ($periodMonth < 1 || $periodMonth > 2)) {
            $periodMonth = 1;
        } elseif (in_array($config->interval, ['once', 'insidental', 'event', 'sekali'])) {
            $periodMonth = 1;
        }

        $result = $billingService->generateBillsFromConfig(
            $this->genConfigId,
            $periodMonth,
            $this->genYear,
            auth()->id() ?: User::first()?->id
        );

        session()->flash('message', "Tagihan berhasil dibuat: {$result['generated']} tagihan baru dibuat, {$result['skipped']} tagihan dilewati (sudah ada).");
    }

    public function generateFullAcademicYearFromConfig(BillingService $billingService): void
    {
        $this->validate([
            'genConfigId' => 'required',
            'genYear' => 'required|integer|min:2020|max:2030',
        ]);

        $config = BillingConfiguration::findOrFail($this->genConfigId);
        $totalGenerated = 0;
        $totalSkipped = 0;

        if ($config->interval === 'monthly') {
            // Generate 12 months starting from July of genYear to June of genYear + 1
            $months = [
                ['m' => 7, 'y' => $this->genYear],
                ['m' => 8, 'y' => $this->genYear],
                ['m' => 9, 'y' => $this->genYear],
                ['m' => 10, 'y' => $this->genYear],
                ['m' => 11, 'y' => $this->genYear],
                ['m' => 12, 'y' => $this->genYear],
                ['m' => 1, 'y' => $this->genYear + 1],
                ['m' => 2, 'y' => $this->genYear + 1],
                ['m' => 3, 'y' => $this->genYear + 1],
                ['m' => 4, 'y' => $this->genYear + 1],
                ['m' => 5, 'y' => $this->genYear + 1],
                ['m' => 6, 'y' => $this->genYear + 1],
            ];

            foreach ($months as $p) {
                $res = $billingService->generateBillsFromConfig($config->id, $p['m'], $p['y'], auth()->id() ?: User::first()?->id);
                $totalGenerated += $res['generated'];
                $totalSkipped += $res['skipped'];
            }

            session()->flash('message', "Tagihan 1 Tahun Ajaran ({$this->genYear}/" . ($this->genYear + 1) . ") untuk iuran '{$config->label}' berhasil diterbitkan: {$totalGenerated} tagihan baru, {$totalSkipped} tagihan dilewati.");

        } elseif ($config->interval === 'semester') {
            // Generate 2 semesters (Semester 1 and 2 of genYear)
            $semesters = [
                ['m' => 1, 'y' => $this->genYear],
                ['m' => 2, 'y' => $this->genYear],
            ];

            foreach ($semesters as $p) {
                $res = $billingService->generateBillsFromConfig($config->id, $p['m'], $p['y'], auth()->id() ?: User::first()?->id);
                $totalGenerated += $res['generated'];
                $totalSkipped += $res['skipped'];
            }

            session()->flash('message', "Tagihan 2 Semester untuk iuran '{$config->label}' di tahun {$this->genYear} berhasil diterbitkan: {$totalGenerated} tagihan baru, {$totalSkipped} tagihan dilewati.");

        } else {
            // interval is once or other, just run once
            $res = $billingService->generateBillsFromConfig($config->id, $this->genMonth, $this->genYear, auth()->id() ?: User::first()?->id);
            session()->flash('message', "Tagihan iuran '{$config->label}' untuk periode {$this->genMonth}/{$this->genYear} berhasil diterbitkan: {$res['generated']} tagihan baru, {$res['skipped']} tagihan dilewati.");
        }
    }

    public function generateCalendarYearFromConfig(BillingService $billingService): void
    {
        $this->validate([
            'genConfigId' => 'required',
            'genYear' => 'required|integer|min:2020|max:2030',
        ]);

        $config = BillingConfiguration::findOrFail($this->genConfigId);
        $totalGenerated = 0;
        $totalSkipped = 0;

        if ($config->interval === 'monthly') {
            // Generate 12 months starting from January of genYear to December of genYear
            for ($m = 1; $m <= 12; $m++) {
                $res = $billingService->generateBillsFromConfig($config->id, $m, $this->genYear, auth()->id() ?: User::first()?->id);
                $totalGenerated += $res['generated'];
                $totalSkipped += $res['skipped'];
            }

            session()->flash('message', "Tagihan 1 Tahun Kalender (Jan-Des {$this->genYear}) untuk iuran '{$config->label}' berhasil diterbitkan: {$totalGenerated} tagihan baru, {$totalSkipped} tagihan dilewati.");
        } else {
            $this->generateFullAcademicYearFromConfig($billingService);
        }
    }

    public function generateCustom12MonthsFromConfig(BillingService $billingService): void
    {
        $this->validate([
            'genConfigId' => 'required',
            'genMonth' => 'required|integer|min:1|max:12',
            'genYear' => 'required|integer|min:2020|max:2030',
        ]);

        $config = BillingConfiguration::findOrFail($this->genConfigId);
        $totalGenerated = 0;
        $totalSkipped = 0;

        if ($config->interval === 'monthly') {
            $currM = $this->genMonth;
            $currY = $this->genYear;

            for ($i = 0; $i < 12; $i++) {
                $res = $billingService->generateBillsFromConfig($config->id, $currM, $currY, auth()->id() ?: User::first()?->id);
                $totalGenerated += $res['generated'];
                $totalSkipped += $res['skipped'];

                $currM++;
                if ($currM > 12) {
                    $currM = 1;
                    $currY++;
                }
            }

            $endM = ($this->genMonth == 1) ? 12 : ($this->genMonth - 1);
            $endY = ($this->genMonth == 1) ? $this->genYear : ($this->genYear + 1);

            session()->flash('message', "Tagihan 12 Bulan ({$this->genMonth}/{$this->genYear} s/d {$endM}/{$endY}) untuk iuran '{$config->label}' berhasil diterbitkan: {$totalGenerated} tagihan baru, {$totalSkipped} tagihan dilewati.");
        } else {
            $this->generateFullAcademicYearFromConfig($billingService);
        }
    }

    public function deleteBatchGeneration(string $configId, int $month, int $year): void
    {
        $config = BillingConfiguration::findOrFail($configId);

        // Security check for unit manager delegation
        $user = auth()->user();
        $isCentral = $user && ($user->hasRole('super-admin') || $user->hasRole('manajemen') || $user->hasRole('bendahara-pondok') || $user->hasRole('bendahara-pusat'));
        
        if (!$isCentral && $user) {
            $userRoles = $user->roles->pluck('name')->toArray();
            $userId = $user->id;
            
            $hasAccess = false;
            if (empty($config->manager_role) && empty($config->manager_ids)) {
                $hasAccess = true;
            }
            if (!empty($config->manager_ids) && in_array($userId, (array)$config->manager_ids)) {
                $hasAccess = true;
            }
            if (!$hasAccess && $config->manager_role) {
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
                        $hasAccess = true;
                        break;
                    }
                }
            }
            if (!$hasAccess) {
                session()->flash('error', 'Anda tidak memiliki wewenang untuk mengelola iuran ini.');
                return;
            }
        }

        // Run deletion of unpaid bills
        $deletedCount = DB::transaction(function () use ($configId, $month, $year) {
            $query = Bill::where('billing_config_id', $configId)
                ->where('period_month', $month)
                ->where('period_year', $year)
                ->where('status', 'unpaid')
                ->where('amount_paid', 0.00);
            
            $count = $query->count();
            $query->delete();
            return $count;
        });

        session()->flash('message', "Berhasil menghapus {$deletedCount} tagihan berstatus belum dibayar (unpaid) untuk iuran '{$config->label}' periode {$month}/{$year}.");
    }



    public function deleteConfig(string $id): void
    {
        $config = BillingConfiguration::find($id);
        if ($config) {
            $config->delete();
            session()->flash('message', 'Konfigurasi tarif berhasil dihapus.');
        }
    }





    public function selectSantri(string $id): void
    {
        $this->selectedSantriId = $id;
        $this->selectedBillIds  = [];
        $this->previousSelectedBillIds = [];
        $this->payAmount        = 0.00;
        $this->searchQuery      = '';

        // Track recently selected santri (max 5, no duplicates)
        $this->recentSantriIds = array_values(array_unique(
            array_merge([$id], array_filter($this->recentSantriIds, fn($r) => $r !== $id))
        ));
        if (count($this->recentSantriIds) > 5) {
            $this->recentSantriIds = array_slice($this->recentSantriIds, 0, 5);
        }
    }

    public function updatedSelectedBillIds(): void
    {
        $unpaidGrouped = $this->unpaidBills->groupBy('billing_config_id');

        // Added elements (newly checked)
        $added = array_diff($this->selectedBillIds, $this->previousSelectedBillIds);
        // Removed elements (newly unchecked)
        $removed = array_diff($this->previousSelectedBillIds, $this->selectedBillIds);

        // Process added: auto-select prior unpaid bills of the same iuran/config
        foreach ($added as $billId) {
            $bill = Bill::find($billId);
            if (!$bill) continue;

            $configId = $bill->billing_config_id;
            if (isset($unpaidGrouped[$configId])) {
                foreach ($unpaidGrouped[$configId] as $ub) {
                    $date1 = $ub->due_date ? $ub->due_date->toDateString() : '0000-00-00';
                    $date2 = $bill->due_date ? $bill->due_date->toDateString() : '0000-00-00';

                    $isOlder = false;
                    if ($date1 !== $date2) {
                        $isOlder = $date1 < $date2;
                    } else {
                        $isOlder = $ub->created_at <= $bill->created_at;
                    }

                    if ($isOlder && !in_array($ub->id, $this->selectedBillIds)) {
                        $this->selectedBillIds[] = $ub->id;
                    }
                }
            }
        }

        // Process removed: auto-deselect succeeding unpaid bills of the same iuran/config
        foreach ($removed as $billId) {
            $bill = Bill::find($billId);
            if (!$bill) continue;

            $configId = $bill->billing_config_id;
            if (isset($unpaidGrouped[$configId])) {
                foreach ($unpaidGrouped[$configId] as $ub) {
                    $date1 = $ub->due_date ? $ub->due_date->toDateString() : '0000-00-00';
                    $date2 = $bill->due_date ? $bill->due_date->toDateString() : '0000-00-00';

                    $isNewer = false;
                    if ($date1 !== $date2) {
                        $isNewer = $date1 > $date2;
                    } else {
                        $isNewer = $ub->created_at >= $bill->created_at;
                    }

                    if ($isNewer) {
                        $this->selectedBillIds = array_diff($this->selectedBillIds, [$ub->id]);
                    }
                }
            }
        }

        $this->selectedBillIds = array_values(array_unique($this->selectedBillIds));
        $this->previousSelectedBillIds = $this->selectedBillIds;
    }

    public function selectTunggakan(): void
    {
        $tunggakanIds = $this->tunggakanLamaBills->pluck('id')->toArray();
        $this->selectedBillIds = array_values(array_unique(array_merge($this->selectedBillIds, $tunggakanIds)));
        $this->previousSelectedBillIds = $this->selectedBillIds;
    }

    public function deselectTunggakan(): void
    {
        $tunggakanIds = $this->tunggakanLamaBills->pluck('id')->toArray();
        $this->selectedBillIds = array_values(array_diff($this->selectedBillIds, $tunggakanIds));
        $this->previousSelectedBillIds = $this->selectedBillIds;
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

        return $query->whereHas('config', function($q) use ($userRoles, $userId) {
            $q->where(function($sub) use ($userRoles, $userId) {
                $sub->whereNull('manager_role')->whereNull('manager_ids');
                $sub->orWhereJsonContains('manager_ids', $userId);
                foreach ($userRoles as $role) {
                    $sub->orWhere('manager_role', 'like', '%' . $role . '%');
                }
            });
        });
    }

    public function getUnpaidBillsProperty()
    {
        if (!$this->selectedSantriId) return collect();
        $query = Bill::where('person_id', $this->selectedSantriId)
            ->whereIn('status', ['unpaid', 'partial']);
        
        $query = $this->applyManagerRoleScope($query);

        return $query->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getTunggakanLamaBillsProperty()
    {
        if (!$this->selectedSantriId) return collect();
        $year = $this->cashierYear;
        $query = Bill::where('person_id', $this->selectedSantriId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->where('period_year', '<', $year)
            ->with('config');

        $query = $this->applyManagerRoleScope($query);

        return $query->orderBy('period_year', 'asc')
            ->orderBy('period_month', 'asc')
            ->get();
    }

    public function getPaidFutureBillsProperty()
    {
        if (!$this->selectedSantriId) return collect();
        $year = $this->cashierYear;
        $query = Bill::where('person_id', $this->selectedSantriId)
            ->where('status', 'paid')
            ->where('period_year', '>', $year)
            ->with('config');

        $query = $this->applyManagerRoleScope($query);

        return $query->orderBy('period_year', 'asc')
            ->orderBy('period_month', 'asc')
            ->get();
    }

    // All bills for selected santri in the cashier year, keyed by config_id -> month
    public function getBulananBillsProperty(): array
    {
        if (!$this->selectedSantriId) return [];
        $year = $this->cashierYear;

        $query = Bill::where('person_id', $this->selectedSantriId)
            ->where('period_year', $year)
            ->whereHas('config', fn($q) => $q->where('interval', 'monthly'))
            ->with('config');

        $query = $this->applyManagerRoleScope($query);
        $bills = $query->get();

        // Group: configId -> month -> bill
        $configs = [];
        foreach ($bills as $bill) {
            $cid = $bill->billing_config_id;
            if (!isset($configs[$cid])) {
                $configs[$cid] = [
                    'label'  => $bill->config?->label ?? $bill->bill_type,
                    'months' => array_fill(1, 12, null),
                ];
            }
            $configs[$cid]['months'][$bill->period_month] = $bill;
        }

        return $configs;
    }

    public function getSemesterBillsProperty(): array
    {
        if (!$this->selectedSantriId) return [];
        $year = $this->cashierYear;

        $query = Bill::where('person_id', $this->selectedSantriId)
            ->where('period_year', $year)
            ->whereHas('config', fn($q) => $q->where('interval', 'semester'))
            ->with('config');

        $query = $this->applyManagerRoleScope($query);
        $bills = $query->orderBy('period_month')->get();

        // Group: configId -> [sem1_bill, sem2_bill]
        $configs = [];
        foreach ($bills as $bill) {
            $cid = $bill->billing_config_id;
            if (!isset($configs[$cid])) {
                $configs[$cid] = ['label' => $bill->config?->label ?? $bill->bill_type, 'bills' => []];
            }
            $configs[$cid]['bills'][$bill->period_month] = $bill;
        }

        return $configs;
    }

    public function getInsidentalBillsProperty()
    {
        if (!$this->selectedSantriId) return collect();

        $query = Bill::where('person_id', $this->selectedSantriId)
            ->where(function($q) {
                $q->whereNull('billing_config_id')
                  ->orWhereHas('config', fn($sq) => $sq->whereIn('interval', ['once', 'insidental', 'event', 'sekali', 'yearly']));
            })
            ->with('config');

        $query = $this->applyManagerRoleScope($query);

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getSelectedBillsTotalProperty(): float
    {
        if (empty($this->selectedBillIds)) return 0.00;
        return Bill::whereIn('id', $this->selectedBillIds)->get()->sum(fn($b) => $b->amount - $b->amount_paid);
    }

    public function getConfirmBillsProperty()
    {
        if (empty($this->selectedBillIds)) return collect();
        return Bill::whereIn('id', $this->selectedBillIds)
            ->whereIn('status', ['unpaid', 'partial'])
            ->with('config')
            ->orderBy('due_date', 'asc')
            ->get();
    }

    public function initiatePayment(): void
    {
        $selectedBillsTotal = $this->selectedBillsTotal;
        $this->validate([
            'selectedSantriId' => 'required',
            'payAmount' => 'required|numeric|min:1|max:' . $selectedBillsTotal,
            'payMethod' => 'required|in:CASH,TRANSFER,EWALLET',
        ], [
            'payAmount.max' => 'Uang diterima tidak boleh melebihi total tagihan terpilih (Rp ' . number_format($selectedBillsTotal, 0, ',', '.') . '). Masukkan nominal pas yang dibayarkan saja.',
        ]);

        $billsToPay = $this->confirmBills;

        if ($billsToPay->isEmpty()) {
            session()->flash('error', 'Silakan pilih minimal satu tagihan untuk dibayar.');
            return;
        }

        $this->showPaymentConfirmModal = true;
    }

    public function cancelPayment(): void
    {
        $this->showPaymentConfirmModal = false;
    }

    public function recordPayment(BillingService $billingService): void
    {
        $selectedBillsTotal = $this->selectedBillsTotal;
        $this->validate([
            'selectedSantriId' => 'required',
            'payAmount' => 'required|numeric|min:1|max:' . $selectedBillsTotal,
            'payMethod' => 'required|in:CASH,TRANSFER,EWALLET',
        ], [
            'payAmount.max' => 'Uang diterima tidak boleh melebihi total tagihan terpilih (Rp ' . number_format($selectedBillsTotal, 0, ',', '.') . '). Masukkan nominal pas yang dibayarkan saja.',
        ]);

        $billsToPay = $this->confirmBills;

        if ($billsToPay->isEmpty()) {
            session()->flash('error', 'Silakan pilih minimal satu tagihan untuk dibayar.');
            $this->showPaymentConfirmModal = false;
            return;
        }

        DB::transaction(function () use ($billsToPay, $billingService) {
            $remainingAmount = $this->payAmount;

            foreach ($billsToPay as $bill) {
                if ($remainingAmount <= 0) {
                    break;
                }

                $unpaidAmount = $bill->amount - $bill->amount_paid;
                $paymentForThisBill = min($remainingAmount, $unpaidAmount);

                $billingService->recordPayment(
                    $bill->id,
                    $paymentForThisBill,
                    $this->payMethod,
                    $this->payNotes ?: 'Pembayaran Kasir',
                    auth()->id() ?: User::first()?->id
                );

                $remainingAmount -= $paymentForThisBill;
            }
        });

        session()->flash('message', 'Pembayaran berhasil dicatat.');
        $this->showPaymentConfirmModal = false;
        $this->previousSelectedBillIds = [];
        $this->selectSantri($this->selectedSantriId);
    }

    public function deletePayment(string $paymentId): void
    {
        $user = auth()->user();
        if ($user && ! ($user->hasRole('super-admin') || $user->hasRole('pengasuh') || $user->hasPermissionTo('void-pembayaran'))) {
            session()->flash('error', 'Anda tidak memiliki wewenang untuk membatalkan (void) transaksi pembayaran ini.');
            return;
        }

        $payment = BillPayment::find($paymentId);
        if ($payment) {
            $payment->delete();
            session()->flash('message', 'Pencatatan pembayaran berhasil dibatalkan & sisa tagihan dikembalikan.');
        }
    }



    public function showGroupMembers(string $configId, string $type, float $amount, string $notes): void
    {
        $gScope = $this->genderScope();

        $query = BillingException::with('person')
            ->where('billing_config_id', $configId)
            ->where('exception_type', $type)
            ->where('amount', $amount)
            ->where('notes', $notes);

        if ($gScope) {
            $query->whereHas('person', fn($pq) => $pq->where('gender', $gScope));
        }

        $exceptions = $query->get();

        $this->modalGroupName = $notes ?: 'Tanpa Alasan/Keterangan';
        $this->modalMembers = $exceptions->map(function ($exc) {
            return [
                'name' => $exc->person->name,
                'gender' => $exc->person->gender,
            ];
        })->toArray();

        $this->showMembersModal = true;
    }

    public function closeMembersModal(): void
    {
        $this->showMembersModal = false;
        $this->modalMembers = [];
        $this->modalGroupName = '';
    }

    public function deleteGroup(string $configId, string $type, float $amount, string $notes): void
    {
        $gScope = $this->genderScope();

        $query = BillingException::with('configuration')
            ->where('billing_config_id', $configId)
            ->where('exception_type', $type)
            ->where('amount', $amount)
            ->where('notes', $notes);

        if ($gScope) {
            $query->whereHas('person', fn($pq) => $pq->where('gender', $gScope));
        }

        $exceptions = $query->get();

        DB::transaction(function () use ($exceptions) {
            foreach ($exceptions as $exc) {
                $config = $exc->configuration;
                $santriId = $exc->person_id;

                $exc->delete();

                // Revert unpaid/partial bills back to configuration default amount
                if ($config) {
                    $unpaidBills = Bill::where('person_id', $santriId)
                        ->where('billing_config_id', $config->id)
                        ->whereIn('status', ['unpaid', 'partial'])
                        ->get();

                    foreach ($unpaidBills as $bill) {
                        $newAmount = $config->amount;
                        $bill->amount = $newAmount;
                        $bill->status = ($bill->amount_paid >= $newAmount) ? 'paid' : ($bill->amount_paid > 0 ? 'partial' : 'unpaid');
                        $bill->save();
                    }
                }
            }
        });

        session()->flash('message', 'Kelompok dispensasi berhasil dihapus' . ($gScope ? ' untuk gender Anda.' : '.'));
    }

    public function showInstallmentDetails(string $parentBillId): void
    {
        $this->selectedParentBillId = $parentBillId;
        $this->showInstallmentDetailsModal = true;
    }

    public function closeInstallmentDetailsModal(): void
    {
        $this->showInstallmentDetailsModal = false;
        $this->selectedParentBillId = null;
    }

    public function cancelInstallmentPlan(string $parentBillId): void
    {
        $parentBill = Bill::findOrFail($parentBillId);

        // Security check for gender scope
        if ($this->genderScope() && $parentBill->person->gender !== $this->genderScope()) {
            session()->flash('error', 'Anda tidak memiliki wewenang untuk membatalkan cicilan santri ini.');
            return;
        }

        DB::transaction(function () use ($parentBill) {
            // Delete all child bills
            Bill::where('parent_bill_id', $parentBill->id)->delete();
            // Delete parent bill
            $parentBill->delete();
        });

        session()->flash('message', 'Skema cicilan berhasil dibatalkan.');
    }

    public function selectInstSantri(string $id): void
    {
        $this->instSantriId = $id;
        $this->instSearchQuery = Person::find($id)?->name ?? '';

        // Auto-calculate final amount based on student discounts/exceptions if config is selected
        if ($this->instConfigId) {
            $config = BillingConfiguration::find($this->instConfigId);
            if ($config) {
                $billingService = new BillingService();
                $this->instTotalAmount = $billingService->calculateFinalAmount($config, $id, $config->amount);
            }
        }
    }

    public function updatedInstConfigId($value): void
    {
        if ($value) {
            $config = BillingConfiguration::find($value);
            if ($config) {
                $amount = $config->amount;
                if ($this->instSantriId) {
                    $billingService = new BillingService();
                    $amount = $billingService->calculateFinalAmount($config, $this->instSantriId, $config->amount);
                }
                $this->instTotalAmount = $amount;
            }
        } else {
            $this->instTotalAmount = 0.00;
        }
    }

    public function generateInstallments(BillingService $billingService): void
    {
        $this->validate([
            'instSantriId' => 'required',
            'instConfigId' => 'required',
            'instTotalAmount' => 'required|numeric|min:0',
            'instTermCount' => 'required|integer|min:2|max:12',
        ]);

        $result = $billingService->generateInstallmentBills(
            $this->instSantriId,
            $this->instConfigId,
            $this->instTotalAmount,
            $this->instTermCount,
            auth()->id() ?: User::first()?->id
        );

        $this->instSantriId = null;
        $this->instSearchQuery = '';
        $this->instTotalAmount = 0.00;

        session()->flash('message', "Tagihan cicilan berhasil dibuat sebanyak {$result['terms']} termin.");
    }

    public function render()
    {
        // Santri list: built from filters (komplek/kamar/kelas) OR from search query (2+ chars)
        $hasFilters    = $this->filterKomplek || $this->filterKamar || $this->filterKelas;
        $hasSearchText = strlen($this->searchQuery) >= 2;
        $santriSearch  = collect();

        if ($hasFilters || $hasSearchText) {
            // Pre-resolve rooms to avoid nested and multiple exists subqueries
            $roomIds = null;
            if ($this->filterKamar) {
                $roomIds = [$this->filterKamar];
            } elseif ($this->filterKomplek) {
                $roomIds = Room::where('dormitory_id', $this->filterKomplek)->pluck('id')->toArray();
            }

            $q = Person::whereHas('activeRoles', function ($rq) {
                $rq->where('role_type', 'santri')->where('enrollment_status', 'aktif');
            })
            ->when($this->genderScope(), fn($q, $g) => $q->where('gender', $g))
            ->when($roomIds !== null, fn($q) =>
                $q->whereHas('roomAssignments', fn($rq) =>
                    $rq->where('is_active', true)->whereIn('room_id', $roomIds)
                )
            )
            ->when($this->filterKelas, fn($q) =>
                $q->whereHas('madrasahEnrollments', fn($eq) =>
                    $eq->where('is_active', true)->where('kelas_id', $this->filterKelas)
                )
            )
            ->when($hasSearchText, fn($q) =>
                $q->where(fn($sq) =>
                    $sq->where('name', 'like', '%' . $this->searchQuery . '%')
                       ->orWhere('nik',  'like', '%' . $this->searchQuery . '%')
                       ->orWhereHas('santriProfile', fn($sp) =>
                           $sp->where('additional_info->nis', 'like', '%' . $this->searchQuery . '%')
                              ->orWhere('additional_info->nisn', 'like', '%' . $this->searchQuery . '%')
                       )
                )
            )
            ->with(['roomAssignments.room.dormitory', 'madrasahEnrollments.kelas'])
            ->orderBy('name')
            ->limit($hasFilters && !$hasSearchText ? 20 : 10)
            ->get();

            $santriSearch = $q;
        }

        // Rooms for selected komplek (for kamar dropdown)
        $roomsForKomplek = $this->filterKomplek
            ? Room::where('dormitory_id', $this->filterKomplek)->orderBy('name')->get()
            : collect();

        // Load recent santri
        $recentSantri = !empty($this->recentSantriIds)
            ? Person::whereIn('id', $this->recentSantriIds)
                ->with(['roomAssignments.room.dormitory', 'madrasahEnrollments.kelas'])
                ->get()
                ->sortBy(fn($s) => array_search($s->id, $this->recentSantriIds))
                ->values()
            : collect();

        // Selected Santri details
        $selectedSantri = $this->selectedSantriId
            ? Person::with(['roomAssignments.room.dormitory', 'madrasahEnrollments.kelas'])->find($this->selectedSantriId)
            : null;

        // Bills lists with filter
        $billsQuery = Bill::with(['person', 'config'])
            ->when($this->genderScope(), function ($q, $g) {
                $q->whereHas('person', fn($pq) => $pq->where('gender', $g));
            })
            ->when($this->filterSearch, function ($q) {
                $q->whereHas('person', function ($sq) {
                    $sq->where('name', 'like', '%' . $this->filterSearch . '%');
                });
            })
            ->when($this->filterType, function ($q) {
                $q->where('bill_type', $this->filterType);
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->filterMonth, function ($q) {
                $q->where('period_month', $this->filterMonth);
            })
            ->when($this->filterYear, function ($q) {
                $q->where('period_year', $this->filterYear);
            })
            ->orderBy('created_at', 'desc');



        $instSearch = [];
        if (strlen($this->instSearchQuery) >= 3 && !$this->instSantriId) {
            $instSearch = Person::whereHas('activeRoles', function ($q) {
                $q->where('role_type', 'santri');
            })
            ->when($this->genderScope(), fn($q, $g) => $q->where('gender', $g))
            ->where('name', 'like', '%' . $this->instSearchQuery . '%')
            ->limit(5)
            ->get();
        }

        $exceptions = BillingException::with(['configuration', 'person'])
            ->whereHas('person', function ($q) {
                $q->when($this->genderScope(), fn($sq, $g) => $sq->where('gender', $g));
            })
            ->get();

        $user = auth()->user();
        $isCentral = $user && ($user->hasRole('super-admin') || $user->hasRole('manajemen') || $user->hasRole('bendahara-pondok') || $user->hasRole('bendahara-pusat'));

        if (!$isCentral && $user) {
            $userRoles = $user->roles->pluck('name')->toArray();
            $userId = $user->id;

            $exceptions = $exceptions->filter(function ($exc) {
                $config = $exc->configuration;
                if (!$config) return false;
                
                $user = auth()->user();
                $userRoles = $user->roles->pluck('name')->toArray();
                $userId = $user->id;

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
            });
        }

        $user = auth()->user();
        $isCentral = $user && ($user->hasRole('super-admin') || $user->hasRole('manajemen') || $user->hasRole('bendahara-pondok') || $user->hasRole('bendahara-pusat'));

        $activeConfigs = BillingConfiguration::with('creator')->where('is_active', true)->get();
        $installmentConfigs = BillingConfiguration::where('is_active', true)
            ->where('can_be_installment', true)
            ->get();

        if (!$isCentral && $user) {
            $userRoles = $user->roles->pluck('name')->toArray();
            $userId = $user->id;

            $filterFunc = function ($config) use ($userRoles, $userId) {
                // If no manager_role and no manager_ids is specified, anyone has access
                if (empty($config->manager_role) && empty($config->manager_ids)) {
                    return true;
                }

                // If user's ID is in manager_ids
                if (!empty($config->manager_ids) && in_array($userId, (array)$config->manager_ids)) {
                    return true;
                }

                // Decode manager_role if it is JSON, otherwise treat as plain string/array
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

                // Check if any of user's roles match manager_roles
                foreach ($userRoles as $role) {
                    if (in_array($role, $managerRoles)) {
                        return true;
                    }
                }

                return false;
            };

            $activeConfigs = $activeConfigs->filter($filterFunc);
            $installmentConfigs = $installmentConfigs->filter($filterFunc);
        }

        // Fetch active installment plans (parent bills)
        $installmentPlansQuery = Bill::with(['person', 'config', 'installments'])
            ->whereNull('parent_bill_id')
            ->whereHas('installments')
            ->when($this->genderScope(), function ($q, $g) {
                $q->whereHas('person', fn($pq) => $pq->where('gender', $g));
            })
            ->when(strlen($this->instFilterSearch) >= 2, function ($q) {
                $q->whereHas('person', function ($sq) {
                    $sq->where('name', 'like', '%' . $this->instFilterSearch . '%');
                });
            })
            ->orderBy('created_at', 'desc');

        $installmentPlans = $installmentPlansQuery->get();

        // Filter active plans by unit manager role permissions
        if (!$isCentral && $user) {
            $userRoles = $user->roles->pluck('name')->toArray();
            $userId = $user->id;

            $installmentPlans = $installmentPlans->filter(function ($plan) use ($userRoles, $userId) {
                $config = $plan->config;
                if (!$config) return false;
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
            });
        }

        // Selected parent details
        $selectedParentBill = null;
        $installmentChildBills = [];
        if ($this->selectedParentBillId) {
            $selectedParentBill = Bill::with(['person', 'config'])->find($this->selectedParentBillId);
            if ($selectedParentBill) {
                $installmentChildBills = Bill::where('parent_bill_id', $this->selectedParentBillId)
                    ->orderBy('due_date')
                    ->get();
            }
        }
        // Fetch generation history (grouped logs)
        $historyQuery = Bill::with(['config', 'creator'])
            ->select(
                'billing_config_id',
                'period_month',
                'period_year',
                'created_by',
                DB::raw('count(*) as total_students'),
                DB::raw('sum(amount) as total_amount'),
                DB::raw('min(created_at) as generated_at')
            )
            ->whereNotNull('billing_config_id')
            ->when($this->genderScope(), function ($q, $g) {
                $q->whereHas('person', fn($pq) => $pq->where('gender', $g));
            })
            ->when($this->histSearch, function ($q) {
                $q->whereHas('config', function ($cq) {
                    $cq->where('label', 'like', '%' . $this->histSearch . '%');
                });
            })
            ->when($this->histInterval, function ($q) {
                $q->whereHas('config', function ($cq) {
                    if (in_array($this->histInterval, ['insidental', 'once', 'sekali', 'event'])) {
                        $cq->whereIn('interval', ['insidental', 'once', 'sekali', 'event']);
                    } else {
                        $cq->where('interval', $this->histInterval);
                    }
                });
            })
            ->when($this->histType, function ($q) {
                $q->whereHas('config', function ($cq) {
                    $cq->where('type', $this->histType);
                });
            })
            ->when($this->histGender, function ($q) {
                if (in_array($this->histGender, ['L', 'P'])) {
                    $q->whereHas('person', fn($pq) => $pq->where('gender', $this->histGender));
                }
            })
            ->when($this->histMonth, function ($q) {
                $q->where('period_month', $this->histMonth);
            })
            ->when($this->histYear, function ($q) {
                $q->where('period_year', $this->histYear);
            })
            ->groupBy('billing_config_id', 'period_month', 'period_year', 'created_by')
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->orderBy('generated_at', 'desc');

        if (!$isCentral && $user) {
            $allowedConfigIds = BillingConfiguration::all()->filter($filterFunc)->pluck('id')->toArray();
            $historyQuery->whereIn('billing_config_id', $allowedConfigIds);
        }

        // KPI Stats calculation for current month & year
        $currentMonth = (int) now()->format('n');
        $currentYear  = (int) now()->format('Y');

        $currentMonthBillsQuery = Bill::where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->when($this->genderScope(), function ($q, $g) {
                $q->whereHas('person', fn($pq) => $pq->where('gender', $g));
            });

        $kpiTotalBillsCount = (clone $currentMonthBillsQuery)->count();
        $kpiTotalAmount     = (float) (clone $currentMonthBillsQuery)->sum('amount');
        $kpiPaidAmount      = (float) (clone $currentMonthBillsQuery)->sum('amount_paid');
        $kpiPaidBillsCount  = (clone $currentMonthBillsQuery)->where('status', 'paid')->count();

        $kpiPaymentPercentage = $kpiTotalAmount > 0 
            ? round(($kpiPaidAmount / $kpiTotalAmount) * 100, 1) 
            : 0;

        $allBillsConfigMap = Bill::whereNotNull('billing_config_id')
            ->get(['billing_config_id', 'period_month', 'period_year'])
            ->groupBy('billing_config_id');

        $generatedConfigIdsThisMonth = [];
        foreach ($activeConfigs as $cfg) {
            if (!$allBillsConfigMap->has($cfg->id)) {
                continue;
            }

            $billsForConfig = $allBillsConfigMap->get($cfg->id);
            $isEventInterval = in_array($cfg->interval, ['once', 'insidental', 'event', 'sekali']);

            if ($isEventInterval) {
                // Insidental / Sekali bayar: if any bill exists for this config, mark as published!
                if ($billsForConfig->isNotEmpty()) {
                    $generatedConfigIdsThisMonth[] = $cfg->id;
                }
            } elseif ($cfg->interval === 'yearly') {
                // Yearly: check if bills exist for currentYear
                if ($billsForConfig->where('period_year', $currentYear)->isNotEmpty()) {
                    $generatedConfigIdsThisMonth[] = $cfg->id;
                }
            } else {
                // Monthly / Semester: check if bills exist for currentMonth & currentYear
                if ($billsForConfig->where('period_month', $currentMonth)->where('period_year', $currentYear)->isNotEmpty()) {
                    $generatedConfigIdsThisMonth[] = $cfg->id;
                }
            }
        }

        // Fetch recorded payment logs (Riwayat Setoran)
        $paymentsLogQuery = BillPayment::with(['bill.person', 'bill.config', 'logger'])
            ->when($this->genderScope(), function ($q, $g) {
                $q->whereHas('bill.person', fn($pq) => $pq->where('gender', $g));
            })
            ->when($this->payLogSearch, function ($q) {
                $q->where(function($sub) {
                    $sub->whereHas('bill.person', function ($sq) {
                        $sq->where('name', 'like', '%' . $this->payLogSearch . '%')
                          ->orWhere('nik', 'like', '%' . $this->payLogSearch . '%')
                          ->orWhereHas('santriProfile', fn($sp) =>
                              $sp->where('additional_info->nis', 'like', '%' . $this->payLogSearch . '%')
                          );
                    })
                    ->orWhereHas('bill.config', function ($sq) {
                        $sq->where('label', 'like', '%' . $this->payLogSearch . '%');
                    })
                    ->orWhere('notes', 'like', '%' . $this->payLogSearch . '%');
                });
            })
            ->when($this->payLogMethod, function ($q) {
                $q->where('payment_method', strtolower($this->payLogMethod));
            })
            ->when($this->payLogDate, function ($q) {
                $q->whereDate('payment_date', $this->payLogDate);
            })
            ->orderBy('created_at', 'desc');

        if (!$isCentral && $user) {
            $allowedConfigIds = BillingConfiguration::all()->filter($filterFunc)->pluck('id')->toArray();
            $paymentsLogQuery->whereHas('bill', function ($q) use ($allowedConfigIds) {
                $q->whereIn('billing_config_id', $allowedConfigIds);
            });
        }

        $paymentsLog = $paymentsLogQuery->paginate(15, pageName: 'payLogPage');
        $generationHistory = $historyQuery->paginate(10, pageName: 'historyPage');

        return view('livewire.keuangan.billing-manager', [
            'registrationItems'   => BillingConfiguration::where('type', 'pendaftaran')->orderBy('is_active', 'desc')->orderBy('created_at', 'desc')->get(),
            'santriSearchResults' => $santriSearch,
            'recentSantri'        => $recentSantri,
            'roomsForKomplek'     => $roomsForKomplek,
            'instSearchResults'   => $instSearch,
            'selectedSantri'      => $selectedSantri,
            'exceptions'          => $exceptions,
            'activeConfigs'       => $activeConfigs,
            'installmentConfigs'  => $installmentConfigs,
            'bills'               => $billsQuery->paginate(10),
            'dormitories'         => Dormitory::when($this->genderScope(), fn($q, $g) => $q->where('gender', $g))->orderBy('name')->get(),
            'kelasList'           => MadrasahKelas::where('is_active', true)
                ->when($this->genderScope(), function($q, $g) {
                    $keyword = $g === 'L' ? 'Putra' : 'Putri';
                    $q->where(function($sub) use ($g, $keyword) {
                        $sub->where('name', 'like', '%' . $keyword . '%')
                            ->orWhereHas('activeEnrollments.person', fn($pq) => $pq->where('gender', $g));
                    });
                })
                ->orderBy('jenjang')->orderBy('name')->get(),
            'systemRoles'         => \Spatie\Permission\Models\Role::orderBy('name')->get(),
            'installmentPlans'    => $installmentPlans,
            'selectedParentBill'  => $selectedParentBill,
            'installmentChildBills' => $installmentChildBills,
            'generationHistory'   => $generationHistory,
            'paymentsLog'         => $paymentsLog,
            'kpiStats'            => [
                'total_count'         => $kpiTotalBillsCount,
                'total_amount'        => $kpiTotalAmount,
                'paid_amount'         => $kpiPaidAmount,
                'paid_count'          => $kpiPaidBillsCount,
                'percentage'          => $kpiPaymentPercentage,
                'generated_configs'   => $generatedConfigIdsThisMonth,
                'current_period_name' => now()->locale('id')->translatedFormat('F Y'),
            ],
        ])->layout('layouts.app');
    }
}
