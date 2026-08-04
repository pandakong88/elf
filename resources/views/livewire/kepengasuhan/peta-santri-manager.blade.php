<div class="space-y-6">
    {{-- Header Page --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100 flex items-center gap-3">
                <span>Data Santri (Master Tabel)</span>
                @if($isGenderLocked)
                    <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 text-xs font-bold rounded-full border border-emerald-300/30">
                        Scope: {{ $genderFilter === 'L' ? 'Putra (L)' : 'Putri (P)' }}
                    </span>
                @endif
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Peta penempatan dan direktori lengkap santri (Mukim, Laju, Boyong, dan Alumni) seluruh pesantren.
            </p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <button type="button" wire:click="openExportConfirmModal"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Unduh Excel
            </button>
            @can('update-person')
            <button type="button" wire:click="$set('showImportModal', true)"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"/></svg>
                Update via Excel
            </button>
            @endcan
        </div>
    </div>

    {{-- Main Control Card: Tabs & Search/Filters --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-4">
        {{-- Navigation Sub-Tabs & View Mode Switcher --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
            {{-- Tabs --}}
            <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-800/80 p-1 rounded-xl">
                <button type="button" wire:click="$set('activeTab', 'komplek')"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg font-bold text-xs transition-all {{ $activeTab === 'komplek' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Penempatan Asrama &amp; Kamar</span>
                </button>

                <button type="button" wire:click="$set('activeTab', 'kelas')"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg font-bold text-xs transition-all {{ $activeTab === 'kelas' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    <span>Peta Kelas Madrasah</span>
                </button>
            </div>

            {{-- Search Bar --}}
            <div class="flex-1">
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama santri, NIK, NIS..."
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                    <div class="absolute left-3.5 top-3 text-slate-400 dark:text-slate-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
            </div>

            {{-- View Mode Switcher Button --}}
            <div class="flex items-center gap-2 self-end md:self-auto bg-slate-100 dark:bg-slate-800 p-1 rounded-xl">
                <button type="button" wire:click="$set('viewMode', 'table')"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-bold text-xs transition-all {{ $viewMode === 'table' ? 'bg-emerald-600 text-white shadow' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    <span>Mode Tabel (Default)</span>
                </button>
                <button type="button" wire:click="$set('viewMode', 'card')"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-bold text-xs transition-all {{ $viewMode === 'card' ? 'bg-emerald-600 text-white shadow' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                    <span>Mode Kartu / Bagan</span>
                </button>
            </div>
        </div>

        {{-- Secondary Filter Row --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
            {{-- Status Keanggotaan (Enrollment) Filter --}}
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status Keanggotaan</label>
                <select wire:model.live="enrollmentFilter"
                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    <option value="aktif">Aktif (Mukim &amp; Laju)</option>
                    <option value="boyong">Boyong / Keluar</option>
                    <option value="alumni">Alumni / Lulus</option>
                    <option value="">Semua Status Keanggotaan</option>
                </select>
            </div>

            {{-- Gender Filter --}}
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Filter Gender</label>
                <select wire:model.live="genderFilter" {{ $isGenderLocked ? 'disabled' : '' }}
                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 disabled:opacity-60">
                    <option value="">Semua Gender</option>
                    <option value="L">Putra (L)</option>
                    <option value="P">Putri (P)</option>
                </select>
            </div>

            {{-- Dormitory / Class Filter --}}
            @if($activeTab === 'komplek')
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Filter Komplek</label>
                    <select wire:model.live="dormitoryFilter"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                        <option value="">Semua Komplek</option>
                        @foreach($dormitoryOptions as $dOption)
                            <option value="{{ $dOption->id }}">{{ $dOption->name }} ({{ $dOption->gender }})</option>
                        @endforeach
                    </select>
                </div>
            @else
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Filter Kelas</label>
                    <select wire:model.live="kelasFilter"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasOptions as $kOption)
                            <option value="{{ $kOption->id }}">{{ strtoupper($kOption->jenjang) }} - {{ $kOption->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Presence Filter --}}
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status Keberadaan</label>
                <select wire:model.live="presenceFilter"
                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    <option value="">Semua Status Keberadaan</option>
                    <option value="mukim">Mukim (Tinggal di Asrama)</option>
                    <option value="laju">Laju (Non-Asrama)</option>
                    <option value="izin">Izin / Pulang Sementara</option>
                </select>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Statistik Ringkas (Ikut Filter Aktif)                        --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
            <div class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ number_format($stats['total']) }}</div>
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wide mt-0.5">Total Santri</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-indigo-200/60 dark:border-indigo-800/30 rounded-2xl p-4 shadow-sm">
            <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ number_format($stats['mukim']) }}</div>
            <div class="text-[11px] font-bold text-indigo-500 uppercase tracking-wide mt-0.5">Mukim</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-amber-200/60 dark:border-amber-800/30 rounded-2xl p-4 shadow-sm">
            <div class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ number_format($stats['laju']) }}</div>
            <div class="text-[11px] font-bold text-amber-500 uppercase tracking-wide mt-0.5">Laju (Non-Asrama)</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-orange-200/60 dark:border-orange-800/30 rounded-2xl p-4 shadow-sm">
            <div class="text-2xl font-black text-orange-600 dark:text-orange-400">{{ number_format($stats['izin']) }}</div>
            <div class="text-[11px] font-bold text-orange-500 uppercase tracking-wide mt-0.5">Izin / Pulang</div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-rose-200/60 dark:border-rose-800/30 rounded-2xl p-4 shadow-sm">
            <div class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ number_format($stats['boyong']) }}</div>
            <div class="text-[11px] font-bold text-rose-500 uppercase tracking-wide mt-0.5">Boyong / Alumni</div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Main Content: Mode Tabel (Default)                           --}}
    {{-- ============================================================ --}}
    @if($viewMode === 'table')
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4">Santri</th>
                            <th class="py-3.5 px-4">NIK / NIS</th>
                            <th class="py-3.5 px-4 text-center">Gender</th>
                            <th class="py-3.5 px-4 text-center">Status Keanggotaan</th>
                            <th class="py-3.5 px-4 text-center">Status Keberadaan</th>
                            <th class="py-3.5 px-4">Komplek &amp; Kamar</th>
                            <th class="py-3.5 px-4">Kelas Madrasah</th>
                            <th class="py-3.5 px-4 text-center w-36">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($santriList as $index => $santri)
                            @php
                                $currentAssignment = $santri->roomAssignments->first();
                                $currentEnrollment = $santri->madrasahEnrollments->first();
                                $role = $santri->roles->where('role_type', 'santri')->first();
                                $enrollmentStatus = $role->enrollment_status ?? 'aktif';
                                $presenceStatus = $role->presence_status ?? 'mukim';
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                {{-- Row Index --}}
                                <td class="py-3 px-4 text-center text-xs font-semibold text-slate-400">
                                    {{ $santriList->firstItem() + $index }}
                                </td>

                                {{-- Santri Profile --}}
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center flex-shrink-0 text-slate-600 dark:text-slate-300 font-bold text-xs overflow-hidden">
                                            @if($santri->photo)
                                                <img src="{{ Storage::url($santri->photo) }}" class="w-full h-full object-cover" alt="{{ $santri->name }}">
                                            @else
                                                {{ strtoupper(substr($santri->name, 0, 2)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <button type="button" wire:click="openQuickProfile('{{ $santri->id }}')" class="font-bold text-slate-800 dark:text-slate-100 hover:text-emerald-600 dark:hover:text-emerald-400 text-left transition-colors">
                                                {{ $santri->name }}
                                            </button>
                                            <div class="text-[11px] text-slate-400">
                                                {{ $santri->phone ?? 'Tidak ada HP' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- NIK / NIS --}}
                                <td class="py-3 px-4 text-xs font-mono text-slate-600 dark:text-slate-300">
                                    <div>NIS: <span class="font-bold text-slate-800 dark:text-slate-100">{{ $santri->santriProfile->nis ?? '-' }}</span></div>
                                    <div class="text-[10px] text-slate-400">NIK: {{ $santri->nik ?? '-' }}</div>
                                </td>

                                {{-- Gender --}}
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-md {{ $santri->gender === 'L' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-950/50 dark:text-pink-300' }}">
                                        {{ $santri->gender === 'L' ? 'Putra' : 'Putri' }}
                                    </span>
                                </td>

                                {{-- Status Keanggotaan --}}
                                <td class="py-3 px-4 text-center">
                                    @if(in_array($enrollmentStatus, ['boyong', 'keluar_resmi', 'dikeluarkan', 'tanpa_keterangan']))
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-extrabold rounded-full bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Boyong
                                        </span>
                                    @elseif($enrollmentStatus === 'alumni')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-extrabold rounded-full bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> Alumni / Lulus
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-extrabold rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    @endif
                                </td>

                                {{-- Status Keberadaan --}}
                                <td class="py-3 px-4 text-center">
                                    @if($presenceStatus === 'mukim')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200/50 dark:border-indigo-800/40">
                                            Mukim (Asrama)
                                        </span>
                                    @elseif($presenceStatus === 'izin' || $presenceStatus === 'pulang')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200/50 dark:border-amber-800/40">
                                            Izin / Pulang
                                        </span>
                                    @elseif($presenceStatus === 'laju')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold rounded-lg bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/50 dark:border-amber-800/40">
                                            Laju (Non-Asrama)
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>

                                {{-- Komplek & Kamar --}}
                                <td class="py-3 px-4">
                                    @if($currentAssignment && $currentAssignment->room)
                                        <div class="font-bold text-slate-800 dark:text-slate-200 text-xs">
                                            {{ $currentAssignment->room->dormitory->name }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-medium">
                                            {{ $currentAssignment->room->name }}
                                        </div>
                                    @else
                                        <span class="text-xs font-semibold text-slate-400">
                                            -
                                        </span>
                                    @endif
                                </td>

                                {{-- Kelas Madrasah --}}
                                <td class="py-3 px-4">
                                    @if($currentEnrollment && $currentEnrollment->kelas)
                                        <span class="inline-block px-2.5 py-1 text-xs font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                                            {{ strtoupper($currentEnrollment->kelas->jenjang) }} — {{ $currentEnrollment->kelas->name }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Belum Ada Kelas</span>
                                    @endif
                                </td>

                                {{-- Quick Action Buttons --}}
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" wire:click="openQuickProfile('{{ $santri->id }}')"
                                            class="p-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg transition-colors"
                                            title="Profil Lengkap">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>

                                        @can('change-enrollment-status')
                                        <button type="button" wire:click="openStatusModal('{{ $santri->id }}')"
                                            class="p-1.5 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/60 dark:hover:bg-amber-900 text-amber-600 dark:text-amber-300 rounded-lg transition-colors"
                                            title="Ubah Status Santri">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        </button>
                                        @endcan

                                        @can('manage-kamar')
                                        <button type="button" wire:click="openTransferRoomModal('{{ $santri->id }}')"
                                            class="p-1.5 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:hover:bg-emerald-900 text-emerald-600 dark:text-emerald-300 rounded-lg transition-colors"
                                            title="Pindah Kamar">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                        </button>
                                        @endcan

                                        @can('manage-kelas')
                                        <button type="button" wire:click="openTransferKelasModal('{{ $santri->id }}')"
                                            class="p-1.5 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900 text-indigo-600 dark:text-indigo-300 rounded-lg transition-colors"
                                            title="Pindah Kelas">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        </button>
                                        @endcan

                                        @can('delete-person')
                                        <button type="button" wire:click="openDeleteSantriModal('{{ $santri->id }}')"
                                            class="p-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 dark:hover:bg-rose-900 text-rose-600 dark:text-rose-300 rounded-lg transition-colors"
                                            title="Hapus Data Santri">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-12 text-center text-slate-400">
                                    Tidak ada data santri ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $santriList->links() }}
            </div>
        </div>
    @else
        {{-- MODE KARTU / BAGAN --}}
        @if($activeTab === 'komplek')
            {{-- TAB KOMPLEK: GRID HIRARKI --}}
            <div class="space-y-6">
                @forelse($dormitoriesData as $dorm)
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 text-xs font-extrabold uppercase rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                    Komplek {{ $dorm->gender === 'L' ? 'Putra' : 'Putri' }}
                                </span>
                                <h3 class="font-extrabold text-slate-900 dark:text-slate-100 text-lg">{{ $dorm->name }}</h3>
                            </div>
                            <span class="text-xs text-slate-500 font-bold">Total {{ $dorm->rooms->count() }} Kamar</span>
                        </div>

                        {{-- Rooms Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($dorm->rooms as $room)
                                <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-200/60 dark:border-slate-700/60 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm">{{ $room->name }}</h4>
                                        <span class="text-[11px] font-bold text-slate-500">
                                            {{ $room->currentAssignments->count() }}/{{ $room->capacity }}
                                        </span>
                                    </div>

                                    <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                                        @forelse($room->currentAssignments as $assignment)
                                            @php $rPerson = $assignment->person; @endphp
                                            <div class="flex items-center justify-between bg-white dark:bg-slate-900 p-2 rounded-lg text-xs shadow-sm">
                                                <span class="font-bold text-slate-800 dark:text-slate-200 truncate pr-1" title="{{ $rPerson->name }}">{{ $rPerson->name }}</span>
                                                <div class="flex items-center gap-1 flex-shrink-0">
                                                    <button type="button" wire:click="openQuickProfile('{{ $rPerson->id }}')"
                                                        class="p-1 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded transition-colors"
                                                        title="Lihat Detail Profil">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </button>

                                                    @can('manage-kamar')
                                                    <button type="button" wire:click="openTransferRoomModal('{{ $rPerson->id }}')"
                                                        class="p-1 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:hover:bg-emerald-900 text-emerald-600 dark:text-emerald-300 rounded transition-colors"
                                                        title="Pindah Kamar">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                                    </button>
                                                    @endcan

                                                    @can('manage-kelas')
                                                    <button type="button" wire:click="openTransferKelasModal('{{ $rPerson->id }}')"
                                                        class="p-1 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900 text-indigo-600 dark:text-indigo-300 rounded transition-colors"
                                                        title="Pindah Kelas">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                                    </button>
                                                    @endcan
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-4 text-slate-400 text-xs italic">Kamar kosong</div>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-slate-400">Tidak ada data komplek.</div>
                @endforelse
            </div>
        @else
            {{-- TAB KELAS MADRASAH: GRID HIRARKI --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($kelasListData as $kelas)
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                            <div>
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300">
                                    {{ strtoupper($kelas->jenjang) }}
                                </span>
                                <h3 class="font-extrabold text-slate-900 dark:text-slate-100 text-base mt-1">{{ $kelas->name }}</h3>
                                <p class="text-xs text-slate-500">Wali: {{ $kelas->waliKelas->name ?? 'Belum ditentukan' }}</p>
                            </div>
                            <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl">
                                {{ $kelas->enrollments->count() }} Santri
                            </span>
                        </div>

                        <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                            @forelse($kelas->enrollments as $enrollment)
                                @php $kPerson = $enrollment->person; @endphp
                                <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-800/50 p-2.5 rounded-xl border border-slate-100 dark:border-slate-700/50 text-xs">
                                    <div class="flex items-center gap-2.5 min-w-0 pr-1">
                                        <div class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-[10px] font-bold text-slate-600 dark:text-slate-300 flex-shrink-0">
                                            {{ strtoupper(substr($kPerson->name, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-800 dark:text-slate-200 truncate" title="{{ $kPerson->name }}">{{ $kPerson->name }}</div>
                                            <div class="text-[10px] text-slate-400">
                                                Komplek: {{ $kPerson->roomAssignments->first()?->room?->dormitory?->name ?? 'Non-Asrama' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        <button type="button" wire:click="openQuickProfile('{{ $kPerson->id }}')"
                                            class="p-1 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded transition-colors"
                                            title="Lihat Detail Profil">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>

                                        @can('manage-kamar')
                                        <button type="button" wire:click="openTransferRoomModal('{{ $kPerson->id }}')"
                                            class="p-1 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:hover:bg-emerald-900 text-emerald-600 dark:text-emerald-300 rounded transition-colors"
                                            title="Pindah Kamar">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                        </button>
                                        @endcan

                                        @can('manage-kelas')
                                        <button type="button" wire:click="openTransferKelasModal('{{ $kPerson->id }}')"
                                            class="p-1 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900 text-indigo-600 dark:text-indigo-300 rounded transition-colors"
                                            title="Pindah Kelas">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        </button>
                                        @endcan
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-slate-400 text-xs italic">Belum ada santri terdaftar di kelas ini.</div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-12 text-center text-slate-400">Tidak ada data kelas madrasah.</div>
                @endforelse
            </div>
        @endif
    @endif

        {{-- MODAL IMPORT EXCEL UPDATE DENGAN INTERACTIVE PREVIEW --}}
        @if($showImportModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl {{ $importStep === 2 ? 'max-w-4xl' : 'max-w-lg' }} w-full p-6 shadow-2xl space-y-5 transition-all">
                    
                    {{-- Header Modal --}}
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-950/60 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold">
                                @if($importStep === 1)
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"/></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">
                                    {{ $importStep === 1 ? 'Update Data via Excel' : 'Pratinjau Perubahan Data Excel' }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $importStep === 1 ? 'Langkah 1: Pilih & Upload file Excel hasil edit' : 'Langkah 2: Periksa daftar perubahan sebelum mengeksekusi update' }}
                                </p>
                            </div>
                        </div>

                        <button type="button" wire:click="resetImportModal" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    @if($importStep === 1)
                        {{-- LANGKAH 1: UPLOAD FILE & PERATURAN --}}
                        <div class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-800/40 rounded-2xl p-4 text-xs text-amber-800 dark:text-amber-300 space-y-1.5">
                            <div class="font-extrabold">⚠️ Ketentuan Update via Excel:</div>
                            <ul class="space-y-1 text-amber-700 dark:text-amber-300/80 list-disc list-inside">
                                <li>Kolom <strong>NIS tidak boleh diubah</strong> (digunakan sebagai kunci pencocok data santri)</li>
                                <li>Seluruh <strong>16 kolom</strong> (Nama, NIK, Gender, Tempat/Tgl Lahir, Status, Komplek, Kamar, Kelas, Wali, Alamat, Sekolah) akan di-detect perubahannya</li>
                                <li>Anda akan melihat **pratinjau perbedaan data (Before ➔ After)** sebelum data benar-benar disimpan</li>
                            </ul>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Pilih File Excel (.xlsx)</label>
                            <input type="file" wire:model="importFile" accept=".xlsx,.xls"
                                class="w-full text-sm text-slate-700 dark:text-slate-300 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950/60 dark:file:text-indigo-300 hover:file:bg-indigo-100">
                            @error('importFile') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-1">
                            <button type="button" wire:click="resetImportModal"
                                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs">Batal</button>
                            <button type="button" wire:click="generateImportPreview" wire:loading.attr="disabled"
                                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold rounded-xl text-xs shadow-md transition-all flex items-center gap-2">
                                <span wire:loading wire:target="generateImportPreview" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                                <span>Pratinjau Perubahan Data ➔</span>
                            </button>
                        </div>
                    @else
                        {{-- LANGKAH 2: TABEL PRATINJAU PERUBAHAN (DIFF PREVIEW) --}}
                        @php $stats = $importPreviewData['stats'] ?? ['total' => 0, 'changed' => 0, 'unchanged' => 0, 'skipped' => 0]; @endphp
                        
                        {{-- Rangkuman Statistik Preview --}}
                        <div class="grid grid-cols-4 gap-3">
                            <div class="p-3 bg-slate-100 dark:bg-slate-800 rounded-2xl text-center">
                                <div class="text-lg font-black text-slate-800 dark:text-slate-200">{{ $stats['total'] }}</div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase">Total Baris</div>
                            </div>
                            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/60 dark:border-emerald-800/40 rounded-2xl text-center">
                                <div class="text-lg font-black text-emerald-600 dark:text-emerald-400">{{ $stats['changed'] }}</div>
                                <div class="text-[10px] font-bold text-emerald-500 uppercase">Akan Di-update</div>
                            </div>
                            <div class="p-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60 rounded-2xl text-center">
                                <div class="text-lg font-black text-slate-500">{{ $stats['unchanged'] }}</div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase">Tanpa Perubahan</div>
                            </div>
                            <div class="p-3 bg-rose-50 dark:bg-rose-950/40 border border-rose-200/60 dark:border-rose-800/40 rounded-2xl text-center">
                                <div class="text-lg font-black text-rose-600 dark:text-rose-400">{{ $stats['skipped'] }}</div>
                                <div class="text-[10px] font-bold text-rose-500 uppercase">NIS Dilewati</div>
                            </div>
                        </div>

                        {{-- Tabel Rincian Perubahan --}}
                        <div class="border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden max-h-80 overflow-y-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-slate-100 dark:bg-slate-800 text-[10px] font-extrabold uppercase text-slate-500 border-b border-slate-200 dark:border-slate-700">
                                        <th class="py-2.5 px-3 w-10 text-center">No</th>
                                        <th class="py-2.5 px-3">Santri &amp; NIS</th>
                                        <th class="py-2.5 px-3 text-center">Status</th>
                                        <th class="py-2.5 px-3">Perincian Perubahan Data (Sebelum ➔ Sesudah)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @forelse($importPreviewData['rows'] ?? [] as $pRow)
                                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                                            <td class="py-2.5 px-3 text-center font-bold text-slate-400">{{ $pRow['row_num'] }}</td>
                                            <td class="py-2.5 px-3 font-bold text-slate-800 dark:text-slate-200">
                                                <div>{{ $pRow['name'] }}</div>
                                                <div class="text-[10px] font-mono text-slate-400">NIS: {{ $pRow['nis'] }}</div>
                                            </td>
                                            <td class="py-2.5 px-3 text-center">
                                                @if($pRow['status'] === 'changed')
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                                        🟢 Perubahan
                                                    </span>
                                                @elseif($pRow['status'] === 'unchanged')
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                                        ⚪ Sama
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300">
                                                        🔴 Dilewati
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-2.5 px-3">
                                                @if($pRow['status'] === 'changed')
                                                    <div class="space-y-1">
                                                        @foreach($pRow['diffs'] as $diff)
                                                            <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200/50 dark:border-indigo-800/40 text-[11px]">
                                                                <span class="font-bold text-indigo-700 dark:text-indigo-300">{{ $diff['field'] }}:</span>
                                                                <span class="line-through text-slate-400">{{ $diff['old'] }}</span>
                                                                <span class="text-indigo-600 dark:text-indigo-400 font-extrabold">➔ {{ $diff['new'] }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @elseif($pRow['status'] === 'unchanged')
                                                    <span class="text-slate-400 italic">Data Excel identik dengan database (tidak ada perubahan).</span>
                                                @else
                                                    <span class="text-rose-500 font-medium">{{ $pRow['reason'] ?? 'Dilewati' }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-6 text-center text-slate-400">Tidak ada baris data untuk dipratinjau.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Action Buttons Step 2 --}}
                        <div class="flex items-center justify-between pt-2">
                            <button type="button" wire:click="$set('importStep', 1)"
                                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs flex items-center gap-1.5">
                                <span>⏮️ Upload File Lain</span>
                            </button>
                            <div class="flex items-center gap-2">
                                <button type="button" wire:click="resetImportModal"
                                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs">Batal</button>
                                <button type="button" wire:click="processImport" wire:loading.attr="disabled"
                                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-xl text-xs shadow-lg transition-all flex items-center gap-2">
                                    <span wire:loading wire:target="processImport" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Konfirmasi &amp; Eksekusi Update ({{ $stats['changed'] }} Data)</span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Hasil Import (jika ada) --}}
        @if(!empty($importResults))
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-3 mb-6">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <h3 class="font-extrabold text-sm text-slate-900 dark:text-slate-100">Hasil Import Excel</h3>
                </div>
                <div class="flex items-center gap-4 text-sm">
                    <span class="px-3 py-1.5 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold rounded-xl text-xs">
                        ✅ {{ $importResults['updated'] }} data diperbarui
                    </span>
                    @if(!empty($importResults['skipped']))
                        <span class="px-3 py-1.5 bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-bold rounded-xl text-xs">
                            ⚠️ {{ count($importResults['skipped']) }} baris dilewati
                        </span>
                    @endif
                </div>
                @if(!empty($importResults['skipped']))
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 space-y-1 max-h-40 overflow-y-auto">
                        @foreach($importResults['skipped'] as $skipMsg)
                            <div class="text-xs text-amber-700 dark:text-amber-400">• {{ $skipMsg }}</div>
                        @endforeach
                    </div>
                @endif
                <button type="button" wire:click="$set('importResults', [])" class="text-xs text-slate-400 hover:text-slate-600 transition-colors">Tutup laporan</button>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- MODAL 1: Quick Profile Santri                                --}}
        {{-- ============================================================ --}}
        @if($showQuickProfileModal && $selectedSantri)
            @php
                $prof = $selectedSantri->santriProfile;
                $role = $selectedSantri->roles->where('role_type', 'santri')->first();
                $enrollStatus = $role->enrollment_status ?? 'aktif';
                $presStatus   = $role->presence_status ?? 'mukim';
                $currRoom     = $selectedSantri->roomAssignments->first();
                $currKelas    = $selectedSantri->madrasahEnrollments->first();
                $addInfo      = $prof->additional_info ?? [];

                $fatherAddr   = $addInfo['father_address'] ?? ($selectedSantri->address ?: '-');
                $motherAddr   = $addInfo['mother_address'] ?? ($selectedSantri->address ?: '-');
                $motherJob    = $addInfo['mother_job'] ?? '-';
                $formalGrade  = $prof->school_year ?? ($addInfo['school_grade'] ?? '-');
            @endphp
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md overflow-y-auto">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-4xl w-full p-6 sm:p-8 shadow-2xl space-y-6 my-8 max-h-[90vh] overflow-y-auto">
                    {{-- Header Profile Banner --}}
                    <div class="relative bg-slate-50 dark:bg-slate-800/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-black text-2xl shadow-lg flex-shrink-0 overflow-hidden border-2 border-emerald-400">
                                @if($selectedSantri->photo)
                                    <img src="{{ Storage::url($selectedSantri->photo) }}" class="w-full h-full object-cover" alt="">
                                @else
                                    {{ strtoupper(substr($selectedSantri->name, 0, 2)) }}
                                @endif
                            </div>
                            <div>
                                <h3 class="font-black text-xl text-slate-900 dark:text-slate-100 tracking-tight">{{ $selectedSantri->name }}</h3>
                                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mt-1">
                                    <span class="font-medium">NIS: <strong class="font-mono text-slate-900 dark:text-slate-100">{{ $prof->nis ?? '-' }}</strong></span>
                                    <span class="text-slate-300 dark:text-slate-700">•</span>
                                    <span class="font-medium">NIK: <strong class="font-mono text-slate-900 dark:text-slate-100">{{ $selectedSantri->nik ?? '-' }}</strong></span>
                                </div>
                                <div class="mt-2.5 flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase rounded-full {{ $selectedSantri->gender === 'L' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950/80 dark:text-blue-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-950/80 dark:text-pink-300' }}">
                                        {{ $selectedSantri->gender === 'L' ? 'Putra (L)' : 'Putri (P)' }}
                                    </span>

                                    @if($enrollStatus === 'alumni')
                                        <span class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase rounded-full bg-purple-100 text-purple-700 dark:bg-purple-950/80 dark:text-purple-300">
                                            ALUMNI / LULUS
                                        </span>
                                    @elseif(in_array($enrollStatus, ['boyong', 'keluar_resmi', 'dikeluarkan']))
                                        <span class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase rounded-full bg-rose-100 text-rose-700 dark:bg-rose-950/80 dark:text-rose-300">
                                            BOYONG / KELUAR
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300">
                                            SANTRI AKTIF
                                        </span>
                                    @endif

                                    <span class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-950/80 dark:text-indigo-300">
                                        {{ strtoupper($presStatus) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <button type="button" wire:click="closeQuickProfile" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-xl hover:bg-slate-200/60 dark:hover:bg-slate-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Detail Information Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                        {{-- 1. Penempatan Asrama & Madrasah --}}
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 space-y-3">
                            <div class="flex items-center gap-2 border-b border-slate-200/60 dark:border-slate-700/60 pb-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <h4 class="font-extrabold text-xs text-slate-900 dark:text-slate-100 uppercase tracking-wider">
                                    1. Penempatan Pesantren &amp; Diniyyah
                                </h4>
                            </div>
                            <div class="space-y-2 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium">Komplek Asrama:</span>
                                    <span class="font-bold text-slate-900 dark:text-slate-100">{{ $currRoom->room->dormitory->name ?? 'Non-Asrama / Laju' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium">Kamar:</span>
                                    <span class="font-extrabold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-lg border border-indigo-200/40 dark:border-indigo-800/40">{{ $currRoom->room->name ?? '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium">Kelas Diniyyah:</span>
                                    <span class="font-bold text-slate-900 dark:text-slate-100">{{ $currKelas->kelas->name ?? 'Belum Ada Kelas' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium">Jenjang Diniyyah:</span>
                                    <span class="font-extrabold text-slate-800 dark:text-slate-200 uppercase">{{ strtoupper($currKelas->kelas->jenjang ?? '-') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium">Wali Kelas:</span>
                                    <span class="font-bold text-slate-900 dark:text-slate-100">{{ $currKelas->kelas->waliKelas->name ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Data Orang Tua / Wali Kandung --}}
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 space-y-3">
                            <div class="flex items-center gap-2 border-b border-slate-200/60 dark:border-slate-700/60 pb-2">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                <h4 class="font-extrabold text-xs text-slate-900 dark:text-slate-100 uppercase tracking-wider">
                                    2. Data Orang Tua / Wali Kandung
                                </h4>
                            </div>
                            <div class="grid grid-cols-1 gap-2.5 text-xs">
                                <div class="bg-white dark:bg-slate-900/80 p-3 rounded-xl border border-slate-200/80 dark:border-slate-700/80 space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-extrabold text-indigo-600 dark:text-indigo-400 uppercase">👨 Ayah Kandung</span>
                                        <span class="font-bold text-slate-900 dark:text-slate-100">{{ $prof->father_name ?? '-' }}</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-600 dark:text-slate-300 pt-0.5">
                                        <div>No. HP: <strong class="font-mono font-bold text-slate-900 dark:text-slate-100">{{ $prof->father_phone ?? '-' }}</strong></div>
                                        <div>Pekerjaan: <strong class="font-bold text-slate-900 dark:text-slate-100">{{ $prof->father_occupation ?? '-' }}</strong></div>
                                    </div>
                                    <div class="text-[11px] text-slate-600 dark:text-slate-300 pt-0.5">
                                        <span class="text-slate-400">Alamat:</span> <span class="font-medium text-slate-900 dark:text-slate-100">{{ $fatherAddr }}</span>
                                    </div>
                                </div>

                                <div class="bg-white dark:bg-slate-900/80 p-3 rounded-xl border border-slate-200/80 dark:border-slate-700/80 space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-extrabold text-pink-600 dark:text-pink-400 uppercase">👩 Ibu Kandung</span>
                                        <span class="font-bold text-slate-900 dark:text-slate-100">{{ $prof->mother_name ?? '-' }}</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-600 dark:text-slate-300 pt-0.5">
                                        <div>No. HP: <strong class="font-mono font-bold text-slate-900 dark:text-slate-100">{{ $prof->mother_phone ?? '-' }}</strong></div>
                                        <div>Pekerjaan: <strong class="font-bold text-slate-900 dark:text-slate-100">{{ $motherJob }}</strong></div>
                                    </div>
                                    <div class="text-[11px] text-slate-600 dark:text-slate-300 pt-0.5">
                                        <span class="text-slate-400">Alamat:</span> <span class="font-medium text-slate-900 dark:text-slate-100">{{ $motherAddr }}</span>
                                    </div>
                                </div>

                                @if($prof->getAdditional('guardian_name') || $prof->getAdditional('guardian_phone'))
                                    <div class="bg-white dark:bg-slate-900/80 p-3 rounded-xl border border-amber-200/80 dark:border-amber-700/80 space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[11px] font-extrabold text-amber-600 dark:text-amber-400 uppercase">👤 Wali Santri ({{ $prof->getAdditional('guardian_relationship') ?: 'Wali' }})</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100">{{ $prof->getAdditional('guardian_name') ?? '-' }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-600 dark:text-slate-300 pt-0.5">
                                            <div>No. HP: <strong class="font-mono font-bold text-slate-900 dark:text-slate-100">{{ $prof->getAdditional('guardian_phone') ?? '-' }}</strong></div>
                                            <div>Hubungan: <strong class="font-bold text-slate-900 dark:text-slate-100">{{ $prof->getAdditional('guardian_relationship') ?: 'Wali' }}</strong></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- 3. Data Pribadi & Kontak Santri --}}
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 space-y-3">
                            <div class="flex items-center gap-2 border-b border-slate-200/60 dark:border-slate-700/60 pb-2">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <h4 class="font-extrabold text-xs text-slate-900 dark:text-slate-100 uppercase tracking-wider">
                                    3. Data Pribadi &amp; Kontak Santri
                                </h4>
                            </div>
                            <div class="space-y-2 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium">Tempat / Tgl Lahir:</span>
                                    <span class="font-bold text-slate-900 dark:text-slate-100">
                                        {{ $selectedSantri->birth_place ?? '-' }}, {{ $selectedSantri->birth_date?->format('d M Y') ?? '-' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium">No. HP / WA Santri:</span>
                                    <span class="font-mono font-bold text-slate-900 dark:text-slate-100">{{ $selectedSantri->phone ?? '-' }}</span>
                                </div>
                                <div class="pt-1">
                                    <span class="text-slate-500 font-medium block mb-0.5">Alamat Lengkap Santri:</span>
                                    <span class="font-semibold text-slate-900 dark:text-slate-100 block bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200/60 dark:border-slate-700/60">{{ $selectedSantri->address ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- 4. Kesehatan & Sekolah Formal --}}
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 space-y-3">
                            <div class="flex items-center gap-2 border-b border-slate-200/60 dark:border-slate-700/60 pb-2">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                <h4 class="font-extrabold text-xs text-slate-900 dark:text-slate-100 uppercase tracking-wider">
                                    4. Kesehatan &amp; Sekolah Formal
                                </h4>
                            </div>
                            <div class="space-y-2 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium">Golongan Darah:</span>
                                    <span class="px-2 py-0.5 rounded-md font-black bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300 text-[11px]">{{ strtoupper($prof->blood_type ?? '-') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium">Riwayat Penyakit:</span>
                                    <span class="font-bold text-slate-900 dark:text-slate-100">{{ $prof->medical_history ?? 'Tidak Ada' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium">Alergi:</span>
                                    <span class="font-bold text-slate-900 dark:text-slate-100">{{ $prof->allergies ?? 'Tidak Ada' }}</span>
                                </div>
                                <div class="flex items-center justify-between border-t border-slate-200/60 dark:border-slate-700/60 pt-2">
                                    <span class="text-slate-500 font-medium">Sekolah Formal / Luar:</span>
                                    <span class="font-bold text-slate-900 dark:text-slate-100">{{ $prof->school_name ?? '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium">Tingkat / Kelas Formal:</span>
                                    <span class="font-extrabold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-lg border border-emerald-200/40 dark:border-emerald-800/40">{{ ($formalGrade && $formalGrade !== '-') ? $formalGrade : 'Belum Diisi' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}

                    <div class="flex flex-wrap items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800 pt-4">
                        @can('update-person')
                        <a href="{{ route('kepengasuhan.santri.edit', $selectedSantri->id) }}"
                            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-extrabold rounded-xl text-xs shadow-md transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Edit Biodata</span>
                        </a>
                        @endcan

                        @can('manage-kamar')
                        <button type="button" wire:click="openTransferRoomModal('{{ $selectedSantri->id }}')"
                            class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-xl text-xs shadow-md transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span>Pindah Kamar</span>
                        </button>
                        @endcan

                        @can('manage-kelas')
                        <button type="button" wire:click="openTransferKelasModal('{{ $selectedSantri->id }}')"
                            class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold rounded-xl text-xs shadow-md transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span>Pindah Kelas</span>
                        </button>
                        @endcan

                        <button type="button" wire:click="closeQuickProfile" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold rounded-xl text-xs shadow-md transition-all">Tutup Informasi Profil</button>
                    </div>
                </div>
            </div>
        @endif

    {{-- ============================================================ --}}
    {{-- MODAL 2: Transfer Kamar                                       --}}
    {{-- ============================================================ --}}
    @if($showTransferRoomModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">Pindah / Assign Kamar</h3>
                <p class="text-xs text-slate-500">Pilih kamar tujuan untuk santri <strong>{{ $transferSantriName }}</strong>:</p>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Pilih Kamar Tujuan</label>
                    <select wire:model="targetRoomId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                        <option value="">-- Pilih Kamar --</option>
                        @foreach($roomOptions as $rOpt)
                            <option value="{{ $rOpt->id }}">{{ $rOpt->dormitory->name }} — {{ $rOpt->name }} (Sisa: {{ $rOpt->capacity - $rOpt->currentAssignments->count() }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3">
                    <button type="button" wire:click="closeTransferRoomModal" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
                    <button type="button" wire:click="requestTransferRoomConfirm" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow transition-all">Simpan &amp; Pindahkan</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL 3: Transfer Kelas                                       --}}
    {{-- ============================================================ --}}
    @if($showTransferKelasModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">Pindah / Enroll Kelas Madrasah</h3>
                <p class="text-xs text-slate-500">Pilih kelas madrasah tujuan untuk santri <strong>{{ $transferKelasSantriName }}</strong>:</p>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Pilih Kelas Tujuan</label>
                    <select wire:model="targetKelasId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasOptions as $kOpt)
                            <option value="{{ $kOpt->id }}">{{ strtoupper($kOpt->jenjang) }} — {{ $kOpt->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3">
                    <button type="button" wire:click="closeTransferKelasModal" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
                    <button type="button" wire:click="requestTransferKelasConfirm" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs shadow transition-all">Simpan &amp; Pindahkan</button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL UBAH STATUS SANTRI --}}
    @if($showStatusModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">Ubah Status Santri</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $statusSantriName }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Status Keanggotaan</label>
                        <select wire:model="targetEnrollmentStatus" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100">
                            <option value="aktif">✅ Aktif (Santri Mukim/Laju)</option>
                            <option value="boyong">🚪 Boyong (Keluar Tidak Resmi)</option>
                            <option value="keluar_resmi">📋 Keluar Resmi</option>
                            <option value="dikeluarkan">❌ Dikeluarkan</option>
                            <option value="alumni">🎓 Alumni / Lulus</option>
                            <option value="tanpa_keterangan">❓ Tanpa Keterangan</option>
                        </select>
                    </div>

                    @if($targetEnrollmentStatus === 'aktif')
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Status Keberadaan</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition-colors {{ $targetPresenceStatus === 'mukim' ? 'bg-indigo-50 border-indigo-400 dark:bg-indigo-950/60 dark:border-indigo-600' : 'bg-slate-50 border-slate-200 dark:bg-slate-800 dark:border-slate-700' }}">
                                <input type="radio" wire:model="targetPresenceStatus" value="mukim" class="text-indigo-600">
                                <div>
                                    <div class="text-xs font-bold text-slate-800 dark:text-slate-200">Mukim</div>
                                    <div class="text-[10px] text-slate-400">Tinggal di asrama</div>
                                </div>
                            </label>
                            <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition-colors {{ $targetPresenceStatus === 'laju' ? 'bg-amber-50 border-amber-400 dark:bg-amber-950/60 dark:border-amber-600' : 'bg-slate-50 border-slate-200 dark:bg-slate-800 dark:border-slate-700' }}">
                                <input type="radio" wire:model="targetPresenceStatus" value="laju" class="text-amber-600">
                                <div>
                                    <div class="text-xs font-bold text-slate-800 dark:text-slate-200">Laju</div>
                                    <div class="text-[10px] text-slate-400">Non-asrama</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Catatan Perubahan (Opsional)</label>
                        <textarea wire:model="statusChangeNotes" rows="2" placeholder="Alasan perubahan status..."
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 resize-none"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-1">
                    <button type="button" wire:click="$set('showStatusModal', false)"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs">Batal</button>
                    <button type="button" wire:click="requestStatusChangeConfirm"
                        class="px-5 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-extrabold rounded-xl text-xs shadow-md transition-all">Simpan Perubahan Status</button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL KONFIRMASI KOSTUM ELEGAN (NO BROWSER ALERT) --}}
    @if($showConfirmModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold flex-shrink-0
                        {{ $confirmButtonColor === 'rose' ? 'bg-rose-100 text-rose-600 dark:bg-rose-950 dark:text-rose-400' : '' }}
                        {{ $confirmButtonColor === 'amber' ? 'bg-amber-100 text-amber-600 dark:bg-amber-950 dark:text-amber-400' : '' }}
                        {{ $confirmButtonColor === 'emerald' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400' : '' }}
                        {{ $confirmButtonColor === 'indigo' ? 'bg-indigo-100 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400' : '' }}">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">{{ $confirmTitle }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Konfirmasi Tindakan</p>
                    </div>
                </div>

                <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 text-xs text-slate-700 dark:text-slate-300 leading-relaxed font-medium">
                    {{ $confirmMessage }}
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" wire:click="$set('showConfirmModal', false)"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors">
                        Batal
                    </button>
                    <button type="button" wire:click="processConfirmedAction"
                        class="px-5 py-2.5 text-white font-extrabold rounded-xl text-xs shadow-lg transition-all
                        {{ $confirmButtonColor === 'rose' ? 'bg-rose-600 hover:bg-rose-500 active:bg-rose-700' : '' }}
                        {{ $confirmButtonColor === 'amber' ? 'bg-amber-600 hover:bg-amber-500 active:bg-amber-700' : '' }}
                        {{ $confirmButtonColor === 'emerald' ? 'bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700' : '' }}
                        {{ $confirmButtonColor === 'indigo' ? 'bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700' : '' }}">
                        {{ $confirmButtonText }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL KONFIRMASI UNDUH EXCEL DENGAN RANGKUMAN FILTER --}}
    @if($showExportConfirmModal)
        @php $summary = $this->exportSummary; @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">Konfirmasi Unduh Excel</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Periksa rangkuman filter sebelum mengunduh data</p>
                    </div>
                </div>

                <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 space-y-3 text-xs">
                    <div class="flex items-center justify-between pb-2.5 border-b border-slate-200/60 dark:border-slate-700/60">
                        <span class="font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wide text-[10px]">Total Santri Diekspor</span>
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 font-extrabold rounded-lg text-xs">
                            {{ number_format($summary['total_count']) }} Santri
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-slate-700 dark:text-slate-300">
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status Keanggotaan</div>
                            <div class="font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $summary['enrollment_label'] }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status Keberadaan</div>
                            <div class="font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $summary['presence_label'] }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Filter Gender</div>
                            <div class="font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $summary['gender_label'] }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $activeTab === 'komplek' ? 'Komplek Asrama' : 'Kelas Madrasah' }}</div>
                            <div class="font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $summary['location_label'] }}</div>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kata Kunci Pencarian</div>
                        <div class="font-medium text-slate-700 dark:text-slate-300 mt-0.5 italic">{{ $summary['search_label'] }}</div>
                    </div>

                    <div class="pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Rencana Nama File Output</div>
                        <div class="px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl font-mono text-[11px] text-emerald-600 dark:text-emerald-400 font-bold break-all flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>{{ $summary['filename'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-1">
                    <button type="button" wire:click="$set('showExportConfirmModal', false)"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors">
                        Batal
                    </button>
                    <button type="button" wire:click="exportSantri" wire:loading.attr="disabled"
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-xl text-xs shadow-lg transition-all flex items-center gap-2">
                        <span wire:loading wire:target="exportSantri" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Ya, Unduh File Excel</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
