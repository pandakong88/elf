<?php

namespace App\Http\Controllers;

use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillPayment;
use App\Modules\Keuangan\Models\PaymentTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SettlementReportController extends Controller
{
    /**
     * Resolve gender scope based on user roles or abort if unauthorized.
     */
    private function resolveGenderScope(): ?string
    {
        $user = auth()->user();
        if (!$user) abort(401);

        if ($user->hasRole(['super-admin', 'manajemen', 'pengasuh', 'bendahara-pondok', 'bendahara-pusat'])) {
            return null; // All access
        }

        if ($user->hasRole(['bendahara-putra', 'lurah-putra'])) {
            return 'L';
        }

        if ($user->hasRole(['bendahara-putri', 'lurah-putri'])) {
            return 'P';
        }

        abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk melihat rekonsiliasi keuangan.');
    }

    /**
     * Download PDF Rekap Settlement & Distribusi Dana per Pos Anggaran.
     */
    public function downloadSettlementPdf(Request $request): Response
    {
        $genderScope = $this->resolveGenderScope();

        $dateFrom = $request->query('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->query('date_to', now()->toDateString());
        $source   = $request->query('source', 'gateway'); // 'gateway' | 'kasir' | 'all'
        $targetGender = $genderScope ?: $request->query('gender', null);

        $fromCarbon = Carbon::parse($dateFrom)->startOfDay();
        $toCarbon   = Carbon::parse($dateTo)->endOfDay();

        $appName = config('app.name', 'Pondok Pesantren Al-Fithroh');

        // Query Transactions / Payments
        $totalGross = 0.0;
        $totalMdr   = 0.0;
        $totalNet   = 0.0;
        $totalTrx   = 0;

        // Breakdown categories: Syahriah Putra, Syahriah Putri, Madrasah, Kitab, Majek Pagi, Majek Sore, Kas Komplek, Lainnya
        $categories = [
            'syahriah_putra' => ['label' => 'Syahriah / SPP Pondok Putra', 'desc' => 'Operasional pesantren unit putra', 'amount' => 0.0, 'count' => 0],
            'syahriah_putri' => ['label' => 'Syahriah / SPP Pondok Putri', 'desc' => 'Operasional pesantren unit putri', 'amount' => 0.0, 'count' => 0],
            'madrasah'       => ['label' => 'Syahriah Madrasah', 'desc' => 'Operasional unit pendidikan formal/diniyah', 'amount' => 0.0, 'count' => 0],
            'kitab'          => ['label' => 'Biaya Kitab / Buku', 'desc' => 'Pengadaan sarana belajar santri', 'amount' => 0.0, 'count' => 0],
            'majek_pagi'     => ['label' => 'Katering Majek (Pagi)', 'desc' => 'Logistik konsumsi makan pagi', 'amount' => 0.0, 'count' => 0],
            'majek_sore'     => ['label' => 'Katering Majek (Sore)', 'desc' => 'Logistik konsumsi makan sore', 'amount' => 0.0, 'count' => 0],
            'kas_komplek'    => ['label' => 'Kas Komplek / Asrama', 'desc' => 'Dana titipan kebersihan & kegiatan asrama', 'amount' => 0.0, 'count' => 0],
            'lainnya'        => ['label' => 'Iuran Lainnya / Insidental', 'desc' => 'Pendaftaran, kebersihan, & event', 'amount' => 0.0, 'count' => 0],
        ];

        // Dormitories breakdown
        $dormitories = Dormitory::active()
            ->when($targetGender, fn($q, $g) => $q->where('gender', $g))
            ->orderByRaw("gender ASC, name ASC")->get();
        $dormBreakdown = [];
        foreach ($dormitories as $d) {
            $dormBreakdown[$d->id] = [
                'dormitory_id'   => $d->id,
                'dormitory_name' => $d->name,
                'gender'         => $d->gender,
                'count_santri'   => 0,
                'count_bills'    => 0,
                'total_amount'   => 0.0,
                'santri_ids'     => [],
            ];
        }

        // 1. Process Gateway Transactions
        if ($source === 'gateway' || $source === 'all') {
            $gatewayQuery = PaymentTransaction::where('status', 'success')
                ->where(function ($q) use ($fromCarbon, $toCarbon) {
                    $q->whereBetween('callback_received_at', [$fromCarbon, $toCarbon])
                      ->orWhere(function ($oq) use ($fromCarbon, $toCarbon) {
                          $oq->whereNull('callback_received_at')
                             ->whereBetween('created_at', [$fromCarbon, $toCarbon]);
                      });
                })
                ->when($targetGender, fn($q, $g) => $q->whereHas('person', fn($pq) => $pq->where('gender', $g)))
                ->with(['person.roomAssignments' => fn($q) => $q->active()->with('room.dormitory')]);

            $gatewayTrx = $gatewayQuery->get();
            $totalTrx += $gatewayTrx->count();

            foreach ($gatewayTrx as $trx) {
                $totalGross += (float) $trx->total_amount;
                $totalMdr   += (float) $trx->mdr_amount;
                $totalNet   += (float) $trx->bill_amount;

                $person = $trx->person;
                $activeAssignment = $person?->roomAssignments?->first();
                $dormId = $activeAssignment?->room?->dormitory_id;

                foreach ($trx->bill_breakdown ?? [] as $item) {
                    $amt = (float) ($item['pay_portion'] ?? $item['net_amount'] ?? 0);
                    $type = $item['bill_type'] ?? '';

                    $this->allocateToCategory($categories, $type, $amt, $person?->gender, $item['config_label'] ?? null);

                    if ($type === 'kas_komplek' && $dormId && isset($dormBreakdown[$dormId])) {
                        $dormBreakdown[$dormId]['total_amount'] += $amt;
                        $dormBreakdown[$dormId]['count_bills']++;
                        if ($person && !in_array($person->id, $dormBreakdown[$dormId]['santri_ids'])) {
                            $dormBreakdown[$dormId]['santri_ids'][] = $person->id;
                            $dormBreakdown[$dormId]['count_santri']++;
                        }
                    }
                }
            }
        }

        // 2. Process Cashier Payments
        if ($source === 'kasir' || $source === 'all') {
            $kasirQuery = BillPayment::where('payment_method', '!=', 'gateway_duitku')
                ->where(function ($q) use ($dateFrom, $dateTo, $fromCarbon, $toCarbon) {
                    $q->whereBetween('payment_date', [$dateFrom, $dateTo])
                      ->orWhereBetween('created_at', [$fromCarbon, $toCarbon]);
                })
                ->when($targetGender, fn($q, $g) => $q->whereHas('bill.person', fn($pq) => $pq->where('gender', $g)))
                ->with(['bill.person.roomAssignments' => fn($q) => $q->active()->with('room.dormitory'), 'bill.config']);

            $kasirPayments = $kasirQuery->get();
            $totalTrx += $kasirPayments->count();

            foreach ($kasirPayments as $pay) {
                $amt = (float) $pay->amount_paid;
                $totalGross += $amt;
                $totalNet   += $amt;

                $bill = $pay->bill;
                $person = $bill?->person;
                $activeAssignment = $person?->roomAssignments?->first();
                $dormId = $activeAssignment?->room?->dormitory_id;
                $type = $bill?->bill_type ?? '';

                $this->allocateToCategory($categories, $type, $amt, $person?->gender, $bill?->config?->label ?? null);

                if ($type === 'kas_komplek' && $dormId && isset($dormBreakdown[$dormId])) {
                    $dormBreakdown[$dormId]['total_amount'] += $amt;
                    $dormBreakdown[$dormId]['count_bills']++;
                    if ($person && !in_array($person->id, $dormBreakdown[$dormId]['santri_ids'])) {
                        $dormBreakdown[$dormId]['santri_ids'][] = $person->id;
                        $dormBreakdown[$dormId]['count_santri']++;
                    }
                }
            }
        }

        $sourceLabel = match ($source) {
            'gateway' => '⚡ Khusus Gateway Online (Duitku)',
            'kasir'   => '💵 Khusus Kasir Manual (Tunai / Bank)',
            default   => '🌐 Seluruh Pembayaran (Gateway + Kasir)',
        };

        $periodLabel = Carbon::parse($dateFrom)->locale('id')->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($dateTo)->locale('id')->translatedFormat('d M Y');

        $activeDormBreakdown = array_values(array_filter($dormBreakdown, fn($d) => $d['total_amount'] > 0));

        $data = [
            'app_name'            => $appName,
            'period_label'        => $periodLabel,
            'source_label'        => $sourceLabel,
            'total_gross'         => $totalGross,
            'total_mdr'           => $totalMdr,
            'total_net'           => $totalNet,
            'total_trx'           => $totalTrx,
            'category_breakdown'  => array_values(array_filter($categories, fn($c) => $c['amount'] > 0)),
            'dormitory_breakdown' => $activeDormBreakdown,
            'generated_at'        => now()->locale('id')->translatedFormat('d F Y, H:i') . ' WIB',
            'generated_by'        => auth()->user()?->name ?? 'Bendahara Pusat',
        ];

        $pdf = Pdf::loadView('pdf.rekap-settlement', $data)->setPaper('a4', 'portrait');

        return $pdf->stream('Rekap-Settlement-' . Carbon::parse($dateFrom)->format('Ymd') . '-' . Carbon::parse($dateTo)->format('Ymd') . '.pdf');
    }

    /**
     * Download PDF Slip Serah Terima Kas Komplek per Asrama.
     */
    public function downloadSlipKomplekPdf(Request $request, string $dormitoryId): Response
    {
        $genderScope = $this->resolveGenderScope();

        $dormitory = Dormitory::findOrFail($dormitoryId);

        if ($genderScope && $dormitory->gender !== $genderScope) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk mengunduh slip kas komplek unit ini.');
        }

        $dateFrom  = $request->query('date_from', now()->startOfMonth()->toDateString());
        $dateTo    = $request->query('date_to', now()->toDateString());
        $source    = $request->query('source', 'gateway');

        $fromCarbon = Carbon::parse($dateFrom)->startOfDay();
        $toCarbon   = Carbon::parse($dateTo)->endOfDay();
        $appName    = config('app.name', 'Pondok Pesantren Al-Fithroh');

        $santriList = [];
        $totalAmount = 0.0;

        // 1. Gateway
        if ($source === 'gateway' || $source === 'all') {
            $gatewayTrx = PaymentTransaction::where('status', 'success')
                ->whereBetween('created_at', [$fromCarbon, $toCarbon])
                ->whereHas('person.roomAssignments', function ($q) use ($dormitoryId) {
                    $q->active()->whereHas('room', fn($r) => $r->where('dormitory_id', $dormitoryId));
                })
                ->with(['person.roomAssignments' => fn($q) => $q->active()->with('room')]);

            foreach ($gatewayTrx as $trx) {
                $person = $trx->person;
                $activeAssignment = $person?->roomAssignments?->first();

                foreach ($trx->bill_breakdown ?? [] as $item) {
                    if (($item['bill_type'] ?? '') === 'kas_komplek') {
                        $amt = (float) ($item['pay_portion'] ?? $item['net_amount'] ?? 0);
                        $totalAmount += $amt;
                        $santriList[] = [
                            'nis'       => $person->nis ?? '-',
                            'name'      => $person->name ?? '—',
                            'room_name' => $activeAssignment?->room?->name ?? '-',
                            'paid_date' => $trx->created_at->locale('id')->translatedFormat('d M Y, H:i'),
                            'method'    => ($trx->channel_label ?? $trx->payment_channel ?? 'Online') . ' (Duitku)',
                            'amount'    => $amt,
                        ];
                    }
                }
            }
        }

        // 2. Kasir
        if ($source === 'kasir' || $source === 'all') {
            $kasirPayments = BillPayment::where('payment_method', '!=', 'gateway_duitku')
                ->whereBetween('payment_date', [$dateFrom, $dateTo])
                ->whereHas('bill', function ($q) use ($dormitoryId) {
                    $q->where('bill_type', 'kas_komplek')
                        ->whereHas('person.roomAssignments', function ($rq) use ($dormitoryId) {
                            $rq->active()->whereHas('room', fn($r) => $r->where('dormitory_id', $dormitoryId));
                        });
                })
                ->with(['bill.person.roomAssignments' => fn($q) => $q->active()->with('room')])
                ->get();

            foreach ($kasirPayments as $pay) {
                $bill = $pay->bill;
                $person = $bill?->person;
                $activeAssignment = $person?->roomAssignments?->first();
                $amt = (float) $pay->amount_paid;

                $totalAmount += $amt;
                $santriList[] = [
                    'nis'       => $person->nis ?? '-',
                    'name'      => $person->name ?? '—',
                    'room_name' => $activeAssignment?->room?->name ?? '-',
                    'paid_date' => $pay->payment_date ? Carbon::parse($pay->payment_date)->locale('id')->translatedFormat('d M Y') : '-',
                    'method'    => strtoupper($pay->payment_method ?? 'Kasir'),
                    'amount'    => $amt,
                ];
            }
        }

        $periodLabel = Carbon::parse($dateFrom)->locale('id')->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($dateTo)->locale('id')->translatedFormat('d M Y');

        $data = [
            'app_name'     => $appName,
            'dormitory'    => $dormitory,
            'period_label' => $periodLabel,
            'total_amount' => $totalAmount,
            'santri_list'  => $santriList,
            'generated_at' => now()->locale('id')->translatedFormat('d F Y, H:i') . ' WIB',
            'generated_by' => auth()->user()?->name ?? 'Bendahara Pusat',
        ];

        $pdf = Pdf::loadView('pdf.slip-kas-komplek', $data)->setPaper('a4', 'portrait');

        return $pdf->stream('Slip-Kas-Komplek-' . str_replace(' ', '-', $dormitory->name) . '.pdf');
    }

    private function allocateToCategory(array &$categories, string $type, float $amt, ?string $gender = null, ?string $customLabel = null): void
    {
        switch ($type) {
            case 'syahriah_pondok':
                if ($gender === 'P') {
                    $categories['syahriah_putri']['amount'] += $amt;
                    $categories['syahriah_putri']['count']++;
                } else {
                    $categories['syahriah_putra']['amount'] += $amt;
                    $categories['syahriah_putra']['count']++;
                }
                break;
            case 'syahriah_madrasah':
                $categories['madrasah']['amount'] += $amt;
                $categories['madrasah']['count']++;
                break;
            case 'kitab':
                $categories['kitab']['amount'] += $amt;
                $categories['kitab']['count']++;
                break;
            case 'majek_pagi':
                $categories['majek_pagi']['amount'] += $amt;
                $categories['majek_pagi']['count']++;
                break;
            case 'majek_sore':
                $categories['majek_sore']['amount'] += $amt;
                $categories['majek_sore']['count']++;
                break;
            case 'kas_komplek':
                $categories['kas_komplek']['amount'] += $amt;
                $categories['kas_komplek']['count']++;
                break;
            default:
                $key = !empty($customLabel) ? \Illuminate\Support\Str::slug($customLabel, '_') : (!empty($type) ? $type : 'lainnya');
                if (!isset($categories[$key])) {
                    $label = $customLabel ?: ucwords(str_replace('_', ' ', $key));
                    $categories[$key] = [
                        'label'  => $label,
                        'desc'   => 'Pos Tagihan ' . $label,
                        'amount' => 0.0,
                        'count'  => 0,
                    ];
                }
                $categories[$key]['amount'] += $amt;
                $categories[$key]['count']++;
                break;
        }
    }
}
