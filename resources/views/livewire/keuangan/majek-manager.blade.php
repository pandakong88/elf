<div class="min-h-screen bg-slate-50 dark:bg-slate-950 p-3 sm:p-4 md:p-6">

    {{-- Flash Messages (Toast Banners) --}}
    @if(session('majek_success') || $flashSuccess)
        @php $successMsg = session('majek_success') ?: $flashSuccess; @endphp
        <div wire:key="flash-success-{{ md5($successMsg) }}" x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2"
             class="mb-4 px-4 py-3.5 bg-emerald-500/15 border border-emerald-500/30 text-emerald-800 dark:text-emerald-200 rounded-2xl text-xs font-black flex items-center justify-between shadow-md">
            <div class="flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center font-bold text-xs shrink-0">✓</span>
                <span>{{ $successMsg }}</span>
            </div>
            <button type="button" @click="show = false" class="text-emerald-700 dark:text-emerald-300 hover:text-emerald-900 font-bold text-sm px-1.5">✕</button>
        </div>
    @endif
    @if($flashError)
        <div wire:key="flash-error-{{ md5($flashError) }}" x-data="{ show: true }" x-init="setTimeout(() => show = false, 6000)" x-show="show"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2"
             class="mb-4 px-4 py-3.5 bg-rose-500/15 border border-rose-500/30 text-rose-800 dark:text-rose-200 rounded-2xl text-xs font-black flex items-center justify-between shadow-md">
            <div class="flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-lg bg-rose-500 text-white flex items-center justify-center font-bold text-xs shrink-0">⚠️</span>
                <span>{{ $flashError }}</span>
            </div>
            <button type="button" @click="show = false" class="text-rose-700 dark:text-rose-300 hover:text-rose-900 font-bold text-sm px-1.5">✕</button>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- HEADER: Navigasi Bulan & Status Periode                                --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-3xl shadow-xs mb-4 sm:mb-5 overflow-hidden">
        <div class="p-4 sm:px-6 sm:py-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            {{-- Kiri: Judul & Bulan --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between lg:justify-start gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/10 flex items-center justify-center text-xl shrink-0">
                        🍽️
                    </div>
                    <div>
                        <h1 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wide">Majek (Katering)</h1>
                        <p class="text-[10px] text-slate-400 font-semibold">Pendaftaran & Setoran Pembayaran Katering</p>
                    </div>
                </div>

                {{-- Navigasi Bulan --}}
                <div class="flex items-center justify-between sm:justify-start gap-2 bg-slate-100 dark:bg-slate-800 rounded-2xl px-2 py-1.5 self-stretch sm:self-auto">
                    <button type="button" wire:click="decrementMonth"
                        class="w-8 h-8 sm:w-7 sm:h-7 rounded-xl bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 font-bold text-sm flex items-center justify-center shadow-xs transition-all">
                        ◀
                    </button>
                    <span class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wide px-2 flex-1 sm:min-w-32 text-center">
                        {{ $this->monthLabel }}
                    </span>
                    <button type="button" wire:click="incrementMonth"
                        class="w-8 h-8 sm:w-7 sm:h-7 rounded-xl bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 font-bold text-sm flex items-center justify-center shadow-xs transition-all">
                        ▶
                    </button>
                </div>
            </div>

            {{-- Kanan: Status Periode --}}
            @if($this->activePeriod)
                <div class="grid grid-cols-3 sm:flex items-center gap-2 sm:gap-3 text-[10px] bg-slate-50/80 dark:bg-slate-950/40 p-2.5 sm:p-0 rounded-2xl border border-slate-200/50 dark:border-slate-800 sm:border-none">
                    <div class="text-center sm:text-left">
                        <div class="text-slate-400 font-bold uppercase tracking-wider text-[9px]">Hari Aktif</div>
                        <div class="font-black text-slate-800 dark:text-slate-100 text-xs sm:text-sm">{{ $this->activePeriod->active_days }} hari</div>
                    </div>
                    <div class="hidden sm:block w-px h-6 bg-slate-200 dark:bg-slate-700"></div>
                    <div class="text-center sm:text-left">
                        <div class="text-slate-400 font-bold uppercase tracking-wider text-[9px]">👦 Putra (1x / 2x)</div>
                        <div class="font-black text-amber-600 text-[11px] sm:text-xs">Rp {{ number_format($this->tarif1x, 0, ',', '.') }} / {{ number_format($this->tarif2x, 0, ',', '.') }}</div>
                    </div>
                    <div class="hidden sm:block w-px h-6 bg-slate-200 dark:bg-slate-700"></div>
                    <div class="text-center sm:text-left">
                        <div class="text-slate-400 font-bold uppercase tracking-wider text-[9px]">👧 Putri (1x / 2x)</div>
                        <div class="font-black text-purple-600 dark:text-purple-400 text-[11px] sm:text-xs">Rp {{ number_format($this->tarif1xPutri, 0, ',', '.') }} / {{ number_format($this->tarif2xPutri, 0, ',', '.') }}</div>
                    </div>
                    <button type="button" wire:click="openPeriodModal"
                        class="col-span-3 sm:col-span-1 mt-1 sm:mt-0 sm:ml-1 px-3 py-1.5 bg-slate-200/70 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-[10px] font-bold transition-all text-center">
                        ⚙️ Edit Periode
                    </button>
                </div>
            @else
                <button type="button" wire:click="openPeriodModal"
                    class="w-full sm:w-auto px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-2">
                    ⚙️ Buat Konfigurasi Periode
                </button>
            @endif
        </div>

        {{-- Notes Periode --}}
        @if($this->activePeriod?->notes)
            <div class="px-4 sm:px-6 py-2.5 bg-amber-50 dark:bg-amber-950/20 border-t border-amber-200/60 dark:border-amber-800/30 text-[10px] text-amber-700 dark:text-amber-400 font-semibold flex items-center gap-2">
                📝 {{ $this->activePeriod->notes }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MAIN CONTENT                                                            --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-3xl shadow-xs overflow-hidden transition-all @if($countChecked > 0) pb-28 sm:pb-0 @endif">

        {{-- Sub-header: Statistik + Tombol Tambah & Salin --}}
        <div class="p-4 sm:px-6 sm:py-4 border-b border-slate-100 dark:border-slate-800 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-slate-50/20 dark:bg-slate-950/5">
            {{-- Stat Cards Grid (Mobile 2x2, Desktop 4-col) --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-5 text-[10px]">
                <div class="bg-slate-50 dark:bg-slate-950/40 p-2.5 sm:p-0 rounded-xl sm:bg-transparent sm:dark:bg-transparent">
                    <span class="text-slate-400 font-bold uppercase tracking-wider block text-[9px]">Total Peserta</span>
                    <span class="font-black text-slate-800 dark:text-slate-100 text-sm sm:text-base">{{ $this->overallStats['total'] }}</span>
                </div>
                <div class="bg-slate-50 dark:bg-slate-950/40 p-2.5 sm:p-0 rounded-xl sm:bg-transparent sm:dark:bg-transparent">
                    <span class="text-slate-400 font-bold uppercase tracking-wider block text-[9px]">Lunas</span>
                    <span class="font-black text-emerald-600 dark:text-emerald-400 text-sm sm:text-base">{{ $this->overallStats['paid'] }}</span>
                </div>
                <div class="bg-slate-50 dark:bg-slate-950/40 p-2.5 sm:p-0 rounded-xl sm:bg-transparent sm:dark:bg-transparent">
                    <span class="text-slate-400 font-bold uppercase tracking-wider block text-[9px]">Sebagian (Cicilan)</span>
                    <span class="font-black text-amber-600 dark:text-amber-400 text-sm sm:text-base">{{ $this->overallStats['partial'] }}</span>
                </div>
                <div class="bg-slate-50 dark:bg-slate-950/40 p-2.5 sm:p-0 rounded-xl sm:bg-transparent sm:dark:bg-transparent">
                    <span class="text-slate-400 font-bold uppercase tracking-wider block text-[9px]">Belum Bayar</span>
                    <span class="font-black text-rose-500 dark:text-rose-400 text-sm sm:text-base">{{ $this->overallStats['unpaid'] }}</span>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-2 shrink-0 flex-wrap sm:flex-nowrap">
                <button type="button" wire:click="exportExcel"
                    class="flex-1 sm:flex-initial px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-1.5"
                    title="Export Laporan Excel Majek Bulan Ini">
                    📊 Export Excel
                </button>
                <button type="button" wire:click="openCopyPeriodModal"
                    class="flex-1 sm:flex-initial px-3.5 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-1.5">
                    📋 Salin Peserta
                </button>
                <button type="button" wire:click="openAddModal"
                    class="flex-1 sm:flex-initial px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-2">
                    + Tambah Peserta
                </button>
            </div>
        </div>

        {{-- Search and Filter Bar --}}
        <div class="p-4 sm:px-6 sm:py-3.5 bg-slate-50/50 dark:bg-slate-950/10 border-b border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row gap-3 items-center">
            {{-- Search input --}}
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none text-xs">🔍</span>
                <input type="text" wire:model.live.debounce.300ms="searchParticipant" placeholder="Cari nama, NIK, atau NIS..."
                    class="w-full pl-9 pr-4 py-2.5 text-xs border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-xl text-slate-850 dark:text-slate-200 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 outline-none transition-all">
            </div>

            <div class="grid grid-cols-1 sm:flex items-center gap-2.5 w-full sm:w-auto">
                {{-- Gender Filter --}}
                @if(!$this->genderScope())
                    <div class="w-full sm:w-36 shrink-0">
                        <select wire:model.live="filterGender"
                            class="w-full px-3 py-2.5 text-xs font-bold border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-xl text-slate-700 dark:text-slate-300 outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all">
                            <option value="all">👥 Semua Gender</option>
                            <option value="L">👦 Santri Putra</option>
                            <option value="P">👧 Santri Putri</option>
                        </select>
                    </div>
                @else
                    <div class="hidden sm:flex items-center px-3 py-2.5 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 rounded-xl text-[11px] font-black text-amber-700 dark:text-amber-400 shrink-0">
                        @if($this->genderScope() === 'L')
                            👦 Scope: Putra
                        @else
                            👧 Scope: Putri
                        @endif
                    </div>
                @endif

                {{-- Status Filter --}}
                <div class="w-full sm:w-40 shrink-0">
                    <select wire:model.live="filterStatus"
                        class="w-full px-3 py-2.5 text-xs font-bold border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-xl text-slate-700 dark:text-slate-300 outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all">
                        <option value="all">💳 Semua Status</option>
                        <option value="paid">🟢 Lunas</option>
                        <option value="unpaid">🔴 Belum Bayar</option>
                        <option value="partial">🟡 Sebagian</option>
                    </select>
                </div>

                {{-- Dormitory Multiselect --}}
                <div x-data="{ open: false }" class="relative w-full sm:w-56 shrink-0" @click.away="open = false">
                    <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between px-3.5 py-2.5 text-xs font-bold border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-xl text-slate-700 dark:text-slate-300 outline-none focus:ring-2 focus:ring-amber-500/20 transition-all text-left">
                        <span class="truncate flex items-center gap-1.5">
                            <span>🏢</span>
                            <span>
                                @if(empty($filterDormitoryIds))
                                    Semua Komplek
                                @else
                                    {{ count($filterDormitoryIds) }} Komplek Terpilih
                                @endif
                            </span>
                        </span>
                        <span class="text-[9px] text-slate-400">▼</span>
                    </button>
                    
                    <div x-show="open" x-transition
                        class="absolute right-0 mt-1.5 w-full bg-white dark:bg-slate-900 border border-slate-250 dark:border-slate-800 rounded-2xl shadow-lg z-30 max-h-60 overflow-y-auto p-2.5 space-y-1">
                        @foreach($this->dormitories as $dorm)
                            <label class="flex items-center gap-2 px-2.5 py-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 rounded-xl cursor-pointer select-none">
                                <input type="checkbox" value="{{ $dorm->id }}" wire:model.live="filterDormitoryIds"
                                    class="rounded text-amber-500 focus:ring-amber-500 border-slate-300 dark:border-slate-700">
                                <span class="text-xs text-slate-700 dark:text-slate-300 font-semibold">{{ $dorm->name }} ({{ $dorm->gender === 'P' ? 'Putri' : 'Putra' }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Reset Filters Button --}}
                @if($searchParticipant !== '' || !empty($filterDormitoryIds) || $filterStatus !== 'all' || $filterGender !== 'all')
                    <button type="button" wire:click="resetFilters"
                        class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-750 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 shrink-0 border border-slate-200/40 dark:border-slate-700/60 shadow-xs">
                        🧹 Reset
                    </button>
                @endif
            </div>
        </div>

        {{-- Tabel / Kartu Peserta --}}
        @if($this->registrations->isEmpty())
            <div class="py-16 sm:py-20 text-center px-4">
                <div class="text-4xl mb-3">🍽️</div>
                @if($searchParticipant || !empty($filterDormitoryIds) || $filterStatus !== 'all')
                    <div class="text-slate-400 font-bold text-sm">Tidak ada peserta yang cocok dengan filter</div>
                    <p class="text-slate-400 text-xs mt-1">Coba sesuaikan kata kunci pencarian atau filter Anda.</p>
                @else
                    <div class="text-slate-400 font-bold text-sm">Belum ada peserta terdaftar</div>
                    <p class="text-slate-400 text-xs mt-1">
                        @if(!$this->activePeriod)
                            Buat konfigurasi periode terlebih dahulu, lalu tambahkan peserta.
                        @else
                            Klik tombol "+ Tambah Peserta" untuk mendaftarkan santri.
                        @endif
                    </p>
                @endif
            </div>
        @else
            {{-- DESKTOP TABLE VIEW (md:block) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800">
                            <th class="py-3 px-4 sticky top-0 left-0 bg-slate-50 dark:bg-slate-950 z-20 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">No</th>
                            <th class="py-3 px-4 sticky top-0 bg-slate-50 dark:bg-slate-950 z-10 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">Nama Santri</th>
                            <th class="py-3 px-4 sticky top-0 bg-slate-50 dark:bg-slate-950 z-10 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">Komplek</th>
                            <th class="py-3 px-4 sticky top-0 bg-slate-50 dark:bg-slate-950 z-10 text-center text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">Sesi</th>
                            <th class="py-3 px-4 sticky top-0 bg-slate-50 dark:bg-slate-950 z-10 text-right text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">Sisa Tagihan</th>
                            <th class="py-3 px-4 sticky top-0 bg-slate-50 dark:bg-slate-950 z-10 text-center text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">Status</th>
                            <th class="py-3 px-4 sticky top-0 bg-slate-50 dark:bg-slate-950 z-10 text-center text-emerald-600 font-extrabold uppercase tracking-wider text-[9px]">Bayar?</th>
                            <th class="py-3 px-4 sticky top-0 bg-slate-50 dark:bg-slate-950 z-10 text-center text-slate-400 font-extrabold uppercase tracking-wider text-[9px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        @foreach($this->registrations as $i => $reg)
                            @php
                                $detail   = $this->paidDetails[$reg->id] ?? ['status' => 'unpaid', 'paid' => 0, 'remaining' => (float)$reg->amount_pagi + (float)$reg->amount_sore];
                                $status   = $detail['status'];
                                $paidAmt  = $detail['paid'];
                                $remAmt   = $detail['remaining'];
                                $total    = (float)$reg->amount_pagi + (float)$reg->amount_sore;
                                $dormName = $reg->person?->roomAssignments?->first()?->room?->dormitory?->name ?? '—';
                                $customDays = $reg->active_days ?? ($this->activePeriod ? $this->activePeriod->active_days : 30);
                            @endphp
                            <tr wire:key="main-table-reg-{{ $reg->id }}" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors {{ $status === 'paid' ? 'opacity-60' : '' }}">
                                <td class="py-3 px-4 text-slate-400 text-[10px] sticky left-0 bg-white dark:bg-slate-900 shadow-xs">{{ ($this->registrations->firstItem() ?? 1) + $i }}</td>
                                <td class="py-3 px-4 font-bold text-slate-800 dark:text-slate-200 whitespace-nowrap">
                                    <div>{{ $reg->person->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-semibold flex items-center gap-1.5 mt-0.5">
                                        <span>📅 {{ $customDays }} hari aktif</span>
                                        @if($reg->notes)
                                            <span class="text-slate-300 dark:text-slate-700">•</span>
                                            <span class="text-slate-500 truncate max-w-44" title="{{ $reg->notes }}">📝 {{ $reg->notes }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-slate-500 whitespace-nowrap">{{ $dormName }}</td>
                                <td class="py-3 px-4 text-center">
                                    @if($reg->session_pagi && $reg->session_sore)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[9px] font-extrabold uppercase">🍽️🍽️ 2x</span>
                                    @elseif($reg->session_pagi)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400 text-[9px] font-extrabold uppercase">🌅 Pagi</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-[9px] font-extrabold uppercase">🌆 Sore</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap font-mono">
                                    @if($status === 'partial')
                                        <div class="font-extrabold text-rose-600 dark:text-rose-400 text-xs">
                                            Rp {{ number_format($remAmt, 0, ',', '.') }} <span class="text-[9px] font-sans font-semibold text-rose-500">(sisa)</span>
                                        </div>
                                        <div class="text-[9px] text-slate-400 font-sans">
                                            Total {{ number_format($total/1000, 0) }}k · Masuk {{ number_format($paidAmt/1000, 0) }}k
                                        </div>
                                    @elseif($status === 'paid')
                                        <div class="font-bold text-emerald-600 dark:text-emerald-400 text-xs">
                                            Rp 0 <span class="text-[9px] font-sans font-semibold text-emerald-500">(Lunas)</span>
                                        </div>
                                        <div class="text-[9px] text-slate-400 font-sans">
                                            Total Rp {{ number_format($total, 0, ',', '.') }}
                                        </div>
                                    @else
                                        <div class="font-bold text-slate-800 dark:text-slate-200 text-xs">
                                            Rp {{ number_format($total, 0, ',', '.') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($status === 'paid')
                                        <span class="px-2.5 py-1 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[9px] font-extrabold uppercase tracking-wide border border-emerald-500/20">✓ Lunas</span>
                                    @elseif($status === 'partial')
                                        <span class="px-2.5 py-1 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[9px] font-extrabold uppercase tracking-wide border border-amber-500/20">◑ Sebagian</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 text-[9px] font-extrabold uppercase tracking-wide">Belum Bayar</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($status !== 'paid')
                                        <input type="checkbox"
                                            wire:key="check-desktop-{{ $reg->id }}"
                                            wire:model.live="paymentChecks.{{ $reg->id }}"
                                            class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 cursor-pointer">
                                    @else
                                        <span class="text-emerald-500 font-bold text-sm">✓</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if($status !== 'paid')
                                            <button type="button" wire:click="openEditModal('{{ $reg->id }}')"
                                                class="px-2.5 py-1 bg-sky-50 dark:bg-sky-950/40 hover:bg-sky-500 hover:text-white text-sky-600 dark:text-sky-400 border border-sky-200/80 dark:border-sky-800/60 rounded-xl text-[10px] font-extrabold transition-all flex items-center gap-1 shadow-2xs" title="Edit Detail Peserta">
                                                ✏️ Edit
                                            </button>
                                            <button type="button" wire:click="confirmRemovePeserta('{{ $reg->id }}', '{{ addslashes($reg->person->name) }}')"
                                                class="px-2.5 py-1 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-600 hover:text-white text-rose-600 dark:text-rose-400 border border-rose-200/80 dark:border-rose-800/60 rounded-xl text-[10px] font-extrabold transition-all flex items-center gap-1 shadow-2xs" title="Hapus Peserta">
                                                🗑️ Hapus
                                            </button>
                                        @else
                                            <span class="text-slate-300 dark:text-slate-700 text-[10px] font-bold">—</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- MOBILE CARDS VIEW (md:hidden) --}}
            <div class="block md:hidden p-3 space-y-3">
                @foreach($this->registrations as $i => $reg)
                    @php
                        $detail   = $this->paidDetails[$reg->id] ?? ['status' => 'unpaid', 'paid' => 0, 'remaining' => (float)$reg->amount_pagi + (float)$reg->amount_sore];
                        $status   = $detail['status'];
                        $paidAmt  = $detail['paid'];
                        $remAmt   = $detail['remaining'];
                        $total    = (float)$reg->amount_pagi + (float)$reg->amount_sore;
                        $dormName = $reg->person?->roomAssignments?->first()?->room?->dormitory?->name ?? '—';
                        $customDays = $reg->active_days ?? ($this->activePeriod ? $this->activePeriod->active_days : 30);
                        $isChecked = $paymentChecks[$reg->id] ?? false;
                    @endphp
                    <div wire:key="main-mobile-card-{{ $reg->id }}" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-3.5 space-y-2.5 shadow-2xs transition-all {{ $isChecked ? 'ring-2 ring-emerald-500/40 bg-emerald-500/5 dark:bg-emerald-950/20' : '' }}">
                        
                        {{-- Top Header: Checkbox & Name --}}
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-2">
                            <div class="flex items-center gap-2.5">
                                @if($status !== 'paid')
                                    <input type="checkbox"
                                        wire:key="check-mobile-{{ $reg->id }}"
                                        wire:model.live="paymentChecks.{{ $reg->id }}"
                                        class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 cursor-pointer shrink-0">
                                @else
                                    <span class="w-6 h-6 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-xs shrink-0">✓</span>
                                @endif
                                <div>
                                    <div class="font-extrabold text-slate-900 dark:text-white text-xs leading-snug">
                                        {{ $reg->person->name }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-semibold flex items-center gap-1.5">
                                        <span class="bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded-md font-mono text-[9px] text-slate-600 dark:text-slate-300">{{ $dormName }}</span>
                                        <span>•</span>
                                        <span>📅 {{ $customDays }} hari</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            @if($status !== 'paid')
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <button type="button" wire:click="openEditModal('{{ $reg->id }}')"
                                        class="px-2 py-1 bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800/60 rounded-xl text-[10px] font-extrabold flex items-center gap-0.5 active:scale-95 transition-all">
                                        ✏️ Edit
                                    </button>
                                    <button type="button" wire:click="confirmRemovePeserta('{{ $reg->id }}', '{{ addslashes($reg->person->name) }}')"
                                        class="px-2 py-1 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60 rounded-xl text-[10px] font-extrabold flex items-center gap-0.5 active:scale-95 transition-all">
                                        🗑️ Hapus
                                    </button>
                                </div>
                            @endif
                        </div>

                        {{-- Session & Tagihan Summary --}}
                        <div class="flex items-center justify-between text-xs pt-0.5">
                            <div class="flex items-center gap-1.5">
                                @if($reg->session_pagi && $reg->session_sore)
                                    <span class="px-2 py-0.5 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[9px] font-extrabold uppercase">🍽️🍽️ 2x Makan</span>
                                @elseif($reg->session_pagi)
                                    <span class="px-2 py-0.5 rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400 text-[9px] font-extrabold uppercase">🌅 Pagi Saja</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-[9px] font-extrabold uppercase"><ctrl42> Sore Saja</span>
                                @endif

                                @if($status === 'paid')
                                    <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-600 text-[9px] font-black uppercase">✓ Lunas</span>
                                @elseif($status === 'partial')
                                    <span class="px-2 py-0.5 rounded-lg bg-amber-500/10 text-amber-600 text-[9px] font-black uppercase">◑ Sebagian</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400 text-[9px] font-black uppercase">Belum Bayar</span>
                                @endif
                            </div>

                            <div class="text-right font-mono font-black">
                                @if($status === 'partial')
                                    <span class="text-rose-600 dark:text-rose-400">Rp {{ number_format($remAmt, 0, ',', '.') }}</span>
                                @elseif($status === 'paid')
                                    <span class="text-emerald-600 dark:text-emerald-400">Rp 0</span>
                                @else
                                    <span class="text-slate-800 dark:text-slate-100">Rp {{ number_format($total, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>

                        @if($reg->notes)
                            <div class="text-[9px] text-slate-400 bg-slate-50 dark:bg-slate-950 p-1.5 rounded-lg italic">
                                📝 {{ $reg->notes }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Pagination Footer for Participants Table -->
            <div class="px-4 sm:px-6 py-4 border-t border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex flex-col sm:flex-row items-center gap-3 text-[11px] font-semibold text-slate-400 text-center sm:text-left">
                    <div>
                        Menampilkan <span class="font-bold text-slate-700 dark:text-slate-300">{{ $this->registrations->firstItem() ?? 0 }}</span> s.d. <span class="font-bold text-slate-700 dark:text-slate-300">{{ $this->registrations->lastItem() ?? 0 }}</span> dari <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $this->registrations->total() }}</span> peserta
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="text-[10px] text-slate-400 uppercase font-bold">Per Hal:</span>
                        <select wire:model.live="perPage"
                            class="px-2 py-1 text-xs font-bold border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-lg text-slate-700 dark:text-slate-300 outline-none focus:ring-2 focus:ring-amber-500/20">
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="250">250</option>
                            <option value="500">500</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-center">
                    {{ $this->registrations->links(data: ['scrollTo' => false]) }}
                </div>
            </div>
        @endif

        {{-- Footer: Total + Action (Sticky Floating on Mobile) --}}
        @if($this->registrations->isNotEmpty())
            <div class="border-t border-slate-100 dark:border-slate-800 p-4 sm:px-6 sm:py-4 bg-slate-900 dark:bg-slate-950 text-white sm:bg-slate-50/50 sm:dark:bg-slate-950/20 sm:text-slate-900
                @if($countChecked > 0) fixed bottom-4 left-4 right-4 z-40 rounded-2xl shadow-2xl border-slate-800 sm:relative sm:bottom-auto sm:left-auto sm:right-auto sm:z-auto sm:rounded-none sm:shadow-none @else relative @endif">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center justify-between sm:justify-start gap-6 text-xs">
                        <div>
                            <span class="text-slate-400 text-[9px] font-extrabold uppercase tracking-wider block">Dicentang</span>
                            <span class="font-bold sm:text-slate-800 sm:dark:text-slate-200 text-amber-400 sm:text-inherit">{{ $countChecked }} peserta</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[9px] font-extrabold uppercase tracking-wider block">Total Setoran</span>
                            <span class="font-bold text-emerald-400 sm:text-emerald-600 sm:dark:text-emerald-400 text-sm sm:text-base">Rp {{ number_format($totalChecked, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 sm:gap-3">
                        {{-- Metode Bayar --}}
                        <select wire:model="payMethod"
                            class="text-xs border border-slate-700 sm:border-slate-200 dark:border-slate-700 bg-slate-800 sm:bg-white dark:bg-slate-800 text-white sm:text-slate-700 dark:text-slate-200 rounded-xl px-3 py-2 font-semibold focus:ring-2 focus:ring-emerald-500/30 outline-none flex-1 sm:flex-initial">
                            <option value="cash">💵 Cash</option>
                            <option value="transfer">🏦 Transfer</option>
                        </select>
                        <button type="button" wire:click="confirmSetoran" @if($countChecked === 0) disabled @endif
                            class="flex-1 sm:flex-initial px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-md
                                @if($countChecked > 0) bg-emerald-600 hover:bg-emerald-700 text-white
                                @else bg-slate-800 sm:bg-slate-200 dark:bg-slate-800 text-slate-500 sm:text-slate-400 cursor-not-allowed @endif">
                            Proses & Simpan ({{ $countChecked }})
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Konfigurasi Periode                                              --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($showPeriodModal)
        <div wire:key="period-modal-container" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden animate-zoom-in">
                
                {{-- Header --}}
                <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-950/20 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg shrink-0">
                            ⚙️
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wide">Konfigurasi Periode Majek</h3>
                            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">{{ $this->monthLabel }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closePeriodModal" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 font-bold transition-all flex items-center justify-center">✕</button>
                </div>

                {{-- Body --}}
                <div class="p-4 sm:p-6 space-y-4 overflow-y-auto flex-1 text-xs">
                    
                    {{-- Section 1: Hari Aktif --}}
                    <div class="bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-800 p-3.5 sm:p-4 rounded-2xl space-y-2">
                        <label class="block text-[11px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            📅 Jumlah Hari Aktif Makan
                        </label>
                        <div class="flex items-center gap-3">
                            <div class="relative flex-1">
                                <input type="number" wire:model.live.debounce.500ms="periodActiveDays" min="1" max="31"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm font-black focus:ring-2 focus:ring-amber-500/30 focus:border-amber-400 outline-none transition-all">
                            </div>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-extrabold px-1">Hari / Bulan</span>
                        </div>
                        @error('periodActiveDays') <p class="text-rose-500 text-[10px] font-bold mt-1">⚠️ {{ $message }}</p> @enderror
                    </div>

                    {{-- Section 2: Tarif Harian per Gender --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        
                        {{-- Tarif Putra --}}
                        <div class="bg-amber-500/5 border border-amber-500/15 p-3.5 sm:p-4 rounded-2xl space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-extrabold text-amber-700 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <span>👦</span> Tarif Putra / Hari
                                </span>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300">1 Sesi</span>
                            </div>
                            <div class="relative flex items-center">
                                <span class="absolute left-3 text-xs font-bold text-amber-600 dark:text-amber-400">Rp</span>
                                <input type="number" step="0.01" wire:model.live.debounce.500ms="periodTarifPerHari" min="1"
                                    class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-amber-200 dark:border-amber-800/50 bg-white dark:bg-slate-900 text-amber-900 dark:text-amber-100 text-xs font-black font-mono focus:ring-2 focus:ring-amber-500/30 outline-none">
                            </div>
                            @error('periodTarifPerHari') <p class="text-rose-500 text-[10px] font-bold">⚠️ {{ $message }}</p> @enderror
                        </div>

                        {{-- Tarif Putri --}}
                        <div class="bg-purple-500/5 border border-purple-500/15 p-3.5 sm:p-4 rounded-2xl space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-extrabold text-purple-700 dark:text-purple-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <span>👧</span> Tarif Putri / Hari
                                </span>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-md bg-purple-100 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300">1 Sesi</span>
                            </div>
                            <div class="relative flex items-center">
                                <span class="absolute left-3 text-xs font-bold text-purple-600 dark:text-purple-400">Rp</span>
                                <input type="number" step="0.01" wire:model.live.debounce.500ms="periodTarifPerHariPutri" min="1"
                                    class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/50 bg-white dark:bg-slate-900 text-purple-900 dark:text-purple-100 text-xs font-black font-mono focus:ring-2 focus:ring-purple-500/30 outline-none">
                            </div>
                            @error('periodTarifPerHariPutri') <p class="text-rose-500 text-[10px] font-bold">⚠️ {{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Section 3: Ringkasan Kalkulasi Total --}}
                    @if($periodActiveDays > 0 && $periodTarifPerHari > 0 && $periodTarifPerHariPutri > 0)
                        <div class="bg-slate-900 text-white dark:bg-slate-950 border border-slate-800 rounded-2xl p-3.5 sm:p-4 space-y-2.5 shadow-sm">
                            <div class="text-[10px] font-black uppercase tracking-wider text-slate-400 border-b border-slate-800 pb-2">
                                📊 Estimasi Total 1 Bulan ({{ $periodActiveDays }} Hari)
                            </div>
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div class="space-y-1">
                                    <div class="text-[10px] font-extrabold text-amber-400 uppercase tracking-wide">👦 Putra</div>
                                    <div class="flex justify-between text-[10px] text-slate-300">
                                        <span>1x:</span>
                                        <strong class="font-mono text-white">Rp {{ number_format($periodTarifPerHari * $periodActiveDays, 0, ',', '.') }}</strong>
                                    </div>
                                    <div class="flex justify-between text-[10px] text-slate-300">
                                        <span>2x:</span>
                                        <strong class="font-mono font-bold text-amber-400">Rp {{ number_format($periodTarifPerHari * 2 * $periodActiveDays, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                                <div class="space-y-1 border-l border-slate-800 pl-3">
                                    <div class="text-[10px] font-extrabold text-purple-400 uppercase tracking-wide">👧 Putri</div>
                                    <div class="flex justify-between text-[10px] text-slate-300">
                                        <span>1x:</span>
                                        <strong class="font-mono text-white">Rp {{ number_format($periodTarifPerHariPutri * $periodActiveDays, 0, ',', '.') }}</strong>
                                    </div>
                                    <div class="flex justify-between text-[10px] text-slate-300">
                                        <span>2x:</span>
                                        <strong class="font-mono font-bold text-purple-400">Rp {{ number_format($periodTarifPerHariPutri * 2 * $periodActiveDays, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Section 4: Catatan --}}
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">📝 Catatan Periode (Opsional)</label>
                        <textarea wire:model="periodNotes" rows="2"
                            placeholder="Tulis keterangan atau alasan pengubahan tarif/hari aktif..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-2 focus:ring-amber-500/30 focus:border-amber-400 outline-none resize-none transition-all"></textarea>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3 bg-slate-50/50 dark:bg-slate-950/20 shrink-0">
                    <button type="button" wire:click="closePeriodModal"
                        class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition-all">
                        Batal
                    </button>
                    <button type="button" wire:click="savePeriod"
                        class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-2">
                        💾 Simpan Konfigurasi
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Pendaftaran Peserta Majek (Mode Massal & Mode Cepat 1 Santri)     --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($showAddModal)
        <div wire:key="add-modal-container" class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4 bg-slate-950/70 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl w-full max-w-6xl max-h-[92vh] flex flex-col overflow-hidden animate-zoom-in">
                
                {{-- Header Modal & Mode Switcher --}}
                <div class="p-3.5 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/60 dark:bg-slate-950/30 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl shrink-0 shadow-2xs">
                            🍱
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white uppercase tracking-wide">Pendaftaran Peserta Majek</h3>
                                <span class="px-2 py-0.5 rounded-lg bg-amber-500/15 text-amber-700 dark:text-amber-300 font-extrabold text-[9px] uppercase tracking-wider">
                                    {{ $this->monthLabel }}
                                </span>
                            </div>
                            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">
                                @if($addTab === 'bulk')
                                    Mode Massal: Pilih banyak santri per kamar atau komplek sekaligus
                                @else
                                    Mode Cepat: Pendaftaran instan 1 santri susulan
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Mode Switcher Tabs + Close Button --}}
                    <div class="flex items-center justify-between sm:justify-end gap-2">
                        <div class="flex items-center p-1 bg-slate-200/70 dark:bg-slate-800 rounded-2xl">
                            <button type="button" wire:click="switchTab('bulk')"
                                class="px-3 py-1.5 rounded-xl text-xs font-black transition-all flex items-center gap-1.5 {{ $addTab === 'bulk' ? 'bg-white dark:bg-slate-900 text-amber-600 dark:text-amber-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200' }}">
                                🏢 Massal (Kamar/Komplek)
                            </button>
                            <button type="button" wire:click="switchTab('single')"
                                class="px-3 py-1.5 rounded-xl text-xs font-black transition-all flex items-center gap-1.5 {{ $addTab === 'single' ? 'bg-white dark:bg-slate-900 text-amber-600 dark:text-amber-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200' }}">
                                ⚡ Cepat (1 Santri)
                            </button>
                        </div>
                        <button type="button" wire:click="closeAddModal"
                            class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 font-bold transition-all flex items-center justify-center shrink-0">
                            ✕
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="p-3 sm:p-5 overflow-y-auto flex-1 text-xs">

                    {{-- ══════════════════════════════════════════════════════════ --}}
                    {{-- TAB 1: PENDAFTARAN MASSAL (BULK MODE)                      --}}
                    {{-- ══════════════════════════════════════════════════════════ --}}
                    @if($addTab === 'bulk')
                        <div class="space-y-4">

                            {{-- Preset & Filter Top Bar --}}
                            <div class="bg-slate-50 dark:bg-slate-950/40 p-3 sm:p-4 rounded-2xl border border-slate-200/70 dark:border-slate-800 space-y-3">
                                
                                {{-- Baris 1: Preset Default Sebelum Memilih Santri --}}
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-3 border-b border-slate-200/50 dark:border-slate-800/80">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-600 flex items-center justify-center text-xs font-black">⚙️</span>
                                        <div>
                                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">Setelan Default Santri Baru:</span>
                                            <p class="text-[9px] text-slate-400">Santri yang baru dicentang otomatis menerapkan sesi & hari ini</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 flex-wrap">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[10px] font-bold text-slate-500">Sesi:</span>
                                            <select wire:model.live="presetSesi"
                                                class="px-2.5 py-1 text-xs font-extrabold border border-amber-300 dark:border-amber-700/60 bg-white dark:bg-slate-900 text-amber-950 dark:text-amber-200 rounded-xl outline-none focus:ring-2 focus:ring-amber-500/30">
                                                <option value="2x">🍽️ 2x Makan (Pagi & Sore)</option>
                                                <option value="pagi">🌅 1x Pagi Saja</option>
                                                <option value="sore">🌆 1x Sore Saja</option>
                                            </select>
                                        </div>

                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[10px] font-bold text-slate-500">Hari:</span>
                                            <input type="number" min="1" max="31" wire:model.live="presetDays"
                                                class="w-16 px-2 py-1 text-xs font-mono font-black text-center border border-amber-300 dark:border-amber-700/60 bg-white dark:bg-slate-900 text-slate-900 dark:text-white rounded-xl outline-none focus:ring-2 focus:ring-amber-500/30">
                                        </div>
                                    </div>
                                </div>

                                {{-- Baris 2: Filter Pencarian, Gender, Komplek, Kamar, & Status --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 {{ !$this->genderScope() ? 'lg:grid-cols-5' : 'lg:grid-cols-4' }} gap-2.5 text-xs">
                                    {{-- Cari Nama / NIS --}}
                                    <div>
                                        <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Cari Nama / NIS / NIK</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-slate-400 pointer-events-none text-xs">🔍</span>
                                            <input type="text" wire:model.live.debounce.250ms="searchBulkQuery"
                                                placeholder="Ketik nama atau NIS..."
                                                class="w-full pl-7 pr-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-amber-500/30 outline-none">
                                        </div>
                                    </div>

                                    {{-- Filter Gender Santri (Hanya tampil jika user punya akses kedua gender) --}}
                                    @if(!$this->genderScope())
                                    <div>
                                        <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Gender Santri</label>
                                        <select wire:model.live="filterBulkGender"
                                            class="w-full px-2.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-amber-500/30 outline-none">
                                            <option value="all">👥 Semua Gender</option>
                                            <option value="L">👦 Santri Putra</option>
                                            <option value="P">👧 Santri Putri</option>
                                        </select>
                                    </div>
                                    @endif

                                    {{-- Filter Komplek --}}
                                    <div>
                                        <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Komplek / Asrama</label>
                                        <select wire:model.live="filterBulkDormitoryId"
                                            class="w-full px-2.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-amber-500/30 outline-none">
                                            <option value="">🏢 Semua Komplek</option>
                                            @foreach($this->modalDormitories as $dorm)
                                                <option value="{{ $dorm->id }}">{{ $dorm->name }} ({{ $dorm->gender === 'P' ? 'Putri' : 'Putra' }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Filter Kamar (Room) --}}
                                    <div>
                                        <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Kamar Asrama</label>
                                        <select wire:model.live="filterBulkRoomId" @if(empty($filterBulkDormitoryId)) disabled @endif
                                            class="w-full px-2.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-amber-500/30 outline-none @if(empty($filterBulkDormitoryId)) opacity-60 cursor-not-allowed @endif">
                                            <option value="">🚪 Semua Kamar</option>
                                            @foreach($this->availableRooms as $room)
                                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Filter Status Majek --}}
                                    <div>
                                        <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Status di Bulan Ini</label>
                                        <select wire:model.live="filterBulkStatus"
                                            class="w-full px-2.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-amber-500/30 outline-none">
                                            <option value="unregistered">⏳ Belum Terdaftar Majek</option>
                                            <option value="all">👥 Semua Santri</option>
                                            <option value="registered">✓ Sudah Terdaftar</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- SPLIT VIEW (2 KOLOM DI DESKTOP): KIRI = PILIH SANTRI, KANAN = KERANJANG TERPILIH --}}
                            @php
                                $bulkList      = $this->bulkStudentsList;
                                $selectedList  = $this->selectedStudentsList;
                                $selectedCount = count($selectedList);
                                $totalEstimasi = array_sum(array_column($selectedList, 'estimated_total'));
                            @endphp

                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
                                
                                {{-- ─── KOLOM KIRI (8/12 atau 12/12): DAFTAR SANTRI UNTUK DICENTANG ─── --}}
                                <div class="{{ $showSelectedPanel ? 'lg:col-span-8' : 'lg:col-span-12' }} space-y-2.5 flex flex-col transition-all duration-200">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 px-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-black text-slate-800 dark:text-slate-200 uppercase tracking-wide">
                                                Daftar Santri
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md bg-slate-200 dark:bg-slate-800 text-[10px] font-bold text-slate-600 dark:text-slate-300">
                                                {{ count($bulkList) }} santri
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <button type="button" wire:click="selectAllFilteredStudents"
                                                class="px-2.5 py-1 bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-300/40 rounded-lg text-[10px] font-extrabold transition-all flex items-center gap-1">
                                                ✓ Centang Semua
                                            </button>
                                            <button type="button" wire:click="clearAllBulkSelections"
                                                class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-rose-500 rounded-lg text-[10px] font-extrabold transition-all">
                                                ✕ Bersihkan
                                            </button>
                                            {{-- Toggle Panel Terpilih di Desktop --}}
                                            <button type="button" wire:click="toggleSelectedPanel"
                                                class="hidden lg:flex px-2.5 py-1 rounded-lg text-[10px] font-extrabold transition-all items-center gap-1 border border-slate-200/80 dark:border-slate-700 {{ $showSelectedPanel ? 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300' : 'bg-amber-500 hover:bg-amber-600 text-white shadow-2xs' }}"
                                                title="{{ $showSelectedPanel ? 'Ciutkan panel kanan agar tabel santri tampil full width' : 'Buka panel rincian santri terpilih' }}">
                                                @if($showSelectedPanel)
                                                    <span>◀ Ciutkan Panel</span>
                                                @else
                                                    <span>🧺 Buka Panel Terpilih (<strong>{{ $selectedCount }}</strong>)</span>
                                                @endif
                                            </button>
                                        </div>
                                    </div>

                                    @if(empty($bulkList))
                                        <div class="py-12 text-center text-slate-400 text-xs italic bg-slate-50/50 dark:bg-slate-950/20 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                                            Tidak ada santri ditemukan sesuai filter atau seluruh santri di filter ini sudah terdaftar Majek.
                                        </div>
                                    @else
                                        {{-- Desktop Table View --}}
                                        <div class="hidden sm:block border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs max-h-[50vh] overflow-y-auto">
                                            <table class="w-full text-left text-xs border-collapse">
                                                <thead>
                                                    <tr class="bg-slate-100 dark:bg-slate-950 text-[10px] text-slate-500 font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800 sticky top-0 z-10">
                                                        <th class="py-2.5 px-3 text-center w-12">Pilih</th>
                                                        <th class="py-2.5 px-3">Nama Santri</th>
                                                        <th class="py-2.5 px-3 w-44">Komplek & Kamar</th>
                                                        <th class="py-2.5 px-3 text-center w-28">Status Majek</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                                    @foreach($bulkList as $std)
                                                        @php
                                                            $isSelected = $bulkSelections[$std['id']] ?? false;
                                                            $isRegistered = $std['is_registered'] ?? false;
                                                        @endphp
                                                        <tr wire:key="bulk-student-table-{{ $std['id'] }}"
                                                            class="transition-colors border-b border-slate-100 dark:border-slate-800
                                                            {{ $isRegistered ? 'bg-slate-50/50 dark:bg-slate-900/40 opacity-60' : ($isSelected ? 'bg-amber-500/10 dark:bg-amber-950/30' : 'bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/60') }}">
                                                            <td class="py-2 px-3 text-center">
                                                                @if($isRegistered)
                                                                    <span class="text-emerald-500 font-bold" title="Sudah terdaftar majek">✓</span>
                                                                @else
                                                                    <input type="checkbox" wire:click="toggleStudentSelection('{{ $std['id'] }}')" @checked($isSelected)
                                                                        class="w-4 h-4 rounded text-amber-500 focus:ring-amber-500 border-slate-300 dark:border-slate-650 cursor-pointer">
                                                                @endif
                                                            </td>
                                                            <td class="py-2 px-3 font-bold text-xs">
                                                                <div class="flex items-center gap-1.5">
                                                                    <span class="text-slate-850 dark:text-slate-100">{{ $std['name'] }}</span>
                                                                    @if($std['gender'] === 'P')
                                                                        <span class="px-1.5 py-0.2 rounded bg-purple-500/10 text-purple-600 dark:text-purple-400 text-[8px] font-black">Putri</span>
                                                                    @else
                                                                        <span class="px-1.5 py-0.2 rounded bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[8px] font-black">Putra</span>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td class="py-2 px-3 text-[10px] text-slate-500 font-mono truncate" title="{{ $std['location'] }}">
                                                                {{ $std['location'] }}
                                                            </td>
                                                            <td class="py-2 px-3 text-center">
                                                                @if($isRegistered)
                                                                    <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[8px] font-black uppercase">Sudah Ikut</span>
                                                                @elseif($isSelected)
                                                                    <span class="px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-700 dark:text-amber-300 text-[8px] font-black uppercase">✓ Dipilih</span>
                                                                @else
                                                                    <span class="text-slate-400 text-[9px]">—</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        {{-- Mobile Card List View --}}
                                        <div class="block sm:hidden space-y-2 max-h-[45vh] overflow-y-auto pr-0.5">
                                            @foreach($bulkList as $std)
                                                @php
                                                    $isSelected = $bulkSelections[$std['id']] ?? false;
                                                    $isRegistered = $std['is_registered'] ?? false;
                                                @endphp
                                                <div wire:key="bulk-student-mobile-{{ $std['id'] }}"
                                                    class="p-2.5 rounded-xl border flex items-center justify-between text-xs transition-colors
                                                    {{ $isRegistered ? 'bg-slate-50/60 dark:bg-slate-950/40 border-slate-200 dark:border-slate-800 opacity-60' : ($isSelected ? 'bg-amber-500/10 border-amber-300 dark:border-amber-800' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800') }}">
                                                    <div class="flex items-center gap-2.5">
                                                        @if($isRegistered)
                                                            <span class="text-emerald-500 font-bold text-sm">✓</span>
                                                        @else
                                                            <input type="checkbox" wire:click="toggleStudentSelection('{{ $std['id'] }}')" @checked($isSelected)
                                                                class="w-4 h-4 rounded text-amber-500 focus:ring-amber-500 border-slate-300 dark:border-slate-650 cursor-pointer">
                                                        @endif
                                                        <div>
                                                            <div class="font-extrabold text-slate-850 dark:text-slate-100 text-xs flex items-center gap-1.5">
                                                                <span>{{ $std['name'] }}</span>
                                                                <span class="text-[8px] px-1 rounded font-bold {{ $std['gender'] === 'P' ? 'bg-purple-500/10 text-purple-600' : 'bg-amber-500/10 text-amber-600' }}">
                                                                    {{ $std['gender'] === 'P' ? 'Putri' : 'Putra' }}
                                                                </span>
                                                            </div>
                                                            <div class="text-[9px] text-slate-400 font-mono">{{ $std['location'] }}</div>
                                                        </div>
                                                    </div>
                                                    @if($isRegistered)
                                                        <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 text-[8px] font-black rounded uppercase">Terdaftar</span>
                                                    @elseif($isSelected)
                                                        <span class="px-2 py-0.5 bg-amber-500/20 text-amber-700 dark:text-amber-300 text-[8px] font-black rounded uppercase">✓ Dipilih</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                {{-- ─── KOLOM KANAN (4/12): KERANJANG SANTRI TERPILIH (COMPACT) ─── --}}
                                <div class="{{ $showSelectedPanel ? 'lg:col-span-4' : 'hidden' }} bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-3 space-y-2.5 flex flex-col transition-all duration-200 shadow-xs">
                                    {{-- Header Keranjang --}}
                                    <div class="flex items-center justify-between border-b border-slate-200/60 dark:border-slate-800 pb-2">
                                        <div>
                                            <h4 class="text-xs font-black text-slate-850 dark:text-slate-100 uppercase tracking-wide flex items-center gap-1.5">
                                                <span>🧺 Santri Terpilih</span>
                                                <span class="px-1.5 py-0.2 rounded-md bg-amber-500/15 text-amber-600 dark:text-amber-400 font-black text-[10px]">
                                                    {{ $selectedCount }}
                                                </span>
                                            </h4>
                                            <div class="text-[10px] font-mono font-bold text-slate-500 dark:text-slate-400 mt-0.5">
                                                Total: <span class="text-amber-600 dark:text-amber-400 font-black">Rp {{ number_format($totalEstimasi, 0, ',', '.') }}</span>
                                            </div>
                                        </div>

                                        @if($selectedCount > 0)
                                            <div class="flex items-center gap-1">
                                                <span class="text-[9px] text-slate-400 font-bold hidden xl:inline">Semua:</span>
                                                <button type="button" wire:click="setAllSelectedSessions('2x')" title="Samakan Semua Sesi ke 2x"
                                                    class="px-1.5 py-0.5 bg-amber-500 hover:bg-amber-600 text-white rounded text-[9px] font-black shadow-2xs transition-all">
                                                    2x
                                                </button>
                                                <button type="button" wire:click="setAllSelectedSessions('pagi')" title="Samakan Semua Sesi ke Pagi Saja"
                                                    class="px-1.5 py-0.5 bg-sky-500 hover:bg-sky-600 text-white rounded text-[9px] font-black shadow-2xs transition-all">
                                                    Pagi
                                                </button>
                                                <button type="button" wire:click="setAllSelectedSessions('sore')" title="Samakan Semua Sesi ke Sore Saja"
                                                    class="px-1.5 py-0.5 bg-indigo-500 hover:bg-indigo-600 text-white rounded text-[9px] font-black shadow-2xs transition-all">
                                                    Sore
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    @if($selectedCount === 0)
                                        <div class="py-10 text-center text-slate-400 text-xs italic bg-white/60 dark:bg-slate-950/40 rounded-xl border border-dashed border-slate-200 dark:border-slate-800 p-4">
                                            👈 Centang santri di kolom sebelah kiri untuk menambahkannya ke pendaftaran Majek.
                                        </div>
                                    @else
                                        {{-- Scrollable List of Selected Students (Ultra-Compact Cards) --}}
                                        <div class="space-y-1.5 max-h-[50vh] overflow-y-auto pr-0.5">
                                            @foreach($selectedList as $selStd)
                                                @php
                                                    $currentSesi = $bulkSessions[$selStd['id']] ?? $selStd['session'] ?? '2x';
                                                @endphp
                                                <div wire:key="selected-card-{{ $selStd['id'] }}"
                                                    x-data="{ showNote: {{ !empty($bulkNotes[$selStd['id']] ?? '') ? 'true' : 'false' }} }"
                                                    class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 hover:border-amber-300 dark:hover:border-amber-700/60 rounded-xl p-2 space-y-1.5 shadow-2xs transition-all">
                                                    
                                                    {{-- Baris 1: Nama, Gender, Estimasi, & Tombol Hapus --}}
                                                    <div class="flex items-center justify-between gap-1.5">
                                                        <div class="flex items-center gap-1.5 min-w-0">
                                                            <span class="text-xs shrink-0">{{ $selStd['gender'] === 'P' ? '👧' : '👦' }}</span>
                                                            <span class="font-extrabold text-slate-850 dark:text-slate-100 text-xs truncate" title="{{ $selStd['name'] }}">
                                                                {{ $selStd['name'] }}
                                                            </span>
                                                        </div>
                                                        <div class="flex items-center gap-1.5 shrink-0">
                                                            <span class="font-mono font-black text-amber-600 dark:text-amber-400 text-xs">
                                                                Rp {{ number_format($selStd['estimated_total'], 0, ',', '.') }}
                                                            </span>
                                                            <button type="button" wire:click="uncheckStudent('{{ $selStd['id'] }}')"
                                                                class="w-5 h-5 rounded-md text-slate-400 hover:text-white hover:bg-rose-500 font-bold text-xs flex items-center justify-center transition-colors"
                                                                title="Batalkan santri ini">
                                                                ✕
                                                            </button>
                                                        </div>
                                                    </div>

                                                    {{-- Baris 2: Lokasi & Inline Controls (Sesi 1-klik, Hari, Catatan) --}}
                                                    <div class="flex items-center justify-between gap-1.5 text-[10px]">
                                                        <div class="text-slate-400 font-mono truncate max-w-[85px] sm:max-w-[110px]" title="{{ $selStd['location'] }}">
                                                            {{ $selStd['location'] }}
                                                        </div>

                                                        <div class="flex items-center gap-1 shrink-0">
                                                            {{-- Sesi Segmented Pill Buttons (1-Klik Ganti) --}}
                                                            <div class="inline-flex rounded-lg p-0.5 bg-slate-100 dark:bg-slate-800 border border-slate-200/70 dark:border-slate-700/80">
                                                                <button type="button" wire:click="setStudentSession('{{ $selStd['id'] }}', '2x')"
                                                                    class="px-1.5 py-0.5 rounded-md font-black text-[9px] transition-all {{ $currentSesi === '2x' ? 'bg-amber-500 text-white shadow-2xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}"
                                                                    title="2x Makan (Pagi & Sore)">
                                                                    2x
                                                                </button>
                                                                <button type="button" wire:click="setStudentSession('{{ $selStd['id'] }}', 'pagi')"
                                                                    class="px-1.5 py-0.5 rounded-md font-black text-[9px] transition-all {{ $currentSesi === 'pagi' ? 'bg-sky-500 text-white shadow-2xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}"
                                                                    title="1x Saja (Pagi)">
                                                                    Pagi
                                                                </button>
                                                                <button type="button" wire:click="setStudentSession('{{ $selStd['id'] }}', 'sore')"
                                                                    class="px-1.5 py-0.5 rounded-md font-black text-[9px] transition-all {{ $currentSesi === 'sore' ? 'bg-indigo-500 text-white shadow-2xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}"
                                                                    title="1x Saja (Sore)">
                                                                    Sore
                                                                </button>
                                                            </div>

                                                            {{-- Hari Input --}}
                                                            <div class="inline-flex items-center gap-0.5">
                                                                <input type="number" min="1" max="31" wire:model.live.debounce.300ms="bulkDays.{{ $selStd['id'] }}"
                                                                    class="w-10 px-1 py-0.5 text-[10px] font-mono font-black text-center border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-md outline-none focus:ring-1 focus:ring-amber-500">
                                                                <span class="text-[9px] text-slate-400">hr</span>
                                                            </div>

                                                            {{-- Tombol Toggle Note --}}
                                                            <button type="button" @click="showNote = !showNote"
                                                                :class="showNote ? 'bg-amber-500/20 text-amber-600 dark:text-amber-400 font-black' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200'"
                                                                class="p-1 rounded-md text-[10px] transition-colors"
                                                                title="Tambah / Ubah Catatan Santri Ini">
                                                                📝
                                                            </button>
                                                        </div>
                                                    </div>

                                                    {{-- Baris 3 (Optional - Hanya tampil saat tombol 📝 diklik) --}}
                                                    <div x-show="showNote" x-transition class="pt-0.5">
                                                        <input type="text" placeholder="Catatan santri ini..." wire:model.live.debounce.400ms="bulkNotes.{{ $selStd['id'] }}"
                                                            class="w-full px-2 py-0.5 text-[10px] border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 rounded-md outline-none focus:ring-1 focus:ring-amber-500">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    {{-- ══════════════════════════════════════════════════════════ --}}
                    {{-- TAB 2: INPUT CEPAT 1 SANTRI (SINGLE MODE)                  --}}
                    {{-- ══════════════════════════════════════════════════════════ --}}
                    @elseif($addTab === 'single')
                        <div class="max-w-xl mx-auto py-2 sm:py-4 space-y-4">
                            
                            {{-- Step 1: Input Cari Santri --}}
                            <div class="space-y-1.5">
                                <label class="block text-xs font-black text-slate-800 dark:text-slate-200 uppercase tracking-wide">
                                    🔍 Cari Santri
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none text-sm">🔍</span>
                                    <input type="text" wire:model.live.debounce.250ms="singleSearchQuery"
                                        placeholder="Ketik nama lengkap, NIS, atau NIK santri..."
                                        class="w-full pl-10 pr-10 py-2.5 text-xs font-bold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 rounded-2xl text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/30 outline-none transition-all">
                                    @if($singleSelectedPersonId || $singleSearchQuery)
                                        <button type="button" wire:click="clearSingleSelectedStudent"
                                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 font-bold text-sm">
                                            ✕
                                        </button>
                                    @endif
                                </div>

                                {{-- Dropdown Hasil Pencarian Single --}}
                                @if(!empty($singleSearchResults))
                                    <div class="border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900 shadow-xl overflow-hidden divide-y divide-slate-100 dark:divide-slate-800/80 max-h-56 overflow-y-auto">
                                        @foreach($singleSearchResults as $sr)
                                            <div wire:key="single-search-result-{{ $sr['id'] }}"
                                                @if(!$sr['is_registered']) wire:click="selectSingleStudent('{{ $sr['id'] }}')" @endif
                                                class="p-3 flex items-center justify-between transition-colors
                                                {{ $sr['is_registered'] ? 'bg-slate-50/70 dark:bg-slate-950/40 opacity-60 cursor-not-allowed' : 'hover:bg-amber-50 dark:hover:bg-amber-950/20 cursor-pointer' }}">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="w-8 h-8 rounded-xl {{ $sr['gender'] === 'P' ? 'bg-purple-500/10 text-purple-600' : 'bg-amber-500/10 text-amber-600' }} flex items-center justify-center font-bold text-xs">
                                                        {{ $sr['gender'] === 'P' ? '👧' : '👦' }}
                                                    </span>
                                                    <div>
                                                        <div class="font-black text-slate-900 dark:text-slate-100 text-xs">
                                                            {{ $sr['name'] }}
                                                        </div>
                                                        <div class="text-[10px] text-slate-400 font-mono">
                                                            {{ $sr['location'] }}
                                                        </div>
                                                    </div>
                                                </div>
                                                @if($sr['is_registered'])
                                                    <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 text-[9px] font-black uppercase">
                                                        Sudah Terdaftar
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-1 rounded-xl bg-amber-500 text-white text-[10px] font-extrabold shadow-xs">
                                                        Pilih Santri 👉
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Step 2: Form Konfigurasi Santri Terpilih --}}
                            @if($singleSelectedPerson)
                                <div class="bg-amber-50/40 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-900/60 rounded-3xl p-4 sm:p-5 space-y-4 shadow-sm animate-fade-in">
                                    
                                    {{-- Info Card Santri --}}
                                    <div class="flex items-center justify-between pb-3 border-b border-amber-200/60 dark:border-amber-900/40">
                                        <div class="flex items-center gap-3">
                                            <div class="w-11 h-11 rounded-2xl {{ $singleSelectedPerson['gender'] === 'P' ? 'bg-purple-500/15 text-purple-600' : 'bg-amber-500/15 text-amber-600' }} flex items-center justify-center text-2xl shrink-0 shadow-2xs">
                                                {{ $singleSelectedPerson['gender'] === 'P' ? '👧' : '👦' }}
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <h4 class="font-black text-slate-900 dark:text-slate-100 text-sm sm:text-base">
                                                        {{ $singleSelectedPerson['name'] }}
                                                    </h4>
                                                    <span class="px-1.5 py-0.5 rounded font-black text-[9px] {{ $singleSelectedPerson['gender'] === 'P' ? 'bg-purple-500/15 text-purple-600' : 'bg-amber-500/15 text-amber-600' }}">
                                                        {{ $singleSelectedPerson['gender'] === 'P' ? 'Santri Putri' : 'Santri Putra' }}
                                                    </span>
                                                </div>
                                                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                                                    🏢 {{ $singleSelectedPerson['location'] }}
                                                </div>
                                            </div>
                                        </div>

                                        <button type="button" wire:click="clearSingleSelectedStudent"
                                            class="text-[10px] font-bold text-slate-400 hover:text-rose-500 transition-colors">
                                            Ganti Santri
                                        </button>
                                    </div>

                                    @if($singleSelectedPerson['is_registered'])
                                        <div class="p-3 bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-300 rounded-xl text-xs font-bold flex items-center gap-2">
                                            <span>⚠️</span>
                                            <span>Santri ini sudah terdaftar pada periode {{ $this->monthLabel }}. Tidak dapat didaftarkan ganda.</span>
                                        </div>
                                    @else
                                        {{-- Setting Sesi & Hari --}}
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">
                                                    Pilihan Sesi Makan
                                                </label>
                                                <select wire:model.live="singleSesi"
                                                    class="w-full px-3 py-2 text-xs font-bold border border-amber-300 dark:border-amber-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white rounded-xl outline-none focus:ring-2 focus:ring-amber-500/30">
                                                    <option value="2x">🍽️ 2x Makan (Pagi & Sore)</option>
                                                    <option value="pagi">🌅 1x Pagi Saja</option>
                                                    <option value="sore">🌆 1x Sore Saja</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">
                                                    Jumlah Hari Aktif
                                                </label>
                                                <input type="number" min="1" max="31" wire:model.live="singleDays"
                                                    class="w-full px-3 py-2 text-xs font-mono font-black border border-amber-300 dark:border-amber-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white rounded-xl outline-none focus:ring-2 focus:ring-amber-500/30">
                                            </div>
                                        </div>

                                        {{-- Catatan --}}
                                        <div>
                                            <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">
                                                Catatan Tambahan (Opsional)
                                            </label>
                                            <input type="text" placeholder="Contoh: Mulai aktif katering tgl 5..." wire:model.live="singleNotes"
                                                class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-amber-500/30">
                                        </div>

                                        {{-- Estimasi Biaya Box --}}
                                        @php
                                            $rate = $this->activePeriod ? $this->activePeriod->getTarifPerHariForGender($singleSelectedPerson['gender']) : 0;
                                            $mult = match($singleSesi) { '2x' => 2, default => 1 };
                                            $singleTotal = $rate * (int)$singleDays * $mult;
                                        @endphp
                                        <div class="p-3 bg-white dark:bg-slate-900 rounded-2xl border border-amber-200/80 dark:border-amber-900/60 flex items-center justify-between">
                                            <div>
                                                <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider block">Total Tagihan Katering:</span>
                                                <span class="text-xs text-slate-500 font-semibold">
                                                    Rp {{ number_format($rate, 0, ',', '.') }}/hari × {{ $singleDays }} hari × {{ $mult }} sesi
                                                </span>
                                            </div>
                                            <span class="text-sm sm:text-base font-black text-amber-600 dark:text-amber-400">
                                                Rp {{ number_format($singleTotal, 0, ',', '.') }}
                                            </span>
                                        </div>

                                        {{-- Tombol Simpan Instan --}}
                                        <button type="button" wire:click="addSinglePeserta"
                                            class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl text-xs font-black uppercase tracking-wider transition-all shadow-md flex items-center justify-center gap-2">
                                            💾 Daftarkan {{ $singleSelectedPerson['name'] }}
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($flashError)
                        <div class="p-3 bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-300 rounded-xl text-xs font-bold flex items-center gap-2">
                            <span>⚠️</span>
                            <span>{{ $flashError }}</span>
                        </div>
                    @endif
                    @if($flashSuccess)
                        <div class="p-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-300 rounded-xl text-xs font-bold flex items-center gap-2">
                            <span>✓</span>
                            <span>{{ $flashSuccess }}</span>
                        </div>
                    @endif
                </div>

                {{-- Footer Modal (Khusus Tab Bulk) --}}
                @if($addTab === 'bulk')
                    <div class="px-4 sm:px-6 py-3.5 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/60 dark:bg-slate-950/30 shrink-0">
                        <div class="flex items-center gap-2 text-xs">
                            <span class="text-slate-500 font-semibold">Total Terpilih:</span>
                            <span class="px-2 py-0.5 rounded-lg bg-amber-500/20 text-amber-800 dark:text-amber-300 font-black">
                                {{ $selectedCount }} Santri
                            </span>
                            @if($selectedCount > 0)
                                <span class="text-slate-400">|</span>
                                <span class="font-extrabold text-slate-700 dark:text-slate-300">
                                    Total Rp {{ number_format($totalEstimasi, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center justify-end gap-2.5">
                            <button type="button" wire:click="closeAddModal"
                                class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition-all">
                                Batal
                            </button>
                            <button type="button" wire:click="addPesertaBulk" @if($selectedCount === 0) disabled @endif
                                class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-md flex items-center gap-2
                                    {{ $selectedCount > 0 ? 'bg-amber-500 hover:bg-amber-600 text-white cursor-pointer' : 'bg-slate-200 dark:bg-slate-800 text-slate-400 cursor-not-allowed' }}">
                                <span>💾</span>
                                <span>SIMPAN {{ $selectedCount }} PESERTA MAJEK</span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Edit Detail Peserta                                              --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($showEditModal)
        <div wire:key="edit-modal-container" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xl w-full max-w-md max-h-[90vh] flex flex-col overflow-hidden animate-zoom-in">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-950/20 shrink-0">
                    <div>
                        <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wide">Edit Peserta Majek</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">Ubah konfigurasi detail porsi santri</p>
                    </div>
                    <button type="button" wire:click="closeEditModal" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 font-bold flex items-center justify-center">✕</button>
                </div>
                <div class="p-5 space-y-4 overflow-y-auto flex-1 text-xs">
                    <div class="p-3 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-200 dark:border-slate-800">
                        <span class="text-slate-400 text-[10px] font-bold block uppercase">Nama Santri</span>
                        <strong class="text-slate-800 dark:text-white text-sm font-black">{{ $editPersonName }}</strong>
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Sesi Makan</label>
                        <select wire:model="editSesi" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold outline-none">
                            <option value="2x">🍽️🍽️ 2x Makan (Pagi & Sore)</option>
                            <option value="pagi">🌅 Pagi Saja</option>
                            <option value="sore">🌆 Sore Saja</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Hari Aktif Makan</label>
                        <input type="number" min="1" max="31" wire:model="editDays" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-mono font-bold outline-none">
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Catatan Khusus</label>
                        <input type="text" placeholder="Catatan opsional..." wire:model="editNotes" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs outline-none">
                    </div>
                </div>
                <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3 bg-slate-50/50 dark:bg-slate-950/20 shrink-0">
                    <button type="button" wire:click="closeEditModal" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold">Batal</button>
                    <button type="button" wire:click="saveEdit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold shadow-sm">💾 Simpan</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Preview & Konfirmasi Setoran (Kasir / Cicilan) RESPONSIF         --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($showConfirmModal)
        <div wire:key="confirm-modal-container" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden animate-zoom-in">
                
                {{-- Header --}}
                <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-950/20 shrink-0">
                    <div>
                        <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wide">Preview Setoran & Cicilan Majek</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $this->monthLabel }} · Sesuaikan nominal bayar jika cicilan.</p>
                    </div>
                    <span class="text-[10px] font-extrabold bg-amber-500/10 text-amber-700 dark:text-amber-400 px-2.5 py-1 rounded-xl border border-amber-500/20">{{ $countChecked }} Peserta</span>
                </div>

                {{-- Modal Body (Scrollable) --}}
                <div class="p-4 sm:p-6 space-y-4 overflow-y-auto flex-1 text-xs">
                    
                    {{-- Desktop Preview Table (sm:block) --}}
                    <div class="hidden sm:block border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden max-h-72 overflow-y-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-950 text-[9px] text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                                    <th class="py-2.5 px-3">No</th>
                                    <th class="py-2.5 px-3">Nama Santri</th>
                                    <th class="py-2.5 px-3 text-center">Sesi</th>
                                    <th class="py-2.5 px-3 text-right">Sisa Tagihan</th>
                                    <th class="py-2.5 px-3 text-right w-44">Nominal Setoran (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 text-[11px]">
                                @foreach($this->previewData as $i => $item)
                                    @php $isOver = $item['pay_amt'] > ($item['remaining'] + 0.01); @endphp
                                    <tr class="hover:bg-slate-100/50 dark:hover:bg-slate-800/30 transition-colors {{ $isOver ? 'bg-rose-500/10' : '' }}">
                                        <td class="py-2.5 px-3 text-slate-400 font-bold">{{ $i + 1 }}</td>
                                        <td class="py-2.5 px-3 font-bold text-slate-800 dark:text-slate-200">
                                            <div>{{ $item['name'] }}</div>
                                            <div class="text-[9px] text-slate-400 font-normal">Total: Rp {{ number_format($item['total'], 0, ',', '.') }}</div>
                                        </td>
                                        <td class="py-2.5 px-3 text-center text-slate-500 font-semibold">{{ $item['sesi'] }}</td>
                                        <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-600 dark:text-slate-400">
                                            Rp {{ number_format($item['remaining'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-2 px-3 text-right">
                                            <div class="relative flex items-center">
                                                <span class="absolute left-2.5 text-[10px] font-bold {{ $isOver ? 'text-rose-500' : 'text-slate-400' }}">Rp</span>
                                                <input type="number" step="5000" min="0" max="{{ $item['remaining'] }}"
                                                    wire:model.live.debounce.300ms="paymentAmounts.{{ $item['id'] }}"
                                                    class="w-full pl-7 pr-2 py-1.5 rounded-xl border {{ $isOver ? 'border-rose-500 bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-300 focus:ring-rose-500' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-emerald-500/30 focus:border-emerald-500' }} text-xs font-black font-mono text-right outline-none transition-all">
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Preview Card List (sm:hidden) --}}
                    <div class="block sm:hidden space-y-2.5 max-h-64 overflow-y-auto">
                        @foreach($this->previewData as $i => $item)
                            @php $isOver = $item['pay_amt'] > ($item['remaining'] + 0.01); @endphp
                            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-3 space-y-2 shadow-2xs">
                                <div class="flex items-center justify-between text-xs font-bold border-b border-slate-100 dark:border-slate-800 pb-1.5">
                                    <div class="text-slate-900 dark:text-white">
                                        <span class="text-slate-400 font-mono text-[10px] mr-1">#{{ $i+1 }}</span> {{ $item['name'] }}
                                    </div>
                                    <span class="text-[9px] px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500 font-extrabold">{{ $item['sesi'] }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-400 text-[10px]">Sisa Tagihan:</span>
                                    <strong class="font-mono text-rose-600 dark:text-rose-400">Rp {{ number_format($item['remaining'], 0, ',', '.') }}</strong>
                                </div>
                                <div>
                                    <label class="block text-[9px] font-extrabold uppercase text-slate-400 mb-1">Setoran Diinput (Rp):</label>
                                    <div class="relative flex items-center">
                                        <span class="absolute left-2.5 text-[10px] font-bold text-slate-400">Rp</span>
                                        <input type="number" step="5000" min="0" max="{{ $item['remaining'] }}"
                                            wire:model.live.debounce.300ms="paymentAmounts.{{ $item['id'] }}"
                                            class="w-full pl-7 pr-2 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-black font-mono text-right outline-none">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Total Setoran Kasir --}}
                    <div class="bg-emerald-500/10 border border-emerald-500/20 p-3.5 sm:p-4 rounded-2xl flex items-center justify-between shadow-xs">
                        <div>
                            <span class="text-[10px] text-emerald-700 dark:text-emerald-400 font-extrabold uppercase tracking-wider block">Total Setoran Kasir</span>
                            <span class="text-[9px] text-slate-400 hidden sm:block">Otomatis menghitung nominal setoran santri di atas.</span>
                        </div>
                        <strong class="text-emerald-600 dark:text-emerald-400 text-lg sm:text-xl font-black font-mono">Rp {{ number_format($totalChecked, 0, ',', '.') }}</strong>
                    </div>

                    {{-- Konfirmasi Checkbox --}}
                    <label class="flex items-start gap-2.5 p-3 bg-rose-500/5 border border-rose-500/10 rounded-2xl cursor-pointer">
                        <input type="checkbox" wire:model.live="confirmCheck"
                            class="mt-0.5 rounded text-rose-600 focus:ring-rose-500 border-slate-300 dark:border-slate-700">
                        <span class="text-[10px] leading-relaxed text-rose-700 dark:text-rose-300 font-bold select-none">
                            Saya menyatakan data setoran/cicilan di atas sudah sesuai dengan uang kasir yang diterima.
                        </span>
                    </label>
                </div>

                {{-- Footer --}}
                <div class="px-5 py-4 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3 shrink-0">
                    <button type="button" wire:click="cancelConfirm" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold">⬅️ Kembali</button>
                    <button type="button" wire:click="prosesSetoran" @if(!$confirmCheck) disabled @endif
                        class="px-5 py-2 text-white rounded-xl text-xs font-bold transition-all shadow-md {{ $confirmCheck ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-slate-300 dark:bg-slate-800 text-slate-400 cursor-not-allowed' }}">
                        💾 Simpan Setoran
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Konfirmasi Hapus Peserta                                         --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($showDeleteModal)
        <div wire:key="delete-modal-container" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xl w-full max-w-md overflow-hidden animate-zoom-in">
                <div class="p-6 text-center space-y-4">
                    <div class="w-16 h-16 bg-rose-500/10 dark:bg-rose-500/20 text-rose-550 dark:text-rose-400 rounded-full flex items-center justify-center mx-auto text-2xl">
                        ⚠️
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wide">Konfirmasi Hapus Peserta</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            Apakah Anda yakin ingin menghapus <strong class="text-slate-800 dark:text-white font-bold">{{ $deletePersonName }}</strong> dari daftar katering Majek bulan ini?
                        </p>
                    </div>
                </div>
                
                {{-- Footer --}}
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                    <button type="button" wire:click="closeDeleteModal" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold">Batal</button>
                    <button type="button" wire:click="removePeserta" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm">🗑️ Ya, Hapus</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: SALIN PESERTA DARI BULAN LAIN RESPONSIF                         --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($showCopyPeriodModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-4" wire:click.self="closeCopyPeriodModal">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-lg max-h-[90vh] flex flex-col border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden p-5 sm:p-6 space-y-4">
                <div class="flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-violet-100 dark:bg-violet-950/40 text-violet-600 dark:text-violet-400 flex items-center justify-center text-xl shrink-0">
                            📋
                        </div>
                        <div>
                            <h3 class="font-extrabold text-xs sm:text-base text-slate-800 dark:text-slate-100">Salin Peserta Majek</h3>
                            <p class="text-[10px] text-slate-400">Duplikasi pendaftar katering ke {{ $this->monthLabel }}.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 pt-1 overflow-y-auto flex-1 text-xs">
                    @if(!$this->activePeriod)
                        <div class="p-3.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 rounded-2xl text-xs space-y-2">
                            <div class="font-extrabold text-amber-700 dark:text-amber-300 flex items-center gap-1.5">
                                ⚠️ Konfigurasi Periode {{ $this->monthLabel }} Belum Dibuat!
                            </div>
                            <button type="button" wire:click="closeCopyPeriodModal(); openPeriodModal();" class="px-3.5 py-1.5 bg-amber-500 text-white font-bold rounded-xl text-[10px]">
                                ⚙️ Atur Konfigurasi Sekarang
                            </button>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[9px] font-extrabold text-slate-400 uppercase mb-1">Bulan Asal</label>
                            <select wire:model.live="copySourceMonth" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs font-bold">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">{{ \Carbon\Carbon::createFromDate(2026, $m, 1)->translatedFormat('F') }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-extrabold text-slate-400 uppercase mb-1">Tahun Asal</label>
                            <select wire:model.live="copySourceYear" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs font-bold">
                                @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    @php $preview = $this->copyPreviewData; @endphp
                    <div class="grid grid-cols-3 gap-2 text-center text-[9px]">
                        <div class="p-2 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 block font-bold uppercase">Total Asal</span>
                            <span class="text-xs font-black text-slate-800 dark:text-slate-100">{{ $preview['total_source'] }} Santri</span>
                        </div>
                        <div class="p-2 bg-emerald-50 dark:bg-emerald-950/30 rounded-xl border border-emerald-200/60 dark:border-emerald-800/40">
                            <span class="text-emerald-600 block font-bold uppercase">Akan Disalin</span>
                            <span class="text-xs font-black text-emerald-700 dark:text-emerald-300">+{{ $preview['will_copy_count'] }}</span>
                        </div>
                        <div class="p-2 bg-amber-50 dark:bg-amber-950/30 rounded-xl border border-amber-200/60 dark:border-amber-800/40">
                            <span class="text-amber-600 block font-bold uppercase">Dilewati</span>
                            <span class="text-xs font-black text-amber-700 dark:text-amber-300">{{ $preview['already_registered_count'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2 shrink-0">
                    <button type="button" wire:click="closeCopyPeriodModal" class="flex-1 py-2 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-xs">Batal</button>
                    <button type="button" wire:click="copyRegistrationsFromPeriod" @if(!$this->activePeriod || $preview['will_copy_count'] === 0) disabled @endif
                        class="flex-1 py-2 bg-gradient-to-br from-violet-600 to-purple-700 text-white font-bold rounded-xl text-xs disabled:opacity-50">
                        Ya, Salin {{ $preview['will_copy_count'] }} Santri
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
