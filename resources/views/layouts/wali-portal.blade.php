<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Portal Wali Santri — Al-Fithroh' }}</title>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const toast = document.getElementById('copyToast');
                if (toast) {
                    toast.classList.remove('translate-y-20', 'opacity-0');
                    toast.classList.add('translate-y-0', 'opacity-100');
                    setTimeout(() => {
                        toast.classList.remove('translate-y-0', 'opacity-100');
                        toast.classList.add('translate-y-20', 'opacity-0');
                    }, 2500);
                }
            });
        }

        // Generator Gambar Struk Simulasi Native (100% Handal & Cepat)
        function generateAndDownloadSimulasiImage(santriName, nominal, itemsData) {
            try {
                const items = typeof itemsData === 'string' ? JSON.parse(itemsData) : itemsData;
                if (!items || items.length === 0) {
                    alert('Belum ada data simulasi yang dapat diunduh.');
                    return;
                }

                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                const width = 600;
                const padding = 30;
                const itemHeight = 44;
                const headerHeight = 110;
                const footerHeight = 60;
                const height = headerHeight + (items.length * itemHeight) + footerHeight + 60;

                canvas.width = width;
                canvas.height = height;

                // Background
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, width, height);

                // Frame Border
                ctx.strokeStyle = '#059669';
                ctx.lineWidth = 6;
                ctx.strokeRect(3, 3, width - 6, height - 6);

                // Header Banner
                ctx.fillStyle = '#047857';
                ctx.fillRect(6, 6, width - 12, 85);

                // Header Title Text
                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 20px sans-serif';
                ctx.fillText('SIMULASI PEMBAYARAN SANTRI', padding, 40);
                ctx.font = '13px sans-serif';
                ctx.fillStyle = '#a7f3d0';
                ctx.fillText('Pondok Pesantren Al-Fithroh', padding, 64);

                // Santri Info & Nominal Header
                let y = 125;
                ctx.fillStyle = '#0f172a';
                ctx.font = 'bold 16px sans-serif';
                ctx.fillText('Nama Santri: ' + santriName, padding, y);

                ctx.fillStyle = '#047857';
                ctx.font = 'bold 15px sans-serif';
                ctx.textAlign = 'right';
                ctx.fillText('Rencana Bayar: Rp ' + Number(nominal).toLocaleString('id-ID'), width - padding, y);
                ctx.textAlign = 'left';

                y += 18;
                ctx.strokeStyle = '#cbd5e1';
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.moveTo(padding, y);
                ctx.lineTo(width - padding, y);
                ctx.stroke();

                // Section Label
                y += 28;
                ctx.font = 'bold 12px sans-serif';
                ctx.fillStyle = '#64748b';
                ctx.fillText('RINCIAN ALOKASI TAGIHAN:', padding, y);
                y += 15;

                // Loop items
                items.forEach((item, index) => {
                    y += 10;
                    ctx.fillStyle = index % 2 === 0 ? '#f8fafc' : '#ffffff';
                    ctx.fillRect(padding, y - 18, width - (padding * 2), 36);

                    // Border line for item
                    ctx.strokeStyle = '#e2e8f0';
                    ctx.lineWidth = 1;
                    ctx.strokeRect(padding, y - 18, width - (padding * 2), 36);

                    ctx.fillStyle = '#0f172a';
                    ctx.font = 'bold 12px sans-serif';
                    ctx.fillText((index + 1) + '. ' + item.label, padding + 10, y + 4);

                    const isLunas = item.status.includes('LUNAS');
                    ctx.fillStyle = isLunas ? '#047857' : '#b45309';
                    ctx.font = 'bold 11px sans-serif';
                    ctx.textAlign = 'right';
                    ctx.fillText(item.status, width - padding - 10, y + 4);
                    ctx.textAlign = 'left';

                    y += 26;
                });

                // Footer Info
                y += 30;
                ctx.strokeStyle = '#e2e8f0';
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.moveTo(padding, y);
                ctx.lineTo(width - padding, y);
                ctx.stroke();

                y += 20;
                ctx.fillStyle = '#94a3b8';
                ctx.font = '11px sans-serif';
                const todayStr = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                ctx.fillText('Dicetak dari Portal Wali Al-Fithroh • Tanggal: ' + todayStr, padding, y);

                // Download File Action
                const link = document.createElement('a');
                const cleanName = santriName.replace(/[^a-zA-Z0-9]/g, '-');
                link.download = 'Simulasi-Bayar-' + cleanName + '.png';
                link.href = canvas.toDataURL('image/png');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } catch (err) {
                console.error(err);
                alert('Gagal mengunduh gambar. Silakan hubungi admin.');
            }
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        [x-cloak] { display: none !important; }
    </style>
    @livewireStyles
