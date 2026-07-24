<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white font-serif-display">Pusat Kendali Keuangan &amp; Tagihan</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Pusat kontrol tagihan bulanan syahriah, kas komplek, katering, dispensasi, dan penagihan kasir terpadu.</p>
        </div>
    </div>

    <!-- Alert Message -->
    @if (session()->has('message'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-2xl text-xs font-semibold">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 rounded-2xl text-xs font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="flex border-b border-slate-200 dark:border-slate-800 gap-2 overflow-x-auto pb-1">
        <button wire:click="$set('activeTab', 'generate')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'generate' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span>Penerbitan Tagihan</span>
        </button>
        <button wire:click="$set('activeTab', 'rates')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'rates' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
            <span>Konfigurasi Tarif & Target</span>
        </button>
        <button wire:click="$set('activeTab', 'cashier')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'cashier' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span>Kasir Utama (12 Bulan)</span>
        </button>
        <button wire:click="$set('activeTab', 'payments_log')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'payments_log' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <span>Riwayat Setoran (Log)</span>
        </button>
        <button wire:click="$set('activeTab', 'exceptions')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'exceptions' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <span>Dispensasi & Potongan</span>
        </button>
        <button wire:click="$set('activeTab', 'registration_rates')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'registration_rates' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Tarif Santri Baru &amp; Kitab</span>
        </button>
        <button wire:click="$set('activeTab', 'installments')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'installments' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Cicilan Event</span>
        </button>
    </div>

    <!-- Tabs Contents -->
    <div>
        <!-- TAB 1: GENERATE TAGIHAN -->
        @if ($activeTab === 'generate')
            <div class="space-y-8">
                <!-- Choices.js dependencies & ultra-sleek high-contrast styling -->
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
                <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
                <style>
                    .choices { margin-bottom: 0 !important; }
                    .choices__inner {
                        background-color: #f8fafc !important;
                        border: 1.5px solid #cbd5e1 !important;
                        border-radius: 0.75rem !important;
                        padding: 0.35rem 0.85rem !important;
                        min-height: 42px !important;
                        font-size: 0.75rem !important;
                        font-weight: 700 !important;
                        color: #0f172a !important;
                    }
                    .dark .choices__inner {
                        background-color: #020617 !important;
                        border-color: #334155 !important;
                        color: #f8fafc !important;
                    }
                    .choices__list--single {
                        padding-left: 0 !important;
                        color: #0f172a !important;
                        font-weight: 700 !important;
                    }
                    .dark .choices__list--single {
                        color: #f8fafc !important;
                    }
                    .choices__placeholder {
                        color: #64748b !important;
                        opacity: 1 !important;
                        font-weight: 600 !important;
                    }
                    .dark .choices__placeholder {
                        color: #94a3b8 !important;
                        opacity: 1 !important;
                    }
                    .choices__list--dropdown, .choices__list[aria-expanded] {
                        border: 1.5px solid #cbd5e1 !important;
                        border-radius: 1rem !important;
                        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
                        background-color: #ffffff !important;
                        color: #0f172a !important;
                        padding: 0.35rem !important;
                        z-index: 50 !important;
                    }
                    .dark .choices__list--dropdown, .dark .choices__list[aria-expanded] {
                        border-color: #334155 !important;
                        background-color: #0f172a !important;
                        color: #f8fafc !important;
                    }
                    .choices__list--dropdown .choices__item--selectable, .choices__list[aria-expanded] .choices__item--selectable {
                        padding: 0.65rem 0.85rem !important;
                        border-radius: 0.6rem !important;
                        font-size: 0.75rem !important;
                        font-weight: 700 !important;
                        color: #0f172a !important;
                        transition: all 0.15s ease !important;
                    }
                    .dark .choices__list--dropdown .choices__item--selectable, .dark .choices__list[aria-expanded] .choices__item--selectable {
                        color: #f8fafc !important;
                    }
                    .choices__list--dropdown .choices__item--selectable.is-highlighted {
                        background-color: rgba(16, 185, 129, 0.15) !important;
                        color: #047857 !important;
                    }
                    .dark .choices__list--dropdown .choices__item--selectable.is-highlighted {
                        background-color: rgba(16, 185, 129, 0.25) !important;
                        color: #34d399 !important;
                    }
                    .choices__input {
                        background-color: #f1f5f9 !important;
                        font-size: 0.75rem !important;
                        padding: 0.4rem 0.75rem !important;
                        border-radius: 0.5rem !important;
                        color: #0f172a !important;
                        border: 1px solid #cbd5e1 !important;
                        margin-bottom: 0.35rem !important;
                        font-weight: 600 !important;
                    }
                    .dark .choices__input {
                        background-color: #020617 !important;
                        color: #f8fafc !important;
                        border-color: #334155 !important;
                    }
                </style>

                <!-- Header KPI Stats Cards (Point 1) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Card 1: Status Penerbitan Bulan Ini -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 p-5 rounded-3xl shadow-xs flex items-center justify-between">
                        <div class="space-y-1">
                            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">Status Penerbitan ({{ $kpiStats['current_period_name'] }})</span>
                            @if($kpiStats['total_count'] > 0)
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></span>
                                    <span class="text-base font-extrabold text-emerald-600 dark:text-emerald-400">✓ Tagihan Terbit</span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">{{ $kpiStats['total_count'] }} tagihan aktif diterbitkan</p>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 bg-amber-500 rounded-full"></span>
                                    <span class="text-base font-extrabold text-amber-600 dark:text-amber-400">⚠️ Belum Terbit</span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Gunakan generator di bawah untuk men-generate</p>
                            @endif
                        </div>
                        <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800/80 rounded-2xl flex items-center justify-center text-xl">
                            📋
                        </div>
                    </div>

                    <!-- Card 2: Total Nominal Terbit Bulan Ini -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 p-5 rounded-3xl shadow-xs flex items-center justify-between">
                        <div class="space-y-1">
                            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">Nominal Terbit Bulan Ini</span>
                            <div class="text-lg font-black text-slate-900 dark:text-white font-serif-display">
                                Rp {{ number_format($kpiStats['total_amount'], 0, ',', '.') }}
                            </div>
                            <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold">
                                Terbayar: Rp {{ number_format($kpiStats['paid_amount'], 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center text-xl font-bold">
                            💰
                        </div>
                    </div>

                    <!-- Card 3: Persentase Pelunasan Bulan Ini -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 p-5 rounded-3xl shadow-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Pelunasan Bulan Ini</span>
                            <span class="text-xs font-black text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-full">
                                {{ $kpiStats['percentage'] }}%
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $kpiStats['percentage'] }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-slate-400 font-semibold pt-0.5">
                            <span>Lunas: {{ $kpiStats['paid_count'] }} santri</span>
                            <span>Total: {{ $kpiStats['total_count'] }} santri</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left: Dynamic Custom Generator Form -->
                    <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 sm:p-8 rounded-3xl space-y-6 shadow-sm">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider block font-serif-display">Penerbitan Tagihan Dinamis (Dynamic Bill Generator)</h3>
                            <p class="text-[11px] text-slate-400">Pilih konfigurasi tarif aktif untuk menerbitkan tagihan secara otomatis ke kelompok target santri yang telah ditentukan.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Pilih Konfigurasi Tarif</label>
                                <div x-data="{
                                    initChoices() {
                                        if (typeof Choices === 'undefined') return;
                                        const el = this.$refs.configSelect;
                                        if (el._choices) el._choices.destroy();
                                        const choices = new Choices(el, {
                                            searchEnabled: true,
                                            itemSelectText: '',
                                            shouldSort: false,
                                            placeholder: true,
                                            placeholderValue: '-- Pilih Konfigurasi Tarif --',
                                            noResultsText: 'Tidak ditemukan iuran yang cocok',
                                        });
                                        el._choices = choices;

                                        if ($wire.genConfigId) {
                                            choices.setChoiceByValue($wire.genConfigId);
                                        }

                                        this.$watch('$wire.genConfigId', val => {
                                            if (val) {
                                                choices.setChoiceByValue(val);
                                            } else {
                                                choices.removeActiveItems();
                                            }
                                        });

                                        el.addEventListener('change', (e) => {
                                            $wire.set('genConfigId', e.target.value);
                                        });
                                    }
                                }" x-init="initChoices()" wire:ignore>
                                    <select x-ref="configSelect" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-emerald-500 font-bold shadow-xs">
                                        <option value="">-- Pilih Konfigurasi Tarif --</option>
                                        @foreach($activeConfigs as $cfg)
                                            <option value="{{ $cfg->id }}" @selected($genConfigId == $cfg->id)>{{ $cfg->label }} (Rp {{ number_format($cfg->amount, 0, ',', '.') }} / {{ $cfg->interval }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @php
                                $selectedConfig = $genConfigId ? $activeConfigs->firstWhere('id', $genConfigId) : null;
                            @endphp
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Periode / Semester</label>
                                <select wire:model="genMonth" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-emerald-500 font-bold shadow-xs">
                                    @if($selectedConfig && $selectedConfig->interval === 'semester')
                                        <option value="1">Semester 1 (Ganjil)</option>
                                        <option value="2">Semester 2 (Genap)</option>
                                    @elseif($selectedConfig && in_array($selectedConfig->interval, ['once', 'insidental', 'event', 'sekali']))
                                        <option value="1">Insidental / Event (Sekali)</option>
                                    @else
                                        @for($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}">{{ $m }} ({{ date('F', mktime(0, 0, 0, $m, 1)) }})</option>
                                        @endfor
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Tahun</label>
                                <select wire:model="genYear" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-emerald-500 font-bold shadow-xs">
                                    @for($y = now()->format('Y') - 1; $y <= now()->format('Y') + 2; $y++)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-end gap-3 pt-2">
                            @php
                                $selectedConfig = $genConfigId ? $activeConfigs->firstWhere('id', $genConfigId) : null;
                            @endphp

                            @if($selectedConfig)
                                @if($selectedConfig->interval === 'monthly')
                                    <button wire:click="generateFullAcademicYearFromConfig" 
                                            wire:confirm="Apakah Anda yakin ingin men-generate tagihan untuk 1 tahun ajaran penuh (12 bulan) sekaligus mulai dari Juli tahun {{ $genYear }}?"
                                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-2">
                                        <span>📅 Terbitkan 1 Tahun Ajaran (12 Bulan)</span>
                                    </button>
                                @elseif($selectedConfig->interval === 'semester')
                                    <button wire:click="generateFullAcademicYearFromConfig" 
                                            wire:confirm="Apakah Anda yakin ingin men-generate tagihan me2 semester sekaligus di tahun {{ $genYear }}?"
                                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-2">
                                        <span>📅 Terbitkan 2 Semester sekaligus</span>
                                    </button>
                                @endif
                            @endif

                            <button wire:click="generateDynamicBills" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-2">
                                <span>Mulai Terbitkan Periode Terpilih</span>
                            </button>
                        </div>
                    </div>

                    <!-- Right: Polished Active Billing Status Panel (Point 5) -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 p-6 rounded-3xl space-y-4 shadow-sm">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider text-[11px] font-serif-display">Status Iuran Periode Ini</h4>
                                <p class="text-[10px] text-slate-400">Daftar tarif aktif & statusnya bulan ini</p>
                            </div>
                            <span class="text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-lg">
                                {{ count($activeConfigs) }} Config
                            </span>
                        </div>

                        <div class="space-y-2.5 max-h-72 overflow-y-auto pr-1">
                            @forelse($activeConfigs as $cfg)
                                @php
                                    $isGen = in_array($cfg->id, $kpiStats['generated_configs']);
                                @endphp
                                <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200/50 dark:border-slate-800/80 rounded-2xl text-xs">
                                    <div class="pr-2 truncate">
                                        <span class="font-bold text-slate-800 dark:text-slate-200 block truncate">{{ $cfg->label }}</span>
                                        <span class="text-[10px] text-slate-400">Rp {{ number_format($cfg->amount, 0, ',', '.') }} / {{ $cfg->interval }}</span>
                                    </div>
                                    <div>
                                        @if($isGen)
                                            <span class="px-2 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-extrabold rounded-xl border border-emerald-500/20 whitespace-nowrap">
                                                ✓ Terbit
                                            </span>
                                        @else
                                            <button wire:click="$set('genConfigId', '{{ $cfg->id }}')" class="px-2 py-1 bg-amber-500/10 hover:bg-amber-500 text-amber-600 hover:text-white text-[10px] font-extrabold rounded-xl transition-all whitespace-nowrap">
                                                ⚡ Pilih & Terbitkan
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-slate-400 text-xs font-semibold">
                                    Belum ada konfigurasi tarif aktif.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Tabel Riwayat Penerbitan Tagihan -->
                <div id="riwayat-penerbitan" class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 sm:p-8 rounded-3xl space-y-6 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider block font-serif-display">Riwayat Penerbitan Tagihan</h3>
                            <p class="text-[11px] text-slate-400">Daftar iuran yang sudah pernah digenerate dalam database. Anda dapat menghapus massal tagihan yang salah buat di sini.</p>
                        </div>
                        <!-- Filter Controls -->
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="w-40">
                                <input type="text" wire:model.live.debounce.300ms="histSearch" placeholder="🔍 Cari Nama..." 
                                       class="w-full bg-slate-50/90 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 placeholder-slate-400 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500 font-bold shadow-xs">
                            </div>
                            <div class="w-32">
                                <select wire:model.live="histMonth" class="w-full bg-slate-50/90 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-2.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 font-bold shadow-xs">
                                    <option value="">📅 Semua Bulan</option>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="w-28">
                                <select wire:model.live="histYear" class="w-full bg-slate-50/90 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-2.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 font-bold shadow-xs">
                                    <option value="">🗓️ Semua Thn</option>
                                    @for($y = now()->format('Y') - 2; $y <= now()->format('Y') + 2; $y++)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="w-36">
                                <select wire:model.live="histInterval" class="w-full bg-slate-50/90 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-2.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 font-bold shadow-xs">
                                    <option value="">⚡ Semua Siklus</option>
                                    <option value="monthly">Bulanan</option>
                                    <option value="semester">Semesteran</option>
                                    <option value="yearly">Tahunan</option>
                                    <option value="insidental">Sekali / Event</option>
                                </select>
                            </div>
                            <div class="w-36">
                                <select wire:model.live="histType" class="w-full bg-slate-50/90 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-2.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 font-bold shadow-xs">
                                    <option value="">🏷️ Semua Tipe</option>
                                    <option value="syahriah_pondok">Syahriah Pondok</option>
                                    <option value="kas_komplek">Kas Komplek</option>
                                    <option value="majek_pagi">Majek Pagi</option>
                                    <option value="majek_sore">Majek Sore</option>
                                    <option value="syahriah_madrasah">Syahriah Madrasah</option>
                                    <option value="kebersihan">Kebersihan</option>
                                    <option value="kitab">Kitab</option>
                                    <option value="insidental">Event / Kegiatan</option>
                                </select>
                            </div>
                            <div class="w-32">
                                <select wire:model.live="histGender" class="w-full bg-slate-50/90 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-2.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 font-bold shadow-xs">
                                    <option value="">👥 Target Gender</option>
                                    <option value="L">👦 Putra (L)</option>
                                    <option value="P">👧 Putri (P)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800">
                                    <th class="py-3.5 px-4 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">Nama Iuran</th>
                                    <th class="py-3.5 px-4 text-slate-500 font-extrabold uppercase tracking-wider text-[9px] text-center">Target Gender</th>
                                    <th class="py-3.5 px-4 text-slate-500 font-extrabold uppercase tracking-wider text-[9px] text-center">Tipe & Siklus</th>
                                    <th class="py-3.5 px-4 text-slate-500 font-extrabold uppercase tracking-wider text-[9px] text-center">Periode Tagihan</th>
                                    <th class="py-3.5 px-4 text-slate-500 font-extrabold uppercase tracking-wider text-[9px] text-center">Jumlah Santri</th>
                                    <th class="py-3.5 px-4 text-slate-500 font-extrabold uppercase tracking-wider text-[9px] text-right">Total Nominal</th>
                                    <th class="py-3.5 px-4 text-slate-500 font-extrabold uppercase tracking-wider text-[9px] text-center">Diterbitkan Oleh</th>
                                    <th class="py-3.5 px-4 text-slate-500 font-extrabold uppercase tracking-wider text-[9px] text-center">Waktu Penerbitan</th>
                                    <th class="py-3.5 px-4 text-slate-500 font-extrabold uppercase tracking-wider text-[9px] text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                @forelse($generationHistory as $hist)
                                    @php
                                        $cfg = $hist->config;
                                        $targetGenders = ($cfg && $cfg->target_type === 'all' && !empty($cfg->target_filters)) ? (array)$cfg->target_filters : [];
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                        <td class="py-3.5 px-4 font-semibold text-slate-800 dark:text-slate-200">
                                            {{ $cfg?->label ?? 'Iuran Terhapus' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            @if(empty($targetGenders) || count($targetGenders) >= 2)
                                                <span class="px-2 py-0.5 bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[10px] font-extrabold rounded-md">
                                                    🌐 Putra & Putri
                                                </span>
                                            @elseif(in_array('L', $targetGenders))
                                                <span class="px-2 py-0.5 bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 text-[10px] font-extrabold rounded-md">
                                                    👦 Putra
                                                </span>
                                            @elseif(in_array('P', $targetGenders))
                                                <span class="px-2 py-0.5 bg-pink-500/10 text-pink-600 dark:text-pink-400 text-[10px] font-extrabold rounded-md">
                                                    👧 Putri
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-[10px]">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-[10px] font-extrabold rounded-lg uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                                {{ $cfg?->interval ?? 'once' }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center font-bold text-slate-700 dark:text-slate-300">
                                            @if($cfg && $cfg->interval === 'semester')
                                                Semester {{ $hist->period_month }} / {{ $hist->period_year }}
                                            @elseif($cfg && in_array($cfg->interval, ['once', 'insidental', 'event', 'sekali']))
                                                Event / Tahun {{ $hist->period_year }}
                                            @else
                                                {{ date('F', mktime(0, 0, 0, $hist->period_month, 1)) }} {{ $hist->period_year }}
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-center font-bold text-emerald-600 dark:text-emerald-400">
                                            👤 {{ $hist->total_students }} Santri
                                        </td>
                                        <td class="py-3.5 px-4 text-right font-bold text-slate-800 dark:text-slate-200">
                                            Rp {{ number_format($hist->total_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-purple-500/10 text-purple-700 dark:text-purple-300 text-[10px] font-extrabold rounded-lg">
                                                👤 {{ $hist->creator?->name ?? ($cfg?->creator?->name ?? 'Sistem') }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center text-slate-400 text-[10px]">
                                            {{ \Carbon\Carbon::parse($hist->generated_at)->translatedFormat('d M Y H:i') }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            @if($cfg)
                                                <button wire:click="deleteBatchGeneration('{{ $cfg->id }}', {{ $hist->period_month }}, {{ $hist->period_year }})"
                                                        wire:confirm="Apakah Anda yakin ingin membatalkan & menghapus massal seluruh tagihan BELUM DIBAYAR untuk iuran '{{ $cfg->label }}' periode ini? Tagihan yang sudah dibayar lunas atau sebagian tidak akan terhapus demi keamanan data."
                                                        class="px-3 py-1.5 bg-rose-500/10 hover:bg-rose-500 text-rose-600 hover:text-white rounded-xl text-[10px] font-bold transition-all shadow-2xs">
                                                    🗑️ Hapus Unpaid
                                                </button>
                                            @else
                                                <span class="text-slate-300 dark:text-slate-700">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="py-12 text-center text-slate-400 font-semibold">
                                            Belum ada riwayat penerbitan tagihan yang cocok dengan filter.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Footer for History Table -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-[11px] font-semibold text-slate-400">
                            Menampilkan <span class="font-bold text-slate-700 dark:text-slate-300">{{ $generationHistory->firstItem() ?? 0 }}</span> s.d. <span class="font-bold text-slate-700 dark:text-slate-300">{{ $generationHistory->lastItem() ?? 0 }}</span> dari <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $generationHistory->total() }}</span> riwayat penerbitan
                        </div>
                        <div>
                            {{ $generationHistory->links(data: ['scrollTo' => false]) }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB 2: KONFIGURASI TARIF & TARGET -->
        @if ($activeTab === 'rates')
            <div class="space-y-8">
                 <!-- Configurations List Table -->
                 <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 rounded-3xl shadow-sm overflow-hidden space-y-4">
                     <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-3">
                         <div>
                             <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider block font-serif-display">Daftar Konfigurasi Tarif Aktif</h3>
                             <p class="text-[11px] text-slate-400">Semua skema iuran aktif yang dapat diterbitkan ke santri.</p>
                         </div>
                         <a href="{{ route('keuangan.billing.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-1.5 self-start sm:self-auto">
                             <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                             Tambah Iuran Baru
                         </a>
                     </div>
                     <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">
                                    <th class="py-3 px-4">Nama Iuran</th>
                                    <th class="py-3 px-4">Tipe Tagihan</th>
                                    <th class="py-3 px-4 text-right">Nominal</th>
                                    <th class="py-3 px-4">Siklus</th>
                                    <th class="py-3 px-4 text-center">Bisa Dicicil</th>
                                    <th class="py-3 px-4">Target</th>
                                    <th class="py-3 px-4">Pengelola</th>
                                    <th class="py-3 px-4">Dibuat Oleh</th>
                                    <th class="py-3 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                @foreach($activeConfigs as $ac)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                        <td class="py-3 px-4 font-bold text-slate-800 dark:text-slate-200">{{ $ac->label }}</td>
                                        <td class="py-3 px-4 uppercase text-slate-500 text-[10px]">{{ str_replace('_', ' ', $ac->type) }}</td>
                                        <td class="py-3 px-4 text-right font-semibold text-slate-800 dark:text-slate-200">Rp {{ number_format($ac->amount, 0, ',', '.') }}</td>
                                        <td class="py-3 px-4 uppercase text-[10px] text-slate-500">{{ $ac->interval }}</td>
                                        <td class="py-3 px-4 text-center">
                                            @if($ac->can_be_installment)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-emerald-500/10 text-emerald-600">YA</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-slate-100 text-slate-500">TIDAK</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase {{ $ac->target_type === 'all' ? 'bg-blue-500/10 text-blue-600' : ($ac->target_type === 'dormitory' ? 'bg-amber-500/10 text-amber-600' : 'bg-purple-500/10 text-purple-600') }}">
                                                @if($ac->target_type === 'all')
                                                    @if(is_array($ac->target_filters) && in_array('P', $ac->target_filters))
                                                        Semua Santri (Putri)
                                                    @elseif(is_array($ac->target_filters) && in_array('L', $ac->target_filters))
                                                        Semua Santri (Putra)
                                                    @else
                                                        Semua Santri
                                                    @endif
                                                @else
                                                    {{ $ac->target_type }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-slate-600 dark:text-slate-400 font-semibold">{{ $ac->manager_role ?: 'Bendahara Pusat' }}</td>
                                        <td class="py-3 px-4 text-slate-700 dark:text-slate-300 font-semibold text-xs">
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md text-[10px] text-slate-700 dark:text-slate-300 font-bold">
                                                👤 {{ $ac->creator?->name ?: 'Sistem' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                             <a href="{{ route('keuangan.billing.print-setup', $ac->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 transition-colors mr-3">
                                                 Cetak
                                             </a>
                                             <a href="{{ route('keuangan.billing.edit', $ac->id) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition-colors mr-3">
                                                 Edit
                                             </a>
                                             <button wire:click="deleteConfig('{{ $ac->id }}')" onclick="confirm('Apakah Anda yakin ingin menghapus konfigurasi ini?') || event.stopImmediatePropagation()" class="text-xs font-bold text-rose-600 hover:text-rose-700 transition-colors">
                                                 Hapus
                                             </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB 3: KASIR PEMBAYARAN UTAMA -->
        @if ($activeTab === 'cashier')
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- ===== LEFT PANEL: BROWSE & SEARCH ===== --}}
                <div class="lg:col-span-4 space-y-4">

                    {{-- Browse / Search Card --}}
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">

                        {{-- Header --}}
                        <div class="px-5 pt-5 pb-4 border-b border-slate-100 dark:border-slate-800">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider font-serif-display">Pilih Santri</h3>
                            <p class="text-[10px] text-slate-400 mt-0.5">Browse lewat filter, atau langsung cari nama / NIS.</p>
                        </div>

                        {{-- Filter Row: Komplek → Kamar → Kelas --}}
                        <div class="px-4 pt-4 pb-3 space-y-2 border-b border-slate-100 dark:border-slate-800">
                            {{-- Komplek --}}
                            <select wire:model.live="filterKomplek"
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-xl px-3 py-2 text-xs focus:ring-emerald-500 font-semibold">
                                <option value="">🏘 Semua Komplek</option>
                                @foreach($dormitories as $dorm)
                                    <option value="{{ $dorm->id }}">{{ $dorm->name }}</option>
                                @endforeach
                            </select>

                            {{-- Kamar (hanya muncul jika komplek dipilih) --}}
                            @if($filterKomplek && !$roomsForKomplek->isEmpty())
                                <select wire:model.live="filterKamar"
                                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-xl px-3 py-2 text-xs focus:ring-emerald-500 font-semibold">
                                    <option value="">🚪 Semua Kamar</option>
                                    @foreach($roomsForKomplek as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                                    @endforeach
                                </select>
                            @endif

                            {{-- Kelas --}}
                            <select wire:model.live="filterKelas"
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-xl px-3 py-2 text-xs focus:ring-emerald-500 font-semibold">
                                <option value="">📚 Semua Kelas</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->name }}</option>
                                @endforeach
                            </select>

                            {{-- Reset filter --}}
                            @if($filterKomplek || $filterKamar || $filterKelas)
                                <button wire:click="$set('filterKomplek', ''); $set('filterKamar', ''); $set('filterKelas', '')"
                                    class="w-full text-center text-[10px] text-slate-400 hover:text-rose-500 font-bold transition-colors py-0.5">
                                    ✕ Reset Filter
                                </button>
                            @endif
                        </div>

                        {{-- Search Bar --}}
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                                </svg>
                                <input type="text" wire:model.live.debounce.300ms="searchQuery"
                                    placeholder="Cari nama santri atau NIS..."
                                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl pl-9 pr-8 py-2 text-xs focus:ring-emerald-500">
                                @if($searchQuery)
                                    <button wire:click="$set('searchQuery', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 font-bold text-xs">✕</button>
                                @endif
                            </div>
                        </div>

                        {{-- Santri List --}}
                        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
                            @if(!$santriSearchResults->isEmpty())
                                @if($filterKomplek || $filterKamar || $filterKelas || strlen($searchQuery) >= 2)
                                    <div class="px-4 py-2 flex items-center justify-between">
                                        <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest">
                                            {{ $santriSearchResults->count() }} Santri Ditemukan
                                        </span>
                                        @if($filterKomplek || $filterKamar || $filterKelas)
                                            <span class="text-[9px] text-emerald-600 font-bold">Hasil Filter</span>
                                        @else
                                            <span class="text-[9px] text-slate-400">Hasil Pencarian</span>
                                        @endif
                                    </div>
                                @endif

                                @foreach($santriSearchResults as $s)
                                    @php
                                        $sRoom = $s->roomAssignments->firstWhere('is_active', true)?->room;
                                        $sDorm = $sRoom?->dormitory?->name ?? null;
                                        $sKamar = $sRoom?->name ?? null;
                                        $sKelas = $s->madrasahEnrollments->firstWhere('is_active', true)?->kelas?->name ?? null;
                                        $isSelected = $selectedSantriId === $s->id;
                                    @endphp
                                    <button wire:click="selectSantri('{{ $s->id }}')"
                                        class="w-full text-left px-4 py-3 transition-all flex items-center gap-3
                                            {{ $isSelected
                                                ? 'bg-emerald-50 dark:bg-emerald-950/30 border-l-2 border-emerald-500'
                                                : 'hover:bg-slate-50 dark:hover:bg-slate-800/40 border-l-2 border-transparent' }}">
                                        {{-- Avatar --}}
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-xs font-extrabold
                                            {{ $isSelected ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                                            {{ substr($s->name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <span class="font-bold text-slate-800 dark:text-slate-200 block text-xs truncate">{{ $s->name }}</span>
                                            <span class="text-[9px] text-slate-400 block truncate">
                                                @if($sDorm) {{ $sDorm }} @if($sKamar) · {{ $sKamar }} @endif @else Laju @endif
                                                @if($sKelas)  · {{ $sKelas }} @endif
                                            </span>
                                        </div>
                                        @if($isSelected)
                                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        @else
                                            <svg class="w-3.5 h-3.5 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        @endif
                                    </button>
                                @endforeach

                            @elseif($filterKomplek || $filterKamar || $filterKelas || strlen($searchQuery) >= 2)
                                <div class="px-4 py-8 text-center">
                                    <div class="text-2xl mb-2">😕</div>
                                    <p class="text-[10px] text-slate-400 font-semibold">Tidak ada santri ditemukan</p>
                                    <p class="text-[9px] text-slate-400 mt-0.5">Coba ubah filter atau kata pencarian</p>
                                </div>

                            @elseif(!$recentSantri->isEmpty())
                                {{-- Recent Santri (shown when no active filter/search) --}}
                                <div class="px-4 py-2">
                                    <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest">🕐 Terakhir Dibuka</span>
                                </div>
                                @foreach($recentSantri as $rs)
                                    @php
                                        $rsRoom = $rs->roomAssignments->firstWhere('is_active', true)?->room;
                                        $rsDorm = $rsRoom?->dormitory?->name ?? 'Laju';
                                        $rsKamar = $rsRoom?->name ?? null;
                                        $rsSelected = $selectedSantriId === $rs->id;
                                    @endphp
                                    <button wire:click="selectSantri('{{ $rs->id }}')"
                                        class="w-full text-left px-4 py-3 transition-all flex items-center gap-3
                                            {{ $rsSelected
                                                ? 'bg-emerald-50 dark:bg-emerald-950/30 border-l-2 border-emerald-500'
                                                : 'hover:bg-slate-50 dark:hover:bg-slate-800/40 border-l-2 border-transparent' }}">
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-xs font-extrabold
                                            {{ $rsSelected ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                                            {{ substr($rs->name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <span class="font-bold text-slate-800 dark:text-slate-200 block text-xs truncate">{{ $rs->name }}</span>
                                            <span class="text-[9px] text-slate-400">{{ $rsDorm }}@if($rsKamar) · {{ $rsKamar }} @endif</span>
                                        </div>
                                        @if($rsSelected)
                                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </button>
                                @endforeach

                            @else
                                <div class="px-4 py-8 text-center">
                                    <div class="text-3xl mb-2">👥</div>
                                    <p class="text-[10px] text-slate-400 font-semibold">Pilih komplek / kelas di atas</p>
                                    <p class="text-[9px] text-slate-400 mt-0.5">atau ketik nama / NIS santri</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Payment Form --}}
                    @if($selectedSantri)
                        <div x-data="{ 
                            totalSelected: {{ $this->selectedBillsTotal }},
                            payAmount: @entangle('payAmount')
                        }" x-effect="totalSelected = {{ $this->selectedBillsTotal }}" class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-5 rounded-3xl shadow-sm space-y-4">
                            <div>
                                <span class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider">Formulir Pembayaran</span>
                                <p class="text-[10px] text-slate-500 mt-0.5">Centang tagihan di kanan lalu isi nominal.</p>
                            </div>

                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Total Terpilih</label>
                                <div class="px-4 py-3 bg-emerald-50 dark:bg-emerald-950/20 rounded-xl text-lg font-extrabold text-emerald-600 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/30">
                                    Rp {{ number_format($this->selectedBillsTotal, 0, ',', '.') }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Uang Diterima (Rp)</label>
                                <input type="number" x-model.number="payAmount" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500 text-right font-bold">
                                @error('payAmount')
                                    <p class="text-[10px] text-rose-500 mt-1 font-semibold">⚠ {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Metode Setoran</label>
                                <select wire:model="payMethod" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2 text-xs focus:ring-emerald-500 font-bold">
                                    <option value="CASH">💵 Cash (Tunai)</option>
                                    <option value="TRANSFER">🏦 Transfer Bank</option>
                                    <option value="EWALLET">📱 E-Wallet</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Catatan</label>
                                <input type="text" wire:model="payNotes" placeholder="Opsional: catatan transaksi..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2 text-xs focus:ring-emerald-500">
                            </div>

                            @if(!empty($selectedBillIds))
                                <button wire:click="initiatePayment"
                                    class="w-full px-5 py-3 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Catat Pembayaran ({{ count($selectedBillIds) }} tagihan)
                                </button>
                            @else
                                <div class="w-full px-5 py-3 bg-slate-100 dark:bg-slate-800 text-slate-400 rounded-xl text-xs font-bold text-center">
                                    ☑ Centang tagihan terlebih dahulu
                                </div>
                            @endif
                        </div>
                    @endif

                </div>

                {{-- ===== RIGHT PANEL: LEMBAR TAGIHAN ===== --}}
                <div class="lg:col-span-8 space-y-5">
                    @if(!$selectedSantri)
                        <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-16 rounded-3xl text-center shadow-sm">
                            <div class="text-4xl mb-3">🔍</div>
                            <p class="text-slate-400 text-sm font-semibold">Cari dan pilih santri di sebelah kiri</p>
                            <p class="text-slate-400 text-xs mt-1">untuk membuka lembar tagihan & pembayaran.</p>
                        </div>
                    @else
                        @php
                            $monthNames = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agt',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
                            $nowMonth = (int)now()->format('n');
                            $nowYear  = (int)now()->format('Y');
                        @endphp

                        {{-- Profile Header --}}
                        <div class="p-5 bg-slate-900 text-white rounded-3xl shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center font-extrabold text-xl text-white shrink-0">
                                    {{ substr($selectedSantri->name, 0, 1) }}
                                </div>
                                <div>
                                    <h2 class="text-base font-bold">{{ $selectedSantri->name }}</h2>
                                    <span class="text-xs text-slate-400">
                                        NIS: {{ $selectedSantri->nis ?? '—' }} &nbsp;|&nbsp;
                                        {{ $selectedSantri->gender === 'L' ? '👦 Putra' : '👧 Putri' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-5 text-xs shrink-0">
                                <div>
                                    <span class="text-slate-500 text-[9px] uppercase tracking-wider block">Komplek</span>
                                    <span class="font-bold text-emerald-400">{{ $selectedSantri->roomAssignments->firstWhere('is_active', true)?->room?->dormitory?->name ?? '🚗 Laju' }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-500 text-[9px] uppercase tracking-wider block">Kelas</span>
                                    <span class="font-bold text-amber-400">{{ $selectedSantri->madrasahEnrollments->firstWhere('is_active', true)?->kelas?->name ?? 'Non-Madrasah' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- 0: TUNGGAKAN LAMA --}}
                        @if(!$this->tunggakanLamaBills->isEmpty())
                            <div class="bg-rose-500/5 border border-rose-500/20 p-5 rounded-3xl space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-xs font-extrabold text-rose-600 dark:text-rose-400 uppercase tracking-widest">🚨 Tunggakan Tahun Lalu</h4>
                                        <p class="text-[10px] text-slate-500">Tagihan dari sebelum tahun {{ $cashierYear }} yang belum dilunasi.</p>
                                    </div>
                                    <div class="flex gap-2">
                                        @php
                                            $tunggakanIds = $this->tunggakanLamaBills->pluck('id')->toArray();
                                            $selectedTunggakanCount = count(array_intersect($tunggakanIds, $selectedBillIds));
                                        @endphp
                                        
                                        @if($selectedTunggakanCount > 0)
                                            <button type="button" wire:click="deselectTunggakan"
                                                class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-[10px] font-bold shrink-0 transition-colors">
                                                Batal Pilih ({{ $selectedTunggakanCount }})
                                            </button>
                                        @endif
                                        
                                        @if($selectedTunggakanCount < count($tunggakanIds))
                                            <button type="button" wire:click="selectTunggakan"
                                                class="px-3 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 rounded-xl text-[10px] font-bold shrink-0 transition-colors">
                                                Pilih Semua
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-xs border-collapse">
                                        <thead>
                                            <tr class="text-rose-600 font-bold uppercase text-[9px] border-b border-rose-500/10">
                                                <th class="py-2 px-2 text-left w-6"></th>
                                                <th class="py-2 px-2 text-left">Nama Tagihan</th>
                                                <th class="py-2 px-2 text-center">Periode</th>
                                                <th class="py-2 px-2 text-right">Sisa Bayar</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-rose-500/5">
                                            @foreach($this->tunggakanLamaBills as $tb)
                                                <tr class="{{ in_array($tb->id, $selectedBillIds) ? 'bg-rose-50 dark:bg-rose-950/20' : '' }}">
                                                    <td class="py-2 px-2">
                                                        <input type="checkbox" wire:model.live="selectedBillIds" value="{{ $tb->id }}" class="rounded text-rose-600 focus:ring-rose-500">
                                                    </td>
                                                    <td class="py-2 px-2 font-semibold text-slate-700 dark:text-slate-300">{{ $tb->config?->label ?? str_replace('_', ' ', $tb->bill_type) }}</td>
                                                    <td class="py-2 px-2 text-center text-slate-500">{{ $monthNames[$tb->period_month] }} {{ $tb->period_year }}</td>
                                                    <td class="py-2 px-2 text-right font-bold text-rose-600">Rp {{ number_format($tb->amount - $tb->amount_paid, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- 1: TAGIHAN BULANAN (Jan-Des tabel) --}}
                        @php $bulananBills = $this->bulananBills; @endphp
                        <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                                <div>
                                    <h4 class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-widest">📋 Tagihan Bulanan</h4>
                                    <p class="text-[10px] text-slate-400">Lembar iuran Januari — Desember</p>
                                </div>
                                <select wire:model.live="cashierYear" class="bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-1.5 text-xs font-bold focus:ring-emerald-500">
                                    @for($y = $nowYear - 2; $y <= $nowYear + 1; $y++)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            @if(empty($bulananBills))
                                <div class="p-8 text-center text-slate-400 text-xs">
                                    Belum ada tagihan bulanan untuk santri ini di tahun {{ $cashierYear }}.
                                </div>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse text-xs min-w-[700px]">
                                        <thead>
                                            <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800">
                                                <th class="py-3 px-3 text-[9px] font-extrabold text-slate-500 uppercase tracking-wider w-36 sticky left-0 bg-slate-50 dark:bg-slate-950">Jenis Iuran</th>
                                                @foreach($monthNames as $mNum => $mLabel)
                                                    <th class="py-3 px-1 text-center text-[9px] font-extrabold uppercase
                                                        {{ ($mNum === $nowMonth && $cashierYear === $nowYear) ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500' }}">
                                                        {{ $mLabel }}
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                            @foreach($bulananBills as $configId => $configData)
                                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors">
                                                    <td class="py-3 px-3 font-semibold text-slate-700 dark:text-slate-300 text-[10px] leading-tight sticky left-0 bg-white dark:bg-slate-900">
                                                        {{ $configData['label'] }}
                                                    </td>
                                                    @foreach($monthNames as $mNum => $mLabel)
                                                        @php $bill = $configData['months'][$mNum] ?? null; @endphp
                                                        <td class="py-2 px-1 text-center {{ ($bill && $bill->status === 'partial') ? 'bg-amber-500/5 dark:bg-amber-950/20' : '' }}">
                                                            @if(!$bill)
                                                                <span class="text-slate-200 dark:text-slate-700 text-base">—</span>
                                                            @elseif($bill->status === 'paid')
                                                                <span title="Lunas — Rp {{ number_format($bill->amount, 0, ',', '.') }}"
                                                                    class="inline-flex items-center justify-center w-6 h-6 bg-emerald-500 rounded-full cursor-default">
                                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                                </span>
                                                            @elseif($bill->status === 'partial')
                                                                <label class="cursor-pointer flex flex-col items-center justify-center gap-0.5" title="Cicilan — Sisa Rp {{ number_format($bill->amount - $bill->amount_paid, 0, ',', '.') }}">
                                                                    <input type="checkbox" wire:model.live="selectedBillIds" value="{{ $bill->id }}"
                                                                        class="rounded text-amber-500 accent-amber-500 focus:ring-amber-400 w-4 h-4 border-amber-300 dark:border-amber-700 bg-amber-50/50">
                                                                    <span class="text-[8px] font-extrabold text-amber-600 dark:text-amber-400 whitespace-nowrap scale-90 leading-none">Sisa {{ number_format(($bill->amount - $bill->amount_paid)/1000, 0) }}k</span>
                                                                </label>
                                                            @else
                                                                <label class="cursor-pointer flex items-center justify-center" title="Belum Bayar — Rp {{ number_format($bill->amount, 0, ',', '.') }}">
                                                                    <input type="checkbox" wire:model.live="selectedBillIds" value="{{ $bill->id }}"
                                                                        class="rounded text-emerald-600 accent-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                                                </label>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{-- Legend --}}
                                <div class="flex items-center gap-4 px-5 py-3 border-t border-slate-100 dark:border-slate-800 text-[9px] text-slate-400 font-semibold">
                                    <span class="flex items-center gap-1"><span class="w-4 h-4 bg-emerald-500 rounded-full inline-flex items-center justify-center"><svg class="w-2 h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></span> Lunas</span>
                                    <span class="flex items-center gap-1"><input type="checkbox" class="rounded text-emerald-600 accent-emerald-600 w-3.5 h-3.5" disabled> Belum Bayar (klik untuk pilih)</span>
                                    <span class="flex items-center gap-1"><input type="checkbox" class="rounded text-amber-500 accent-amber-500 w-3.5 h-3.5" disabled checked> Cicilan/Parsial</span>
                                    <span class="flex items-center gap-1"><span class="text-slate-300">—</span> Belum Terbit</span>
                                </div>
                            @endif
                        </div>

                        {{-- 2: TAGIHAN SEMESTER --}}
                        @php $semesterBills = $this->semesterBills; @endphp
                        @if(!empty($semesterBills))
                            <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
                                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                                    <h4 class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-widest">📅 Tagihan Semester</h4>
                                    <p class="text-[10px] text-slate-400">Iuran yang dibayarkan per 6 bulan (Semester 1 & 2)</p>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-xs border-collapse">
                                        <thead>
                                            <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800">
                                                <th class="py-3 px-4 text-left text-[9px] font-extrabold text-slate-500 uppercase tracking-wider">Jenis Iuran</th>
                                                <th class="py-3 px-4 text-center text-[9px] font-extrabold text-slate-500 uppercase tracking-wider">Semester 1 (Jan–Jun)</th>
                                                <th class="py-3 px-4 text-center text-[9px] font-extrabold text-slate-500 uppercase tracking-wider">Semester 2 (Jul–Des)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                            @foreach($semesterBills as $configId => $configData)
                                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                                    <td class="py-3 px-4 font-semibold text-slate-700 dark:text-slate-300">{{ $configData['label'] }}</td>
                                                    @foreach([1 => 'Semester 1', 2 => 'Semester 2'] as $semNum => $semLabel)
                                                        @php $sBill = $configData['bills'][$semNum] ?? null; @endphp
                                                        <td class="py-3 px-4 text-center">
                                                            @if(!$sBill)
                                                                <span class="text-[10px] text-slate-400 italic">Belum Terbit</span>
                                                            @elseif($sBill->status === 'paid')
                                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-xl text-[10px] font-bold">
                                                                    ✓ Lunas · Rp {{ number_format($sBill->amount, 0, ',', '.') }}
                                                                </span>
                                                            @else
                                                                <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border cursor-pointer transition-all
                                                                    {{ in_array($sBill->id, $selectedBillIds) ? 'bg-rose-50 border-rose-400 dark:bg-rose-950/20' : 'bg-slate-50 border-slate-200 dark:bg-slate-950 dark:border-slate-700 hover:border-rose-300' }}">
                                                                    <input type="checkbox" wire:model.live="selectedBillIds" value="{{ $sBill->id }}" class="rounded text-rose-500 focus:ring-rose-400 w-3.5 h-3.5">
                                                                    <span class="text-[10px] font-bold text-slate-700 dark:text-slate-300">
                                                                        Rp {{ number_format($sBill->amount - $sBill->amount_paid, 0, ',', '.') }}
                                                                        @if($sBill->status === 'partial') <span class="text-amber-500">(cicilan)</span> @endif
                                                                    </span>
                                                                </label>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- 3: TAGIHAN INSIDENTAL / EVENT --}}
                        @php $insidentalBills = $this->insidentalBills; @endphp
                        @if(!$insidentalBills->isEmpty())
                            <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
                                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                                    <h4 class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-widest">⚡ Tagihan Khusus / Insidental</h4>
                                    <p class="text-[10px] text-slate-400">Iuran sekali bayar, event, atau tahunan</p>
                                </div>
                                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach($insidentalBills as $ib)
                                        <div class="flex items-center justify-between px-5 py-3.5 {{ $ib->status !== 'paid' ? 'hover:bg-slate-50 dark:hover:bg-slate-800/30' : '' }} transition-colors">
                                            <div class="flex items-center gap-3 min-w-0">
                                                @if($ib->status !== 'paid')
                                                    <input type="checkbox" wire:model.live="selectedBillIds" value="{{ $ib->id }}"
                                                        class="rounded text-purple-600 focus:ring-purple-500 w-4 h-4 shrink-0">
                                                @else
                                                    <span class="w-4 h-4 bg-emerald-500 rounded-full flex items-center justify-center shrink-0">
                                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    </span>
                                                @endif
                                                <div class="min-w-0">
                                                    <span class="font-semibold text-slate-700 dark:text-slate-300 text-xs block truncate">{{ $ib->config?->label ?? ($ib->title ?: str_replace('_', ' ', $ib->bill_type)) }}</span>
                                                    <span class="text-[9px] text-slate-400">{{ $monthNames[$ib->period_month] ?? '' }} {{ $ib->period_year }}</span>
                                                </div>
                                            </div>
                                            <div class="text-right shrink-0">
                                                @if($ib->status === 'paid')
                                                    <span class="text-[10px] font-bold text-emerald-600">✓ LUNAS</span>
                                                    <span class="block text-[9px] text-slate-400">Rp {{ number_format($ib->amount, 0, ',', '.') }}</span>
                                                @elseif($ib->status === 'partial')
                                                    <span class="text-[10px] font-bold text-amber-500">Sisa Rp {{ number_format($ib->amount - $ib->amount_paid, 0, ',', '.') }}</span>
                                                    <span class="block text-[9px] text-slate-400">dari Rp {{ number_format($ib->amount, 0, ',', '.') }}</span>
                                                @else
                                                    <span class="text-[10px] font-bold text-rose-500">Rp {{ number_format($ib->amount, 0, ',', '.') }}</span>
                                                    <span class="block text-[9px] text-slate-400">Belum Dibayar</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- 4: PREPAID TAHUN DEPAN --}}
                        @if(!$this->paidFutureBills->isEmpty())
                            <div class="bg-emerald-500/5 border border-emerald-500/20 p-5 rounded-3xl space-y-3">
                                <div>
                                    <h4 class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">💚 Pembayaran di Muka</h4>
                                    <p class="text-[10px] text-slate-400">Tagihan tahun {{ $cashierYear + 1 }} ke atas yang sudah dilunasi.</p>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    @foreach($this->paidFutureBills as $fb)
                                        <div class="p-3 bg-white dark:bg-slate-950 border border-emerald-100 dark:border-emerald-900/30 rounded-2xl flex items-center justify-between text-xs">
                                            <div>
                                                <span class="font-bold text-slate-700 dark:text-slate-300 block text-[10px] truncate">{{ $fb->config?->label ?? str_replace('_', ' ', $fb->bill_type) }}</span>
                                                <span class="text-[9px] text-slate-400">{{ $monthNames[$fb->period_month] ?? '' }} {{ $fb->period_year }}</span>
                                            </div>
                                            <span class="w-5 h-5 bg-emerald-500 text-white rounded-full flex items-center justify-center shrink-0">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- ===== CONFIRMATION MODAL ===== --}}
            @if($showPaymentConfirmModal && $selectedSantri)
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="confirm-modal-title">
                    {{-- Backdrop --}}
                    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" wire:click="cancelPayment"></div>

                    {{-- Modal Box --}}
                    <div class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                            <div>
                                <p class="text-[9px] font-extrabold text-emerald-500 uppercase tracking-widest mb-0.5">Konfirmasi Pembayaran</p>
                                <h3 id="confirm-modal-title" class="text-sm font-bold text-slate-900 dark:text-white">Apakah data pembayaran sudah benar?</h3>
                            </div>
                            <button type="button" wire:click="cancelPayment"
                                class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all font-bold text-lg leading-none">
                                &times;
                            </button>
                        </div>

                        {{-- Body --}}
                        <div class="p-6 space-y-4">
                            {{-- Santri Profile Summary --}}
                            <div class="p-4 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-100 dark:border-slate-850 flex items-center gap-3">
                                <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center font-extrabold text-white">
                                    {{ substr($selectedSantri->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <span class="font-bold text-slate-850 dark:text-slate-100 block text-xs truncate">{{ $selectedSantri->name }}</span>
                                    <span class="text-[10px] text-slate-400 block truncate">
                                        NIS: {{ $selectedSantri->nis ?? '—' }} | {{ $selectedSantri->gender === 'L' ? 'Putra' : 'Putri' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Payment details table --}}
                            <div class="space-y-2">
                                <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">Rincian Tagihan yang Dibayar:</span>
                                <div class="border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden max-h-40 overflow-y-auto">
                                    <table class="w-full text-xs text-left border-collapse">
                                        <thead class="bg-slate-50 dark:bg-slate-950">
                                            <tr class="border-b border-slate-100 dark:border-slate-800 text-[9px] font-extrabold text-slate-400 uppercase">
                                                <th class="py-2 px-3">Nama Tagihan</th>
                                                <th class="py-2 px-3 text-center">Periode</th>
                                                <th class="py-2 px-3 text-right">Sisa Tagihan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                            @foreach($this->confirmBills as $cb)
                                                <tr>
                                                    <td class="py-2 px-3 font-semibold text-slate-700 dark:text-slate-300">
                                                        {{ $cb->config?->label ?? str_replace('_', ' ', $cb->bill_type) }}
                                                    </td>
                                                    <td class="py-2 px-3 text-center text-slate-500">
                                                        {{ $monthNames[$cb->period_month] ?? '' }} {{ $cb->period_year }}
                                                    </td>
                                                    <td class="py-2 px-3 text-right font-bold text-slate-800 dark:text-slate-200">
                                                        Rp {{ number_format($cb->amount - $cb->amount_paid, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Math calculations summary --}}
                            <div class="p-4 bg-emerald-500/5 border border-emerald-500/10 rounded-2xl space-y-2">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-500 font-semibold">Total Tagihan Terpilih:</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">Rp {{ number_format($this->selectedBillsTotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-555 dark:text-slate-400 font-semibold">Uang Diterima:</span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-450">Rp {{ number_format($payAmount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-500 font-semibold">Tipe Pembayaran:</span>
                                    @if($payAmount < $this->selectedBillsTotal)
                                        <span class="font-extrabold text-amber-600 dark:text-amber-405 bg-amber-500/10 px-2 py-0.5 rounded-lg text-[9px] uppercase tracking-wider">
                                            ⚡ Cicilan / Sebagian
                                        </span>
                                    @else
                                        <span class="font-extrabold text-emerald-600 dark:text-emerald-450 bg-emerald-500/10 px-2 py-0.5 rounded-lg text-[9px] uppercase tracking-wider">
                                            ✓ Pelunasan Lunas
                                        </span>
                                    @endif
                                </div>
                                <div class="flex justify-between items-center text-[10px] border-t border-emerald-500/10 pt-2">
                                    <span class="text-slate-400 font-semibold">Metode Setoran:</span>
                                    <span class="font-bold text-slate-650 dark:text-slate-350">
                                        {{ $payMethod === 'CASH' ? '💵 Tunai (Cash)' : ($payMethod === 'TRANSFER' ? '🏦 Transfer Bank' : '📱 E-Wallet') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Footer buttons --}}
                        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                            <button type="button" wire:click="cancelPayment"
                                class="px-5 py-2.5 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all">
                                Batalkan
                            </button>
                            <button type="button" wire:click="recordPayment"
                                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-850 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Ya, Konfirmasi & Simpan
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @endif


        <!-- TAB 4: DISPENSASI & POTONGAN -->
        @if ($activeTab === 'exceptions')
            <div class="space-y-8">
                <!-- Exception List -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 rounded-3xl shadow-sm overflow-hidden space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider block font-serif-display">Kelompok Dispensasi & Keringanan Aktif</h3>
                            <p class="text-[11px] text-slate-400">Semua kelompok potongan iuran yang telah terdaftar untuk santri.</p>
                        </div>
                        <a href="{{ route('keuangan.billing.exceptions.create') }}" wire:navigate class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-1.5 self-start sm:self-auto">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            Tambah Dispensasi Baru
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">
                                    <th class="py-3 px-4">Nama/Alasan Potongan</th>
                                    <th class="py-3 px-4">Nama Tagihan</th>
                                    <th class="py-3 px-4 text-center">Jumlah Santri</th>
                                    <th class="py-3 px-4">Tipe Keringanan</th>
                                    <th class="py-3 px-4 text-right">Nominal / Potongan</th>
                                    <th class="py-3 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                @php
                                    $groupedExceptions = $exceptions->groupBy(function($exc) {
                                        return $exc->billing_config_id . '-' . $exc->exception_type . '-' . $exc->amount . '-' . $exc->notes;
                                    });
                                @endphp
                                @forelse($groupedExceptions as $groupKey => $group)
                                    @php
                                        $first = $group->first();
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                        <td class="py-3 px-4 font-bold text-slate-800 dark:text-slate-200">{{ $first->notes ?: 'Tanpa Alasan/Keterangan' }}</td>
                                        <td class="py-3 px-4 font-semibold text-slate-600 dark:text-slate-400">{{ $first->configuration->label ?? 'Iuran Terhapus' }}</td>
                                        <td class="py-3 px-4 text-center">
                                            <button type="button" wire:click="showGroupMembers('{{ $first->billing_config_id }}', '{{ $first->exception_type }}', {{ $first->amount }}, '{{ addslashes($first->notes) }}')" 
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-500/10 hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 rounded-xl font-bold transition-all">
                                                👤 {{ count($group) }} Santri
                                            </button>
                                        </td>
                                        <td class="py-3 px-4 uppercase text-[9px] font-bold text-slate-500">
                                            @if($first->exception_type === 'discount')
                                                Potongan
                                            @elseif($first->exception_type === 'waived')
                                                Bebas Biaya
                                            @else
                                                Tarif Khusus
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-right font-semibold text-slate-800 dark:text-slate-200">
                                            @if($first->exception_type === 'waived')
                                                Rp 0 (Gratis)
                                            @else
                                                Rp {{ number_format($first->amount, 0, ',', '.') }}
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex items-center justify-center gap-3">
                                                <a href="{{ route('keuangan.billing.exceptions.edit', [
                                                    'config_id' => $first->billing_config_id,
                                                    'type' => $first->exception_type,
                                                    'amount' => $first->amount,
                                                    'notes' => $first->notes
                                                ]) }}" wire:navigate class="text-xs font-bold text-blue-600 hover:text-blue-750 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                                    Edit
                                                </a>
                                                <span class="text-slate-350 dark:text-slate-700">|</span>
                                                <button wire:click="deleteGroup('{{ $first->billing_config_id }}', '{{ $first->exception_type }}', {{ $first->amount }}, '{{ addslashes($first->notes) }}')" 
                                                    wire:confirm="Apakah Anda yakin ingin menghapus kelompok dispensasi ini? Seluruh santri anggota kelompok ini akan dikembalikan ke tarif normal."
                                                    class="text-xs font-bold text-rose-600 hover:text-rose-700 transition-colors">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-slate-400 font-medium">Belum ada kelompok dispensasi yang didaftarkan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Members Detail Modal -->
            @if($showMembersModal)
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="modal-title">
                    {{-- Backdrop --}}
                    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" wire:click="closeMembersModal"></div>

                    {{-- Modal Box --}}
                    <div class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                            <div>
                                <p class="text-[9px] font-extrabold text-emerald-500 uppercase tracking-widest mb-0.5">Detail Kelompok Dispensasi</p>
                                <h3 id="modal-title" class="text-sm font-bold text-slate-900 dark:text-white">{{ $modalGroupName }}</h3>
                            </div>
                            <button type="button" wire:click="closeMembersModal"
                                class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all font-bold text-lg leading-none">
                                &times;
                            </button>
                        </div>

                        {{-- Body --}}
                        <div class="max-h-80 overflow-y-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead class="sticky top-0 bg-slate-50 dark:bg-slate-950 z-10">
                                    <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">
                                        <th class="py-2.5 px-4 w-10">No</th>
                                        <th class="py-2.5 px-4">Nama Santri</th>
                                        <th class="py-2.5 px-4 text-center">Gender</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                    @foreach($modalMembers as $idx => $m)
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                            <td class="py-2.5 px-4 font-semibold text-slate-400">{{ $idx + 1 }}</td>
                                            <td class="py-2.5 px-4 font-bold text-slate-800 dark:text-slate-200">{{ $m['name'] }}</td>
                                            <td class="py-2.5 px-4 text-center">
                                                <span class="inline-block px-2 py-0.5 rounded-lg text-[9px] font-extrabold uppercase tracking-wider
                                                    {{ $m['gender'] === 'L' ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400' : 'bg-pink-500/10 text-pink-600 dark:text-pink-400' }}">
                                                    {{ $m['gender'] === 'L' ? 'Putra' : 'Putri' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Footer --}}
                        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <span class="text-[10px] text-slate-400 font-semibold">Total: {{ count($modalMembers) }} santri</span>
                            <button type="button" wire:click="closeMembersModal"
                                class="px-5 py-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold transition-all">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- TAB 5: CICILAN EVENT -->
        @if ($activeTab === 'installments')
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Add Installment Form -->
                <div class="lg:col-span-4 bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 rounded-3xl shadow-sm space-y-6 h-fit">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider block font-serif-display">Buat Skema Cicilan</h3>
                        <p class="text-[10px] text-slate-400">Pecah tagihan besar menjadi beberapa termin pembayaran dengan jatuh tempo berbeda.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Cari Santri</label>
                            <input type="text" wire:model.live="instSearchQuery" placeholder="Cari santri..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-emerald-500">

                            @if(!empty($instSearchResults))
                                <div class="bg-white dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 rounded-2xl overflow-hidden divide-y divide-slate-100 dark:divide-slate-800/50 mt-2">
                                    @foreach($instSearchResults as $s)
                                        <button wire:click="selectInstSantri('{{ $s->id }}')" class="w-full text-left px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-900 transition-all flex items-center justify-between text-xs">
                                            <div>
                                                <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ $s->name }}</span>
                                                <span class="text-[10px] text-slate-400 uppercase tracking-wider">{{ $s->gender === 'L' ? 'PUTRA' : 'PUTRI' }}</span>
                                            </div>
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Pilih Jenis Iuran / Event</label>
                            <select wire:model.live="instConfigId" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-emerald-500">
                                <option value="">-- Pilih Konfigurasi --</option>
                                @foreach($installmentConfigs as $c)
                                    <option value="{{ $c->id }}">{{ $c->label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Total Biaya (Rp)</label>
                            <input type="number" wire:model="instTotalAmount" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-emerald-500 text-right font-bold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Jumlah Termin Cicilan</label>
                            <select wire:model="instTermCount" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2 text-xs focus:ring-emerald-500 font-bold">
                                <option value="2">2 Kali Cicilan</option>
                                <option value="3">3 Kali Cicilan</option>
                                <option value="4">4 Kali Cicilan</option>
                                <option value="5">5 Kali Cicilan</option>
                                <option value="6">6 Kali Cicilan</option>
                                <option value="12">12 Kali Cicilan</option>
                            </select>
                        </div>

                        <button wire:click="generateInstallments" class="w-full px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                            Generate Cicilan
                        </button>
                    </div>
                </div>

                <!-- Active Installment Plans Dashboard -->
                <div class="lg:col-span-8 bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 rounded-3xl shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider block font-serif-display">Daftar Skema Cicilan Aktif</h3>
                            <p class="text-[11px] text-slate-400">Pantau progres cicilan event dan pembayaran bertermin santri.</p>
                        </div>
                        <div class="w-full sm:w-64">
                            <input type="text" wire:model.live="instFilterSearch" placeholder="Cari nama santri..." 
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-1.5 text-xs focus:ring-emerald-500">
                        </div>
                    </div>

                    @if($installmentPlans->isEmpty())
                        <div class="py-16 text-center text-slate-400 text-xs font-semibold">
                            Tidak ada skema cicilan aktif yang ditemukan.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">
                                        <th class="py-2.5 px-3">Santri</th>
                                        <th class="py-2.5 px-3">Event / Iuran</th>
                                        <th class="py-2.5 px-3 text-center">Skema</th>
                                        <th class="py-2.5 px-3 text-right">Total</th>
                                        <th class="py-2.5 px-3 text-right">Terbayar</th>
                                        <th class="py-2.5 px-3 text-center">Status</th>
                                        <th class="py-2.5 px-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                    @foreach($installmentPlans as $plan)
                                        @php
                                            $dormName = $plan->person->roomAssignments->firstWhere('is_active', true)?->room?->dormitory?->name ?? '—';
                                            $paidCount = $plan->installments->where('status', 'paid')->count();
                                            $totalTerms = $plan->installments->count();
                                        @endphp
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                            <td class="py-3 px-3">
                                                <strong class="text-slate-800 dark:text-slate-200 block">{{ $plan->person->name }}</strong>
                                                <span class="text-[9px] text-slate-400 block truncate">Komplek: {{ $dormName }}</span>
                                            </td>
                                            <td class="py-3 px-3 font-semibold text-slate-600 dark:text-slate-400">
                                                {{ $plan->config->label ?? 'Iuran Terhapus' }}
                                            </td>
                                            <td class="py-3 px-3 text-center">
                                                <span class="inline-block px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-350 text-[10px] font-bold rounded-lg">
                                                    {{ $totalTerms }}x ({{ $paidCount }} Lunas)
                                                </span>
                                            </td>
                                            <td class="py-3 px-3 text-right font-bold text-slate-800 dark:text-slate-200">
                                                Rp {{ number_format($plan->amount, 0, ',', '.') }}
                                            </td>
                                            <td class="py-3 px-3 text-right font-bold text-emerald-650 dark:text-emerald-400">
                                                Rp {{ number_format($plan->amount_paid, 0, ',', '.') }}
                                            </td>
                                            <td class="py-3 px-3 text-center">
                                                <span class="inline-block px-2 py-0.5 rounded-lg text-[9px] font-extrabold uppercase tracking-wider
                                                    @if($plan->status === 'paid') bg-emerald-500/10 text-emerald-600 dark:text-emerald-400
                                                    @elseif($plan->status === 'partial') bg-amber-500/10 text-amber-600 dark:text-amber-400
                                                    @else bg-slate-500/10 text-slate-500 dark:text-slate-400 @endif">
                                                    @if($plan->status === 'paid') Lunas
                                                    @elseif($plan->status === 'partial') Sebagian
                                                    @else Belum Bayar @endif
                                                </span>
                                            </td>
                                            <td class="py-3 px-3 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button type="button" wire:click="showInstallmentDetails('{{ $plan->id }}')" 
                                                        class="px-2.5 py-1 bg-blue-500/10 hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 text-[10px] font-extrabold rounded-lg transition-all">
                                                        Detail
                                                    </button>
                                                    <button type="button" wire:click="cancelInstallmentPlan('{{ $plan->id }}')" 
                                                        wire:confirm="Apakah Anda yakin ingin membatalkan skema cicilan ini? Seluruh tagihan termin terkait yang belum dibayar akan dihapus!"
                                                        class="px-2.5 py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-450 text-[10px] font-extrabold rounded-lg transition-all">
                                                        Batal
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Installment Details Modal -->
                @if($showInstallmentDetailsModal && $selectedParentBill)
                    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="inst-modal-title">
                        {{-- Backdrop --}}
                        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" wire:click="closeInstallmentDetailsModal"></div>

                        {{-- Modal Box --}}
                        <div class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                            {{-- Header --}}
                            <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                                <div>
                                    <p class="text-[9px] font-extrabold text-emerald-500 uppercase tracking-widest mb-0.5">Detail Rincian Cicilan</p>
                                    <h3 id="inst-modal-title" class="text-sm font-bold text-slate-900 dark:text-white">{{ $selectedParentBill->person->name }}</h3>
                                </div>
                                <button type="button" wire:click="closeInstallmentDetailsModal"
                                    class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all font-bold text-lg leading-none">
                                    &times;
                                </button>
                            </div>

                            {{-- Summary Card inside Modal --}}
                            <div class="p-6 bg-slate-50 dark:bg-slate-950 border-b border-slate-100 dark:border-slate-800 grid grid-cols-3 gap-4 text-center text-xs">
                                <div>
                                    <span class="text-[9px] text-slate-400 font-extrabold uppercase tracking-wider block mb-1">Total Biaya</span>
                                    <strong class="text-slate-800 dark:text-slate-200 font-bold">Rp {{ number_format($selectedParentBill->amount, 0, ',', '.') }}</strong>
                                </div>
                                <div>
                                    <span class="text-[9px] text-emerald-500 font-extrabold uppercase tracking-wider block mb-1">Terbayar</span>
                                    <strong class="text-emerald-600 dark:text-emerald-400 font-bold">Rp {{ number_format($selectedParentBill->amount_paid, 0, ',', '.') }}</strong>
                                </div>
                                <div>
                                    <span class="text-[9px] text-rose-500 font-extrabold uppercase tracking-wider block mb-1">Sisa</span>
                                    <strong class="text-rose-600 dark:text-rose-450 font-bold">Rp {{ number_format($selectedParentBill->amount - $selectedParentBill->amount_paid, 0, ',', '.') }}</strong>
                                </div>
                            </div>

                            {{-- Body Table --}}
                            <div class="max-h-80 overflow-y-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead class="sticky top-0 bg-slate-100 dark:bg-slate-950 z-10">
                                        <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">
                                            <th class="py-2.5 px-4 w-12 text-center">No</th>
                                            <th class="py-2.5 px-4">Termin / Jatuh Tempo</th>
                                            <th class="py-2.5 px-4 text-right">Nominal</th>
                                            <th class="py-2.5 px-4 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                        @foreach($installmentChildBills as $idx => $child)
                                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                                <td class="py-2.5 px-4 text-center font-semibold text-slate-400">{{ $idx + 1 }}</td>
                                                <td class="py-2.5 px-4">
                                                    <strong class="text-slate-800 dark:text-slate-200 block">{{ $child->notes }}</strong>
                                                    <span class="text-[9px] text-slate-400 block mt-0.5">Jatuh Tempo: {{ $child->due_date ? $child->due_date->format('d-m-Y') : '—' }}</span>
                                                </td>
                                                <td class="py-2.5 px-4 text-right font-bold text-slate-800 dark:text-slate-200">
                                                    Rp {{ number_format($child->amount, 0, ',', '.') }}
                                                </td>
                                                <td class="py-2.5 px-4 text-center">
                                                    <span class="inline-block px-2 py-0.5 rounded-lg text-[9px] font-extrabold uppercase tracking-wider
                                                        @if($child->status === 'paid') bg-emerald-500/10 text-emerald-600 dark:text-emerald-400
                                                        @elseif($child->status === 'partial') bg-amber-500/10 text-amber-600 dark:text-amber-400
                                                        @else bg-slate-500/10 text-slate-500 dark:text-slate-400 @endif">
                                                        @if($child->status === 'paid') Lunas
                                                        @elseif($child->status === 'partial') Sebagian
                                                        @else Belum @endif
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Footer --}}
                            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <span class="text-[10px] text-slate-400 font-semibold">Iuran: {{ $selectedParentBill->config->label ?? '—' }}</span>
                                <button type="button" wire:click="closeInstallmentDetailsModal"
                                    class="px-5 py-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold transition-all">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- TAB 6: RIWAYAT SETORAN (LOG PEMBAYARAN KASIR) -->
        @if ($activeTab === 'payments_log')
            <div class="space-y-8 animate-fade-in">
                <!-- Filters & Stats Header Card -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 rounded-3xl shadow-xs space-y-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider block font-serif-display">Riwayat Setoran & Transaksi Kasir</h3>
                            <p class="text-[11px] text-slate-400">Jejak pembayaran iuran santri yang dicatat oleh kasir. Anda dapat melakukan pembatalan pencatatan/void pembayaran jika terjadi kesalahan.</p>
                        </div>
                    </div>

                    <!-- Filter Controls -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Search Box -->
                        <div>
                            <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Cari Santri / Jenis Iuran</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>
                                <input type="text" wire:model.live.debounce.300ms="payLogSearch" placeholder="Cari nama santri, NIS, atau jenis iuran..." 
                                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-2xl pl-10 pr-4 py-2.5 text-xs font-bold focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                            </div>
                        </div>

                        <!-- Method Selector -->
                        <div>
                            <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Metode Setoran</label>
                            <select wire:model.live="payLogMethod" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-2xl px-4 py-2.5 text-xs font-bold focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                                <option value="">-- Semua Metode --</option>
                                <option value="CASH">💵 Tunai (Cash)</option>
                                <option value="TRANSFER">🏦 Transfer Bank</option>
                                <option value="EWALLET">📱 E-Wallet / Digital</option>
                            </select>
                        </div>

                        <!-- Date Selector -->
                        <div>
                            <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Tanggal Setor</label>
                            <input type="date" wire:model.live="payLogDate" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-2xl px-4 py-2.5 text-xs font-bold focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                        </div>
                    </div>
                </div>

                <!-- Payment Logs Table -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-3xl shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs min-w-[900px]">
                            <thead>
                                <tr class="bg-slate-50/80 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">
                                    <th class="py-4 px-4 w-32">Tanggal Setor</th>
                                    <th class="py-4 px-4">Nama Santri</th>
                                    <th class="py-4 px-4">Jenis Iuran</th>
                                    <th class="py-4 px-4 text-center">Periode</th>
                                    <th class="py-4 px-4 text-right">Jumlah Setor</th>
                                    <th class="py-4 px-4 text-center">Metode</th>
                                    <th class="py-4 px-4">Catatan</th>
                                    <th class="py-4 px-4">Petugas</th>
                                    <th class="py-4 px-4 text-center w-28">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                @forelse($paymentsLog as $pay)
                                    @php
                                        $bill = $pay->bill;
                                        $santri = $bill?->person;
                                        $config = $bill?->config;
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                        <td class="py-4 px-4 font-medium text-slate-500">
                                            {{ $pay->payment_date ? $pay->payment_date->translatedFormat('d M Y') : '—' }}
                                            <span class="text-[9px] text-slate-400 block mt-0.5">{{ $pay->created_at->format('H:i') }} WIB</span>
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($santri)
                                                <strong class="text-slate-800 dark:text-slate-200 block font-bold">{{ $santri->name }}</strong>
                                                <span class="text-[9px] text-slate-400 block mt-0.5">
                                                    NIS: {{ $santri->nis ?? '—' }} &nbsp;|&nbsp;
                                                    {{ $santri->gender === 'L' ? '👦 L' : '👧 P' }}
                                                </span>
                                            @else
                                                <span class="text-slate-400 italic">Data Terhapus</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 font-semibold text-slate-700 dark:text-slate-300">
                                            {{ $config?->label ?? ($bill?->bill_type ? str_replace('_', ' ', $bill->bill_type) : '—') }}
                                        </td>
                                        <td class="py-4 px-4 text-center font-bold text-slate-600 dark:text-slate-350">
                                            @if($bill)
                                                @if($config && $config->interval === 'semester')
                                                    Sem {{ $bill->period_month }} / {{ $bill->period_year }}
                                                @elseif($config && in_array($config->interval, ['once', 'insidental', 'event', 'sekali']))
                                                    Event / {{ $bill->period_year }}
                                                @else
                                                    @php
                                                        $months = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agt',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
                                                        $mName = $months[$bill->period_month] ?? $bill->period_month;
                                                    @endphp
                                                    {{ $mName }} {{ $bill->period_year }}
                                                @endif
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-right font-extrabold text-emerald-600 dark:text-emerald-400">
                                            Rp {{ number_format($pay->amount_paid, 0, ',', '.') }}
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <span class="inline-block px-2.5 py-1 rounded-lg text-[9px] font-extrabold uppercase tracking-wider
                                                @if(strtoupper($pay->payment_method) === 'CASH') bg-teal-500/10 text-teal-600 dark:text-teal-400
                                                @elseif(strtoupper($pay->payment_method) === 'TRANSFER') bg-blue-500/10 text-blue-600 dark:text-blue-400
                                                @else bg-purple-500/10 text-purple-600 dark:text-purple-400 @endif">
                                                @if(strtoupper($pay->payment_method) === 'CASH') 💵 Tunai
                                                @elseif(strtoupper($pay->payment_method) === 'TRANSFER') 🏦 Transfer
                                                @else 📱 E-Wallet @endif
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-slate-505 max-w-[150px] truncate" title="{{ $pay->notes }}">
                                            {{ $pay->notes ?: '—' }}
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md text-[10px] text-slate-700 dark:text-slate-300 font-bold whitespace-nowrap">
                                                👤 {{ $pay->logger?->name ?? 'Sistem' }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <button wire:click="deletePayment('{{ $pay->id }}')" 
                                                wire:confirm="Apakah Anda yakin ingin membatalkan/menghapus pencatatan pembayaran sebesar Rp {{ number_format($pay->amount_paid, 0, ',', '.') }} ini? Nominal pembayaran akan ditarik kembali dan tagihan santri akan dikembalikan seperti semula."
                                                class="px-2.5 py-1.5 bg-rose-500/10 hover:bg-rose-500 text-rose-600 hover:text-white rounded-xl text-[10px] font-bold transition-all whitespace-nowrap">
                                                🗑️ Batalkan / Void
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="py-16 text-center text-slate-400 font-semibold">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                                <span>Tidak ada riwayat setoran yang cocok dengan pencarian Anda.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Footer for Payments Log -->
                    @if($paymentsLog->hasPages())
                        <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/50 dark:bg-slate-950/20">
                            <div class="text-[11px] font-semibold text-slate-400">
                                Menampilkan <span class="font-bold text-slate-700 dark:text-slate-300">{{ $paymentsLog->firstItem() ?? 0 }}</span> s.d. <span class="font-bold text-slate-700 dark:text-slate-300">{{ $paymentsLog->lastItem() ?? 0 }}</span> dari <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $paymentsLog->total() }}</span> pembayaran dicatat
                            </div>
                            <div>
                                {{ $paymentsLog->links(data: ['scrollTo' => false]) }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- TAB: TARIF SANTRI BARU & KITAB --}}
        @if ($activeTab === 'registration_rates')
            <div class="space-y-6">
                {{-- Header Actions --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-6 rounded-3xl shadow-sm">
                    <div>
                        <h2 class="font-extrabold text-lg text-slate-900 dark:text-slate-100">Pengaturan Tarif Pendaftaran Santri Baru &amp; Kitab</h2>
                        <p class="text-xs text-slate-400">Kelola komponen rincian harga pendaftaran, seragam, almari, dan paket kitab per kelas</p>
                    </div>
                    <div>
                        <button type="button" wire:click="openItemModal" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-xl shadow-md transition-all text-xs flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>+ Tambah Item Tarif Baru</span>
                        </button>
                    </div>
                </div>

                {{-- Sub Tabs Navigation --}}
                <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-2">
                    <button type="button" wire:click="$set('activeRegSubTab', 'items')"
                        class="px-5 py-2.5 font-extrabold text-xs rounded-xl transition-all flex items-center gap-2 {{ $activeRegSubTab === 'items' ? 'bg-emerald-600 text-white shadow-md' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Item Tarif Registrasi (Pendaftaran, Seragam, Almari)</span>
                    </button>
                    <button type="button" wire:click="$set('activeRegSubTab', 'kitab')"
                        class="px-5 py-2.5 font-extrabold text-xs rounded-xl transition-all flex items-center gap-2 {{ $activeRegSubTab === 'kitab' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>Tarif Paket Kitab Per Kelas Madrasah</span>
                    </button>
                </div>

                {{-- SUB-TAB 1: ITEM TARIF REGISTRASI --}}
                @if($activeRegSubTab === 'items')
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                        <div class="overflow-x-auto border border-slate-200/60 dark:border-slate-800 rounded-2xl">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 font-bold uppercase text-[10px] border-b border-slate-200 dark:border-slate-700">
                                    <tr>
                                        <th class="p-3">Nama Item Tarif</th>
                                        <th class="p-3">Kategori</th>
                                        <th class="p-3">Target Gender</th>
                                        <th class="p-3">Target Keberadaan</th>
                                        <th class="p-3 text-right">Nominal Tarif (Rp)</th>
                                        <th class="p-3 text-center">Status</th>
                                        <th class="p-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                                    @forelse($registrationItems as $item)
                                        @php
                                            $filters = $item->target_filters ?? [];
                                            $cat = $filters['category'] ?? 'dasar';
                                            $gen = $filters['gender'] ?? 'ALL';
                                            $res = $filters['residence'] ?? 'ALL';
                                        @endphp
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                                            <td class="p-3">
                                                <div class="font-extrabold text-slate-900 dark:text-slate-100">{{ $item->label }}</div>
                                            </td>
                                            <td class="p-3">
                                                <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase rounded-full bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                    {{ $cat }}
                                                </span>
                                            </td>
                                            <td class="p-3 font-semibold text-slate-700 dark:text-slate-300">
                                                @if($gen === 'L')
                                                    <span class="text-blue-600 font-bold">Putra (L)</span>
                                                @elseif($gen === 'P')
                                                    <span class="text-rose-600 font-bold">Putri (P)</span>
                                                @else
                                                    <span>Semua Gender</span>
                                                @endif
                                            </td>
                                            <td class="p-3 font-semibold text-slate-700 dark:text-slate-300">
                                                @if($res === 'mukim')
                                                    <span class="text-indigo-600 font-bold">Khusus Mukim</span>
                                                @elseif($res === 'laju')
                                                    <span class="text-amber-600 font-bold">Khusus Laju</span>
                                                @else
                                                    <span>Mukim &amp; Laju</span>
                                                @endif
                                            </td>
                                            <td class="p-3 text-right font-mono font-black text-emerald-600 dark:text-emerald-400 text-sm">
                                                Rp {{ number_format($item->amount, 0, ',', '.') }}
                                            </td>
                                            <td class="p-3 text-center">
                                                <button type="button" wire:click="toggleItemActive('{{ $item->id }}')"
                                                    class="px-2.5 py-1 text-[10px] font-extrabold rounded-full transition-all {{ $item->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-500' }}">
                                                    {{ $item->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                                                </button>
                                            </td>
                                            <td class="p-3 text-center">
                                                <button type="button" wire:click="openItemModal('{{ $item->id }}')"
                                                    class="px-3 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-lg text-xs transition-colors">
                                                    Edit
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="p-8 text-center text-slate-400">Belum ada item tarif pendaftaran yang dikonfigurasi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- SUB-TAB 2: TARIF KITAB PER KELAS --}}
                @if($activeRegSubTab === 'kitab')
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                        {{-- Filter Bar --}}
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-200/60 dark:border-slate-700/60">
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-xs text-slate-700 dark:text-slate-300">Filter Jenjang:</span>
                                <select wire:model.live="kitabJenjangFilter" class="px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100">
                                    <option value="">Semua Jenjang (Awaliyah, Wustho, Ulya)</option>
                                    <option value="awaliyah">Awaliyah / Ula</option>
                                    <option value="wustho">Wustho</option>
                                    <option value="ulya">Ulya</option>
                                </select>
                            </div>

                            <div class="relative w-full md:w-64">
                                <input type="text" wire:model.live="kitabSearch" placeholder="Cari nama kelas..." class="w-full pl-9 pr-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-100">
                                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        </div>

                        {{-- Table Display --}}
                        <div class="overflow-x-auto border border-slate-200/60 dark:border-slate-800 rounded-2xl">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 font-bold uppercase text-[10px] border-b border-slate-200 dark:border-slate-700">
                                    <tr>
                                        <th class="p-3.5">Jenjang</th>
                                        <th class="p-3.5">Nama Kelas Madrasah</th>
                                        <th class="p-3.5 text-right">Nominal Tarif Paket Kitab (Rp)</th>
                                        <th class="p-3.5 text-center">Aksi Simpan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                                    @php
                                        $filteredKitab = array_filter($kitabPrices, function($item) {
                                            $filter  = strtolower($this->kitabJenjangFilter);
                                            $jenjang = strtolower($item['jenjang']);
                                            $name    = strtolower($item['kelas_name']);

                                            $matchJenjang = true;
                                            if (!empty($filter)) {
                                                if ($filter === 'awaliyah' || $filter === 'ula') {
                                                    $matchJenjang = ($jenjang === 'ula' || $jenjang === 'awaliyah' || str_contains($name, 'awaliyah') || str_contains($name, 'ula'));
                                                } else {
                                                    $matchJenjang = ($jenjang === $filter || str_contains($name, $filter));
                                                }
                                            }

                                            $matchSearch = empty($this->kitabSearch) || str_contains($name, strtolower($this->kitabSearch));

                                            return $matchJenjang && $matchSearch;
                                        });
                                    @endphp

                                    @forelse($filteredKitab as $kelasId => $kData)
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                                            <td class="p-3.5">
                                                <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                                    {{ ($kData['jenjang'] === 'ULA' || $kData['jenjang'] === 'AWALIYAH') ? 'AWALIYAH' : $kData['jenjang'] }}
                                                </span>
                                            </td>
                                            <td class="p-3.5 font-extrabold text-slate-900 dark:text-slate-100 text-sm">
                                                {{ $kData['kelas_name'] }}
                                            </td>
                                            <td class="p-3.5 text-right">
                                                <div class="inline-flex items-center gap-2 justify-end">
                                                    <span class="text-slate-400 font-bold text-xs">Rp</span>
                                                    <input type="number" wire:model="kitabPrices.{{ $kelasId }}.amount"
                                                        class="w-36 px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono font-black text-emerald-600 dark:text-emerald-400 text-right">
                                                </div>
                                            </td>
                                            <td class="p-3.5 text-center">
                                                <button type="button" wire:click="saveKitabPrice('{{ $kelasId }}')"
                                                    class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-xl text-xs shadow transition-all">
                                                    Simpan Tarif
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-8 text-center text-slate-400">Tidak ada data kelas madrasah yang sesuai filter pencarian.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            {{-- MODAL EDIT / TAMBAH ITEM TARIF --}}
            @if($showItemModal)
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md">
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                            <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">
                                {{ $editingItemId ? 'Edit Item Tarif Registrasi' : 'Tambah Item Tarif Baru' }}
                            </h3>
                            <button type="button" wire:click="$set('showItemModal', false)" class="text-slate-400 hover:text-slate-600">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Nama / Label Item Tarif</label>
                                <input type="text" wire:model="itemLabel" placeholder="Contoh: Seragam Khusus Putri" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Nominal Harga (Rp)</label>
                                <input type="number" wire:model="itemAmount" placeholder="150000" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-mono text-slate-800 dark:text-slate-100">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Target Gender</label>
                                    <select wire:model="itemGender" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                                        <option value="ALL">Semua Gender</option>
                                        <option value="L">Putra (L) Saja</option>
                                        <option value="P">Putri (P) Saja</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Target Keberadaan</label>
                                    <select wire:model="itemResidence" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                                        <option value="ALL">Mukim &amp; Laju</option>
                                        <option value="mukim">Mukim Saja</option>
                                        <option value="laju">Laju Saja</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Kategori Item</label>
                                <select wire:model="itemCategory" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                                    <option value="dasar">Dasar Pendaftaran</option>
                                    <option value="asrama">Fasilitas Asrama</option>
                                    <option value="seragam">Seragam</option>
                                    <option value="konsumsi">Konsumsi / Majek</option>
                                    <option value="kitab">Kitab</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" wire:click="$set('showItemModal', false)" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
                            <button type="button" wire:click="saveItem" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow">Simpan Item Tarif</button>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
