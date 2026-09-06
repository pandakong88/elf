<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Bukti Pembayaran - {{ $no_bukti }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #ffffff;
            padding: 0;
        }

        .page {
            padding: 30px 35px;
            max-width: 794px;
            margin: 0 auto;
        }

        /* ── HEADER ─────────────────────────────────── */
        .header {
            border-bottom: 3px solid #1e293b;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .institution-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e293b;
            letter-spacing: 0.5px;
        }
        .institution-sub {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
        .doc-badge {
            text-align: right;
        }
        .doc-badge .title {
            font-size: 13px;
            font-weight: bold;
            color: #1e293b;
            letter-spacing: 0.3px;
        }
        .doc-badge .subtitle {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }

        /* ── STATUS BANNER ─────────────────────────── */
        .status-banner {
            background: #f0fdf4;
            border: 1.5px solid #86efac;
            border-radius: 6px;
            padding: 10px 16px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .status-banner .icon { font-size: 16px; }
        .status-banner .label {
            font-size: 12px;
            font-weight: bold;
            color: #166534;
        }
        .status-banner .sub {
            font-size: 9px;
            color: #4ade80;
            margin-top: 1px;
        }

        /* ── INFO GRID ──────────────────────────────── */
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 18px;
            border-collapse: collapse;
        }
        .info-grid .row {
            display: table-row;
        }
        .info-grid .key {
            display: table-cell;
            width: 32%;
            padding: 5px 8px 5px 0;
            font-size: 10px;
            color: #64748b;
            font-weight: normal;
            vertical-align: top;
        }
        .info-grid .sep {
            display: table-cell;
            width: 4%;
            padding: 5px 8px;
            color: #cbd5e1;
            vertical-align: top;
        }
        .info-grid .val {
            display: table-cell;
            padding: 5px 0;
            font-size: 10px;
            font-weight: bold;
            color: #1e293b;
            vertical-align: top;
        }
        .info-section-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #94a3b8;
            margin-bottom: 6px;
            margin-top: 4px;
        }

        /* ── DIVIDER ─────────────────────────────────── */
        .divider {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 14px 0;
        }
        .divider-dark {
            border: none;
            border-top: 2px solid #1e293b;
            margin: 14px 0;
        }

        /* ── BREAKDOWN TABLE ─────────────────────────── */
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .breakdown-table thead tr th {
            background: #f8fafc;
            padding: 7px 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        .breakdown-table thead tr th.right { text-align: right; }
        .breakdown-table tbody tr td {
            padding: 8px 10px;
            font-size: 10px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        .breakdown-table tbody tr td.right { text-align: right; }
        .breakdown-table tbody tr:last-child td { border-bottom: none; }
        .pill {
            display: inline-block;
            padding: 1px 7px;
            border-radius: 20px;
            font-size: 8px;
            font-weight: bold;
        }
        .pill-green { background: #dcfce7; color: #166534; }
        .pill-amber { background: #fef3c7; color: #92400e; }

        /* ── TOTAL BOX ─────────────────────────────── */
        .total-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 18px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }
        .total-row:last-child { margin-bottom: 0; }
        .total-row .tkey { font-size: 10px; color: #64748b; }
        .total-row .tval { font-size: 10px; font-weight: bold; color: #334155; }
        .total-row.grand .tkey { font-size: 12px; color: #1e293b; font-weight: bold; }
        .total-row.grand .tval { font-size: 14px; color: #1e293b; font-weight: bold; }
        .total-separator { border: none; border-top: 1px dashed #cbd5e1; margin: 7px 0; }
        .mdr-note { font-size: 8px; color: #f59e0b; margin-top: 4px; text-align: right; }

        /* ── KASIR INFO ─────────────────────────────── */
        .kasir-info {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 14px;
        }

        /* ── FOOTER ─────────────────────────────────── */
        .footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
            margin-top: 10px;
        }
        .footer-main {
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
            line-height: 1.6;
        }
        .footer-legal {
            font-size: 8px;
            color: #cbd5e1;
            text-align: center;
            margin-top: 4px;
        }
        .no-bukti-code {
            font-family: "DejaVu Sans Mono", monospace;
            font-size: 9px;
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 3px;
            color: #475569;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ═════ HEADER ═════ --}}
    <div class="header">
        <div class="header-top">
            <div>
                <div class="institution-name">{{ strtoupper($app_name) }}</div>
                <div class="institution-sub">Sistem Manajemen Keuangan Santri</div>
            </div>
            <div class="doc-badge">
                <div class="title">BUKTI PEMBAYARAN</div>
                <div class="subtitle">Dokumen Resmi &bull; Diterbitkan Elektronik</div>
            </div>
        </div>
    </div>

    {{-- ═════ STATUS BANNER ═════ --}}
    <div class="status-banner">
        <div class="icon">&#10003;</div>
        <div>
            <div class="label">Pembayaran Berhasil Diterima</div>
            <div class="sub">Transaksi telah diverifikasi dan tercatat dalam sistem</div>
        </div>
    </div>

    {{-- ═════ INFO TRANSAKSI ═════ --}}
    <div class="info-section-title">Informasi Transaksi</div>
    <div class="info-grid">
        <div class="row">
            <div class="key">No. Bukti</div>
            <div class="sep">:</div>
            <div class="val"><span class="no-bukti-code">{{ $no_bukti }}</span></div>
        </div>
        @if($type === 'gateway' && isset($duitku_reference) && $duitku_reference !== '—')
        <div class="row">
            <div class="key">Ref. Gateway</div>
            <div class="sep">:</div>
            <div class="val"><span class="no-bukti-code">{{ $duitku_reference }}</span></div>
        </div>
        @endif
        <div class="row">
            <div class="key">Nama Santri</div>
            <div class="sep">:</div>
            <div class="val">{{ $santri_name }}</div>
        </div>
        <div class="row">
            <div class="key">Metode Pembayaran</div>
            <div class="sep">:</div>
            <div class="val">{{ $payment_method }}</div>
        </div>
        <div class="row">
            <div class="key">Tanggal &amp; Waktu</div>
            <div class="sep">:</div>
            <div class="val">{{ $payment_date }}</div>
        </div>
        @if($type === 'kasir' && isset($logged_by))
        <div class="row">
            <div class="key">Dicatat Oleh</div>
            <div class="sep">:</div>
            <div class="val">{{ $logged_by }}</div>
        </div>
        @endif
        @if(!empty($notes))
        <div class="row">
            <div class="key">Catatan</div>
            <div class="sep">:</div>
            <div class="val">{{ $notes }}</div>
        </div>
        @endif
    </div>

    <hr class="divider">

    {{-- ═════ BREAKDOWN TAGIHAN ═════ --}}
    <div class="info-section-title">Rincian Tagihan</div>
    <table class="breakdown-table">
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th>Jenis Iuran</th>
                <th>Periode</th>
                <th class="right" style="width:22%">Jumlah</th>
                <th class="right" style="width:15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($breakdown as $i => $item)
            <tr>
                <td style="color:#94a3b8;text-align:center">{{ $i + 1 }}</td>
                <td>
                    <strong>{{ $item['config_label'] ?? '—' }}</strong>
                </td>
                <td style="color:#64748b">{{ $item['period_label'] ?? '—' }}</td>
                <td class="right"><strong>Rp {{ number_format($item['pay_portion'] ?? $item['net_amount'] ?? 0, 0, ',', '.') }}</strong></td>
                <td class="right">
                    @if($item['is_partial'] ?? false)
                        <span class="pill pill-amber">Sebagian</span>
                    @else
                        <span class="pill pill-green">Lunas</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <hr class="divider">

    {{-- ═════ TOTAL ═════ --}}
    <div class="total-box">
        @if($mdr_amount > 0)
        <div class="total-row">
            <span class="tkey">Nominal Tagihan</span>
            <span class="tval">Rp {{ number_format($bill_amount, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span class="tkey">Biaya Layanan Gateway</span>
            <span class="tval" style="color:#ef4444">+ Rp {{ number_format($mdr_amount, 0, ',', '.') }}</span>
        </div>
        <hr class="total-separator">
        @endif
        <div class="total-row grand">
            <span class="tkey">TOTAL DIBAYAR</span>
            <span class="tval">Rp {{ number_format($total_amount, 0, ',', '.') }}</span>
        </div>
        @if($mdr_amount > 0)
        <div class="mdr-note">* Biaya layanan ditanggung oleh wali santri sesuai ketentuan.</div>
        @endif
    </div>

    {{-- ═════ FOOTER ═════ --}}
    <div class="footer">
        <div class="footer-main">
            Dokumen ini diterbitkan secara elektronik oleh sistem {{ $app_name }} dan sah tanpa tanda tangan fisik.<br>
            Dicetak pada: {{ $generated_at }}
        </div>
        <div class="footer-legal">
            Simpan bukti ini sebagai referensi. Untuk pertanyaan, hubungi bagian keuangan pondok.
        </div>
    </div>

</div>
</body>
</html>