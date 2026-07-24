<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checklist Keuangan Komplek {{ $dormitory->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px double #333;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 11px;
            color: #666;
        }
        .meta-info {
            width: 100%;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .meta-info td {
            padding: 2px 0;
        }
        table.grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        table.grid th, table.grid td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }
        table.grid th {
            background-color: #f2f2f2;
            text-transform: uppercase;
            font-size: 9px;
            font-weight: bold;
        }
        table.grid td.center, table.grid th.center {
            text-align: center;
        }
        .footer-sign {
            width: 100%;
            margin-top: 30px;
        }
        .footer-sign td {
            width: 33%;
            text-align: center;
            vertical-align: top;
            height: 80px;
        }
        @media print {
            body { margin: 10px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 12px; font-weight: bold; cursor: pointer; background: #10b981; color: #fff; border: 0; border-radius: 6px;">Cetak Halaman (Print)</button>
    </div>

    <div class="header">
        <h1>Pondok Pesantren Al-Fithroh</h1>
        <p>Buku Pedoman Keuangan Santri — Lembar Checklist Tagihan Komplek</p>
    </div>

    <table class="meta-info">
        <tr>
            <td style="width: 12%;">Komplek</td>
            <td style="width: 38%;">: {{ $dormitory->name }}</td>
            <td style="width: 12%;">Periode</td>
            <td style="width: 38%;">: {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}</td>
        </tr>
        <tr>
            <td>Tipe Tagihan</td>
            <td>: {{ strtoupper(str_replace('_', ' ', $billType)) }}</td>
            <td>Tanggal Cetak</td>
            <td>: {{ now()->format('d-m-Y H:i') }}</td>
        </tr>
    </table>

    <table class="grid">
        <thead>
            <tr>
                <th style="width: 5%;" class="center">No</th>
                <th style="width: 35%;">Nama Lengkap Santri</th>
                <th class="center" style="width: 15%;">Tunggakan Lama</th>
                @foreach($months as $periodKey => $periodLabel)
                    <th class="center" style="width: 10%;">{{ $periodLabel }}</th>
                @endforeach
                <th class="center" style="width: 15%;">Lunas di Muka</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gridData as $i => $row)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td style="font-weight: bold;">{{ $row['person']->name }}</td>
                    <td class="center" style="color: #b91c1c; font-weight: bold;">
                        @if($row['tunggakanLamaSum'] > 0)
                            Rp {{ number_format($row['tunggakanLamaSum'], 0, ',', '.') }}
                        @else
                            —
                        @endif
                    </td>
                    @foreach($row['bills'] as $periodKey => $bill)
                        <td class="center">
                            @if(!$bill)
                                —
                            @elseif($bill->status === 'paid')
                                [ LUNAS ]
                            @else
                                [ &nbsp; &nbsp; ]
                            @endif
                        </td>
                    @endforeach
                    <td class="center" style="color: #047857; font-weight: bold; font-size: 9px;">
                        @if($row['lunasDiMukaLabel'])
                            s.d. {{ $row['lunasDiMukaLabel'] }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="footer-sign">
        <tr>
            <td>
                Diserahkan Oleh,<br>
                <strong>Bendahara Komplek</strong>
                <br><br><br><br>
                ( .................................... )
            </td>
            <td>
                Diperiksa Oleh,<br>
                <strong>Musyrif Komplek</strong>
                <br><br><br><br>
                ( .................................... )
            </td>
            <td>
                Diterima Oleh,<br>
                <strong>Bendahara Pusat</strong>
                <br><br><br><br>
                ( .................................... )
            </td>
        </tr>
    </table>
</body>
</html>
