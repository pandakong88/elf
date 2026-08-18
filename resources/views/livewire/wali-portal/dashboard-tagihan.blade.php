<div x-data="{ openSimulasi: false, openBayarModal: false, processingChannel: '' }" class="space-y-5 relative"
     x-on:livewire-payment-redirect.window="openBayarModal = false">
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

    <!-- Profil Santri Card Super Premium -->
    @php
        $activeRoom = $santri->roomAssignments->first()?->room;
        $activeDorm = $activeRoom?->dormitory;
        $activeKelas = $santri->madrasahEnrollments->first()?->kelas;
    @endphp
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-md overflow-hidden transition-colors">
        <!-- Top Accent Line -->
        <div class="h-1.5 w-full bg-gradient-to-r from-emerald-500 via-teal-500 to-sky-500"></div>

        <div class="p-4 sm:p-5 space-y-3.5">
            <!-- Header Status & Live Sync Pill -->
            <div class="flex items-center justify-between gap-2 flex-wrap pb-1">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                        <span>●</span>
                        <span>Santri Aktif</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[9px] font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/60 shadow-2xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Terakhir Di-update: <strong class="text-slate-800 dark:text-slate-100 font-extrabold">{{ $lastUpdatedLabel }}</strong></span>
                    </span>
                </div>
            </div>

            <!-- Profile Info: Avatar + Name + NIS -->
            <div class="flex items-center gap-3.5">
                <!-- Avatar Photo / Initials -->
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white flex items-center justify-center font-black text-xl shrink-0 shadow-md overflow-hidden border-2 border-white dark:border-slate-800">
                    @if($santri->photo)
                        <img src="{{ Storage::url($santri->photo) }}" alt="{{ $santri->name }}" class="w-full h-full object-cover">
                    @else
                        <span>{{ strtoupper(substr($santri->name, 0, 2)) }}</span>
                    @endif
                </div>

                <!-- Name & NIS -->
                <div class="flex-1 min-w-0 space-y-1">
                    <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white leading-snug truncate">
                        {{ $santri->name }}
                    </h2>
                    @if($santri->nis)
                        <div class="inline-flex items-center gap-1.5 text-[10px] font-mono font-extrabold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800/60 px-2 py-0.5 rounded-md border border-slate-200/80 dark:border-slate-700/60">
                            <span>💳 NIS:</span>
                            <span class="text-emerald-700 dark:text-emerald-400 font-bold">{{ $santri->nis }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Detail Grid Info Mobile-First (Asrama, Kamar, Kelas, Alamat) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
                <!-- 1. Komplek / Asrama -->
                <div class="bg-slate-50/80 dark:bg-slate-950/60 p-2.5 rounded-2xl border border-slate-200/60 dark:border-slate-800/80 space-y-0.5">
                    <span class="block text-[9px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider flex items-center gap-1 truncate">
                        <span>🏠</span>
                        <span>Komplek Asrama</span>
                    </span>
                    <strong class="text-slate-800 dark:text-slate-200 text-[11px] block truncate font-bold">
                        {{ $activeDorm ? $activeDorm->name : '-' }}
                    </strong>
                </div>

                <!-- 2. Nomor Kamar -->
                <div class="bg-slate-50/80 dark:bg-slate-950/60 p-2.5 rounded-2xl border border-slate-200/60 dark:border-slate-800/80 space-y-0.5">
                    <span class="block text-[9px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider flex items-center gap-1 truncate">
                        <span>🔑</span>
                        <span>Kamar</span>
                    </span>
                    <strong class="text-slate-800 dark:text-slate-200 text-[11px] block truncate font-bold">
                        {{ $activeRoom ? $activeRoom->name : '-' }}
                    </strong>
                </div>

                <!-- 3. Kelas Madrasah -->
                <div class="col-span-2 sm:col-span-1 bg-slate-50/80 dark:bg-slate-950/60 p-2.5 rounded-2xl border border-slate-200/60 dark:border-slate-800/80 space-y-0.5">
                    <span class="block text-[9px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider flex items-center gap-1 truncate">
                        <span>🏫</span>
                        <span>Kelas Madrasah</span>
                    </span>
                    <strong class="text-slate-800 dark:text-slate-200 text-[11px] block truncate font-bold">
                        {{ $activeKelas ? $activeKelas->name : '-' }}
                    </strong>
                </div>

                <!-- 4. Alamat Asal Santri -->
                @if(!empty($santri->address))
                    <div class="col-span-2 sm:col-span-3 bg-slate-50/80 dark:bg-slate-950/60 p-2.5 rounded-2xl border border-slate-200/60 dark:border-slate-800/80 space-y-0.5">
                        <span class="block text-[9px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider flex items-center gap-1">
                            <span>📍</span>
                            <span>Alamat Asal Santri</span>
                        </span>
                        <strong class="text-slate-800 dark:text-slate-200 text-[11px] block break-words font-semibold leading-relaxed">
                            {{ $santri->address }}
                        </strong>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- TAB SWITCHER: Tagihan & Bayar vs Riwayat Pembayaran -->
    <div class="grid grid-cols-2 gap-1.5 p-1.5 bg-slate-200/80 dark:bg-slate-900/90 rounded-2xl border border-slate-300/80 dark:border-slate-800 shadow-inner">
        <button type="button" wire:click="setPortalTab('tagihan')"
                class="py-2.5 px-3 rounded-xl text-xs font-black transition-all flex items-center justify-center gap-2 {{ $portalTab === 'tagihan' ? 'bg-white dark:bg-slate-800 text-emerald-700 dark:text-emerald-400 shadow-md border border-slate-200 dark:border-slate-700' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span>Tagihan & Bayar</span>
            @if($totalHarusDibayarNow > 0)
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
            @endif
        </button>

        <button type="button" wire:click="setPortalTab('riwayat')"
                class="py-2.5 px-3 rounded-xl text-xs font-black transition-all flex items-center justify-center gap-2 {{ $portalTab === 'riwayat' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-md border border-slate-200 dark:border-slate-700' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Riwayat Bayar</span>
            @if($historyTotalTrx > 0)
                <span class="px-1.5 py-0.5 rounded-full text-[9px] font-black {{ $portalTab === 'riwayat' ? 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300' : 'bg-slate-300 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                    {{ $historyTotalTrx }}
                </span>
            @endif
        </button>
    </div>

    @if($portalTab === 'tagihan')
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
    <div id="simulasiAccordionBox" class="bg-white dark:bg-slate-900 border-2 border-emerald-600/30 dark:border-slate-800 rounded-3xl p-4 shadow-md space-y-3 transition-colors">
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
                            $maxKekurangan = max(0, (float)$bill->amount - (float)$bill->amount_paid);
                            $cat = $bill->simulasi_cat ?? 'current';
                            $isSelected = in_array($bill->id, $selectedBillIds);
                            $customVal = $customAmounts[$bill->id] ?? null;
                            $hasCustom = isset($customVal) && is_numeric($customVal) && (float)$customVal > 0 && (float)$customVal < $maxKekurangan;
                            $currentPay = $hasCustom ? (float)$customVal : $maxKekurangan;
                        @endphp
                        <div class="rounded-2xl border-2 transition-all overflow-hidden
                                    {{ $isSelected
                                        ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-400 dark:border-emerald-600'
                                        : 'bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 hover:border-emerald-300 dark:hover:border-emerald-700' }}">

                            <label for="cb_{{ $bill->id }}" class="flex items-center gap-3 p-3 cursor-pointer">
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

                                {{-- Nominal sisa / Bayar --}}
                                <div class="text-right shrink-0">
                                    <span class="block text-xs font-black text-slate-900 dark:text-slate-100 font-mono">
                                        Rp {{ number_format($currentPay, 0, ',', '.') }}
                                    </span>
                                    @if($hasCustom)
                                        <span class="text-[10px] text-amber-700 dark:text-amber-400 font-bold block">Cicil Sebagian</span>
                                    @elseif($bill->status === 'partial')
                                        <span class="text-[10px] text-amber-600 dark:text-amber-400">sisa tagihan</span>
                                    @else
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500">lunas penuh</span>
                                    @endif
                                </div>
                            </label>

                            {{-- Form Atur Cicilan / Parsial (Hanya muncul jika dicentang) --}}
                            @if($isSelected)
                                <div class="px-3 pb-3 pt-1 border-t border-emerald-200/60 dark:border-emerald-800/40 bg-white/70 dark:bg-slate-900/70" x-data="{ isEditingCicilan: {{ $hasCustom ? 'true' : 'false' }} }">
                                    <div class="flex items-center justify-between gap-2 text-xs">
                                        <button type="button"
                                                @click="isEditingCicilan = !isEditingCicilan"
                                                class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 dark:text-emerald-400 hover:underline">
                                            <span>✍️</span>
                                            <span x-text="isEditingCicilan ? 'Tutup Pengaturan Cicil' : 'Ingin bayar sebagian / cicil tagihan ini?'"></span>
                                        </button>

                                        @if($hasCustom)
                                            <button type="button"
                                                    wire:click="resetCustomBillAmount('{{ $bill->id }}')"
                                                    class="text-[10px] font-extrabold text-rose-600 hover:underline">
                                                Kembalikan Lunas Penuh (Rp {{ number_format($maxKekurangan, 0, ',', '.') }})
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Input Nominal Cicilan --}}
                                    <div x-show="isEditingCicilan" x-collapse class="mt-2 pt-2 border-t border-slate-200/60 dark:border-slate-800 space-y-2">
                                        <div class="flex items-center justify-between text-[11px]">
                                            <span class="text-slate-600 dark:text-slate-400 font-semibold">Tentukan Nominal Pembayaran:</span>
                                            <span class="text-slate-500 dark:text-slate-400">Maks: <strong class="font-mono text-slate-800 dark:text-slate-200">Rp {{ number_format($maxKekurangan, 0, ',', '.') }}</strong></span>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <div class="relative flex-1">
                                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 font-mono">Rp</span>
                                                <input type="number"
                                                       wire:model.live.debounce.500ms="customAmounts.{{ $bill->id }}"
                                                       placeholder="{{ (int)$maxKekurangan }}"
                                                       min="5000"
                                                       max="{{ (int)$maxKekurangan }}"
                                                       step="5000"
                                                       class="w-full pl-9 pr-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white text-xs font-bold font-mono focus:ring-2 focus:ring-emerald-500">
                                            </div>

                                            {{-- Quick Shortcut: Bayar Setengah --}}
                                            @if($maxKekurangan >= 50000)
                                                <button type="button"
                                                        wire:click="setCustomBillAmount('{{ $bill->id }}', {{ round($maxKekurangan / 2) }})"
                                                        class="px-2.5 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-[11px] font-extrabold border border-slate-200 dark:border-slate-700 transition-all shrink-0">
                                                    50%
                                                </button>
                                            @endif

                                            <button type="button"
                                                    wire:click="resetCustomBillAmount('{{ $bill->id }}')"
                                                    class="px-2.5 py-1.5 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 hover:bg-emerald-200 rounded-xl text-[11px] font-extrabold border border-emerald-300 dark:border-emerald-500/30 transition-all shrink-0">
                                                Penuh
                                            </button>
                                        </div>

                                        @if($hasCustom)
                                            <div class="text-[10px] text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 p-2 rounded-lg border border-amber-200 dark:border-amber-800">
                                                💡 Anda membayar <strong>Rp {{ number_format($currentPay, 0, ',', '.') }}</strong> terlebih dahulu. Sisa kekurangan sebesar <strong>Rp {{ number_format(max(0, $maxKekurangan - $currentPay), 0, ',', '.') }}</strong> akan tetap tercatat di sistem sebagai tagihan sisa/cicilan.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
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

    <!-- FLOATING STICKY BAR KALKULATOR SIMULASI (MOBILE & DESKTOP) -->
    @if(count($selectedBillIds) > 0 && $simulasiTotal > 0)
        <div class="fixed bottom-4 left-3 right-3 sm:left-auto sm:right-6 sm:max-w-md z-40 transition-all duration-300">
            <div class="bg-slate-900/95 dark:bg-slate-950/95 backdrop-blur-md text-white p-3.5 rounded-2xl shadow-2xl border-2 border-emerald-500/80 flex items-center justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5 text-[10px] font-black uppercase text-emerald-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Simulasi ({{ count($selectedBillIds) }} Tagihan)</span>
                    </div>
                    <div class="text-base font-black text-white truncate font-mono">
                        Rp {{ number_format($simulasiTotal, 0, ',', '.') }}
                    </div>
                </div>

                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" 
                            @click="openSimulasi = !openSimulasi; if(openSimulasi) { $nextTick(() => { document.getElementById('simulasiAccordionBox').scrollIntoView({ behavior: 'smooth', block: 'center' }); }); }"
                            class="px-3 py-2 bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-extrabold rounded-xl text-xs transition-all shadow-md flex items-center gap-1">
                        <span>🧮</span>
                        <span x-text="openSimulasi ? 'Tutup' : 'Buka Detail'"></span>
                    </button>

                    {{-- TOMBOL BAYAR ONLINE --}}
                    <button type="button"
                            @click="openBayarModal = true"
                            class="px-3 py-2 bg-sky-500 hover:bg-sky-400 active:scale-95 text-white font-extrabold rounded-xl text-xs transition-all shadow-md flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Bayar Online</span>
                    </button>

                    @if($simulasiWaUrl)
                        <a href="{{ $simulasiWaUrl }}" target="_blank" 
                           class="p-2 bg-emerald-500/20 hover:bg-emerald-500 text-emerald-400 hover:text-white rounded-xl border border-emerald-500/40 transition-all text-xs flex items-center justify-center" 
                           title="Kirim Ke WA">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
    @else
        <!-- ================================================================= -->
        <!-- TAB 2: RIWAYAT PEMBAYARAN SANTRI                                 -->
        <!-- ================================================================= -->
        <div class="space-y-4">
            <!-- Hero Summary Riwayat -->
            <div class="bg-gradient-to-br from-indigo-700 via-indigo-800 to-slate-900 text-white rounded-3xl p-5 shadow-lg border border-indigo-600/30 relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
                
                <div class="flex items-center justify-between gap-2 pb-2 border-b border-indigo-500/30">
                    <span class="text-[10px] font-black uppercase tracking-wider text-indigo-200 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Total Akumulasi Pembayaran</span>
                    </span>
                    <span class="text-[10px] font-extrabold bg-indigo-500/30 text-indigo-100 px-2.5 py-0.5 rounded-full border border-indigo-400/20">
                        {{ $historyTotalTrx }} Transaksi
                    </span>
                </div>

                <div class="pt-3">
                    <div class="text-2xl sm:text-3xl font-black tracking-tight text-white font-mono">
                        Rp {{ number_format($historyTotalAmount, 0, ',', '.') }}
                    </div>
                    <div class="flex items-center gap-3 text-[11px] text-indigo-200 mt-2">
                        <span class="inline-flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                            <span>Online Duitku: <strong>{{ $historyGatewayTrx }}</strong></span>
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span>Kasir: <strong>{{ $historyKasirTrx }}</strong></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Filter Bar Riwayat -->
            <div class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-extrabold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        <span>Filter Riwayat</span>
                    </span>
                    @if($historyMethod !== '' || $historyYear !== '')
                        <button type="button" wire:click="$set('historyMethod', ''); $set('historyYear', '');"
                                class="text-[10px] text-rose-500 hover:text-rose-600 font-bold">
                            Reset Filter
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-2">
                    {{-- Filter Metode --}}
                    <div>
                        <select wire:model.live="historyMethod"
                                class="w-full text-xs font-semibold rounded-xl bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 py-2 px-2.5 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Metode</option>
                            <option value="gateway">⚡ Online (Gateway)</option>
                            <option value="kasir">💵 Kasir (Tunai / Bank)</option>
                        </select>
                    </div>

                    {{-- Filter Tahun --}}
                    <div>
                        <select wire:model.live="historyYear"
                                class="w-full text-xs font-semibold rounded-xl bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 py-2 px-2.5 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Tahun</option>
                            @foreach($historyYears as $y)
                                <option value="{{ $y }}">Tahun {{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- List Kartu Riwayat Pembayaran -->
            <div class="space-y-3">
                @forelse($paymentHistory as $item)
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 shadow-sm space-y-3 transition-all hover:border-indigo-300 dark:hover:border-indigo-700">
                        {{-- Header Item --}}
                        <div class="flex items-start justify-between gap-2 pb-2.5 border-b border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-9 h-9 rounded-2xl flex items-center justify-center shrink-0 {{ $item['source'] === 'gateway' ? 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' }}">
                                    @if($item['source'] === 'gateway')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-black text-slate-800 dark:text-slate-100 truncate">
                                        {{ $item['method_label'] }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 flex items-center gap-1.5 mt-0.5">
                                        <span>📅 {{ $item['date_fmt'] }}</span>
                                        <span>•</span>
                                        <span class="font-mono text-slate-600 dark:text-slate-400 font-bold">{{ $item['order_id'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider shrink-0 {{ str_contains($item['status'], 'Cicilan') ? 'bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-300/40' : 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-300/40' }}">
                                {{ $item['status'] }}
                            </span>
                        </div>

                        {{-- Rincian Tagihan yang Dibayarkan --}}
                        <div class="space-y-1.5 bg-slate-50 dark:bg-slate-950/60 p-3 rounded-2xl border border-slate-100 dark:border-slate-800/80">
                            <span class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider block">
                                Rincian Alokasi Tagihan:
                            </span>
                            <div class="space-y-1.5">
                                @foreach($item['breakdown'] as $b)
                                    <div class="flex items-center justify-between text-xs">
                                        <div class="min-w-0 pr-2">
                                            <span class="font-bold text-slate-700 dark:text-slate-300 block truncate">
                                                {{ $b['config_label'] ?? '—' }}
                                            </span>
                                            @if(!empty($b['period_label']))
                                                <span class="text-[10px] text-slate-400 dark:text-slate-500 block">
                                                    {{ $b['period_label'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="font-mono font-bold text-slate-800 dark:text-slate-200 shrink-0 text-xs">
                                            Rp {{ number_format($b['pay_portion'] ?? $b['net_amount'] ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            @if(!empty($item['notes']))
                                <div class="pt-1.5 mt-1.5 border-t border-slate-200/60 dark:border-slate-800 text-[10px] text-slate-500 dark:text-slate-400 italic">
                                    💬 {{ $item['notes'] }}
                                </div>
                            @endif

                            @if(!empty($item['logger_name']) && $item['source'] === 'kasir')
                                <div class="text-[9px] text-slate-400 dark:text-slate-500 pt-0.5">
                                    Petugas: {{ $item['logger_name'] }}
                                </div>
                            @endif
                        </div>

                        {{-- Footer Total & Download PDF --}}
                        <div class="flex items-center justify-between pt-1">
                            <div>
                                <span class="text-[9px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">
                                    Total Dibayar
                                </span>
                                <div class="text-sm font-black text-slate-900 dark:text-white font-mono">
                                    Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                </div>
                                @if(($item['mdr_amount'] ?? 0) > 0)
                                    <span class="text-[9px] text-amber-600 dark:text-amber-400 block">
                                        (Termasuk biaya layanan Rp {{ number_format($item['mdr_amount'], 0, ',', '.') }})
                                    </span>
                                @endif
                            </div>

                            {{-- Tombol Unduh PDF --}}
                            <a href="{{ $item['pdf_url'] }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-50 dark:bg-indigo-950/40 hover:bg-indigo-600 text-indigo-600 dark:text-indigo-400 hover:text-white rounded-2xl text-xs font-extrabold transition-all border border-indigo-200 dark:border-indigo-800 shadow-xs active:scale-95">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Unduh PDF</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 text-center space-y-3">
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Belum Ada Riwayat</h4>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 max-w-xs mx-auto leading-relaxed">
                                @if($historyMethod !== '' || $historyYear !== '')
                                    Tidak ada riwayat pembayaran yang cocok dengan filter yang dipilih.
                                @else
                                    Belum ada transaksi pembayaran yang tercatat untuk santri ini.
                                @endif
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- ================================================================= --}}
    {{-- MODAL PILIH CHANNEL PEMBAYARAN DUITKU                              --}}
    {{-- ================================================================= --}}
    <div x-show="openBayarModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
         style="display: none;">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="openBayarModal = false"></div>

        {{-- Modal Content --}}
        <div class="relative w-full sm:max-w-md bg-white dark:bg-slate-900 rounded-t-3xl sm:rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full sm:translate-y-4 opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0 opacity-100"
             x-transition:leave-end="translate-y-full sm:translate-y-4 opacity-0">

            {{-- Header Modal --}}
            <div class="h-1 w-full bg-gradient-to-r from-sky-500 via-blue-500 to-indigo-500"></div>
            <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">⚡ Pilih Metode Pembayaran</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                        Total: <strong class="text-sky-600 dark:text-sky-400 font-black">Rp {{ number_format($simulasiTotal, 0, ',', '.') }}</strong>
                        <span class="text-slate-400">+ biaya layanan</span>
                    </p>
                </div>
                <button @click="openBayarModal = false" class="p-1.5 rounded-xl text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Error Message --}}
            @if($paymentError)
                <div class="mx-4 mt-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/40 rounded-2xl flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-xs text-red-700 dark:text-red-400 font-semibold">{{ $paymentError }}</p>
                </div>
            @endif

            {{-- Daftar Channel --}}
            <div class="p-4 space-y-2.5 max-h-[60vh] overflow-y-auto">
                @php
                    $channels = config('duitku.enabled_channels', []);
                    $channelIcons = [
                        'SP' => '📱', 'BR' => '🏦', 'BT' => '🕌',
                        'I1' => '🏦', 'M2' => '🏦',
                    ];
                @endphp

                @foreach($channels as $code => $channel)
                    @php
                        $mdrAmount  = $simulasiTotal * ($channel['mdr_rate'] ?? 0) + ($channel['mdr_fixed'] ?? 0);
                        $totalBayar = $simulasiTotal + $mdrAmount;
                        $icon       = $channelIcons[$code] ?? '💳';
                    @endphp

                    <button type="button"
                            wire:click="initiateBayarOnline('{{ $code }}')"
                            wire:loading.attr="disabled"
                            @click="processingChannel = '{{ $code }}'"
                            :disabled="$wire.isProcessingPayment"
                            class="w-full text-left bg-white dark:bg-slate-800/60 hover:bg-sky-50 dark:hover:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 hover:border-sky-400 dark:hover:border-sky-600 rounded-2xl p-3.5 transition-all group disabled:opacity-60 disabled:cursor-not-allowed">

                        <div class="flex items-center gap-3">
                            {{-- Icon --}}
                            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-xl shrink-0 group-hover:bg-sky-100 dark:group-hover:bg-sky-900/30 transition-colors">
                                <span x-show="processingChannel !== '{{ $code }}' || !$wire.isProcessingPayment">{{ $icon }}</span>
                                <span x-show="processingChannel === '{{ $code }}' && $wire.isProcessingPayment">
                                    <svg class="w-5 h-5 animate-spin text-sky-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                                </span>
                            </div>

                            {{-- Info Channel --}}
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100 group-hover:text-sky-700 dark:group-hover:text-sky-400 transition-colors">
                                    {{ $channel['name'] }}
                                </div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">
                                    @if($mdrAmount > 0)
                                        Biaya layanan:
                                        @if(($channel['mdr_rate'] ?? 0) > 0)
                                            {{ ($channel['mdr_rate'] * 100) }}%
                                        @else
                                            Rp {{ number_format($channel['mdr_fixed'], 0, ',', '.') }}
                                        @endif
                                    @else
                                        Tanpa biaya layanan
                                    @endif
                                </div>
                            </div>

                            {{-- Total Bayar --}}
                            <div class="text-right shrink-0">
                                <div class="text-xs font-black text-slate-800 dark:text-slate-200 group-hover:text-sky-700 dark:group-hover:text-sky-400 transition-colors">
                                    Rp {{ number_format($totalBayar, 0, ',', '.') }}
                                </div>
                                @if($mdrAmount > 0)
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500">
                                        +Rp {{ number_format($mdrAmount, 0, ',', '.') }}
                                    </div>
                                @endif
                            </div>

                            {{-- Arrow --}}
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-sky-500 shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </button>
                @endforeach
            </div>

            {{-- Footer Info --}}
            <div class="px-4 pb-4 pt-1">
                <p class="text-[10px] text-slate-400 dark:text-slate-500 text-center leading-relaxed">
                    🔒 Pembayaran diproses secara aman oleh <strong>Duitku</strong>.
                    Setelah membayar, status tagihan akan otomatis diperbarui.
                </p>
            </div>
        </div>
    </div>

</div>

