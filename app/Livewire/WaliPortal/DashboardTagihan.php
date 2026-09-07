<?php

namespace App\Livewire\WaliPortal;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Url;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\LandingPageContent;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillPayment;
use App\Modules\Keuangan\Models\PaymentTransaction;
use App\Modules\Keuangan\Models\ManualTransferSubmission;
use App\Modules\Keuangan\Models\PocketMoneyDeposit;
use App\Modules\Keuangan\Services\DuitkuService;
use App\Modules\Keuangan\Services\ProofImageCompressionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardTagihan extends Component
{
    use WithFileUploads;

    public string $personId;

    // Tab Utama Portal: 'tagihan' (Tagihan Aktif) | 'bayar' (Pilih Bayar / Transfer) | 'riwayat' (Riwayat & Bukti)
    #[Url(as: 'tab')]
    public string $portalTab = 'tagihan';

    // Filter Riwayat Pembayaran
    public string $historyMethod = ''; // '' (Semua) | 'gateway' (Online) | 'kasir' (Kasir) | 'manual' (Transfer)
    public string $historyYear   = ''; // '' (Semua Tahun) | '2026'

    // Checklist Tagihan yang dipilih wali
    public array $selectedBillIds = [];
    public bool $isInitialized = false;

    // Pembayaran Parsial / Cicilan — nominal custom per bill [bill_id => amount]
    public array $customAmounts = [];
    public array $editingCustomBills = [];
    public bool $showFutureBills = false;
    public ?string $fifoNotice = null;

    // ─── Fitur Titipan Uang Saku Anak ─────────────────────────────────────────
    public bool $includePocketMoney = false;
    public float $pocketMoneyAmount = 0.0;
    public string $customPocketMoney = '';
    public array $presetPocketMoney = [50000, 100000, 200000, 500000];

    // ─── Form Checkout Transfer Manual ────────────────────────────────────────
    public string $checkoutMethod = 'manual'; // 'manual' | 'duitku'
    public $proofImage; // Temporary uploaded image
    public string $senderBank = '';
    public string $senderAccountName = '';
    public string $transferNotes = '';
    public string $selectedBankDestination = 'BSI';

    // Status & Error
    public ?string $manualSuccessMessage = null;
    public ?string $manualErrorMessage = null;
    public bool $isSubmittingManual = false;

    // Bayar Online (Duitku)
    public string $selectedChannel = '';
    public bool $isProcessingPayment = false;
    public ?string $paymentError = null;

    // Public properties untuk mencegah undefined variable di Livewire hydration
    public float $totalTunggakan = 0;
    public float $totalSudahDibayar = 0;
    public float $totalCurrentMonthUnpaid = 0;
    public float $totalPastTunggakan = 0;
    public float $totalHarusDibayarNow = 0;
    public float $totalFuturePaid = 0;
    public float $totalAllPaid = 0;
    public float $simulasiTotal = 0;

    public function mount(string $personId)
    {
        $this->personId = $personId;
    }

    public function setPortalTab(string $tab): void
    {
        $this->portalTab = in_array($tab, ['tagihan', 'bayar', 'riwayat']) ? $tab : 'tagihan';
        $this->manualSuccessMessage = null;
        $this->manualErrorMessage = null;
        $this->fifoNotice = null;
    }

    public function setCheckoutMethod(string $method): void
    {
        $this->checkoutMethod = in_array($method, ['manual', 'duitku']) ? $method : 'manual';
    }

    public function togglePocketMoney(): void
    {
        $this->includePocketMoney = !$this->includePocketMoney;
        if (!$this->includePocketMoney) {
            $this->pocketMoneyAmount = 0.0;
            $this->customPocketMoney = '';
        } elseif ($this->pocketMoneyAmount <= 0) {
            $this->pocketMoneyAmount = 100000;
        }
    }

    public function selectPresetPocketMoney(float $amount): void
    {
        $this->includePocketMoney = true;
        $this->pocketMoneyAmount = $amount;
        $this->customPocketMoney = '';
    }

    public function updatedCustomPocketMoney($val): void
    {
        $numeric = (float) preg_replace('/[^0-9]/', '', (string)$val);
        if ($numeric > 0) {
            $this->includePocketMoney = true;
            $this->pocketMoneyAmount = $numeric;
        } else {
            $this->pocketMoneyAmount = 0.0;
        }
    }

    public function selectAllBills(array $allBillIds): void
    {
        $this->selectedBillIds = $allBillIds;
    }

    public function deselectAllBills(): void
    {
        $this->selectedBillIds = [];
    }

    public function getUnpaidBillsPartition(): array
    {
        $now = now();
        $currentMonth = (int) $now->format('m');
        $currentYear  = (int) $now->format('Y');

        $allBills = Bill::with('config')
            ->where('person_id', $this->personId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->get();

        $past = collect();
        $current = collect();
        $future = collect();

        foreach ($allBills as $bill) {
            $interval = $bill->config?->interval;
            $isEvent = in_array($interval, ['once', 'insidental', 'event', 'sekali']) || in_array($bill->bill_type, ['kitab', 'pendaftaran', 'event_iuran']);

            if ($isEvent) {
                if ($bill->due_date && $bill->due_date->lt(now()->startOfDay())) {
                    $past->push($bill);
                } else {
                    $current->push($bill);
                }
                continue;
            }

            $status = $this->classifyBillPeriodStatus($bill, $currentMonth, $currentYear);
            if ($status === 'past') {
                $past->push($bill);
            } elseif ($status === 'future') {
                $future->push($bill);
            } else {
                $current->push($bill);
            }
        }

        return [
            'past'    => $past,
            'current' => $current,
            'future'  => $future,
        ];
    }

    public function selectQuickMode(string $mode): void
    {
        $this->fifoNotice = null;
        $partition = $this->getUnpaidBillsPartition();
        $pastIds = $partition['past']->pluck('id')->toArray();
        $currentIds = $partition['current']->pluck('id')->toArray();

        if ($mode === 'all_active') {
            $this->selectedBillIds = array_values(array_unique(array_merge($pastIds, $currentIds)));
        } elseif ($mode === 'past_only') {
            $this->selectedBillIds = $pastIds;
        } elseif ($mode === 'current_only') {
            if (!empty($pastIds)) {
                $this->selectedBillIds = array_values(array_unique(array_merge($pastIds, $currentIds)));
                $this->fifoNotice = 'Tunggakan bulan lalu otomatis diikutsertakan karena wajib diselesaikan terlebih dahulu.';
            } else {
                $this->selectedBillIds = $currentIds;
            }
        } elseif ($mode === 'none') {
            $this->selectedBillIds = [];
        }
    }

    public function toggleBillSelection(string $billId): void
    {
        $this->fifoNotice = null;
        $partition = $this->getUnpaidBillsPartition();
        $pastIds = $partition['past']->pluck('id')->toArray();
        $currentIds = $partition['current']->pluck('id')->toArray();
        $futureIds = $partition['future']->pluck('id')->toArray();

        $isSelected = in_array($billId, $this->selectedBillIds);

        if ($isSelected) {
            // Uncheck bill
            $this->selectedBillIds = array_values(array_diff($this->selectedBillIds, [$billId]));

            // If user unchecks a past bill, current & future bills must also be unchecked (FIFO)
            if (in_array($billId, $pastIds)) {
                $intersect = array_intersect($this->selectedBillIds, array_merge($currentIds, $futureIds));
                if (!empty($intersect)) {
                    $this->selectedBillIds = array_values(array_diff($this->selectedBillIds, array_merge($currentIds, $futureIds)));
                    $this->fifoNotice = 'Tagihan bulan berjalan/mendatang disesuaikan karena tunggakan lama belum dipilih.';
                }
            } elseif (in_array($billId, $currentIds)) {
                $intersectFuture = array_intersect($this->selectedBillIds, $futureIds);
                if (!empty($intersectFuture)) {
                    $this->selectedBillIds = array_values(array_diff($this->selectedBillIds, $futureIds));
                    $this->fifoNotice = 'Tagihan bulan mendatang disesuaikan karena tagihan bulan ini belum dipilih.';
                }
            }
        } else {
            // Check bill
            if (in_array($billId, $currentIds)) {
                $missingPast = array_diff($pastIds, $this->selectedBillIds);
                if (!empty($missingPast)) {
                    $this->selectedBillIds = array_values(array_unique(array_merge($this->selectedBillIds, $pastIds, [$billId])));
                    $this->fifoNotice = 'Tagihan tunggakan bulan sebelumnya otomatis diikutsertakan agar urutan pelunasan tertib.';
                    return;
                }
            } elseif (in_array($billId, $futureIds)) {
                $missingMandatory = array_diff(array_merge($pastIds, $currentIds), $this->selectedBillIds);
                if (!empty($missingMandatory)) {
                    $this->selectedBillIds = array_values(array_unique(array_merge($this->selectedBillIds, $pastIds, $currentIds, [$billId])));
                    $this->fifoNotice = 'Tunggakan & tagihan bulan ini otomatis diikutsertakan sebelum membayar bulan depan.';
                    return;
                }
            }

            $this->selectedBillIds[] = $billId;
        }
    }

    public function toggleCustomAmountInput(string $billId): void
    {
        $current = $this->editingCustomBills[$billId] ?? false;
        $this->editingCustomBills[$billId] = !$current;
        if (!in_array($billId, $this->selectedBillIds)) {
            $this->toggleBillSelection($billId);
        }
    }

    public function setCustomAmountPercent(string $billId, int $percent, float $maxRemaining): void
    {
        if ($percent >= 100) {
            unset($this->customAmounts[$billId]);
            $this->editingCustomBills[$billId] = false;
        } else {
            $val = round(($maxRemaining * $percent) / 100);
            $this->customAmounts[$billId] = max(1000, $val);
            $this->editingCustomBills[$billId] = true;
        }
        if (!in_array($billId, $this->selectedBillIds)) {
            $this->toggleBillSelection($billId);
        }
    }

    public function resetBillCustomAmount(string $billId): void
    {
        unset($this->customAmounts[$billId]);
        $this->editingCustomBills[$billId] = false;
    }

    public function toggleShowFutureBills(): void
    {
        $this->showFutureBills = !$this->showFutureBills;
    }

    public function getGrandTotalTransfer(): float
    {
        $billsTotal = $this->simulasiTotal;
        $pocket = ($this->includePocketMoney && $this->pocketMoneyAmount > 0) ? $this->pocketMoneyAmount : 0.0;
        return $billsTotal + $pocket;
    }

    /**
     * Submit Form Transfer Bank Manual & Upload Struk
     */
    public function submitManualTransfer(ProofImageCompressionService $compressor): void
    {
        $this->manualErrorMessage = null;
        $this->manualSuccessMessage = null;
        $this->isSubmittingManual = true;

        $this->validate([
            'selectedBillIds' => ['required', 'array', 'min:1'],
            'proofImage'      => ['required', 'image', 'max:12288'], // max 12MB raw (will be compressed)
            'senderBank'      => ['nullable', 'string', 'max:50'],
            'senderAccountName' => ['nullable', 'string', 'max:100'],
            'transferNotes'   => ['nullable', 'string', 'max:255'],
        ], [
            'selectedBillIds.required' => 'Pilih minimal satu tagihan yang ingin dibayar.',
            'selectedBillIds.min'      => 'Pilih minimal satu tagihan yang ingin dibayar.',
            'proofImage.required'      => 'Foto bukti transfer wajib diunggah.',
            'proofImage.image'         => 'File bukti transfer harus berupa gambar (JPG, PNG, atau WebP).',
            'proofImage.max'           => 'Ukuran file foto maksimal 12MB.',
        ]);

        try {
            DB::beginTransaction();

            // 1. Ambil data tagihan aktif
            $bills = Bill::with('config')
                ->whereIn('id', $this->selectedBillIds)
                ->where('person_id', $this->personId)
                ->whereIn('status', ['unpaid', 'partial'])
                ->get();

            if ($bills->isEmpty()) {
                throw new \Exception('Tagihan yang dipilih tidak ditemukan atau sudah lunas.');
            }

            // 2. Susun Breakdown Tagihan
            $breakdown = [];
            $totalBills = 0.0;

            foreach ($bills as $bill) {
                $maxKekurangan = max(0, (float)$bill->amount - (float)$bill->amount_paid);
                $customVal = $this->customAmounts[$bill->id] ?? null;
                $payAmount = (isset($customVal) && is_numeric($customVal) && (float)$customVal > 0)
                    ? min($maxKekurangan, (float)$customVal)
                    : $maxKekurangan;

                $totalBills += $payAmount;
                $monthName = $this->getMonthName($bill->period_month);
                $periodLabel = $this->getBillPeriodLabel($bill);

                $breakdown[] = [
                    'bill_id'      => $bill->id,
                    'bill_type'    => $bill->bill_type,
                    'config_label' => $bill->config?->label ?? $this->getBillTypeLabel($bill->bill_type),
                    'period_label' => $periodLabel,
                    'amount'       => $payAmount,
                ];
            }

            $pocketMoney = ($this->includePocketMoney && $this->pocketMoneyAmount > 0) ? (float)$this->pocketMoneyAmount : 0.0;
            $grandTotal = $totalBills + $pocketMoney;

            // 3. Kompresi Cerdas & Simpan Gambar
            $compressedResult = $compressor->compressAndStore(
                file: $this->proofImage,
                disk: 'public',
                folder: 'transfer-proofs',
                maxDimension: 1600,
                quality: 80
            );

            $proofPath = $compressedResult['path'];

            // 4. Buat Record Manual Transfer Submission
            $submission = ManualTransferSubmission::create([
                'submission_code'       => ManualTransferSubmission::generateSubmissionCode(),
                'person_id'             => $this->personId,
                'bill_ids'              => $this->selectedBillIds,
                'bill_breakdown'        => $breakdown,
                'total_bills_amount'    => $totalBills,
                'pocket_money_amount'   => $pocketMoney,
                'total_transfer_amount' => $grandTotal,
                'bank_destination'      => $this->selectedBankDestination,
                'sender_bank'           => $this->senderBank ?: null,
                'sender_account_name'   => $this->senderAccountName ?: null,
                'notes'                 => $this->transferNotes ?: null,
                'proof_image_path'      => $proofPath,
                'status'                => 'pending',
            ]);

            // 5. Catat Titipan Uang Saku jika ada
            if ($pocketMoney > 0) {
                PocketMoneyDeposit::create([
                    'person_id'    => $this->personId,
                    'amount'       => $pocketMoney,
                    'source'       => 'manual_transfer',
                    'reference_id' => $submission->id,
                    'status'       => 'pending',
                    'notes'        => 'Titipan uang saku via transfer manual kode ' . $submission->submission_code,
                ]);
            }

            DB::commit();

            // Reset form input
            $this->proofImage = null;
            $this->senderBank = '';
            $this->senderAccountName = '';
            $this->transferNotes = '';
            $this->includePocketMoney = false;
            $this->pocketMoneyAmount = 0.0;
            $this->customPocketMoney = '';
            $this->isSubmittingManual = false;

            // Pindah ke tab riwayat & tampilkan notifikasi sukses
            $this->portalTab = 'riwayat';
            $this->manualSuccessMessage = 'Alhamdulillah! Bukti transfer berhasil dikirim. Pengajuan Anda sedang diverifikasi oleh Bendahara Pondok.';

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[DashboardTagihan] submitManualTransfer failed', [
                'person_id' => $this->personId,
                'error'     => $e->getMessage(),
            ]);
            $this->manualErrorMessage = 'Gagal menyimpan pengajuan: ' . $e->getMessage();
            $this->isSubmittingManual = false;
        }
    }

    public function getBillTypeLabel(string $type): string
    {
        $labels = [
            'syahriah_pondok'   => 'SPP / Syahriah Pondok',
            'kas_komplek'       => 'Kas Komplek Asrama',
            'majek_pagi'        => 'Majek / Catering Pagi',
            'majek_sore'        => 'Majek / Catering Sore',
            'syahriah_madrasah' => 'Syahriah Madrasah',
            'kebersihan'        => 'Uang Kebersihan',
            'kitab'             => 'Biaya Kitab / Buku',
            'pendaftaran'       => 'Biaya Pendaftaran',
            'event_iuran'       => 'Iuran Acara / Event',
            'insidental'        => 'Iuran Acara / Event',
        ];

        return $labels[$type] ?? ucwords(str_replace('_', ' ', $type));
    }

    public function getMonthName(?int $month): string
    {
        if (!$month) return '';
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return $months[$month] ?? '';
    }

    public function getBillPeriodLabel(Bill $bill): string
    {
        $interval = $bill->config?->interval ?? 'monthly';
        $dueStr = $bill->due_date ? ' • Tenggat: ' . $bill->due_date->translatedFormat('d M Y') : '';

        if (in_array($interval, ['semester', '2x_yearly'])) {
            $s = $bill->period_sub ?? ($bill->period_month && $bill->period_month <= 6 ? 1 : 2);
            return "Semester {$s} ({$bill->period_year}){$dueStr}";
        }

        if (in_array($interval, ['caturwulan', '3x_yearly'])) {
            $cw = $bill->period_sub ?? ($bill->period_month ? ($bill->period_month <= 4 ? 1 : ($bill->period_month <= 8 ? 2 : 3)) : 1);
            return "Caturwulan {$cw} ({$bill->period_year}){$dueStr}";
        }

        if (in_array($interval, ['triwulan', '4x_yearly'])) {
            $tw = $bill->period_sub ?? ($bill->period_month ? (int)ceil($bill->period_month / 3) : 1);
            return "Triwulan {$tw} ({$bill->period_year}){$dueStr}";
        }

        if (in_array($interval, ['bimulanan', '6x_yearly'])) {
            $b = $bill->period_sub ?? ($bill->period_month ? (int)ceil($bill->period_month / 2) : 1);
            return "Dwibulanan {$b} ({$bill->period_year}){$dueStr}";
        }

        if (in_array($interval, ['once', 'insidental', 'event', 'sekali'])) {
            if ($bill->due_date) {
                return "Tenggat: " . $bill->due_date->translatedFormat('d M Y');
            }
            return "Sekali Bayar ({$bill->period_year})";
        }

        if ($interval === 'yearly') {
            return "Tahunan ({$bill->period_year}){$dueStr}";
        }

        $monthName = $this->getMonthName($bill->period_month);
        return trim("{$monthName} {$bill->period_year}{$dueStr}");
    }

    public function classifyBillPeriodStatus(Bill $bill, int $currentMonth, int $currentYear): string
    {
        if ($bill->due_date && $bill->due_date->gte(now()->startOfDay()) && $bill->status !== 'paid') {
            $bMonth = $bill->period_month ?? (int)$bill->due_date->format('m');
            $bYear  = $bill->period_year ?? (int)$bill->due_date->format('Y');
            if ($bYear > $currentYear || ($bYear === $currentYear && $bMonth > $currentMonth)) {
                return 'future';
            }
            return 'current';
        }

        $interval = $bill->config?->interval ?? 'monthly';
        $bYear    = $bill->period_year ?? (int)($bill->due_date ? $bill->due_date->format('Y') : $bill->created_at->format('Y'));
        $bMonth   = $bill->period_month ?? (int)($bill->due_date ? $bill->due_date->format('m') : $bill->created_at->format('m'));
        $sub      = $bill->period_sub;

        if (in_array($interval, ['semester', '2x_yearly'])) {
            $s = $sub ?? ($bMonth <= 6 ? 1 : 2);
            $startM = ($s - 1) * 6 + 1;
            $endM   = $s * 6;
        } elseif (in_array($interval, ['caturwulan', '3x_yearly'])) {
            $cw = $sub ?? ($bMonth <= 4 ? 1 : ($bMonth <= 8 ? 2 : 3));
            $startM = ($cw - 1) * 4 + 1;
            $endM   = $cw * 4;
        } elseif (in_array($interval, ['triwulan', '4x_yearly'])) {
            $tw = $sub ?? (int)ceil($bMonth / 3);
            $startM = ($tw - 1) * 3 + 1;
            $endM   = $tw * 3;
        } elseif (in_array($interval, ['bimulanan', '6x_yearly'])) {
            $b = $sub ?? ($bMonth ? (int)ceil($bMonth / 2) : 1);
            $startM = ($b - 1) * 2 + 1;
            $endM   = $b * 2;
        } elseif (in_array($interval, ['yearly'])) {
            $startM = 1;
            $endM   = 12;
        } else {
            $startM = $bMonth;
            $endM   = $bMonth;
        }

        if ($bYear < $currentYear) {
            return 'past';
        } elseif ($bYear > $currentYear) {
            return 'future';
        } else {
            if ($currentMonth > $endM) {
                return 'past';
            } elseif ($currentMonth < $startM) {
                return 'future';
            } else {
                return 'current';
            }
        }
    }

    public function getBillDisplayName(Bill $bill): string
    {
        if ($bill->config && $bill->config->label) {
            return $bill->config->label;
        }
        if (!empty($bill->title)) {
            return $bill->title;
        }
        return $this->getBillTypeLabel($bill->bill_type);
    }

    /**
     * Inisiasi pembayaran online via Duitku.
     */
    public function initiateBayarOnline(string $channel): void
    {
        $this->paymentError       = null;
        $this->isProcessingPayment = true;
        $this->selectedChannel    = $channel;

        try {
            if (empty($this->selectedBillIds)) {
                $this->paymentError       = 'Pilih minimal satu tagihan terlebih dahulu.';
                $this->isProcessingPayment = false;
                return;
            }

            $channels = config('duitku.enabled_channels', []);
            if (!array_key_exists($channel, $channels)) {
                $this->paymentError       = 'Metode pembayaran tidak valid.';
                $this->isProcessingPayment = false;
                return;
            }

            $bills = Bill::whereIn('id', $this->selectedBillIds)
                ->where('person_id', $this->personId)
                ->whereIn('status', ['unpaid', 'partial'])
                ->get()
                ->all();

            if (empty($bills)) {
                $this->paymentError       = 'Tagihan yang dipilih tidak ditemukan atau sudah lunas.';
                $this->isProcessingPayment = false;
                return;
            }

            $duitkuService = app(DuitkuService::class);
            $transaction   = $duitkuService->createTransaction(
                bills:         $bills,
                channel:       $channel,
                personId:      $this->personId,
                userId:        null,
                customAmounts: $this->customAmounts,
            );

            $this->redirect($transaction->payment_url, navigate: false);

        } catch (\Exception $e) {
            Log::error('[DashboardTagihan] initiateBayarOnline failed', [
                'person_id' => $this->personId,
                'channel'   => $channel,
                'error'     => $e->getMessage(),
            ]);
            $this->paymentError       = 'Gagal menghubungi server pembayaran: ' . $e->getMessage();
            $this->isProcessingPayment = false;
        }
    }

    public function setCustomBillAmount(string $billId, float $amount): void
    {
        $this->customAmounts[$billId] = $amount;
    }

    public function resetCustomBillAmount(string $billId): void
    {
        unset($this->customAmounts[$billId]);
    }

    public function render()
    {
        $santri = Person::with([
            'roomAssignments' => fn($q) => $q->where('is_active', true)->with('room.dormitory'),
            'madrasahEnrollments' => fn($q) => $q->where('is_active', true)->with('kelas'),
            'santriProfile'
        ])->findOrFail($this->personId);

        $contents = LandingPageContent::all()->pluck('value', 'key')->toArray();
        $isPutri = ($santri->gender === 'P');

        if ($isPutri) {
            $bank1Name   = $contents['wali_bank1_name_putri'] ?? 'Bank Syariah Indonesia (BSI)';
            $bsiRekening = $contents['wali_bsi_putri'] ?? '7987654321';
            $bsiAn       = $contents['wali_bsi_putri_an'] ?? 'Pesantren Al-Fithroh Putri';

            $bank2Name   = $contents['wali_bank2_name_putri'] ?? 'Bank BRI';
            $briRekening = $contents['wali_bri_putri'] ?? '001201009876505';
            $briAn       = $contents['wali_bri_putri_an'] ?? 'Yayasan Al-Fithroh Putri';

            $waBendahara = $contents['wali_wa_putri'] ?? '6281234567891';
            $waName      = $contents['wali_wa_putri_name'] ?? 'Bendahara Putri Al-Fithroh';
        } else {
            $bank1Name   = $contents['wali_bank1_name_putra'] ?? 'Bank Syariah Indonesia (BSI)';
            $bsiRekening = $contents['wali_bsi_putra'] ?? '7123456789';
            $bsiAn       = $contents['wali_bsi_putra_an'] ?? 'Pesantren Al-Fithroh Putra';

            $bank2Name   = $contents['wali_bank2_name_putra'] ?? 'Bank BRI';
            $briRekening = $contents['wali_bri_putra'] ?? '001201009876504';
            $briAn       = $contents['wali_bri_putra_an'] ?? 'Yayasan Al-Fithroh Putra';

            $waBendahara = $contents['wali_wa_putra'] ?? '6281234567890';
            $waName      = $contents['wali_wa_putra_name'] ?? 'Bendahara Putra Al-Fithroh';
        }

        $cleanWa     = preg_replace('/[^0-9]/', '', $waBendahara);
        $directWaUrl = 'https://wa.me/' . $cleanWa . '?text=' . urlencode("Assalamu'alaikum {$waName}, saya Wali Santri dari {$santri->name} ingin konfirmasi pembayaran.");

        $now = now();
        $currentMonth = (int) $now->format('m');
        $currentYear  = (int) $now->format('Y');

        $allBills = Bill::with('config')
            ->where('person_id', $this->personId)
            ->whereNotIn('status', ['refund_requested', 'refunded', 'cancelled', 'exempt'])
            ->get();

        $typePriority = [
            'syahriah_pondok'   => 1,
            'syahriah_madrasah' => 2,
            'majek_pagi'        => 3,
            'majek_sore'        => 4,
            'kas_komplek'       => 5,
            'kebersihan'        => 6,
            'kitab'             => 7,
            'pendaftaran'       => 8,
            'event_iuran'       => 9,
        ];

        $currentMonthBills = collect();
        $pastUnpaidBills   = collect();
        $eventBills        = collect();
        $futureBills       = collect();
        $pastPaidBills     = collect();

        foreach ($allBills as $bill) {
            $interval = $bill->config?->interval;
            $isEvent = in_array($interval, ['once', 'insidental', 'event', 'sekali']) || in_array($bill->bill_type, ['kitab', 'pendaftaran', 'event_iuran']);

            if ($isEvent) {
                if ($bill->status === 'paid') {
                    $pastPaidBills->push($bill);
                } else {
                    $eventBills->push($bill);
                }
                continue;
            }

            $status = $this->classifyBillPeriodStatus($bill, $currentMonth, $currentYear);

            if ($status === 'past') {
                if ($bill->status === 'paid') {
                    $pastPaidBills->push($bill);
                } else {
                    $pastUnpaidBills->push($bill);
                }
            } elseif ($status === 'current') {
                $currentMonthBills->push($bill);
            } elseif ($status === 'future') {
                $futureBills->push($bill);
            }
        }

        $currentMonthBills = $currentMonthBills->sort(function($a, $b) use ($typePriority) {
            $statusRankA = ($a->status === 'paid') ? 1 : 0;
            $statusRankB = ($b->status === 'paid') ? 1 : 0;
            if ($statusRankA !== $statusRankB) return $statusRankA <=> $statusRankB;

            $prioA = $typePriority[$a->bill_type] ?? 99;
            $prioB = $typePriority[$b->bill_type] ?? 99;
            if ($prioA !== $prioB) return $prioA <=> $prioB;

            return $a->created_at <=> $b->created_at;
        })->values();

        $eventBills = $eventBills->sort(fn($a, $b) => $a->created_at <=> $b->created_at)->values();

        $pastUnpaidBills = $pastUnpaidBills->sort(function($a, $b) use ($typePriority) {
            $periodA = ($a->period_year ?? 2000) * 100 + ($a->period_month ?? 1);
            $periodB = ($b->period_year ?? 2000) * 100 + ($b->period_month ?? 1);
            if ($periodA !== $periodB) return $periodA <=> $periodB;

            $prioA = $typePriority[$a->bill_type] ?? 99;
            $prioB = $typePriority[$b->bill_type] ?? 99;
            return $prioA <=> $prioB;
        })->values();

        $futureBills = $futureBills->sort(function($a, $b) use ($typePriority) {
            $periodA = ($a->period_year ?? 2099) * 100 + ($a->period_month ?? 1);
            $periodB = ($b->period_year ?? 2099) * 100 + ($b->period_month ?? 1);
            if ($periodA !== $periodB) return $periodA <=> $periodB;

            $prioA = $typePriority[$a->bill_type] ?? 99;
            $prioB = $typePriority[$b->bill_type] ?? 99;
            return $prioA <=> $prioB;
        })->values();

        $pastPaidBills = $pastPaidBills->sort(function($a, $b) {
            $periodA = ($a->period_year ?? 2000) * 100 + ($a->period_month ?? 0);
            $periodB = ($b->period_year ?? 2000) * 100 + ($b->period_month ?? 0);
            return $periodB <=> $periodA;
        })->values();

        $totalEventUnpaid              = $eventBills->whereIn('status', ['unpaid', 'partial'])->sum(fn($b) => max(0, $b->amount - $b->amount_paid));
        $this->totalCurrentMonthUnpaid = $currentMonthBills->whereIn('status', ['unpaid', 'partial'])->sum(fn($b) => max(0, $b->amount - $b->amount_paid));
        $this->totalPastTunggakan      = $pastUnpaidBills->sum(fn($b) => max(0, $b->amount - $b->amount_paid));
        $this->totalTunggakan          = $this->totalPastTunggakan;
        $this->totalHarusDibayarNow    = $this->totalCurrentMonthUnpaid + $this->totalPastTunggakan + $totalEventUnpaid;
        $this->totalFuturePaid         = $futureBills->where('status', 'paid')->sum('amount');
        $this->totalAllPaid            = $allBills->sum('amount_paid');
        $this->totalSudahDibayar       = $this->totalAllPaid;

        // Queue all unpaid bills for selection
        $unpaidQueue = collect()
            ->merge($pastUnpaidBills)
            ->merge($eventBills->whereIn('status', ['unpaid', 'partial']))
            ->merge($currentMonthBills->whereIn('status', ['unpaid', 'partial']))
            ->merge($futureBills->whereIn('status', ['unpaid', 'partial']))
            ->sort(function($a, $b) use ($typePriority) {
                $prioA = $typePriority[$a->bill_type] ?? 99;
                $prioB = $typePriority[$b->bill_type] ?? 99;
                if ($prioA !== $prioB) return $prioA <=> $prioB;

                $periodA = ($a->period_year ?? 2000) * 100 + ($a->period_month ?? 1);
                $periodB = ($b->period_year ?? 2000) * 100 + ($b->period_month ?? 1);
                return $periodA <=> $periodB;
            })->values();

        $mandatoryBillIds = collect()
            ->merge($pastUnpaidBills)
            ->merge($eventBills->whereIn('status', ['unpaid', 'partial']))
            ->merge($currentMonthBills->whereIn('status', ['unpaid', 'partial']))
            ->pluck('id')
            ->toArray();

        if (!$this->isInitialized) {
            $this->selectedBillIds = $mandatoryBillIds;
            $this->isInitialized = true;
        }

        $simulasiHasil    = [];
        $simulasiTotal    = 0.0;

        if (!empty($this->selectedBillIds)) {
            foreach ($unpaidQueue as $bill) {
                if (!in_array($bill->id, $this->selectedBillIds)) continue;

                $maxKekurangan = max(0, (float)$bill->amount - (float)$bill->amount_paid);
                if ($maxKekurangan <= 0) continue;

                $customVal = $this->customAmounts[$bill->id] ?? null;
                $payAmount = (isset($customVal) && is_numeric($customVal) && (float)$customVal > 0)
                    ? min($maxKekurangan, (float)$customVal)
                    : $maxKekurangan;

                $sisaBill = max(0, $maxKekurangan - $payAmount);
                $isFull   = ($sisaBill <= 0);

                $bMonthName = $this->getMonthName($bill->period_month);
                $label = $this->getBillDisplayName($bill) . ($bMonthName ? " ($bMonthName {$bill->period_year})" : "");

                $simulasiHasil[] = [
                    'bill_id'    => $bill->id,
                    'label'      => $label,
                    'terbayar'   => $payAmount,
                    'status'     => $isFull ? 'LUNAS' : 'SEBAGIAN / CICILAN',
                    'sisa_bill'  => $sisaBill,
                    'is_partial' => !$isFull,
                ];
                $simulasiTotal += $payAmount;
            }
        }

        $this->simulasiTotal = $simulasiTotal;
        $allUnpaidIds = $unpaidQueue->pluck('id')->toArray();

        // ─── Riwayat Pengajuan Transfer Manual (Wali) ────────────────────────
        $manualSubmissions = ManualTransferSubmission::where('person_id', $this->personId)
            ->orderBy('created_at', 'desc')
            ->get();

        // ─── Payment History Aggregation (Gateway + Kasir) ───────────────────
        $gatewayQuery = PaymentTransaction::where('person_id', $this->personId)
            ->where('status', 'success');

        if ($this->historyYear) {
            $gatewayQuery->whereYear('created_at', (int)$this->historyYear);
        }

        $gatewayList = ($this->historyMethod === 'kasir' || $this->historyMethod === 'manual') ? collect() : $gatewayQuery->orderBy('created_at', 'desc')->get()->map(function ($trx) {
            $breakdown = collect($trx->bill_breakdown ?? [])->map(function ($item) {
                if (!empty($item['config_label']) && !empty($item['period_label'])) {
                    return $item;
                }
                $bill = Bill::with('config')->find($item['bill_id'] ?? null);
                return array_merge($item, [
                    'config_label' => $bill?->config?->label ?? ucwords(str_replace('_', ' ', $item['bill_type'] ?? '')),
                    'period_label' => $bill ? $this->getBillPeriodLabel($bill) : '',
                ]);
            })->all();

            return [
                'id'           => $trx->id,
                'source'       => 'gateway',
                'order_id'     => $trx->merchant_order_id,
                'method_label' => ($trx->channel_label ?? $trx->payment_channel ?? 'Online') . ' (Duitku)',
                'channel_code' => $trx->payment_channel,
                'amount'       => (float) $trx->total_amount,
                'bill_amount'  => (float) $trx->bill_amount,
                'mdr_amount'   => (float) $trx->mdr_amount,
                'date'         => $trx->created_at,
                'date_fmt'     => $trx->created_at->locale('id')->translatedFormat('d M Y • H:i') . ' WIB',
                'breakdown'    => $breakdown,
                'preview_url'  => route('bukti-bayar.gateway', $trx->id),
                'pdf_url'      => route('bukti-bayar.gateway', $trx->id),
                'status'       => 'Lunas (Online)',
            ];
        });

        $kasirQuery = BillPayment::whereHas('bill', fn($q) => $q->where('person_id', $this->personId))
            ->where('payment_method', '!=', 'gateway_duitku')
            ->with(['bill.config', 'logger']);

        if ($this->historyYear) {
            $kasirQuery->whereYear('payment_date', (int)$this->historyYear);
        }

        $kasirList = ($this->historyMethod === 'gateway' || $this->historyMethod === 'manual') ? collect() : $kasirQuery->orderBy('created_at', 'desc')->get()
            ->groupBy(function ($pay) {
                return $pay->receipt_no ?: ($pay->payment_group_id ?: $pay->id);
            })
            ->map(function ($group, $groupKey) {
                $first = $group->first();

                $methodName = match(strtolower($first->payment_method ?? '')) {
                    'cash'     => '💵 Tunai (Kasir)',
                    'transfer' => '🏦 Transfer Bank',
                    default    => strtoupper($first->payment_method ?? 'Kasir'),
                };

                $totalAmount = (float) $group->sum('amount_paid');
                $isAnyPartial = false;

                $breakdown = $group->map(function ($pay) use (&$isAnyPartial) {
                    $b = $pay->bill;
                    $periodLabel = $b ? $this->getBillPeriodLabel($b) : '';
                    $isPartial = (float)$pay->amount_paid < (float)($b?->amount ?? 0);
                    if ($isPartial) $isAnyPartial = true;

                    return [
                        'config_label' => $b?->config?->label ?? ucwords(str_replace('_', ' ', $b?->bill_type ?? '')),
                        'period_label' => $periodLabel,
                        'pay_portion'  => (float) $pay->amount_paid,
                        'is_partial'   => $isPartial,
                    ];
                })->all();

                $receiptNo = $first->receipt_no ?: ('KSR-' . strtoupper(substr($first->id, 0, 8)));
                $dateObj = $first->payment_date ? \Carbon\Carbon::parse($first->payment_date) : $first->created_at;

                return [
                    'id'           => $first->id,
                    'group_key'    => $groupKey,
                    'source'       => 'kasir',
                    'order_id'     => $receiptNo,
                    'method_label' => $methodName,
                    'channel_code' => $first->payment_method,
                    'amount'       => $totalAmount,
                    'bill_amount'  => $totalAmount,
                    'mdr_amount'   => 0,
                    'date'         => $dateObj,
                    'date_fmt'     => $dateObj ? $dateObj->locale('id')->translatedFormat('d M Y • H:i') . ' WIB' : '—',
                    'breakdown'    => $breakdown,
                    'notes'        => $first->notes,
                    'logger_name'  => $first->logger?->name ?? 'Kasir Pesantren',
                    'preview_url'  => route('bukti-bayar.kuitansi', ['receiptNo' => ($first->receipt_no ?: $first->id), 'from' => 'portal-wali']),
                    'pdf_url'      => route('bukti-bayar.kuitansi.pdf', $first->receipt_no ?: $first->id),
                    'status'       => $isAnyPartial ? 'Sebagian (Kasir)' : 'Lunas (Kasir)',
                ];
            })
            ->values();

        $paymentHistory = $gatewayList->concat($kasirList)->sortByDesc(fn($item) => $item['date'] ? $item['date']->timestamp : 0)->values();

        $gatewayYears = PaymentTransaction::where('person_id', $this->personId)->where('status', 'success')->pluck('created_at')->map(fn($d) => (int)$d->format('Y'));
        $kasirYears   = BillPayment::whereHas('bill', fn($q) => $q->where('person_id', $this->personId))->pluck('payment_date')->filter()->map(fn($d) => (int)\Carbon\Carbon::parse($d)->format('Y'));
        $historyYears = $gatewayYears->concat($kasirYears)->filter()->unique()->sortDesc()->values();

        $lastBillUpdate = Bill::where('person_id', $this->personId)->max('updated_at');
        $lastPayment = BillPayment::whereHas('bill', fn($q) => $q->where('person_id', $this->personId))->max('created_at');
        $latestTimestamp = max($lastBillUpdate, $lastPayment);
        $lastUpdatedLabel = $latestTimestamp
            ? \Carbon\Carbon::parse($latestTimestamp)->locale('id')->translatedFormat('d M Y • H:i') . ' WIB'
            : 'Hari ini (Sistem Real-Time)';

        $partition = $this->getUnpaidBillsPartition();
        $pastUnpaidList = $partition['past'];
        $currentUnpaidList = $partition['current'];
        $futureUnpaidList = $partition['future'];
        $hasPastUnpaid = $pastUnpaidList->isNotEmpty();

        return view('livewire.wali-portal.dashboard-tagihan', [
            'portalTab'               => $this->portalTab,
            'santri'                  => $santri,
            'isPutri'                 => $isPutri,
            'bank1Name'               => $bank1Name,
            'bsiRekening'             => $bsiRekening,
            'bsiAn'                   => $bsiAn,
            'bank2Name'               => $bank2Name,
            'briRekening'             => $briRekening,
            'briAn'                   => $briAn,
            'waBendahara'             => $waBendahara,
            'waName'                  => $waName,
            'directWaUrl'             => $directWaUrl,
            'currentMonth'            => $currentMonth,
            'currentYear'             => $currentYear,
            'currentMonthName'        => $this->getMonthName($currentMonth),
            'currentMonthBills'       => $currentMonthBills,
            'eventBills'              => $eventBills,
            'pastUnpaidBills'         => $pastUnpaidBills,
            'futureBills'             => $futureBills,
            'pastPaidBills'           => $pastPaidBills,
            'unpaidQueue'             => $unpaidQueue,
            'pastUnpaidList'          => $pastUnpaidList,
            'currentUnpaidList'       => $currentUnpaidList,
            'futureUnpaidList'        => $futureUnpaidList,
            'hasPastUnpaid'           => $hasPastUnpaid,
            'allUnpaidIds'            => $allUnpaidIds,
            'totalCurrentMonthUnpaid' => $this->totalCurrentMonthUnpaid,
            'totalPastTunggakan'      => $this->totalPastTunggakan,
            'totalTunggakan'          => $this->totalTunggakan,
            'totalHarusDibayarNow'    => $this->totalHarusDibayarNow,
            'totalFuturePaid'         => $this->totalFuturePaid,
            'totalAllPaid'            => $this->totalAllPaid,
            'totalSudahDibayar'       => $this->totalSudahDibayar,
            'simulasiHasil'           => $simulasiHasil,
            'simulasiTotal'           => $simulasiTotal,
            'manualSubmissions'       => $manualSubmissions,
            'paymentHistory'          => $paymentHistory,
            'historyYears'            => $historyYears,
            'lastUpdatedLabel'        => $lastUpdatedLabel,
        ])->layout('layouts.wali-portal', ['title' => 'Portal Wali — ' . $santri->name]);
    }
}
