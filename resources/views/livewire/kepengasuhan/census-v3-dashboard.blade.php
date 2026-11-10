<div class="py-6 px-4 sm:px-6 lg:px-8">
    <!-- Success & Error Alert -->
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-lg bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 text-[10px] font-black uppercase tracking-wider">
                    Executive Command Center
                </span>
                @if($userGenderScope)
                    <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-bold">
                        Lingkup {{ $userGenderScope === 'L' ? 'Putra' : 'Putri' }}
                    </span>
                @endif
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight mt-1 flex items-center gap-2">
                <span>🛡️</span>
                <span>Pusat Kendali Sensus &amp; Asrama</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Pusat monitoring pimpinan: hunian kamar, kesiapan fasilitas, dan audit pemutakhiran biodata profil santri.
            </p>
        </div>
        @can('manage-sensus-v3')
            <div class="flex items-center gap-2">
                <a href="{{ route('sensus.campaigns.create', ['type' => 'formal']) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-xl shadow-md shadow-blue-600/20 hover:-translate-y-0.5 transition-all text-xs">
                    <span>📋</span>
                    <span>+ Sensus Biodata Baru</span>
                </a>
            </div>
        @endcan
    </div>

    <!-- Quick Launchpad: Pintu Gerbang ke Alat Operasional Live Realtime -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <!-- Launchpad 1: Papan Mutasi Santri Live -->
        <a href="{{ route('kepengasuhan.papan-mutasi') }}" class="group relative overflow-hidden bg-gradient-to-br from-blue-700 via-indigo-800 to-blue-950 text-white rounded-3xl p-5 shadow-lg border border-blue-500/30 hover:shadow-xl hover:scale-[1.01] transition-all">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <span class="px-2.5 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-black tracking-wider uppercase">
                        Operasional Kamar Realtime
                    </span>
                    <h3 class="text-xl font-black mt-2 tracking-tight flex items-center gap-2">
                        <span>🔄</span>
                        <span>Papan Mutasi Santri Live</span>
                        <span class="text-sm opacity-60 group-hover:translate-x-1 transition-transform">➔</span>
                    </h3>
                    <p class="text-xs text-blue-100 mt-1 max-w-sm">
                        Seret santri untuk pindah kamar, komplek, laju, atau boyong detik ini juga secara realtime.
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-2xl shrink-0 group-hover:rotate-12 transition-transform">
                    🚪
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-white/15 flex items-center gap-3 text-xs font-bold flex-wrap">
                <span class="px-2.5 py-0.5 rounded-lg bg-emerald-500/30 border border-emerald-400/30 text-emerald-200">
                    🟢 {{ $executiveStats['total_vacant_beds'] }} Kasur Kosong
                </span>
                @if($executiveStats['transit_mukim_count'] > 0)
                    <span class="px-2.5 py-0.5 rounded-lg bg-amber-500/30 border border-amber-400/30 text-amber-200">
                        ⚠️ {{ $executiveStats['transit_mukim_count'] }} Santri Transit
                    </span>
                @else
                    <span class="text-[11px] text-blue-200 font-semibold">Semua Mukim Terkamar</span>
                @endif
            </div>
        </a>

        <!-- Launchpad 2: Papan Denah Fasilitas & Lemari Live -->
        <a href="{{ route('kepengasuhan.papan-fasilitas') }}" class="group relative overflow-hidden bg-gradient-to-br from-indigo-700 via-purple-800 to-slate-900 text-white rounded-3xl p-5 shadow-lg border border-purple-500/30 hover:shadow-xl hover:scale-[1.01] transition-all">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <span class="px-2.5 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-black tracking-wider uppercase">
                        Inventaris &amp; Perabot Kamar
                    </span>
                    <h3 class="text-xl font-black mt-2 tracking-tight flex items-center gap-2">
                        <span>🗄️</span>
                        <span>Papan Denah Fasilitas Live</span>
                        <span class="text-sm opacity-60 group-hover:translate-x-1 transition-transform">➔</span>
                    </h3>
                    <p class="text-xs text-purple-100 mt-1 max-w-sm">
                        Atur pintu lemari, rak kitab, sekat visual per kamar, dan catat kondisi fisik perabot.
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-2xl shrink-0 group-hover:rotate-12 transition-transform">
                    ✨
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-white/15 flex items-center gap-3 text-xs font-bold flex-wrap">
                <span class="px-2.5 py-0.5 rounded-lg bg-white/15 text-purple-100">
                    📦 {{ $executiveStats['occupied_lockers'] }}/{{ $executiveStats['total_lockers'] }} Loker Terisi
                </span>
                @if($executiveStats['damaged_lockers'] > 0)
                    <span class="px-2.5 py-0.5 rounded-lg bg-rose-500/30 border border-rose-400/30 text-rose-200">
                        🛠️ {{ $executiveStats['damaged_lockers'] }} Perabot Rusak
                    </span>
                @else
                    <span class="text-[11px] text-emerald-200 font-semibold">Semua Perabot Baik 🟢</span>
                @endif
            </div>
        </a>
    </div>

    <!-- Executive KPI Metric Cards (3 Kartu Ringkasan Kesehatan Pesantren) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Card 1: Hunian Kamar & Okupansi Kasur -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Okupansi Kasur</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-black {{ $executiveStats['occupancy_rate'] >= 90 ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' }}">
                    {{ $executiveStats['occupancy_rate'] }}% Terisi
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-800 dark:text-white">{{ $executiveStats['total_assigned_beds'] }}</span>
                    <span class="text-xs text-slate-400 font-bold">/ {{ $executiveStats['total_capacity'] }} Bed Total</span>
                </div>
                <!-- Progress Bar -->
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 mt-2 overflow-hidden">
                    <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ min(100, $executiveStats['occupancy_rate']) }}%"></div>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 pt-1 flex items-center justify-between border-t border-slate-100 dark:border-slate-800">
                <span>Sisa Kosong: <strong class="text-emerald-600 dark:text-emerald-400">{{ $executiveStats['total_vacant_beds'] }} Bed</strong></span>
                @if($executiveStats['transit_mukim_count'] > 0)
                    <span class="text-amber-600 font-bold">⚠️ {{ $executiveStats['transit_mukim_count'] }} di transit</span>
                @endif
            </p>
        </div>

        <!-- Card 2: Kesehatan Fasilitas & Lemari -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Kesiapan Fasilitas</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-black {{ $executiveStats['damaged_lockers'] > 0 ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' }}">
                    {{ $executiveStats['damaged_lockers'] > 0 ? $executiveStats['damaged_lockers'] . ' Butuh Perbaikan' : 'Semua Baik' }}
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-800 dark:text-white">{{ $executiveStats['occupied_lockers'] }}</span>
                    <span class="text-xs text-slate-400 font-bold">/ {{ $executiveStats['total_lockers'] }} Loker Aktif</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 mt-2 overflow-hidden">
                    @php
                        $lockerRate = $executiveStats['total_lockers'] > 0 ? round(($executiveStats['occupied_lockers'] / $executiveStats['total_lockers']) * 100) : 0;
                    @endphp
                    <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ min(100, $lockerRate) }}%"></div>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 pt-1 flex items-center justify-between border-t border-slate-100 dark:border-slate-800">
                <span>Tersedia: <strong>{{ max(0, $executiveStats['total_lockers'] - $executiveStats['occupied_lockers']) }} Loker Kosong</strong></span>
                <span class="{{ $executiveStats['damaged_lockers'] > 0 ? 'text-rose-500 font-bold' : 'text-slate-400' }}">
                    {{ $executiveStats['damaged_lockers'] }} Rusak
                </span>
            </p>
        </div>

        <!-- Card 3: Kelengkapan Biodata Santri -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Kelengkapan Profil Santri</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-black {{ $executiveStats['data_completeness_rate'] >= 80 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' }}">
                    {{ $executiveStats['data_completeness_rate'] }}% Lengkap
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-800 dark:text-white">{{ $executiveStats['total_active_santri'] }}</span>
                    <span class="text-xs text-slate-400 font-bold">Santri Aktif Terdata</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 mt-2 overflow-hidden">
                    <div class="bg-emerald-600 h-2 rounded-full transition-all" style="width: {{ min(100, $executiveStats['data_completeness_rate']) }}%"></div>
                </div>
            </div>
            <div class="text-[11px] text-slate-500 dark:text-slate-400 pt-1 flex items-center justify-between border-t border-slate-100 dark:border-slate-800">
                <span title="Santri belum ada NIK">{{ $executiveStats['missing_nik_count'] }} Belum NIK</span>
                <span class="text-slate-300">•</span>
                <span title="Santri belum ada kontak">{{ $executiveStats['missing_phone_count'] }} Belum Kontak</span>
                <span class="text-slate-300">•</span>
                <a href="{{ route('sensus.campaigns.create', ['type' => 'formal']) }}" class="text-blue-600 dark:text-blue-400 font-bold hover:underline">
                    + Sensus ➔
                </a>
            </div>
        </div>
    </div>

    <!-- Header Seksi Kampanye Sensus Profil Santri -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-sm font-black text-slate-800 dark:text-white flex items-center gap-2">
                <span>📋</span>
                <span>Audit &amp; Pemutakhiran Biodata Santri</span>
            </h3>
            <p class="text-[11px] text-slate-400">
                Formulir sensus berkala untuk verifikasi kelengkapan profil, NIK, dan data wali santri per asrama.
            </p>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto flex-wrap sm:flex-nowrap">
            <!-- Search Input -->
            <div class="relative flex-1 sm:w-64">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari sensus..." class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 pl-9 pr-3 py-1.5 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
            </div>

            <!-- Status Filter Tabs -->
            <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl text-xs font-bold">
                <button type="button" wire:click="$set('statusFilter', 'active')" class="py-1 px-3 rounded-lg transition-all {{ $statusFilter === 'active' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-2xs font-black' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-300' }}">
                    Aktif ({{ $stats['active'] ?? 0 }})
                </button>
                <button type="button" wire:click="$set('statusFilter', 'draft')" class="py-1 px-3 rounded-lg transition-all {{ $statusFilter === 'draft' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-2xs font-black' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-300' }}">
                    Draft
                </button>
                <button type="button" wire:click="$set('statusFilter', 'closed')" class="py-1 px-3 rounded-lg transition-all {{ $statusFilter === 'closed' ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-2xs font-black' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-300' }}">
                    Arsip ({{ $stats['closed'] ?? 0 }})
                </button>
            </div>
        </div>
    </div>

    <!-- Campaigns List -->
    <div class="space-y-8">
        @forelse ($campaigns as $camp)
            @php
                $progress = $camp->getOverallProgress();
            @endphp
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <!-- Card Header -->
                <div class="p-6 sm:p-8 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="text-2xl font-bold text-slate-800 dark:text-white leading-tight">{{ $camp->name }}</h2>
                            @if ($camp->census_type === 'mutasi')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                                    🔄 Sensus Santri &amp; Mutasi
                                </span>
                            @elseif ($camp->census_type === 'fasilitas')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                    🏢 Sensus Fasilitas
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                    📋 Sensus Formal
                                </span>
                            @endif
                            @php
                                $campColor = $camp->getStatusColor();
                                $campBadgeClass = 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-350';
                                if ($campColor === 'blue') {
                                    $campBadgeClass = 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300';
                                } elseif ($campColor === 'yellow') {
                                    $campBadgeClass = 'bg-amber-100 text-amber-850 dark:bg-amber-950 dark:text-amber-300';
                                } elseif ($campColor === 'purple') {
                                    $campBadgeClass = 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300';
                                } elseif ($campColor === 'green') {
                                    $campBadgeClass = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
                                } elseif ($campColor === 'red') {
                                    $campBadgeClass = 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300';
                                }
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $campBadgeClass }}">
                                {{ $camp->getStatusLabel() }}
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-slate-400">
                            @if ($camp->template)
                                <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg> Template: <strong>{{ $camp->template->name }}</strong></span>
                            @else
                                <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg> Mode: <strong>{{ $camp->census_type === 'mutasi' ? 'Pergerakan & Hunian Kamar' : 'Inventaris Asrama' }}</strong></span>
                            @endif
                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Periode: <strong>{{ $camp->getMonthName() }} {{ $camp->year }}</strong></span>
                            <span class="flex items-center gap-1">⏱️ Alur: <strong>{{ $camp->getWorkflowLabel() }}</strong></span>
                        </div>
                        @if ($camp->description)
                            <p class="text-sm text-slate-500 dark:text-slate-400 pt-1">{{ $camp->description }}</p>
                        @endif

                        @if ($camp->status === 'cancelled')
                            <div class="mt-2.5 p-3 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-xs text-rose-700 dark:text-rose-300 flex items-start gap-2.5">
                                <span class="text-base leading-none">🚫</span>
                                <div class="space-y-0.5">
                                    <div class="font-bold">Kampanye Dibatalkan: <span class="font-normal">{{ $camp->cancellation_reason ?: 'Tidak ada keterangan alasan tertulis.' }}</span></div>
                                    @if ($camp->canceller || $camp->cancelled_at)
                                        <div class="text-[10px] text-rose-500 dark:text-rose-400">
                                            Dibatalkan {{ $camp->canceller ? 'oleh ' . $camp->canceller->name : '' }} {{ $camp->cancelled_at ? 'pada ' . $camp->cancelled_at->format('d/m/Y H:i') : '' }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Overall Progress bar -->
                    <div class="w-full md:w-64 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400">Penyelesaian Asrama</span>
                            <span class="font-bold text-slate-800 dark:text-white">{{ $progress['done'] }} / {{ $progress['total'] }} ({{ $progress['percentage'] }}%)</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full transition-all duration-300" style="width: {{ $progress['percentage'] }}%"></div>
                        </div>

                        <!-- Top-level Campaign actions -->
                        <div class="flex items-center justify-end gap-2 pt-2">
                            @if ($this->canManageCampaign($camp))
                                @if ($camp->status === 'draft')
                                    <button type="button" wire:click="publishCampaign('{{ $camp->id }}')" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-emerald-600/10 hover:shadow-emerald-500/20 transition-all">
                                        Terbitkan Sensus
                                    </button>
                                    <button type="button" onclick="confirm('Apakah Anda yakin ingin menghapus draft kampanye ini?') || event.stopImmediatePropagation()" wire:click="deleteCampaign('{{ $camp->id }}')" class="px-3 py-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-550/10 rounded-xl text-xs transition-all">
                                        Hapus
                                    </button>
                                @elseif ($camp->status === 'collecting')
                                    <button type="button" wire:click="closeForReview('{{ $camp->id }}')" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-indigo-600/10 hover:shadow-indigo-500/20 transition-all">
                                        Hentikan &amp; Review
                                    </button>
                                    <button type="button" wire:click="openCancelModal('{{ $camp->id }}')" class="px-3 py-1.5 text-rose-600 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl text-xs font-bold transition-all border border-rose-200 dark:border-rose-800">
                                        Batalkan
                                    </button>
                                @elseif ($camp->status === 'review')
                                    <button type="button" onclick="confirm('Apakah Anda yakin ingin menyelesaikan kampanye sensus ini? Data profil santri yang disinkronkan akan diperbarui secara permanen.') || event.stopImmediatePropagation()" wire:click="finalizeCampaign('{{ $camp->id }}')" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-emerald-600/10 hover:shadow-emerald-500/20 transition-all">
                                        Finalisasi &amp; Selesai
                                    </button>
                                    <button type="button" wire:click="openCancelModal('{{ $camp->id }}')" class="px-3 py-1.5 text-rose-600 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl text-xs font-bold transition-all border border-rose-200 dark:border-rose-800">
                                        Batalkan
                                    </button>
                                @elseif ($camp->status === 'cancelled')
                                    <span class="px-3 py-1 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 text-xs font-bold rounded-xl border border-rose-200 dark:border-rose-900">
                                        🚫 Sensus Dibatalkan
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Dormitories List (Target) -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 font-bold border-b border-slate-100 dark:border-slate-800">
                                <th class="p-4 sm:px-6">Nama Asrama</th>
                                <th class="p-4">Penanggung Jawab</th>
                                <th class="p-4">Penyelesaian Isian</th>
                                <th class="p-4">Status Laporan</th>
                                <th class="p-4 sm:px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                            @forelse ($camp->dormitories as $cd)
                                <tr>
                                    <!-- Dorm Name -->
                                    <td class="p-4 sm:px-6">
                                        <div class="flex items-center gap-2">
                                            <span class="text-base">{{ $cd->dormitory->gender === 'L' ? 'Putra' : 'Putri' }}</span>
                                            <div>
                                                <span class="block font-bold text-slate-800 dark:text-white">{{ $cd->dormitory->name }}</span>
                                                <span class="block text-[10px] text-slate-400">{{ $cd->dormitory->gender === 'L' ? 'Komplek Putra' : 'Komplek Putri' }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Assigned User -->
                                    <td class="p-4">
                                        <span class="font-medium text-slate-700 dark:text-slate-300">{{ $cd->assignee_names }}</span>
                                    </td>

                                    <!-- Filled Progress -->
                                    <td class="p-4">
                                        <div class="flex items-center gap-3 min-w-[8rem]">
                                            <div class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                                <div class="h-full bg-emerald-500 rounded-full transition-all duration-300" style="width: {{ $cd->getProgressPercentage() }}%"></div>
                                            </div>
                                            <span class="font-bold text-slate-800 dark:text-white whitespace-nowrap">{{ $cd->progress_filled }} / {{ $cd->progress_total }}</span>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="p-4">
                                        @php
                                            $dormColor = $cd->getStatusColor();
                                            $dormBadgeClass = 'bg-slate-100 text-slate-850 dark:bg-slate-800 dark:text-slate-300';
                                            if ($dormColor === 'yellow') {
                                                $dormBadgeClass = 'bg-amber-100 text-amber-850 dark:bg-amber-950 dark:text-amber-300';
                                            } elseif ($dormColor === 'blue') {
                                                $dormBadgeClass = 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300';
                                            } elseif ($dormColor === 'green') {
                                                $dormBadgeClass = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
                                            } elseif ($dormColor === 'red') {
                                                $dormBadgeClass = 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300';
                                            }
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $dormBadgeClass }}">
                                            {{ $cd->getStatusLabel() }}
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="p-4 sm:px-6 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if ($camp->status === 'cancelled')
                                                <span class="text-[11px] text-slate-400 font-semibold italic">Terkunci (Dibatalkan)</span>
                                            @else
                                                <!-- Web input form button -->
                                                @if ($camp->status === 'collecting' && $camp->allow_direct_input)
                                                    @if ($this->canFillDormitory($cd))
                                                        <a href="{{ $this->getInputRoute($camp, $cd) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-lg hover:shadow-sm transition-all" title="Isi Lembar Sensus">
                                                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Isi Sensus
                                                        </a>
                                                    @endif
                                                @endif

                                                <!-- Review / Approval page for admin / secretary -->
                                                @if ($cd->status === 'submitted')
                                                    @if ($this->canReviewDormitory($cd))
                                                        <a href="{{ $this->getReviewRoute($camp, $cd) }}" class="px-3 py-1.5 bg-indigo-650 hover:bg-indigo-600 text-white font-bold rounded-lg shadow-sm hover:shadow transition-all" title="Review Hasil Sensus">
                                                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Review Laporan
                                                        </a>
                                                    @endif
                                                @endif

                                                <!-- Read-only view for closed or approved -->
                                                @if ($cd->status === 'approved' || $camp->status === 'closed')
                                                    @if ($this->canReviewDormitory($cd) || $this->canFillDormitory($cd))
                                                        <a href="{{ $this->getReviewRoute($camp, $cd) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 font-bold rounded-lg transition-all" title="Lihat Laporan">
                                                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> Lihat Hasil
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-400">Tidak ada target komplek asrama untuk sensus ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="py-16 text-center bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl">
                <div class="text-slate-300 dark:text-slate-700 mb-4 flex justify-center"><svg class="w-16 h-16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"/></svg></div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-300">Tidak ada kampanye sensus</h3>
                <p class="text-slate-400 mt-1 max-w-sm mx-auto text-sm">Tidak ada sensus dengan kriteria status ini. Klik "Mulai Kampanye Sensus" untuk membuat baru.</p>
            </div>
        @endforelse

        <!-- Pagination Links -->
        <div class="mt-6">
            {{ $campaigns->links() }}
        </div>
    </div>

    <!-- Modal Pembatalan Kampanye -->
    @if ($showCancelModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" wire:click="closeCancelModal"></div>

                <!-- Modal Panel -->
                <div class="relative bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-800 p-6 space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-rose-100 dark:bg-rose-950 flex items-center justify-center text-rose-600 dark:text-rose-400 text-2xl shrink-0">
                            ⚠️
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white" id="modal-title">
                                Batalkan Kampanye Sensus
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Apakah Anda yakin ingin membatalkan kampanye <strong class="text-slate-800 dark:text-slate-200 font-bold">{{ $campaignNameToCancel }}</strong>?
                            </p>
                        </div>
                    </div>

                    <div class="p-3.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/40 rounded-2xl text-xs text-amber-800 dark:text-amber-300 space-y-1.5">
                        <div class="font-bold flex items-center gap-1.5">
                            <span>📌</span> Konsekuensi Pembatalan:
                        </div>
                        <ul class="list-disc pl-5 space-y-0.5 text-[11px] leading-relaxed">
                            <li>Seluruh formulir pengisian di tingkat asrama akan otomatis terkunci.</li>
                            <li>Kampanye dipindahkan ke tab Arsip dengan status <strong>Dibatalkan</strong>.</li>
                            <li>Data isian yang sempat diinput tidak akan hilang, namun tidak disinkronkan ke profil santri.</li>
                        </ul>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Alasan Pembatalan <span class="text-slate-400 font-normal">(opsional)</span>:
                        </label>
                        <textarea wire:model="cancelReason" rows="3" placeholder="Contoh: Perubahan template dari pimpinan, salah memilih periode bulan, atau agenda ditunda..." class="w-full rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 p-3 text-xs focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition-colors"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="closeCancelModal" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            Tutup / Kembali
                        </button>
                        <button type="button" wire:click="executeCancelCampaign" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-black rounded-xl shadow-md shadow-rose-600/20 hover:shadow-rose-500/30 transition-all flex items-center gap-1.5">
                            <span>🚫</span>
                            <span>Konfirmasi Batalkan</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
