<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Berita Acara Rekonsiliasi & Tutup Buku Kas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 10px;
            color: #0f172a;
            background: #ffffff;
            padding: 30px 35px;
        }

        /* ── HEADER KOP ── */
        .header {
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .inst-title { font-size: 16px; font-weight: bold; color: #0f172a; }
        .inst-sub { font-size: 9px; color: #64748b; margin-top: 2px; }
        .doc-title { font-size: 13px; font-weight: bold; color: #0f172a; text-align: right; }
        .doc-sub { font-size: 9px; color: #64748b; text-align: right; margin-top: 2px; }

        /* ── METADATA BOX ── */
        .meta-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 18px;
        }
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td { font-size: 9px; padding: 3px 6px; vertical-align: top; }
        .meta-label { color: #64748b; font-weight: bold; width: 22%; }
        .meta-val { color: #0f172a; font-weight: bold; }

        /* ── SECTION TITLE ── */
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 14px;
            margin-bottom: 6px;
            border-left: 3.5px solid #0284c7;
            padding-left: 6px;
        }

        /* ── TABLE ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
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
        .data-table th.center { text-align: center; }
        .data-table td {
            padding: 5.5px 8px;
            font-size: 9px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        .data-table td.right { text-align: right; }
        .data-table td.center { text-align: center; }
        .data-table tr.total-row td {
            background: #f8fafc;
            font-weight: bold;
            border-top: 1.5px solid #cbd5e1;
            border-bottom: 1.5px solid #cbd5e1;
            color: #0f172a;
        }

        /* ── NOTES BOX ── */
        .notes-box {
            background: #fdfdfd;
            border: 1px dashed #94a3b8;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 16px;
            font-size: 9px;
            color: #334155;
            line-height: 1.5;
        }

        /* ── SIGNATURES ── */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .sig-box { width: 50%; text-align: center; font-size: 9px; vertical-align: top; }
        .sig-space { height: 60px; }
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

    {{-- HEADER KOP --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <div class="inst-title">{{ strtoupper($app_name) }}</div>
                    <div class="inst-sub">Sistem Manajemen Keuangan & Rekonsiliasi Kas Pesantren</div>
                </td>
                <td>
                    <div class="doc-title">BERITA ACARA TUTUP BUKU KAS</div>
                    <div class="doc-sub">No. Snapshot: <strong>{{ substr($snapshot->id, 0, 8) }}</strong></div>
                </td>
            </tr>
        </table>
    </div>

    {{-- METADATA DOKUMEN --}}
    <div class="meta-box">
        <table class="meta-table">
            <tr>
                <td class="meta-label">Periode Rekonsiliasi:</td>
                <td class="meta-val">{{ $period_label }}</td>
                <td class="meta-label">Waktu Penguncian:</td>
                <td class="meta-val">{{ $snapshot->distributed_at ? $snapshot->distributed_at->locale('id')->translatedFormat('d F Y, H:i') : $snapshot->created_at->locale('id')->translatedFormat('d F Y, H:i') }} WIB</td>
            </tr>
            <tr>
                <td class="meta-label">Lingkup Unit:</td>
                <td class="meta-val">{{ $snapshot->gender === 'L' ? 'Unit Putra' : ($snapshot->gender === 'P' ? 'Unit Putri' : 'Semua Unit (Pusat)') }}</td>
                <td class="meta-label">Petugas Bendahara:</td>
                <td class="meta-val">{{ $snapshot->distributor?->name ?? 'Bendahara Pusat' }}</td>
            </tr>
        </table>
    </div>

    {{-- 1. RINGKASAN ARUS KAS MASUK --}}
    <div class="section-title">1. Ringkasan Penerimaan Arus Kas (3 Saluran)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Saluran Penerimaan Dana</th>
                <th class="right">Total Nominal Kotor</th>
                <th class="right">Potongan MDR / Biaya</th>
                <th class="right">Total Uang Bersih</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sources = $breakdown['sources'] ?? [];
                $gwNet   = $sources['gateway_net'] ?? (float)($snapshot->online_amount ?? 0);
                $gwMdr   = $sources['gateway_mdr'] ?? (float)($snapshot->total_mdr ?? 0);
                $gwGross = $sources['gateway_gross'] ?? ($gwNet + $gwMdr);
                $trfAmt  = $sources['transfer_amount'] ?? 0;
                $cashAmt = $sources['cash_amount'] ?? (float)($snapshot->manual_amount ?? 0);
            @endphp
            <tr>
                <td><strong>⚡ Payment Gateway (DOKU Online)</strong></td>
                <td class="right">Rp {{ number_format($gwGross, 0, ',', '.') }}</td>
                <td class="right" style="color: #dc2626;">- Rp {{ number_format($gwMdr, 0, ',', '.') }}</td>
                <td class="right"><strong>Rp {{ number_format($gwNet, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td><strong>🏦 Transfer Bank Manual (Mutasi Rekening Pondok)</strong></td>
                <td class="right">Rp {{ number_format($trfAmt, 0, ',', '.') }}</td>
                <td class="right" style="color: #64748b;">Rp 0</td>
                <td class="right"><strong>Rp {{ number_format($trfAmt, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td><strong>💵 Setoran Kasir / Fisik Tunai di Brankas</strong></td>
                <td class="right">Rp {{ number_format($cashAmt, 0, ',', '.') }}</td>
                <td class="right" style="color: #64748b;">Rp 0</td>
                <td class="right"><strong>Rp {{ number_format($cashAmt, 0, ',', '.') }}</strong></td>
            </tr>
            <tr class="total-row">
                <td>TOTAL UANG MASUK BERSIH (KLOP):</td>
                <td class="right">Rp {{ number_format((float)$snapshot->total_gross, 0, ',', '.') }}</td>
                <td class="right" style="color: #dc2626;">- Rp {{ number_format((float)$snapshot->total_mdr, 0, ',', '.') }}</td>
                <td class="right" style="color: #047857; font-size: 10px;"><strong>Rp {{ number_format((float)$snapshot->total_net, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    {{-- 2. RINCIAN ALOKASI PEMBAGIAN DANA --}}
    <div class="section-title">2. Lembar Alokasi & Distribusi Peruntukan Pos Anggaran</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th>Pos Peruntukan Anggaran</th>
                <th class="center" style="width: 15%;">Jumlah Santri</th>
                <th class="right" style="width: 20%;">Nominal Alokasi</th>
                <th class="center" style="width: 25%;">Status Penyerahan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $categories = $breakdown['categories'] ?? $breakdown['category_breakdown'] ?? [];
                $checklist  = $breakdown['checklist'] ?? [];
            @endphp
            @forelse($categories as $idx => $cat)
                @php
                    $catKey = $cat['key'] ?? '';
                    $chk = $checklist[$catKey] ?? null;
                    $isHanded = !empty($chk['handed_over']);
                    $note = $chk['recipient_note'] ?? '-';
                @endphp
                <tr>
                    <td class="center" style="color: #94a3b8;">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $cat['label'] ?? '-' }}</strong>
                    </td>
                    <td class="center">{{ $cat['count'] ?? 0 }} Tagihan/Santri</td>
                    <td class="right"><strong>Rp {{ number_format((float)($cat['amount'] ?? 0), 0, ',', '.') }}</strong></td>
                    <td class="center" style="font-size: 8px;">
                        @if($isHanded)
                            <span style="color: #047857; font-weight: bold;">✔ SUDAH DISERAHKAN</span>
                            @if(!empty($note) && $note !== '-')
                                <div style="color: #64748b; font-size: 7.5px;">Ket: {{ $note }}</div>
                            @endif
                        @else
                            <span style="color: #d97706; font-weight: bold;">⏳ Menunggu / Kas Utama</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="center" style="padding: 15px; color: #94a3b8;">Tidak ada data alokasi pos anggaran.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- CATATAN PENUTUPAN KAS --}}
    @if(!empty($snapshot->notes))
    <div class="section-title">3. Catatan Bendahara & Pengasuhan</div>
    <div class="notes-box">
        <strong>Catatan Rekonsiliasi:</strong> {{ $snapshot->notes }}
    </div>
    @endif

    {{-- TANDA TANGAN BERITA ACARA --}}
    <table class="sig-table">
        <tr>
            <td class="sig-box">
                <div>Dibuat & Dikunci Oleh,</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $snapshot->distributor?->name ?? 'Bendahara Pusat' }}</div>
                <div class="sig-title">Bendahara Keuangan Pesantren</div>
            </td>
            <td class="sig-box">
                <div>Mengetahui & Menyetujui,</div>
                <div class="sig-space"></div>
                <div class="sig-name">___________________________</div>
                <div class="sig-title">Pengasuh / Pimpinan Pesantren</div>
            </td>
        </tr>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        Dicetak pada {{ $generated_at }} melalui Sistem Keuangan {{ $app_name }}. Berita acara ini adalah dokumen sah arsip keuangan pesantren.
    </div>

</body>
</html>
