<?php

namespace App\Livewire\Keuangan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\Concerns\SendsToast;
use App\Traits\HasGenderScope;
use App\Modules\Core\Models\Person;
use App\Modules\Keuangan\Models\SantriLoan;
use App\Modules\Keuangan\Services\SantriLoanService;
use Illuminate\Database\Eloquent\Builder;

class PinjamanSantriManager extends Component
{
    use SendsToast, HasGenderScope, WithPagination;

    // Filters
    public string $search = '';
    public string $statusFilter = 'active'; // 'active' (unpaid & partial) | 'all' | 'unpaid' | 'partial' | 'paid' | 'cancelled'
    public string $genderFilter = ''; // '' | 'L' | 'P'
    public string $filterDateFrom = '';
    public string $filterDateTo = '';

    // Modal Create Loan
    public bool $showCreateModal = false;
    public string $personSearch = '';
    public string $selectedPersonId = '';
    public string $selectedPersonName = '';
    public ?string $selectedPersonNIS = null;
    public ?string $selectedPersonGender = null;
    public string $amount = '';
    public string $borrowedAt = '';
    public string $dueDate = '';
    public string $reason = '';
    public string $notes = '';
    public bool $isVisibleToGuardian = false;
    public bool $showConfirmCreateModal = false;

    // Modal Cash Payment
    public bool $showPaymentModal = false;
    public bool $showConfirmPaymentModal = false;
    public ?string $selectedLoanId = null;
    public ?SantriLoan $selectedLoan = null;
    public string $payAmount = '';
    public string $payReceiptNo = '';
    public string $payNotes = '';

    // Modal Edit Loan
    public bool $showEditModal = false;
    public ?string $editLoanId = null;
    public ?SantriLoan $editingLoan = null;
    public string $editAmount = '';
    public string $editBorrowedAt = '';
    public string $editDueDate = '';
    public string $editReason = '';
    public string $editNotes = '';
    public bool $editIsVisibleToGuardian = false;

    // Success Notification Banner
    public ?string $successNotification = null;

    // Modal History
    public bool $showHistoryModal = false;
    public ?SantriLoan $viewingLoan = null;

    protected $queryString = [
        'search'       => ['except' => ''],
        'statusFilter' => ['except' => 'active'],
        'genderFilter' => ['except' => ''],
    ];

