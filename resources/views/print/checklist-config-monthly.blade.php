<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checklist Keuangan — {{ $config->label }}</title>
    @php
        $is24Months = isset($yearsHeader) && count($yearsHeader) > 1;
        $blankRowsCount = isset($extraBlankRows) ? max(0, (int)$extraBlankRows) : 0;
        $mode = $printMode ?? 'blank';
    @endphp
    <style>
        @page {
            size: {{ $paperSize === 'f4' ? '330mm 215mm' : '297mm 210mm' }};
            margin: {{ $is24Months ? '8mm 10mm' : '15mm 20mm' }};
        }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: {{ $is24Months ? '8px' : '9px' }};
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.3;
            background-color: #fff;
        }
        .page-container {
            {{ $pageBreakRoom ? 'page-break-after: always; break-after: page;' : '' }}
        }
        .page-container:last-child {
            page-break-after: avoid;
            break-after: avoid;
        }
        
        /* Premium Header Kop */
        .header {
            text-align: center;
            margin-bottom: {{ $is24Months ? '8px' : '15px' }};
            border-bottom: 3px double #0f172a;
            padding-bottom: {{ $is24Months ? '4px' : '8px' }};
            position: relative;
        }
        .header h1 {
            font-size: {{ $is24Months ? '15px' : '18px' }};
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 2px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 0;
            font-size: {{ $is24Months ? '9px' : '10px' }};
            color: #475569;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        /* Structured Meta Container */
        .meta-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: {{ $is24Months ? '4px 20px' : '6px 30px' }};
            margin-bottom: {{ $is24Months ? '10px' : '15px' }};
            border: 1px solid #cbd5e1;
            padding: {{ $is24Months ? '6px 12px' : '10px 18px' }};
            background-color: #f8fafc;
            border-radius: 6px;
        }
        .meta-item {
            display: flex;
            align-items: center;
            font-size: {{ $is24Months ? '8.5px' : '9.5px' }};
        }
        .meta-label {
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            font-size: {{ $is24Months ? '7.5px' : '8px' }};
            width: 110px;
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
            margin-bottom: {{ $is24Months ? '10px' : '20px' }};
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        table.grid th {
            border: 1px solid #475569;
            background-color: #f1f5f9;
            color: #0f172a;
            text-transform: uppercase;
            font-size: {{ $is24Months ? '7px' : '8px' }};
            font-weight: 800;
            text-align: center;
            padding: {{ $is24Months ? '3px 1px' : '8px 5px' }};
            letter-spacing: 0.2px;
        }
        table.grid td {
            border: 1px solid #cbd5e1;
            padding: {{ $is24Months ? '4px 3px' : '7px 8px' }};
            color: #334155;
            vertical-align: middle;
            font-size: {{ $is24Months ? '8px' : '9px' }};
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
        table.grid tr.room-header td {
            background-color: #e2e8f0 !important;
            color: #0f172a;
            font-weight: 800;
            font-size: {{ $is24Months ? '8.5px' : '9.5px' }};
            text-align: left;
            padding: {{ $is24Months ? '5px 8px' : '8px 12px' }};
            letter-spacing: 0.5px;
            border: 1.5px solid #475569;
        }

        /* Checkbox Design */
        .checkbox-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: {{ $is24Months ? '10px' : '12px' }};
            height: {{ $is24Months ? '10px' : '12px' }};
            border: 1.2px solid #334155;
            border-radius: 2px;
            background-color: #fff;
            vertical-align: middle;
            box-sizing: border-box;
        }
        .checkbox-box.checked {
            border-color: #16a34a;
            background-color: #dcfce7;
        }
        .checkbox-box.checked::after {
            content: "✓";
            font-size: {{ $is24Months ? '8.5px' : '10px' }};
            color: #15803d;
            font-weight: 900;
            line-height: 1;
            margin-top: -0.5px;
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
            margin-top: {{ $is24Months ? '12px' : '25px' }};
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
            font-size: 8.5px;
            color: #64748b;
            margin: 0;
        }
        .signature-title {
            font-size: {{ $is24Months ? '9px' : '10px' }};
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            margin: 2px 0 0 0;
            letter-spacing: 0.5px;
        }
        .signature-line {
            height: {{ $is24Months ? '32px' : '48px' }};
        }
        .signature-name {
            font-size: {{ $is24Months ? '8.5px' : '9.5px' }};
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
            table.grid tr.room-header td {
                background-color: #e2e8f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right; padding: 12px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-family: sans-serif;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between;">
            <span style="font-size: 12px; font-weight: bold; color: #475569;">Mode Cetak Kertas Checklist Keuangan ({{ $is24Months ? '24 Bulan / 2 Tahun' : '12 Bulan / 1 Tahun' }})</span>
            <button onclick="window.print()" style="padding: 8px 20px; font-size: 11px; font-weight: bold; cursor: pointer; background: #10b981; color: #fff; border: 0; border-radius: 6px; box-shadow: 0 2px 4px rgba(16,185,129,0.2); transition: all 0.2s;">🖨️ Cetak / Simpan PDF</button>
        </div>
    </div>

    @if($pageBreakRoom)
        {{-- Group by dormitory and room name, render separate sheets --}}
        @php
            $grouped = collect($gridData)->groupBy(fn($item) => $item['dormitory_name'] . '|' . $item['room_name']);
        @endphp
        @foreach($grouped as $key => $rows)
            @php
                [$dormName, $roomName] = explode('|', $key);
            @endphp
            <div class="page-container" style="{{ !$loop->first ? 'margin-top: 20px;' : '' }}">
                <div class="header">
                    <h1>Pondok Pesantren Al-Fithroh</h1>
                    <p>Buku Pedoman Keuangan Santri — Lembar Checklist Tagihan Bulanan ({{ $is24Months ? '24 Bulan / 2 Tahun' : '12 Bulan / 1 Tahun' }})</p>
                </div>

                <div class="meta-container">
                    <div class="meta-item">
                        <span class="meta-label">Komplek</span>
                        <span class="meta-value">: {{ $dormName }} (KAMAR: {{ strtoupper($roomName) }})</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Nama Iuran</span>
                        <span class="meta-value">: {{ $config->label }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Tipe Tagihan</span>
                        <span class="meta-value">: {{ strtoupper(str_replace('_', ' ', $config->type)) }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Tahun Buku</span>
                        <span class="meta-value">: {{ $year }}{{ $is24Months ? ' – ' . ($year + 1) : '' }}</span>
                    </div>
                </div>

                <table class="grid">
                    <thead>
                        @if($is24Months)
                            @php $yLoop = 0; @endphp
                            <tr>
                                <th style="width: 3%;" class="center" rowspan="2">No</th>
                                <th style="width: 18%; text-align: left;" class="border-dark" rowspan="2">Nama Lengkap Santri</th>
                                <th class="center border-dark" style="width: 7%;" rowspan="2">Tunggakan</th>
                                @foreach($yearsHeader as $yr => $colspan)
                                    @php $yLoop++; @endphp
                                    <th class="center border-dark" colspan="{{ $colspan }}"
                                        style="{{ $yLoop === 1 ? 'background-color: #f1f5f9; border-right: 2.5px solid #0f172a;' : 'background-color: #e2e8f0; color: #0f172a;' }}">
                                        TAHUN BUKU {{ $yr }}{{ $yLoop === 2 ? ' (LANJUTAN)' : '' }}
                                    </th>
                                @endforeach
                                <th class="center border-left-dark" style="width: 7%;" rowspan="2">Keterangan</th>
                            </tr>
                            <tr>
                                @php $mIdx = 0; @endphp
                                @foreach($months as $periodKey => $periodLabel)
                                    @php $mIdx++; @endphp
                                    <th class="center"
                                        style="width: 2.7%; {{ $mIdx == 12 ? 'border-right: 2.5px solid #0f172a; background-color: #f1f5f9;' : ($mIdx > 12 ? 'background-color: #e2e8f0;' : 'background-color: #f1f5f9;') }}">
                                        {{ $periodLabel }}
                                    </th>
                                @endforeach
                            </tr>
                        @else
                            <tr>
                                <th style="width: 4%;" class="center">No</th>
                                <th style="width: 25%; text-align: left;" class="border-dark">Nama Lengkap Santri</th>
                                <th class="center border-dark" style="width: 9%;">Tunggakan</th>
                                @foreach($months as $periodKey => $periodLabel)
                                    <th class="center" style="width: 4.5%;">{{ $periodLabel }}</th>
                                @endforeach
                                <th class="center border-left-dark" style="width: 8%;">Keterangan</th>
                            </tr>
                        @endif
                    </thead>
                    <tbody>
                        @foreach($rows as $i => $row)
                            <tr>
                                <td class="center" style="color: #64748b; font-weight: bold;">{{ $i + 1 }}</td>
                                <td style="font-weight: bold; font-size: {{ $is24Months ? '8.5px' : '9.5px' }}; color: #0f172a; white-space: nowrap;" class="border-dark">{{ $row['person']->name }}</td>
                                <td class="center font-bold border-dark" style="color: #b91c1c; white-space: nowrap;">
                                    @if($row['tunggakanLamaSum'] > 0)
                                        <span style="font-size: 7px; color: #475569; font-weight: 700;">{{ $row['tunggakanLamaCount'] ?? 1 }} Bln</span>
                                        <span style="font-size: 7.5px; color: #b91c1c; font-weight: 800;">Rp {{ number_format($row['tunggakanLamaSum'], 0, ',', '.') }}</span>
                                    @else
                                        <span style="color: #cbd5e1;">—</span>
                                    @endif
                                </td>
                                @php $colIdx = 0; @endphp
                                @foreach($months as $periodKey => $periodLabel)
                                    @php
                                        $colIdx++;
                                        $bill = $row['bills'][$periodKey] ?? null;
                                        $isYearDivider = ($is24Months && $colIdx == 12);
                                        $isYear2 = ($is24Months && $colIdx > 12);
                                    @endphp
                                    <td class="center font-bold"
                                        style="padding: 2px 1px; white-space: nowrap; {{ $isYearDivider ? 'border-right: 2.5px solid #0f172a;' : '' }} {{ $isYear2 ? 'background-color: #f8fafc;' : '' }}">
                                        @if($mode === 'history')
                                            @if($bill && $bill->status === 'paid')
                                                <span class="checkbox-box checked"></span>
                                            @elseif($bill && $bill->amount_paid > 0)
                                                <span style="font-size: {{ $is24Months ? '6.5px' : '7.5px' }}; color: #16a34a; font-weight: 800; white-space: nowrap; display: inline-block;">
                                                    Rp {{ number_format($bill->amount_paid, 0, ',', '.') }}
                                                </span>
                                            @else
                                                <span class="checkbox-box"></span>
                                            @endif
                                        @else
                                            <span class="checkbox-box"></span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="center border-left-dark" style="color: #15803d; font-weight: bold; font-size: 8px;">
                                    @if($row['lunasDiMukaLabel'])
                                        {{ $row['lunasDiMukaLabel'] }}
                                    @else
                                        <span style="color: #cbd5e1; font-weight: normal;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        {{-- Extra Blank Rows --}}
                        @if($blankRowsCount > 0)
                            @for($b = 1; $b <= $blankRowsCount; $b++)
                                <tr>
                                    <td class="center" style="color: #94a3b8; font-weight: bold;">{{ count($rows) + $b }}</td>
                                    <td class="border-dark">&nbsp;</td>
                                    <td class="center border-dark" style="color: #cbd5e1;">—</td>
                                    @php $bColIdx = 0; @endphp
                                    @foreach($months as $periodKey => $periodLabel)
                                        @php
                                            $bColIdx++;
                                            $isYearDivider = ($is24Months && $bColIdx == 12);
                                            $isYear2 = ($is24Months && $bColIdx > 12);
                                        @endphp
                                        <td class="center" style="{{ $isYearDivider ? 'border-right: 2.5px solid #0f172a;' : '' }} {{ $isYear2 ? 'background-color: #f8fafc;' : '' }}">
                                            <span class="checkbox-box"></span>
                                        </td>
                                    @endforeach
                                    <td class="center border-left-dark">&nbsp;</td>
                                </tr>
                            @endfor
                        @endif
                    </tbody>
                </table>

                <div class="signature-container">
                    <div class="signature-box">
                        <span class="signature-role">Diserahkan Oleh,</span>
                        <span class="signature-title">Bendahara Komplek</span>
                        <div class="signature-line"></div>
                        <p class="signature-name">( .................................... )</p>
                    </div>
                    <div class="signature-box">
                        <span class="signature-role">Diperiksa Oleh,</span>
                        <span class="signature-title">Musyrif Komplek</span>
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
    @else
        {{-- Unified page layout --}}
        <div class="page-container">
            <div class="header">
                <h1>Pondok Pesantren Al-Fithroh</h1>
                <p>Buku Pedoman Keuangan Santri — Lembar Checklist Tagihan Bulanan ({{ $is24Months ? '24 Bulan / 2 Tahun' : '12 Bulan / 1 Tahun' }})</p>
            </div>

            <div class="meta-container">
                @if($config->target_type === 'kelas')
                    <div class="meta-item">
                        <span class="meta-label">Kelas</span>
                        <span class="meta-value">: {{ $kelasList->pluck('name')->implode(', ') }}</span>
                    </div>
                @else
                    <div class="meta-item">
                        <span class="meta-label">Komplek</span>
                        <span class="meta-value">: {{ $dormitories->pluck('name')->implode(', ') }}</span>
                    </div>
                @endif
                <div class="meta-item">
                    <span class="meta-label">Nama Iuran</span>
                    <span class="meta-value">: {{ $config->label }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Tipe Tagihan</span>
                    <span class="meta-value">: {{ strtoupper(str_replace('_', ' ', $config->type)) }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Tahun Buku</span>
                    <span class="meta-value">: {{ $year }}{{ $is24Months ? ' – ' . ($year + 1) : '' }}</span>
                </div>
            </div>

            <table class="grid">
                <thead>
                    @if($is24Months)
                        @php $yLoop = 0; @endphp
                        <tr>
                            <th style="width: 3%;" class="center" rowspan="2">No</th>
                            <th style="width: 18%; text-align: left;" class="border-dark" rowspan="2">Nama Lengkap Santri</th>
                            <th class="center border-dark" style="width: 7%;" rowspan="2">Tunggakan</th>
                            @foreach($yearsHeader as $yr => $colspan)
                                @php $yLoop++; @endphp
                                <th class="center border-dark" colspan="{{ $colspan }}"
                                    style="{{ $yLoop === 1 ? 'background-color: #f1f5f9; border-right: 2.5px solid #0f172a;' : 'background-color: #e2e8f0; color: #0f172a;' }}">
                                    TAHUN BUKU {{ $yr }}{{ $yLoop === 2 ? ' (LANJUTAN)' : '' }}
                                </th>
                            @endforeach
                            <th class="center border-left-dark" style="width: 7%;" rowspan="2">Keterangan</th>
                        </tr>
                        <tr>
                            @php $mIdx = 0; @endphp
                            @foreach($months as $periodKey => $periodLabel)
                                @php $mIdx++; @endphp
                                <th class="center"
                                    style="width: 2.7%; {{ $mIdx == 12 ? 'border-right: 2.5px solid #0f172a; background-color: #f1f5f9;' : ($mIdx > 12 ? 'background-color: #e2e8f0;' : 'background-color: #f1f5f9;') }}">
                                    {{ $periodLabel }}
                                </th>
                            @endforeach
                        </tr>
                    @else
                        <tr>
                            <th style="width: 4%;" class="center">No</th>
                            <th style="width: 25%; text-align: left;" class="border-dark">Nama Lengkap Santri</th>
                            <th class="center border-dark" style="width: 9%;">Tunggakan</th>
                            @foreach($months as $periodKey => $periodLabel)
                                <th class="center" style="width: 4.5%;">{{ $periodLabel }}</th>
                            @endforeach
                            <th class="center border-left-dark" style="width: 8%;">Keterangan</th>
                        </tr>
                    @endif
                </thead>
                <tbody>
                    @php $currentGroup = null; $counter = 1; @endphp
                    @foreach($gridData as $row)
                        @php
                            if ($config->target_type === 'kelas') {
                                $groupKey = $row['kelas_name'] ?? 'Tanpa Kelas';
                                $groupLabel = "🏫 KELAS: " . strtoupper($groupKey) . ($row['kelas_jenjang'] ? " (" . strtoupper($row['kelas_jenjang']) . ")" : "");
                            } else {
                                $roomName = $row['room_name'] ?? 'Tanpa Kamar';
                                $dormName = $row['dormitory_name'] ?? '';
                                $groupKey = $dormName . ' - ' . $roomName;
                                $groupLabel = "🏢 " . strtoupper($dormName) . " — KAMAR: " . strtoupper($roomName);
                            }
                        @endphp
                        @if($currentGroup !== $groupKey)
                            @if($currentGroup !== null && $blankRowsCount > 0)
                                @for($b = 1; $b <= $blankRowsCount; $b++)
                                    <tr>
                                        <td class="center" style="color: #94a3b8; font-weight: bold;">{{ $counter++ }}</td>
                                        <td class="border-dark">&nbsp;</td>
                                        <td class="center border-dark" style="color: #cbd5e1;">—</td>
                                        @php $bColIdx = 0; @endphp
                                        @foreach($months as $periodKey => $periodLabel)
                                            @php
                                                $bColIdx++;
                                                $isYearDivider = ($is24Months && $bColIdx == 12);
                                                $isYear2 = ($is24Months && $bColIdx > 12);
                                            @endphp
                                            <td class="center" style="{{ $isYearDivider ? 'border-right: 2.5px solid #0f172a;' : '' }} {{ $isYear2 ? 'background-color: #f8fafc;' : '' }}">
                                                <span class="checkbox-box"></span>
                                            </td>
                                        @endforeach
                                        <td class="center border-left-dark">&nbsp;</td>
                                    </tr>
                                @endfor
                            @endif

                            <tr class="room-header">
                                <td colspan="{{ 4 + count($months) }}">
                                    {{ $groupLabel }}
                                </td>
                            </tr>
                            @php $currentGroup = $groupKey; @endphp
                        @endif
                        <tr>
                            <td class="center" style="color: #64748b; font-weight: bold;">{{ $counter++ }}</td>
                            <td style="font-weight: bold; font-size: {{ $is24Months ? '8.5px' : '9.5px' }}; color: #0f172a; white-space: nowrap;" class="border-dark">{{ $row['person']->name }}</td>
                            <td class="center font-bold border-dark" style="color: #b91c1c; white-space: nowrap;">
                                @if($row['tunggakanLamaSum'] > 0)
                                    <span style="font-size: 7px; color: #475569; font-weight: 700;">{{ $row['tunggakanLamaCount'] ?? 1 }} Bln</span>
                                    <span style="font-size: 7.5px; color: #b91c1c; font-weight: 800;">Rp {{ number_format($row['tunggakanLamaSum'], 0, ',', '.') }}</span>
                                @else
                                    <span style="color: #cbd5e1;">—</span>
                                @endif
                            </td>
                            @php $colIdx = 0; @endphp
                            @foreach($months as $periodKey => $periodLabel)
                                @php
                                    $colIdx++;
                                    $bill = $row['bills'][$periodKey] ?? null;
                                    $isYearDivider = ($is24Months && $colIdx == 12);
                                    $isYear2 = ($is24Months && $colIdx > 12);
                                @endphp
                                <td class="center font-bold"
                                    style="padding: 2px 1px; white-space: nowrap; {{ $isYearDivider ? 'border-right: 2.5px solid #0f172a;' : '' }} {{ $isYear2 ? 'background-color: #f8fafc;' : '' }}">
                                    @if($mode === 'history')
                                        @if($bill && $bill->status === 'paid')
                                            <span class="checkbox-box checked"></span>
                                        @elseif($bill && $bill->amount_paid > 0)
                                            <span style="font-size: {{ $is24Months ? '6.5px' : '7.5px' }}; color: #16a34a; font-weight: 800; white-space: nowrap; display: inline-block;">
                                                Rp {{ number_format($bill->amount_paid, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="checkbox-box"></span>
                                        @endif
                                    @else
                                        <span class="checkbox-box"></span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="center border-left-dark" style="color: #15803d; font-weight: bold; font-size: 8px;">
                                @if($row['lunasDiMukaLabel'])
                                    {{ $row['lunasDiMukaLabel'] }}
                                @else
                                    <span style="color: #cbd5e1; font-weight: normal;">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    @if($blankRowsCount > 0)
                        @for($b = 1; $b <= $blankRowsCount; $b++)
                            <tr>
                                <td class="center" style="color: #94a3b8; font-weight: bold;">{{ $counter++ }}</td>
                                <td class="border-dark">&nbsp;</td>
                                <td class="center border-dark" style="color: #cbd5e1;">—</td>
                                @php $bColIdx = 0; @endphp
                                @foreach($months as $periodKey => $periodLabel)
                                    @php
                                        $bColIdx++;
                                        $isYearDivider = ($is24Months && $bColIdx == 12);
                                        $isYear2 = ($is24Months && $bColIdx > 12);
                                    @endphp
                                    <td class="center" style="{{ $isYearDivider ? 'border-right: 2.5px solid #0f172a;' : '' }} {{ $isYear2 ? 'background-color: #f8fafc;' : '' }}">
                                        <span class="checkbox-box"></span>
                                    </td>
                                @endforeach
                                <td class="center border-left-dark">&nbsp;</td>
                            </tr>
                        @endfor
                    @endif
                </tbody>
            </table>

            <div class="signature-container">
                <div class="signature-box">
                    <span class="signature-role">Diserahkan Oleh,</span>
                    <span class="signature-title">Bendahara Komplek</span>
                    <div class="signature-line"></div>
                    <p class="signature-name">( .................................... )</p>
                </div>
                <div class="signature-box">
                    <span class="signature-role">Diperiksa Oleh,</span>
                    <span class="signature-title">Musyrif Komplek</span>
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
    @endif
</body>
</html>
