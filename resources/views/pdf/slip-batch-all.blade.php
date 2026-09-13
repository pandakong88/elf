<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Batch Slip Serah Terima Dana Kas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            background: #ffffff;
        }

        .slip-page {
            padding: 25px 30px;
            page-break-after: always;
        }
        .slip-page:last-child {
            page-break-after: auto;
        }

        /* ── HEADER ── */
        .header {
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .inst-title { font-size: 15px; font-weight: bold; color: #0f172a; }
        .inst-sub { font-size: 8.5px; color: #64748b; margin-top: 2px; }
        .doc-title { font-size: 12px; font-weight: bold; color: #0f172a; text-align: right; }
        .doc-sub { font-size: 8.5px; color: #64748b; text-align: right; margin-top: 2px; }

        /* ── BANNER KATEGORI ── */
        .category-banner {
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 14px;
        }
        .category-banner table { width: 100%; border-collapse: collapse; }
        .cat-name { font-size: 13px; font-weight: bold; color: #0f172a; }
        .cat-unit { font-size: 9px; color: #64748b; margin-top: 2px; }
        .cat-total-label { font-size: 8.5px; text-transform: uppercase; color: #64748b; text-align: right; font-weight: bold; }
        .cat-total-val { font-size: 15px; font-weight: bold; color: #047857; text-align: right; font-family: "DejaVu Sans Mono", monospace; margin-top: 2px; }

        /* ── TABLE SANTRI ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .data-table th {
            background: #f1f5f9;
            border-bottom: 1.5px solid #cbd5e1;
            padding: 5px 7px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            color: #475569;
            text-align: left;
        }
        .data-table th.right { text-align: right; }
        .data-table td {
            padding: 5px 7px;
            font-size: 8.5px;
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

        /* ── TANDA TERIMA ── */
        .handover-box {
            background: #fdfdfd;
            border: 1px dashed #94a3b8;
            border-radius: 6px;
            padding: 8px 12px;
            margin-top: 10px;
            margin-bottom: 12px;
            font-size: 8.5px;
            line-height: 1.5;
        }

        /* ── SIGNATURES ── */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            page-break-inside: avoid;
        }
        .sig-box { width: 50%; text-align: center; font-size: 8.5px; vertical-align: top; }
        .sig-space { height: 45px; }
        .sig-name { font-weight: bold; text-decoration: underline; }
        .sig-title { color: #64748b; font-size: 8px; margin-top: 2px; }

        /* ── FOOTER ── */
        .footer {
            margin-top: 14px;
            padding-top: 6px;
            border-top: 1px solid #e2e8f0;
            font-size: 7.5px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

@forelse($slips as $idx => $slip)
    <div class="slip-page">
        {{-- HEADER --}}
        <div class="header">
            <table class="header-table">
                <tr>
                    <td>
                        <div class="inst-title">{{ strtoupper($app_name) }}</div>
                        <div class="inst-sub">Sistem Manajemen Keuangan & Pembukuan Pesantren</div>
                    </td>
                    <td>
                        <div class="doc-title">SLIP SERAH TERIMA DANA ({{ $idx + 1 }}/{{ count($slips) }})</div>
                        <div class="doc-sub">Periode: <strong>{{ $period_label }}</strong></div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- BANNER KATEGORI --}}
        <div class="category-banner">
            <table>
                <tr>
                    <td>
                        <div class="cat-name">{{ $slip['meta']['title'] }}</div>
                        <div class="cat-unit">Peruntukan: <strong>{{ $slip['meta']['unit_label'] }}</strong> &bull; Total: <strong>{{ count($slip['santri_list']) }} Data Santri</strong></div>
                    </td>
                    <td>
                        <div class="cat-total-label">Total Dana Diserahkan</div>
                        <div class="cat-total-val">Rp {{ number_format($slip['total_amount'], 0, ',', '.') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- PERNYATAAN SERAH TERIMA --}}
        <div class="handover-box">
            Telah diserahkan dana penerimaan <strong>{{ $slip['meta']['title'] }}</strong> dari Bendahara Pusat Pesantren kepada <strong>{{ $slip['meta']['recipient_role'] }}</strong> sebesar <strong>Rp {{ number_format($slip['total_amount'], 0, ',', '.') }}</strong> ({{ count($slip['santri_list']) }} santri terlampir) untuk dicatatkan dan dipergunakan sesuai dengan peruntukannya.
        </div>

        {{-- DAFTAR SANTRI --}}
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;">#</th>
                    <th style="width: 12%;">NIS</th>
                    <th>Nama Santri</th>
                    <th style="width: 12%;">Kamar/Unit</th>
                    <th style="width: 20%;">Rincian / Periode</th>
                    <th style="width: 13%;">Tgl Bayar</th>
                    <th style="width: 13%;">Metode</th>
                    <th class="right" style="width: 16%;">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($slip['santri_list'] as $i => $s)
                <tr>
                    <td style="text-align: center; color: #94a3b8;">{{ $i + 1 }}</td>
                    <td style="font-family: monospace;">{{ $s['nis'] ?? '-' }}</td>
                    <td><strong>{{ $s['name'] }}</strong></td>
                    <td>{{ $s['unit_info'] ?? $s['room_name'] ?? '-' }}</td>
                    <td><span style="color: #0369a1; font-weight: bold;">{{ $s['period_label'] ?? $slip['meta']['title'] }}</span></td>
                    <td>{{ $s['paid_date'] ?? '-' }}</td>
                    <td>{{ $s['method'] ?? 'Online' }}</td>
                    <td class="right"><strong>Rp {{ number_format($s['amount'], 0, ',', '.') }}</strong></td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 15px; color: #94a3b8;">Tidak ada santri yang membayar pos ini pada periode tanggal yang dipilih.</td>
                </tr>
                @endforelse
                <tr class="total-row">
                    <td colspan="7" style="text-align: right;">TOTAL DISERAHKAN:</td>
                    <td class="right"><strong>Rp {{ number_format($slip['total_amount'], 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        {{-- TANDA TANGAN SERAH TERIMA --}}
        <table class="sig-table">
            <tr>
                <td class="sig-box">
                    <div>Yang Menyerahkan,</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $generated_by }}</div>
                    <div class="sig-title">Bendahara Pusat Pesantren</div>
                </td>
                <td class="sig-box">
                    <div>Yang Menerima,</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">______________________</div>
                    <div class="sig-title">{{ $slip['meta']['recipient_role'] }}</div>
                </td>
            </tr>
        </table>

        {{-- FOOTER --}}
        <div class="footer">
            Dokumen {{ $idx + 1 }} dari {{ count($slips) }} &bull; Dicetak pada {{ $generated_at }} melalui Sistem Keuangan {{ $app_name }}. Dokumen sah tanda serah terima kas internal.
        </div>
    </div>
@empty
    <div style="text-align: center; padding: 50px; font-size: 14px; color: #64748b;">
        Tidak ada transaksi dana masuk pada periode ini.
    </div>
@endforelse

</body>
</html>
