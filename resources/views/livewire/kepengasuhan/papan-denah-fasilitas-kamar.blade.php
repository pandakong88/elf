<div class="space-y-6 pb-24">
    <!-- Header Page & Controls -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-3">
                <span class="text-3xl">🗄️</span>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white flex items-center gap-2">
                        <span>Papan Denah &amp; Sensus Lemari Kamar</span>
                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $genderScope === 'L' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'bg-pink-100 text-pink-800 dark:bg-pink-950 dark:text-pink-300' }}">
                            {{ $genderScope === 'L' ? 'Wilayah Putra' : 'Wilayah Putri' }}
                        </span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        Digital Twin lemari &amp; aset kamar. Desain bentuk lemari, pantau pengisian loker santri, dan inspeksi fisik real-time.
                    </p>
                </div>
            </div>
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

            <!-- Quick Action Buttons -->
            @if($activeRoom)
                <button
                    type="button"
                    wire:click="toggleHighlightAllVacant"
                    class="px-3 py-2 font-extrabold rounded-xl text-xs transition-all flex items-center gap-1.5 {{ $highlightAllVacant ? 'bg-emerald-600 text-white shadow-md shadow-emerald-500/30 ring-2 ring-emerald-400' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300' }}"
                    title="{{ $highlightAllVacant ? 'Matikan sorotan tempat kosong' : 'Nyalakan lampu indikator semua lemari/rak yang kosong di kamar ini' }}"
                >
                    <span>{{ $highlightAllVacant ? '✨' : '💡' }}</span>
                    <span class="hidden sm:inline">{{ $highlightAllVacant ? 'Semua Kosong Menyala' : 'Sorot Semua Kosong' }}</span>
                </button>

                <button
                    type="button"
                    wire:click="openBuilderModal()"
                    class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-extrabold rounded-xl text-xs shadow-sm transition-all flex items-center gap-1.5 hover:scale-105"
                    title="Desain / Tambah Lemari &amp; Perabot"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Desain Lemari / Fasilitas</span>
                </button>

                <button
                    type="button"
                    wire:click="openCloneModal()"
                    class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-all flex items-center gap-1.5"
                    title="Salin bentuk lemari ini ke kamar lain"
                >
                    <span>📋</span>
                    <span class="hidden sm:inline">Salin Bentuk ke Kamar Lain</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Main Grid: Room Explorer & Visual Canvas (Responsive Mobile & Desktop) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start" x-data="{ mobileTab: 'canvas' }">
        <!-- LEFT SIDEBAR: Navigasi Penghuni Kamar (col-span-12 lg:col-span-4 xl:col-span-3) -->
        <div class="lg:col-span-4 xl:col-span-3 space-y-4">

            <!-- Mobile View Switcher (Tab Denah vs Daftar Penghuni di Layar HP) -->
            @if($activeRoom)
                <div class="lg:hidden flex items-center p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl text-xs font-bold shadow-2xs">
                    <button
                        type="button"
                        @click="mobileTab = 'canvas'"
                        :class="mobileTab === 'canvas' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-xs font-black' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-300'"
                        class="flex-1 py-2 rounded-xl text-center flex items-center justify-center gap-1.5 transition-all"
                    >
                        <span>🚪 Denah Perabot</span>
                    </button>
                    <button
                        type="button"
                        @click="mobileTab = 'roster'"
                        :class="mobileTab === 'roster' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-xs font-black' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-300'"
                        class="flex-1 py-2 rounded-xl text-center flex items-center justify-center gap-1.5 transition-all"
                    >
                        <span>👥 Daftar Penghuni</span>
                        <span class="px-1.5 py-0.2 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-full text-[10px]">
                            {{ $occupantStats['total'] ?? 0 }}
                        </span>
                    </button>
                </div>
            @endif

            <!-- Santri Penghuni Kamar Aktif (Draggable Roster) -->
            @if($activeRoom)
                <div 
                    :class="mobileTab === 'roster' ? 'block' : 'hidden lg:block'"
                    class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-100 dark:border-slate-800 shadow-sm space-y-4"
                >
                    <!-- Sidebar Header with Stats -->
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div>
                            <h2 class="font-extrabold text-sm text-slate-800 dark:text-white flex items-center gap-1.5">
                                <span>👥</span>
                                <span>Penghuni {{ $activeRoom->name }}</span>
                            </h2>
                            <p class="text-[11px] text-slate-400">{{ $occupantStats['total'] }} Santri Mukim</p>
                        </div>

                        @if($occupantStats['total'] > 0 && $occupantStats['complete'] === $occupantStats['total'])
                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-[10px] font-black rounded-full flex items-center gap-1">
                                <span>✨</span>
                                <span>Semua Terplot</span>
                            </span>
                        @elseif($occupantStats['total'] > 0)
                            <span class="px-2 py-0.5 bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 text-[10px] font-bold rounded-full">
                                {{ $occupantStats['complete'] }}/{{ $occupantStats['total'] }} Lengkap
                            </span>
                        @endif
                    </div>

                    <!-- Filter Tabs: Semua, Belum Lemari, Belum Rak, Lengkap (Scrollable & Interactive) -->
                    <div 
                        x-data="{
                            canScrollLeft: false,
                            canScrollRight: false,
                            isDown: false,
                            startX: 0,
                            scrollLeft: 0,
                            init() {
                                this.$nextTick(() => this.updateScrollState());
                            },
                            updateScrollState() {
                                const el = this.$refs.scrollContainer;
                                if (!el) return;
                                this.canScrollLeft = el.scrollLeft > 6;
                                this.canScrollRight = el.scrollLeft < (el.scrollWidth - el.clientWidth - 6);
                            },
                            scrollByOffset(distance) {
                                const el = this.$refs.scrollContainer;
                                if (!el) return;
                                el.scrollBy({ left: distance, behavior: 'smooth' });
                                setTimeout(() => this.updateScrollState(), 200);
                            },
                            handleWheel(e) {
                                const el = this.$refs.scrollContainer;
                                if (!el) return;
                                el.scrollLeft += (e.deltaY || e.deltaX) * 0.85;
                                this.updateScrollState();
                            },
                            startDrag(e) {
                                const el = this.$refs.scrollContainer;
                                this.isDown = true;
                                this.startX = e.pageX - el.offsetLeft;
                                this.scrollLeft = el.scrollLeft;
                            },
                            stopDrag() {
                                this.isDown = false;
                            },
                            doDrag(e) {
                                if (!this.isDown) return;
                                const el = this.$refs.scrollContainer;
                                const x = e.pageX - el.offsetLeft;
                                const walk = (x - this.startX) * 1.3;
                                el.scrollLeft = this.scrollLeft - walk;
                                this.updateScrollState();
                            }
                        }"
                        class="relative group"
                    >
                        <!-- Left Arrow Trigger -->
                        <button
                            type="button"
                            x-show="canScrollLeft"
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-75"
                            x-transition:enter-end="opacity-100 scale-100"
                            @click="scrollByOffset(-120)"
                            class="absolute -left-2 top-1/2 -translate-y-1/2 z-20 w-6 h-6 rounded-full bg-white dark:bg-slate-700 shadow-md border border-slate-200 dark:border-slate-600 flex items-center justify-center text-slate-700 dark:text-white text-xs hover:bg-slate-50 active:scale-95 transition-all"
                            title="Geser filter ke kiri"
                        >
                            ‹
                        </button>

                        <!-- Scrollable Track -->
                        <div
                            x-ref="scrollContainer"
                            @scroll.passive="updateScrollState()"
                            @wheel.prevent="handleWheel($event)"
                            @mousedown="startDrag($event)"
                            @mouseleave="stopDrag()"
                            @mouseup="stopDrag()"
                            @mousemove="doDrag($event)"
                            class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl overflow-x-auto text-[11px] font-bold select-none cursor-grab active:cursor-grabbing scroll-smooth pb-1.5"
                            style="scrollbar-width: thin; scrollbar-color: rgba(148, 163, 184, 0.4) transparent;"
                        >
                            <button
                                type="button"
                                wire:click="setOccupantFilter('all')"
                                class="px-2.5 py-1.5 rounded-xl transition-all whitespace-nowrap flex items-center gap-1.5 shrink-0 {{ $occupantFilter === 'all' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}"
                            >
                                <span>👥 Semua</span>
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $occupantFilter === 'all' ? 'bg-slate-100 dark:bg-slate-600 text-slate-800 dark:text-slate-200' : 'bg-slate-200 dark:bg-slate-700' }}">{{ $occupantStats['total'] }}</span>
                            </button>

                            <button
                                type="button"
                                wire:click="setOccupantFilter('no_baju')"
                                class="px-2.5 py-1.5 rounded-xl transition-all whitespace-nowrap flex items-center gap-1.5 shrink-0 {{ $occupantFilter === 'no_baju' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-500 hover:text-amber-600 dark:hover:text-amber-400' }}"
                                title="Tampilkan santri yang belum dapat lemari pakaian"
                            >
                                <span>🚪 Belum Lemari</span>
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $occupantFilter === 'no_baju' ? 'bg-white/20 text-white' : ($occupantStats['no_baju'] > 0 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 font-black' : 'bg-slate-200 dark:bg-slate-700') }}">{{ $occupantStats['no_baju'] }}</span>
                            </button>

                            <button
                                type="button"
                                wire:click="setOccupantFilter('no_kitab')"
                                class="px-2.5 py-1.5 rounded-xl transition-all whitespace-nowrap flex items-center gap-1.5 shrink-0 {{ $occupantFilter === 'no_kitab' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-500 hover:text-purple-600 dark:hover:text-purple-400' }}"
                                title="Tampilkan santri yang belum dapat rak kitab"
                            >
                                <span>📚 Belum Rak</span>
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $occupantFilter === 'no_kitab' ? 'bg-white/20 text-white' : ($occupantStats['no_kitab'] > 0 ? 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300 font-black' : 'bg-slate-200 dark:bg-slate-700') }}">{{ $occupantStats['no_kitab'] }}</span>
                            </button>

                            <button
                                type="button"
                                wire:click="setOccupantFilter('complete')"
                                class="px-2.5 py-1.5 rounded-xl transition-all whitespace-nowrap flex items-center gap-1.5 shrink-0 {{ $occupantFilter === 'complete' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-500 hover:text-emerald-600 dark:hover:text-emerald-400' }}"
                                title="Tampilkan santri yang sudah punya lemari dan rak"
                            >
                                <span>✨ Lengkap</span>
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $occupantFilter === 'complete' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700' }}">{{ $occupantStats['complete'] }}</span>
                            </button>
                        </div>

                        <!-- Right Arrow Trigger -->
                        <button
                            type="button"
                            x-show="canScrollRight"
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-75"
                            x-transition:enter-end="opacity-100 scale-100"
                            @click="scrollByOffset(120)"
                            class="absolute -right-2 top-1/2 -translate-y-1/2 z-20 w-6 h-6 rounded-full bg-white dark:bg-slate-700 shadow-md border border-slate-200 dark:border-slate-600 flex items-center justify-center text-slate-700 dark:text-white text-xs hover:bg-slate-50 active:scale-95 transition-all"
                            title="Geser filter ke kanan"
                        >
                            ›
                        </button>
                    </div>

                    <!-- Quick Search Bar -->
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.250ms="occupantSearch"
                            placeholder="Cari & tarik nama santri..."
                            class="w-full text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 pl-8 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:text-slate-200 placeholder-slate-400"
                        />
                        <span class="absolute left-2.5 top-2.5 text-xs text-slate-400">🔍</span>
                        @if($occupantSearch)
                            <button
                                type="button"
                                wire:click="$set('occupantSearch', '')"
                                class="absolute right-2.5 top-2 text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                            >✕</button>
                        @endif
                    </div>

                    <!-- Santri Draggable Cards List -->
                    <div class="space-y-1.5 max-h-[580px] overflow-y-auto pr-1">
                        @forelse($occupantsList as $santri)
                            <div
                                draggable="true"
                                ondragstart="event.dataTransfer.setData('text/plain', '{{ $santri['id'] }}'); event.dataTransfer.setData('santri-name', '{{ addslashes($santri['name']) }}');"
                                class="group p-2.5 rounded-2xl border transition-all cursor-grab active:cursor-grabbing space-y-1.5 {{ $santri['is_complete'] ? 'bg-slate-50/70 dark:bg-slate-800/40 border-slate-200/80 dark:border-slate-800 hover:border-emerald-400' : 'bg-white dark:bg-slate-800 border-amber-200/80 dark:border-amber-900/40 shadow-2xs hover:border-amber-400' }}"
                            >
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black shrink-0 {{ $santri['is_complete'] ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                                            {{ strtoupper(substr($santri['name'], 0, 1)) }}
                                        </div>
                                        <div class="truncate">
                                            <div class="font-bold text-xs text-slate-800 dark:text-slate-100 truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                {{ $santri['name'] }}
                                            </div>
                                            <div class="text-[10px] font-mono text-slate-400">
                                                NIS: {{ $santri['nis'] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="shrink-0 flex items-center gap-1.5">
                                        @if($santri['is_complete'])
                                            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200/60 dark:border-emerald-900 px-1.5 py-0.2 rounded-md">
                                                ✓ Lengkap
                                            </span>
                                        @else
                                            <span class="text-[9px] font-bold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 border border-amber-200/60 dark:border-amber-900 px-1.5 py-0.2 rounded-md">
                                                Perlu Plot
                                            </span>
                                        @endif
                                        <span class="text-slate-300 group-hover:text-slate-500 transition-colors text-xs select-none">⋮⋮</span>
                                    </div>
                                </div>

                                <!-- Locker Badges (Status Lemari & Rak) -->
                                <div class="flex items-center gap-1.5 flex-wrap pt-0.5 text-[10px]">
                                    @if($santri['has_baju'])
                                        <span class="px-1.5 py-0.5 bg-blue-50 text-blue-700 dark:bg-blue-950/70 dark:text-blue-300 border border-blue-200/60 dark:border-blue-900 rounded-md font-semibold flex items-center gap-1">
                                            <span>🚪</span>
                                            <span>Lemari: {{ $santri['baju_code'] }}</span>
                                        </span>
                                    @else
                                        <span class="px-1.5 py-0.5 bg-amber-50 text-amber-700 dark:bg-amber-950/70 dark:text-amber-300 border border-amber-200/60 dark:border-amber-900 rounded-md font-semibold flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            <span>⚠️ Belum Lemari</span>
                                        </span>
                                    @endif

                                    @if($santri['has_kitab'])
                                        <span class="px-1.5 py-0.5 bg-purple-50 text-purple-700 dark:bg-purple-950/70 dark:text-purple-300 border border-purple-200/60 dark:border-purple-900 rounded-md font-semibold flex items-center gap-1">
                                            <span>📚</span>
                                            <span>Rak: {{ $santri['kitab_code'] }}</span>
                                        </span>
                                    @else
                                        <span class="px-1.5 py-0.5 bg-rose-50 text-rose-700 dark:bg-rose-950/70 dark:text-rose-300 border border-rose-200/60 dark:border-rose-900 rounded-md font-semibold flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            <span>⚠️ Belum Rak</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center px-3 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl space-y-2">
                                @if($occupantFilter === 'no_baju')
                                    <span class="text-2xl">🎉</span>
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Semua Sudah Dapat Lemari</p>
                                    <p class="text-[11px] text-slate-400">Seluruh penghuni kamar sudah memiliki slot lemari pakaian.</p>
                                    <button type="button" wire:click="setOccupantFilter('all')" class="mt-1 text-xs text-blue-600 hover:underline font-bold">
                                        Tampilkan Semua
                                    </button>
                                @elseif($occupantFilter === 'no_kitab')
                                    <span class="text-2xl">✨</span>
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Semua Sudah Dapat Rak Kitab</p>
                                    <p class="text-[11px] text-slate-400">Seluruh penghuni kamar sudah memiliki slot rak kitab.</p>
                                    <button type="button" wire:click="setOccupantFilter('all')" class="mt-1 text-xs text-blue-600 hover:underline font-bold">
                                        Tampilkan Semua
                                    </button>
                                @elseif($occupantFilter === 'complete')
                                    <span class="text-2xl">📋</span>
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Belum Ada yang Lengkap</p>
                                    <p class="text-[11px] text-slate-400">Tarik santri ke lemari dan rak untuk melengkapinya.</p>
                                    <button type="button" wire:click="setOccupantFilter('all')" class="mt-1 text-xs text-blue-600 hover:underline font-bold">
                                        Tampilkan Semua
                                    </button>
                                @else
                                    <span class="text-2xl">🔍</span>
                                    <p class="text-xs text-slate-400">Tidak ada santri yang cocok dengan kriteria pencarian.</p>
                                @endif
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>

        <!-- RIGHT CANVAS: Denah Visual Perabot Kamar (col-span-12 lg:col-span-8 xl:col-span-9) -->
        <div :class="mobileTab === 'canvas' ? 'block' : 'hidden lg:block'" class="lg:col-span-8 xl:col-span-9 space-y-6">
            @if(!$activeRoom)
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-12 text-center border border-slate-100 dark:border-slate-800 shadow-sm space-y-3">
                    <span class="text-5xl">🚪</span>
                    <h3 class="text-base font-bold text-slate-700 dark:text-slate-300">Pilih Kamar Terlebih Dahulu</h3>
                    <p class="text-xs text-slate-400">Pilih salah satu kamar asrama dari menu sebelah kiri untuk melihat denah lemarinya.</p>
                </div>
            @else
                <!-- Unified Compact Room Banner & Navigation (Ringkas & Hemat Ruang) -->
                <div class="bg-gradient-to-r from-blue-700 via-indigo-800 to-slate-900 text-white rounded-2xl p-3 sm:p-4 shadow-md">
                    <!-- Top Row: Identitas Kamar, Dropdown, Stepper & Quick Stats -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <!-- Left: Nama Kamar & Asrama & Dropdown -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 bg-white/20 backdrop-blur-md rounded-lg text-[10px] font-black uppercase tracking-wider text-blue-100">
                                {{ $activeRoom->dormitory->name }}
                            </span>

                            <h2 class="text-lg sm:text-xl font-black tracking-tight flex items-center gap-1.5">
                                <span>Kamar: {{ $activeRoom->name }}</span>
                            </h2>

                            <!-- Dropdown Cepat Pilih Kamar (Alpine.js Popover) -->
                            <div class="relative" x-data="{ openPicker: false, pickerFilter: '' }">
                                <button
                                    type="button"
                                    @click="openPicker = !openPicker"
                                    class="px-2.5 py-1 bg-white/15 hover:bg-white/25 active:scale-95 text-white text-xs font-bold rounded-lg border border-white/20 backdrop-blur-md transition-all flex items-center gap-1 cursor-pointer"
                                    title="Pilih langsung dari daftar seluruh kamar"
                                >
                                    <span>📋 Ganti Kamar ▾</span>
                                </button>

                                <!-- Popover Panel -->
                                <div
                                    x-show="openPicker"
                                    @click.outside="openPicker = false"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                    class="absolute left-0 mt-2 w-72 sm:w-80 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 p-3 z-50 text-slate-800 dark:text-slate-200"
                                    style="display: none;"
                                >
                                    <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                                        <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Pilih Kamar Cepat</span>
                                        <span class="text-[10px] text-slate-400">{{ count($availableRoomsList) }} Kamar</span>
                                    </div>

                                    <div class="mt-2">
                                        <input
                                            type="text"
                                            x-model="pickerFilter"
                                            placeholder="Cari kamar / asrama..."
                                            class="w-full text-xs px-3 py-1.5 bg-slate-100 dark:bg-slate-800 rounded-xl border-none focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-white placeholder-slate-400"
                                        >
                                    </div>

                                    <div class="mt-2 max-h-60 overflow-y-auto space-y-1" style="scrollbar-width: thin;">
                                        @foreach($availableRoomsList as $roomItem)
                                            <button
                                                type="button"
                                                x-show="!pickerFilter || '{{ strtolower($roomItem['name'] . ' ' . $roomItem['dormitory_name']) }}'.includes(pickerFilter.toLowerCase())"
                                                wire:click="selectRoom('{{ $roomItem['id'] }}')"
                                                @click="openPicker = false"
                                                class="w-full text-left px-3 py-1.5 rounded-xl text-xs transition-all flex items-center justify-between {{ $roomItem['id'] === $activeRoom->id ? 'bg-blue-600 text-white font-black' : 'hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300' }}"
                                            >
                                                <div class="truncate">
                                                    <div class="flex items-center gap-1.5 truncate">
                                                        <span class="font-bold truncate">{{ $roomItem['name'] }}</span>
                                                        <span class="text-[10px] px-1 py-0.2 rounded {{ $roomItem['id'] === $activeRoom->id ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                                            {{ $roomItem['dormitory_name'] }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <span class="text-[10px] font-semibold shrink-0 ml-2 {{ $roomItem['id'] === $activeRoom->id ? 'text-white' : ($roomItem['assigned_count'] >= $roomItem['capacity'] ? 'text-rose-400' : 'text-emerald-400') }}">
                                                    {{ $roomItem['assigned_count'] }}/{{ $roomItem['capacity'] }}
                                                </span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Stepper Buttons & Stats -->
                        <div class="flex items-center gap-2 justify-between sm:justify-end shrink-0 flex-wrap">
                            <div class="flex items-center gap-1.5 text-xs">
                                <span class="px-2 py-0.5 bg-emerald-500/25 border border-emerald-400/30 rounded-md text-[11px] font-bold text-emerald-200">
                                    {{ $activeRoom->currentAssignments->count() }}/{{ $activeRoom->capacity }} Bed
                                </span>
                                <span class="px-2 py-0.5 bg-white/10 rounded-md text-[11px] font-bold text-blue-100">
                                    {{ count($furnitureUnits) }} Perabot
                                </span>
                            </div>

                            <!-- Stepper Panah: Sebelumnya & Berikutnya -->
                            <div class="flex items-center gap-1 bg-white/10 p-0.5 rounded-lg border border-white/15">
                                <button
                                    type="button"
                                    wire:click="goToPreviousRoom"
                                    class="px-2.5 py-1 rounded-md bg-white/10 hover:bg-white/25 active:scale-95 text-white text-xs font-bold transition-all flex items-center gap-1 cursor-pointer"
                                    title="Kamar Sebelumnya"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                                    <span class="hidden sm:inline">Sebelumnya</span>
                                </button>
                                @if($currentRoomIndex && $totalRoomsCount)
                                    <span class="text-[10px] text-white/60 px-1 font-bold">
                                        {{ $currentRoomIndex }}/{{ $totalRoomsCount }}
                                    </span>
                                @endif
                                <button
                                    type="button"
                                    wire:click="goToNextRoom"
                                    class="px-2.5 py-1 rounded-md bg-white/10 hover:bg-white/25 active:scale-95 text-white text-xs font-bold transition-all flex items-center gap-1 cursor-pointer"
                                    title="Kamar Berikutnya"
                                >
                                    <span class="hidden sm:inline">Berikutnya</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Row: Strip Kamar Berdampingan (Terintegrasi Langsung, Ramping & Hemat Ruang) -->
                    @if(isset($dormitoryRooms) && $dormitoryRooms->count() > 1)
                        <div class="mt-2.5 pt-2 border-t border-white/10">
                            <div 
                                class="flex items-center gap-1.5 overflow-x-auto select-none"
                                onwheel="this.scrollLeft += (event.deltaY || event.deltaX) * 0.85; event.preventDefault();"
                                style="scrollbar-width: thin; scrollbar-color: rgba(255, 255, 255, 0.2) transparent;"
                            >
                                @foreach($dormitoryRooms as $dRoom)
                                    @php
                                        $isCurrent = $dRoom->id === $activeRoom->id;
                                        $isFull = $dRoom->current_assignments_count >= $dRoom->capacity;
                                    @endphp
                                    <button
                                        type="button"
                                        wire:click="selectRoom('{{ $dRoom->id }}')"
                                        class="shrink-0 px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer {{ $isCurrent ? 'bg-white text-blue-900 shadow-xs ring-2 ring-white/60' : 'bg-white/10 hover:bg-white/20 text-white/90' }}"
                                    >
                                        <span>{{ $isCurrent ? '📍' : '🚪' }} {{ $dRoom->name }}</span>
                                        <span class="text-[10px] px-1.5 py-0.2 rounded-md font-extrabold {{ $isCurrent ? 'bg-blue-100 text-blue-900' : ($isFull ? 'bg-rose-500/40 text-rose-100' : 'bg-emerald-500/30 text-emerald-100') }}">
                                            {{ $dRoom->current_assignments_count }}/{{ $dRoom->capacity }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Unit Filter Tabs (Lemari vs Rak Kitab vs Fasilitas Kamar) -->
                <div 
                    class="flex items-center gap-2 p-1.5 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl overflow-x-auto text-xs font-bold shadow-2xs select-none"
                    onwheel="this.scrollLeft += (event.deltaY || event.deltaX) * 0.85; event.preventDefault();"
                    style="scrollbar-width: thin; scrollbar-color: rgba(148, 163, 184, 0.4) transparent;"
                >
                    <button
                        type="button"
                        wire:click="setActiveUnitTab('all')"
                        class="px-3 py-1.5 rounded-xl transition-all flex items-center gap-2 whitespace-nowrap {{ $activeUnitTab === 'all' ? 'bg-blue-600 text-white shadow-xs font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                    >
                        <span>👁️ Semua Perabot</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeUnitTab === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800' }}">{{ $unitCounts['all'] }}</span>
                    </button>

                    @if($unitCounts['baju'] > 0)
                        <button
                            type="button"
                            wire:click="setActiveUnitTab('baju')"
                            class="px-3 py-1.5 rounded-xl transition-all flex items-center gap-2 whitespace-nowrap {{ $activeUnitTab === 'baju' ? 'bg-blue-600 text-white shadow-xs font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                        >
                            <span>🚪 Lemari Pakaian</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeUnitTab === 'baju' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800' }}">{{ $unitCounts['baju'] }}</span>
                        </button>
                    @endif

                    @if($unitCounts['kitab'] > 0)
                        <button
                            type="button"
                            wire:click="setActiveUnitTab('kitab')"
                            class="px-3 py-1.5 rounded-xl transition-all flex items-center gap-2 whitespace-nowrap {{ $activeUnitTab === 'kitab' ? 'bg-emerald-600 text-white shadow-xs font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                        >
                            <span>📚 Rak Kitab</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeUnitTab === 'kitab' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800' }}">{{ $unitCounts['kitab'] }}</span>
                        </button>
                    @endif

                    @if($unitCounts['single'] > 0)
                        <button
                            type="button"
                            wire:click="setActiveUnitTab('single')"
                            class="px-3 py-1.5 rounded-xl transition-all flex items-center gap-2 whitespace-nowrap {{ $activeUnitTab === 'single' ? 'bg-purple-600 text-white shadow-xs font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                        >
                            <span>🛋️ Fasilitas Kamar</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeUnitTab === 'single' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800' }}">{{ $unitCounts['single'] }}</span>
                        </button>
                    @endif
                </div>

                <!-- Units Canvas Loop -->
                @forelse($visibleUnits as $unit)
                    @php
                        $isCompartment = ($unit['category'] ?? '') === 'compartment';
                        $unitType = $unit['type'] ?? 'baju';
                    @endphp

                    @if($isCompartment)
                        {{-- LEMARI / RAK BER-SLOT --}}
                        @php
                            $unitVacantCount = 0;
                            foreach ($unit['rows'] ?? [] as $r) {
                                foreach ($r['slots'] ?? [] as $s) {
                                    $sAlloc = $s['allocation_type'] ?? 'santri';
                                    $sOcc = !empty($s['assigned_person_name']) || in_array($sAlloc, ['inventaris', 'external']);
                                    $sUsable = !in_array($s['condition'] ?? 'baik', ['rusak_berat', 'hilang']);
                                    if (!$sOcc && $sUsable) {
                                        $unitVacantCount++;
                                    }
                                }
                            }
                            $isUnitHighlighted = $highlightAllVacant || in_array($unit['id'], $highlightVacantUnitIds);
                        @endphp
                        <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-6 border {{ $isUnitHighlighted && $unitVacantCount > 0 ? 'border-emerald-300 dark:border-emerald-800 shadow-lg shadow-emerald-500/10' : 'border-slate-200/80 dark:border-slate-800 shadow-sm' }} space-y-4">
                            <!-- Unit Header -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-2xl flex items-center justify-center text-lg shrink-0 {{ $unitType === 'kitab' ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700' : 'bg-blue-100 dark:bg-blue-950 text-blue-700' }}">
                                        {{ $unitType === 'kitab' ? '📚' : '🚪' }}
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2 flex-wrap">
                                            <span>{{ $unit['name'] }}</span>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0 {{ $unitType === 'kitab' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' }}">
                                                {{ $unitType === 'kitab' ? 'Rak Kitab' : 'Lemari Pakaian' }}
                                            </span>
                                        </h3>
                                        <p class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-2">
                                            <span>{{ count($unit['rows'] ?? []) }} Baris Kompartemen</span>
                                            <span class="sm:hidden text-[10px] text-blue-500 font-semibold">👈 Geser daun pintu 👉</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                                    <!-- Tombol Cek Tempat Kosong (Menyala) -->
                                    <button
                                        type="button"
                                        wire:click="toggleHighlightVacant('{{ $unit['id'] }}')"
                                        class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $isUnitHighlighted ? 'bg-emerald-600 text-white shadow-md shadow-emerald-500/30 ring-2 ring-emerald-400 font-extrabold' : ($unitVacantCount > 0 ? 'bg-emerald-50 hover:bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-300/80 dark:border-emerald-800' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-400') }}"
                                        title="{{ $unitVacantCount > 0 ? ($isUnitHighlighted ? 'Matikan indikator menyala' : 'Cek '.$unitVacantCount.' tempat kosong di lemari ini') : 'Semua slot di unit ini terisi' }}"
                                    >
                                        <span>{{ $isUnitHighlighted ? '✨' : '💡' }}</span>
                                        <span>{{ $isUnitHighlighted ? 'Menyala' : 'Cek Kosong' }}</span>
                                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $isUnitHighlighted ? 'bg-white/20 text-white' : ($unitVacantCount > 0 ? 'bg-emerald-200/90 text-emerald-900 dark:bg-emerald-900 dark:text-emerald-200 font-black' : 'bg-slate-200 text-slate-500 dark:bg-slate-700 dark:text-slate-400') }}">
                                            {{ $unitVacantCount }}
                                        </span>
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="openBuilderModal('{{ $unit['id'] }}')"
                                        class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition-all"
                                        title="Ubah konfigurasi bentuk lemari"
                                    >
                                        ✏️ Edit Bentuk
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="confirmDeleteFurnitureUnit('{{ $unit['id'] }}')"
                                        class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl transition-colors"
                                        title="Hapus unit perabot ini"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Physical Wardrobe Frame (Rangka Visual Lemari Bersih) -->
                            <div class="p-3 sm:p-5 rounded-3xl bg-slate-50/60 dark:bg-slate-950/60 border border-slate-200/80 dark:border-slate-800 space-y-3">
                                @foreach($unit['rows'] ?? [] as $rIndex => $row)
                                    @php
                                        $slotCount = count($row['slots'] ?? []);
                                    @endphp
                                    <div class="overflow-x-auto pb-2 -mx-1 px-1 sm:mx-0 sm:px-0 scrollbar-thin">
                                        <div
                                            class="grid gap-2.5 sm:gap-3"
                                            style="grid-template-columns: repeat({{ max(1, $slotCount) }}, minmax(135px, 1fr)); min-width: {{ max(1, $slotCount) * 140 }}px;"
                                        >
                                        @foreach($row['slots'] ?? [] as $slot)
                                            @php
                                                $alloc = $slot['allocation_type'] ?? 'santri';
                                                $isSantriOccupied = !empty($slot['assigned_person_name']);
                                                $isSpecialOccupied = in_array($alloc, ['inventaris', 'external']);
                                                $isOccupied = $isSantriOccupied || $isSpecialOccupied;
                                                $isDamaged = in_array($slot['condition'] ?? 'baik', ['rusak_ringan', 'rusak_berat', 'hilang']);
                                                $isUsable = !in_array($slot['condition'] ?? 'baik', ['rusak_berat', 'hilang']);
                                                $isVacantAndUsable = !$isOccupied && $isUsable;
                                                $isGlowing = $isUnitHighlighted && $isVacantAndUsable;
                                                $isDimmed = $isUnitHighlighted && !$isVacantAndUsable;
                                                $displayName = $isSantriOccupied ? $slot['assigned_person_name'] : ($slot['custom_holder_name'] ?: ($alloc === 'inventaris' ? 'Kas / Inventaris' : 'Pengurus / Tamu'));
                                            @endphp

                                            <!-- Daun Pintu / Kotak Sekat Lemari (Modern Compact) -->
                                            <div
                                                ondragover="event.preventDefault();"
                                                ondrop="event.preventDefault(); var pid = event.dataTransfer.getData('text/plain'); if(pid) { @this.quickAssignFromDrag('{{ $slot['code'] }}', pid); }"
                                                class="group relative rounded-2xl p-2.5 sm:p-3 transition-all flex flex-col justify-between min-h-[115px] border {{ $isGlowing ? 'ring-4 ring-emerald-400 dark:ring-emerald-500 border-2 border-emerald-500 bg-emerald-50/95 dark:bg-emerald-950/90 shadow-xl shadow-emerald-500/30 scale-[1.02] animate-pulse z-10' : ($isDimmed ? 'opacity-35 hover:opacity-100 transition-opacity bg-white dark:bg-slate-900 border-slate-200/80 dark:border-slate-800' : ($isDamaged ? ($slot['condition'] === 'rusak_berat' ? 'bg-rose-50/40 dark:bg-rose-950/20 border-rose-300 dark:border-rose-900/60 border-l-4 border-l-rose-500' : 'bg-amber-50/40 dark:bg-amber-950/20 border-amber-300 dark:border-amber-900/60 border-l-4 border-l-amber-500') : ($alloc === 'inventaris' ? 'bg-amber-50/30 dark:bg-amber-950/20 border-amber-300/80 dark:border-amber-900/60 shadow-2xs hover:border-amber-400' : ($alloc === 'external' ? 'bg-indigo-50/30 dark:bg-indigo-950/20 border-indigo-300/80 dark:border-indigo-900/60 shadow-2xs hover:border-indigo-400' : ($isOccupied ? 'bg-white dark:bg-slate-900 border-slate-200/90 dark:border-slate-800 shadow-2xs hover:border-blue-400 dark:hover:border-blue-500' : 'bg-slate-50/60 dark:bg-slate-900/40 border-dashed border-slate-300 dark:border-slate-700/80 hover:border-emerald-400'))))) }}"
                                            >
                                                <!-- Top Bar: Plat Nomor & Status Kondisi -->
                                                <div class="flex items-center justify-between gap-1.5">
                                                    <span class="font-extrabold text-[11px] font-mono px-2 py-0.5 rounded-lg shrink-0 whitespace-nowrap {{ $unitType === 'kitab' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800' : 'bg-blue-50 text-blue-700 dark:bg-blue-950/70 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800' }}">
                                                        {{ $slot['code'] }}
                                                    </span>

                                                    <!-- Condition Pill (Clickable to quick inspect) -->
                                                    <button
                                                        type="button"
                                                        wire:click="openConditionModal('slot', '{{ $unit['id'] }}', '{{ $slot['code'] }}', '{{ $slot['condition'] }}', '{{ addslashes($slot['notes']) }}')"
                                                        class="text-[10px] transition-all font-bold px-1.5 py-0.5 rounded-md flex items-center gap-1 shrink-0 whitespace-nowrap {{ $slot['condition'] === 'baik' ? 'text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800' : ($slot['condition'] === 'rusak_ringan' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300') }}"
                                                        title="Kondisi fisik (Klik untuk ubah/catat kerusakan)"
                                                    >
                                                        @if($slot['condition'] === 'baik')
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                                            <span class="text-[9px] uppercase tracking-wider text-emerald-600 dark:text-emerald-400 whitespace-nowrap">Baik</span>
                                                        @elseif($slot['condition'] === 'rusak_ringan')
                                                            <span class="whitespace-nowrap">⚠️ Rusak</span>
                                                        @elseif($slot['condition'] === 'rusak_berat')
                                                            <span class="whitespace-nowrap">🔴 Jebol</span>
                                                        @elseif($slot['condition'] === 'hilang')
                                                            <span class="whitespace-nowrap">🟣 Hilang</span>
                                                        @endif
                                                    </button>
                                                </div>

                                                <!-- Middle: Visual Daun Pintu / Handle Lemari & Isi Santri -->
                                                <div class="py-1">
                                                    @if($isSantriOccupied)
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-black text-[10px] flex items-center justify-center border border-slate-200 dark:border-slate-700 shrink-0">
                                                                {{ strtoupper(substr($slot['assigned_person_name'], 0, 2)) }}
                                                            </div>
                                                            <div class="min-w-0 flex-1">
                                                                <div class="font-bold text-xs text-slate-800 dark:text-white truncate" title="{{ $slot['assigned_person_name'] }}">
                                                                    {{ $slot['assigned_person_name'] }}
                                                                </div>
                                                                <div class="text-[10px] text-slate-400 font-mono truncate">
                                                                    NIS: {{ $slot['assigned_person_nis'] }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @elseif($alloc === 'inventaris')
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-6 h-6 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 font-black text-xs flex items-center justify-center border border-amber-300 dark:border-amber-800 shrink-0">
                                                                💰
                                                            </div>
                                                            <div class="min-w-0 flex-1">
                                                                <div class="font-extrabold text-xs text-amber-900 dark:text-amber-200 truncate" title="{{ $slot['custom_holder_name'] }}">
                                                                    {{ $slot['custom_holder_name'] ?: 'Kas / Uang Bendahara' }}
                                                                </div>
                                                                <div class="text-[9px] font-bold text-amber-600 dark:text-amber-400 truncate">
                                                                    🔒 Khusus Inventaris
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @elseif($alloc === 'external')
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300 font-black text-[10px] flex items-center justify-center border border-indigo-300 dark:border-indigo-800 shrink-0">
                                                                👤
                                                            </div>
                                                            <div class="min-w-0 flex-1">
                                                                <div class="font-extrabold text-xs text-indigo-900 dark:text-indigo-200 truncate" title="{{ $slot['custom_holder_name'] }}">
                                                                    {{ $slot['custom_holder_name'] ?: 'Pengurus / Tamu' }}
                                                                </div>
                                                                <div class="text-[9px] font-bold text-indigo-600 dark:text-indigo-400 truncate">
                                                                    Non-Sistem / Pembina
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <!-- Slot Kosong -->
                                                        <div class="text-center py-1">
                                                            @if($isGlowing)
                                                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-600 text-white text-[10px] font-black shadow-sm whitespace-nowrap">
                                                                    <span>✨</span>
                                                                    <span>SIAP PAKAI</span>
                                                                </div>
                                                                <span class="text-[9px] font-extrabold text-emerald-700 dark:text-emerald-300 uppercase tracking-wider block mt-0.5 whitespace-nowrap">
                                                                    Slot Kosong
                                                                </span>
                                                            @else
                                                                <span class="text-sm opacity-60 block leading-tight">📭</span>
                                                                <span class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider block whitespace-nowrap">
                                                                    Kosong
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endif

                                                    @if(!empty($slot['notes']))
                                                        <div class="mt-1 px-1.5 py-0.5 bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 text-[9px] font-semibold rounded border border-amber-200/50 line-clamp-1" title="{{ $slot['notes'] }}">
                                                            📝 {{ $slot['notes'] }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Bottom Bar: Tombol Aksi Pasang / Tukar -->
                                                <div class="pt-1.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[10px]">
                                                    @if($isOccupied)
                                                        <button
                                                            type="button"
                                                            wire:click="openAssignModal('{{ $slot['code'] }}', '{{ $unitType }}', '{{ $slot['assigned_person_id'] }}', '{{ $alloc }}', '{{ addslashes($slot['custom_holder_name'] ?? '') }}')"
                                                            class="text-blue-600 hover:text-blue-700 dark:text-blue-400 font-bold hover:underline whitespace-nowrap"
                                                        >
                                                            Ganti
                                                        </button>
                                                        <button
                                                            type="button"
                                                            wire:click="confirmUnassignSlot('{{ $slot['code'] }}')"
                                                            class="text-rose-500 hover:text-rose-700 font-bold whitespace-nowrap"
                                                            title="Lepaskan penghuni dari loker ini"
                                                        >
                                                            Lepas
                                                        </button>
                                                    @else
                                                        <button
                                                            type="button"
                                                            wire:click="openAssignModal('{{ $slot['code'] }}', '{{ $unitType }}', null, 'santri', '')"
                                                            class="{{ $isGlowing ? 'w-full text-center py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-black text-[10px] shadow-sm transition-all whitespace-nowrap' : 'w-full text-center text-blue-600 hover:text-blue-700 dark:text-blue-400 font-bold hover:underline text-[10px] whitespace-nowrap' }}"
                                                        >
                                                            {{ $isGlowing ? '+ Pasang Santri' : '+ Isi Lemari' }}
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        {{-- FASILITAS RUANGAN UMUM (Kipas, Lampu, Kasur, dsb) --}}
                        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center text-xl">
                                    🔌
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-sm text-slate-800 dark:text-white flex items-center gap-2">
                                        <span>{{ $unit['name'] }}</span>
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 uppercase">
                                            Fasilitas Umum
                                        </span>
                                    </h4>
                                    @if(!empty($unit['notes']))
                                        <p class="text-xs text-rose-500 font-medium mt-0.5">Catatan: {{ $unit['notes'] }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    wire:click="openConditionModal('single', '{{ $unit['id'] }}', null, '{{ $unit['condition'] ?? 'baik' }}', '{{ addslashes($unit['notes'] ?? '') }}')"
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold border transition-all flex items-center gap-1.5 {{ ($unit['condition'] ?? 'baik') === 'baik' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border-rose-300' }}"
                                >
                                    <span>{{ ($unit['condition'] ?? 'baik') === 'baik' ? '🟢 Kondisi Baik' : '⚠️ ' . ucfirst($unit['condition'] ?? 'rusak') }}</span>
                                    <span class="text-[10px] text-slate-400">✏️</span>
                                </button>

                                <button
                                    type="button"
                                    wire:click="confirmDeleteFurnitureUnit('{{ $unit['id'] }}')"
                                    class="p-2 text-rose-400 hover:text-rose-600 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800"
                                    title="Hapus fasilitas ini"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    @endif
                @empty
                    <!-- Empty State: Belum ada perabot diset -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-10 text-center border-2 border-dashed border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                        <span class="text-5xl block">🚪</span>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Kamar ini belum memiliki denah perabot</h3>
                        <p class="text-xs text-slate-400 max-w-md mx-auto">
                            Gunakan tombol di bawah untuk mendesain lemari pakaian, rak kitab, atau fasilitas lainnya sesuai bentuk fisik lemari di kamar {{ $activeRoom->name }}.
                        </p>
                        <button
                            type="button"
                            wire:click="openBuilderModal()"
                            class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-2xl text-xs shadow-lg transition-all"
                        >
                            + Desain Bentuk Lemari Sekarang
                        </button>
                    </div>
                @endforelse
            @endif
        </div>
    </div>

    <!-- MODAL 1: QUICK INSPECTION / PENCATATAN KERUSAKAN -->
    @if($showConditionModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                        <span>🔍</span>
                        <span>Inspeksi Fisik {{ $targetSlotCode ?: 'Fasilitas' }}</span>
                    </h3>
                    <button type="button" wire:click="$set('showConditionModal', false)" class="text-slate-400 hover:text-slate-600 p-1 rounded-xl">
                        ✕
                    </button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-2">Pilih Kondisi Fisik:</label>
                        <div class="grid grid-cols-2 gap-2 font-bold">
                            <button
                                type="button"
                                wire:click="setQuickCondition('baik')"
                                class="p-3 rounded-2xl border text-left flex items-center gap-2 transition-all {{ $selectedCondition === 'baik' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}"
                            >
                                <span class="text-lg">🟢</span>
                                <div>
                                    <div>Baik</div>
                                    <div class="text-[10px] opacity-80 font-normal">Normal &amp; layak pakai</div>
                                </div>
                            </button>

                            <button
                                type="button"
                                wire:click="setQuickCondition('rusak_ringan')"
                                class="p-3 rounded-2xl border text-left flex items-center gap-2 transition-all {{ $selectedCondition === 'rusak_ringan' ? 'bg-amber-500 text-white border-amber-500 shadow-sm' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}"
                            >
                                <span class="text-lg">🟡</span>
                                <div>
                                    <div>Rusak Ringan</div>
                                    <div class="text-[10px] opacity-80 font-normal">Kunci macet, goyang</div>
                                </div>
                            </button>

                            <button
                                type="button"
                                wire:click="setQuickCondition('rusak_berat')"
                                class="p-3 rounded-2xl border text-left flex items-center gap-2 transition-all {{ $selectedCondition === 'rusak_berat' ? 'bg-rose-600 text-white border-rose-600 shadow-sm' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}"
                            >
                                <span class="text-lg">🔴</span>
                                <div>
                                    <div>Rusak Berat</div>
                                    <div class="text-[10px] opacity-80 font-normal">Pintu lepas, jebol</div>
                                </div>
                            </button>

                            <button
                                type="button"
                                wire:click="setQuickCondition('hilang')"
                                class="p-3 rounded-2xl border text-left flex items-center gap-2 transition-all {{ $selectedCondition === 'hilang' ? 'bg-purple-600 text-white border-purple-600 shadow-sm' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}"
                            >
                                <span class="text-lg">🟣</span>
                                <div>
                                    <div>Hilang</div>
                                    <div class="text-[10px] opacity-80 font-normal">Fisik tidak ada</div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Catatan Kerusakan (Opsional):</label>
                        <textarea
                            wire:model="conditionNotes"
                            rows="2"
                            placeholder="Contoh: Kunci gembok hilang, engsel bawah patah..."
                            class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-2 text-slate-800 dark:text-slate-100"
                        ></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" wire:click="$set('showConditionModal', false)" class="px-4 py-2 bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold rounded-xl text-xs">
                        Batal
                    </button>
                    <button type="button" wire:click="saveConditionModal" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-xl text-xs shadow-sm">
                        Simpan Kondisi
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 2: PILIH SANTRI PEMEGANG LOKER -->
    @if($showAssignModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                        <span>👤</span>
                        <span>Pemegang Loker {{ $assignSlotCode }}</span>
                    </h3>
                    <button type="button" wire:click="$set('showAssignModal', false)" class="text-slate-400 hover:text-slate-600 p-1 rounded-xl">
                        ✕
                    </button>
                </div>

                <div class="space-y-4 text-xs">
                    <!-- Segmented Mode Switcher: Santri Kamar vs Khusus Inventaris vs Pengurus Non-Sistem -->
                    <div class="space-y-1.5">
                        <label class="block font-bold text-slate-700 dark:text-slate-300">Pilih Tipe Alokasi Loker:</label>
                        <div class="grid grid-cols-3 gap-1 p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl text-[11px] font-bold">
                            <button
                                type="button"
                                wire:click="$set('assignAllocationType', 'santri')"
                                class="py-2 px-1 rounded-xl text-center transition-all flex flex-col items-center gap-0.5 {{ $assignAllocationType === 'santri' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-2xs font-extrabold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}"
                            >
                                <span class="text-sm">👥</span>
                                <span class="truncate">Santri Kamar</span>
                            </button>

                            <button
                                type="button"
                                wire:click="$set('assignAllocationType', 'inventaris')"
                                class="py-2 px-1 rounded-xl text-center transition-all flex flex-col items-center gap-0.5 {{ $assignAllocationType === 'inventaris' ? 'bg-amber-500 text-white shadow-2xs font-extrabold' : 'text-slate-500 hover:text-amber-600 dark:hover:text-amber-400' }}"
                            >
                                <span class="text-sm">💰</span>
                                <span class="truncate">Kas / Khusus</span>
                            </button>

                            <button
                                type="button"
                                wire:click="$set('assignAllocationType', 'external')"
                                class="py-2 px-1 rounded-xl text-center transition-all flex flex-col items-center gap-0.5 {{ $assignAllocationType === 'external' ? 'bg-indigo-600 text-white shadow-2xs font-extrabold' : 'text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400' }}"
                            >
                                <span class="text-sm">👤</span>
                                <span class="truncate">Pengurus / Luar</span>
                            </button>
                        </div>
                    </div>

                    <!-- MODE 1: SANTRI MUKIM KAMAR -->
                    @if($assignAllocationType === 'santri')
                        <div class="space-y-2">
                            <p class="text-slate-500">Pilih santri penghuni kamar {{ $activeRoom->name }} yang akan memegang lemari ini:</p>

                            <div class="space-y-1.5 max-h-56 overflow-y-auto">
                                <label class="flex items-center gap-3 p-2.5 rounded-2xl border border-slate-200/80 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 cursor-pointer hover:bg-slate-100">
                                    <input type="radio" wire:model="assignSelectedPersonId" value="" class="text-blue-600">
                                    <span class="font-bold text-slate-600 dark:text-slate-300">📭 Kosongkan Lemari (Tanpa Pemilik)</span>
                                </label>

                                @foreach($occupantsList as $occ)
                                    <label class="flex items-center gap-3 p-2.5 rounded-2xl border border-slate-200/80 dark:border-slate-700 bg-white dark:bg-slate-900 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                        <input type="radio" wire:model="assignSelectedPersonId" value="{{ $occ['id'] }}" class="text-blue-600">
                                        <div class="flex-1 min-w-0">
                                            <div class="font-extrabold text-slate-800 dark:text-white truncate">{{ $occ['name'] }}</div>
                                            <div class="text-[10px] text-slate-400">
                                                NIS: {{ $occ['nis'] }}
                                                @if($assignSlotType === 'baju' && $occ['baju_code'] && $occ['baju_code'] !== $assignSlotCode)
                                                    • <span class="text-amber-500">Saat ini di {{ $occ['baju_code'] }} (akan dipindah)</span>
                                                @elseif($assignSlotType === 'kitab' && $occ['kitab_code'] && $occ['kitab_code'] !== $assignSlotCode)
                                                    • <span class="text-purple-500">Saat ini di {{ $occ['kitab_code'] }} (akan dipindah)</span>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                    <!-- MODE 2: KEPERLUAN KHUSUS / INVENTARIS KAMAR / BENDAHARA -->
                    @elseif($assignAllocationType === 'inventaris')
                        <div class="space-y-3 p-3 bg-amber-50/50 dark:bg-amber-950/20 rounded-2xl border border-amber-200 dark:border-amber-900/60">
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pilih Keperluan Khusus:</label>
                                <div class="grid grid-cols-2 gap-1.5">
                                    <button
                                        type="button"
                                        wire:click="setAssignInventarisPreset('Kas / Uang Bendahara')"
                                        class="p-2 rounded-xl text-left border text-xs transition-all flex items-center gap-1.5 {{ $assignCustomHolderName === 'Kas / Uang Bendahara' ? 'bg-amber-500 text-white border-amber-500 font-extrabold shadow-2xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}"
                                    >
                                        <span>💰</span>
                                        <span>Kas Bendahara</span>
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="setAssignInventarisPreset('Kotak Obat / P3K')"
                                        class="p-2 rounded-xl text-left border text-xs transition-all flex items-center gap-1.5 {{ $assignCustomHolderName === 'Kotak Obat / P3K' ? 'bg-amber-500 text-white border-amber-500 font-extrabold shadow-2xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}"
                                    >
                                        <span>💊</span>
                                        <span>Kotak P3K</span>
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="setAssignInventarisPreset('Arsip & Kitab Kamar')"
                                        class="p-2 rounded-xl text-left border text-xs transition-all flex items-center gap-1.5 {{ $assignCustomHolderName === 'Arsip & Kitab Kamar' ? 'bg-amber-500 text-white border-amber-500 font-extrabold shadow-2xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}"
                                    >
                                        <span>📁</span>
                                        <span>Arsip Kamar</span>
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="setAssignInventarisPreset('Alat & Inventaris Bersama')"
                                        class="p-2 rounded-xl text-left border text-xs transition-all flex items-center gap-1.5 {{ $assignCustomHolderName === 'Alat & Inventaris Bersama' ? 'bg-amber-500 text-white border-amber-500 font-extrabold shadow-2xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}"
                                    >
                                        <span>🧹</span>
                                        <span>Inventaris Alat</span>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama / Keterangan Khusus:</label>
                                <input
                                    type="text"
                                    wire:model="assignCustomHolderName"
                                    placeholder="Contoh: Kas / Uang Bendahara Kamar"
                                    class="w-full text-xs px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white"
                                />
                            </div>

                            <p class="text-[10px] text-amber-700 dark:text-amber-300 leading-relaxed">
                                💡 <em>Lemari ini akan ditandai khusus dan dihitung <strong>terpakai</strong> (tidak kosong), sehingga sistem tidak akan menganggap lemari ini sebagai sisa jatah untuk santri baru.</em>
                            </p>
                        </div>

                    <!-- MODE 3: PENGURUS / NON-SISTEM -->
                    @elseif($assignAllocationType === 'external')
                        <div class="space-y-3 p-3 bg-indigo-50/50 dark:bg-indigo-950/20 rounded-2xl border border-indigo-200 dark:border-indigo-900/60">
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Pemakai (Pengurus / Non-Sistem):</label>
                                <input
                                    type="text"
                                    wire:model="assignCustomHolderName"
                                    placeholder="Contoh: Ust. Ahmad Fauzi (Pembina Asrama)"
                                    class="w-full text-xs px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white"
                                />
                            </div>

                            <p class="text-[10px] text-indigo-700 dark:text-indigo-300 leading-relaxed">
                                💡 <em>Gunakan opsi ini jika lemari digunakan oleh ustadz, pembina, pengurus komplek, atau tamu pondok yang tidak terdaftar di database santri mukim kamar ini.</em>
                            </p>
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" wire:click="$set('showAssignModal', false)" class="px-4 py-2 bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold rounded-xl text-xs">
                        Batal
                    </button>
                    <button type="button" wire:click="saveAssignModal" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-extrabold rounded-xl text-xs shadow-sm">
                        Simpan Alokasi Loker
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 3: VISUAL BLOCK BUILDER (DESAIN SENDIRI BENTUK LEMARI) -->
    @if($showBuilderModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                            <span>🛠️</span>
                            <span>{{ $builderEditingUnitId ? 'Edit Bentuk Lemari' : 'Desain Bentuk Lemari / Perabot Baru' }}</span>
                        </h3>
                        <p class="text-xs text-slate-400">Atur jumlah pintu per tingkat atau gunakan template cepat</p>
                    </div>
                    <button type="button" wire:click="$set('showBuilderModal', false)" class="text-slate-400 hover:text-slate-600 p-1 rounded-xl">
                        ✕
                    </button>
                </div>

                <!-- Template Cepat / Presets -->
                <div class="space-y-2">
                    <label class="block font-bold text-xs text-slate-600 dark:text-slate-300">Pilih Template Bentuk Lemari Cepat:</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <button
                            type="button"
                            wire:click="applyBuilderPreset('2_atas_4_bawah')"
                            class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-left text-xs hover:border-emerald-500 transition-all font-bold"
                        >
                            <span>🚪 2 Atas + 4 Bawah</span>
                            <span class="block text-[10px] text-slate-400 font-normal">Kombinasi 6 pintu</span>
                        </button>

                        <button
                            type="button"
                            wire:click="applyBuilderPreset('3x3')"
                            class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-left text-xs hover:border-emerald-500 transition-all font-bold"
                        >
                            <span>🚪 3x3 Simetris</span>
                            <span class="block text-[10px] text-slate-400 font-normal">9 Pintu kotak seragam</span>
                        </button>

                        <button
                            type="button"
                            wire:click="applyBuilderPreset('2x4_standar')"
                            class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-left text-xs hover:border-emerald-500 transition-all font-bold"
                        >
                            <span>🚪 2x4 Standar</span>
                            <span class="block text-[10px] text-slate-400 font-normal">8 Pintu (4 atas, 4 bawah)</span>
                        </button>

                        <button
                            type="button"
                            wire:click="applyBuilderPreset('5x5_kitab')"
                            class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-left text-xs hover:border-emerald-500 transition-all font-bold"
                        >
                            <span>📚 Rak Kitab 5x5</span>
                            <span class="block text-[10px] text-slate-400 font-normal">25 Sekat kubus cubicle</span>
                        </button>

                        <button
                            type="button"
                            wire:click="applyBuilderPreset('4x4_kitab')"
                            class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-left text-xs hover:border-emerald-500 transition-all font-bold"
                        >
                            <span>📚 Rak Kitab 4x4</span>
                            <span class="block text-[10px] text-slate-400 font-normal">16 Sekat kubus cubicle</span>
                        </button>

                        <button
                            type="button"
                            wire:click="applyBuilderPreset('fasilitas_umum')"
                            class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-left text-xs hover:border-emerald-500 transition-all font-bold"
                        >
                            <span>🔌 Fasilitas Umum</span>
                            <span class="block text-[10px] text-slate-400 font-normal">Kipas, lampu, kasur, dll</span>
                        </button>
                    </div>
                </div>

                <!-- Form Attributes -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs pt-2 border-t border-slate-100 dark:border-slate-800">
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Nama Perabot / Lemari</label>
                        <input type="text" wire:model="builderName" placeholder="Contoh: Lemari Pakaian Utama" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Jenis Perabot</label>
                        <select wire:model.live="builderType" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white">
                            <option value="baju">🚪 Lemari Pakaian Santri</option>
                            <option value="kitab">📚 Rak Kitab &amp; Buku</option>
                            <option value="umum">🔌 Fasilitas Ruangan Umum</option>
                        </select>
                    </div>
                </div>

                @if($builderCategory === 'compartment')
                    <!-- Compartment Row Builder -->
                    <div class="space-y-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between">
                            <label class="font-extrabold text-xs text-slate-800 dark:text-white">
                                Susunan Tingkat / Baris Pintu (Atas ke Bawah):
                            </label>
                            <button
                                type="button"
                                wire:click="addRowToBuilder(4, 1)"
                                class="px-3 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded-xl text-xs transition-colors"
                            >
                                + Tambah Baris
                            </button>
                        </div>

                        <div class="space-y-2">
                            @foreach($builderRows as $rIdx => $row)
                                <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3 text-xs">
                                    <span class="font-extrabold text-slate-600 dark:text-slate-300 w-20">
                                        Tingkat {{ $rIdx + 1 }}:
                                    </span>

                                    <div class="flex items-center gap-2 flex-1">
                                        <label class="font-medium text-slate-500">Jumlah Pintu:</label>
                                        <input
                                            type="number"
                                            min="1"
                                            max="12"
                                            wire:model.live="builderRows.{{ $rIdx }}.slots_count"
                                            class="w-16 px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-700 text-center font-bold"
                                        >
                                        <span class="text-slate-400">pintu berjejer</span>
                                    </div>

                                    @if(count($builderRows) > 1)
                                        <button
                                            type="button"
                                            wire:click="removeRowFromBuilder({{ $rIdx }})"
                                            class="p-1.5 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg"
                                            title="Hapus baris ini"
                                        >
                                            ✕
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Live Preview Lemari yang Sedang Didesain -->
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                            <span class="text-[11px] font-black uppercase tracking-wider text-slate-400 block mb-2">Pratinjau Bentuk Lemari:</span>
                            <div class="p-4 rounded-2xl bg-amber-950/10 dark:bg-slate-950 border-2 border-amber-900/20 dark:border-slate-800 space-y-2">
                                @foreach($builderRows as $pRow)
                                    @php $cnt = max(1, min(12, (int)($pRow['slots_count'] ?? 1))); @endphp
                                    <div class="grid gap-2" style="grid-template-columns: repeat({{ $cnt }}, minmax(0, 1fr));">
                                        @for($c = 1; $c <= $cnt; $c++)
                                            <div class="h-10 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 flex items-center justify-center font-bold text-[10px] text-slate-500 shadow-2xs">
                                                Pintu
                                            </div>
                                        @endfor
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Single Facility Details -->
                    <div class="space-y-3 pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
                        <div>
                            <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Kondisi Awal:</label>
                            <select wire:model="builderSingleCondition" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white">
                                <option value="baik">🟢 Baik</option>
                                <option value="rusak_ringan">🟡 Rusak Ringan</option>
                                <option value="rusak_berat">🔴 Rusak Berat</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Catatan Tambahan:</label>
                            <input type="text" wire:model="builderSingleNotes" placeholder="Contoh: Merk Cosmos, terpasang di dinding barat" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white">
                        </div>
                    </div>
                @endif

                {{-- DISTRIBUSI MULTI-KAMAR (Hanya tampil saat buat fasilitas baru / Skenario A) --}}
                @if(!$builderEditingUnitId)
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="font-extrabold text-xs text-slate-800 dark:text-white flex items-center gap-1.5">
                                    <span>📍 Terapkan Fasilitas Ini ke Kamar:</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ count($builderTargetRoomIds) > 1 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' }}">
                                        {{ count($builderTargetRoomIds) }} Kamar Terpilih
                                    </span>
                                </label>
                                <p class="text-[11px] text-slate-400">
                                    Fasilitas ini otomatis dibuatkan di kamar-kamar yang dicentang.
                                </p>
                            </div>

                            <button
                                type="button"
                                wire:click="$toggle('isBuilderRoomAccordionOpen')"
                                class="text-xs font-bold text-blue-600 dark:text-blue-400 flex items-center gap-1 hover:underline"
                            >
                                <span>{{ $isBuilderRoomAccordionOpen ? 'Tutup Pilihan ▲' : 'Pilih Kamar Lain ▾' }}</span>
                            </button>
                        </div>

                        {{-- Collapsible Room Selection List --}}
                        @if($isBuilderRoomAccordionOpen)
                            <div class="mt-2 space-y-3 max-h-52 overflow-y-auto border border-slate-200 dark:border-slate-800 rounded-2xl p-3 bg-slate-50 dark:bg-slate-900/60">
                                @foreach($dormitories as $d)
                                    <div class="space-y-1.5">
                                        <div class="flex items-center justify-between px-1 pt-0.5">
                                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                                {{ $d->name }} ({{ $d->rooms->count() }} Kamar)
                                            </span>
                                            <button
                                                type="button"
                                                wire:click="selectAllRoomsInDormForBuilder('{{ $d->id }}')"
                                                class="text-[10px] font-bold text-blue-600 dark:text-blue-400 hover:underline"
                                            >
                                                Pilih Semua di Gedung Ini
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5">
                                            @foreach($d->rooms as $tr)
                                                <label class="flex items-center gap-2 p-2 rounded-xl border transition-all cursor-pointer text-xs {{ in_array($tr->id, $builderTargetRoomIds) ? 'bg-blue-50 border-blue-300 dark:bg-blue-950/40 dark:border-blue-800 font-bold text-blue-900 dark:text-blue-200' : 'bg-white dark:bg-slate-800/80 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100' }}">
                                                    <input
                                                        type="checkbox"
                                                        wire:click="toggleBuilderTargetRoom('{{ $tr->id }}')"
                                                        {{ in_array($tr->id, $builderTargetRoomIds) ? 'checked' : '' }}
                                                        class="rounded text-blue-600 focus:ring-blue-500"
                                                    >
                                                    <div class="truncate min-w-0">
                                                        <div class="truncate">{{ $tr->name }}</div>
                                                        <div class="text-[9px] text-slate-400 font-normal">{{ $tr->capacity }} Bed</div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-[11px] text-slate-500 bg-slate-50 dark:bg-slate-800/60 p-2.5 rounded-xl border border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between">
                                <span class="truncate">
                                    📍 Target: 
                                    @if(count($builderTargetRoomIds) === 1 && $activeRoom && in_array($activeRoom->id, $builderTargetRoomIds))
                                        <strong>Hanya {{ $activeRoom->name }}</strong> (kamar aktif saat ini)
                                    @else
                                        <strong>{{ count($builderTargetRoomIds) }} Kamar</strong> akan dibuatkan fasilitas ini
                                    @endif
                                </span>
                                <button
                                    type="button"
                                    wire:click="$set('isBuilderRoomAccordionOpen', true)"
                                    class="text-blue-600 dark:text-blue-400 font-bold shrink-0 ml-2 hover:underline"
                                >
                                    + Tambah Kamar Lain
                                </button>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" wire:click="$set('showBuilderModal', false)" class="px-5 py-2.5 bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold rounded-xl text-xs">
                        Batal
                    </button>
                    <button type="button" wire:click="saveBuilderModal" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-xl text-xs shadow-lg transition-all flex items-center gap-1.5">
                        <span>💾</span>
                        <span>
                            @if(!$builderEditingUnitId && count($builderTargetRoomIds) > 1)
                                Simpan ke {{ count($builderTargetRoomIds) }} Kamar
                            @elseif($builderEditingUnitId)
                                Simpan Perubahan Lemari
                            @else
                                Simpan Desain Lemari
                            @endif
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 4: SALIN BENTUK LEMARI KE KAMAR LAIN (CLONE) -->
    @if($showCloneModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                            <span>📋</span>
                            <span>Salin Perabot dari {{ $activeRoom->name }}</span>
                        </h3>
                        <p class="text-xs text-slate-400">Pilih perabot mana yang mau disalin, metodenya, dan kamar tujuannya</p>
                    </div>
                    <button type="button" wire:click="$set('showCloneModal', false)" class="text-slate-400 hover:text-slate-600 p-1 rounded-xl">
                        ✕
                    </button>
                </div>

                <div class="space-y-4 text-xs">
                    <!-- SEKSI 1: PILIH PERABOT YANG MAU DISALIN -->
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-xs text-slate-800 dark:text-white">1. Pilih Perabot yang Ingin Disalin:</span>
                            <button
                                type="button"
                                wire:click="selectAllCloneUnits"
                                class="text-blue-600 dark:text-blue-400 font-bold hover:underline text-[11px]"
                            >
                                {{ count($cloneSelectedUnitIds) === count($furnitureUnits) ? 'Hapus Pilihan' : 'Pilih Semua' }}
                            </button>
                        </div>

                        <div class="space-y-1.5">
                            @foreach($furnitureUnits as $u)
                                <label class="flex items-center justify-between p-2.5 rounded-xl border {{ in_array($u['id'], $cloneSelectedUnitIds) ? 'bg-blue-50 dark:bg-blue-950/40 border-blue-400 text-blue-900 dark:text-blue-200' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300' }} cursor-pointer transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <input
                                            type="checkbox"
                                            wire:click="toggleCloneUnit('{{ $u['id'] }}')"
                                            {{ in_array($u['id'], $cloneSelectedUnitIds) ? 'checked' : '' }}
                                            class="rounded text-blue-600 focus:ring-blue-500"
                                        >
                                        <span class="text-base">{{ ($u['type'] ?? '') === 'kitab' ? '📚' : (($u['category'] ?? '') === 'single' ? '🔌' : '🚪') }}</span>
                                        <div>
                                            <span class="font-bold block">{{ $u['name'] }}</span>
                                            <span class="text-[10px] opacity-75">
                                                @if(($u['category'] ?? '') === 'compartment')
                                                    {{ count($u['rows'] ?? []) }} baris / tingkat ({{ ($u['type'] ?? '') === 'kitab' ? 'Rak Kitab' : 'Lemari Baju' }})
                                                @else
                                                    Fasilitas Ruangan
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg {{ in_array($u['id'], $cloneSelectedUnitIds) ? 'bg-blue-200 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-slate-100 text-slate-500 dark:bg-slate-800' }}">
                                        {{ in_array($u['id'], $cloneSelectedUnitIds) ? 'Akan Disalin' : 'Dilewati' }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- SEKSI 2: METODE PENYALINAN DI KAMAR TUJUAN -->
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-2">
                        <span class="font-extrabold text-xs text-slate-800 dark:text-white block">2. Metode Penyalinan di Kamar Tujuan:</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <label class="p-3 rounded-xl border cursor-pointer flex items-start gap-2.5 transition-all {{ $cloneMode === 'overwrite' ? 'bg-amber-50 dark:bg-amber-950/40 border-amber-400 text-amber-900 dark:text-amber-200' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300' }}">
                                <input type="radio" wire:model="cloneMode" value="overwrite" class="mt-0.5 text-amber-600 focus:ring-amber-500">
                                <div>
                                    <div class="font-bold">🔄 Timpa / Reset (Overwrite)</div>
                                    <div class="text-[10px] opacity-80 mt-0.5">Ganti seluruh perabot lama kamar tujuan dengan perabot yang dipilih.</div>
                                </div>
                            </label>

                            <label class="p-3 rounded-xl border cursor-pointer flex items-start gap-2.5 transition-all {{ $cloneMode === 'append' ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-400 text-emerald-900 dark:text-emerald-200' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300' }}">
                                <input type="radio" wire:model="cloneMode" value="append" class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <div class="font-bold">➕ Tambahkan Saja (Append)</div>
                                    <div class="text-[10px] opacity-80 mt-0.5">Pertahankan perabot yang sudah ada, lalu tambahkan perabot yang disalin.</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- SEKSI 3: PILIH KAMAR TUJUAN -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-xs text-slate-800 dark:text-white">3. Pilih Kamar Tujuan:</span>
                            <button
                                type="button"
                                wire:click="selectAllRoomsInDormitory"
                                class="text-blue-600 dark:text-blue-400 font-bold hover:underline text-[11px]"
                            >
                                Pilih Semua di {{ $activeRoom->dormitory->name }}
                            </button>
                        </div>

                        <div class="space-y-1.5 max-h-48 overflow-y-auto border border-slate-200 dark:border-slate-800 rounded-2xl p-2 bg-white dark:bg-slate-900">
                            @foreach($dormitories as $d)
                                <div class="text-[10px] font-black uppercase text-slate-400 px-2 pt-1">{{ $d->name }}</div>
                                @foreach($d->rooms as $tr)
                                    @if($tr->id !== $activeRoom->id)
                                        <label class="flex items-center justify-between p-2 rounded-xl border border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/60 cursor-pointer">
                                            <div class="flex items-center gap-2">
                                                <input
                                                    type="checkbox"
                                                    wire:click="toggleCloneTarget('{{ $tr->id }}')"
                                                    {{ in_array($tr->id, $cloneTargetRoomIds) ? 'checked' : '' }}
                                                    class="rounded text-blue-600 focus:ring-blue-500"
                                                >
                                                <span class="font-bold text-slate-800 dark:text-white">{{ $tr->name }}</span>
                                            </div>
                                            <span class="text-[10px] text-slate-400">{{ $tr->capacity }} bed</span>
                                        </label>
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" wire:click="$set('showCloneModal', false)" class="px-4 py-2 bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold rounded-xl text-xs">
                        Batal
                    </button>
                    <button type="button" wire:click="executeCloneLayout" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white font-extrabold rounded-xl text-xs shadow-md transition-all">
                        Salin ({{ count($cloneSelectedUnitIds) }} Perabot) ke ({{ count($cloneTargetRoomIds) }} Kamar)
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ===================================================================== -->
    <!-- MODAL KONFIRMASI HAPUS UNIT PERABOT (Estetik & Impact Analysis) -->
    <!-- ===================================================================== -->
    @if($showDeleteUnitModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 animate-fade-in">
            <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden p-6 space-y-5 animate-scale-up">
                <!-- Icon & Header -->
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-100 dark:bg-rose-950/80 text-rose-600 dark:text-rose-400 flex items-center justify-center text-2xl shrink-0 shadow-sm border border-rose-200/50 dark:border-rose-900/50">
                        🗑️
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-base font-black text-slate-900 dark:text-white">
                            Hapus Unit Fasilitas?
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Kamar <span class="font-bold text-slate-700 dark:text-slate-300">{{ $activeRoom->name }}</span> • {{ $activeRoom->dormitory->name }}
                        </p>
                    </div>
                    <button
                        type="button"
                        wire:click="cancelDeleteFurnitureUnit"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg p-1"
                    >
                        ✕
                    </button>
                </div>

                <!-- Info Perabot -->
                <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200/80 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="text-xl">{{ ($deleteUnitData['type'] ?? '') === 'kitab' ? '📚' : (($deleteUnitData['category'] ?? '') === 'single' ? '🛋️' : '🚪') }}</span>
                        <div>
                            <div class="font-extrabold text-xs text-slate-900 dark:text-white">
                                {{ $deleteUnitData['name'] ?? 'Perabot' }}
                            </div>
                            <div class="text-[10px] text-slate-400">
                                {{ ($deleteUnitData['category'] ?? '') === 'compartment' ? ($deleteUnitData['slot_count'] ?? 0) . ' Daun Pintu / Sekat' : 'Fasilitas Ruangan Umum' }}
                            </div>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ ($deleteUnitData['type'] ?? '') === 'kitab' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' }}">
                        {{ ($deleteUnitData['type'] ?? '') === 'kitab' ? 'Rak Kitab' : (($deleteUnitData['category'] ?? '') === 'single' ? 'Fasilitas Umum' : 'Lemari Pakaian') }}
                    </span>
                </div>

                <!-- Analisis Dampak Penghuni -->
                @php
                    $affectedOccupants = $deleteUnitData['occupants'] ?? [];
                    $hasOccupants = count($affectedOccupants) > 0;
                @endphp

                @if($hasOccupants)
                    <div class="p-4 rounded-2xl bg-amber-50/80 dark:bg-amber-950/40 border border-amber-300/80 dark:border-amber-900/60 space-y-3">
                        <div class="flex items-center gap-2 text-amber-900 dark:text-amber-200 font-extrabold text-xs">
                            <span>⚠️</span>
                            <span>Ada {{ count($affectedOccupants) }} Penghuni di Lemari Ini:</span>
                        </div>

                        <div class="space-y-1.5 max-h-36 overflow-y-auto pr-1 scrollbar-thin">
                            @foreach($affectedOccupants as $occ)
                                <div class="flex items-center justify-between p-2 rounded-xl bg-white/80 dark:bg-slate-900/80 border border-amber-200/60 dark:border-amber-900/40 text-xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="font-mono font-black text-[10px] px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                            {{ $occ['code'] }}
                                        </span>
                                        <div class="truncate">
                                            <div class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $occ['name'] }}</div>
                                            @if(!empty($occ['nis']))
                                                <div class="text-[9px] text-slate-400 font-mono">NIS: {{ $occ['nis'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded {{ $occ['alloc'] === 'inventaris' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : ($occ['alloc'] === 'external' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300') }}">
                                        {{ $occ['alloc_label'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <p class="text-[11px] text-amber-800 dark:text-amber-300/90 leading-relaxed">
                            💡 <strong>Catatan:</strong> Santri-santri di atas <u>tetap tinggal di kamar ini</u>, namun hak pemakaian lemarinya akan dicabut dan statusnya kembali menjadi <strong class="text-rose-600 dark:text-rose-400">"Belum Lemari"</strong> untuk diplotkan kembali.
                        </p>
                    </div>
                @else
                    <div class="p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 flex items-center gap-2.5 text-xs text-emerald-800 dark:text-emerald-300">
                        <span class="text-lg">✅</span>
                        <div class="font-semibold leading-snug">
                            Unit ini dalam keadaan kosong melompong. Aman untuk langsung dihapus tanpa ada santri yang terdampak.
                        </div>
                    </div>
                @endif

                <!-- Tombol Aksi -->
                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button
                        type="button"
                        wire:click="cancelDeleteFurnitureUnit"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition-colors"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        wire:click="executeDeleteFurnitureUnit"
                        class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 active:bg-rose-700 text-white font-extrabold text-xs shadow-md shadow-rose-500/20 transition-all flex items-center gap-1.5"
                    >
                        <span>🗑️</span>
                        <span>{{ $hasOccupants ? 'Ya, Hapus & Lepaskan Santri' : 'Ya, Hapus Unit' }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ===================================================================== -->
    <!-- MODAL KONFIRMASI LEPAS SANTRI PER-PINTU (Estetik & HP-Friendly) -->
    <!-- ===================================================================== -->
    @if($showUnassignModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 animate-fade-in">
            <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-sm w-full border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden p-6 space-y-4 animate-scale-up">
                <!-- Header -->
                <div class="flex items-start gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 flex items-center justify-center text-xl shrink-0 border border-amber-200 dark:border-amber-900">
                        👋
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-sm font-black text-slate-900 dark:text-white">
                            Lepas Penghuni Loker?
                        </h3>
                        <p class="text-xs text-slate-400">
                            Pintu <span class="font-mono font-bold text-blue-600 dark:text-blue-400">{{ $unassignSlotData['code'] ?? '' }}</span> • {{ $unassignSlotData['unit_name'] ?? 'Lemari' }}
                        </p>
                    </div>
                    <button
                        type="button"
                        wire:click="cancelUnassignSlot"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg p-1"
                    >
                        ✕
                    </button>
                </div>

                <!-- Kartu Penghuni Yang Dilepas -->
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200/80 dark:border-slate-700 flex items-center gap-3">
                    @php
                        $slotAlloc = $unassignSlotData['alloc'] ?? 'santri';
                    @endphp
                    @if($slotAlloc === 'inventaris')
                        <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 flex items-center justify-center text-lg shrink-0">
                            💰
                        </div>
                    @elseif($slotAlloc === 'external')
                        <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300 flex items-center justify-center text-base shrink-0">
                            👤
                        </div>
                    @else
                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300 font-black text-xs flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($unassignSlotData['occupant_name'] ?? 'S', 0, 2)) }}
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="font-extrabold text-xs text-slate-900 dark:text-white truncate">
                            {{ $unassignSlotData['occupant_name'] ?? 'Penghuni' }}
                        </div>
                        @if(!empty($unassignSlotData['nis']))
                            <div class="text-[10px] text-slate-400 font-mono">NIS: {{ $unassignSlotData['nis'] }}</div>
                        @elseif($slotAlloc === 'inventaris')
                            <div class="text-[10px] text-amber-600 font-bold">Khusus Kas / Inventaris</div>
                        @elseif($slotAlloc === 'external')
                            <div class="text-[10px] text-indigo-600 font-bold">Non-Sistem / Pembina</div>
                        @endif
                    </div>
                </div>

                <!-- Penjelasan -->
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    @if(($unassignSlotData['alloc'] ?? 'santri') === 'santri')
                        Santri ini <strong>tetap tinggal di kamar {{ $activeRoom->name }}</strong>, namun pintu lemari <strong class="font-mono text-slate-800 dark:text-white">{{ $unassignSlotData['code'] ?? '' }}</strong> akan dikosongkan dan siap digunakan oleh santri lain.
                    @else
                        Peruntukan pintu <strong class="font-mono text-slate-800 dark:text-white">{{ $unassignSlotData['code'] ?? '' }}</strong> akan dikosongkan dan kembali menjadi slot santri biasa.
                    @endif
                </p>

                <!-- Tombol Aksi -->
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button
                        type="button"
                        wire:click="cancelUnassignSlot"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        wire:click="executeUnassignSlot"
                        class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 active:bg-rose-700 text-white font-extrabold text-xs shadow-md transition-all flex items-center gap-1"
                    >
                        <span>👋</span>
                        <span>Ya, Lepaskan</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
