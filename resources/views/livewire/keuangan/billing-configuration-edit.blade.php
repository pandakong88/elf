<div class="space-y-0">
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
                    <span class="text-slate-600 dark:text-slate-300">Edit Iuran</span>
                </div>
                <h1 class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight">Edit Konfigurasi Iuran</h1>
            </div>
        </div>
        <span class="hidden sm:block text-[10px] text-amber-600 font-semibold bg-amber-500/10 px-3 py-1.5 rounded-full">
            Mode Pengeditan Tarif
        </span>
    </div>

    {{-- ===== FLASH MESSAGES ===== --}}
    @if(session()->has('error'))
        <div class="mb-4 p-4 bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- ===== MAIN LAYOUT GRID ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- ===== LEFT: LIVE PREVIEW CARD ===== --}}
        <div class="lg:col-span-1 space-y-4 lg:sticky lg:top-6">

            {{-- Preview Tagihan --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-r from-amber-600 to-orange-600 flex items-center justify-between">
                    <div>
                        <span class="block text-[9px] font-extrabold text-amber-100 uppercase tracking-widest">Preview Perubahan</span>
                        <span class="block text-xs font-bold text-white mt-0.5">
                            {{ $newConfigName ?: 'Nama Iuran...' }}
                        </span>
                    </div>
                    <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    {{-- Nominal --}}
                    <div class="text-center py-3 bg-slate-50 dark:bg-slate-950/50 rounded-xl border border-slate-100 dark:border-slate-800">
                        <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nominal Baru</span>
                        <span class="text-2xl font-extrabold text-slate-900 dark:text-white">
                            Rp {{ number_format($newConfigAmount ?: 0, 0, ',', '.') }}
                        </span>
                        @if($newConfigCanBeInstallment)
                            <span class="block text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold mt-1">✦ Dapat dicicil</span>
                        @endif
                    </div>

                    {{-- Badges Info --}}
                    <div class="grid grid-cols-2 gap-2">
                        <div class="p-2 bg-slate-50 dark:bg-slate-950/50 rounded-xl border border-slate-100 dark:border-slate-800 text-center">
                            <span class="block text-[8px] font-extrabold text-slate-400 uppercase mb-0.5">Siklus</span>
                            <span class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500">
                                @php
                                    $intervalMap = ['monthly' => 'Bulanan', 'semester' => 'Semesteran', 'yearly' => 'Tahunan', 'insidental' => 'Sekali Bayar'];
                                @endphp
                                🔒 {{ $intervalMap[$newConfigInterval] ?? $newConfigInterval }}
                            </span>
                        </div>
                        <div class="p-2 bg-slate-50 dark:bg-slate-950/50 rounded-xl border border-slate-100 dark:border-slate-800 text-center">
                            <span class="block text-[8px] font-extrabold text-slate-400 uppercase mb-0.5">Mulai Berlaku</span>
                            <span class="text-[10px] font-extrabold text-slate-700 dark:text-slate-300">
                                {{ $newConfigEffectiveFrom ? \Carbon\Carbon::parse($newConfigEffectiveFrom)->format('d M Y') : '—' }}
                            </span>
                        </div>
                    </div>

                    {{-- Target Penerima --}}
                    <div class="p-3 bg-slate-50 dark:bg-slate-950/50 rounded-xl border border-slate-100 dark:border-slate-800 space-y-1.5">
                        <span class="block text-[8px] font-extrabold text-slate-400 uppercase tracking-wider">Target Penerima</span>
                        <div class="flex items-center gap-2">
                            @if($newConfigTargetType === 'all')
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-lg text-[10px] font-bold">
                                    🌐 Semua Santri Aktif
                                </span>
                            @elseif($newConfigTargetType === 'dormitory')
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-lg text-[10px] font-bold">
                                    🏠 {{ count($newConfigTargetFilters) }} Komplek
                                </span>
                            @elseif($newConfigTargetType === 'kelas')
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-500/10 text-purple-600 dark:text-purple-400 rounded-lg text-[10px] font-bold">
                                    📚 {{ count($newConfigTargetFilters) }} Kelas
                                </span>
                            @elseif($newConfigTargetType === 'individual')
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded-lg text-[10px] font-bold">
                                    👤 {{ count($newConfigTargetFilters) }} Santri Terpilih
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Pengelola --}}
                    <div class="space-y-1.5">
                        <span class="block text-[8px] font-extrabold text-slate-400 uppercase tracking-wider">Otoritas Pengelola</span>
                        @if(empty($newConfigManagerRoles) && empty($newConfigManagerIds))
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Semua Bendahara (Pondok/Utama)</span>
                        @else
                            <div class="flex flex-wrap gap-1">
                                @foreach($newConfigManagerRoles as $role)
                                    <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-md text-[9px] font-bold">{{ $role }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Progress Kelengkapan --}}
            @php
                $filled = 0;
                $total = 5;
                if($newConfigName) $filled++;
                if($newConfigType) $filled++;
                if($newConfigAmount > 0) $filled++;
                if($newConfigEffectiveFrom) $filled++;
                if($newConfigTargetType === 'all' || !empty($newConfigTargetFilters)) $filled++;
                $pct = round(($filled / $total) * 100);
            @endphp
            <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kelengkapan Form</span>
                    <span class="text-sm font-extrabold {{ $pct >= 100 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-700 dark:text-slate-300' }}">{{ $pct }}%</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500 {{ $pct >= 100 ? 'bg-emerald-500' : 'bg-slate-400 dark:bg-slate-600' }}" style="width: {{ $pct }}%"></div>
                </div>
            </div>

        </div>

        {{-- ===== RIGHT: FORM ===== --}}
        <div class="lg:col-span-2 space-y-5">
            <form wire:submit.prevent="updateConfig" class="space-y-5">

                {{-- === LANGKAH 1: NAMA & NOMINAL === --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-2xl shadow-sm">
                    <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20">
                        <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-extrabold shrink-0 bg-emerald-500 text-white">
                            ✓
                        </span>
                        <div>
                            <h4 class="text-[11px] font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-widest">Nama & Nominal Iuran</h4>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">Identitas dasar tagihan ini yang akan tercetak di struk santri.</p>
                        </div>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1.5">
                                    Nama / Label Iuran <span class="text-rose-400">*</span>
                                </label>
                                <input type="text" wire:model.live.debounce.300ms="newConfigName"
                                    placeholder="Contoh: Syahriah Pondok Januari 2025"
                                    class="w-full bg-white dark:bg-slate-950/60 border border-slate-200 dark:border-slate-700/80 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all placeholder-slate-300 dark:placeholder-slate-600">
                                @error('newConfigName') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                <span class="text-[9px] text-slate-400 mt-1 block">Nama yang tercetak di slip tagihan santri.</span>
                            </div>
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                    Tipe / Kategori Tagihan 🔒 <span class="text-[8.5px] lowercase font-normal italic text-slate-400">(tidak dapat diubah)</span>
                                </label>
                                <select wire:model="newConfigType" disabled
                                    class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 rounded-xl px-4 py-2.5 text-xs cursor-not-allowed">
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="syahriah_pondok">🏠 Syahriah Pondok — Iuran bulanan asrama</option>
                                    <option value="kas_komplek">💰 Kas Komplek — Kas asrama / kamar</option>
                                    <option value="majek_pagi">☀️ Majek Pagi — Katering sarapan</option>
                                    <option value="majek_sore">🌙 Majek Sore — Katering makan malam</option>
                                    <option value="syahriah_madrasah">📖 Syahriah Madrasah — Iuran sekolah diniyyah</option>
                                    <option value="kebersihan">🧹 Kebersihan — Iuran sampah / kebersihan</option>
                                    <option value="kitab">📕 Kitab — Buku pelajaran diniyyah</option>
                                    <option value="pendaftaran">🎫 Pendaftaran / Event — Ziarah, haflah, dll.</option>
                                </select>
                            </div>
                        </div>

                        {{-- Nominal dengan format rupiah visual --}}
                        <div class="max-w-sm">
                            <label class="block text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1.5">
                                Nominal Tarif (Rupiah) <span class="text-rose-400">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-extrabold text-slate-500 dark:text-slate-400 select-none">Rp</span>
                                <input type="number" wire:model.live.debounce.300ms="newConfigAmount"
                                    placeholder="0"
                                    min="0" step="500"
                                    class="w-full bg-white dark:bg-slate-950/60 border border-slate-200 dark:border-slate-700/80 text-slate-800 dark:text-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-sm font-extrabold focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all text-right">
                            </div>
                            @if($newConfigAmount > 0)
                                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold mt-1 block">
                                    = Rp {{ number_format($newConfigAmount, 0, ',', '.') }}
                                </span>
                            @endif
                            @error('newConfigAmount') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- === LANGKAH 2: ATURAN & PENGELOLA === --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-2xl shadow-sm">
                    <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20">
                        <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-extrabold shrink-0 bg-emerald-500 text-white">
                            ✓
                        </span>
                        <div>
                            <h4 class="text-[11px] font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-widest">Aturan Bayar & Pengelola</h4>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">Siklus penagihan, tanggal berlaku, dan siapa yang berhak mengelola iuran ini.</p>
                        </div>
                    </div>
                    <div class="p-5 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                    Siklus Penagihan 🔒 <span class="text-[8.5px] lowercase font-normal italic text-slate-400">(tidak dapat diubah)</span>
                                </label>
                                <select wire:model="newConfigInterval" disabled
                                    class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 rounded-xl px-4 py-2.5 text-xs cursor-not-allowed">
                                    <option value="monthly">📅 Bulanan — ditagih setiap bulan</option>
                                    <option value="semester">📆 Semesteran — ditagih 6 bulan sekali</option>
                                    <option value="yearly">🗓️ Tahunan — ditagih sekali dalam setahun</option>
                                    <option value="insidental">⚡ Sekali Bayar — iuran event atau kegiatan khusus</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1.5">
                                    Tanggal Mulai Berlaku <span class="text-rose-400">*</span>
                                </label>
                                <input type="date" wire:model="newConfigEffectiveFrom"
                                    class="w-full bg-white dark:bg-slate-950/60 border border-slate-200 dark:border-slate-700/80 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                                @error('newConfigEffectiveFrom') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                <span class="text-[9px] text-slate-400 mt-1 block">Tarif mulai berlaku sejak tanggal ini.</span>
                            </div>
                        </div>

                        {{-- Opsi Cicilan --}}
                        <label class="relative flex items-start gap-3.5 cursor-pointer p-4 border rounded-xl transition-all group
                            {{ $newConfigCanBeInstallment ? 'bg-emerald-50 dark:bg-emerald-950/20 border-emerald-300 dark:border-emerald-800' : 'bg-slate-50 dark:bg-slate-950/30 border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700' }}">
                            <input type="checkbox" wire:model.live="newConfigCanBeInstallment"
                                class="w-4.5 h-4.5 rounded border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500 mt-0.5 shrink-0">
                            <div>
                                <span class="block text-xs font-extrabold {{ $newConfigCanBeInstallment ? 'text-emerald-700 dark:text-emerald-300' : 'text-slate-700 dark:text-slate-300' }}">
                                    Iuran ini boleh dibayar secara cicilan
                                </span>
                                <span class="block text-[10px] font-medium {{ $newConfigCanBeInstallment ? 'text-emerald-600/80 dark:text-emerald-400/80' : 'text-slate-400 dark:text-slate-500' }} mt-0.5">
                                    Aktifkan jika nominal cukup besar dan wali santri diperbolehkan mengangsur pembayaran dalam beberapa termin.
                                </span>
                            </div>
                            @if($newConfigCanBeInstallment)
                                <span class="absolute right-4 top-4 text-emerald-500 text-sm">✓</span>
                            @endif
                        </label>

                        {{-- Role Pengelola — Pill Chips --}}
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                                    Role Pengelola (siapa yang bisa merekap iuran ini?)
                                </label>
                                <div class="flex items-center gap-2">
                                    <button type="button" wire:click="autoSelectMyRole" class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                                        ⚡ Auto-Pilih Role Saya
                                    </button>
                                    <span class="text-slate-300 dark:text-slate-700">|</span>
                                    <button type="button" wire:click="clearManagerRoles" class="text-[10px] font-bold text-rose-500 hover:underline flex items-center gap-1">
                                        ❌ Kosongkan (Semua Bendahara)
                                    </button>
                                </div>
                            </div>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mb-3">Pilih satu atau lebih jabatan. Kosongkan jika boleh dikelola oleh semua bendahara (Pondok/Pusat).</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($systemRoles as $role)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" wire:model.live="newConfigManagerRoles" value="{{ $role->name }}" class="sr-only peer">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[11px] font-bold transition-all border
                                            peer-checked:bg-emerald-500 peer-checked:border-emerald-500 peer-checked:text-white
                                            bg-slate-100 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400
                                            hover:border-emerald-400 hover:text-emerald-600 dark:hover:text-emerald-400">
                                            {{ $role->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @if(empty($newConfigManagerRoles))
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 mt-2 block italic">Saat ini: dikelola oleh semua bendahara (Pondok/Pusat).</span>
                            @endif
                        </div>

                        {{-- Co-Manager Search --}}
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                            <label class="block text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1.5">
                                Pengurus Tambahan (Akses Personal Khusus)
                            </label>
                            <p class="text-[10px] text-slate-400 mb-2.5">Berikan akses kelola iuran ini ke staf tertentu secara personal, terlepas dari jabatannya.</p>
                            <div class="relative" style="overflow: visible;">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <input type="text" wire:model.live="newConfigCoManagerSearchQuery"
                                    placeholder="Cari nama staf / pengurus..."
                                    class="w-full bg-white dark:bg-slate-950/60 border border-slate-200 dark:border-slate-700/80 text-slate-800 dark:text-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all placeholder-slate-400">
                                @if(!empty($coManagerSearchResults))
                                    <div class="absolute z-50 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl divide-y divide-slate-100 dark:divide-slate-800 mt-1 shadow-xl" style="top: 100%; left: 0;">
                                        @foreach($coManagerSearchResults as $u)
                                            <button type="button" wire:click="addCoManager('{{ $u->id }}')"
                                                class="w-full text-left px-4 py-2.5 hover:bg-emerald-50 dark:hover:bg-emerald-950/20 text-xs transition-all flex items-center justify-between group">
                                                <div>
                                                    <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ $u->name }}</span>
                                                    <span class="text-[9px] text-slate-400">{{ $u->email }}</span>
                                                </div>
                                                <span class="text-[10px] font-bold text-emerald-600 group-hover:text-emerald-700">+ Tambah</span>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            @if(!empty($this->selectedCoManagers) && $this->selectedCoManagers->isNotEmpty())
                                <div class="flex flex-wrap gap-2 mt-3">
                                    @foreach($this->selectedCoManagers as $u)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 dark:bg-blue-950/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 rounded-xl text-[11px] font-bold">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                                            {{ $u->name }}
                                            <button type="button" wire:click="removeCoManager('{{ $u->id }}')" class="text-blue-400 hover:text-blue-600 dark:hover:text-blue-200 font-extrabold leading-none text-sm ml-0.5">&times;</button>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- === LANGKAH 3: TARGET SANTRI === --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-2xl shadow-sm">
                    <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20">
                        <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-extrabold shrink-0 bg-emerald-500 text-white">
                            ✓
                        </span>
                        <div>
                            <h4 class="text-[11px] font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-widest">Target Kelompok Santri Penerima</h4>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">Tentukan siapa saja yang akan dibebankan tagihan ini.</p>
                        </div>
                    </div>
                    <div class="p-5 space-y-5">

                        {{-- Target Gender Santri --}}
                        <div class="p-4 bg-slate-50/80 dark:bg-slate-950/40 border border-slate-200/80 dark:border-slate-800 rounded-xl">
                            <label class="block text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                                Target Gender Santri <span class="text-rose-400">*</span>
                            </label>
                            <div class="flex flex-wrap items-center gap-3">
                                <label class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 cursor-pointer shadow-xs">
                                    <input type="checkbox" wire:model.live="newConfigGenderTargets" value="L"
                                           @if($this->genderScope() === 'P') disabled @endif
                                           class="rounded text-emerald-600 focus:ring-emerald-500">
                                    <span class="{{ $this->genderScope() === 'P' ? 'opacity-40' : '' }}">👦 Santri Putra (L)</span>
                                </label>
                                <label class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 cursor-pointer shadow-xs">
                                    <input type="checkbox" wire:model.live="newConfigGenderTargets" value="P"
                                           @if($this->genderScope() === 'L') disabled @endif
                                           class="rounded text-emerald-600 focus:ring-emerald-500">
                                    <span class="{{ $this->genderScope() === 'L' ? 'opacity-40' : '' }}">👧 Santri Putri (P)</span>
                                </label>
                            </div>
                            <p class="text-[9px] text-slate-400 mt-1.5">Pilih Putra, Putri, atau keduanya. Opsi komplek dan kelas di bawah akan otomatis menyesuaikan gender yang dicentang.</p>
                            @error('newConfigGenderTargets') <span class="text-[10px] text-rose-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>

                        {{-- Target Status Residensi Santri --}}
                        <div class="p-4 bg-slate-50/80 dark:bg-slate-950/40 border border-slate-200/80 dark:border-slate-800 rounded-xl">
                            <label class="block text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                                Target Status Residensi Santri <span class="text-rose-400">*</span>
                            </label>
                            <div class="flex flex-wrap items-center gap-3">
                                <label class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 cursor-pointer shadow-xs">
                                    <input type="checkbox" wire:model.live="newConfigResidenceTargets" value="mukim" class="rounded text-emerald-600 focus:ring-emerald-500">
                                    <span>🏠 Santri Mukim (Menetap Asrama)</span>
                                </label>
                                <label class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 cursor-pointer shadow-xs">
                                    <input type="checkbox" wire:model.live="newConfigResidenceTargets" value="laju" class="rounded text-emerald-600 focus:ring-emerald-500">
                                    <span>🚗 Santri Laju (Pulang Pergi / PP)</span>
                                </label>
                            </div>
                            <p class="text-[9px] text-slate-400 mt-1.5">Centang <b>Santri Mukim</b> saja untuk tarif khusus pondok (seperti Syahriah Pondok / Kas Komplek). Hilangkan centang Santri Laju agar mereka tidak tertagih.</p>
                            @error('newConfigResidenceTargets') <span class="text-[10px] text-rose-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>

                        {{-- Radio Cards Target Type --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach([
                                ['value' => 'all', 'icon' => '🌐', 'title' => 'Semua Santri', 'desc' => 'Seluruh santri aktif pondok'],
                                ['value' => 'dormitory', 'icon' => '🏠', 'title' => 'Per Komplek', 'desc' => 'Pilih asrama tertentu'],
                                ['value' => 'kelas', 'icon' => '📚', 'title' => 'Per Kelas', 'desc' => 'Pilih kelas madrasah'],
                                ['value' => 'individual', 'icon' => '👤', 'title' => 'Santri Tertentu', 'desc' => 'Pilih nama per nama'],
                            ] as $opt)
                                <label class="cursor-pointer" wire:key="target-{{ $opt['value'] }}">
                                    <input type="radio" wire:model.live="newConfigTargetType" value="{{ $opt['value'] }}" class="sr-only peer">
                                    <div class="flex flex-col items-center text-center p-3.5 rounded-xl border-2 transition-all
                                        peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-950/20
                                        border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600
                                        bg-white dark:bg-slate-950/30">
                                        <span class="text-2xl mb-1.5">{{ $opt['icon'] }}</span>
                                        <span class="text-[11px] font-extrabold text-slate-800 dark:text-slate-200 peer-checked:text-emerald-700 block leading-snug">{{ $opt['title'] }}</span>
                                        <span class="text-[9px] text-slate-400 dark:text-slate-500 leading-tight mt-0.5">{{ $opt['desc'] }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        @error('newConfigTargetFilters')
                            <div class="p-3.5 bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 rounded-xl text-xs font-semibold flex items-center gap-2 animate-pulse">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $message }}
                            </div>
                        @enderror

                        {{-- Conditional Sub-Filters --}}
                        @if($newConfigTargetType === 'dormitory')
                            <div class="space-y-4">
                                <div class="p-4 bg-amber-50/50 dark:bg-amber-950/10 border border-amber-200/50 dark:border-amber-800/30 rounded-xl space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="block text-[10px] font-extrabold text-amber-700 dark:text-amber-400 uppercase tracking-wider">Pilih Komplek Asrama</span>
                                        <div class="flex items-center gap-1.5 text-[9px] font-extrabold">
                                            @if(in_array('L', $newConfigGenderTargets) && !$this->genderScope())
                                                <button type="button" wire:click="selectAllTargetFilters('L')" class="text-cyan-600 dark:text-cyan-400 hover:underline">👦 All Putra</button>
                                                <span class="text-amber-300 dark:text-amber-700">|</span>
                                            @endif
                                            @if(in_array('P', $newConfigGenderTargets) && !$this->genderScope())
                                                <button type="button" wire:click="selectAllTargetFilters('P')" class="text-pink-600 dark:text-pink-400 hover:underline">👧 All Putri</button>
                                                <span class="text-amber-300 dark:text-amber-700">|</span>
                                            @endif
                                            <button type="button" wire:click="selectAllTargetFilters('all')" class="text-amber-700 dark:text-amber-300 hover:underline">⚡ Pilih Semua</button>
                                            <span class="text-amber-300 dark:text-amber-700">|</span>
                                            <button type="button" wire:click="clearAllTargetFilters" class="text-rose-500 hover:underline">❌ Kosongkan</button>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                        @foreach($dormitories as $d)
                                            @php $isChecked = in_array($d->id, $newConfigTargetFilters); @endphp
                                            <label class="cursor-pointer" wire:key="dorm-{{ $d->id }}">
                                                <input type="checkbox" wire:model.live="newConfigTargetFilters" value="{{ $d->id }}" class="sr-only">
                                                <div class="flex items-center gap-2.5 p-2.5 border-2 rounded-xl text-xs font-bold transition-all
                                                    {{ $isChecked
                                                        ? 'bg-amber-500 border-amber-500 text-white shadow-md shadow-amber-200 dark:shadow-amber-900'
                                                        : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-amber-300 dark:hover:border-amber-700' }}">
                                                    <span class="w-4 h-4 rounded border-2 flex items-center justify-center flex-shrink-0 transition-all
                                                        {{ $isChecked ? 'bg-white/30 border-white' : 'border-current' }}">
                                                        @if($isChecked)
                                                            <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"/></svg>
                                                        @endif
                                                    </span>
                                                    {{ $d->name }}
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Selected Summary: Dormitory --}}
                                @if(!empty($newConfigTargetFilters))
                                    <div class="p-3.5 bg-white dark:bg-slate-900 border border-amber-200 dark:border-amber-800/40 rounded-xl space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-extrabold text-amber-700 dark:text-amber-400 uppercase tracking-wider">
                                                ✓ Komplek Terpilih ({{ count($newConfigTargetFilters) }})
                                            </span>
                                            <button type="button" wire:click="$set('newConfigTargetFilters', [])"
                                                class="text-[9px] font-bold text-rose-500 hover:text-rose-600 hover:underline transition-colors">
                                                Hapus Semua
                                            </button>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($dormitories->whereIn('id', $newConfigTargetFilters) as $sel)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 text-white rounded-xl text-[11px] font-bold shadow-sm">
                                                    🏠 {{ $sel->name }}
                                                    <button type="button" wire:click="removeTargetFilter('{{ $sel->id }}')"
                                                        class="text-white/70 hover:text-white font-extrabold leading-none text-sm ml-0.5 transition-colors">&times;</button>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="p-3 bg-amber-50/30 dark:bg-amber-950/5 border border-dashed border-amber-200 dark:border-amber-800/30 rounded-xl text-center">
                                        <span class="text-[10px] text-amber-600/70 dark:text-amber-500/60 font-semibold">Belum ada komplek yang dipilih — centang di atas untuk menambahkan.</span>
                                    </div>
                                @endif
                            </div>

                        @elseif($newConfigTargetType === 'kelas')
                            <div class="space-y-4">
                                <div class="p-4 bg-purple-50/50 dark:bg-purple-950/10 border border-purple-200/50 dark:border-purple-800/30 rounded-xl space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="block text-[10px] font-extrabold text-purple-700 dark:text-purple-400 uppercase tracking-wider">Pilih Kelas Madrasah</span>
                                        <div class="flex items-center gap-1.5 text-[9px] font-extrabold">
                                            @if(in_array('L', $newConfigGenderTargets) && !$this->genderScope())
                                                <button type="button" wire:click="selectAllTargetFilters('L')" class="text-cyan-600 dark:text-cyan-400 hover:underline">👦 All Putra</button>
                                                <span class="text-purple-300 dark:text-purple-700">|</span>
                                            @endif
                                            @if(in_array('P', $newConfigGenderTargets) && !$this->genderScope())
                                                <button type="button" wire:click="selectAllTargetFilters('P')" class="text-pink-600 dark:text-pink-400 hover:underline">👧 All Putri</button>
                                                <span class="text-purple-300 dark:text-purple-700">|</span>
                                            @endif
                                            <button type="button" wire:click="selectAllTargetFilters('all')" class="text-purple-700 dark:text-purple-300 hover:underline">⚡ Pilih Semua</button>
                                            <span class="text-purple-300 dark:text-purple-700">|</span>
                                            <button type="button" wire:click="clearAllTargetFilters" class="text-rose-500 hover:underline">❌ Kosongkan</button>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                        @foreach($kelasList as $kls)
                                            @php $isChecked = in_array($kls->id, $newConfigTargetFilters); @endphp
                                            <label class="cursor-pointer" wire:key="kelas-{{ $kls->id }}">
                                                <input type="checkbox" wire:model.live="newConfigTargetFilters" value="{{ $kls->id }}" class="sr-only">
                                                <div class="flex items-center gap-2 p-2.5 border-2 rounded-xl text-xs font-bold transition-all
                                                    {{ $isChecked
                                                        ? 'bg-purple-500 border-purple-500 text-white shadow-md shadow-purple-200 dark:shadow-purple-900'
                                                        : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-purple-300 dark:hover:border-purple-700' }}">
                                                    <span class="w-4 h-4 rounded border-2 flex items-center justify-center flex-shrink-0
                                                        {{ $isChecked ? 'bg-white/30 border-white' : 'border-current' }}">
                                                        @if($isChecked)
                                                            <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"/></svg>
                                                        @endif
                                                    </span>
                                                    <span class="truncate">{{ $kls->name }}</span>
                                                    <span class="text-[9px] opacity-75 shrink-0">({{ $kls->academic_year }})</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Selected Summary: Kelas --}}
                                @if(!empty($newConfigTargetFilters))
                                    <div class="p-3.5 bg-white dark:bg-slate-900 border border-purple-200 dark:border-purple-800/40 rounded-xl space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-extrabold text-purple-700 dark:text-purple-400 uppercase tracking-wider">
                                                ✓ Kelas Terpilih ({{ count($newConfigTargetFilters) }})
                                            </span>
                                            <button type="button" wire:click="$set('newConfigTargetFilters', [])"
                                                class="text-[9px] font-bold text-rose-500 hover:text-rose-600 hover:underline transition-colors">
                                                Hapus Semua
                                            </button>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($kelasList->whereIn('id', $newConfigTargetFilters) as $sel)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-500 text-white rounded-xl text-[11px] font-bold shadow-sm">
                                                    📚 {{ $sel->name }} ({{ $sel->academic_year }})
                                                    <button type="button" wire:click="removeTargetFilter('{{ $sel->id }}')"
                                                        class="text-white/70 hover:text-white font-extrabold leading-none text-sm ml-0.5 transition-colors">&times;</button>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="p-3 bg-purple-50/30 dark:bg-purple-950/5 border border-dashed border-purple-200 dark:border-purple-800/30 rounded-xl text-center">
                                        <span class="text-[10px] text-purple-600/70 dark:text-purple-500/60 font-semibold">Belum ada kelas yang dipilih — centang di atas untuk menambahkan.</span>
                                    </div>
                                @endif
                            </div>

                        @elseif($newConfigTargetType === 'individual')
                            <div class="space-y-4">
                                {{-- Filter Bar --}}
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-4 bg-slate-50 dark:bg-slate-950/40 rounded-xl border border-slate-200/50 dark:border-slate-800">
                                    <div>
                                        <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Filter Komplek</label>
                                        <select wire:model.live="newConfigFilterDormitoryId"
                                            class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                            <option value="">Semua Komplek</option>
                                            @foreach($dormitories as $d)
                                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Filter Kelas</label>
                                        <select wire:model.live="newConfigFilterKelasId"
                                            class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                            <option value="">Semua Kelas</option>
                                            @foreach($kelasList as $kls)
                                                <option value="{{ $kls->id }}">{{ $kls->name }} ({{ $kls->academic_year }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Cari Nama</label>
                                        <div class="relative">
                                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            <input type="text" wire:model.live.debounce.300ms="newConfigFilterSearch"
                                                placeholder="Ketik nama santri..."
                                                class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl pl-8 pr-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30 placeholder-slate-400">
                                        </div>
                                    </div>
                                </div>

                                {{-- Bulk Actions --}}
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5">
                                    <div class="flex items-center gap-2">
                                        <button type="button" wire:click="toggleAllIndividualSantri(true)"
                                            class="px-3 py-1.5 bg-emerald-50 dark:bg-emerald-950/30 hover:bg-emerald-100 dark:hover:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 rounded-lg text-[10px] font-bold transition-all">
                                            ✓ Centang Semua
                                        </button>
                                        <button type="button" wire:click="toggleAllIndividualSantri(false)"
                                            class="px-3 py-1.5 bg-rose-50 dark:bg-rose-950/30 hover:bg-rose-100 dark:hover:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 rounded-lg text-[10px] font-bold transition-all">
                                            ✕ Hapus Semua
                                        </button>
                                        @if($newConfigFilterDormitoryId || $newConfigFilterKelasId || $newConfigFilterSearch)
                                            <button type="button" wire:click="$set('newConfigFilterDormitoryId', ''); $set('newConfigFilterKelasId', ''); $set('newConfigFilterSearch', '');"
                                                class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 rounded-lg text-[10px] font-bold transition-all hover:bg-slate-200">
                                                Reset Filter
                                            </button>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400">Terpilih:</span>
                                        <span class="px-2.5 py-1 bg-emerald-500 text-white rounded-lg text-[11px] font-extrabold min-w-[40px] text-center">
                                            {{ count($newConfigTargetFilters) }}
                                        </span>
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400">santri</span>
                                    </div>
                                </div>

                                {{-- Checklist Grid --}}
                                <div class="border border-slate-200 dark:border-slate-700/80 rounded-xl overflow-hidden">
                                    <div class="px-3 py-2 bg-slate-50 dark:bg-slate-950/40 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                                        <span class="text-[9px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                                            Daftar Santri (Maks. 200 per filter)
                                        </span>
                                        <span wire:loading wire:target="newConfigFilterDormitoryId,newConfigFilterKelasId,newConfigFilterSearch"
                                            class="text-[9px] text-emerald-500 font-bold animate-pulse">
                                            Memuat...
                                        </span>
                                    </div>
                                    @if(empty($individualSantriOptions) || $individualSantriOptions->isEmpty())
                                        <div class="p-10 text-center">
                                            <div class="text-3xl mb-2">🔍</div>
                                            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500">Tidak ada santri yang cocok dengan filter di atas.</p>
                                            <p class="text-[10px] text-slate-300 dark:text-slate-600 mt-1">Coba ubah atau hapus filter untuk menampilkan daftar santri.</p>
                                        </div>
                                    @else
                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 p-3 max-h-72 overflow-y-auto bg-white dark:bg-slate-900">
                                            @foreach($individualSantriOptions as $s)
                                                @php $isChecked = in_array($s->id, $newConfigTargetFilters); @endphp
                                                <label class="cursor-pointer" wire:key="santri-{{ $s->id }}">
                                                    <input type="checkbox" wire:model.live="newConfigTargetFilters" value="{{ $s->id }}" class="sr-only">
                                                    <div class="flex items-center gap-2.5 p-2.5 border-2 rounded-xl transition-all
                                                        {{ $isChecked
                                                            ? 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-400 dark:peer-checked:border-emerald-700'
                                                            : 'bg-slate-50/50 dark:bg-slate-950/20 border-slate-100 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700' }}">
                                                        <span class="w-4 h-4 rounded border-2 flex items-center justify-center flex-shrink-0 transition-all
                                                            {{ $isChecked
                                                                ? 'bg-emerald-500 border-emerald-500'
                                                                : 'border-slate-300 dark:border-slate-600' }}">
                                                            @if($isChecked)
                                                                <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"/></svg>
                                                            @endif
                                                        </span>
                                                        <div class="min-w-0 leading-none">
                                                            <span class="block text-[11px] font-bold truncate
                                                                {{ $isChecked ? 'text-emerald-700 dark:text-emerald-300' : 'text-slate-800 dark:text-slate-200' }}">
                                                                {{ $s->name }}
                                                            </span>
                                                            <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider mt-0.5 block">
                                                                {{ $s->gender === 'L' ? 'Putra' : 'Putri' }}
                                                                @php
                                                                    $activeRoom = $s->roomAssignments ? $s->roomAssignments->where('is_active', true)->first() : null;
                                                                    $dormName = $activeRoom?->room?->dormitory?->name;
                                                                @endphp
                                                                @if($dormName) · {{ $dormName }} @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                {{-- Selected Summary: Individual Santri --}}
                                @if(!empty($newConfigTargetFilters))
                                    <div class="p-3.5 bg-white dark:bg-slate-900 border border-emerald-200 dark:border-emerald-800/40 rounded-xl space-y-2.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-extrabold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">
                                                ✓ Santri Terpilih ({{ count($newConfigTargetFilters) }})
                                            </span>
                                            <button type="button" wire:click="$set('newConfigTargetFilters', [])"
                                                class="text-[9px] font-bold text-rose-500 hover:text-rose-600 hover:underline transition-colors">
                                                Hapus Semua
                                            </button>
                                        </div>
                                        <div class="flex flex-wrap gap-1.5 max-h-40 overflow-y-auto">
                                            @foreach($this->selectedIndividualSantri as $sel)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700/50 rounded-xl text-[11px] font-bold">
                                                    {{ $sel->name }}
                                                    <span class="text-[8px] text-emerald-500 dark:text-emerald-400 font-extrabold uppercase">{{ $sel->gender === 'L' ? '♂' : '♀' }}</span>
                                                    <button type="button" wire:click="removeTargetFilter('{{ $sel->id }}')"
                                                        class="text-emerald-400 hover:text-rose-500 font-extrabold leading-none text-sm ml-0.5 transition-colors">&times;</button>
                                                </span>
                                            @endforeach
                                        </div>
                                        <p class="text-[9px] text-slate-400 dark:text-slate-500 italic">
                                            Klik × untuk menghapus santri tertentu dari daftar penerima tagihan ini.
                                        </p>
                                    </div>
                                @else
                                    <div class="p-3 bg-slate-50 dark:bg-slate-950/30 border border-dashed border-slate-200 dark:border-slate-700 rounded-xl text-center">
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold">Belum ada santri yang dipilih — centang nama di atas untuk menambahkan ke daftar penerima.</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Sync Checkbox Option (For Edit Only) --}}
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex">
                            <label class="relative inline-flex items-center cursor-pointer text-xs font-bold text-slate-750 dark:text-slate-350 p-4 bg-emerald-500/5 border border-emerald-500/20 rounded-2xl hover:bg-emerald-500/10 transition-all w-full">
                                <input type="checkbox" wire:model="syncNewTargets" class="w-4.5 h-4.5 rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 mr-3.5 shrink-0">
                                <div>
                                    <span class="block text-[11px] font-extrabold text-emerald-700 dark:text-emerald-400 leading-snug">Otomatis Terbitkan Tagihan Periode Ini untuk Santri Target Baru</span>
                                    <span class="block text-[9px] font-semibold text-slate-400 dark:text-slate-500 leading-normal mt-0.5">
                                        Jika dicentang, sistem akan langsung membuatkan tagihan periode berjalan (bulan/semester ini) untuk santri baru yang baru saja ditambahkan ke kelompok target iuran ini.
                                    </span>
                                </div>
                            </label>
                        </div>

                    </div>
                </div>

                {{-- === FOOTER ACTIONS === --}}
                <div class="flex items-center justify-between pt-2 pb-6">
                    <a href="{{ route('keuangan.billing', ['tab' => 'rates']) }}"
                        class="flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Batalkan
                    </a>
                    <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="updateConfig"
                        class="relative flex items-center gap-2.5 px-7 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-xs font-extrabold transition-all shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 disabled:opacity-70 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="updateConfig" class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Simpan Perubahan Tarif
                        </span>
                        <span wire:loading wire:target="updateConfig" class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
