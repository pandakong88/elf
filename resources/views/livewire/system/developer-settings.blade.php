<div class="p-6 max-w-5xl mx-auto space-y-6">
    {{-- Header Banner --}}
    <div class="bg-gradient-to-r from-slate-900 via-emerald-950 to-slate-900 border border-emerald-500/30 rounded-3xl p-6 sm:p-8 text-white shadow-2xl relative overflow-hidden">
        <div class="absolute -top-12 -right-12 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-300 text-[10px] font-extrabold uppercase border border-amber-500/30 tracking-wider">SUPER ADMIN ONLY</span>
                    <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-extrabold uppercase border border-emerald-500/30 tracking-wider">DEVELOPER MODE</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold font-serif-display text-white tracking-tight">Pengaturan Developer & Testing System</h1>
                <p class="text-xs sm:text-sm text-slate-300">Pusat kendali Mode Penguji (Quick Switcher Login), pengaturan kredensial dev, dan pembersihan cache sistem.</p>
            </div>
            <button type="button" wire:click="clearCache"
                    class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl transition-all shadow-lg flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Bersihkan Cache System</span>
            </button>
        </div>
    </div>

    {{-- Alert Success Message --}}
    @if($successMessage)
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl text-emerald-400 text-xs font-bold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ $successMessage }}</span>
            </div>
            <button type="button" wire:click="$set('successMessage', '')" class="text-emerald-400 hover:text-white">✕</button>
        </div>
    @endif

    {{-- Main Settings Card --}}
    <form wire:submit.prevent="saveSettings" class="space-y-6">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
            
            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                <span>🔒 Pengaturan Quick Switcher Mode Penguji</span>
            </h3>

            {{-- Toggle Enable/Disable --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl gap-4">
                <div class="space-y-1 max-w-xl">
                    <span class="text-sm font-bold text-slate-900 dark:text-slate-100 block">Tampilkan Quick Switcher di Halaman Login (/login)</span>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Saat saklar ini **AKTIF (🟢 ON)**, panel penguji akun multi-role akan tampil di halaman login. Saat **NONAKTIF (🔴 OFF)**, panel akan tersembunyi total demi keamanan mode produksi.
                    </p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" wire:model="dev_quick_switcher_enabled" class="sr-only peer">
                    <div class="w-12 h-6 bg-slate-300 dark:bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                </label>
            </div>

            {{-- Custom Dev Password Input --}}
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Kata Sandi Penguji Dev (Seragam)</label>
                <div class="relative max-w-md">
                    <input type="text" wire:model="dev_quick_switcher_password" placeholder="rahasia123"
                           class="w-full text-xs font-mono font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-emerald-600 dark:text-emerald-400 p-3.5 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <p class="text-[11px] text-slate-400">Kata sandi ini digunakan oleh fitur 1-klik Quick Switcher saat menguji hak akses akun musyrif/pengurus.</p>
            </div>

        {{-- DOKU PAYMENT GATEWAY CARD --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                    <span>💳 Konfigurasi Payment Gateway DOKU (Jokul Checkout)</span>
                </h3>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $doku_environment === 'production' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                    MODE: {{ strtoupper($doku_environment) }}
                </span>
            </div>

            {{-- Toggle Enable/Disable DOKU --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl gap-4">
                <div class="space-y-1 max-w-xl">
                    <span class="text-sm font-bold text-slate-900 dark:text-slate-100 block">Aktifkan Pembayaran Otomatis DOKU di Portal Wali</span>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Saat saklar ini **AKTIF (🟢 ON)**, wali santri dapat memilih metode pembayaran otomatis instan (Virtual Account, QRIS, E-Wallet) yang diverifikasi otomatis oleh DOKU.
                    </p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" wire:model="doku_enabled" class="sr-only peer">
                    <div class="w-12 h-6 bg-slate-300 dark:bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                </label>
            </div>

            {{-- Grid Inputs --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                
                {{-- Environment --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Target Environment</label>
                    <select wire:model="doku_environment"
                            class="w-full text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 p-3.5 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="sandbox">Sandbox (Uji Coba / Testing)</option>
                        <option value="production">Production (Live Transaksi Nyata)</option>
                    </select>
                </div>

                {{-- Expiry Minutes --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Masa Berlaku Invoice (Menit)</label>
                    <input type="number" wire:model="doku_expiry_minutes" placeholder="1440"
                           class="w-full text-xs font-mono font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 p-3.5 focus:ring-emerald-500 focus:border-emerald-500">
                    <span class="text-[10px] text-slate-400 block">Bawaan: 1440 menit (24 jam)</span>
                </div>

                {{-- Client ID --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">DOKU Client ID</label>
                    <input type="text" wire:model="doku_client_id" placeholder="MCH-..."
                           class="w-full text-xs font-mono font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 p-3.5 focus:ring-emerald-500 focus:border-emerald-500">
                    <span class="text-[10px] text-slate-400 block">Ditemukan di DOKU Back Office &gt; Integrasi</span>
                </div>

                {{-- Secret Key --}}
                <div class="space-y-2" x-data="{ showSecret: false }">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">DOKU Secret Key / Shared Key</label>
                    <div class="relative">
                        <input :type="showSecret ? 'text' : 'password'" wire:model="doku_secret_key" placeholder="SK-..."
                               class="w-full text-xs font-mono font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 p-3.5 pr-10 focus:ring-emerald-500 focus:border-emerald-500">
                        <button type="button" @click="showSecret = !showSecret"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs font-bold">
                            <span x-text="showSecret ? '🙈' : '👁️'"></span>
                        </button>
                    </div>
                    <span class="text-[10px] text-slate-400 block">Kunci rahasia untuk HMAC Signature verification</span>
                </div>
            </div>

            {{-- DOKU Webhook & Callback URLs Info --}}
            <div class="p-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl space-y-3 text-xs">
                <span class="font-bold text-slate-700 dark:text-slate-300 block">📌 URL untuk Didaftarkan di DOKU Back Office:</span>
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2" x-data="{ copied: false }">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase text-slate-400 block">Notification / Webhook URL</span>
                        <code class="font-mono text-xs font-bold text-emerald-600 dark:text-emerald-400">{{ $notificationUrl }}</code>
                    </div>
                    <button type="button" @click="navigator.clipboard.writeText('{{ $notificationUrl }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-[10px] font-bold transition-all shrink-0">
                        <span x-text="copied ? '✓ Tersalin!' : '📋 Salin URL Webhook'"></span>
                    </button>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2 border-t border-slate-200/60 dark:border-slate-800/60">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase text-slate-400 block">Callback / Return URL</span>
                        <code class="font-mono text-xs font-bold text-indigo-600 dark:text-indigo-400">{{ $returnUrl }}</code>
                    </div>
                </div>
            </div>

            {{-- Test Connection Button & Result Box --}}
            <div class="p-4 bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/40 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <span class="font-bold text-slate-900 dark:text-white block text-xs">Uji Validitas Kredensial API</span>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Kirim request uji coba ke DOKU API untuk memastikan Client ID & Secret Key terhubung sempurna.</p>
                </div>
                <button type="button" wire:click="testDokuConnection" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-2 shrink-0">
                    <span wire:loading.remove wire:target="testDokuConnection">⚡ Uji Koneksi DOKU</span>
                    <span wire:loading wire:target="testDokuConnection" class="inline-flex items-center gap-1.5">
                        <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Menghubungi API...
                    </span>
                </button>
            </div>

            @if($dokuTestResult)
                <div class="p-3.5 rounded-2xl text-xs font-semibold flex items-center gap-2.5 {{ $dokuTestResult['success'] ? 'bg-emerald-100 text-emerald-900 dark:bg-emerald-950/80 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-800' : 'bg-rose-100 text-rose-900 dark:bg-rose-950/80 dark:text-rose-200 border border-rose-300 dark:border-rose-800' }}">
                    <span>{{ $dokuTestResult['success'] ? '✅' : '❌' }}</span>
                    <span>{{ $dokuTestResult['message'] }}</span>
                </div>
            @endif

            {{-- Submit Button --}}
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                <button type="submit"
                        class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Pengaturan Developer</span>
                </button>
            </div>

        </div>
    </form>
</div>
