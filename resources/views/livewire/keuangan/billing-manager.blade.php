<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white font-serif-display">Pusat Kendali Keuangan &amp; Tagihan</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Pusat kontrol tagihan bulanan syahriah, kas komplek, katering, dispensasi, dan penagihan kasir terpadu.</p>
        </div>
    </div>

    <!-- Alert Message -->
    @if (session()->has('message'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-2xl text-xs font-semibold">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 rounded-2xl text-xs font-semibold">
            {{ session('error') }}
        </div>
    @endif

    @if($historyDetailConfigId)
        {{-- ================================================================= --}}
        {{-- DEDICATED VIEW: DETAIL RIWAYAT PENERBITAN TAGIHAN                 --}}
        {{-- Isolated render - very fast, no heavy tabs rendered below          --}}
        {{-- ================================================================= --}}
        @php $hStats = $this->historyDetailStats; @endphp
        <div class="space-y-5">

            {{-- ── HEADER NAVIGASI ── --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 rounded-2xl shadow-sm">
                <div class="flex items-center gap-4">
                    <button type="button" wire:click="closeHistoryDetail"
                        class="flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl transition-all text-xs font-bold shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali
                    </button>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-base font-black text-slate-900 dark:text-white uppercase tracking-wide">{{ $hStats['config_label'] ?? 'Detail Penerbitan' }}</h2>
                            <span class="px-2.5 py-0.5 bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 text-[10px] font-extrabold rounded-lg uppercase">
                                {{ $hStats['period_label'] ?? '' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            Diterbitkan: {{ $hStats['created_at'] ? \Carbon\Carbon::parse($hStats['created_at'])->translatedFormat('d F Y · H:i') : '-' }} WIB
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                        <span class="font-bold text-slate-700 dark:text-slate-200">{{ number_format($hStats['total_count'] ?? 0) }}</span> Santri Terdaftar
                    </div>
                    @if(($hStats['unpaid_count'] ?? 0) > 0)
                        <button type="button"
                            wire:click="openBatchDeleteConfirmModal('{{ $historyDetailConfigId }}', {{ $historyDetailMonth }}, {{ $historyDetailYear }})"
                            class="px-3.5 py-2 bg-rose-500/10 hover:bg-rose-600 text-rose-600 hover:text-white rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Hapus Unpaid Periode Ini</span>
                        </button>
                    @endif
                </div>
            </div>

            {{-- ── 4 STAT CARDS ── --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="p-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total</span>
                    </div>
                    <span class="text-2xl font-black text-slate-900 dark:text-white block">{{ number_format($hStats['total_count'] ?? 0) }}</span>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block">Rp {{ number_format($hStats['total_amount'] ?? 0, 0, ',', '.') }}</span>
                </div>

                <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/40 rounded-2xl">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 bg-emerald-100 dark:bg-emerald-900/50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Lunas</span>
                    </div>
                    <span class="text-2xl font-black text-emerald-700 dark:text-emerald-300 block">{{ number_format($hStats['paid_count'] ?? 0) }}</span>
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-0.5 block">Rp {{ number_format($hStats['paid_amount'] ?? 0, 0, ',', '.') }}</span>
                </div>

                <div class="p-4 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/40 rounded-2xl">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 bg-amber-100 dark:bg-amber-900/50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-[10px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-wider">Cicilan</span>
                    </div>
                    <span class="text-2xl font-black text-amber-700 dark:text-amber-300 block">{{ number_format($hStats['partial_count'] ?? 0) }}</span>
                    <span class="text-[11px] text-amber-600 dark:text-amber-400 mt-0.5 block">Sisa Rp {{ number_format($hStats['partial_remaining'] ?? 0, 0, ',', '.') }}</span>
                </div>

                <div class="p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/40 rounded-2xl">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 bg-rose-100 dark:bg-rose-900/50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <span class="text-[10px] font-black text-rose-600 dark:text-rose-400 uppercase tracking-wider">Belum Bayar</span>
                    </div>
                    <span class="text-2xl font-black text-rose-700 dark:text-rose-300 block">{{ number_format($hStats['unpaid_count'] ?? 0) }}</span>
                    <span class="text-[11px] text-rose-600 dark:text-rose-400 mt-0.5 block">Rp {{ number_format($hStats['unpaid_amount'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- ── PROGRESS BAR PELUNASAN ── --}}
            <div class="p-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl">
                <div class="flex justify-between items-center text-xs font-bold mb-2">
                    <span class="text-slate-500 uppercase tracking-wider">Capaian Pelunasan Batch Ini</span>
                    <span class="text-emerald-600 dark:text-emerald-400">{{ $hStats['progress_percent'] ?? 0 }}% Lunas</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-2.5 rounded-full transition-all duration-700"
                        style="width: {{ $hStats['progress_percent'] ?? 0 }}%"></div>
                </div>
                <div class="flex justify-between text-[10px] text-slate-400 mt-1.5">
                    <span>0%</span>
                    <span>Lunas: {{ $hStats['paid_count'] ?? 0 }} / {{ $hStats['total_count'] ?? 0 }} santri</span>
                    <span>100%</span>
                </div>
            </div>

            {{-- ── TABEL SANTRI DENGAN SEARCH & FILTER ── --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">

                {{-- Search & Filter Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wide">Daftar Santri</h3>
                        <p class="text-[11px] text-slate-400">Filter berdasarkan nama, NIS, atau status pembayaran</p>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        {{-- Search Input --}}
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input
                                type="text"
                                wire:model.live.debounce.400ms="historyDetailSearch"
                                placeholder="Nama Santri atau NIS..."
                                class="pl-8 pr-4 py-2 w-52 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-400 placeholder-slate-400 transition-all"
                            >
                        </div>
                        {{-- Status Filter --}}
                        <select wire:model.live="historyDetailStatusFilter"
                            class="py-2 pl-3 pr-8 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="unpaid">Belum Bayar</option>
                            <option value="partial">Cicilan / Parsial</option>
                            <option value="paid">Lunas</option>
                        </select>
                        {{-- Clear button --}}
                        @if($historyDetailSearch || $historyDetailStatusFilter)
                            <button type="button" wire:click="$set('historyDetailSearch', ''); $set('historyDetailStatusFilter', '')"
                                class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 rounded-xl text-xs font-bold transition-all flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reset
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Tabel --}}
                @php $pageBills = $this->historyDetailBills; @endphp
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[10px]">
                                <th class="px-4 py-3 text-left font-extrabold">No</th>
                                <th class="px-4 py-3 text-left font-extrabold">Santri</th>
                                <th class="px-4 py-3 text-left font-extrabold">Nominal</th>
                                <th class="px-4 py-3 text-left font-extrabold">Dibayar</th>
                                <th class="px-4 py-3 text-left font-extrabold">Status</th>
                                <th class="px-4 py-3 text-center font-extrabold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($pageBills as $dBillIdx => $dBill)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-4 py-3 text-slate-400 font-mono text-[10px]">
                                        {{ $pageBills->firstItem() + $dBillIdx }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-900 dark:text-white">{{ $dBill->person?->name ?? '-' }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $dBill->person?->nis ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 font-bold text-slate-700 dark:text-slate-300">
                                        Rp {{ number_format($dBill->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($dBill->status === 'paid')
                                            <span class="font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($dBill->amount_paid, 0, ',', '.') }}</span>
                                        @elseif($dBill->status === 'partial')
                                            <span class="font-bold text-amber-600 dark:text-amber-400">Rp {{ number_format($dBill->amount_paid, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($dBill->status === 'paid')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 rounded-lg font-bold text-[10px] uppercase">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                Lunas
                                            </span>
                                        @elseif($dBill->status === 'partial')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 rounded-lg font-bold text-[10px] uppercase">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Cicilan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-300 rounded-lg font-bold text-[10px] uppercase">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                Unpaid
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($dBill->status === 'unpaid' && $dBill->amount_paid == 0)
                                            <button type="button"
                                                wire:click="openIndividualDeleteConfirmModal('{{ $dBill->id }}')"
                                                class="p-1.5 text-rose-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-lg transition-all"
                                                title="Hapus tagihan santri ini">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        @else
                                            <span class="text-[10px] text-slate-300 dark:text-slate-600 font-bold">Terkunci</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            </div>
                                            <p class="text-slate-400 font-semibold text-xs">Tidak ada santri yang cocok dengan filter.</p>
                                            @if($historyDetailSearch || $historyDetailStatusFilter)
                                                <button type="button" wire:click="$set('historyDetailSearch', ''); $set('historyDetailStatusFilter', '')"
                                                    class="text-xs text-indigo-500 hover:text-indigo-700 font-bold underline">Reset Filter</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($pageBills->hasPages())
                    <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-800">
                        {{ $pageBills->links() }}
                    </div>
                @endif

            </div>{{-- end table card --}}
        </div>{{-- end space-y-5 --}}
    @else
        <!-- Navigation Tabs -->
        <div class="flex border-b border-slate-200 dark:border-slate-800 gap-2 overflow-x-auto pb-1">
            <button wire:click="$set('activeTab', 'generate')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'generate' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>Penerbitan Tagihan</span>
            </button>
            <button wire:click="$set('activeTab', 'rates')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'rates' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                <span>Konfigurasi Tarif & Target</span>
            </button>
            <button wire:click="$set('activeTab', 'cashier')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'cashier' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Kasir Utama (12 Bulan)</span>
            </button>
            <button wire:click="$set('activeTab', 'gateway_transactions')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'gateway_transactions' ? 'border-amber-500 text-amber-600 dark:text-amber-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>Transaksi Gateway</span>
                @if(isset($gatewayPendingCount) && $gatewayPendingCount > 0)
                    <span class="inline-flex items-center justify-center w-4 h-4 text-[9px] font-extrabold bg-amber-500 text-white rounded-full">{{ $gatewayPendingCount }}</span>
                @endif
            </button>
            @if($this->canViewSettlementTab())
                <button wire:click="$set('activeTab', 'settlement')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'settlement' ? 'border-sky-500 text-sky-600 dark:text-sky-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    <span>Rekonsiliasi &amp; Settlement</span>
                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[9px] font-black bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/30 rounded-full">Fase 4</span>
                </button>
            @endif
            <button wire:click="$set('activeTab', 'payments_log')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'payments_log' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span>Riwayat Setoran (Log)</span>
            </button>
            <button wire:click="$set('activeTab', 'exceptions')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'exceptions' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Dispensasi & Potongan</span>
            </button>
            <button wire:click="$set('activeTab', 'registration_rates')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'registration_rates' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Tarif Santri Baru &amp; Kitab</span>
            </button>
            <button wire:click="$set('activeTab', 'installments')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'installments' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Cicilan Event</span>
            </button>
        </div>

        <!-- Tabs Contents -->
        <div>
            <!-- TAB 1: GENERATE TAGIHAN -->
            @if ($activeTab === 'generate')
                <div class="space-y-8">
                    <!-- Choices.js dependencies & ultra-sleek high-contrast styling -->
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
                    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
                    <style>
                        .choices { margin-bottom: 0 !important; }
                        .choices__inner {
                        background-color: #f8fafc !important;
                        border: 1.5px solid #cbd5e1 !important;
                        border-radius: 0.75rem !important;
                        padding: 0.35rem 0.85rem !important;
                        min-height: 42px !important;
                        font-size: 0.75rem !important;
                        font-weight: 700 !important;
                        color: #0f172a !important;
                    }
                    .dark .choices__inner {
                        background-color: #020617 !important;
                        border-color: #334155 !important;
                        color: #f8fafc !important;
                    }
                    .choices__list--single {
                        padding-left: 0 !important;
                        color: #0f172a !important;
                        font-weight: 700 !important;
                    }
                    .dark .choices__list--single {
                        color: #f8fafc !important;
                    }
                    .choices__placeholder {
                        color: #64748b !important;
                        opacity: 1 !important;
                        font-weight: 600 !important;
                    }
                    .dark .choices__placeholder {
                        color: #94a3b8 !important;
                        opacity: 1 !important;
                    }
                    .choices__list--dropdown, .choices__list[aria-expanded] {
                        border: 1.5px solid #cbd5e1 !important;
                        border-radius: 1rem !important;
                        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
                        background-color: #ffffff !important;
                        color: #0f172a !important;
                        padding: 0.35rem !important;
                        z-index: 50 !important;
                    }
                    .dark .choices__list--dropdown, .dark .choices__list[aria-expanded] {
                        border-color: #334155 !important;
                        background-color: #0f172a !important;
                        color: #f8fafc !important;
                    }
                    .choices__list--dropdown .choices__item--selectable, .choices__list[aria-expanded] .choices__item--selectable {
                        padding: 0.65rem 0.85rem !important;
                        border-radius: 0.6rem !important;
                        font-size: 0.75rem !important;
                        font-weight: 700 !important;
                        color: #0f172a !important;
                        transition: all 0.15s ease !important;
                    }
                    .dark .choices__list--dropdown .choices__item--selectable, .dark .choices__list[aria-expanded] .choices__item--selectable {
                        color: #f8fafc !important;
                    }
                    .choices__list--dropdown .choices__item--selectable.is-highlighted {
                        background-color: rgba(16, 185, 129, 0.15) !important;
                        color: #047857 !important;
                    }
                    .dark .choices__list--dropdown .choices__item--selectable.is-highlighted {
                        background-color: rgba(16, 185, 129, 0.25) !important;
                        color: #34d399 !important;
                    }
                    .choices__input {
                        background-color: #f1f5f9 !important;
                        font-size: 0.75rem !important;
                        padding: 0.4rem 0.75rem !important;
                        border-radius: 0.5rem !important;
                        color: #0f172a !important;
                        border: 1px solid #cbd5e1 !important;
                        margin-bottom: 0.35rem !important;
                        font-weight: 600 !important;
                    }
                    .dark .choices__input {
                        background-color: #020617 !important;
                        color: #f8fafc !important;
                        border-color: #334155 !important;
                    }
                </style>

                <!-- Header KPI Stats Cards (Point 1) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Card 1: Status Penerbitan Bulan Ini -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 p-5 rounded-3xl shadow-xs flex items-center justify-between">
                        <div class="space-y-1">
                            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">Status Penerbitan ({{ $kpiStats['current_period_name'] }})</span>
                            @if($kpiStats['total_count'] > 0)
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></span>
                                    <span class="text-base font-extrabold text-emerald-600 dark:text-emerald-400">✓ Tagihan Terbit</span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">{{ $kpiStats['total_count'] }} tagihan aktif diterbitkan</p>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 bg-amber-500 rounded-full"></span>
                                    <span class="text-base font-extrabold text-amber-600 dark:text-amber-400">⚠️ Belum Terbit</span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Gunakan generator di bawah untuk men-generate</p>
                            @endif
                        </div>
                        <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800/80 rounded-2xl flex items-center justify-center text-xl">
                            📋
                        </div>
                    </div>

                    <!-- Card 2: Total Nominal Terbit Bulan Ini -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 p-5 rounded-3xl shadow-xs flex items-center justify-between">
                        <div class="space-y-1">
                            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">Nominal Terbit Bulan Ini</span>
                            <div class="text-lg font-black text-slate-900 dark:text-white font-serif-display">
                                Rp {{ number_format($kpiStats['total_amount'], 0, ',', '.') }}
                            </div>
                            <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold">
                                Terbayar: Rp {{ number_format($kpiStats['paid_amount'], 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center text-xl font-bold">
                            💰
                        </div>
                    </div>

                    <!-- Card 3: Persentase Pelunasan Bulan Ini -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 p-5 rounded-3xl shadow-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Pelunasan Bulan Ini</span>
                            <span class="text-xs font-black text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-full">
                                {{ $kpiStats['percentage'] }}%
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $kpiStats['percentage'] }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-slate-400 font-semibold pt-0.5">
                            <span>Lunas: {{ $kpiStats['paid_count'] }} santri</span>
                            <span>Total: {{ $kpiStats['total_count'] }} santri</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left: Dynamic Custom Generator Form -->
                    <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 sm:p-8 rounded-3xl space-y-6 shadow-sm">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800/80 pb-4">
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider font-serif-display flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span>Generator Penerbitan Tagihan Dinamis</span>
                                </h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">Terbitkan tagihan otomatis ke santri berdasarkan tarif & siklus penagihan yang telah ditentukan.</p>
                            </div>
                            @php
                                $selectedConfig = $genConfigId ? $generatorConfigs->firstWhere('id', $genConfigId) : null;
                            @endphp
                            @if($selectedConfig)
                                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-[10px] font-black rounded-xl uppercase tracking-wider hidden sm:inline-flex">
                                    {{ $selectedConfig->interval }}
                                </span>
                            @endif
                        </div>

                        <!-- Form Field Section -->
                        <div class="space-y-4">
                            <!-- 1. Select Tariff Dropdown (Full Width for Clarity) -->
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Pilih Konfigurasi Tarif Target <span class="text-rose-500">*</span></label>
                                <div x-data="{
                                    initChoices() {
                                        if (typeof Choices === 'undefined') return;
                                        const el = this.$refs.configSelect;
                                        if (el._choices) el._choices.destroy();
                                        const choices = new Choices(el, {
                                            searchEnabled: true,
                                            itemSelectText: '',
                                            shouldSort: false,
                                            placeholder: true,
                                            placeholderValue: '-- Pilih Konfigurasi Tarif --',
                                            noResultsText: 'Tidak ditemukan iuran yang cocok',
                                        });
                                        el._choices = choices;

                                        if ($wire.genConfigId) {
                                            choices.setChoiceByValue($wire.genConfigId);
                                        }

                                        this.$watch('$wire.genConfigId', val => {
                                            if (val) {
                                                choices.setChoiceByValue(val);
                                            } else {
                                                choices.removeActiveItems();
                                            }
                                        });

                                        el.addEventListener('change', (e) => {
                                            $wire.set('genConfigId', e.target.value);
                                        });
                                    }
                                }" x-init="initChoices()" wire:ignore>
                                    <select x-ref="configSelect" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-emerald-500 font-bold shadow-xs">
                                        <option value="">-- Pilih Konfigurasi Tarif --</option>
                                        @foreach($generatorConfigs as $cfg)
                                            <option value="{{ $cfg->id }}" @selected($genConfigId == $cfg->id)>
                                                {{ $cfg->label }} — Rp {{ number_format($cfg->amount, 0, ',', '.') }} ({{ strtoupper($cfg->interval) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- 2. Dynamic Mode & Period Inputs -->
                            @if($selectedConfig)
                                <div class="p-4 bg-slate-50/80 dark:bg-slate-950/50 border border-slate-200/80 dark:border-slate-800 rounded-2xl space-y-4">
                                    <!-- Summary Header Card -->
                                    <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-slate-200/60 dark:border-slate-800 text-xs">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $selectedConfig->label }}</span>
                                            <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-md text-[10px] font-extrabold">
                                                Rp {{ number_format($selectedConfig->amount, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <div class="text-[10px] font-semibold text-slate-400">
                                            <span>Target: {{ ucfirst($selectedConfig->target_type) }}</span>
                                        </div>
                                    </div>

                                    <!-- Mode Penerbitan Dropdown -->
                                    <div>
                                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Mode Penerbitan Tagihan <span class="text-rose-500">*</span></label>
                                        <select wire:model.live="genMode" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-emerald-500 font-bold shadow-xs">
                                            <option value="single">📌 Periode Terpilih Saja (misal: Periode 1 / Bulan Terpilih)</option>
                                            @if(!in_array($selectedConfig->interval, ['once', 'insidental', 'event', 'sekali']))
                                                <option value="full_year">
                                                    🎓 Semua Periode Sekaligus Dalam Setahun 
                                                    @if(in_array($selectedConfig->interval, ['semester', '2x_yearly'])) (2 Semester sekaligus)
                                                    @elseif(in_array($selectedConfig->interval, ['caturwulan', '3x_yearly'])) (3 Caturwulan sekaligus)
                                                    @elseif(in_array($selectedConfig->interval, ['triwulan', '4x_yearly'])) (4 Triwulan sekaligus)
                                                    @elseif(in_array($selectedConfig->interval, ['bimulanan', '6x_yearly'])) (6 Dwibulanan sekaligus)
                                                    @else (12 Bulan / 1 Tahun Ajaran)
                                                    @endif
                                                </option>
                                            @endif
                                        </select>
                                    </div>

                                    @if(in_array($selectedConfig->interval, ['once', 'insidental', 'event', 'sekali']))
                                        <!-- Insidental Info Box -->
                                        <div class="p-3 bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800/50 rounded-xl flex flex-wrap items-center justify-between gap-3">
                                            <div>
                                                <span class="block text-[10px] font-extrabold text-purple-700 dark:text-purple-300 uppercase tracking-wider">⚡ Tagihan Insidental / Event</span>
                                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block mt-0.5">
                                                    Tarif Sekali Bayar — Diterbitkan 1x untuk Tahun {{ $genYear }}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2 text-[11px] font-semibold">
                                                <span class="px-2.5 py-1 bg-white dark:bg-slate-900 border border-purple-200 dark:border-purple-800 rounded-xl text-purple-700 dark:text-purple-300">
                                                    Resmi Berlaku: {{ $selectedConfig->effective_from ? $selectedConfig->effective_from->format('d M Y') : '—' }}
                                                </span>
                                                <span class="px-2.5 py-1 bg-white dark:bg-slate-900 border border-purple-200 dark:border-purple-800 rounded-xl text-purple-700 dark:text-purple-300">
                                                    Tenggat: 
                                                    @if($selectedConfig->due_day_type === 'fixed_date' && $selectedConfig->due_date_specific)
                                                        {{ $selectedConfig->due_date_specific->format('d M Y') }}
                                                    @elseif($selectedConfig->due_day_type === 'days_after')
                                                        {{ $selectedConfig->due_day_value }} Hari
                                                    @elseif($selectedConfig->due_day_type === 'fixed_day')
                                                        Tgl {{ $selectedConfig->due_day_value }}
                                                    @else
                                                        Bebas
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Period & Year Select Grid -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            @if($genMode === 'single' || $selectedConfig->interval === 'monthly')
                                                <div>
                                                    <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">
                                                        @if($genMode === 'full_year')
                                                            Bulan Awal Mulai (12 Bulan Berurutan) <span class="text-rose-500">*</span>
                                                        @else
                                                            Pilih Periode Penagihan <span class="text-rose-500">*</span>
                                                        @endif
                                                    </label>
                                                    <select wire:model="genMonth" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-emerald-500 font-bold shadow-xs">
                                                        @if($genMode === 'single' && in_array($selectedConfig->interval, ['semester', '2x_yearly']))
                                                            <option value="1">Semester 1 (Ganjil / Periode 1)</option>
                                                            <option value="2">Semester 2 (Genap / Periode 2)</option>
                                                        @elseif($genMode === 'single' && in_array($selectedConfig->interval, ['caturwulan', '3x_yearly']))
                                                            <option value="1">Caturwulan 1 (Jan–Apr / Periode 1)</option>
                                                            <option value="2">Caturwulan 2 (Mei–Agt / Periode 2)</option>
                                                            <option value="3">Caturwulan 3 (Sep–Des / Periode 3)</option>
                                                        @elseif($genMode === 'single' && in_array($selectedConfig->interval, ['triwulan', '4x_yearly']))
                                                            <option value="1">Triwulan 1 (Jan–Mar / Periode 1)</option>
                                                            <option value="2">Triwulan 2 (Apr–Jun / Periode 2)</option>
                                                            <option value="3">Triwulan 3 (Jul–Sep / Periode 3)</option>
                                                            <option value="4">Triwulan 4 (Okt–Des / Periode 4)</option>
                                                        @elseif($genMode === 'single' && in_array($selectedConfig->interval, ['bimulanan', '6x_yearly']))
                                                            <option value="1">Dwibulanan 1 (Jan–Feb / Siklus 1)</option>
                                                            <option value="2">Dwibulanan 2 (Mar–Apr / Siklus 2)</option>
                                                            <option value="3">Dwibulanan 3 (Mei–Jun / Siklus 3)</option>
                                                            <option value="4">Dwibulanan 4 (Jul–Agt / Siklus 4)</option>
                                                            <option value="5">Dwibulanan 5 (Sep–Okt / Siklus 5)</option>
                                                            <option value="6">Dwibulanan 6 (Nov–Des / Siklus 6)</option>
                                                        @else
                                                            @for($m = 1; $m <= 12; $m++)
                                                                <option value="{{ $m }}">Bulan {{ $m }} ({{ date('F', mktime(0, 0, 0, $m, 1)) }})</option>
                                                            @endfor
                                                        @endif
                                                    </select>
                                                </div>
                                            @endif
                                            <div class="{{ ($genMode === 'full_year' && $selectedConfig->interval !== 'monthly') ? 'sm:col-span-2' : '' }}">
                                                <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Tahun Penagihan <span class="text-rose-500">*</span></label>
                                                <select wire:model="genYear" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-emerald-500 font-bold shadow-xs cursor-pointer">
                                                    @for($y = now()->format('Y') - 1; $y <= now()->format('Y') + 2; $y++)
                                                        <option value="{{ $y }}">
                                                            {{ $y }}
                                                            @if($y == now()->format('Y') - 1) — Tahun Lalu
                                                            @elseif($y == now()->format('Y')) — Tahun Ini ✓
                                                            @elseif($y == now()->format('Y') + 1) — Tahun Depan
                                                            @elseif($y == now()->format('Y') + 2) — 2 Tahun Depan
                                                            @endif
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- 3. Clean Primary Action Button -->
                        @if($selectedConfig)
                            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                                <button type="button" wire:click="openGeneratePreviewModal" class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-2 ml-auto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Pratinjau & Terbitkan Tagihan</span>
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Right: Polished Active Billing Status Panel (Point 5) -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 p-6 rounded-3xl space-y-4 shadow-sm">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider text-[11px] font-serif-display">Status Iuran Periode Ini</h4>
                                <p class="text-[10px] text-slate-400">Daftar tarif aktif & statusnya bulan ini</p>
                            </div>
                            <span class="text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-lg">
                                {{ count($activeConfigs) }} Config
                            </span>
                        </div>

                        <div class="space-y-2.5 max-h-72 overflow-y-auto pr-1">
                            @forelse($activeConfigs as $cfg)
                                @php
                                    $isGen = in_array($cfg->id, $kpiStats['generated_configs']);
                                @endphp
                                <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200/50 dark:border-slate-800/80 rounded-2xl text-xs">
                                    <div class="pr-2 truncate">
                                        <span class="font-bold text-slate-800 dark:text-slate-200 block truncate">{{ $cfg->label }}</span>
                                        <span class="text-[10px] text-slate-400">Rp {{ number_format($cfg->amount, 0, ',', '.') }} / {{ $cfg->interval }}</span>
                                    </div>
                                    <div>
                                        @if($isGen)
                                            <span class="px-2 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-extrabold rounded-xl border border-emerald-500/20 whitespace-nowrap">
                                                ✓ Terbit
                                            </span>
                                        @else
                                            <button wire:click="$set('genConfigId', '{{ $cfg->id }}')" class="px-2 py-1 bg-amber-500/10 hover:bg-amber-500 text-amber-600 hover:text-white text-[10px] font-extrabold rounded-xl transition-all whitespace-nowrap">
                                                ⚡ Pilih & Terbitkan
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-slate-400 text-xs font-semibold">
                                    Belum ada konfigurasi tarif aktif.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Tabel Riwayat Penerbitan Tagihan -->
                <div id="riwayat-penerbitan" class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 sm:p-8 rounded-3xl space-y-6 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider block font-serif-display">Riwayat Penerbitan Tagihan</h3>
                            <p class="text-[11px] text-slate-400">Daftar iuran yang sudah pernah digenerate dalam database. Anda dapat menghapus massal tagihan yang salah buat di sini.</p>
                        </div>
                        <!-- Filter Controls -->
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="w-40">
                                <input type="text" wire:model.live.debounce.300ms="histSearch" placeholder="🔍 Cari Nama..." 
                                       class="w-full bg-slate-50/90 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 placeholder-slate-400 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500 font-bold shadow-xs">
                            </div>
                            <div class="w-32">
                                <select wire:model.live="histMonth" class="w-full bg-slate-50/90 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-2.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 font-bold shadow-xs">
                                    <option value="">📅 Semua Bulan</option>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="w-28">
                                <select wire:model.live="histYear" class="w-full bg-slate-50/90 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-2.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 font-bold shadow-xs cursor-pointer">
                                    <option value="">🗓️ Semua Thn</option>
                                    @for($y = now()->format('Y') - 2; $y <= now()->format('Y') + 2; $y++)
                                        <option value="{{ $y }}">
                                            {{ $y }}
                                            @if($y == now()->format('Y') - 2) (2 Thn Lalu)
                                            @elseif($y == now()->format('Y') - 1) (Thn Lalu)
                                            @elseif($y == now()->format('Y')) (Thn Ini ✓)
                                            @elseif($y == now()->format('Y') + 1) (Thn Depan)
                                            @elseif($y == now()->format('Y') + 2) (2 Thn Depan)
                                            @endif
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="w-36">
                                <select wire:model.live="histInterval" class="w-full bg-slate-50/90 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-2.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 font-bold shadow-xs">
                                    <option value="">⚡ Semua Siklus</option>
                                    <option value="monthly">Bulanan</option>
                                    <option value="semester">Semesteran</option>
                                    <option value="yearly">Tahunan</option>
                                    <option value="insidental">Sekali / Event</option>
                                </select>
                            </div>
                            <div class="w-36">
                                <select wire:model.live="histType" class="w-full bg-slate-50/90 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-2.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 font-bold shadow-xs">
                                    <option value="">🏷️ Semua Tipe</option>
                                    <option value="syahriah_pondok">Syahriah Pondok</option>
                                    <option value="kas_komplek">Kas Komplek</option>
                                    <option value="majek_pagi">Majek Pagi</option>
                                    <option value="majek_sore">Majek Sore</option>
                                    <option value="syahriah_madrasah">Syahriah Madrasah</option>
                                    <option value="kebersihan">Kebersihan</option>
                                    <option value="kitab">Kitab</option>
                                    <option value="insidental">Event / Kegiatan</option>
                                </select>
                            </div>
                            <div class="w-32">
                                <select wire:model.live="histGender" class="w-full bg-slate-50/90 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-2.5 py-2 text-xs focus:ring-2 focus:ring-emerald-500 font-bold shadow-xs">
                                    <option value="">👥 Target Gender</option>
                                    <option value="L">👦 Putra (L)</option>
                                    <option value="P">👧 Putri (P)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800">
                                    <th class="py-3.5 px-4 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">Nama Iuran</th>
                                    <th class="py-3.5 px-4 text-slate-500 font-extrabold uppercase tracking-wider text-[9px] text-center">Target Gender</th>
                                    <th class="py-3.5 px-4 text-slate-500 font-extrabold uppercase tracking-wider text-[9px] text-center">Tipe & Siklus</th>
                                    <th class="py-3.5 px-4 text-slate-500 font-extrabold uppercase tracking-wider text-[9px] text-center">Periode Tagihan</th>
                                    <th class="py-3.5 px-4 text-slate-500 font-extrabold uppercase tracking-wider text-[9px] text-center">Jumlah Santri</th>
                                    <th class="py-3.5 px-4 text-slate-500 font-extrabold uppercase tracking-wider text-[9px] text-right">Total Nominal</th>
                                    <th class="py-3.5 px-4 text-slate-500 font-extrabold uppercase tracking-wider text-[9px] text-center">Diterbitkan Oleh</th>
                                    <th class="py-3.5 px-4 text-slate-500 font-extrabold uppercase tracking-wider text-[9px] text-center">Waktu Penerbitan</th>
                                    <th class="py-3.5 px-4 text-slate-500 font-extrabold uppercase tracking-wider text-[9px] text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                @forelse($generationHistory as $hist)
                                    @php
                                        $cfg = $hist->config;
                                        $targetGenders = ($cfg && $cfg->target_type === 'all' && !empty($cfg->target_filters)) ? (array)$cfg->target_filters : [];
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                        <td class="py-3.5 px-4 font-semibold text-slate-800 dark:text-slate-200">
                                            {{ $cfg?->label ?? 'Iuran Terhapus' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            @if(empty($targetGenders) || count($targetGenders) >= 2)
                                                <span class="px-2 py-0.5 bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[10px] font-extrabold rounded-md">
                                                    🌐 Putra & Putri
                                                </span>
                                            @elseif(in_array('L', $targetGenders))
                                                <span class="px-2 py-0.5 bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 text-[10px] font-extrabold rounded-md">
                                                    👦 Putra
                                                </span>
                                            @elseif(in_array('P', $targetGenders))
                                                <span class="px-2 py-0.5 bg-pink-500/10 text-pink-600 dark:text-pink-400 text-[10px] font-extrabold rounded-md">
                                                    👧 Putri
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-[10px]">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-[10px] font-extrabold rounded-lg uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                                {{ $cfg?->interval ?? 'once' }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center font-bold text-slate-700 dark:text-slate-300">
                                            {{ $this->formatPeriodLabel($cfg?->interval, $hist->period_month, $hist->period_year) }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center font-bold text-emerald-600 dark:text-emerald-400">
                                            👤 {{ $hist->total_students }} Santri
                                        </td>
                                        <td class="py-3.5 px-4 text-right font-bold text-slate-800 dark:text-slate-200">
                                            Rp {{ number_format($hist->total_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-purple-500/10 text-purple-700 dark:text-purple-300 text-[10px] font-extrabold rounded-lg">
                                                👤 {{ $hist->creator?->name ?? ($cfg?->creator?->name ?? 'Sistem') }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center text-slate-400 text-[10px]">
                                            {{ \Carbon\Carbon::parse($hist->generated_at)->translatedFormat('d M Y H:i') }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            @if($cfg)
                                                <div class="flex items-center justify-center gap-1.5">
                                                    <button wire:click="viewHistoryDetail('{{ $cfg->id }}', {{ $hist->period_month }}, {{ $hist->period_year }})"
                                                            title="Lihat Detail Penerbitan & Progress Pelunasan"
                                                            class="px-2.5 py-1.5 bg-indigo-500/10 hover:bg-indigo-600 text-indigo-600 hover:text-white rounded-xl text-[10px] font-bold transition-all inline-flex items-center gap-1 shadow-2xs">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        <span>Detail</span>
                                                    </button>
                                                    <button wire:click="openBatchDeleteConfirmModal('{{ $cfg->id }}', {{ $hist->period_month }}, {{ $hist->period_year }})"
                                                            title="Hapus Massal Tagihan Unpaid Periode Ini"
                                                            class="px-2.5 py-1.5 bg-rose-500/10 hover:bg-rose-600 text-rose-600 hover:text-white rounded-xl text-[10px] font-bold transition-all inline-flex items-center gap-1 shadow-2xs">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        <span>Hapus Unpaid</span>
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-slate-300 dark:text-slate-700">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="py-12 text-center text-slate-400 font-semibold">
                                            Belum ada riwayat penerbitan tagihan yang cocok dengan filter.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Footer for History Table -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-[11px] font-semibold text-slate-400">
                            Menampilkan <span class="font-bold text-slate-700 dark:text-slate-300">{{ $generationHistory->firstItem() ?? 0 }}</span> s.d. <span class="font-bold text-slate-700 dark:text-slate-300">{{ $generationHistory->lastItem() ?? 0 }}</span> dari <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $generationHistory->total() }}</span> riwayat penerbitan
                        </div>
                        <div>
                            {{ $generationHistory->links(data: ['scrollTo' => false]) }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB 2: KONFIGURASI TARIF & TARGET -->
        @if ($activeTab === 'rates')
            <div class="space-y-8">
                 <!-- Configurations List Table -->
                 <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 rounded-3xl shadow-sm overflow-hidden space-y-4">
                     <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-3">
                         <div>
                             <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider block font-serif-display">Daftar Konfigurasi Tarif</h3>
                             <p class="text-[11px] text-slate-400">Semua skema iuran aktif maupun nonaktif yang dapat dikelola.</p>
                         </div>

                         <div class="flex flex-wrap items-center gap-3">
                             {{-- Search Input --}}
                             <div class="relative min-w-[200px]">
                                 <input type="text" wire:model.live.debounce.300ms="rateSearchQuery" placeholder="Cari nama tarif..."
                                     class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 text-xs rounded-xl pl-8 pr-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                                 <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                             </div>

                             {{-- Status Filter --}}
                             <select wire:model.live="rateStatusFilter" class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 text-xs rounded-xl px-3 py-2 focus:ring-emerald-500 font-semibold">
                                 <option value="">Semua Status</option>
                                 <option value="active">🟢 Aktif</option>
                                 <option value="inactive">⚪ Nonaktif</option>
                             </select>

                             {{-- Tambah Baru --}}
                             <a href="{{ route('keuangan.billing.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-1.5">
                                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                 Tambah Iuran Baru
                             </a>
                         </div>
                     </div>
                     <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">
                                    <th class="py-3 px-4">Nama Iuran</th>
                                    <th class="py-3 px-4">Tipe Tagihan</th>
                                    <th class="py-3 px-4 text-right">Nominal</th>
                                    <th class="py-3 px-4">Siklus</th>
                                    <th class="py-3 px-4 text-center">Bisa Dicicil</th>
                                    <th class="py-3 px-4">Target</th>
                                    <th class="py-3 px-4">Pengelola</th>
                                    <th class="py-3 px-4">Dibuat Oleh</th>
                                    <th class="py-3 px-4 text-center">Status</th>
                                    <th class="py-3 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                @foreach($activeConfigs as $ac)
                                    <tr class="{{ $ac->is_active ? '' : 'opacity-70 bg-slate-50/50 dark:bg-slate-900/40' }} hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                        <td class="py-3 px-4 font-bold text-slate-800 dark:text-slate-200">{{ $ac->label }}</td>
                                        <td class="py-3 px-4 uppercase text-slate-500 text-[10px]">{{ str_replace('_', ' ', $ac->type) }}</td>
                                        <td class="py-3 px-4 text-right font-semibold text-slate-800 dark:text-slate-200">Rp {{ number_format($ac->amount, 0, ',', '.') }}</td>
                                        <td class="py-3 px-4 uppercase text-[10px] text-slate-500">{{ $ac->interval }}</td>
                                        <td class="py-3 px-4 text-center">
                                            @if($ac->can_be_installment)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-emerald-500/10 text-emerald-600">YA</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-slate-100 text-slate-500">TIDAK</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase {{ $ac->target_type === 'all' ? 'bg-blue-500/10 text-blue-600' : ($ac->target_type === 'dormitory' ? 'bg-amber-500/10 text-amber-600' : 'bg-purple-500/10 text-purple-600') }}">
                                                @if($ac->target_type === 'all')
                                                    @if(is_array($ac->target_filters) && in_array('P', $ac->target_filters))
                                                        Semua Santri (Putri)
                                                    @elseif(is_array($ac->target_filters) && in_array('L', $ac->target_filters))
                                                        Semua Santri (Putra)
                                                    @else
                                                        Semua Santri
                                                    @endif
                                                @else
                                                    {{ $ac->target_type }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-slate-600 dark:text-slate-400 font-semibold">{{ $ac->manager_role ?: 'Bendahara Pusat' }}</td>
                                        <td class="py-3 px-4 text-slate-700 dark:text-slate-300 font-semibold text-xs">
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md text-[10px] text-slate-700 dark:text-slate-300 font-bold">
                                                👤 {{ $ac->creator?->name ?: 'Sistem' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if($ac->is_active)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/40">
                                                    🟢 Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                                    ⚪ Nonaktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                             <div class="flex items-center justify-center gap-1.5">
                                                 {{-- 1. Cetak --}}
                                                 <a href="{{ route('keuangan.billing.print-setup', $ac->id) }}"
                                                     class="p-1.5 bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/60 dark:hover:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-lg transition-colors"
                                                     title="Cetak Rincian Tarif">
                                                     <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                 </a>

                                                 {{-- 2. Salin --}}
                                                 <button type="button" wire:click="duplicateConfig('{{ $ac->id }}')"
                                                     class="p-1.5 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/60 dark:hover:bg-amber-900 text-amber-600 dark:text-amber-300 rounded-lg transition-colors"
                                                     title="Salin / Duplikat Tarif Ini">
                                                     <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                                 </button>

                                                 {{-- 3. Edit --}}
                                                 <a href="{{ route('keuangan.billing.edit', $ac->id) }}"
                                                     class="p-1.5 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:hover:bg-emerald-900 text-emerald-600 dark:text-emerald-300 rounded-lg transition-colors"
                                                     title="Edit Pengaturan Tarif">
                                                     <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                 </a>

                                                 {{-- 4. Toggle Nonaktifkan / Aktifkan --}}
                                                 <button type="button" wire:click="openTariffActionModal('{{ $ac->id }}', 'toggle_status')"
                                                     class="p-1.5 {{ $ac->is_active ? 'bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-300' : 'bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-300' }} rounded-lg transition-colors"
                                                     title="{{ $ac->is_active ? 'Nonaktifkan Tarif' : 'Aktifkan Kembali Tarif' }}">
                                                     @if($ac->is_active)
                                                         <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                     @else
                                                         <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                     @endif
                                                 </button>

                                                 {{-- 5. Hapus Permanent --}}
                                                 <button type="button" wire:click="openTariffActionModal('{{ $ac->id }}', 'delete')"
                                                     class="p-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 dark:hover:bg-rose-900 text-rose-600 dark:text-rose-300 rounded-lg transition-colors"
                                                     title="Hapus Permanent (Jika belum ada tagihan terbit)">
                                                     <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                 </button>
                                             </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Links --}}
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                        {{ $activeConfigs->links() }}
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB 3: KASIR PEMBAYARAN UTAMA -->
        @if ($activeTab === 'cashier')
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- ===== LEFT PANEL: BROWSE & SEARCH ===== --}}
                <div class="lg:col-span-4 space-y-4">

                    {{-- Browse / Search Card --}}
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">

                        {{-- Header --}}
                        <div class="px-5 pt-5 pb-4 border-b border-slate-100 dark:border-slate-800">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider font-serif-display">Pilih Santri</h3>
                            <p class="text-[10px] text-slate-400 mt-0.5">Browse lewat filter, atau langsung cari nama / NIS.</p>
                        </div>

                        {{-- Filter Row: Komplek → Kamar → Kelas --}}
                        <div class="px-4 pt-4 pb-3 space-y-2 border-b border-slate-100 dark:border-slate-800">
                            {{-- Komplek --}}
                            <select wire:model.live="filterKomplek"
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-xl px-3 py-2 text-xs focus:ring-emerald-500 font-semibold">
                                <option value="">🏘 Semua Komplek</option>
                                @foreach($dormitories as $dorm)
                                    <option value="{{ $dorm->id }}">{{ $dorm->name }}</option>
                                @endforeach
                            </select>

                            {{-- Kamar (hanya muncul jika komplek dipilih) --}}
                            @if($filterKomplek && !$roomsForKomplek->isEmpty())
                                <select wire:model.live="filterKamar"
                                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-xl px-3 py-2 text-xs focus:ring-emerald-500 font-semibold">
                                    <option value="">🚪 Semua Kamar</option>
                                    @foreach($roomsForKomplek as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                                    @endforeach
                                </select>
                            @endif

                            {{-- Kelas --}}
                            <select wire:model.live="filterKelas"
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-xl px-3 py-2 text-xs focus:ring-emerald-500 font-semibold">
                                <option value="">📚 Semua Kelas</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->name }}</option>
                                @endforeach
                            </select>

                            {{-- Reset filter --}}
                            @if($filterKomplek || $filterKamar || $filterKelas)
                                <button wire:click="$set('filterKomplek', ''); $set('filterKamar', ''); $set('filterKelas', '')"
                                    class="w-full text-center text-[10px] text-slate-400 hover:text-rose-500 font-bold transition-colors py-0.5">
                                    ✕ Reset Filter
                                </button>
                            @endif
                        </div>

                        {{-- Search Bar --}}
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                                </svg>
                                <input type="text" wire:model.live.debounce.300ms="searchQuery"
                                    placeholder="Cari nama santri atau NIS..."
                                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl pl-9 pr-8 py-2 text-xs focus:ring-emerald-500">
                                @if($searchQuery)
                                    <button wire:click="$set('searchQuery', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 font-bold text-xs">✕</button>
                                @endif
                            </div>
                        </div>

                        {{-- Santri List --}}
                        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
                            @if(!$santriSearchResults->isEmpty())
                                @if($filterKomplek || $filterKamar || $filterKelas || strlen($searchQuery) >= 2)
                                    <div class="px-4 py-2 flex items-center justify-between">
                                        <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest">
                                            {{ $santriSearchResults->count() }} Santri Ditemukan
                                        </span>
                                        @if($filterKomplek || $filterKamar || $filterKelas)
                                            <span class="text-[9px] text-emerald-600 font-bold">Hasil Filter</span>
                                        @else
                                            <span class="text-[9px] text-slate-400">Hasil Pencarian</span>
                                        @endif
                                    </div>
                                @endif

                                @foreach($santriSearchResults as $s)
                                    @php
                                        $sRoom = $s->roomAssignments->firstWhere('is_active', true)?->room;
                                        $sDorm = $sRoom?->dormitory?->name ?? null;
                                        $sKamar = $sRoom?->name ?? null;
                                        $sKelas = $s->madrasahEnrollments->firstWhere('is_active', true)?->kelas?->name ?? null;
                                        $isSelected = $selectedSantriId === $s->id;
                                    @endphp
                                    <button wire:click="selectSantri('{{ $s->id }}')"
                                        class="w-full text-left px-4 py-3 transition-all flex items-center gap-3
                                            {{ $isSelected
                                                ? 'bg-emerald-50 dark:bg-emerald-950/30 border-l-2 border-emerald-500'
                                                : 'hover:bg-slate-50 dark:hover:bg-slate-800/40 border-l-2 border-transparent' }}">
                                        {{-- Avatar --}}
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-xs font-extrabold
                                            {{ $isSelected ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                                            {{ substr($s->name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <span class="font-bold text-slate-800 dark:text-slate-200 block text-xs truncate">{{ $s->name }}</span>
                                            <span class="text-[9px] text-slate-400 block truncate">
                                                @if($sDorm) {{ $sDorm }} @if($sKamar) · {{ $sKamar }} @endif @else Laju @endif
                                                @if($sKelas)  · {{ $sKelas }} @endif
                                            </span>
                                        </div>
                                        @if($isSelected)
                                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        @else
                                            <svg class="w-3.5 h-3.5 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        @endif
                                    </button>
                                @endforeach

                            @elseif($filterKomplek || $filterKamar || $filterKelas || strlen($searchQuery) >= 2)
                                <div class="px-4 py-8 text-center">
                                    <div class="text-2xl mb-2">😕</div>
                                    <p class="text-[10px] text-slate-400 font-semibold">Tidak ada santri ditemukan</p>
                                    <p class="text-[9px] text-slate-400 mt-0.5">Coba ubah filter atau kata pencarian</p>
                                </div>

                            @elseif(!$recentSantri->isEmpty())
                                {{-- Recent Santri (shown when no active filter/search) --}}
                                <div class="px-4 py-2">
                                    <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest">🕐 Terakhir Dibuka</span>
                                </div>
                                @foreach($recentSantri as $rs)
                                    @php
                                        $rsRoom = $rs->roomAssignments->firstWhere('is_active', true)?->room;
                                        $rsDorm = $rsRoom?->dormitory?->name ?? 'Laju';
                                        $rsKamar = $rsRoom?->name ?? null;
                                        $rsSelected = $selectedSantriId === $rs->id;
                                    @endphp
                                    <button wire:click="selectSantri('{{ $rs->id }}')"
                                        class="w-full text-left px-4 py-3 transition-all flex items-center gap-3
                                            {{ $rsSelected
                                                ? 'bg-emerald-50 dark:bg-emerald-950/30 border-l-2 border-emerald-500'
                                                : 'hover:bg-slate-50 dark:hover:bg-slate-800/40 border-l-2 border-transparent' }}">
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-xs font-extrabold
                                            {{ $rsSelected ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                                            {{ substr($rs->name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <span class="font-bold text-slate-800 dark:text-slate-200 block text-xs truncate">{{ $rs->name }}</span>
                                            <span class="text-[9px] text-slate-400">{{ $rsDorm }}@if($rsKamar) · {{ $rsKamar }} @endif</span>
                                        </div>
                                        @if($rsSelected)
                                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </button>
                                @endforeach

                            @else
                                <div class="px-4 py-8 text-center">
                                    <div class="text-3xl mb-2">👥</div>
                                    <p class="text-[10px] text-slate-400 font-semibold">Pilih komplek / kelas di atas</p>
                                    <p class="text-[9px] text-slate-400 mt-0.5">atau ketik nama / NIS santri</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Payment Form --}}
                    @if($selectedSantri)
                        <div x-data="{ 
                            totalSelected: {{ $this->selectedBillsTotal }},
                            payAmount: @entangle('payAmount')
                        }" x-effect="totalSelected = {{ $this->selectedBillsTotal }}" class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-5 rounded-3xl shadow-sm space-y-4">
                            <div>
                                <span class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider">Formulir Pembayaran</span>
                                <p class="text-[10px] text-slate-500 mt-0.5">Centang tagihan di kanan lalu isi nominal.</p>
                            </div>

                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Total Terpilih</label>
                                <div class="px-4 py-3 bg-emerald-50 dark:bg-emerald-950/20 rounded-xl text-lg font-extrabold text-emerald-600 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/30">
                                    Rp {{ number_format($this->selectedBillsTotal, 0, ',', '.') }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Uang Diterima (Rp)</label>
                                <input type="number" x-model.number="payAmount" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500 text-right font-bold">
                                @error('payAmount')
                                    <p class="text-[10px] text-rose-500 mt-1 font-semibold">⚠ {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Metode Setoran</label>
                                <select wire:model="payMethod" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2 text-xs focus:ring-emerald-500 font-bold">
                                    <option value="CASH">💵 Cash (Tunai)</option>
                                    <option value="TRANSFER">🏦 Transfer Bank</option>
                                    <option value="EWALLET">📱 E-Wallet</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Catatan</label>
                                <input type="text" wire:model="payNotes" placeholder="Opsional: catatan transaksi..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2 text-xs focus:ring-emerald-500">
                            </div>

                            @if(!empty($selectedBillIds))
                                <button wire:click="initiatePayment"
                                    class="w-full px-5 py-3 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Catat Pembayaran ({{ count($selectedBillIds) }} tagihan)
                                </button>
                            @else
                                <div class="w-full px-5 py-3 bg-slate-100 dark:bg-slate-800 text-slate-400 rounded-xl text-xs font-bold text-center">
                                    ☑ Centang tagihan terlebih dahulu
                                </div>
                            @endif
                        </div>
                    @endif

                </div>

                {{-- ===== RIGHT PANEL: LEMBAR TAGIHAN ===== --}}
                <div class="lg:col-span-8 space-y-5 @if(!empty($selectedBillIds)) pb-28 md:pb-0 @endif">
                    @if(!$selectedSantri)
                        <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-16 rounded-3xl text-center shadow-sm">
                            <div class="text-4xl mb-3">🔍</div>
                            <p class="text-slate-400 text-sm font-semibold">Cari dan pilih santri di sebelah kiri</p>
                            <p class="text-slate-400 text-xs mt-1">untuk membuka lembar tagihan & pembayaran.</p>
                        </div>
                    @else
                        @php
                            $monthNames = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agt',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
                            $nowMonth = (int)now()->format('n');
                            $nowYear  = (int)now()->format('Y');
                        @endphp

                        {{-- Profile Header --}}
                        <div class="p-5 bg-slate-900 text-white rounded-3xl shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center font-extrabold text-xl text-white shrink-0">
                                    {{ substr($selectedSantri->name, 0, 1) }}
                                </div>
                                <div>
                                    <h2 class="text-base font-bold">{{ $selectedSantri->name }}</h2>
                                    <span class="text-xs text-slate-400">
                                        NIS: {{ $selectedSantri->nis ?? '—' }} &nbsp;|&nbsp;
                                        {{ $selectedSantri->gender === 'L' ? '👦 Putra' : '👧 Putri' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <div class="flex gap-4 text-xs">
                                    <div>
                                        <span class="text-slate-500 text-[9px] uppercase tracking-wider block">Komplek</span>
                                        <span class="font-bold text-emerald-400">{{ $selectedSantri->roomAssignments->firstWhere('is_active', true)?->room?->dormitory?->name ?? 'Laju' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 text-[9px] uppercase tracking-wider block">Kelas</span>
                                        <span class="font-bold text-amber-400">{{ $selectedSantri->madrasahEnrollments->firstWhere('is_active', true)?->kelas?->name ?? 'Non-Madrasah' }}</span>
                                    </div>
                                </div>
                                <button type="button" wire:click="openKasirAddBillModal" class="px-3 py-2 bg-emerald-500/20 hover:bg-emerald-500 text-emerald-300 hover:text-white border border-emerald-500/30 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    <span>Buka Tagihan Di Muka</span>
                                </button>
                            </div>
                        </div>

                        {{-- 0: TUNGGAKAN LAMA --}}
                        @if(!$this->tunggakanLamaBills->isEmpty())
                            <div class="bg-rose-500/5 border border-rose-500/20 p-5 rounded-3xl space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-xs font-extrabold text-rose-600 dark:text-rose-400 uppercase tracking-widest">🚨 Tunggakan Tahun Lalu</h4>
                                        <p class="text-[10px] text-slate-500">Tagihan dari sebelum tahun {{ $cashierYear }} yang belum dilunasi.</p>
                                    </div>
                                    <div class="flex gap-2">
                                        @php
                                            $tunggakanIds = $this->tunggakanLamaBills->pluck('id')->toArray();
                                            $selectedTunggakanCount = count(array_intersect($tunggakanIds, $selectedBillIds));
                                        @endphp
                                        
                                        @if($selectedTunggakanCount > 0)
                                            <button type="button" wire:click="deselectTunggakan"
                                                class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-[10px] font-bold shrink-0 transition-colors">
                                                Batal Pilih ({{ $selectedTunggakanCount }})
                                            </button>
                                        @endif
                                        
                                        @if($selectedTunggakanCount < count($tunggakanIds))
                                            <button type="button" wire:click="selectTunggakan"
                                                class="px-3 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 rounded-xl text-[10px] font-bold shrink-0 transition-colors">
                                                Pilih Semua
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-xs border-collapse">
                                        <thead>
                                            <tr class="text-rose-600 font-bold uppercase text-[9px] border-b border-rose-500/10">
                                                <th class="py-2 px-2 text-left w-6"></th>
                                                <th class="py-2 px-2 text-left">Nama Tagihan</th>
                                                <th class="py-2 px-2 text-center">Periode</th>
                                                <th class="py-2 px-2 text-right">Sisa Bayar</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-rose-500/5">
                                            @foreach($this->tunggakanLamaBills as $tb)
                                                <tr class="{{ in_array($tb->id, $selectedBillIds) ? 'bg-rose-50 dark:bg-rose-950/20' : '' }}">
                                                    <td class="py-2 px-2">
                                                        <input type="checkbox" wire:model.live="selectedBillIds" value="{{ $tb->id }}" class="rounded text-rose-600 focus:ring-rose-500">
                                                    </td>
                                                    <td class="py-2 px-2 font-semibold text-slate-700 dark:text-slate-300">{{ $tb->config?->label ?? str_replace('_', ' ', $tb->bill_type) }}</td>
                                                    <td class="py-2 px-2 text-center text-slate-500">{{ $monthNames[$tb->period_month] }} {{ $tb->period_year }}</td>
                                                    <td class="py-2 px-2 text-right font-bold text-rose-600">Rp {{ number_format($tb->amount - $tb->amount_paid, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- LEGENDA KASIR RESMI & INTERAKTIF --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3.5 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-2xs text-[10px]">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-[10px]">Legenda Status:</span>
                            </div>
                            <div class="flex flex-wrap items-center gap-4 text-slate-600 dark:text-slate-300 font-bold">
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="w-4 h-4 bg-emerald-500 rounded-full inline-flex items-center justify-center text-white shadow-2xs">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span>Lunas</span>
                                </span>
                                <span class="inline-flex items-center gap-1.5">
                                    <input type="checkbox" class="rounded text-emerald-600 accent-emerald-600 w-3.5 h-3.5" disabled checked>
                                    <span>Belum Bayar (klik untuk pilih)</span>
                                </span>
                                <span class="inline-flex items-center gap-1.5">
                                    <input type="checkbox" class="rounded text-amber-500 accent-amber-500 w-3.5 h-3.5" disabled checked>
                                    <span class="text-amber-600 dark:text-amber-400">Cicilan / Parsial</span>
                                </span>
                                <span class="inline-flex items-center gap-1.5 text-slate-400">
                                    <span class="text-slate-300 dark:text-slate-600 font-bold">—</span>
                                    <span>Belum Terbit</span>
                                </span>
                            </div>
                        </div>

                        {{-- 1: TAGIHAN BULANAN (Jan-Des tabel) --}}
                        @php $bulananBills = $this->bulananBills; @endphp
                        <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                                <div>
                                    <h4 class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-widest">📋 Tagihan Bulanan</h4>
                                    <p class="text-[10px] text-slate-400">Lembar iuran Januari — Desember</p>
                                </div>
                                <select wire:model.live="cashierYear" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-1.5 text-xs font-bold focus:ring-2 focus:ring-emerald-500 shadow-sm cursor-pointer">
                                    @for($y = $nowYear - 2; $y <= $nowYear + 1; $y++)
                                        <option value="{{ $y }}">
                                            {{ $y }}
                                            @if($y === $nowYear - 2) — 2 Tahun Lalu
                                            @elseif($y === $nowYear - 1) — Tahun Lalu
                                            @elseif($y === $nowYear) — Tahun Ini ✓
                                            @elseif($y === $nowYear + 1) — Tahun Depan
                                            @endif
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            @if(empty($bulananBills))
                                <div class="p-8 text-center text-slate-400 text-xs">
                                    Belum ada tagihan bulanan untuk santri ini di tahun {{ $cashierYear }}.
                                </div>
                            @else
                                {{-- DESKTOP VIEW MATRIX TABLE (hidden on mobile) --}}
                                <div class="hidden md:block overflow-x-auto">
                                    <table class="w-full text-left border-collapse text-xs min-w-[700px]">
                                        <thead>
                                            <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800">
                                                <th class="py-3 px-3 text-[9px] font-extrabold text-slate-500 uppercase tracking-wider w-40 sticky left-0 bg-slate-50 dark:bg-slate-950">Jenis Iuran</th>
                                                @foreach($monthNames as $mNum => $mLabel)
                                                    <th class="py-3 px-1 text-center text-[9px] font-extrabold uppercase
                                                        {{ ($mNum === $nowMonth && $cashierYear === $nowYear) ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500' }}">
                                                        {{ $mLabel }}
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                            @foreach($bulananBills as $configId => $configData)
                                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors">
                                                    <td class="py-3 px-3 font-semibold text-slate-700 dark:text-slate-300 text-[10px] leading-tight sticky left-0 bg-white dark:bg-slate-900">
                                                        <div class="space-y-1">
                                                            <span class="block truncate font-bold text-slate-800 dark:text-slate-200">{{ $configData['label'] }}</span>
                                                            <button type="button" wire:click="selectUpToCurrentMonth('{{ $configId }}')"
                                                                class="inline-flex items-center gap-1 text-[8px] font-extrabold text-emerald-600 dark:text-emerald-400 hover:underline">
                                                                ⚡ s.d. {{ $monthNames[$nowMonth] ?? 'Bulan Ini' }}
                                                            </button>
                                                        </div>
                                                    </td>
                                                    @foreach($monthNames as $mNum => $mLabel)
                                                        @php $bill = $configData['months'][$mNum] ?? null; @endphp
                                                        <td class="py-2 px-1 text-center {{ ($bill && $bill->status === 'partial') ? 'bg-amber-500/5 dark:bg-amber-950/20' : '' }}">
                                                            @if(!$bill)
                                                                <span class="text-slate-200 dark:text-slate-700 text-base">—</span>
                                                            @elseif($bill->status === 'paid')
                                                                <span title="Lunas — Rp {{ number_format($bill->amount, 0, ',', '.') }}"
                                                                    class="inline-flex items-center justify-center w-6 h-6 bg-emerald-500 rounded-full cursor-default shadow-2xs">
                                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                                </span>
                                                            @elseif($bill->status === 'partial')
                                                                <label class="cursor-pointer flex flex-col items-center justify-center gap-0.5" title="Cicilan — Sisa Rp {{ number_format($bill->amount - $bill->amount_paid, 0, ',', '.') }}">
                                                                    <input type="checkbox" wire:model.live="selectedBillIds" value="{{ $bill->id }}"
                                                                        class="rounded text-amber-500 accent-amber-500 focus:ring-amber-400 w-4 h-4 border-amber-300 dark:border-amber-700 bg-amber-50/50">
                                                                    <span class="text-[8px] font-extrabold text-amber-600 dark:text-amber-400 whitespace-nowrap scale-90 leading-none">Sisa {{ number_format(($bill->amount - $bill->amount_paid)/1000, 0) }}k</span>
                                                                </label>
                                                            @else
                                                                <label class="cursor-pointer flex items-center justify-center" title="Belum Bayar — Rp {{ number_format($bill->amount, 0, ',', '.') }}">
                                                                    <input type="checkbox" wire:model.live="selectedBillIds" value="{{ $bill->id }}"
                                                                        class="rounded text-emerald-600 accent-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                                                </label>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- MOBILE VIEW CARD GRID CHIPS (block on mobile, hidden on desktop) --}}
                                <div class="block md:hidden p-4 space-y-4">
                                    @foreach($bulananBills as $configId => $configData)
                                        <div class="p-3.5 bg-slate-50/70 dark:bg-slate-950/60 border border-slate-200/80 dark:border-slate-800 rounded-2xl space-y-2.5">
                                            <div class="flex items-center justify-between gap-2 border-b border-slate-200/60 dark:border-slate-800 pb-2">
                                                <div>
                                                    <span class="font-extrabold text-slate-800 dark:text-slate-200 text-xs block">{{ $configData['label'] }}</span>
                                                    <span class="text-[9px] text-slate-400">Bulanan {{ $cashierYear }}</span>
                                                </div>
                                                <button type="button" wire:click="selectUpToCurrentMonth('{{ $configId }}')"
                                                    class="px-2.5 py-1 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 rounded-xl text-[9px] font-black transition-all">
                                                    ⚡ s.d. {{ $monthNames[$nowMonth] ?? 'Bulan Ini' }}
                                                </button>
                                            </div>

                                            <!-- 12 Month Touch Grid Chips (4 cols x 3 rows) -->
                                            <div class="grid grid-cols-4 gap-1.5">
                                                @foreach($monthNames as $mNum => $mLabel)
                                                    @php
                                                        $bill = $configData['months'][$mNum] ?? null;
                                                        $isSelected = $bill && in_array($bill->id, $selectedBillIds);
                                                    @endphp
                                                    @if(!$bill)
                                                        <div class="py-2 px-1 rounded-xl bg-slate-100/50 dark:bg-slate-900/50 border border-slate-200/40 dark:border-slate-800/40 text-center opacity-40">
                                                            <span class="block text-[8px] font-extrabold text-slate-400 uppercase">{{ $mLabel }}</span>
                                                            <span class="text-[8px] text-slate-300 dark:text-slate-600">—</span>
                                                        </div>
                                                    @elseif($bill->status === 'paid')
                                                        <div class="py-2 px-1 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-center">
                                                            <span class="block text-[8px] font-black text-emerald-600 dark:text-emerald-400 uppercase">✓ {{ $mLabel }}</span>
                                                            <span class="text-[8px] font-bold text-emerald-600/80">Lunas</span>
                                                        </div>
                                                    @elseif($bill->status === 'partial')
                                                        <label class="cursor-pointer py-2 px-1 rounded-xl border text-center transition-all block
                                                            {{ $isSelected ? 'bg-amber-500 text-white border-amber-500 shadow-xs' : 'bg-amber-500/10 border-amber-500/30 text-amber-700 dark:text-amber-300' }}">
                                                            <input type="checkbox" wire:model.live="selectedBillIds" value="{{ $bill->id }}" class="hidden">
                                                            <span class="block text-[8px] font-black uppercase">{{ $isSelected ? '✓ ' : '⚡ ' }}{{ $mLabel }}</span>
                                                            <span class="text-[8px] font-extrabold block truncate">Sisa {{ number_format(($bill->amount - $bill->amount_paid)/1000, 0) }}k</span>
                                                        </label>
                                                    @else
                                                        <label class="cursor-pointer py-2 px-1 rounded-xl border text-center transition-all block
                                                            {{ $isSelected ? 'bg-emerald-500 text-white border-emerald-500 shadow-xs' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:border-emerald-500/40' }}">
                                                            <input type="checkbox" wire:model.live="selectedBillIds" value="{{ $bill->id }}" class="hidden">
                                                            <span class="block text-[8px] font-black uppercase">{{ $isSelected ? '✓ ' : '' }}{{ $mLabel }}</span>
                                                            <span class="text-[8px] font-extrabold opacity-80 block truncate">{{ number_format($bill->amount/1000, 0) }}k</span>
                                                        </label>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- 2: TAGIHAN PERIODE (Semester / Caturwulan / Triwulan) --}}
                        @php $semesterBills = $this->semesterBills; @endphp
                        @if(!empty($semesterBills))
                            <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden space-y-3">
                                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                                    <h4 class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-widest">📅 Tagihan Periode (Semester / Caturwulan / Triwulan / Dwibulanan)</h4>
                                    <p class="text-[10px] text-slate-400">Iuran yang dibayarkan 2x, 3x, 4x, atau 6x dalam setahun</p>
                                </div>
                                <div class="p-5 space-y-4">
                                    @foreach($semesterBills as $configId => $configData)
                                        @php
                                            $maxP = $configData['max_period'] ?? 2;
                                            $titleType = $configData['type_title'] ?? 'Semester';
                                        @endphp
                                        <div class="p-4 bg-slate-50/70 dark:bg-slate-950/60 border border-slate-200/60 dark:border-slate-800 rounded-2xl space-y-2.5">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $configData['label'] }}</span>
                                                <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded bg-indigo-100 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300">
                                                    {{ $maxP }}x Dalam Setahun
                                                </span>
                                            </div>
                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                                @for($pNum = 1; $pNum <= $maxP; $pNum++)
                                                    @php $sBill = $configData['bills'][$pNum] ?? null; @endphp
                                                    <div class="p-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-center space-y-1">
                                                        <span class="block text-[9px] font-black uppercase text-slate-400 dark:text-slate-500">
                                                            {{ $titleType }} {{ $pNum }}
                                                        </span>
                                                        @if(!$sBill)
                                                            <span class="text-[10px] text-slate-400 dark:text-slate-600 italic block py-1">— Belum Terbit</span>
                                                        @elseif($sBill->status === 'paid')
                                                            <span class="inline-flex items-center justify-center gap-1 px-2.5 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-xl text-[10px] font-bold shadow-2xs">
                                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                                Lunas · Rp {{ number_format($sBill->amount, 0, ',', '.') }}
                                                            </span>
                                                        @elseif($sBill->status === 'partial')
                                                            <label class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl border cursor-pointer transition-all
                                                                {{ in_array($sBill->id, $selectedBillIds) ? 'bg-amber-100 border-amber-400 dark:bg-amber-950/40' : 'bg-amber-50/50 border-amber-200 dark:bg-amber-950/20 dark:border-amber-800 hover:border-amber-300' }}">
                                                                <input type="checkbox" wire:model.live="selectedBillIds" value="{{ $sBill->id }}" class="rounded text-amber-500 accent-amber-500 focus:ring-amber-400 w-3.5 h-3.5">
                                                                <span class="text-[10px] font-bold text-amber-700 dark:text-amber-300">
                                                                    Cicilan (Sisa Rp {{ number_format($sBill->amount - $sBill->amount_paid, 0, ',', '.') }})
                                                                </span>
                                                            </label>
                                                        @else
                                                            <label class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl border cursor-pointer transition-all
                                                                {{ in_array($sBill->id, $selectedBillIds) ? 'bg-emerald-50 border-emerald-400 dark:bg-emerald-950/20' : 'bg-slate-50 border-slate-200 dark:bg-slate-950 dark:border-slate-700 hover:border-emerald-300' }}">
                                                                <input type="checkbox" wire:model.live="selectedBillIds" value="{{ $sBill->id }}" class="rounded text-emerald-600 accent-emerald-600 focus:ring-emerald-500 w-3.5 h-3.5">
                                                                <span class="text-[10px] font-bold text-slate-700 dark:text-slate-300">
                                                                    Rp {{ number_format($sBill->amount - $sBill->amount_paid, 0, ',', '.') }}
                                                                </span>
                                                            </label>
                                                        @endif
                                                    </div>
                                                @endfor
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- 3: TAGIHAN INSIDENTAL / EVENT --}}
                        @php $insidentalBills = $this->insidentalBills; @endphp
                        @if(!$insidentalBills->isEmpty())
                            <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
                                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                                    <h4 class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-widest">⚡ Tagihan Khusus / Insidental</h4>
                                    <p class="text-[10px] text-slate-400">Iuran sekali bayar, event, atau tahunan</p>
                                </div>
                                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach($insidentalBills as $ib)
                                        <div class="flex items-center justify-between px-5 py-3.5 {{ $ib->status !== 'paid' ? 'hover:bg-slate-50 dark:hover:bg-slate-800/30' : '' }} transition-colors">
                                            <div class="flex items-center gap-3 min-w-0">
                                                @if($ib->status !== 'paid')
                                                    <input type="checkbox" wire:model.live="selectedBillIds" value="{{ $ib->id }}"
                                                        class="rounded {{ $ib->status === 'partial' ? 'text-amber-500 accent-amber-500 focus:ring-amber-400' : 'text-emerald-600 accent-emerald-600 focus:ring-emerald-500' }} w-4 h-4 shrink-0">
                                                @else
                                                    <span class="w-4 h-4 bg-emerald-500 rounded-full flex items-center justify-center shrink-0">
                                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    </span>
                                                @endif
                                                <div class="min-w-0">
                                                    <span class="font-semibold text-slate-700 dark:text-slate-300 text-xs block truncate">{{ $ib->config?->label ?? ($ib->title ?: str_replace('_', ' ', $ib->bill_type)) }}</span>
                                                    <span class="text-[9px] text-slate-400">{{ $monthNames[$ib->period_month] ?? '' }} {{ $ib->period_year }}</span>
                                                </div>
                                            </div>
                                            <div class="text-right shrink-0">
                                                @if($ib->status === 'paid')
                                                    <span class="text-[10px] font-bold text-emerald-600">✓ LUNAS</span>
                                                    <span class="block text-[9px] text-slate-400">Rp {{ number_format($ib->amount, 0, ',', '.') }}</span>
                                                @elseif($ib->status === 'partial')
                                                    <span class="text-[10px] font-bold text-amber-500">Sisa Rp {{ number_format($ib->amount - $ib->amount_paid, 0, ',', '.') }}</span>
                                                    <span class="block text-[9px] text-slate-400">dari Rp {{ number_format($ib->amount, 0, ',', '.') }}</span>
                                                @else
                                                    <span class="text-[10px] font-bold text-slate-700 dark:text-slate-300">Rp {{ number_format($ib->amount, 0, ',', '.') }}</span>
                                                    <span class="block text-[9px] text-emerald-600 font-semibold">Belum Dibayar</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- 4: PREPAID TAHUN DEPAN --}}
                        @if(!$this->paidFutureBills->isEmpty())
                            <div class="bg-emerald-500/5 border border-emerald-500/20 p-5 rounded-3xl space-y-3">
                                <div>
                                    <h4 class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">💚 Pembayaran di Muka</h4>
                                    <p class="text-[10px] text-slate-400">Tagihan tahun {{ $cashierYear + 1 }} ke atas yang sudah dilunasi.</p>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    @foreach($this->paidFutureBills as $fb)
                                        <div class="p-3 bg-white dark:bg-slate-950 border border-emerald-100 dark:border-emerald-900/30 rounded-2xl flex items-center justify-between text-xs">
                                            <div>
                                                <span class="font-bold text-slate-700 dark:text-slate-300 block text-[10px] truncate">{{ $fb->config?->label ?? str_replace('_', ' ', $fb->bill_type) }}</span>
                                                <span class="text-[9px] text-slate-400">{{ $monthNames[$fb->period_month] ?? '' }} {{ $fb->period_year }}</span>
                                            </div>
                                            <span class="w-5 h-5 bg-emerald-500 text-white rounded-full flex items-center justify-center shrink-0">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- ===== STICKY FLOATING MOBILE CHECKOUT BAR ===== --}}
            @if(!empty($selectedBillIds))
                <div class="fixed bottom-4 inset-x-4 z-40 md:hidden bg-slate-900/95 dark:bg-slate-950/95 backdrop-blur-md text-white p-3 rounded-2xl shadow-2xl border border-slate-700/80 flex items-center justify-between gap-3 animate-fade-in">
                    <div class="min-w-0">
                        <span class="text-[9px] font-extrabold uppercase text-emerald-400 tracking-wider block">🛒 {{ count($selectedBillIds) }} Tagihan Dipilih</span>
                        <span class="text-sm font-black text-white block truncate">Rp {{ number_format($this->selectedBillsTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" wire:click="$set('selectedBillIds', [])" class="px-2.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold transition-all flex items-center gap-1" title="Batal Pilihan">
                            🗑️ <span class="text-[10px]">Batal</span>
                        </button>
                        <button type="button" wire:click="initiatePayment"
                            class="px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-xs font-black rounded-xl shadow-lg shadow-emerald-500/30 flex items-center gap-1.5 shrink-0">
                            <span>PROSES BAYAR</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>
            @endif

            {{-- ===== CONFIRMATION MODAL ===== --}}
            @if($showPaymentConfirmModal && $selectedSantri)
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="confirm-modal-title">
                    {{-- Backdrop --}}
                    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" wire:click="cancelPayment"></div>

                    {{-- Modal Box --}}
                    <div class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                            <div>
                                <p class="text-[9px] font-extrabold text-emerald-500 uppercase tracking-widest mb-0.5">Konfirmasi Pembayaran</p>
                                <h3 id="confirm-modal-title" class="text-sm font-bold text-slate-900 dark:text-white">Apakah data pembayaran sudah benar?</h3>
                            </div>
                            <button type="button" wire:click="cancelPayment"
                                class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all font-bold text-lg leading-none">
                                &times;
                            </button>
                        </div>

                        {{-- Body --}}
                        <div class="p-6 space-y-4">
                            {{-- Santri Profile Summary --}}
                            <div class="p-4 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-100 dark:border-slate-850 flex items-center gap-3">
                                <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center font-extrabold text-white">
                                    {{ substr($selectedSantri->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <span class="font-bold text-slate-850 dark:text-slate-100 block text-xs truncate">{{ $selectedSantri->name }}</span>
                                    <span class="text-[10px] text-slate-400 block truncate">
                                        NIS: {{ $selectedSantri->nis ?? '—' }} | {{ $selectedSantri->gender === 'L' ? 'Putra' : 'Putri' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Payment details table --}}
                            <div class="space-y-2">
                                <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">Rincian Tagihan yang Dibayar:</span>
                                <div class="border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden max-h-40 overflow-y-auto">
                                    <table class="w-full text-xs text-left border-collapse">
                                        <thead class="bg-slate-50 dark:bg-slate-950">
                                            <tr class="border-b border-slate-100 dark:border-slate-800 text-[9px] font-extrabold text-slate-400 uppercase">
                                                <th class="py-2 px-3">Nama Tagihan</th>
                                                <th class="py-2 px-3 text-center">Periode</th>
                                                <th class="py-2 px-3 text-right">Sisa Tagihan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                            @foreach($this->confirmBills as $cb)
                                                <tr>
                                                    <td class="py-2 px-3 font-semibold text-slate-700 dark:text-slate-300">
                                                        {{ $cb->config?->label ?? str_replace('_', ' ', $cb->bill_type) }}
                                                    </td>
                                                    <td class="py-2 px-3 text-center text-slate-500">
                                                        {{ $monthNames[$cb->period_month] ?? '' }} {{ $cb->period_year }}
                                                    </td>
                                                    <td class="py-2 px-3 text-right font-bold text-slate-800 dark:text-slate-200">
                                                        Rp {{ number_format($cb->amount - $cb->amount_paid, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Math calculations summary --}}
                            <div class="p-4 bg-emerald-500/5 border border-emerald-500/10 rounded-2xl space-y-2">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-500 font-semibold">Total Tagihan Terpilih:</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">Rp {{ number_format($this->selectedBillsTotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-555 dark:text-slate-400 font-semibold">Uang Diterima:</span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-450">Rp {{ number_format($payAmount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-500 font-semibold">Tipe Pembayaran:</span>
                                    @if($payAmount < $this->selectedBillsTotal)
                                        <span class="font-extrabold text-amber-600 dark:text-amber-405 bg-amber-500/10 px-2 py-0.5 rounded-lg text-[9px] uppercase tracking-wider">
                                            ⚡ Cicilan / Sebagian
                                        </span>
                                    @else
                                        <span class="font-extrabold text-emerald-600 dark:text-emerald-450 bg-emerald-500/10 px-2 py-0.5 rounded-lg text-[9px] uppercase tracking-wider">
                                            ✓ Pelunasan Lunas
                                        </span>
                                    @endif
                                </div>
                                <div class="flex justify-between items-center text-[10px] border-t border-emerald-500/10 pt-2">
                                    <span class="text-slate-400 font-semibold">Metode Setoran:</span>
                                    <span class="font-bold text-slate-650 dark:text-slate-350">
                                        {{ $payMethod === 'CASH' ? '💵 Tunai (Cash)' : ($payMethod === 'TRANSFER' ? '🏦 Transfer Bank' : '📱 E-Wallet') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Footer buttons --}}
                        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                            <button type="button" wire:click="cancelPayment"
                                class="px-5 py-2.5 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all">
                                Batalkan
                            </button>
                            <button type="button" wire:click="recordPayment"
                                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-850 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Ya, Konfirmasi & Simpan
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @endif


        <!-- TAB 4: DISPENSASI & POTONGAN -->
        @if ($activeTab === 'exceptions')
            <div class="space-y-8">
                <!-- Exception List -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 rounded-3xl shadow-sm overflow-hidden space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider block font-serif-display">Kelompok Dispensasi & Keringanan Aktif</h3>
                            <p class="text-[11px] text-slate-400">Semua kelompok potongan iuran yang telah terdaftar untuk santri.</p>
                        </div>
                        <a href="{{ route('keuangan.billing.exceptions.create') }}" wire:navigate class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-1.5 self-start sm:self-auto">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            Tambah Dispensasi Baru
                        </a>
                    </div>

                    <!-- Filter Controls for Exceptions -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50/60 dark:bg-slate-950/40 p-4 rounded-2xl border border-slate-200/60 dark:border-slate-800">
                        <!-- Search Box -->
                        <div>
                            <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Cari Dispensasi / Tagihan / Santri</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>
                                <input type="text" wire:model.live.debounce.300ms="exceptionSearch" placeholder="Cari nama/alasan potongan, nama iuran, atau nama santri..." 
                                    class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl pl-9 pr-4 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                            </div>
                        </div>

                        <!-- Type Filter -->
                        <div>
                            <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Tipe Keringanan</label>
                            <select wire:model.live="exceptionTypeFilter" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-4 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                                <option value="">-- Semua Tipe Keringanan --</option>
                                <option value="discount">🏷️ Potongan (Discount)</option>
                                <option value="waived">🎉 Bebas Biaya (Waived / Gratis)</option>
                                <option value="custom">⚙️ Tarif Khusus (Custom Amount)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">
                                    <th class="py-3 px-4">Nama/Alasan Potongan</th>
                                    <th class="py-3 px-4">Nama Tagihan</th>
                                    <th class="py-3 px-4 text-center">Jumlah Santri</th>
                                    <th class="py-3 px-4">Tipe Keringanan</th>
                                    <th class="py-3 px-4 text-right">Nominal / Potongan</th>
                                    <th class="py-3 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                @php
                                    $groupedExceptions = $exceptions->groupBy(function($exc) {
                                        return $exc->billing_config_id . '-' . $exc->exception_type . '-' . $exc->amount . '-' . $exc->notes;
                                    });
                                @endphp
                                @forelse($groupedExceptions as $groupKey => $group)
                                    @php
                                        $first = $group->first();
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                        <td class="py-3 px-4 font-bold text-slate-800 dark:text-slate-200">{{ $first->notes ?: 'Tanpa Alasan/Keterangan' }}</td>
                                        <td class="py-3 px-4 font-semibold text-slate-600 dark:text-slate-400">{{ $first->configuration->label ?? 'Iuran Terhapus' }}</td>
                                        <td class="py-3 px-4 text-center">
                                            <button type="button" wire:click="showGroupMembers('{{ $first->billing_config_id }}', '{{ $first->exception_type }}', {{ $first->amount }}, '{{ addslashes($first->notes) }}')" 
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-500/10 hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 rounded-xl font-bold transition-all">
                                                👤 {{ count($group) }} Santri
                                            </button>
                                        </td>
                                        <td class="py-3 px-4 uppercase text-[9px] font-bold text-slate-500">
                                            @if($first->exception_type === 'discount')
                                                Potongan
                                            @elseif($first->exception_type === 'waived')
                                                Bebas Biaya
                                            @else
                                                Tarif Khusus
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-right font-semibold text-slate-800 dark:text-slate-200">
                                            @if($first->exception_type === 'waived')
                                                Rp 0 (Gratis)
                                            @else
                                                Rp {{ number_format($first->amount, 0, ',', '.') }}
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('keuangan.billing.exceptions.create', [
                                                    'copy_config_id' => $first->billing_config_id,
                                                    'copy_type' => $first->exception_type,
                                                    'copy_amount' => $first->amount,
                                                    'copy_notes' => $first->notes
                                                ]) }}" wire:navigate class="text-xs font-bold text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300 transition-colors flex items-center gap-1" title="Salin daftar santri penerima ke tagihan/iuran lain">
                                                    📋 Salin
                                                </a>
                                                <span class="text-slate-350 dark:text-slate-700">|</span>
                                                <a href="{{ route('keuangan.billing.exceptions.edit', [
                                                    'config_id' => $first->billing_config_id,
                                                    'type' => $first->exception_type,
                                                    'amount' => $first->amount,
                                                    'notes' => $first->notes
                                                ]) }}" wire:navigate class="text-xs font-bold text-blue-600 hover:text-blue-750 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                                    Edit
                                                </a>
                                                <span class="text-slate-350 dark:text-slate-700">|</span>
                                                <button wire:click="deleteGroup('{{ $first->billing_config_id }}', '{{ $first->exception_type }}', {{ $first->amount }}, '{{ addslashes($first->notes) }}')" 
                                                    wire:confirm="Apakah Anda yakin ingin menghapus kelompok dispensasi ini? Seluruh santri anggota kelompok ini akan dikembalikan ke tarif normal."
                                                    class="text-xs font-bold text-rose-600 hover:text-rose-700 transition-colors">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-slate-400 font-medium">Belum ada kelompok dispensasi yang didaftarkan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Members Detail Modal -->
            @if($showMembersModal)
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="modal-title">
                    {{-- Backdrop --}}
                    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" wire:click="closeMembersModal"></div>

                    {{-- Modal Box --}}
                    <div class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                            <div>
                                <p class="text-[9px] font-extrabold text-emerald-500 uppercase tracking-widest mb-0.5">Detail Kelompok Dispensasi</p>
                                <h3 id="modal-title" class="text-sm font-bold text-slate-900 dark:text-white">{{ $modalGroupName }}</h3>
                            </div>
                            <button type="button" wire:click="closeMembersModal"
                                class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all font-bold text-lg leading-none">
                                &times;
                            </button>
                        </div>

                        {{-- Body --}}
                        <div class="max-h-80 overflow-y-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead class="sticky top-0 bg-slate-50 dark:bg-slate-950 z-10">
                                    <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">
                                        <th class="py-2.5 px-4 w-10">No</th>
                                        <th class="py-2.5 px-4">Nama Santri</th>
                                        <th class="py-2.5 px-4 text-center">Gender</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                    @foreach($modalMembers as $idx => $m)
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                            <td class="py-2.5 px-4 font-semibold text-slate-400">{{ $idx + 1 }}</td>
                                            <td class="py-2.5 px-4 font-bold text-slate-800 dark:text-slate-200">{{ $m['name'] }}</td>
                                            <td class="py-2.5 px-4 text-center">
                                                <span class="inline-block px-2 py-0.5 rounded-lg text-[9px] font-extrabold uppercase tracking-wider
                                                    {{ $m['gender'] === 'L' ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400' : 'bg-pink-500/10 text-pink-600 dark:text-pink-400' }}">
                                                    {{ $m['gender'] === 'L' ? 'Putra' : 'Putri' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Footer --}}
                        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <span class="text-[10px] text-slate-400 font-semibold">Total: {{ count($modalMembers) }} santri</span>
                            <button type="button" wire:click="closeMembersModal"
                                class="px-5 py-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold transition-all">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- TAB 5: CICILAN EVENT -->
        @if ($activeTab === 'installments')
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Add Installment Form -->
                <div class="lg:col-span-4 bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 rounded-3xl shadow-sm space-y-6 h-fit">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider block font-serif-display">Buat Skema Cicilan</h3>
                        <p class="text-[10px] text-slate-400">Pecah tagihan besar menjadi beberapa termin pembayaran dengan jatuh tempo berbeda.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Cari Santri</label>
                            <input type="text" wire:model.live="instSearchQuery" placeholder="Cari santri..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-emerald-500">

                            @if(!empty($instSearchResults))
                                <div class="bg-white dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 rounded-2xl overflow-hidden divide-y divide-slate-100 dark:divide-slate-800/50 mt-2">
                                    @foreach($instSearchResults as $s)
                                        <button wire:click="selectInstSantri('{{ $s->id }}')" class="w-full text-left px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-900 transition-all flex items-center justify-between text-xs">
                                            <div>
                                                <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ $s->name }}</span>
                                                <span class="text-[10px] text-slate-400 uppercase tracking-wider">{{ $s->gender === 'L' ? 'PUTRA' : 'PUTRI' }}</span>
                                            </div>
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Pilih Jenis Iuran / Event</label>
                            <select wire:model.live="instConfigId" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-emerald-500">
                                <option value="">-- Pilih Konfigurasi --</option>
                                @foreach($installmentConfigs as $c)
                                    <option value="{{ $c->id }}">{{ $c->label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Total Biaya (Rp)</label>
                            <input type="number" wire:model="instTotalAmount" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-emerald-500 text-right font-bold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Jumlah Termin Cicilan</label>
                            <select wire:model="instTermCount" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2 text-xs focus:ring-emerald-500 font-bold">
                                <option value="2">2 Kali Cicilan</option>
                                <option value="3">3 Kali Cicilan</option>
                                <option value="4">4 Kali Cicilan</option>
                                <option value="5">5 Kali Cicilan</option>
                                <option value="6">6 Kali Cicilan</option>
                                <option value="12">12 Kali Cicilan</option>
                            </select>
                        </div>

                        <button wire:click="generateInstallments" class="w-full px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                            Generate Cicilan
                        </button>
                    </div>
                </div>

                <!-- Active Installment Plans Dashboard -->
                <div class="lg:col-span-8 bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 rounded-3xl shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider block font-serif-display">Daftar Skema Cicilan Aktif</h3>
                            <p class="text-[11px] text-slate-400">Pantau progres cicilan event dan pembayaran bertermin santri.</p>
                        </div>
                        <div class="w-full sm:w-64">
                            <input type="text" wire:model.live="instFilterSearch" placeholder="Cari nama santri..." 
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-1.5 text-xs focus:ring-emerald-500">
                        </div>
                    </div>

                    @if($installmentPlans->isEmpty())
                        <div class="py-16 text-center text-slate-400 text-xs font-semibold">
                            Tidak ada skema cicilan aktif yang ditemukan.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">
                                        <th class="py-2.5 px-3">Santri</th>
                                        <th class="py-2.5 px-3">Event / Iuran</th>
                                        <th class="py-2.5 px-3 text-center">Skema</th>
                                        <th class="py-2.5 px-3 text-right">Total</th>
                                        <th class="py-2.5 px-3 text-right">Terbayar</th>
                                        <th class="py-2.5 px-3 text-center">Status</th>
                                        <th class="py-2.5 px-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                    @foreach($installmentPlans as $plan)
                                        @php
                                            $dormName = $plan->person->roomAssignments->firstWhere('is_active', true)?->room?->dormitory?->name ?? '—';
                                            $paidCount = $plan->installments->where('status', 'paid')->count();
                                            $totalTerms = $plan->installments->count();
                                        @endphp
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                            <td class="py-3 px-3">
                                                <strong class="text-slate-800 dark:text-slate-200 block">{{ $plan->person->name }}</strong>
                                                <span class="text-[9px] text-slate-400 block truncate">Komplek: {{ $dormName }}</span>
                                            </td>
                                            <td class="py-3 px-3 font-semibold text-slate-600 dark:text-slate-400">
                                                {{ $plan->config->label ?? 'Iuran Terhapus' }}
                                            </td>
                                            <td class="py-3 px-3 text-center">
                                                <span class="inline-block px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-350 text-[10px] font-bold rounded-lg">
                                                    {{ $totalTerms }}x ({{ $paidCount }} Lunas)
                                                </span>
                                            </td>
                                            <td class="py-3 px-3 text-right font-bold text-slate-800 dark:text-slate-200">
                                                Rp {{ number_format($plan->amount, 0, ',', '.') }}
                                            </td>
                                            <td class="py-3 px-3 text-right font-bold text-emerald-650 dark:text-emerald-400">
                                                Rp {{ number_format($plan->amount_paid, 0, ',', '.') }}
                                            </td>
                                            <td class="py-3 px-3 text-center">
                                                <span class="inline-block px-2 py-0.5 rounded-lg text-[9px] font-extrabold uppercase tracking-wider
                                                    @if($plan->status === 'paid') bg-emerald-500/10 text-emerald-600 dark:text-emerald-400
                                                    @elseif($plan->status === 'partial') bg-amber-500/10 text-amber-600 dark:text-amber-400
                                                    @else bg-slate-500/10 text-slate-500 dark:text-slate-400 @endif">
                                                    @if($plan->status === 'paid') Lunas
                                                    @elseif($plan->status === 'partial') Sebagian
                                                    @else Belum Bayar @endif
                                                </span>
                                            </td>
                                            <td class="py-3 px-3 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button type="button" wire:click="showInstallmentDetails('{{ $plan->id }}')" 
                                                        class="px-2.5 py-1 bg-blue-500/10 hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 text-[10px] font-extrabold rounded-lg transition-all">
                                                        Detail
                                                    </button>
                                                    <button type="button" wire:click="cancelInstallmentPlan('{{ $plan->id }}')" 
                                                        wire:confirm="Apakah Anda yakin ingin membatalkan skema cicilan ini? Seluruh tagihan termin terkait yang belum dibayar akan dihapus!"
                                                        class="px-2.5 py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-450 text-[10px] font-extrabold rounded-lg transition-all">
                                                        Batal
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Installment Details Modal -->
                @if($showInstallmentDetailsModal && $selectedParentBill)
                    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="inst-modal-title">
                        {{-- Backdrop --}}
                        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" wire:click="closeInstallmentDetailsModal"></div>

                        {{-- Modal Box --}}
                        <div class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                            {{-- Header --}}
                            <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                                <div>
                                    <p class="text-[9px] font-extrabold text-emerald-500 uppercase tracking-widest mb-0.5">Detail Rincian Cicilan</p>
                                    <h3 id="inst-modal-title" class="text-sm font-bold text-slate-900 dark:text-white">{{ $selectedParentBill->person->name }}</h3>
                                </div>
                                <button type="button" wire:click="closeInstallmentDetailsModal"
                                    class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all font-bold text-lg leading-none">
                                    &times;
                                </button>
                            </div>

                            {{-- Summary Card inside Modal --}}
                            <div class="p-6 bg-slate-50 dark:bg-slate-950 border-b border-slate-100 dark:border-slate-800 grid grid-cols-3 gap-4 text-center text-xs">
                                <div>
                                    <span class="text-[9px] text-slate-400 font-extrabold uppercase tracking-wider block mb-1">Total Biaya</span>
                                    <strong class="text-slate-800 dark:text-slate-200 font-bold">Rp {{ number_format($selectedParentBill->amount, 0, ',', '.') }}</strong>
                                </div>
                                <div>
                                    <span class="text-[9px] text-emerald-500 font-extrabold uppercase tracking-wider block mb-1">Terbayar</span>
                                    <strong class="text-emerald-600 dark:text-emerald-400 font-bold">Rp {{ number_format($selectedParentBill->amount_paid, 0, ',', '.') }}</strong>
                                </div>
                                <div>
                                    <span class="text-[9px] text-rose-500 font-extrabold uppercase tracking-wider block mb-1">Sisa</span>
                                    <strong class="text-rose-600 dark:text-rose-450 font-bold">Rp {{ number_format($selectedParentBill->amount - $selectedParentBill->amount_paid, 0, ',', '.') }}</strong>
                                </div>
                            </div>

                            {{-- Body Table --}}
                            <div class="max-h-80 overflow-y-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead class="sticky top-0 bg-slate-100 dark:bg-slate-950 z-10">
                                        <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">
                                            <th class="py-2.5 px-4 w-12 text-center">No</th>
                                            <th class="py-2.5 px-4">Termin / Jatuh Tempo</th>
                                            <th class="py-2.5 px-4 text-right">Nominal</th>
                                            <th class="py-2.5 px-4 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                        @foreach($installmentChildBills as $idx => $child)
                                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                                <td class="py-2.5 px-4 text-center font-semibold text-slate-400">{{ $idx + 1 }}</td>
                                                <td class="py-2.5 px-4">
                                                    <strong class="text-slate-800 dark:text-slate-200 block">{{ $child->notes }}</strong>
                                                    <span class="text-[9px] text-slate-400 block mt-0.5">Jatuh Tempo: {{ $child->due_date ? $child->due_date->format('d-m-Y') : '—' }}</span>
                                                </td>
                                                <td class="py-2.5 px-4 text-right font-bold text-slate-800 dark:text-slate-200">
                                                    Rp {{ number_format($child->amount, 0, ',', '.') }}
                                                </td>
                                                <td class="py-2.5 px-4 text-center">
                                                    <span class="inline-block px-2 py-0.5 rounded-lg text-[9px] font-extrabold uppercase tracking-wider
                                                        @if($child->status === 'paid') bg-emerald-500/10 text-emerald-600 dark:text-emerald-400
                                                        @elseif($child->status === 'partial') bg-amber-500/10 text-amber-600 dark:text-amber-400
                                                        @else bg-slate-500/10 text-slate-500 dark:text-slate-400 @endif">
                                                        @if($child->status === 'paid') Lunas
                                                        @elseif($child->status === 'partial') Sebagian
                                                        @else Belum @endif
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Footer --}}
                            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <span class="text-[10px] text-slate-400 font-semibold">Iuran: {{ $selectedParentBill->config->label ?? '—' }}</span>
                                <button type="button" wire:click="closeInstallmentDetailsModal"
                                    class="px-5 py-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold transition-all">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- TAB 6: RIWAYAT SETORAN (LOG PEMBAYARAN KASIR) -->
        @if ($activeTab === 'payments_log')
            <div class="space-y-8 animate-fade-in">
                <!-- Real-time KPI Summary Cards for Filtered Results -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gradient-to-br from-emerald-500/10 via-emerald-500/5 to-transparent border border-emerald-500/20 dark:border-emerald-500/30 p-4 rounded-2xl flex items-center gap-3.5 shadow-2xs">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg font-bold shrink-0">
                            💵
                        </div>
                        <div>
                            <span class="block text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Tunai (Cash)</span>
                            <span class="text-base font-extrabold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($payLogTotalCash, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-blue-500/10 via-blue-500/5 to-transparent border border-blue-500/20 dark:border-blue-500/30 p-4 rounded-2xl flex items-center gap-3.5 shadow-2xs">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg font-bold shrink-0">
                            🏦
                        </div>
                        <div>
                            <span class="block text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Transfer Bank</span>
                            <span class="text-base font-extrabold text-blue-600 dark:text-blue-400">Rp {{ number_format($payLogTotalTransfer, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent border border-amber-500/20 dark:border-amber-500/30 p-4 rounded-2xl flex items-center gap-3.5 shadow-2xs">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg font-bold shrink-0">
                            ⚡
                        </div>
                        <div>
                            <span class="block text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Via Gateway Duitku</span>
                            <span class="text-base font-extrabold text-amber-600 dark:text-amber-400">Rp {{ number_format($payLogTotalGateway, 0, ',', '.') }}</span>
                            <span class="text-[9px] text-slate-400">{{ $payLogGatewayCount }} transaksi online</span>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-indigo-500/10 via-indigo-500/5 to-transparent border border-indigo-500/20 dark:border-indigo-500/30 p-4 rounded-2xl flex items-center gap-3.5 shadow-2xs">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg font-bold shrink-0">
                            🧾
                        </div>
                        <div>
                            <span class="block text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Transaksi Terfilter</span>
                            <span class="text-base font-extrabold text-indigo-600 dark:text-indigo-400">{{ $payLogTotalCount }} <span class="text-xs font-semibold text-slate-500">Transaksi</span></span>
                        </div>
                    </div>
                </div>

                <!-- Filters & Control Panel Card -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 rounded-3xl shadow-xs space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider block font-serif-display">Riwayat Setoran & Transaksi Kasir</h3>
                            <p class="text-[11px] text-slate-400">Jejak pembayaran iuran santri yang dicatat oleh kasir. Anda dapat melakukan pembatalan pencatatan/void pembayaran jika terjadi kesalahan.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="togglePayLogAdvancedFilters"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-all">
                                🎛️ {{ $showPayLogAdvancedFilters ? 'Sembunyikan Filter' : 'Filter Lanjutan' }}
                            </button>
                            @if($payLogSearch || $payLogMethod || $payLogDate || $payLogStartDate || $payLogEndDate || $payLogUser || $payLogConfigId || $payLogDormitoryId || $payLogKelasId)
                                <button type="button" wire:click="resetPayLogFilters"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-500/10 hover:bg-rose-500 text-rose-600 hover:text-white rounded-xl text-xs font-bold transition-all">
                                    ❌ Reset Filter
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Main Search & Primary Filters Row -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <!-- Search Box (Santri / NIS / NIK) -->
                        <div class="md:col-span-5">
                            <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Cari Santri (Nama / NIS / NIK)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>
                                <input type="text" wire:model.live.debounce.300ms="payLogSearch" placeholder="Ketik nama santri, NIS, NIK, atau catatan..." 
                                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-2xl pl-10 pr-4 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                            </div>
                        </div>

                        <!-- Date Range (Mulai s.d Selesai) -->
                        <div class="md:col-span-4">
                            <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Rentang Tanggal Setor</label>
                            <div class="flex items-center gap-1.5">
                                <input type="date" wire:model.live="payLogStartDate" title="Tanggal Mulai" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-2xl px-2.5 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                                <span class="text-slate-400 text-xs font-bold">s/d</span>
                                <input type="date" wire:model.live="payLogEndDate" title="Tanggal Selesai" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-2xl px-2.5 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                            </div>
                        </div>

                        <!-- Cashier User Select -->
                        <div class="md:col-span-3">
                            <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Petugas Kasir</label>
                            <select wire:model.live="payLogUser" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-2xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                                <option value="">-- Semua Petugas --</option>
                                @foreach($cashierUsers as $u)
                                    <option value="{{ $u->id }}">👤 {{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Date Presets Shortcuts -->
                    <div class="flex items-center gap-1.5 flex-wrap text-[10px] font-extrabold pt-1">
                        <span class="text-slate-400 mr-1">Preset Tanggal:</span>
                        <button type="button" wire:click="setPayLogDatePreset('today')" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-emerald-500/10 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-lg text-slate-600 dark:text-slate-300 transition-all">Hari Ini</button>
                        <button type="button" wire:click="setPayLogDatePreset('yesterday')" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-emerald-500/10 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-lg text-slate-600 dark:text-slate-300 transition-all">Kemarin</button>
                        <button type="button" wire:click="setPayLogDatePreset('7days')" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-emerald-500/10 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-lg text-slate-600 dark:text-slate-300 transition-all">7 Hari Terakhir</button>
                        <button type="button" wire:click="setPayLogDatePreset('this_month')" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-emerald-500/10 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-lg text-slate-600 dark:text-slate-300 transition-all">Bulan Ini</button>
                        <button type="button" wire:click="setPayLogDatePreset('last_month')" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-emerald-500/10 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-lg text-slate-600 dark:text-slate-300 transition-all">Bulan Lalu</button>
                        @if($payLogStartDate || $payLogEndDate)
                            <button type="button" wire:click="setPayLogDatePreset('clear')" class="px-2.5 py-1 bg-rose-500/10 text-rose-600 rounded-lg transition-all">Clear Tanggal</button>
                        @endif
                    </div>

                    <!-- Advanced Filters Panel (Expandable) -->
                    @if($showPayLogAdvancedFilters)
                        <div class="pt-4 border-t border-dashed border-slate-200 dark:border-slate-800 grid grid-cols-1 sm:grid-cols-4 gap-4 animate-fade-in bg-slate-50/50 dark:bg-slate-950/30 p-4 rounded-2xl">
                            <!-- Filter Jenis Iuran -->
                            <div>
                                <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Jenis Iuran / Config</label>
                                <select wire:model.live="payLogConfigId" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                                    <option value="">-- Semua Jenis Iuran --</option>
                                    @foreach($payLogConfigs as $cfg)
                                        <option value="{{ $cfg->id }}">🏷️ {{ $cfg->label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Komplek Asrama -->
                            <div>
                                <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Komplek Asrama</label>
                                <select wire:model.live="payLogDormitoryId" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                                    <option value="">-- Semua Komplek --</option>
                                    @foreach($payLogDormitories as $dorm)
                                        <option value="{{ $dorm->id }}">🏠 {{ $dorm->name }} ({{ $dorm->gender === 'L' ? 'Putra' : 'Putri' }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Kelas Madrasah -->
                            <div>
                                <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Kelas Madrasah</label>
                                <select wire:model.live="payLogKelasId" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                                    <option value="">-- Semua Kelas --</option>
                                    @foreach($payLogClasses as $kls)
                                        <option value="{{ $kls->id }}">🏫 {{ $kls->name }} ({{ $kls->academic_year }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Metode Setoran -->
                            <div>
                                <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Metode Setoran</label>
                                <select wire:model.live="payLogMethod" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                                    <option value="">-- Semua Metode --</option>
                                    <option value="cash">💵 Tunai (Cash)</option>
                                    <option value="transfer">🏦 Transfer Bank</option>
                                    <option value="gateway_duitku">⚡ Gateway Duitku (QRIS/VA)</option>
                                </select>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Payment Logs Table -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-3xl shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs min-w-[900px]">
                            <thead>
                                <tr class="bg-slate-50/80 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">
                                    <th class="py-4 px-4 w-32">Tanggal Setor</th>
                                    <th class="py-4 px-4">Nama Santri</th>
                                    <th class="py-4 px-4">Jenis Iuran</th>
                                    <th class="py-4 px-4 text-center">Periode</th>
                                    <th class="py-4 px-4 text-right">Jumlah Setor</th>
                                    <th class="py-4 px-4 text-center">Metode</th>
                                    <th class="py-4 px-4">Catatan</th>
                                    <th class="py-4 px-4">Petugas</th>
                                    <th class="py-4 px-4 text-center w-28">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                @forelse($paymentsLog as $pay)
                                    @php
                                        $bill = $pay->bill;
                                        $santri = $bill?->person;
                                        $config = $bill?->config;
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                        <td class="py-4 px-4 font-medium text-slate-500">
                                            {{ $pay->payment_date ? $pay->payment_date->translatedFormat('d M Y') : '—' }}
                                            <span class="text-[9px] text-slate-400 block mt-0.5">{{ $pay->created_at->format('H:i') }} WIB</span>
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($santri)
                                                <strong class="text-slate-800 dark:text-slate-200 block font-bold">{{ $santri->name }}</strong>
                                                <span class="text-[9px] text-slate-400 block mt-0.5">
                                                    NIS: {{ $santri->nis ?? '—' }} &nbsp;|&nbsp;
                                                    {{ $santri->gender === 'L' ? '👦 L' : '👧 P' }}
                                                </span>
                                            @else
                                                <span class="text-slate-400 italic">Data Terhapus</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 font-semibold text-slate-700 dark:text-slate-300">
                                            {{ $config?->label ?? ($bill?->bill_type ? str_replace('_', ' ', $bill->bill_type) : '—') }}
                                        </td>
                                        <td class="py-4 px-4 text-center font-bold text-slate-600 dark:text-slate-350">
                                            @if($bill)
                                                @if($config && $config->interval === 'semester')
                                                    Sem {{ $bill->period_month }} / {{ $bill->period_year }}
                                                @elseif($config && in_array($config->interval, ['once', 'insidental', 'event', 'sekali']))
                                                    Event / {{ $bill->period_year }}
                                                @else
                                                    @php
                                                        $months = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agt',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
                                                        $mName = $months[$bill->period_month] ?? $bill->period_month;
                                                    @endphp
                                                    {{ $mName }} {{ $bill->period_year }} @if($bill->period_sub) <span class="text-[10px] font-semibold text-amber-600 dark:text-amber-400 block">(Gel. {{ $bill->period_sub }})</span> @endif
                                                @endif
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-right font-extrabold text-emerald-600 dark:text-emerald-400">
                                            Rp {{ number_format($pay->amount_paid, 0, ',', '.') }}
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @php $method = strtolower($pay->payment_method); @endphp
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[9px] font-extrabold uppercase tracking-wider
                                                @if($method === 'cash') bg-teal-500/10 text-teal-600 dark:text-teal-400
                                                @elseif($method === 'transfer') bg-blue-500/10 text-blue-600 dark:text-blue-400
                                                @elseif($method === 'gateway_duitku') bg-amber-500/10 text-amber-600 dark:text-amber-400
                                                @else bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 @endif">
                                                @if($method === 'cash') 💵 Tunai
                                                @elseif($method === 'transfer') 🏦 Transfer
                                                @elseif($method === 'gateway_duitku') ⚡ Duitku Online
                                                @else 💳 {{ $pay->payment_method }} @endif
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 max-w-[180px]">
                                            @if($method === 'gateway_duitku')
                                                @php
                                                    // Ekstrak nomor referensi dari catatan
                                                    preg_match('/Ref transaksi:\s*(\S+)/', $pay->notes ?? '', $refMatch);
                                                    $refCode = $refMatch[1] ?? null;
                                                @endphp
                                                <span class="text-[10px] text-slate-500 dark:text-slate-400 block">Bayar mandiri via Duitku</span>
                                                @if($refCode)
                                                    <code class="text-[9px] font-mono bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 px-1.5 py-0.5 rounded mt-0.5 block truncate" title="{{ $refCode }}">{{ $refCode }}</code>
                                                @endif
                                            @else
                                                <span class="text-slate-500 dark:text-slate-400 text-[11px] truncate block" title="{{ $pay->notes }}">{{ $pay->notes ?: '—' }}</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md text-[10px] text-slate-700 dark:text-slate-300 font-bold whitespace-nowrap">
                                                👤 {{ $pay->logger?->name ?? 'Sistem' }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <div class="flex items-center justify-center gap-1.5">
                                                {{-- ⬇ PDF Download --}}
                                                @if($method === 'gateway_duitku')
                                                    {{-- Gateway: cari PaymentTransaction via reference di notes --}}
                                                @else
                                                    <a href="{{ route('bukti-bayar.kasir', $pay->id) }}" target="_blank"
                                                       class="inline-flex items-center gap-1 px-2 py-1.5 bg-indigo-500/10 hover:bg-indigo-500 text-indigo-600 hover:text-white rounded-xl text-[9px] font-bold transition-all whitespace-nowrap"
                                                       title="Unduh PDF Bukti Bayar">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        PDF
                                                    </a>
                                                @endif

                                                {{-- 🗑 Void Button --}}
                                                @if($method === 'gateway_duitku')
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 rounded-xl text-[9px] font-bold whitespace-nowrap cursor-not-allowed" title="Transaksi online tidak dapat di-void. Hubungi Duitku jika diperlukan.">
                                                        🔒 Tidak Bisa Void
                                                    </span>
                                                @else
                                                    <button type="button" wire:click="confirmVoidPayment('{{ $pay->id }}')"
                                                        class="px-2.5 py-1.5 bg-rose-500/10 hover:bg-rose-500 text-rose-600 hover:text-white rounded-xl text-[9px] font-bold transition-all whitespace-nowrap">
                                                        🗑️ Void
                                                    </button>
                                                @endif
                                            </div>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="py-16 text-center text-slate-400 font-semibold">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                                <span>Tidak ada riwayat setoran yang cocok dengan pencarian Anda.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Footer for Payments Log -->
                    @if($paymentsLog->total() > 0)
                        <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/50 dark:bg-slate-950/20">
                            <div class="text-[11px] font-semibold text-slate-400">
                                Menampilkan <span class="font-bold text-slate-700 dark:text-slate-300">{{ $paymentsLog->firstItem() ?? 0 }}</span> s.d. <span class="font-bold text-slate-700 dark:text-slate-300">{{ $paymentsLog->lastItem() ?? 0 }}</span> dari <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $paymentsLog->total() }}</span> pembayaran dicatat
                            </div>
                            @if($paymentsLog->hasPages())
                                <div>
                                    {{ $paymentsLog->links(data: ['scrollTo' => false]) }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- TAB: TRANSAKSI GATEWAY DUITKU --}}
        @if ($activeTab === 'gateway_transactions')
            <div class="space-y-6 animate-fade-in">

                {{-- KPI Cards --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gradient-to-br from-emerald-500/10 to-transparent border border-emerald-500/20 dark:border-emerald-500/30 p-4 rounded-2xl flex items-center gap-3 shadow-2xs">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-600 flex items-center justify-center text-base shrink-0">✅</div>
                        <div>
                            <span class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider">Sukses</span>
                            <span class="text-sm font-extrabold text-emerald-600 dark:text-emerald-400">{{ $gatewayStats['success_count'] ?? 0 }}</span>
                            <span class="text-[9px] text-slate-400 block">Rp {{ number_format($gatewayStats['success_amount'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-amber-500/10 to-transparent border border-amber-500/20 dark:border-amber-500/30 p-4 rounded-2xl flex items-center gap-3 shadow-2xs">
                        <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-600 flex items-center justify-center text-base shrink-0">⏳</div>
                        <div>
                            <span class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider">Menunggu</span>
                            <span class="text-sm font-extrabold text-amber-600 dark:text-amber-400">{{ $gatewayStats['pending_count'] ?? 0 }}</span>
                            <span class="text-[9px] text-slate-400 block">transaksi aktif</span>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-rose-500/10 to-transparent border border-rose-500/20 dark:border-rose-500/30 p-4 rounded-2xl flex items-center gap-3 shadow-2xs">
                        <div class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-600 flex items-center justify-center text-base shrink-0">❌</div>
                        <div>
                            <span class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider">Gagal / Expired</span>
                            <span class="text-sm font-extrabold text-rose-600 dark:text-rose-400">{{ $gatewayStats['failed_count'] ?? 0 }}</span>
                            <span class="text-[9px] text-slate-400 block">tidak dibayar</span>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-indigo-500/10 to-transparent border border-indigo-500/20 dark:border-indigo-500/30 p-4 rounded-2xl flex items-center gap-3 shadow-2xs">
                        <div class="w-9 h-9 rounded-xl bg-indigo-500/20 text-indigo-600 flex items-center justify-center text-base shrink-0">💸</div>
                        <div>
                            <span class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider">Total MDR Dibayar</span>
                            <span class="text-sm font-extrabold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($gatewayStats['total_mdr'] ?? 0, 0, ',', '.') }}</span>
                            <span class="text-[9px] text-slate-400 block">biaya gateway</span>
                        </div>
                    </div>
                </div>

                {{-- Table --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-3xl shadow-xs overflow-hidden">

                    {{-- Filter Bar --}}
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 space-y-3">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-3">
                            {{-- Search --}}
                            <div class="relative flex-1 min-w-0">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <input wire:model.live.debounce.300ms="gatewaySearch" type="text" placeholder="Cari nama santri, no. order, atau ref Duitku…" class="w-full pl-8 pr-3 py-2 text-xs bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:ring-1 focus:ring-amber-400 focus:border-amber-400 outline-none transition"/>
                            </div>
                            {{-- Status --}}
                            <select wire:model.live="gatewayStatus" class="px-3 py-2 text-xs bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-600 dark:text-slate-300 focus:ring-1 focus:ring-amber-400 outline-none transition shrink-0">
                                <option value="">Semua Status</option>
                                <option value="success">✅ Sukses</option>
                                <option value="pending">⏳ Menunggu</option>
                                <option value="failed">❌ Gagal</option>
                                <option value="expired">🕐 Expired</option>
                            </select>
                            {{-- Channel --}}
                            <select wire:model.live="gatewayChannel" class="px-3 py-2 text-xs bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-600 dark:text-slate-300 focus:ring-1 focus:ring-amber-400 outline-none transition shrink-0">
                                <option value="">Semua Channel</option>
                                @foreach(config('duitku.payment_channels', []) as $code => $cfg)
                                    <option value="{{ $code }}">{{ $cfg['name'] ?? $code }}</option>
                                @endforeach
                            </select>
                            {{-- Date Range --}}
                            <div class="flex items-center gap-2 shrink-0">
                                <input wire:model.live="gatewayStartDate" type="date" class="px-2 py-2 text-xs bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-600 dark:text-slate-300 focus:ring-1 focus:ring-amber-400 outline-none transition w-32"/>
                                <span class="text-slate-300 dark:text-slate-600 text-xs">—</span>
                                <input wire:model.live="gatewayEndDate" type="date" class="px-2 py-2 text-xs bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-600 dark:text-slate-300 focus:ring-1 focus:ring-amber-400 outline-none transition w-32"/>
                            </div>
                        </div>
                        {{-- Date Presets + Reset --}}
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider">Cepat:</span>
                            @foreach(['today'=>'Hari Ini','yesterday'=>'Kemarin','7days'=>'7 Hari','this_month'=>'Bulan Ini','last_month'=>'Bln Lalu'] as $preset => $label)
                                <button type="button" wire:click="setGatewayDatePreset('{{ $preset }}')"
                                    class="px-2.5 py-1 text-[9px] font-bold rounded-lg transition-all
                                    {{ ($gatewayStartDate && $gatewayEndDate) ? 'bg-amber-500/10 text-amber-700 dark:text-amber-400 hover:bg-amber-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                            @if($gatewaySearch || $gatewayStatus || $gatewayChannel || $gatewayStartDate || $gatewayEndDate)
                                <button type="button" wire:click="resetGatewayFilters" class="ml-auto flex items-center gap-1 px-2.5 py-1 text-[9px] font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-500/10 rounded-lg transition-all">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Reset Filter
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="px-6 py-3.5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider font-serif-display">Log Transaksi Gateway Duitku</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Semua percobaan pembayaran wali santri via QRIS / Virtual Account — termasuk yang belum berhasil.</p>
                        </div>
                        <button type="button" wire:click="syncAllPendingGateway" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-300 rounded-xl text-xs font-extrabold border border-amber-500/30 transition shrink-0 active:scale-95">
                            <svg wire:loading.class="animate-spin" wire:target="syncAllPendingGateway" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span wire:loading.remove wire:target="syncAllPendingGateway">🔄 Sinkronkan Status Pending</span>
                            <span wire:loading wire:target="syncAllPendingGateway">Menghubungi Duitku...</span>
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs min-w-[900px]">
                            <thead>
                                <tr class="bg-slate-50/80 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">
                                    <th class="py-4 px-4 w-36">Waktu</th>
                                    <th class="py-4 px-4">Santri</th>
                                    <th class="py-4 px-4 text-center">Channel</th>
                                    <th class="py-4 px-4 text-right">Tagihan</th>
                                    <th class="py-4 px-4 text-right">MDR</th>
                                    <th class="py-4 px-4 text-right">Total Bayar</th>
                                    <th class="py-4 px-4 text-center">Status</th>
                                    <th class="py-4 px-4">Ref. Duitku</th>
                                    <th class="py-4 px-4 text-center">Aksi &amp; Rincian</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                @forelse($gatewayTransactions ?? [] as $trx)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                        <td class="py-3 px-4 text-slate-500 font-medium">
                                            {{ $trx->created_at->translatedFormat('d M Y') }}
                                            <span class="text-[9px] text-slate-400 block">{{ $trx->created_at->format('H:i') }} WIB</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ $trx->person?->name ?? '—' }}</span>
                                            <span class="text-[9px] text-slate-400">{{ $trx->person?->gender === 'L' ? '👦' : '👧' }} {{ $trx->merchant_order_id }}</span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="inline-block px-2 py-0.5 rounded-lg text-[9px] font-extrabold bg-amber-500/10 text-amber-700 dark:text-amber-400 uppercase">
                                                {{ $trx->channel_label ?? $trx->payment_channel ?? '—' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-right font-semibold text-slate-700 dark:text-slate-300">
                                            Rp {{ number_format($trx->bill_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-right text-rose-500 font-semibold text-[10px]">
                                            + Rp {{ number_format($trx->mdr_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-extrabold text-slate-900 dark:text-white">
                                            Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @php $status = $trx->status; @endphp
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[9px] font-extrabold uppercase
                                                @if($status === 'success') bg-emerald-500/10 text-emerald-600 dark:text-emerald-400
                                                @elseif($status === 'pending') bg-amber-500/10 text-amber-600 dark:text-amber-400
                                                @elseif($status === 'expired') bg-slate-500/10 text-slate-500
                                                @else bg-rose-500/10 text-rose-600 dark:text-rose-400 @endif">
                                                @if($status === 'success') ✅ Sukses
                                                @elseif($status === 'pending') ⏳ Menunggu
                                                @elseif($status === 'expired') 🕐 Expired
                                                @else ❌ Gagal @endif
                                            </span>
                                            @if($status === 'pending' && $trx->expires_at)
                                                <span class="text-[9px] text-slate-400 block mt-0.5">exp: {{ $trx->expires_at->diffForHumans() }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            @if($trx->duitku_reference)
                                                <code class="text-[9px] font-mono bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 px-1.5 py-0.5 rounded">{{ $trx->duitku_reference }}</code>
                                            @else
                                                <span class="text-slate-400 text-[10px]">—</span>
                                            @endif
                                        </td>
                                        {{-- Aksi & Tagihan Terkait --}}
                                        <td class="py-3 px-4 text-center">
                                            <div class="inline-flex items-center justify-center gap-1.5 flex-wrap">
                                                @if($status === 'pending')
                                                    <button type="button" 
                                                            wire:click="syncGatewayStatus('{{ $trx->id }}')" 
                                                            wire:loading.attr="disabled"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-[9px] font-black rounded-xl bg-amber-500 hover:bg-amber-600 text-white shadow-xs transition active:scale-95"
                                                            title="Cek status pembayaran ke Duitku secara real-time">
                                                        <svg wire:loading.class="animate-spin" wire:target="syncGatewayStatus('{{ $trx->id }}')" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                        <span>Cek Status</span>
                                                    </button>
                                                @endif

                                                @php $breakdownCount = count($trx->bill_breakdown ?? []); @endphp
                                                @if($breakdownCount > 0)
                                                    <button type="button" wire:click="showGatewayBreakdown('{{ $trx->id }}')"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-[9px] font-extrabold rounded-xl transition-all
                                                        bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 hover:bg-indigo-500/20 border border-indigo-500/20 dark:border-indigo-500/30">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                                        {{ $breakdownCount }} Tagihan
                                                    </button>
                                                @else
                                                    <span class="text-slate-400 text-[10px]">—</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="py-16 text-center text-slate-400 font-semibold">
                                            <div class="flex flex-col items-center gap-3">
                                                <svg class="w-10 h-10 text-slate-200 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                <span class="text-sm">Tidak ada transaksi yang cocok dengan filter ini.</span>
                                                @if($gatewaySearch || $gatewayStatus || $gatewayChannel || $gatewayStartDate || $gatewayEndDate)
                                                    <button type="button" wire:click="resetGatewayFilters" class="text-xs text-amber-600 hover:underline font-semibold">Reset Filter</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(isset($gatewayTransactions) && $gatewayTransactions->total() > 0)
                        <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/50 dark:bg-slate-950/20">
                            <div class="text-[11px] font-semibold text-slate-400">
                                Menampilkan <span class="font-bold text-slate-700 dark:text-slate-300">{{ $gatewayTransactions->firstItem() ?? 0 }}</span> s.d. <span class="font-bold text-slate-700 dark:text-slate-300">{{ $gatewayTransactions->lastItem() ?? 0 }}</span> dari <span class="font-bold text-amber-600">{{ $gatewayTransactions->total() }}</span> transaksi
                            </div>
                            @if($gatewayTransactions->hasPages())
                                <div>{{ $gatewayTransactions->links(data: ['scrollTo' => false]) }}</div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- ════ MODAL: Breakdown Tagihan ════ --}}
                @if($showGatewayBreakdownModal && !empty($selectedGatewayTrxData))
                    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                        {{-- Backdrop --}}
                        <div class="fixed inset-0" wire:click="closeGatewayBreakdownModal"></div>

                        {{-- Modal Panel --}}
                        <div class="relative z-10 bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col border border-slate-200/80 dark:border-slate-700 overflow-hidden">

                            {{-- Modal Header --}}
                            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-start justify-between gap-4 bg-gradient-to-r from-indigo-500/5 to-transparent shrink-0">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[9px] font-extrabold uppercase tracking-widest text-indigo-500">Detail Tagihan Terkait</span>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[9px] font-extrabold uppercase
                                            @if($selectedGatewayTrxData['status'] === 'success') bg-emerald-500/10 text-emerald-600
                                            @elseif($selectedGatewayTrxData['status'] === 'pending') bg-amber-500/10 text-amber-600
                                            @else bg-rose-500/10 text-rose-600 @endif">
                                            @if($selectedGatewayTrxData['status'] === 'success') ✅ Sukses
                                            @elseif($selectedGatewayTrxData['status'] === 'pending') ⏳ Menunggu
                                            @else ❌ Gagal/Expired @endif
                                        </span>
                                    </div>
                                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white truncate">{{ $selectedGatewayTrxData['santri_name'] }}</h3>
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $selectedGatewayTrxData['created_at'] }} &nbsp;·&nbsp; <span class="font-mono">{{ $selectedGatewayTrxData['merchant_order_id'] }}</span></p>
                                </div>
                                <button type="button" wire:click="closeGatewayBreakdownModal" class="shrink-0 w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            {{-- Summary Row --}}
                            <div class="px-6 py-3 flex items-center gap-6 bg-slate-50/60 dark:bg-slate-950/40 border-b border-slate-100 dark:border-slate-800 shrink-0 text-xs">
                                <div>
                                    <span class="text-slate-400 text-[9px] uppercase font-bold block">Channel</span>
                                    <span class="font-bold text-amber-600 dark:text-amber-400">{{ $selectedGatewayTrxData['channel_label'] ?? '—' }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-[9px] uppercase font-bold block">Total Tagihan</span>
                                    <span class="font-bold text-slate-700 dark:text-slate-300">Rp {{ number_format($selectedGatewayTrxData['bill_amount'], 0, ',', '.') }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-[9px] uppercase font-bold block">Biaya MDR</span>
                                    <span class="font-bold text-rose-500">+ Rp {{ number_format($selectedGatewayTrxData['mdr_amount'], 0, ',', '.') }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-[9px] uppercase font-bold block">Total Dibayar</span>
                                    <span class="font-extrabold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($selectedGatewayTrxData['total_amount'], 0, ',', '.') }}</span>
                                </div>
                                @if($selectedGatewayTrxData['duitku_reference'] !== '—')
                                    <div class="ml-auto">
                                        <span class="text-slate-400 text-[9px] uppercase font-bold block">Ref. Duitku</span>
                                        <code class="text-[10px] font-mono text-slate-500 dark:text-slate-400">{{ $selectedGatewayTrxData['duitku_reference'] }}</code>
                                    </div>
                                @endif
                            </div>

                            {{-- Breakdown Table --}}
                            <div class="overflow-y-auto flex-1">
                                <table class="w-full text-xs border-collapse">
                                    <thead class="sticky top-0 z-10">
                                        <tr class="bg-slate-50/95 dark:bg-slate-950/95 border-b border-slate-200 dark:border-slate-800 text-slate-400 font-extrabold uppercase tracking-wider text-[9px]">
                                            <th class="py-3 px-5 text-left">#</th>
                                            <th class="py-3 px-4 text-left">Jenis Iuran</th>
                                            <th class="py-3 px-4 text-left">Periode</th>
                                            <th class="py-3 px-4 text-right">Nominal</th>
                                            <th class="py-3 px-4 text-center">Cicilan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                        @foreach($selectedGatewayTrxData['breakdown'] as $i => $item)
                                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20">
                                                <td class="py-3 px-5 text-slate-400 font-bold text-center w-8">{{ $i + 1 }}</td>
                                                <td class="py-3 px-4">
                                                    <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ $item['config_label'] ?? ucwords(str_replace('_', ' ', $item['bill_type'] ?? '')) }}</span>
                                                    <span class="text-[9px] font-mono text-slate-300 dark:text-slate-600">{{ Str::limit($item['bill_id'], 20) }}</span>
                                                </td>
                                                <td class="py-3 px-4">
                                                    <span class="text-slate-600 dark:text-slate-400 font-semibold">{{ $item['period_label'] ?? '—' }}</span>
                                                </td>
                                                <td class="py-3 px-4 text-right font-extrabold text-slate-900 dark:text-white">
                                                    Rp {{ number_format($item['pay_portion'] ?? $item['net_amount'] ?? 0, 0, ',', '.') }}
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    @if($item['is_partial'] ?? false)
                                                        <span class="inline-block px-2 py-0.5 rounded-lg text-[9px] font-extrabold bg-amber-500/10 text-amber-600">Sebagian</span>
                                                    @else
                                                        <span class="inline-block px-2 py-0.5 rounded-lg text-[9px] font-extrabold bg-emerald-500/10 text-emerald-600">Lunas</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-indigo-500/5 border-t-2 border-indigo-500/20">
                                            <td colspan="3" class="py-3 px-5 text-xs font-extrabold text-slate-700 dark:text-slate-300 text-right">Total</td>
                                            <td class="py-3 px-4 text-right text-sm font-extrabold text-indigo-600 dark:text-indigo-400">
                                                Rp {{ number_format(collect($selectedGatewayTrxData['breakdown'])->sum(fn($i) => $i['pay_portion'] ?? $i['net_amount'] ?? 0), 0, ',', '.') }}
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            {{-- Modal Footer --}}
                            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex justify-end shrink-0">
                                <button type="button" wire:click="closeGatewayBreakdownModal" class="px-5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════════════════ --}}
        {{-- TAB: REKONSILIASI & SETTLEMENT GATEWAY (FASE 4)                        --}}
        {{-- ═══════════════════════════════════════════════════════════════════════ --}}
        @if ($activeTab === 'settlement')
            <div class="space-y-6">
                {{-- 1. Header Banner & Actions --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-sky-900/10 via-indigo-900/5 to-slate-900/0 dark:from-sky-950/40 dark:via-indigo-950/20 dark:to-slate-900 border border-sky-200/80 dark:border-sky-800/40 p-6 rounded-3xl shadow-sm backdrop-blur-xs">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20">
                                🏦 Rekonsiliasi &amp; Distribusi Dana
                            </span>
                            <span class="text-xs text-slate-400 font-semibold">• Periode: <strong class="text-slate-700 dark:text-slate-200">{{ $settlementReport['period_label'] }}</strong></span>
                        </div>
                        <h2 class="font-black text-xl text-slate-900 dark:text-slate-100 tracking-tight">Settlement Report &amp; Alokasi Kas Komplek</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-2xl">
                            Pencocokan arus dana masuk dari Duitku ke rekening pondok serta pemisahan porsi anggaran per unit (Pondok, Madrasah, Dapur Majek, dan Kas per Komplek Asrama).
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        {{-- Tombol Cetak PDF Rekap --}}
                        <a href="{{ route('keuangan.settlement.pdf', ['date_from' => $settlementDateFrom, 'date_to' => $settlementDateTo, 'source' => $settlementSource]) }}" 
                           target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-extrabold text-xs rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs transition-all active:scale-95">
                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Cetak Rekap PDF</span>
                        </a>

                        {{-- Tombol Kunci & Simpan Distribusi (Khusus Pusat / Admin) --}}
                        @if($this->canLockSettlement())
                            <button type="button" 
                                    wire:click="saveSettlementSnapshot"
                                    wire:confirm="Apakah Anda yakin ingin menyimpan dan mengunci snapshot distribusi dana periode ini ke audit log pembukuan?"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-2xl shadow-sm transition-all active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                <span>Kunci &amp; Simpan Rekap</span>
                            </button>
                        @endif
                    </div>
                </div>

                {{-- 2. Filter & Rentang Tanggal --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-5 rounded-3xl shadow-xs space-y-4">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        {{-- Presets --}}
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-[11px] font-bold text-slate-400 mr-1">Preset Cepat:</span>
                            <button type="button" wire:click="setSettlementQuickDate('today')" 
                                    class="px-2.5 py-1 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition">
                                Hari Ini
                            </button>
                            <button type="button" wire:click="setSettlementQuickDate('last_7_days')" 
                                    class="px-2.5 py-1 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition">
                                7 Hari Terakhir
                            </button>
                            <button type="button" wire:click="setSettlementQuickDate('this_month')" 
                                    class="px-2.5 py-1 rounded-xl text-xs font-bold bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 border border-sky-300 dark:border-sky-500/30 transition">
                                Bulan Ini
                            </button>
                            <button type="button" wire:click="setSettlementQuickDate('last_month')" 
                                    class="px-2.5 py-1 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition">
                                Bulan Lalu
                            </button>
                        </div>

                        {{-- Date Inputs & Source Selector --}}
                        <div class="flex items-center gap-3 flex-wrap">
                            {{-- Date From --}}
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] font-bold text-slate-400">Dari:</span>
                                <input type="date" wire:model.live="settlementDateFrom" 
                                       class="text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white px-2.5 py-1.5 focus:ring-2 focus:ring-sky-500">
                            </div>

                            {{-- Date To --}}
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] font-bold text-slate-400">Sampai:</span>
                                <input type="date" wire:model.live="settlementDateTo" 
                                       class="text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white px-2.5 py-1.5 focus:ring-2 focus:ring-sky-500">
                            </div>

                            {{-- Source Selector --}}
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] font-bold text-slate-400">Sumber:</span>
                                <select wire:model.live="settlementSource" 
                                        class="text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white px-2.5 py-1.5 focus:ring-2 focus:ring-sky-500">
                                    <option value="gateway">⚡ Khusus Online (Duitku)</option>
                                    <option value="kasir">💵 Khusus Kasir Manual</option>
                                    <option value="all">🌐 Semua Pembayaran</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. KPI Cards: Arus Kas & Rekonsiliasi --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Card 1: Gross --}}
                    <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-1">
                        <div class="flex items-center justify-between text-slate-400">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider">Total Uang Diterima (Gross)</span>
                            <span class="p-1.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        </div>
                        <div class="text-2xl font-black text-slate-900 dark:text-slate-100 font-mono">
                            Rp {{ number_format($settlementReport['total_gross'], 0, ',', '.') }}
                        </div>
                        <div class="text-[11px] text-slate-400">
                            Dibayar oleh wali via QRIS &amp; VA
                        </div>
                    </div>

                    {{-- Card 2: MDR Fee --}}
                    <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-rose-200/60 dark:border-rose-900/40 shadow-xs space-y-1">
                        <div class="flex items-center justify-between text-rose-500">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider">Biaya Layanan (MDR)</span>
                            <span class="p-1.5 bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 rounded-xl">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                            </span>
                        </div>
                        <div class="text-2xl font-black text-rose-600 dark:text-rose-400 font-mono">
                            - Rp {{ number_format($settlementReport['total_mdr'], 0, ',', '.') }}
                        </div>
                        <div class="text-[11px] text-rose-500/80">
                            Fee Duitku (Ditanggung Wali)
                        </div>
                    </div>

                    {{-- Card 3: Net Settlement --}}
                    <div class="bg-gradient-to-br from-emerald-500/10 to-transparent dark:from-emerald-950/40 dark:to-slate-900 p-5 rounded-3xl border-2 border-emerald-500/60 dark:border-emerald-500/40 shadow-sm space-y-1">
                        <div class="flex items-center justify-between text-emerald-600 dark:text-emerald-400">
                            <span class="text-[11px] font-black uppercase tracking-wider">Dana Bersih Cair (Net)</span>
                            <span class="p-1.5 bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        </div>
                        <div class="text-2xl font-black text-emerald-700 dark:text-emerald-300 font-mono">
                            Rp {{ number_format($settlementReport['total_net'], 0, ',', '.') }}
                        </div>
                        <div class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                            Dana masuk ke rekening pondok
                        </div>
                    </div>

                    {{-- Card 4: Volume Trx --}}
                    <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-1">
                        <div class="flex items-center justify-between text-sky-500">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider">Total Transaksi</span>
                            <span class="p-1.5 bg-sky-100 dark:bg-sky-500/20 text-sky-600 dark:text-sky-400 rounded-xl">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </span>
                        </div>
                        <div class="text-2xl font-black text-slate-900 dark:text-slate-100">
                            {{ $settlementReport['total_trx'] }} <span class="text-xs font-normal text-slate-400">Trx</span>
                        </div>
                        <div class="text-[11px] text-slate-400">
                            Status Berhasil (Success)
                        </div>
                    </div>
                </div>

                {{-- 4. SECTION 1: Alokasi Pembagian Pos Anggaran Utama --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-xl">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                            </span>
                            <div>
                                <h3 class="font-extrabold text-sm text-slate-900 dark:text-slate-100">1. Alokasi Pembagian Pos Anggaran Utama</h3>
                                <p class="text-[11px] text-slate-400">Distribusi dana bersih ke kas operasional masing-masing unit</p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80 dark:bg-slate-950/80 text-slate-400 uppercase font-black text-[9px] tracking-wider border-b border-slate-100 dark:border-slate-800">
                                    <th class="py-3 px-6">Pos Anggaran / Unit</th>
                                    <th class="py-3 px-4 text-center">Jumlah Tagihan</th>
                                    <th class="py-3 px-4">Porsi (%)</th>
                                    <th class="py-3 px-6 text-right">Total Dana Bersih</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @forelse($settlementReport['category_breakdown'] as $cat)
                                    @php
                                        $percent = $settlementReport['total_net'] > 0 
                                            ? round(($cat['amount'] / $settlementReport['total_net']) * 100, 1) 
                                            : 0;
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                        <td class="py-3.5 px-6">
                                            <div class="flex items-center gap-2.5">
                                                <span class="text-base">{{ $cat['icon'] ?? '🏷️' }}</span>
                                                <div>
                                                    <span class="font-extrabold text-slate-900 dark:text-slate-100 block">{{ $cat['label'] }}</span>
                                                    <span class="text-[10px] text-slate-400">{{ $cat['desc'] }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center font-bold text-slate-600 dark:text-slate-300">
                                            {{ $cat['count'] }} item
                                        </td>
                                        <td class="py-3.5 px-4 w-48">
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1 h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                                    <div class="h-full bg-indigo-500 rounded-full" style="width: {{ min(100, $percent) }}%"></div>
                                                </div>
                                                <span class="text-[11px] font-black text-slate-700 dark:text-slate-300 font-mono w-10 text-right">{{ $percent }}%</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-6 text-right font-black text-sm text-slate-900 dark:text-white font-mono">
                                            Rp {{ number_format($cat['amount'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-10 text-center text-slate-400 font-semibold">
                                            Belum ada data pembayaran dalam rentang tanggal ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-indigo-500/5 dark:bg-indigo-950/20 border-t-2 border-indigo-500/20 font-black">
                                    <td class="py-3.5 px-6 text-slate-800 dark:text-slate-200">TOTAL SELURUH POS ANGGARAN:</td>
                                    <td class="py-3.5 px-4 text-center text-slate-800 dark:text-slate-200">
                                        {{ collect($settlementReport['category_breakdown'])->sum('count') }} item
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-500 font-mono">100.0%</td>
                                    <td class="py-3.5 px-6 text-right text-base text-indigo-600 dark:text-indigo-400 font-mono">
                                        Rp {{ number_format($settlementReport['total_net'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- 5. SECTION 2: Rincian Kas Komplek per Asrama (Drill-down) --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </span>
                            <div>
                                <h3 class="font-extrabold text-sm text-slate-900 dark:text-slate-100">2. Rincian Alokasi Kas Komplek Asrama (Per Asrama/Komplek)</h3>
                                <p class="text-[11px] text-slate-400">Porsi dana kas komplek santri yang dialokasikan ke masing-masing bendahara komplek</p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80 dark:bg-slate-950/80 text-slate-400 uppercase font-black text-[9px] tracking-wider border-b border-slate-100 dark:border-slate-800">
                                    <th class="py-3 px-6">Nama Komplek Asrama</th>
                                    <th class="py-3 px-4">Unit</th>
                                    <th class="py-3 px-4 text-center">Jumlah Santri / Tagihan</th>
                                    <th class="py-3 px-6 text-right">Total Kas Terkumpul</th>
                                    <th class="py-3 px-6 text-center">Aksi Dokumen &amp; Rincian</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @forelse($settlementReport['dormitory_breakdown'] as $dorm)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                        <td class="py-3.5 px-6">
                                            <div class="flex items-center gap-2">
                                                <span class="text-base">🏠</span>
                                                <span class="font-extrabold text-slate-900 dark:text-slate-100">{{ $dorm['dormitory_name'] }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase
                                                {{ $dorm['gender'] === 'L' ? 'bg-sky-100 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-950/60 dark:text-pink-300' }}">
                                                {{ $dorm['gender'] === 'L' ? 'Putra' : 'Putri' }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <strong class="font-black text-slate-700 dark:text-slate-300">{{ $dorm['count_santri'] }} Santri</strong>
                                            <span class="text-[10px] font-bold text-slate-400 block">({{ $dorm['count_bills'] ?? $dorm['count_santri'] }} Tagihan)</span>
                                        </td>
                                        <td class="py-3.5 px-6 text-right font-black text-sm text-emerald-600 dark:text-emerald-400 font-mono">
                                            Rp {{ number_format($dorm['total_amount'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-6 text-center">
                                            <div class="inline-flex items-center gap-1.5">
                                                {{-- Tombol Lihat Santri Modal --}}
                                                <button type="button" 
                                                        wire:click="openDormitoryDetailModal('{{ $dorm['dormitory_id'] }}')"
                                                        class="px-2.5 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-[11px] font-extrabold border border-slate-200 dark:border-slate-700 transition flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span>Lihat Santri</span>
                                                </button>

                                                {{-- Tombol Slip PDF --}}
                                                <a href="{{ route('keuangan.settlement.slip-komplek', ['dormitoryId' => $dorm['dormitory_id'], 'date_from' => $settlementDateFrom, 'date_to' => $settlementDateTo, 'source' => $settlementSource]) }}" 
                                                   target="_blank"
                                                   class="px-2.5 py-1.5 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 rounded-xl text-[11px] font-extrabold border border-emerald-300 dark:border-emerald-700 transition flex items-center gap-1"
                                                   title="Cetak Slip Serah Terima Kas Komplek">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    <span>Slip PDF</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-10 text-center text-slate-400 font-semibold">
                                            Tidak ada pembayaran Kas Komplek dalam periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if(count($settlementReport['dormitory_breakdown']) > 0)
                            <tfoot>
                                <tr class="bg-emerald-500/5 dark:bg-emerald-950/20 border-t-2 border-emerald-500/20 font-black">
                                    <td colspan="2" class="py-3.5 px-6 text-slate-800 dark:text-slate-200">TOTAL KAS SELURUH KOMPLEK:</td>
                                    <td class="py-3.5 px-4 text-center text-slate-800 dark:text-slate-200">
                                        {{ collect($settlementReport['dormitory_breakdown'])->sum('count_santri') }} Santri
                                        <span class="text-[10px] text-slate-500 block">({{ collect($settlementReport['dormitory_breakdown'])->sum('count_bills') }} Tagihan)</span>
                                    </td>
                                    <td class="py-3.5 px-6 text-right text-base text-emerald-600 dark:text-emerald-400 font-mono">
                                        Rp {{ number_format(collect($settlementReport['dormitory_breakdown'])->sum('total_amount'), 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- 6. SECTION 3: Riwayat Rekonsiliasi Tersimpan (Audit Trail) --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm p-6 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <h3 class="font-extrabold text-sm text-slate-900 dark:text-slate-100">Riwayat Audit Rekonsiliasi &amp; Distribusi Tersimpan</h3>
                            <p class="text-[11px] text-slate-400">Snapshot data rekonsiliasi yang pernah dikunci dan dicatat sebelumnya</p>
                        </div>
                    </div>

                    @if($savedDistributions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/80 dark:bg-slate-950/80 text-slate-400 uppercase font-black text-[9px] tracking-wider border-b border-slate-100 dark:border-slate-800">
                                        <th class="py-3 px-4">Periode</th>
                                        <th class="py-3 px-4">Dicatat Oleh</th>
                                        <th class="py-3 px-4">Waktu Kunci</th>
                                        <th class="py-3 px-4 text-right">Dana Bersih (Net)</th>
                                        <th class="py-3 px-4">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach($savedDistributions as $dist)
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20">
                                            <td class="py-3 px-4 font-bold text-slate-800 dark:text-slate-200">
                                                {{ $dist->period_from?->locale('id')->translatedFormat('d M Y') }} s/d {{ $dist->period_to?->locale('id')->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="py-3 px-4 text-slate-600 dark:text-slate-400 font-medium">
                                                {{ $dist->distributor?->name ?? 'Bendahara Pusat' }}
                                            </td>
                                            <td class="py-3 px-4 text-slate-500 text-[11px]">
                                                {{ $dist->distributed_at?->locale('id')->translatedFormat('d M Y, H:i') }} WIB
                                            </td>
                                            <td class="py-3 px-4 text-right font-black font-mono text-emerald-600 dark:text-emerald-400">
                                                Rp {{ number_format($dist->total_net, 0, ',', '.') }}
                                            </td>
                                            <td class="py-3 px-4 text-slate-500 text-[11px]">
                                                {{ $dist->notes ?? '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($savedDistributions->hasPages())
                            <div class="pt-2">
                                {{ $savedDistributions->links(data: ['scrollTo' => false]) }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-6 text-slate-400 text-xs font-semibold bg-slate-50 dark:bg-slate-950/40 rounded-2xl border border-slate-100 dark:border-slate-800">
                            Belum ada snapshot rekonsiliasi yang disimpan. Klik tombol <strong>"Kunci &amp; Simpan Rekap"</strong> di atas saat pencairan selesai.
                        </div>
                    @endif
                </div>

                {{-- 7. MODAL: Drill-down Santri per Komplek --}}
                @if($showDormitoryModal && !empty($modalDormitoryData))
                    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                        {{-- Backdrop --}}
                        <div class="fixed inset-0" wire:click="closeDormitoryDetailModal"></div>

                        {{-- Modal Content --}}
                        <div class="relative z-10 bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col border border-slate-200 dark:border-slate-800 overflow-hidden">
                            {{-- Header --}}
                            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-emerald-500/5">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-base">🏠</span>
                                        <h3 class="font-extrabold text-base text-slate-900 dark:text-white">{{ $modalDormitoryData['dormitory_name'] }}</h3>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $modalDormitoryData['gender'] === 'L' ? 'bg-sky-100 text-sky-700' : 'bg-pink-100 text-pink-700' }}">
                                            {{ $modalDormitoryData['gender'] === 'L' ? 'Putra' : 'Putri' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5">Daftar {{ count($modalDormitoryData['santri_list']) }} santri yang telah membayar Kas Komplek</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Total Kas Terkumpul</span>
                                    <span class="text-base font-black text-emerald-600 dark:text-emerald-400 font-mono">Rp {{ number_format($modalDormitoryData['total_amount'], 0, ',', '.') }}</span>
                                </div>
                            </div>

                            {{-- Table Santri --}}
                            <div class="overflow-y-auto flex-1 p-4">
                                <table class="w-full text-xs text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 dark:bg-slate-950 text-slate-400 uppercase font-black text-[9px] tracking-wider border-b border-slate-100 dark:border-slate-800">
                                            <th class="py-2.5 px-3">#</th>
                                            <th class="py-2.5 px-3">Nama Santri</th>
                                            <th class="py-2.5 px-3">Kamar</th>
                                            <th class="py-2.5 px-3">Waktu Bayar</th>
                                            <th class="py-2.5 px-3">Metode</th>
                                            <th class="py-2.5 px-3 text-right">Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                        @foreach($modalDormitoryData['santri_list'] as $idx => $santri)
                                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                                <td class="py-2.5 px-3 text-slate-400 font-bold">{{ $idx + 1 }}</td>
                                                <td class="py-2.5 px-3">
                                                    <strong class="text-slate-800 dark:text-slate-200 block">{{ $santri['name'] }}</strong>
                                                    <span class="text-[10px] font-mono text-slate-400">NIS: {{ $santri['nis'] }}</span>
                                                </td>
                                                <td class="py-2.5 px-3 text-slate-600 dark:text-slate-400 font-medium">
                                                    {{ $santri['room_name'] }}
                                                </td>
                                                <td class="py-2.5 px-3 text-slate-500 text-[11px]">
                                                    {{ $santri['paid_date'] }}
                                                </td>
                                                <td class="py-2.5 px-3 text-[10px] text-slate-500 font-bold">
                                                    {{ $santri['method'] }}
                                                </td>
                                                <td class="py-2.5 px-3 text-right font-black font-mono text-slate-900 dark:text-white">
                                                    Rp {{ number_format($santri['amount'], 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Footer --}}
                            <div class="px-6 py-3.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-950/40">
                                <a href="{{ route('keuangan.settlement.slip-komplek', ['dormitoryId' => $modalDormitoryData['dormitory_id'], 'date_from' => $settlementDateFrom, 'date_to' => $settlementDateTo, 'source' => $settlementSource]) }}" 
                                   target="_blank"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Cetak Slip Serah Terima (PDF)</span>
                                </a>

                                <button type="button" wire:click="closeDormitoryDetailModal" class="px-4 py-1.5 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        @if ($activeTab === 'registration_rates')
            <div class="space-y-6">
                {{-- Header Actions --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-6 rounded-3xl shadow-sm">
                    <div>
                        <h2 class="font-extrabold text-lg text-slate-900 dark:text-slate-100">Pengaturan Tarif Pendaftaran Santri Baru &amp; Kitab</h2>
                        <p class="text-xs text-slate-400">Kelola komponen rincian harga pendaftaran, seragam, almari, dan paket kitab per kelas</p>
                    </div>
                    <div>
                        <button type="button" wire:click="openItemModal" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-xl shadow-md transition-all text-xs flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>+ Tambah Item Tarif Baru</span>
                        </button>
                    </div>
                </div>

                {{-- Sub Tabs Navigation --}}
                <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-2">
                    <button type="button" wire:click="$set('activeRegSubTab', 'items')"
                        class="px-5 py-2.5 font-extrabold text-xs rounded-xl transition-all flex items-center gap-2 {{ $activeRegSubTab === 'items' ? 'bg-emerald-600 text-white shadow-md' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Item Tarif Registrasi (Pendaftaran, Seragam, Almari)</span>
                    </button>
                    <button type="button" wire:click="$set('activeRegSubTab', 'kitab')"
                        class="px-5 py-2.5 font-extrabold text-xs rounded-xl transition-all flex items-center gap-2 {{ $activeRegSubTab === 'kitab' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>Tarif Paket Kitab Per Kelas Madrasah</span>
                    </button>
                </div>

                {{-- SUB-TAB 1: ITEM TARIF REGISTRASI --}}
                @if($activeRegSubTab === 'items')
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                        {{-- Search & Filter Bar --}}
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-200/60 dark:border-slate-700/60">
                            <div class="relative w-full sm:w-72">
                                <input type="text" wire:model.live="regItemSearch" placeholder="Cari nama item registrasi..." class="w-full pl-9 pr-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-100 focus:ring-emerald-500">
                                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                                <select wire:model.live="regItemCategoryFilter" class="px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100">
                                    <option value="">Semua Kategori</option>
                                    <option value="dasar">Dasar Pendaftaran</option>
                                    <option value="fasilitas">Fasilitas Asrama</option>
                                    <option value="seragam">Seragam</option>
                                    <option value="katering">Katering / Majek</option>
                                    <option value="bangunan">Bangunan</option>
                                    <option value="administrasi">Administrasi (KTS)</option>
                                    <option value="kitab">Kitab</option>
                                </select>

                                <select wire:model.live="regItemGenderFilter" class="px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100">
                                    <option value="">Semua Target Gender</option>
                                    <option value="L">Putra (L)</option>
                                    <option value="P">Putri (P)</option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto border border-slate-200/60 dark:border-slate-800 rounded-2xl">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 font-bold uppercase text-[10px] border-b border-slate-200 dark:border-slate-700">
                                    <tr>
                                        <th class="p-3">Nama Item Tarif</th>
                                        <th class="p-3">Kategori</th>
                                        <th class="p-3">Target Gender</th>
                                        <th class="p-3">Target Keberadaan</th>
                                        <th class="p-3 text-right">Nominal Tarif (Rp)</th>
                                        <th class="p-3 text-center">Status</th>
                                        <th class="p-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                                    @forelse($registrationItems as $item)
                                        @php
                                            $filters = $item->target_filters ?? [];
                                            $cat = $filters['category'] ?? 'dasar';
                                            $gen = $filters['gender'] ?? 'ALL';
                                            $res = $filters['residence'] ?? 'ALL';
                                        @endphp
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                                            <td class="p-3">
                                                <div class="font-extrabold text-slate-900 dark:text-slate-100">{{ $item->label }}</div>
                                            </td>
                                            <td class="p-3">
                                                <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase rounded-full bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                    {{ $cat }}
                                                </span>
                                            </td>
                                            <td class="p-3 font-semibold text-slate-700 dark:text-slate-300">
                                                @if($gen === 'L')
                                                    <span class="text-blue-600 font-bold">Putra (L)</span>
                                                @elseif($gen === 'P')
                                                    <span class="text-rose-600 font-bold">Putri (P)</span>
                                                @else
                                                    <span>Semua Gender</span>
                                                @endif
                                            </td>
                                            <td class="p-3 font-semibold text-slate-700 dark:text-slate-300">
                                                @if($res === 'mukim')
                                                    <span class="text-indigo-600 font-bold">Khusus Mukim</span>
                                                @elseif($res === 'laju')
                                                    <span class="text-amber-600 font-bold">Khusus Laju</span>
                                                @else
                                                    <span>Mukim &amp; Laju</span>
                                                @endif
                                            </td>
                                            <td class="p-3 text-right font-mono font-black text-emerald-600 dark:text-emerald-400 text-sm">
                                                Rp {{ number_format($item->amount, 0, ',', '.') }}
                                            </td>
                                            <td class="p-3 text-center">
                                                <button type="button" wire:click="toggleItemActive('{{ $item->id }}')"
                                                    class="px-2.5 py-1 text-[10px] font-extrabold rounded-full transition-all {{ $item->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-500' }}">
                                                    {{ $item->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                                                </button>
                                            </td>
                                            <td class="p-3 text-center">
                                                <div class="inline-flex items-center justify-center gap-1.5">
                                                    {{-- Edit --}}
                                                    <button type="button" wire:click="openItemModal('{{ $item->id }}')"
                                                        class="p-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-lg text-xs transition-colors flex items-center gap-1"
                                                        title="Edit Item Tarif">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        <span>Edit</span>
                                                    </button>
                                                    {{-- Delete with confirmation --}}
                                                    <button type="button" wire:click="confirmDeleteRegItem('{{ $item->id }}')"
                                                        class="p-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 dark:hover:bg-rose-900 text-rose-600 dark:text-rose-300 font-bold rounded-lg text-xs transition-colors flex items-center gap-1"
                                                        title="Hapus Item Tarif">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        <span>Hapus</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="p-8 text-center text-slate-400">Belum ada item tarif pendaftaran yang dikonfigurasi / cocok dengan filter.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- SUB-TAB 2: TARIF KITAB PER KELAS --}}
                @if($activeRegSubTab === 'kitab')
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                        {{-- Filter Bar --}}
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-200/60 dark:border-slate-700/60">
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-xs text-slate-700 dark:text-slate-300">Filter Jenjang:</span>
                                <select wire:model.live="kitabJenjangFilter" class="px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100">
                                    <option value="">Semua Jenjang (Awaliyah, Wustho, Ulya)</option>
                                    <option value="awaliyah">Awaliyah / Ula</option>
                                    <option value="wustho">Wustho</option>
                                    <option value="ulya">Ulya</option>
                                </select>
                            </div>

                            <div class="relative w-full md:w-64">
                                <input type="text" wire:model.live="kitabSearch" placeholder="Cari nama kelas..." class="w-full pl-9 pr-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-100">
                                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        </div>

                        {{-- Table Display --}}
                        <div class="overflow-x-auto border border-slate-200/60 dark:border-slate-800 rounded-2xl">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 font-bold uppercase text-[10px] border-b border-slate-200 dark:border-slate-700">
                                    <tr>
                                        <th class="p-3.5">Jenjang</th>
                                        <th class="p-3.5">Nama Kelas Madrasah</th>
                                        <th class="p-3.5 text-right">Nominal Tarif Paket Kitab (Rp)</th>
                                        <th class="p-3.5 text-center">Aksi Simpan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                                    @php
                                        $filteredKitab = array_filter($kitabPrices, function($item) {
                                            $filter  = strtolower($this->kitabJenjangFilter);
                                            $jenjang = strtolower($item['jenjang']);
                                            $name    = strtolower($item['kelas_name']);

                                            $matchJenjang = true;
                                            if (!empty($filter)) {
                                                if ($filter === 'awaliyah' || $filter === 'ula') {
                                                    $matchJenjang = ($jenjang === 'ula' || $jenjang === 'awaliyah' || str_contains($name, 'awaliyah') || str_contains($name, 'ula'));
                                                } else {
                                                    $matchJenjang = ($jenjang === $filter || str_contains($name, $filter));
                                                }
                                            }

                                            $matchSearch = empty($this->kitabSearch) || str_contains($name, strtolower($this->kitabSearch));

                                            return $matchJenjang && $matchSearch;
                                        });
                                    @endphp

                                    @forelse($filteredKitab as $kelasId => $kData)
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                                            <td class="p-3.5">
                                                <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                                    {{ ($kData['jenjang'] === 'ULA' || $kData['jenjang'] === 'AWALIYAH') ? 'AWALIYAH' : $kData['jenjang'] }}
                                                </span>
                                            </td>
                                            <td class="p-3.5 font-extrabold text-slate-900 dark:text-slate-100 text-sm">
                                                {{ $kData['kelas_name'] }}
                                            </td>
                                            <td class="p-3.5 text-right">
                                                <div class="inline-flex items-center gap-2 justify-end">
                                                    <span class="text-slate-400 font-bold text-xs">Rp</span>
                                                    <input type="number" wire:model="kitabPrices.{{ $kelasId }}.amount"
                                                        class="w-36 px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono font-black text-emerald-600 dark:text-emerald-400 text-right">
                                                </div>
                                            </td>
                                            <td class="p-3.5 text-center">
                                                <button type="button" wire:click="saveKitabPrice('{{ $kelasId }}')"
                                                    class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-xl text-xs shadow transition-all">
                                                    Simpan Tarif
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-8 text-center text-slate-400">Tidak ada data kelas madrasah yang sesuai filter pencarian.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            {{-- MODAL EDIT / TAMBAH ITEM TARIF --}}
            @if($showItemModal)
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md">
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                            <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">
                                {{ $editingItemId ? 'Edit Item Tarif Registrasi' : 'Tambah Item Tarif Baru' }}
                            </h3>
                            <button type="button" wire:click="$set('showItemModal', false)" class="text-slate-400 hover:text-slate-600">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Nama / Label Item Tarif</label>
                                <input type="text" wire:model="itemLabel" placeholder="Contoh: Seragam Khusus Putri" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Nominal Harga (Rp)</label>
                                <input type="number" wire:model="itemAmount" placeholder="150000" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-mono text-slate-800 dark:text-slate-100">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Target Gender</label>
                                    <select wire:model="itemGender" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                                        <option value="ALL">Semua Gender</option>
                                        <option value="L">Putra (L) Saja</option>
                                        <option value="P">Putri (P) Saja</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Target Keberadaan</label>
                                    <select wire:model="itemResidence" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                                        <option value="ALL">Mukim &amp; Laju</option>
                                        <option value="mukim">Mukim Saja</option>
                                        <option value="laju">Laju Saja</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Kategori Item</label>
                                <select wire:model="itemCategory" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                                    <option value="dasar">Dasar Pendaftaran</option>
                                    <option value="fasilitas">Fasilitas Asrama &amp; Kitab Pegangan</option>
                                    <option value="seragam">Seragam Pondok</option>
                                    <option value="katering">Katering / Majek</option>
                                    <option value="bangunan">Sumbangan Pembangunan</option>
                                    <option value="administrasi">Administrasi (KTS)</option>
                                    <option value="kitab">Kitab Madrasah</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" wire:click="$set('showItemModal', false)" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
                            <button type="button" wire:click="saveItem" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow">Simpan Item Tarif</button>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- Live Preview Generator Modal -->
        @if($showGeneratePreviewModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl space-y-6 animate-in fade-in zoom-in-95 duration-150">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-2xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white uppercase tracking-wider font-serif-display">Pratinjau Simulasi Tagihan</h3>
                                <p class="text-xs text-slate-400">Konfirmasi rincian sebelum tagihan resmi diterbitkan ke kasir & wali santri.</p>
                            </div>
                        </div>
                        <button type="button" wire:click="$set('showGeneratePreviewModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Simulation Stats Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-3.5 bg-slate-50 dark:bg-slate-950/60 border border-slate-200/60 dark:border-slate-800 rounded-2xl">
                            <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Target Santri</span>
                            <span class="text-lg font-black text-slate-900 dark:text-white mt-1 block">
                                {{ number_format($previewGenStats['student_count'] ?? 0, 0, ',', '.') }} Santri
                            </span>
                            <span class="text-[10px] font-semibold text-slate-400 block mt-0.5">Filter: {{ ucfirst($previewGenStats['target_type'] ?? 'all') }}</span>
                        </div>

                        <div class="p-3.5 bg-slate-50 dark:bg-slate-950/60 border border-slate-200/60 dark:border-slate-800 rounded-2xl">
                            <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Lembar Tagihan</span>
                            <span class="text-lg font-black text-indigo-600 dark:text-indigo-400 mt-1 block">
                                {{ number_format($previewGenStats['total_bills'] ?? 0, 0, ',', '.') }} Lembar
                            </span>
                            <span class="text-[10px] font-semibold text-slate-400 block mt-0.5">{{ $previewGenStats['student_count'] ?? 0 }} × {{ $previewGenStats['period_count'] ?? 1 }} Periode</span>
                        </div>

                        <div class="p-3.5 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl">
                            <span class="block text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Total Nilai Tagihan</span>
                            <span class="text-lg font-black text-emerald-700 dark:text-emerald-300 mt-1 block">
                                Rp {{ number_format($previewGenStats['total_amount'] ?? 0, 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] font-semibold text-emerald-600/80 dark:text-emerald-400/80 block mt-0.5">Rp {{ number_format($previewGenStats['config_amount'] ?? 0, 0, ',', '.') }} / lembar</span>
                        </div>
                    </div>

                    <!-- Simulation Visual Card (Tampilan di Kasir / Lembar Kolektif) -->
                    <div class="space-y-2">
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Simulasi Gambaran Tagihan (Tampilan Kasir & Kolektif)</label>
                        <div class="p-4 bg-slate-50/80 dark:bg-slate-950/40 border border-slate-200/80 dark:border-slate-800 rounded-2xl max-h-48 overflow-y-auto space-y-2">
                            @foreach(($previewGenStats['periods'] ?? []) as $prd)
                                <div class="flex items-center justify-between p-3 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-xl text-xs">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                        <div>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 block">{{ $previewGenStats['config_label'] ?? '' }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $prd['label'] }} {{ $previewGenStats['gen_year'] ?? '' }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-extrabold text-slate-900 dark:text-slate-100 block">Rp {{ number_format($previewGenStats['config_amount'] ?? 0, 0, ',', '.') }}</span>
                                        <span class="px-2 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[9px] font-black rounded-md uppercase">Belum Dibayar</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('showGeneratePreviewModal', false)" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all">
                            Batal
                        </button>

                        <button type="button" wire:click="confirmGenerateBills" class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Ya, Terbitkan Tagihan Sekarang</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Delete Unpaid Bills Modal -->
        @if($showDeleteUnpaidModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl space-y-6 animate-in fade-in zoom-in-95 duration-150">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-rose-100 dark:border-rose-950/60 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded-2xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white uppercase tracking-wider font-serif-display">Konfirmasi Hapus Tagihan</h3>
                                <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold mt-0.5">Pembersihan Massal Tagihan Belum Lunas</p>
                            </div>
                        </div>
                        <button type="button" wire:click="$set('showDeleteUnpaidModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Simulation Stats Grid -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3.5 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 rounded-2xl">
                            <span class="block text-[10px] font-extrabold text-rose-700 dark:text-rose-300 uppercase tracking-wider">Akan Dihapus (Unpaid)</span>
                            <span class="text-xl font-black text-rose-600 dark:text-rose-400 mt-1 block">
                                {{ number_format($deleteUnpaidStats['unpaid_count'] ?? 0, 0, ',', '.') }} Lembar
                            </span>
                            <span class="text-[10px] font-semibold text-rose-600/80 block mt-0.5">Belum ada pembayaran</span>
                        </div>

                        <div class="p-3.5 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl">
                            <span class="block text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Terlindungi & Aman</span>
                            <span class="text-xl font-black text-emerald-700 dark:text-emerald-300 mt-1 block">
                                {{ number_format($deleteUnpaidStats['paid_count'] ?? 0, 0, ',', '.') }} Lembar
                            </span>
                            <span class="text-[10px] font-semibold text-emerald-600/80 dark:text-emerald-400/80 block mt-0.5">Sudah lunas / dicicil</span>
                        </div>
                    </div>

                    <!-- Warning Notice Box -->
                    <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 rounded-2xl space-y-2">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <div class="text-xs text-slate-700 dark:text-slate-300 space-y-1">
                                <span class="font-extrabold block text-amber-800 dark:text-amber-300">Penting Diketahui:</span>
                                <p class="leading-relaxed">
                                    Tindakan ini hanya menghapus lembar tagihan yang <strong>belum pernah dibayar</strong> untuk tarif <strong>'{{ $deleteUnpaidStats['config_label'] ?? '' }}'</strong>.
                                </p>
                                <p class="leading-relaxed text-[11px] text-slate-500 dark:text-slate-400 pt-1 border-t border-amber-200/60 dark:border-amber-900/40">
                                    💡 Jika Anda ingin menghapus seluruh tagihan secara bersih (termasuk yang lunas), silakan minta <strong>Super Admin</strong> untuk melakukannya demi keamanan data & audit pembukuan.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="$set('showDeleteUnpaidModal', false)" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all">
                            Batal
                        </button>

                        <button type="button" wire:click="confirmDeleteUnpaidBills" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Ya, Hapus {{ $deleteUnpaidStats['unpaid_count'] ?? 0 }} Tagihan Unpaid</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Kasir Wizard: Buka Tagihan Di Muka / Susulan -->
        @if($showKasirAddBillModal && $selectedSantri)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/70 backdrop-blur-sm">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden max-h-[90vh] flex flex-col">

                    {{-- ===== MODAL HEADER ===== --}}
                    <div class="flex items-center justify-between px-4 sm:px-6 pt-4 sm:pt-5 pb-3 sm:pb-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-2xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Buka Tagihan Di Muka / Susulan</h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">Untuk: <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $selectedSantri->name }}</span></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            {{-- Step indicator --}}
                            <div class="flex items-center gap-1.5 text-[10px] font-bold">
                                <span class="px-2.5 py-1 rounded-full {{ $kasirWizardStep === 1 ? 'bg-emerald-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-500' }}">1 Tarif</span>
                                <span class="text-slate-400">›</span>
                                <span class="px-2.5 py-1 rounded-full {{ $kasirWizardStep === 2 ? 'bg-emerald-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-500' }}">2 Periode</span>
                            </div>
                            <button type="button" wire:click="$set('showKasirAddBillModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- ===== STEP 1: PILIH TARIF ===== --}}
                    @if($kasirWizardStep === 1)
                        <div class="px-6 py-4 space-y-3 overflow-y-auto">
                            <p class="text-[11px] text-slate-500 font-semibold">Pilih tarif iuran yang ingin dibuka untuk santri ini. Tarif dengan ikon <span class="text-amber-500 font-bold">⚠</span> berarti santri ini bukan target utama tarif tersebut, namun Anda tetap bisa membukanya.</p>

                            <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                                @foreach($activeConfigs as $cfg)
                                    @php
                                        $isSelected = $kasirAddConfigId === $cfg->id;
                                    @endphp
                                    <button type="button"
                                        wire:click="$set('kasirAddConfigId', '{{ $cfg->id }}')"
                                        class="w-full text-left flex items-center justify-between gap-3 p-3.5 rounded-2xl border transition-all
                                            {{ $isSelected
                                                ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/30'
                                                : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 hover:border-slate-400 dark:hover:border-slate-500' }}">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="shrink-0 w-8 h-8 rounded-xl flex items-center justify-center {{ $isSelected ? 'bg-emerald-500 text-white' : 'bg-white dark:bg-slate-700 text-slate-400' }} border border-slate-200 dark:border-slate-600">
                                                @if($isSelected)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <span class="block text-xs font-bold text-slate-900 dark:text-white truncate">{{ $cfg->label }}</span>
                                                <span class="block text-[10px] text-slate-400 font-semibold">Rp {{ number_format($cfg->amount, 0, ',', '.') }} / {{ strtoupper($cfg->interval) }}</span>
                                            </div>
                                        </div>
                                        @if($isSelected && !$kasirSantriIsInTarget)
                                            <span class="shrink-0 inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-amber-100 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-700 text-amber-700 dark:text-amber-300 text-[10px] font-extrabold">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                Bukan Target
                                            </span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>

                            @if($kasirAddConfigId && !$kasirSantriIsInTarget)
                                <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 rounded-xl text-[11px] text-amber-800 dark:text-amber-300 font-semibold leading-relaxed">
                                    <svg class="w-3.5 h-3.5 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Perhatian: Santri ini <strong>bukan target</strong> tarif terpilih. Kemungkinan data komplek/kelas belum diperbarui. Anda tetap bisa melanjutkan jika yakin.
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 dark:border-slate-800 shrink-0">
                            <button type="button" wire:click="$set('showKasirAddBillModal', false)" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all">
                                Batal
                            </button>
                            <button type="button" wire:click="kasirGoToStep2" @disabled(!$kasirAddConfigId)
                                class="px-6 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-2
                                    {{ $kasirAddConfigId ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-400 cursor-not-allowed' }}">
                                Pilih Periode
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    @endif

                    {{-- ===== STEP 2: PILIH PERIODE ===== --}}
                    @if($kasirWizardStep === 2)
                        @php
                            $selectedCfg = $activeConfigs->firstWhere('id', $kasirAddConfigId);
                            $hasAnyAvailable = collect($kasirAvailablePeriods)->where('exists', false)->isNotEmpty();
                        @endphp
                        <div class="px-6 py-4 space-y-3 overflow-y-auto">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Tarif Terpilih</span>
                                    <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $selectedCfg?->label }}</p>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1 rounded-xl">
                                    {{ count($kasirSelectedPeriods) }} periode dipilih
                                </span>
                            </div>

                            {{-- Year Selector --}}
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 rounded-2xl flex items-center justify-between gap-3">
                                <div>
                                    <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider block">Target Tahun Periode</span>
                                    <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200">
                                        {{ $kasirSelectedYear == now()->year ? '🟢 Tahun Ini (' . $kasirSelectedYear . ')' : ($kasirSelectedYear < now()->year ? '⏮ Lampau (' . $kasirSelectedYear . ')' : '⏭ Masa Depan (' . $kasirSelectedYear . ')') }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase hidden sm:inline">Pilih Tahun:</label>
                                    <select wire:model.live="kasirSelectedYear" class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-100 rounded-xl px-3 py-1.5 text-xs font-bold focus:ring-emerald-500">
                                        @php $currentY = (int) now()->format('Y'); @endphp
                                        @for($y = $currentY - 3; $y <= $currentY + 2; $y++)
                                            <option value="{{ $y }}">
                                                Tahun {{ $y }} {{ $y === $currentY ? '(Tahun Ini)' : ($y < $currentY ? '(Lampau)' : '(Masa Depan)') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            {{-- Quick Presets & Smart Range Hint --}}
                            <div class="space-y-1.5">
                                <div class="flex flex-wrap items-center justify-between gap-1.5 p-2 bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl text-[10px]">
                                    <span class="font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider px-1">Pilih Cepat:</span>
                                    <div class="flex flex-wrap items-center gap-1">
                                        <button type="button" wire:click="selectAllKasirPeriods"
                                            class="px-2 py-1 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 rounded-lg font-bold transition-all">
                                            ⚡ Full 1 Tahun
                                        </button>
                                        <button type="button" wire:click="selectUpToCurrentKasirPeriods"
                                            class="px-2 py-1 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 border border-indigo-500/30 rounded-lg font-bold transition-all">
                                            📅 s.d. Bulan Ini
                                        </button>
                                        <button type="button" wire:click="selectSemester1KasirPeriods"
                                            class="px-2 py-1 bg-slate-200/60 dark:bg-slate-700/60 hover:bg-slate-300 text-slate-700 dark:text-slate-300 rounded-lg font-bold transition-all">
                                            🌗 Sem 1
                                        </button>
                                        <button type="button" wire:click="selectSemester2KasirPeriods"
                                            class="px-2 py-1 bg-slate-200/60 dark:bg-slate-700/60 hover:bg-slate-300 text-slate-700 dark:text-slate-300 rounded-lg font-bold transition-all">
                                            🌘 Sem 2
                                        </button>
                                        <button type="button" wire:click="clearKasirPeriods"
                                            class="px-2 py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20 rounded-lg font-bold transition-all">
                                            🧹 Reset
                                        </button>
                                    </div>
                                </div>
                                <p class="text-[9px] text-slate-400 italic px-1">
                                    💡 <strong>Tips Rentang Otomatis:</strong> Klik bulan awal (misal: <em>Januari</em>), lalu klik bulan akhir (misal: <em>Desember</em>) ➔ Rentang bulan otomatis tercentang.
                                </p>
                            </div>

                            {{-- 3-Column Touch Grid for Months --}}
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-48 overflow-y-auto pr-1">
                                @forelse($kasirAvailablePeriods as $idx => $period)
                                    @php $checked = in_array($idx, $kasirSelectedPeriods); @endphp
                                    <button type="button"
                                        wire:click="{{ $period['exists'] ? '' : 'toggleKasirPeriod(' . $idx . ')' }}"
                                        @class([
                                            'text-left flex items-center justify-between gap-1.5 p-2 rounded-xl border transition-all',
                                            'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/30' => $checked && !$period['exists'],
                                            'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 hover:border-emerald-400' => !$checked && !$period['exists'],
                                            'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/30 opacity-50 cursor-not-allowed' => $period['exists'],
                                        ])>
                                        <div class="flex items-center gap-2 min-w-0">
                                            <div @class([
                                                'w-4 h-4 rounded border-2 flex items-center justify-center shrink-0',
                                                'border-emerald-500 bg-emerald-500' => $checked && !$period['exists'],
                                                'border-slate-300 dark:border-slate-600' => !$checked && !$period['exists'],
                                                'border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800' => $period['exists'],
                                            ])>
                                                @if($checked && !$period['exists'])
                                                    <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                @endif
                                            </div>
                                            <span class="text-[11px] font-bold truncate {{ $period['exists'] ? 'text-slate-400 line-through' : 'text-slate-800 dark:text-slate-200' }}">
                                                {{ $period['label'] }}
                                            </span>
                                        </div>
                                        @if($period['exists'])
                                            <span class="shrink-0 text-[9px] font-extrabold text-emerald-600 dark:text-emerald-400">
                                                ✓ Ada
                                            </span>
                                        @endif
                                    </button>
                                @empty
                                    <div class="col-span-full text-center py-6 text-slate-400 text-xs">Tidak ada periode tersedia.</div>
                                @endforelse
                            </div>

                            @if(!$hasAnyAvailable)
                                <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 rounded-xl text-[10px] text-emerald-800 dark:text-emerald-300 font-semibold">
                                    Semua periode dalam daftar ini sudah ada tagihannya untuk santri ini. Tidak ada yang perlu dibuka.
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-between gap-3 px-6 py-4 border-t border-slate-100 dark:border-slate-800 shrink-0">
                            <button type="button" wire:click="kasirGoBackToStep1" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                Ganti Tarif
                            </button>
                            <div class="flex gap-2">
                                <button type="button" wire:click="$set('showKasirAddBillModal', false)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all">
                                    Batal
                                </button>
                                <button type="button" wire:click="generateFutureBillForSelectedSantri"
                                    @disabled(empty($kasirSelectedPeriods))
                                    class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-2
                                        {{ !empty($kasirSelectedPeriods) ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-400 cursor-not-allowed' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    Buka {{ count($kasirSelectedPeriods) > 0 ? count($kasirSelectedPeriods) . ' ' : '' }}Tagihan
                                </button>
                            </div>
                        </div>
                    @endif

        @endif
    @endif

    {{-- ========================================== --}}
    {{-- MODAL KONFIRMASI HAPUS GLOBAL & INDAH     --}}
    {{-- ========================================== --}}
    @if($showDeleteConfirmModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 border border-rose-200 dark:border-rose-900/40 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-6 animate-in fade-in zoom-in-95 duration-150 text-center">
                
                <!-- Professional SVG Warning Icon -->
                <div class="mx-auto w-16 h-16 bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded-3xl flex items-center justify-center border border-rose-500/20">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>

                <!-- Modal Title & Warning Body -->
                <div class="space-y-2">
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white uppercase tracking-wider font-serif-display">
                        {{ $deleteConfirmData['title'] ?? 'Konfirmasi Pembatalan Tagihan' }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        {{ $deleteConfirmData['warning'] ?? 'Apakah Anda yakin ingin membatalkan & menghapus tagihan ini dari database?' }}
                    </p>
                </div>

                <!-- Target Detail Box -->
                <div class="p-4 bg-rose-50/60 dark:bg-rose-950/20 border border-rose-200/60 dark:border-rose-900/30 rounded-2xl text-left space-y-2 text-xs">
                    @if($deleteType === 'individual')
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Nama Santri</span>
                            <span class="font-extrabold text-slate-900 dark:text-slate-100">{{ $deleteConfirmData['santri_name'] ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase">NIS</span>
                            <span class="font-bold font-mono text-slate-700 dark:text-slate-300">{{ $deleteConfirmData['nis'] ?? '-' }}</span>
                        </div>
                    @else
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Jumlah Tagihan Unpaid</span>
                            <span class="font-extrabold text-rose-600 dark:text-rose-400">{{ number_format($deleteConfirmData['count'] ?? 0, 0, ',', '.') }} Lembar</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Nama Tarif</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $deleteConfirmData['config_label'] ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Periode</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $deleteConfirmData['period_label'] ?? '-' }}</span>
                    </div>
                    @if(isset($deleteConfirmData['amount']) || isset($deleteConfirmData['total_amount']))
                        <div class="flex justify-between items-center pt-2 border-t border-rose-200/50 dark:border-rose-900/40">
                            <span class="text-[10px] font-extrabold text-rose-700 dark:text-rose-300 uppercase">Total Nominal</span>
                            <span class="font-black text-rose-700 dark:text-rose-300">Rp {{ number_format($deleteConfirmData['amount'] ?? ($deleteConfirmData['total_amount'] ?? 0), 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showDeleteConfirmModal', false)"
                        class="w-1/2 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all">
                        Batal
                    </button>
                    <button type="button" wire:click="executeConfirmedDeletion"
                        class="w-1/2 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-all shadow-md inline-flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Ya, Hapus Tagihan
                    </button>
                </div>

            </div>
        </div>
    @endif

    {{-- ==================================================== --}}
    {{-- MODAL KONFIRMASI TINDAKAN TARIF (NONAKTIFKAN/HAPUS) --}}
    {{-- ==================================================== --}}
    @if($showTariffActionModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-6 animate-in fade-in zoom-in-95 duration-150 text-center">
                
                <div class="mx-auto w-16 h-16 bg-{{ $tariffActionData['button_color'] ?? 'emerald' }}-500/10 text-{{ $tariffActionData['button_color'] ?? 'emerald' }}-600 dark:text-{{ $tariffActionData['button_color'] ?? 'emerald' }}-400 rounded-3xl flex items-center justify-center border border-{{ $tariffActionData['button_color'] ?? 'emerald' }}-500/20">
                    @if(($tariffActionData['button_color'] ?? '') === 'rose')
                        <svg class="w-8 h-8 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    @elseif(($tariffActionData['button_color'] ?? '') === 'amber')
                        <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    @else
                        <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>

                <div class="space-y-2">
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white uppercase tracking-wider font-serif-display">
                        {{ $tariffActionData['title'] ?? 'Konfirmasi Tindakan Tarif' }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        {{ $tariffActionData['message'] ?? 'Apakah Anda yakin ingin memproses tindakan ini?' }}
                    </p>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/50 rounded-2xl text-left space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Nama Tarif</span>
                        <span class="font-extrabold text-slate-900 dark:text-slate-100">{{ $tariffActionData['label'] ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Nominal Tarif</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">Rp {{ number_format($tariffActionData['amount'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Status Saat Ini</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $tariffActionData['status_now'] ?? 'Aktif' }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showTariffActionModal', false)"
                        class="w-1/2 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all">
                        Batal
                    </button>
                    <button type="button" wire:click="executeConfirmedTariffAction"
                        class="w-1/2 py-2.5 {{ ($tariffActionData['button_color'] ?? '') === 'rose' ? 'bg-rose-600 hover:bg-rose-700' : (($tariffActionData['button_color'] ?? '') === 'amber' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-600 hover:bg-emerald-700') }} text-white rounded-xl text-xs font-bold transition-all shadow-md inline-flex items-center justify-center gap-1.5">
                        <span>{{ $tariffActionData['button_text'] ?? 'Ya, Lanjutkan' }}</span>
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- MODAL BEAUTIFUL VOID CONFIRMATION -->
    @if($showVoidModal && $paymentToVoidData)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 flex items-center justify-center p-4 animate-fade-in" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Backdrop Click Overlay (No heavy blur) -->
            <div wire:click="closeVoidModal" class="fixed inset-0"></div>

            <!-- Modal Container -->
            <div class="relative bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all max-w-lg w-full border border-slate-200 dark:border-slate-800 animate-scale-up z-10">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-rose-500/10 via-rose-500/5 to-transparent border-b border-rose-500/20 px-6 py-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl shrink-0 font-extrabold shadow-xs">
                        ⚠️
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white uppercase tracking-wider font-serif-display">Konfirmasi Void Pembayaran</h3>
                        <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold">Tindakan ini akan membatalkan pencatatan setoran</p>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-5">
                    <!-- Detail Ringkasan Pembayaran -->
                    <div class="bg-slate-50 dark:bg-slate-950/60 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-200/60 dark:border-slate-800/60 pb-2.5">
                            <span class="text-xs text-slate-400 font-medium">Santri</span>
                            <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $paymentToVoidData['santri_name'] }} <span class="text-[10px] text-slate-400 font-normal">(NIS: {{ $paymentToVoidData['santri_nis'] }})</span></span>
                        </div>
                        <div class="flex items-center justify-between border-b border-slate-200/60 dark:border-slate-800/60 pb-2.5">
                            <span class="text-xs text-slate-400 font-medium">Jenis Iuran</span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $paymentToVoidData['config_label'] }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-slate-200/60 dark:border-slate-800/60 pb-2.5">
                            <span class="text-xs text-slate-400 font-medium">Periode Tagihan</span>
                            <span class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400">📅 {{ $paymentToVoidData['period_label'] }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-slate-200/60 dark:border-slate-800/60 pb-2.5">
                            <span class="text-xs text-slate-400 font-medium">Metode & Waktu</span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $paymentToVoidData['payment_method'] }} • {{ $paymentToVoidData['payment_date'] }} ({{ $paymentToVoidData['created_at'] }})</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-slate-200/60 dark:border-slate-800/60 pb-2.5">
                            <span class="text-xs text-slate-400 font-medium">Petugas Input</span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">👤 {{ $paymentToVoidData['logger_name'] }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <span class="text-xs text-slate-400 font-medium">Nominal Setor</span>
                            <span class="text-base font-extrabold text-rose-600 dark:text-rose-400">Rp {{ number_format($paymentToVoidData['amount_paid'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Warning Callout Box -->
                    <div class="bg-amber-500/10 border border-amber-500/30 text-amber-800 dark:text-amber-300 p-3.5 rounded-2xl text-xs font-medium leading-relaxed flex items-start gap-3">
                        <span class="text-lg">📢</span>
                        <div>
                            <strong class="font-bold block text-amber-900 dark:text-amber-200">Dampak Pembatalan:</strong>
                            Nominal <strong class="font-bold text-rose-600 dark:text-rose-400">Rp {{ number_format($paymentToVoidData['amount_paid'], 0, ',', '.') }}</strong> akan ditarik dari laporan kasir, dan status tagihan santri akan dikembalikan menjadi belum bayar / sisa tunggakan bertambah kembali.
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                    <button type="button" wire:click="closeVoidModal"
                        class="px-5 py-2.5 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold transition-all">
                        Kembali / Batal
                    </button>
                    <button type="button" wire:click="executeVoidPayment" wire:loading.attr="disabled"
                        class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-extrabold shadow-lg shadow-rose-500/20 transition-all flex items-center gap-2">
                        <span wire:loading.remove wire:target="executeVoidPayment">🗑️ Ya, Batalkan (Void)</span>
                        <span wire:loading wire:target="executeVoidPayment" class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Memproses Void...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
</div>
</div>
</div>

