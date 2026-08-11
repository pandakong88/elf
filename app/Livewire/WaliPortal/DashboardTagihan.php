<?php

namespace App\Livewire\WaliPortal;

use Livewire\Component;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\LandingPageContent;
use App\Modules\Keuangan\Models\Bill;

class DashboardTagihan extends Component
{
    public string $personId;
    public string $activeSection = 'ringkasan';

    // Simulasi Checklist — array of bill IDs yang dipilih wali
    public array $selectedBillIds = [];
    public bool $isInitialized = false;

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

        if (in_array($interval, ['semester', '2x_yearly'])) {
            $s = $bill->period_sub ?? ($bill->period_month && $bill->period_month <= 6 ? 1 : 2);
            return "Semester {$s} ({$bill->period_year})";
        }

        if (in_array($interval, ['caturwulan', '3x_yearly'])) {
            $cw = $bill->period_sub ?? ($bill->period_month ? ($bill->period_month <= 4 ? 1 : ($bill->period_month <= 8 ? 2 : 3)) : 1);
            return "Caturwulan {$cw} ({$bill->period_year})";
        }

        if (in_array($interval, ['triwulan', '4x_yearly'])) {
            $tw = $bill->period_sub ?? ($bill->period_month ? (int)ceil($bill->period_month / 3) : 1);
            return "Triwulan {$tw} ({$bill->period_year})";
        }

        if (in_array($interval, ['bimulanan', '6x_yearly'])) {
            $b = $bill->period_sub ?? ($bill->period_month ? (int)ceil($bill->period_month / 2) : 1);
            return "Dwibulanan {$b} ({$bill->period_year})";
        }

        if (in_array($interval, ['once', 'insidental', 'event', 'sekali', 'yearly'])) {
            return "Tahun {$bill->period_year}";
        }

