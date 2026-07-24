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

        <!-- Detail Lokasi -->
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

    <!-- KALKULATOR SIMULASI PEMBAYARAN -->
    <div x-data="{ openSimulasi: false }" class="bg-white dark:bg-slate-900 border-2 border-emerald-600/30 dark:border-slate-800 rounded-3xl p-4 shadow-md space-y-3 transition-colors">
        <button type="button" @click="openSimulasi = !openSimulasi" class="w-full flex items-center justify-between text-xs font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider py-1">
            <span class="flex items-center gap-1.5 text-emerald-700 dark:text-emerald-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Simulasi Pembayaran (Kalkulator Cicilan)</span>
            </span>
            <svg class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{ 'rotate-180': openSimulasi }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
        </button>

        <div x-show="openSimulasi" x-collapse class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-3">
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Masukkan nominal uang yang ingin Bapak/Ibu bayarkan hari ini untuk melihat tagihan mana saja yang terbayar.
            </p>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1">Nominal Uang Pembayaran (Rp)</label>
                <input type="number" 
                       wire:model.live.debounce.300ms="simulasiInput" 
                       placeholder="Contoh: 300000" 
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-2xl text-sm font-bold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- Tombol Preset Cepat -->
            <div class="flex flex-wrap gap-1.5">
                <button type="button" wire:click="$set('simulasiInput', 100000)" class="px-2.5 py-1 text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-emerald-100 hover:text-emerald-800">Rp 100.000</button>
                <button type="button" wire:click="$set('simulasiInput', 200000)" class="px-2.5 py-1 text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-emerald-100 hover:text-emerald-800">Rp 200.000</button>
                <button type="button" wire:click="$set('simulasiInput', 500000)" class="px-2.5 py-1 text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-emerald-100 hover:text-emerald-800">Rp 500.000</button>
                @if($totalHarusDibayarNow > 0)
                    <button type="button" wire:click="$set('simulasiInput', {{ $totalHarusDibayarNow }})" class="px-2.5 py-1 text-[11px] font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 rounded-lg hover:bg-emerald-200">Pas Total (Rp {{ number_format($totalHarusDibayarNow, 0, ',', '.') }})</button>
                @endif
            </div>

            <!-- Hasil Simulasi -->
            @if(count($simulasiHasil) > 0)
                <div id="simulasiCardBox" class="bg-slate-50 dark:bg-slate-950 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-3 text-xs">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-2">
                        <div>
                            <span class="block text-[10px] font-extrabold uppercase text-slate-500 dark:text-slate-400">Rincian Simulasi Pembayaran</span>
                            <strong class="text-slate-900 dark:text-slate-100 font-extrabold text-sm">{{ $santri->name }}</strong>
                        </div>
                        <span class="text-xs font-black text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-500/10 px-2.5 py-1 rounded-lg">
                            Rp {{ number_format($simulasiInput, 0, ',', '.') }}
                        </span>
                    </div>

                    <ul class="space-y-2">
                        @foreach($simulasiHasil as $item)
                            <li class="flex items-center justify-between p-2.5 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                                <div>
                                    <strong class="text-slate-900 dark:text-slate-100 block text-xs">{{ $item['label'] }}</strong>
                                    <span class="text-[10px] text-emerald-700 dark:text-emerald-400 font-bold">Teralokasi: Rp {{ number_format($item['terbayar'], 0, ',', '.') }}</span>
                                </div>
                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md shrink-0 {{ $item['sisa_bill'] == 0 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300' }}">
                                    {{ $item['status'] }}
                                </span>
                            </li>
                        @endforeach
                    </ul>

                    @if($simulasiSisaUang > 0)
                        <div class="pt-1.5 text-right font-extrabold text-emerald-700 dark:text-emerald-400 text-xs">
                            Sisa Kelebihan Uang: Rp {{ number_format($simulasiSisaUang, 0, ',', '.') }}
                        </div>
                    @endif

                    <!-- Tombol Aksi: Simpan Gambar PNG & Kirim WA -->
                    <div class="pt-3 border-t border-slate-200 dark:border-slate-800 grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <!-- Simpan Gambar PNG (Native Canvas Engine) -->
                        <button type="button" 
                                onclick='generateAndDownloadSimulasiImage({{ json_encode($santri->name) }}, {{ (float) $simulasiInput }}, {{ json_encode($simulasiHasil) }})' 
                                class="w-full py-2.5 px-3 bg-slate-800 dark:bg-slate-800 hover:bg-slate-900 dark:hover:bg-slate-700 text-white font-extrabold rounded-xl transition-all shadow-xs flex items-center justify-center gap-1.5 text-xs active:scale-95">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Simpan Gambar PNG</span>
                        </button>

                        <!-- Kirim Rincian via WA Bendahara -->
                        @if($simulasiWaUrl)
                            <a href="{{ $simulasiWaUrl }}" 
                               target="_blank" 
                               class="w-full py-2.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl transition-all shadow-xs flex items-center justify-center gap-1.5 text-xs">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                <span>Kirim Rincian via WA</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- SEKSI 1: TAGIHAN BULAN INI -->
    <div class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-xs font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Tagihan Bulan Ini ({{ $currentMonthName }} {{ $currentYear }})</span>
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
                                    {{ $this->getBillTypeLabel($bill->bill_type) }}
                                </span>
                                <h4 class="text-sm font-extrabold text-slate-900 dark:text-slate-100 mt-1">
                                    {{ $bill->notes ?? $this->getBillTypeLabel($bill->bill_type) }}
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

    <!-- SEKSI 2: TUNGGAKAN BULAN LALU -->
    <div class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-xs font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Tunggakan Bulan-Bulan Lalu</span>
            </h3>
        </div>

        @if($pastUnpaidBills->count() > 0)
            <div class="space-y-2.5">
                @foreach($pastUnpaidBills as $bill)
                    @php
                        $sisa = max(0, $bill->amount - $bill->amount_paid);
                    @endphp
                    <div class="bg-amber-50/70 dark:bg-slate-900 border-2 border-amber-200 dark:border-amber-500/30 rounded-2xl p-3.5 shadow-sm space-y-2.5 transition-colors">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="inline-block text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-md bg-amber-200/80 dark:bg-amber-500/20 text-amber-900 dark:text-amber-300 border border-amber-300 dark:border-amber-500/30">
                                    Tunggakan {{ $this->getMonthName($bill->period_month) }} {{ $bill->period_year }}
                                </span>
                                <h4 class="text-sm font-extrabold text-slate-900 dark:text-slate-100 mt-1">
                                    {{ $bill->notes ?? $this->getBillTypeLabel($bill->bill_type) }}
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
            </div>
        @else
            <div class="bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/40 rounded-2xl p-3.5 text-center text-xs text-emerald-800 dark:text-emerald-400 font-semibold flex items-center justify-center gap-1.5 transition-colors">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Tidak ada sisa tunggakan dari bulan-bulan sebelumnya.</span>
            </div>
        @endif
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
                                    Periode: {{ $this->getMonthName($bill->period_month) }} {{ $bill->period_year }}
                                </span>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 mt-0.5">
                                    {{ $bill->notes ?? $this->getBillTypeLabel($bill->bill_type) }}
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

    <!-- KOTAK PETUNJUK PEMBAYARAN DINAMIS (BERDASARKAN CMS PUTRA / PUTRI) -->
    <div class="bg-emerald-800 dark:bg-slate-900 text-white rounded-3xl p-5 shadow-lg border border-emerald-700/80 dark:border-slate-800 space-y-3 transition-colors">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-300 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h4 class="text-sm font-extrabold text-white">Petunjuk Pembayaran (Unit {{ $isPutri ? 'Putri' : 'Putra' }})</h4>
            </div>
            <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-md bg-emerald-700 dark:bg-slate-800 border border-emerald-600 dark:border-slate-700 text-emerald-100">
                {{ $isPutri ? '🧕 Unit Putri' : '👳‍♂️ Unit Putra' }}
            </span>
        </div>

        @if($waliAnnouncement)
            <div class="bg-emerald-900/60 dark:bg-slate-950 p-2.5 rounded-xl border border-emerald-700/50 dark:border-slate-800 text-xs text-emerald-100 dark:text-slate-300 leading-relaxed">
                📌 {{ $waliAnnouncement }}
            </div>
        @endif

        <div class="space-y-2">
            <!-- Bank 1 -->
            @if(!empty($bsiRekening))
                <div class="bg-emerald-900/80 dark:bg-slate-950 p-3 rounded-2xl border border-emerald-700/60 dark:border-slate-800 text-xs font-mono flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-emerald-300 dark:text-slate-400 font-sans block font-semibold uppercase">{{ $bank1Name }} ({{ $isPutri ? 'Putri' : 'Putra' }}):</span>
                        <div class="text-emerald-50 dark:text-emerald-400 font-bold text-sm">{{ $bsiRekening }}</div>
                        <div class="text-[10px] text-emerald-200 dark:text-slate-400 font-sans">a.n. {{ $bsiAn }}</div>
                    </div>
                    <button type="button" onclick="copyToClipboard('{{ $bsiRekening }}')" class="px-2.5 py-1 bg-emerald-700 hover:bg-emerald-600 text-white font-sans text-[11px] font-bold rounded-lg border border-emerald-600 transition-all flex items-center gap-1 active:scale-95">
                        <span>Salin</span>
                    </button>
                </div>
            @endif

            <!-- Bank 2 (Hanya tampil jika diisi di CMS) -->
            @if(!empty($briRekening))
                <div class="bg-emerald-900/80 dark:bg-slate-950 p-3 rounded-2xl border border-emerald-700/60 dark:border-slate-800 text-xs font-mono flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-emerald-300 dark:text-slate-400 font-sans block font-semibold uppercase">{{ $bank2Name }} ({{ $isPutri ? 'Putri' : 'Putra' }}):</span>
                        <div class="text-emerald-50 dark:text-emerald-400 font-bold text-sm">{{ $briRekening }}</div>
                        <div class="text-[10px] text-emerald-200 dark:text-slate-400 font-sans">a.n. {{ $briAn }}</div>
                    </div>
                    <button type="button" onclick="copyToClipboard('{{ $briRekening }}')" class="px-2.5 py-1 bg-emerald-700 hover:bg-emerald-600 text-white font-sans text-[11px] font-bold rounded-lg border border-emerald-600 transition-all flex items-center gap-1 active:scale-95">
                        <span>Salin</span>
                    </button>
                </div>
            @endif
        </div>

        <!-- WA Bendahara Button -->
        <a href="{{ $directWaUrl }}" target="_blank" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-2xl transition-all shadow-md flex items-center justify-center gap-2 text-xs tracking-wide">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
            <span>Hubungi {{ $waName }} via WA</span>
        </a>
    </div>
</div>
