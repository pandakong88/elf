<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center gap-2 text-xs text-slate-400 mb-4">
        <a href="{{ route('sensus.campaigns') }}" class="hover:text-slate-600 dark:hover:text-slate-200">Sensus</a>
        <span>/</span>
        <span>{{ $campaign->name }}</span>
        <span>/</span>
        <span class="text-slate-800 dark:text-slate-200 font-semibold">{{ $dormitory->name }}</span>
    </div>

    <!-- Header Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-sm mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                    🏢 Sensus Fasilitas &amp; Lemari
                </span>
                <span class="text-xs font-bold px-2 py-0.5 rounded {{ $dormitory->gender === 'L' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-950 dark:text-pink-300' }}">
                    {{ $dormitory->gender === 'L' ? 'Putra' : 'Putri' }}
                </span>
            </div>
            <h1 class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $dormitory->name }}</h1>
            <p class="text-xs text-slate-400 mt-0.5">Inspeksi fisik fasilitas komplek, kamar hunian, dan inventaris lemari santri.</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2">
            <button type="button" wire:click="saveDraft" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-2xl text-xs transition-all shadow-sm">
                💾 Simpan Draf
            </button>
            <button type="button" onclick="confirm('Kirim laporan sensus fasilitas ini ke Sekretaris untuk direview?') || event.stopImmediatePropagation()" wire:click="submitForReview" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-2xl text-xs transition-all shadow-md shadow-emerald-600/10 hover:shadow-emerald-500/20">
                🚀 Kirim ke Sekretaris
            </button>
        </div>
    </div>

    <!-- Layout: Left Section Navigator + Right Inspection Area -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar Navigation -->
        <div class="lg:col-span-1 space-y-2">
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 border border-slate-100 dark:border-slate-800 shadow-sm space-y-1">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider px-3 py-1">Area Inspeksi</p>
                
                <!-- Komplek Tab -->
                <button type="button" wire:click="$set('activeSection', 'komplek')" class="w-full flex items-center justify-between px-3 py-2.5 rounded-2xl text-xs font-bold transition-all {{ $activeSection === 'komplek' ? 'bg-amber-500 text-white shadow-md shadow-amber-500/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    <span class="flex items-center gap-2">
                        <span>🏛️</span>
                        <span>Fasilitas Komplek</span>
                    </span>
                    <span class="text-[10px] opacity-80">{{ count($komplekItems) }} item</span>
                </button>

                <!-- Divider -->
                <div class="pt-2 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kamar &amp; Lemari</div>

                <!-- Rooms List -->
                @foreach ($rooms as $r)
                    <button type="button" wire:click="$set('activeSection', '{{ $r['id'] }}')" class="w-full flex items-center justify-between px-3 py-2 rounded-2xl text-xs font-bold transition-all {{ $activeSection === $r['id'] ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        <span class="flex items-center gap-2 truncate">
                            <span>🚪</span>
                            <span class="truncate">{{ $r['name'] }}</span>
                        </span>
                        <span class="text-[10px] {{ $activeSection === $r['id'] ? 'text-white' : 'text-slate-400' }}">
                            {{ count($r['lockers']) }} lemari
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Right Main Inspection Area -->
        <div class="lg:col-span-3 space-y-6">
            <!-- SECTION 1: KOMPLEK AREA -->
            @if ($activeSection === 'komplek')
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                        <h2 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span>🏛️</span>
                            <span>Inspeksi Fasilitas Komplek &amp; Area Publik</span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">Periksa kondisi sarana prasarana yang digunakan bersama di komplek {{ $dormitory->name }}.</p>
                    </div>

                    <div class="space-y-4">
                        @foreach ($komplekItems as $item)
                            @php
                                $key = 'kmp_' . md5($item);
                                $currCond = $entries[$key]['condition'] ?? 'baik';
                            @endphp
                            <div class="p-4 rounded-2xl border border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-800/40 space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div class="font-bold text-sm text-slate-800 dark:text-slate-200">
                                        {{ $item }}
                                    </div>

                                    <!-- Condition Selector Buttons -->
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <button type="button" wire:click="setCondition('{{ $key }}', 'baik')" class="px-3 py-1 rounded-xl text-xs font-bold transition-all border {{ $currCond === 'baik' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-emerald-500' }}">
                                            🟢 Baik
                                        </button>
                                        <button type="button" wire:click="setCondition('{{ $key }}', 'rusak_ringan')" class="px-3 py-1 rounded-xl text-xs font-bold transition-all border {{ $currCond === 'rusak_ringan' ? 'bg-amber-500 text-white border-amber-500 shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-amber-500' }}">
                                            🟡 Rusak Ringan
                                        </button>
                                        <button type="button" wire:click="setCondition('{{ $key }}', 'rusak_berat')" class="px-3 py-1 rounded-xl text-xs font-bold transition-all border {{ $currCond === 'rusak_berat' ? 'bg-rose-600 text-white border-rose-600 shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-rose-500' }}">
                                            🔴 Rusak Berat
                                        </button>
                                        <button type="button" wire:click="setCondition('{{ $key }}', 'hilang')" class="px-3 py-1 rounded-xl text-xs font-bold transition-all border {{ $currCond === 'hilang' ? 'bg-purple-600 text-white border-purple-600 shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-purple-500' }}">
                                            🟣 Hilang
                                        </button>
                                    </div>
                                </div>

                                <!-- Note input -->
                                <div>
                                    <input type="text" wire:model.lazy="entries.{{ $key }}.notes" placeholder="Catatan kondisi / bagian yang perlu diperbaiki (opsional)..." class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 px-3 py-1.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            <!-- SECTION 2: ROOM & LOCKERS AREA -->
            @else
                @php
                    $curRoom = collect($rooms)->firstWhere('id', $activeSection);
                @endphp

                @if ($curRoom)
                    <!-- Room Facilities Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-sm space-y-6">
                        <div class="border-b border-slate-100 dark:border-slate-800 pb-4 flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                    <span>🚪</span>
                                    <span>{{ $curRoom['name'] }} — Sarana Fisik Kamar</span>
                                </h2>
                                <p class="text-xs text-slate-400 mt-1">Kapasitas: {{ $curRoom['capacity'] }} santri.</p>
                            </div>
                        </div>

                        <!-- Room Checklist -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($curRoom['kamar_items'] as $item)
                                @php
                                    $key = 'kmr_' . $curRoom['id'] . '_' . md5($item);
                                    $currCond = $entries[$key]['condition'] ?? 'baik';
                                @endphp
                                <div class="p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-800/40 space-y-2">
                                    <div class="font-bold text-xs text-slate-800 dark:text-slate-200 flex items-center justify-between">
                                        <span>{{ $item }}</span>
                                    </div>

                                    <div class="flex items-center gap-1 flex-wrap">
                                        <button type="button" wire:click="setCondition('{{ $key }}', 'baik')" class="px-2 py-0.5 rounded-lg text-[10px] font-bold border {{ $currCond === 'baik' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}">
                                            Baik
                                        </button>
                                        <button type="button" wire:click="setCondition('{{ $key }}', 'rusak_ringan')" class="px-2 py-0.5 rounded-lg text-[10px] font-bold border {{ $currCond === 'rusak_ringan' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}">
                                            Rusak Ringan
                                        </button>
                                        <button type="button" wire:click="setCondition('{{ $key }}', 'rusak_berat')" class="px-2 py-0.5 rounded-lg text-[10px] font-bold border {{ $currCond === 'rusak_berat' ? 'bg-rose-600 text-white border-rose-600' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}">
                                            Rusak Berat
                                        </button>
                                    </div>

                                    <input type="text" wire:model.lazy="entries.{{ $key }}.notes" placeholder="Catatan..." class="w-full text-[11px] rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-2 py-1">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @php
                        $bajuLockers = $curRoom['lockers']->filter(fn($l) => ($l->type ?? 'baju') === 'baju');
                        $kitabLockers = $curRoom['lockers']->filter(fn($l) => ($l->type ?? 'baju') === 'kitab');
                    @endphp

                    <!-- Room Lockers & Racks Container -->
                    <div class="space-y-6">
                        <!-- SECTION 2A: LEMARI PAKAIAN SANTRI -->
                        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-sm space-y-6">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                        <span>🚪</span>
                                        <span>Lemari Pakaian Santri ({{ count($bajuLockers) }})</span>
                                    </h3>
                                    <p class="text-xs text-slate-400 mt-0.5">Inspeksi loker baju dan kepemilikan santri di {{ $curRoom['name'] }}.</p>
                                </div>

                                @if (count($bajuLockers) === 0)
                                    <button type="button" wire:click="autoGenerateLockers('{{ $curRoom['id'] }}', {{ $curRoom['capacity'] ?: 8 }}, 'baju')" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs transition-all shadow-sm">
                                        ⚡ Generate Lemari Baju (B-01..)
                                    </button>
                                @endif
                            </div>

                            @if (count($bajuLockers) === 0)
                                <div class="py-8 text-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl">
                                    <span class="text-3xl">🚪</span>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300 mt-2">Belum ada lemari pakaian terdaftar di kamar ini</p>
                                    <button type="button" wire:click="autoGenerateLockers('{{ $curRoom['id'] }}', {{ $curRoom['capacity'] ?: 8 }}, 'baju')" class="mt-3 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs transition-all shadow-sm">
                                        Generate {{ $curRoom['capacity'] ?: 8 }} Lemari Pakaian (B-01 s/d B-08)
                                    </button>
                                </div>
                            @else
                                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                                    @foreach ($bajuLockers as $locker)
                                        @php
                                            $key = 'lmr_' . $locker->id;
                                            $currCond = $entries[$key]['condition'] ?? 'baik';
                                        @endphp
                                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 shadow-xs space-y-3">
                                            <!-- Locker Header -->
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-1.5">
                                                    <span>🚪</span>
                                                    <span class="font-black text-xs text-slate-900 dark:text-white px-2 py-0.5 rounded-lg bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300">
                                                        {{ $locker->code }}
                                                    </span>
                                                </div>
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $locker->condition_badge_class }}">
                                                    {{ $locker->condition_label }}
                                                </span>
                                            </div>

                                            <!-- Owner Reassignment -->
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Santri Pemegang Loker</label>
                                                <select wire:model.lazy="entries.{{ $key }}.assigned_person_id" class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 py-1.5 px-2">
                                                    <option value="">-- Lemari Kosong --</option>
                                                    @foreach ($roomOccupants[$curRoom['id']] ?? [] as $occ)
                                                        <option value="{{ $occ['id'] }}">{{ $occ['name'] }} ({{ $occ['nis'] ?? '-' }})</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Condition Selector Buttons -->
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kondisi Fisik Loker</label>
                                                <div class="grid grid-cols-2 gap-1 text-[10px]">
                                                    <button type="button" wire:click="setCondition('{{ $key }}', 'baik')" class="py-1 px-1.5 rounded-lg font-bold border text-center {{ $currCond === 'baik' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}">
                                                        🟢 Baik
                                                    </button>
                                                    <button type="button" wire:click="setCondition('{{ $key }}', 'rusak_ringan')" class="py-1 px-1.5 rounded-lg font-bold border text-center {{ $currCond === 'rusak_ringan' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}">
                                                        🟡 Rusak Ringan
                                                    </button>
                                                    <button type="button" wire:click="setCondition('{{ $key }}', 'rusak_berat')" class="py-1 px-1.5 rounded-lg font-bold border text-center {{ $currCond === 'rusak_berat' ? 'bg-rose-600 text-white border-rose-600' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}">
                                                        🔴 Rusak Berat
                                                    </button>
                                                    <button type="button" wire:click="setCondition('{{ $key }}', 'hilang')" class="py-1 px-1.5 rounded-lg font-bold border text-center {{ $currCond === 'hilang' ? 'bg-purple-600 text-white border-purple-600' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}">
                                                        🟣 Hilang
                                                    </button>
                                                </div>
                                            </div>

                                            <input type="text" wire:model.lazy="entries.{{ $key }}.notes" placeholder="Catatan pintu/engsel rusak..." class="w-full text-[11px] rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-2 py-1 text-slate-700 dark:text-slate-200">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- SECTION 2B: RAK KITAB & BUKU SANTRI -->
                        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-sm space-y-6">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                        <span>📚</span>
                                        <span>Rak Kitab &amp; Buku Santri ({{ count($kitabLockers) }})</span>
                                    </h3>
                                    <p class="text-xs text-slate-400 mt-0.5">Inspeksi kondisi rak penyimpanan kitab kuning &amp; Al-Qur'an santri.</p>
                                </div>

                                @if (count($kitabLockers) === 0)
                                    <button type="button" wire:click="autoGenerateLockers('{{ $curRoom['id'] }}', {{ $curRoom['capacity'] ?: 8 }}, 'kitab')" class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs transition-all shadow-sm">
                                        ⚡ Generate Rak Kitab (K-01..)
                                    </button>
                                @endif
                            </div>

                            @if (count($kitabLockers) === 0)
                                <div class="py-8 text-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl">
                                    <span class="text-3xl">📚</span>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300 mt-2">Belum ada rak kitab terdaftar di kamar ini</p>
                                    <button type="button" wire:click="autoGenerateLockers('{{ $curRoom['id'] }}', {{ $curRoom['capacity'] ?: 8 }}, 'kitab')" class="mt-3 px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs transition-all shadow-sm">
                                        Generate {{ $curRoom['capacity'] ?: 8 }} Rak Kitab (K-01 s/d K-08)
                                    </button>
                                </div>
                            @else
                                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                                    @foreach ($kitabLockers as $locker)
                                        @php
                                            $key = 'lmr_' . $locker->id;
                                            $currCond = $entries[$key]['condition'] ?? 'baik';
                                        @endphp
                                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 shadow-xs space-y-3">
                                            <!-- Locker Header -->
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-1.5">
                                                    <span>📚</span>
                                                    <span class="font-black text-xs text-slate-900 dark:text-white px-2 py-0.5 rounded-lg bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300">
                                                        {{ $locker->code }}
                                                    </span>
                                                </div>
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $locker->condition_badge_class }}">
                                                    {{ $locker->condition_label }}
                                                </span>
                                            </div>

                                            <!-- Owner Reassignment -->
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Santri Pemilik Rak Kitab</label>
                                                <select wire:model.lazy="entries.{{ $key }}.assigned_person_id" class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 py-1.5 px-2">
                                                    <option value="">-- Rak Kosong --</option>
                                                    @foreach ($roomOccupants[$curRoom['id']] ?? [] as $occ)
                                                        <option value="{{ $occ['id'] }}">{{ $occ['name'] }} ({{ $occ['nis'] ?? '-' }})</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Condition Selector Buttons -->
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kondisi Fisik Rak</label>
                                                <div class="grid grid-cols-2 gap-1 text-[10px]">
                                                    <button type="button" wire:click="setCondition('{{ $key }}', 'baik')" class="py-1 px-1.5 rounded-lg font-bold border text-center {{ $currCond === 'baik' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}">
                                                        🟢 Baik
                                                    </button>
                                                    <button type="button" wire:click="setCondition('{{ $key }}', 'rusak_ringan')" class="py-1 px-1.5 rounded-lg font-bold border text-center {{ $currCond === 'rusak_ringan' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}">
                                                        🟡 Rusak Ringan
                                                    </button>
                                                    <button type="button" wire:click="setCondition('{{ $key }}', 'rusak_berat')" class="py-1 px-1.5 rounded-lg font-bold border text-center {{ $currCond === 'rusak_berat' ? 'bg-rose-600 text-white border-rose-600' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}">
                                                        🔴 Rusak Berat
                                                    </button>
                                                    <button type="button" wire:click="setCondition('{{ $key }}', 'hilang')" class="py-1 px-1.5 rounded-lg font-bold border text-center {{ $currCond === 'hilang' ? 'bg-purple-600 text-white border-purple-600' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}">
                                                        🟣 Hilang
                                                    </button>
                                                </div>
                                            </div>

                                            <input type="text" wire:model.lazy="entries.{{ $key }}.notes" placeholder="Catatan papan patah / kena rayap..." class="w-full text-[11px] rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-2 py-1 text-slate-700 dark:text-slate-200">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
