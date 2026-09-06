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
                                    $intervalMap = [
                                        'monthly' => 'Bulanan',
                                        'biweekly' => '2x Sebulan',
                                        '2x_monthly' => '2x Sebulan',
                                        'trimonthly' => '3x Sebulan',
                                        '3x_monthly' => '3x Sebulan',
                                        'weekly' => '4x Sebulan',
                                        '4x_monthly' => '4x Sebulan',
                                        'semester' => 'Semesteran',
                                        'yearly' => 'Tahunan',
                                        'insidental' => 'Sekali Bayar'
                                    ];
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
                                    <option value="pendaftaran">🎫 Pendaftaran — Pendaftaran Santri Baru (PSB)</option>
                                    <option value="event_iuran">🎉 Event / Insidental — Ziarah, Bukhoren, Haflah, dll.</option>
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
                                <label class="block text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                    Siklus Penagihan
                                    @if($hasIssuedBills)
                                        🔒 <span class="text-[8.5px] lowercase font-normal italic text-slate-400">(dikunci karena sudah ada tagihan terbit)</span>
                                    @else
                                        <span class="text-rose-400">*</span>
                                    @endif
                                </label>
                                <select wire:model="newConfigInterval" {{ $hasIssuedBills ? 'disabled' : '' }}
                                    class="w-full {{ $hasIssuedBills ? 'bg-slate-100 dark:bg-slate-800 text-slate-500 cursor-not-allowed' : 'bg-white dark:bg-slate-950/60 text-slate-800 dark:text-slate-200' }} border border-slate-200 dark:border-slate-700/80 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                                    <option value="monthly">📅 Bulanan — ditagih 12x dalam setahun (setiap bulan)</option>
                                    <option value="semester">📆 2x Dalam Setahun — ditagih Semesteran (per 6 bulan)</option>
                                    <option value="caturwulan">⏳ 3x Dalam Setahun — ditagih Caturwulan (per 4 bulan)</option>
                                    <option value="triwulan">📆 4x Dalam Setahun — ditagih Triwulan (per 3 bulan)</option>
                                    <option value="bimulanan">⏳ 6x Dalam Setahun — ditagih Dwibulanan / Bimulanan (per 2 bulan)</option>
                                    <option value="yearly">🗓️ Tahunan — ditagih 1x dalam setahun</option>
                                    <option value="insidental">⚡ Sekali Bayar — iuran event / insidental / kegiatan khusus</option>
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

                        {{-- Section Tenggat Pembayaran (Due Date) --}}
                        <div class="p-4 bg-slate-50 dark:bg-slate-950/40 rounded-xl border border-slate-200/80 dark:border-slate-800 space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-[10px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                    ⏰ Tenggat Pembayaran (Due Date)
                                </label>
                                <span class="text-[9px] font-medium text-slate-400">Aturan batas waktu pelunasan tagihan</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1">Tipe Tenggat</label>
                                    <select wire:model.live="newConfigDueDayType"
                                        class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                        @if(in_array($newConfigInterval, ['insidental', 'once', 'event', 'sekali']))
                                            <option value="fixed_date">📌 Tanggal Paten / Spesifik (Misal: 31 Agustus 2026)</option>
                                            <option value="days_after">⏳ Jumlah Hari Setelah Terbit (Misal: 7 Hari)</option>
                                            <option value="none">🌐 Tanpa Tenggat Waktu (Bebas)</option>
                                            <option value="fixed_day">📅 Tanggal Tetap Bulanan (Misal: Tgl 10)</option>
                                        @else
                                            <option value="fixed_day">📅 Tanggal Tetap Bulanan (Misal: Tgl 10 tiap bulan)</option>
                                            <option value="fixed_date">📌 Tanggal Paten / Spesifik (Misal: 31 Agustus 2026)</option>
                                            <option value="days_after">⏳ Jumlah Hari Setelah Terbit (Misal: 7 Hari)</option>
                                            <option value="none">🌐 Tanpa Tenggat Waktu (Bebas)</option>
                                        @endif
                                    </select>
                                </div>
                                @if($newConfigDueDayType === 'fixed_date')
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1">Pilih Tanggal Paten</label>
                                        <input type="date" wire:model="newConfigDueDateSpecific"
                                            class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                        @error('newConfigDueDateSpecific') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                @elseif($newConfigDueDayType !== 'none')
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1">
                                            {{ $newConfigDueDayType === 'fixed_day' ? 'Jatuh Tempo Tanggal' : 'Jumlah Hari Pelunasan' }}
                                        </label>
                                        <div class="relative">
                                            <input type="number" min="1" max="31" wire:model="newConfigDueDayValue"
                                                class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                            <span class="absolute right-3 top-2 text-[10px] text-slate-400 font-medium">
                                                {{ $newConfigDueDayType === 'fixed_day' ? 'Setiap bulan' : 'Hari' }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
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
                        <div class="p-4 bg-slate-50/80 dark:bg-slate-950/40 border border-slate-200/80 dark:border-slate-800 rounded-2xl">
                            <label class="block text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2.5">
                                Target Gender Santri <span class="text-rose-400">*</span>
                            </label>
                            <div class="flex flex-wrap items-center gap-3">
                                <label class="inline-flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 cursor-pointer shadow-xs hover:border-sky-300 transition-all">
                                    <input type="checkbox" wire:model.live="newConfigGenderTargets" value="L"
                                           @if($this->genderScope() === 'P') disabled @endif
                                           class="rounded text-sky-600 focus:ring-sky-500">
                                    <span class="flex items-center gap-1.5 {{ $this->genderScope() === 'P' ? 'opacity-40' : '' }}">
                                        <span class="w-5 h-5 rounded-full bg-sky-100 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center text-[10px] font-black">♂</span>
                                        <span>Santri Putra (L)</span>
                                    </span>
                                </label>
                                <label class="inline-flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 cursor-pointer shadow-xs hover:border-rose-300 transition-all">
                                    <input type="checkbox" wire:model.live="newConfigGenderTargets" value="P"
                                           @if($this->genderScope() === 'L') disabled @endif
                                           class="rounded text-rose-600 focus:ring-rose-500">
                                    <span class="flex items-center gap-1.5 {{ $this->genderScope() === 'L' ? 'opacity-40' : '' }}">
                                        <span class="w-5 h-5 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center text-[10px] font-black">♀</span>
                                        <span>Santri Putri (P)</span>
                                    </span>
                                </label>
                            </div>
                            <p class="text-[9px] text-slate-400 mt-2">Pilih Putra, Putri, atau keduanya. Opsi komplek dan kelas di bawah akan otomatis menyesuaikan gender yang dicentang.</p>
                            @error('newConfigGenderTargets') <span class="text-[10px] text-rose-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>

                        {{-- Target Status Residensi Santri --}}
                        <div class="p-4 bg-slate-50/80 dark:bg-slate-950/40 border border-slate-200/80 dark:border-slate-800 rounded-2xl">
                            <label class="block text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2.5">
                                Target Status Residensi Santri <span class="text-rose-400">*</span>
                            </label>
                            <div class="flex flex-wrap items-center gap-3">
                                <label class="inline-flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 cursor-pointer shadow-xs hover:border-emerald-300 transition-all">
                                    <input type="checkbox" wire:model.live="newConfigResidenceTargets" value="mukim" class="rounded text-emerald-600 focus:ring-emerald-500">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        <span>Santri Mukim (Menetap Asrama)</span>
                                    </span>
                                </label>
                                <label class="inline-flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 cursor-pointer shadow-xs hover:border-blue-300 transition-all">
                                    <input type="checkbox" wire:model.live="newConfigResidenceTargets" value="laju" class="rounded text-blue-600 focus:ring-blue-500">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                        <span>Santri Laju (Pulang Pergi / PP)</span>
                                    </span>
                                </label>
                            </div>
                            <p class="text-[9px] text-slate-400 mt-2">Centang <b>Santri Mukim</b> saja untuk tarif khusus pondok (seperti Syahriah Pondok / Kas Komplek). Hilangkan centang Santri Laju agar mereka tidak tertagih.</p>
                            @error('newConfigResidenceTargets') <span class="text-[10px] text-rose-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>

                        {{-- Radio Cards Target Type --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            {{-- All --}}
                            <label class="cursor-pointer" wire:key="target-all">
                                <input type="radio" wire:model.live="newConfigTargetType" value="all" class="sr-only peer">
                                <div class="flex flex-col items-center text-center p-4 rounded-2xl border-2 transition-all h-full
                                    peer-checked:border-emerald-500 peer-checked:bg-emerald-50/60 dark:peer-checked:bg-emerald-950/20 peer-checked:shadow-sm
                                    border-slate-200 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600
                                    bg-white dark:bg-slate-950/30 group">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 peer-checked:bg-emerald-500 peer-checked:text-white flex items-center justify-center mb-2.5 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
                                    <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 block leading-snug">Semua Santri</span>
                                    <span class="text-[9px] text-slate-400 dark:text-slate-500 leading-tight mt-1">Seluruh santri aktif pondok</span>
                                </div>
                            </label>

                            {{-- Dormitory --}}
                            <label class="cursor-pointer" wire:key="target-dormitory">
                                <input type="radio" wire:model.live="newConfigTargetType" value="dormitory" class="sr-only peer">
                                <div class="flex flex-col items-center text-center p-4 rounded-2xl border-2 transition-all h-full
                                    peer-checked:border-amber-500 peer-checked:bg-amber-50/60 dark:peer-checked:bg-amber-950/20 peer-checked:shadow-sm
                                    border-slate-200 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600
                                    bg-white dark:bg-slate-950/30 group">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 peer-checked:bg-amber-500 peer-checked:text-white flex items-center justify-center mb-2.5 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200 group-hover:text-amber-600 block leading-snug">Per Komplek</span>
                                    <span class="text-[9px] text-slate-400 dark:text-slate-500 leading-tight mt-1">Pilih asrama tertentu</span>
                                </div>
                            </label>

                            {{-- Kelas --}}
                            <label class="cursor-pointer" wire:key="target-kelas">
                                <input type="radio" wire:model.live="newConfigTargetType" value="kelas" class="sr-only peer">
                                <div class="flex flex-col items-center text-center p-4 rounded-2xl border-2 transition-all h-full
                                    peer-checked:border-purple-500 peer-checked:bg-purple-50/60 dark:peer-checked:bg-purple-950/20 peer-checked:shadow-sm
                                    border-slate-200 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600
                                    bg-white dark:bg-slate-950/30 group">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 peer-checked:bg-purple-500 peer-checked:text-white flex items-center justify-center mb-2.5 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    </div>
                                    <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200 group-hover:text-purple-600 block leading-snug">Per Kelas</span>
                                    <span class="text-[9px] text-slate-400 dark:text-slate-500 leading-tight mt-1">Pilih kelas madrasah</span>
                                </div>
                            </label>

                            {{-- Individual --}}
                            <label class="cursor-pointer" wire:key="target-individual">
                                <input type="radio" wire:model.live="newConfigTargetType" value="individual" class="sr-only peer">
                                <div class="flex flex-col items-center text-center p-4 rounded-2xl border-2 transition-all h-full
                                    peer-checked:border-emerald-500 peer-checked:bg-emerald-50/60 dark:peer-checked:bg-emerald-950/20 peer-checked:shadow-sm
                                    border-slate-200 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600
                                    bg-white dark:bg-slate-950/30 group">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 peer-checked:bg-emerald-500 peer-checked:text-white flex items-center justify-center mb-2.5 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 block leading-snug">Santri Tertentu</span>
                                    <span class="text-[9px] text-slate-400 dark:text-slate-500 leading-tight mt-1">Pilih nama per nama</span>
                                </div>
                            </label>
                        </div>

                        @error('newConfigTargetFilters')
                            <div class="p-3.5 bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 rounded-xl text-xs font-semibold flex items-center gap-2 animate-pulse">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $message }}
                            </div>
                        @enderror

                        {{-- Conditional Sub-Filters: DORMITORY --}}
                        @if($newConfigTargetType === 'dormitory')
                            <div class="space-y-4">
                                <div class="p-5 bg-amber-50/40 dark:bg-amber-950/10 border border-amber-200/60 dark:border-amber-800/30 rounded-2xl space-y-4">
                                    {{-- Header Controls --}}
                                    <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-amber-200/50 dark:border-amber-800/30">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            </div>
                                            <span class="block text-[11px] font-extrabold text-amber-900 dark:text-amber-300 uppercase tracking-wider">Pilih Komplek Asrama</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-[10px] font-bold">
                                            @if(in_array('L', $newConfigGenderTargets) && !$this->genderScope())
                                                <button type="button" wire:click="selectAllTargetFilters('L')" class="px-2.5 py-1 rounded-lg bg-sky-500/10 text-sky-700 dark:text-sky-300 hover:bg-sky-500/20 transition-all flex items-center gap-1">
                                                    <span>♂ All Putra</span>
                                                </button>
                                            @endif
                                            @if(in_array('P', $newConfigGenderTargets) && !$this->genderScope())
                                                <button type="button" wire:click="selectAllTargetFilters('P')" class="px-2.5 py-1 rounded-lg bg-rose-500/10 text-rose-700 dark:text-rose-300 hover:bg-rose-500/20 transition-all flex items-center gap-1">
                                                    <span>♀ All Putri</span>
                                                </button>
                                            @endif
                                            <button type="button" wire:click="selectAllTargetFilters('all')" class="px-2.5 py-1 rounded-lg bg-amber-500/15 text-amber-800 dark:text-amber-200 hover:bg-amber-500/25 transition-all">
                                                Pilih Semua
                                            </button>
                                            <button type="button" wire:click="clearAllTargetFilters" class="px-2.5 py-1 rounded-lg bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20 transition-all">
                                                Kosongkan
                                            </button>
                                        </div>
                                    </div>

                                    @php
                                        $dormsByGender = $dormitories->groupBy(fn($d) => $d->gender === 'P' ? 'Putri' : 'Putra');
                                    @endphp

                                    @foreach($dormsByGender as $genderLabel => $dormsGroup)
                                        @php
                                            $isPutri = $genderLabel === 'Putri';
                                            $groupDormIds = $dormsGroup->pluck('id')->toArray();
                                            $allGroupSelected = count(array_intersect($groupDormIds, $newConfigTargetFilters)) === count($groupDormIds) && count($groupDormIds) > 0;
                                        @endphp
                                        <div class="space-y-2.5">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 rounded-full {{ $isPutri ? 'bg-rose-500' : 'bg-sky-500' }}"></span>
                                                    <span class="text-[10px] font-extrabold uppercase tracking-wider {{ $isPutri ? 'text-rose-700 dark:text-rose-400' : 'text-sky-700 dark:text-sky-400' }}">
                                                        Komplek Asrama {{ $genderLabel }} ({{ count($dormsGroup) }})
                                                    </span>
                                                </div>
                                                <button type="button" wire:click="toggleTargetFilterGroup(@js($groupDormIds))" class="text-[10px] font-bold {{ $allGroupSelected ? 'text-rose-500 hover:text-rose-600' : 'text-amber-700 dark:text-amber-400 hover:underline' }}">
                                                    {{ $allGroupSelected ? '✕ Batalkan Grup Ini' : '+ Pilih Semua ' . $genderLabel }}
                                                </button>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                                                @foreach($dormsGroup as $d)
                                                    @php $isChecked = in_array($d->id, $newConfigTargetFilters); @endphp
                                                    <label class="cursor-pointer select-none" wire:key="dorm-{{ $d->id }}">
                                                        <input type="checkbox" wire:model.live="newConfigTargetFilters" value="{{ $d->id }}" class="sr-only">
                                                        <div class="p-3 rounded-xl border-2 transition-all flex items-start gap-3
                                                            {{ $isChecked
                                                                ? 'bg-amber-500/10 border-amber-500 dark:bg-amber-950/30 shadow-xs'
                                                                : 'bg-white dark:bg-slate-900 border-slate-200/80 dark:border-slate-800 hover:border-amber-300 dark:hover:border-amber-700' }}">
                                                            <div class="w-4 h-4 rounded-md border-2 mt-0.5 flex items-center justify-center shrink-0 transition-all
                                                                {{ $isChecked ? 'bg-amber-500 border-amber-500 text-white' : 'border-slate-300 dark:border-slate-600' }}">
                                                                @if($isChecked)
                                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"/></svg>
                                                                @endif
                                                            </div>
                                                            <div class="min-w-0 flex-1">
                                                                <span class="block text-xs font-bold text-slate-800 dark:text-slate-200 leading-snug truncate">
                                                                    {{ $d->name }}
                                                                </span>
                                                                <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-bold {{ $isPutri ? 'bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200/60 dark:border-rose-900/40' : 'bg-sky-50 text-sky-600 dark:bg-sky-950/40 dark:text-sky-400 border border-sky-200/60 dark:border-sky-900/40' }}">
                                                                        {{ $isPutri ? '♀ Putri' : '♂ Putra' }}
                                                                    </span>
                                                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                                                                        Asrama
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Selected Summary: Dormitory --}}
                                @if(!empty($newConfigTargetFilters))
                                    <div class="p-4 bg-white dark:bg-slate-900 border border-amber-200 dark:border-amber-800/40 rounded-2xl space-y-2.5 shadow-xs">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-extrabold text-amber-700 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                Komplek Terpilih ({{ count($newConfigTargetFilters) }})
                                            </span>
                                            <button type="button" wire:click="$set('newConfigTargetFilters', [])"
                                                class="text-[10px] font-bold text-rose-500 hover:text-rose-600 hover:underline transition-colors">
                                                Hapus Semua
                                            </button>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($dormitories->whereIn('id', $newConfigTargetFilters) as $sel)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 text-white rounded-xl text-xs font-bold shadow-xs">
                                                    <span>{{ $sel->name }}</span>
                                                    <button type="button" wire:click="removeTargetFilter('{{ $sel->id }}')"
                                                        class="text-white/80 hover:text-white font-extrabold leading-none text-sm ml-0.5 transition-colors">&times;</button>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="p-3.5 bg-amber-50/30 dark:bg-amber-950/5 border border-dashed border-amber-200 dark:border-amber-800/30 rounded-xl text-center">
                                        <span class="text-[10px] text-amber-600/80 dark:text-amber-500/70 font-semibold">Belum ada komplek yang dipilih — centang daftar di atas untuk menambahkan.</span>
                                    </div>
                                @endif
                            </div>

                        {{-- Conditional Sub-Filters: KELAS --}}
                        @elseif($newConfigTargetType === 'kelas')
                            <div class="space-y-4">
                                <div class="p-5 bg-purple-50/40 dark:bg-purple-950/10 border border-purple-200/60 dark:border-purple-800/30 rounded-2xl space-y-5">
                                    {{-- Header Controls --}}
                                    <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-purple-200/50 dark:border-purple-800/30">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-lg bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                            </div>
                                            <span class="block text-[11px] font-extrabold text-purple-900 dark:text-purple-300 uppercase tracking-wider">Pilih Kelas Madrasah Diniyyah</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-[10px] font-bold">
                                            @if(in_array('L', $newConfigGenderTargets) && !$this->genderScope())
                                                <button type="button" wire:click="selectAllTargetFilters('L')" class="px-2.5 py-1 rounded-lg bg-sky-500/10 text-sky-700 dark:text-sky-300 hover:bg-sky-500/20 transition-all flex items-center gap-1">
                                                    <span>♂ All Putra</span>
                                                </button>
                                            @endif
                                            @if(in_array('P', $newConfigGenderTargets) && !$this->genderScope())
                                                <button type="button" wire:click="selectAllTargetFilters('P')" class="px-2.5 py-1 rounded-lg bg-rose-500/10 text-rose-700 dark:text-rose-300 hover:bg-rose-500/20 transition-all flex items-center gap-1">
                                                    <span>♀ All Putri</span>
                                                </button>
                                            @endif
                                            <button type="button" wire:click="selectAllTargetFilters('all')" class="px-2.5 py-1 rounded-lg bg-purple-500/15 text-purple-800 dark:text-purple-200 hover:bg-purple-500/25 transition-all">
                                                Pilih Semua
                                            </button>
                                            <button type="button" wire:click="clearAllTargetFilters" class="px-2.5 py-1 rounded-lg bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20 transition-all">
                                                Kosongkan
                                            </button>
                                        </div>
                                    </div>

                                    @php
                                        // Group kelas by jenjang with explicit priority order: Awaliyah -> Wustho -> Ulya -> Tahasus
                                        $groupedKelas = $kelasList->groupBy(function($k) {
                                            $name = strtolower($k->name ?? '');
                                            $jenjang = strtolower($k->jenjang ?? '');

                                            if (str_contains($name, 'tahasus') || str_contains($jenjang, 'tahasus')) {
                                                return 'Program Khusus / Tahasus';
                                            }
                                            if (str_contains($jenjang, 'ula') || str_contains($name, 'awaliyah') || str_contains($name, 'ula')) {
                                                return 'Jenjang Awaliyah / Ula';
                                            }
                                            if (str_contains($jenjang, 'wustho') || str_contains($name, 'wustho')) {
                                                return 'Jenjang Wustho';
                                            }
                                            if (str_contains($jenjang, 'ulya') || str_contains($name, 'ulya')) {
                                                return 'Jenjang Ulya';
                                            }
                                            return 'Program Khusus / Lainnya';
                                        })->sortBy(function($classes, $key) {
                                            return match($key) {
                                                'Jenjang Awaliyah / Ula' => 1,
                                                'Jenjang Wustho' => 2,
                                                'Jenjang Ulya' => 3,
                                                'Program Khusus / Tahasus' => 4,
                                                default => 5,
                                            };
                                        });
                                    @endphp

                                    @foreach($groupedKelas as $jenjangTitle => $classes)
                                        @php
                                            $jenjangIds = $classes->pluck('id')->toArray();
                                            $allJenjangSelected = count(array_intersect($jenjangIds, $newConfigTargetFilters)) === count($jenjangIds) && count($jenjangIds) > 0;
                                        @endphp
                                        <div class="space-y-2.5">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-purple-900 dark:text-purple-300">
                                                        {{ $jenjangTitle }} ({{ count($classes) }})
                                                    </span>
                                                </div>
                                                <button type="button" wire:click="toggleTargetFilterGroup(@js($jenjangIds))" class="text-[10px] font-bold {{ $allJenjangSelected ? 'text-rose-500 hover:text-rose-600' : 'text-purple-700 dark:text-purple-300 hover:underline' }}">
                                                    {{ $allJenjangSelected ? '✕ Batalkan Jenjang Ini' : '+ Pilih Semua di ' . $jenjangTitle }}
                                                </button>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                                                @foreach($classes as $kls)
                                                    @php
                                                        $isChecked = in_array($kls->id, $newConfigTargetFilters);
                                                        $isPi = str_contains($kls->name, '(Pi)') || str_contains(strtolower($kls->name), 'putri');
                                                        $isPa = str_contains($kls->name, '(Pa)') || str_contains(strtolower($kls->name), 'putra');
                                                    @endphp
                                                    <label class="cursor-pointer select-none" wire:key="kelas-{{ $kls->id }}">
                                                        <input type="checkbox" wire:model.live="newConfigTargetFilters" value="{{ $kls->id }}" class="sr-only">
                                                        <div class="p-3 rounded-xl border-2 transition-all flex items-start gap-3
                                                            {{ $isChecked
                                                                ? 'bg-purple-500/10 border-purple-500 dark:bg-purple-950/30 shadow-xs'
                                                                : 'bg-white dark:bg-slate-900 border-slate-200/80 dark:border-slate-800 hover:border-purple-300 dark:hover:border-purple-700' }}">
                                                            <div class="w-4 h-4 rounded-md border-2 mt-0.5 flex items-center justify-center shrink-0 transition-all
                                                                {{ $isChecked ? 'bg-purple-500 border-purple-500 text-white' : 'border-slate-300 dark:border-slate-600' }}">
                                                                @if($isChecked)
                                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"/></svg>
                                                                @endif
                                                            </div>
                                                            <div class="min-w-0 flex-1">
                                                                <span class="block text-xs font-bold text-slate-800 dark:text-slate-200 leading-snug truncate">
                                                                    {{ $kls->name }}
                                                                </span>
                                                                <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                                                    @if($isPi)
                                                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200/60 dark:border-rose-900/40">
                                                                            ♀ Putri
                                                                        </span>
                                                                    @elseif($isPa)
                                                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-sky-50 text-sky-600 dark:bg-sky-950/40 dark:text-sky-400 border border-sky-200/60 dark:border-sky-900/40">
                                                                            ♂ Putra
                                                                        </span>
                                                                    @endif

                                                                    @if($kls->academic_year)
                                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                                                            <svg class="w-2.5 h-2.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                                            {{ $kls->academic_year }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Selected Summary: Kelas --}}
                                @if(!empty($newConfigTargetFilters))
                                    <div class="p-4 bg-white dark:bg-slate-900 border border-purple-200 dark:border-purple-800/40 rounded-2xl space-y-2.5 shadow-xs">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-extrabold text-purple-700 dark:text-purple-400 uppercase tracking-wider flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                Kelas Terpilih ({{ count($newConfigTargetFilters) }})
                                            </span>
                                            <button type="button" wire:click="$set('newConfigTargetFilters', [])"
                                                class="text-[10px] font-bold text-rose-500 hover:text-rose-600 hover:underline transition-colors">
                                                Hapus Semua
                                            </button>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($kelasList->whereIn('id', $newConfigTargetFilters) as $sel)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-600 text-white rounded-xl text-xs font-bold shadow-xs">
                                                    <span>{{ $sel->name }}</span>
                                                    <button type="button" wire:click="removeTargetFilter('{{ $sel->id }}')"
                                                        class="text-white/80 hover:text-white font-extrabold leading-none text-sm ml-0.5 transition-colors">&times;</button>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="p-3.5 bg-purple-50/30 dark:bg-purple-950/5 border border-dashed border-purple-200 dark:border-purple-800/30 rounded-xl text-center">
                                        <span class="text-[10px] text-purple-600/80 dark:text-purple-500/70 font-semibold">Belum ada kelas yang dipilih — centang daftar di atas untuk menambahkan.</span>
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
