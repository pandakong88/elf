<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checklist Keuangan — {{ $config->label }}</title>
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
        table.grid tr.room-header td {
            background-color: #e2e8f0 !important;
            color: #0f172a;
            font-weight: 800;
            font-size: 10px;
            text-align: left;
            padding: 8px 12px;
            letter-spacing: 0.5px;
            border: 1.5px solid #475569;
        }

        /* Write lines for manual handwriting */
        .write-lines {
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 2px 0;
        }
        .write-line {
            border-bottom: 1.5px dotted #cbd5e1;
            height: 11px;
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
            margin-top: 25px;
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
            height: 48px;
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
            <span style="font-size: 12px; font-weight: bold; color: #475569;">Mode Cetak Kertas Checklist Keuangan (Sekali Bayar / Tahunan)</span>
            <button onclick="window.print()" style="padding: 8px 20px; font-size: 11px; font-weight: bold; cursor: pointer; background: #10b981; color: #fff; border: 0; border-radius: 6px; box-shadow: 0 2px 4px rgba(16,185,129,0.2); transition: all 0.2s;">🖨️ Cetak / Simpan PDF</button>
        </div>
    </div>

    @if($pageBreakRoom)
        {{-- Group by class or dormitory/room based on target_type --}}
        @php
            if ($config->target_type === 'kelas') {
                $grouped = collect($gridData)->groupBy('kelas_name');
            } else {
                $grouped = collect($gridData)->groupBy(fn($item) => $item['dormitory_name'] . '|' . $item['room_name']);
            }
        @endphp
        @foreach($grouped as $key => $rows)
            @php
                if ($config->target_type === 'kelas') {
                    $kelasName = $key;
                    $firstRow = $rows->first();
                    $headerLabel = "Kelas: " . $kelasName . ($firstRow['kelas_jenjang'] ? " (" . strtoupper($firstRow['kelas_jenjang']) . ")" : "");
                    $metaLabelName = "Kelas / Jenjang";
                    $metaValueString = $kelasName . ($firstRow['kelas_jenjang'] ? " / " . strtoupper($firstRow['kelas_jenjang']) : "");
                } else {
                    [$dormName, $roomName] = explode('|', $key);
                    $headerLabel = $dormName . " (KAMAR: " . strtoupper($roomName) . ")";
                    $metaLabelName = "Komplek";
                    $metaValueString = $dormName . " (KAMAR: " . strtoupper($roomName) . ")";
                }
            @endphp
            <div class="page-container" style="{{ !$loop->first ? 'margin-top: 20px;' : '' }}">
                <div class="header">
                    <h1>Pondok Pesantren Al-Fithroh</h1>
                    <p>Buku Pedoman Keuangan Santri — Lembar Checklist Tagihan Khusus</p>
                </div>

                <div class="meta-container">
                    <div class="meta-item">
                        <span class="meta-label">{{ $metaLabelName }}</span>
                        <span class="meta-value">: {{ $metaValueString }}</span>
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
                        <span class="meta-label">Interval</span>
                        <span class="meta-value">: {{ strtoupper($config->interval) }}</span>
                    </div>
                </div>

                <table class="grid">
                    <thead>
                        <tr>
                            <th style="width: 4%;" class="center">No</th>
                            <th style="width: 28%; text-align: left;" class="border-dark">Nama Lengkap Santri</th>
                            <th class="center border-dark" style="width: 14%;">Tagihan</th>
                            <th class="center border-dark" style="width: 21%;">Bayar I (Nominal & Paraf)</th>
                            <th class="center border-dark" style="width: 21%;">Bayar II / Pelunasan</th>
                            <th class="center border-left-dark" style="width: 12%;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $i => $row)
                            @php $bill = $row['bills']['single'] ?? null; @endphp
                            <tr>
                                <td class="center" style="color: #64748b; font-weight: bold;">{{ $i + 1 }}</td>
                                <td style="font-weight: bold; font-size: 10px; color: #0f172a; white-space: nowrap;" class="border-dark">{{ $row['person']->name }}</td>
                                <td class="center font-bold border-dark">
                                    Rp {{ number_format($bill ? $bill->amount : ($row['expectedAmount'] ?? $config->amount), 0, ',', '.') }}
                                </td>
                                @if($bill && $bill->status === 'paid')
                                    <td colspan="2" class="center font-bold border-dark" style="color: #16a34a; font-weight: 800;">
                                        — LUNAS DI SISTEM —
                                    </td>
                                @else
                                    <td class="center border-dark" style="color: #cbd5e1; font-size: 8px; vertical-align: bottom; padding-bottom: 4px;">
                                        Rp .......................... (Paraf)
                                    </td>
                                    <td class="center border-dark" style="color: #cbd5e1; font-size: 8px; vertical-align: bottom; padding-bottom: 4px;">
                                        Rp .......................... (Paraf)
                                    </td>
                                @endif
                                <td class="center border-left-dark" style="color: #64748b; font-size: 8.5px;">
                                    @if(!empty($row['exceptionNote']))
                                        <span style="color: #d97706; font-weight: bold;">{{ $row['exceptionNote'] }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
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
                <p>Buku Pedoman Keuangan Santri — Lembar Checklist Tagihan Khusus</p>
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
            </div>

            <table class="grid">
                <thead>
                    <tr>
                        <th style="width: 4%;" class="center">No</th>
                        <th style="width: 28%; text-align: left;" class="border-dark">Nama Lengkap Santri</th>
                        <th class="center border-dark" style="width: 14%;">Tagihan</th>
                        <th class="center border-dark" style="width: 21%;">Bayar I (Nominal & Paraf)</th>
                        <th class="center border-dark" style="width: 21%;">Bayar II / Pelunasan</th>
                        <th class="center border-left-dark" style="width: 12%;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $currentGroup = null; @endphp
                    @foreach($gridData as $i => $row)
                        @php 
                            $bill = $row['bills']['single'] ?? $row['bills'][0] ?? null;
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
                            <tr class="room-header">
                                <td colspan="6">
                                    {{ $groupLabel }}
                                </td>
                            </tr>
                            @php $currentGroup = $groupKey; @endphp
                        @endif
                        <tr>
                            <td class="center" style="color: #64748b; font-weight: bold;">{{ $i + 1 }}</td>
                            <td style="font-weight: bold; font-size: 10px; color: #0f172a; white-space: nowrap;" class="border-dark">{{ $row['person']->name }}</td>
                            <td class="center font-bold border-dark">
                                Rp {{ number_format($bill ? $bill->amount : ($row['expectedAmount'] ?? $config->amount), 0, ',', '.') }}
                            </td>
                            @if($bill && $bill->status === 'paid')
                                <td colspan="2" class="center font-bold border-dark" style="color: #16a34a; font-weight: 800;">
                                    — LUNAS DI SISTEM —
                                </td>
                            @else
                                <td class="center border-dark" style="color: #cbd5e1; font-size: 8px; vertical-align: bottom; padding-bottom: 4px;">
                                    Rp .......................... (Paraf)
                                </td>
                                <td class="center border-dark" style="color: #cbd5e1; font-size: 8px; vertical-align: bottom; padding-bottom: 4px;">
                                    Rp .......................... (Paraf)
                                </td>
                            @endif
                            <td class="center border-left-dark" style="color: #64748b; font-size: 8.5px;">
                                @if(!empty($row['exceptionNote']))
                                    <span style="color: #d97706; font-weight: bold;">{{ $row['exceptionNote'] }}</span>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
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
