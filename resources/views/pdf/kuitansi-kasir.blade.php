<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Kuitansi Pembayaran - {{ $receipt_no }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 10.5px;
            color: #1e293b;
            background: #ffffff;
            padding: 0;
            line-height: 1.4;
        }

        .page {
            padding: 24px 30px;
            max-width: 794px;
            margin: 0 auto;
        }

        /* ── HEADER ─────────────────────────────────── */
        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
        }
        .institution-name {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .institution-sub {
            font-size: 9.5px;
            color: #64748b;
            margin-top: 2px;
        }
        .doc-badge {
            text-align: right;
        }
        .doc-badge .title {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .doc-badge .receipt-num {
            font-size: 11px;
            font-weight: bold;
            color: #059669;
            margin-top: 2px;
            font-family: "Courier New", Courier, monospace;
        }

        /* ── STATUS BANNER ─────────────────────────── */
        .status-banner {
            background: #f0fdf4;
            border: 1.5px solid #86efac;
            border-radius: 6px;
            padding: 8px 14px;
            margin-bottom: 14px;
        }
        .status-banner-table {
            width: 100%;
            border-collapse: collapse;
        }
        .status-label {
            font-size: 11px;
            font-weight: bold;
            color: #166534;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-sub {
            font-size: 9px;
            color: #15803d;
            text-align: right;
        }

        /* ── INFO GRID ──────────────────────────────── */
        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 14px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 2.5px 4px;
            vertical-align: top;
            font-size: 10px;
        }
        .info-table .key {
            width: 18%;
            color: #64748b;
        }
        .info-table .sep {
            width: 2%;
            color: #94a3b8;
            text-align: center;
        }
        .info-table .val {
            width: 30%;
            font-weight: bold;
            color: #1e293b;
        }

        /* ── BREAKDOWN TABLE ─────────────────────────── */
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .breakdown-table thead tr th {
            background: #0f172a;
            color: #ffffff;
            padding: 7px 10px;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #0f172a;
        }
        .breakdown-table thead tr th.text-left { text-align: left; }
        .breakdown-table thead tr th.text-center { text-align: center; }
        .breakdown-table thead tr th.text-right { text-align: right; }

        .breakdown-table tbody tr td {
            padding: 7px 10px;
            font-size: 10px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .breakdown-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .breakdown-table tfoot tr td {
            padding: 6px 10px;
            border: 1px solid #cbd5e1;
            font-size: 10px;
        }

        .total-row {
            background: #f1f5f9;
            font-weight: bold;
        }
        .total-highlight {
            background: #ecfdf5 !important;
            font-weight: bold;
            color: #065f46;
            font-size: 11px !important;
        }

        /* ── TERBILANG BOX ──────────────────────────── */
        .terbilang-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 16px;
            font-size: 9.5px;
        }
        .terbilang-label {
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            font-size: 8.5px;
            margin-bottom: 2px;
        }
        .terbilang-text {
            font-style: italic;
            color: #0f172a;
            font-weight: bold;
        }

        /* ── SIGNATURES ─────────────────────────────── */
        .sig-section {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }
        .sig-section td {
            width: 50%;
            vertical-align: top;
            text-align: center;
        }
        .sig-title {
            font-size: 9.5px;
            color: #64748b;
            margin-bottom: 50px;
        }
        .sig-name {
            font-size: 10.5px;
            font-weight: bold;
            color: #0f172a;
            border-bottom: 1px solid #94a3b8;
            display: inline-block;
            padding-bottom: 2px;
            min-width: 160px;
        }

        /* ── FOOTER ─────────────────────────────────── */
        .footer {
            margin-top: 25px;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- HEADER --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <div class="institution-name">{{ config('app.name', 'PONDOK PESANTREN') }}</div>
                    <div class="institution-sub">Sistem Informasi Manajemen Keuangan Santri</div>
                </td>
                <td class="doc-badge">
                    <div class="title">Kuitansi Pembayaran Kasir</div>
                    <div class="receipt-num">{{ $receipt_no }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- STATUS BANNER --}}
    <div class="status-banner">
        <table class="status-banner-table">
            <tr>
                <td class="status-label">✓ Pembayaran Berhasil (Lunas)</td>
                <td class="status-sub">Dicetak pada: {{ $generated_at }}</td>
            </tr>
        </table>
    </div>

    {{-- INFO GRID DUA KOLOM --}}
    <div class="info-card">
        <table class="info-table">
            <tr>
                <td class="key">Nama Santri</td>
                <td class="sep">:</td>
                <td class="val">{{ $santri_name }} ({{ $santri_gender }})</td>

                <td class="key">No. Kuitansi</td>
                <td class="sep">:</td>
                <td class="val">{{ $receipt_no }}</td>
            </tr>
            <tr>
                <td class="key">Kelas / Madrasah</td>
                <td class="sep">:</td>
                <td class="val">{{ $kelas_name }}</td>

                <td class="key">Tanggal Bayar</td>
                <td class="sep">:</td>
                <td class="val">{{ $payment_date }} {{ $payment_time }}</td>
            </tr>
            <tr>
                <td class="key">Komplek / Asrama</td>
                <td class="sep">:</td>
                <td class="val">{{ $dorm_name }}</td>

                <td class="key">Metode Bayar</td>
                <td class="sep">:</td>
                <td class="val">{{ $payment_method }}</td>
            </tr>
            <tr>
                <td class="key">Petugas Kasir</td>
                <td class="sep">:</td>
                <td class="val">{{ $cashier_name }}</td>

                <td class="key">Catatan</td>
                <td class="sep">:</td>
                <td class="val">{{ $notes ?: '—' }}</td>
            </tr>
        </table>
    </div>

    {{-- RINCIAN TAGIHAN (MULTI-ITEM TABLE) --}}
    <table class="breakdown-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 6%;">No</th>
                <th class="text-left" style="width: 44%;">Nama Iuran / Tagihan</th>
                <th class="text-center" style="width: 25%;">Periode Tagihan</th>
                <th class="text-right" style="width: 25%;">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($breakdown as $idx => $item)
                <tr>
                    <td class="text-center" style="color: #64748b;">{{ $idx + 1 }}</td>
                    <td>
                        <span style="font-weight: bold; color: #0f172a;">{{ $item['config_label'] }}</span>
                        @if(!empty($item['is_partial']))
                            <span style="font-size: 8px; color: #d97706; margin-left: 4px;">(Sebagian)</span>
                        @endif
                    </td>
                    <td class="text-center" style="color: #475569;">{{ $item['period_label'] }}</td>
                    <td class="text-right" style="font-weight: bold; font-family: 'Courier New', Courier, monospace;">
                        Rp {{ number_format($item['amount'], 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row total-highlight">
                <td colspan="3" class="text-right" style="text-transform: uppercase; letter-spacing: 0.5px;">TOTAL PEMBAYARAN</td>
                <td class="text-right" style="font-family: 'Courier New', Courier, monospace; font-size: 12px;">
                    Rp {{ number_format($total_amount, 0, ',', '.') }}
                </td>
            </tr>
            @if($payment_method === 'Tunai' && $tendered_amount > $total_amount)
                <tr class="total-row">
                    <td colspan="3" class="text-right" style="color: #64748b;">Uang Diterima</td>
                    <td class="text-right" style="font-family: 'Courier New', Courier, monospace;">
                        Rp {{ number_format($tendered_amount, 0, ',', '.') }}
                    </td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" class="text-right" style="color: #64748b;">Kembalian</td>
                    <td class="text-right" style="font-family: 'Courier New', Courier, monospace; color: #059669;">
                        Rp {{ number_format($change_amount, 0, ',', '.') }}
                    </td>
                </tr>
            @endif
        </tfoot>
    </table>

    {{-- TERBILANG BOX --}}
    <div class="terbilang-box">
        <div class="terbilang-label">Terbilang:</div>
        <div class="terbilang-text"># {{ $terbilang }} #</div>
    </div>

    {{-- TANDA TANGAN --}}
    <table class="sig-section">
        <tr>
            <td>
                <div class="sig-title">Wali Santri / Penyetor,</div>
                <div class="sig-name">( ........................................ )</div>
            </td>
            <td>
                <div class="sig-title">Kasir / Bendahara Pondok,</div>
                <div class="sig-name">{{ $cashier_name }}</div>
            </td>
        </tr>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        Dokumen ini merupakan bukti pembayaran resmi yang sah dan diterbitkan secara elektronik oleh sistem {{ config('app.name', 'Elvith') }}.
    </div>

</div>
</body>
</html>
