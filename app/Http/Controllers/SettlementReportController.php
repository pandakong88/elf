<?php

namespace App\Http\Controllers;

use App\Modules\Core\Models\Person;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillPayment;
use App\Modules\Keuangan\Models\PaymentTransaction;
use App\Modules\Keuangan\Models\ManualTransferSubmission;
use App\Modules\Keuangan\Models\FundDistribution;
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

        // Breakdown categories: Syahriah Putra, Syahriah Putri, Madrasah, Kitab, Majek Pagi, Majek Sore, Kas Komplek, Pocket Money, Lainnya
        $categories = [
            'syahriah_putra' => ['label' => 'Syahriah / SPP Pondok Putra', 'desc' => 'Operasional pesantren unit putra', 'amount' => 0.0, 'count' => 0],
            'syahriah_putri' => ['label' => 'Syahriah / SPP Pondok Putri', 'desc' => 'Operasional pesantren unit putri', 'amount' => 0.0, 'count' => 0],
            'madrasah'       => ['label' => 'Syahriah Madrasah', 'desc' => 'Operasional unit pendidikan formal/diniyah', 'amount' => 0.0, 'count' => 0],
            'kitab'          => ['label' => 'Biaya Kitab / Buku', 'desc' => 'Pengadaan sarana belajar santri', 'amount' => 0.0, 'count' => 0],
            'majek_pagi'     => ['label' => 'Katering Majek (Pagi)', 'desc' => 'Logistik konsumsi makan pagi', 'amount' => 0.0, 'count' => 0],
            'majek_sore'     => ['label' => 'Katering Majek (Sore)', 'desc' => 'Logistik konsumsi makan sore', 'amount' => 0.0, 'count' => 0],
            'kas_komplek'    => ['label' => 'Kas Komplek / Asrama', 'desc' => 'Dana titipan kebersihan & kegiatan asrama', 'amount' => 0.0, 'count' => 0],
            'pocket_money'   => ['label' => 'Titipan Uang Saku Santri', 'desc' => 'Dana titipan uang saku / jajan santri', 'amount' => 0.0, 'count' => 0],
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
                $netTrx = (float) ($trx->net_amount > 0 ? $trx->net_amount : ((float)$trx->bill_amount + (float)($trx->pocket_money_amount ?? 0)));
                $totalGross += (float) $trx->total_amount;
                $totalMdr   += (float) $trx->mdr_amount;
                $totalNet   += $netTrx;

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

                if ((float)($trx->pocket_money_amount ?? 0) > 0) {
                    $this->allocateToCategory($categories, 'pocket_money', (float)$trx->pocket_money_amount, $person?->gender, 'Titipan Uang Saku Santri');
                }
            }
        }

        // 2. Process Cashier Payments
        if ($source === 'kasir' || $source === 'all') {
            $kasirQuery = BillPayment::where(function ($q) {
                    $q->where('payment_method', 'not like', 'gateway%')
                      ->orWhereNull('payment_method');
                })
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

            // Hitung titipan uang saku dari transfer manual yang disetujui kasir
            $manualSubsWithPocket = ManualTransferSubmission::where('status', 'approved')
                ->where('pocket_money_amount', '>', 0)
                ->where(function ($q) use ($dateFrom, $dateTo, $fromCarbon, $toCarbon) {
                    $q->whereBetween('verified_at', [$fromCarbon, $toCarbon])
                      ->orWhere(function ($oq) use ($fromCarbon, $toCarbon) {
                          $oq->whereNull('verified_at')
                             ->whereBetween('created_at', [$fromCarbon, $toCarbon]);
                      });
                })
                ->when($targetGender, fn($q, $g) => $q->whereHas('person', fn($pq) => $pq->where('gender', $g)))
                ->with('person')
                ->get();

            foreach ($manualSubsWithPocket as $mSub) {
                $amtPm = (float) $mSub->pocket_money_amount;
                $totalGross += $amtPm;
                $totalNet   += $amtPm;
                $this->allocateToCategory($categories, 'pocket_money', $amtPm, $mSub->person?->gender, 'Titipan Uang Saku Santri');
            }
        }

        $sourceLabel = match ($source) {
            'gateway' => '⚡ Khusus Gateway Online',
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
        $appName   = config('app.name', 'Pondok Pesantren Al-Fithroh');
        $periodLabel = Carbon::parse($dateFrom)->locale('id')->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($dateTo)->locale('id')->translatedFormat('d M Y');

        $dormData = $this->getDormitorySlipData($request, $dormitoryId);

        $data = [
            'app_name'     => $appName,
            'dormitory'    => $dormitory,
            'period_label' => $periodLabel,
            'total_amount' => $dormData['total_amount'],
            'santri_list'  => $dormData['santri_list'],
            'generated_at' => now()->locale('id')->translatedFormat('d F Y, H:i') . ' WIB',
            'generated_by' => auth()->user()?->name ?? 'Bendahara Pusat',
        ];

        $pdf = Pdf::loadView('pdf.slip-kas-komplek', $data)->setPaper('a4', 'portrait');

        return $pdf->stream('Slip-Kas-' . \Illuminate\Support\Str::slug($dormitory->name) . '.pdf');
    }

    /**
     * Helper data gathering untuk Slip Kas Komplek
     */
    public function getDormitorySlipData(Request $request, string $dormitoryId): array
    {
        $dateFrom   = $request->query('date_from', now()->startOfMonth()->toDateString());
        $dateTo     = $request->query('date_to', now()->toDateString());
        $source     = $request->query('source', 'all');

        $fromCarbon = Carbon::parse($dateFrom)->startOfDay();
        $toCarbon   = Carbon::parse($dateTo)->endOfDay();

        $santriList = [];
        $totalAmount = 0.0;

        // 1. Gateway
        if ($source === 'gateway' || $source === 'all') {
            $gatewayTrx = PaymentTransaction::where('status', 'success')
                ->where(function ($q) use ($fromCarbon, $toCarbon) {
                    $q->whereBetween('callback_received_at', [$fromCarbon, $toCarbon])
                      ->orWhere(function ($oq) use ($fromCarbon, $toCarbon) {
                          $oq->whereNull('callback_received_at')
                             ->whereBetween('created_at', [$fromCarbon, $toCarbon]);
                      });
                })
                ->whereHas('person.roomAssignments', function ($q) use ($dormitoryId) {
                    $q->active()->whereHas('room', fn($r) => $r->where('dormitory_id', $dormitoryId));
                })
                ->with(['person.roomAssignments' => fn($q) => $q->active()->with('room')])
                ->get();

            foreach ($gatewayTrx as $trx) {
                $person = $trx->person;
                $activeAssignment = $person?->roomAssignments?->first();

                foreach ($trx->bill_breakdown ?? [] as $item) {
                    if (($item['bill_type'] ?? '') === 'kas_komplek') {
                        $amt = (float) ($item['pay_portion'] ?? $item['net_amount'] ?? 0);
                        $totalAmount += $amt;
                        $santriList[] = [
                            'nis'          => $person?->nis ?? '-',
                            'name'         => $person?->name ?? '—',
                            'room_name'    => $activeAssignment?->room?->name ?? '-',
                            'period_label' => $item['period_label'] ?? $item['config_label'] ?? 'Kas Asrama',
                            'paid_date'    => $trx->created_at->locale('id')->translatedFormat('d M Y, H:i'),
                            'method'       => ($trx->channel_label ?? $trx->payment_channel ?? 'Online') . ' (Online Gateway)',
                            'amount'       => $amt,
                        ];
                    }
                }
            }
        }

        // 2. Kasir
        if ($source === 'kasir' || $source === 'all') {
            $kasirPayments = BillPayment::where(function ($q) {
                    $q->where('payment_method', 'not like', 'gateway%')
                      ->orWhereNull('payment_method');
                })
                ->where(function ($q) use ($dateFrom, $dateTo, $fromCarbon, $toCarbon) {
                    $q->whereBetween('payment_date', [$dateFrom, $dateTo])
                      ->orWhereBetween('created_at', [$fromCarbon, $toCarbon]);
                })
                ->whereHas('bill', function ($q) use ($dormitoryId) {
                    $q->where('bill_type', 'kas_komplek')
                        ->whereHas('person.roomAssignments', function ($rq) use ($dormitoryId) {
                            $rq->active()->whereHas('room', fn($r) => $r->where('dormitory_id', $dormitoryId));
                        });
                })
                ->with(['bill.person.roomAssignments' => fn($q) => $q->active()->with('room'), 'bill.config'])
                ->get();

            foreach ($kasirPayments as $pay) {
                $bill = $pay->bill;
                $person = $bill?->person;
                $activeAssignment = $person?->roomAssignments?->first();
                $amt = (float) $pay->amount_paid;

                $totalAmount += $amt;
                $santriList[] = [
                    'nis'          => $person?->nis ?? '-',
                    'name'         => $person?->name ?? '—',
                    'room_name'    => $activeAssignment?->room?->name ?? '-',
                    'period_label' => $bill?->period_formatted ?: ($bill?->config?->label ?: 'Kas Asrama'),
                    'paid_date'    => $pay->payment_date ? Carbon::parse($pay->payment_date)->locale('id')->translatedFormat('d M Y') : '-',
                    'method'       => strtoupper($pay->payment_method ?? 'Kasir'),
                    'amount'       => $amt,
                ];
            }
        }

        return [
            'total_amount' => $totalAmount,
            'santri_list'  => $santriList,
        ];
    }

    /**
     * Download PDF Slip Serah Terima Dana per Pos Kategori (Madrasah, Kitab, Majek, Uang Saku, dll).
     */
    public function downloadSlipKategoriPdf(Request $request, string $categoryKey): Response
    {
        $genderScope = $this->resolveGenderScope();
        $targetGender = $genderScope ?: $request->query('gender', null);

        $catData = $this->getCategorySlipData($request, $categoryKey);
        $meta    = $catData['meta'];

        if ($meta['filter_gender'] && $targetGender && $targetGender !== $meta['filter_gender']) {
            abort(403, 'Akses ditolak untuk unit ini.');
        }

        $dateFrom   = $request->query('date_from', now()->startOfMonth()->toDateString());
        $dateTo     = $request->query('date_to', now()->toDateString());
        $appName    = config('app.name', 'Pondok Pesantren Al-Fithroh');
        $periodLabel = Carbon::parse($dateFrom)->locale('id')->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($dateTo)->locale('id')->translatedFormat('d M Y');

        $data = [
            'app_name'       => $appName,
            'category_key'   => $categoryKey,
            'meta'           => $meta,
            'period_label'   => $periodLabel,
            'total_amount'   => $catData['total_amount'],
            'santri_list'    => $catData['santri_list'],
            'generated_at'   => now()->locale('id')->translatedFormat('d F Y, H:i') . ' WIB',
            'generated_by'   => auth()->user()?->name ?? 'Bendahara Pusat',
        ];

        $pdf = Pdf::loadView('pdf.slip-serah-terima-kategori', $data)->setPaper('a4', 'portrait');

        return $pdf->stream('Slip-Serah-Terima-' . \Illuminate\Support\Str::slug($meta['title']) . '.pdf');
    }

    /**
     * Helper data gathering untuk Slip Kategori
     */
    public function getCategorySlipData(Request $request, string $categoryKey): array
    {
        $genderScope = $this->resolveGenderScope();
        $targetGender = $genderScope ?: $request->query('gender', null);

        $dateFrom   = $request->query('date_from', now()->startOfMonth()->toDateString());
        $dateTo     = $request->query('date_to', now()->toDateString());
        $source     = $request->query('source', 'all');

        $fromCarbon = Carbon::parse($dateFrom)->startOfDay();
        $toCarbon   = Carbon::parse($dateTo)->endOfDay();

        $meta = match ($categoryKey) {
            'syahriah_putra' => [
                'title'          => 'SPP / Syahriah Pondok Putra',
                'recipient_role' => 'Bendahara Pondok Putra',
                'unit_label'     => 'Unit Putra',
                'filter_gender'  => 'L',
                'bill_type'      => 'syahriah_pondok',
            ],
            'syahriah_putri' => [
                'title'          => 'SPP / Syahriah Pondok Putri',
                'recipient_role' => 'Bendahara Pondok Putri',
                'unit_label'     => 'Unit Putri',
                'filter_gender'  => 'P',
                'bill_type'      => 'syahriah_pondok',
            ],
            'madrasah' => [
                'title'          => 'Syahriah / SPP Madrasah',
                'recipient_role' => 'Bendahara Madrasah / Pendidikan',
                'unit_label'     => 'Bagian Pendidikan Madrasah',
                'filter_gender'  => null,
                'bill_type'      => 'syahriah_madrasah',
            ],
            'kitab' => [
                'title'          => 'Biaya Kitab & Sarana Belajar',
                'recipient_role' => 'Pengurus Kitab & Perpustakaan',
                'unit_label'     => 'Bagian Pengadaan Kitab',
                'filter_gender'  => null,
                'bill_type'      => 'kitab',
            ],
            'majek_pagi' => [
                'title'          => 'Katering Majek (Makan Pagi)',
                'recipient_role' => 'Pengurus Dapur & Logistik Santri',
                'unit_label'     => 'Bagian Konsumsi Santri',
                'filter_gender'  => null,
                'bill_type'      => 'majek_pagi',
            ],
            'majek_sore' => [
                'title'          => 'Katering Majek (Makan Sore)',
                'recipient_role' => 'Pengurus Dapur & Logistik Santri',
                'unit_label'     => 'Bagian Konsumsi Santri',
                'filter_gender'  => null,
                'bill_type'      => 'majek_sore',
            ],
            'kas_komplek' => [
                'title'          => 'Kas Komplek / Asrama Santri',
                'recipient_role' => 'Koordinator Pengurus Asrama',
                'unit_label'     => 'Bagian Kepengasuhan & Asrama',
                'filter_gender'  => null,
                'bill_type'      => 'kas_komplek',
            ],
            'pocket_money' => [
                'title'          => 'Titipan Uang Saku Santri',
                'recipient_role' => 'Pengelola Uang Saku / Kantin Santri',
                'unit_label'     => 'Bagian Pengelolaan Uang Saku',
                'filter_gender'  => null,
                'bill_type'      => 'pocket_money',
            ],
            default => [
                'title'          => 'Alokasi ' . ucwords(str_replace('_', ' ', $categoryKey)),
                'recipient_role' => 'Pengurus Terkait',
                'unit_label'     => 'Bagian Terkait',
                'filter_gender'  => null,
                'bill_type'      => $categoryKey,
            ],
        };

        $effectiveGender = $meta['filter_gender'] ?: $targetGender;

        $santriList = [];
        $totalAmount = 0.0;

        // Khusus Titipan Uang Saku
        if ($categoryKey === 'pocket_money') {
            if ($source === 'gateway' || $source === 'all') {
                $gtx = PaymentTransaction::where('status', 'success')
                    ->where('pocket_money_amount', '>', 0)
                    ->where(function ($q) use ($fromCarbon, $toCarbon) {
                        $q->whereBetween('callback_received_at', [$fromCarbon, $toCarbon])
                          ->orWhere(function ($oq) use ($fromCarbon, $toCarbon) {
                              $oq->whereNull('callback_received_at')
                                 ->whereBetween('created_at', [$fromCarbon, $toCarbon]);
                          });
                    })
                    ->when($effectiveGender, fn($q, $g) => $q->whereHas('person', fn($pq) => $pq->where('gender', $g)))
                    ->with(['person.roomAssignments' => fn($q) => $q->active()->with('room.dormitory'), 'person.madrasahEnrollments' => fn($q) => $q->where('is_active', true)->with('kelas')])
                    ->get();

                foreach ($gtx as $trx) {
                    $person = $trx->person;
                    $room = $person?->roomAssignments?->first()?->room?->name ?? '-';
                    $amt = (float) $trx->pocket_money_amount;
                    $totalAmount += $amt;

                    $santriList[] = [
                        'nis'          => $person?->nis ?? '-',
                        'name'         => $person?->name ?? '—',
                        'unit_info'    => $room,
                        'period_label' => 'Titipan Uang Saku',
                        'paid_date'    => $trx->created_at->locale('id')->translatedFormat('d M Y, H:i'),
                        'method'       => ($trx->channel_label ?? $trx->payment_channel ?? 'Online') . ' (Online)',
                        'amount'       => $amt,
                    ];
                }
            }

            if ($source === 'kasir' || $source === 'all') {
                $manualSubs = ManualTransferSubmission::where('status', 'approved')
                    ->where('pocket_money_amount', '>', 0)
                    ->where(function ($q) use ($fromCarbon, $toCarbon) {
                        $q->whereBetween('verified_at', [$fromCarbon, $toCarbon])
                          ->orWhere(function ($oq) use ($fromCarbon, $toCarbon) {
                              $oq->whereNull('verified_at')
                                 ->whereBetween('created_at', [$fromCarbon, $toCarbon]);
                          });
                    })
                    ->when($effectiveGender, fn($q, $g) => $q->whereHas('person', fn($pq) => $pq->where('gender', $g)))
                    ->with(['person.roomAssignments' => fn($q) => $q->active()->with('room.dormitory')])
                    ->get();

                foreach ($manualSubs as $mSub) {
                    $person = $mSub->person;
                    $room = $person?->roomAssignments?->first()?->room?->name ?? '-';
                    $amt = (float) $mSub->pocket_money_amount;
                    $totalAmount += $amt;

                    $santriList[] = [
                        'nis'          => $person?->nis ?? '-',
                        'name'         => $person?->name ?? '—',
                        'unit_info'    => $room,
                        'period_label' => 'Titipan Uang Saku',
                        'paid_date'    => $mSub->verified_at ? Carbon::parse($mSub->verified_at)->locale('id')->translatedFormat('d M Y, H:i') : '-',
                        'method'       => 'Transfer Bank (Manual)',
                        'amount'       => $amt,
                    ];
                }
            }
        } else {
            // Pos Tagihan Normal
            $billType = $meta['bill_type'];

            // 1. Gateway
            if ($source === 'gateway' || $source === 'all') {
                $gtx = PaymentTransaction::where('status', 'success')
                    ->where(function ($q) use ($fromCarbon, $toCarbon) {
                        $q->whereBetween('callback_received_at', [$fromCarbon, $toCarbon])
                          ->orWhere(function ($oq) use ($fromCarbon, $toCarbon) {
                              $oq->whereNull('callback_received_at')
                                 ->whereBetween('created_at', [$fromCarbon, $toCarbon]);
                          });
                    })
                    ->when($effectiveGender, fn($q, $g) => $q->whereHas('person', fn($pq) => $pq->where('gender', $g)))
                    ->with(['person.roomAssignments' => fn($q) => $q->active()->with('room.dormitory')])
                    ->get();

                foreach ($gtx as $trx) {
                    $person = $trx->person;
                    $room = $person?->roomAssignments?->first()?->room?->name ?? '-';

                    foreach ($trx->bill_breakdown ?? [] as $item) {
                        $itemType = $item['bill_type'] ?? '';
                        if ($itemType === $billType || ($billType === 'syahriah_pondok' && in_array($categoryKey, ['syahriah_putra', 'syahriah_putri']))) {
                            if ($categoryKey === 'syahriah_putra' && $person?->gender !== 'L') continue;
                            if ($categoryKey === 'syahriah_putri' && $person?->gender !== 'P') continue;

                            $amt = (float) ($item['pay_portion'] ?? $item['net_amount'] ?? 0);
                            $totalAmount += $amt;

                            $santriList[] = [
                                'nis'          => $person?->nis ?? '-',
                                'name'         => $person?->name ?? '—',
                                'unit_info'    => $room,
                                'period_label' => $item['period_label'] ?? $item['config_label'] ?? $meta['title'],
                                'paid_date'    => $trx->created_at->locale('id')->translatedFormat('d M Y, H:i'),
                                'method'       => ($trx->channel_label ?? $trx->payment_channel ?? 'Online') . ' (Online)',
                                'amount'       => $amt,
                            ];
                        }
                    }
                }
            }

            // 2. Kasir & Transfer Manual
            if ($source === 'kasir' || $source === 'all') {
                $kasirPayments = BillPayment::where(function ($q) {
                        $q->where('payment_method', 'not like', 'gateway%')
                          ->orWhereNull('payment_method');
                    })
                    ->where(function ($q) use ($dateFrom, $dateTo, $fromCarbon, $toCarbon) {
                        $q->whereBetween('payment_date', [$dateFrom, $dateTo])
                          ->orWhereBetween('created_at', [$fromCarbon, $toCarbon]);
                    })
                    ->whereHas('bill', function ($q) use ($billType, $effectiveGender) {
                        $q->where('bill_type', $billType)
                            ->when($effectiveGender, fn($bq, $g) => $bq->whereHas('person', fn($pq) => $pq->where('gender', $g)));
                    })
                    ->with(['bill.person.roomAssignments' => fn($q) => $q->active()->with('room.dormitory'), 'bill.config'])
                    ->get();

                foreach ($kasirPayments as $pay) {
                    $bill = $pay->bill;
                    $person = $bill?->person;
                    $room = $person?->roomAssignments?->first()?->room?->name ?? '-';

                    if ($categoryKey === 'syahriah_putra' && $person?->gender !== 'L') continue;
                    if ($categoryKey === 'syahriah_putri' && $person?->gender !== 'P') continue;

                    $amt = (float) $pay->amount_paid;
                    $totalAmount += $amt;

                    $santriList[] = [
                        'nis'          => $person?->nis ?? '-',
                        'name'         => $person?->name ?? '—',
                        'unit_info'    => $room,
                        'period_label' => $bill?->period_formatted ?: ($bill?->config?->label ?: $meta['title']),
                        'paid_date'    => $pay->payment_date ? Carbon::parse($pay->payment_date)->locale('id')->translatedFormat('d M Y') : '-',
                        'method'       => strtoupper($pay->payment_method ?? 'Kasir'),
                        'amount'       => $amt,
                    ];
                }
            }
        }

        return [
            'meta'         => $meta,
            'total_amount' => $totalAmount,
            'santri_list'  => $santriList,
        ];
    }

    /**
     * Download Batch Slip Serah Terima (Seluruh Pos & Asrama dalam 1 Dokumen PDF).
     */
    public function downloadBatchSlipsPdf(Request $request): Response
    {
        $genderScope = $this->resolveGenderScope();
        $targetGender = $genderScope ?: $request->query('gender', null);

        $dateFrom   = $request->query('date_from', now()->startOfMonth()->toDateString());
        $dateTo     = $request->query('date_to', now()->toDateString());
        $appName    = config('app.name', 'Pondok Pesantren Al-Fithroh');
        $periodLabel = Carbon::parse($dateFrom)->locale('id')->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($dateTo)->locale('id')->translatedFormat('d M Y');

        $categoryKeys = ['syahriah_putra', 'syahriah_putri', 'madrasah', 'kitab', 'majek_pagi', 'majek_sore', 'pocket_money'];
        $slips = [];

        foreach ($categoryKeys as $catKey) {
            $catData = $this->getCategorySlipData($request, $catKey);
            if ($catData['total_amount'] > 0) {
                $slips[] = [
                    'type'         => 'kategori',
                    'meta'         => $catData['meta'],
                    'total_amount' => $catData['total_amount'],
                    'santri_list'  => $catData['santri_list'],
                ];
            }
        }

        $dormitories = Dormitory::active()
            ->when($targetGender, fn($q, $g) => $q->where('gender', $g))
            ->orderByRaw("gender ASC, name ASC")->get();

        foreach ($dormitories as $dorm) {
            $dormData = $this->getDormitorySlipData($request, $dorm->id);
            if ($dormData['total_amount'] > 0) {
                $slips[] = [
                    'type'         => 'komplek',
                    'meta'         => [
                        'title'          => 'Kas Komplek ' . $dorm->name,
                        'recipient_role' => 'Bendahara ' . $dorm->name,
                        'unit_label'     => 'Asrama ' . ($dorm->gender === 'P' ? 'Putri' : 'Putra'),
                    ],
                    'dormitory'    => $dorm,
                    'total_amount' => $dormData['total_amount'],
                    'santri_list'  => $dormData['santri_list'],
                ];
            }
        }

        $data = [
            'app_name'     => $appName,
            'period_label' => $periodLabel,
            'slips'        => $slips,
            'generated_at' => now()->locale('id')->translatedFormat('d F Y, H:i') . ' WIB',
            'generated_by' => auth()->user()?->name ?? 'Bendahara Pusat',
        ];

        $pdf = Pdf::loadView('pdf.slip-batch-all', $data)->setPaper('a4', 'portrait');

        return $pdf->stream('Batch-Slip-Serah-Terima-' . Carbon::parse($dateFrom)->format('Ymd') . '.pdf');
    }

    /**
     * Download PDF Berita Acara Rekonsiliasi & Tutup Buku Kas dari Snapshot.
     */
    public function downloadSnapshotPdf(Request $request, string $id): Response
    {
        $genderScope = $this->resolveGenderScope();
        $snapshot = FundDistribution::with('distributor')->findOrFail($id);

        if ($genderScope && $snapshot->gender !== 'A' && $snapshot->gender !== $genderScope) {
            abort(403, 'Akses ditolak untuk arsip rekonsiliasi unit ini.');
        }

        $appName = config('app.name', 'Pondok Pesantren Al-Fithroh');
        $periodLabel = $snapshot->period_from->locale('id')->translatedFormat('d M Y') . ' s/d ' . $snapshot->period_to->locale('id')->translatedFormat('d M Y');

        $data = [
            'app_name'     => $appName,
            'snapshot'     => $snapshot,
            'period_label' => $periodLabel,
            'breakdown'    => $snapshot->breakdown ?? [],
            'generated_at' => now()->locale('id')->translatedFormat('d F Y, H:i') . ' WIB',
            'generated_by' => auth()->user()?->name ?? 'Bendahara Pusat',
        ];

        $pdf = Pdf::loadView('pdf.berita-acara-settlement', $data)->setPaper('a4', 'portrait');

        return $pdf->stream('Berita-Acara-Rekonsiliasi-Kas-' . $snapshot->period_from->format('Ymd') . '.pdf');
    }

    private function allocateToCategory(array &$categories, string $type, float $amt, ?string $gender = null, ?string $customLabel = null): void
    {
        switch ($type) {
            case 'pocket_money':
                $categories['pocket_money']['amount'] += $amt;
                $categories['pocket_money']['count']++;
                break;
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
