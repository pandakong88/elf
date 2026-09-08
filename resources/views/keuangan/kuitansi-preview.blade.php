<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Pembayaran #{{ $receipt_no }} - elvith.id</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --brand-emerald: #10b981;
            --brand-emerald-dark: #059669;
            --brand-emerald-light: #ecfdf5;
            --brand-slate-dark: #0f172a;
            --brand-slate-medium: #334155;
            --brand-slate-muted: #64748b;
            --brand-slate-light: #f8fafc;
            --brand-border: #e2e8f0;
            --paper-bg: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #0b0f19;
            background-image: radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.12) 0px, transparent 50%),
                              radial-gradient(at 100% 100%, rgba(14, 165, 233, 0.08) 0px, transparent 50%);
            background-attachment: fixed;
            color: var(--brand-slate-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-bottom: 70px;
        }

        /* ── TOP ACTION BAR (FLOATING GLASSMORPHISM) ────────────────────────── */
        .action-bar-wrapper {
            position: sticky;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 999;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
        }

        .action-bar {
            max-width: 860px;
            margin: 0 auto;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .action-bar .left-group {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .action-bar .brand-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .action-bar .brand-logo-mini {
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 800;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);
        }

        .action-bar .brand-name {
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.3px;
        }
        .action-bar .brand-name span {
            color: #34d399;
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
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 9px;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
            line-height: 1;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.08);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
        .btn-back:hover {
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff;
            transform: translateX(-2px);
        }

        .btn-print {
            background: #ffffff;
            color: #0f172a;
            box-shadow: 0 2px 10px rgba(255, 255, 255, 0.2);
        }
        .btn-print:hover {
            background: #f1f5f9;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(255, 255, 255, 0.3);
        }

        .btn-download {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
        }
        .btn-download:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45);
        }

        /* ── PAPER CONTAINER ────────────────────────── */
        .paper-container {
            margin-top: 36px;
            width: 100%;
            max-width: 840px;
            padding: 0 20px;
        }

        .receipt-paper {
            background: var(--paper-bg);
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.1);
            padding: 44px 50px;
            position: relative;
            overflow: hidden;
        }

        /* Top decorative accent bar */
        .receipt-paper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #10b981 0%, #0284c7 50%, #10b981 100%);
        }

        /* Watermark Background */
        .watermark-bg {
            position: absolute;
            right: -40px;
            bottom: 40px;
            font-size: 160px;
            font-weight: 900;
            color: rgba(16, 185, 129, 0.03);
            pointer-events: none;
            user-select: none;
            transform: rotate(-15deg);
            z-index: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -5px;
        }

        .content-relative {
            position: relative;
            z-index: 1;
        }

        /* ── HEADER ─────────────────────────────────── */
        .receipt-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 22px;
            border-bottom: 1.5px dashed #cbd5e1;
            margin-bottom: 24px;
        }

        .brand-group {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-logo-box {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 800;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            flex-shrink: 0;
        }

        .brand-text-block .inst-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.3px;
            line-height: 1.2;
        }

        .brand-text-block .brand-sub {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #64748b;
            margin-top: 3px;
            font-weight: 500;
        }

        .brand-sub .elvith-tag {
            color: #059669;
            font-weight: 700;
        }

        .doc-identity {
            text-align: right;
        }

        .doc-title-badge {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #0f172a;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 6px;
            display: inline-block;
            margin-bottom: 6px;
        }

        .doc-no {
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            font-weight: 700;
            color: #059669;
            letter-spacing: -0.2px;
        }

        /* ── STATUS STRIP ──────────────────────────── */
        .status-strip {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 12px 20px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-strip-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-icon-circle {
            width: 26px;
            height: 26px;
            background: #16a34a;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            box-shadow: 0 2px 6px rgba(22, 163, 74, 0.3);
        }

        .status-main-label {
            font-size: 13.5px;
            font-weight: 800;
            color: #166534;
            letter-spacing: 0.2px;
        }

        .status-strip-right {
            font-size: 11.5px;
            color: #15803d;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ── INFO BENTO GRID ───────────────────────── */
        .bento-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 26px;
        }

        .bento-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .bento-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
        }

        .bento-label {
            color: #64748b;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .bento-label i {
            color: #94a3b8;
            font-size: 13px;
            width: 14px;
            text-align: center;
        }

        .bento-val {
            font-weight: 700;
            color: #0f172a;
            text-align: right;
        }

        .gender-pill {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 999px;
            background: #e2e8f0;
            color: #334155;
            margin-left: 4px;
        }

        .pill-method {
            background: #e0f2fe;
            color: #0369a1;
            padding: 3px 9px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
        }

        /* ── TABLE ─────────────────────────────────── */
        .table-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 24px;
            background: #ffffff;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        .items-table thead th {
            background: #f8fafc;
            color: #475569;
            padding: 12px 18px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            border-bottom: 1px solid #e2e8f0;
        }

        .items-table tbody td {
            padding: 13px 18px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            vertical-align: middle;
        }

        .items-table tbody tr:last-child td {
            border-bottom: none;
        }

        .items-table tbody tr:hover {
            background: #fafafa;
        }

        .item-main-name {
            font-weight: 700;
            color: #0f172a;
            font-size: 13px;
        }

        .item-period-badge {
            display: inline-block;
            background: #f1f5f9;
            color: #475569;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }

        .item-partial-tag {
            font-size: 9.5px;
            font-weight: 700;
            color: #d97706;
            background: #fef3c7;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 6px;
        }

        /* Table Totals */
        .totals-section {
            border-top: 1.5px solid #cbd5e1;
            background: #f8fafc;
            padding: 16px 20px;
        }

        .total-item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 6px;
        }
        .total-item-row:last-child {
            margin-bottom: 0;
        }

        .grand-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 10px;
            margin-top: 8px;
            border-top: 1px dashed #cbd5e1;
        }

        .grand-total-label {
            font-size: 13px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .grand-total-val {
            font-family: 'JetBrains Mono', monospace;
            font-size: 19px;
            font-weight: 800;
            color: #059669;
            letter-spacing: -0.5px;
        }

        /* ── TERBILANG CARD ────────────────────────── */
        .terbilang-box {
            background: #f8fafc;
            border-left: 3.5px solid #10b981;
            border-radius: 0 10px 10px 0;
            padding: 12px 18px;
            margin-bottom: 28px;
        }

        .terbilang-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            margin-bottom: 3px;
        }

        .terbilang-text {
            font-size: 12.5px;
            font-weight: 700;
            font-style: italic;
            color: #0f172a;
        }

        /* ── SIGNATURES & SECURITY ─────────────────── */
        .footer-signatures {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            align-items: flex-end;
            gap: 16px;
            margin-top: 36px;
            padding-top: 10px;
        }

        .sig-block {
            text-align: center;
        }

        .sig-caption {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 54px;
        }

        .sig-line {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            border-bottom: 1.5px solid #94a3b8;
            display: inline-block;
            padding-bottom: 3px;
            min-width: 150px;
        }

        .security-seal {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .seal-badge {
            width: 52px;
            height: 52px;
            border: 2px dashed #059669;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #059669;
            font-size: 20px;
            background: #ecfdf5;
            margin-bottom: 4px;
        }

        .seal-text {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #059669;
        }

        .seal-sub {
            font-size: 8px;
            color: #94a3b8;
        }

        /* ── FOOTER LEGAL ──────────────────────────── */
        .system-footer {
            margin-top: 36px;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10px;
            color: #94a3b8;
        }

        .system-footer .power-by {
            display: flex;
            align-items: center;
            gap: 4px;
            font-weight: 600;
            color: #64748b;
        }
        .system-footer .power-by .brand {
            color: #059669;
            font-weight: 800;
        }

        .mono {
            font-family: 'JetBrains Mono', monospace;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }

        /* ── RESPONSIVE & MOBILE RULES ───────────────── */
        @media (max-width: 768px) {
            body {
                padding-bottom: 40px;
            }

            .action-bar {
                padding: 10px 14px;
                gap: 10px;
            }

            .action-bar .brand-badge {
                display: none;
            }

            .btn-nav {
                padding: 7px 11px;
                font-size: 11.5px;
                gap: 5px;
            }

            .paper-container {
                margin-top: 18px;
                padding: 0 10px;
            }

            .receipt-paper {
                padding: 24px 18px;
                border-radius: 12px;
            }

            .bento-info-grid {
                grid-template-columns: 1fr;
                gap: 10px;
                margin-bottom: 18px;
            }

            .bento-card {
                padding: 12px 14px;
            }

            .receipt-header {
                flex-direction: column;
                gap: 14px;
                align-items: stretch;
                padding-bottom: 16px;
                margin-bottom: 18px;
            }

            .doc-identity {
                text-align: left;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-top: 1px dashed #e2e8f0;
                padding-top: 10px;
            }

            .doc-title-badge {
                margin-bottom: 0;
            }

            .status-strip {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
                padding: 10px 14px;
                margin-bottom: 18px;
            }

            .items-table {
                font-size: 11.5px;
            }

            .items-table thead th {
                padding: 9px 10px;
                font-size: 10px;
            }

            .items-table tbody td {
                padding: 10px 10px;
            }

            .item-main-name {
                font-size: 12px;
            }

            .footer-signatures {
                grid-template-columns: 1fr 1fr;
                gap: 16px;
                margin-top: 24px;
            }

            .security-seal {
                grid-column: span 2;
                order: 3;
                margin-top: 10px;
                padding-top: 10px;
                border-top: 1px dashed #e2e8f0;
            }

            .sig-line {
                min-width: 100px;
                width: 100%;
                font-size: 11px;
            }

            .sig-caption {
                font-size: 10px;
                margin-bottom: 38px;
            }

            .system-footer {
                flex-direction: column;
                gap: 8px;
                text-align: center;
                font-size: 9.5px;
            }
        }

        @media (max-width: 480px) {
            .btn-nav span.desktop-only {
                display: none;
            }

            .brand-text-block .inst-title {
                font-size: 14px;
            }

            .grand-total-val {
                font-size: 16px;
            }

            .terbilang-text {
                font-size: 11px;
            }
        }

        /* ── PRINT RULES ───────────────────────────── */
        @media print {
            *, *::before, *::after {
                text-shadow: none !important;
                box-shadow: none !important;
            }

            html, body {
                background: #ffffff !important;
                color: #000000 !important;
                width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
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
                padding: 6mm 8mm !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .receipt-paper::before {
                display: none !important;
            }

            .watermark-bg {
                display: none !important;
            }

            .bento-info-grid {
                grid-template-columns: 1fr 1fr !important;
            }

            .footer-signatures {
                grid-template-columns: 1fr 1fr 1fr !important;
            }

            .security-seal {
                grid-column: auto !important;
                order: 2 !important;
                border-top: none !important;
                margin-top: 0 !important;
                padding-top: 0 !important;
            }

            @page {
                size: auto;
                margin: 8mm 10mm;
            }
        }
    </style>
</head>
<body>

    <!-- FLOATING TOP BAR -->
    <div class="action-bar-wrapper">
        <div class="action-bar">
            <div class="left-group">
                <a href="{{ $back_url ?? route('keuangan.billing', ['tab' => 'payments_log']) }}" class="btn-nav btn-back" title="{{ $back_label ?? 'Kembali' }}">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>{{ $back_label ?? 'Kembali' }}</span>
                </a>
                <div class="brand-badge">
                    <div class="brand-logo-mini">E</div>
                    <span class="brand-name">elvith<span>.id</span></span>
                </div>
            </div>
            <div class="right-group">
                <button type="button" onclick="window.print()" class="btn-nav btn-print" title="Cetak Kuitansi">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak <span class="desktop-only">Kuitansi</span></span>
                </button>
                <a href="{{ route('bukti-bayar.kuitansi.pdf', $receipt_no) }}" class="btn-nav btn-download" title="Unduh PDF">
                    <i class="fa-solid fa-download"></i>
                    <span>Unduh <span class="desktop-only">PDF</span></span>
                </a>
            </div>
        </div>
    </div>

    <!-- MAIN RECEIPT PAPER -->
    <div class="paper-container">
        <div class="receipt-paper">
            
            <div class="watermark-bg">ELVITH</div>

            <div class="content-relative">
                
                <!-- HEADER BRANDING -->
                <div class="receipt-header">
                    <div class="brand-group">
                        <div class="brand-logo-box">E</div>
                        <div class="brand-text-block">
                            <div class="inst-title">{{ config('app.name', 'PONDOK PESANTREN AL-FITHROH') }}</div>
                            <div class="brand-sub">
                                <span>SIM Keuangan Santri</span>
                                <span>&bull;</span>
                                <span class="elvith-tag">elvith.id</span>
                            </div>
                        </div>
                    </div>
                    <div class="doc-identity">
                        <div class="doc-title-badge">Kuitansi Pembayaran</div>
                        <div class="doc-no">{{ $receipt_no }}</div>
                    </div>
                </div>

                <!-- STATUS BANNER -->
                <div class="status-strip">
                    <div class="status-strip-left">
                        <div class="status-icon-circle">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div class="status-main-label">PEMBAYARAN RESMI &bull; LUNAS</div>
                    </div>
                    <div class="status-strip-right">
                        <i class="fa-regular fa-clock"></i>
                        <span>{{ $generated_at }}</span>
                    </div>
                </div>

                <!-- BENTO INFO GRID -->
                <div class="bento-info-grid">
                    <!-- Santri & Akademik -->
                    <div class="bento-card">
                        <div class="bento-row">
                            <span class="bento-label"><i class="fa-regular fa-user"></i> Nama Santri</span>
                            <span class="bento-val">
                                {{ $santri_name }} 
                                <span class="gender-pill">{{ $santri_gender }}</span>
                            </span>
                        </div>
                        <div class="bento-row">
                            <span class="bento-label"><i class="fa-solid fa-graduation-cap"></i> Kelas / Madrasah</span>
                            <span class="bento-val">{{ $kelas_name }}</span>
                        </div>
                        <div class="bento-row">
                            <span class="bento-label"><i class="fa-solid fa-hotel"></i> Komplek / Kamar</span>
                            <span class="bento-val">{{ $dorm_name }}</span>
                        </div>
                    </div>

                    <!-- Transaksi Info -->
                    <div class="bento-card">
                        <div class="bento-row">
                            <span class="bento-label"><i class="fa-regular fa-calendar-check"></i> Tanggal Bayar</span>
                            <span class="bento-val">{{ $payment_date }} {{ $payment_time }}</span>
                        </div>
                        <div class="bento-row">
                            <span class="bento-label"><i class="fa-solid fa-wallet"></i> Metode Bayar</span>
                            <span class="bento-val">
                                <span class="pill-method">{{ $payment_method }}</span>
                            </span>
                        </div>
                        <div class="bento-row">
                            <span class="bento-label"><i class="fa-solid fa-user-tie"></i> Petugas Kasir</span>
                            <span class="bento-val">{{ $cashier_name }}</span>
                        </div>
                    </div>
                </div>

                @if(!empty($notes))
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 16px; margin-bottom: 22px; font-size: 11.5px; color: #475569;">
                        <strong style="color: #0f172a;">Catatan Transaksi:</strong> {{ $notes }}
                    </div>
                @endif

                <!-- ITEMS TABLE CARD -->
                <div class="table-card">
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
                                    <td class="text-center" style="color: #94a3b8; font-weight: 600;">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="item-main-name">{{ $item['config_label'] }}</div>
                                        @if(!empty($item['is_partial']))
                                            <span class="item-partial-tag">Pembayaran Sebagian</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="item-period-badge">{{ $item['period_label'] }}</span>
                                    </td>
                                    <td class="text-right mono" style="font-weight: 700; color: #0f172a;">
                                        Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Totals & Calculations -->
                    <div class="totals-section">
                        @if($payment_method === 'Tunai' && $tendered_amount > $total_amount)
                            <div class="total-item-row">
                                <span>Uang Tunai Diterima:</span>
                                <span class="mono" style="font-weight: 600; color: #334155;">Rp {{ number_format($tendered_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="total-item-row">
                                <span>Kembalian Tunai:</span>
                                <span class="mono" style="font-weight: 700; color: #059669;">Rp {{ number_format($change_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="grand-total-row">
                            <span class="grand-total-label">TOTAL PEMBAYARAN</span>
                            <span class="grand-total-val">Rp {{ number_format($total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- TERBILANG BOX -->
                <div class="terbilang-box">
                    <div class="terbilang-label">Terbilang</div>
                    <div class="terbilang-text"># {{ $terbilang }} #</div>
                </div>

                <!-- SIGNATURES SECTION & DIGITAL SEAL -->
                <div class="footer-signatures">
                    <div class="sig-block">
                        <div class="sig-caption">Wali Santri / Penyetor,</div>
                        <div class="sig-line">( ........................................ )</div>
                    </div>

                    <div class="security-seal">
                        <div class="seal-badge">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div class="seal-text">elvith.id Verified</div>
                        <div class="seal-sub">Valid Official E-Receipt</div>
                    </div>

                    <div class="sig-block">
                        <div class="sig-caption">Kasir / Bendahara Pondok,</div>
                        <div class="sig-line">{{ $cashier_name }}</div>
                    </div>
                </div>

                <!-- SYSTEM FOOTER -->
                <div class="system-footer">
                    <div>
                        Dokumen ini adalah bukti pembayaran resmi yang diterbitkan secara elektronik oleh sistem pesantren.
                    </div>
                    <div class="power-by">
                        <span>Powered by</span>
                        <span class="brand">elvith.id</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>