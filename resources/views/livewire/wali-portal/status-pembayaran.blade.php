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

            {{-- Ringkasan Pembayaran --}}
            @if($transaction)
                <div class="bg-white dark:bg-slate-900 border-2 border-emerald-200 dark:border-emerald-800/40 rounded-3xl overflow-hidden shadow-sm">
                    <div class="h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
                    <div class="p-4 space-y-3">
                        {{-- Header --}}
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-500/10 flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                            <div class="text-left">
                                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Bukti Pembayaran</p>
                                <p class="text-[11px] font-mono font-black text-slate-700 dark:text-slate-300">{{ $transaction->merchant_order_id }}</p>
                            </div>
                        </div>

                        {{-- Rincian tagihan yang dibayar --}}
                        @if($bills->count() > 0)
                            <div class="space-y-2">
                                @foreach($bills as $bill)
                                    @php
                                        $isFull = $bill->status === 'paid';
                                        $sisa = max(0, $bill->amount - $bill->amount_paid);
                                    @endphp
                                    <div class="flex items-center justify-between text-xs py-0.5">
                                        <div class="truncate flex-1 mr-2 text-left">
                                            <span class="text-slate-700 dark:text-slate-300 font-semibold block truncate">
                                                {{ $bill->notes ?: ($bill->config?->label ?: ($bill->bill_type ? ucwords(str_replace('_', ' ', $bill->bill_type)) : '-')) }}
                                            </span>
                                            @if(!$isFull && $sisa > 0)
                                                <span class="text-[10px] text-amber-600 dark:text-amber-400">Sisa kekurangan: Rp {{ number_format($sisa, 0, ',', '.') }}</span>
                                            @endif
                                        </div>

                                        @if($isFull)
                                            <span class="font-black text-emerald-700 dark:text-emerald-400 shrink-0 flex items-center gap-1 text-[11px]">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                LUNAS
                                            </span>
                                        @else
                                            <span class="font-bold text-amber-600 dark:text-amber-400 shrink-0 px-2 py-0.5 bg-amber-50 dark:bg-amber-950/40 rounded border border-amber-200 dark:border-amber-800 text-[10px]">
                                                DICICIL
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif


                        {{-- Total --}}
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Total Dibayar</span>
                            <span class="text-base font-black text-emerald-700 dark:text-emerald-400">
                                Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Metode --}}
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 dark:text-slate-400 font-semibold">Via</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $transaction->channel_label }}</span>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 dark:text-slate-400 font-semibold">Waktu</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300">
                                {{ ($transaction->callback_received_at ?? now())->translatedFormat('d M Y, H:i') }} WIB
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tombol Kembali --}}
            @if($santri)
                <a href="{{ route('portal-wali.dashboard', $santri->id) }}"
                   class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-2xl text-sm transition-all shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Dashboard Tagihan
                </a>
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
