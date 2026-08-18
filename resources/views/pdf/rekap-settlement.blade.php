<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Laporan Rekonsiliasi & Distribusi Dana - {{ $period_label }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            background: #ffffff;
            padding: 25px 30px;
        }

        /* ── HEADER ── */
        .header {
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .inst-title { font-size: 16px; font-weight: bold; color: #0f172a; letter-spacing: 0.5px; }
        .inst-sub { font-size: 9px; color: #64748b; margin-top: 2px; }
        .doc-title { font-size: 13px; font-weight: bold; color: #0f172a; text-align: right; }
        .doc-sub { font-size: 9px; color: #64748b; text-align: right; margin-top: 2px; }

        /* ── SUMMARY BOXES ── */
        .kpi-table { width: 100%; border-collapse: separate; border-spacing: 6px; margin-bottom: 14px; }
        .kpi-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 10px;
            vertical-align: top;
        }
        .kpi-label { font-size: 8px; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .kpi-val { font-size: 13px; font-weight: bold; color: #0f172a; margin-top: 3px; font-family: "DejaVu Sans Mono", monospace; }
        .kpi-sub { font-size: 8px; color: #94a3b8; margin-top: 2px; }

        /* ── SECTION TITLE ── */
        .sec-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #0f172a;
            background: #f1f5f9;
            padding: 5px 8px;
            border-left: 3px solid #059669;
            margin-top: 14px;
            margin-bottom: 8px;
        }

        /* ── TABLES ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .data-table th {
            background: #f8fafc;
            border-bottom: 1.5px solid #cbd5e1;
            padding: 6px 8px;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #475569;
            text-align: left;
        }
        .data-table th.right { text-align: right; }
        .data-table td {
            padding: 6px 8px;
            font-size: 9.5px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        .data-table td.right { text-align: right; }
        .data-table tr.total-row td {
            background: #f8fafc;
            font-weight: bold;
            border-top: 1.5px solid #cbd5e1;
            border-bottom: 1.5px solid #cbd5e1;
            color: #0f172a;
        }

        /* ── SIGNATURES ── */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .sig-box { width: 33.33%; text-align: center; font-size: 9px; vertical-align: top; }
        .sig-space { height: 50px; }
        .sig-name { font-weight: bold; text-decoration: underline; }
        .sig-title { color: #64748b; font-size: 8.5px; margin-top: 2px; }

        /* ── FOOTER ── */
        .footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <div class="inst-title">{{ strtoupper($app_name) }}</div>
                    <div class="inst-sub">Sistem Manajemen Keuangan & Pembukuan Pesantren</div>
                </td>
                <td>
                    <div class="doc-title">REKAP REKONSILIASI & DISTRIBUSI DANA</div>
                    <div class="doc-sub">Periode: <strong>{{ $period_label }}</strong> &bull; Sumber: {{ $source_label }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- KPI ARUS KAS --}}
    <table class="kpi-table">
        <tr>
            <td class="kpi-box" style="width: 25%;">
                <div class="kpi-label">Total Uang Diterima (Gross)</div>
                <div class="kpi-val">Rp {{ number_format($total_gross, 0, ',', '.') }}</div>
                <div class="kpi-sub">{{ $total_trx }} Transaksi Sukses</div>
            </td>
            <td class="kpi-box" style="width: 25%;">
                <div class="kpi-label">Biaya Layanan Gateway (MDR)</div>
                <div class="kpi-val" style="color: #e11d48;">- Rp {{ number_format($total_mdr, 0, ',', '.') }}</div>
                <div class="kpi-sub">Dibebankan ke Wali / Gateway</div>
            </td>
            <td class="kpi-box" style="width: 30%; background: #ecfdf5; border-color: #a7f3d0;">
                <div class="kpi-label" style="color: #047857;">Dana Bersih Masuk (Net)</div>
                <div class="kpi-val" style="color: #047857;">Rp {{ number_format($total_net, 0, ',', '.') }}</div>
                <div class="kpi-sub" style="color: #059669;">Dana Efektif untuk Pesantren</div>
            </td>
            <td class="kpi-box" style="width: 20%;">
                <div class="kpi-label">Tanggal Cetak</div>
                <div class="kpi-val" style="font-size: 11px;">{{ $generated_at }}</div>
                <div class="kpi-sub">Dicetak oleh: {{ $generated_by }}</div>
            </td>
        </tr>
    </table>

    {{-- 1. ALOKASI POS ANGGARAN UTAMA --}}
    <div class="sec-title">1. Alokasi Pembagian Pos Anggaran Utama</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th>Pos Anggaran / Unit Keuangan</th>
                <th class="right" style="width: 18%;">Jumlah Transaksi</th>
                <th class="right" style="width: 25%;">Total Dana Masuk</th>
                <th class="right" style="width: 15%;">Porsi (%)</th>
            </tr>
        </thead>
        <tbody>
            @php $idx = 1; @endphp
            @foreach($category_breakdown as $cat)
            <tr>
                <td style="text-align: center; color: #94a3b8;">{{ $idx++ }}</td>
                <td>
                    <strong>{{ $cat['label'] }}</strong>
                    <div style="font-size: 8px; color: #64748b;">{{ $cat['desc'] ?? '' }}</div>
                </td>
                <td class="right">{{ $cat['count'] }} item</td>
                <td class="right"><strong>Rp {{ number_format($cat['amount'], 0, ',', '.') }}</strong></td>
                <td class="right">{{ $total_net > 0 ? number_format(($cat['amount'] / $total_net) * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">TOTAL DANA BERSIH TERBAGI:</td>
                <td class="right"><strong>Rp {{ number_format($total_net, 0, ',', '.') }}</strong></td>
                <td class="right">100.0%</td>
            </tr>
        </tbody>
    </table>

    {{-- 2. RINCIAN KHUSUS KAS KOMPLEK PER ASRAMA --}}
    <div class="sec-title">2. Rincian Alokasi Kas Komplek Asrama (Per Asrama/Komplek)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th>Nama Komplek Asrama</th>
                <th style="width: 15%;">Unit</th>
                <th class="right" style="width: 25%;">Jumlah Santri / Tagihan</th>
                <th class="right" style="width: 22%;">Total Kas Komplek</th>
                <th class="right" style="width: 15%;">Status Alokasi</th>
            </tr>
        </thead>
        <tbody>
            @php $dormIdx = 1; $totalKasKomplek = 0; $totalSantriKomplek = 0; $totalBillsKomplek = 0; @endphp
            @forelse($dormitory_breakdown as $dorm)
            @php 
                $totalKasKomplek += $dorm['total_amount']; 
                $totalSantriKomplek += $dorm['count_santri'];
                $totalBillsKomplek += ($dorm['count_bills'] ?? $dorm['count_santri']);
            @endphp
            <tr>
                <td style="text-align: center; color: #94a3b8;">{{ $dormIdx++ }}</td>
                <td>
                    <strong>{{ $dorm['dormitory_name'] }}</strong>
                </td>
                <td>
                    <span style="font-size: 8px; font-weight: bold; color: {{ $dorm['gender'] === 'L' ? '#0284c7' : '#db2777' }};">
                        {{ $dorm['gender'] === 'L' ? 'Komplek Putra' : 'Komplek Putri' }}
                    </span>
                </td>
                <td class="right">
                    <strong>{{ $dorm['count_santri'] }} Santri</strong>
                    <span style="font-size: 8px; color: #64748b;">({{ $dorm['count_bills'] ?? $dorm['count_santri'] }} Tagihan)</span>
                </td>
                <td class="right"><strong>Rp {{ number_format($dorm['total_amount'], 0, ',', '.') }}</strong></td>
                <td class="right" style="color: #059669; font-weight: bold; font-size: 8.5px;">Siap Diserahkan</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; color: #94a3b8; padding: 15px;">
                    Tidak ada transaksi Kas Komplek dalam periode ini.
                </td>
            </tr>
            @endforelse
            @if(count($dormitory_breakdown) > 0)
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">TOTAL KAS SELURUH KOMPLEK:</td>
                <td class="right"><strong>{{ $totalSantriKomplek }} Santri ({{ $totalBillsKomplek }} Tagihan)</strong></td>
                <td class="right"><strong>Rp {{ number_format($totalKasKomplek, 0, ',', '.') }}</strong></td>
                <td class="right">—</td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- TANDA TANGAN VERIFIKASI --}}
    <table class="sig-table">
        <tr>
            <td class="sig-box">
                <div>Dibuat / Dicatat Oleh,</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $generated_by }}</div>
                <div class="sig-title">Staf Keuangan / Kasir</div>
            </td>
            <td class="sig-box">
                <div>Diverifikasi Oleh,</div>
                <div class="sig-space"></div>
                <div class="sig-name">______________________</div>
                <div class="sig-title">Bendahara Pusat Pesantren</div>
            </td>
            <td class="sig-box">
                <div>Mengetahui,</div>
                <div class="sig-space"></div>
                <div class="sig-name">______________________</div>
                <div class="sig-title">Pengasuh / Pimpinan Pondok</div>
            </td>
        </tr>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        Dokumen rekapitulasi keuangan ini digenerate secara otomatis oleh sistem {{ $app_name }} pada {{ $generated_at }}.
    </div>

</body>
</html>
