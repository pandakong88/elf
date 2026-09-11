<?php

namespace App\Http\Controllers;

use App\Modules\Keuangan\Models\BillPayment;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\PaymentTransaction;
use App\Modules\Keuangan\Models\ManualTransferSubmission;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BuktiBayarController extends Controller
{
    /**
     * Stream / display uploaded manual transfer proof image directly from storage.
     * Works without symlink or exec requirements on shared hosting.
     */
    public function viewProofImage(string $id)
    {
        $submission = ManualTransferSubmission::findOrFail($id);

        if (empty($submission->proof_image_path)) {
            abort(404, 'Bukti transfer tidak ditemukan.');
        }

        $fullPath = storage_path('app/public/' . $submission->proof_image_path);

        if (!file_exists($fullPath)) {
            $altJpg  = preg_replace('/\.(webp|png)$/i', '.jpg', $fullPath);
            $altWebp = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $fullPath);
            $altPng  = preg_replace('/\.(jpg|jpeg|webp)$/i', '.png', $fullPath);

            if (file_exists($altJpg)) {
                $fullPath = $altJpg;
            } elseif (file_exists($altWebp)) {
                $fullPath = $altWebp;
            } elseif (file_exists($altPng)) {
                $fullPath = $altPng;
            } else {
                abort(404, 'File gambar bukti tidak ditemukan di server.');
            }
        }

        return response()->file($fullPath, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
    /**
     * Helper privat untuk mengekstrak dan memformat data transaksi gateway (Duitku/DOKU).
     */
    private function getGatewayData(string $trxId): array
    {
        $trx = PaymentTransaction::with([
            'person.activeMadrasahEnrollment.kelas',
            'person.activeRoomAssignment.room.dormitory',
        ])
        ->where('id', $trxId)
        ->orWhere('merchant_order_id', $trxId)
        ->orWhere('duitku_reference', $trxId)
        ->firstOrFail();

        // Hanya bisa cetak bukti transaksi sukses
        if ($trx->status !== 'success') {
            abort(400, 'Bukti hanya tersedia untuk transaksi yang berhasil.');
        }

        $santri = $trx->person;
        $santriPersonId = $santri?->id;

        // Enrich breakdown dengan label human-readable jika record lama belum punya
        $months   = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                     7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        $breakdown = collect($trx->bill_breakdown ?? [])->map(function ($item) use ($months) {
            if (!empty($item['config_label']) && !empty($item['period_label'])) {
                return [
                    'config_label' => $item['config_label'],
                    'period_label' => $item['period_label'],
                    'amount'       => (float)($item['pay_portion'] ?? ($item['amount'] ?? 0)),
                    'is_partial'   => !empty($item['is_partial']),
                ];
            }
            $bill     = Bill::with('config')->find($item['bill_id'] ?? null);
            $interval = $bill?->config?->interval ?? '';
            if ($interval === 'semester') {
                $period = 'Semester ' . ($bill->period_month) . '/' . ($bill->period_year);
            } elseif (in_array($interval, ['once', 'insidental', 'event', 'sekali'])) {
                $period = 'Event ' . ($bill?->period_year ?? '');
            } else {
                $period = ($months[$bill?->period_month ?? 0] ?? '') . ' ' . ($bill?->period_year ?? '');
            }
            return [
                'config_label' => $bill?->config?->label ?? ucwords(str_replace('_', ' ', $item['bill_type'] ?? '')),
                'period_label' => trim($period),
                'amount'       => (float)($item['pay_portion'] ?? ($item['amount'] ?? 0)),
                'is_partial'   => !empty($item['is_partial']),
            ];
        })->all();

        // Deteksi info kelas & komplek santri
        $kelasName = $santri?->activeMadrasahEnrollment?->kelas?->name ?? '—';
        $dormName  = $santri?->activeRoomAssignment?->room?->dormitory?->name ?? '—';
        $roomName  = $santri?->activeRoomAssignment?->room?->name ?? '';
        $dormFull  = $roomName ? ($dormName . ' - ' . $roomName) : $dormName;

        // Dynamic Back URL & Back Label
        $user = Auth::user();
        $fromParam = request()->query('from', '');
        $referer = request()->headers->get('referer', '');

        if ($fromParam === 'portal-wali' || str_contains($referer, '/portal-wali')) {
            $backUrl = $santriPersonId ? route('portal-wali.dashboard', $santriPersonId) : route('portal-wali.search');
            $backLabel = 'Kembali ke Data Santri';
        } elseif ($fromParam === 'payments_log') {
            $backUrl = route('keuangan.billing', ['tab' => 'payments_log']);
            $backLabel = 'Kembali ke Riwayat Pembayaran';
        } elseif (!$user || ($user && $user->hasRole('wali-santri'))) {
            $backUrl = $santriPersonId ? route('portal-wali.dashboard', $santriPersonId) : route('portal-wali.search');
            $backLabel = 'Kembali ke Data Santri';
        } elseif ($user && $user->hasAnyRole(['super-admin', 'pengasuh', 'manajemen', 'bendahara-pondok', 'bendahara-putra', 'bendahara-putri', 'bendahara-madin', 'bendahara-unit', 'admin-data'])) {
            $backUrl = route('keuangan.billing', ['tab' => 'payments_log']);
            $backLabel = 'Kembali ke Riwayat Pembayaran';
        } else {
            $backUrl = $santriPersonId ? route('portal-wali.dashboard', $santriPersonId) : route('portal-wali.search');
            $backLabel = $santriPersonId ? 'Kembali ke Data Santri' : 'Kembali ke Riwayat Pembayaran';
        }

        $paymentDate = $trx->created_at ? $trx->created_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y');
        $paymentTime = $trx->created_at ? $trx->created_at->format('H:i') . ' WIB' : '';
        $methodLabel = ($trx->channel_label ?? $trx->payment_channel ?? 'Online') . ' (' . (strtoupper($trx->gateway_provider ?: 'Payment Gateway')) . ')';

        $totalAmount = (float) $trx->total_amount;
        $billAmount  = (float) $trx->bill_amount;
        $mdrAmount   = (float) $trx->mdr_amount;

        $reference = $trx->duitku_reference ?: ($trx->gateway_provider ? strtoupper($trx->gateway_provider) : '—');

        return [
            'type'              => 'gateway',
            'app_name'          => config('app.name', 'Elvith'),
            'trx_id'            => $trx->id,
            'receipt_no'        => $trx->merchant_order_id,
            'no_bukti'          => $trx->merchant_order_id,
            'duitku_reference'  => $reference,
            'santri_id'         => $santriPersonId,
            'santri_name'       => $santri?->name ?? '—',
            'santri_gender'     => $santri?->gender === 'P' ? 'Putri' : 'Putra',
            'kelas_name'        => $kelasName,
            'dorm_name'         => $dormFull,
            'cashier_name'      => 'Sistem Otomatis (Payment Gateway)',
            'payment_method'    => $methodLabel,
            'payment_date'      => $paymentDate,
            'payment_time'      => $paymentTime,
            'breakdown'         => $breakdown,
            'bill_amount'       => $billAmount,
            'mdr_amount'        => $mdrAmount,
            'total_amount'      => $totalAmount,
            'tendered_amount'   => $totalAmount,
            'change_amount'     => 0.0,
            'terbilang'         => $this->terbilang($totalAmount),
            'notes'             => 'Ref Gateway: ' . ($reference !== '—' ? $reference : $trx->merchant_order_id),
            'generated_at'      => now()->translatedFormat('d F Y, H:i') . ' WIB',
            'back_url'          => $backUrl,
            'back_label'        => $backLabel,
            'pdf_download_url'  => route('bukti-bayar.gateway.pdf', $trx->id),
        ];
    }

    /**
     * Halaman Web Preview Bukti Bayar Gateway (On-Screen View).
     */
    public function gateway(string $trxId)
    {
        $data = $this->getGatewayData($trxId);
        return view('keuangan.kuitansi-preview', $data);
    }

    /**
     * Unduh file PDF Bukti Bayar Gateway secara langsung.
     */
    public function gatewayPdf(string $trxId): Response
    {
        $data = $this->getGatewayData($trxId);

        $pdf = Pdf::loadView('pdf.kuitansi-kasir', $data)
                  ->setPaper('a4', 'portrait');

        $filename = 'Bukti-Bayar-' . $data['receipt_no'] . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Helper privat untuk mengekstrak dan memformat data kuitansi kasir.
     */
    private function getKuitansiData(string $identifier): array
    {
        $payments = BillPayment::with([
            'bill.config',
            'bill.person.activeMadrasahEnrollment.kelas',
            'bill.person.activeRoomAssignment.room.dormitory',
            'logger'
        ])
        ->where('receipt_no', $identifier)
        ->orWhere('payment_group_id', $identifier)
        ->orWhere('id', $identifier)
        ->get();

        if ($payments->isEmpty()) {
            abort(404, 'Data kuitansi / pembayaran tidak ditemukan.');
        }

        $firstPayment = $payments->first();
        $bill = $firstPayment->bill;
        $santri = $bill?->person;

        $months = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                   7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];

        $breakdown = [];
        $totalAmount = 0.0;

        foreach ($payments as $p) {
            $b = $p->bill;
            $interval = $b?->config?->interval ?? '';
            $period = match(true) {
                $interval === 'semester'                                       => 'Semester ' . $b->period_month . '/' . $b->period_year,
                in_array($interval, ['once', 'insidental', 'event', 'sekali']) => 'Event ' . ($b->period_year ?? ''),
                default                                                        => ($months[$b?->period_month ?? 0] ?? '') . ' ' . ($b?->period_year ?? ''),
            };

            $amountPaid = (float) $p->amount_paid;
            $totalAmount += $amountPaid;

            $breakdown[] = [
                'payment_id'   => $p->id,
                'config_label' => $b?->config?->label ?? ucwords(str_replace('_', ' ', $b?->bill_type ?? '')),
                'period_label' => trim($period),
                'amount'       => $amountPaid,
                'is_partial'   => $amountPaid < (float) ($b?->amount ?? 0),
                'notes'        => $p->notes,
            ];
        }

        $receiptNo = $firstPayment->receipt_no ?: ('KSR-' . substr($firstPayment->id, 0, 8));
        $paymentDate = $firstPayment->payment_date
            ? $firstPayment->payment_date->translatedFormat('d F Y')
            : now()->translatedFormat('d F Y');

        $paymentTime = $firstPayment->created_at ? $firstPayment->created_at->format('H:i') . ' WIB' : '';

        // Deteksi info kelas & komplek santri
        $kelasName = $santri?->activeMadrasahEnrollment?->kelas?->name ?? '—';
        $dormName  = $santri?->activeRoomAssignment?->room?->dormitory?->name ?? '—';
        $roomName  = $santri?->activeRoomAssignment?->room?->name ?? '';
        $dormFull  = $roomName ? ($dormName . ' - ' . $roomName) : $dormName;

        $santriPersonId = $santri?->id;

        // Dynamic Back URL & Back Label
        $user = Auth::user();
        $fromParam = request()->query('from', '');
        $referer = request()->headers->get('referer', '');

        // 1. Explicitly came from portal wali or referer is portal-wali
        if ($fromParam === 'portal-wali' || str_contains($referer, '/portal-wali')) {
            $backUrl = $santriPersonId ? route('portal-wali.dashboard', $santriPersonId) : route('portal-wali.search');
            $backLabel = 'Kembali ke Data Santri';
        }
        // 2. Explicitly came from payments_log
        elseif ($fromParam === 'payments_log') {
            $backUrl = route('keuangan.billing', ['tab' => 'payments_log']);
            $backLabel = 'Kembali ke Riwayat Pembayaran';
        }
        // 3. Wali santri user or unauthenticated public visitor viewing santri
        elseif (!$user || ($user && $user->hasRole('wali-santri'))) {
            $backUrl = $santriPersonId ? route('portal-wali.dashboard', $santriPersonId) : route('portal-wali.search');
            $backLabel = 'Kembali ke Data Santri';
        }
        // 4. Financial/Admin/Staff user
        elseif ($user && $user->hasAnyRole(['super-admin', 'pengasuh', 'manajemen', 'bendahara-pondok', 'bendahara-putra', 'bendahara-putri', 'bendahara-madin', 'bendahara-unit', 'admin-data'])) {
            $backUrl = route('keuangan.billing', ['tab' => 'payments_log']);
            $backLabel = 'Kembali ke Riwayat Pembayaran';
        }
        // 5. Default fallback
        else {
            $backUrl = $santriPersonId ? route('portal-wali.dashboard', $santriPersonId) : route('keuangan.billing', ['tab' => 'payments_log']);
            $backLabel = $santriPersonId ? 'Kembali ke Data Santri' : 'Kembali ke Riwayat Pembayaran';
        }

        $tendered = $firstPayment->tendered_amount ? (float) $firstPayment->tendered_amount : $totalAmount;
        $change   = $firstPayment->change_amount ? (float) $firstPayment->change_amount : 0.0;

        return [
            'type'            => 'kuitansi_kasir',
            'app_name'        => config('app.name', 'Elvith'),
            'receipt_no'      => $receiptNo,
            'payment_date'    => $paymentDate,
            'payment_time'    => $paymentTime,
            'santri_id'       => $santriPersonId,
            'santri_name'     => $santri?->name ?? '—',
            'santri_gender'   => $santri?->gender === 'P' ? 'Putri' : 'Putra',
            'kelas_name'      => $kelasName,
            'dorm_name'       => $dormFull,
            'cashier_name'    => $firstPayment->logger?->name ?? 'Kasir Pondok',
            'payment_method'  => match (strtolower($firstPayment->payment_method ?? '')) {
                'cash'            => 'Tunai',
                'transfer'        => 'Transfer Bank',
                'gateway_duitku'  => 'Pembayaran Online (Gateway)',
                default           => strtoupper($firstPayment->payment_method ?? '—'),
            },
            'breakdown'       => $breakdown,
            'total_amount'    => $totalAmount,
            'tendered_amount' => $tendered,
            'change_amount'   => $change,
            'terbilang'       => $this->terbilang($totalAmount),
            'notes'           => $firstPayment->notes,
            'generated_at'    => now()->translatedFormat('d F Y, H:i') . ' WIB',
            'back_url'        => $backUrl,
            'back_label'      => $backLabel,
        ];
    }

    /**
     * Halaman Web Preview Kuitansi Kasir (On-Screen View).
     */
    public function kuitansi(string $identifier)
    {
        $data = $this->getKuitansiData($identifier);
        return view('keuangan.kuitansi-preview', $data);
    }

    /**
     * Unduh file PDF Kuitansi Kasir secara langsung.
     */
    public function kuitansiPdf(string $identifier): Response
    {
        $data = $this->getKuitansiData($identifier);

        $pdf = Pdf::loadView('pdf.kuitansi-kasir', $data)
                  ->setPaper('a4', 'portrait');

        $filename = 'Kuitansi-' . $data['receipt_no'] . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Generate / Preview bukti bayar untuk pembayaran kasir legacy.
     */
    public function kasir(string $paymentId)
    {
        return $this->kuitansi($paymentId);
    }

    private function penyebut(float $nilai): string
    {
        $nilai = abs((int)$nilai);
        $huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->penyebut($nilai - 10) . " Belas";
        } else if ($nilai < 100) {
            $temp = $this->penyebut((int)($nilai / 10)) . " Puluh" . $this->penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " Seratus" . $this->penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->penyebut((int)($nilai / 100)) . " Ratus" . $this->penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " Seribu" . $this->penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->penyebut((int)($nilai / 1000)) . " Ribu" . $this->penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->penyebut((int)($nilai / 1000000)) . " Juta" . $this->penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->penyebut((int)($nilai / 1000000000)) . " Milyar" . $this->penyebut(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = $this->penyebut((int)($nilai / 1000000000000)) . " Trilyun" . $this->penyebut(fmod($nilai, 1000000000000));
        }
        return $temp;
    }

    public function terbilang(float $nilai): string
    {
        if ($nilai < 0) {
            $hasil = "Minus " . trim($this->penyebut($nilai));
        } else {
            $hasil = trim($this->penyebut($nilai));
        }
        return $hasil . ($hasil ? " Rupiah" : "Nol Rupiah");
    }
}
