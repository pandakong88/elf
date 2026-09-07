<div class="space-y-4 sm:space-y-5 pb-16" x-data="{
    openPayModal: false,
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
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </div>
    @endif


    <!-- =================================================================== -->
    <!-- TAB 2: MENU BAYAR / TRANSFER (ALUR CHECKOUT TERPANDU)                -->
    <!-- =================================================================== -->
    @if($portalTab === 'bayar')
        <div class="space-y-5">

            <!-- ─── LANGKAH 1: PILIH TAGIHAN YANG MAU DIBAYAR ───────────────── -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 sm:p-5 space-y-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs flex items-center justify-center font-black">1</span>
                            <span>Pilih Tagihan yang Ingin Dibayar</span>
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 ml-8 mt-0.5">Centang satu atau beberapa tagihan berikut</p>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <button type="button" 
                                wire:click="selectAllBills({{ json_encode($allUnpaidIds) }})"
                                class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-emerald-800 transition-all">
                            Pilih Semua
                        </button>
                    </div>
                </div>

                @if($unpaidQueue->isEmpty())
                    <div class="py-6 text-center text-xs text-slate-400 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-200 dark:border-slate-800">
                        Tidak ada tagihan tertunggak saat ini. Semua tagihan sudah lunas!
                    </div>
                @else
                    <div class="space-y-2 pt-1">
                        @foreach($unpaidQueue as $bill)
                            @php
                                $maxKekurangan = max(0, (float)$bill->amount - (float)$bill->amount_paid);
                                $isChecked = in_array($bill->id, $selectedBillIds);
                                $monthName = $this->getMonthName($bill->period_month);
                            @endphp
                            <label class="block p-3.5 rounded-2xl border-2 cursor-pointer transition-all {{ $isChecked ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20 shadow-xs' : 'border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/50 hover:border-slate-300' }}">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" 
                                           wire:click="toggleBillSelection('{{ $bill->id }}')"
                                           {{ $isChecked ? 'checked' : '' }}
                                           class="w-5 h-5 rounded-lg text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-700 cursor-pointer">
                                    
                                    <div class="flex-1 min-w-0">
                                        <div class="font-black text-xs text-slate-900 dark:text-white truncate">
                                            {{ $this->getBillDisplayName($bill) }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                            Periode: {{ $this->getBillPeriodLabel($bill) }}
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <span class="text-sm font-black text-emerald-700 dark:text-emerald-400">
                                            Rp {{ number_format($maxKekurangan, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>


            <!-- ─── LANGKAH 2: TITIPAN UANG SAKU ANAK (FITUR BARU) ──────────── -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 sm:p-5 space-y-3.5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs flex items-center justify-center font-black">2</span>
                            <span>Titipan Uang Saku Santri</span>
                            <span class="text-[10px] px-2 py-0.5 bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 font-bold rounded-md">Opsional</span>
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 ml-8 mt-0.5 leading-relaxed">
                            Ingin sekalian transfer uang jajan/saku ananda ke pengurus?
                        </p>
                    </div>

                    <!-- Toggle Button -->
                    <button type="button" 
                            wire:click="togglePocketMoney"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $includePocketMoney ? 'bg-emerald-600' : 'bg-slate-300 dark:bg-slate-700' }}">
                        <span class="sr-only">Titip Uang Saku</span>
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $includePocketMoney ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </div>

                @if($includePocketMoney)
                    <div class="pt-2 space-y-3 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Pilih Nominal Titipan Uang Saku:</span>
                        
                        <!-- Preset Chips -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @foreach($presetPocketMoney as $preset)
                                <button type="button" 
                                        wire:click="selectPresetPocketMoney({{ $preset }})"
                                        class="py-2.5 px-3 rounded-xl text-xs font-black transition-all border {{ $pocketMoneyAmount == $preset ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-slate-50 dark:bg-slate-950 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-slate-300' }}">
                                    Rp {{ number_format($preset, 0, ',', '.') }}
                                </button>
                            @endforeach
                        </div>

                        <!-- Custom Input -->
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block mb-1">Atau Masukkan Jumlah Lain (Rp):</label>
                            <input type="number" 
                                   wire:model.live.debounce.300ms="customPocketMoney"
                                   placeholder="Contoh: 150000"
                                   class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div class="p-2.5 bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-800/40 rounded-xl text-[11px] text-purple-900 dark:text-purple-300 flex items-center gap-2">
                            <span>💡</span>
                            <span>Uang saku ini akan langsung diteruskan oleh bendahara/musyrif ke saldo saku ananda di pondok.</span>
                        </div>
                    </div>
                @endif
            </div>


            <!-- ─── LANGKAH 3: RINGKASAN TOTAL TRANSFER ─────────────────────── -->
            @php
                $grandTotal = $this->getGrandTotalTransfer();
            @endphp
            <div class="bg-gradient-to-br from-emerald-950 to-slate-900 text-white border border-emerald-700/60 rounded-3xl p-5 space-y-3 shadow-md">
                <span class="text-xs font-black uppercase text-emerald-300 tracking-wider block">Ringkasan Pembayaran:</span>
                
                <div class="space-y-1.5 text-xs">
                    <div class="flex items-center justify-between text-slate-300">
                        <span>Tagihan Pondok ({{ count($selectedBillIds) }} Tagihan)</span>
                        <strong class="text-white font-bold">Rp {{ number_format($simulasiTotal, 0, ',', '.') }}</strong>
                    </div>

                    @if($includePocketMoney && $pocketMoneyAmount > 0)
                        <div class="flex items-center justify-between text-purple-300">
                            <span>Titipan Uang Saku Ananda</span>
                            <strong class="text-purple-200 font-bold">+ Rp {{ number_format($pocketMoneyAmount, 0, ',', '.') }}</strong>
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-2.5 border-t border-emerald-700/60">
                        <span class="text-xs font-black text-emerald-300 uppercase">TOTAL TRANSFER</span>
                        <span class="text-2xl font-black text-white">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>


            <!-- ─── LANGKAH 4: PILIH CARA PEMBAYARAN ────────────────────────── -->
            <div class="space-y-3">
                <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2 px-1">
                    <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs flex items-center justify-center font-black">3</span>
                    <span>Pilih Cara Pembayaran</span>
                </h3>

                <div class="grid grid-cols-2 gap-2">
                    <button type="button" 
                            wire:click="setCheckoutMethod('manual')"
                            class="p-4 rounded-2xl border-2 text-left transition-all {{ $checkoutMethod === 'manual' ? 'border-emerald-600 bg-emerald-50/60 dark:bg-emerald-950/30 ring-2 ring-emerald-500/20' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900' }}">
                        <span class="text-xl block mb-1">🏦</span>
                        <strong class="text-xs font-black text-slate-900 dark:text-white block">Transfer Bank Manual</strong>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 block mt-0.5">Kirim bukti struk transfer</span>
                    </button>

                    <button type="button" 
                            wire:click="setCheckoutMethod('duitku')"
                            class="p-4 rounded-2xl border-2 text-left transition-all {{ $checkoutMethod === 'duitku' ? 'border-emerald-600 bg-emerald-50/60 dark:bg-emerald-950/30 ring-2 ring-emerald-500/20' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900' }}">
                        <span class="text-xl block mb-1">⚡</span>
                        <strong class="text-xs font-black text-slate-900 dark:text-white block">Bayar Otomatis (QRIS)</strong>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 block mt-0.5">Langsung lunas seketika</span>
                    </button>
                </div>
            </div>


            <!-- ─── METODE A: FORM TRANSFER MANUAL & UPLOAD STRUK ───────────── -->
            @if($checkoutMethod === 'manual')
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 sm:p-5 space-y-4 shadow-sm">
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-200">
                        1. Transfer ke Rekening Resmi Pesantren
                    </h4>

                    <!-- Bank Destination Card -->
                    <div class="space-y-2">
                        @if(!empty($bsiRekening))
                            <div class="bg-slate-50 dark:bg-slate-950 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-extrabold text-emerald-700 dark:text-emerald-400 text-xs">{{ $bank1Name }}</span>
                                    <span class="text-[9px] font-bold bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 px-2 py-0.5 rounded-md">Utama</span>
                                </div>
                                <div class="flex items-center justify-between font-mono bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                                    <span class="font-black text-base text-slate-900 dark:text-white">{{ $bsiRekening }}</span>
                                    <button type="button" onclick="copyToClipboard('{{ $bsiRekening }}')" class="px-3 py-1 bg-emerald-600 text-white font-sans text-xs font-bold rounded-lg hover:bg-emerald-700 transition-all active:scale-95">Salin</button>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">a.n. {{ $bsiAn }}</p>
                            </div>
                        @endif

                        @if(!empty($briRekening))
                            <div class="bg-slate-50 dark:bg-slate-950 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-extrabold text-blue-700 dark:text-blue-400 text-xs">{{ $bank2Name }}</span>
                                    <span class="text-[9px] font-bold bg-blue-100 dark:bg-blue-500/10 text-blue-800 dark:text-blue-300 px-2 py-0.5 rounded-md">Alternatif</span>
                                </div>
                                <div class="flex items-center justify-between font-mono bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                                    <span class="font-black text-base text-slate-900 dark:text-white">{{ $briRekening }}</span>
                                    <button type="button" onclick="copyToClipboard('{{ $briRekening }}')" class="px-3 py-1 bg-emerald-600 text-white font-sans text-xs font-bold rounded-lg hover:bg-emerald-700 transition-all active:scale-95">Salin</button>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">a.n. {{ $briAn }}</p>
                            </div>
                        @endif
                    </div>

                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-200 pt-2 border-t border-slate-100 dark:border-slate-800">
                        2. Unggah Foto Bukti Transfer
                    </h4>

                    <!-- Upload Input with Client-Side Compression -->
                    <div class="space-y-3">
                        <div class="relative border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-2xl p-4 text-center hover:border-emerald-500 transition-all bg-slate-50/50 dark:bg-slate-950/50">
                            <input type="file" 
                                   id="proofInput"
                                   accept="image/*"
                                   @change="clientCompressAndUpload($event)"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                            @if($proofImage)
                                <div class="space-y-2">
                                    <img src="{{ $proofImage->temporaryUrl() }}" class="max-h-48 mx-auto rounded-xl shadow-xs object-contain border">
                                    <span class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400 block">✓ Foto bukti transfer siap dikirim (Klik untuk ganti)</span>
                                </div>
                            @else
                                <div class="space-y-1.5 py-3">
                                    <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto text-xl">
                                        📷
                                    </div>
                                    <strong class="text-xs font-black text-slate-800 dark:text-slate-200 block">Ambil Foto / Pilih Struk dari Galeri HP</strong>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Foto akan otomatis dioptimasi agar hemat kuota & server</p>
                                </div>
                            @endif
                        </div>
                        @error('proofImage') <span class="text-xs text-rose-600 font-bold block">{{ $message }}</span> @enderror

                        <!-- Input Opsional: Bank & Nama Pengirim -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <div>
                                <label class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block mb-1">Bank Pengirim (Opsional):</label>
                                <input type="text" wire:model="senderBank" placeholder="Misal: BCA / BRI / Mandiri" class="w-full p-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block mb-1">Atas Nama Rekening Pengirim (Opsional):</label>
                                <input type="text" wire:model="senderAccountName" placeholder="Nama pemilik rekening" class="w-full p-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                            </div>
                        </div>

                        <!-- Tombol Submit Kirim Bukti -->
                        <div class="pt-2">
                            <button type="button" 
                                    wire:click="submitManualTransfer"
                                    wire:loading.attr="disabled"
                                    class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-black rounded-2xl transition-all shadow-md flex items-center justify-center gap-2 text-sm disabled:opacity-50">
                                <span wire:loading.remove wire:target="submitManualTransfer">🚀 Kirim Bukti Pembayaran</span>
                                <span wire:loading wire:target="submitManualTransfer">Mengunggah & Menyimpan...</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif


            <!-- ─── METODE B: GATEWAY OTOMATIS (DUITKU QRIS / VA) ───────────── -->
            @if($checkoutMethod === 'duitku')
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 sm:p-5 space-y-3.5 shadow-sm">
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-200">
                        Pilih Channel Pembayaran Otomatis
                    </h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Pembayaran akan otomatis diverifikasi oleh sistem dan status tagihan langsung lunas.</p>

                    @php
                        $channels = config('duitku.enabled_channels', [
                            'SP' => ['name' => 'QRIS (Semua E-Wallet / Mobile Banking)', 'type' => 'qris'],
                            'BR' => ['name' => 'Bank BRI (Virtual Account)', 'type' => 'va'],
                            'M2' => ['name' => 'Bank Mandiri (Virtual Account)', 'type' => 'va'],
                            'BT' => ['name' => 'Bank Permata (Virtual Account)', 'type' => 'va'],
                            'I1' => ['name' => 'Bank BNI (Virtual Account)', 'type' => 'va'],
                        ]);
                    @endphp

                    <div class="space-y-2">
                        @foreach($channels as $code => $ch)
                            <button type="button" 
                                    wire:click="initiateBayarOnline('{{ $code }}')"
                                    class="w-full p-3 bg-slate-50 dark:bg-slate-950 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 border border-slate-200 dark:border-slate-800 rounded-2xl text-left transition-all flex items-center justify-between text-xs font-extrabold group">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-base">{{ $ch['type'] === 'qris' ? '📱' : '🏦' }}</span>
                                    <span class="text-slate-800 dark:text-slate-200 group-hover:text-emerald-700 dark:group-hover:text-emerald-400">{{ $ch['name'] }}</span>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    @endif


    <!-- =================================================================== -->
    <!-- TAB 3: RIWAYAT PEMBAYARAN & STATUS BUKTI TRANSFER                    -->
    <!-- =================================================================== -->
    @if($portalTab === 'riwayat')
        <div class="space-y-4">
            
            <!-- ─── STATUS PENGAJUAN TRANSFER MANUAL TERBARU ─────────────────── -->
            @if($manualSubmissions->isNotEmpty())
                <div class="space-y-2.5">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 px-1 flex items-center gap-1.5">
                        <span>⏳</span>
                        <span>Status Pengajuan Bukti Transfer</span>
                    </h3>

                    @foreach($manualSubmissions as $sub)
                        @php
                            $isPending  = $sub->status === 'pending';
                            $isApproved = $sub->status === 'approved';
                            $isRejected = $sub->status === 'rejected';
                        @endphp
                        <div class="bg-white dark:bg-slate-900 border rounded-3xl p-4 space-y-3 shadow-xs {{ $isPending ? 'border-amber-300 dark:border-amber-700/60 bg-amber-50/30' : ($isRejected ? 'border-rose-300 dark:border-rose-700/60 bg-rose-50/30' : 'border-emerald-300 dark:border-emerald-700/60 bg-emerald-50/30') }}">
                            <div class="flex items-center justify-between text-xs flex-wrap gap-2">
                                <span class="font-mono font-bold text-slate-600 dark:text-slate-400">
                                    {{ $sub->submission_code }}
                                </span>

                                @if($isPending)
                                    <span class="px-2.5 py-1 bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-500/30 rounded-xl text-[10px] font-black uppercase">
                                        ⏳ Menunggu Verifikasi Bendahara
                                    </span>
                                @elseif($isApproved)
                                    <span class="px-2.5 py-1 bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 rounded-xl text-[10px] font-black uppercase">
                                        ✓ Pembayaran Disetujui
                                    </span>
                                @elseif($isRejected)
                                    <span class="px-2.5 py-1 bg-rose-500/15 text-rose-700 dark:text-rose-300 border border-rose-500/30 rounded-xl text-[10px] font-black uppercase">
                                        ✕ Perlu Diperbaiki
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-baseline justify-between text-xs pt-1 border-t border-slate-100 dark:border-slate-800">
                                <div>
                                    <span class="text-slate-500 dark:text-slate-400 block text-[11px]">Total Transfer:</span>
                                    <strong class="text-base font-black text-slate-900 dark:text-white">
                                        Rp {{ number_format($sub->total_transfer_amount, 0, ',', '.') }}
                                    </strong>
                                    @if($sub->pocket_money_amount > 0)
                                        <span class="text-[10px] text-purple-600 dark:text-purple-400 block font-semibold">
                                            (Termasuk Titipan Uang Saku Rp {{ number_format($sub->pocket_money_amount, 0, ',', '.') }})
                                        </span>
                                    @endif
                                </div>

                                <span class="text-[10px] text-slate-400">
                                    {{ $sub->created_at->locale('id')->translatedFormat('d M Y, H:i') }}
                                </span>
                            </div>

                            @if($isRejected && !empty($sub->rejection_reason))
                                <div class="p-2.5 bg-rose-100 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-900 rounded-xl text-xs text-rose-900 dark:text-rose-200">
                                    <strong class="block text-[11px] font-black">Catatan Bendahara:</strong>
                                    <span>{{ $sub->rejection_reason }}</span>
                                </div>
                            @endif

                            @if($isApproved && !empty($sub->receipt_no))
                                <div class="pt-1">
                                    <a href="{{ route('bukti-bayar.kuitansi', ['receiptNo' => $sub->receipt_no, 'from' => 'portal-wali']) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all shadow-xs">
                                        <span>Lihat Nota Kuitansi ({{ $sub->receipt_no }})</span>
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
                    </h3>
                </div>

                @if($paymentHistory->isEmpty())
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 text-center space-y-2">
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto text-xl">
                            🧾
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Belum Ada Riwayat Pembayaran</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 max-w-xs mx-auto">
                            Semua transaksi pembayaran yang telah disetujui akan tercatat dan tersimpan rapi di sini.
                        </p>
                    </div>
                @else
                    <div class="space-y-2.5">
                        @foreach($paymentHistory as $item)
                            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 space-y-3 shadow-xs">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-mono font-bold text-slate-500 dark:text-slate-400">
                                        {{ $item['order_id'] }}
                                    </span>
                                    <span class="px-2.5 py-0.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-full text-[10px] font-black">
                                        {{ $item['status'] }}
                                    </span>
                                </div>

                                <div class="flex items-baseline justify-between text-xs pt-1 border-t border-slate-100 dark:border-slate-800">
                                    <div>
                                        <span class="text-slate-500 dark:text-slate-400 block text-[10px] uppercase font-bold">{{ $item['method_label'] }}</span>
                                        <strong class="text-base font-black text-slate-900 dark:text-white">
                                            Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                        </strong>
                                    </div>

                                    <span class="text-[10px] text-slate-400">
                                        {{ $item['date_fmt'] }}
                                    </span>
                                </div>

                                <!-- Action Buttons: Lihat Nota & Unduh PDF -->
                                <div class="flex items-center gap-2 pt-1">
                                    @if(!empty($item['preview_url']))
                                        <a href="{{ $item['preview_url'] }}" target="_blank"
                                           class="flex-1 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold text-center transition-all">
                                            <span>Lihat Nota</span>
                                        </a>
                                    @endif
                                    <a href="{{ $item['pdf_url'] }}" target="_blank"
                                       class="flex-1 py-2 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-600 text-emerald-600 hover:text-white rounded-xl text-xs font-black text-center transition-all border border-emerald-200 dark:border-emerald-800">
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

</div>