    public function mount(): void
    {
        if (!auth()->check() || (!auth()->user()->hasRole('super-admin') && !auth()->user()->can('view-pinjaman-santri'))) {
            abort(403, 'Anda tidak memiliki hak akses untuk membuka Buku Pinjaman Santri.');
        }

        $userScope = $this->genderScope();
        if ($userScope !== null) {
            $this->genderFilter = $userScope;
        }

        $this->borrowedAt = now()->toDateString();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingGenderFilter(): void
    {
        $this->resetPage();
    }

    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function setGenderFilter(string $gender): void
    {
        if ($this->isGenderScoped()) {
            return; // Scoped users cannot change gender filter
        }
        $this->genderFilter = $gender;
        $this->resetPage();
    }

    public function isGenderScoped(): bool
    {
        return $this->genderScope() !== null;
    }

    public function getActiveGender(): ?string
    {
        $scope = $this->genderScope();
        if ($scope !== null) {
            return $scope;
        }
        return $this->genderFilter ?: null;
    }

    // ─── Modal Create Loan ──────────────────────────────────────────────────

    public function openCreateModal(): void
    {
        $this->resetCreateForm();
        $this->borrowedAt = now()->toDateString();
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetCreateForm();
    }

    public function resetCreateForm(): void
    {
        $this->personSearch = '';
        $this->selectedPersonId = '';
        $this->selectedPersonName = '';
        $this->selectedPersonNIS = null;
        $this->selectedPersonGender = null;
        $this->amount = '';
        $this->borrowedAt = now()->toDateString();
        $this->dueDate = '';
        $this->reason = '';
        $this->notes = '';
        $this->isVisibleToGuardian = false;
        $this->showConfirmCreateModal = false;
        $this->resetValidation();
    }

    public function selectPerson(string $id, string $name, ?string $nis = null, ?string $gender = null): void
    {
        $this->selectedPersonId = $id;
        $this->selectedPersonName = $name;
        $this->selectedPersonNIS = $nis;
        $this->selectedPersonGender = $gender;
        $this->personSearch = '';
    }

    public function saveLoan(): void
    {
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->can('manage-pinjaman-santri')) {
            $this->toastError('Anda tidak memiliki wewenang untuk mencatat pinjaman baru.');
            return;
        }

        $this->validate([
            'selectedPersonId' => 'required|exists:persons,id',
            'amount'           => 'required|numeric|min:1000',
            'borrowedAt'       => 'required|date',
            'dueDate'          => 'nullable|date|after_or_equal:borrowedAt',
            'reason'           => 'nullable|string|max:255',
            'notes'            => 'nullable|string|max:500',
        ], [
            'selectedPersonId.required' => 'Pilih santri peminjam terlebih dahulu.',
            'amount.required'          => 'Nominal pinjaman wajib diisi.',
            'amount.numeric'           => 'Nominal harus berupa angka.',
            'amount.min'               => 'Nominal pinjaman minimal Rp 1.000.',
            'borrowedAt.required'      => 'Tanggal pinjam wajib diisi.',
            'dueDate.after_or_equal'   => 'Tenggat waktu harus sama atau setelah tanggal pinjam.',
        ]);

        // Security check: ensure person gender matches user scope
        $scope = $this->genderScope();
        $person = Person::findOrFail($this->selectedPersonId);
        if ($scope && $person->gender !== $scope) {
            $this->toastError('Anda tidak diizinkan mencatat pinjaman untuk santri lintas gender.');
            return;
        }

        // Buka modal konfirmasi sebelum menyimpan
        $this->showConfirmCreateModal = true;
    }

    public function executeSaveLoan(SantriLoanService $service): void
    {
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->can('manage-pinjaman-santri')) {
            $this->toastError('Anda tidak memiliki wewenang untuk mencatat pinjaman baru.');
            return;
        }

