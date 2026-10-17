<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Dana Talangan Santri
                </span>
                @if($userGenderScope)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $userGenderScope === 'L' ? 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/50 dark:text-blue-300' : 'bg-pink-50 text-pink-700 border border-pink-200 dark:bg-pink-950/50 dark:text-pink-300' }}">
                        {{ $userGenderScope === 'L' ? 'Khusus Santri Putra' : 'Khusus Santri Putri' }}
                    </span>
                @endif
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Buku Pinjaman &amp; Talangan Santri</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Pencatatan uang bekal talangan santri (kiriman telat), kontrol tayang portal wali, dan mutasi pelunasan.</p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            @can('manage-pinjaman-santri')
                <button type="button" 
                        wire:click="openCreateModal"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-md shadow-emerald-600/20 active:scale-95 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Catat Pinjaman Baru</span>
                </button>
            @endcan
        </div>
    </div>

    <!-- Alert Notifikasi Berhasil (Unmissable Banner) -->
    @if($successNotification)
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 -translate-y-2 scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 scale-100" 
             class="p-4 sm:p-5 rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white shadow-xl shadow-emerald-600/20 flex items-center justify-between gap-3 border border-emerald-400/30">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-white/20 backdrop-blur-xs flex items-center justify-center shrink-0 border border-white/30">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-black tracking-wide flex items-center gap-2">
                        <span>BERHASIL DISIMPAN!</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-ping"></span>
                    </h4>
                    <p class="text-xs text-emerald-100 font-medium mt-0.5 leading-relaxed">{{ $successNotification }}</p>
                </div>
            </div>
            <button type="button" 
                    @click="show = false; $wire.set('successNotification', null)" 
                    class="p-2 text-white/80 hover:text-white rounded-xl hover:bg-white/10 transition-colors shrink-0" 
                    title="Tutup Notifikasi">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Total Piutang Aktif -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 border border-rose-200 dark:border-rose-900">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Belum Kembali</span>
                <span class="text-xl font-black text-rose-600 dark:text-rose-400">Rp {{ number_format($totalActiveDebt, 0, ',', '.') }}</span>
                <span class="text-[11px] text-slate-500 block mt-0.5">Dari dana kas pondok yang terpakai</span>
            </div>
        </div>

        <!-- Santri Meminjam Aktif -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 border border-amber-200 dark:border-amber-900">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Santri Meminjam</span>
                <span class="text-xl font-black text-slate-900 dark:text-white">{{ $totalActiveSantri }} <span class="text-sm font-semibold text-slate-500">Santri</span></span>
                <span class="text-[11px] text-slate-500 block mt-0.5">Memiliki pinjaman belum lunas</span>
            </div>
        </div>

        <!-- Total Lunas Bulan Ini -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-200 dark:border-emerald-900">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Dilunasi Bulan Ini</span>
                <span class="text-xl font-black text-emerald-600 dark:text-emerald-400">Rp {{ number_format($totalPaidThisMonth, 0, ',', '.') }}</span>
                <span class="text-[11px] text-slate-500 block mt-0.5">Mutasi balik ke kas pondok</span>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <!-- Status Tabs -->
            <div class="flex items-center gap-1 overflow-x-auto p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl text-xs font-bold">
                <button type="button" wire:click="setStatusFilter('active')" 
                        class="px-3 py-1.5 rounded-xl transition-all {{ $statusFilter === 'active' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">
                    Aktif (Belum Lunas)
                </button>
                <button type="button" wire:click="setStatusFilter('all')" 
                        class="px-3 py-1.5 rounded-xl transition-all {{ $statusFilter === 'all' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">
                    Semua
                </button>
                <button type="button" wire:click="setStatusFilter('unpaid')" 
                        class="px-3 py-1.5 rounded-xl transition-all {{ $statusFilter === 'unpaid' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">
                    Belum Dicicil
                </button>
                <button type="button" wire:click="setStatusFilter('partial')" 
                        class="px-3 py-1.5 rounded-xl transition-all {{ $statusFilter === 'partial' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">
                    Sebagian (Cicilan)
                </button>
                <button type="button" wire:click="setStatusFilter('paid')" 
                        class="px-3 py-1.5 rounded-xl transition-all {{ $statusFilter === 'paid' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">
                    Lunas
                </button>
            </div>

            <!-- Gender Filter (Only for Unscoped Users) -->
            @if(!$userGenderScope)
                <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl text-xs font-bold shrink-0">
                    <button type="button" wire:click="setGenderFilter('')"
                            class="px-3 py-1.5 rounded-xl transition-all {{ $genderFilter === '' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs' : 'text-slate-600 dark:text-slate-400' }}">
                        Semua Gender
                    </button>
                    <button type="button" wire:click="setGenderFilter('L')"
                            class="px-3 py-1.5 rounded-xl transition-all {{ $genderFilter === 'L' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400' }}">
                        Santri Putra
                    </button>
                    <button type="button" wire:click="setGenderFilter('P')"
                            class="px-3 py-1.5 rounded-xl transition-all {{ $genderFilter === 'P' ? 'bg-pink-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400' }}">
                        Santri Putri
                    </button>
                </div>
            @endif
        </div>

        <!-- Search Bar & Date Filter -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2 relative">
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       placeholder="Cari nama santri, NIS, kode pinjaman (PINJ-...), atau keperluan..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800 dark:text-slate-200">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <div class="flex items-center gap-2">
                <input type="date" 
                       wire:model.live="filterDateFrom" 
                       title="Tanggal Mulai"
                       class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <span class="text-xs text-slate-400">s/d</span>
                <input type="date" 
                       wire:model.live="filterDateTo" 
                       title="Tanggal Sampai"
                       class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-950/80 border-b border-slate-200 dark:border-slate-800 text-xs text-slate-500 uppercase tracking-wider font-bold">
                    <tr>
                        <th class="py-3.5 px-4">Santri &amp; Kode</th>
                        <th class="py-3.5 px-4">Tanggal Pinjam</th>
                        <th class="py-3.5 px-4">Keperluan</th>
                        <th class="py-3.5 px-4">Nominal Pinjam</th>
                        <th class="py-3.5 px-4">Sisa Belum Lunas</th>
                        <th class="py-3.5 px-4 text-center">Tayang Portal Wali</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($loans as $loan)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                            <!-- Santri & Kode -->
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-slate-900 dark:text-white flex items-center gap-1.5">
                                    <span>{{ $loan->person?->name ?? '-' }}</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded font-mono font-bold {{ $loan->gender === 'L' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-950 dark:text-pink-300' }}">
                                        {{ $loan->gender === 'L' ? 'Putra' : 'Putri' }}
                                    </span>
                                </div>
                                <div class="text-xs text-slate-400 font-mono flex items-center gap-2 mt-0.5">
                                    <span>{{ $loan->loan_code }}</span>
                                    @if($loan->person?->nis)
                                        <span>• NIS: {{ $loan->person->nis }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Tanggal Pinjam & Jatuh Tempo -->
                            <td class="py-3.5 px-4 text-xs">
                                <span class="font-bold text-slate-800 dark:text-slate-200 block">
                                    {{ $loan->borrowed_at?->translatedFormat('d M Y') ?? '-' }}
                                </span>
                                @if($loan->due_date)
                                    <span class="text-[11px] {{ $loan->due_date->lt(now()->startOfDay()) && $loan->status !== 'paid' ? 'text-rose-500 font-bold' : 'text-slate-400' }}">
                                        Tempo: {{ $loan->due_date->translatedFormat('d M Y') }}
                                    </span>
                                @endif
                            </td>

                            <!-- Keperluan -->
                            <td class="py-3.5 px-4 text-xs">
                                <span class="font-semibold text-slate-700 dark:text-slate-300 block truncate max-w-[200px]" title="{{ $loan->reason }}">
                                    {{ $loan->reason ?: '-' }}
                                </span>
                                <span class="text-[10px] text-slate-400 block truncate max-w-[200px]">
                                    Dicatat: {{ $loan->creator?->name ?? 'Sistem' }}
                                </span>
                            </td>

                            <!-- Nominal Pinjam -->
                            <td class="py-3.5 px-4">
                                <strong class="text-slate-900 dark:text-white font-mono text-sm">
                                    Rp {{ number_format($loan->amount, 0, ',', '.') }}
                                </strong>
                            </td>

                            <!-- Sisa Belum Lunas -->
                            <td class="py-3.5 px-4">
                                @if($loan->remaining_amount <= 0)
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Rp 0 (Lunas)</span>
                                @else
                                    <strong class="text-rose-600 dark:text-rose-400 font-mono text-sm block">
                                        Rp {{ number_format($loan->remaining_amount, 0, ',', '.') }}
                                    </strong>
                                    @if($loan->amount_paid > 0)
                                        <span class="text-[10px] text-slate-400 font-mono">
                                            (Dibayar: Rp {{ number_format($loan->amount_paid, 0, ',', '.') }})
                                        </span>
                                    @endif
                                @endif
                            </td>

                            <!-- Tayang Portal Wali -->
                            <td class="py-3.5 px-4 text-center">
                                @can('manage-pinjaman-santri')
                                    <button type="button" 
                                            wire:click="toggleGuardianVisibility('{{ $loan->id }}')"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold transition-all active:scale-95 {{ $loan->is_visible_to_guardian ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800' : 'bg-slate-100 text-slate-500 border border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700' }}"
                                            title="Klik untuk mengubah visibilitas portal wali">
                                        @if($loan->is_visible_to_guardian)
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Tampil di Wali</span>
                                        @else
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                            <span>Internal Saja</span>
                                        @endif
                                    </button>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $loan->is_visible_to_guardian ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $loan->is_visible_to_guardian ? 'Tampil di Wali' : 'Internal' }}
                                    </span>
                                @endcan
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4 text-center">
                                @if($loan->status === 'paid')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800">
                                        <svg class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        Lunas
                                    </span>
                                @elseif($loan->status === 'partial')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800">
                                        Cicilan
                                    </span>
                                @elseif($loan->status === 'cancelled')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700">
                                        Dibatalkan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800">
                                        Belum Lunas
                                    </span>
                                @endif
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @can('manage-pinjaman-santri')
                                        @if($loan->status !== 'paid' && $loan->status !== 'cancelled')
                                            <button type="button" 
                                                    wire:click="openPaymentModal('{{ $loan->id }}')"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800 font-bold text-xs transition-all active:scale-95"
                                                    title="Catat Pembayaran Tunai">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                <span>Lunasi</span>
                                            </button>
                                        @endif
                                    @endcan

                                    @can('manage-pinjaman-santri')
                                        @if($loan->status !== 'cancelled')
                                            <button type="button" 
                                                    wire:click="openEditModal('{{ $loan->id }}')"
                                                    class="p-1.5 rounded-xl text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/40 transition-colors"
                                                    title="Edit Data Pinjaman">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                        @endif
                                    @endcan

                                    <button type="button" 
                                            wire:click="openHistoryModal('{{ $loan->id }}')"
                                            class="p-1.5 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                            title="Lihat Riwayat &amp; Detail">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </button>

                                    @can('manage-pinjaman-santri')
                                        @if($loan->status === 'unpaid' && $loan->amount_paid == 0)
                                            <button type="button" 
                                                    wire:click="cancelLoan('{{ $loan->id }}')"
                                                    wire:confirm="Yakin ingin membatalkan pencatatan pinjaman ini?"
                                                    class="p-1.5 rounded-xl text-rose-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors"
                                                    title="Batalkan Pinjaman">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-700 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="block font-bold text-slate-600 dark:text-slate-300">Belum ada data pinjaman santri</span>
                                <span class="text-xs">Data pinjaman akan muncul di sini setelah dicatat.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($loans->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $loans->links() }}
            </div>
        @endif
    </div>

    <!-- ─── MODAL CATAT PINJAMAN BARU ────────────────────────────────────────── -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-xl overflow-hidden space-y-5 animate-in fade-in duration-200">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-900 dark:text-white">Catat Pinjaman / Talangan Baru</h2>
                        <p class="text-xs text-slate-500">Santri meminjam kas pondok untuk kebutuhan darurat / kiriman telat.</p>
                    </div>
                    <button type="button" wire:click="closeCreateModal" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="saveLoan" class="p-6 space-y-4">
                    <!-- Pilih Santri -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Pilih Santri Peminjam <span class="text-rose-500">*</span>
                        </label>

                        @if($selectedPersonId)
                            <div class="flex items-center justify-between p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800">
                                <div>
                                    <strong class="text-sm font-bold text-emerald-900 dark:text-emerald-200 block">{{ $selectedPersonName }}</strong>
                                    <span class="text-xs text-emerald-700 dark:text-emerald-400 font-mono">
                                        {{ $selectedPersonNIS ? 'NIS: '.$selectedPersonNIS : 'Santri Aktif' }} • {{ $selectedPersonGender === 'L' ? 'Putra' : 'Putri' }}
                                    </span>
                                </div>
                                <button type="button" wire:click="$set('selectedPersonId', '')" class="text-xs text-rose-600 font-bold hover:underline">
                                    Ganti
                                </button>
                            </div>
                        @else
                            <div class="relative">
                                <input type="text" 
                                       wire:model.live.debounce.300ms="personSearch"
                                       placeholder="Ketik minimal 2 huruf nama santri atau NIS..."
                                       class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800 dark:text-slate-200">
                                
                                @if($personSearchResults->isNotEmpty())
                                    <div class="absolute left-0 right-0 mt-1 max-h-56 overflow-y-auto bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl z-20 divide-y divide-slate-100 dark:divide-slate-800">
                                        @foreach($personSearchResults as $res)
                                            <button type="button" 
                                                    wire:click="selectPerson('{{ $res->id }}')"
                                                    class="w-full text-left p-3 hover:bg-slate-50 dark:hover:bg-slate-800 flex items-center justify-between transition-colors">
                                                <div>
                                                    <span class="text-sm font-bold text-slate-800 dark:text-slate-200 block">{{ $res->name }}</span>
                                                    <span class="text-xs text-slate-400 font-mono">NIS: {{ $res->nis ?? '-' }}</span>
                                                </div>
                                                <span class="text-[10px] px-2 py-0.5 rounded font-bold {{ $res->gender === 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                                    {{ $res->gender === 'L' ? 'Putra' : 'Putri' }}
                                                </span>
                                            </button>
                                        @endforeach
                                    </div>
                                @elseif(strlen(trim($personSearch)) >= 2)
                                    <div class="absolute left-0 right-0 mt-1 p-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl z-20 text-center text-xs text-slate-400">
                                        Santri tidak ditemukan (atau beda gender).
                                    </div>
                                @endif
                            </div>
                        @endif
                        @error('selectedPersonId') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Nominal Pinjaman (Fixed Input Group - Anti Tumpang Tindih) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Nominal Pinjaman <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden bg-slate-50 dark:bg-slate-950 focus-within:ring-2 focus-within:ring-emerald-500 focus-within:border-emerald-500">
                            <span class="inline-flex items-center px-4 bg-slate-200/70 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-black text-sm border-r border-slate-200 dark:border-slate-700 select-none">
                                Rp
                            </span>
                            <input type="number" 
                                   wire:model.live="amount" 
                                   placeholder="0"
                                   min="1"
                                   step="any"
                                   class="w-full px-4 py-2.5 bg-transparent border-0 text-base font-black focus:outline-none focus:ring-0 text-slate-900 dark:text-white">
                        </div>
                        @if($amount && is_numeric($amount) && (float)$amount > 0)
                            <div class="flex items-center gap-1.5 text-xs font-extrabold text-emerald-600 dark:text-emerald-400 pt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Terbilang: Rp {{ number_format((float)$amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @error('amount') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tanggal Pinjam & Jatuh Tempo -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Tanggal Pinjam <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" 
                                   wire:model="borrowedAt" 
                                   class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @error('borrowedAt') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Jatuh Tempo (Opsional)
                            </label>
                            <input type="date" 
                                   wire:model="dueDate" 
                                   class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @error('dueDate') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Alasan / Keperluan -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Keperluan / Alasan Pinjam
                        </label>
                        <input type="text" 
                               wire:model="reason" 
                               placeholder="Contoh: Kiriman orang tua telat, butuh uang jajan / beli kitab"
                               class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800 dark:text-slate-200">
                    </div>

                    <!-- Toggle Tampilkan ke Portal Wali (Interaktif Jelas & Responsif) -->
                    <div wire:click="$toggle('isVisibleToGuardian')"
                         class="cursor-pointer p-4 rounded-2xl border transition-all duration-200 flex items-center justify-between gap-3 select-none {{ $isVisibleToGuardian ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-700 shadow-xs ring-1 ring-emerald-400/40' : 'bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800' }}">
                        <div class="space-y-1 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-black {{ $isVisibleToGuardian ? 'text-emerald-900 dark:text-emerald-200' : 'text-slate-800 dark:text-slate-200' }}">
                                    Tampilkan di Dashboard Orang Tua?
                                </span>
                                @if($isVisibleToGuardian)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-600 text-white shadow-xs">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        Aktif (Tayang ke Wali)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                        Off (Internal Saja)
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs {{ $isVisibleToGuardian ? 'text-emerald-700 dark:text-emerald-300 font-medium' : 'text-slate-500' }}">
                                {{ $isVisibleToGuardian ? '✓ Tagihan talangan ini AKAN MUNCUL di Portal Wali sehingga orang tua bisa melihat rinciannya dan melunasi via transfer/gateway.' : 'Hanya dicatat di pembukuan internal pengurus pondok, tidak ditampilkan di Portal Wali.' }}
                            </p>
                        </div>

                        <!-- Modern Pill Toggle Knob -->
                        <div class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out {{ $isVisibleToGuardian ? 'bg-emerald-600' : 'bg-slate-300 dark:bg-slate-700' }}">
                            <span class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out {{ $isVisibleToGuardian ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="closeCreateModal" class="px-4 py-2.5 rounded-2xl text-slate-600 dark:text-slate-400 font-bold text-sm hover:bg-slate-100 dark:hover:bg-slate-800">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-md shadow-emerald-600/20 active:scale-95 transition-all">
                            Simpan Pinjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ─── MODAL KONFIRMASI SEBELUM SIMPAN PINJAMAN ──────────────────────────── -->
    @if($showConfirmCreateModal)
        <div class="fixed inset-0 z-[100] overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4" style="z-index: 9999;">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-md overflow-hidden p-6 space-y-4 animate-in fade-in scale-95 duration-150">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 border border-amber-200 dark:border-amber-800">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">Konfirmasi Simpan Pinjaman</h3>
                        <p class="text-xs text-slate-500">Pastikan data berikut sudah benar sebelum dicatat.</p>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-2.5 text-xs">
                    <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500 font-medium">Santri Peminjam:</span>
                        <strong class="text-slate-900 dark:text-white font-bold">{{ $selectedPersonName }}</strong>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500 font-medium">Nominal Pinjaman:</span>
                        <strong class="text-rose-600 dark:text-rose-400 font-mono text-sm font-black">Rp {{ number_format((float)$amount, 0, ',', '.') }}</strong>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500 font-medium">Tanggal Pinjam:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($borrowedAt)->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500 font-medium">Keperluan:</span>
                        <span class="font-medium text-slate-800 dark:text-slate-200 truncate max-w-[200px]">{{ $reason ?: 'Kiriman orang tua telat' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-500 font-medium">Tampil di Portal Wali:</span>
                        <span class="font-black {{ $isVisibleToGuardian ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500' }}">
                            {{ $isVisibleToGuardian ? '✓ Ya (Ditampilkan)' : '✕ Tidak (Internal Saja)' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" 
                            wire:click="$set('showConfirmCreateModal', false)" 
                            class="px-4 py-2.5 rounded-2xl text-slate-600 dark:text-slate-400 font-bold text-xs hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        Periksa Kembali
                    </button>
                    <button type="button" 
                            wire:click="executeSaveLoan" 
                            class="px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 active:scale-95 transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Ya, Simpan Pinjaman</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ─── MODAL CATAT PELUNASAN TUNAI (CASH DESK) ────────────────────────── -->
    @if($showPaymentModal && $selectedLoan)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-md overflow-hidden space-y-5 animate-in fade-in duration-200">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-900 dark:text-white">Pelunasan Dana Talangan</h2>
                        <p class="text-xs text-slate-500 font-mono">{{ $selectedLoan->loan_code }} • {{ $selectedLoan->person?->name }}</p>
                    </div>
                    <button type="button" wire:click="closePaymentModal" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="savePayment" class="p-6 space-y-4">
                    <!-- Info Box Pinjaman -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-1 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Total Pinjaman:</span>
                            <strong class="text-slate-800 dark:text-slate-200 font-mono">Rp {{ number_format($selectedLoan->amount, 0, ',', '.') }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Sudah Dikembalikan:</span>
                            <span class="text-emerald-600 dark:text-emerald-400 font-mono font-bold">Rp {{ number_format($selectedLoan->amount_paid, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-t border-slate-200 dark:border-slate-800 pt-1">
                            <span class="text-slate-500 font-bold">Sisa yang Harus Dibayar:</span>
                            <strong class="text-rose-600 dark:text-rose-400 font-mono text-sm font-black">Rp {{ number_format($selectedLoan->remaining_amount, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    <!-- Input Nominal Pembayaran (Fixed Input Group - Anti Tumpang Tindih) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Nominal Bayar / Cicil (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden bg-slate-50 dark:bg-slate-950 focus-within:ring-2 focus-within:ring-emerald-500 focus-within:border-emerald-500">
                            <span class="inline-flex items-center px-4 bg-slate-200/70 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-black text-sm border-r border-slate-200 dark:border-slate-700 select-none">
                                Rp
                            </span>
                            <input type="number" 
                                   wire:model.live="payAmount" 
                                   min="1"
                                   max="{{ $selectedLoan->remaining_amount }}"
                                   step="any"
                                   class="w-full px-4 py-2.5 bg-transparent border-0 text-base font-black focus:outline-none focus:ring-0 text-slate-900 dark:text-white">
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-500 pt-0.5">
                            <span>Bisa bayar penuh atau dicicil.</span>
                            <button type="button" wire:click="$set('payAmount', '{{ $selectedLoan->remaining_amount }}')" class="font-bold text-emerald-600 hover:underline">
                                Bayar Lunas Penuh
                            </button>
                        </div>
                        @if($payAmount && is_numeric($payAmount) && (float)$payAmount > 0)
                            <div class="flex items-center gap-1.5 text-xs font-extrabold text-emerald-600 dark:text-emerald-400 pt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Terbilang: Rp {{ number_format((float)$payAmount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @error('payAmount') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Catatan Pelunasan -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Catatan (Opsional)
                        </label>
                        <input type="text" 
                               wire:model="payNotes" 
                               placeholder="Contoh: Dititipkan lewat ustadz asrama / setor tunai"
                               class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800 dark:text-slate-200">
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="closePaymentModal" class="px-4 py-2.5 rounded-2xl text-slate-600 dark:text-slate-400 font-bold text-sm hover:bg-slate-100 dark:hover:bg-slate-800">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-md shadow-emerald-600/20 active:scale-95 transition-all">
                            Konfirmasi Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ─── MODAL KONFIRMASI SEBELUM PROSES PELUNASAN TUNAI ──────────────────── -->
    @if($showConfirmPaymentModal && $selectedLoan)
        <div class="fixed inset-0 z-[100] overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4" style="z-index: 9999;">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-md overflow-hidden p-6 space-y-4 animate-in fade-in scale-95 duration-150">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-200 dark:border-emerald-800">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">Konfirmasi Pelunasan Tunai</h3>
                        <p class="text-xs text-slate-500 font-mono">{{ $selectedLoan->loan_code }} • {{ $selectedLoan->person?->name }}</p>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-2 text-xs">
                    <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500 font-medium">Nominal Disetorkan:</span>
                        <strong class="text-emerald-600 dark:text-emerald-400 font-mono text-sm font-black">Rp {{ number_format((float)$payAmount, 0, ',', '.') }}</strong>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-500 font-medium">Sisa Setelah Pembayaran:</span>
                        <strong class="text-slate-800 dark:text-slate-200 font-mono">
                            Rp {{ number_format(max(0, $selectedLoan->remaining_amount - (float)$payAmount), 0, ',', '.') }}
                        </strong>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" 
                            wire:click="$set('showConfirmPaymentModal', false)" 
                            class="px-4 py-2.5 rounded-2xl text-slate-600 dark:text-slate-400 font-bold text-xs hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        Periksa Kembali
                    </button>
                    <button type="button" 
                            wire:click="executeSavePayment" 
                            class="px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 active:scale-95 transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Ya, Proses Pembayaran</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ─── MODAL RIWAYAT PEMBAYARAN & DETAIL ──────────────────────────────── -->
    @if($showHistoryModal && $viewingLoan)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-lg overflow-hidden space-y-4 animate-in fade-in duration-200">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-900 dark:text-white">Riwayat Mutasi Pinjaman</h2>
                        <p class="text-xs text-slate-500 font-mono">{{ $viewingLoan->loan_code }} • {{ $viewingLoan->person?->name }}</p>
                    </div>
                    <button type="button" wire:click="closeHistoryModal" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <!-- Ringkasan Info Pinjaman -->
                    <div class="grid grid-cols-2 gap-2 text-xs bg-slate-50 dark:bg-slate-950 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800">
                        <div>
                            <span class="text-slate-400 block">Total Nominal:</span>
                            <strong class="text-slate-900 dark:text-white font-mono text-sm">Rp {{ number_format($viewingLoan->amount, 0, ',', '.') }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Sisa Belum Lunas:</span>
                            <strong class="text-rose-600 dark:text-rose-400 font-mono text-sm">Rp {{ number_format($viewingLoan->remaining_amount, 0, ',', '.') }}</strong>
                        </div>
                        <div class="col-span-2 border-t border-slate-200 dark:border-slate-800 pt-2">
                            <span class="text-slate-400 block">Alasan / Catatan:</span>
                            <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $viewingLoan->reason ?: '-' }}</span>
                        </div>
                    </div>

                    <!-- Riwayat Pembayaran / Cicilan -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Riwayat Pembayaran</h4>
                        @if($viewingLoan->payments->isEmpty())
                            <div class="p-4 text-center text-xs text-slate-400 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-200 dark:border-slate-800">
                                Belum ada riwayat pembayaran / cicilan yang dicatat.
                            </div>
                        @else
                            <div class="space-y-2 max-h-56 overflow-y-auto">
                                @foreach($viewingLoan->payments as $pay)
                                    <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs">
                                        <div>
                                            <div class="flex items-center gap-1.5 font-bold text-slate-800 dark:text-slate-200">
                                                <span>Rp {{ number_format($pay->amount, 0, ',', '.') }}</span>
                                                <span class="text-[10px] px-1.5 py-0.2 rounded font-mono uppercase {{ $pay->payment_method === 'portal_wali' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                                    {{ $pay->payment_method === 'portal_wali' ? 'Portal Wali' : 'Tunai' }}
                                                </span>
                                            </div>
                                            <span class="text-[11px] text-slate-400 font-mono block mt-0.5">
                                                {{ $pay->payment_date?->translatedFormat('d M Y') }} • No: {{ $pay->receipt_no ?? $pay->payment_code }}
                                            </span>
                                        </div>
                                        <div class="text-right text-[11px] text-slate-500">
                                            <span>Oleh: {{ $pay->receiver?->name ?? 'Kasir' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Footer Action -->
                    <div class="flex justify-end pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="closeHistoryModal" class="px-5 py-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ─── MODAL EDIT PINJAMAN ─────────────────────────────────────────── -->
    @if($showEditModal && $editingLoan)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-xl overflow-hidden space-y-5 animate-in fade-in duration-200">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-900 dark:text-white">Edit Data Pinjaman</h2>
                        <p class="text-xs text-slate-500 font-mono">{{ $editingLoan->loan_code }} • Dicatat: {{ $editingLoan->created_at?->translatedFormat('d M Y') }}</p>
                    </div>
                    <button type="button" wire:click="closeEditModal" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="saveEditLoan" class="p-6 space-y-4">
                    <!-- Santri Info Banner (Read-only) -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Santri Peminjam</span>
                            <strong class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $editingLoan->person?->name }}</strong>
                            <div class="text-xs text-slate-500 font-mono">
                                {{ $editingLoan->person?->nis ? 'NIS: '.$editingLoan->person->nis : 'Santri Aktif' }} • {{ $editingLoan->gender === 'L' ? 'Putra' : 'Putri' }}
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                            {{ $editingLoan->status }}
                        </span>
                    </div>

                    <!-- Nominal Pinjaman -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Nominal Pinjaman <span class="text-rose-500">*</span>
                            </label>
                            @if($editingLoan->amount_paid > 0)
                                <span class="text-[11px] text-amber-600 dark:text-amber-400 font-bold">
                                    Sudah dibayar: Rp {{ number_format($editingLoan->amount_paid, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                        <div class="flex rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden bg-slate-50 dark:bg-slate-950 focus-within:ring-2 focus-within:ring-amber-500 focus-within:border-amber-500">
                            <span class="inline-flex items-center px-4 bg-slate-200/70 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-black text-sm border-r border-slate-200 dark:border-slate-700 select-none">
                                Rp
                            </span>
                            <input type="number" 
                                   wire:model.live="editAmount" 
                                   placeholder="0"
                                   min="{{ $editingLoan->amount_paid > 0 ? $editingLoan->amount_paid : 1 }}"
                                   step="any"
                                   class="w-full px-4 py-2.5 bg-transparent border-0 text-base font-black focus:outline-none focus:ring-0 text-slate-900 dark:text-white">
                        </div>
                        @if($editAmount && is_numeric($editAmount) && (float)$editAmount > 0)
                            <div class="flex items-center gap-1.5 text-xs font-extrabold text-amber-600 dark:text-amber-400 pt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Terbilang: Rp {{ number_format((float)$editAmount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @error('editAmount') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tanggal Pinjam & Jatuh Tempo -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Tanggal Pinjam <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" 
                                   wire:model="editBorrowedAt" 
                                   class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500">
                            @error('editBorrowedAt') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Jatuh Tempo (Opsional)
                            </label>
                            <input type="date" 
                                   wire:model="editDueDate" 
                                   class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500">
                            @error('editDueDate') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Alasan / Keperluan -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Keperluan / Alasan Pinjam
                        </label>
                        <input type="text" 
                               wire:model="editReason" 
                               placeholder="Contoh: Kiriman orang tua telat, butuh uang jajan / beli kitab"
                               class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-200">
                        @error('editReason') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Catatan Internal -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Catatan Internal (Opsional)
                        </label>
                        <input type="text" 
                               wire:model="editNotes" 
                               placeholder="Catatan tambahan untuk internal pengurus"
                               class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-200">
                        @error('editNotes') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Toggle Tampilkan ke Portal Wali -->
                    <div wire:click="$toggle('editIsVisibleToGuardian')"
                         class="cursor-pointer p-4 rounded-2xl border transition-all duration-200 flex items-center justify-between gap-3 select-none {{ $editIsVisibleToGuardian ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-700 shadow-xs ring-1 ring-emerald-400/40' : 'bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800' }}">
                        <div class="space-y-1 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-black {{ $editIsVisibleToGuardian ? 'text-emerald-900 dark:text-emerald-200' : 'text-slate-800 dark:text-slate-200' }}">
                                    Tampilkan di Dashboard Orang Tua?
                                </span>
                                @if($editIsVisibleToGuardian)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-600 text-white shadow-xs">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        Aktif (Tayang ke Wali)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                        Off (Internal Saja)
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs {{ $editIsVisibleToGuardian ? 'text-emerald-700 dark:text-emerald-300 font-medium' : 'text-slate-500' }}">
                                {{ $editIsVisibleToGuardian ? '✓ Tagihan talangan ini AKAN MUNCUL di Portal Wali sehingga orang tua bisa melihat dan melunasi via transfer/gateway.' : 'Hanya dicatat di pembukuan internal pengurus pondok, tidak ditampilkan di Portal Wali.' }}
                            </p>
                        </div>

                        <!-- Pill Toggle Knob -->
                        <div class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out {{ $editIsVisibleToGuardian ? 'bg-emerald-600' : 'bg-slate-300 dark:bg-slate-700' }}">
                            <span class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out {{ $editIsVisibleToGuardian ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" wire:click="closeEditModal" class="px-4 py-2.5 rounded-2xl text-slate-600 dark:text-slate-400 font-bold text-sm hover:bg-slate-100 dark:hover:bg-slate-800">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-2xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-sm shadow-md shadow-amber-600/20 active:scale-95 transition-all">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
