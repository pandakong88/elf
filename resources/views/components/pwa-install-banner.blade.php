<div x-data="{ 
        canInstall: false, 
        isDismissed: localStorage.getItem('elvith_pwa_dismissed') === 'true',
        isIOS: /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream,
        isAndroid: /Android/.test(navigator.userAgent),
        isStandalone: window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true,
        showHelp: false,
        init() {
            if (this.isStandalone) return;
            
            // Catch installable event from browser
            window.addEventListener('pwa-installable', () => {
                if (!this.isDismissed) this.canInstall = true;
            });

            if (window.deferredPwaPrompt && !this.isDismissed) {
                this.canInstall = true;
            }

            // Always show on mobile browser if not standalone yet
            if ((this.isAndroid || this.isIOS) && !this.isDismissed) {
                this.canInstall = true;
            }
        },
        async promptInstall() {
            if (window.deferredPwaPrompt) {
                window.deferredPwaPrompt.prompt();
                const { outcome } = await window.deferredPwaPrompt.userChoice;
                if (outcome === 'accepted') {
                    this.canInstall = false;
                }
                window.deferredPwaPrompt = null;
            } else {
                // If browser did not fire beforeinstallprompt (e.g. Chrome/Samsung Internet timing)
                this.showHelp = true;
            }
        },
        dismiss() {
            this.canInstall = false;
            this.isDismissed = true;
            localStorage.setItem('elvith_pwa_dismissed', 'true');
        }
    }" 
    x-show="canInstall && !isStandalone" 
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="translate-y-full opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    x-transition:leave="transition ease-in duration-200 transform"
    x-transition:leave-start="translate-y-0 opacity-100"
    x-transition:leave-end="translate-y-full opacity-0"
    class="fixed bottom-4 left-4 right-4 md:left-auto md:right-6 md:max-w-sm z-50 bg-slate-900/95 backdrop-blur-md text-white p-4 rounded-2xl shadow-2xl border border-slate-700/60 flex flex-col gap-3"
    style="display: none;">
    
    <div class="flex items-start gap-3">
        <div class="w-12 h-12 rounded-xl bg-emerald-900 border border-emerald-500/30 flex items-center justify-center p-2 flex-shrink-0 shadow-inner">
            <img src="/icons/icon-96x96.png" alt="Elvith Logo" class="w-full h-full object-contain">
        </div>
        <div class="flex-1 min-w-0">
            <h4 class="text-sm font-bold text-white tracking-wide flex items-center gap-1.5">
                Pasang Aplikasi Elvith
                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">PWA</span>
            </h4>
            <p class="text-xs text-slate-300 mt-0.5 leading-snug">
                Akses lebih cepat & mudah dibuka langsung dari layar HP tanpa buka browser.
            </p>
        </div>
        <button @click="dismiss()" class="text-slate-400 hover:text-white p-1 -mr-1 -mt-1 rounded-lg hover:bg-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Help Instructions Modal/Box if direct prompt is blocked by browser -->
    <div x-show="showHelp" class="text-xs bg-slate-800/90 rounded-xl p-3 border border-slate-700 text-slate-300 space-y-1.5">
        <template x-if="isAndroid">
            <div>
                <div class="font-semibold text-emerald-400 mb-1">Cara pasang di Android (Chrome/Browser):</div>
                <ol class="list-decimal list-inside space-y-1 text-[11px] text-slate-300">
                    <li>Tekan tombol **titik tiga (⋮)** di pojok kanan atas browser.</li>
                    <li>Pilih menu **"Install aplikasi"** atau **"Tambahkan ke Layar Utama"** (*Add to Home Screen*).</li>
                    <li>Tekan **Install / Tambahkan**.</li>
                </ol>
            </div>
        </template>
        <template x-if="isIOS">
            <div>
                <div class="font-semibold text-emerald-400 mb-1">Cara pasang di iPhone/Safari:</div>
                <ol class="list-decimal list-inside space-y-1 text-[11px] text-slate-300">
                    <li>Tekan tombol **Bagikan** (ikon kotak panah atas) di bilah Safari.</li>
                    <li>Pilih menu **"Tambahkan ke Layar Utama"**.</li>
                    <li>Tekan **Tambah** di pojok kanan atas.</li>
                </ol>
            </div>
        </template>
    </div>

    <div class="flex items-center gap-2 pt-1">
        <button @click="promptInstall()" class="flex-1 py-2 px-3 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-semibold rounded-xl text-xs shadow-md shadow-emerald-900/40 transition flex items-center justify-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            <span>Pasang Sekarang</span>
        </button>
        <button @click="dismiss()" class="py-2 px-3 bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium rounded-xl text-xs transition">
            Nanti Saja
        </button>
    </div>
</div>
