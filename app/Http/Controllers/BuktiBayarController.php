<?php

namespace App\Http\Controllers;

use App\Modules\Keuangan\Models\BillPayment;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\PaymentTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BuktiBayarController extends Controller
{
    /**
     * Generate PDF bukti bayar untuk transaksi gateway (Duitku).
     * Wali hanya boleh akses miliknya. Admin/Bendahara bebas.
     */
    public function gateway(string $trxId): Response
    {
        $trx = PaymentTransaction::with('person')->findOrFail($trxId);

        // Auth check — wali hanya boleh lihat miliknya
        $user = Auth::user();
        if ($user->hasRole(['wali'])) {
            $waliPersonId = $user->person?->id ?? null;
            if (!$waliPersonId || $trx->person_id !== $waliPersonId) {
                abort(403, 'Akses ditolak.');
            }
        }

        // Hanya bisa cetak bukti transaksi sukses
        if ($trx->status !== 'success') {
            abort(400, 'Bukti hanya tersedia untuk transaksi yang berhasil.');
        }

        // Enrich breakdown dengan label human-readable jika record lama belum punya
        $months   = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                     7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        $breakdown = collect($trx->bill_breakdown ?? [])->map(function ($item) use ($months) {
            if (!empty($item['config_label']) && !empty($item['period_label'])) {
                return $item;
            }
            $bill     = Bill::with('config')->find($item['bill_id']);
            $interval = $bill?->config?->interval ?? '';
            if ($interval === 'semester') {
                $period = 'Semester ' . ($bill->period_month) . '/' . ($bill->period_year);
            } elseif (in_array($interval, ['once', 'insidental', 'event', 'sekali'])) {
                $period = 'Event ' . ($bill?->period_year ?? '');
            } else {
                $period = ($months[$bill?->period_month ?? 0] ?? '') . ' ' . ($bill?->period_year ?? '');
            }
            return array_merge($item, [
                'config_label' => $bill?->config?->label ?? ucwords(str_replace('_', ' ', $item['bill_type'] ?? '')),
                'period_label' => trim($period),
            ]);
        })->all();

        $data = [
            'type'              => 'gateway',
            'app_name'          => config('app.name', 'Elvith'),
            'no_bukti'          => $trx->merchant_order_id,
            'duitku_reference'  => $trx->duitku_reference ?? '—',
            'santri_name'       => $trx->person?->name ?? '—',
            'payment_method'    => ($trx->channel_label ?? $trx->payment_channel ?? '—') . ' (Online)',
            'payment_date'      => $trx->created_at->translatedFormat('d F Y, H:i') . ' WIB',
            'breakdown'         => $breakdown,
            'bill_amount'       => (float) $trx->bill_amount,
            'mdr_amount'        => (float) $trx->mdr_amount,
            'total_amount'      => (float) $trx->total_amount,
            'generated_at'      => now()->translatedFormat('d F Y, H:i') . ' WIB',
        ];

        $pdf = Pdf::loadView('pdf.bukti-pembayaran', $data)
                  ->setPaper('a4', 'portrait');

        $filename = 'Bukti-Bayar-' . $trx->merchant_order_id . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Generate PDF bukti bayar untuk pembayaran kasir (BillPayment manual).
     */
    public function kasir(string $paymentId): Response
    {
        $payment = BillPayment::with(['bill.config', 'bill.person', 'logger'])->findOrFail($paymentId);

        $bill = $payment->bill;

        $months = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                   7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        $interval = $bill?->config?->interval ?? '';
        if ($interval === 'semester') {
            $period = 'Semester ' . $bill->period_month . '/' . $bill->period_year;
        } elseif (in_array($interval, ['once', 'insidental', 'event', 'sekali'])) {
            $period = 'Event ' . ($bill->period_year ?? '');
        } else {
            $period = ($months[$bill?->period_month ?? 0] ?? '') . ' ' . ($bill?->period_year ?? '');
        }

        $data = [
            'type'           => 'kasir',
            'app_name'       => config('app.name', 'Elvith'),
            'no_bukti'       => $payment->id,
            'santri_name'    => $bill?->person?->name ?? '—',
            'payment_method' => match (strtolower($payment->payment_method ?? '')) {
                'cash'            => 'Tunai',
                'transfer'        => 'Transfer Bank',
                'gateway_duitku'  => 'Duitku (Gateway)',
                default           => strtoupper($payment->payment_method ?? '—'),
            },
            'payment_date'   => $payment->payment_date
                                    ? $payment->payment_date->translatedFormat('d F Y')
                                    : '—',
            'breakdown'      => [[
                'config_label' => $bill?->config?->label ?? ucwords(str_replace('_', ' ', $bill?->bill_type ?? '')),
                'period_label' => trim($period),
                'pay_portion'  => (float) $payment->amount_paid,
                'is_partial'   => (float) $payment->amount_paid < (float) ($bill?->amount ?? 0),
            ]],
            'bill_amount'    => (float) $payment->amount_paid,
            'mdr_amount'     => 0.0,
            'total_amount'   => (float) $payment->amount_paid,
            'logged_by'      => $payment->logger?->name ?? 'Sistem',
            'notes'          => $payment->notes,
            'generated_at'   => now()->translatedFormat('d F Y, H:i') . ' WIB',
        ];

        $pdf = Pdf::loadView('pdf.bukti-pembayaran', $data)
                  ->setPaper('a4', 'portrait');

        $filename = 'Bukti-Kasir-' . substr($payment->id, 0, 8) . '.pdf';
        return $pdf->download($filename);
    }
}
