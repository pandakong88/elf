<div class="space-y-6">
    {{-- ============================================================ --}}
    {{-- Header Page & Sub-Tab Navigation                             --}}
    {{-- ============================================================ --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100 flex flex-wrap items-center gap-2">
                <span>Pusat Kendali</span>
                <span class="hidden sm:inline text-slate-400 font-light">—</span>
                <span class="hidden sm:inline">Asrama &amp; Kelas</span>
                @if($isGenderLocked)
                    <span class="px-2.5 py-0.5 bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 text-xs font-bold rounded-full border border-emerald-300/30">
                        Scope: {{ $genderFilter === 'L' ? 'Putra (L)' : 'Putri (P)' }}
                    </span>
                @endif
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Pengelolaan terpadu komplek, kamar, kelas madrasah, dan alokasi santri.
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-wrap items-center gap-2">
            @can('create-person')
            <button type="button" wire:click="openNewSantriModal"
                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-bold rounded-xl shadow-md transition-all text-xs sm:text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>Daftarkan Santri</span>
            </button>
            @endcan

            <a href="{{ route('setup.santri') }}"
                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-md transition-all text-xs sm:text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Import Excel</span>
            </a>

            @if($activeTab === 'komplek')
                @can('manage-asrama')
                <button type="button" wire:click="openCreateDormitoryModal"
                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl shadow-md transition-all text-xs sm:text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Komplek</span>
                </button>
                @endcan
            @elseif($activeTab === 'kamar')
                @can('manage-kamar')
                <button type="button" wire:click="openCreateRoomModal"
                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl shadow-md transition-all text-xs sm:text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Kamar</span>
                </button>
                @endcan
            @elseif($activeTab === 'kelas')
                @can('manage-kelas')
                <button type="button" wire:click="openCreateKelasModal"
                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-md transition-all text-xs sm:text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Kelas</span>
                </button>
                @endcan
            @endif
        </div>
    </div>

    {{-- Top Sub-Tab Navigation Bar --}}
    <div class="bg-white dark:bg-slate-900 p-2 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm overflow-x-auto">
        <div class="flex items-center gap-1 min-w-max">
            <button type="button" wire:click="$set('activeTab', 'komplek')"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs transition-all {{ $activeTab === 'komplek' ? 'bg-emerald-600 text-white shadow' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Data Komplek</span>
            </button>

            <button type="button" wire:click="$set('activeTab', 'kamar')"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs transition-all {{ $activeTab === 'kamar' ? 'bg-emerald-600 text-white shadow' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Data Kamar</span>
            </button>

            <button type="button" wire:click="$set('activeTab', 'kelas')"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs transition-all {{ $activeTab === 'kelas' ? 'bg-indigo-600 text-white shadow' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                <span>Data Kelas Madrasah</span>
            </button>

            <div class="h-6 w-px bg-slate-200 dark:bg-slate-800 mx-1"></div>

            <button type="button" wire:click="$set('activeTab', 'bagan-komplek')"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs transition-all {{ $activeTab === 'bagan-komplek' ? 'bg-teal-600 text-white shadow' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                <span>Bagan Komplek &amp; Kamar</span>
            </button>

            <button type="button" wire:click="$set('activeTab', 'bagan-kelas')"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs transition-all {{ $activeTab === 'bagan-kelas' ? 'bg-teal-600 text-white shadow' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Bagan Kelas Madrasah</span>
            </button>
        </div>
    </div>

    {{-- Control Filters --}}
    <div class="bg-white dark:bg-slate-900 p-4 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pencarian</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama..."
                        class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    <div class="absolute left-3 top-2.5 text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Filter Gender</label>
                <select wire:model.live="genderFilter" {{ $isGenderLocked ? 'disabled' : '' }}
                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 disabled:opacity-60">
                    <option value="">Semua Gender</option>
                    <option value="L">Putra (L)</option>
                    <option value="P">Putri (P)</option>
                </select>
            </div>

            @if(in_array($activeTab, ['kamar', 'bagan-komplek']))
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Filter Komplek</label>
                    <select wire:model.live="dormitoryFilter"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                        <option value="">Semua Komplek</option>
                        @foreach($dormitoryOptions as $dOpt)
                            <option value="{{ $dOpt->id }}">{{ $dOpt->name }} ({{ $dOpt->gender }})</option>
                        @endforeach
                    </select>
                </div>
            @elseif(in_array($activeTab, ['bagan-kelas']))
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Filter Kelas</label>
                    <select wire:model.live="kelasFilter"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasOptions as $kOpt)
                            <option value="{{ $kOpt->id }}">{{ strtoupper($kOpt->jenjang) }} - {{ $kOpt->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>

    {{-- SUB-TAB 1: DATA KOMPLEK (CRUD) --}}
    @if($activeTab === 'komplek')
        {{-- Tabel: tampil di desktop md+ --}}
        <div class="hidden md:block bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm min-w-[640px]">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Komplek</th>
                        <th class="py-3 px-4 text-center">Gender</th>
                        <th class="py-3 px-4">Nominal Kas Komplek</th>
                        <th class="py-3 px-4 text-center">Jumlah Kamar</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($dormitoriesList as $idx => $dorm)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                            <td class="py-3 px-4 text-center text-xs text-slate-400 font-semibold">{{ $dormitoriesList->firstItem() + $idx }}</td>
                            <td class="py-3 px-4 font-bold text-slate-800 dark:text-slate-100">{{ $dorm->name }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $dorm->gender === 'L' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-950 dark:text-pink-300' }}">
                                    {{ $dorm->gender === 'L' ? 'Putra' : 'Putri' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-mono text-xs">Rp {{ number_format($dorm->kas_komplek_amount ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center font-bold text-slate-700 dark:text-slate-300">{{ $dorm->rooms_count }} Kamar</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $dorm->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $dorm->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @can('manage-asrama')
                                    <button type="button" wire:click="openEditDormitoryModal('{{ $dorm->id }}')" title="Edit Komplek"
                                        class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button type="button" wire:click="requestToggleDormitoryStatusConfirm('{{ $dorm->id }}')" title="Toggle Status Aktif/Nonaktif"
                                        class="p-1.5 text-amber-500 hover:text-amber-700 hover:bg-amber-50 dark:hover:bg-amber-950/40 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    </button>
                                    <button type="button" wire:click="requestDeleteDormitoryConfirm('{{ $dorm->id }}')" title="Hapus Komplek"
                                        class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @else
                                    <span class="text-xs text-slate-300 italic">—</span>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-slate-400 text-xs">Belum ada data komplek.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">{{ $dormitoriesList->links() }}</div>
        </div>

        {{-- Card list: tampil di mobile saja --}}
        <div class="md:hidden space-y-3">
            @forelse($dormitoriesList as $idx => $dorm)
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="font-extrabold text-slate-900 dark:text-slate-100 text-sm">{{ $dorm->name }}</div>
                            <div class="flex flex-wrap gap-1.5 mt-1.5">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $dorm->gender === 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                    {{ $dorm->gender === 'L' ? 'Putra' : 'Putri' }}
                                </span>
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $dorm->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $dorm->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $dorm->rooms_count }} Kamar</span>
                            </div>
                            <div class="text-[11px] text-slate-400 mt-1">Kas: Rp {{ number_format($dorm->kas_komplek_amount ?? 0, 0, ',', '.') }}</div>
                        </div>
                        @can('manage-asrama')
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <button type="button" wire:click="openEditDormitoryModal('{{ $dorm->id }}')" title="Edit"
                                class="p-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button type="button" wire:click="requestToggleDormitoryStatusConfirm('{{ $dorm->id }}')" title="Toggle Status"
                                class="p-2 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            </button>
                            <button type="button" wire:click="requestDeleteDormitoryConfirm('{{ $dorm->id }}')" title="Hapus"
                                class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                        @endcan
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-sm">Belum ada data komplek.</div>
            @endforelse
            <div class="pt-1">{{ $dormitoriesList->links() }}</div>
        </div>
    @endif

    {{-- SUB-TAB 2: DATA KAMAR (CRUD) --}}
    @if($activeTab === 'kamar')
        {{-- Tabel desktop --}}
        <div class="hidden md:block bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm min-w-[640px]">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Komplek</th>
                        <th class="py-3 px-4">Nama Kamar</th>
                        <th class="py-3 px-4 text-center">Kapasitas Bed</th>
                        <th class="py-3 px-4 text-center">Penghuni Aktif</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($roomsList as $idx => $roomItem)
                        @php $occCount = $roomItem->currentAssignments->count(); @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                            <td class="py-3 px-4 text-center text-xs text-slate-400 font-semibold">{{ $roomsList->firstItem() + $idx }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-700 dark:text-slate-300">{{ $roomItem->dormitory->name ?? '-' }}</td>
                            <td class="py-3 px-4 font-bold text-slate-800 dark:text-slate-100">{{ $roomItem->name }}</td>
                            <td class="py-3 px-4 text-center font-bold">{{ $roomItem->capacity }} Bed</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $occCount >= $roomItem->capacity ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' }}">
                                    {{ $occCount }} / {{ $roomItem->capacity }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $roomItem->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $roomItem->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @can('manage-kamar')
                                    <button type="button" wire:click="openEditRoomModal('{{ $roomItem->id }}')" title="Edit Kamar"
                                        class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button type="button" wire:click="requestToggleRoomStatusConfirm('{{ $roomItem->id }}')" title="Toggle Status Aktif/Nonaktif"
                                        class="p-1.5 text-amber-500 hover:text-amber-700 hover:bg-amber-50 dark:hover:bg-amber-950/40 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    </button>
                                    <button type="button" wire:click="requestDeleteRoomConfirm('{{ $roomItem->id }}')" title="Hapus Kamar"
                                        class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @else
                                    <span class="text-xs text-slate-300 italic">—</span>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-slate-400 text-xs">Belum ada data kamar.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">{{ $roomsList->links() }}</div>
        </div>

        {{-- Card list mobile --}}
        <div class="md:hidden space-y-3">
            @forelse($roomsList as $idx => $roomItem)
                @php $occCount = $roomItem->currentAssignments->count(); @endphp
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="font-extrabold text-slate-900 dark:text-slate-100 text-sm">{{ $roomItem->name }}</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">{{ $roomItem->dormitory->name ?? '-' }}</div>
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $occCount >= $roomItem->capacity ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $occCount }}/{{ $roomItem->capacity }} Bed
                                </span>
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $roomItem->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $roomItem->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                        </div>
                        @can('manage-kamar')
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <button type="button" wire:click="openEditRoomModal('{{ $roomItem->id }}')" title="Edit"
                                class="p-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button type="button" wire:click="requestToggleRoomStatusConfirm('{{ $roomItem->id }}')" title="Toggle Status"
                                class="p-2 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            </button>
                            <button type="button" wire:click="requestDeleteRoomConfirm('{{ $roomItem->id }}')" title="Hapus"
                                class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                        @endcan
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-sm">Belum ada data kamar.</div>
            @endforelse
            <div class="pt-1">{{ $roomsList->links() }}</div>
        </div>
    @endif

    {{-- SUB-TAB 3: DATA KELAS (CRUD) --}}
    @if($activeTab === 'kelas')
        {{-- Tabel desktop --}}
        <div class="hidden md:block bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm min-w-[640px]">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Jenjang</th>
                        <th class="py-3 px-4">Nama Kelas</th>
                        <th class="py-3 px-4">Wali Kelas</th>
                        <th class="py-3 px-4 text-center">Tahun Ajaran</th>
                        <th class="py-3 px-4 text-center">Total Santri</th>
                        <th class="py-3 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($kelasList as $idx => $kItem)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                            <td class="py-3 px-4 text-center text-xs text-slate-400 font-semibold">{{ $kelasList->firstItem() + $idx }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                    {{ strtoupper($kItem->jenjang) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-800 dark:text-slate-100">{{ $kItem->name }}</td>
                            <td class="py-3 px-4 text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $kItem->waliKelas->name ?? 'Belum ditentukan' }}</td>
                            <td class="py-3 px-4 text-center text-xs font-mono">{{ $kItem->academic_year }}</td>
                            <td class="py-3 px-4 text-center font-bold text-slate-800 dark:text-slate-200">{{ $kItem->enrollments_count }} Santri</td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @can('manage-kelas')
                                    <button type="button" wire:click="openEditKelasModal('{{ $kItem->id }}')" title="Edit Kelas"
                                        class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button type="button" wire:click="requestDeleteKelasConfirm('{{ $kItem->id }}')" title="Hapus Kelas"
                                        class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @else
                                    <span class="text-xs text-slate-300 italic">—</span>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-slate-400 text-xs">Belum ada data kelas madrasah.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">{{ $kelasList->links() }}</div>
        </div>

        {{-- Card list mobile --}}
        <div class="md:hidden space-y-3">
            @forelse($kelasList as $idx => $kItem)
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">{{ strtoupper($kItem->jenjang) }}</span>
                                <span class="font-extrabold text-slate-900 dark:text-slate-100 text-sm">{{ $kItem->name }}</span>
                            </div>
                            <div class="text-[11px] text-slate-500 mt-1">Wali: {{ $kItem->waliKelas->name ?? 'Belum ditentukan' }}</div>
                            <div class="flex flex-wrap gap-1.5 mt-1.5">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">{{ $kItem->enrollments_count }} Santri</span>
                                <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded bg-slate-100 dark:bg-slate-800 text-slate-500">{{ $kItem->academic_year }}</span>
                            </div>
                        </div>
                        @can('manage-kelas')
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <button type="button" wire:click="openEditKelasModal('{{ $kItem->id }}')" title="Edit"
                                class="p-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button type="button" wire:click="requestDeleteKelasConfirm('{{ $kItem->id }}')" title="Hapus"
                                class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                        @endcan
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-sm">Belum ada data kelas madrasah.</div>
            @endforelse
            <div class="pt-1">{{ $kelasList->links() }}</div>
        </div>
    @endif

    {{-- SUB-TAB 4: BAGAN KOMPLEK & KAMAR --}}
    @if($activeTab === 'bagan-komplek')
        <div class="space-y-6">
            @forelse($baganKomplekData as $dormitory)
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold {{ $dormitory->gender === 'L' ? 'bg-blue-100 text-blue-600 dark:bg-blue-950 dark:text-blue-400' : 'bg-pink-100 text-pink-600 dark:bg-pink-950 dark:text-pink-400' }}">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">{{ $dormitory->name }}</h2>
                                <span class="text-xs text-slate-500">Gender: {{ $dormitory->gender === 'L' ? 'Putra' : 'Putri' }}</span>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">{{ $dormitory->rooms->count() }} Kamar</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($dormitory->rooms as $room)
                            @php
                                $occupants = $room->currentAssignments;
                                $count = $occupants->count();
                            @endphp
                            <div class="bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 p-4 space-y-3">
                                <div class="flex items-center justify-between pb-2 border-b border-slate-200/40 dark:border-slate-700/40">
                                    <div>
                                        <h4 class="font-bold text-slate-800 dark:text-slate-100 text-sm">{{ $room->name }}</h4>
                                        @if($count > 0)
                                            <button type="button" wire:click="selectAllInRoom('{{ $room->id }}')" class="text-[10px] font-bold text-emerald-600 hover:underline">
                                                Pilih Semua Santri di Kamar Ini
                                            </button>
                                        @endif
                                    </div>
                                    <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ $count >= $room->capacity ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' }}">
                                        {{ $count }} / {{ $room->capacity }} Bed
                                    </span>
                                </div>

                                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                                    @forelse($occupants as $assignment)
                                        @php $sPerson = $assignment->person; @endphp
                                        <div class="flex items-center justify-between bg-white dark:bg-slate-900 p-2 rounded-xl border border-slate-100 dark:border-slate-800 text-xs">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <input type="checkbox" wire:model.live="selectedSantriIds" value="{{ $sPerson->id }}" class="rounded text-emerald-600 focus:ring-emerald-500">
                                                <div class="min-w-0">
                                                    <div class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $sPerson->name }}</div>
                                                    <div class="text-[10px] text-slate-400">NIS: {{ $sPerson->santriProfile->nis ?? '-' }}</div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                @canany(['change-enrollment-status', 'change-presence-status'])
                                                <button type="button" wire:click="openStatusModal('{{ $sPerson->id }}')" title="Ubah Status (Mukim/Laju/Boyong)"
                                                    class="px-2 py-1 bg-amber-50 hover:bg-amber-100 text-amber-600 dark:bg-amber-950 dark:text-amber-400 text-[10px] font-bold rounded-lg transition-colors">
                                                    Status
                                                </button>
                                                @endcanany
                                                @can('manage-kamar')
                                                <button type="button" wire:click="openTransferRoomModal('{{ $sPerson->id }}')" title="Pindah Kamar Instan"
                                                    class="px-2 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400 text-[10px] font-bold rounded-lg transition-colors">
                                                    Pindah
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
    @endif

    {{-- SUB-TAB 5: BAGAN KELAS MADRASAH --}}
    @if($activeTab === 'bagan-kelas')
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($baganKelasData as $kelas)
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                {{ strtoupper($kelas->jenjang) }}
                            </span>
                            <h3 class="font-extrabold text-slate-900 dark:text-slate-100 text-base mt-1">{{ $kelas->name }}</h3>
                            @if($kelas->enrollments->count() > 0)
                                <button type="button" wire:click="selectAllInKelas('{{ $kelas->id }}')" class="text-[10px] font-bold text-indigo-600 hover:underline">
                                    Pilih Semua Santri di Kelas Ini
                                </button>
                            @endif
                        </div>
                        <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl">
                            {{ $kelas->enrollments->count() }} Santri
                        </span>
                    </div>

                    <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                        @forelse($kelas->enrollments as $enrollment)
                            @php $kPerson = $enrollment->person; @endphp
                            <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-800/40 p-2.5 rounded-xl border border-slate-100 dark:border-slate-700/40 text-xs">
                                <div class="flex items-center gap-2 min-w-0">
                                    <input type="checkbox" wire:model.live="selectedSantriIds" value="{{ $kPerson->id }}" class="rounded text-indigo-600 focus:ring-indigo-500">
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $kPerson->name }}</div>
                                        <div class="text-[10px] text-slate-400">
                                            Komplek: {{ $kPerson->roomAssignments->first()?->room?->dormitory?->name ?? 'Non-Asrama' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    @canany(['change-enrollment-status', 'change-presence-status'])
                                    <button type="button" wire:click="openStatusModal('{{ $kPerson->id }}')" title="Ubah Status (Mukim/Laju/Boyong)"
                                        class="px-2 py-1 bg-amber-50 hover:bg-amber-100 text-amber-600 dark:bg-amber-950 dark:text-amber-400 text-[10px] font-bold rounded-lg transition-colors">
                                        Status
                                    </button>
                                    @endcanany
                                    @can('manage-kelas')
                                    <button type="button" wire:click="openTransferKelasModal('{{ $kPerson->id }}')" title="Pindah Kelas Instan"
                                        class="px-2 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400 text-[10px] font-bold rounded-lg transition-colors">
                                        Pindah
                                    </button>
                                    @endcan
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400 text-xs italic">Belum ada santri terdaftar.</div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="col-span-full p-12 text-center text-slate-400">Tidak ada data kelas madrasah.</div>
            @endforelse
        </div>
    @endif

    {{-- FLOATING SELECTION BAR — hanya tampil jika user punya permission transfer --}}
    @if(count($selectedSantriIds) > 0)
        @canany(['manage-kamar', 'manage-kelas'])
        <div class="fixed bottom-4 sm:bottom-6 left-1/2 -translate-x-1/2 z-40 w-[calc(100%-2rem)] sm:w-auto bg-slate-900 text-white px-4 sm:px-6 py-3 sm:py-3.5 rounded-2xl shadow-2xl border border-slate-700">
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center font-extrabold text-xs">
                        {{ count($selectedSantriIds) }}
                    </span>
                    <span class="text-xs font-bold">Santri Terpilih</span>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    @if($activeTab === 'bagan-komplek')
                        @can('manage-kamar')
                        <button type="button" wire:click="openBulkTransferRoomModal"
                            class="flex-1 sm:flex-none px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow transition-all">
                            Pindahkan ke Kamar Lain
                        </button>
                        @endcan
                    @elseif($activeTab === 'bagan-kelas')
                        @can('manage-kelas')
                        <button type="button" wire:click="openBulkTransferKelasModal"
                            class="flex-1 sm:flex-none px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow transition-all">
                        Pindahkan Sekaligus ke Kelas Lain
                    </button>
                    @endcan
                @endif

                <button type="button" wire:click="clearSelection" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl">
                    Batal
                </button>
            </div>
        </div>
        @endcanany
    @endif

    {{-- MODAL UBAH STATUS SANTRI (MUKIM / LAJU / BOYONG) --}}
    @if($showStatusModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 max-h-[85vh] sm:max-h-[90vh] overflow-y-auto my-auto">
                <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">Ubah Status Santri</h3>
                <p class="text-xs text-slate-500">Mengatur status keberadaan atau keanggotaan untuk <strong>{{ $statusSantriName }}</strong>:</p>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Status Keberadaan (Daily Presence)</label>
                        <select wire:model="targetPresenceStatus" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            <option value="mukim">Mukim (Tinggal di Asrama)</option>
                            <option value="laju">Laju (Non-Asrama / Pulang Pergi)</option>
                            <option value="izin">Izin</option>
                            <option value="pulang">Pulang Temporary</option>
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">Jika diubah ke 'Laju', alokasi kamar aktif santri ini akan otomatis di-nonaktifkan.</p>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Status Keanggotaan (Enrollment Status)</label>
                        <select wire:model="targetEnrollmentStatus" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            <option value="aktif">Aktif (Masih Terdaftar)</option>
                            <option value="boyong">Boyong / Keluar Pondok &amp; Madrasah</option>
                            <option value="alumni">Alumni / Lulus</option>
                            <option value="keluar_resmi">Keluar Resmi</option>
                            <option value="dikeluarkan">Dikeluarkan</option>
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">Jika diubah ke 'Boyong', alokasi kamar &amp; kelas santri ini akan otomatis di-nonaktifkan.</p>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Catatan / Alasan (Opsional)</label>
                        <input type="text" wire:model="statusChangeNotes" placeholder="Alasan perubahan status..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3">
                    <button type="button" wire:click="$set('showStatusModal', false)" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
                    <button type="button" wire:click="requestStatusChangeConfirm" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs shadow transition-all">Simpan Perubahan Status</button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL PENDAFTARAN SANTRI BARU SEWAKTU-WAKTU --}}
    {{-- MODAL PENDAFTARAN SANTRI BARU SUPER LENGKAP --}}
    @if($showNewSantriModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">Pendaftaran Santri Baru</h3>
                        <p class="text-xs text-slate-400">Pengisian biodata lengkap santri baru, wali, penempatan &amp; rincian tagihan</p>
                    </div>
                    <button type="button" wire:click="$set('showNewSantriModal', false)" class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-4 text-xs">
                    {{-- SEKSI 1: DATA UTAMA SANTRI --}}
                    <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 space-y-3">
                        <h4 class="font-extrabold text-xs text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">1. Data Pribadi Santri</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Nama Lengkap Santri <span class="text-rose-500">*</span></label>
                                <input type="text" wire:model="newSantriName" placeholder="Nama Lengkap Santri" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">NIK (16 Digit)</label>
                                <input type="text" wire:model="newSantriNik" placeholder="Nomor Induk Kependudukan" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Gender <span class="text-rose-500">*</span></label>
                                <select wire:model.live="newSantriGender" {{ $isGenderLocked ? 'disabled' : '' }} class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                                    <option value="L">Putra (L)</option>
                                    <option value="P">Putri (P)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Tempat Lahir</label>
                                <input type="text" wire:model="newSantriPob" placeholder="Kota / Kabupaten Lahir" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Tanggal Lahir</label>
                                <input type="date" wire:model="newSantriDob" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">No. HP / WA Santri</label>
                                <input type="text" wire:model="newSantriPhone" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Golongan Darah</label>
                                <select wire:model="newSantriBloodType" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                                    <option value="">-- Pilih --</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="AB">AB</option>
                                    <option value="O">O</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Sekolah Formal / Luar</label>
                                <input type="text" wire:model="newSantriFormalSchool" placeholder="SMPN 1 / SMAN 2" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Tingkat / Kelas Formal</label>
                                <input type="text" wire:model="newSantriFormalGrade" placeholder="Kelas 7 / 1 SMP" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            </div>
                        </div>
                    </div>

                    {{-- SEKSI 2: DATA ORANG TUA / WALI --}}
                    <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 space-y-3">
                        <h4 class="font-extrabold text-xs text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">2. Data Orang Tua / Wali Kandung</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Nama Ayah Kandung</label>
                                <input type="text" wire:model="newSantriFatherName" placeholder="Nama Ayah" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">No. HP / WA Ayah</label>
                                <input type="text" wire:model="newSantriFatherPhone" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Pekerjaan Ayah</label>
                                <input type="text" wire:model="newSantriFatherJob" placeholder="Pekerjaan Ayah" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Alamat Lengkap Ayah Kandung</label>
                            <input type="text" wire:model.live="newSantriFatherAddress" placeholder="Jalan, RT/RW, Desa, Kecamatan, Kabupaten/Kota" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Nama Ibu Kandung</label>
                                <input type="text" wire:model="newSantriMotherName" placeholder="Nama Ibu" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">No. HP / WA Ibu</label>
                                <input type="text" wire:model="newSantriMotherPhone" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Pekerjaan Ibu</label>
                                <input type="text" wire:model="newSantriMotherJob" placeholder="Pekerjaan Ibu" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="font-bold text-slate-600 dark:text-slate-300">Alamat Lengkap Ibu Kandung</label>
                                <label class="inline-flex items-center gap-1.5 text-[11px] font-bold text-emerald-600 cursor-pointer">
                                    <input type="checkbox" wire:model.live="sameMotherAddress" class="rounded text-emerald-600 focus:ring-emerald-500">
                                    <span>Alamat Ibu sama dengan Ayah</span>
                                </label>
                            </div>
                            <input type="text" wire:model="newSantriMotherAddress" {{ $sameMotherAddress ? 'readonly' : '' }} placeholder="Jalan, RT/RW, Desa, Kecamatan, Kabupaten/Kota" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 {{ $sameMotherAddress ? 'opacity-70 cursor-not-allowed bg-slate-100 dark:bg-slate-800' : '' }}">
                        </div>

                        {{-- Data Wali Santri (Non-Orang Tua / Opsional) --}}
                        <div class="pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                            <label class="block font-extrabold text-xs text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-2">👤 Data Wali Santri (Non-Orang Tua / Opsional)</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Nama Wali</label>
                                    <input type="text" wire:model="newSantriGuardianName" placeholder="Nama Wali (jika tidak bersama orang tua)" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">No. HP / WA Wali</label>
                                    <input type="text" wire:model="newSantriGuardianPhone" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Hubungan Wali</label>
                                    <input type="text" wire:model="newSantriGuardianRelationship" placeholder="Contoh: Paman, Kakek, Kakak, Wali" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SEKSI 3: JALUR MASUK & ALOKASI PENEMPATAN --}}
                    <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 space-y-3">
                        <h4 class="font-extrabold text-xs text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">3. Jalur Masuk &amp; Penempatan Kelas / Kamar</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Jalur Pendaftaran / Penempatan</label>
                                <select wire:model="newSantriEntryPath" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                                    <option value="reguler">🔰 Reguler (Kelas Awal)</option>
                                    <option value="tes_placement">⚡ Hasil Tes Muhafadzah / Loncat Kelas</option>
                                    <option value="pindahan">🔄 Pindahan dari Pondok Lain</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Status Keberadaan Awal</label>
                                <select wire:model.live="newSantriPresence" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                                    <option value="mukim">Mukim (Tinggal di Asrama)</option>
                                    <option value="laju">Laju (Non-Asrama)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @if($newSantriPresence === 'mukim')
                                <div>
                                    <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Pilih Kamar &amp; Komplek Asrama</label>
                                    <select wire:model="newSantriRoomId" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                                        <option value="">-- Pilih Kamar --</option>
                                        @foreach($roomOptions as $rOpt)
                                            <option value="{{ $rOpt->id }}">{{ $rOpt->dormitory->name }} — {{ $rOpt->name }} (Sisa: {{ $rOpt->capacity - $rOpt->currentAssignments->count() }} Bed)</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Pilih Kelas Madrasah Diniyyah</label>
                                <select wire:model.live="newSantriKelasId" class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelasOptions as $kOpt)
                                        <option value="{{ $kOpt->id }}">{{ strtoupper($kOpt->jenjang) }} — {{ $kOpt->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- SEKSI 4: INTERACTIVE BILLING CHECKLIST --}}
                    <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="font-extrabold text-xs text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">4. Rincian Paket Tagihan Registrasi (Checklist)</h4>
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="genBill" wire:model.live="generateBillPackage" class="rounded text-emerald-600 focus:ring-emerald-500">
                                <label for="genBill" class="font-bold text-xs text-slate-700 dark:text-slate-300 cursor-pointer">
                                    Buat Tagihan Registrasi
                                </label>
                            </div>
                        </div>

                        @if($generateBillPackage)
                            <div class="space-y-2 pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                                <p class="text-[11px] text-slate-500">Centang / hapus centang item tagihan yang akan dimasukkan ke paket registrasi santri ini:</p>
                                
                                <div class="space-y-1.5 bg-white dark:bg-slate-900 p-3 rounded-xl border border-slate-200/60 dark:border-slate-700/60 max-h-48 overflow-y-auto">
                                    @foreach($billingChecklist as $index => $item)
                                        <div class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                            <label class="flex items-center gap-2.5 cursor-pointer font-medium text-slate-800 dark:text-slate-200">
                                                <input type="checkbox" wire:click="toggleBillingItem({{ $index }})" {{ !empty($item['checked']) ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500">
                                                <span>{{ $item['label'] }}</span>
                                            </label>
                                            <span class="font-mono font-extrabold text-emerald-600 dark:text-emerald-400">
                                                Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Total Calculation Bar --}}
                                <div class="bg-emerald-50 dark:bg-emerald-950/40 p-3 rounded-xl border border-emerald-200/50 dark:border-emerald-800/50 flex items-center justify-between">
                                    <span class="font-extrabold text-emerald-900 dark:text-emerald-200 text-xs">Total Tagihan Registrasi Awal:</span>
                                    <span class="font-mono font-black text-emerald-700 dark:text-emerald-300 text-base">
                                        Rp {{ number_format($this->totalRegistrationBill, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" wire:click="$set('showNewSantriModal', false)" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors">Batal</button>
                    <button type="button" wire:click="registerNewSantri" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-extrabold rounded-xl text-xs shadow-lg transition-all">Daftarkan Santri Baru</button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL BULK TRANSFER KAMAR --}}
    @if($showBulkTransferRoomModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-5 sm:p-6 shadow-2xl space-y-3.5 max-h-[85vh] sm:max-h-[90vh] overflow-y-auto my-auto">
                <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">Pemindahan Massal (Bulk Transfer) Kamar</h3>
                <p class="text-xs text-slate-500">
                    Memindahkan <strong>{{ count($selectedSantriIds) }} santri terpilih</strong> sekaligus ke kamar tujuan baru:
                </p>

                <div class="bg-slate-50 dark:bg-slate-800/50 p-2.5 rounded-xl max-h-28 overflow-y-auto space-y-1 border border-slate-200/50 dark:border-slate-700/50">
                    @foreach($selectedSantriList as $sItem)
                        <div class="text-xs font-semibold text-slate-700 dark:text-slate-300 flex items-center justify-between">
                            <span>• {{ $sItem->name }}</span>
                            <span class="text-[10px] text-slate-400">NIS: {{ $sItem->santriProfile->nis ?? '-' }}</span>
                        </div>
                    @endforeach
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Pilih Komplek &amp; Kamar Tujuan</label>
                    <select wire:model="bulkTargetRoomId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                        <option value="">-- Pilih Kamar Tujuan --</option>
                        @foreach($roomOptions as $rOpt)
                            <option value="{{ $rOpt->id }}">{{ $rOpt->dormitory->name }} — {{ $rOpt->name }} (Sisa: {{ $rOpt->capacity - $rOpt->currentAssignments->count() }} Bed)</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" wire:click="$set('showBulkTransferRoomModal', false)" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
                    <button type="button" wire:click="requestBulkRoomTransferConfirm" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow transition-all">Simpan &amp; Pindahkan</button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL BULK TRANSFER KELAS --}}
    @if($showBulkTransferKelasModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-5 sm:p-6 shadow-2xl space-y-3.5 max-h-[85vh] sm:max-h-[90vh] overflow-y-auto my-auto">
                <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">Pemindahan Massal (Bulk Transfer) Kelas</h3>
                <p class="text-xs text-slate-500">
                    Memindahkan <strong>{{ count($selectedSantriIds) }} santri terpilih</strong> sekaligus ke kelas madrasah tujuan:
                </p>

                <div class="bg-slate-50 dark:bg-slate-800/50 p-2.5 rounded-xl max-h-28 overflow-y-auto space-y-1 border border-slate-200/50 dark:border-slate-700/50">
                    @foreach($selectedSantriList as $sItem)
                        <div class="text-xs font-semibold text-slate-700 dark:text-slate-300 flex items-center justify-between">
                            <span>• {{ $sItem->name }}</span>
                            <span class="text-[10px] text-slate-400">NIS: {{ $sItem->santriProfile->nis ?? '-' }}</span>
                        </div>
                    @endforeach
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Pilih Kelas Tujuan</label>
                    <select wire:model="bulkTargetKelasId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                        <option value="">-- Pilih Kelas Tujuan --</option>
                        @foreach($kelasOptions as $kOpt)
                            <option value="{{ $kOpt->id }}">{{ strtoupper($kOpt->jenjang) }} — {{ $kOpt->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" wire:click="$set('showBulkTransferKelasModal', false)" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
                    <button type="button" wire:click="requestBulkKelasTransferConfirm" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs shadow transition-all">Simpan &amp; Pindahkan</button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL CRUD KOMPLEK --}}
    @if($showDormitoryModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 max-h-[85vh] sm:max-h-[90vh] overflow-y-auto my-auto">
                <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">
                    {{ $editingDormitoryId ? 'Edit Data Komplek' : 'Tambah Komplek Baru' }}
                </h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Nama Komplek</label>
                        <input type="text" wire:model="dormitoryName" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Gender</label>
                        <select wire:model="dormitoryGender" {{ $isGenderLocked ? 'disabled' : '' }} class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            <option value="L">Putra (L)</option>
                            <option value="P">Putri (P)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Nominal Kas Komplek (Rp)</label>
                        <input type="number" wire:model="dormitoryKasAmount" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Keterangan / Catatan</label>
                        <textarea wire:model="dormitoryDesc" rows="2" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3">
                    <button type="button" wire:click="$set('showDormitoryModal', false)" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
                    <button type="button" wire:click="saveDormitory" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl text-xs shadow">Simpan</button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL CRUD KAMAR --}}
    @if($showRoomModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 max-h-[85vh] sm:max-h-[90vh] overflow-y-auto my-auto">
                <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">
                    {{ $editingRoomId ? 'Edit Data Kamar' : 'Tambah Kamar Baru' }}
                </h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Pilih Komplek</label>
                        <select wire:model="targetDormitoryId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            @foreach($dormitoryOptions as $dOpt)
                                <option value="{{ $dOpt->id }}">{{ $dOpt->name }} ({{ $dOpt->gender }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Nama Kamar</label>
                        <input type="text" wire:model="roomName" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Kapasitas Bed (Jumlah Kasur)</label>
                        <input type="number" wire:model="roomCapacity" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3">
                    <button type="button" wire:click="$set('showRoomModal', false)" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
                    <button type="button" wire:click="saveRoom" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl text-xs shadow">Simpan</button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL CRUD KELAS --}}
    @if($showKelasModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 max-h-[85vh] sm:max-h-[90vh] overflow-y-auto my-auto">
                <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">
                    {{ $editingKelasId ? 'Edit Data Kelas' : 'Tambah Kelas Baru' }}
                </h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Jenjang</label>
                        <select wire:model="formJenjang" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            <option value="ula">ULA</option>
                            <option value="wustho">WUSTHO</option>
                            <option value="ulya">ULYA</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Nama Kelas</label>
                        <input type="text" wire:model="formName" placeholder="Contoh: Awaliyah 1A" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Wali Kelas</label>
                        <select wire:model="formWaliKelasId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            <option value="">-- Pilih Guru / Wali Kelas --</option>
                            @foreach($guruOptions as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Tahun Ajaran</label>
                        <input type="text" wire:model="formAcademicYear" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3">
                    <button type="button" wire:click="$set('showKelasModal', false)" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
                    <button type="button" wire:click="saveKelas" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-xl text-xs shadow">Simpan</button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL SINGLE TRANSFER KAMAR --}}
    @if($showTransferRoomModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 max-h-[85vh] sm:max-h-[90vh] overflow-y-auto my-auto">
                <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">Pindah Kamar / Komplek Instan</h3>
                <p class="text-xs text-slate-500">Pilih kamar &amp; komplek tujuan untuk <strong>{{ $transferSantriName }}</strong>:</p>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Tujuan Komplek &amp; Kamar</label>
                    <select wire:model="targetRoomId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                        <option value="">-- Pilih Kamar Tujuan --</option>
                        @foreach($roomOptions as $rOpt)
                            <option value="{{ $rOpt->id }}">{{ $rOpt->dormitory->name }} — {{ $rOpt->name }} (Sisa: {{ $rOpt->capacity - $rOpt->currentAssignments->count() }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3">
                    <button type="button" wire:click="$set('showTransferRoomModal', false)" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
                    <button type="button" wire:click="requestTransferRoomConfirm" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow transition-all">Simpan &amp; Pindahkan</button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL SINGLE TRANSFER KELAS --}}
    @if($showTransferKelasModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 max-h-[85vh] sm:max-h-[90vh] overflow-y-auto my-auto">
                <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">Pindah Kelas Madrasah Instan</h3>
                <p class="text-xs text-slate-500">Pilih kelas madrasah tujuan untuk <strong>{{ $transferKelasSantriName }}</strong>:</p>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Kelas Tujuan</label>
                    <select wire:model="targetKelasId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                        <option value="">-- Pilih Kelas Tujuan --</option>
                        @foreach($kelasOptions as $kOpt)
                            <option value="{{ $kOpt->id }}">{{ strtoupper($kOpt->jenjang) }} — {{ $kOpt->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3">
                    <button type="button" wire:click="$set('showTransferKelasModal', false)" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
                    <button type="button" wire:click="requestTransferKelasConfirm" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs shadow transition-all">Simpan &amp; Pindahkan</button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL KONFIRMASI KOSTUM ELEGAN (NO BROWSER ALERT) --}}
    @if($showConfirmModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md overflow-y-auto">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 max-h-[85vh] sm:max-h-[90vh] overflow-y-auto my-auto">
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
</div>