        try {
            $person = Person::findOrFail($this->selectedPersonId);
            $loan = $service->createLoan([
                'person_id'              => $this->selectedPersonId,
                'amount'                 => (float) $this->amount,
                'borrowed_at'            => $this->borrowedAt,
                'due_date'               => $this->dueDate ?: null,
                'reason'                 => $this->reason ?: 'Kiriman orang tua telat',
                'notes'                  => $this->notes ?: null,
                'is_visible_to_guardian' => $this->isVisibleToGuardian,
            ], auth()->id());

            $amountFmt = number_format($loan->amount, 0, ',', '.');
            $successMsg = "Pinjaman dana talangan sebesar Rp {$amountFmt} untuk {$person->name} berhasil dicatat dengan kode [{$loan->loan_code}].";
            
            $this->successNotification = $successMsg;
            $this->toastSuccess($successMsg, 'Berhasil Disimpan');

            $this->showConfirmCreateModal = false;
            $this->closeCreateModal();
        } catch (\Exception $e) {
            $this->toastError('Terjadi kesalahan saat menyimpan: ' . $e->getMessage());
            $this->showConfirmCreateModal = false;
        }
    }

    // ─── Modal Cash Payment ─────────────────────────────────────────────────

    public function openPaymentModal(string $loanId): void
    {
        $loan = SantriLoan::with('person')->findOrFail($loanId);

        // Security check for gender scope
        $scope = $this->genderScope();
        if ($scope && $loan->gender !== $scope) {
            $this->toastError('Anda tidak berwenang memproses pelunasan santri ini.');
            return;
        }

        if ($loan->status === 'paid') {
            $this->toastInfo('Pinjaman ini sudah lunas.');
            return;
        }

        $this->selectedLoanId = $loan->id;
        $this->selectedLoan = $loan;
        $this->payAmount = (string) $loan->remaining_amount;
        $this->payReceiptNo = '';
        $this->payNotes = '';
        $this->showConfirmPaymentModal = false;
        $this->resetValidation();
        $this->showPaymentModal = true;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->showConfirmPaymentModal = false;
        $this->selectedLoanId = null;
        $this->selectedLoan = null;
        $this->payAmount = '';
        $this->payReceiptNo = '';
        $this->payNotes = '';
    }

    public function savePayment(): void
    {
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->can('manage-pinjaman-santri')) {
            $this->toastError('Anda tidak memiliki wewenang untuk mencatat pelunasan.');
            return;
        }

        if (!$this->selectedLoan) {
            return;
        }

        $maxPayable = (float) $this->selectedLoan->remaining_amount;

        $this->validate([
            'payAmount' => "required|numeric|min:1|max:{$maxPayable}",
            'payNotes'  => 'nullable|string|max:255',
        ], [
            'payAmount.required' => 'Nominal pembayaran wajib diisi.',
            'payAmount.min'      => 'Nominal minimal Rp 1.',
            'payAmount.max'      => 'Nominal melebihi sisa pinjaman (Maks: Rp ' . number_format($maxPayable, 0, ',', '.') . ').',
        ]);

        $this->showConfirmPaymentModal = true;
    }

    public function executeSavePayment(SantriLoanService $service): void
    {
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->can('manage-pinjaman-santri')) {
            $this->toastError('Anda tidak memiliki wewenang untuk mencatat pelunasan.');
            return;
        }

        if (!$this->selectedLoan) {
            return;
        }

        try {
            $payAmountFmt = number_format((float)$this->payAmount, 0, ',', '.');
            $service->recordPayment(
                $this->selectedLoan,
                (float) $this->payAmount,
                'cash',
                auth()->id(),
                $this->payReceiptNo ?: null,
                $this->payNotes ?: 'Pelunasan tunai kasir'
            );

            $successMsg = "Pembayaran sebesar Rp {$payAmountFmt} untuk pinjaman [{$this->selectedLoan->loan_code}] berhasil dicatat.";
            $this->successNotification = $successMsg;
            $this->toastSuccess($successMsg, 'Pembayaran Berhasil');

            $this->showConfirmPaymentModal = false;
            $this->closePaymentModal();
        } catch (\Exception $e) {
            $this->toastError('Gagal memproses pembayaran: ' . $e->getMessage());
            $this->showConfirmPaymentModal = false;
        }
    }

    // ─── Modal History ──────────────────────────────────────────────────────

    public function openHistoryModal(string $loanId): void
    {
        $loan = SantriLoan::with(['person', 'payments.receiver', 'creator', 'bill'])->findOrFail($loanId);
        $this->viewingLoan = $loan;
        $this->showHistoryModal = true;
    }

    public function closeHistoryModal(): void
    {
        $this->showHistoryModal = false;
        $this->viewingLoan = null;
    }

    // ─── Action: Toggle Visibility ──────────────────────────────────────────

    public function toggleGuardianVisibility(string $loanId, SantriLoanService $service): void
    {
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->can('manage-pinjaman-santri')) {
            $this->toastError('Anda tidak memiliki wewenang untuk mengubah visibilitas.');
            return;
        }

        $loan = SantriLoan::findOrFail($loanId);
        $scope = $this->genderScope();
        if ($scope && $loan->gender !== $scope) {
            $this->toastError('Anda tidak berwenang mengelola data ini.');
            return;
        }

        $newStatus = $service->toggleGuardianVisibility($loan);
        if ($newStatus) {
            $this->toastSuccess("Pinjaman sekarang DITAMPILKAN di Portal Orang Tua santri.");
        } else {
            $this->toastInfo("Pinjaman DISEMBUNYIKAN dari Portal Orang Tua santri.");
        }
    }

    // ─── Action: Cancel Loan ────────────────────────────────────────────────

    public function cancelLoan(string $loanId): void
    {
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->can('manage-pinjaman-santri')) {
            $this->toastError('Anda tidak memiliki wewenang untuk membatalkan pinjaman.');
            return;
        }

        $loan = SantriLoan::findOrFail($loanId);
        $scope = $this->genderScope();
        if ($scope && $loan->gender !== $scope) {
            $this->toastError('Anda tidak berwenang mengelola data ini.');
            return;
        }

        if ($loan->payments()->count() > 0) {
            $this->toastError('Pinjaman yang sudah memiliki riwayat pembayaran tidak dapat dibatalkan.');
            return;
        }

        $loan->update(['status' => 'cancelled']);
        if ($loan->bill) {
            $loan->bill->update(['status' => 'cancelled']);
        }

        $this->toastSuccess("Pinjaman [{$loan->loan_code}] berhasil dibatalkan.");
    }

    // ─── Modal Edit Loan ────────────────────────────────────────────────────

    public function openEditModal(string $loanId): void
    {
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->can('manage-pinjaman-santri')) {
            $this->toastError('Anda tidak memiliki wewenang untuk mengedit pinjaman.');
            return;
        }

        $loan = SantriLoan::with('person')->findOrFail($loanId);

        $scope = $this->genderScope();
        if ($scope && $loan->gender !== $scope) {
            $this->toastError('Anda tidak diizinkan mengedit pinjaman santri lintas gender.');
            return;
        }

        $this->editLoanId = $loan->id;
        $this->editingLoan = $loan;
        $this->editAmount = (string) (int)$loan->amount;
        $this->editBorrowedAt = $loan->borrowed_at ? $loan->borrowed_at->format('Y-m-d') : now()->toDateString();
        $this->editDueDate = $loan->due_date ? $loan->due_date->format('Y-m-d') : '';
        $this->editReason = $loan->reason ?? '';
        $this->editNotes = $loan->notes ?? '';
        $this->editIsVisibleToGuardian = (bool)$loan->is_visible_to_guardian;

        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->editLoanId = null;
        $this->editingLoan = null;
        $this->editAmount = '';
        $this->editBorrowedAt = '';
        $this->editDueDate = '';
        $this->editReason = '';
        $this->editNotes = '';
    }

    public function saveEditLoan(SantriLoanService $service): void
    {
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->can('manage-pinjaman-santri')) {
            $this->toastError('Anda tidak memiliki wewenang untuk mengedit pinjaman.');
            return;
        }

        $loan = SantriLoan::findOrFail($this->editLoanId);

        $minAllowed = (float)$loan->amount_paid > 0 ? (float)$loan->amount_paid : 1000;
        $this->validate([
            'editAmount'     => "required|numeric|min:{$minAllowed}",
            'editBorrowedAt' => 'required|date',
            'editDueDate'    => 'nullable|date|after_or_equal:editBorrowedAt',
            'editReason'     => 'nullable|string|max:255',
            'editNotes'      => 'nullable|string|max:500',
        ], [
            'editAmount.required'        => 'Nominal pinjaman wajib diisi.',
            'editAmount.numeric'         => 'Nominal harus berupa angka.',
            'editAmount.min'             => "Nominal pinjaman minimal Rp " . number_format($minAllowed, 0, ',', '.') . " (sesuai pembayaran yang sudah masuk).",
            'editBorrowedAt.required'    => 'Tanggal pinjam wajib diisi.',
            'editDueDate.after_or_equal' => 'Tenggat waktu harus sama atau setelah tanggal pinjam.',
        ]);

        try {
            $updated = $service->updateLoan($loan, [
                'amount'                 => (float)$this->editAmount,
                'borrowed_at'            => $this->editBorrowedAt,
                'due_date'               => $this->editDueDate ?: null,
                'reason'                 => $this->editReason ?: 'Kiriman orang tua telat',
                'notes'                  => $this->editNotes ?: null,
                'is_visible_to_guardian' => $this->editIsVisibleToGuardian,
            ]);

            $this->closeEditModal();
            $this->toastSuccess("Perubahan pinjaman [{$updated->loan_code}] berhasil disimpan.");
            $this->successNotification = "Perubahan data pinjaman [{$updated->loan_code}] berhasil disimpan.";
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    // ─── Render ─────────────────────────────────────────────────────────────

    public function render()
    {
        $activeGender = $this->getActiveGender();

        // 1. KPI Stats Query
        $baseKpiQuery = SantriLoan::query()
            ->when($activeGender, fn($q, $g) => $q->where('gender', $g));

        $totalActiveDebt = (float) (clone $baseKpiQuery)->whereIn('status', ['unpaid', 'partial'])
            ->selectRaw('SUM(amount - amount_paid) as total_debt')
            ->value('total_debt') ?? 0.0;

        $totalActiveSantri = (clone $baseKpiQuery)->whereIn('status', ['unpaid', 'partial'])
            ->distinct('person_id')
            ->count('person_id');

        $totalPaidThisMonth = (float) (clone $baseKpiQuery)
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->sum('amount_paid');

        // 2. Loans List Query
        $loansQuery = SantriLoan::with(['person', 'creator'])
            ->when($activeGender, fn($q, $g) => $q->where('gender', $g))
            ->when($this->statusFilter === 'active', fn($q) => $q->whereIn('status', ['unpaid', 'partial']))
            ->when($this->statusFilter === 'unpaid', fn($q) => $q->where('status', 'unpaid'))
            ->when($this->statusFilter === 'partial', fn($q) => $q->where('status', 'partial'))
            ->when($this->statusFilter === 'paid', fn($q) => $q->where('status', 'paid'))
            ->when($this->statusFilter === 'cancelled', fn($q) => $q->where('status', 'cancelled'))
            ->when($this->search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('loan_code', 'like', "%{$search}%")
                        ->orWhere('reason', 'like', "%{$search}%")
                        ->orWhereHas('person', function ($pq) use ($search) {
                            $pq->where('name', 'like', "%{$search}%")
                               ->orWhere('nis', 'like', "%{$search}%");
                        });
                });
            })
            ->when($this->filterDateFrom, fn($q, $from) => $q->whereDate('borrowed_at', '>=', $from))
            ->when($this->filterDateTo, fn($q, $to) => $q->whereDate('borrowed_at', '<=', $to))
            ->orderBy('borrowed_at', 'desc')
            ->orderBy('created_at', 'desc');

        $loans = $loansQuery->paginate(15);

        // 3. Search Results for Create Modal
        $personSearchResults = collect();
        if ($this->showCreateModal && strlen(trim($this->personSearch)) >= 2) {
            $personSearchResults = Person::query()
                ->whereHas('roles', fn($q) => $q->where('role_type', 'santri')->where('enrollment_status', 'aktif'))
                ->when($activeGender, fn($q, $g) => $q->where('gender', $g))
                ->where(function ($q) {
                    $q->where('name', 'like', "%{$this->personSearch}%")
                      ->orWhere('nis', 'like', "%{$this->personSearch}%");
                })
                ->limit(10)
                ->get();
        }

        return view('livewire.keuangan.pinjaman-santri-manager', [
            'loans'                => $loans,
            'totalActiveDebt'      => $totalActiveDebt,
            'totalActiveSantri'    => $totalActiveSantri,
            'totalPaidThisMonth'   => $totalPaidThisMonth,
            'personSearchResults' => $personSearchResults,
            'userGenderScope'      => $this->genderScope(),
        ]);
    }
}
