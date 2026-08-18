<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Slip Serah Terima Kas Komplek - {{ $dormitory->name }}</title>
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
        .inst-title { font-size: 16px; font-weight: bold; color: #0f172a; }
        .inst-sub { font-size: 9px; color: #64748b; margin-top: 2px; }
        .doc-title { font-size: 13px; font-weight: bold; color: #0f172a; text-align: right; }
        .doc-sub { font-size: 9px; color: #64748b; text-align: right; margin-top: 2px; }

        /* ── BANNER KOMPLEK ── */
        .dorm-banner {
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 16px;
        }
        .dorm-banner table { width: 100%; border-collapse: collapse; }
        .dorm-name { font-size: 14px; font-weight: bold; color: #0f172a; }
        .dorm-unit { font-size: 10px; color: #64748b; margin-top: 2px; }
        .dorm-total-label { font-size: 9px; text-transform: uppercase; color: #64748b; text-align: right; font-weight: bold; }
        .dorm-total-val { font-size: 16px; font-weight: bold; color: #047857; text-align: right; font-family: "DejaVu Sans Mono", monospace; margin-top: 2px; }

        /* ── TABLE SANTRI ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th {
            background: #f1f5f9;
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
            font-size: 9px;
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
            padding: 10px 14px;
            margin-top: 15px;
            margin-bottom: 15px;
            font-size: 9px;
            line-height: 1.6;
        }

        /* ── SIGNATURES ── */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .sig-box { width: 50%; text-align: center; font-size: 9px; vertical-align: top; }
        .sig-space { height: 55px; }
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
                    <div class="doc-title">SLIP SERAH TERIMA KAS KOMPLEK</div>
                    <div class="doc-sub">Periode: <strong>{{ $period_label }}</strong></div>
                </td>
            </tr>
        </table>
    </div>

    {{-- BANNER KOMPLEK --}}
    <div class="dorm-banner">
        <table>
            <tr>
                <td>
                    <div class="dorm-name">🏠 {{ $dormitory->name }}</div>
                    <div class="dorm-unit">Unit: {{ $dormitory->gender === 'L' ? 'Putra' : 'Putri' }} &bull; Total Santri Membayar: <strong>{{ count($santri_list) }} Santri</strong></div>
                </td>
                <td>
                    <div class="dorm-total-label">Total Dana Kas Diserahkan</div>
                    <div class="dorm-total-val">Rp {{ number_format($total_amount, 0, ',', '.') }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- PERNYATAAN SERAH TERIMA --}}
    <div class="handover-box">
        Telah diserahkan dana titipan <strong>Kas Komplek {{ $dormitory->name }}</strong> dari Bendahara Pusat Pesantren kepada Pengurus/Bendahara Komplek sebesar <strong>Rp {{ number_format($total_amount, 0, ',', '.') }}</strong> ({{ count($santri_list) }} santri terlampir di bawah) untuk dipergunakan sebagaimana mestinya sesuai ketentuan pengelolaan komplek.
    </div>

    {{-- DAFTAR SANTRI --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 15%;">NIS</th>
                <th>Nama Santri</th>
                <th style="width: 15%;">Kamar</th>
                <th style="width: 15%;">Tgl Bayar</th>
                <th style="width: 15%;">Metode</th>
                <th class="right" style="width: 18%;">Jumlah Kas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($santri_list as $i => $s)
            <tr>
                <td style="text-align: center; color: #94a3b8;">{{ $i + 1 }}</td>
                <td style="font-family: monospace;">{{ $s['nis'] ?? '-' }}</td>
                <td><strong>{{ $s['name'] }}</strong></td>
                <td>{{ $s['room_name'] ?? '-' }}</td>
                <td>{{ $s['paid_date'] ?? '-' }}</td>
                <td>{{ $s['method'] ?? 'Online' }}</td>
                <td class="right"><strong>Rp {{ number_format($s['amount'], 0, ',', '.') }}</strong></td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6" style="text-align: right;">TOTAL KAS KOMPLEK {{ strtoupper($dormitory->name) }}:</td>
                <td class="right"><strong>Rp {{ number_format($total_amount, 0, ',', '.') }}</strong></td>
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
                <div class="sig-title">Lurah / Bendahara {{ $dormitory->name }}</div>
            </td>
        </tr>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        Dicetak pada {{ $generated_at }} melalui Sistem Keuangan {{ $app_name }}.
    </div>

</body>
</html>
