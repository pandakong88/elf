<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Pembayaran - {{ $receipt_no }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary: #0f172a;
            --primary-accent: #0284c7;
            --success: #16a34a;
            --bg-body: #f1f5f9;
            --paper-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-bottom: 60px;
        }

        /* ── TOP ACTION BAR ────────────────────────── */
        .action-bar-wrapper {
            position: sticky;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            background: rgba(15, 23, 42, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 999;
            box-shadow: 0 4px 20px -5px rgba(0, 0, 0, 0.3);
        }

        .action-bar {
            max-width: 900px;
            margin: 0 auto;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .action-bar .left-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .action-bar .right-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-nav {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.1);
            color: #f8fafc;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .btn-back:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            transform: translateX(-2px);
        }

        .btn-print {
            background: #0284c7;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(2, 132, 199, 0.4);
        }
        .btn-print:hover {
            background: #0369a1;
            transform: translateY(-1px);
        }

        .btn-download {
            background: #10b981;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);
        }
        .btn-download:hover {
            background: #059669;
            transform: translateY(-1px);
        }

        .badge-preview {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        /* ── PAPER CONTAINER ────────────────────────── */
        .paper-container {
            margin-top: 30px;
            width: 100%;
            max-width: 820px;
            padding: 0 16px;
        }

        .receipt-paper {
            background: var(--paper-bg);
            border-radius: 12px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(0, 0, 0, 0.04);
            padding: 40px 48px;
            position: relative;
        }

        /* ── HEADER ─────────────────────────────────── */
        .header-section {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 18px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .inst-title {
            font-size: 19px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .inst-subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 3px;
            font-weight: 500;
        }

        .receipt-header-right {
            text-align: right;
        }

        .doc-title {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .receipt-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            font-weight: 700;
            color: #059669;
            margin-top: 3px;
            background: #ecfdf5;
            padding: 2px 8px;
            border-radius: 6px;
            display: inline-block;
            border: 1px solid #a7f3d0;
        }

        /* ── STATUS BANNER ─────────────────────────── */
        .status-banner {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1.5px solid #86efac;
            border-radius: 10px;
            padding: 12px 18px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-badge-text {
            font-size: 13px;
            font-weight: 700;
            color: #166534;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-timestamp {
            font-size: 11px;
            color: #15803d;
            font-weight: 500;
        }

        /* ── INFO GRID ──────────────────────────────── */
        .info-grid-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 22px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 24px;
        }

        .info-row {
            display: flex;
            align-items: flex-start;
            font-size: 12px;
            line-height: 1.5;
        }

        .info-label {
            width: 125px;
            color: #64748b;
            font-weight: 500;
            flex-shrink: 0;
        }

        .info-sep {
            width: 15px;
            color: #94a3b8;
            flex-shrink: 0;
        }

        .info-val {
            color: #0f172a;
            font-weight: 700;
            flex-grow: 1;
            word-break: break-word;
        }

        /* ── BREAKDOWN TABLE ─────────────────────────── */
        .table-wrapper {
            margin-bottom: 20px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #cbd5e1;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .items-table thead th {
            background: #0f172a;
            color: #ffffff;
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }
        .items-table thead th:last-child {
            border-right: none;
        }

        .items-table tbody td {
            padding: 10px 14px;
            border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            color: #1e293b;
        }
        .items-table tbody td:last-child {
            border-right: none;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .items-table tfoot td {
            padding: 10px 14px;
            border-top: 1px solid #cbd5e1;
            border-right: 1px solid #cbd5e1;
        }
        .items-table tfoot td:last-child {
            border-right: none;
        }

        .row-total-main {
            background: #ecfdf5 !important;
            color: #065f46;
            font-weight: 800;
        }

        .row-calc {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
        }

        .mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }

        /* ── TERBILANG BOX ──────────────────────────── */
        .terbilang-card {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 24px;
        }

        .terbilang-title {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .terbilang-content {
            font-size: 12.5px;
            font-weight: 700;
            font-style: italic;
            color: #0f172a;
        }

        /* ── SIGNATURES ─────────────────────────────── */
        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 30px;
            padding-top: 10px;
        }

        .sig-box {
            text-align: center;
        }

        .sig-label {
            font-size: 11.5px;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 60px;
        }

        .sig-under {
            font-size: 12.5px;
            font-weight: 700;
            color: #0f172a;
            border-bottom: 1.5px solid #94a3b8;
            display: inline-block;
            padding-bottom: 3px;
            min-width: 180px;
        }

        /* ── FOOTER ─────────────────────────────────── */
        .page-footer {
            margin-top: 35px;
            padding-top: 14px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            font-weight: 500;
        }

        /* ── PRINT MEDIA QUERIES ────────────────────── */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .action-bar-wrapper {
                display: none !important;
            }

            .paper-container {
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .receipt-paper {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                padding: 15mm 15mm !important;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <!-- STICKY TOP ACTION BAR (Hidden in print) -->
    <div class="action-bar-wrapper">
        <div class="action-bar">
            <div class="left-group">
                <a href="{{ route('keuangan.billing') }}" class="btn-nav btn-back">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Kembali ke Kasir</span>
                </a>
                <span class="badge-preview">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>Preview Kuitansi</span>
                </span>
            </div>
            <div class="right-group">
                <button type="button" onclick="window.print()" class="btn-nav btn-print">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak Kuitansi</span>
                </button>
                <a href="{{ route('bukti-bayar.kuitansi.pdf', $receipt_no) }}" class="btn-nav btn-download">
                    <i class="fa-solid fa-download"></i>
                    <span>Unduh PDF</span>
                </a>
            </div>
        </div>
    </div>

    <!-- MAIN PAPER CONTAINER -->
    <div class="paper-container">
        <div class="receipt-paper">
            
            <!-- HEADER -->
            <div class="header-section">
                <div>
                    <div class="inst-title">{{ config('app.name', 'PONDOK PESANTREN') }}</div>
                    <div class="inst-subtitle">Sistem Informasi Manajemen Keuangan Santri</div>
                </div>
                <div class="receipt-header-right">
                    <div class="doc-title">Kuitansi Pembayaran Kasir</div>
                    <div class="receipt-code">{{ $receipt_no }}</div>
                </div>
            </div>

            <!-- STATUS BANNER -->
            <div class="status-banner">
                <div class="status-badge-text">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Pembayaran Berhasil (Lunas)</span>
                </div>
                <div class="status-timestamp">
                    Dicetak pada: {{ $generated_at }}
                </div>
            </div>

            <!-- INFO GRID DUA KOLOM -->
            <div class="info-grid-card">
                <div class="info-row">
                    <span class="info-label">Nama Santri</span>
                    <span class="info-sep">:</span>
                    <span class="info-val">{{ $santri_name }} ({{ $santri_gender }})</span>
                </div>
                <div class="info-row">
                    <span class="info-label">No. Kuitansi</span>
                    <span class="info-sep">:</span>
                    <span class="info-val mono">{{ $receipt_no }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Kelas / Madrasah</span>
                    <span class="info-sep">:</span>
                    <span class="info-val">{{ $kelas_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Bayar</span>
                    <span class="info-sep">:</span>
                    <span class="info-val">{{ $payment_date }} {{ $payment_time }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Komplek / Asrama</span>
                    <span class="info-sep">:</span>
                    <span class="info-val">{{ $dorm_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Metode Bayar</span>
                    <span class="info-sep">:</span>
                    <span class="info-val">{{ $payment_method }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Petugas Kasir</span>
                    <span class="info-sep">:</span>
                    <span class="info-val">{{ $cashier_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Catatan</span>
                    <span class="info-sep">:</span>
                    <span class="info-val">{{ $notes ?: '—' }}</span>
                </div>
            </div>

            <!-- BREAKDOWN ITEMS TABLE -->
            <div class="table-wrapper">
                <table class="items-table">
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
                                    <span style="font-weight: 700; color: #0f172a;">{{ $item['config_label'] }}</span>
                                    @if(!empty($item['is_partial']))
                                        <span style="font-size: 10px; color: #d97706; background: #fef3c7; padding: 2px 6px; border-radius: 4px; margin-left: 6px;">Sebagian</span>
                                    @endif
                                </td>
                                <td class="text-center" style="color: #475569;">{{ $item['period_label'] }}</td>
                                <td class="text-right mono" style="font-weight: 700;">
                                    Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="row-total-main">
                            <td colspan="3" class="text-right" style="text-transform: uppercase; letter-spacing: 0.5px;">TOTAL PEMBAYARAN</td>
                            <td class="text-right mono" style="font-size: 13.5px;">
                                Rp {{ number_format($total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                        @if($payment_method === 'Tunai' && $tendered_amount > $total_amount)
                            <tr class="row-calc">
                                <td colspan="3" class="text-right">Uang Diterima (Tunai)</td>
                                <td class="text-right mono">
                                    Rp {{ number_format($tendered_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="row-calc">
                                <td colspan="3" class="text-right">Kembalian</td>
                                <td class="text-right mono" style="color: #059669; font-weight: 700;">
                                    Rp {{ number_format($change_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                    </tfoot>
                </table>
            </div>

            <!-- TERBILANG BOX -->
            <div class="terbilang-card">
                <div class="terbilang-title">Terbilang:</div>
                <div class="terbilang-content"># {{ $terbilang }} #</div>
            </div>

            <!-- TANDA TANGAN -->
            <div class="signature-grid">
                <div class="sig-box">
                    <div class="sig-label">Wali Santri / Penyetor,</div>
                    <div class="sig-under">( ........................................ )</div>
                </div>
                <div class="sig-box">
                    <div class="sig-label">Kasir / Bendahara Pondok,</div>
                    <div class="sig-under">{{ $cashier_name }}</div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="page-footer">
                Dokumen ini merupakan bukti pembayaran resmi yang sah dan diterbitkan secara elektronik oleh sistem {{ config('app.name', 'Elvith') }}.
            </div>

        </div>
    </div>

</body>
</html>