<div class="space-y-6">
    {{-- ============================================================ --}}
    {{-- Header Page                                                  --}}
    {{-- ============================================================ --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            @if($view !== 'list')
                <button type="button" wire:click="goBack"
                    class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
            @endif
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">
                    @if($view === 'fill')
                        Isi Laporan Sensus v2
                    @elseif($view === 'review')
                        Tinjau Laporan Sensus v2
                    @else
                        Sensus Santri Bulanan
                    @endif
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    @if($view === 'fill' && $fillDormitoryCensus)
                        {{ $fillDormitoryCensus->dormitory->name }} &mdash; {{ $fillDormitoryCensus->period->name }}
                    @elseif($view === 'review' && $reviewDormitoryCensus)
                        {{ $reviewDormitoryCensus->dormitory->name }} &mdash; {{ $reviewDormitoryCensus->period->name }}
                    @else
                        Rekap kondisi santri per komplek setiap bulan.
                    @endif
                </p>
            </div>
        </div>

        @if($view === 'list' && $isPusat)
            <button type="button" wire:click="openCreatePeriodModal"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-br from-violet-500 to-purple-600 hover:from-violet-400 hover:to-purple-500 text-white font-bold rounded-xl shadow-lg shadow-violet-500/20 transition-all text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Buka Periode Sensus Baru
            </button>
        @endif
    </div>

    {{-- Alerts --}}
    {{-- ============================================================ --}}
    {{-- VIEW: LIST (default)                                         --}}
    {{-- ============================================================ --}}
    @if($view === 'list')
        {{-- Period Selector --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm p-5">
            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Pilih Periode Sensus</label>
            @if($periods->isEmpty())
                <div class="text-center py-6 text-slate-400 dark:text-slate-500 text-sm">
                    Belum ada periode sensus. @if($isPusat) <button wire:click="openCreatePeriodModal" class="text-violet-500 font-semibold underline">Buat sekarang</button>. @endif
                </div>
            @else
                <div class="flex flex-wrap gap-2">
                    @foreach($periods as $period)
                        <button type="button" wire:click="$set('selectedPeriodId', '{{ $period->id }}')"
                            class="px-4 py-2 rounded-xl text-sm font-semibold border-2 transition-all {{ $selectedPeriodId === $period->id ? 'border-violet-500 bg-violet-50 dark:bg-violet-950/30 text-violet-700 dark:text-violet-300' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-600' }}">
                            {{ $period->name }}
                            <span class="ml-1.5 text-[10px] font-bold px-1.5 py-0.5 rounded-full uppercase
                                {{ $period->status === 'active' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-400' :
                                   ($period->status === 'closed' ? 'bg-slate-200 text-slate-500 dark:bg-slate-800 dark:text-slate-400' : 'bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-400') }}">
                                {{ $period->status_label }}
                            </span>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Period Detail & Controls --}}
        @if($selectedPeriod)
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm p-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">{{ $selectedPeriod->name }}</h2>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full
                                {{ $selectedPeriod->status === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' :
                                   ($selectedPeriod->status === 'closed' ? 'bg-slate-100 text-slate-500 dark:bg-slate-800' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400') }}">
                                {{ $selectedPeriod->status_label }}
                            </span>
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $selectedPeriod->month_name }} {{ $selectedPeriod->year }} &bull; Dibuat oleh {{ $selectedPeriod->creator?->name ?? 'Sistem' }}</p>
                    </div>
                    @if($isPusat)
                        <div class="flex gap-2">
                            @if($selectedPeriod->status === 'draft')
                                <button type="button" wire:click="confirmStartPeriod('{{ $selectedPeriod->id }}')"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl text-sm shadow-sm shadow-emerald-500/20 transition-all">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Aktifkan Periode
                                </button>
                            @elseif($selectedPeriod->status === 'active')
                                <button type="button" wire:click="confirmClosePeriod('{{ $selectedPeriod->id }}')"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-700 hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold rounded-xl text-sm transition-all">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                                    Tutup Periode
                                </button>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Progress Bar (hanya untuk pusat) --}}
                @if($isPusat && !$dormitoryCensuses->isEmpty())
                    @php
                        $progress = $selectedPeriod->submission_progress;
                    @endphp
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 mb-1.5">
                            <span>Progress Setoran</span>
                            <span class="font-bold">{{ $progress['submitted'] }}/{{ $progress['total'] }} komplek</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-violet-500 to-purple-600 rounded-full transition-all duration-700" style="width: {{ $progress['percent'] }}%"></div>
                        </div>
                        <div class="flex gap-4 mt-3 text-xs">
                            <span class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400"><span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>{{ $progress['approved'] }} Disetujui</span>
                            <span class="flex items-center gap-1 text-blue-600 dark:text-blue-400"><span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span>{{ $progress['submitted'] - $progress['approved'] }} Menunggu</span>
                            <span class="flex items-center gap-1 text-amber-600 dark:text-amber-400"><span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>{{ $progress['pending'] }} Belum</span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Dormitory Census Cards --}}
            @if($dormitoryCensuses->isEmpty())
                <div class="text-center py-12 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 text-slate-400 dark:text-slate-500 shadow-sm">
                    <p class="font-semibold">Tidak ada data sensus untuk periode ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($dormitoryCensuses as $dc)
                        @php
                            $stats = $dc->statistics;
                        @endphp
                        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                            {{-- Card Header --}}
                            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="min-w-0">
                                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">{{ $dc->dormitory->name }}</h3>
                                        <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ $dc->dormitory->gender === 'L' ? 'Putra' : 'Putri' }}</span>
                                    </div>
                                    <span class="text-[10px] font-bold px-2 py-1 rounded-lg flex-shrink-0
                                        {{ $dc->status === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' :
                                           ($dc->status === 'submitted' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400' :
                                           ($dc->status === 'rejected' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400')) }}">
                                        {{ $dc->status_label }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-4 space-y-3">
                                {{-- Statistics --}}
                                @if($stats['total'] > 0)
                                    <div class="grid grid-cols-5 gap-1 text-center">
                                        <div class="p-1.5 bg-emerald-50 dark:bg-emerald-950/20 rounded-lg">
                                            <div class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['present'] }}</div>
                                            <div class="text-[9px] text-slate-400 font-semibold">Hadir</div>
                                        </div>
                                        <div class="p-1.5 bg-amber-50 dark:bg-amber-950/20 rounded-lg">
                                            <div class="text-sm font-bold text-amber-600 dark:text-amber-400">{{ $stats['sick'] }}</div>
                                            <div class="text-[9px] text-slate-400 font-semibold">Sakit</div>
                                        </div>
                                        <div class="p-1.5 bg-blue-50 dark:bg-blue-950/20 rounded-lg">
                                            <div class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ $stats['leave'] }}</div>
                                            <div class="text-[9px] text-slate-400 font-semibold">Izin</div>
                                        </div>
                                        <div class="p-1.5 bg-rose-50 dark:bg-rose-950/20 rounded-lg">
                                            <div class="text-sm font-bold text-rose-600 dark:text-rose-400">{{ $stats['absent'] }}</div>
                                            <div class="text-[9px] text-slate-400 font-semibold">Alpa</div>
                                        </div>
                                        <div class="p-1.5 bg-purple-50 dark:bg-purple-950/20 rounded-lg">
                                            <div class="text-sm font-bold text-purple-600 dark:text-purple-400">{{ $stats['moved'] }}</div>
                                            <div class="text-[9px] text-slate-400 font-semibold">Pindah</div>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-xs text-slate-400 dark:text-slate-500 italic text-center py-2">Belum ada data diisi.</p>
                                @endif

                                {{-- Notes jika ditolak --}}
                                @if($dc->status === 'rejected' && $dc->notes)
                                    <div class="p-2.5 bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/40 rounded-lg text-xs text-rose-700 dark:text-rose-400">
                                        <p class="font-semibold mb-1">Catatan Revisi:</p>
                                        <p>{{ $dc->notes }}</p>
                                    </div>
                                @endif

                                {{-- Action Buttons --}}
                                <div class="flex gap-2">
                                    @if($isMusyrif && in_array($dc->status, ['pending', 'rejected']))
                                        <button type="button" wire:click="openFillView('{{ $dc->id }}')"
                                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-violet-500 hover:bg-violet-600 text-white font-bold rounded-lg text-xs transition-all shadow-sm shadow-violet-500/20">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            {{ $dc->notes ? 'Revisi Laporan' : 'Isi Laporan' }}
                                        </button>
                                    @endif
                                    @if($isPusat && $dc->status === 'submitted')
                                        <button type="button" wire:click="openReviewView('{{ $dc->id }}')"
                                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-lg text-xs transition-all shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Tinjau
                                        </button>
                                    @endif
                                    @if($isPusat && $dc->status === 'approved')
                                        <div class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 font-bold rounded-lg text-xs border border-emerald-100 dark:border-emerald-900/40">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            Selesai
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    @endif

    {{-- ============================================================ --}}
    {{-- VIEW: FILL (Pengisian Sensus oleh Musyrif)                   --}}
    {{-- ============================================================ --}}
    @if($view === 'fill' && $fillDormitoryCensus)
        <div class="space-y-6">
            {{-- Tabs Selection & Bulk Confirm --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    {{-- Tabs Buttons --}}
                    <div class="flex bg-slate-100 dark:bg-slate-800 p-1.5 rounded-xl gap-1">
                        <button type="button" wire:click="$set('fillTab', 'form')"
                            class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $fillTab === 'form' ? 'bg-white dark:bg-slate-700 text-violet-600 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                            📝 Mode Cepat / Form
                        </button>
                        <button type="button" wire:click="$set('fillTab', 'excel')"
                            class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $fillTab === 'excel' ? 'bg-white dark:bg-slate-700 text-violet-600 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                            📊 Upload Excel
                        </button>
                    </div>

                    {{-- Bulk Actions --}}
                    @if($fillTab === 'form')
                        <div class="flex flex-wrap gap-2">
                            <button type="button" wire:click="confirmAllNormal"
                                class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-500 hover:bg-emerald-600 text-white shadow-sm shadow-emerald-500/20 transition-all">
                                ✅ Konfirmasi Semua Hadir
                            </button>
                            <label class="inline-flex items-center gap-2 cursor-pointer bg-slate-100 dark:bg-slate-800 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700">
                                <input type="checkbox" wire:model.live="onlyShowExceptions" class="rounded text-violet-600 focus:ring-violet-500 dark:bg-slate-900 border-slate-300 dark:border-slate-700">
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Pengecualian Saja</span>
                            </label>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tab 1: Form Input --}}
            @if($fillTab === 'form')
                <div class="space-y-6">
                    @foreach($fillRooms as $roomName => $roomInfo)
                        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                            {{-- Room Header --}}
                            <div class="px-5 py-4 bg-slate-50/50 dark:bg-slate-800/20 border-b border-slate-100 dark:border-slate-800 flex flex-wrap justify-between items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-extrabold text-slate-700 dark:text-slate-300 text-base">🚪 {{ $roomName }}</h3>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                                        {{ count($roomInfo['persons']) }} Santri
                                    </span>
                                </div>
                                <button type="button" wire:click="confirmRoomNormal('{{ collect($censusData)->where('room_name', $roomName)->first()['room_id'] }}')"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold border border-emerald-200 bg-emerald-50 dark:bg-emerald-950/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 transition-colors">
                                    ⚡ Kamar Ini Normal (Semua Hadir)
                                </button>
                            </div>

                            {{-- Room Members list --}}
                            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($roomInfo['persons'] as $personId)
                                    @php $data = $censusData[$personId]; @endphp
                                    <div class="p-5 space-y-4">
                                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-400 to-purple-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                                    {{ mb_substr($data['person_name'], 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $data['person_name'] }}</p>
                                                    <p class="text-[10px] text-slate-400 dark:text-slate-500">ID: {{ $personId }}</p>
                                                </div>
                                            </div>

                                            {{-- Status Dropdowns or Pills --}}
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach($statusOptions as $statusKey => $statusInfo)
                                                    <label class="cursor-pointer">
                                                        <input type="radio" wire:model.live="censusData.{{ $personId }}.status" value="{{ $statusKey }}" class="sr-only">
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold border-2 transition-all
                                                            {{ ($censusData[$personId]['status'] ?? 'present') === $statusKey
                                                                ? match($statusKey) {
                                                                    'present' => 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300',
                                                                    'sick'    => 'border-amber-500 bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300',
                                                                    'leave'   => 'border-blue-500 bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-300',
                                                                    'absent'  => 'border-rose-500 bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-300',
                                                                    'moved'   => 'border-purple-500 bg-purple-50 dark:bg-purple-950/30 text-purple-700 dark:text-purple-300',
                                                                    default   => 'border-slate-300 bg-slate-50 text-slate-600'
                                                                }
                                                                : 'border-slate-100 dark:border-slate-800 text-slate-400 dark:text-slate-500 hover:border-slate-200 dark:hover:border-slate-700 bg-slate-50/50 dark:bg-slate-800/40' }}">
                                                            <span>{{ $statusInfo['icon'] }}</span>
                                                            <span>{{ $statusInfo['label'] }}</span>
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Catatan --}}
                                        <div>
                                            <input type="text" wire:model.lazy="censusData.{{ $personId }}.notes" placeholder="Tulis catatan sensus jika berhalangan..."
                                                   class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all text-xs">
                                        </div>

                                        {{-- Collapsible Profile Section --}}
                                        <div class="border border-slate-100 dark:border-slate-800/60 rounded-xl overflow-hidden">
                                            <button type="button" wire:click="togglePersonProfile('{{ $personId }}')"
                                                class="w-full flex items-center justify-between px-4 py-2.5 bg-slate-50/30 dark:bg-slate-800/20 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                                <span class="flex items-center gap-1.5">
                                                    ✨ Perbarui Profil Lengkap (Pendidikan, Kesehatan, Wali, Sibling)
                                                </span>
                                                <svg class="w-4 h-4 transition-transform {{ $expandedPersonId === $personId ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>

                                            @if($expandedPersonId === $personId)
                                                <div class="p-4 bg-slate-50/20 dark:bg-slate-800/10 space-y-4 divide-y divide-slate-100 dark:divide-slate-800">
                                                    {{-- Pendidikan --}}
                                                    <div class="space-y-3 pt-1">
                                                        <h4 class="text-[11px] font-bold text-violet-500 uppercase tracking-widest">🎓 DATA PENDIDIKAN</h4>
                                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                            <div>
                                                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Status Sekolah</label>
                                                                <select wire:model.lazy="censusData.{{ $personId }}.profile_updates.school_status" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 text-xs focus:ring-1 focus:ring-violet-500">
                                                                    <option value="mondok_full">Mondok Full (Tidak Sekolah Luar)</option>
                                                                    <option value="sekolah_luar">Sekolah di Luar Pondok</option>
                                                                    <option value="kuliah">Kuliah / Mahasiswa</option>
                                                                    <option value="tidak_sekolah">Tidak Sekolah</option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nama Sekolah/Univ</label>
                                                                <input type="text" wire:model.lazy="censusData.{{ $personId }}.profile_updates.school_name" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 text-xs">
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Jenjang (SD/SMP/SMA/S1)</label>
                                                                <input type="text" wire:model.lazy="censusData.{{ $personId }}.profile_updates.school_type" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 text-xs">
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Jurusan</label>
                                                                <input type="text" wire:model.lazy="censusData.{{ $personId }}.profile_updates.major" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 text-xs">
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Kelas/Semester</label>
                                                                <input type="text" wire:model.lazy="censusData.{{ $personId }}.profile_updates.school_year" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 text-xs">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Kesehatan --}}
                                                    <div class="space-y-3 pt-3">
                                                        <h4 class="text-[11px] font-bold text-amber-500 uppercase tracking-widest">🏥 DATA KESEHATAN</h4>
                                                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                                            <div>
                                                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Gol. Darah</label>
                                                                <select wire:model.lazy="censusData.{{ $personId }}.profile_updates.blood_type" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 text-xs">
                                                                    <option value="">Pilih</option>
                                                                    <option value="A">A</option>
                                                                    <option value="B">B</option>
                                                                    <option value="AB">AB</option>
                                                                    <option value="O">O</option>
                                                                </select>
                                                            </div>
                                                            <div class="sm:col-span-3">
                                                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Riwayat Medis (Penyakit / Kondisi Khusus)</label>
                                                                <input type="text" wire:model.lazy="censusData.{{ $personId }}.profile_updates.medical_history" placeholder="cth: Sakit maag kronis, perlu minum obat rutin..." class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 text-xs">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Wali dan Saudara --}}
                                                    <div class="space-y-3 pt-3">
                                                        <h4 class="text-[11px] font-bold text-emerald-500 uppercase tracking-widest">🤝 ORANG TUA / WALI &amp; SAUDARA KANDUNG</h4>
                                                        
                                                        {{-- Orang Tua --}}
                                                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-3">
                                                            <div>
                                                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nama Ayah</label>
                                                                <input type="text" wire:model.lazy="censusData.{{ $personId }}.profile_updates.father_name" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 text-xs">
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">HP Ayah</label>
                                                                <input type="text" wire:model.lazy="censusData.{{ $personId }}.profile_updates.father_phone" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 text-xs">
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nama Ibu</label>
                                                                <input type="text" wire:model.lazy="censusData.{{ $personId }}.profile_updates.mother_name" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 text-xs">
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">HP Ibu</label>
                                                                <input type="text" wire:model.lazy="censusData.{{ $personId }}.profile_updates.mother_phone" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 text-xs">
                                                            </div>
                                                        </div>

                                                        {{-- Wali Lain --}}
                                                        <div class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl space-y-2">
                                                            <p class="text-[10px] font-bold text-slate-500 uppercase">Wali Lain (Jika bukan Ayah/Ibu Kandung)</p>
                                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                                <div>
                                                                    <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1">Nama Wali</label>
                                                                    <input type="text" wire:model.lazy="censusData.{{ $personId }}.guardian_updates.name" class="w-full px-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 text-xs">
                                                                </div>
                                                                <div>
                                                                    <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1">Hubungan</label>
                                                                    <select wire:model.lazy="censusData.{{ $personId }}.guardian_updates.relationship" class="w-full px-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 text-xs">
                                                                        <option value="wali_resmi">Wali Resmi</option>
                                                                        <option value="kakek">Kakek</option>
                                                                        <option value="nenek">Nenek</option>
                                                                        <option value="paman">Paman</option>
                                                                        <option value="bibi">Bibi</option>
                                                                        <option value="kakak_kandung">Kakak Kandung</option>
                                                                        <option value="lainnya">Lainnya</option>
                                                                    </select>
                                                                </div>
                                                                <div>
                                                                    <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1">HP Wali</label>
                                                                    <input type="text" wire:model.lazy="censusData.{{ $personId }}.guardian_updates.phone_primary" class="w-full px-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 text-xs">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Saudara di Pondok --}}
                                                        <div class="p-3 bg-violet-50/50 dark:bg-slate-800 rounded-xl space-y-2">
                                                            <p class="text-[10px] font-bold text-violet-500 dark:text-violet-400 uppercase">Hubungkan Saudara Kandung di Pondok</p>
                                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                                <div>
                                                                    <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1">Nama Saudara</label>
                                                                    <input type="text" wire:model.lazy="censusData.{{ $personId }}.profile_updates.sibling.name" placeholder="Nama Lengkap Saudara" class="w-full px-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 text-xs">
                                                                </div>
                                                                <div>
                                                                    <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1">Status Hubungan</label>
                                                                    <select wire:model.lazy="censusData.{{ $personId }}.profile_updates.sibling.relationship" class="w-full px-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 text-xs">
                                                                        <option value="kakak">Kakak</option>
                                                                        <option value="adik">Adik</option>
                                                                        <option value="kembar">Kembar</option>
                                                                    </select>
                                                                </div>
                                                                <div>
                                                                    <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1">NIK / NIS Saudara</label>
                                                                    <input type="text" wire:model.lazy="censusData.{{ $personId }}.profile_updates.sibling.nik_nis" placeholder="Opsional (Untuk pencocokan)" class="w-full px-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 text-xs">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Tab 2: Excel Upload --}}
            @if($fillTab === 'excel')
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Step 1: Download Template --}}
                        <div class="p-5 border border-slate-100 dark:border-slate-800 rounded-xl space-y-3">
                            <h4 class="font-bold text-sm text-slate-800 dark:text-slate-200">1. Unduh Template Sensus</h4>
                            <p class="text-xs text-slate-400 leading-relaxed">Unduh template sensus yang sudah di-pre-populate dengan data kamar dan santri asrama Anda untuk bulan ini.</p>
                            <button type="button" wire:click="downloadTemplate"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-lg text-xs transition-colors">
                                📥 Unduh Template (.xlsx)
                            </button>
                        </div>

                        {{-- Step 2: Upload Excel --}}
                        <div class="p-5 border border-slate-100 dark:border-slate-800 rounded-xl space-y-3">
                            <h4 class="font-bold text-sm text-slate-800 dark:text-slate-200">2. Unggah File Sensus</h4>
                            <p class="text-xs text-slate-400 leading-relaxed">Setelah selesai mengedit status, profil, dan wali di Excel offline, unggah file kembali ke sistem.</p>
                            
                            <div class="space-y-2">
                                <input type="file" wire:model="excelFile" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 dark:file:bg-slate-800 dark:file:text-slate-300">
                                @error('excelFile') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
                                
                                @if($excelFile)
                                    <button type="button" wire:click="uploadExcel"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-lg text-xs transition-colors">
                                        🚀 Proses &amp; Unggah Data
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Save / Submit Buttons --}}
            <div class="flex gap-3 justify-end">
                <button type="button" wire:click="saveDraftCensus"
                    class="inline-flex items-center gap-2 px-5 py-2.5 border-2 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 font-bold rounded-xl text-sm transition-all">
                    💾 Simpan Draf
                </button>
                <button type="button" wire:click="submitCensus"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-br from-violet-500 to-purple-600 hover:from-violet-400 hover:to-purple-500 text-white font-bold rounded-xl shadow-lg shadow-violet-500/20 transition-all text-sm">
                    📨 Kirim Ke Pusat
                </button>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- VIEW: REVIEW (Pengurus Pusat tinjau setoran)                --}}
    {{-- ============================================================ --}}
    @if($view === 'review' && $reviewDormitoryCensus)
        <div class="space-y-6">
            {{-- Header Info --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm p-5">
                <div class="flex flex-wrap gap-4 items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Disetor oleh</p>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $reviewDormitoryCensus->submitter?->name ?? '-' }}</p>
                        <p class="text-xs text-slate-400">{{ $reviewDormitoryCensus->submitted_at?->format('d M Y, H:i') ?? '-' }}</p>
                        @if($reviewDormitoryCensus->import_source === 'excel')
                            <span class="inline-block px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 text-[10px] font-bold">Via Excel Upload</span>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <button type="button" wire:click="openRejectModal('{{ $reviewDormitoryCensus->id }}')"
                            class="inline-flex items-center gap-2 px-4 py-2 border-2 border-rose-300 dark:border-rose-800 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/20 font-bold rounded-xl text-sm transition-all">
                            ❌ Kembalikan / Revisi
                        </button>
                        <button type="button" wire:click="confirmApproveCensus('{{ $reviewDormitoryCensus->id }}')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl text-sm shadow-sm shadow-emerald-500/20 transition-all">
                            ✅ Setujui Sensus
                        </button>
                    </div>
                </div>
            </div>

            {{-- Detail per santri --}}
            @foreach($reviewDormitoryCensus->details->groupBy('room_id') as $roomId => $roomDetails)
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300">🚪 {{ $roomDetails->first()->room?->name ?? 'Kamar' }}</h3>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($roomDetails as $detail)
                            <div class="px-5 py-4 space-y-3">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-400 to-purple-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                            {{ mb_substr($detail->person?->name ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $detail->person?->name ?? '-' }}</p>
                                            @if($detail->notes)
                                                <p class="text-xs text-slate-400 mt-0.5">💬 Catatan: {{ $detail->notes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg flex-shrink-0
                                        {{ $detail->status === 'present' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' :
                                           ($detail->status === 'sick'    ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400' :
                                           ($detail->status === 'leave'   ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400' :
                                           ($detail->status === 'absent'  ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-400' :
                                                                            'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400'))) }}">
                                        {{ $detail->status_label }}
                                    </span>
                                </div>

                                {{-- Usulan Perubahan --}}
                                @if($detail->has_profile_update || $detail->has_guardian_update)
                                    <div class="ml-11 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        {{-- Profile --}}
                                        @if($detail->has_profile_update && !empty($detail->profile_updates))
                                            <div class="p-3 bg-violet-50 dark:bg-slate-800 border border-violet-100 dark:border-slate-700 rounded-xl space-y-1.5">
                                                <p class="text-[10px] font-extrabold text-violet-600 dark:text-violet-400 tracking-wider uppercase">📝 Usulan Update Profil</p>
                                                @foreach($detail->profile_updates as $k => $v)
                                                    @if($k === 'sibling')
                                                        @if(!empty($v['name']))
                                                            <div class="text-[11px] bg-white dark:bg-slate-900 p-2 rounded border border-slate-200 dark:border-slate-700">
                                                                <span class="font-bold text-violet-500">Saudara Kandung:</span> {{ $v['name'] }} ({{ $v['relationship'] }})
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="flex justify-between text-[11px]">
                                                            <span class="text-slate-500 font-semibold capitalize">{{ str_replace('_', ' ', $k) }}:</span>
                                                            <span class="text-slate-800 dark:text-slate-200">{{ $v }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- Guardian --}}
                                        @if($detail->has_guardian_update && !empty($detail->guardian_updates))
                                            <div class="p-3 bg-emerald-50 dark:bg-slate-800 border border-emerald-100 dark:border-slate-700 rounded-xl space-y-1.5">
                                                <p class="text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400 tracking-wider uppercase">👨‍👦 Usulan Wali Baru</p>
                                                @foreach($detail->guardian_updates as $k => $v)
                                                    <div class="flex justify-between text-[11px]">
                                                        <span class="text-slate-500 font-semibold capitalize">{{ str_replace('_', ' ', $k) }}:</span>
                                                        <span class="text-slate-800 dark:text-slate-200">{{ $v }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- Modal: Buat Periode Baru                                     --}}
    {{-- ============================================================ --}}
    @if($showCreatePeriodModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showCreatePeriodModal', false)">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Buat Periode Sensus Baru</h3>
                    <button type="button" wire:click="$set('showCreatePeriodModal', false)" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama Periode</label>
                        <input type="text" wire:model="periodName" placeholder="cth: Sensus Santri Juli 2026"
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all text-sm">
                        @error('periodName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Bulan</label>
                            <select wire:model="periodMonth" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all text-sm">
                                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $m)
                                    <option value="{{ $i + 1 }}">{{ $m }}</option>
                                @endforeach
                            </select>
                            @error('periodMonth') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Tahun</label>
                            <input type="number" wire:model="periodYear" min="2020" max="2099"
                                   class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all text-sm">
                            @error('periodYear') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="p-3.5 bg-violet-50 dark:bg-violet-950/20 border border-violet-100 dark:border-violet-900/40 rounded-xl text-xs text-violet-700 dark:text-violet-400">
                        <p class="font-semibold mb-1">ℹ️ Yang akan terjadi:</p>
                        <ul class="list-disc list-inside space-y-0.5 text-violet-600 dark:text-violet-400/80">
                            <li>Sistem otomatis membuat lembar sensus untuk setiap asrama aktif.</li>
                            <li>Periode masih berstatus <strong>Draf</strong> hingga Anda mengaktifkannya.</li>
                            <li>Musyrif baru dapat mengisi laporan setelah periode diaktifkan.</li>
                        </ul>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" wire:click="$set('showCreatePeriodModal', false)" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-sm transition-colors">Batal</button>
                    <button type="button" wire:click="createPeriod" class="px-5 py-2 bg-gradient-to-br from-violet-500 to-purple-600 hover:from-violet-400 hover:to-purple-500 text-white font-bold rounded-xl text-sm shadow-lg shadow-violet-500/20 transition-all">Buat Periode</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- Modal: Konfirmasi Kustom                                     --}}
    {{-- ============================================================ --}}
    @if($showConfirmModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-sm border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden text-center p-8 space-y-5">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto
                    {{ $confirmVariant === 'success' ? 'bg-emerald-100 dark:bg-emerald-950/40' : ($confirmVariant === 'warning' ? 'bg-amber-100 dark:bg-amber-950/40' : 'bg-rose-100 dark:bg-rose-950/40') }}">
                    @if($confirmVariant === 'success')
                        <svg class="w-8 h-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($confirmVariant === 'warning')
                        <svg class="w-8 h-8 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @else
                        <svg class="w-8 h-8 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @endif
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100">{{ $confirmTitle }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">{!! $confirmMessage !!}</p>
                </div>
                <div class="flex gap-3">
                    <button type="button" wire:click="closeConfirmModal" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-sm transition-colors">Batal</button>
                    <button type="button" wire:click="executeConfirmAction"
                        class="flex-1 px-4 py-2.5 font-bold rounded-xl text-sm shadow-lg transition-all text-white
                            {{ $confirmVariant === 'success' ? 'bg-emerald-500 hover:bg-emerald-600 shadow-emerald-500/30' : ($confirmVariant === 'warning' ? 'bg-amber-500 hover:bg-amber-600 shadow-amber-500/30' : 'bg-rose-500 hover:bg-rose-600 shadow-rose-500/30') }}">
                        Ya, Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- Modal: Reject / Catatan Penolakan                            --}}
    {{-- ============================================================ --}}
    @if($showRejectModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-[60] flex items-center justify-center p-4" wire:click.self="$set('showRejectModal', false)">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Kembalikan Laporan Sensus</h3>
                    <button type="button" wire:click="$set('showRejectModal', false)" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Tuliskan alasan penolakan / catatan revisi yang harus diperbaiki oleh musyrif komplek:</p>
                    <textarea wire:model="rejectNotes" rows="4" placeholder="Contoh: Harap lengkapi data santri yang berstatus 'Pindah Kamar' dengan tujuan kamarnya..."
                           class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-sm resize-none"></textarea>
                    @error('rejectNotes') <span class="text-xs text-rose-500 block -mt-2">{{ $message }}</span> @enderror
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" wire:click="$set('showRejectModal', false)" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-sm transition-colors">Batal</button>
                    <button type="button" wire:click="rejectCensus" class="px-5 py-2 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-xl text-sm shadow-lg shadow-rose-500/30 transition-all">Kirim Catatan Revisi</button>
                </div>
            </div>
        </div>
    @endif
</div>
