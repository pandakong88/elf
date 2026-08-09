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
    public int $genSubPeriod = 1;

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

    // Tab: Daftar Konfigurasi Tarif
    public string $rateSearchQuery = '';
    public string $rateStatusFilter = '';

    public function updatedRateSearchQuery(): void
    {
        $this->resetPage('ratesPage');
    }

    public function updatedRateStatusFilter(): void
    {
        $this->resetPage('ratesPage');
    }
    public ?string $historyDetailConfigId = null;
    public ?int $historyDetailMonth = null;
    public ?int $historyDetailYear = null;
    public ?int $historyDetailSub = null;
    public string $historyDetailSearch = '';
    public string $historyDetailStatusFilter = '';

    // Modal Konfirmasi Hapus Kustom (Batch & Individual)
    public bool $showDeleteConfirmModal = false;
    public ?string $deleteType = null; // 'batch' | 'individual'
    public ?string $deleteBillId = null;
    public ?string $deleteConfigId = null;
    public ?int $deletePeriodMonth = null;
    public ?int $deletePeriodYear = null;
    public array $deleteConfirmData = [];

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
    public string  $itemCategory       = 'dasar'; // 'dasar', 'fasilitas', 'seragam', 'katering', 'bangunan', 'administrasi', 'kitab'
    public string  $itemGender         = 'ALL';   // 'ALL', 'L', 'P'
    public string  $itemResidence      = 'ALL';   // 'ALL', 'mukim', 'laju'
    public bool    $itemIsActive       = true;
    public string  $regItemSearch         = '';
    public string  $regItemCategoryFilter = '';
    public string  $regItemGenderFilter   = '';
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
            $actionText = $this->editingItemId ? 'diperbarui' : 'ditambahkan';
            BillingConfiguration::updateOrCreate(
                ['id' => $this->editingItemId ?: Str::uuid()->toString()],
                [
                    'type'           => 'pendaftaran',
                    'label'          => $this->itemLabel,
                    'amount'         => $this->itemAmount,
                    'effective_from' => now()->startOfYear(),
                    'interval'       => 'once',
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

            $msg = "Tarif item '{$this->itemLabel}' berhasil {$actionText}.";
            session()->flash('message', $msg);
            $this->toastSuccess($msg);
            $this->showItemModal = false;
            $this->editingItemId = null;
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            $this->toastError($e->getMessage());
        }
    }

    public function confirmDeleteRegItem(string $itemId): void
    {
        $config = BillingConfiguration::find($itemId);
        if (!$config) {
            $this->toastError('Item tarif tidak ditemukan.');
            return;
        }

        $this->tariffActionConfigId    = $itemId;
        $this->tariffActionType        = 'delete_reg_item';
        $this->tariffActionConfigLabel = $config->label;
        $this->showTariffActionModal   = true;
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
    public string $payLogSearch      = '';
    public string $payLogMethod      = '';
    public string $payLogDate        = '';
    public string $payLogStartDate   = '';
    public string $payLogEndDate     = '';
    public string $payLogUser        = '';
    public string $payLogConfigId    = '';
    public string $payLogDormitoryId = '';
    public string $payLogKelasId     = '';
    public bool   $showPayLogAdvancedFilters = false;

    // Filters for exceptions (Dispensasi & Keringanan)
    public string $exceptionSearch     = '';
    public string $exceptionTypeFilter = '';

    public function updatingActiveTab(): void
    {
        $this->resetPage();
        $this->resetPage('payLogPage');
        $this->resetPage('historyPage');
    }

    public function updatingPayLogSearch(): void { $this->resetPage('payLogPage'); }
    public function updatingPayLogMethod(): void { $this->resetPage('payLogPage'); }
    public function updatingPayLogDate(): void { $this->resetPage('payLogPage'); }
    public function updatingPayLogStartDate(): void { $this->resetPage('payLogPage'); }
    public function updatingPayLogEndDate(): void { $this->resetPage('payLogPage'); }
    public function updatingPayLogUser(): void { $this->resetPage('payLogPage'); }
    public function updatingPayLogConfigId(): void { $this->resetPage('payLogPage'); }
    public function updatingPayLogDormitoryId(): void { $this->resetPage('payLogPage'); }
    public function updatingPayLogKelasId(): void { $this->resetPage('payLogPage'); }

    public function togglePayLogAdvancedFilters(): void
    {
        $this->showPayLogAdvancedFilters = !$this->showPayLogAdvancedFilters;
    }

    public function setPayLogDatePreset(string $preset): void
    {
        $this->payLogDate = '';
        if ($preset === 'today') {
            $this->payLogStartDate = now()->toDateString();
            $this->payLogEndDate   = now()->toDateString();
        } elseif ($preset === 'yesterday') {
            $this->payLogStartDate = now()->subDay()->toDateString();
            $this->payLogEndDate   = now()->subDay()->toDateString();
        } elseif ($preset === '7days') {
            $this->payLogStartDate = now()->subDays(6)->toDateString();
            $this->payLogEndDate   = now()->toDateString();
        } elseif ($preset === 'this_month') {
            $this->payLogStartDate = now()->startOfMonth()->toDateString();
            $this->payLogEndDate   = now()->endOfMonth()->toDateString();
        } elseif ($preset === 'last_month') {
            $this->payLogStartDate = now()->subMonth()->startOfMonth()->toDateString();
            $this->payLogEndDate   = now()->subMonth()->endOfMonth()->toDateString();
        } elseif ($preset === 'clear') {
            $this->payLogStartDate = '';
            $this->payLogEndDate   = '';
        }
        $this->resetPage('payLogPage');
    }

    public function resetPayLogFilters(): void
    {
        $this->payLogSearch      = '';
        $this->payLogMethod      = '';
        $this->payLogDate        = '';
        $this->payLogStartDate   = '';
        $this->payLogEndDate     = '';
        $this->payLogUser        = '';
        $this->payLogConfigId    = '';
        $this->payLogDormitoryId = '';
        $this->payLogKelasId     = '';
        $this->resetPage('payLogPage');
    }

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

        $this->genMonth = 1;
        $this->genYear = (int) now()->format('Y');

        $this->newConfigEffectiveFrom = now()->toDateString();
        $this->cashierYear = (int) now()->format('Y');

        $this->loadKitabPrices();
    }

    public string $genMode = 'single'; // 'single' or 'full_year'
    public bool   $showGeneratePreviewModal = false;
    public array  $previewGenStats = [];

    public bool   $showDeleteUnpaidModal = false;
    public ?string $configIdToDelete = null;
    public array  $deleteUnpaidStats = [];

    // =====================================================================
    // Kasir Wizard: "Buka Tagihan Di Muka / Susulan"
    // =====================================================================
    public bool    $showKasirAddBillModal  = false;
    public int     $kasirWizardStep        = 1;           // 1 = pilih tarif, 2 = pilih periode
    public ?string $kasirAddConfigId       = null;
    public int     $kasirSelectedYear      = 2026;
    public bool    $kasirSantriIsInTarget  = true;        // warning flag
    public array   $kasirAvailablePeriods  = [];          // computed list
    public array   $kasirSelectedPeriods   = [];          // checked by kasir
    public ?int    $kasirLastPeriodIndex   = null;        // smart range anchor

    public function openKasirAddBillModal(): void
    {
        if (!$this->selectedSantriId) {
            $this->toastError('Pilih santri terlebih dahulu.');
            return;
        }
        $this->kasirWizardStep       = 1;
        $this->kasirAddConfigId      = null;
        $this->kasirSelectedYear     = (int) now()->format('Y');
        $this->kasirSantriIsInTarget = true;
        $this->kasirAvailablePeriods = [];
        $this->kasirSelectedPeriods  = [];
        $this->kasirLastPeriodIndex  = null;
        $this->showKasirAddBillModal = true;
    }

    // Called by Livewire when kasirAddConfigId changes (wire:model.live)
    public function updatedKasirAddConfigId(): void
    {
        $this->kasirAvailablePeriods = [];
        $this->kasirSelectedPeriods  = [];
        $this->kasirLastPeriodIndex  = null;
        $this->kasirSantriIsInTarget = true;

        if (!$this->kasirAddConfigId || !$this->selectedSantriId) return;

        $config = BillingConfiguration::find($this->kasirAddConfigId);
        if (!$config) return;

        $billingService = new BillingService();
        $this->kasirSantriIsInTarget = $billingService->isSantriInTargetForConfig($config, $this->selectedSantriId);
        $this->kasirAvailablePeriods = $billingService->getAvailablePeriodsForSantri($config, $this->selectedSantriId, $this->kasirSelectedYear);
    }

    public function updatedKasirSelectedYear(): void
    {
        $this->kasirAvailablePeriods = [];
        $this->kasirSelectedPeriods  = [];
        $this->kasirLastPeriodIndex  = null;

        if (!$this->kasirAddConfigId || !$this->selectedSantriId) return;

        $config = BillingConfiguration::find($this->kasirAddConfigId);
        if (!$config) return;

        $billingService = new BillingService();
        $this->kasirAvailablePeriods = $billingService->getAvailablePeriodsForSantri($config, $this->selectedSantriId, $this->kasirSelectedYear);
    }

    public function kasirGoToStep2(): void
    {
        if (!$this->kasirAddConfigId) {
            $this->toastError('Pilih tarif terlebih dahulu.');
            return;
        }
        $this->updatedKasirAddConfigId(); // ensure periods are fresh
        $this->kasirWizardStep = 2;
    }

    public function kasirGoBackToStep1(): void
    {
        $this->kasirWizardStep      = 1;
        $this->kasirSelectedPeriods = [];
        $this->kasirLastPeriodIndex = null;
    }

    public function toggleKasirPeriod(int $index): void
    {
        $period = $this->kasirAvailablePeriods[$index] ?? null;
        if (!$period || $period['exists']) return; // ignore already-billed

        // Smart Range Selection: if last period index exists and user clicks another period index
        if ($this->kasirLastPeriodIndex !== null && $this->kasirLastPeriodIndex !== $index && in_array($this->kasirLastPeriodIndex, $this->kasirSelectedPeriods)) {
            $start = min($this->kasirLastPeriodIndex, $index);
            $end   = max($this->kasirLastPeriodIndex, $index);

            $rangeIndexes = [];
            for ($i = $start; $i <= $end; $i++) {
                $p = $this->kasirAvailablePeriods[$i] ?? null;
                if ($p && !$p['exists']) {
                    $rangeIndexes[] = $i;
                }
            }

            $this->kasirSelectedPeriods = array_values(array_unique(array_merge($this->kasirSelectedPeriods, $rangeIndexes)));
            $this->kasirLastPeriodIndex = $index;
            return;
        }

        if (in_array($index, $this->kasirSelectedPeriods)) {
            $this->kasirSelectedPeriods = array_values(array_filter(
                $this->kasirSelectedPeriods,
                fn($i) => $i !== $index
            ));
            $this->kasirLastPeriodIndex = null;
        } else {
            $this->kasirSelectedPeriods[] = $index;
            $this->kasirLastPeriodIndex = $index;
        }
    }

    public function selectAllKasirPeriods(): void
    {
        $available = [];
        foreach ($this->kasirAvailablePeriods as $idx => $period) {
            if (!$period['exists']) {
                $available[] = $idx;
            }
        }
        $this->kasirSelectedPeriods = $available;
        $this->kasirLastPeriodIndex = null;
    }

    public function selectUpToCurrentKasirPeriods(): void
    {
        $nowMonth = (int) now()->format('n');
        $nowYear  = (int) now()->format('Y');

        $available = [];
        foreach ($this->kasirAvailablePeriods as $idx => $period) {
            if ($period['exists']) continue;

            $pMonth = (int) ($period['month'] ?? 1);
            $pYear  = (int) ($period['year'] ?? $nowYear);

            $isDue = ($pYear < $nowYear) || ($pYear === $nowYear && $pMonth <= $nowMonth);
            if ($isDue) {
                $available[] = $idx;
            }
        }
        $this->kasirSelectedPeriods = $available;
        $this->kasirLastPeriodIndex = null;
    }

    public function selectSemester1KasirPeriods(): void
    {
        $available = [];
        foreach ($this->kasirAvailablePeriods as $idx => $period) {
            if ($period['exists']) continue;
            $pMonth = (int) ($period['month'] ?? 1);
            if ($pMonth >= 1 && $pMonth <= 6) {
                $available[] = $idx;
            }
        }
        $this->kasirSelectedPeriods = $available;
        $this->kasirLastPeriodIndex = null;
    }

    public function selectSemester2KasirPeriods(): void
    {
        $available = [];
        foreach ($this->kasirAvailablePeriods as $idx => $period) {
            if ($period['exists']) continue;
            $pMonth = (int) ($period['month'] ?? 1);
            if ($pMonth >= 7 && $pMonth <= 12) {
                $available[] = $idx;
            }
        }
        $this->kasirSelectedPeriods = $available;
        $this->kasirLastPeriodIndex = null;
    }

    public function clearKasirPeriods(): void
    {
        $this->kasirSelectedPeriods = [];
        $this->kasirLastPeriodIndex = null;
    }

    public function generateFutureBillForSelectedSantri(BillingService $billingService): void
    {
        if (!$this->selectedSantriId || !$this->kasirAddConfigId) {
            $this->toastError('Data tidak lengkap.');
            return;
        }
        if (empty($this->kasirSelectedPeriods)) {
            $this->toastError('Pilih minimal 1 periode.');
            return;
        }

        $config        = BillingConfiguration::findOrFail($this->kasirAddConfigId);
        $totalGenerated = 0;
        $totalSkipped   = 0;

        foreach ($this->kasirSelectedPeriods as $index) {
            $period = $this->kasirAvailablePeriods[$index] ?? null;
            if (!$period || $period['exists']) continue; // double-safety

            $res = $billingService->generateBillsFromConfig(
                $this->kasirAddConfigId,
                (int) $period['month'],
                (int) $period['year'],
                auth()->id() ?: User::first()?->id,
                null,
                $this->selectedSantriId   // force only this santri
            );
            $totalGenerated += $res['generated'];
            $totalSkipped   += $res['skipped'];
        }

        if ($totalGenerated > 0) {
            $msg = "{$totalGenerated} tagihan '{$config->label}' berhasil dibuka untuk santri ini!";
            session()->flash('message', $msg);
            $this->toastSuccess($msg);
        } else {
            $msg = 'Semua periode yang dipilih sudah ada tagihannya.';
            session()->flash('error', $msg);
            $this->toastError($msg);
        }

        $this->showKasirAddBillModal = false;
        $this->kasirSelectedPeriods  = [];
        $this->kasirAvailablePeriods = [];
        $this->selectSantri($this->selectedSantriId);
    }

    public function openDeleteUnpaidModal(string $configId): void
    {
        $config = BillingConfiguration::findOrFail($configId);

        $unpaidCount = Bill::where('billing_config_id', $configId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->where('amount_paid', 0)
            ->count();

        $paidCount = Bill::where('billing_config_id', $configId)
            ->where(function($q) {
                $q->where('amount_paid', '>', 0)
                  ->orWhere('status', 'paid');
            })
            ->count();

        $this->configIdToDelete = $configId;
        $this->deleteUnpaidStats = [
            'config_label' => $config->label,
            'unpaid_count' => $unpaidCount,
            'paid_count' => $paidCount,
            'total_count' => $unpaidCount + $paidCount,
        ];

        $this->showDeleteUnpaidModal = true;
    }

    public function confirmDeleteUnpaidBills(): void
    {
        if ($this->configIdToDelete) {
            $this->deleteAllUnpaidBillsForConfig($this->configIdToDelete);
        }
        $this->showDeleteUnpaidModal = false;
        $this->configIdToDelete = null;
    }

    public function openGeneratePreviewModal(): void
    {
        if (!$this->genMonth) {
            $this->genMonth = 1;
        }

        $this->validate([
            'genConfigId' => 'required',
            'genMonth'    => 'required|integer|min:1|max:12',
            'genYear'     => 'required|integer|min:2020|max:2030',
        ]);

        $config = BillingConfiguration::findOrFail($this->genConfigId);
        if (!$config->is_active) {
            $this->toastError("Konfigurasi tarif '{$config->label}' sedang nonaktif. Silakan aktifkan tarif terlebih dahulu di tab Konfigurasi Tarif & Target.");
            return;
        }
        $billingService = new BillingService();
        $targetStudents = $billingService->getTargetPersonsForConfig($config);
        $studentCount = $targetStudents->count();

        $periodsList = [];
        if (in_array($config->interval, ['once', 'insidental', 'event', 'sekali'])) {
            $periodsList[] = [
                'label' => "Insidental ({$this->genYear})",
                'month' => $config->effective_from ? (int)$config->effective_from->format('m') : 1,
            ];
        } elseif ($this->genMode === 'full_year') {
            if (in_array($config->interval, ['semester', '2x_yearly'])) {
                $periodsList[] = ['label' => 'Semester 1 (Jan–Jun)', 'month' => 1];
                $periodsList[] = ['label' => 'Semester 2 (Jul–Des)', 'month' => 7];
            } elseif (in_array($config->interval, ['caturwulan', '3x_yearly'])) {
                $periodsList[] = ['label' => 'Caturwulan 1 (Jan–Apr)', 'month' => 1];
                $periodsList[] = ['label' => 'Caturwulan 2 (Mei–Agt)', 'month' => 5];
                $periodsList[] = ['label' => 'Caturwulan 3 (Sep–Des)', 'month' => 9];
            } elseif (in_array($config->interval, ['triwulan', '4x_yearly'])) {
                $periodsList[] = ['label' => 'Triwulan 1 (Jan–Mar)', 'month' => 1];
                $periodsList[] = ['label' => 'Triwulan 2 (Apr–Jun)', 'month' => 4];
                $periodsList[] = ['label' => 'Triwulan 3 (Jul–Sep)', 'month' => 7];
                $periodsList[] = ['label' => 'Triwulan 4 (Okt–Des)', 'month' => 10];
            } elseif (in_array($config->interval, ['bimulanan', '6x_yearly'])) {
                $periodsList[] = ['label' => 'Dwibulanan 1 (Jan–Feb)', 'month' => 1];
                $periodsList[] = ['label' => 'Dwibulanan 2 (Mar–Apr)', 'month' => 3];
                $periodsList[] = ['label' => 'Dwibulanan 3 (Mei–Jun)', 'month' => 5];
                $periodsList[] = ['label' => 'Dwibulanan 4 (Jul–Agt)', 'month' => 7];
                $periodsList[] = ['label' => 'Dwibulanan 5 (Sep–Okt)', 'month' => 9];
                $periodsList[] = ['label' => 'Dwibulanan 6 (Nov–Des)', 'month' => 11];
            } else {
                $startMonth = (int)$this->genMonth;
                $startYear = (int)$this->genYear;
                for ($i = 0; $i < 12; $i++) {
                    $m = (($startMonth - 1 + $i) % 12) + 1;
                    $y = $startYear + (int)floor(($startMonth - 1 + $i) / 12);
                    $monthName = date('F', mktime(0, 0, 0, $m, 1));
                    $periodsList[] = ['label' => "Bulan {$m} ({$monthName} {$y})", 'month' => $m];
                }
            }
        } else {
            $m = (int)$this->genMonth;
            $label = $this->formatPeriodLabel($config->interval, $m, $this->genYear);
            $periodsList[] = ['label' => $label, 'month' => $m];
        }

        $periodCount = count($periodsList);
        $totalBillsToCreate = $studentCount * $periodCount;
        $totalAmount = (float)$config->amount * $totalBillsToCreate;

        $this->previewGenStats = [
            'config_label'  => $config->label,
            'config_amount' => (float)$config->amount,
            'interval'      => $config->interval,
            'target_type'   => $config->target_type,
            'student_count' => $studentCount,
            'period_count'  => $periodCount,
            'periods'       => $periodsList,
            'total_bills'   => $totalBillsToCreate,
            'total_amount'  => $totalAmount,
            'gen_year'      => $this->genYear,
            'gen_mode'      => $this->genMode,
        ];

        $this->showGeneratePreviewModal = true;
    }

    public function confirmGenerateBills(BillingService $billingService): void
    {
        if ($this->genMode === 'full_year') {
            $this->generateFullAcademicYearFromConfig($billingService);
        } else {
            $this->generateDynamicBills($billingService);
        }
        $this->showGeneratePreviewModal = false;
    }

    public function generateDynamicBills(BillingService $billingService): void
    {
        $this->validate([
            'genConfigId' => 'required',
            'genMonth' => 'required|integer|min:1|max:12',
            'genYear' => 'required|integer|min:2020|max:2030',
        ]);

        $config = BillingConfiguration::findOrFail($this->genConfigId);
        $isEventInterval = in_array($config->interval, ['once', 'insidental', 'event', 'sekali']);

        $periodMonth = (int) $this->genMonth;
        $periodSub = null;

        if (in_array($config->interval, ['semester', '2x_yearly'])) {
            $periodSub = ($periodMonth >= 1 && $periodMonth <= 2) ? $periodMonth : 1;
            $periodMonth = ($periodSub - 1) * 6 + 1; // 1 or 7
        } elseif (in_array($config->interval, ['caturwulan', '3x_yearly'])) {
            $periodSub = ($periodMonth >= 1 && $periodMonth <= 3) ? $periodMonth : 1;
            $periodMonth = ($periodSub - 1) * 4 + 1; // 1, 5, 9
        } elseif (in_array($config->interval, ['triwulan', '4x_yearly'])) {
            $periodSub = ($periodMonth >= 1 && $periodMonth <= 4) ? $periodMonth : 1;
            $periodMonth = ($periodSub - 1) * 3 + 1; // 1, 4, 7, 10
        } elseif (in_array($config->interval, ['bimulanan', '6x_yearly'])) {
            $periodSub = ($periodMonth >= 1 && $periodMonth <= 6) ? $periodMonth : 1;
            $periodMonth = ($periodSub - 1) * 2 + 1; // 1, 3, 5, 7, 9, 11
        } elseif ($isEventInterval) {
            $periodMonth = $config->effective_from ? (int)$config->effective_from->format('m') : 1;
        }

        $result = $billingService->generateBillsFromConfig(
            $this->genConfigId,
            $periodMonth,
            $this->genYear,
            auth()->id() ?: User::first()?->id,
            $periodSub
        );

        if ($isEventInterval) {
            session()->flash('message', "Tagihan Insidental '{$config->label}' untuk tahun {$this->genYear} berhasil diterbitkan: {$result['generated']} tagihan baru dibuat, {$result['skipped']} tagihan dilewati (sudah ada).");
        } else {
            $subMsg = $periodSub ? " (Gelombang {$periodSub})" : "";
            session()->flash('message', "Tagihan{$subMsg} berhasil dibuat: {$result['generated']} tagihan baru dibuat, {$result['skipped']} tagihan dilewati (sudah ada).");
        }
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
            $startMonth = (int)$this->genMonth;
            $startYear = (int)$this->genYear;

            for ($i = 0; $i < 12; $i++) {
                $m = (($startMonth - 1 + $i) % 12) + 1;
                $y = $startYear + (int)floor(($startMonth - 1 + $i) / 12);

                $res = $billingService->generateBillsFromConfig($config->id, $m, $y, auth()->id() ?: User::first()?->id);
                $totalGenerated += $res['generated'];
                $totalSkipped += $res['skipped'];
            }

            $endMonth = (($startMonth - 1 + 11) % 12) + 1;
            $endYear = $startYear + (int)floor(($startMonth - 1 + 11) / 12);

            $startMonthName = date('F', mktime(0, 0, 0, $startMonth, 1));
            $endMonthName = date('F', mktime(0, 0, 0, $endMonth, 1));

            session()->flash('message', "Tagihan 12 Bulan ({$startMonthName} {$startYear} s/d {$endMonthName} {$endYear}) untuk iuran '{$config->label}' berhasil diterbitkan: {$totalGenerated} tagihan baru, {$totalSkipped} tagihan dilewati.");

        } else {
            $periodCount = match($config->interval) {
                'bimulanan', '6x_yearly'  => 6,
                'caturwulan', '3x_yearly' => 3,
                'triwulan', '4x_yearly'   => 4,
                'semester', '2x_yearly'   => 2,
                default                   => 1,
            };

            if ($periodCount > 1) {
                $startMonth = (int)($this->genMonth ?: 1);
                $startYear  = (int)($this->genYear ?: date('Y'));
                $stepMonths = match($config->interval) {
                    'bimulanan', '6x_yearly'  => 2,
                    'caturwulan', '3x_yearly' => 4,
                    'triwulan', '4x_yearly'   => 3,
                    'semester', '2x_yearly'   => 6,
                    default                   => 12,
                };

                for ($p = 1; $p <= $periodCount; $p++) {
                    $offset = ($p - 1) * $stepMonths;
                    $monthForCycle = (($startMonth - 1 + $offset) % 12) + 1;
                    $yearForCycle  = $startYear + (int)floor(($startMonth - 1 + $offset) / 12);

                    $res = $billingService->generateBillsFromConfig($config->id, $monthForCycle, $yearForCycle, auth()->id() ?: User::first()?->id, $p);
                    $totalGenerated += $res['generated'];
                    $totalSkipped += $res['skipped'];
                }
                $typeName = match($periodCount) { 6 => 'Dwibulanan', 3 => 'Caturwulan', 4 => 'Triwulan', default => 'Semester' };
                session()->flash('message', "Tagihan {$periodCount} {$typeName} (mulai Bulan {$startMonth}/{$startYear}) untuk iuran '{$config->label}' berhasil diterbitkan: {$totalGenerated} tagihan baru, {$totalSkipped} tagihan dilewati.");
            } else {
                $res = $billingService->generateBillsFromConfig($config->id, $this->genMonth, $this->genYear, auth()->id() ?: User::first()?->id);
                session()->flash('message', "Tagihan iuran '{$config->label}' untuk periode {$this->genMonth}/{$this->genYear} berhasil diterbitkan: {$res['generated']} tagihan baru, {$res['skipped']} tagihan dilewati.");
            }
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

            session()->flash('message', "Tagihan 12 Bulan berurutan untuk iuran '{$config->label}' berhasil diterbitkan: {$totalGenerated} tagihan baru, {$totalSkipped} tagihan dilewati.");
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
            if (is_null($config->manager_role) && is_null($config->manager_ids)) {
                $hasAccess = true;
            } elseif (!is_null($config->manager_ids) && in_array($userId, (array)$config->manager_ids)) {
                $hasAccess = true;
            } elseif (!is_null($config->manager_role)) {
                foreach ($userRoles as $role) {
                    if (str_contains($config->manager_role, $role)) {
                        $hasAccess = true;
                        break;
                    }
                }
            }

            if (!$hasAccess) {
                session()->flash('error', 'Anda tidak memiliki akses untuk menghapus tagihan dari tarif ini.');
                return;
            }
        }

        $bills = Bill::where('billing_config_id', $configId)
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->get();

        $deletedCount = 0;
        $skippedPaid = 0;

        foreach ($bills as $bill) {
            if ($bill->amount_paid > 0 || $bill->status === 'paid' || $bill->status === 'partial') {
                $skippedPaid++;
                continue;
            }

            $bill->delete();
            $deletedCount++;
        }

        if ($skippedPaid > 0) {
            session()->flash('message', "Berhasil menghapus {$deletedCount} tagihan belum dibayar. {$skippedPaid} tagihan dilewati karena sudah ada pembayaran.");
        } else {
            session()->flash('message', "Berhasil menghapus seluruh {$deletedCount} tagihan untuk periode {$month}/{$year}.");
        }
    }

    public function deleteAllUnpaidBillsForConfig(string $configId): void
    {
        $config = BillingConfiguration::findOrFail($configId);

        // Security check for unit manager delegation
        $user = auth()->user();
        $isCentral = $user && ($user->hasRole('super-admin') || $user->hasRole('manajemen') || $user->hasRole('bendahara-pondok') || $user->hasRole('bendahara-pusat'));
        
        if (!$isCentral && $user) {
            $userRoles = $user->roles->pluck('name')->toArray();
            $userId = $user->id;

            $hasAccess = false;
            if (is_null($config->manager_role) && is_null($config->manager_ids)) {
                $hasAccess = true;
            } elseif (!is_null($config->manager_ids) && in_array($userId, (array)$config->manager_ids)) {
                $hasAccess = true;
            } elseif (!is_null($config->manager_role)) {
                foreach ($userRoles as $role) {
                    if (str_contains($config->manager_role, $role)) {
                        $hasAccess = true;
                        break;
                    }
                }
            }

            if (!$hasAccess) {
                session()->flash('error', 'Anda tidak memiliki akses untuk menghapus tagihan dari tarif ini.');
                return;
            }
        }

        $sessionMsg = "Berhasil memproses tindakan.";
    }

    public function viewHistoryDetail(string $configId, int $month, int $year, ?int $sub = null): void
    {
        $this->historyDetailConfigId = $configId;
        $this->historyDetailMonth = $month;
        $this->historyDetailYear = $year;
        $this->historyDetailSub = $sub;
        $this->historyDetailSearch = '';
        $this->historyDetailStatusFilter = '';
        $this->resetPage('historyDetailNavPage');
    }

    public function closeHistoryDetail(): void
    {
        $this->historyDetailConfigId = null;
        $this->historyDetailMonth = null;
        $this->historyDetailYear = null;
        $this->historyDetailSub = null;
        $this->historyDetailSearch = '';
        $this->historyDetailStatusFilter = '';
    }

    public function updatedHistoryDetailSearch(): void
    {
        $this->resetPage('historyDetailNavPage');
    }

    public function updatedHistoryDetailStatusFilter(): void
    {
        $this->resetPage('historyDetailNavPage');
    }

    public function getHistoryDetailStatsProperty(): array
    {
        if (!$this->historyDetailConfigId || !$this->historyDetailYear) {
            return [];
        }

        $config = BillingConfiguration::find($this->historyDetailConfigId);

        $query = Bill::where('billing_config_id', $this->historyDetailConfigId)
            ->where('period_year', $this->historyDetailYear);

        if ($this->historyDetailSub) {
            $query->where('period_sub', $this->historyDetailSub);
        } else {
            $query->where('period_month', $this->historyDetailMonth);
        }

        $bills = $query->get();

        $totalCount = $bills->count();
        $totalAmount = $bills->sum('amount');

        $paidBills = $bills->where('status', 'paid');
        $paidCount = $paidBills->count();
        $paidAmount = $paidBills->sum('amount');

        $partialBills = $bills->where('status', 'partial');
        $partialCount = $partialBills->count();
        $partialRemaining = $partialBills->sum(fn($b) => $b->amount - $b->amount_paid);

        $unpaidBills = $bills->where('status', 'unpaid');
        $unpaidCount = $unpaidBills->count();
        $unpaidAmount = $unpaidBills->sum('amount');

        $progressPercent = $totalCount > 0 ? round(($paidCount / $totalCount) * 100, 1) : 0;

        return [
            'config_label'      => $config?->label ?? 'Iuran',
            'interval'          => $config?->interval ?? 'monthly',
            'period_label'      => $this->formatPeriodLabel($config?->interval, $this->historyDetailMonth, $this->historyDetailYear, $this->historyDetailSub),
            'total_count'       => $totalCount,
            'total_amount'      => $totalAmount,
            'paid_count'        => $paidCount,
            'paid_amount'       => $paidAmount,
            'partial_count'     => $partialCount,
            'partial_remaining' => $partialRemaining,
            'unpaid_count'      => $unpaidCount,
            'unpaid_amount'     => $unpaidAmount,
            'progress_percent'  => $progressPercent,
            'created_at'        => $bills->first()?->created_at,
        ];
    }

    public function getHistoryDetailBillsProperty()
    {
        if (!$this->historyDetailConfigId || !$this->historyDetailYear) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        }

        $query = Bill::where('billing_config_id', $this->historyDetailConfigId)
            ->where('period_year', $this->historyDetailYear)
            ->with(['person', 'person.santriProfile']);

        if ($this->historyDetailSub) {
            $query->where('period_sub', $this->historyDetailSub);
        } else {
            $query->where('period_month', $this->historyDetailMonth);
        }

        if ($this->historyDetailStatusFilter !== '') {
            $query->where('status', $this->historyDetailStatusFilter);
        }

        if (trim($this->historyDetailSearch) !== '') {
            $search = '%' . trim($this->historyDetailSearch) . '%';
            $query->whereHas('person', function($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('nik', 'like', $search)
                  ->orWhereHas('santriProfile', function($sq) use ($search) {
                      $sq->where('additional_info->nis', 'like', $search)
                        ->orWhere('additional_info->nisn', 'like', $search);
                  });
            });
        }

        return $query->orderBy('status', 'desc')->paginate(15, ['*'], 'historyDetailNavPage');
    }

    public function openBatchDeleteConfirmModal(string $configId, int $month, int $year): void
    {
        $config = BillingConfiguration::findOrFail($configId);

        // Security check for unit manager delegation
        $user = auth()->user();
        $isCentral = $user && ($user->hasRole('super-admin') || $user->hasRole('manajemen') || $user->hasRole('bendahara-pondok') || $user->hasRole('bendahara-pusat'));
        
        if (!$isCentral && $user) {
            $userRoles = $user->roles->pluck('name')->toArray();
            $userId = $user->id;

            $hasAccess = false;
            if (is_null($config->manager_role) && is_null($config->manager_ids)) {
                $hasAccess = true;
            } elseif (!is_null($config->manager_ids) && in_array($userId, (array)$config->manager_ids)) {
                $hasAccess = true;
            } elseif (!is_null($config->manager_role)) {
                foreach ($userRoles as $role) {
                    if (str_contains($config->manager_role, $role)) {
                        $hasAccess = true;
                        break;
                    }
                }
            }

            if (!$hasAccess) {
                session()->flash('error', 'Anda tidak memiliki akses untuk menghapus tagihan dari tarif ini.');
                return;
            }
        }

        $query = Bill::where('billing_config_id', $configId)
            ->where('period_year', $year)
            ->where('status', 'unpaid')
            ->where('amount_paid', 0);

        if (in_array($config->interval, ['semester', '2x_yearly'])) {
            $semMonths = ($month >= 7 || $month == 2) ? [7, 2] : [1];
            $query->whereIn('period_month', $semMonths);
        } else {
            $query->where('period_month', $month);
        }

        $unpaidBills = $query->get();

        $this->deleteType = 'batch';
        $this->deleteConfigId = $configId;
        $this->deletePeriodMonth = $month;
        $this->deletePeriodYear = $year;

        $periodLabel = $this->formatPeriodLabel($config->interval, $month, $year);

        $this->deleteConfirmData = [
            'title'        => 'Hapus Massal Tagihan Belum Dibayar (Unpaid)',
            'config_label' => $config->label,
            'period_label' => $periodLabel,
            'count'        => $unpaidBills->count(),
            'total_amount' => $unpaidBills->sum('amount'),
            'warning'      => 'Seluruh tagihan yang belum dibayar pada periode ini akan dihapus permanen. Tagihan yang sudah dibayar/cicilan TIDAK akan terpengaruh.',
        ];

        $this->showDeleteConfirmModal = true;
    }

    public function openIndividualDeleteConfirmModal(string $billId): void
    {
        $bill = Bill::with(['config', 'person'])->findOrFail($billId);

        if ($bill->status === 'paid' || $bill->amount_paid > 0) {
            session()->flash('error', 'Tagihan yang sudah dibayar lunas atau dicicil tidak dapat dihapus demi integritas pembukuan.');
            return;
        }

        $this->deleteType = 'individual';
        $this->deleteBillId = $billId;

        $periodLabel = $this->formatPeriodLabel($bill->config?->interval, $bill->period_month, $bill->period_year, $bill->period_sub);

        $this->deleteConfirmData = [
            'title'        => 'Hapus Tagihan Santri Spesifik',
            'santri_name'  => $bill->person?->name ?? 'Santri',
            'nis'          => $bill->person?->nis ?? '-',
            'config_label' => $bill->config?->label ?? str_replace('_', ' ', $bill->bill_type),
            'period_label' => $periodLabel,
            'amount'       => $bill->amount,
            'warning'      => 'Tagihan untuk santri ini akan dibatalkan & dihapus dari sistem. Tagihan santri lainnya dalam periode ini tetap aman.',
        ];

        $this->showDeleteConfirmModal = true;
    }

    public function executeConfirmedDeletion(): void
    {
        if ($this->deleteType === 'batch' && $this->deleteConfigId) {
            $config = BillingConfiguration::find($this->deleteConfigId);
            $query = Bill::where('billing_config_id', $this->deleteConfigId)
                ->where('period_year', $this->deletePeriodYear)
                ->where('status', 'unpaid')
                ->where('amount_paid', 0);

            if (in_array($config?->interval, ['semester', '2x_yearly'])) {
                $semMonths = ($this->deletePeriodMonth >= 7 || $this->deletePeriodMonth == 2) ? [7, 2] : [1];
                $query->whereIn('period_month', $semMonths);
            } else {
                $query->where('period_month', $this->deletePeriodMonth);
            }

            $bills = $query->get();

            $deletedCount = 0;
            foreach ($bills as $b) {
                $b->delete();
                $deletedCount++;
            }

            session()->flash('message', "Berhasil membatalkan & menghapus massal {$deletedCount} tagihan belum dibayar untuk '{$config?->label}'.");

            if ($this->historyDetailConfigId === $this->deleteConfigId && $this->historyDetailYear === $this->deletePeriodYear) {
                $this->closeHistoryDetail();
            }
        } elseif ($this->deleteType === 'individual' && $this->deleteBillId) {
            $bill = Bill::with(['config', 'person'])->find($this->deleteBillId);
            if ($bill && $bill->status === 'unpaid' && $bill->amount_paid == 0) {
                $santriName = $bill->person?->name;
                $configLabel = $bill->config?->label;
                $bill->delete();
                session()->flash('message', "Berhasil menghapus tagihan '{$configLabel}' milik santri {$santriName}.");
            }
        }

        $this->showDeleteConfirmModal = false;
        $this->deleteType = null;
        $this->deleteBillId = null;
        $this->deleteConfigId = null;
    }

    public function formatPeriodLabel(?string $interval, int $month, int $year, ?int $sub = null): string
    {
        if (in_array($interval, ['bimulanan', '6x_yearly'])) {
            $labels = [
                1 => 'Dwibulanan 1 (Jan–Feb)',
                2 => 'Dwibulanan 2 (Mar–Apr)',
                3 => 'Dwibulanan 3 (Mei–Jun)',
                4 => 'Dwibulanan 4 (Jul–Agt)',
                5 => 'Dwibulanan 5 (Sep–Okt)',
                6 => 'Dwibulanan 6 (Nov–Des)',
            ];
            $bNum = $sub ?? ($month > 6 ? (int)ceil($month / 2) : $month);
            return ($labels[$bNum] ?? "Dwibulanan {$bNum}") . " {$year}";
        }

        if (in_array($interval, ['triwulan', '4x_yearly'])) {
            $labels = [
                1 => 'Triwulan 1 (Jan–Mar)',
                2 => 'Triwulan 2 (Apr–Jun)',
                3 => 'Triwulan 3 (Jul–Sep)',
                4 => 'Triwulan 4 (Okt–Des)',
            ];
            $twNum = $sub ?? ($month > 4 ? (int)ceil($month / 3) : $month);
            return ($labels[$twNum] ?? "Triwulan {$twNum}") . " {$year}";
        }

        if (in_array($interval, ['caturwulan', '3x_yearly'])) {
            $labels = [
                1 => 'Caturwulan 1 (Jan–Apr)',
                2 => 'Caturwulan 2 (Mei–Agt)',
                3 => 'Caturwulan 3 (Sep–Des)',
            ];
            $cwNum = $sub ?? ($month > 3 ? ($month <= 4 ? 1 : ($month <= 8 ? 2 : 3)) : $month);
            return ($labels[$cwNum] ?? "Caturwulan {$cwNum}") . " {$year}";
        }

        if (in_array($interval, ['semester', '2x_yearly'])) {
            $semNum = ($sub === 2 || $month >= 7 || $month === 2) ? 2 : 1;
            return "Semester {$semNum} {$year}";
        }

        if (in_array($interval, ['once', 'insidental', 'event', 'sekali'])) {
            return "Insidental ({$year})";
        }

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $mName = $monthNames[$month] ?? "Bulan {$month}";
        $subLabel = $sub ? " Gel.{$sub}" : "";

        return "{$mName} {$year}{$subLabel}";
    }



    public bool   $showTariffActionModal = false;
    public ?string $tariffActionConfigId = null;
    public string  $tariffActionType     = ''; // 'toggle_status' | 'delete'
    public array   $tariffActionData     = [];

    public function openTariffActionModal(string $configId, string $actionType): void
    {
        $config = BillingConfiguration::find($configId);
        if (!$config) {
            $this->toastError('Konfigurasi tarif tidak ditemukan.');
            return;
        }

        $labelBase = $config->label ?: str_replace('_', ' ', $config->type);
        $this->tariffActionConfigId = $configId;
        $this->tariffActionType     = $actionType;

        if ($actionType === 'toggle_status') {
            $isActivating = !$config->is_active;
            $this->tariffActionData = [
                'title'        => $isActivating ? 'Konfirmasi Aktifkan Tarif' : 'Konfirmasi Nonaktifkan Tarif',
                'message'      => $isActivating 
                    ? "Apakah Anda YAKIN ingin mengaktifkan kembali tarif '{$labelBase}'? Tarif ini akan dapat diterbitkan kembali ke santri di periode mendatang."
                    : "Apakah Anda YAKIN ingin menonaktifkan tarif '{$labelBase}'? Tarif ini tidak akan muncul di opsi penerbitan bulan berikutnya, tetapi riwayat tagihan terdahulu tetap 100% aman.",
                'button_text'  => $isActivating ? 'Ya, Aktifkan Kembali' : 'Ya, Nonaktifkan Tarif',
                'button_color' => $isActivating ? 'emerald' : 'amber',
                'label'        => $labelBase,
                'amount'       => $config->amount,
                'status_now'   => $config->is_active ? 'Aktif' : 'Nonaktif',
            ];
        } elseif ($actionType === 'delete') {
            $issuedCount = Bill::where('billing_config_id', $configId)->count();
            if ($issuedCount > 0) {
                $this->toastError("Tarif '{$labelBase}' tidak dapat dihapus permanent karena sudah memiliki {$issuedCount} tagihan terbit. Silakan gunakan tombol 'Nonaktifkan' untuk mematikan penerbitan tarif ini.");
                return;
            }

            $this->tariffActionData = [
                'title'        => 'Konfirmasi Hapus Permanent Tarif',
                'message'      => "Apakah Anda YAKIN ingin menghapus permanent tarif '{$labelBase}'? Tarif ini belum pernah diterbitkan ke santri dan akan dihapus dari sistem.",
                'button_text'  => 'Ya, Hapus Permanent',
                'button_color' => 'rose',
                'label'        => $labelBase,
                'amount'       => $config->amount,
                'status_now'   => $config->is_active ? 'Aktif' : 'Nonaktif',
            ];
        }

        $this->showTariffActionModal = true;
    }

    public function executeConfirmedTariffAction(): void
    {
        $this->showTariffActionModal = false;

        if ($this->tariffActionType === 'toggle_status' && $this->tariffActionConfigId) {
            $this->toggleConfigStatus($this->tariffActionConfigId);
        } elseif ($this->tariffActionType === 'delete' && $this->tariffActionConfigId) {
            $this->deleteConfig($this->tariffActionConfigId);
        }

        $this->tariffActionConfigId = null;
        $this->tariffActionType     = '';
    }

    public function duplicateConfig(string $id): void
    {
        $config = BillingConfiguration::find($id);
        if (!$config) {
            $this->toastError('Konfigurasi tarif tidak ditemukan.');
            return;
        }

        $labelBase = $config->label ?: str_replace('_', ' ', $config->type);
        $this->toastSuccess("Membuka form pembuatan tarif salinan dari '{$labelBase}'.");
        $this->redirect(route('keuangan.billing.create', ['copy_from' => $id]), navigate: true);
    }

    public function toggleConfigStatus(string $id): void
    {
        $config = BillingConfiguration::find($id);
        if (!$config) {
            $this->toastError('Konfigurasi tarif tidak ditemukan.');
            return;
        }

        $config->is_active = !$config->is_active;
        $config->save();

        $statusLabel = $config->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';
        $labelBase = $config->label ?: str_replace('_', ' ', $config->type);
        $this->toastSuccess("Tarif '{$labelBase}' berhasil {$statusLabel}.");
    }

    public function deleteConfig(string $id): void
    {
        $config = BillingConfiguration::find($id);
        if (!$config) {
            $this->toastError('Konfigurasi tarif tidak ditemukan.');
            return;
        }

        $labelBase = $config->label ?: str_replace('_', ' ', $config->type);
        $issuedCount = Bill::where('billing_config_id', $id)->count();
        if ($issuedCount > 0) {
            $this->toastError("Tarif '{$labelBase}' tidak dapat dihapus permanent karena sudah memiliki {$issuedCount} tagihan terbit. Silakan gunakan tombol 'Nonaktifkan' untuk mematikan penerbitan tarif ini.");
            return;
        }

        $config->delete();
        $this->toastSuccess("Konfigurasi tarif '{$labelBase}' berhasil dihapus.");
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
                    $date1 = $ub->due_date ? $ub->due_date->toDateString() : sprintf('%04d-%02d-01', $ub->period_year, $ub->period_month);
                    $date2 = $bill->due_date ? $bill->due_date->toDateString() : sprintf('%04d-%02d-01', $bill->period_year, $bill->period_month);

                    $isOlder = false;
                    if ($date1 !== $date2) {
                        $isOlder = $date1 < $date2;
                    } else {
                        $isOlder = $ub->created_at < $bill->created_at;
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
                    $date1 = $ub->due_date ? $ub->due_date->toDateString() : sprintf('%04d-%02d-01', $ub->period_year, $ub->period_month);
                    $date2 = $bill->due_date ? $bill->due_date->toDateString() : sprintf('%04d-%02d-01', $bill->period_year, $bill->period_month);

                    $isNewer = false;
                    if ($date1 !== $date2) {
                        $isNewer = $date1 > $date2;
                    } else {
                        $isNewer = $ub->created_at > $bill->created_at;
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

    public function selectUpToCurrentMonth(string $configId): void
    {
        if (!$this->selectedSantriId) {
            return;
        }

        $nowMonth = (int) now()->format('n');
        $nowYear = (int) now()->format('Y');

        $bills = Bill::where('person_id', $this->selectedSantriId)
            ->where('billing_config_id', $configId)
            ->where('period_year', $this->cashierYear)
            ->whereIn('status', ['unpaid', 'partial'])
            ->get();

        $selectedAdd = [];
        foreach ($bills as $bill) {
            $isDueOrCurrent = ($this->cashierYear < $nowYear) 
                || ($this->cashierYear === $nowYear && $bill->period_month <= $nowMonth);

            if ($isDueOrCurrent) {
                $selectedAdd[] = $bill->id;
            }
        }

        if (!empty($selectedAdd)) {
            $this->selectedBillIds = array_values(array_unique(array_merge($this->selectedBillIds, $selectedAdd)));
            $this->previousSelectedBillIds = $this->selectedBillIds;
            $this->toastSuccess(count($selectedAdd) . ' tagihan s.d. bulan ini berhasil dipilih!');
        } else {
            $this->toastInfo('Semua tagihan s.d. bulan ini sudah lunas atau dipilih.');
        }
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

        return $query->where(function($parentQ) use ($userRoles, $userId) {
            $parentQ->whereNull('billing_config_id')
                ->orWhereHas('config', function($q) use ($userRoles, $userId) {
                    $q->where(function($sub) use ($userRoles, $userId) {
                        $sub->whereNull('manager_role')->whereNull('manager_ids');
                        $sub->orWhereJsonContains('manager_ids', $userId);
                        foreach ($userRoles as $role) {
                            $sub->orWhere('manager_role', 'like', '%' . $role . '%');
                        }
                    });
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
            $cid = $bill->billing_config_id ?: 'custom_' . $bill->bill_type;
            if (!isset($configs[$cid])) {
                $configs[$cid] = [
                    'label'  => $bill->config?->label ?? str_replace('_', ' ', $bill->bill_type),
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
            ->whereHas('config', fn($q) => $q->whereIn('interval', ['semester', '2x_yearly', 'caturwulan', '3x_yearly', 'triwulan', '4x_yearly', 'bimulanan', '6x_yearly']))
            ->with('config');

        $query = $this->applyManagerRoleScope($query);
        $bills = $query->orderBy('period_month')->get();

        // Group: configId -> ['label' => ..., 'interval' => ..., 'max_period' => ..., 'bills' => [...]]
        $configs = [];
        foreach ($bills as $bill) {
            $cid = $bill->billing_config_id ?: 'sem_' . $bill->bill_type;
            $interval = $bill->config?->interval ?? 'semester';

            $maxPeriod = match($interval) {
                'bimulanan', '6x_yearly'  => 6,
                'caturwulan', '3x_yearly' => 3,
                'triwulan', '4x_yearly'   => 4,
                default                   => 2,
            };

            $typeTitle = match($interval) {
                'bimulanan', '6x_yearly'  => 'Dwibulanan',
                'caturwulan', '3x_yearly' => 'Caturwulan',
                'triwulan', '4x_yearly'   => 'Triwulan',
                default                   => 'Semester',
            };

            if (!isset($configs[$cid])) {
                $configs[$cid] = [
                    'label'      => $bill->config?->label ?? str_replace('_', ' ', $bill->bill_type),
                    'interval'   => $interval,
                    'max_period' => $maxPeriod,
                    'type_title' => $typeTitle,
                    'bills'      => [],
                ];
            }

            // Determine 1-based cycle index (1..max_period) for grid column placement
            $cycleIndex = $bill->period_sub;
            if (!$cycleIndex) {
                $m = $bill->period_month ?? 1;
                $cycleIndex = match($interval) {
                    'bimulanan', '6x_yearly'  => (int)ceil($m / 2),
                    'caturwulan', '3x_yearly' => $m <= 4 ? 1 : ($m <= 8 ? 2 : 3),
                    'triwulan', '4x_yearly'   => (int)ceil($m / 3),
                    default                   => $m <= 6 ? 1 : 2,
                };
            }

            $configs[$cid]['bills'][$cycleIndex] = $bill;
        }

        return $configs;
    }

    public function getInsidentalBillsProperty()
    {
        if (!$this->selectedSantriId) return collect();

        $query = Bill::where('person_id', $this->selectedSantriId)
            ->where(function($q) {
                $q->whereNull('billing_config_id')
                  ->orWhereHas('config', fn($sq) => $sq->whereNotIn('interval', ['monthly', 'semester', '2x_yearly', 'caturwulan', '3x_yearly', 'triwulan', '4x_yearly', 'bimulanan', '6x_yearly']));
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

    // Void Modal State
    public bool $showVoidModal = false;
    public ?string $paymentToVoidId = null;
    public ?array $paymentToVoidData = null;

    public function confirmVoidPayment(string $paymentId): void
    {
        $user = auth()->user();
        $isCentral = $user && ($user->hasRole('super-admin') || $user->hasRole('manajemen') || $user->hasRole('bendahara-pondok') || $user->hasRole('bendahara-pusat') || $user->hasRole('pengasuh'));

        $payment = BillPayment::with(['bill.person', 'bill.config', 'logger'])->find($paymentId);
        if (!$payment) {
            $this->toastError('Data transaksi pembayaran tidak ditemukan.');
            return;
        }

        $canVoid = $isCentral 
            || ($user && $payment->logged_by === $user->id)
            || ($user && $user->hasPermissionTo('void-pembayaran'));

        if (!$canVoid) {
            $this->toastError('Anda tidak memiliki wewenang untuk membatalkan (void) transaksi pembayaran ini.');
            return;
        }

        $bill = $payment->bill;
        $config = $bill?->config;

        $periodLabel = '—';
        if ($bill) {
            if ($config && $config->interval === 'semester') {
                $periodLabel = 'Semester ' . $bill->period_month . ' / ' . $bill->period_year;
            } elseif ($config && in_array($config->interval, ['once', 'insidental', 'event', 'sekali'])) {
                $periodLabel = 'Event / ' . $bill->period_year;
            } else {
                $months = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
                $mName = $months[$bill->period_month] ?? ('Bulan ' . $bill->period_month);
                $periodLabel = $mName . ' ' . $bill->period_year . ($bill->period_sub ? ' (Gel. ' . $bill->period_sub . ')' : '');
            }
        }

        $this->paymentToVoidId = $payment->id;
        $this->paymentToVoidData = [
            'id'             => $payment->id,
            'amount_paid'    => $payment->amount_paid,
            'payment_date'   => $payment->payment_date ? $payment->payment_date->translatedFormat('d M Y') : '—',
            'created_at'     => $payment->created_at->format('H:i') . ' WIB',
            'payment_method' => strtoupper($payment->payment_method),
            'notes'          => $payment->notes,
            'santri_name'    => $payment->bill?->person?->name ?? 'Santri',
            'santri_nis'     => $payment->bill?->person?->nis ?? '—',
            'config_label'   => $payment->bill?->config?->label ?? ($payment->bill?->bill_type ? str_replace('_', ' ', $payment->bill->bill_type) : '—'),
            'period_label'   => $periodLabel,
            'logger_name'    => $payment->logger?->name ?? 'Sistem',
        ];

        $this->showVoidModal = true;
    }

    public function closeVoidModal(): void
    {
        $this->showVoidModal = false;
        $this->paymentToVoidId = null;
        $this->paymentToVoidData = null;
    }

    public function executeVoidPayment(): void
    {
        if (!$this->paymentToVoidId) {
            return;
        }

        $user = auth()->user();
        $isCentral = $user && ($user->hasRole('super-admin') || $user->hasRole('manajemen') || $user->hasRole('bendahara-pondok') || $user->hasRole('bendahara-pusat') || $user->hasRole('pengasuh'));

        $payment = BillPayment::find($this->paymentToVoidId);
        if (!$payment) {
            $this->toastError('Data transaksi pembayaran tidak ditemukan.');
            $this->closeVoidModal();
            return;
        }

        $canVoid = $isCentral 
            || ($user && $payment->logged_by === $user->id)
            || ($user && $user->hasPermissionTo('void-pembayaran'));

        if (!$canVoid) {
            $this->toastError('Anda tidak memiliki wewenang untuk membatalkan (void) transaksi pembayaran ini.');
            $this->closeVoidModal();
            return;
        }

        $amountFormatted = number_format($payment->amount_paid, 0, ',', '.');
        $payment->delete();

        $this->toastSuccess("Pencatatan pembayaran sebesar Rp {$amountFormatted} berhasil dibatalkan & sisa tagihan dikembalikan.");
        $this->closeVoidModal();
    }

    public function deletePayment(string $paymentId): void
    {
        $this->confirmVoidPayment($paymentId);
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

        // Apply Search & Type Filter for Dispensasi / Exception List
        if (!empty($this->exceptionSearch)) {
            $kw = strtolower(trim($this->exceptionSearch));
            $exceptions = $exceptions->filter(function ($exc) use ($kw) {
                $matchNotes  = str_contains(strtolower($exc->notes ?? ''), $kw);
                $matchConfig = str_contains(strtolower($exc->configuration->label ?? ''), $kw);
                $matchPerson = str_contains(strtolower($exc->person->name ?? ''), $kw);
                return $matchNotes || $matchConfig || $matchPerson;
            });
        }

        if (!empty($this->exceptionTypeFilter)) {
            $exceptions = $exceptions->filter(fn($exc) => $exc->exception_type === $this->exceptionTypeFilter);
        }

        $user = auth()->user();
        $isCentral = $user && ($user->hasRole('super-admin') || $user->hasRole('manajemen') || $user->hasRole('bendahara-pondok') || $user->hasRole('bendahara-pusat'));

        $ratesQuery = BillingConfiguration::with('creator');

        if (trim($this->rateSearchQuery) !== '') {
            $s = '%' . trim($this->rateSearchQuery) . '%';
            $ratesQuery->where(function($q) use ($s) {
                $q->where('label', 'like', $s)
                  ->orWhere('type', 'like', $s);
            });
        }

        if ($this->rateStatusFilter === 'active') {
            $ratesQuery->where('is_active', true);
        } elseif ($this->rateStatusFilter === 'inactive') {
            $ratesQuery->where('is_active', false);
        }

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

            $allowedConfigIds = BillingConfiguration::all()->filter($filterFunc)->pluck('id')->toArray();
            $ratesQuery->whereIn('id', $allowedConfigIds);
            $installmentConfigs = $installmentConfigs->filter($filterFunc);
        }

        $activeConfigs = $ratesQuery->orderBy('is_active', 'desc')->orderBy('label')->paginate(10, ['*'], 'ratesPage');

        // Configs specifically for Generator dropdown (Only active tariffs allowed to generate)
        $generatorConfigs = BillingConfiguration::where('is_active', true)->orderBy('label')->get();
        if (!$isCentral && $user) {
            $generatorConfigs = $generatorConfigs->filter($filterFunc);
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
            ->when($this->payLogStartDate, function ($q) {
                $q->whereDate('payment_date', '>=', $this->payLogStartDate);
            })
            ->when($this->payLogEndDate, function ($q) {
                $q->whereDate('payment_date', '<=', $this->payLogEndDate);
            })
            ->when($this->payLogUser, function ($q) {
                $q->where('logged_by', $this->payLogUser);
            })
            ->when($this->payLogConfigId, function ($q) {
                $q->whereHas('bill', fn($bq) => $bq->where('billing_config_id', $this->payLogConfigId));
            })
            ->when($this->payLogDormitoryId, function ($q) {
                $q->whereHas('bill.person.roomAssignments', function($rq) {
                    $rq->where('is_active', true)
                      ->whereHas('room', fn($rmq) => $rmq->where('dormitory_id', $this->payLogDormitoryId));
                });
            })
            ->when($this->payLogKelasId, function ($q) {
                $q->whereHas('bill.person.madrasahEnrollments', function($mq) {
                    $mq->where('is_active', true)->where('kelas_id', $this->payLogKelasId);
                });
            })
            ->orderBy('created_at', 'desc');

        if (!$isCentral && $user) {
            $allowedConfigIds = BillingConfiguration::all()->filter($filterFunc)->pluck('id')->toArray();
            $paymentsLogQuery->where(function ($q) use ($allowedConfigIds, $user) {
                $q->where('logged_by', $user->id)
                  ->orWhereHas('bill', function ($bq) use ($allowedConfigIds) {
                      $bq->whereIn('billing_config_id', $allowedConfigIds);
                  });
            });
        }

        // Summary Statistics for the filtered log query
        $summaryBaseQuery = clone $paymentsLogQuery;
        $payLogTotalCash = (float) (clone $summaryBaseQuery)->where('payment_method', 'cash')->sum('amount_paid');
        $payLogTotalTransfer = (float) (clone $summaryBaseQuery)->where('payment_method', 'transfer')->sum('amount_paid');
        $payLogTotalCount = (int) (clone $summaryBaseQuery)->count();

        $paymentsLog = $paymentsLogQuery->paginate(15, pageName: 'payLogPage');
        $generationHistory = $historyQuery->paginate(10, pageName: 'historyPage');

        // Dropdown Lists for Advanced Filters
        $cashierUsers = User::whereIn('id', BillPayment::select('logged_by')->distinct())->orderBy('name')->get(['id', 'name']);
        $payLogConfigs = BillingConfiguration::orderBy('label')->get(['id', 'label', 'type']);
        $payLogDormitories = Dormitory::when($this->genderScope(), fn($q, $g) => $q->where('gender', $g))->orderBy('name')->get(['id', 'name', 'gender']);
        $payLogClasses = MadrasahKelas::where('is_active', true)->orderBy('name')->get(['id', 'name', 'academic_year']);

        $regItemsQuery = BillingConfiguration::where('type', 'pendaftaran');
        if (!empty($this->regItemSearch)) {
            $regItemsQuery->where('label', 'like', '%' . $this->regItemSearch . '%');
        }
        if (!empty($this->regItemCategoryFilter)) {
            $regItemsQuery->where('target_filters->category', $this->regItemCategoryFilter);
        }
        if (!empty($this->regItemGenderFilter)) {
            $regItemsQuery->where(function ($q) {
                $q->where('target_filters->gender', $this->regItemGenderFilter)
                  ->orWhere('target_filters->gender', 'ALL');
            });
        }
        $registrationItems = $regItemsQuery->orderBy('is_active', 'desc')->orderBy('created_at', 'desc')->get();

        return view('livewire.keuangan.billing-manager', [
            'registrationItems'   => $registrationItems,
            'santriSearchResults' => $santriSearch,
            'recentSantri'        => $recentSantri,
            'roomsForKomplek'     => $roomsForKomplek,
            'instSearchResults'   => $instSearch,
            'selectedSantri'      => $selectedSantri,
            'exceptions'          => $exceptions,
            'activeConfigs'       => $activeConfigs,
            'generatorConfigs'    => $generatorConfigs,
            'allSantriList'       => Person::whereHas('activeRoles', fn($q) => $q->where('role_type', 'santri')->where('enrollment_status', 'aktif'))->when($this->genderScope(), fn($q, $g) => $q->where('gender', $g))->orderBy('name')->get(['id', 'name', 'gender']),
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
            'payLogTotalCash'     => $payLogTotalCash,
            'payLogTotalTransfer' => $payLogTotalTransfer,
            'payLogTotalCount'    => $payLogTotalCount,
            'cashierUsers'        => $cashierUsers,
            'payLogConfigs'       => $payLogConfigs,
            'payLogDormitories'   => $payLogDormitories,
            'payLogClasses'       => $payLogClasses,
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
