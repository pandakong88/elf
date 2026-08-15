<?php

namespace App\Livewire\WaliPortal;

use App\Modules\Keuangan\Models\PaymentTransaction;
use App\Modules\Keuangan\Services\DuitkuService;
use Livewire\Attributes\Url;
use Livewire\Component;

class StatusPembayaran extends Component
{
    #[Url]
    public string $orderId = '';

    public ?PaymentTransaction $transaction = null;

    // State polling
    public int  $pollCount   = 0;
    public int  $maxPolls    = 20;       // Maks 20x poll = 60 detik
    public bool $isDone      = false;    // Stop polling jika true
    public string $uiState   = 'loading'; // loading | pending | success | failed

    public function mount(): void
    {
        if (empty($this->orderId)) {
            $this->uiState = 'failed';
            $this->isDone  = true;
            return;
        }

        $this->transaction = PaymentTransaction::where('merchant_order_id', $this->orderId)->first();

        if (!$this->transaction) {
            $this->uiState = 'failed';
            $this->isDone  = true;
            return;
        }

        // Cek status awal
        $this->evaluateStatus();
    }

    /**
     * Dipanggil oleh Livewire polling (wire:poll.3s) dari blade.
     * Cek status transaksi ke Duitku API jika masih pending.
     */
    public function checkStatus(): void
    {
        if ($this->isDone || !$this->transaction) {
            return;
        }

        $this->pollCount++;

        // Refresh dari DB terlebih dahulu (mungkin callback sudah masuk)
        $this->transaction = $this->transaction->fresh();

        if ($this->transaction->status === 'success') {
            $this->uiState = 'success';
            $this->isDone  = true;
            return;
        }

        // Jika sudah maks poll, anggap pending tanpa hasil
        if ($this->pollCount >= $this->maxPolls) {
            $this->uiState = 'pending';
            $this->isDone  = true;
            return;
        }

        // Setiap 3 poll (9 detik), tanya langsung ke Duitku
        if ($this->pollCount % 3 === 0) {
            try {
                $duitkuService = app(DuitkuService::class);
                $statusData    = $duitkuService->checkTransactionStatus($this->orderId);

                if (($statusData['statusCode'] ?? '') === '00' || ($statusData['resultCode'] ?? '') === '00') {
                    // Duitku bilang sukses tapi callback belum masuk — proses manual
                    $duitkuService->processCallback([
                        'merchantCode'    => config('duitku.merchant_code'),
                        'amount'          => (int) $this->transaction->total_amount,
                        'merchantOrderId' => $this->orderId,
                        'resultCode'      => '00',
                        'reference'       => $statusData['reference'] ?? null,
                        'signature'       => md5(
                            config('duitku.merchant_code') .
                            (int) $this->transaction->total_amount .
                            $this->orderId .
                            config('duitku.api_key')
                        ),
                    ]);

                    $this->transaction = $this->transaction->fresh();
                    $this->uiState     = 'success';
                    $this->isDone      = true;
                    return;
                }
            } catch (\Exception $e) {
                // Gagal cek status — lanjut polling berikutnya
                \Illuminate\Support\Facades\Log::warning('[StatusPembayaran] checkStatus API error', [
                    'order_id' => $this->orderId,
                    'error'    => $e->getMessage(),
                ]);
            }
        }

        $this->evaluateStatus();
    }

    /**
     * Evaluasi state UI berdasarkan status transaksi di DB.
     */
    private function evaluateStatus(): void
    {
        if (!$this->transaction) {
            $this->uiState = 'failed';
            $this->isDone  = true;
            return;
        }

        match ($this->transaction->status) {
            'success' => ($this->uiState = 'success') && ($this->isDone = true),
            'failed'  => ($this->uiState = 'failed')  && ($this->isDone = true),
            'expired' => ($this->uiState = 'failed')  && ($this->isDone = true),
            default   => $this->uiState = 'pending',
        };
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

    public function getBillPeriodLabel(?Bill $bill): string
    {
        if (!$bill) return '';

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $interval = $bill->config?->interval ?? 'monthly';

        if (in_array($interval, ['semester', '2x_yearly'])) {
            $s = $bill->period_sub ?? ($bill->period_month && $bill->period_month <= 6 ? 1 : 2);
            return "Semester {$s} ({$bill->period_year})";
        }

        if (in_array($interval, ['once', 'insidental', 'event', 'sekali', 'yearly'])) {
            return $bill->period_year ? "Tahun {$bill->period_year}" : '';
        }

        if ($bill->period_month && isset($monthNames[$bill->period_month])) {
            return "{$monthNames[$bill->period_month]} {$bill->period_year}";
        }

        return $bill->period_year ? (string)$bill->period_year : '';
    }

    public function getBillTitle(?Bill $bill, ?string $fallbackType = null): string
    {
        if (!$bill) {
            return $fallbackType ? $this->getBillTypeLabel($fallbackType) : 'Tagihan Santri';
        }

        $baseName = $bill->notes ?: ($bill->config?->label ?: $this->getBillTypeLabel($bill->bill_type));
        $period   = $this->getBillPeriodLabel($bill);

        return $period ? "{$baseName} ({$period})" : $baseName;
    }

    public function render()
    {
        $santri = null;
        if ($this->transaction) {
            $santri = $this->transaction->person()->with([
                'roomAssignments' => fn($q) => $q->where('is_active', true)->with('room.dormitory'),
                'madrasahEnrollments' => fn($q) => $q->where('is_active', true)->with('kelas'),
            ])->first();
        }

        $bills = $this->transaction ? $this->transaction->bills()->load('config') : collect();

        return view('livewire.wali-portal.status-pembayaran', [
            'transaction' => $this->transaction,
            'santri'      => $santri,
            'bills'       => $bills,
        ])->layout('layouts.wali-portal', [
            'title' => 'Status Pembayaran — Elvith',
        ]);
    }
}

