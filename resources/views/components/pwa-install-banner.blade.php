<div x-data="{ 
        canInstall: false, 
        isDismissed: localStorage.getItem('elvith_pwa_dismissed') === 'true',
        isIOS: /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream,
        isStandalone: window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true,
        showIOSGuide: false,
        init() {
            if (this.isStandalone) return;
            
            // Check Android / Chromium install prompt
            window.addEventListener('pwa-installable', () => {
                if (!this.isDismissed) this.canInstall = true;
            });

            // If prompt was already caught before Alpine init
            if (window.deferredPwaPrompt && !this.isDismissed) {
                this.canInstall = true;
            }

            // Show for iOS if mobile and not in standalone
            if (this.isIOS && !this.isStandalone && !this.isDismissed) {
                this.canInstall = true;
            }
        },
        async promptInstall() {
            if (this.isIOS) {
                this.showIOSGuide = true;
                return;
            }
            if (window.deferredPwaPrompt) {
                window.deferredPwaPrompt.prompt();
                const { outcome } = await window.deferredPwaPrompt.userChoice;
                if (outcome === 'accepted') {
                    this.canInstall = false;
                }
                window.deferredPwaPrompt = null;
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
                Akses lebih cepat, hemat kuota & mudah dibuka langsung dari Beranda HP.
            </p>
        </div>
        <button @click="dismiss()" class="text-slate-400 hover:text-white p-1 -mr-1 -mt-1 rounded-lg hover:bg-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- iOS Guide Modal / Popup -->
    <div x-show="showIOSGuide" class="text-xs bg-slate-800/90 rounded-xl p-3 border border-slate-700 text-slate-300 space-y-1.5">
        <div class="font-semibold text-emerald-400 flex items-center gap-1">
            <span>Cara pasang di iPhone/iPad:</span>
        </div>
        <ol class="list-decimal list-inside space-y-1 text-[11px] text-slate-300">
            <li>Tekan tombol Bagikan / <strong>Share</strong> (<svg class="w-3.5 h-3.5 inline-block text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>) di Safari</li>
            <li>Pilih menu <strong>"Tambahkan ke Layar Utama"</strong> (<em>Add to Home Screen</em>)</li>
            <li>Tekan <strong>Tambah</strong> (<em>Add</em>) di pojok kanan atas</li>
        </ol>
    </div>

    <div class="flex items-center gap-2 pt-1">
        <button @click="promptInstall()" class="flex-1 py-2 px-3 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-semibold rounded-xl text-xs shadow-md shadow-emerald-900/40 transition flex items-center justify-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            <span x-text="isIOS ? 'Petunjuk Pasang' : 'Pasang Sekarang'"></span>
        </button>
        <button @click="dismiss()" class="py-2 px-3 bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium rounded-xl text-xs transition">
            Nanti Saja
        </button>
    </div>
</div>
