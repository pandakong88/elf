<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6"
     x-data="{
        draggedPersonId: null,
        draggedFromRoom: null,
        isDragging: false,
        dropTarget: null,
        handleDragStart(e, pid, fromRoom) {
            this.draggedPersonId = pid;
            this.draggedFromRoom = fromRoom;
            this.isDragging = true;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', JSON.stringify({ personId: pid, fromRoom: fromRoom }));
        },
        handleDragEnd() {
            this.isDragging = false;
            this.dropTarget = null;
        },
        handleDropOnRoom(e, roomId) {
            e.preventDefault();
            this.dropTarget = null;
            if (this.draggedPersonId) {
                $wire.moveSantri(this.draggedPersonId, 'room', roomId);
                this.draggedPersonId = null;
            }
        },
        handleDropOnDock(e, dockType) {
            e.preventDefault();
            this.dropTarget = null;
            if (this.draggedPersonId) {
                $wire.moveSantri(this.draggedPersonId, dockType);
                this.draggedPersonId = null;
            }
        }
     }">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('sensus.campaigns') }}" class="hover:underline">Pusat Kendali Sensus</a>
                <span>&rsaquo;</span>
                <span>Sensus Mutasi Santri</span>
                <span>&rsaquo;</span>
                <span class="text-slate-800 dark:text-slate-200">{{ $cd?->dormitory?->name }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                <span>Sensus Mutasi: {{ $cd?->dormitory?->name }}</span>
                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                    {{ $cd?->dormitory?->gender === 'L' ? 'Putra' : 'Putri' }}
                </span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Pindahkan santri dengan cara <strong>tarik &amp; lepas (drag and drop)</strong> ke kamar tujuan atau kotak transit status.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- View Mode Switcher -->
            <div class="inline-flex rounded-xl p-1 bg-slate-100 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700">
                <button
                    type="button"
                    wire:click="$set('viewMode', 'board')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ $viewMode === 'board' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-600 dark:text-slate-400' }}"
                >
                    <span>🗂️</span>
                    <span>Papan Kamar (Drag &amp; Drop)</span>
                </button>
                <button
                    type="button"
                    wire:click="$set('viewMode', 'table')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ $viewMode === 'table' ? 'bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 shadow-xs' : 'text-slate-600 dark:text-slate-400' }}"
                >
                    <span>📋</span>
                    <span>Mode Tabel</span>
                </button>
            </div>

            <a href="{{ route('sensus.campaigns') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-all">
                Kembali
            </a>
            <button type="button" wire:click="saveDraft" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-xs shadow-sm transition-all">
                Simpan Draf
            </button>
            <button type="button" wire:click="submitCensus" wire:confirm="Laporan sensus mutasi ini akan dikirim untuk direview oleh sekretaris. Lanjutkan?" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow-lg shadow-emerald-600/20 hover:-translate-y-0.5 transition-all">
                Kirim Laporan
            </button>
        </div>
    </div>

    <!-- Summary Chips Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
        <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500">Tetap di Kamar</span>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ $summary['tetap'] }}</div>
            </div>
            <span class="text-2xl">🏠</span>
        </div>
        <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500">Pindah Kamar</span>
                <div class="text-2xl font-black text-blue-600 mt-1">{{ $summary['pindah_kamar'] }}</div>
            </div>
            <span class="text-2xl">🔄</span>
        </div>
        <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500">Ubah Status (Laju/Boyong)</span>
                <div class="text-2xl font-black text-amber-500 mt-1">{{ $summary['ubah_status'] }}</div>
            </div>
            <span class="text-2xl">🚶</span>
        </div>
        <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500">Total Santri Terdata</span>
                <div class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $summary['total'] }}</div>
            </div>
            <span class="text-2xl">👥</span>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- VIEW 1: INTERACTIVE DRAG & DROP ROOM KANBAN BOARD                         --}}
    {{-- ========================================================================= --}}
    @if ($viewMode === 'board')
        <div class="space-y-6">
            <!-- Room Columns Grid -->
            <div>
                <div class="flex items-center justify-between mb-3 px-1">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                        <span>Papan Kamar Asrama</span>
                        <span class="text-[11px] font-normal lowercase text-slate-400">(Tarik kartu santri ke kamar tujuan)</span>
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 items-start">
                    @forelse ($boardData['rooms'] as $room)
                        @php
                            $occCount = count($room['occupants']);
                            $isOvercapacity = $room['capacity'] > 0 && $occCount > $room['capacity'];
                            $isFull = $room['capacity'] > 0 && $occCount === $room['capacity'];
                        @endphp
                        <div
                            class="bg-white dark:bg-slate-900 rounded-2xl border transition-all duration-200 overflow-hidden shadow-sm flex flex-col {{ $isOvercapacity ? 'border-rose-400 bg-rose-50/10 dark:border-rose-700/60' : ($isFull ? 'border-amber-300 dark:border-amber-700/50' : 'border-slate-200/80 dark:border-slate-800') }}"
                            :class="{ 'ring-2 ring-blue-500 ring-offset-2 dark:ring-offset-slate-900 bg-blue-50/30': dropTarget === '{{ $room['id'] }}' }"
                            @dragover.prevent="dropTarget = '{{ $room['id'] }}'"
                            @dragleave="if (dropTarget === '{{ $room['id'] }}') dropTarget = null"
                            @drop="handleDropOnRoom($event, '{{ $room['id'] }}')"
                        >
                            <!-- Room Header -->
                            <div class="p-3.5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/40">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full {{ $isOvercapacity ? 'bg-rose-500 animate-pulse' : ($isFull ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                                    <h4 class="font-extrabold text-sm text-slate-800 dark:text-slate-100">{{ $room['name'] }}</h4>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-black {{ $isOvercapacity ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' : ($isFull ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300') }}">
                                    {{ $occCount }}/{{ $room['capacity'] }}
                                </span>
                            </div>

                            @if ($isOvercapacity)
                                <div class="px-3 py-1 bg-rose-500 text-white text-[10px] font-bold text-center">
                                    ⚠️ Melebihi Kapasitas Kamar (+{{ $occCount - $room['capacity'] }})
                                </div>
                            @endif

                            <!-- Room Droppable Body -->
                            <div class="p-3 space-y-2 min-h-[140px] flex-1">
                                @forelse ($room['occupants'] as $person)
                                    <div
                                        draggable="true"
                                        @dragstart="handleDragStart($event, '{{ $person['person_id'] }}', '{{ $room['id'] }}')"
                                        @dragend="handleDragEnd()"
                                        class="p-2.5 rounded-xl border bg-white dark:bg-slate-800/90 shadow-xs cursor-grab active:cursor-grabbing hover:shadow-md transition-all select-none group {{ $person['is_moved'] ? 'border-blue-400 dark:border-blue-500/60 bg-blue-50/40 dark:bg-blue-950/30' : 'border-slate-200 dark:border-slate-700' }}"
                                    >
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="flex items-start gap-2 min-w-0">
                                                <div class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center font-bold text-slate-600 dark:text-slate-300 text-xs flex-shrink-0">
                                                    {{ strtoupper(substr($person['name'] ?? 'S', 0, 1)) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate" title="{{ $person['name'] }}">
                                                        {{ $person['name'] }}
                                                    </p>
                                                    <div class="text-[10px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                                        <span>NIS: {{ $person['nis'] ?? '-' }}</span>
                                                        <span>&bull;</span>
                                                        <span>{{ $person['class'] ?? 'Santri' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($person['is_moved'])
                                                <button
                                                    type="button"
                                                    wire:click="resetSantri('{{ $person['person_id'] }}')"
                                                    class="p-1 text-slate-400 hover:text-rose-500 rounded-md transition-colors"
                                                    title="Kembalikan ke kamar asal"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            @endif
                                        </div>

                                        @if ($person['is_moved'])
                                            <div class="mt-2 pt-1.5 border-t border-blue-200/60 dark:border-blue-800/40 flex items-center justify-between text-[10px]">
                                                <span class="text-blue-600 dark:text-blue-400 font-bold flex items-center gap-1">
                                                    <span>🔄</span> Pindah dari: {{ $person['original_room_name'] }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="h-28 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-xl flex flex-col items-center justify-center text-slate-400 text-xs text-center p-3">
                                        <span class="text-lg mb-1">📭</span>
                                        <span>Kamar Kosong</span>
                                        <span class="text-[10px] text-slate-400">Tarik santri ke sini</span>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full p-8 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 text-slate-400 text-sm">
                            Belum ada kamar aktif di asrama ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Transit Dock Section (Unassigned, Laju, Boyong) -->
            <div class="border-t border-slate-200 dark:border-slate-800 pt-6">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 px-1">
                    Dermaga Transit &amp; Perubahan Status Santri
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Dock 1: Santri Belum Punya Kamar (Unassigned) -->
                    <div
                        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 transition-all"
                        :class="{ 'ring-2 ring-amber-500 bg-amber-50/20': dropTarget === 'dock-unassigned' }"
                        @dragover.prevent="dropTarget = 'dock-unassigned'"
                        @dragleave="if (dropTarget === 'dock-unassigned') dropTarget = null"
                        @drop="handleDropOnDock($event, 'unassigned')"
                    >
                        <div class="flex items-center justify-between mb-3 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                            <div class="flex items-center gap-2">
                                <span class="text-base">📦</span>
                                <h4 class="font-bold text-xs text-slate-800 dark:text-slate-100">Santri Tanpa Kamar</h4>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                {{ count($boardData['dock_unassigned']) }}
                            </span>
                        </div>
                        <div class="space-y-2 min-h-[100px] max-h-60 overflow-y-auto pr-1">
                            @forelse ($boardData['dock_unassigned'] as $person)
                                <div
                                    draggable="true"
                                    @dragstart="handleDragStart($event, '{{ $person['person_id'] }}', 'unassigned')"
                                    @dragend="handleDragEnd()"
                                    class="p-2.5 rounded-xl border border-amber-200 dark:border-amber-900/50 bg-amber-50/40 dark:bg-slate-800/80 text-xs shadow-xs cursor-grab active:cursor-grabbing hover:shadow-md transition-all flex items-center justify-between"
                                >
                                    <div>
                                        <p class="font-bold text-slate-800 dark:text-slate-100 truncate">{{ $person['name'] }}</p>
                                        <p class="text-[10px] text-amber-600 dark:text-amber-400">Tarik ke kamar tujuan</p>
                                    </div>
                                    <span class="text-xs">👉</span>
                                </div>
                            @empty
                                <div class="h-20 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-center text-slate-400 text-xs text-center p-2">
                                    Semua santri sudah memiliki kamar.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Dock 2: Santri Laju (Pulang-Pergi) -->
                    <div
                        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 transition-all"
                        :class="{ 'ring-2 ring-blue-500 bg-blue-50/20': dropTarget === 'dock-laju' }"
                        @dragover.prevent="dropTarget = 'dock-laju'"
                        @dragleave="if (dropTarget === 'dock-laju') dropTarget = null"
                        @drop="handleDropOnDock($event, 'laju')"
                    >
                        <div class="flex items-center justify-between mb-3 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                            <div class="flex items-center gap-2">
                                <span class="text-base">🏠</span>
                                <h4 class="font-bold text-xs text-slate-800 dark:text-slate-100">Santri Laju (Pulang-Pergi)</h4>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                                {{ count($boardData['dock_laju']) }}
                            </span>
                        </div>
                        <div class="space-y-2 min-h-[100px] max-h-60 overflow-y-auto pr-1">
                            @forelse ($boardData['dock_laju'] as $person)
                                <div class="p-2.5 rounded-xl border border-blue-200 dark:border-blue-900/50 bg-blue-50/40 dark:bg-slate-800/80 text-xs shadow-xs flex items-center justify-between">
                                    <div>
                                        <p class="font-bold text-slate-800 dark:text-slate-100 truncate">{{ $person['name'] }}</p>
                                        <p class="text-[10px] text-blue-600 dark:text-blue-400">Usulan Status Laju</p>
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="resetSantri('{{ $person['person_id'] }}')"
                                        class="p-1 text-slate-400 hover:text-rose-500"
                                        title="Batalkan mutasi laju"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            @empty
                                <div class="h-20 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-center text-slate-400 text-xs text-center p-2">
                                    Tarik santri ke sini untuk usulan santri laju.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Dock 3: Usulan Boyong (Keluar Pondok) -->
                    <div
                        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 transition-all"
                        :class="{ 'ring-2 ring-rose-500 bg-rose-50/20': dropTarget === 'dock-boyong' }"
                        @dragover.prevent="dropTarget = 'dock-boyong'"
                        @dragleave="if (dropTarget === 'dock-boyong') dropTarget = null"
                        @drop="handleDropOnDock($event, 'boyong')"
                    >
                        <div class="flex items-center justify-between mb-3 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                            <div class="flex items-center gap-2">
                                <span class="text-base">🚶</span>
                                <h4 class="font-bold text-xs text-slate-800 dark:text-slate-100">Usulan Boyong (Keluar)</h4>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                {{ count($boardData['dock_boyong']) }}
                            </span>
                        </div>
                        <div class="space-y-2 min-h-[100px] max-h-60 overflow-y-auto pr-1">
                            @forelse ($boardData['dock_boyong'] as $person)
                                <div class="p-2.5 rounded-xl border border-rose-200 dark:border-rose-900/50 bg-rose-50/40 dark:bg-slate-800/80 text-xs shadow-xs flex items-center justify-between">
                                    <div>
                                        <p class="font-bold text-slate-800 dark:text-slate-100 truncate">{{ $person['name'] }}</p>
                                        <p class="text-[10px] text-rose-600 dark:text-rose-400">Usulan Boyong / Keluar</p>
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="resetSantri('{{ $person['person_id'] }}')"
                                        class="p-1 text-slate-400 hover:text-rose-500"
                                        title="Batalkan usulan boyong"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            @empty
                                <div class="h-20 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-center text-slate-400 text-xs text-center p-2">
                                    Tarik santri ke sini untuk usulan boyong.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {{-- ========================================================================= --}}
    {{-- VIEW 2: CONVENTIONAL TABLE VIEW                                           --}}
    {{-- ========================================================================= --}}
    @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Left: Room Navigator (span 4) -->
            <div class="lg:col-span-4 space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 px-1">Daftar Kamar Asrama</h3>

                <div class="space-y-2 max-h-[70vh] overflow-y-auto pr-1">
                    @forelse ($rooms as $room)
                        <button
                            type="button"
                            wire:click="selectRoom('{{ $room['id'] }}')"
                            class="w-full text-left p-3.5 rounded-2xl border transition-all flex items-center justify-between {{ $selectedRoomId === $room['id'] ? 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-500/50 shadow-sm' : 'bg-white dark:bg-slate-900 border-slate-100 dark:border-slate-800 hover:border-slate-200' }}"
                        >
                            <div class="flex items-center gap-3">
                                <span class="w-2.5 h-2.5 rounded-full {{ $selectedRoomId === $room['id'] ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-700' }}"></span>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $room['name'] }}</h4>
                                    <span class="text-xs text-slate-400">Kapasitas: {{ $room['capacity'] }}</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $room['occupied'] >= $room['capacity'] ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                                {{ $room['occupied'] }}/{{ $room['capacity'] }}
                            </span>
                        </button>
                    @empty
                        <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl text-center text-xs text-slate-400 border border-slate-100 dark:border-slate-800">
                            Tidak ada data kamar di asrama ini.
                        </div>
                    @endforelse

                    @if (count($unassigned) > 0)
                        <button
                            type="button"
                            wire:click="selectRoom('unassigned')"
                            class="w-full text-left p-3.5 rounded-2xl border transition-all flex items-center justify-between {{ $selectedRoomId === 'unassigned' ? 'bg-amber-50 dark:bg-amber-950/30 border-amber-500/50 shadow-sm' : 'bg-amber-50/50 dark:bg-slate-900 border-amber-200/50 dark:border-amber-900/30 hover:border-amber-300' }}"
                        >
                            <div class="flex items-center gap-3">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                <div>
                                    <h4 class="text-sm font-bold text-amber-900 dark:text-amber-200">Santri Belum Ada Kamar</h4>
                                    <span class="text-xs text-amber-600 dark:text-amber-400">Perlu penempatan</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                {{ count($unassigned) }} Santri
                            </span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Right: Santri Actions Table (span 8) -->
            <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm p-6 space-y-6">
                @if ($currentRoom)
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div>
                            <h2 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                <span>{{ $currentRoom['name'] }}</span>
                                <span class="text-xs font-normal text-slate-400">({{ count($currentRoom['occupants']) }} santri)</span>
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Tentukan tindakan mutasi atau verifikasi untuk setiap santri di bawah ini.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @forelse ($currentRoom['occupants'] as $occ)
                            @php
                                $pid = $occ['person_id'];
                                $currentAction = $entries[$pid]['action'] ?? 'tetap';
                            @endphp
                            <div class="p-4 rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $occ['name'] }}</h4>
                                        <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-400">
                                            <span>NIS: {{ $occ['nis'] ?? '-' }}</span>
                                            <span>&bull;</span>
                                            <span>Status: <strong class="text-slate-600 dark:text-slate-300 capitalize">{{ $occ['status'] ?? 'aktif' }}</strong></span>
                                        </div>
                                    </div>

                                    <!-- Action Segmented Buttons -->
                                    <div class="inline-flex rounded-xl p-1 bg-slate-200/80 dark:bg-slate-800 text-xs font-semibold">
                                        <button
                                            type="button"
                                            wire:click="setAction('{{ $pid }}', 'tetap')"
                                            class="px-3 py-1.5 rounded-lg transition-all {{ $currentAction === 'tetap' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}"
                                        >
                                            Tetap
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="setAction('{{ $pid }}', 'pindah_kamar')"
                                            class="px-3 py-1.5 rounded-lg transition-all {{ $currentAction === 'pindah_kamar' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}"
                                        >
                                            Pindah Kamar
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="setAction('{{ $pid }}', 'ubah_status')"
                                            class="px-3 py-1.5 rounded-lg transition-all {{ $currentAction === 'ubah_status' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}"
                                        >
                                            Ubah Status
                                        </button>
                                    </div>
                                </div>

                                <!-- Sub-Forms -->
                                @if ($currentAction === 'pindah_kamar')
                                    <div class="pt-3 border-t border-slate-200/60 dark:border-slate-700/60 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                                Kamar Tujuan <span class="text-rose-500">*</span>
                                            </label>
                                            <select
                                                wire:model="entries.{{ $pid }}.to_room_id"
                                                class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 py-2 px-3 focus:ring-blue-500"
                                            >
                                                <option value="">-- Pilih Kamar Tujuan --</option>
                                                @foreach ($availableTargetRooms as $tRoom)
                                                    <option value="{{ $tRoom['id'] }}">
                                                        {{ $tRoom['name'] }} (Tersisa {{ $tRoom['remaining'] }}/{{ $tRoom['capacity'] }}) {{ $tRoom['is_full'] ? '[PENUH]' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan Pindah</label>
                                            <input
                                                type="text"
                                                wire:model="entries.{{ $pid }}.notes"
                                                placeholder="Contoh: Pindah permintaan wali / musyrif"
                                                class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 py-2 px-3 focus:ring-blue-500"
                                            />
                                        </div>
                                    </div>
                                @endif

                                @if ($currentAction === 'ubah_status')
                                    <div class="pt-3 border-t border-slate-200/60 dark:border-slate-700/60 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                                Status Santri Baru <span class="text-rose-500">*</span>
                                            </label>
                                            <select
                                                wire:model="entries.{{ $pid }}.new_enrollment_status"
                                                class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 py-2 px-3 focus:ring-amber-500"
                                            >
                                                <option value="aktif">Aktif (Mukim)</option>
                                                <option value="santri_laju">Aktif (Laju / Pulang Pergi)</option>
                                                <option value="keluar_resmi">Boyong / Keluar Resmi</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Alasan / Catatan</label>
                                            <input
                                                type="text"
                                                wire:model="entries.{{ $pid }}.notes"
                                                placeholder="Contoh: Izin pindah laju"
                                                class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 py-2 px-3 focus:ring-amber-500"
                                            />
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="py-12 text-center text-slate-400 text-sm">
                                Tidak ada santri di kamar ini.
                            </div>
                        @endforelse
                    </div>
                @else
                    <div class="py-16 text-center text-slate-400 text-sm">
                        Pilih salah satu kamar di sebelah kiri untuk mulai melakukan verifikasi.
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
