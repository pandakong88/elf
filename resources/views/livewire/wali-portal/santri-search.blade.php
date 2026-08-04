<div class="space-y-5">
    <!-- Welcome Card -->
    <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 dark:from-emerald-800 dark:to-slate-900 rounded-3xl p-5 text-white shadow-lg relative overflow-hidden transition-colors">
        <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-white/10 rounded-full blur-xl"></div>
        
        <div class="relative z-10 space-y-2">
            <span class="inline-block text-[11px] font-bold bg-white/20 dark:bg-emerald-500/20 px-3 py-1 rounded-full text-emerald-100 dark:text-emerald-300 uppercase tracking-wider backdrop-blur-sm">
                Portal Wali Santri
            </span>
            <h2 class="text-xl font-black tracking-tight leading-snug">
                Assalamu'alaikum Wr. Wb.
            </h2>
            <p class="text-xs text-emerald-100/90 dark:text-slate-300 leading-relaxed font-normal">
                Selamat datang di layanan informasi tagihan santri Pondok Pesantren Al-Fithroh. Silakan cari nama putra/putri Bapak/Ibu di bawah ini.
            </p>
        </div>
    </div>

    <!-- Search Form -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 shadow-md border border-slate-200/80 dark:border-slate-800 space-y-3 transition-colors">
        <div>
            <label for="searchName" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Cari Nama Santri / Anak</span>
                </span>
                @if($searchName || $filterKomplek || $filterKamar || $filterKelas)
                    <button wire:click="$set('searchName', ''); $set('filterKomplek', ''); $set('filterKamar', ''); $set('filterKelas', '');" 
                            class="text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline">
                        Bersihkan Pencarian
                    </button>
                @endif
            </label>
            <div class="relative">
                <input type="text" 
                       id="searchName"
                       wire:model.live.debounce.300ms="searchName"
                       placeholder="Contoh: Ahmad, Fatimah, dll..." 
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border-2 border-slate-200 dark:border-slate-800 rounded-2xl text-sm font-bold text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-inner">
                @if($searchName)
                    <button wire:click="$set('searchName', '')" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 bg-slate-200 dark:bg-slate-800 rounded-full p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                @endif
            </div>
        </div>

        <!-- Filter Dropdown Opsional -->
        <div x-data="{ openFilter: false }" class="pt-2 border-t border-slate-100 dark:border-slate-800">
            <button type="button" @click="openFilter = !openFilter" class="w-full flex items-center justify-between text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-emerald-700 dark:hover:text-emerald-400 py-1 transition-colors">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    <span>Filter Berdasarkan Komplek / Kelas (Opsional)</span>
                </span>
                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openFilter }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="openFilter" x-collapse class="grid grid-cols-1 gap-2.5 pt-3">
                <!-- Filter Komplek -->
                <div>
                    <label for="filterKomplek" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Komplek / Asrama</label>
                    <select id="filterKomplek" wire:model.live="filterKomplek" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Semua Komplek</option>
                        @foreach($dormitories as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Kamar -->
                <div>
                    <label for="filterKamar" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Kamar</label>
                    <select id="filterKamar" wire:model.live="filterKamar" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Semua Kamar</option>
                        @foreach($rooms as $r)
                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Kelas -->
                <div>
                    <label for="filterKelas" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Kelas Madrasah</label>
                    <select id="filterKelas" wire:model.live="filterKelas" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Semua Kelas</option>
                        @foreach($kelases as $k)
                            <option value="{{ $k->id }}">{{ $k->name }} ({{ strtoupper($k->jenjang) }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div class="space-y-3">
        @if(!$hasQuery)
            <!-- Initial Empty State with Animation & Guidance -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 text-center border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-5 transition-all">
                <!-- Animated Pulse Radar Icon -->
                <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
                    <div class="absolute inset-0 bg-emerald-500/10 dark:bg-emerald-400/10 rounded-full animate-ping"></div>
                    <div class="absolute inset-2 bg-gradient-to-tr from-emerald-600 via-teal-600 to-emerald-700 rounded-2xl shadow-lg flex items-center justify-center transform -rotate-3 hover:rotate-0 transition-transform">
                        <svg class="w-9 h-9 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Guidance Heading & Text -->
                <div class="max-w-md mx-auto space-y-2">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200/60 dark:border-emerald-800/40 text-emerald-700 dark:text-emerald-300 text-xs font-black uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>✨ Terdapat Total {{ $totalSantriCount }} Santri Terdaftar</span>
                    </div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-slate-100">
                        Cari Data Santri / Putra-Putri Anda
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Silakan ketik <strong>nama santri / anak</strong> pada kolom pencarian di atas atau gunakan <strong>filter komplek / kelas</strong> untuk menampilkan daftar santri.
                    </p>
                </div>

                <!-- Quick Action Hints -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-center gap-2 text-xs">
                    <span class="text-slate-400 font-medium">Petunjuk:</span>
                    <button type="button" onclick="document.getElementById('searchName').focus()" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 text-slate-700 dark:text-slate-300 hover:text-emerald-600 text-xs font-bold transition-all border border-slate-200/60 dark:border-slate-700/60 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Ketik Nama Santri</span>
                    </button>
                    <button type="button" @click="openFilter = true" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 text-slate-700 dark:text-slate-300 hover:text-emerald-600 text-xs font-bold transition-all border border-slate-200/60 dark:border-slate-700/60 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        <span>Buka Filter Komplek / Kelas</span>
                    </button>
                </div>
            </div>
        @else
            <!-- Results View (Fades in when search or filter is active) -->
            <div class="flex items-center justify-between px-1">
                <span class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Hasil Pencarian</span>
                <span class="text-xs font-extrabold text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 px-2.5 py-0.5 rounded-full">
                    {{ $santris->total() }} Santri Ditemukan
                </span>
            </div>

            @if($santris->count() > 0)
                <div class="grid grid-cols-1 gap-3">
                    @foreach($santris as $s)
                        @php
                            $activeRoom = $s->roomAssignments->first()?->room;
                            $activeDorm = $activeRoom?->dormitory;
                            $activeKelas = $s->madrasahEnrollments->first()?->kelas;
                        @endphp
                        <div class="bg-white dark:bg-slate-900 border-2 border-slate-200/80 dark:border-slate-800 hover:border-emerald-500 dark:hover:border-emerald-500/70 rounded-3xl p-4 shadow-sm hover:shadow-md transition-all space-y-3">
                            <div class="flex items-center gap-3.5">
                                <!-- Avatar -->
                                <div class="w-13 h-13 rounded-2xl bg-emerald-700 text-white flex items-center justify-center font-black text-lg shrink-0 shadow-md overflow-hidden border-2 border-emerald-600">
                                    @if($s->photo)
                                        <img src="{{ Storage::url($s->photo) }}" alt="{{ $s->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span>{{ strtoupper(substr($s->name, 0, 2)) }}</span>
                                    @endif
                                </div>

                                <!-- Santri Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-extrabold text-slate-900 dark:text-slate-100 truncate">
                                        {{ $s->name }}
                                    </h3>

                                    <div class="space-y-1 mt-1 text-xs text-slate-600 dark:text-slate-400">
                                        <div class="flex items-center gap-1.5 font-medium">
                                            <span class="text-slate-400">NIS:</span>
                                            <span class="font-mono font-bold text-slate-800 dark:text-slate-200">{{ $s->santriProfile->additional_info['nis'] ?? ($s->santriProfile->nis ?? '-') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Details Badges -->
                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 dark:border-slate-800/60 text-xs">
                                <div class="bg-slate-50 dark:bg-slate-950 p-2.5 rounded-xl border border-slate-200/60 dark:border-slate-800 space-y-0.5">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Komplek &amp; Kamar</span>
                                    <div class="font-extrabold text-slate-800 dark:text-slate-200 truncate">
                                        {{ $activeDorm->name ?? 'Non-Asrama' }} - {{ $activeRoom->name ?? '-' }}
                                    </div>
                                </div>
                                <div class="bg-slate-50 dark:bg-slate-950 p-2.5 rounded-xl border border-slate-200/60 dark:border-slate-800 space-y-0.5">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Kelas Madrasah</span>
                                    <div class="font-extrabold text-emerald-600 dark:text-emerald-400 truncate">
                                        {{ $activeKelas->name ?? 'Belum ada kelas' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <a href="{{ route('wali-portal.dashboard-tagihan', $s->id) }}" 
                               class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-extrabold rounded-2xl text-xs flex items-center justify-center gap-2 shadow-md transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Lihat Tagihan &amp; Profil Santri ➔</span>
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pt-2">
                    {{ $santris->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 text-center border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/60 text-rose-500 flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h4 class="font-extrabold text-sm text-slate-800 dark:text-slate-200">Santri Tidak Ditemukan</h4>
                    <p class="text-xs text-slate-500">Tidak ada data santri yang cocok dengan kata kunci atau filter pencarian Anda.</p>
                </div>
            @endif
        @endif
    </div>

    {{-- Menu Bantuan & Rekening Pembayaran Bank (Tab Switcher Profesional) --}}
    <div x-data="{ activeTab: 'putra' }" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-4">
        
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-100 dark:border-emerald-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Rekening Pembayaran & Layanan Bantuan Wali</h3>
                    <p class="text-[10px] text-slate-400">Pilih komplek di bawah untuk melihat nomor rekening & kontak bendahara yang sesuai.</p>
                </div>
            </div>
        </div>

        @if($waliAnnouncement)
            <div class="bg-emerald-50 dark:bg-emerald-950/40 p-3 rounded-2xl border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-300 leading-relaxed">
                📌 {{ $waliAnnouncement }}
            </div>
        @endif

        {{-- Tab Switcher Putra / Putri (Professional SVG Icons) --}}
        <div class="grid grid-cols-2 gap-2 bg-slate-100 dark:bg-slate-950 p-1.5 rounded-2xl border border-slate-200/80 dark:border-slate-800">
            <button type="button" @click="activeTab = 'putra'" 
                    class="py-2.5 px-3 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2"
                    :class="activeTab === 'putra' ? 'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-sm border border-slate-200/50 dark:border-slate-700' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'">
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h4"/></svg>
                <span>Komplek Putra</span>
            </button>
            <button type="button" @click="activeTab = 'putri'" 
                    class="py-2.5 px-3 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2"
                    :class="activeTab === 'putri' ? 'bg-white dark:bg-slate-800 text-pink-600 dark:text-pink-400 shadow-sm border border-slate-200/50 dark:border-slate-700' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'">
                <svg class="w-4 h-4 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Komplek Putri</span>
            </button>
        </div>

        {{-- Tab Content: Putra --}}
        <div x-show="activeTab === 'putra'" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                {{-- BSI Putra --}}
                @if(!empty($putraData['bsi']))
                    <div class="bg-slate-50 dark:bg-slate-950 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ $putraData['bank1_name'] }} (Putra)</span>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-mono font-bold text-slate-900 dark:text-emerald-400">{{ $putraData['bsi'] }}</span>
                            <button type="button" onclick="copyToClipboard('{{ $putraData['bsi'] }}')" class="px-2.5 py-1 bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-bold rounded-lg transition-all shadow-sm">Salin</button>
                        </div>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 block">a.n. {{ $putraData['bsi_an'] }}</span>
                    </div>
                @endif

                {{-- BRI Putra --}}
                @if(!empty($putraData['bri']))
                    <div class="bg-slate-50 dark:bg-slate-950 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ $putraData['bank2_name'] }} (Putra)</span>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-mono font-bold text-slate-900 dark:text-emerald-400">{{ $putraData['bri'] }}</span>
                            <button type="button" onclick="copyToClipboard('{{ $putraData['bri'] }}')" class="px-2.5 py-1 bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-bold rounded-lg transition-all shadow-sm">Salin</button>
                        </div>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 block">a.n. {{ $putraData['bri_an'] }}</span>
                    </div>
                @endif
            </div>

            {{-- WA Bendahara Putra --}}
            <a href="{{ $putraData['wa_url'] }}" target="_blank" 
               class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl transition-all shadow-md flex items-center justify-center gap-2 text-xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span>Chat WhatsApp {{ $putraData['wa_name'] }}</span>
            </a>
        </div>

        {{-- Tab Content: Putri --}}
        <div x-show="activeTab === 'putri'" class="space-y-3" style="display: none;">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                {{-- BSI Putri --}}
                @if(!empty($putriData['bsi']))
                    <div class="bg-slate-50 dark:bg-slate-950 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ $putriData['bank1_name'] }} (Putri)</span>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-mono font-bold text-slate-900 dark:text-pink-400">{{ $putriData['bsi'] }}</span>
                            <button type="button" onclick="copyToClipboard('{{ $putriData['bsi'] }}')" class="px-2.5 py-1 bg-pink-500 hover:bg-pink-600 text-white text-[10px] font-bold rounded-lg transition-all shadow-sm">Salin</button>
                        </div>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 block">a.n. {{ $putriData['bsi_an'] }}</span>
                    </div>
                @endif

                {{-- BRI Putri --}}
                @if(!empty($putriData['bri']))
                    <div class="bg-slate-50 dark:bg-slate-950 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ $putriData['bank2_name'] }} (Putri)</span>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-mono font-bold text-slate-900 dark:text-pink-400">{{ $putriData['bri'] }}</span>
                            <button type="button" onclick="copyToClipboard('{{ $putriData['bri'] }}')" class="px-2.5 py-1 bg-pink-500 hover:bg-pink-600 text-white text-[10px] font-bold rounded-lg transition-all shadow-sm">Salin</button>
                        </div>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 block">a.n. {{ $putriData['bri_an'] }}</span>
                    </div>
                @endif
            </div>

            {{-- WA Bendahara Putri --}}
            <a href="{{ $putriData['wa_url'] }}" target="_blank" 
               class="w-full py-3 bg-pink-600 hover:bg-pink-700 text-white font-bold rounded-2xl transition-all shadow-md flex items-center justify-center gap-2 text-xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span>Chat WhatsApp {{ $putriData['wa_name'] }}</span>
            </a>
        </div>
    </div>
</div>
