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
            font-size: 10px;
            color: #0f172a;
            background: #ffffff;
            padding: 0;
            line-height: 1.4;
        }

        .page {
            padding: 24px 28px;
            max-width: 794px;
            margin: 0 auto;
        }

        /* ── HEADER ─────────────────────────────────── */
        .header {
            border-bottom: 2px dashed #cbd5e1;
            padding-bottom: 14px;
            margin-bottom: 16px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo-cell {
            width: 44px;
        }
        .logo-box {
            width: 36px;
            height: 36px;
            background: #10b981;
            border-radius: 8px;
            text-align: center;
            color: #ffffff;
            font-size: 20px;
            font-weight: bold;
            line-height: 36px;
        }
        .institution-name {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.2px;
        }
        .institution-sub {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }
        .institution-sub .elvith-brand {
            color: #059669;
            font-weight: bold;
        }

        .doc-badge {
            text-align: right;
        }
        .doc-badge .title-tag {
            font-size: 9px;
            font-weight: bold;
            color: #0f172a;
            background: #f1f5f9;
            padding: 3px 8px;
            border-radius: 4px;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 3px;
        }
        .doc-badge .receipt-num {
            font-size: 12px;
            font-weight: bold;
            color: #059669;
            font-family: "Courier New", Courier, monospace;
        }

        /* ── STATUS STRIP ──────────────────────────── */
        .status-strip {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 7px 12px;
            margin-bottom: 14px;
        }
        .status-table {
            width: 100%;
            border-collapse: collapse;
        }
        .status-label {
            font-size: 10.5px;
            font-weight: bold;
            color: #166534;
            letter-spacing: 0.3px;
        }
        .status-sub {
            font-size: 8.5px;
            color: #15803d;
            text-align: right;
        }

        /* ── BENTO INFO GRID (TABLE) ────────────────── */
        .info-grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .info-grid-table > tbody > tr > td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }
        .info-card-inner {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 10px;
        }
        .info-card-left {
            margin-right: 4px;
        }
        .info-card-right {
            margin-left: 4px;
        }

        .nested-info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .nested-info-table td {
            padding: 2.5px 0;
            font-size: 9.5px;
            vertical-align: top;
        }
        .nested-info-table .n-key {
            width: 40%;
            color: #64748b;
        }
        .nested-info-table .n-sep {
            width: 5%;
            color: #94a3b8;
            text-align: center;
        }
        .nested-info-table .n-val {
            width: 55%;
            font-weight: bold;
            color: #0f172a;
        }

        /* ── BREAKDOWN TABLE ─────────────────────────── */
        .table-container {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-bottom: 14px;
            overflow: hidden;
        }
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
        }
        .breakdown-table thead tr th {
            background: #f8fafc;
            color: #475569;
            padding: 8px 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
        }
        .breakdown-table thead tr th.text-left { text-align: left; }
        .breakdown-table thead tr th.text-center { text-align: center; }
        .breakdown-table thead tr th.text-right { text-align: right; }

        .breakdown-table tbody tr td {
            padding: 8px 10px;
            font-size: 9.5px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .breakdown-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .breakdown-table tfoot tr td {
            padding: 7px 10px;
            font-size: 9.5px;
        }

        .total-item-row {
            background: #f8fafc;
            color: #475569;
            border-top: 1px solid #e2e8f0;
        }

        .grand-total-row {
            background: #ecfdf5;
            color: #065f46;
            font-weight: bold;
            border-top: 1.5px solid #cbd5e1;
        }

        /* ── TERBILANG BOX ──────────────────────────── */
        .terbilang-box {
            background: #f8fafc;
            border-left: 3px solid #10b981;
            border-radius: 0 6px 6px 0;
            padding: 8px 12px;
            margin-bottom: 16px;
            font-size: 9px;
        }
        .terbilang-label {
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            font-size: 8px;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }
        .terbilang-text {
            font-style: italic;
            color: #0f172a;
            font-weight: bold;
        }

        /* ── SIGNATURES ─────────────────────────────── */
        .sig-section {
            margin-top: 18px;
            width: 100%;
            border-collapse: collapse;
        }
        .sig-section td {
            vertical-align: top;
            text-align: center;
        }
        .sig-title {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 42px;
        }
        .sig-name {
            font-size: 10px;
            font-weight: bold;
            color: #0f172a;
            border-bottom: 1px solid #94a3b8;
            display: inline-block;
            padding-bottom: 2px;
            min-width: 150px;
        }

        .seal-box {
            width: 44px;
            height: 44px;
            border: 1.5px dashed #059669;
            border-radius: 50%;
            color: #059669;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            margin: 0 auto;
            padding-top: 10px;
            background: #ecfdf5;
        }

        /* ── FOOTER ─────────────────────────────────── */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 22px;
            border-top: 1px solid #f1f5f9;
            padding-top: 8px;
        }
        .footer-table td {
            font-size: 8px;
            color: #94a3b8;
        }
        .footer-table td.right {
            text-align: right;
            color: #64748b;
            font-weight: bold;
        }
        .footer-table td.right span {
            color: #059669;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- HEADER --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <div class="logo-box">E</div>
                </td>
                <td>
                    <div class="institution-name">{{ config('app.name', 'PONDOK PESANTREN AL-FITHROH') }}</div>
                    <div class="institution-sub">SIM Keuangan Santri &bull; <span class="elvith-brand">elvith.id</span></div>
                </td>
                <td class="doc-badge">
                    <div class="title-tag">Kuitansi Pembayaran</div>
                    <div class="receipt-num">{{ $receipt_no }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- STATUS STRIP --}}
    <div class="status-strip">
        <table class="status-table">
            <tr>
                <td class="status-label">&#10003; PEMBAYARAN RESMI &bull; LUNAS</td>
                <td class="status-sub">Dicetak pada: {{ $generated_at }}</td>
            </tr>
        </table>
    </div>

    {{-- BENTO INFO GRID --}}
    <table class="info-grid-table">
        <tr>
            <!-- Kolom Santri -->
            <td>
                <div class="info-card-inner info-card-left">
                    <table class="nested-info-table">
                        <tr>
                            <td class="n-key">Nama Santri</td>
                            <td class="n-sep">:</td>
                            <td class="n-val">{{ $santri_name }} ({{ $santri_gender }})</td>
                        </tr>
                        <tr>
                            <td class="n-key">Kelas / Madrasah</td>
                            <td class="n-sep">:</td>
                            <td class="n-val">{{ $kelas_name }}</td>
                        </tr>
                        <tr>
                            <td class="n-key">Komplek / Kamar</td>
                            <td class="n-sep">:</td>
                            <td class="n-val">{{ $dorm_name }}</td>
                        </tr>
                    </table>
                </div>
            </td>
            <!-- Kolom Transaksi -->
            <td>
                <div class="info-card-inner info-card-right">
                    <table class="nested-info-table">
                        <tr>
                            <td class="n-key">Tanggal Bayar</td>
                            <td class="n-sep">:</td>
                            <td class="n-val">{{ $payment_date }} {{ $payment_time }}</td>
                        </tr>
                        <tr>
                            <td class="n-key">Metode Bayar</td>
                            <td class="n-sep">:</td>
                            <td class="n-val">{{ $payment_method }}</td>
                        </tr>
                        <tr>
                            <td class="n-key">Petugas Kasir</td>
                            <td class="n-sep">:</td>
                            <td class="n-val">{{ $cashier_name }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    @if(!empty($notes))
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 10px; margin-bottom: 12px; font-size: 8.5px; color: #475569;">
            <strong style="color: #0f172a;">Catatan:</strong> {{ $notes }}
        </div>
    @endif

    {{-- RINCIAN TAGIHAN (BREAKDOWN TABLE) --}}
    <div class="table-container">
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
                        <td class="text-center" style="color: #94a3b8;">{{ $idx + 1 }}</td>
                        <td>
                            <strong style="color: #0f172a;">{{ $item['config_label'] }}</strong>
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
                @if($payment_method === 'Tunai' && $tendered_amount > $total_amount)
                    <tr class="total-item-row">
                        <td colspan="3" class="text-right" style="color: #64748b;">Uang Tunai Diterima</td>
                        <td class="text-right" style="font-family: 'Courier New', Courier, monospace;">
                            Rp {{ number_format($tendered_amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr class="total-item-row">
                        <td colspan="3" class="text-right" style="color: #64748b;">Kembalian Tunai</td>
                        <td class="text-right" style="font-family: 'Courier New', Courier, monospace; color: #059669; font-weight: bold;">
                            Rp {{ number_format($change_amount, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
                <tr class="grand-total-row">
                    <td colspan="3" class="text-right" style="text-transform: uppercase; letter-spacing: 0.5px;">TOTAL PEMBAYARAN</td>
                    <td class="text-right" style="font-family: 'Courier New', Courier, monospace; font-size: 11px;">
                        Rp {{ number_format($total_amount, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- TERBILANG BOX --}}
    <div class="terbilang-box">
        <div class="terbilang-label">Terbilang:</div>
        <div class="terbilang-text"># {{ $terbilang }} #</div>
    </div>

    {{-- TANDA TANGAN & SEAL --}}
    <table class="sig-section">
        <tr>
            <td style="width: 35%;">
                <div class="sig-title">Wali Santri / Penyetor,</div>
                <div class="sig-name">( ........................................ )</div>
            </td>
            <td style="width: 30%;">
                <div class="seal-box">
                    <div>elvith.id</div>
                    <div style="font-size: 7px; color: #64748b;">VERIFIED</div>
                </div>
            </td>
            <td style="width: 35%;">
                <div class="sig-title">Kasir / Bendahara Pondok,</div>
                <div class="sig-name">{{ $cashier_name }}</div>
            </td>
        </tr>
    </table>

    {{-- FOOTER --}}
    <table class="footer-table">
        <tr>
            <td>
                Dokumen ini merupakan bukti pembayaran resmi yang sah dan diterbitkan secara elektronik oleh sistem pesantren.
            </td>
            <td class="right">
                Powered by <span>elvith.id</span>
            </td>
        </tr>
    </table>

</div>
</body>
</html>