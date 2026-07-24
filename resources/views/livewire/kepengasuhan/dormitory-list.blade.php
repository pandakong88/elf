<div class="space-y-6">
    {{-- ============================================================ --}}
    {{-- Header Page                                                  --}}
    {{-- ============================================================ --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">Manajemen Asrama &amp; Kamar</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Peta kapasitas hunian kamar santri, pengelolaan gedung, dan penempatan santri.</p>
        </div>
        <button type="button" wire:click="openCreateDormitoryModal"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-br from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 active:from-emerald-600 active:to-teal-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/20 transition-all text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Gedung Asrama</span>
        </button>
    </div>

    {{-- ============================================================ --}}
    {{-- Filters Section                                              --}}
    {{-- ============================================================ --}}
    <div class="flex flex-col sm:flex-row gap-4 bg-white dark:bg-slate-900 p-5 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm">
        <div class="flex-1">
            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Pencarian</label>
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama asrama atau kamar..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                <div class="absolute left-3.5 top-3 text-slate-400 dark:text-slate-500">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="w-full sm:w-52">
            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Filter Gender</label>
            <select wire:model.live="genderFilter" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                <option value="">Semua Gender</option>
                <option value="L">Putra (L)</option>
                <option value="P">Putri (P)</option>
            </select>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Alerts                                                       --}}
    {{-- ============================================================ --}}
    {{-- ============================================================ --}}
    {{-- Main Content: Dormitory Grid + Waiting List Sidebar          --}}
    {{-- ============================================================ --}}
    <div class="flex flex-col xl:flex-row gap-6 items-start">

        {{-- Dormitory Cards Grid --}}
        <div class="flex-1 min-w-0">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @forelse ($dormitories as $dormitory)
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                        {{-- Card Header --}}
                        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center {{ $dormitory->gender === 'L' ? 'bg-blue-100 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400' : 'bg-pink-100 dark:bg-pink-950/40 text-pink-600 dark:text-pink-400' }}">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">{{ $dormitory->name }}</h3>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] font-semibold uppercase tracking-wider {{ $dormitory->gender === 'L' ? 'text-blue-500' : 'text-pink-500' }}">
                                            {{ $dormitory->gender === 'L' ? 'Putra' : 'Putri' }}
                                        </span>
                                        @if(!$dormitory->is_active)
                                            <span class="text-[10px] font-bold text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded">Nonaktif</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- Action Buttons --}}
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <button type="button" wire:click="openCreateRoomModal('{{ $dormitory->id }}')" title="Tambah Kamar"
                                    class="p-1.5 rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/30 dark:hover:bg-emerald-900/40 dark:text-emerald-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                </button>
                                <button type="button" wire:click="openEditDormitoryModal('{{ $dormitory->id }}')" title="Edit Asrama"
                                    class="p-1.5 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-300 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button type="button" wire:click="confirmToggleDormitoryStatus('{{ $dormitory->id }}')" title="{{ $dormitory->is_active ? 'Nonaktifkan' : 'Aktifkan' }} Asrama"
                                    class="p-1.5 rounded-lg transition-colors {{ $dormitory->is_active ? 'text-amber-500 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/30 dark:hover:bg-amber-900/40' : 'text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Room List --}}
                        <div class="p-4 space-y-3">
                            @if($dormitory->rooms->isEmpty())
                                <div class="text-center py-8 text-slate-400 dark:text-slate-500 text-sm">
                                    <svg class="w-8 h-8 mx-auto mb-2 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    Belum ada kamar. <button wire:click="openCreateRoomModal('{{ $dormitory->id }}')" class="text-emerald-500 font-semibold underline">Tambah kamar</button>
                                </div>
                            @else
                                @foreach ($dormitory->rooms as $room)
                                    @php
                                        $occupantsCount = $room->currentAssignments->count();
                                        $percent = $room->capacity > 0 ? min(100, round(($occupantsCount / $room->capacity) * 100)) : 0;
                                        $barColor = 'bg-emerald-500';
                                        if ($percent >= 100) { $barColor = 'bg-rose-500'; }
                                        elseif ($percent >= 75) { $barColor = 'bg-amber-500'; }
                                    @endphp
                                    <div class="p-3.5 bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800 rounded-xl space-y-3 {{ !$room->is_active ? 'opacity-50' : '' }}">
                                        {{-- Room Header --}}
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <h4 class="font-bold text-sm text-slate-800 dark:text-slate-200 truncate">{{ $room->name }}</h4>
                                                    @if(!$room->is_active)
                                                        <span class="text-[9px] font-bold text-slate-400 bg-slate-200 dark:bg-slate-700 px-1.5 py-0.5 rounded flex-shrink-0">OFF</span>
                                                    @endif
                                                </div>
                                                <span class="text-[11px] text-slate-400 dark:text-slate-500">{{ $room->description ?: 'Tidak ada deskripsi' }}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                                <span class="text-xs font-bold {{ $percent >= 100 ? 'text-rose-600' : 'text-slate-700 dark:text-slate-300' }}">{{ $occupantsCount }}/{{ $room->capacity }}</span>
                                                <button type="button" wire:click="openEditRoomModal('{{ $room->id }}')" title="Edit Kamar"
                                                    class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200 dark:hover:bg-slate-700 dark:hover:text-slate-200 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Capacity Bar --}}
                                        <div class="w-full bg-slate-200 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                            <div class="h-full {{ $barColor }} transition-all duration-700 rounded-full" style="width: {{ $percent }}%"></div>
                                        </div>

                                        {{-- Occupants --}}
                                        <div>
                                            <div class="flex flex-wrap gap-1.5">
                                                @forelse ($room->currentAssignments as $assignment)
                                                    <span class="inline-flex items-center gap-1 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 pl-2 pr-1 py-0.5 rounded-lg text-xs text-slate-700 dark:text-slate-300 group">
                                                        <button type="button" wire:click="openStatusModal('{{ $assignment->person->id }}')" title="Kelola Status &amp; Riwayat" class="hover:text-emerald-500 hover:underline text-left">
                                                            {{ $assignment->person->name }}
                                                        </button>
                                                        <button type="button" wire:click="confirmUnassignSantri('{{ $assignment->id }}')" title="Keluarkan dari kamar"
                                                            class="p-0.5 rounded text-slate-300 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-all opacity-0 group-hover:opacity-100">
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </span>
                                                @empty
                                                    <span class="text-xs text-slate-400 dark:text-slate-500 italic">Kamar kosong</span>
                                                @endforelse
                                            </div>
                                        </div>

                                        {{-- Room Action Button --}}
                                        @if($room->is_active && $occupantsCount < $room->capacity)
                                            <div class="pt-1">
                                                <button type="button" wire:click="openAssignModal('{{ $room->id }}')"
                                                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-emerald-400 hover:bg-emerald-50/50 dark:hover:border-emerald-600 dark:hover:bg-emerald-950/20 text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-lg text-xs font-semibold transition-all">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                    Tempatkan Santri
                                                </button>
                                            </div>
                                        @elseif($percent >= 100)
                                            <div class="pt-1">
                                                <div class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/50 text-rose-500 dark:text-rose-400 rounded-lg text-xs font-semibold">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                    Kamar Penuh
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 text-center py-16 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 text-slate-400 dark:text-slate-500 shadow-sm">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <p class="font-semibold">Gedung asrama tidak ditemukan.</p>
                        <p class="text-sm mt-1">Ubah filter atau <button wire:click="openCreateDormitoryModal" class="text-emerald-500 underline">tambah asrama baru</button>.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $dormitories->links() }}
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- Waiting List Sidebar (hanya tampil jika ada filter gender)    --}}
        {{-- ============================================================ --}}
        @if($genderFilter)
            <div class="w-full xl:w-80 flex-shrink-0 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden sticky top-6">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Waiting List</h3>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Santri belum memiliki kamar</p>
                    </div>
                    @if(count($waitingList) > 0)
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-100 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 text-xs font-bold">{{ count($waitingList) }}</span>
                    @endif
                </div>

                <div class="p-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="searchWaiting" placeholder="Cari santri..."
                               class="w-full pl-8 pr-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all text-xs">
                        <div class="absolute left-2.5 top-2.5 text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>
                </div>

                <div class="overflow-y-auto max-h-[calc(100vh-18rem)] divide-y divide-slate-50 dark:divide-slate-800/80">
                    @forelse($waitingList as $santri)
                        <div class="px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <div class="flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <button type="button" wire:click="openStatusModal('{{ $santri->id }}')" title="Kelola Status &amp; Riwayat" class="hover:text-emerald-500 hover:underline text-sm font-semibold text-slate-800 dark:text-slate-200 truncate text-left">
                                        {{ $santri->name }}
                                    </button>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5 uppercase tracking-wider">{{ $santri->nik ?? '-' }}</p>
                                </div>
                                <button type="button" wire:click="openAssignModal('{{ null }}')"
                                    onclick="event.preventDefault()"
                                    title="Tempatkan ke Kamar"
                                    class="p-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white flex-shrink-0 transition-all shadow-sm shadow-emerald-500/30">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center text-slate-400 dark:text-slate-500 text-xs">
                            <svg class="w-8 h-8 mx-auto mb-2 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="font-semibold">Semua santri sudah memiliki kamar</p>
                        </div>
                    @endforelse
                </div>

                @if(!$genderFilter)
                    <div class="p-4 text-center text-xs text-slate-400 dark:text-slate-500 italic">
                        Pilih filter gender untuk melihat waiting list.
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- Modal: Tambah / Edit Asrama                                  --}}
    {{-- ============================================================ --}}
    @if($showDormitoryModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showDormitoryModal', false)">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ $editingDormitoryId ? 'Edit Asrama' : 'Tambah Asrama Baru' }}</h3>
                    <button type="button" wire:click="$set('showDormitoryModal', false)" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama Asrama / Gedung</label>
                        <input type="text" wire:model="dormitoryName" placeholder="cth: Komplek A Putra"
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                        @error('dormitoryName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Gender Asrama</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all {{ $dormitoryGender === 'L' ? 'border-blue-500 bg-blue-50 dark:bg-blue-950/30' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300' }}">
                                <input type="radio" wire:model.live="dormitoryGender" value="L" class="hidden">
                                <svg class="w-4 h-4 text-sky-500 mr-1 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <div>
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200">Putra</p>
                                    <p class="text-[10px] text-slate-400">Laki-laki</p>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all {{ $dormitoryGender === 'P' ? 'border-pink-500 bg-pink-50 dark:bg-pink-950/30' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300' }}">
                                <input type="radio" wire:model.live="dormitoryGender" value="P" class="hidden">
                                <svg class="w-4 h-4 text-pink-500 mr-1 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <div>
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200">Putri</p>
                                    <p class="text-[10px] text-slate-400">Perempuan</p>
                                </div>
                            </label>
                        </div>
                        @error('dormitoryGender') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Deskripsi (Opsional)</label>
                        <textarea wire:model="dormitoryDesc" rows="2" placeholder="Catatan tambahan tentang asrama ini..."
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm resize-none"></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" wire:click="$set('showDormitoryModal', false)" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-sm transition-colors">Batal</button>
                    <button type="button" wire:click="saveDormitory" class="px-5 py-2 bg-gradient-to-br from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-bold rounded-xl text-sm shadow-lg shadow-emerald-500/20 transition-all">Simpan</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- Modal: Tambah / Edit Kamar                                   --}}
    {{-- ============================================================ --}}
    @if($showRoomModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showRoomModal', false)">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">{{ $editingRoomId ? 'Edit Kamar' : 'Tambah Kamar Baru' }}</h3>
                    <button type="button" wire:click="$set('showRoomModal', false)" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama / Nomor Kamar</label>
                        <input type="text" wire:model="roomName" placeholder="cth: Kamar 1, Kamar A, dll"
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                        @error('roomName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Kapasitas (jumlah orang)</label>
                        <input type="number" wire:model="roomCapacity" min="1" max="100" placeholder="10"
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                        @error('roomCapacity') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Deskripsi (Opsional)</label>
                        <textarea wire:model="roomDesc" rows="2" placeholder="Catatan tambahan..."
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm resize-none"></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" wire:click="$set('showRoomModal', false)" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-sm transition-colors">Batal</button>
                    <button type="button" wire:click="saveRoom" class="px-5 py-2 bg-gradient-to-br from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-bold rounded-xl text-sm shadow-lg shadow-emerald-500/20 transition-all">Simpan</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- Modal: Assign Santri ke Kamar                                --}}
    {{-- ============================================================ --}}
    @if($showAssignModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-lg border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Pilih Santri</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Penempatan untuk: <span class="font-semibold text-slate-600 dark:text-slate-300">{{ $selectedRoomName }}</span></p>
                    </div>
                    <button type="button" wire:click="closeAssignModal" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="searchSantri" placeholder="Cari nama santri..."
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                        <div class="absolute left-3.5 top-3 text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>
                    <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                        @forelse ($santriList as $santri)
                            <div class="p-3 bg-slate-50 dark:bg-slate-950/40 hover:bg-slate-100/80 dark:hover:bg-slate-800/40 border border-slate-100 dark:border-slate-800 rounded-xl flex items-center justify-between transition-colors">
                                <div>
                                    <h4 class="font-bold text-sm text-slate-800 dark:text-slate-200">{{ $santri->name }}</h4>
                                    <span class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">NIK: {{ $santri->nik ?? '-' }}</span>
                                </div>
                                <button type="button" wire:click="assignSantri('{{ $santri->id }}')"
                                        class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-xs font-bold transition-all shadow-sm">
                                    Pilih
                                </button>
                            </div>
                        @empty
                            <div class="text-center py-8 text-slate-400 dark:text-slate-500 text-xs italic">
                                @if($searchSantri) Santri tidak ditemukan. @else Ketik nama santri untuk mencari... @endif
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <button type="button" wire:click="closeAssignModal" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-sm transition-colors">Tutup</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- Modal: Konfirmasi Kustom (Hapus / Toggle Status)             --}}
    {{-- ============================================================ --}}
    @if($showConfirmModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-sm border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden text-center p-8 space-y-5">
                <div class="w-16 h-16 bg-rose-100 dark:bg-rose-950/40 rounded-2xl flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100">{{ $confirmTitle }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">{!! $confirmMessage !!}</p>
                </div>
                <div class="flex gap-3">
                    <button type="button" wire:click="closeConfirmModal" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-sm transition-colors">Batal</button>
                    <button type="button" wire:click="executeConfirmAction" class="flex-1 px-4 py-2.5 bg-rose-500 hover:bg-rose-600 active:bg-rose-700 text-white font-bold rounded-xl text-sm shadow-lg shadow-rose-500/30 transition-all">Ya, Lanjutkan</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- Modal: Kelola Status Santri & Riwayat                         --}}
    {{-- ============================================================ --}}
    @if($showStatusModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="closeStatusModal">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-2xl border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Status &amp; Riwayat Keanggotaan</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Nama Santri: <span class="font-semibold text-slate-600 dark:text-slate-300">{{ $santriName }}</span></p>
                    </div>
                    <button type="button" wire:click="closeStatusModal" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Form Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Status Keanggotaan (Enrollment)</label>
                            @can('change-enrollment-status')
                                <select wire:model.live="currentEnrollmentStatus" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                    <option value="aktif">Aktif</option>
                                    <option value="alumni">Alumni</option>
                                    <option value="keluar_resmi">Keluar Resmi</option>
                                    <option value="dikeluarkan">Dikeluarkan</option>
                                    <option value="tanpa_keterangan">Tanpa Keterangan</option>
                                </select>
                            @else
                                <div class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 text-sm font-semibold">
                                    {{ $currentEnrollmentStatus }}
                                </div>
                            @endcan
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Status Keberadaan (Presence)</label>
                            @if ($currentEnrollmentStatus !== 'aktif')
                                <div class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-400 text-sm italic">
                                    Status Keberadaan dinonaktifkan (Keanggotaan Non-aktif).
                                </div>
                            @else
                                @can('change-presence-status')
                                    <select wire:model.live="currentPresenceStatus" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                        <option value="mukim">Mukim</option>
                                        <option value="laju">Laju</option>
                                        <option value="izin">Izin</option>
                                        <option value="alpa">Alpa</option>
                                    </select>
                                @else
                                    <div class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 text-sm font-semibold">
                                        {{ $currentPresenceStatus }}
                                    </div>
                                @endcan
                            @endif
                        </div>

                        @if ($currentEnrollmentStatus === 'aktif' && $currentPresenceStatus === 'izin')
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Sampai Tanggal (Perkiraan Kembali)</label>
                                <input type="date" wire:model="presenceUntil" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            </div>
                        @endif

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Alasan / Catatan Perubahan</label>
                            <textarea wire:model="statusNotes" rows="2" placeholder="Tulis catatan pendukung tentang perubahan status..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-2 border-b border-slate-100 dark:border-slate-800 pb-5">
                        <button type="button" wire:click="closeStatusModal" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors">Batal</button>
                        <button type="button" wire:click="updateStatus" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-500/20 transition-all">Simpan Perubahan</button>
                    </div>

                    <!-- History Logs -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Log Riwayat Perubahan Status</h4>
                        <div class="border border-slate-100 dark:border-slate-800/80 rounded-2xl overflow-hidden max-h-48 overflow-y-auto">
                            <table class="w-full text-left border-collapse text-[11px]">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 font-bold border-b border-slate-100 dark:border-slate-800">
                                        <th class="p-2">Tanggal</th>
                                        <th class="p-2">Bidang</th>
                                        <th class="p-2">Nilai Lama</th>
                                        <th class="p-2">Nilai Baru</th>
                                        <th class="p-2">Oleh</th>
                                        <th class="p-2">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                                    @forelse ($statusHistory as $log)
                                        <tr>
                                            <td class="p-2 whitespace-nowrap text-slate-400">{{ \Carbon\Carbon::parse($log['changed_at'])->format('d M Y H:i') }}</td>
                                            <td class="p-2 font-semibold">
                                                {{ $log['changed_field'] === 'enrollment_status' ? 'Keanggotaan' : 'Keberadaan' }}
                                            </td>
                                            <td class="p-2"><span class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded">{{ $log['old_value'] ?: '-' }}</span></td>
                                            <td class="p-2 font-bold"><span class="px-1.5 py-0.5 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 rounded">{{ $log['new_value'] }}</span></td>
                                            <td class="p-2">{{ $log['changed_by']['name'] ?? 'Sistem' }}</td>
                                            <td class="p-2 text-slate-400">{{ $log['notes'] ?: '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="p-4 text-center text-slate-450 italic">Belum ada riwayat perubahan status.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
