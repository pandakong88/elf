<div class="p-6 max-w-5xl mx-auto space-y-6">
    {{-- Header Banner --}}
    <div class="bg-slate-950 bg-gradient-to-r from-slate-900 via-emerald-950 to-slate-900 border border-emerald-500/30 rounded-3xl p-6 sm:p-8 text-white shadow-2xl relative overflow-hidden" style="background-color: #020617;">
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
                    <span class="text-sm font-bold text-slate-900 dark:text-slate-100 block">Aktifkan Pembayaran Online DOKU di Portal Wali</span>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Saat status **AKTIF**, wali santri dapat memilih metode pembayaran online resmi (Virtual Account, QRIS, E-Wallet) yang diverifikasi langsung oleh DOKU.
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

        </div>

        {{-- PUSH NOTIFIKASI (FIREBASE FCM) & WHATSAPP CARD --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                    <span>🔔 Pengaturan Push Notifikasi (Firebase FCM) & WhatsApp</span>
                </h3>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $fcm_enabled ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' }}">
                    STATUS FCM: {{ $fcm_enabled ? 'AKTIF (ON)' : 'NONAKTIF (OFF)' }}
                </span>
            </div>

            {{-- Status & Diagnostics Widgets --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Credential Status --}}
                <div class="p-4 rounded-2xl border {{ $firebaseCredentialsExist ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40' : 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-200 dark:border-rose-800/40' }}">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold {{ $firebaseCredentialsExist ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-700 dark:text-rose-400' }}">
                            {{ $firebaseCredentialsExist ? '✓ Kredensial Firebase Terhubung' : '✕ Kredensial Tidak Ditemukan' }}
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 space-y-0.5">
                        <p>Project: <span class="font-mono font-bold text-slate-800 dark:text-slate-200">{{ $firebaseProjectId ?: 'elvith-notif' }}</span></p>
                        <p class="truncate" title="storage/app/firebase/firebase_credentials.json">File: <span class="font-mono text-[10px]">firebase_credentials.json</span></p>
                    </div>
                </div>

                {{-- Active Tokens Count --}}
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-1">Perangkat Web Aktif (60 Hari)</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $activeTokensCount }}</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400">Token Terdaftar</span>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Browser admin/bendahara yang mengaktifkan izin notifikasi.</p>
                </div>

                {{-- Role Distribution --}}
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-1.5">Sebaran Perangkat per Role</span>
                    <div class="flex flex-wrap gap-1.5">
                        <span class="px-2 py-0.5 rounded-md bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300 text-[10px] font-bold">
                            Super Admin: {{ $tokenStats['super_admin'] ?? 0 }}
                        </span>
                        <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 text-[10px] font-bold">
                            Putra: {{ $tokenStats['bendahara_putra'] ?? 0 }}
                        </span>
                        <span class="px-2 py-0.5 rounded-md bg-pink-100 text-pink-700 dark:bg-pink-900/40 dark:text-pink-300 text-[10px] font-bold">
                            Putri: {{ $tokenStats['bendahara_putri'] ?? 0 }}
                        </span>
                        <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 text-[10px] font-bold">
                            Pusat: {{ $tokenStats['bendahara_pondok'] ?? 0 }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Master Toggles (FCM & WhatsApp) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Master FCM Push Toggle --}}
                <div class="flex items-start justify-between p-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl gap-3">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-slate-900 dark:text-slate-100 block">Master Push Notifikasi Browser (FCM)</span>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                            Aktifkan pengiriman pop-up web push notifikasi ke perangkat pengurus/bendahara.
                        </p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0 mt-0.5">
                        <input type="checkbox" wire:model="fcm_enabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 dark:bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                {{-- WhatsApp Transfer Alert Toggle --}}
                <div class="flex items-start justify-between p-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl gap-3">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-slate-900 dark:text-slate-100 block">Kirim Alert WA ke Grup Bendahara</span>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                            Kirim notifikasi pesan instan WhatsApp ke grup/nomor bendahara saat wali mengunggah bukti transfer manual.
                        </p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0 mt-0.5">
                        <input type="checkbox" wire:model="wa_notify_transfers" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 dark:bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>
            </div>

            {{-- Routing Matrix / Forwarding Controls --}}
            <div class="p-5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl space-y-3">
                <div>
                    <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider flex items-center gap-1.5">
                        <span>🎯 Matriks Forwarding Notifikasi Transfer Santri</span>
                    </h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                        Notifikasi transfer santri putra otomatis dikirim ke <strong>Bendahara Putra</strong>, dan santri putri ke <strong>Bendahara Putri</strong>. Pilih akun tambahan yang juga ingin menerima salinan notifikasi:
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                    {{-- Forward Bendahara Pondok --}}
                    <label class="flex items-start gap-3 p-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl cursor-pointer hover:border-emerald-500/50 transition-colors">
                        <input type="checkbox" wire:model="fcm_notify_bendahara_pondok" class="mt-0.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <div class="space-y-0.5">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Bendahara Pondok (Pusat)</span>
                            <span class="text-[10px] text-slate-400 block">Terima notifikasi dari asrama Putra & Putri.</span>
                        </div>
                    </label>

                    {{-- Forward Super Admin --}}
                    <label class="flex items-start gap-3 p-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl cursor-pointer hover:border-emerald-500/50 transition-colors">
                        <input type="checkbox" wire:model="fcm_notify_super_admin" class="mt-0.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <div class="space-y-0.5">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Super Admin (Monitoring)</span>
                            <span class="text-[10px] text-slate-400 block">Pantau seluruh transfer masuk secara realtime.</span>
                        </div>
                    </label>

                    {{-- Forward Manajemen --}}
                    <label class="flex items-start gap-3 p-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl cursor-pointer hover:border-emerald-500/50 transition-colors">
                        <input type="checkbox" wire:model="fcm_notify_manajemen" class="mt-0.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <div class="space-y-0.5">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Akun Manajemen / Direksi</span>
                            <span class="text-[10px] text-slate-400 block">Salinan notifikasi untuk jajaran pimpinan.</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Live Push Notification Tester --}}
            <div class="p-5 bg-gradient-to-br from-slate-50 to-slate-100/60 dark:from-slate-950 dark:to-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-2xl space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/80 dark:border-slate-800/80 pb-3">
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider flex items-center gap-1.5">
                            <span>⚡ Uji Coba Pengiriman Push Notifikasi Langsung</span>
                        </h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            Kirim pesan uji coba ke browser untuk mengonfirmasi bahwa Service Worker dan browser pop-up berfungsi lancar.
                        </p>
                    </div>
                    <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded-md self-start sm:self-auto">
                        Live Sandbox
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    {{-- Target Selector --}}
                    <div class="space-y-1 sm:col-span-1">
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">Target Penerima Test</label>
                        <select wire:model="test_target"
                                class="w-full text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 p-2.5 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="me">📱 Hanya Browser Saya Sendiri</option>
                            <option value="putra">👦 Channel Bendahara Putra</option>
                            <option value="putri">👧 Channel Bendahara Putri</option>
                            <option value="all_bendahara">👥 Seluruh Bendahara (Putra & Putri)</option>
                        </select>
                    </div>

                    {{-- Title Input --}}
                    <div class="space-y-1 sm:col-span-1">
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">Judul Notifikasi</label>
                        <input type="text" wire:model="test_title" placeholder="Judul notifikasi..."
                               class="w-full text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 p-2.5 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    {{-- Body Input --}}
                    <div class="space-y-1 sm:col-span-1">
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">Isi Pesan Notifikasi</label>
                        <input type="text" wire:model="test_body" placeholder="Isi pesan notifikasi..."
                               class="w-full text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 p-2.5 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                    <p class="text-[10px] text-slate-400">
                        Catatan: Pastikan browser telah mengizinkan notifikasi di domain ini.
                    </p>
                    <button type="button" wire:click="sendTestPushNotification" wire:loading.attr="disabled"
                            class="w-full sm:w-auto px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center justify-center gap-2 shrink-0">
                        <span wire:loading.remove wire:target="sendTestPushNotification">🚀 Kirim Notifikasi Uji Coba</span>
                        <span wire:loading wire:target="sendTestPushNotification" class="inline-flex items-center gap-1.5">
                            <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Mengirim ke FCM...
                        </span>
                    </button>
                </div>

                @if($testNotificationResult)
                    <div class="p-3.5 rounded-2xl text-xs font-semibold flex items-center gap-2.5 {{ $testNotificationResult['success'] ? 'bg-emerald-100 text-emerald-900 dark:bg-emerald-950/80 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-800' : 'bg-rose-100 text-rose-900 dark:bg-rose-950/80 dark:text-rose-200 border border-rose-300 dark:border-rose-800' }}">
                        <span>{{ $testNotificationResult['success'] ? '✅' : '❌' }}</span>
                        <span>{{ $testNotificationResult['message'] }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Save Settings Button Bar --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm">
            <div class="text-xs text-slate-500 dark:text-slate-400">
                <span>Klik tombol simpan untuk memperbarui pengaturan Developer, Gateway DOKU, dan Notifikasi secara permanen.</span>
            </div>
            <button type="submit"
                    class="w-full sm:w-auto px-6 py-3 bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-all shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Simpan Seluruh Pengaturan</span>
            </button>
        </div>
    </form>
</div>
