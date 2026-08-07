<div class="space-y-0">
    <style>
        .write-lines {
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 2px 0;
        }
        .write-line {
            border-bottom: 1.5px dotted #cbd5e1;
            height: 11px;
        }
        .dark .write-line {
            border-bottom-color: #334155;
        }
    </style>
    {{-- ===== TOP NAV BAR ===== --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('keuangan.billing', ['tab' => 'rates']) }}"
               class="flex items-center justify-center w-8 h-8 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-2 text-[10px] text-slate-400 dark:text-slate-500 font-semibold mb-0.5">
                    <a href="{{ route('keuangan.billing', ['tab' => 'rates']) }}" class="hover:text-slate-600 dark:hover:text-slate-300 transition-colors">Konfigurasi Tarif</a>
                    <span>/</span>
                    <span class="text-slate-600 dark:text-slate-300">Cetak Checklist</span>
                </div>
                <h1 class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight">Print Setup & Pratinjau</h1>
            </div>
        </div>
        <span class="text-[10px] text-blue-600 font-bold bg-blue-500/10 px-3 py-1.5 rounded-full">
            🖨️ Checklist Generator
        </span>
    </div>

    {{-- ===== MAIN LAYOUT GRID ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- ===== LEFT: SETTINGS PANEL ===== --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-4">
                <div>
                    <h3 class="text-xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">Pengaturan Kertas</h3>
                    
                    {{-- Iuran Info --}}
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-950/40 border border-slate-200/50 dark:border-slate-800 rounded-xl space-y-1 mb-4">
                        <span class="block text-[8px] font-extrabold text-slate-400 uppercase">Iuran terpilih</span>
                        <span class="block text-xs font-bold text-slate-800 dark:text-slate-200 leading-snug">{{ $config->label }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[8px] font-extrabold uppercase mt-1
                            {{ $config->can_be_installment ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-blue-500/10 text-blue-600 dark:text-blue-400' }}">
                            {{ $config->can_be_installment ? '⚡ Cicilan / Event' : '📅 Bulanan Regular' }}
                        </span>
                    </div>
                </div>

                <div class="space-y-3">
                    @if($config->target_type === 'kelas')
                        {{-- Class Checkboxes --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-[10px] font-extrabold text-slate-450 dark:text-slate-500 uppercase tracking-wider">
                                    Pilih Kelas Madrasah <span class="text-rose-450">*</span>
                                </label>
                                <div class="flex items-center gap-1.5 text-[9px] font-extrabold">
                                    @if(!$this->genderScope())
                                        <button type="button" wire:click="selectAll('L')" class="text-cyan-600 dark:text-cyan-400 hover:underline">👦 All Putra</button>
                                        <span class="text-slate-300 dark:text-slate-700">|</span>
                                        <button type="button" wire:click="selectAll('P')" class="text-pink-600 dark:text-pink-400 hover:underline">👧 All Putri</button>
                                        <span class="text-slate-300 dark:text-slate-700">|</span>
                                    @endif
                                    <button type="button" wire:click="selectAll('all')" class="text-emerald-600 dark:text-emerald-400 hover:underline">⚡ All</button>
                                    <span class="text-slate-300 dark:text-slate-700">|</span>
                                    <button type="button" wire:click="clearAllSelection" class="text-rose-500 hover:underline">❌ Clear</button>
                                </div>
                            </div>
                            <div class="border border-slate-200 dark:border-slate-800 rounded-xl p-3 max-h-40 overflow-y-auto space-y-2 bg-slate-50 dark:bg-slate-950/40">
                                @foreach($kelasList as $k)
                                    <label class="flex items-center gap-2.5 text-xs font-semibold text-slate-750 dark:text-slate-300 cursor-pointer">
                                        <input type="checkbox" wire:model.live="selectedKelasIds" value="{{ $k->id }}"
                                            class="rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer">
                                        <span>🏫 {{ $k->name }} ({{ $k->academic_year }})</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Period Select based on interval --}}
                        @if(!$config->can_be_installment)
                            @if($config->interval === 'semester')
                                <div class="grid grid-cols-2 gap-3 pt-2">
                                    <div>
                                        <label class="block text-[10px] font-extrabold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1.5">
                                            Semester
                                        </label>
                                        <select wire:model.live="selectedSemester"
                                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                            <option value="1">Semester 1 (Ganjil)</option>
                                            <option value="2">Semester 2 (Genap)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-extrabold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1.5">
                                            Tahun Buku
                                        </label>
                                        <select wire:model.live="selectedYear"
                                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                            @for($y = now()->year - 1; $y <= now()->year + 2; $y++)
                                                <option value="{{ $y }}">{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            @elseif($config->interval === 'monthly')
                                <div class="grid grid-cols-2 gap-3 pt-2">
                                    <div>
                                        <label class="block text-[10px] font-extrabold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1.5">
                                            Bulan Terakhir
                                        </label>
                                        <select wire:model.live="selectedMonth"
                                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                            @for($m = 1; $m <= 12; $m++)
                                                <option value="{{ $m }}">{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-extrabold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1.5">
                                            Tahun
                                        </label>
                                        <select wire:model.live="selectedYear"
                                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                            @for($y = now()->year - 1; $y <= now()->year + 2; $y++)
                                                <option value="{{ $y }}">{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            @else
                                <div class="pt-2">
                                    <label class="block text-[10px] font-extrabold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1.5">
                                        Tahun Buku
                                    </label>
                                    <select wire:model.live="selectedYear"
                                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                        @for($y = now()->year - 1; $y <= now()->year + 2; $y++)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            @endif
                        @endif
                    @else
                        {{-- Dormitory Checkboxes --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-[10px] font-extrabold text-slate-450 dark:text-slate-500 uppercase tracking-wider">
                                    Pilih Komplek Asrama <span class="text-rose-450">*</span>
                                </label>
                                <div class="flex items-center gap-1.5 text-[9px] font-extrabold">
                                    @if(!$this->genderScope())
                                        <button type="button" wire:click="selectAll('L')" class="text-cyan-600 dark:text-cyan-400 hover:underline">👦 All Putra</button>
                                        <span class="text-slate-300 dark:text-slate-700">|</span>
                                        <button type="button" wire:click="selectAll('P')" class="text-pink-600 dark:text-pink-400 hover:underline">👧 All Putri</button>
                                        <span class="text-slate-300 dark:text-slate-700">|</span>
                                    @endif
                                    <button type="button" wire:click="selectAll('all')" class="text-emerald-600 dark:text-emerald-400 hover:underline">⚡ All</button>
                                    <span class="text-slate-300 dark:text-slate-700">|</span>
                                    <button type="button" wire:click="clearAllSelection" class="text-rose-500 hover:underline">❌ Clear</button>
                                </div>
                            </div>
                            <div class="border border-slate-200 dark:border-slate-800 rounded-xl p-3 max-h-40 overflow-y-auto space-y-2 bg-slate-50 dark:bg-slate-950/40">
                                @foreach($dormitories as $d)
                                    <label class="flex items-center gap-2.5 text-xs font-semibold text-slate-750 dark:text-slate-300 cursor-pointer">
                                        <input type="checkbox" wire:model.live="selectedDormitoryIds" value="{{ $d->id }}"
                                            class="rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer">
                                        <span>🏠 {{ $d->name }} ({{ $d->gender === 'L' ? 'Putra' : 'Putri' }})</span>
                                    </label>
                                @endforeach
                            </div>
                            <span class="text-[9px] text-slate-400 mt-1.5 block">Menampilkan komplek sesuai dengan otorisasi gender Anda.</span>
                        </div>

                        {{-- Period Select based on interval --}}
                        @if(!$config->can_be_installment)
                            @if($config->interval === 'semester')
                                <div class="grid grid-cols-2 gap-3 pt-2">
                                    <div>
                                        <label class="block text-[10px] font-extrabold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1.5">
                                            Semester
                                        </label>
                                        <select wire:model.live="selectedSemester"
                                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                            <option value="1">Semester 1 (Ganjil)</option>
                                            <option value="2">Semester 2 (Genap)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-extrabold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1.5">
                                            Tahun Buku
                                        </label>
                                        <select wire:model.live="selectedYear"
                                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                            @for($y = now()->year - 1; $y <= now()->year + 2; $y++)
                                                <option value="{{ $y }}">{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            @elseif($config->interval === 'monthly')
                                <div class="grid grid-cols-2 gap-3 pt-2">
                                    <div>
                                        <label class="block text-[10px] font-extrabold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1.5">
                                            Bulan Terakhir
                                        </label>
                                        <select wire:model.live="selectedMonth"
                                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                            @for($m = 1; $m <= 12; $m++)
                                                <option value="{{ $m }}">{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-extrabold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1.5">
                                            Tahun
                                        </label>
                                        <select wire:model.live="selectedYear"
                                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                            @for($y = now()->year - 1; $y <= now()->year + 2; $y++)
                                                <option value="{{ $y }}">{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <span class="text-[9px] text-slate-400 mt-1 block">Checklist akan memuat 12 kolom bulan penuh dalam tahun terpilih.</span>
                            @else
                                <div class="pt-2">
                                    <label class="block text-[10px] font-extrabold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1.5">
                                        Tahun Buku
                                    </label>
                                    <select wire:model.live="selectedYear"
                                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                        @for($y = now()->year - 1; $y <= now()->year + 2; $y++)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            @endif
                        @endif
                    @endif

                    {{-- Ukuran Kertas & Format Halaman --}}
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-3">
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1.5">
                                Ukuran Kertas
                            </label>
                            <select wire:model.live="paperSize"
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                <option value="a4">📄 A4 (297 x 210 mm)</option>
                                <option value="f4">📄 F4 / Folio (330 x 215 mm)</option>
                            </select>
                        </div>
                        
                        @if($config->target_type !== 'kelas')
                            <div class="flex items-center gap-2 pt-1">
                                <input type="checkbox" wire:model.live="pageBreakPerRoom" id="pageBreakPerRoom"
                                    class="rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer">
                                <label for="pageBreakPerRoom" class="text-[10px] font-bold text-slate-650 dark:text-slate-350 cursor-pointer select-none">
                                    Potong Halaman per Kamar (1 Kamar 1 Lembar)
                                </label>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2.5">
                    @if($config->target_type === 'kelas')
                        @if(!empty($selectedKelasIds))
                            <a href="{{ route('print.checklist-kelas', ['kelas_ids' => implode(',', $selectedKelasIds), 'bill_type' => $config->type, 'semester' => $selectedSemester, 'year' => $selectedYear, 'paper_size' => $paperSize]) }}"
                               target="_blank"
                               class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-blue-500/10">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Buka File Cetakan (Cetak)
                            </a>
                        @else
                            <button disabled
                                    class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-slate-200 dark:bg-slate-800 text-slate-400 dark:text-slate-600 rounded-xl text-xs font-bold cursor-not-allowed">
                                Pilih Kelas Terlebih Dahulu
                            </button>
                        @endif
                    @else
                        @if(!empty($selectedDormitoryIds))
                            <a href="{{ route('print.checklist-config', ['id' => $config->id, 'dormitory_ids' => implode(',', $selectedDormitoryIds), 'month' => $selectedMonth, 'year' => $selectedYear, 'paper_size' => $paperSize, 'page_break_room' => $pageBreakPerRoom ? 1 : 0]) }}"
                               target="_blank"
                               class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-blue-500/10">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Buka File Cetakan (Cetak)
                            </a>
                        @else
                            <button disabled
                                    class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-slate-200 dark:bg-slate-800 text-slate-400 dark:text-slate-600 rounded-xl text-xs font-bold cursor-not-allowed">
                                Pilih Komplek Terlebih Dahulu
                            </button>
                        @endif
                    @endif

                    <a href="{{ route('keuangan.billing', ['tab' => 'rates']) }}"
                       class="w-full flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-350 rounded-xl text-xs font-bold transition-all">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        {{-- ===== RIGHT: LIVE LAYOUT PREVIEW PANEL ===== --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">
                    <div>
                        <h3 class="text-xs font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-widest">Pratinjau Lembar Kertas Fisik</h3>
                        <p class="text-[10px] text-slate-450">Simulasi layout kertas yang akan dicetak (menampilkan 10 data santri pertama).</p>
                    </div>
                    <span class="px-2.5 py-1 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-lg text-[9px] font-bold">
                        📄 Pratinjau Kertas A4
                    </span>
                </div>

                @if(empty($preview) || empty($preview['gridData']) || $preview['gridData']->isEmpty())
                    <div class="py-16 text-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl bg-slate-50/50 dark:bg-slate-950/20">
                        <div class="text-3xl mb-2.5">🏠</div>
                        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">Belum Ada {{ $config->target_type === 'kelas' ? 'Kelas' : 'Komplek' }} Terpilih</h4>
                        <p class="text-[10px] text-slate-400 max-w-xs mx-auto mt-1">Pilih {{ $config->target_type === 'kelas' ? 'kelas madrasah' : 'komplek asrama' }} di sebelah kiri untuk memuat pratinjau lembar kertas checklist fisik.</p>
                    </div>
                @else
                    @if($pageBreakPerRoom && $config->target_type !== 'kelas')
                        {{-- MOCK SEPARATED PAPERS PREVIEW --}}
                        @php
                            $groupedPreview = collect($preview['gridData'])->groupBy('room_name');
                        @endphp
                        <div class="space-y-6">
                            @foreach($groupedPreview as $roomName => $rows)
                                <div class="border border-slate-300/80 dark:border-slate-800 rounded-xl p-5 bg-white dark:bg-slate-950/40 text-[10px] font-serif shadow-md relative">
                                    <span class="absolute top-4 right-4 text-[8px] font-extrabold text-blue-600 dark:text-blue-400 uppercase tracking-wider bg-blue-500/10 px-2 py-1 rounded-md">
                                        📄 Halaman Kamar: {{ strtoupper($roomName) }}
                                    </span>

                                    {{-- Mock Header --}}
                                    <div class="text-center border-b-2 border-double border-slate-800 pb-2 mb-3.5 leading-tight">
                                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white m-0">Pondok Pesantren Al-Fithroh</h2>
                                        <p class="text-[9px] text-slate-500 dark:text-slate-400 m-0 mt-0.5">Buku Pedoman Keuangan Santri — Lembar Checklist Tagihan Komplek</p>
                                    </div>

                                    {{-- Mock Meta Info --}}
                                    <table class="w-full text-left font-bold text-slate-800 dark:text-slate-300 leading-normal mb-3.5">
                                        <tr>
                                            <td style="width: 15%;">Komplek</td>
                                            <td style="width: 35%;">: {{ $rows->first()['dormitory_name'] ?? '' }} (KAMAR: {{ strtoupper($roomName) }})</td>
                                            <td style="width: 15%;">Tipe Tagihan</td>
                                            <td style="width: 35%;">: {{ strtoupper(str_replace('_', ' ', $config->type)) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Nama Iuran</td>
                                            <td>: {{ $config->label }}</td>
                                            <td>Periode/Bulan</td>
                                            <td>: 
                                                @if($config->can_be_installment)
                                                    🔒 Cicilan (Event)
                                                @else
                                                    12 Bulan / Th {{ $selectedYear }}
                                                @endif
                                            </td>
                                        </tr>
                                    </table>

                                    {{-- Mock Table Grid --}}
                                    <div class="overflow-x-auto">
                                        <table class="w-full border-collapse border border-slate-800 text-left">
                                             <thead>
                                                 <tr class="bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-300 font-extrabold uppercase text-[8px] leading-tight">
                                                     <th class="border border-slate-800 py-1.5 px-2 text-center" style="width: 5%;">No</th>
                                                     <th class="border border-slate-800 py-1.5 px-2" style="width: 30%;">Nama Lengkap Santri</th>
                                                     @foreach($preview['headers'] as $header)
                                                         <th class="border border-slate-800 py-1.5 px-2 text-center text-[7px]" style="width: 4.5%;">{{ $header }}</th>
                                                     @endforeach
                                                     <th class="border border-slate-800 py-1.5 px-2 text-center" style="width: 10%;">Ket</th>
                                                 </tr>
                                             </thead>
                                             <tbody class="divide-y divide-slate-800/20 leading-normal text-[8px]">
                                                 @foreach($rows as $i => $row)
                                                     <tr class="text-slate-700 dark:text-slate-300">
                                                         <td class="border border-slate-800 py-1.5 px-2 text-center">{{ $i + 1 }}</td>
                                                         <td class="border border-slate-800 py-1.5 px-2 font-bold">{{ $row['person']->name }}</td>
                                                         
                                                         @foreach($row['bills'] as $bill)
                                                             <td class="border border-slate-800 py-1.5 px-1 text-center text-[7px] font-bold">
                                                                 @if(!$bill)
                                                                     <span class="text-slate-300 dark:text-slate-700">—</span>
                                                                 @elseif($bill->status === 'paid')
                                                                     <span class="text-emerald-600 dark:text-emerald-400">L</span>
                                                                 @else
                                                                     <span class="text-slate-350 dark:text-slate-650">[ ]</span>
                                                                 @endif
                                                             </td>
                                                         @endforeach

                                                         <td class="border border-slate-800 py-1.5 px-2 text-center text-slate-400 italic">
                                                             —
                                                         </td>
                                                     </tr>
                                                 @endforeach
                                             </tbody>
                                        </table>
                                    </div>

                                    {{-- Mock Signature Block --}}
                                    <div class="grid grid-cols-3 text-center text-slate-800 dark:text-slate-300 mt-5 pt-2 border-t border-dashed border-slate-800/30">
                                        <div>
                                            <p class="m-0">Diserahkan Oleh,</p>
                                            <p class="font-extrabold m-0 mt-0.5">Bendahara Komplek</p>
                                            <p class="m-0 mt-8">( .................................... )</p>
                                        </div>
                                        <div>
                                            <p class="m-0">Diperiksa Oleh,</p>
                                            <p class="font-extrabold m-0 mt-0.5">Musyrif Komplek</p>
                                            <p class="m-0 mt-8">( .................................... )</p>
                                        </div>
                                        <div>
                                            <p class="m-0">Diterima Oleh,</p>
                                            <p class="font-extrabold m-0 mt-0.5">Bendahara Pusat</p>
                                            <p class="m-0 mt-8">( .................................... )</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- MOCK PHYSICAL PAPER PREVIEW (UNIFIED) --}}
                        <div class="border border-slate-300/80 dark:border-slate-800 rounded-xl p-5 bg-white dark:bg-slate-950/40 text-[10px] font-serif shadow-inner">
                            
                            {{-- Mock Header --}}
                            <div class="text-center border-b-2 border-double border-slate-800 pb-2 mb-3.5 leading-tight">
                                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white m-0">Pondok Pesantren Al-Fithroh</h2>
                                <p class="text-[9px] text-slate-500 dark:text-slate-400 m-0 mt-0.5">Buku Pedoman Keuangan Santri — Lembar Checklist Tagihan {{ $config->target_type === 'kelas' ? 'Madrasah (Kelas)' : 'Komplek' }}</p>
                            </div>

                            {{-- Mock Meta Info --}}
                            <table class="w-full text-left font-bold text-slate-800 dark:text-slate-300 leading-normal mb-3.5">
                                <tr>
                                    <td style="width: 15%;">{{ $config->target_type === 'kelas' ? 'Kelas' : 'Komplek' }}</td>
                                    <td style="width: 35%;">: 
                                        @if($config->target_type === 'kelas')
                                            {{ $kelasList->whereIn('id', $selectedKelasIds)->pluck('name')->implode(', ') }}
                                        @else
                                            {{ $dormitories->whereIn('id', $selectedDormitoryIds)->pluck('name')->implode(', ') }}
                                        @endif
                                    </td>
                                    <td style="width: 15%;">Tipe Tagihan</td>
                                    <td style="width: 35%;">: {{ strtoupper(str_replace('_', ' ', $config->type)) }}</td>
                                </tr>
                                <tr>
                                    <td>Nama Iuran</td>
                                    <td>: {{ $config->label }}</td>
                                    <td>Periode/Bulan</td>
                                    <td>: 
                                        @if($config->can_be_installment)
                                            🔒 Cicilan (Event)
                                        @elseif($config->target_type === 'kelas')
                                            Sem {{ $selectedSemester }} / Th {{ $selectedYear }}
                                        @else
                                            12 Bulan / Th {{ $selectedYear }}
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            {{-- Mock Table Grid --}}
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border border-slate-800 text-left">
                                     <thead>
                                         <tr class="bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-300 font-extrabold uppercase text-[8px] leading-tight">
                                             <th class="border border-slate-800 py-1.5 px-2 text-center" style="width: 5%;">No</th>
                                             <th class="border border-slate-800 py-1.5 px-2" style="width: 30%;">Nama Lengkap Santri</th>
                                             @if($preview['type'] === 'single')
                                                 <th class="border border-slate-800 py-1.5 px-2 text-center" style="width: 14%;">Tagihan</th>
                                                 <th class="border border-slate-800 py-1.5 px-2 text-center" style="width: 21%;">Bayar I (Nominal & Paraf)</th>
                                                 <th class="border border-slate-800 py-1.5 px-2 text-center" style="width: 21%;">Bayar II / Pelunasan</th>
                                             @else
                                                 @if($preview['type'] === 'semester')
                                                     <th class="border border-slate-800 py-1.5 px-2 text-center" style="width: 10%;">Tunggakan</th>
                                                 @endif
                                                 @foreach($preview['headers'] as $header)
                                                     <th class="border border-slate-800 py-1.5 px-2 text-center text-[7px]" style="width: 15%;">{{ $header }}</th>
                                                 @endforeach
                                             @endif
                                             <th class="border border-slate-800 py-1.5 px-2 text-center" style="width: 11%;">Ket</th>
                                         </tr>
                                     </thead>
                                     <tbody class="divide-y divide-slate-800/20 leading-normal text-[8px]">
                                         @php $currentRoom = null; @endphp
                                         @foreach($preview['gridData'] as $index => $row)
                                             @php 
                                                 $roomName = $row['room_name'] ?? 'Tanpa Kamar'; 
                                                 $dormName = $row['dormitory_name'] ?? '';
                                                 $roomKey = $dormName . ' - ' . $roomName;
                                             @endphp
                                             @if($currentRoom !== $roomKey)
                                                 <tr class="bg-slate-50 dark:bg-slate-900/60 font-extrabold text-slate-800 dark:text-slate-200">
                                                     <td colspan="{{ $preview['type'] === 'single' ? 6 : (3 + count($preview['headers']) + ($preview['type'] === 'semester' ? 1 : 0)) }}" class="border border-slate-800 py-1 px-2">
                                                         🏢 {{ strtoupper($dormName) }} — KAMAR: {{ strtoupper($roomName) }}
                                                     </td>
                                                 </tr>
                                                 @php $currentRoom = $roomKey; @endphp
                                             @endif
                                             <tr class="text-slate-700 dark:text-slate-300">
                                                 <td class="border border-slate-800 py-1.5 px-2 text-center">{{ $index + 1 }}</td>
                                                 <td class="border border-slate-800 py-1.5 px-2 font-bold">{{ $row['person']->name }}</td>
                                                 
                                                 @if($preview['type'] === 'single')
                                                     @php $bill = $row['bills'][0] ?? null; @endphp
                                                     <td class="border border-slate-800 py-1.5 px-2 text-center font-bold">
                                                         Rp {{ number_format($bill ? $bill->amount : ($row['expectedAmount'] ?? $config->amount), 0, ',', '.') }}
                                                     </td>
                                                     @if($bill && $bill->status === 'paid')
                                                         <td colspan="2" class="border border-slate-800 p-1 text-center text-emerald-600 dark:text-emerald-400 font-extrabold py-1">
                                                             — LUNAS DI SISTEM —
                                                         </td>
                                                     @else
                                                         <td class="border border-slate-800 p-1 text-center text-slate-300 dark:text-slate-700 text-[7.5px] align-bottom pb-1">
                                                             Rp .......................... (Paraf)
                                                         </td>
                                                         <td class="border border-slate-800 p-1 text-center text-slate-300 dark:text-slate-700 text-[7.5px] align-bottom pb-1">
                                                             Rp .......................... (Paraf)
                                                         </td>
                                                     @endif
                                                 @else
                                                     @if($preview['type'] === 'semester')
                                                         <td class="border border-slate-800 py-1.5 px-2 text-center text-rose-600 font-bold">
                                                             —
                                                         </td>
                                                     @endif
                                                     @foreach($row['bills'] as $bill)
                                                         <td class="border border-slate-800 p-1 text-[7px] font-bold">
                                                             @if(!$bill)
                                                                 <div class="text-center text-slate-300 dark:text-slate-700">—</div>
                                                             @elseif($bill->status === 'paid')
                                                                 <div class="text-center text-emerald-600 dark:text-emerald-400">LUNAS</div>
                                                             @else
                                                                 <div class="write-lines">
                                                                     <div class="write-line"></div>
                                                                     <div class="write-line"></div>
                                                                 </div>
                                                             @endif
                                                         </td>
                                                     @endforeach
                                                 @endif

                                                 <td class="border border-slate-800 py-1.5 px-2 text-center">
                                                     @if(!empty($row['exceptionNote']))
                                                         <span class="text-amber-600 dark:text-amber-400 font-bold text-[8px]">{{ $row['exceptionNote'] }}</span>
                                                     @else
                                                         <span class="text-slate-400 italic">—</span>
                                                     @endif
                                                 </td>
                                             </tr>
                                         @endforeach
                                     </tbody>
                                </table>
                            </div>

                            {{-- Mock Signature Block --}}
                            <div class="grid grid-cols-3 text-center text-slate-800 dark:text-slate-300 mt-5 pt-2 border-t border-dashed border-slate-800/30">
                                <div>
                                    <p class="m-0">Diserahkan Oleh,</p>
                                    <p class="font-extrabold m-0 mt-0.5">Bendahara Komplek</p>
                                    <p class="m-0 mt-8">( .................................... )</p>
                                </div>
                                <div>
                                    <p class="m-0">Diperiksa Oleh,</p>
                                    <p class="font-extrabold m-0 mt-0.5">Musyrif Komplek</p>
                                    <p class="m-0 mt-8">( .................................... )</p>
                                </div>
                                <div>
                                    <p class="m-0">Diterima Oleh,</p>
                                    <p class="font-extrabold m-0 mt-0.5">Bendahara Pusat</p>
                                    <p class="m-0 mt-8">( .................................... )</p>
                                </div>
                            </div>

                        </div>
                    @endif
                @endif

            </div>
        </div>

    </div>
</div>
