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
