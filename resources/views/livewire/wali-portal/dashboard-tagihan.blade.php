<div class="space-y-5">
    <!-- Tombol Kembali & Header Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('portal-wali.search') }}" 
           class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-emerald-700 dark:hover:text-emerald-400 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 px-3.5 py-2 rounded-2xl shadow-sm transition-all">
            <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            <span>Cari Nama Santri Lain</span>
        </a>

        <button type="button" 
                @click="sidebarOpen = true" 
                class="inline-flex items-center gap-1.5 text-xs font-extrabold text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-500/10 border border-emerald-300 dark:border-emerald-500/20 px-3 py-2 rounded-2xl shadow-xs hover:bg-emerald-200 transition-all">
            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            <span>Rekening & WA</span>
        </button>
    </div>

    <!-- Profil Santri Card -->
    @php
        $activeRoom = $santri->roomAssignments->first()?->room;
        $activeDorm = $activeRoom?->dormitory;
        $activeKelas = $santri->madrasahEnrollments->first()?->kelas;
    @endphp
    <div class="bg-white dark:bg-slate-900 border-2 border-emerald-600/30 dark:border-slate-800 rounded-3xl p-4 shadow-md space-y-3 relative overflow-hidden transition-colors">
        <div class="flex items-center gap-3.5">
            <!-- Avatar -->
            <div class="w-14 h-14 rounded-2xl bg-emerald-700 text-white flex items-center justify-center font-black text-xl shrink-0 shadow-md overflow-hidden border-2 border-emerald-600">
                @if($santri->photo)
                    <img src="{{ Storage::url($santri->photo) }}" alt="{{ $santri->name }}" class="w-full h-full object-cover">
                @else
                    <span>{{ strtoupper(substr($santri->name, 0, 2)) }}</span>
                @endif
            </div>

            <!-- Profile Info -->
            <div class="flex-1 min-w-0">
                <span class="inline-block text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-500/10 px-2 py-0.5 rounded-md border border-emerald-200 dark:border-emerald-500/20">
                    Santri Aktif
                </span>
                <h2 class="text-base font-extrabold text-slate-900 dark:text-slate-100 truncate mt-0.5">{{ $santri->name }}</h2>
                @if($santri->nis)
                    <p class="text-[11px] font-mono text-slate-500 dark:text-slate-400">NIS: {{ $santri->nis }}</p>
                @endif
            </div>
        </div>

        <!-- Detail Lokasi & Alamat -->
        <div class="grid grid-cols-2 gap-2 pt-2.5 border-t border-slate-100 dark:border-slate-800 text-xs">
            <div class="bg-slate-50 dark:bg-slate-950 p-2 rounded-xl border border-slate-200/80 dark:border-slate-800">
                <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase">Komplek / Asrama</span>
                <strong class="text-slate-800 dark:text-slate-200 text-[11px] block truncate">
                    {{ $activeDorm ? $activeDorm->name : '-' }} {{ $activeRoom ? '(' . $activeRoom->name . ')' : '' }}
                </strong>
            </div>
            <div class="bg-slate-50 dark:bg-slate-950 p-2 rounded-xl border border-slate-200/80 dark:border-slate-800">
                <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase">Kelas Madrasah</span>
                <strong class="text-slate-800 dark:text-slate-200 text-[11px] block truncate">
                    {{ $activeKelas ? $activeKelas->name : '-' }}
                </strong>
            </div>
            @if(!empty($santri->address))
                <div class="col-span-2 bg-slate-50 dark:bg-slate-950 p-2 rounded-xl border border-slate-200/80 dark:border-slate-800">
                    <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase">Alamat Asal Santri</span>
                    <strong class="text-slate-800 dark:text-slate-200 text-[11px] block truncate">
                        📍 {{ $santri->address }}
                    </strong>
                </div>
            @endif
        </div>
    </div>

    <!-- HERO CARD RINGKASAN TAGIHAN HARUS DIBAYAR -->
    @if($totalHarusDibayarNow == 0)
        <!-- Kondisi LUNAS / TIDAK ADA TAGIHAN SEKARANG -->
        <div class="bg-emerald-50 dark:bg-emerald-950/40 border-2 border-emerald-300 dark:border-emerald-700 rounded-3xl p-5 text-center space-y-2 shadow-sm transition-colors">
            <div class="w-12 h-12 rounded-full bg-emerald-600 dark:bg-emerald-500 text-white flex items-center justify-center mx-auto shadow-md">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 class="text-lg font-black text-emerald-800 dark:text-emerald-300">Alhamdulillah, Bebas Tagihan!</h3>
            <p class="text-xs text-emerald-700 dark:text-emerald-400 leading-relaxed max-w-xs mx-auto">
                Seluruh tagihan bulan ini ({{ $currentMonthName }} {{ $currentYear }}) dan tunggakan sebelumnya sudah lunas diselesaikan.
            </p>
        </div>
    @else
        <!-- Kondisi ADA TAGIHAN BELUM DIBAYAR -->
        <div class="bg-gradient-to-br from-rose-50 to-amber-50 dark:from-rose-950/40 dark:to-slate-900 border-2 border-rose-300 dark:border-rose-800 rounded-3xl p-5 shadow-sm space-y-3 transition-colors">
            <div class="flex items-center justify-between">
                <span class="text-xs font-black uppercase text-rose-700 dark:text-rose-400 tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Total Belum Dibayar</span>
                </span>
                <span class="text-[10px] font-extrabold text-rose-700 dark:text-rose-300 bg-rose-100 dark:bg-rose-500/20 px-2.5 py-0.5 rounded-full border border-rose-200 dark:border-rose-500/30 shadow-xs">
                    Belum Diselesaikan
                </span>
            </div>

            <div class="text-2xl font-black text-slate-900 dark:text-slate-100 tracking-tight">
                Rp {{ number_format($totalHarusDibayarNow, 0, ',', '.') }}
            </div>

            <div class="grid grid-cols-2 gap-2 text-xs pt-2 border-t border-rose-200/60 dark:border-rose-900/60 font-medium text-slate-700 dark:text-slate-300">
                <div class="bg-white/80 dark:bg-slate-900/80 p-2 rounded-xl border border-rose-100 dark:border-rose-900/50">
                    <span class="block text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase">Tagihan Bulan Ini</span>
                    <strong class="text-slate-800 dark:text-slate-200 text-xs">Rp {{ number_format($totalCurrentMonthUnpaid, 0, ',', '.') }}</strong>
                </div>
                <div class="bg-white/80 dark:bg-slate-900/80 p-2 rounded-xl border border-rose-100 dark:border-rose-900/50">
                    <span class="block text-[10px] text-rose-600 dark:text-rose-400 font-bold uppercase">Tunggakan Lalu</span>
                    <strong class="text-rose-700 dark:text-rose-300 text-xs">Rp {{ number_format($totalPastTunggakan, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- BANNER INFO: JADWAL REKAP BENDAHARA (Dinamis dari CMS)       --}}
    {{-- ============================================================ --}}
    @if($waliRekapInfo)
    <div class="flex items-start gap-3 bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800/50 rounded-2xl p-4 shadow-sm transition-colors">
        {{-- Icon --}}
        <div class="shrink-0 w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/60 border border-blue-200 dark:border-blue-700/50 flex items-center justify-center mt-0.5">
            <svg class="w-4.5 h-4.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        {{-- Content --}}
        <div class="flex-1 min-w-0 space-y-1">
            <p class="text-xs font-black text-blue-800 dark:text-blue-300 uppercase tracking-wide">
                📅 Informasi Pembaruan Data Tagihan
            </p>
            <p class="text-xs text-blue-700 dark:text-blue-400 leading-relaxed">
                {{ $waliRekapInfo }}
            </p>
        </div>
    </div>
    @endif

    <!-- KALKULATOR SIMULASI PEMBAYARAN — CHECKLIST PILIH TAGIHAN (ACCORDION) -->
    <div x-data="{ openSimulasi: false }" class="bg-white dark:bg-slate-900 border-2 border-emerald-600/30 dark:border-slate-800 rounded-3xl p-4 shadow-md space-y-3 transition-colors">
        <button type="button" @click="openSimulasi = !openSimulasi" class="w-full flex items-center justify-between text-xs font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider py-1">
            <span class="flex items-center gap-2 text-emerald-700 dark:text-emerald-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Simulasi Pembayaran (Hitung Cicilan / Pilihan Tagihan)</span>
                @if(count($selectedBillIds) > 0)
                    <span class="bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-500/30 px-2 py-0.5 rounded-full text-[10px] font-black">
                        {{ count($selectedBillIds) }} Dipilih (Rp {{ number_format($simulasiTotal, 0, ',', '.') }})
                    </span>
                @endif
            </span>
            <svg class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': openSimulasi }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
        </button>

        <div x-show="openSimulasi" x-collapse class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-4">
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Centang tagihan di bawah ini yang ingin Bapak/Ibu bayar — total otomatis dihitung dan rinciannya bisa langsung dikirim ke WA Bendahara:
            </p>

            @if($simulasiBillOptions->count() > 0)
                {{-- Quick Shortcut Buttons --}}
                <div class="flex flex-wrap gap-1.5 pb-1">
                    @if(count($mandatoryBillIds) > 0)
                        <button type="button"
                                wire:click="$set('selectedBillIds', {{ json_encode($mandatoryBillIds) }})"
                                class="px-2.5 py-1 text-[11px] font-extrabold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 rounded-xl hover:bg-emerald-200 transition-all border border-emerald-300 dark:border-emerald-500/30">
                            ✨ Centang Tagihan Wajib
                        </button>
                    @endif
                    @if(count($pastBillIdsOnly) > 0)
                        <button type="button"
                                wire:click="$set('selectedBillIds', {{ json_encode($pastBillIdsOnly) }})"
                                class="px-2.5 py-1 text-[11px] font-extrabold bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300 rounded-xl hover:bg-rose-200 transition-all border border-rose-300 dark:border-rose-500/30">
                            🔴 Tunggakan Saja
                        </button>
                    @endif
                    <button type="button"
                            wire:click="$set('selectedBillIds', {{ json_encode($simulasiBillOptions->pluck('id')->toArray()) }})"
                            class="px-2.5 py-1 text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-200 transition-all border border-slate-200 dark:border-slate-700">
                        ☑️ Pilih Semua
                    </button>
                    <button type="button"
                            wire:click="$set('selectedBillIds', [])"
                            class="px-2.5 py-1 text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-200 transition-all border border-slate-200 dark:border-slate-700">
                        ✕ Batal Semua
                    </button>
                </div>

                {{-- Daftar Checklist dengan Badge Kategori --}}
                <div class="space-y-2">
                    @foreach($simulasiBillOptions as $bill)
                        @php
                            $kekurangan = max(0, $bill->amount - $bill->amount_paid);
                            $cat = $bill->simulasi_cat ?? 'current';
                        @endphp
                        <label for="cb_{{ $bill->id }}"
                               class="flex items-center gap-3 p-3 rounded-2xl border-2 cursor-pointer transition-all
                                      {{ in_array($bill->id, $selectedBillIds)
                                          ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-400 dark:border-emerald-600'
                                          : 'bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 hover:border-emerald-300 dark:hover:border-emerald-700' }}">

                            {{-- Checkbox --}}
                            <input type="checkbox"
                                   id="cb_{{ $bill->id }}"
                                   wire:model.live="selectedBillIds"
                                   value="{{ $bill->id }}"
                                   class="w-4 h-4 rounded text-emerald-600 border-slate-300 dark:border-slate-700 focus:ring-emerald-500 shrink-0">

                            {{-- Label Tagihan --}}
                            <div class="flex-1 min-w-0 space-y-0.5">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @if($cat === 'past')
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800">🔴 Tunggakan Lalu</span>
                                    @elseif($cat === 'current')
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800">🟡 Tagihan Bulan Ini</span>
                                    @elseif($cat === 'event')
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800">⚡ Kegiatan / Kitab</span>
                                    @elseif($cat === 'future')
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800">🔵 Bayar Di Awal</span>
                                    @endif

                                    @if($bill->status === 'partial')
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-200/80 text-amber-900 dark:bg-amber-900/60 dark:text-amber-300">Dicicil</span>
                                    @endif
                                </div>
                                <span class="block text-xs font-extrabold text-slate-800 dark:text-slate-200 truncate">
                                    {{ $this->getBillDisplayName($bill) }}
                                </span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400">
                                    Periode: {{ $this->getBillPeriodLabel($bill) }}
                                </span>
                            </div>

                            {{-- Nominal sisa --}}
                            <div class="text-right shrink-0">
                                <span class="block text-xs font-black text-slate-900 dark:text-slate-100">
                                    Rp {{ number_format($kekurangan, 0, ',', '.') }}
                                </span>
                                @if($bill->status === 'partial')
                                    <span class="text-[10px] text-amber-600 dark:text-amber-400">sisa kekurangan</span>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>

                {{-- Hasil Total & Aksi (muncul jika ada yang dicentang) --}}
                @if(!empty($selectedBillIds) && $simulasiTotal > 0)
                    <div id="simulasiCardBox" class="bg-emerald-950 dark:bg-slate-950 rounded-2xl p-4 space-y-3 border border-emerald-700/60">

                        {{-- Rincian Terpilih --}}
                        <div class="space-y-1.5">
                            <span class="text-[10px] font-black text-emerald-300 uppercase tracking-wider block">Rincian Tagihan yang Dipilih</span>
                            @foreach($simulasiHasil as $item)
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-emerald-100 dark:text-slate-300 font-medium truncate flex-1 mr-2">{{ $item['label'] }}</span>
                                    <span class="font-black text-white shrink-0">Rp {{ number_format($item['terbayar'], 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Total --}}
                        <div class="flex items-center justify-between pt-2 border-t border-emerald-700/60">
                            <span class="text-xs font-bold text-emerald-300">Total yang Perlu Ditransfer</span>
                            <span class="text-xl font-black text-white">Rp {{ number_format($simulasiTotal, 0, ',', '.') }}</span>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                            <button type="button"
                                    onclick='generateAndDownloadSimulasiImage({{ json_encode($santri->name) }}, {{ (float) $simulasiTotal }}, {{ json_encode($simulasiHasil) }})'
                                    class="w-full py-2.5 px-3 bg-slate-800 hover:bg-slate-700 text-white font-extrabold rounded-xl transition-all flex items-center justify-center gap-1.5 text-xs active:scale-95 border border-slate-700">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Simpan PNG</span>
                            </button>

                            @if($simulasiWaUrl)
                                <a href="{{ $simulasiWaUrl }}" target="_blank"
                                   class="w-full py-2.5 px-3 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-xl transition-all flex items-center justify-center gap-1.5 text-xs">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                    <span>Kirim ke WA Bendahara</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @elseif(!empty($selectedBillIds))
                    <div class="text-center text-xs text-slate-400 py-2">Menghitung...</div>
                @else
                    <div class="bg-slate-50 dark:bg-slate-950 rounded-2xl p-3 text-center text-xs text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-800">
                        ☝️ Centang tagihan di atas untuk melihat simulasi total pembayaran.
                    </div>
                @endif

            @else
                <div class="bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/40 rounded-2xl p-4 text-center text-xs text-emerald-700 dark:text-emerald-400 font-semibold flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Tidak ada tagihan yang belum terbayar — Alhamdulillah!</span>
                </div>
            @endif
        </div>
    </div>

    <!-- SEKSI 1: TAGIHAN PERIODE INI -->
    <div class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-xs font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Tagihan Periode Ini ({{ $currentMonthName }})</span>
            </h3>
        </div>

        @if($currentMonthBills->count() > 0)
            <div class="space-y-2.5">
                @foreach($currentMonthBills as $bill)
                    @php
                        $sisa = max(0, $bill->amount - $bill->amount_paid);
                    @endphp
                    <div class="bg-white dark:bg-slate-900 border-2 border-slate-200/90 dark:border-slate-800 rounded-2xl p-3.5 shadow-sm space-y-2.5 transition-colors">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="inline-block text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                    {{ $this->getBillTypeLabel($bill->bill_type) }} • {{ $this->getBillPeriodLabel($bill) }}
                                </span>
                                <h4 class="text-sm font-extrabold text-slate-900 dark:text-slate-100 mt-1">
                                    {{ $this->getBillDisplayName($bill) }}
                                </h4>
                            </div>

                            <!-- Badge Status -->
                            @if($bill->status === 'paid')
                                <span class="px-2.5 py-1 text-[10px] font-black uppercase text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-500/10 border border-emerald-300 dark:border-emerald-500/20 rounded-full shrink-0 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>LUNAS</span>
                                </span>
                            @elseif($bill->status === 'partial')
                                <span class="px-2.5 py-1 text-[10px] font-black uppercase text-amber-700 dark:text-amber-400 bg-amber-100 dark:bg-amber-500/10 border border-amber-300 dark:border-amber-500/20 rounded-full shrink-0">
                                    DICICIL
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-[10px] font-black uppercase text-rose-700 dark:text-rose-400 bg-rose-100 dark:bg-rose-500/10 border border-rose-300 dark:border-rose-500/20 rounded-full shrink-0">
                                    BELUM DIBAYAR
                                </span>
                            @endif
                        </div>

                        <!-- Nominal Rincian -->
                        <div class="bg-slate-50 dark:bg-slate-950 rounded-xl p-2.5 border border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs">
                            <div>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-bold block uppercase">Nominal Tagihan</span>
                                <strong class="text-slate-800 dark:text-slate-200 font-extrabold">Rp {{ number_format($bill->amount, 0, ',', '.') }}</strong>
                            </div>

                            @if($bill->status !== 'paid')
                                <div class="text-right">
                                    <span class="text-[10px] text-rose-600 dark:text-rose-400 font-bold block uppercase">Sisa Kekurangan</span>
                                    <strong class="text-rose-700 dark:text-rose-400 font-black text-sm">Rp {{ number_format($sisa, 0, ',', '.') }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 text-center text-xs text-slate-500 dark:text-slate-400 transition-colors">
                Belum ada rincian tagihan khusus untuk bulan {{ $currentMonthName }}.
            </div>
        @endif
    </div>

    {{-- SEKSI TAGIHAN KHUSUS & KEGIATAN (EVENT / INSIDENTAL - ACCORDION) --}}
    @if(isset($eventBills) && $eventBills->count() > 0)
        <div x-data="{ openEvent: false }" class="bg-white dark:bg-slate-900 border-2 border-purple-200 dark:border-purple-500/30 rounded-3xl p-4 shadow-sm space-y-3 transition-colors">
            <button type="button" @click="openEvent = !openEvent" class="w-full flex items-center justify-between text-xs font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider py-1">
                <span class="flex items-center gap-1.5 text-purple-700 dark:text-purple-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Tagihan Khusus & Kegiatan</span>
                    <span class="bg-purple-100 dark:bg-purple-500/20 text-purple-800 dark:text-purple-300 border border-purple-200 dark:border-purple-500/30 px-2.5 py-0.5 rounded-full text-[10px] font-black">
                        {{ $eventBills->count() }} Tagihan
                    </span>
                </span>
                <svg class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': openEvent }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="openEvent" x-collapse class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2.5">
                @foreach($eventBills as $bill)
                    @php
                        $sisa = max(0, $bill->amount - $bill->amount_paid);
                    @endphp
                    <div class="bg-purple-50/60 dark:bg-slate-900 border-2 border-purple-200 dark:border-purple-500/30 rounded-2xl p-3.5 shadow-sm space-y-2.5 transition-colors">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="inline-block text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-md bg-purple-100 dark:bg-purple-500/20 text-purple-800 dark:text-purple-300 border border-purple-200 dark:border-purple-500/30">
                                    ⚡ {{ $this->getBillTypeLabel($bill->bill_type) }}
                                </span>
                                <h4 class="text-sm font-extrabold text-slate-900 dark:text-slate-100 mt-1">
                                    {{ $this->getBillDisplayName($bill) }}
                                </h4>
                                @if($bill->due_date)
                                    <span class="block text-[10px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                        ⏰ Jatuh Tempo: {{ $bill->due_date->format('d M Y') }}
                                    </span>
                                @endif
                            </div>

                            @if($bill->status === 'partial')
                                <span class="px-2.5 py-1 text-[10px] font-black uppercase text-amber-700 dark:text-amber-400 bg-amber-100 dark:bg-amber-500/10 border border-amber-300 dark:border-amber-500/20 rounded-full shrink-0">
                                    DICICIL
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-[10px] font-black uppercase text-rose-700 dark:text-rose-400 bg-rose-100 dark:bg-rose-500/10 border border-rose-300 dark:border-rose-500/20 rounded-full shrink-0">
                                    BELUM DIBAYAR
                                </span>
                            @endif
                        </div>

                        <!-- Nominal Rincian -->
                        <div class="bg-white dark:bg-slate-950 rounded-xl p-2.5 border border-purple-200/80 dark:border-slate-800 flex items-center justify-between text-xs">
                            <div>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-bold block uppercase">Total Tagihan</span>
                                <strong class="text-slate-800 dark:text-slate-200">Rp {{ number_format($bill->amount, 0, ',', '.') }}</strong>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-rose-600 dark:text-rose-400 font-bold block uppercase">Sisa Kekurangan</span>
                                <strong class="text-rose-700 dark:text-rose-400 font-black text-sm">Rp {{ number_format($sisa, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- SEKSI 2: TUNGGAKAN BULAN LALU (ACCORDION) -->
    <div x-data="{ openPast: false }" class="bg-white dark:bg-slate-900 border-2 border-amber-200 dark:border-amber-500/30 rounded-3xl p-4 shadow-sm space-y-3 transition-colors">
        <button type="button" @click="openPast = !openPast" class="w-full flex items-center justify-between text-xs font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider py-1">
            <span class="flex items-center gap-1.5 text-amber-700 dark:text-amber-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Tunggakan Periode Lalu</span>
                @if($pastUnpaidBills->count() > 0)
                    <span class="bg-amber-100 dark:bg-amber-500/20 text-amber-900 dark:text-amber-300 border border-amber-300 dark:border-amber-500/30 px-2.5 py-0.5 rounded-full text-[10px] font-black">
                        {{ $pastUnpaidBills->count() }} Tunggakan (Rp {{ number_format($totalPastTunggakan, 0, ',', '.') }})
                    </span>
                @else
                    <span class="bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 px-2 py-0.5 rounded-full text-[10px] font-black">
                        Nihil
                    </span>
                @endif
            </span>
            <svg class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': openPast }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
        </button>

        <div x-show="openPast" x-collapse class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2.5">
            @if($pastUnpaidBills->count() > 0)
                @foreach($pastUnpaidBills as $bill)
                    @php
                        $sisa = max(0, $bill->amount - $bill->amount_paid);
                    @endphp
                    <div class="bg-amber-50/70 dark:bg-slate-900 border-2 border-amber-200 dark:border-amber-500/30 rounded-2xl p-3.5 shadow-sm space-y-2.5 transition-colors">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="inline-block text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-md bg-amber-200/80 dark:bg-amber-500/20 text-amber-900 dark:text-amber-300 border border-amber-300 dark:border-amber-500/30">
                                    Tunggakan {{ $this->getBillPeriodLabel($bill) }}
                                </span>
                                <h4 class="text-sm font-extrabold text-slate-900 dark:text-slate-100 mt-1">
                                    {{ $this->getBillDisplayName($bill) }}
                                </h4>
                            </div>

                            <span class="px-2.5 py-1 text-[10px] font-black uppercase text-rose-700 dark:text-rose-400 bg-rose-100 dark:bg-rose-500/10 border border-rose-300 dark:border-rose-500/20 rounded-full shrink-0">
                                TUNGGAKAN
                            </span>
                        </div>

                        <!-- Nominal Rincian -->
                        <div class="bg-white dark:bg-slate-950 rounded-xl p-2.5 border border-amber-200/80 dark:border-slate-800 flex items-center justify-between text-xs">
                            <div>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-bold block uppercase">Total Tagihan</span>
                                <strong class="text-slate-800 dark:text-slate-200">Rp {{ number_format($bill->amount, 0, ',', '.') }}</strong>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-rose-600 dark:text-rose-400 font-bold block uppercase">Belum Dibayar</span>
                                <strong class="text-rose-700 dark:text-rose-400 font-black text-sm">Rp {{ number_format($sisa, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/40 rounded-2xl p-3.5 text-center text-xs text-emerald-800 dark:text-emerald-400 font-semibold flex items-center justify-center gap-1.5 transition-colors">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Tidak ada sisa tunggakan dari bulan-bulan sebelumnya.</span>
                </div>
            @endif
        </div>
    </div>

    <!-- SEKSI 3: TAGIHAN MENDATANG & BAYAR DI AWAL -->
    <div x-data="{ openFuture: false }" class="bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-800 rounded-3xl p-4 shadow-sm space-y-3 transition-colors">
        <button type="button" @click="openFuture = !openFuture" class="w-full flex items-center justify-between text-xs font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-wider py-1">
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Tagihan Mendatang & Bayar Di Awal</span>
                @if($futureBills->where('status', 'paid')->count() > 0)
                    <span class="bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-500/20 px-2 py-0.5 rounded-full text-[10px] font-black">
                        {{ $futureBills->where('status', 'paid')->count() }} Lunas di Awal
                    </span>
                @endif
            </span>
            <svg class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': openFuture }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
        </button>

        <div x-show="openFuture" x-collapse class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2.5">
            @if($futureBills->count() > 0)
                @foreach($futureBills as $bill)
                    <div class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl p-3 shadow-xs space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase block">
                                    Periode: {{ $this->getBillPeriodLabel($bill) }}
                                </span>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 mt-0.5">
                                    {{ $this->getBillDisplayName($bill) }}
                                </h4>
                            </div>

                            @if($bill->status === 'paid')
                                <span class="px-2.5 py-1 text-[10px] font-black uppercase text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-500/10 border border-emerald-300 dark:border-emerald-500/20 rounded-full shrink-0 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>LUNAS (DIBAYAR DI AWAL)</span>
                                </span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-bold text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-slate-800 rounded-md shrink-0">
                                    Belum Jatuh Tempo
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between text-xs pt-1.5 border-t border-slate-200/60 dark:border-slate-800">
                            <span class="text-slate-500 dark:text-slate-400 text-[11px]">Nominal Tagihan:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">Rp {{ number_format($bill->amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-xs text-slate-500 dark:text-slate-400 text-center py-2">
                    Belum ada terbitan tagihan untuk periode mendatang.
                </p>
            @endif
        </div>
    </div>

    <!-- SEKSI: RIWAYAT TAGIHAN LUNAS (ACCORDION) -->
    <div x-data="{ openLunas: false }" class="bg-white dark:bg-slate-900 border-2 border-emerald-200 dark:border-emerald-800/40 rounded-3xl p-4 shadow-sm space-y-3 transition-colors">
        <button type="button" @click="openLunas = !openLunas" class="w-full flex items-center justify-between text-xs font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider py-1">
            <span class="flex items-center gap-1.5 text-emerald-700 dark:text-emerald-400">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Riwayat Tagihan Lunas</span>
                @if($pastPaidBills->count() > 0)
                    <span class="bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 px-2.5 py-0.5 rounded-full text-[10px] font-black">
                        {{ $pastPaidBills->count() }} Tagihan
                    </span>
                @endif
            </span>
            <svg class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': openLunas }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
        </button>

        <div x-show="openLunas" x-collapse class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2.5">
            @if($pastPaidBills->count() > 0)
                @foreach($pastPaidBills as $bill)
                    @php
                        $isPaidFull    = $bill->status === 'paid';
                        $isPartial     = $bill->status === 'partial';
                        $paidAt        = $bill->updated_at ?? $bill->created_at;
                    @endphp
                    <div class="bg-emerald-50/60 dark:bg-slate-900 border-2
                                {{ $isPaidFull ? 'border-emerald-200 dark:border-emerald-800/40' : 'border-amber-200 dark:border-amber-700/30' }}
                                rounded-2xl p-3.5 shadow-sm space-y-2 transition-colors">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <span class="inline-block text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-md mb-1
                                             {{ $isPaidFull ? 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20'
                                                            : 'bg-amber-100 dark:bg-amber-500/10 text-amber-800 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20' }}">
                                    {{ $this->getBillPeriodLabel($bill) }}
                                </span>
                                <h4 class="text-sm font-extrabold text-slate-900 dark:text-slate-100 truncate">
                                    {{ $this->getBillDisplayName($bill) }}
                                </h4>
                            </div>

                            {{-- Badge Status --}}
                            @if($isPaidFull)
                                <span class="px-2.5 py-1 text-[10px] font-black uppercase text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-500/10 border border-emerald-300 dark:border-emerald-500/20 rounded-full shrink-0 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    LUNAS
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-[10px] font-black uppercase text-amber-700 dark:text-amber-400 bg-amber-100 dark:bg-amber-500/10 border border-amber-300 dark:border-amber-500/20 rounded-full shrink-0">
                                    SEBAGIAN
                                </span>
                            @endif
                        </div>

                        {{-- Detail Nominal --}}
                        <div class="bg-white dark:bg-slate-950 rounded-xl p-2.5 border border-emerald-100 dark:border-slate-800 grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-bold block uppercase">Total Tagihan</span>
                                <strong class="text-slate-800 dark:text-slate-200">Rp {{ number_format($bill->amount, 0, ',', '.') }}</strong>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold block uppercase">Sudah Dibayar</span>
                                <strong class="text-emerald-700 dark:text-emerald-400">Rp {{ number_format($bill->amount_paid, 0, ',', '.') }}</strong>
                            </div>
                        </div>

                        {{-- Timestamp dicatat --}}
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Terakhir diperbarui: {{ $paidAt->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 text-center text-xs text-slate-400 dark:text-slate-500 transition-colors">
                Belum ada riwayat tagihan yang lunas tercatat.
            </div>
        @endif
    </div>

    <div x-data="{ activeTab: '{{ $isPutri ? 'putri' : 'putra' }}' }" class="bg-emerald-950 dark:bg-slate-900 text-white rounded-3xl p-5 shadow-lg border border-emerald-700/80 dark:border-slate-800 space-y-4 transition-colors">
        
        <div class="flex items-center justify-between border-b border-emerald-800/80 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-emerald-800/80 text-emerald-300 flex items-center justify-center border border-emerald-700/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
                <div>
                    <h4 class="text-sm font-extrabold text-white leading-tight">Rekening & Konfirmasi Pembayaran</h4>
                    <p class="text-[10px] text-emerald-300/80 dark:text-slate-400">Otomatis disesuaikan dengan unit santri: <strong class="text-white">{{ $santri->name }}</strong></p>
                </div>
            </div>
            <span class="text-[10px] font-bold px-2.5 py-1 rounded-xl border"
                  :class="activeTab === 'putri' ? 'bg-pink-950/80 text-pink-300 border-pink-700/50' : 'bg-emerald-900/80 text-emerald-300 border-emerald-700/50'">
                <span x-text="activeTab === 'putri' ? 'Unit Putri' : 'Unit Putra'"></span>
            </span>
        </div>

        @if($waliAnnouncement)
            <div class="bg-emerald-900/60 dark:bg-slate-950 p-2.5 rounded-xl border border-emerald-700/50 dark:border-slate-800 text-xs text-emerald-100 dark:text-slate-300 leading-relaxed">
                📌 {{ $waliAnnouncement }}
            </div>
        @endif

        {{-- Professional Tab Switcher --}}
        <div class="grid grid-cols-2 gap-2 bg-emerald-900/50 dark:bg-slate-950 p-1.5 rounded-2xl border border-emerald-800/80 dark:border-slate-800">
            <button type="button" @click="activeTab = 'putra'" 
                    class="py-2.5 px-3 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2"
                    :class="activeTab === 'putra' ? 'bg-emerald-600 dark:bg-slate-800 text-white shadow-md border border-emerald-500/50 dark:border-slate-700' : 'text-emerald-300/70 hover:text-white dark:text-slate-400'">
                <svg class="w-4 h-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h4"/></svg>
                <span>Komplek Putra</span>
            </button>
            <button type="button" @click="activeTab = 'putri'" 
                    class="py-2.5 px-3 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2"
                    :class="activeTab === 'putri' ? 'bg-pink-600 dark:bg-slate-800 text-white shadow-md border border-pink-500/50 dark:border-slate-700' : 'text-emerald-300/70 hover:text-white dark:text-slate-400'">
                <svg class="w-4 h-4 text-pink-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Komplek Putri</span>
            </button>
        </div>

        {{-- Tab Content: Putra --}}
        <div x-show="activeTab === 'putra'" class="space-y-3">
            <div class="space-y-2">
                @if(!empty($putraData['bsi']))
                    <div class="bg-emerald-900/80 dark:bg-slate-950 p-3 rounded-2xl border border-emerald-700/60 dark:border-slate-800 text-xs font-mono flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-emerald-300 dark:text-slate-400 font-sans block font-semibold uppercase">{{ $putraData['bank1_name'] }} (Putra):</span>
                            <div class="text-emerald-50 dark:text-emerald-400 font-bold text-sm">{{ $putraData['bsi'] }}</div>
                            <div class="text-[10px] text-emerald-200 dark:text-slate-400 font-sans">a.n. {{ $putraData['bsi_an'] }}</div>
                        </div>
                        <button type="button" onclick="copyToClipboard('{{ $putraData['bsi'] }}')" class="px-2.5 py-1 bg-emerald-700 hover:bg-emerald-600 text-white font-sans text-[11px] font-bold rounded-lg border border-emerald-600 transition-all flex items-center gap-1 active:scale-95">
                            <span>Salin</span>
                        </button>
                    </div>
                @endif

                @if(!empty($putraData['bri']))
                    <div class="bg-emerald-900/80 dark:bg-slate-950 p-3 rounded-2xl border border-emerald-700/60 dark:border-slate-800 text-xs font-mono flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-emerald-300 dark:text-slate-400 font-sans block font-semibold uppercase">{{ $putraData['bank2_name'] }} (Putra):</span>
                            <div class="text-emerald-50 dark:text-emerald-400 font-bold text-sm">{{ $putraData['bri'] }}</div>
                            <div class="text-[10px] text-emerald-200 dark:text-slate-400 font-sans">a.n. {{ $putraData['bri_an'] }}</div>
                        </div>
                        <button type="button" onclick="copyToClipboard('{{ $putraData['bri'] }}')" class="px-2.5 py-1 bg-emerald-700 hover:bg-emerald-600 text-white font-sans text-[11px] font-bold rounded-lg border border-emerald-600 transition-all flex items-center gap-1 active:scale-95">
                            <span>Salin</span>
                        </button>
                    </div>
                @endif
            </div>

            <a href="{{ $putraData['wa_url'] }}" target="_blank" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-2xl transition-all shadow-md flex items-center justify-center gap-2 text-xs tracking-wide">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span>Hubungi {{ $putraData['wa_name'] }} via WA</span>
            </a>
        </div>

        {{-- Tab Content: Putri --}}
        <div x-show="activeTab === 'putri'" class="space-y-3" style="display: none;">
            <div class="space-y-2">
                @if(!empty($putriData['bsi']))
                    <div class="bg-pink-950/80 dark:bg-slate-950 p-3 rounded-2xl border border-pink-700/60 dark:border-slate-800 text-xs font-mono flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-pink-300 dark:text-slate-400 font-sans block font-semibold uppercase">{{ $putriData['bank1_name'] }} (Putri):</span>
                            <div class="text-pink-50 dark:text-pink-400 font-bold text-sm">{{ $putriData['bsi'] }}</div>
                            <div class="text-[10px] text-pink-200 dark:text-slate-400 font-sans">a.n. {{ $putriData['bsi_an'] }}</div>
                        </div>
                        <button type="button" onclick="copyToClipboard('{{ $putriData['bsi'] }}')" class="px-2.5 py-1 bg-pink-700 hover:bg-pink-600 text-white font-sans text-[11px] font-bold rounded-lg border border-pink-600 transition-all flex items-center gap-1 active:scale-95">
                            <span>Salin</span>
                        </button>
                    </div>
                @endif

                @if(!empty($putriData['bri']))
                    <div class="bg-pink-950/80 dark:bg-slate-950 p-3 rounded-2xl border border-pink-700/60 dark:border-slate-800 text-xs font-mono flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-pink-300 dark:text-slate-400 font-sans block font-semibold uppercase">{{ $putriData['bank2_name'] }} (Putri):</span>
                            <div class="text-pink-50 dark:text-pink-400 font-bold text-sm">{{ $putriData['bri'] }}</div>
                            <div class="text-[10px] text-pink-200 dark:text-slate-400 font-sans">a.n. {{ $putriData['bri_an'] }}</div>
                        </div>
                        <button type="button" onclick="copyToClipboard('{{ $putriData['bri'] }}')" class="px-2.5 py-1 bg-pink-700 hover:bg-pink-600 text-white font-sans text-[11px] font-bold rounded-lg border border-pink-600 transition-all flex items-center gap-1 active:scale-95">
                            <span>Salin</span>
                        </button>
                    </div>
                @endif
            </div>

            <a href="{{ $putriData['wa_url'] }}" target="_blank" class="w-full py-3 bg-pink-600 hover:bg-pink-500 text-white font-extrabold rounded-2xl transition-all shadow-md flex items-center justify-center gap-2 text-xs tracking-wide">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span>Hubungi {{ $putriData['wa_name'] }} via WA</span>
            </a>
        </div>
    </div>
</div>