</head>
<body x-data="{ sidebarOpen: false }" class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased min-h-screen flex flex-col transition-colors duration-200">

    @php
        $contents = \App\Modules\Core\Models\LandingPageContent::all()->pluck('value', 'key')->toArray();
        $drawerBank1Name = $bank1Name ?? ($contents['wali_bank1_name_putra'] ?? 'Bank Syariah Indonesia (BSI)');
        $drawerBsiRek    = $bsiRekening ?? ($contents['wali_bsi_putra'] ?? '7123456789');
        $drawerBsiAn     = $bsiAn ?? ($contents['wali_bsi_putra_an'] ?? 'Pesantren Al-Fithroh Putra');

        $drawerBank2Name = $bank2Name ?? ($contents['wali_bank2_name_putra'] ?? 'Bank BRI');
        $drawerBriRek    = $briRekening ?? ($contents['wali_bri_putra'] ?? '001201009876504');
        $drawerBriAn     = $briAn ?? ($contents['wali_bri_putra_an'] ?? 'Yayasan Al-Fithroh Putra');

        $drawerWa        = $waBendahara ?? ($contents['wali_wa_putra'] ?? '6281234567890');
        $drawerWaName    = $waName ?? ($contents['wali_wa_putra_name'] ?? 'Bendahara Putra Al-Fithroh');
        $cleanWaNumber   = preg_replace('/[^0-9]/', '', $drawerWa);
        $drawerWaUrl     = "https://wa.me/{$cleanWaNumber}?text=" . urlencode("Assalamu'alaikum {$drawerWaName}, saya Wali Santri ingin konfirmasi pembayaran.");
    @endphp

    <!-- Header Ramah Wali -->
    <header class="bg-emerald-700 dark:bg-slate-900 text-white shadow-lg sticky top-0 z-30 px-4 py-3 border-b border-emerald-800 dark:border-slate-800 transition-colors">
        <div class="max-w-md mx-auto flex items-center justify-between">
            <a href="{{ url('/portal-wali') }}" class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 text-emerald-700 dark:text-emerald-400 flex items-center justify-center font-black text-lg shadow-md border border-emerald-600/30 dark:border-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                </div>
                <div>
                    <h1 class="text-sm font-bold tracking-tight text-white">Portal Wali Santri</h1>
                    <p class="text-[11px] text-emerald-100 dark:text-slate-400 font-medium">Pondok Pesantren Al-Fithroh</p>
                </div>
            </a>

            <div class="flex items-center gap-2">
                <!-- Tombol Menu Sidebar -->
                <button type="button" 
                        @click="sidebarOpen = true"
                        class="px-2.5 py-1.5 rounded-xl bg-emerald-800/80 dark:bg-slate-800 hover:bg-emerald-800 dark:hover:bg-slate-700 text-emerald-100 dark:text-slate-200 border border-emerald-600/50 dark:border-slate-700 transition-all flex items-center gap-1.5 text-xs font-bold shadow-sm">
                    <svg class="w-4 h-4 text-emerald-300 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <span>Menu</span>
                </button>

                <!-- Toggle Mode Gelap/Terang -->
                <button type="button" 
                        x-data="{ isDark: document.documentElement.classList.contains('dark') }" 
                        @click="isDark = !isDark; if(isDark) { document.documentElement.classList.add('dark'); localStorage.setItem('theme', 'dark'); } else { document.documentElement.classList.remove('dark'); localStorage.setItem('theme', 'light'); }"
                        title="Ubah Mode Gelap / Terang"
                        class="p-2 rounded-xl bg-emerald-800/60 dark:bg-slate-800 hover:bg-emerald-800 dark:hover:bg-slate-700 text-emerald-100 dark:text-amber-400 border border-emerald-600/50 dark:border-slate-700 transition-all flex items-center justify-center">
                    <svg x-show="!isDark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="isDark" x-cloak class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
            </div>
        </div>
    </header>

    <!-- SIDEBAR DRAWER SLIDE-OVER MENU -->
    <div x-show="sidebarOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-hidden" 
         role="dialog" 
         aria-modal="true">
        <!-- Backdrop dark overlay -->
        <div x-show="sidebarOpen" 
             x-transition:enter="ease-in-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in-out duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div x-show="sidebarOpen" 
                 x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="w-screen max-w-xs sm:max-w-sm bg-white dark:bg-slate-900 shadow-2xl border-l border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                
                <!-- Drawer Header -->
                <div class="p-4 bg-emerald-700 dark:bg-slate-950 text-white flex items-center justify-between border-b border-emerald-800 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-300 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h2 class="text-sm font-extrabold tracking-wide">Menu Bantuan Wali</h2>
                    </div>
                    <button type="button" @click="sidebarOpen = false" class="p-1.5 rounded-xl hover:bg-emerald-800 dark:hover:bg-slate-800 text-emerald-100 dark:text-slate-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Drawer Scrollable Content -->
                <div class="flex-1 overflow-y-auto p-4 space-y-5 text-xs text-slate-700 dark:text-slate-300">
                    
                    <!-- 1. Rekening Pembayaran Resmi (Dinamis dari CMS) -->
                    <div class="space-y-3">
                        <h3 class="text-xs font-black uppercase text-slate-800 dark:text-slate-200 tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span>Rekening Pembayaran Bank</span>
                        </h3>

                        <!-- Card Bank 1 -->
                        @if(!empty($drawerBsiRek))
                            <div class="bg-slate-50 dark:bg-slate-950 p-3 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-extrabold text-emerald-700 dark:text-emerald-400">{{ $drawerBank1Name }}</span>
                                    <span class="text-[9px] font-bold bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 px-2 py-0.5 rounded-md">Utama</span>
                                </div>
                                <div class="flex items-center justify-between font-mono bg-white dark:bg-slate-900 p-2 rounded-xl border border-slate-200 dark:border-slate-800">
                                    <span class="font-black text-sm text-slate-900 dark:text-slate-100">{{ $drawerBsiRek }}</span>
                                    <button type="button" 
                                            onclick="copyToClipboard('{{ $drawerBsiRek }}')"
                                            class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-sans text-[11px] font-bold rounded-lg transition-all flex items-center gap-1 shadow-xs active:scale-95">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        <span>Salin</span>
                                    </button>
                                </div>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400">a.n. {{ $drawerBsiAn }}</p>
                            </div>
                        @endif

                        <!-- Card Bank 2 (Hanya tampil jika diisi di CMS) -->
                        @if(!empty($drawerBriRek))
                            <div class="bg-slate-50 dark:bg-slate-950 p-3 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-extrabold text-blue-700 dark:text-blue-400">{{ $drawerBank2Name }}</span>
                                    <span class="text-[9px] font-bold bg-blue-100 dark:bg-blue-500/10 text-blue-800 dark:text-blue-300 px-2 py-0.5 rounded-md">Alternatif</span>
                                </div>
                                <div class="flex items-center justify-between font-mono bg-white dark:bg-slate-900 p-2 rounded-xl border border-slate-200 dark:border-slate-800">
                                    <span class="font-black text-sm text-slate-900 dark:text-slate-100">{{ $drawerBriRek }}</span>
                                    <button type="button" 
                                            onclick="copyToClipboard('{{ $drawerBriRek }}')"
                                            class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-sans text-[11px] font-bold rounded-lg transition-all flex items-center gap-1 shadow-xs active:scale-95">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        <span>Salin</span>
                                    </button>
                                </div>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400">a.n. {{ $drawerBriAn }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- 2. Chat WhatsApp Bendahara Dinamis -->
                    <div class="space-y-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                        <h3 class="text-xs font-black uppercase text-slate-800 dark:text-slate-200 tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span>Konfirmasi / Tanya Bendahara</span>
                        </h3>
                        <a href="{{ $drawerWaUrl }}" 
                           target="_blank" 
                           class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-extrabold rounded-2xl transition-all shadow-md flex items-center justify-center gap-2 text-xs tracking-wide">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                            <span>Chat WhatsApp {{ $drawerWaName }}</span>
                        </a>
                    </div>

                    <!-- 3. FAQ / Pertanyaan Umum -->
                    <div class="space-y-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                        <h3 class="text-xs font-black uppercase text-slate-800 dark:text-slate-200 tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Tanya Jawab (FAQ)</span>
                        </h3>
                        <div class="space-y-2 text-[11px]">
                            <div class="bg-slate-50 dark:bg-slate-950 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                                <strong class="text-slate-800 dark:text-slate-200 block mb-0.5">Bagaimana cara kirim bukti bayar?</strong>
                                <span class="text-slate-500 dark:text-slate-400">Setelah transfer, foto resi/bukti bayar lalu kirimkan via tombol WhatsApp Bendahara di atas.</span>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-950 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                                <strong class="text-slate-800 dark:text-slate-200 block mb-0.5">Apakah bisa membayar tunai?</strong>
                                <span class="text-slate-500 dark:text-slate-400">Bisa. Pembayaran tunai diterima langsung di kantor Kasir Bendahara Pesantren.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Drawer Footer -->
                <div class="p-4 bg-slate-50 dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800 text-center">
                    <p class="text-[10px] text-slate-400">Pondok Pesantren Al-Fithroh © {{ date('Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Area (Mobile Container) -->
    <main class="flex-1 w-full max-w-md mx-auto p-4 pb-12">
        {{ $slot }}
    </main>

    <!-- Toast Notification Copy Success -->
    <div id="copyToast" 
         class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
        <div class="bg-slate-900 text-white text-xs font-bold px-4 py-2.5 rounded-2xl shadow-2xl flex items-center gap-2 border border-slate-700">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>Nomor rekening berhasil disalin!</span>
        </div>
    </div>

    <!-- Footer Ramah -->
    <footer class="py-5 text-center border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs text-slate-500 dark:text-slate-400 space-y-1 shadow-inner transition-colors">
        <p class="font-semibold text-slate-700 dark:text-slate-300">Pondok Pesantren Al-Fithroh</p>
        <p class="text-[11px] text-slate-400 dark:text-slate-500">Layanan Informasi Tagihan Wali Santri © {{ date('Y') }}</p>
    </footer>

    @livewireScripts
</body>
</html>