        $monthName = $this->getMonthName($bill->period_month);
        return trim("{$monthName} {$bill->period_year}");
    }

    public function classifyBillPeriodStatus(Bill $bill, int $currentMonth, int $currentYear): string
    {
        // 1. Safety net: If due_date exists and due_date >= today (and unpaid), it cannot be past yet!
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

        // Determine period start month & end month based on interval
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
            // monthly
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
        if (!empty($bill->notes)) {
            return $bill->notes;
        }
        if ($bill->config && !empty($bill->config->label)) {
            return $bill->config->label;
        }
        return $this->getBillTypeLabel($bill->bill_type);
    }

    public function render()
    {
        $santri = Person::with([
            'roomAssignments' => fn($q) => $q->where('is_active', true)->with('room.dormitory'),
            'madrasahEnrollments' => fn($q) => $q->where('is_active', true)->with('kelas'),
            'santriProfile'
        ])->findOrFail($this->personId);

        // Load Dynamic CMS Content
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

        $waliAnnouncement = $contents['wali_announcement'] ?? 'Pembayaran tagihan santri dilakukan sebelum tanggal 10 setiap bulannya.';
        $waliRekapInfo    = $contents['wali_rekap_info'] ?? 'Data tagihan diperbarui oleh bendahara setiap Tanggal 1 dan 15 setiap bulannya. Jika Bapak/Ibu sudah melakukan transfer namun status tagihan belum berubah, mohon bersabar hingga tanggal pembaruan berikutnya. Untuk konfirmasi lebih lanjut, silakan hubungi bendahara melalui tombol WhatsApp di bawah.';

        $now = now();
        $currentMonth = (int) $now->format('m');
        $currentYear  = (int) $now->format('Y');

        $allBills = Bill::with('config')
            ->where('person_id', $this->personId)
            ->whereNotIn('status', ['refund_requested', 'refunded', 'cancelled'])
            ->get();

        // Priority Order untuk Jenis Tagihan Utama
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

        // Grouping Logika Blok
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

        // PENGURUTAN TERSTRUKTUR & RAPI:
        $currentMonthBills = $currentMonthBills->sort(function($a, $b) use ($typePriority) {
            $statusRankA = ($a->status === 'paid') ? 1 : 0;
            $statusRankB = ($b->status === 'paid') ? 1 : 0;
            if ($statusRankA !== $statusRankB) return $statusRankA <=> $statusRankB;

            $prioA = $typePriority[$a->bill_type] ?? 99;
            $prioB = $typePriority[$b->bill_type] ?? 99;
            if ($prioA !== $prioB) return $prioA <=> $prioB;

            return $a->created_at <=> $b->created_at;
        })->values();

        $eventBills = $eventBills->sort(function($a, $b) {
            return $a->created_at <=> $b->created_at;
        })->values();

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

        // Sort pastPaidBills: terbaru di atas
        $pastPaidBills = $pastPaidBills->sort(function($a, $b) {
            $periodA = ($a->period_year ?? 2000) * 100 + ($a->period_month ?? 0);
            $periodB = ($b->period_year ?? 2000) * 100 + ($b->period_month ?? 0);
            return $periodB <=> $periodA; // DESC — terbaru dulu
        })->values();

        // Kalkulasi Total
        $totalEventUnpaid              = $eventBills->whereIn('status', ['unpaid', 'partial'])->sum(fn($b) => max(0, $b->amount - $b->amount_paid));
        $this->totalCurrentMonthUnpaid = $currentMonthBills->whereIn('status', ['unpaid', 'partial'])->sum(fn($b) => max(0, $b->amount - $b->amount_paid));
        $this->totalPastTunggakan      = $pastUnpaidBills->sum(fn($b) => max(0, $b->amount - $b->amount_paid));
        $this->totalTunggakan          = $this->totalPastTunggakan;
        $this->totalHarusDibayarNow    = $this->totalCurrentMonthUnpaid + $this->totalPastTunggakan + $totalEventUnpaid;

        $this->totalFuturePaid         = $futureBills->where('status', 'paid')->sum('amount');
        $this->totalAllPaid            = $allBills->sum('amount_paid');
        $this->totalSudahDibayar       = $this->totalAllPaid;

        // =========================================================
        // KALKULATOR SIMULASI — BERBASIS CHECKLIST PILIHAN WALI
        // =========================================================
        // Tandai kategori untuk masing-masing item
        $pastUnpaidBills->transform(function($b) { $b->simulasi_cat = 'past'; return $b; });
        $eventUnpaid = $eventBills->whereIn('status', ['unpaid', 'partial'])->transform(function($b) { $b->simulasi_cat = 'event'; return $b; });
        $currentUnpaid = $currentMonthBills->whereIn('status', ['unpaid', 'partial'])->transform(function($b) { $b->simulasi_cat = 'current'; return $b; });
        $futureUnpaid = $futureBills->whereIn('status', ['unpaid', 'partial'])->transform(function($b) { $b->simulasi_cat = 'future'; return $b; });

        $unpaidQueue = collect()
            ->merge($pastUnpaidBills)
            ->merge($eventUnpaid)
            ->merge($currentUnpaid)
            ->merge($futureUnpaid)
            ->sort(function($a, $b) use ($typePriority) {
                // 1. Urutkan berdasarkan jenis tagihan/nama (SPP Pondok -> Syahriah Madrasah -> dst)
                $nameA = $this->getBillDisplayName($a);
                $nameB = $this->getBillDisplayName($b);

                $prioA = $typePriority[$a->bill_type] ?? 99;
                $prioB = $typePriority[$b->bill_type] ?? 99;

                if ($prioA !== $prioB) return $prioA <=> $prioB;

                $cmpName = strcmp($nameA, $nameB);
                if ($cmpName !== 0) return $cmpName;

                // 2. Urutkan berdasarkan periode bulan & tahun (Terkecil/Terlama -> Terbaru)
                $periodA = ($a->period_year ?? 2000) * 100 + ($a->period_month ?? 1);
                $periodB = ($b->period_year ?? 2000) * 100 + ($b->period_month ?? 1);
                if ($periodA !== $periodB) return $periodA <=> $periodB;

                return $a->created_at <=> $b->created_at;
            })->values();

        // Smart Default: Auto-select Tunggakan + Bulan Ini + Kegiatan pada render pertama
        $mandatoryBillIds = collect()
            ->merge($pastUnpaidBills)
            ->merge($eventUnpaid)
            ->merge($currentUnpaid)
            ->pluck('id')
            ->toArray();

        if (!$this->isInitialized) {
            $this->selectedBillIds = $mandatoryBillIds;
            $this->isInitialized = true;
        }

        $simulasiHasil    = [];
        $simulasiTotal    = 0.0;
        $simulasiWaUrl    = '';

        if (!empty($this->selectedBillIds)) {
            foreach ($unpaidQueue as $bill) {
                if (!in_array($bill->id, $this->selectedBillIds)) continue;

                $kekurangan = max(0, $bill->amount - $bill->amount_paid);
                if ($kekurangan <= 0) continue;

                $bMonthName = $this->getMonthName($bill->period_month);
                $label = $this->getBillDisplayName($bill) . ($bMonthName ? " ($bMonthName {$bill->period_year})" : "");

                $simulasiHasil[] = [
                    'bill_id'   => $bill->id,
                    'label'     => $label,
                    'terbayar'  => $kekurangan,
                    'status'    => 'LUNAS',
                    'sisa_bill' => 0,
                ];
                $simulasiTotal += $kekurangan;
            }

            $this->simulasiTotal = $simulasiTotal;

            if ($simulasiTotal > 0) {
                $waText  = "Assalamu'alaikum $waName,\n\n";
                $waText .= "Saya Wali Santri dari:\n";
                $waText .= "• Nama: {$santri->name}\n";
                if ($santri->nis) { $waText .= "• NIS: {$santri->nis}\n"; }
                $waText .= "\nSaya bermaksud mengonfirmasi pembayaran sebesar *Rp " . number_format($simulasiTotal, 0, ',', '.') . "* dengan rincian:\n";
                foreach ($simulasiHasil as $idx => $item) {
                    $num = $idx + 1;
                    $waText .= "{$num}. {$item['label']} = Rp " . number_format($item['terbayar'], 0, ',', '.') . "\n";
                }
                $waText .= "\nMohon info petunjuk konfirmasi selanjutnya. Terima kasih.";

                $cleanWa = preg_replace('/[^0-9]/', '', $waBendahara);
                $simulasiWaUrl = "https://wa.me/{$cleanWa}?text=" . urlencode($waText);
            }
        }

        $simulasiBillOptions = $unpaidQueue->values();
        $pastBillIdsOnly = $pastUnpaidBills->pluck('id')->toArray();

        $putraData = [
            'bank1_name' => $contents['wali_bank1_name_putra'] ?? 'Bank Syariah Indonesia (BSI)',
            'bsi'        => $contents['wali_bsi_putra'] ?? '7123456789',
            'bsi_an'     => $contents['wali_bsi_putra_an'] ?? 'Pesantren Al-Fithroh Putra',
            'bank2_name' => $contents['wali_bank2_name_putra'] ?? 'Bank BRI',
            'bri'        => $contents['wali_bri_putra'] ?? '',
            'bri_an'     => $contents['wali_bri_putra_an'] ?? '',
            'wa'         => $contents['wali_wa_putra'] ?? '6281234567890',
            'wa_name'    => $contents['wali_wa_putra_name'] ?? 'Bendahara Putra Al-Fithroh',
            'wa_url'     => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $contents['wali_wa_putra'] ?? '6281234567890') . '?text=' . urlencode("Assalamu'alaikum Bendahara Putra Al-Fithroh, saya Wali Santri dari {$santri->name} ingin konfirmasi pembayaran."),
        ];

        $putriData = [
            'bank1_name' => $contents['wali_bank1_name_putri'] ?? 'Bank Syariah Indonesia (BSI)',
            'bsi'        => $contents['wali_bsi_putri'] ?? '',
            'bsi_an'     => $contents['wali_bsi_putri_an'] ?? 'Pesantren Al-Fithroh Putri',
            'bank2_name' => $contents['wali_bank2_name_putri'] ?? 'Bank BRI',
            'bri'        => $contents['wali_bri_putri'] ?? '',
            'bri_an'     => $contents['wali_bri_putri_an'] ?? '',
            'wa'         => $contents['wali_wa_putri'] ?? '6285713285438',
            'wa_name'    => $contents['wali_wa_putri_name'] ?? 'Bendahara Putri Al-Fithroh',
            'wa_url'     => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $contents['wali_wa_putri'] ?? '6285713285438') . '?text=' . urlencode("Assalamu'alaikum Bendahara Putri Al-Fithroh, saya Wali Santri dari {$santri->name} ingin konfirmasi pembayaran."),
        ];

        // Compute Last Updated Time for Wali Portal Transparency
        $lastBillUpdate = Bill::where('person_id', $this->personId)->max('updated_at');
        $lastPayment = \App\Modules\Keuangan\Models\BillPayment::whereHas('bill', fn($q) => $q->where('person_id', $this->personId))
            ->max('paid_at');

        $latestTimestamp = max($lastBillUpdate, $lastPayment);

        $lastUpdatedLabel = $latestTimestamp
            ? \Carbon\Carbon::parse($latestTimestamp)->locale('id')->translatedFormat('d M Y • H:i') . ' WIB'
            : 'Hari ini (Sistem Real-Time)';

        return view('livewire.wali-portal.dashboard-tagihan', [
            'santri'                  => $santri,
            'isPutri'                 => $isPutri,
            'putraData'               => $putraData,
            'putriData'               => $putriData,
            'bank1Name'               => $bank1Name,
            'bsiRekening'             => $bsiRekening,
            'bsiAn'                   => $bsiAn,
            'bank2Name'               => $bank2Name,
            'briRekening'             => $briRekening,
            'briAn'                   => $briAn,
            'waBendahara'             => $waBendahara,
            'waName'                  => $waName,
            'directWaUrl'             => $directWaUrl,
            'waliAnnouncement'        => $waliAnnouncement,
            'waliRekapInfo'           => $waliRekapInfo,
            'currentMonth'            => $currentMonth,
            'currentYear'             => $currentYear,
            'currentMonthName'        => $this->getMonthName($currentMonth),
            'currentMonthBills'       => $currentMonthBills,
            'eventBills'              => $eventBills,
            'pastUnpaidBills'         => $pastUnpaidBills,
            'futureBills'             => $futureBills,
            'pastPaidBills'           => $pastPaidBills,
            'totalCurrentMonthUnpaid' => $this->totalCurrentMonthUnpaid,
            'totalPastTunggakan'      => $this->totalPastTunggakan,
            'totalTunggakan'          => $this->totalTunggakan,
            'totalHarusDibayarNow'    => $this->totalHarusDibayarNow,
            'totalFuturePaid'         => $this->totalFuturePaid,
            'totalAllPaid'            => $this->totalAllPaid,
            'totalSudahDibayar'       => $this->totalSudahDibayar,
            'simulasiHasil'           => $simulasiHasil,
            'simulasiTotal'           => $simulasiTotal,
            'simulasiWaUrl'           => $simulasiWaUrl,
            'simulasiBillOptions'     => $simulasiBillOptions,
            'mandatoryBillIds'        => $mandatoryBillIds,
            'pastBillIdsOnly'         => $pastBillIdsOnly,
            'lastUpdatedLabel'        => $lastUpdatedLabel,
        ])->layout('layouts.wali-portal', ['title' => 'Dashboard Tagihan — ' . $santri->name]);
    }
}
