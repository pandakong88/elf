<div class="min-h-[70vh] flex flex-col items-center justify-center px-4 py-10 space-y-6">

    {{-- ================================================================= --}}
    {{-- STATE: LOADING / PENDING (Menunggu konfirmasi)                    --}}
    {{-- ================================================================= --}}
    @if($uiState === 'loading' || ($uiState === 'pending' && !$isDone))
        <div wire:poll.3s="checkStatus" class="w-full max-w-sm text-center space-y-6">

            {{-- Animasi Spinner --}}
            <div class="flex justify-center">
                <div class="relative w-24 h-24">
                    <div class="absolute inset-0 rounded-full border-4 border-slate-200 dark:border-slate-800"></div>
                    <div class="absolute inset-0 rounded-full border-4 border-t-sky-500 border-r-sky-500 border-b-transparent border-l-transparent animate-spin"></div>
                    <div class="absolute inset-3 rounded-full bg-sky-50 dark:bg-sky-900/20 flex items-center justify-center">
                        <svg class="w-8 h-8 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Info Teks --}}
            <div class="space-y-2">
                <h2 class="text-lg font-black text-slate-900 dark:text-white">Menunggu Konfirmasi Pembayaran</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                    Halaman ini akan otomatis diperbarui setelah pembayaran dikonfirmasi.
                    Mohon jangan tutup tab ini.
                </p>
            </div>

            {{-- Info Transaksi --}}
            @if($transaction)
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 text-left space-y-2.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400 font-semibold">No. Pesanan</span>
                        <span class="font-mono font-bold text-slate-800 dark:text-slate-200 text-[11px]">{{ $transaction->merchant_order_id }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400 font-semibold">Metode</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $transaction->channel_label }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs border-t border-slate-100 dark:border-slate-800 pt-2">
                        <span class="text-slate-500 dark:text-slate-400 font-semibold">Total Dibayar</span>
                        <span class="font-black text-sky-600 dark:text-sky-400">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($transaction->expires_at)
                        <div class="flex items-center justify-between text-xs text-amber-600 dark:text-amber-400">
                            <span class="font-semibold">Batas Waktu</span>
                            <span class="font-bold">{{ $transaction->expires_at->translatedFormat('d M Y, H:i') }} WIB</span>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Progress Poll --}}
            <div class="text-[10px] text-slate-400 dark:text-slate-600">
                Memeriksa status... ({{ $pollCount }}/{{ $maxPolls }})
            </div>
        </div>

    {{-- ================================================================= --}}
    {{-- STATE: PENDING TIMEOUT (Selesai menunggu tapi belum konfirmasi)   --}}
    {{-- ================================================================= --}}
    @elseif($uiState === 'pending' && $isDone)
        <div class="w-full max-w-sm text-center space-y-6">
            <div class="flex justify-center">
                <div class="w-24 h-24 rounded-full bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-200 dark:border-amber-800/40 flex items-center justify-center">
                    <svg class="w-12 h-12 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="space-y-2">
                <h2 class="text-lg font-black text-slate-900 dark:text-white">Pembayaran Masih Diproses</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                    Kami belum menerima konfirmasi dari server pembayaran.
                    Status tagihan akan diperbarui otomatis setelah pembayaran dikonfirmasi (maks. 1x24 jam).
                </p>
            </div>
            @if($transaction)
                <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/30 rounded-2xl p-3 text-xs text-amber-700 dark:text-amber-400 font-semibold">
                    No. Pesanan: <span class="font-mono">{{ $transaction->merchant_order_id }}</span><br>
                    Simpan nomor ini sebagai bukti pembayaran.
                </div>
            @endif
            @if($santri)
                <a href="{{ route('portal-wali.dashboard', $santri->id) }}"
                   class="inline-flex items-center gap-2 px-5 py-3 bg-slate-800 dark:bg-slate-700 hover:bg-slate-700 text-white font-extrabold rounded-2xl text-sm transition-all shadow-md">
                    Kembali ke Dashboard
                </a>
            @endif
        </div>

    {{-- ================================================================= --}}
    {{-- STATE: SUCCESS ✅                                                  --}}
    {{-- ================================================================= --}}
    @elseif($uiState === 'success')
        <div class="w-full max-w-sm text-center space-y-6">

            {{-- Animasi Centang --}}
            <div class="flex justify-center">
                <div class="w-24 h-24 rounded-full bg-emerald-50 dark:bg-emerald-900/20 border-2 border-emerald-200 dark:border-emerald-800/40 flex items-center justify-center animate-bounce-once">
                    <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>

            <div class="space-y-2">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-full text-[10px] font-black uppercase tracking-wider border border-emerald-200 dark:border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Pembayaran Berhasil
                </div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Terima Kasih! 🎉</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Pembayaran telah dikonfirmasi dan status tagihan sudah diperbarui secara otomatis.
                </p>
            </div>

            {{-- Ringkasan Bukti Pembayaran Super Informatif --}}
            @if($transaction)
                @php
                    $activeRoom = $santri?->roomAssignments->first()?->room;
                    $activeDorm = $activeRoom?->dormitory;
                    $activeKelas = $santri?->madrasahEnrollments->first()?->kelas;
                    $breakdownList = $transaction->bill_breakdown ?? [];
                @endphp

                <div id="printableReceipt" class="bg-white dark:bg-slate-900 border-2 border-emerald-300 dark:border-emerald-700/60 rounded-3xl overflow-hidden shadow-lg transition-colors text-left">
                    {{-- Header Gradient Accent --}}
                    <div class="h-2 bg-gradient-to-r from-emerald-500 via-teal-500 to-sky-500"></div>

                    <div class="p-5 space-y-4">
                        {{-- Receipt Header --}}
                        <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                            <div>
                                <span class="text-[9px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest block">BUKTI PEMBAYARAN RESMI</span>
                                <h3 class="text-base font-black text-slate-900 dark:text-white mt-0.5">PESANTREN AL-FITHROH</h3>
                                <p class="text-[11px] font-mono font-bold text-slate-500 dark:text-slate-400 mt-0.5">
                                    No. Transaksi: <span class="text-slate-800 dark:text-slate-200">{{ $transaction->merchant_order_id }}</span>
                                </p>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-500/30">
                                    <span>✓</span>
                                    <span>BERHASIL</span>
                                </span>
                                <span class="block text-[10px] text-slate-400 dark:text-slate-500 font-medium mt-1">
                                    {{ ($transaction->callback_received_at ?? $transaction->updated_at ?? now())->translatedFormat('d M Y, H:i') }} WIB
                                </span>
                            </div>
                        </div>

                        {{-- Identitas Santri --}}
                        @if($santri)
                            <div class="bg-slate-50 dark:bg-slate-950/60 p-3 rounded-2xl border border-slate-200/80 dark:border-slate-800 grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Nama Santri</span>
                                    <strong class="text-slate-900 dark:text-white font-extrabold text-sm block truncate">{{ $santri->name }}</strong>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Komplek / Kelas</span>
                                    <span class="text-slate-700 dark:text-slate-300 font-semibold block truncate">
                                        {{ $activeDorm?->name ?: ($activeRoom ? "Kamar {$activeRoom->name}" : '-') }}
                                        @if($activeKelas)
                                            • {{ $activeKelas->name }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif

                        {{-- Rincian Tagihan yang Dibayarkan --}}
                        @if(!empty($breakdownList))
                            <div class="space-y-2">
                                <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider block">
                                    Rincian Tagihan ({{ count($breakdownList) }} Item)
                                </span>

                                <div class="divide-y divide-slate-100 dark:divide-slate-800/80 border-t border-b border-slate-100 dark:border-slate-800">
                                    @foreach($breakdownList as $item)
                                        @php
                                            $billObj = $bills->firstWhere('id', $item['bill_id']);
                                            $fullTitle = $this->getBillTitle($billObj, $item['bill_type'] ?? null);
                                            $isPartial = !empty($item['is_partial']);
                                            $payPortion = (float)($item['pay_portion'] ?? $item['net_amount'] ?? 0);
                                            $remainingBefore = (float)($item['bill_remaining'] ?? $payPortion);
                                            $remainingAfter = max(0, $remainingBefore - $payPortion);
                                        @endphp

                                        <div class="py-2.5 flex items-start justify-between gap-3 text-xs">
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-extrabold text-slate-900 dark:text-slate-100 leading-snug">
                                                    {{ $fullTitle }}
                                                </h4>
                                                <div class="flex items-center gap-2 text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 font-mono flex-wrap">
                                                    <span>Dibayar: <strong class="text-slate-800 dark:text-slate-200">Rp {{ number_format($payPortion, 0, ',', '.') }}</strong></span>
                                                    @if($isPartial && $remainingAfter > 0)
                                                        <span class="text-amber-600 dark:text-amber-400 font-bold">• Sisa kekurangan: Rp {{ number_format($remainingAfter, 0, ',', '.') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="text-right shrink-0">
                                                @if(!$isPartial)
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-black text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-500/20 px-2 py-0.5 rounded-md border border-emerald-300 dark:border-emerald-500/30">
                                                        <span>✓</span> LUNAS
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 dark:text-amber-400 bg-amber-100 dark:bg-amber-500/20 px-2 py-0.5 rounded-md border border-amber-300 dark:border-amber-500/30">
                                                        DICICIL
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Rekap Pembayaran & Biaya Layanan --}}
                        <div class="space-y-1.5 pt-2 text-xs">
                            <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                                <span>Subtotal Tagihan</span>
                                <span class="font-mono font-bold text-slate-800 dark:text-slate-200">
                                    Rp {{ number_format($transaction->bill_amount ?? ($transaction->total_amount - ($transaction->mdr_amount ?? 0)), 0, ',', '.') }}
                                </span>
                            </div>

                            @if(($transaction->mdr_amount ?? 0) > 0)
                                <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                                    <span>Biaya Layanan ({{ $transaction->channel_label }})</span>
                                    <span class="font-mono text-slate-600 dark:text-slate-400">
                                        Rp {{ number_format($transaction->mdr_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif

                            <div class="flex items-center justify-between pt-2 border-t border-slate-200 dark:border-slate-800 text-sm">
                                <span class="font-black text-slate-900 dark:text-white">TOTAL DIBAYARKAN</span>
                                <span class="font-black text-emerald-700 dark:text-emerald-400 text-base font-mono">
                                    Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 pt-1">
                                <span>Metode Pembayaran</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $transaction->channel_label }}</span>
                            </div>
                        </div>

                        {{-- Footer Struk --}}
                        <div class="pt-3 border-t border-dashed border-slate-200 dark:border-slate-800 text-center">
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                🔒 Bukti pembayaran sah dan tervalidasi otomatis oleh sistem Elvith v1.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons: Cetak & Kembali --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 w-full">
                    <button type="button"
                            onclick="window.print()"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-slate-800 hover:bg-slate-700 text-white font-extrabold rounded-2xl text-xs transition-all shadow-md active:scale-95 border border-slate-700">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Bukti Pembayaran</span>
                    </button>

                    @if($santri)
                        <a href="{{ route('portal-wali.dashboard', $santri->id) }}"
                           class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-2xl text-xs transition-all shadow-md active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Kembali ke Dashboard</span>
                        </a>
                    @endif
                </div>
            @endif
        </div>


    {{-- ================================================================= --}}
    {{-- STATE: FAILED / EXPIRED ❌                                         --}}
    {{-- ================================================================= --}}
    @else
        <div class="w-full max-w-sm text-center space-y-6">
            <div class="flex justify-center">
                <div class="w-24 h-24 rounded-full bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-800/40 flex items-center justify-center">
                    <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
            <div class="space-y-2">
                <h2 class="text-lg font-black text-slate-900 dark:text-white">Pembayaran Gagal / Kedaluwarsa</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                    Transaksi tidak dapat diselesaikan. Silakan coba lagi dari dashboard tagihan.
                </p>
            </div>
            @if($santri)
                <a href="{{ route('portal-wali.dashboard', $santri->id) }}"
                   class="inline-flex items-center gap-2 px-5 py-3 bg-slate-800 dark:bg-slate-700 hover:bg-slate-700 text-white font-extrabold rounded-2xl text-sm transition-all shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali & Coba Lagi
                </a>
            @else
                <a href="{{ route('portal-wali.search') }}"
                   class="inline-flex items-center gap-2 px-5 py-3 bg-slate-800 dark:bg-slate-700 hover:bg-slate-700 text-white font-extrabold rounded-2xl text-sm transition-all shadow-md">
                    Kembali ke Pencarian
                </a>
            @endif
        </div>
    @endif

</div>
