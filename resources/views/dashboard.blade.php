<x-app-layout>
    <div class="space-y-8">
        <!-- 1. HERO STAGE: ARCADE GAME HUB (TRI-PEAKS SOLITAIRE & ELVITH ROYALE HI-LO) -->
        <div x-data="{ 
            activeArcade: localStorage.getItem('elf_active_arcade') || 'tripeaks',
            setGame(game) {
                this.activeArcade = game;
                localStorage.setItem('elf_active_arcade', game);
            }
        }" class="space-y-4">
            
            <!-- Game Switcher Tab Selector -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-950/80 border border-slate-800 p-2 sm:p-2.5 rounded-2xl sm:rounded-3xl shadow-xl backdrop-blur-xl">
                <div class="flex items-center gap-2 px-2">
                    <span class="text-xl">🎮</span>
                    <div>
                        <h3 class="text-xs sm:text-sm font-black text-white uppercase tracking-wider font-serif-display">
                            Arcade Hub Admin
                        </h3>
                        <p class="text-[10px] text-slate-400">Pilih mini-game kartu santai &amp; kompetitif untuk pengisi waktu istirahat</p>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 p-1 bg-slate-900/90 rounded-xl sm:rounded-2xl border border-slate-800 self-stretch sm:self-auto">
                    <!-- Tab 1: Tri-Peaks Pyramid -->
                    <button 
                        type="button" 
                        @click="setGame('tripeaks')"
                        :class="activeArcade === 'tripeaks' 
                            ? 'bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 shadow-md font-black' 
                            : 'text-slate-400 hover:text-white font-bold hover:bg-slate-800/60'"
                        class="flex-1 sm:flex-initial px-3.5 sm:px-5 py-2 rounded-lg sm:rounded-xl text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <span>🏔️ Tri-Peaks Solitaire</span>
                        <span class="px-1.5 py-0.2 rounded-md text-[9px] font-black uppercase tracking-wider bg-red-600 text-white animate-pulse">
                            BARU 🔥
                        </span>
                    </button>

                    <!-- Tab 2: Hi-Lo Royale -->
                    <button 
                        type="button" 
                        @click="setGame('hilo')"
                        :class="activeArcade === 'hilo' 
                            ? 'bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 shadow-md font-black' 
                            : 'text-slate-400 hover:text-white font-bold hover:bg-slate-800/60'"
                        class="flex-1 sm:flex-initial px-3.5 sm:px-5 py-2 rounded-lg sm:rounded-xl text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <span>👑 Elvith Royale Hi-Lo</span>
                        <span class="px-1.5 py-0.2 rounded-md text-[9px] font-black uppercase tracking-wider bg-slate-800 text-slate-300">
                            KLASIK
                        </span>
                    </button>
                </div>
            </div>

            <!-- Active Game Display -->
            <div x-show="activeArcade === 'tripeaks'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                @livewire('dashboard.tripeaks-arena')
            </div>

            <div x-show="activeArcade === 'hilo'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                @livewire('dashboard.card-game-arena')
            </div>

        </div>

        <!-- 2. Executive Welcome Banner -->
        <div class="bg-slate-950 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 text-white rounded-3xl p-6 sm:p-8 border border-slate-700/60 shadow-xl relative overflow-hidden" style="background-color: #020617;">
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">
                            Sistem ELF Al-Fithroh
                        </span>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-slate-800 border border-slate-700 text-slate-300 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>{{ $stats['gregorian_date'] }}</span>
                        </span>
                        @if($stats['gender_scope'])
                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-amber-500/10 border border-amber-500/20 text-amber-300">
                                Scope: {{ $stats['gender_scope'] === 'L' ? '👦 Putra' : '👧 Putri' }}
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-blue-500/10 border border-blue-500/20 text-blue-300">
                                🌐 Scope Global (Putra &amp; Putri)
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white font-serif-display">
                        Ahlan wa Sahlan, {{ $user->name }}!
                    </h1>
                    <p class="text-slate-400 text-xs sm:text-sm max-w-2xl leading-relaxed">
                        Selamat datang di Pusat Kontrol Operasional Pondok Pesantren &amp; Diniyyah. Pantau metrik statistik, tagihan, perizinan, dan aktivitas harian santri secara real-time.
                    </p>
                </div>

                <div class="flex flex-wrap lg:flex-col items-start lg:items-end gap-2 shrink-0">
                    <div class="flex flex-wrap gap-1.5 justify-end">
                        @foreach ($roles as $role)
                            <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold rounded-xl shadow-xs">
                                🛡️ {{ $role }}
                            </span>
                        @endforeach
                    </div>
                    <span class="text-[11px] text-slate-400 font-mono bg-slate-900/80 px-3 py-1 rounded-lg border border-slate-800">
                        {{ $user->email }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 3. Interactive Widget Kalender Masehi & Hijriah -->
        <div x-data="{
            monthOffset: 0,
            currentMonthName: '{{ $calendarData['masehi_month_name'] }}',
            currentHijriName: '{{ $calendarData['hijri_month_name'] }}',
            currentTimeStr: '{{ now()->setTimezone('Asia/Jakarta')->format('H:i:s') }} WIB',
            init() {
                setInterval(() => {
                    const now = new Date();
                    this.currentTimeStr = now.toLocaleTimeString('id-ID', { timeZone: 'Asia/Jakarta' }) + ' WIB (Asia/Jakarta)';
                }, 1000);
            }
        }" class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 p-6 sm:p-8 rounded-3xl shadow-sm space-y-6">
            
            <!-- Calendar Header with Prev/Next Controls -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white uppercase tracking-wider font-serif-display flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Kalender Masehi &amp; Hijriah Interaktif</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Penanggalan Masehi &amp; Hijriah resmi dengan zona waktu <strong class="text-emerald-600 dark:text-emerald-400">Asia/Jakarta (WIB)</strong>.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-mono font-bold flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span x-text="currentTimeStr"></span>
                    </div>

                    <div class="px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl text-xs font-black">
                        🗓️ Masehi: <strong x-text="currentMonthName"></strong>
                    </div>

                    <div class="px-3 py-1.5 bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 rounded-xl text-xs font-black">
                        🌙 Hijriah: <strong x-text="currentHijriName"></strong>
                    </div>
                </div>
            </div>

            <!-- Main Calendar Grid & Important Events Split Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- Left: Highlight Today & Hari-Hari Penting (5 Cols) -->
                <div class="lg:col-span-5 space-y-5">
                    <!-- Today Highlight Box -->
                    <div class="bg-slate-950 bg-gradient-to-br from-slate-900 via-slate-950 to-slate-900 text-white border border-slate-800 p-6 rounded-3xl shadow-lg relative overflow-hidden space-y-4" style="background-color: #020617;">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/15 rounded-full blur-2xl pointer-events-none"></div>

                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Waktu Sekarang (WIB)
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono" x-text="currentTimeStr"></span>
                        </div>

                        <div class="space-y-1">
                            <span class="text-xs text-slate-400 uppercase font-bold tracking-wider">Tanggal Masehi Hari Ini</span>
                            <h2 class="text-2xl font-black text-white font-serif-display tracking-tight">{{ $calendarData['today_masehi'] }}</h2>
                        </div>

                        <div class="pt-3 border-t border-slate-800 space-y-1">
                            <span class="text-xs text-amber-400/90 uppercase font-bold tracking-wider flex items-center gap-1">
                                🌙 Tanggal Hijriah Hari Ini
                            </span>
                            <h3 class="text-xl font-extrabold text-amber-300 tracking-tight">{{ $calendarData['today_hijri'] }}</h3>
                        </div>
                    </div>

                    <!-- Important Days & PHBI Card -->
                    <div class="bg-slate-50/80 dark:bg-slate-950/60 border border-slate-200/80 dark:border-slate-800 p-5 rounded-3xl space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-200/60 dark:border-slate-800 pb-3">
                            <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                                <span>📌 Agenda &amp; Hari Penting (PHBI &amp; Nasional)</span>
                            </h4>
                        </div>

                        <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
                            @foreach($importantEvents as $evt)
                                <div class="p-3 bg-white dark:bg-slate-900 border border-slate-200/70 dark:border-slate-800 rounded-2xl flex items-center justify-between gap-3 text-xs shadow-xs hover:border-emerald-500/40 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xl shrink-0">{{ $evt['icon'] }}</span>
                                        <div class="space-y-0.5">
                                            <h5 class="font-bold text-slate-800 dark:text-slate-200 leading-tight">{{ $evt['title'] }}</h5>
                                            <span class="text-[10px] text-slate-400 block font-mono">{{ $evt['date'] }}</span>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-wider shrink-0 shadow-xs border
                                        {{ $evt['category'] === 'nasional' ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20' : '' }}
                                        {{ $evt['category'] === 'santri' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20' : '' }}
                                        {{ $evt['category'] === 'islamic' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20' : '' }}
                                    ">
                                        {{ $evt['badge'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right: Monthly Grid Calendar (7 Cols) -->
                <div class="lg:col-span-7 bg-slate-50/80 dark:bg-slate-950/60 border border-slate-200/80 dark:border-slate-800/80 p-5 rounded-3xl space-y-4">
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                                Grid Penanggalan Bulanan
                            </h4>
                            <p class="text-[10px] text-slate-400">Atas: Masehi &bull; Bawah: Hijriah</p>
                        </div>

                        <!-- Reset / Today Button -->
                        <button type="button" @click="monthOffset = 0; $dispatch('toast', 'Kembali ke bulan berjalan');" class="px-3 py-1 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-[10px] font-extrabold rounded-xl transition-all">
                            Hari Ini
                        </button>
                    </div>

                    <!-- Days Header -->
                    <div class="grid grid-cols-7 gap-1 sm:gap-1.5 text-center text-[10px] sm:text-[11px] font-black uppercase text-slate-400 tracking-wider py-1.5 border-b border-slate-200/60 dark:border-slate-800">
                        <span class="text-rose-500">Ahd</span>
                        <span>Sen</span>
                        <span>Sel</span>
                        <span>Rab</span>
                        <span>Kam</span>
                        <span class="text-emerald-500">Jum</span>
                        <span>Sab</span>
                    </div>

                    <!-- Days Grid -->
                    <div class="grid grid-cols-7 gap-1 sm:gap-2">
                        @foreach($calendarData['calendar_days'] as $day)
                            @if(is_null($day))
                                <div class="h-10 sm:h-14 bg-transparent rounded-xl sm:rounded-2xl"></div>
                            @else
                                <div class="h-10 sm:h-14 rounded-xl sm:rounded-2xl border p-1 sm:p-2 flex flex-col justify-between transition-all relative overflow-hidden group cursor-pointer {{ $day['is_today'] ? 'bg-emerald-600 text-white border-emerald-500 shadow-md font-bold ring-2 ring-emerald-400/50' : 'bg-white dark:bg-slate-900 border-slate-200/70 dark:border-slate-800 text-slate-800 dark:text-slate-200 hover:border-emerald-500/50 hover:shadow-xs' }}">
                                    <div class="flex items-center justify-between leading-none">
                                        <span class="text-[10px] sm:text-xs font-extrabold {{ $day['is_today'] ? 'text-white' : '' }}">{{ $day['day'] }}</span>
                                    </div>
                                    <div class="text-right leading-none">
                                        <span class="text-[8px] sm:text-[9px] font-bold {{ $day['is_today'] ? 'text-amber-200' : 'text-amber-600 dark:text-amber-400' }}">{{ $day['hijri_day'] }}</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="pt-2 text-center text-[10px] text-slate-400">
                        <span>💡 Kalender Masehi &amp; Hijriah disinkronkan secara otomatis sesuai dengan penetapan zona waktu <strong class="text-slate-600 dark:text-slate-300">WIB (Asia/Jakarta)</strong>.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Role-Based Quick Actions Shortcuts Grid (Di Bawah Kalender) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 p-6 sm:p-8 rounded-3xl shadow-sm space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white uppercase tracking-wider font-serif-display flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Pintasan Aksi Cepat Operasional</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Modul utama yang siap diakses berdasarkan wewenang otorisasi akun Anda.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Keuangan: Pusat Keuangan & Kasir -->
                @if($user->hasRole('super-admin') || $user->hasRole('bendahara-pondok') || $user->hasRole('bendahara-unit') || $user->hasRole('bendahara-putra') || $user->hasRole('bendahara-putri') || $user->can('view-tagihan') || $user->can('record-pembayaran') || $user->can('manage-billing-config'))
                    <a href="{{ route('keuangan.billing') }}" class="group p-5 border border-slate-200/80 dark:border-slate-800 hover:border-emerald-500/50 hover:bg-emerald-50/20 dark:hover:bg-emerald-950/20 rounded-2xl transition-all duration-300 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 text-sm">Pusat Keuangan &amp; Kasir</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Kasir utama, pembayaran syahriah, dan konfigurasi tarif.</p>
                            </div>
                        </div>
                        <span class="mt-4 text-[11px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Buka Modul &rarr;</span>
                    </a>
                @endif

                <!-- Lembar Setoran Kolektif -->
                @if($user->hasRole('super-admin') || $user->hasRole('bendahara-pondok') || $user->hasRole('manajemen') || $user->hasRole('pengasuh') || $user->can('manage-setoran-kolektif'))
                    <a href="{{ route('keuangan.lembar-setoran') }}" class="group p-5 border border-slate-200/80 dark:border-slate-800 hover:border-emerald-500/50 hover:bg-emerald-50/20 dark:hover:bg-emerald-950/20 rounded-2xl transition-all duration-300 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-teal-600 dark:group-hover:text-teal-400 text-sm">Lembar Setoran Kolektif</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Verifikasi setoran kasir harian ke bendahara pusat.</p>
                            </div>
                        </div>
                        <span class="mt-4 text-[11px] font-bold text-teal-600 dark:text-teal-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Buka Modul &rarr;</span>
                    </a>
                @endif

                <!-- Perizinan Santri -->
                @can('view-perizinan')
                    <a href="{{ route('kepengasuhan.perizinan') }}" class="group p-5 border border-slate-200/80 dark:border-slate-800 hover:border-blue-500/50 hover:bg-blue-50/20 dark:hover:bg-blue-950/20 rounded-2xl transition-all duration-300 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 text-sm">Buku Perizinan Santri</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Catat izin santri keluar, checkout/checkin, dan persetujuan.</p>
                            </div>
                        </div>
                        <span class="mt-4 text-[11px] font-bold text-blue-600 dark:text-blue-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Buka Modul &rarr;</span>
                    </a>
                @endcan

                <!-- Buku Pelanggaran -->
                @can('view-pelanggaran')
                    <a href="{{ route('kepengasuhan.violations') }}" class="group p-5 border border-slate-200/80 dark:border-slate-800 hover:border-rose-500/50 hover:bg-rose-50/20 dark:hover:bg-rose-950/20 rounded-2xl transition-all duration-300 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-rose-600 dark:group-hover:text-rose-400 text-sm">Buku Pelanggaran</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Pencatatan jenis pelanggaran &amp; akumulasi poin santri.</p>
                            </div>
                        </div>
                        <span class="mt-4 text-[11px] font-bold text-rose-600 dark:text-rose-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Buka Modul &rarr;</span>
                    </a>
                @endcan

                <!-- Master Data Santri -->
                <a href="{{ route('kepengasuhan.peta-santri') }}" class="group p-5 border border-slate-200/80 dark:border-slate-800 hover:border-purple-500/50 hover:bg-purple-50/20 dark:hover:bg-purple-950/20 rounded-2xl transition-all duration-300 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-purple-600 dark:group-hover:text-purple-400 text-sm">Master Tabel Santri</h4>
                            <p class="text-slate-400 text-xs mt-1 leading-relaxed">Kelola biodata santri mukim, laju, dan riwayat status santri.</p>
                        </div>
                    </div>
                    <span class="mt-4 text-[11px] font-bold text-purple-600 dark:text-purple-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Buka Modul &rarr;</span>
                </a>

                <!-- Asrama & Kamar -->
                @if($user->hasRole('super-admin') || $user->hasRole('pengasuh') || $user->can('manage-asrama'))
                    <a href="{{ route('kepengasuhan.asrama-kelas') }}" class="group p-5 border border-slate-200/80 dark:border-slate-800 hover:border-amber-500/50 hover:bg-amber-50/20 dark:hover:bg-amber-950/20 rounded-2xl transition-all duration-300 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-amber-600 dark:group-hover:text-amber-400 text-sm">Pusat Kendali Asrama &amp; Kelas</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Kapasitas gedung, peta kamar, dan rombel madrasah.</p>
                            </div>
                        </div>
                        <span class="mt-4 text-[11px] font-bold text-amber-600 dark:text-amber-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Buka Modul &rarr;</span>
                    </a>
                @endif

                <!-- Majek / Katering -->
                @if($user->hasRole('super-admin') || $user->hasRole('manajemen') || $user->hasRole('pengasuh') || $user->can('manage-majek'))
                    <a href="{{ route('keuangan.majek') }}" class="group p-5 border border-slate-200/80 dark:border-slate-800 hover:border-orange-500/50 hover:bg-orange-50/20 dark:hover:bg-orange-950/20 rounded-2xl transition-all duration-300 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-orange-600 dark:group-hover:text-orange-400 text-sm">Majek (Katering Santri)</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Manajemen katering porsi makan pagi &amp; sore santri.</p>
                            </div>
                        </div>
                        <span class="mt-4 text-[11px] font-bold text-orange-600 dark:text-orange-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Buka Modul &rarr;</span>
                    </a>
                @endif

                <!-- Wizard Kenaikan Kelas -->
                @if($user->hasRole('super-admin') || $user->hasRole('manajemen') || $user->can('manage-kenaikan-kelas'))
                    <a href="{{ route('madrasah.kenaikan-kelas') }}" class="group p-5 border border-slate-200/80 dark:border-slate-800 hover:border-indigo-500/50 hover:bg-indigo-50/20 dark:hover:bg-indigo-950/20 rounded-2xl transition-all duration-300 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 text-sm">Wizard Kenaikan Kelas</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Proses promosi kenaikan &amp; kelulusan kelas massal.</p>
                            </div>
                        </div>
                        <span class="mt-4 text-[11px] font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Buka Modul &rarr;</span>
                    </a>
                @endif

                <!-- CMS Portal Wali -->
                @if($user->hasRole('super-admin') || $user->hasRole('manajemen') || $user->can('manage-roles'))
                    <a href="{{ route('system.wali-cms') }}" class="group p-5 border border-slate-200/80 dark:border-slate-800 hover:border-emerald-500/50 hover:bg-emerald-50/20 dark:hover:bg-emerald-950/20 rounded-2xl transition-all duration-300 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 text-sm">CMS Portal Wali</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Pengaturan nomor rekening bank &amp; WA Bendahara.</p>
                            </div>
                        </div>
                        <span class="mt-4 text-[11px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Buka Modul &rarr;</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
