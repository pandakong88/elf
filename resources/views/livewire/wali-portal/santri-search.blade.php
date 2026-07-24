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
                                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h4"/></svg>
                                        <span>Komplek:</span>
                                        <strong class="text-slate-800 dark:text-slate-200 truncate">{{ $activeDorm ? $activeDorm->name : '-' }} {{ $activeRoom ? '(' . $activeRoom->name . ')' : '' }}</strong>
                                    </div>
                                    <div class="flex items-center gap-1.5 font-medium">
                                        <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        <span>Madrasah:</span>
                                        <strong class="text-slate-800 dark:text-slate-200 truncate">{{ $activeKelas ? $activeKelas->name : '-' }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Big Friendly Button -->
                        <a href="{{ route('portal-wali.dashboard', $s->id) }}" 
                           class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-extrabold text-xs rounded-2xl transition-all shadow-md flex items-center justify-center gap-2 tracking-wide uppercase">
                            <span>Lihat Tagihan Anak</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="pt-2">
                {{ $santris->links() }}
            </div>
        @else
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 text-center space-y-3 shadow-sm transition-colors">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center mx-auto border border-amber-200 dark:border-amber-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div>
                    <h4 class="text-base font-extrabold text-slate-800 dark:text-slate-200">Nama Santri Tidak Ditemukan</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-xs mx-auto">
                        Pastikan ejaan nama anak sudah benar, atau coba ketik beberapa huruf dari nama anak saja (contoh: "Ahmad").
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
