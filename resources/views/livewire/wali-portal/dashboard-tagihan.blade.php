<div class="space-y-4 sm:space-y-5 pb-16" x-data="{
    openPayModal: false,
    showImageModal: false,
    modalImgSrc: '',
    modalImgTitle: '',
    clientCompressAndUpload(event) {
        const file = event.target.files[0];
        if (!file) return;
        if (window.compressImageFile) {
            window.compressImageFile(file, 1600, 0.8).then(compressed => {
                $wire.upload('proofImage', compressed, 
                    (uploadedFilename) => {}, 
                    () => { alert('Gagal memproses gambar. Coba gunakan foto lain.'); }, 
                    (event) => {}
                );
            });
        }
    }
}">

    <!-- Top Action Navigation -->
    <div class="flex items-center justify-between gap-2">
        <a href="{{ route('portal-wali.search') }}" 
           class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-emerald-700 dark:hover:text-emerald-400 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 px-3.5 py-2 rounded-2xl shadow-xs transition-all active:scale-95">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            <span>Cari Santri Lain</span>
        </a>

        @if(!empty($directWaUrl))
            <a href="{{ $directWaUrl }}" target="_blank"
               class="inline-flex items-center gap-1.5 text-xs font-extrabold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 px-3 py-2 rounded-2xl shadow-xs hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-all active:scale-95">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                <span>Bantuan WA Bendahara</span>
            </a>
        @endif
    </div>

    <!-- ─── KARTU IDENTITAS SANTRI (RAMAH & JELAS) ─────────────────────────── -->
    @php
        $activeRoom = $santri->roomAssignments->first()?->room;
        $activeDorm = $activeRoom?->dormitory;
        $activeKelas = $santri->madrasahEnrollments->first()?->kelas;
    @endphp
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="h-2 w-full bg-gradient-to-r from-emerald-600 via-teal-500 to-emerald-400"></div>

        <div class="p-4 sm:p-5 space-y-4">
            <!-- Header Status -->
            <div class="flex items-center justify-between gap-2 flex-wrap text-xs">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Santri Aktif Pesantren</span>
                </span>
                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">
                    Diperbarui: {{ $lastUpdatedLabel }}
                </span>
            </div>

            <!-- Profile Info: Foto + Nama + Identitas -->
            <div class="flex items-center gap-3.5">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white flex items-center justify-center font-black text-xl shrink-0 shadow-sm overflow-hidden border-2 border-white dark:border-slate-800">
                    @if($santri->photo)
                        <img src="{{ Storage::url($santri->photo) }}" alt="{{ $santri->name }}" class="w-full h-full object-cover">
                    @else
                        <span>{{ strtoupper(substr($santri->name, 0, 2)) }}</span>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white truncate leading-tight">
                        {{ $santri->name }}
                    </h2>
                    <div class="flex items-center gap-2 mt-1 flex-wrap text-[11px] text-slate-600 dark:text-slate-300">
                        @if($santri->nis)
                            <span class="bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md font-mono font-bold">
                                NIS: {{ $santri->nis }}
                            </span>
                        @endif
                        <span class="bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md font-semibold">
                            {{ $isPutri ? 'Santri Putri' : 'Santri Putra' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Info Asrama & Kelas -->
            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 dark:border-slate-800/80 text-xs">
                <div class="bg-slate-50 dark:bg-slate-950 p-2.5 rounded-2xl border border-slate-200/80 dark:border-slate-800/80">
                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 block uppercase">Komplek & Kamar</span>
                    <strong class="text-slate-800 dark:text-slate-200 text-xs block font-extrabold mt-0.5 truncate">
                        {{ $activeDorm ? $activeDorm->name : '-' }} {{ $activeRoom ? '('.$activeRoom->name.')' : '' }}
                    </strong>
                </div>
                <div class="bg-slate-50 dark:bg-slate-950 p-2.5 rounded-2xl border border-slate-200/80 dark:border-slate-800/80">
                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 block uppercase">Kelas Madrasah</span>
                    <strong class="text-slate-800 dark:text-slate-200 text-xs block font-extrabold mt-0.5 truncate">
                        {{ $activeKelas ? $activeKelas->name : '-' }}
                    </strong>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── 3 TAB NAVIGASI UTAMA (UKURAN BESAR & NYAMAN) ─────────────────── -->
    <div class="grid grid-cols-3 gap-1.5 p-1.5 bg-slate-200/80 dark:bg-slate-800/80 rounded-2xl border border-slate-300 dark:border-slate-700/60 shadow-xs">
        <button type="button" 
                wire:click="setPortalTab('tagihan')" 
                class="py-2.5 px-2 rounded-xl text-xs font-black transition-all flex flex-col items-center justify-center gap-0.5 {{ $portalTab === 'tagihan' ? 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">
            <span class="text-sm">📋</span>
            <span>Tagihan</span>
        </button>

        <button type="button" 
                wire:click="setPortalTab('bayar')" 
                class="py-2.5 px-2 rounded-xl text-xs font-black transition-all flex flex-col items-center justify-center gap-0.5 relative {{ $portalTab === 'bayar' ? 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">
            <span class="text-sm">💳</span>
            <span>Bayar / Transfer</span>
            @if($totalHarusDibayarNow > 0)
                <span class="absolute top-1 right-2 w-2 h-2 rounded-full bg-amber-500 ring-2 ring-white"></span>
            @endif
        </button>

        <button type="button" 
                wire:click="setPortalTab('riwayat')" 
                class="py-2.5 px-2 rounded-xl text-xs font-black transition-all flex flex-col items-center justify-center gap-0.5 {{ $portalTab === 'riwayat' ? 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">
            <span class="text-sm">📜</span>
            <span>Riwayat</span>
        </button>
    </div>

    <!-- Alert / Toast Sukses & Error -->
    @if(!empty($manualSuccessMessage))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-700 rounded-2xl flex items-start gap-3 text-xs text-emerald-900 dark:text-emerald-200">
            <span class="text-xl shrink-0">✅</span>
            <div class="flex-1 font-semibold leading-relaxed">
                {{ $manualSuccessMessage }}
            </div>
        </div>
    @endif

    @if(!empty($manualErrorMessage))
        <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-700 rounded-2xl flex items-start gap-3 text-xs text-rose-900 dark:text-rose-200">
            <span class="text-xl shrink-0">⚠️</span>
            <div class="flex-1 font-semibold leading-relaxed">
                {{ $manualErrorMessage }}
            </div>
        </div>
    @endif


    <!-- =================================================================== -->
    <!-- TAB 1: TAGIHAN SANTRI (DAFTAR LENGKAP & STATUS)                      -->
    <!-- =================================================================== -->
    @if($portalTab === 'tagihan')
        <div class="space-y-4">
            
            <!-- Summary Banner Status Tagihan -->
            @if($totalHarusDibayarNow <= 0)
                <div class="bg-gradient-to-br from-emerald-500 to-teal-700 text-white rounded-3xl p-5 shadow-sm text-center space-y-2">
                    <div class="w-12 h-12 rounded-full bg-white/20 text-white text-2xl flex items-center justify-center mx-auto">
                        ✓
                    </div>
                    <h3 class="text-base font-black">Alhamdulillah, Semua Tagihan Lunas!</h3>
                    <p class="text-xs text-emerald-100 max-w-xs mx-auto leading-relaxed">
                        Tidak ada tagihan tertunggak untuk ananda {{ $santri->name }}. Terima kasih atas pembayarannya.
                    </p>
                </div>
            @else
                <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-3xl p-5 shadow-sm space-y-3 border border-slate-700">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-amber-300 uppercase tracking-wider">Total Tagihan Saat Ini</span>
                        <span class="text-[10px] bg-amber-400/20 text-amber-300 border border-amber-400/30 px-2 py-0.5 rounded-full font-bold">
                            Belum Lunas
                        </span>
                    </div>

                    <div class="flex items-baseline justify-between">
                        <div class="text-2xl sm:text-3xl font-black text-white">
                            Rp {{ number_format($totalHarusDibayarNow, 0, ',', '.') }}
                        </div>
                    </div>

                    @if($totalPastTunggakan > 0)
                        <div class="text-xs text-amber-200 bg-amber-500/20 p-2.5 rounded-xl border border-amber-500/30 flex items-center gap-2">
                            <span>⚠️</span>
                            <span>Termasuk tunggakan bulan lalu sebesar <strong>Rp {{ number_format($totalPastTunggakan, 0, ',', '.') }}</strong></span>
                        </div>
                    @endif

                    <!-- Tombol Cepat Lanjut Pembayaran -->
                    <button type="button" 
                            wire:click="setPortalTab('bayar')"
                            class="w-full py-3 px-4 bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600 text-slate-950 font-black rounded-2xl transition-all shadow-md flex items-center justify-center gap-2 text-xs">
                        <span>Lanjut ke Menu Pembayaran</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            @endif

            <!-- ─── RINCIAN ITEM TAGIHAN TERKELOMPOK ────────────────────────── -->
            <div class="space-y-3">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 px-1 flex items-center gap-1.5">
                    <span>📑</span>
                    <span>Rincian Semua Pos Tagihan</span>
                </h3>

                <!-- 1. Tagihan Tertunggak Bulan Lalu (Jika Ada) -->
                @if($pastUnpaidBills->isNotEmpty())
                    <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900 rounded-3xl p-4 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-rose-700 dark:text-rose-400 uppercase tracking-wider flex items-center gap-1.5">
                                <span>⚠️</span>
                                <span>Tunggakan Bulan Lalu ({{ $pastUnpaidBills->count() }})</span>
                            </span>
                            <span class="text-xs font-black text-rose-700 dark:text-rose-400">
                                Rp {{ number_format($totalPastTunggakan, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="space-y-2">
                            @foreach($pastUnpaidBills as $bill)
                                @php
                                    $remaining = max(0, $bill->amount - $bill->amount_paid);
                                    $isPartial = $bill->status === 'partial';
                                @endphp
                                <div class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-rose-200 dark:border-rose-900/60 flex items-center justify-between text-xs">
                                    <div>
                                        <div class="font-extrabold text-slate-900 dark:text-white">
                                            {{ $this->getBillDisplayName($bill) }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                            Periode: {{ $this->getBillPeriodLabel($bill) }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-black text-rose-600 dark:text-rose-400 text-sm">
                                            Rp {{ number_format($remaining, 0, ',', '.') }}
                                        </div>
                                        @if($isPartial)
                                            <span class="text-[9px] text-amber-600 dark:text-amber-400 font-bold block">Dicicil sebagian</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- 2. Tagihan Bulan Ini -->
                @if($currentMonthBills->isNotEmpty())
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 space-y-2.5 shadow-xs">
                        <div class="flex items-center justify-between pb-1 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-xs font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-1.5">
                                <span>🗓️</span>
                                <span>Tagihan Bulan Ini ({{ $currentMonthName }} {{ $currentYear }})</span>
                            </span>
                        </div>

                        <div class="space-y-2">
                            @foreach($currentMonthBills as $bill)
                                @php
                                    $isPaid = $bill->status === 'paid';
                                    $remaining = max(0, $bill->amount - $bill->amount_paid);
                                @endphp
                                <div class="p-3 rounded-2xl border flex items-center justify-between text-xs {{ $isPaid ? 'bg-emerald-50/60 dark:bg-emerald-950/20 border-emerald-200/80 dark:border-emerald-800/40' : 'bg-slate-50 dark:bg-slate-950 border-slate-200/80 dark:border-slate-800' }}">
                                    <div>
                                        <div class="font-extrabold {{ $isPaid ? 'text-emerald-900 dark:text-emerald-200' : 'text-slate-900 dark:text-white' }}">
                                            {{ $this->getBillDisplayName($bill) }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                            Nominal: Rp {{ number_format($bill->amount, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        @if($isPaid)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase">
                                                ✓ Lunas
                                            </span>
                                        @else
                                            <span class="font-black text-slate-900 dark:text-white text-sm block">
                                                Rp {{ number_format($remaining, 0, ',', '.') }}
                                            </span>
                                            <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 block">Belum Lunas</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- 3. Iuran Acara & Kitab / Insidental -->
                @if($eventBills->isNotEmpty())
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 space-y-2.5 shadow-xs">
                        <div class="flex items-center justify-between pb-1 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-xs font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-1.5">
                                <span>📚</span>
                                <span>Iuran Kegiatan, Kitab & Lainnya</span>
                            </span>
                        </div>

                        <div class="space-y-2">
                            @foreach($eventBills as $bill)
                                @php
                                    $isPaid = $bill->status === 'paid';
                                    $remaining = max(0, $bill->amount - $bill->amount_paid);
                                @endphp
                                <div class="p-3 rounded-2xl border flex items-center justify-between text-xs {{ $isPaid ? 'bg-emerald-50/60 dark:bg-emerald-950/20 border-emerald-200/80 dark:border-emerald-800/40' : 'bg-slate-50 dark:bg-slate-950 border-slate-200/80 dark:border-slate-800' }}">
                                    <div>
                                        <div class="font-extrabold text-slate-900 dark:text-white">
                                            {{ $this->getBillDisplayName($bill) }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                            {{ $this->getBillPeriodLabel($bill) }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($isPaid)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase">
                                                ✓ Lunas
                                            </span>
                                        @else
                                            <span class="font-black text-slate-900 dark:text-white text-sm block">
                                                Rp {{ number_format($remaining, 0, ',', '.') }}
                                            </span>
                                            <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 block">Belum Lunas</span>
                                        @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </div>
    @endif


    <!-- =================================================================== -->
    <!-- TAB 2: MENU BAYAR / TRANSFER (ALUR CHECKOUT RAMPING & ELEGAN)        -->
    <!-- =================================================================== -->
    @if($portalTab === 'bayar')
        <div class="space-y-3.5">

            <!-- ─── LANGKAH 1: PILIH TAGIHAN (COMPACT LIST) ──────────────────── -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-3.5 sm:p-4 space-y-3 shadow-xs">
                
                <!-- Header Toolbar & Smart Quick Chips -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white text-[11px] flex items-center justify-center font-black">1</span>
                            <span>Pilih Tagihan yang Ingin Dibayar</span>
                        </h3>
                    </div>

                    <!-- Quick Chips Toolbar -->
                    <div class="flex flex-wrap items-center gap-1 text-[10px]">
                        @if($hasPastUnpaid)
                            <button type="button" 
                                    wire:click="selectQuickMode('all_active')"
                                    class="font-bold text-emerald-800 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-emerald-800 transition-all active:scale-95 flex items-center gap-1">
                                <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Semua Wajib</span>
                            </button>
                            <button type="button" 
                                    wire:click="selectQuickMode('past_only')"
                                    class="font-bold text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 hover:bg-amber-100 px-2.5 py-1 rounded-lg border border-amber-200 dark:border-amber-800 transition-all active:scale-95 flex items-center gap-1">
                                <svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Tunggakan Dulu</span>
                            </button>
                        @else
                            <button type="button" 
                                    wire:click="selectQuickMode('all_active')"
                                    class="font-bold text-emerald-800 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-emerald-800 transition-all active:scale-95 flex items-center gap-1">
                                <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Semua Bulan Ini</span>
                            </button>
                        @endif
                        <button type="button" 
                                wire:click="selectQuickMode('none')"
                                class="font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-700 transition-all active:scale-95">
                            Batal
                        </button>
                    </div>
                </div>

                <!-- FIFO Notice Banner (Compact) -->
                @if($fifoNotice)
                    <div class="p-2.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-700/60 rounded-xl text-[11px] text-amber-900 dark:text-amber-200 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="flex-1 font-medium">{{ $fifoNotice }}</span>
                    </div>
                @endif

                @if($pastUnpaidList->isEmpty() && $currentUnpaidList->isEmpty() && $futureUnpaidList->isEmpty())
                    <div class="py-6 text-center text-xs text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 rounded-xl border border-emerald-200 dark:border-emerald-800">
                        🎉 Alhamdulillah, semua tagihan santri sudah lunas!
                    </div>
                @else
                    <div class="space-y-3">
                        <!-- ─── GRUP 1: TUNGGAKAN BULAN LALU ────────────────────────── -->
                        @if($pastUnpaidList->isNotEmpty())
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-wider text-rose-600 dark:text-rose-400 px-1">
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                                        <span>Tunggakan Bulan Lalu (Wajib Didahulukan)</span>
                                    </span>
                                    <span>{{ $pastUnpaidList->count() }} Tagihan</span>
                                </div>

                                <div class="space-y-1.5">
                                    @foreach($pastUnpaidList as $bill)
                                        @php
                                            $maxKekurangan = max(0, (float)$bill->amount - (float)$bill->amount_paid);
                                            $isChecked = in_array($bill->id, $selectedBillIds);
                                            $isEditingCustom = $editingCustomBills[$bill->id] ?? false;
                                            $customVal = $customAmounts[$bill->id] ?? null;
                                            $payAmount = (isset($customVal) && is_numeric($customVal) && (float)$customVal > 0)
                                                ? min($maxKekurangan, (float)$customVal)
                                                : $maxKekurangan;
                                            $sisaTagihan = max(0, $maxKekurangan - $payAmount);
                                        @endphp
                                        <div class="p-2.5 sm:p-3 rounded-xl border transition-all {{ $isChecked ? 'border-rose-400 bg-rose-50/40 dark:bg-rose-950/20' : 'border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 hover:border-slate-300' }}">
                                            <div class="flex items-center gap-2.5">
                                                <input type="checkbox" 
                                                       wire:click="toggleBillSelection('{{ $bill->id }}')"
                                                       {{ $isChecked ? 'checked' : '' }}
                                                       class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 border-slate-300 dark:border-slate-700 cursor-pointer">
                                                
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between gap-1">
                                                        <span class="font-extrabold text-xs text-slate-900 dark:text-white truncate">
                                                            {{ $this->getBillDisplayName($bill) }}
                                                        </span>
                                                        <span class="text-xs font-black text-rose-600 dark:text-rose-400 shrink-0">
                                                            Rp {{ number_format($maxKekurangan, 0, ',', '.') }}
                                                        </span>
                                                    </div>
                                                    <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                                        {{ $this->getBillPeriodLabel($bill) }}
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Inline Cicilan Bar if Checked -->
                                            @if($isChecked)
                                                <div class="mt-2 pt-1.5 border-t border-rose-200/60 dark:border-rose-900/40 flex flex-wrap items-center justify-between gap-1.5 text-[10px]">
                                                    @if(!$isEditingCustom)
                                                        <span class="text-emerald-700 dark:text-emerald-400 font-bold">
                                                            ✓ Bayar Penuh: Rp {{ number_format($payAmount, 0, ',', '.') }}
                                                        </span>
                                                        <button type="button" 
                                                                wire:click="toggleCustomAmountInput('{{ $bill->id }}')"
                                                                class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                                            ✏️ Bayar Sebagian / Cicil
                                                        </button>
                                                    @else
                                                        <div class="w-full space-y-1.5 bg-white dark:bg-slate-900 p-2 rounded-lg border border-slate-200 dark:border-slate-800">
                                                            <div class="flex items-center justify-between">
                                                                <span class="font-bold text-slate-700 dark:text-slate-300">Nominal Cicilan:</span>
                                                                <button type="button" 
                                                                        wire:click="resetBillCustomAmount('{{ $bill->id }}')"
                                                                        class="text-[10px] font-bold text-slate-400 hover:text-slate-600 underline">
                                                                    ✕ Batal Cicil
                                                                </button>
                                                            </div>
                                                            <div class="flex items-center gap-1.5">
                                                                <button type="button" 
                                                                        wire:click="setCustomAmountPercent('{{ $bill->id }}', 50, {{ $maxKekurangan }})"
                                                                        class="px-2 py-0.5 rounded border text-[10px] font-bold bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                                                    50%
                                                                </button>
                                                                <button type="button" 
                                                                        wire:click="setCustomAmountPercent('{{ $bill->id }}', 75, {{ $maxKekurangan }})"
                                                                        class="px-2 py-0.5 rounded border text-[10px] font-bold bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                                                    75%
                                                                </button>
                                                                <input type="number" 
                                                                       wire:model.live.debounce.300ms="customAmounts.{{ $bill->id }}"
                                                                       placeholder="{{ (int)$maxKekurangan }}"
                                                                       max="{{ (int)$maxKekurangan }}"
                                                                       class="flex-1 py-1 px-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded text-xs font-bold text-slate-900 dark:text-white">
                                                            </div>
                                                            @if($sisaTagihan > 0)
                                                                <div class="text-[10px] text-amber-700 dark:text-amber-400 font-bold flex justify-between pt-0.5">
                                                                    <span>Bayar: Rp {{ number_format($payAmount, 0, ',', '.') }}</span>
                                                                    <span>Sisa: Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- ─── GRUP 2: TAGIHAN BULAN BERJALAN & KEGIATAN ────────────── -->
                        @if($currentUnpaidList->isNotEmpty())
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-400 px-1">
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <span>Tagihan Bulan Berjalan & Kegiatan</span>
                                    </span>
                                    <span>{{ $currentUnpaidList->count() }} Tagihan</span>
                                </div>

                                <div class="space-y-1.5">
                                    @foreach($currentUnpaidList as $bill)
                                        @php
                                            $maxKekurangan = max(0, (float)$bill->amount - (float)$bill->amount_paid);
                                            $isChecked = in_array($bill->id, $selectedBillIds);
                                            $isEditingCustom = $editingCustomBills[$bill->id] ?? false;
                                            $customVal = $customAmounts[$bill->id] ?? null;
                                            $payAmount = (isset($customVal) && is_numeric($customVal) && (float)$customVal > 0)
                                                ? min($maxKekurangan, (float)$customVal)
                                                : $maxKekurangan;
                                            $sisaTagihan = max(0, $maxKekurangan - $payAmount);
                                        @endphp
                                        <div class="p-2.5 sm:p-3 rounded-xl border transition-all {{ $isChecked ? 'border-emerald-500 bg-emerald-50/40 dark:bg-emerald-950/20' : 'border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 hover:border-slate-300' }}">
                                            <div class="flex items-center gap-2.5">
                                                <input type="checkbox" 
                                                       wire:click="toggleBillSelection('{{ $bill->id }}')"
                                                       {{ $isChecked ? 'checked' : '' }}
                                                       class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-700 cursor-pointer">
                                                
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between gap-1">
                                                        <span class="font-extrabold text-xs text-slate-900 dark:text-white truncate">
                                                            {{ $this->getBillDisplayName($bill) }}
                                                        </span>
                                                        <span class="text-xs font-black text-emerald-700 dark:text-emerald-400 shrink-0">
                                                            Rp {{ number_format($maxKekurangan, 0, ',', '.') }}
                                                        </span>
                                                    </div>
                                                    <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                                        {{ $this->getBillPeriodLabel($bill) }}
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Inline Cicilan Bar if Checked -->
                                            @if($isChecked)
                                                <div class="mt-2 pt-1.5 border-t border-emerald-200/60 dark:border-emerald-900/40 flex flex-wrap items-center justify-between gap-1.5 text-[10px]">
                                                    @if(!$isEditingCustom)
                                                        <span class="text-emerald-700 dark:text-emerald-400 font-bold">
                                                            ✓ Bayar Penuh: Rp {{ number_format($payAmount, 0, ',', '.') }}
                                                        </span>
                                                        <button type="button" 
                                                                wire:click="toggleCustomAmountInput('{{ $bill->id }}')"
                                                                class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                                            ✏️ Bayar Sebagian / Cicil
                                                        </button>
                                                    @else
                                                        <div class="w-full space-y-1.5 bg-white dark:bg-slate-900 p-2 rounded-lg border border-slate-200 dark:border-slate-800">
                                                            <div class="flex items-center justify-between">
                                                                <span class="font-bold text-slate-700 dark:text-slate-300">Nominal Cicilan:</span>
                                                                <button type="button" 
                                                                        wire:click="resetBillCustomAmount('{{ $bill->id }}')"
                                                                        class="text-[10px] font-bold text-slate-400 hover:text-slate-600 underline">
                                                                    ✕ Batal Cicil
                                                                </button>
                                                            </div>
                                                            <div class="flex items-center gap-1.5">
                                                                <button type="button" 
                                                                        wire:click="setCustomAmountPercent('{{ $bill->id }}', 50, {{ $maxKekurangan }})"
                                                                        class="px-2 py-0.5 rounded border text-[10px] font-bold bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                                                    50%
                                                                </button>
                                                                <button type="button" 
                                                                        wire:click="setCustomAmountPercent('{{ $bill->id }}', 75, {{ $maxKekurangan }})"
                                                                        class="px-2 py-0.5 rounded border text-[10px] font-bold bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                                                    75%
                                                                </button>
                                                                <input type="number" 
                                                                       wire:model.live.debounce.300ms="customAmounts.{{ $bill->id }}"
                                                                       placeholder="{{ (int)$maxKekurangan }}"
                                                                       max="{{ (int)$maxKekurangan }}"
                                                                       class="flex-1 py-1 px-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded text-xs font-bold text-slate-900 dark:text-white">
                                                            </div>
                                                            @if($sisaTagihan > 0)
                                                                <div class="text-[10px] text-amber-700 dark:text-amber-400 font-bold flex justify-between pt-0.5">
                                                                    <span>Bayar: Rp {{ number_format($payAmount, 0, ',', '.') }}</span>
                                                                    <span>Sisa: Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- ─── GRUP 3: ACCORDION TAGIHAN BULAN MENDATANG ───────────── -->
                        @if($futureUnpaidList->isNotEmpty())
                            <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden bg-slate-50/50 dark:bg-slate-950/50">
                                <button type="button" 
                                        wire:click="toggleShowFutureBills"
                                        class="w-full p-2.5 sm:p-3 flex items-center justify-between text-left text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100/60 dark:hover:bg-slate-900/60 transition-all">
                                    <span class="flex items-center gap-1.5">
                                        <span>📅</span>
                                        <span>Bayar di Muka Bulan Depan ({{ $futureUnpaidList->count() }} Tagihan)</span>
                                    </span>
                                    <span class="text-xs text-slate-400 transform transition-transform duration-200 {{ $showFutureBills ? 'rotate-180' : '' }}">
                                        ▼
                                    </span>
                                </button>

                                @if($showFutureBills)
                                    <div class="p-2.5 pt-0 space-y-1.5 border-t border-slate-200 dark:border-slate-800">
                                        @foreach($futureUnpaidList as $bill)
                                            @php
                                                $maxKekurangan = max(0, (float)$bill->amount - (float)$bill->amount_paid);
                                                $isChecked = in_array($bill->id, $selectedBillIds);
                                                $isEditingCustom = $editingCustomBills[$bill->id] ?? false;
                                                $customVal = $customAmounts[$bill->id] ?? null;
                                                $payAmount = (isset($customVal) && is_numeric($customVal) && (float)$customVal > 0)
                                                    ? min($maxKekurangan, (float)$customVal)
                                                    : $maxKekurangan;
                                                $sisaTagihan = max(0, $maxKekurangan - $payAmount);
                                            @endphp
                                            <div class="p-2.5 rounded-xl border transition-all {{ $isChecked ? 'border-emerald-500 bg-emerald-50/40 dark:bg-emerald-950/20' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-slate-300' }}">
                                                <div class="flex items-center gap-2.5">
                                                    <input type="checkbox" 
                                                           wire:click="toggleBillSelection('{{ $bill->id }}')"
                                                           {{ $isChecked ? 'checked' : '' }}
                                                           class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-700 cursor-pointer">
                                                    
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center justify-between gap-1">
                                                            <span class="font-extrabold text-xs text-slate-900 dark:text-white truncate">
                                                                {{ $this->getBillDisplayName($bill) }}
                                                            </span>
                                                            <span class="text-xs font-black text-emerald-700 dark:text-emerald-400 shrink-0">
                                                                Rp {{ number_format($maxKekurangan, 0, ',', '.') }}
                                                            </span>
                                                        </div>
                                                        <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                                            {{ $this->getBillPeriodLabel($bill) }}
                                                        </div>
                                                    </div>
                                                </div>

                                                @if($isChecked)
                                                    <div class="mt-2 pt-1.5 border-t border-emerald-200/60 dark:border-emerald-900/40 flex flex-wrap items-center justify-between gap-1.5 text-[10px]">
                                                        @if(!$isEditingCustom)
                                                            <span class="text-emerald-700 dark:text-emerald-400 font-bold">
                                                                ✓ Bayar Penuh: Rp {{ number_format($payAmount, 0, ',', '.') }}
                                                            </span>
                                                            <button type="button" 
                                                                    wire:click="toggleCustomAmountInput('{{ $bill->id }}')"
                                                                    class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                                                ✏️ Bayar Sebagian / Cicil
                                                            </button>
                                                        @else
                                                            <div class="w-full space-y-1.5 bg-white dark:bg-slate-900 p-2 rounded-lg border border-slate-200 dark:border-slate-800">
                                                                <div class="flex items-center justify-between">
                                                                    <span class="font-bold text-slate-700 dark:text-slate-300">Nominal Cicilan:</span>
                                                                    <button type="button" 
                                                                            wire:click="resetBillCustomAmount('{{ $bill->id }}')"
                                                                            class="text-[10px] font-bold text-slate-400 hover:text-slate-600 underline">
                                                                        ✕ Batal Cicil
                                                                    </button>
                                                                </div>
                                                                <div class="flex items-center gap-1.5">
                                                                    <button type="button" 
                                                                            wire:click="setCustomAmountPercent('{{ $bill->id }}', 50, {{ $maxKekurangan }})"
                                                                            class="px-2 py-0.5 rounded border text-[10px] font-bold bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                                                        50%
                                                                    </button>
                                                                    <button type="button" 
                                                                            wire:click="setCustomAmountPercent('{{ $bill->id }}', 75, {{ $maxKekurangan }})"
                                                                            class="px-2 py-0.5 rounded border text-[10px] font-bold bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                                                        75%
                                                                    </button>
                                                                    <input type="number" 
                                                                           wire:model.live.debounce.300ms="customAmounts.{{ $bill->id }}"
                                                                           placeholder="{{ (int)$maxKekurangan }}"
                                                                           max="{{ (int)$maxKekurangan }}"
                                                                           class="flex-1 py-1 px-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded text-xs font-bold text-slate-900 dark:text-white">
                                                                </div>
                                                                @if($sisaTagihan > 0)
                                                                    <div class="text-[10px] text-amber-700 dark:text-amber-400 font-bold flex justify-between pt-0.5">
                                                                        <span>Bayar: Rp {{ number_format($payAmount, 0, ',', '.') }}</span>
                                                                        <span>Sisa: Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            </div>


            <!-- ─── LANGKAH 2: TITIPAN UANG SAKU ANAK (COMPACT ROW) ─────────── -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-3.5 sm:p-4 space-y-2.5 shadow-xs">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-purple-600 text-white text-[11px] flex items-center justify-center font-black">2</span>
                        <div>
                            <strong class="text-xs font-black text-slate-900 dark:text-white block">Titip Uang Saku Santri</strong>
                            <span class="text-[10px] text-slate-400">Sekalian transfer uang jajan ke pondok</span>
                        </div>
                    </div>

                    <!-- Toggle Button -->
                    <button type="button" 
                            wire:click="togglePocketMoney"
                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $includePocketMoney ? 'bg-purple-600' : 'bg-slate-300 dark:bg-slate-700' }}">
                        <span class="sr-only">Titip Uang Saku</span>
                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $includePocketMoney ? 'translate-x-4' : 'translate-x-0' }}"></span>
                    </button>
                </div>

                @if($includePocketMoney)
                    <div class="pt-2 space-y-2 border-t border-slate-100 dark:border-slate-800">
                        <div class="grid grid-cols-4 gap-1.5">
                            @foreach($presetPocketMoney as $preset)
                                <button type="button" 
                                        wire:click="selectPresetPocketMoney({{ $preset }})"
                                        class="py-1.5 px-1 rounded-lg text-[10px] font-black transition-all border text-center {{ $pocketMoneyAmount == $preset ? 'bg-purple-600 text-white border-purple-600' : 'bg-slate-50 dark:bg-slate-950 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-slate-300' }}">
                                    {{ number_format($preset / 1000, 0) }}rb
                                </button>
                            @endforeach
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-slate-400">Nominal Lain: Rp</span>
                            <input type="number" 
                                   wire:model.live.debounce.300ms="customPocketMoney"
                                   placeholder="150000"
                                   class="flex-1 py-1 px-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-bold text-slate-900 dark:text-white">
                        </div>
                    </div>
                @endif
            </div>


            <!-- ─── LANGKAH 3: RINGKASAN TOTAL TRANSFER ─────────────────────── -->
            @php
                $grandTotal = $this->getGrandTotalTransfer();
            @endphp
            <div x-data="{ showDetail: false }" class="bg-gradient-to-br from-emerald-950 via-slate-900 to-slate-950 text-white border border-emerald-800/80 rounded-2xl p-4 space-y-2.5 shadow-sm">
                
                <!-- Row Tagihan Terpilih (Interactive Accordion) -->
                <div>
                    <button type="button" 
                            @click="showDetail = !showDetail"
                            class="w-full flex items-center justify-between text-xs pb-1.5 border-b border-emerald-800/60 hover:text-emerald-300 transition-colors">
                        <span class="flex items-center gap-1.5 text-slate-300">
                            <span>Tagihan Terpilih ({{ count($selectedBillIds) }})</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-800/80 text-emerald-200 font-bold flex items-center gap-0.5">
                                <span x-text="showDetail ? 'Tutup Rincian' : 'Lihat Rincian'">Lihat Rincian</span>
                                <span class="transform transition-transform duration-200" :class="showDetail ? 'rotate-180' : ''">▾</span>
                            </span>
                        </span>
                        <strong class="text-white font-bold">Rp {{ number_format($simulasiTotal, 0, ',', '.') }}</strong>
                    </button>

                    <!-- Expanded Breakdown List -->
                    <div x-show="showDetail" x-transition x-cloak class="pt-2 pb-1 space-y-1.5 text-[11px] border-b border-emerald-800/40">
                        @forelse($simulasiHasil as $item)
                            <div class="flex items-center justify-between text-slate-300 pl-2 border-l-2 border-emerald-500/60 py-0.5">
                                <div class="truncate pr-2">
                                    <span class="text-white font-medium">• {{ $item['label'] }}</span>
                                    @if($item['is_partial'] ?? false)
                                        <span class="text-amber-300 text-[9px] font-bold bg-amber-950/60 px-1 py-0.2 rounded border border-amber-500/40 ml-1">Cicil</span>
                                    @endif
                                </div>
                                <span class="font-bold text-white shrink-0">Rp {{ number_format($item['terbayar'], 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <span class="text-[10px] text-slate-400 italic">Belum ada tagihan yang dipilih.</span>
                        @endforelse
                    </div>
                </div>

                @if($includePocketMoney && $pocketMoneyAmount > 0)
                    <div class="flex items-center justify-between text-xs text-purple-300 pb-1.5 border-b border-emerald-800/60">
                        <span class="flex items-center gap-1">
                            <span>💰</span>
                            <span>Titipan Uang Saku Santri</span>
                        </span>
                        <strong class="text-purple-200 font-bold">+ Rp {{ number_format($pocketMoneyAmount, 0, ',', '.') }}</strong>
                    </div>
                @endif

                <div class="flex items-center justify-between pt-0.5">
                    <span class="text-[11px] font-black text-emerald-400 uppercase tracking-wider">TOTAL TRANSFER</span>
                    <span class="text-xl font-black text-white">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                </div>
            </div>


            <!-- ─── LANGKAH 4: PILIH METODE & FORM CHECKOUT ─────────────────── -->
            <!-- ─── LANGKAH 4: PILIH METODE & FORM CHECKOUT ─────────────────── -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-3.5 sm:p-4 space-y-3 shadow-xs">
                <h3 class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded-full bg-emerald-600 text-white text-[11px] flex items-center justify-center font-black">3</span>
                    <span>Pilih Metode Pembayaran</span>
                </h3>

                @php
                    $isDokuEnabled = \App\Modules\Keuangan\Services\DokuService::isEnabled();
                @endphp

                <!-- Segmented Tabs Pembayaran -->
                <div class="grid grid-cols-2 gap-1.5 p-1 bg-slate-100 dark:bg-slate-950 rounded-xl border border-slate-200 dark:border-slate-800">
                    @if($isDokuEnabled)
                        <button type="button" 
                                wire:click="setCheckoutMethod('doku')"
                                class="py-2.5 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 {{ $checkoutMethod === 'doku' ? 'bg-white dark:bg-slate-800 text-emerald-700 dark:text-emerald-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
                            <svg class="w-4 h-4 {{ $checkoutMethod === 'doku' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            <span>Bayar Online</span>
                        </button>
                    @endif

                    <button type="button" 
                            wire:click="setCheckoutMethod('manual')"
                            class="py-2.5 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 {{ $checkoutMethod === 'manual' ? 'bg-white dark:bg-slate-800 text-emerald-700 dark:text-emerald-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
                        <svg class="w-4 h-4 {{ $checkoutMethod === 'manual' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                        </svg>
                        <span>Transfer Manual</span>
                    </button>

                    @if(!$isDokuEnabled)
                        <button type="button" 
                                wire:click="setCheckoutMethod('duitku')"
                                class="py-2.5 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 {{ $checkoutMethod === 'duitku' ? 'bg-white dark:bg-slate-800 text-emerald-700 dark:text-emerald-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
                            <svg class="w-4 h-4 {{ $checkoutMethod === 'duitku' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            <span>Bayar Online</span>
                        </button>
                    @endif
                </div>

                @if(!empty($paymentError))
                    <div class="p-3 bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-700 rounded-xl flex items-start gap-2.5 text-xs text-rose-900 dark:text-rose-200">
                        <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="flex-1 font-semibold leading-relaxed">
                            {{ $paymentError }}
                        </div>
                    </div>
                @endif

                <!-- ─── FORM GATEWAY DOKU (HOSTED CHECKOUT) ───────────────────── -->
                @if($checkoutMethod === 'doku' && $isDokuEnabled)
                    <div class="space-y-3 pt-1">
                        <div class="bg-slate-50 dark:bg-slate-950 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3 text-xs">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-200/80 dark:border-slate-800/80">
                                <span class="font-bold text-slate-700 dark:text-slate-300">Total Pembayaran:</span>
                                <span class="font-mono font-black text-sm text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($this->getGrandTotalTransfer(), 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="space-y-2 text-[11px] text-slate-600 dark:text-slate-400">
                                <p class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    <span>Saluran Pembayaran yang Tersedia:</span>
                                </p>
                                <ul class="space-y-1.5 pl-0.5">
                                    <li class="flex items-start gap-2">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        <span><strong>QRIS Instan:</strong> GoPay, OVO, DANA, ShopeePay, LinkAja, BCA Mobile & Semua M-Banking</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                        <span><strong>Virtual Account:</strong> BCA, Mandiri, BRI, BNI, BSI, Permata, CIMB Niaga, Danamon</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                        <span><strong>Gerai Retail:</strong> Alfamart, Indomaret, Pos Indonesia</span>
                                    </li>
                                </ul>
                            </div>

                            <div class="p-2.5 bg-emerald-50/80 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 rounded-lg text-[11px] text-emerald-800 dark:text-emerald-300 flex items-start gap-2">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Tagihan terverifikasi secara real-time setelah pembayaran berhasil. Tidak perlu konfirmasi manual atau mengunggah bukti transfer.</span>
                            </div>
                        </div>

                        <!-- Tombol Lanjut ke DOKU -->
                        <button type="button" 
                                wire:click="payViaDoku"
                                wire:loading.attr="disabled"
                                class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-xs flex items-center justify-center gap-2 text-xs disabled:opacity-50">
                            <span wire:loading.remove wire:target="payViaDoku" class="flex items-center gap-1.5">
                                <span>Lanjut ke Pembayaran Online</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </span>
                            <span wire:loading wire:target="payViaDoku" class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Menghubungkan ke Gateway Pembayaran...</span>
                            </span>
                        </button>
                    </div>
                @endif

                <!-- ─── FORM TRANSFER MANUAL ────────────────────────────────── -->
                @if($checkoutMethod === 'manual')
                    <div class="space-y-3 pt-1">
                        <!-- Rekening Tujuan Ramping -->
                        <div class="space-y-1.5">
                            @if(!empty($bsiRekening))
                                <div class="bg-slate-50 dark:bg-slate-950 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs">
                                    <div>
                                        <span class="font-extrabold text-emerald-700 dark:text-emerald-400 block text-[11px]">{{ $bank1Name }}</span>
                                        <span class="font-mono font-black text-slate-900 dark:text-white">{{ $bsiRekening }}</span>
                                        <span class="text-[10px] text-slate-400 block">a.n. {{ $bsiAn }}</span>
                                    </div>
                                    <button type="button" onclick="copyToClipboard('{{ $bsiRekening }}')" class="px-2.5 py-1 bg-emerald-600 text-white font-sans text-[11px] font-bold rounded-lg hover:bg-emerald-700 transition-all active:scale-95">Salin</button>
                                </div>
                            @endif

                            @if(!empty($briRekening))
                                <div class="bg-slate-50 dark:bg-slate-950 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs">
                                    <div>
                                        <span class="font-extrabold text-blue-700 dark:text-blue-400 block text-[11px]">{{ $bank2Name }}</span>
                                        <span class="font-mono font-black text-slate-900 dark:text-white">{{ $briRekening }}</span>
                                        <span class="text-[10px] text-slate-400 block">a.n. {{ $briAn }}</span>
                                    </div>
                                    <button type="button" onclick="copyToClipboard('{{ $briRekening }}')" class="px-2.5 py-1 bg-emerald-600 text-white font-sans text-[11px] font-bold rounded-lg hover:bg-emerald-700 transition-all active:scale-95">Salin</button>
                                </div>
                            @endif
                        </div>

                        <!-- Upload Bukti Transfer Ramping -->
                        <div class="space-y-2">
                            <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 block">Unggah Bukti Struk Transfer:</span>
                            
                            <div class="relative border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-xl p-3 text-center hover:border-emerald-500 transition-all bg-slate-50/50 dark:bg-slate-950/50">
                                <input type="file" 
                                       id="proofInput"
                                       accept="image/*"
                                       @change="clientCompressAndUpload($event)"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                                @if($proofImage)
                                    <div class="space-y-1">
                                        <img src="{{ $proofImage->temporaryUrl() }}" class="max-h-36 mx-auto rounded-lg shadow-xs object-contain border">
                                        <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center justify-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>Foto bukti siap dikirim (Klik untuk ganti)</span>
                                        </span>
                                    </div>
                                @else
                                    <div class="flex items-center justify-center gap-3 py-2">
                                        <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                        <div class="text-left">
                                            <strong class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Pilih Foto / Struk Bukti Transfer</strong>
                                            <span class="text-[10px] text-slate-400">Otomatis dioptimasi hemat kuota</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @error('proofImage') <span class="text-[11px] text-rose-600 font-bold block">{{ $message }}</span> @enderror

                            <!-- Bank & Pengirim (Opsional) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-0.5">
                                <div>
                                    <label class="text-[10px] font-bold text-slate-600 dark:text-slate-400 block mb-1 flex items-center justify-between">
                                        <span>Bank Pengirim:</span>
                                        <span class="text-[9px] text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800/80 px-1.5 py-0.5 rounded font-semibold">(Opsional)</span>
                                    </label>
                                    <input type="text" 
                                           wire:model="senderBank" 
                                           placeholder="Contoh: BCA / Mandiri" 
                                           class="w-full p-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-medium text-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-slate-600 dark:text-slate-400 block mb-1 flex items-center justify-between">
                                        <span>Nama Rekening Pengirim:</span>
                                        <span class="text-[9px] text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800/80 px-1.5 py-0.5 rounded font-semibold">(Opsional)</span>
                                    </label>
                                    <input type="text" 
                                           wire:model="senderAccountName" 
                                           placeholder="Nama pemilik rekening" 
                                           class="w-full p-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-medium text-slate-900 dark:text-white">
                                </div>
                            </div>

                            <!-- Tombol Kirim Bukti -->
                            <button type="button" 
                                    wire:click="submitManualTransfer"
                                    wire:loading.attr="disabled"
                                    class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-xs flex items-center justify-center gap-2 text-xs disabled:opacity-50">
                                <span wire:loading.remove wire:target="submitManualTransfer" class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    <span>Kirim Bukti Pembayaran</span>
                                </span>
                                <span wire:loading wire:target="submitManualTransfer" class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span>Mengunggah Bukti...</span>
                                </span>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- ─── FORM GATEWAY DUITKU ─────────────────────────────────── -->
                @if($checkoutMethod === 'duitku')
                    <div class="space-y-2 pt-1">
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 block">Pilih saluran pembayaran online:</span>
                        @php
                            $channels = config('duitku.enabled_channels', [
                                'SP' => ['name' => 'QRIS (Semua E-Wallet / Mobile Banking)', 'type' => 'qris'],
                                'BR' => ['name' => 'Bank BRI (Virtual Account)', 'type' => 'va'],
                                'M2' => ['name' => 'Bank Mandiri (Virtual Account)', 'type' => 'va'],
                                'BT' => ['name' => 'Bank Permata (Virtual Account)', 'type' => 'va'],
                                'I1' => ['name' => 'Bank BNI (Virtual Account)', 'type' => 'va'],
                            ]);
                        @endphp

                        <div class="space-y-1.5">
                            @foreach($channels as $code => $ch)
                                <button type="button" 
                                        wire:click="initiateBayarOnline('{{ $code }}')"
                                        class="w-full p-2.5 bg-slate-50 dark:bg-slate-950 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 border border-slate-200 dark:border-slate-800 rounded-xl text-left transition-all flex items-center justify-between text-xs font-bold group">
                                    <div class="flex items-center gap-2">
                                        @if($ch['type'] === 'qris')
                                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        @else
                                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        @endif
                                        <span class="text-slate-800 dark:text-slate-200 group-hover:text-emerald-700 dark:group-hover:text-emerald-400">{{ $ch['name'] }}</span>
                                    </div>
                                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </div>
    @endif


    <!-- =================================================================== -->
    <!-- TAB 3: RIWAYAT PEMBAYARAN & STATUS BUKTI TRANSFER                    -->
    <!-- =================================================================== -->
    @if($portalTab === 'riwayat')
        <div class="space-y-4">
            
            <!-- ─── BAR FILTER RIWAYAT (TAHUN & METODE) ────────────────────────── -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-3.5 shadow-xs space-y-2.5">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        <span>Filter Riwayat</span>
                    </span>
                    @if($historyYear || $historyMethod)
                        <button type="button" 
                                wire:click="$set('historyYear', ''); $set('historyMethod', '');"
                                class="text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline">
                            Reset Filter ✕
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <!-- Filter Tahun -->
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 mb-1">Tahun</label>
                        <select wire:model.live="historyYear"
                                class="w-full text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 py-1.5 px-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Semua Tahun</option>
                            @foreach($historyYears as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Metode -->
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 mb-1">Metode</label>
                        <select wire:model.live="historyMethod"
                                class="w-full text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 py-1.5 px-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Semua Metode</option>
                            <option value="manual">Transfer Manual</option>
                            <option value="kasir">Kasir / Tunai</option>
                            <option value="gateway">Online (Duitku)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- ─── STATUS PENGAJUAN TRANSFER MANUAL TERBARU ─────────────────── -->
            @if($manualSubmissions->isNotEmpty())
                <div class="space-y-2.5">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 px-1 flex items-center gap-1.5">
                        <span>⏳</span>
                        <span>Status Pengajuan Transfer Manual</span>
                        <span class="text-[10px] font-bold text-slate-400">({{ $manualSubmissions->count() }})</span>
                    </h3>

                    @foreach($manualSubmissions as $sub)
                        @php
                            $isPending  = $sub->status === 'pending';
                            $isApproved = $sub->status === 'approved';
                            $isRejected = $sub->status === 'rejected';
                            $proofUrl   = $sub->proof_image_path ? asset('storage/' . $sub->proof_image_path) : null;
                        @endphp
                        <div class="bg-white dark:bg-slate-900 border rounded-3xl p-4 space-y-3 shadow-xs {{ $isPending ? 'border-amber-300 dark:border-amber-700/60 bg-amber-50/20' : ($isRejected ? 'border-rose-300 dark:border-rose-700/60 bg-rose-50/20' : 'border-emerald-300 dark:border-emerald-700/60 bg-emerald-50/20') }}">
                            
                            <!-- Header Bar -->
                            <div class="flex items-center justify-between text-xs flex-wrap gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-slate-700 dark:text-slate-300">
                                        {{ $sub->submission_code }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">
                                        {{ $sub->created_at->locale('id')->translatedFormat('d M Y, H:i') }}
                                    </span>
                                </div>

                                @if($isPending)
                                    <span class="px-2.5 py-0.5 bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-500/30 rounded-xl text-[10px] font-black uppercase flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        <span>Menunggu Verifikasi</span>
                                    </span>
                                @elseif($isApproved)
                                    <span class="px-2.5 py-0.5 bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 rounded-xl text-[10px] font-black uppercase">
                                        ✓ Disetujui (Sah)
                                    </span>
                                @elseif($isRejected)
                                    <span class="px-2.5 py-0.5 bg-rose-500/15 text-rose-700 dark:text-rose-300 border border-rose-500/30 rounded-xl text-[10px] font-black uppercase">
                                        ✕ Perlu Diperbaiki
                                    </span>
                                @endif
                            </div>

                            <!-- Nominal & Info Transfer -->
                            <div class="flex items-start justify-between text-xs pt-1 border-t border-slate-100 dark:border-slate-800">
                                <div>
                                    <span class="text-slate-500 dark:text-slate-400 block text-[10px] uppercase font-bold">
                                        Total Transfer (Rekening {{ strtoupper($sub->bank_destination ?? 'Pesantren') }})
                                    </span>
                                    <strong class="text-base font-black text-slate-900 dark:text-white">
                                        Rp {{ number_format($sub->total_transfer_amount, 0, ',', '.') }}
                                    </strong>
                                    @if($sub->pocket_money_amount > 0)
                                        <span class="text-[10px] text-purple-600 dark:text-purple-400 block font-semibold">
                                            (Termasuk Titipan Uang Saku Rp {{ number_format($sub->pocket_money_amount, 0, ',', '.') }})
                                        </span>
                                    @endif
                                </div>

                                <!-- Thumbnail Struk Transfer -->
                                @if($proofUrl)
                                    <button type="button" 
                                            @click="showImageModal = true; modalImgSrc = '{{ $proofUrl }}'; modalImgTitle = 'Bukti Transfer {{ $sub->submission_code }}';"
                                            class="group relative w-12 h-12 rounded-xl overflow-hidden border-2 border-slate-200 dark:border-slate-700 hover:border-emerald-500 transition-all shrink-0">
                                        <img src="{{ $proofUrl }}" alt="Struk" class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                                        <div class="absolute inset-0 bg-slate-950/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                        </div>
                                    </button>
                                @endif
                            </div>

                            <!-- Pengirim Info jika diisi -->
                            @if($sub->sender_bank || $sub->sender_account_name)
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 bg-slate-50/80 dark:bg-slate-800/40 px-3 py-1.5 rounded-xl border border-slate-100 dark:border-slate-800 flex items-center gap-1.5 flex-wrap">
                                    <span>Bank Pengirim:</span>
                                    <strong class="text-slate-700 dark:text-slate-300">{{ $sub->sender_bank ?: '—' }}</strong>
                                    @if($sub->sender_account_name)
                                        <span>a.n. <strong class="text-slate-700 dark:text-slate-300">{{ $sub->sender_account_name }}</strong></span>
                                    @endif
                                </div>
                            @endif

                            <!-- Itemized Breakdown Accordion -->
                            @if(!empty($sub->bill_breakdown))
                                <div x-data="{ showDetail: false }" class="pt-1">
                                    <button type="button" @click="showDetail = !showDetail" 
                                            class="w-full flex items-center justify-between text-[11px] font-bold text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 py-1 transition-colors">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            <span x-text="showDetail ? 'Sembunyikan Rincian Tagihan' : 'Lihat Rincian Tagihan (' + {{ count($sub->bill_breakdown) }} + ' Pos)'"></span>
                                        </span>
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="showDetail ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                    </button>

                                    <div x-show="showDetail" x-collapse x-cloak class="mt-2 space-y-1.5 bg-slate-50 dark:bg-slate-800/50 p-2.5 rounded-2xl border border-slate-100 dark:border-slate-800">
                                        @foreach($sub->bill_breakdown as $bItem)
                                            <div class="flex items-start justify-between text-xs py-1 border-b border-slate-200/50 dark:border-slate-700/50 last:border-0">
                                                <div>
                                                    <div class="font-bold text-slate-800 dark:text-slate-200">{{ $bItem['config_label'] ?? 'Tagihan' }}</div>
                                                    <div class="text-[10px] text-slate-500 dark:text-slate-400">{{ $bItem['period_label'] ?? '' }}</div>
                                                </div>
                                                <div class="font-extrabold text-slate-900 dark:text-white">
                                                    Rp {{ number_format($bItem['amount'] ?? 0, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($sub->pocket_money_amount > 0)
                                            <div class="flex items-center justify-between text-xs py-1 text-purple-700 dark:text-purple-300 font-bold border-t border-purple-200/50 dark:border-purple-800/50 pt-1.5">
                                                <span class="flex items-center gap-1">👛 Titipan Uang Saku</span>
                                                <span>+ Rp {{ number_format($sub->pocket_money_amount, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Rejection Alert & Re-upload Action -->
                            @if($isRejected)
                                <div class="p-3 bg-rose-100/90 dark:bg-rose-950/70 border border-rose-200 dark:border-rose-900 rounded-2xl text-xs space-y-2.5">
                                    <div class="text-rose-900 dark:text-rose-200">
                                        <strong class="block text-[10px] font-black uppercase text-rose-700 dark:text-rose-400">Alasan Penolakan dari Bendahara:</strong>
                                        <p class="mt-0.5 text-xs font-medium">{{ $sub->rejection_reason ?: 'Bukti transfer tidak terbaca atau nominal tidak sesuai mutasi.' }}</p>
                                    </div>
                                    <button type="button" 
                                            wire:click="switchTab('bayar')"
                                            class="w-full py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-xs text-center transition-all shadow-xs flex items-center justify-center gap-1.5 active:scale-98">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        <span>Perbaiki & Upload Ulang Bukti Transfer</span>
                                    </button>
                                </div>
                            @endif

                            <!-- Pending Status Help & WhatsApp CTA -->
                            @if($isPending)
                                <div class="flex items-center justify-between pt-1 text-[11px] text-slate-500 dark:text-slate-400 flex-wrap gap-2">
                                    <span class="flex items-center gap-1">
                                        <span>⏳ Sedang dicek mutasi bank oleh Bendahara</span>
                                    </span>
                                    @if(!empty($directWaUrl))
                                        <a href="{{ $directWaUrl . urlencode(' Konfirmasi Bukti Transfer: ' . $sub->submission_code) }}" target="_blank"
                                           class="font-black text-emerald-600 dark:text-emerald-400 hover:underline inline-flex items-center gap-1">
                                            <span>Chat Bendahara</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </a>
                                    @endif
                                </div>
                            @endif

                            <!-- Approved Receipt CTA -->
                            @if($isApproved && !empty($sub->receipt_no))
                                <div class="pt-1">
                                    <a href="{{ route('bukti-bayar.kuitansi', ['receiptNo' => $sub->receipt_no, 'from' => 'portal-wali']) }}" target="_blank"
                                       class="w-full py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center justify-center gap-1.5 text-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Lihat Nota Kuitansi Resmi ({{ $sub->receipt_no }})</span>
                                    </a>
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            @endif

            <!-- ─── RIWAYAT PEMBAYARAN RESMI (LUNAS KASIR & GATEWAY) ────────── -->
            <div class="space-y-3 pt-2">
                <div class="flex items-center justify-between px-1">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <span>📜</span>
                        <span>Riwayat Pembayaran Sah</span>
                        @if($paymentHistory->isNotEmpty())
                            <span class="text-[10px] font-bold text-slate-400">({{ $paymentHistory->count() }})</span>
                        @endif
                    </h3>
                </div>

                @if($paymentHistory->isEmpty() && $manualSubmissions->isEmpty())
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 text-center space-y-3 shadow-xs">
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto text-xl">
                            🧾
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Belum Ada Riwayat Transaksi</h4>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 max-w-xs mx-auto mt-1">
                                @if($historyYear || $historyMethod)
                                    Tidak ditemukan transaksi dengan filter yang dipilih. Silakan ubah atau reset filter.
                                @else
                                    Semua transaksi pembayaran sah atau bukti transfer akan tercatat dan tersimpan rapi di sini.
                                @endif
                            </p>
                        </div>
                        @if($historyYear || $historyMethod)
                            <button type="button" 
                                    wire:click="$set('historyYear', ''); $set('historyMethod', '');"
                                    class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all">
                                Reset Filter
                            </button>
                        @endif
                    </div>
                @elseif($paymentHistory->isEmpty())
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 text-center space-y-1 shadow-xs">
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Tidak ada kuitansi kasir atau transaksi online</p>
                        <p class="text-[10px] text-slate-400">Riwayat pengajuan transfer manual dapat dilihat pada bagian atas.</p>
                    </div>
                @else
                    <div class="space-y-2.5">
                        @foreach($paymentHistory as $item)
                            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 space-y-3 shadow-xs">
                                
                                <!-- Header Transaksi -->
                                <div class="flex items-center justify-between text-xs flex-wrap gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-slate-700 dark:text-slate-300">
                                            {{ $item['order_id'] }}
                                        </span>
                                        <span class="text-[10px] text-slate-400">
                                            {{ $item['date_fmt'] }}
                                        </span>
                                    </div>
                                    <span class="px-2.5 py-0.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-full text-[10px] font-black">
                                        {{ $item['status'] }}
                                    </span>
                                </div>

                                <!-- Nominal & Metode -->
                                <div class="flex items-baseline justify-between text-xs pt-1 border-t border-slate-100 dark:border-slate-800">
                                    <div>
                                        <span class="text-slate-500 dark:text-slate-400 block text-[10px] uppercase font-bold">{{ $item['method_label'] }}</span>
                                        <strong class="text-base font-black text-slate-900 dark:text-white">
                                            Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                        </strong>
                                    </div>

                                    @if(!empty($item['logger_name']))
                                        <span class="text-[10px] text-slate-400">
                                            Petugas: {{ $item['logger_name'] }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Itemized Breakdown Accordion -->
                                @if(!empty($item['breakdown']))
                                    <div x-data="{ showDetail: false }" class="pt-1">
                                        <button type="button" @click="showDetail = !showDetail" 
                                                class="w-full flex items-center justify-between text-[11px] font-bold text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 py-1 transition-colors">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                <span x-text="showDetail ? 'Sembunyikan Rincian Tagihan' : 'Lihat Rincian Tagihan (' + {{ count($item['breakdown']) }} + ' Pos)'"></span>
                                            </span>
                                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="showDetail ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                        </button>

                                        <div x-show="showDetail" x-collapse x-cloak class="mt-2 space-y-1.5 bg-slate-50 dark:bg-slate-800/50 p-2.5 rounded-2xl border border-slate-100 dark:border-slate-800">
                                            @foreach($item['breakdown'] as $bItem)
                                                <div class="flex items-start justify-between text-xs py-1 border-b border-slate-200/50 dark:border-slate-700/50 last:border-0">
                                                    <div>
                                                        <div class="font-bold text-slate-800 dark:text-slate-200">{{ $bItem['config_label'] ?? 'Tagihan' }}</div>
                                                        <div class="text-[10px] text-slate-500 dark:text-slate-400">{{ $bItem['period_label'] ?? '' }}</div>
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="font-extrabold text-slate-900 dark:text-white">
                                                            Rp {{ number_format($bItem['pay_portion'] ?? ($bItem['amount'] ?? 0), 0, ',', '.') }}
                                                        </div>
                                                        @if(!empty($bItem['is_partial']))
                                                            <span class="text-[9px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-1.5 py-0.5 rounded-md">Cicilan</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if(!empty($item['notes']))
                                                <div class="text-[10px] text-slate-500 dark:text-slate-400 italic pt-1">
                                                    Catatan: {{ $item['notes'] }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Action Buttons: Lihat Nota & Unduh PDF -->
                                <div class="flex items-center gap-2 pt-1">
                                    @if(!empty($item['preview_url']))
                                        <a href="{{ $item['preview_url'] }}" target="_blank"
                                           class="flex-1 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold text-center transition-all flex items-center justify-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Lihat Nota</span>
                                        </a>
                                    @endif
                                    <a href="{{ $item['pdf_url'] }}" target="_blank"
                                       class="flex-1 py-2 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-600 text-emerald-600 hover:text-white rounded-xl text-xs font-black text-center transition-all border border-emerald-200 dark:border-emerald-800 flex items-center justify-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <span>Unduh PDF</span>
                                    </a>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    @endif

    <!-- ─── GLOBAL PHOTO LIGHTBOX MODAL ────────────────────────────────────── -->
    <div x-show="showImageModal" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xs transition-opacity"
         @keydown.escape.window="showImageModal = false"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="relative max-w-lg w-full bg-white dark:bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800"
             @click.outside="showImageModal = false">
            <div class="flex items-center justify-between p-3.5 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                <span class="text-xs font-black text-slate-800 dark:text-slate-200" x-text="modalImgTitle || 'Bukti Transfer'"></span>
                <button type="button" @click="showImageModal = false" 
                        class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-300 dark:hover:bg-slate-600 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-3 bg-slate-950 flex items-center justify-center max-h-[75vh] overflow-auto">
                <img :src="modalImgSrc" alt="Bukti Transfer" class="max-w-full max-h-[70vh] object-contain rounded-xl">
            </div>
            <div class="p-3 bg-white dark:bg-slate-900 flex justify-end">
                <a :href="modalImgSrc" target="_blank" download
                   class="px-3.5 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Buka Gambar Asli</span>
                </a>
            </div>
        </div>
    </div>

</div>
