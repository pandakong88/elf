<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6"
     x-data="{
        draggedPersonId: null,
        draggedFromRoom: null,
        isDragging: false,
        dropTarget: null,
        searchQuery: '',

        // Quick Transfer Modal
        quickTransferOpen: false,
        quickPerson: { id: null, name: '', nis: '', fromRoomName: '' },
        roomQuery: '',
        allRooms: @js($board['all_rooms'] ?? []),

        init() {
            // Auto-scroll saat drag mendekati tepi atas / bawah layar
            window.addEventListener('dragover', (e) => {
                if (!this.isDragging) return;
                const edgeDistance = 85;
                const scrollSpeed = 16;
                if (e.clientY < edgeDistance) {
                    window.scrollBy(0, -scrollSpeed);
                } else if (e.clientY > window.innerHeight - edgeDistance) {
                    window.scrollBy(0, scrollSpeed);
                }
            });
        },
        handleDragStart(e, pid, fromRoom) {
            this.draggedPersonId = pid;
            this.draggedFromRoom = fromRoom;
            this.isDragging = true;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', pid);
        },
        handleDragEnd() {
            this.isDragging = false;
            this.dropTarget = null;
            this.draggedPersonId = null;
        },
        handleDropOnRoom(e, roomId) {
            e.preventDefault();
            e.stopPropagation();
            this.dropTarget = null;
            let pid = this.draggedPersonId;
            if (!pid && e.dataTransfer) {
                pid = e.dataTransfer.getData('text/plain');
            }
            if (pid) {
                $wire.stageMove(pid, 'room', roomId);
            }
            this.isDragging = false;
            this.draggedPersonId = null;
        },
        handleDropOnDock(e, dockType) {
            e.preventDefault();
            e.stopPropagation();
            this.dropTarget = null;
            let pid = this.draggedPersonId;
            if (!pid && e.dataTransfer) {
                pid = e.dataTransfer.getData('text/plain');
            }
            if (pid) {
                $wire.stageMove(pid, dockType);
            }
            this.isDragging = false;
            this.draggedPersonId = null;
        },
        openQuickTransfer(pid, name, nis, fromRoomName) {
            this.quickPerson = { id: pid, name: name, nis: nis, fromRoomName: fromRoomName };
            this.roomQuery = '';
            this.quickTransferOpen = true;
            this.$nextTick(() => {
                if (this.$refs.roomSearchInput) {
                    this.$refs.roomSearchInput.focus();
                }
            });
        },
        closeQuickTransfer() {
            this.quickTransferOpen = false;
            this.quickPerson = { id: null, name: '', nis: '', fromRoomName: '' };
            this.roomQuery = '';
        },
        executeQuickMove(targetType, targetValue = null) {
            if (!this.quickPerson.id) return;
            $wire.stageMove(this.quickPerson.id, targetType, targetValue);
            this.closeQuickTransfer();
        },
        filteredRooms() {
            if (!this.roomQuery.trim()) {
                return this.allRooms;
            }
            let q = this.roomQuery.toLowerCase();
            return this.allRooms.filter(r => 
                (r.name && r.name.toLowerCase().includes(q)) || 
                (r.dorm_name && r.dorm_name.toLowerCase().includes(q)) ||
                (r.full_label && r.full_label.toLowerCase().includes(q))
            );
        },
        isMatch(name, nis) {
            if (!this.searchQuery.trim()) return true;
            let q = this.searchQuery.toLowerCase();
            return (name && name.toLowerCase().includes(q)) || (nis && nis.toLowerCase().includes(q));
        }
     }">

    <!-- Page Header & Global Controls -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <span>Kepengasuhan</span>
                <span>&rsaquo;</span>
                <span class="text-slate-800 dark:text-slate-200">Papan Mutasi &amp; Hunian Santri</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                <span>Papan Mutasi Santri (Live 24/7)</span>
                <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $genderScope === 'L' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'bg-pink-100 text-pink-800 dark:bg-pink-950 dark:text-pink-300' }}">
                    {{ $genderScope === 'L' ? 'Wilayah Putra' : 'Wilayah Putri' }}
                </span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Peta kendali hunian hidup. Tarik &amp; lepas santri antar kamar dan antar komplek secara langsung tanpa perlu membuat kampanye.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Gender Switcher (for Super Admin / Manajemen) -->
            @if (!$isGenderLocked)
                <div class="inline-flex rounded-xl p-1 bg-slate-100 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 text-xs font-bold">
                    <button
                        type="button"
                        wire:click="switchGender('L')"
                        class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 {{ $genderScope === 'L' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400' }}"
                    >
                        <span>👦</span>
                        <span>Putra</span>
                    </button>
                    <button
                        type="button"
                        wire:click="switchGender('P')"
                        class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 {{ $genderScope === 'P' ? 'bg-pink-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400' }}"
                    >
                        <span>🧕</span>
                        <span>Putri</span>
                    </button>
                </div>
            @endif

            <!-- Tambah Santri Baru -->
            @can('create-person')
                <button
                    type="button"
                    wire:click="openNewSantriModal"
                    class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-extrabold rounded-xl text-xs shadow-sm transition-all flex items-center gap-1.5 hover:scale-105"
                    title="Daftarkan Santri Baru"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Santri Baru</span>
                </button>
            @endcan

            <!-- Expand / Collapse Controls -->
            <button
                type="button"
                wire:click="expandAll"
                class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-all flex items-center gap-1"
                title="Bentangkan seluruh komplek"
            >
                <span>🔽</span>
                <span class="hidden sm:inline">Buka Semua</span>
            </button>
            <button
                type="button"
                wire:click="collapseAll"
                class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-all flex items-center gap-1"
                title="Ciutkan seluruh komplek"
            >
                <span>🔼</span>
                <span class="hidden sm:inline">Tutup Semua</span>
            </button>
        </div>
    </div>

    <!-- Quick Search, Sorting & Legend Bar -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex-1 relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input
                type="text"
                x-model="searchQuery"
                placeholder="Cari cepat nama santri atau NIS (kartu santri akan menyala otomatis)..."
                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 pl-10 pr-4 py-2.5 text-xs sm:text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
            />
        </div>

        <div class="flex flex-wrap items-center gap-3 justify-between sm:justify-end">
            <!-- Sort Order Selector -->
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-500 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                    </svg>
                    <span class="hidden sm:inline">Urutkan:</span>
                </span>
                <select
                    wire:model.live="sortBy"
                    class="text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-2xs cursor-pointer"
                >
                    <option value="name_asc">🔤 Nama (A &rarr; Z)</option>
                    <option value="name_desc">🔤 Nama (Z &rarr; A)</option>
                    <option value="class_asc">📚 Kelas (Awaliyah &rarr; Wustho &rarr; Ulya)</option>
                    <option value="class_desc">📚 Kelas (Ulya &rarr; Wustho &rarr; Awaliyah)</option>
                    <option value="nis_asc">🔢 NIS / Angkatan</option>
                </select>
            </div>

            <div class="h-4 w-px bg-slate-200 dark:bg-slate-700 hidden sm:block"></div>

            <div class="flex items-center gap-3 text-xs text-slate-500">
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span>Normal</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <span>Diusulkan</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    <span>Overcapacity</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Master Dormitory Swimlanes -->
    <div class="space-y-6">
        @forelse ($board['dormitories'] as $dorm)
            <div wire:key="dorm-{{ $dorm['id'] }}" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden transition-all">
                <!-- Dormitory Header Bar -->
                <div class="p-4 sm:p-5 bg-slate-50/70 dark:bg-slate-800/40 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            wire:click="toggleDormCollapse('{{ $dorm['id'] }}')"
                            class="w-8 h-8 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 text-xs transition-colors"
                        >
                            {{ $dorm['is_collapsed'] ? '▶' : '▼' }}
                        </button>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-black text-base sm:text-lg text-slate-800 dark:text-white flex items-center gap-1.5">
                                    <span>🏛️</span>
                                    <span>{{ $dorm['name'] }}</span>
                                </h3>
                                <span class="text-xs px-2 py-0.5 rounded-full font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                    {{ count($dorm['rooms']) }} Kamar
                                </span>
                            </div>
                            <span class="text-xs text-slate-400">Total Kapasitas: {{ $dorm['capacity_total'] }} santri</span>
                        </div>
                    </div>

                    <button
                        type="button"
                        wire:click="toggleDormCollapse('{{ $dorm['id'] }}')"
                        class="text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 transition-colors"
                    >
                        {{ $dorm['is_collapsed'] ? 'Bentangkan Kamar' : 'Ciutkan' }}
                    </button>
                </div>

                <!-- Rooms Grid in this Dormitory -->
                @if (!$dorm['is_collapsed'])
                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 items-start">
                            @forelse ($dorm['rooms'] as $room)
                                @php
                                    $occCount = count($room['occupants']);
                                    $isOvercapacity = $room['capacity'] > 0 && $occCount > $room['capacity'];
                                    $isFull = $room['capacity'] > 0 && $occCount === $room['capacity'];
                                @endphp
                                <div
                                    wire:key="room-{{ $room['id'] }}"
                                    class="bg-white dark:bg-slate-800/60 rounded-2xl border transition-all duration-200 overflow-hidden shadow-xs flex flex-col {{ $isOvercapacity ? 'border-rose-400 bg-rose-50/10 dark:border-rose-700/60' : ($isFull ? 'border-amber-300 dark:border-amber-700/50' : 'border-slate-200 dark:border-slate-700/70') }}"
                                    :class="{ 'ring-2 ring-blue-500 ring-offset-2 dark:ring-offset-slate-900 bg-blue-50/30': dropTarget === '{{ $room['id'] }}' }"
                                    @dragover.prevent.stop="dropTarget = '{{ $room['id'] }}'"
                                    @dragenter.prevent.stop="dropTarget = '{{ $room['id'] }}'"
                                    @dragleave="if (!$el.contains($event.relatedTarget)) dropTarget = null"
                                    @drop.prevent.stop="handleDropOnRoom($event, '{{ $room['id'] }}')"
                                >
                                    <!-- Room Header -->
                                    <div class="p-3 border-b border-slate-100 dark:border-slate-700/60 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/40">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full {{ $isOvercapacity ? 'bg-rose-500 animate-pulse' : ($isFull ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                                            <h4 class="font-extrabold text-xs text-slate-800 dark:text-slate-100">{{ $room['name'] }}</h4>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $isOvercapacity ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' : ($isFull ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300') }}">
                                            {{ $occCount }}/{{ $room['capacity'] }}
                                        </span>
                                    </div>

                                    @if ($isOvercapacity)
                                        <div class="px-2.5 py-0.5 bg-rose-500 text-white text-[9px] font-bold text-center">
                                            ⚠️ Overcapacity (+{{ $occCount - $room['capacity'] }})
                                        </div>
                                    @endif

                                    <!-- Droppable Room Body -->
                                    <div class="p-2.5 space-y-1.5 min-h-[120px] flex-1">
                                        @forelse ($room['occupants'] as $person)
                                            <div
                                                wire:key="santri-{{ $person['person_id'] }}"
                                                draggable="true"
                                                @dragstart="handleDragStart($event, '{{ $person['person_id'] }}', '{{ $room['id'] }}')"
                                                @dragend="handleDragEnd()"
                                                @dragover.prevent.stop="dropTarget = '{{ $room['id'] }}'"
                                                @drop.prevent.stop="handleDropOnRoom($event, '{{ $room['id'] }}')"
                                                x-show="isMatch('{{ addslashes($person['name']) }}', '{{ addslashes($person['nis']) }}')"
                                                :class="{ 'ring-2 ring-amber-400 bg-amber-50 dark:bg-amber-950/60': searchQuery.trim() && isMatch('{{ addslashes($person['name']) }}', '{{ addslashes($person['nis']) }}') }"
                                                class="p-2 rounded-xl border bg-white dark:bg-slate-800 shadow-xs cursor-grab active:cursor-grabbing hover:shadow-md transition-all select-none group {{ ($person['is_staged'] ?? false) ? 'border-blue-400 dark:border-blue-500/60 bg-blue-50/40 dark:bg-blue-950/30' : 'border-slate-200/80 dark:border-slate-700' }}"
                                            >
                                                <div class="flex items-start justify-between gap-1.5">
                                                    <div class="flex items-center gap-2 min-w-0">
                                                        <div class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center font-bold text-slate-600 dark:text-slate-300 text-[10px] flex-shrink-0">
                                                            {{ strtoupper(substr($person['name'] ?? 'S', 0, 1)) }}
                                                        </div>
                                                        <div class="min-w-0">
                                                            <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate" title="{{ $person['name'] }}">
                                                                {{ $person['name'] }}
                                                            </p>
                                                            <div class="text-[9px] text-slate-400 flex items-center gap-1">
                                                                <span>{{ $person['nis'] ?? '-' }}</span>
                                                                <span>&bull;</span>
                                                                <span>{{ $person['class'] ?? 'Santri' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center gap-0.5 flex-shrink-0">
                                                        <!-- Quick Move Button (No Drag / No Scroll - Mobile Friendly) -->
                                                        <button
                                                            type="button"
                                                            @click.stop="openQuickTransfer('{{ $person['person_id'] }}', '{{ addslashes($person['name']) }}', '{{ addslashes($person['nis'] ?? '-') }}', '{{ addslashes($person['original_room_name'] ?? ($room['dorm_name'] . ' - ' . $room['name'])) }}')"
                                                            class="p-1.5 sm:p-1 rounded-lg text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors flex items-center justify-center"
                                                            title="Pindah Cepat (Pilih Kamar / Laju / Boyong)"
                                                        >
                                                            <svg class="w-4 h-4 sm:w-3.5 sm:h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                                            </svg>
                                                        </button>

                                                        @if ($person['is_staged'] ?? false)
                                                            <button
                                                                type="button"
                                                                wire:click="unstageMove('{{ $person['person_id'] }}')"
                                                                class="p-1.5 sm:p-1 text-slate-400 hover:text-rose-500 transition-colors flex items-center justify-center"
                                                                title="Batalkan mutasi santri ini"
                                                            >
                                                                <svg class="w-4 h-4 sm:w-3.5 sm:h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if ($person['is_staged'] ?? false)
                                                    <div class="mt-1.5 pt-1 border-t border-blue-200/60 dark:border-blue-800/40 text-[9px] text-blue-600 dark:text-blue-400 font-bold flex items-center gap-1">
                                                        <span>🔄</span>
                                                        <span>Dari: {{ $person['original_room_name'] }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @empty
                                            <div
                                                @dragover.prevent.stop="dropTarget = '{{ $room['id'] }}'"
                                                @drop.prevent.stop="handleDropOnRoom($event, '{{ $room['id'] }}')"
                                                class="h-24 border-2 border-dashed border-slate-200 dark:border-slate-700/60 rounded-xl flex flex-col items-center justify-center text-slate-400 text-xs text-center p-2"
                                            >
                                                <span class="text-base mb-0.5">📭</span>
                                                <span class="text-[11px] font-medium">Kamar Kosong</span>
                                                <span class="text-[9px] text-slate-400">Tarik santri ke sini</span>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full p-4 text-center text-xs text-slate-400">
                                    Belum ada kamar terdaftar di komplek ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 text-slate-400">
                Tidak ada asrama / komplek terdaftar untuk wilayah gender ini.
            </div>
        @endforelse
    </div>

    <!-- Master Transit Dock (Unassigned, Laju, Boyong) -->
    <div class="border-t border-slate-200 dark:border-slate-800 pt-6">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 px-1 flex items-center gap-2">
            <span>Dermaga Transit &amp; Penyesuaian Status Santri</span>
            <span class="text-[11px] font-normal lowercase text-slate-400">(Tarik santri ke kotak ini untuk mengubah statusnya)</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Dock 1: Santri Tanpa Kamar (Unassigned) -->
            <div
                wire:key="dock-unassigned"
                class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 transition-all"
                :class="{ 'ring-2 ring-amber-500 bg-amber-50/20': dropTarget === 'dock-unassigned' }"
                @dragover.prevent.stop="dropTarget = 'dock-unassigned'"
                @dragenter.prevent.stop="dropTarget = 'dock-unassigned'"
                @dragleave="if (!$el.contains($event.relatedTarget)) dropTarget = null"
                @drop.prevent.stop="handleDropOnDock($event, 'unassigned')"
            >
                <div class="flex items-center justify-between mb-3 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                    <div class="flex items-center gap-2">
                        <span class="text-base">📦</span>
                        <h4 class="font-bold text-xs text-slate-800 dark:text-slate-100">Santri Tanpa Kamar</h4>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                        {{ count($board['dock_unassigned']) }}
                    </span>
                </div>
                <div class="space-y-2 min-h-[100px] max-h-60 overflow-y-auto pr-1">
                    @forelse ($board['dock_unassigned'] as $person)
                        <div
                            wire:key="dock-unassigned-{{ $person['person_id'] }}"
                            draggable="true"
                            @dragstart="handleDragStart($event, '{{ $person['person_id'] }}', 'unassigned')"
                            @dragend="handleDragEnd()"
                            @dragover.prevent.stop="dropTarget = 'dock-unassigned'"
                            @drop.prevent.stop="handleDropOnDock($event, 'unassigned')"
                            x-show="isMatch('{{ addslashes($person['name']) }}', '{{ addslashes($person['nis']) }}')"
                            class="p-2.5 rounded-xl border border-amber-200 dark:border-amber-900/50 bg-amber-50/40 dark:bg-slate-800 text-xs shadow-xs cursor-grab active:cursor-grabbing hover:shadow-md transition-all flex items-center justify-between gap-2"
                        >
                            <div class="min-w-0">
                                <p class="font-bold text-slate-800 dark:text-slate-100 truncate">{{ $person['name'] }}</p>
                                <p class="text-[10px] text-amber-600 dark:text-amber-400">NIS: {{ $person['nis'] ?? '-' }}</p>
                            </div>
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <button
                                    type="button"
                                    @click.stop="openQuickTransfer('{{ $person['person_id'] }}', '{{ addslashes($person['name']) }}', '{{ addslashes($person['nis'] ?? '-') }}', 'Tanpa Kamar')"
                                    class="px-2 py-1 rounded-lg bg-amber-500/10 text-amber-700 dark:text-amber-300 hover:bg-amber-600 hover:text-white transition-all text-[10px] font-bold flex items-center gap-1 shadow-2xs"
                                    title="Pilih Kamar Tujuan"
                                >
                                    <span>Pilih Kamar</span>
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div
                            @dragover.prevent.stop="dropTarget = 'dock-unassigned'"
                            @drop.prevent.stop="handleDropOnDock($event, 'unassigned')"
                            class="h-20 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-center text-slate-400 text-xs text-center p-2"
                        >
                            Semua santri sudah memiliki kamar.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Dock 2: Santri Laju (Pulang-Pergi) -->
            <div
                wire:key="dock-laju"
                class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 transition-all"
                :class="{ 'ring-2 ring-blue-500 bg-blue-50/20': dropTarget === 'dock-laju' }"
                @dragover.prevent.stop="dropTarget = 'dock-laju'"
                @dragenter.prevent.stop="dropTarget = 'dock-laju'"
                @dragleave="if (!$el.contains($event.relatedTarget)) dropTarget = null"
                @drop.prevent.stop="handleDropOnDock($event, 'laju')"
            >
                <div class="flex items-center justify-between mb-3 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                    <div class="flex items-center gap-2">
                        <span class="text-base">🏠</span>
                        <h4 class="font-bold text-xs text-slate-800 dark:text-slate-100">Santri Laju (Pulang-Pergi)</h4>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                        {{ count($board['dock_laju']) }}
                    </span>
                </div>
                <div class="space-y-2 min-h-[100px] max-h-60 overflow-y-auto pr-1">
                    @forelse ($board['dock_laju'] as $person)
                        @if ($person['is_staged'] ?? false)
                            <div
                                wire:key="dock-laju-staged-{{ $person['person_id'] }}"
                                @dragover.prevent.stop="dropTarget = 'dock-laju'"
                                @drop.prevent.stop="handleDropOnDock($event, 'laju')"
                                class="p-2.5 rounded-xl border border-blue-200 dark:border-blue-900/50 bg-blue-50/40 dark:bg-slate-800 text-xs shadow-xs flex items-center justify-between"
                            >
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-800 dark:text-slate-100 truncate">{{ $person['name'] }}</p>
                                    <p class="text-[10px] text-blue-600 dark:text-blue-400">Usulan Status Laju</p>
                                </div>
                                <button
                                    type="button"
                                    wire:click="unstageMove('{{ $person['person_id'] }}')"
                                    class="p-1 text-slate-400 hover:text-rose-500"
                                    title="Batalkan mutasi laju"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @else
                            <div
                                wire:key="dock-laju-{{ $person['person_id'] }}"
                                draggable="true"
                                @dragstart="handleDragStart($event, '{{ $person['person_id'] }}', 'laju')"
                                @dragend="handleDragEnd()"
                                @dragover.prevent.stop="dropTarget = 'dock-laju'"
                                @drop.prevent.stop="handleDropOnDock($event, 'laju')"
                                x-show="isMatch('{{ addslashes($person['name']) }}', '{{ addslashes($person['nis']) }}')"
                                class="p-2.5 rounded-xl border border-blue-200 dark:border-blue-900/50 bg-blue-50/40 dark:bg-slate-800 text-xs shadow-xs cursor-grab active:cursor-grabbing hover:shadow-md transition-all flex items-center justify-between gap-2"
                            >
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-800 dark:text-slate-100 truncate">{{ $person['name'] }}</p>
                                    <p class="text-[10px] text-blue-600 dark:text-blue-400">NIS: {{ $person['nis'] ?? '-' }} &bull; Pulang-Pergi</p>
                                </div>
                                <div class="flex items-center gap-1 flex-shrink-0">
                                    <button
                                        type="button"
                                        @click.stop="openQuickTransfer('{{ $person['person_id'] }}', '{{ addslashes($person['name']) }}', '{{ addslashes($person['nis'] ?? '-') }}', 'Santri Laju')"
                                        class="px-2 py-1 rounded-lg bg-blue-500/10 text-blue-700 dark:text-blue-300 hover:bg-blue-600 hover:text-white transition-all text-[10px] font-bold flex items-center gap-1 shadow-2xs"
                                        title="Pilih Kamar Tujuan"
                                    >
                                        <span>Pilih Kamar</span>
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div
                            @dragover.prevent.stop="dropTarget = 'dock-laju'"
                            @drop.prevent.stop="handleDropOnDock($event, 'laju')"
                            class="h-20 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-center text-slate-400 text-xs text-center p-2"
                        >
                            Tidak ada santri laju.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Dock 3: Tanpa Keterangan (Masa Tabayyun) -->
            <div
                wire:key="dock-tanpa_keterangan"
                class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 transition-all"
                :class="{ 'ring-2 ring-amber-500 bg-amber-50/20': dropTarget === 'dock-tanpa_keterangan' }"
                @dragover.prevent.stop="dropTarget = 'dock-tanpa_keterangan'"
                @dragenter.prevent.stop="dropTarget = 'dock-tanpa_keterangan'"
                @dragleave="if (!$el.contains($event.relatedTarget)) dropTarget = null"
                @drop.prevent.stop="handleDropOnDock($event, 'tanpa_keterangan')"
            >
                <div class="flex items-center justify-between mb-3 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                    <div class="flex items-center gap-2">
                        <span class="text-base">❓</span>
                        <div>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-slate-100">Tanpa Keterangan</h4>
                            <span class="text-[9px] text-amber-600 dark:text-amber-400 font-medium">Masa Tabayyun (Kamar Ditahan)</span>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                        {{ count($board['dock_tanpa_keterangan']) }}
                    </span>
                </div>
                <div class="space-y-2 min-h-[100px] max-h-60 overflow-y-auto pr-1">
                    @forelse ($board['dock_tanpa_keterangan'] as $person)
                        <div
                            wire:key="dock-tk-{{ $person['person_id'] }}"
                            @dragover.prevent.stop="dropTarget = 'dock-tanpa_keterangan'"
                            @drop.prevent.stop="handleDropOnDock($event, 'tanpa_keterangan')"
                            class="p-2.5 rounded-xl border border-amber-200 dark:border-amber-900/50 bg-amber-50/40 dark:bg-slate-800 text-xs shadow-xs flex items-center justify-between"
                        >
                            <div class="min-w-0">
                                <p class="font-bold text-slate-800 dark:text-slate-100 truncate">{{ $person['name'] }}</p>
                                <p class="text-[10px] text-amber-600 dark:text-amber-400">Kamar &amp; Kelas Tetap Ditahan</p>
                            </div>
                            <button
                                type="button"
                                wire:click="unstageMove('{{ $person['person_id'] }}')"
                                class="p-1 text-slate-400 hover:text-rose-500"
                                title="Batalkan usulan tanpa keterangan"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @empty
                        <div
                            @dragover.prevent.stop="dropTarget = 'dock-tanpa_keterangan'"
                            @drop.prevent.stop="handleDropOnDock($event, 'tanpa_keterangan')"
                            class="h-20 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-center text-slate-400 text-xs text-center p-2"
                        >
                            Tarik santri ke sini untuk status Tanpa Keterangan (kamar &amp; kelas ditahan).
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Dock 4: Usulan Boyong (Keluar Pondok) -->
            <div
                wire:key="dock-boyong"
                class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 transition-all"
                :class="{ 'ring-2 ring-rose-500 bg-rose-50/20': dropTarget === 'dock-boyong' }"
                @dragover.prevent.stop="dropTarget = 'dock-boyong'"
                @dragenter.prevent.stop="dropTarget = 'dock-boyong'"
                @dragleave="if (!$el.contains($event.relatedTarget)) dropTarget = null"
                @drop.prevent.stop="handleDropOnDock($event, 'boyong')"
            >
                <div class="flex items-center justify-between mb-3 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                    <div class="flex items-center gap-2">
                        <span class="text-base">🚶</span>
                        <h4 class="font-bold text-xs text-slate-800 dark:text-slate-100">Usulan Boyong (Keluar)</h4>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                        {{ count($board['dock_boyong']) }}
                    </span>
                </div>
                <div class="space-y-2 min-h-[100px] max-h-60 overflow-y-auto pr-1">
                    @forelse ($board['dock_boyong'] as $person)
                        <div
                            wire:key="dock-boyong-{{ $person['person_id'] }}"
                            @dragover.prevent.stop="dropTarget = 'dock-boyong'"
                            @drop.prevent.stop="handleDropOnDock($event, 'boyong')"
                            class="p-2.5 rounded-xl border border-rose-200 dark:border-rose-900/50 bg-rose-50/40 dark:bg-slate-800 text-xs shadow-xs flex items-center justify-between"
                        >
                            <div>
                                <p class="font-bold text-slate-800 dark:text-slate-100 truncate">{{ $person['name'] }}</p>
                                <p class="text-[10px] text-rose-600 dark:text-rose-400">Usulan Boyong / Keluar</p>
                            </div>
                            <button
                                type="button"
                                wire:click="unstageMove('{{ $person['person_id'] }}')"
                                class="p-1 text-slate-400 hover:text-rose-500"
                                title="Batalkan usulan boyong"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @empty
                        <div
                            @dragover.prevent.stop="dropTarget = 'dock-boyong'"
                            @drop.prevent.stop="handleDropOnDock($event, 'boyong')"
                            class="h-20 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-center text-slate-400 text-xs text-center p-2"
                        >
                            Tarik santri ke sini untuk mencatat usulan santri boyong.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Bar (Sticks to bottom when staged moves exist) -->
    @if ($board['staged_count'] > 0)
        <div class="fixed bottom-4 sm:bottom-6 inset-x-3 sm:inset-x-4 max-w-2xl mx-auto z-50 animate-bounce-short">
            <div class="bg-slate-900 text-white p-3.5 sm:p-4 rounded-2xl shadow-2xl border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 sm:gap-3">
                    <span class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-blue-500 animate-ping flex-shrink-0"></span>
                    <div>
                        <p class="font-bold text-xs sm:text-sm text-white">
                            {{ $board['staged_count'] }} Perubahan Mutasi Siap Direview
                        </p>
                        <p class="text-[10px] sm:text-[11px] text-slate-400">
                            Perubahan dicatat sementara di layar.
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 w-full sm:w-auto border-t border-slate-800 sm:border-0 pt-2 sm:pt-0">
                    <button
                        type="button"
                        wire:click="resetAllMoves"
                        class="px-3 py-1.5 sm:py-2 rounded-xl text-xs font-bold text-slate-300 hover:text-white hover:bg-slate-800 transition-colors"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        wire:click="openReviewModal"
                        class="flex-1 sm:flex-initial px-4 sm:px-5 py-2 sm:py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs shadow-lg shadow-blue-600/30 transition-all text-center flex items-center justify-center gap-1.5 hover:scale-105"
                    >
                        <span>Review &amp; Simpan</span>
                        <span class="px-1.5 py-0.5 rounded-full bg-blue-700 text-white text-[10px]">
                            {{ $board['staged_count'] }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Floating Drop HUD (Muncul Otomatis Melayang Saat Drag Berjalan) -->
    <div
        x-show="isDragging"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="fixed bottom-24 right-6 sm:right-8 z-40 flex flex-col gap-2 p-3 bg-slate-900/95 dark:bg-slate-950/95 backdrop-blur-md rounded-3xl border border-slate-700/80 shadow-2xl text-white select-none w-72"
    >
        <div class="flex items-center justify-between pb-1.5 border-b border-slate-800 text-[11px] font-bold text-slate-300">
            <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span>Dermaga Cepat (Drop ke Sini):</span>
            </span>
            <span class="text-[10px] text-slate-400">Lepas Mouse</span>
        </div>

        <!-- Drop Zone 1: Santri Laju -->
        <div
            @dragover.prevent="dropTarget = 'hud-laju'"
            @dragleave="if (dropTarget === 'hud-laju') dropTarget = null"
            @drop="handleDropOnDock($event, 'laju')"
            :class="{ 'bg-blue-600 ring-2 ring-white scale-[1.02] shadow-lg': dropTarget === 'hud-laju', 'bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60': dropTarget !== 'hud-laju' }"
            class="p-2.5 rounded-2xl transition-all flex items-center gap-3 cursor-pointer"
        >
            <div class="w-8 h-8 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-base flex-shrink-0">
                🏠
            </div>
            <div class="min-w-0">
                <h5 class="font-bold text-xs text-white">Santri Laju</h5>
                <p class="text-[10px] text-slate-400">Pulang-pergi &amp; lepas kamar</p>
            </div>
        </div>

        <!-- Drop Zone 2: Tanpa Keterangan -->
        <div
            @dragover.prevent="dropTarget = 'hud-tanpa_keterangan'"
            @dragleave="if (dropTarget === 'hud-tanpa_keterangan') dropTarget = null"
            @drop="handleDropOnDock($event, 'tanpa_keterangan')"
            :class="{ 'bg-amber-600 ring-2 ring-white scale-[1.02] shadow-lg': dropTarget === 'hud-tanpa_keterangan', 'bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60': dropTarget !== 'hud-tanpa_keterangan' }"
            class="p-2.5 rounded-2xl transition-all flex items-center gap-3 cursor-pointer"
        >
            <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-base flex-shrink-0">
                ❓
            </div>
            <div class="min-w-0">
                <h5 class="font-bold text-xs text-white">Tanpa Keterangan</h5>
                <p class="text-[10px] text-slate-400">Tabayyun &amp; kamar tetap ditahan</p>
            </div>
        </div>

        <!-- Drop Zone 3: Usulan Boyong -->
        <div
            @dragover.prevent="dropTarget = 'hud-boyong'"
            @dragleave="if (dropTarget === 'hud-boyong') dropTarget = null"
            @drop="handleDropOnDock($event, 'boyong')"
            :class="{ 'bg-rose-600 ring-2 ring-white scale-[1.02] shadow-lg': dropTarget === 'hud-boyong', 'bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60': dropTarget !== 'hud-boyong' }"
            class="p-2.5 rounded-2xl transition-all flex items-center gap-3 cursor-pointer"
        >
            <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center font-bold text-base flex-shrink-0">
                🚶
            </div>
            <div class="min-w-0">
                <h5 class="font-bold text-xs text-white">Usulan Boyong</h5>
                <p class="text-[10px] text-slate-400">Keluar pondok secara resmi</p>
            </div>
        </div>

        <!-- Drop Zone 4: Lepas Kamar (Unassigned) -->
        <div
            @dragover.prevent="dropTarget = 'hud-unassigned'"
            @dragleave="if (dropTarget === 'hud-unassigned') dropTarget = null"
            @drop="handleDropOnDock($event, 'unassigned')"
            :class="{ 'bg-amber-600 ring-2 ring-white scale-[1.02] shadow-lg': dropTarget === 'hud-unassigned', 'bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60': dropTarget !== 'hud-unassigned' }"
            class="p-2.5 rounded-2xl transition-all flex items-center gap-3 cursor-pointer"
        >
            <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-base flex-shrink-0">
                📦
            </div>
            <div class="min-w-0">
                <h5 class="font-bold text-xs text-white">Lepas dari Kamar</h5>
                <p class="text-[10px] text-slate-400">Pindahkan ke antrean transit</p>
            </div>
        </div>
    </div>

    <!-- Quick Transfer Modal (Searchable Room Picker & 1-Click Status) -->
    <div
        x-show="quickTransferOpen"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        @keydown.escape.window="closeQuickTransfer()"
    >
        <!-- Backdrop -->
        <div
            x-show="quickTransferOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
            @click="closeQuickTransfer()"
        ></div>

        <!-- Modal Dialog -->
        <div class="min-h-full flex items-center justify-center p-3 sm:p-4">
            <div
                x-show="quickTransferOpen"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                class="relative bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl max-w-lg w-full p-4 sm:p-6 shadow-2xl border border-slate-100 dark:border-slate-800 space-y-4 sm:space-y-5 my-auto max-h-[92vh] flex flex-col"
                @click.stop
            >
                <!-- Modal Header -->
                <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-bold text-base shadow-md shadow-indigo-600/20">
                            ⇄
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 dark:text-white" x-text="'Pindah Cepat: ' + quickPerson.name"></h3>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                <span x-text="'NIS: ' + quickPerson.nis"></span>
                                <span>&bull;</span>
                                <span class="text-blue-600 dark:text-blue-400 font-bold" x-text="'Asal: ' + quickPerson.fromRoomName"></span>
                            </div>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="closeQuickTransfer()"
                        class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- 1-Click Status Quick Actions -->
                <div class="space-y-2">
                    <label class="text-[11px] font-bold uppercase tracking-wider text-slate-400">1. Aksi Status Langsung</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <button
                            type="button"
                            @click="executeQuickMove('laju')"
                            class="p-2.5 rounded-xl border border-blue-200 dark:border-blue-900/50 bg-blue-50/50 dark:bg-blue-950/30 hover:bg-blue-600 hover:text-white text-blue-700 dark:text-blue-300 font-bold text-xs transition-all flex flex-col items-center gap-1 group"
                        >
                            <span class="text-lg">🏠</span>
                            <span class="text-center group-hover:text-white text-[11px]">Santri Laju</span>
                        </button>
                        <button
                            type="button"
                            @click="executeQuickMove('tanpa_keterangan')"
                            class="p-2.5 rounded-xl border border-amber-200 dark:border-amber-900/50 bg-amber-50/50 dark:bg-amber-950/30 hover:bg-amber-600 hover:text-white text-amber-700 dark:text-amber-300 font-bold text-xs transition-all flex flex-col items-center gap-1 group"
                        >
                            <span class="text-lg">❓</span>
                            <span class="text-center group-hover:text-white text-[11px]">Tanpa Ket.</span>
                        </button>
                        <button
                            type="button"
                            @click="executeQuickMove('boyong')"
                            class="p-2.5 rounded-xl border border-rose-200 dark:border-rose-900/50 bg-rose-50/50 dark:bg-rose-950/30 hover:bg-rose-600 hover:text-white text-rose-700 dark:text-rose-300 font-bold text-xs transition-all flex flex-col items-center gap-1 group"
                        >
                            <span class="text-lg">🚶</span>
                            <span class="text-center group-hover:text-white text-[11px]">Boyong</span>
                        </button>
                        <button
                            type="button"
                            @click="executeQuickMove('unassigned')"
                            class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 hover:bg-slate-700 hover:text-white text-slate-700 dark:text-slate-300 font-bold text-xs transition-all flex flex-col items-center gap-1 group"
                        >
                            <span class="text-lg">📦</span>
                            <span class="text-center group-hover:text-white text-[11px]">Lepas Kamar</span>
                        </button>
                    </div>
                </div>

                <!-- Searchable Room Selector -->
                <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <label class="text-[11px] font-bold uppercase tracking-wider text-slate-400">2. Atau Pilih Kamar Tujuan (Semua Komplek)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input
                            type="text"
                            x-ref="roomSearchInput"
                            x-model="roomQuery"
                            placeholder="Ketik nama kamar atau komplek (misal: A-01, Sunan Bonang)..."
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 pl-9 pr-4 py-2.5 text-xs sm:text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors"
                        >
                    </div>

                    <!-- Room List Scrollable (Zero page scroll!) -->
                    <div class="max-h-60 overflow-y-auto space-y-1.5 pr-1 mt-2">
                        <template x-for="room in filteredRooms()" :key="room.id">
                            <button
                                type="button"
                                @click="executeQuickMove('room', room.id)"
                                class="w-full p-2.5 rounded-xl border border-slate-200/80 dark:border-slate-800 hover:border-indigo-500 dark:hover:border-indigo-500 hover:bg-indigo-50/50 dark:hover:bg-indigo-950/30 text-left transition-all flex items-center justify-between group"
                            >
                                <div class="min-w-0">
                                    <div class="font-bold text-xs text-slate-800 dark:text-slate-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400" x-text="room.name"></div>
                                    <div class="text-[10px] text-slate-400" x-text="room.dorm_name"></div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                        :class="room.occupants_count >= room.capacity ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300'"
                                        x-text="room.occupants_count + ' / ' + room.capacity + ' Santri'"
                                    ></span>
                                    <span class="text-xs text-slate-300 group-hover:text-indigo-600 transition-colors">➔</span>
                                </div>
                            </button>
                        </template>

                        <div x-show="filteredRooms().length === 0" class="py-6 text-center text-xs text-slate-400">
                            Tidak ada kamar yang cocok dengan kata kunci.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Review & Konfirmasi Mutasi Sebelum Simpan -->
    @if ($showReviewModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-review-mutasi" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div
                wire:click="closeReviewModal"
                class="fixed inset-0 bg-slate-900/70 backdrop-blur-xs transition-opacity"
            ></div>

            <!-- Modal Container -->
            <div class="min-h-full flex items-center justify-center p-3 sm:p-4">
                <div class="relative bg-white dark:bg-slate-900 rounded-3xl max-w-2xl w-full p-5 sm:p-7 shadow-2xl border border-slate-100 dark:border-slate-800 space-y-5 my-auto max-h-[92vh] flex flex-col animate-scale-up">
                    
                    <!-- Header -->
                    <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-blue-600/20">
                                📋
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span>Review Perubahan Mutasi Santri</span>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-black bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                                        {{ $board['staged_count'] }} Santri
                                    </span>
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    Periksa daftar mutasi di bawah ini sebelum diterapkan permanen ke sistem aktif.
                                </p>
                            </div>
                        </div>
                        <button
                            type="button"
                            wire:click="closeReviewModal"
                            class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Breakdown Badges -->
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        @if ($board['staged_breakdown']['room'] > 0)
                            <div class="px-3 py-1.5 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900/50 text-blue-700 dark:text-blue-300 font-bold flex items-center gap-1.5">
                                <span>🏛️ Pindah Kamar:</span>
                                <span class="px-1.5 py-0.5 rounded-md bg-blue-600 text-white text-[10px]">{{ $board['staged_breakdown']['room'] }}</span>
                            </div>
                        @endif
                        @if ($board['staged_breakdown']['laju'] > 0)
                            <div class="px-3 py-1.5 rounded-xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-900/50 text-sky-700 dark:text-sky-300 font-bold flex items-center gap-1.5">
                                <span>🏠 Santri Laju:</span>
                                <span class="px-1.5 py-0.5 rounded-md bg-sky-600 text-white text-[10px]">{{ $board['staged_breakdown']['laju'] }}</span>
                            </div>
                        @endif
                        @if ($board['staged_breakdown']['boyong'] > 0)
                            <div class="px-3 py-1.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-rose-700 dark:text-rose-300 font-bold flex items-center gap-1.5">
                                <span>🚶 Usulan Boyong:</span>
                                <span class="px-1.5 py-0.5 rounded-md bg-rose-600 text-white text-[10px]">{{ $board['staged_breakdown']['boyong'] }}</span>
                            </div>
                        @endif
                        @if ($board['staged_breakdown']['unassigned'] > 0)
                            <div class="px-3 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 text-amber-700 dark:text-amber-300 font-bold flex items-center gap-1.5">
                                <span>📦 Lepas Kamar:</span>
                                <span class="px-1.5 py-0.5 rounded-md bg-amber-600 text-white text-[10px]">{{ $board['staged_breakdown']['unassigned'] }}</span>
                            </div>
                        @endif
                        @if (($board['staged_breakdown']['tanpa_keterangan'] ?? 0) > 0)
                            <div class="px-3 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 text-amber-700 dark:text-amber-300 font-bold flex items-center gap-1.5">
                                <span>❓ Tanpa Keterangan:</span>
                                <span class="px-1.5 py-0.5 rounded-md bg-amber-600 text-white text-[10px]">{{ $board['staged_breakdown']['tanpa_keterangan'] }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Scrollable Staged List -->
                    <div class="flex-1 overflow-y-auto max-h-[46vh] space-y-2 pr-1">
                        @foreach ($board['staged_list'] as $staged)
                            <div
                                wire:key="review-staged-{{ $staged['person_id'] }}"
                                class="p-3 rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 hover:bg-white dark:hover:bg-slate-800 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-xl bg-slate-200 dark:bg-slate-700 flex items-center justify-center font-bold text-slate-700 dark:text-slate-200 text-xs flex-shrink-0">
                                        {{ strtoupper(substr($staged['person_name'] ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white truncate">
                                            {{ $staged['person_name'] }}
                                        </p>
                                        <div class="text-[10px] text-slate-400 flex items-center gap-1.5">
                                            <span>NIS: {{ $staged['person_nis'] ?? '-' }}</span>
                                            <span>&bull;</span>
                                            <span>{{ $staged['person_class'] ?? 'Santri' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between sm:justify-end gap-3 flex-shrink-0 border-t sm:border-0 border-slate-200 dark:border-slate-700 pt-2 sm:pt-0">
                                    <!-- Mutation Route / Destination -->
                                    <div class="text-left sm:text-right">
                                        <div class="text-[10px] text-slate-400">
                                            <span>Dari: </span>
                                            <span class="font-medium text-slate-600 dark:text-slate-300">{{ $staged['from_room_name'] }}</span>
                                        </div>
                                        <div class="text-xs font-bold flex items-center gap-1 {{ $staged['target_type'] === 'boyong' ? 'text-rose-600 dark:text-rose-400' : ($staged['target_type'] === 'tanpa_keterangan' ? 'text-amber-600 dark:text-amber-400' : ($staged['target_type'] === 'laju' ? 'text-sky-600 dark:text-sky-400' : ($staged['target_type'] === 'unassigned' ? 'text-amber-600 dark:text-amber-400' : 'text-blue-600 dark:text-blue-400'))) }}">
                                            <span>➔</span>
                                            <span>{{ $staged['target_room_name'] }}</span>
                                        </div>
                                    </div>

                                    <!-- Delete from staged moves -->
                                    <button
                                        type="button"
                                        wire:click="unstageMove('{{ $staged['person_id'] }}')"
                                        class="p-1.5 text-slate-400 hover:text-rose-500 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors"
                                        title="Batalkan mutasi santri ini"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <button
                            type="button"
                            wire:click="resetAllMoves"
                            class="px-4 py-2 text-xs font-bold text-rose-600 hover:text-rose-700 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl transition-colors text-center"
                        >
                            Batalkan Semua Mutasi
                        </button>

                        <div class="flex items-center justify-end gap-2">
                            <button
                                type="button"
                                wire:click="closeReviewModal"
                                class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                            >
                                Lanjut Edit
                            </button>
                            <button
                                type="button"
                                wire:click="applyMutations"
                                wire:loading.attr="disabled"
                                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-xl text-xs shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2 hover:scale-105 disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="applyMutations">
                                    💾 Terapkan Mutasi Sekarang ({{ $board['staged_count'] }})
                                </span>
                                <span wire:loading wire:target="applyMutations" class="flex items-center gap-1.5">
                                    <svg class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    <span>Menyimpan ke Database...</span>
                                </span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif

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
</div>
