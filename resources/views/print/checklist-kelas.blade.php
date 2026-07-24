<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checklist Keuangan Kelas</title>
    <style>
        @page {
            size: {{ $paperSize === 'f4' ? '215mm 330mm' : 'A4 portrait' }};
            margin: 15mm 20mm;
        }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.4;
            background-color: #fff;
        }
        
        /* Premium Header Kop */
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px double #0f172a;
            padding-bottom: 8px;
            position: relative;
        }
        .header h1 {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 3px 0;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .header p {
            margin: 0;
            font-size: 10px;
            color: #475569;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        /* Structured Meta Container */
        .meta-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 30px;
            margin-bottom: 15px;
            border: 1px solid #cbd5e1;
            padding: 10px 18px;
            background-color: #f8fafc;
            border-radius: 6px;
        }
        .meta-item {
            display: flex;
            align-items: center;
            font-size: 9.5px;
        }
        .meta-label {
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            font-size: 8px;
            width: 120px;
            letter-spacing: 0.5px;
            flex-shrink: 0;
        }
        .meta-value {
            font-weight: 700;
            color: #0f172a;
        }

        /* Table Design */
        table.grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        table.grid th {
            border: 1px solid #475569;
            background-color: #f1f5f9;
            color: #0f172a;
            text-transform: uppercase;
            font-size: 8.5px;
            font-weight: 800;
            text-align: center;
            padding: 8px 6px;
            letter-spacing: 0.5px;
        }
        table.grid td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            color: #334155;
            vertical-align: middle;
        }
        table.grid th.border-dark, table.grid td.border-dark {
            border-right: 1.5px solid #475569;
        }
        table.grid th.border-left-dark, table.grid td.border-left-dark {
            border-left: 1.5px solid #475569;
        }
        table.grid td.center, table.grid th.center {
            text-align: center;
        }
        table.grid tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Checkbox Design */
        .checkbox-box {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1.5px solid #475569;
            border-radius: 2px;
            position: relative;
            background-color: #fff;
        }
        .checkbox-box.checked {
            border-color: #16a34a;
            background-color: #f0fdf4;
        }
        .checkbox-box.checked::after {
            content: "✓";
            position: absolute;
            top: -4px;
            left: 1px;
            font-size: 11px;
            color: #16a34a;
            font-weight: 900;
        }

        /* Repeat headers */
        thead {
            display: table-header-group;
        }
        tr {
            page-break-inside: avoid;
        }

        /* Premium Signatures */
        .signature-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .signature-role {
            font-size: 9px;
            color: #64748b;
            margin: 0;
        }
        .signature-title {
            font-size: 10px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            margin: 2px 0 0 0;
            letter-spacing: 0.5px;
        }
        .signature-line {
            height: 52px;
        }
        .signature-name {
            font-size: 9.5px;
            color: #334155;
            font-weight: bold;
            margin: 0;
        }

        @media print {
            .no-print { display: none; }
            body {
                background: #fff;
            }
            .meta-container {
                border: 1px solid #475569 !important;
                background-color: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            table.grid th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right; padding: 12px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-family: sans-serif;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between;">
            <span style="font-size: 12px; font-weight: bold; color: #475569;">Mode Cetak Kertas Checklist Kelas (Madrasah)</span>
            <button onclick="window.print()" style="padding: 8px 20px; font-size: 11px; font-weight: bold; cursor: pointer; background: #10b981; color: #fff; border: 0; border-radius: 6px; box-shadow: 0 2px 4px rgba(16,185,129,0.2); transition: all 0.2s;">🖨️ Cetak / Simpan PDF</button>
        </div>
    </div>

    @php
        $grouped = collect($gridData)->groupBy('kelas_name');
    @endphp
    @foreach($grouped as $kelasName => $rows)
        @php
            $firstRow = $rows->first();
            $kelasJenjang = $firstRow['kelas_jenjang'];
            $kelasAcademicYear = $firstRow['kelas_academic_year'];
        @endphp
        <div class="page-container" style="{{ !$loop->first ? 'margin-top: 30px; page-break-before: always; break-before: page;' : '' }}">
            <div class="header">
                <h1>Pondok Pesantren Al-Fithroh</h1>
                <p>Buku Pedoman Keuangan Santri — Lembar Checklist Tagihan Madrasah (Kelas)</p>
            </div>

            <div class="meta-container">
                <div class="meta-item">
                    <span class="meta-label">Kelas / Jenjang</span>
                    <span class="meta-value">: {{ $kelasName }} / {{ $kelasJenjang }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Tahun Ajaran</span>
                    <span class="meta-value">: {{ $kelasAcademicYear }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Tipe Tagihan</span>
                    <span class="meta-value">: {{ strtoupper(str_replace('_', ' ', $billType)) }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Tanggal Cetak</span>
                    <span class="meta-value">: {{ now()->format('d-m-Y H:i') }}</span>
                </div>
            </div>

            <table class="grid">
                <thead>
                    <tr>
                        <th style="width: 5%;" class="center">No</th>
                        <th style="width: 40%; text-align: left;" class="border-dark">Nama Lengkap Santri</th>
                        <th class="center border-dark" style="width: 17%;">Tunggakan Lama</th>
                        @foreach($periods as $periodKey => $periodLabel)
                            <th class="center" style="width: 15%;">{{ $periodLabel }}</th>
                        @endforeach
                        <th class="center border-left-dark" style="width: 13%;">Lunas di Muka</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $i => $row)
                        <tr>
                            <td class="center" style="color: #64748b; font-weight: bold;">{{ $i + 1 }}</td>
                            <td style="font-weight: bold; color: #0f172a;" class="border-dark">{{ $row['person']->name }}</td>
                            <td class="center font-bold border-dark" style="color: #b91c1c;">
                                @if($row['tunggakanLamaSum'] > 0)
                                    Rp {{ number_format($row['tunggakanLamaSum'], 0, ',', '.') }}
                                @else
                                    <span style="color: #cbd5e1;">—</span>
                                @endif
                            </td>
                            @foreach($row['bills'] as $periodKey => $bill)
                                <td class="center font-bold">
                                    @if(!$bill)
                                        <span style="color: #cbd5e1; font-weight: normal;">—</span>
                                    @elseif($bill->status === 'paid')
                                        <span class="checkbox-box checked"></span>
                                    @else
                                        <span class="checkbox-box"></span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="center border-left-dark" style="color: #15803d; font-weight: bold; font-size: 9px;">
                                @if($row['lunasDiMukaLabel'])
                                    {{ $row['lunasDiMukaLabel'] }}
                                @else
                                    <span style="color: #cbd5e1;">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="signature-container">
                <div class="signature-box">
                    <span class="signature-role">Diserahkan Oleh,</span>
                    <span class="signature-title">Bendahara Kelas</span>
                    <div class="signature-line"></div>
                    <p class="signature-name">( .................................... )</p>
                </div>
                <div class="signature-box">
                    <span class="signature-role">Diperiksa Oleh,</span>
                    <span class="signature-title">Wali Kelas</span>
                    <div class="signature-line"></div>
                    <p class="signature-name">( .................................... )</p>
                </div>
                <div class="signature-box">
                    <span class="signature-role">Diterima Oleh,</span>
                    <span class="signature-title">Bendahara Pusat</span>
                    <div class="signature-line"></div>
                    <p class="signature-name">( .................................... )</p>
                </div>
            </div>
        </div>
    @endforeach
</body>
</html>
