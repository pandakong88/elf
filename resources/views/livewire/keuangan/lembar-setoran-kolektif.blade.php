<div class="h-[calc(100vh-6rem)] flex flex-col lg:flex-row gap-6 overflow-hidden">
    <!-- Panel Kiri: Navigator Lembar Setoran -->
    <div class="w-full lg:w-80 bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-3xl shadow-xs flex flex-col overflow-hidden shrink-0">
        <!-- Header & Search -->
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 space-y-4 bg-slate-50/50 dark:bg-slate-950/20">
            <div>
                <h3 class="text-sm font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-wider block font-serif-display">Daftar Lembar</h3>
                <p class="text-[10px] text-slate-400">Pilih lembaran untuk mengisi pembayaran massal.</p>
            </div>
            
            <div class="relative">
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Cari komplek / kelas..."
                    class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl pl-8 pr-3 py-2 text-xs focus:ring-emerald-500"
                >
                <span class="absolute left-2.5 top-2.5 text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
            </div>

            <div class="space-y-1">
                <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider">Filter Tagihan</label>
                <div
                    wire:ignore
                    x-data="{
                        choicesInstance: null,
                        init() {
                            this.choicesInstance = new Choices(this.$refs.selectInput, {
                                searchEnabled: true,
                                searchPlaceholderValue: 'Cari tagihan...',
                                noResultsText: 'Tidak ditemukan tagihan',
                                itemSelectText: '',
                                shouldSort: false,
                                allowHTML: true,
                            });
 
                            this.choicesInstance.setChoiceByValue($wire.filterConfigId);
 
                            this.$refs.selectInput.addEventListener('change', (e) => {
                                $wire.set('filterConfigId', e.target.value);
                            });
 
                            this.$watch('$wire.filterConfigId', (val) => {
                                if (this.choicesInstance.getValue(true) !== val) {
                                    this.choicesInstance.setChoiceByValue(val);
                                }
                            });
                        }
                    }"
                >
                    <select x-ref="selectInput" class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-2.5 py-1.5 text-xs focus:ring-emerald-500">
                        <option value="all">✨ Semua Tagihan</option>
                        @foreach($activeConfigs as $c)
                            <option value="{{ $c->id }}">{{ $c->label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Tahun Buku</label>
                    <select wire:model.live="year" class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-2.5 py-1.5 text-xs focus:ring-emerald-500">
                        @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Metode</label>
                    <select wire:model="payMethod" class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-2.5 py-1.5 text-xs focus:ring-emerald-500">
                        <option value="CASH">💵 Tunai</option>
                        <option value="TRANSFER">🏦 Transfer</option>
                        <option value="EWALLET">📱 E-Wallet</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Scrollable List of Sheets -->
        <div class="flex-1 overflow-y-auto p-4 space-y-2.5 divide-y divide-slate-100 dark:divide-slate-800/40">
            @forelse($sheetsList as $index => $sheet)
                <button
                    type="button"
                    wire:key="sheet-item-{{ $sheet['type'] }}-{{ $sheet['target_id'] }}-{{ $sheet['config_id'] }}"
                    wire:click="selectSheet('{{ $sheet['type'] }}', '{{ $sheet['target_id'] }}', '{{ $sheet['bill_type'] }}', '{{ $sheet['interval'] }}', '{{ $sheet['label'] }}', '{{ $sheet['config_id'] }}')"
                    class="w-full text-left p-3.5 rounded-2xl transition-all flex items-start gap-3 focus:outline-none hover:bg-slate-50 dark:hover:bg-slate-800/60
                        {{ $activeTargetId === $sheet['target_id'] && $activeConfigId === $sheet['config_id']
                            ? 'bg-slate-100 dark:bg-slate-800 border-l-4 border-emerald-500 pl-2.5'
                            : 'border-l-4 border-transparent' }}"
                >
                    <div class="p-2 rounded-xl text-xs shrink-0
                        {{ $sheet['type'] === 'komplek' ? 'bg-sky-500/10 text-sky-600 dark:text-sky-400' : 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' }}">
                        @if($sheet['type'] === 'komplek')
                            🏠
                        @else
                            🏫
                        @endif
                    </div>
                    
                    <div class="space-y-1 overflow-hidden flex-1">
                        <h4 class="font-bold text-slate-800 dark:text-slate-200 text-[11px] truncate leading-tight">
                            {{ $sheet['target_name'] }}
                        </h4>
                        <div class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold truncate uppercase">
                            {{ $sheet['config_label'] }}
                        </div>
                        <div class="flex items-center gap-1.5 pt-0.5">
                            <span class="inline-block px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase tracking-wide
                                {{ $sheet['type'] === 'komplek' ? 'bg-sky-500/10 text-sky-600 dark:text-sky-400' : 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' }}">
                                {{ $sheet['type'] }}
                            </span>
                            <span class="inline-block px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase tracking-wide bg-slate-100 dark:bg-slate-800 text-slate-500">
                                {{ $sheet['interval'] }}
                            </span>
                        </div>
                    </div>
                </button>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs font-semibold">
                    Tidak ada lembaran ditemukan.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Panel Kanan: Area Pengisian Checklist -->
    <div class="flex-1 bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-3xl shadow-xs flex flex-col overflow-hidden relative">
        @if(session()->has('message'))
            <div class="m-5 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-2xl text-xs font-semibold">
                {{ session('message') }}
            </div>
        @endif
        @if(session()->has('error'))
            <div class="m-5 p-4 bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 rounded-2xl text-xs font-semibold">
                {{ session('error') }}
            </div>
        @endif

        @if(!$activeBillType)
            <!-- State Kosong: Belum Ada Lembar Dipilih -->
            <div class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-slate-50/20 dark:bg-slate-950/5">
                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-2xl mb-4 animate-bounce">
                    📑
                </div>
                <h3 class="text-sm font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-wider block font-serif-display">Silakan Pilih Lembar Setoran</h3>
                <p class="text-xs text-slate-400 max-w-sm mt-1">Pilih salah satu lembaran aktif di panel sebelah kiri untuk menampilkan daftar santri dan mulai mengisi pembayaran massal.</p>
            </div>
        @else
            <!-- State Terbuka: Render Grid Checklist -->
            <!-- Dynamic Color Banner Header -->
            <div class="px-6 py-4.5 text-white flex items-center justify-between shadow-sm shrink-0
                @if($activeBillType === 'syahriah_pondok') bg-emerald-600 dark:bg-emerald-800
                @elseif($activeType === 'komplek') bg-sky-600 dark:bg-sky-800
                @else bg-indigo-600 dark:bg-indigo-800 @endif"
            >
                <div class="flex items-center gap-6">
                    <div class="space-y-0.5">
                        <div class="text-[9px] font-extrabold uppercase tracking-widest text-white/70">
                            INPUT SETORAN KOLEKTIF ({{ $activeType }})
                        </div>
                        <h2 class="text-sm font-black uppercase tracking-wide font-serif-display">
                            {{ $activeLabel }}
                        </h2>
                    </div>

                    <!-- Year Kalender Switcher -->
                    <div class="flex items-center bg-white/10 dark:bg-black/20 rounded-xl p-1 border border-white/10 backdrop-blur-xs select-none">
                        <button type="button" wire:click="decrementYear" class="p-1 px-2.5 rounded-lg hover:bg-white/15 active:scale-95 text-white/90 hover:text-white transition-all text-[11px] font-black" title="Tahun Sebelumnya">
                            ◀
                        </button>
                        <span class="px-3 text-xs font-black tracking-wider uppercase text-white font-mono">
                            {{ $year }}
                        </span>
                        <button type="button" wire:click="incrementYear" class="p-1 px-2.5 rounded-lg hover:bg-white/15 active:scale-95 text-white/90 hover:text-white transition-all text-[11px] font-black" title="Tahun Selanjutnya">
                            ▶
                        </button>
                    </div>
                </div>
                <button type="button" wire:click="deselectSheet" class="p-1.5 rounded-full hover:bg-white/10 text-white/80 hover:text-white transition-all" title="Tutup Lembar">
                    ✕
                </button>
            </div>

            <!-- Scrollable Table Container -->
            <div class="flex-1 overflow-x-auto overflow-y-auto">
                <table class="w-full text-left border-collapse text-xs table-fixed">
                    <colgroup>
                        <col class="w-12">
                        <col class="w-48">
                        <col class="w-36">
                        @foreach($months as $periodKey => $periodLabel)
                            <col class="w-32">
                        @endforeach
                        <col class="w-32">
                    </colgroup>
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800">
                            <th class="py-3 px-4 text-slate-500 font-extrabold uppercase tracking-wider text-[9px] sticky top-0 left-0 bg-slate-50 dark:bg-slate-950 z-30 shadow-sm border-r border-slate-200/80 dark:border-slate-800/80">No</th>
                            <th class="py-3 px-4 text-slate-500 font-extrabold uppercase tracking-wider text-[9px] sticky top-0 left-12 bg-slate-50 dark:bg-slate-950 z-30 shadow-sm border-r border-slate-200/80 dark:border-slate-800/80">Nama Santri</th>
                            <th class="py-3 px-4 text-center text-rose-500 font-extrabold uppercase tracking-wider text-[9px] whitespace-nowrap sticky top-0 bg-slate-50 dark:bg-slate-950 z-20 shadow-sm border-r border-slate-200/80 dark:border-slate-800/80">
                                <span class="inline-block px-2 py-0.5 rounded-md bg-rose-500/10 text-rose-600 dark:text-rose-400 font-extrabold text-[9px]">Tunggakan Lama</span>
                            </th>
                            @foreach($months as $periodKey => $periodLabel)
                                <th class="py-3 px-3 text-center sticky top-0 bg-slate-50 dark:bg-slate-950 z-20 shadow-sm border-r border-slate-200/80 dark:border-slate-800/80">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md bg-slate-200/70 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 font-black text-[9px] uppercase tracking-wider">
                                        {{ $periodLabel }}
                                    </span>
                                </th>
                            @endforeach
                            <th class="py-3 px-4 text-center text-emerald-600 font-extrabold uppercase tracking-wider text-[9px] whitespace-nowrap sticky top-0 bg-slate-50 dark:bg-slate-950 z-20 shadow-sm">Lunas di Muka</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        @php
                            $currentRoom = null;
                        @endphp
                        @forelse($gridData as $i => $row)
                            @if($activeType === 'komplek' && isset($row['person']->room_name) && $row['person']->room_name !== $currentRoom)
                                @php
                                    $currentRoom = $row['person']->room_name;
                                @endphp
                                <tr wire:key="room-header-{{ $currentRoom }}" class="bg-slate-50/80 dark:bg-slate-950/80 border-y border-slate-200 dark:border-slate-800">
                                    <td colspan="{{ count($months) + 4 }}" class="py-2.5 px-4 font-black text-slate-700 dark:text-slate-350 uppercase text-[9px] tracking-widest bg-slate-100/60 dark:bg-slate-900/60 sticky left-0 z-5">
                                        <span class="sticky left-4 inline-block">
                                            🚪 Kamar: {{ $currentRoom ?: 'Tanpa Kamar' }}
                                        </span>
                                    </td>
                                </tr>
                            @endif
                            <tr wire:key="student-row-{{ $row['person']->id }}" class="hover:bg-slate-50/40 dark:hover:bg-slate-800/20 transition-colors">
                                <td class="py-2.5 px-4 text-slate-400 text-[10px] sticky left-0 bg-white dark:bg-slate-900 z-10 shadow-xs border-r border-slate-200/50 dark:border-slate-800/50">{{ $i + 1 }}</td>
                                <td class="py-2.5 px-4 font-semibold text-slate-800 dark:text-slate-200 sticky left-12 bg-white dark:bg-slate-900 z-10 shadow-xs truncate border-r border-slate-200/50 dark:border-slate-800/50" title="{{ $row['person']->name }}">
                                    {{ $row['person']->name }}
                                </td>
                                
                                {{-- Tunggakan Lama Column --}}
                                <td class="py-2.5 px-3 text-center border-r border-slate-200/50 dark:border-slate-800/50">
                                    @if($row['tunggakanLamaSum'] > 0)
                                        @php
                                            $oldVal = isset($oldArrearsPayments[$row['person']->id]) ? (float)$oldArrearsPayments[$row['person']->id] : 0.0;
                                            $hasOldInput = $oldVal > 0;
                                            $isOldFullyChecked = $hasOldInput && $oldVal >= $row['tunggakanLamaSum'];
                                            $isOldPartialInput = $hasOldInput && $oldVal < $row['tunggakanLamaSum'];
                                        @endphp
                                        <div class="inline-flex items-center rounded-xl overflow-hidden border shadow-2xs transition-all
                                            @if($isOldFullyChecked)
                                                border-rose-500 bg-rose-500/10 ring-1 ring-rose-500/30
                                            @elseif($isOldPartialInput)
                                                border-amber-500 bg-amber-500/10 ring-1 ring-amber-500/30
                                            @else
                                                border-rose-300/50 dark:border-rose-800/60 bg-rose-500/5
                                            @endif">
                                            <button type="button" wire:click="toggleOldArrearsFullPayment('{{ $row['person']->id }}', {{ $row['tunggakanLamaSum'] }})"
                                                class="h-7 w-7 shrink-0 text-xs font-black transition-all focus:outline-none flex items-center justify-center border-r
                                                    @if($isOldFullyChecked)
                                                        bg-rose-500 text-white border-rose-600
                                                    @elseif($isOldPartialInput)
                                                        bg-amber-500 text-white border-amber-600
                                                    @else
                                                        bg-rose-500/20 text-rose-600 dark:text-rose-400 border-rose-400/30 hover:bg-rose-500/30
                                                    @endif"
                                                title="{{ $isOldFullyChecked ? 'Lunas Tunggakan' : ($isOldPartialInput ? 'Cicilan Tunggakan (Rp ' . number_format($oldVal, 0, ',', '.') . ')' : 'Tandai Lunas Tunggakan') }}">
                                                ⚡
                                            </button>
                                            <input
                                                type="number"
                                                wire:model.live.debounce.200ms="oldArrearsPayments.{{ $row['person']->id }}"
                                                placeholder="Rp {{ number_format($row['tunggakanLamaSum'], 0, ',', '') }}"
                                                class="w-20 h-7 bg-transparent border-0 px-2 text-[10px] text-right font-extrabold focus:ring-0 focus:outline-none transition-all
                                                    @if($isOldFullyChecked) text-rose-700 dark:text-rose-300
                                                    @elseif($isOldPartialInput) text-amber-700 dark:text-amber-300
                                                    @else text-rose-800 dark:text-rose-200 @endif"
                                            >
                                        </div>
                                    @else
                                        <span class="text-[9px] text-slate-300 dark:text-slate-700">—</span>
                                    @endif
                                </td>

                                {{-- Active Month/Semester Grid Columns --}}
                                @foreach($row['bills'] as $periodKey => $data)
                                    <td class="py-2.5 px-3 text-center border-r border-slate-200/50 dark:border-slate-800/50">
                                        @if(!$data['bill'])
                                            <span class="text-[9px] text-slate-300 dark:text-slate-700">—</span>
                                        @elseif($data['bill']->status === 'paid')
                                            <span class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 rounded-lg text-[9px] font-extrabold uppercase tracking-wider">
                                                ✓ Lunas
                                            </span>
                                        @else
                                            @php
                                                $remaining = (float)$data['bill']->amount - (float)$data['bill']->amount_paid;
                                                $inputVal = isset($paymentAmounts[$data['bill']->id]) ? (float)$paymentAmounts[$data['bill']->id] : 0.0;
                                                $hasInput = $inputVal > 0;
                                                $isCellFullyChecked = $hasInput && $inputVal >= $remaining;
                                                $isCellPartialInput = $hasInput && $inputVal < $remaining;
                                                $isPartial = $data['bill']->status === 'partial';
                                            @endphp
                                            <div class="inline-flex items-center rounded-xl overflow-hidden border shadow-2xs transition-all
                                                @if($isCellFullyChecked)
                                                    border-emerald-500 bg-emerald-500/10 ring-1 ring-emerald-500/30
                                                @elseif($isCellPartialInput)
                                                    border-amber-500 bg-amber-500/10 ring-1 ring-amber-500/30
                                                @elseif($isPartial)
                                                    border-amber-400/50 bg-amber-500/5 dark:border-amber-700/60
                                                @else
                                                    border-slate-300 dark:border-slate-700 bg-slate-100 dark:bg-slate-900/90 hover:border-slate-400 dark:hover:border-slate-600
                                                @endif">
                                                <button type="button" wire:click="toggleBillFullPayment('{{ $data['bill']->id }}', {{ $remaining }})"
                                                    class="h-7 w-7 shrink-0 text-xs font-black transition-all focus:outline-none flex items-center justify-center border-r
                                                        @if($isCellFullyChecked)
                                                            bg-emerald-500 text-white border-emerald-600
                                                        @elseif($isCellPartialInput)
                                                            bg-amber-500 text-white border-amber-600
                                                        @else
                                                            @if($isPartial) bg-amber-500/20 text-amber-600 dark:text-amber-400 border-amber-400/30 hover:bg-amber-500/30 @else bg-slate-200 dark:bg-slate-800 text-slate-400 dark:text-slate-500 border-slate-300 dark:border-slate-700 hover:bg-emerald-500/20 hover:text-emerald-600 @endif
                                                        @endif"
                                                    title="{{ $isCellFullyChecked ? 'Lunas Penuh (Rp ' . number_format($inputVal, 0, ',', '.') . ')' : ($isCellPartialInput ? 'Cicilan/Sebagian (Rp ' . number_format($inputVal, 0, ',', '.') . ')' : 'Tandai Lunas Penuh') }}">
                                                    ✓
                                                </button>
                                                <div class="relative">
                                                    <input
                                                        type="number"
                                                        wire:model.live.debounce.200ms="paymentAmounts.{{ $data['bill']->id }}"
                                                        placeholder="Rp {{ number_format($remaining, 0, ',', '') }}"
                                                        class="w-20 h-7 bg-transparent border-0 px-2 text-[10px] text-right font-extrabold focus:ring-0 focus:outline-none transition-all
                                                            @if($isCellFullyChecked) text-emerald-700 dark:text-emerald-300
                                                            @elseif($isCellPartialInput) text-amber-700 dark:text-amber-300
                                                            @elseif($isPartial) text-amber-700 dark:text-amber-300
                                                            @else text-slate-800 dark:text-slate-200 @endif"
                                                    >
                                                    @if($isPartial && !$hasInput)
                                                        <span class="absolute -top-1 -right-1 flex h-2 w-2">
                                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                @endforeach

                                {{-- Lunas di Muka Column --}}
                                <td class="py-3 px-4 text-center">
                                    @if($row['lunasDiMukaLabel'])
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-[9px] font-extrabold uppercase tracking-wider">
                                            s.d. {{ $row['lunasDiMukaLabel'] }}
                                        </span>
                                    @else
                                        <span class="text-[9px] text-slate-300 dark:text-slate-700">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($months) + 4 }}" class="py-8 text-center text-slate-400 font-semibold text-xs">
                                    Tidak ada data santri ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer: Total Summary & Action Buttons -->
            <div class="border-t border-slate-200 dark:border-slate-800 p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4 shrink-0 bg-slate-50/50 dark:bg-slate-950/20">
                <div class="flex items-center gap-6 text-xs">
                    <div>
                        <span class="text-slate-400 text-[9px] font-extrabold uppercase tracking-wider block">Tagihan Diisi</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $countChecked }} tagihan</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[9px] font-extrabold uppercase tracking-wider block">Total Setoran</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400 text-base">Rp {{ number_format($totalChecked, 0, ',', '.') }}</span>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    @if($this->printUrl)
                        <a href="{{ $this->printUrl }}" target="_blank"
                            class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1.5 shadow-xs">
                            🖨️ Cetak / Preview
                        </a>
                    @endif
                    @if($countChecked > 0)
                        <button type="button" wire:click="resetInputAmounts" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl text-xs font-bold transition-all">
                            Hapus Pilihan
                        </button>
                    @endif
                    <button type="button" wire:click="confirmProsesSetoran" @if($countChecked === 0) disabled @endif
                        class="px-6 py-2.5 rounded-xl text-xs font-bold transition-all shadow-md
                            @if($countChecked > 0)
                                @if($activeBillType === 'syahriah_pondok') bg-emerald-600 hover:bg-emerald-700 text-white
                                @elseif($activeType === 'komplek') bg-sky-600 hover:bg-sky-700 text-white
                                @else bg-indigo-600 hover:bg-indigo-700 text-white @endif
                            @else
                                bg-slate-250 dark:bg-slate-800 text-slate-400 cursor-not-allowed
                            @endif"
                    >
                        Proses & Simpan Setoran ({{ $countChecked }})
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal Konfirmasi Setoran Kolektif -->
    @if($showConfirmModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xl w-full max-w-2xl overflow-hidden animate-zoom-in">
                <!-- Header -->
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-950/20">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-950 dark:text-white uppercase tracking-wider block font-serif-display">Preview & Konfirmasi Setoran</h3>
                        <p class="text-[11px] text-slate-400">Cocokkan rincian setoran berikut dengan kertas fisik Anda.</p>
                    </div>
                    <span class="text-[10px] font-extrabold uppercase bg-slate-100 dark:bg-slate-800 text-slate-500 px-2.5 py-1 rounded-lg">
                        {{ $countChecked }} Item
                    </span>
                </div>

                <!-- Content -->
                <div class="p-6 space-y-4 text-xs">
                    <!-- Details of Iuran -->
                    <div class="grid grid-cols-2 gap-4 text-[11px] bg-slate-50 dark:bg-slate-950/40 p-4.5 rounded-2xl border border-slate-200/50 dark:border-slate-800/80">
                        <div class="space-y-1">
                            <div><span class="text-slate-450">Target:</span> <strong class="font-extrabold text-slate-700 dark:text-slate-250">{{ $activeLabel }}</strong></div>
                            <div><span class="text-slate-450">Iuran:</span> <strong class="font-extrabold text-slate-700 dark:text-slate-250 uppercase">{{ str_replace('_', ' ', $activeBillType) }}</strong></div>
                        </div>
                        <div class="space-y-1">
                            <div><span class="text-slate-450">Tahun Buku:</span> <strong class="font-extrabold text-slate-700 dark:text-slate-250">{{ $year }}</strong></div>
                            <div><span class="text-slate-450">Metode:</span> <strong class="font-extrabold text-slate-700 dark:text-slate-250">
                                @if($payMethod === 'CASH') 💵 CASH (Tunai)
                                @elseif($payMethod === 'TRANSFER') 🏦 TRANSFER BANK
                                @else 📱 EWALLET / DIGITAL @endif
                            </strong></div>
                        </div>
                    </div>

                    <!-- Checked Students List Table -->
                    <div class="border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden max-h-56 overflow-y-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-250/60 dark:border-slate-800/80 text-[10px] text-slate-400 font-extrabold uppercase tracking-wider">
                                    <th class="py-2.5 px-3">No</th>
                                    <th class="py-2.5 px-3">Nama Santri</th>
                                    @if($activeType === 'komplek')
                                        <th class="py-2.5 px-3">Kamar</th>
                                    @endif
                                    <th class="py-2.5 px-3">Rincian Pembayaran</th>
                                    <th class="py-2.5 px-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 text-[11px]">
                                @foreach($this->previewData as $idx => $item)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/40">
                                        <td class="py-2.5 px-3 text-slate-400">{{ $idx + 1 }}</td>
                                        <td class="py-2.5 px-3 font-bold text-slate-800 dark:text-slate-200">{{ $item['person_name'] }}</td>
                                        @if($activeType === 'komplek')
                                            <td class="py-2.5 px-3 text-slate-500 font-mono">{{ $item['room_name'] ?: '—' }}</td>
                                        @endif
                                        <td class="py-2.5 px-3 text-slate-500">{{ $item['details'] }}</td>
                                        <td class="py-2.5 px-3 text-right font-bold text-slate-900 dark:text-white">Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-emerald-500/5 border border-emerald-500/10 p-4.5 rounded-2xl flex items-center justify-between">
                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-extrabold uppercase tracking-wider">Total Setoran Keseluruhan</span>
                        <strong class="text-emerald-600 dark:text-emerald-400 text-lg font-black font-mono">Rp {{ number_format($totalChecked, 0, ',', '.') }}</strong>
                    </div>

                    <!-- Persetujuan Aktif Checkbox -->
                    <label class="flex items-start gap-3 p-3.5 bg-rose-500/5 border border-rose-500/10 rounded-2xl cursor-pointer">
                        <input
                            type="checkbox"
                            wire:model.live="confirmCheck"
                            class="mt-0.5 rounded text-rose-600 focus:ring-rose-500 border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900"
                        >
                        <span class="text-[10px] leading-relaxed text-rose-700 dark:text-rose-300 font-bold select-none">
                            Saya menyatakan data preview di atas sudah sama dengan kertas setoran komplek / kelas.
                        </span>
                    </label>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                    <button type="button" wire:click="cancelConfirm"
                        class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition-all">
                        ⬅️ Kembali Edit
                    </button>
                    <button type="button" wire:click="prosesSetoran" @if(!$confirmCheck) disabled @endif
                        class="px-6 py-2.5 text-white rounded-xl text-xs font-bold transition-all shadow-md
                            @if($confirmCheck)
                                @if($activeBillType === 'syahriah_pondok') bg-emerald-600 hover:bg-emerald-700
                                @elseif($activeType === 'komplek') bg-sky-600 hover:bg-sky-700
                                @else bg-indigo-600 hover:bg-indigo-700 @endif
                            @else
                                bg-slate-300 dark:bg-slate-800 text-slate-400 dark:text-slate-500 cursor-not-allowed
                            @endif"
                    >
                        💾 Simpan Setoran
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
