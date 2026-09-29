<div class="space-y-6 max-w-4xl mx-auto pb-12">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-xl">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Pengaturan Notifikasi Real-time</h1>
                <p class="text-xs sm:text-sm text-slate-400">Kelola izin browser dan perangkat Anda untuk menerima notifikasi transfer santri.</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- Tombol Daftarkan / Sambungkan Ulang Perangkat -->
            <button type="button"
                    id="btn-register-device"
                    onclick="triggerManualRegistration()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold transition-all shadow-lg shadow-blue-600/20 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span id="btn-register-text">Sambungkan Perangkat Ini</span>
            </button>

            <!-- Tombol Tes Notifikasi -->
            <button type="button"
                    wire:click="testNotification" 
                    wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all shadow-lg shadow-emerald-600/20 shrink-0">
                <span wire:loading.remove wire:target="testNotification">🔔 Tes Bunyi Notifikasi</span>
                <span wire:loading wire:target="testNotification">Mengirim...</span>
            </button>
        </div>
    </div>

    <!-- Alert Status Interaktif -->
    <div id="client-alert-box" class="hidden p-4 rounded-xl text-xs font-semibold flex items-center gap-3">
        <span id="client-alert-text"></span>
    </div>

    @if($message)
        <div class="p-4 rounded-xl text-xs font-semibold flex items-center gap-3 {{ $statusType === 'success' ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-300' : 'bg-rose-500/10 border border-rose-500/20 text-rose-300' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ $message }}</span>
        </div>
    @endif

    <!-- Status Izin di Perangkat Ini -->
    <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-xl space-y-4">
        <h2 class="text-sm font-bold text-slate-200 uppercase tracking-wider">Status Perangkat Saat Ini</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-slate-950 p-4 rounded-xl border border-slate-800/80 flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-400 block">Izin Notifikasi Browser</span>
                    <span id="browser-permission-label" class="text-sm font-bold text-emerald-400">Mengecek...</span>
                </div>
                <div id="browser-permission-badge" class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>

            <div class="bg-slate-950 p-4 rounded-xl border border-slate-800/80 flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-400 block">Perangkat Terhubung</span>
                    <span class="text-sm font-bold text-white">{{ count($userTokens) }} Perangkat Aktif</span>
                </div>
                <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>

        <div class="pt-2 text-xs text-slate-400 leading-relaxed bg-slate-950/50 p-4 rounded-xl border border-slate-800/50">
            <span class="font-bold text-slate-300">💡 Panduan untuk Admin / Bendahara:</span>
            <ul class="list-disc list-inside mt-1 space-y-1">
                <li>Jika perangkat Anda belum terdaftar di bawah, klik tombol biru <strong>"Sambungkan Perangkat Ini"</strong>.</li>
                <li>Setelah tersambung, Anda bisa menguji bunyi notifikasi dengan tombol hijau di sampingnya.</li>
            </ul>
        </div>
    </div>

    <!-- Daftar Perangkat Terhubung -->
    <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-xl space-y-4">
        <h2 class="text-sm font-bold text-slate-200 uppercase tracking-wider">Daftar Perangkat Akun Anda</h2>

        @if(empty($userTokens))
            <div class="text-center py-8 text-slate-500 text-xs">
                Belum ada perangkat yang terdaftar. Klik tombol biru <strong>"Sambungkan Perangkat Ini"</strong> di atas.
            </div>
        @else
            <div class="space-y-3">
                @foreach($userTokens as $t)
                    <div class="bg-slate-950 p-4 rounded-xl border border-slate-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-white">{{ $t['device_info'] ? substr($t['device_info'], 0, 60) . '...' : 'Perangkat Terdaftar' }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Aktif</span>
                            </div>
                            <span class="text-[11px] text-slate-500 block">Terakhir aktif: {{ \Carbon\Carbon::parse($t['last_active_at'])->translatedFormat('d M Y, H:i') }} WIB</span>
                        </div>
                        <button type="button"
                                wire:click="deleteToken({{ $t['id'] }})"
                                class="text-xs font-bold text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 px-3 py-1.5 rounded-lg border border-rose-500/30 transition-all self-start sm:self-auto">
                            Putus Perangkat
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
    function updatePermissionUI() {
        const permLabel = document.getElementById('browser-permission-label');
        if (!permLabel) return;

        if ('Notification' in window) {
            if (Notification.permission === 'granted') {
                permLabel.innerText = 'Diizinkan (Aktif)';
                permLabel.className = 'text-sm font-bold text-emerald-400';
            } else if (Notification.permission === 'denied') {
                permLabel.innerText = 'Diblokir';
                permLabel.className = 'text-sm font-bold text-rose-400';
            } else {
                permLabel.innerText = 'Belum Diatur';
                permLabel.className = 'text-sm font-bold text-amber-400';
            }
        }
    }

    function showClientAlert(text, isSuccess) {
        const box = document.getElementById('client-alert-box');
        const textSpan = document.getElementById('client-alert-text');
        if (!box || !textSpan) return;

        textSpan.innerText = text;
        box.className = 'p-4 rounded-xl text-xs font-semibold flex items-center gap-3 ' + 
            (isSuccess ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-300' : 'bg-rose-500/10 border border-rose-500/20 text-rose-300');
        box.classList.remove('hidden');
    }

    async function triggerManualRegistration() {
        const btnText = document.getElementById('btn-register-text');
        if (btnText) btnText.innerText = 'Menghubungkan...';

        try {
            if (!('Notification' in window) || !('serviceWorker' in navigator)) {
                showClientAlert('Browser tidak mendukung notifikasi push.', false);
                return;
            }

            const permission = await Notification.requestPermission();
            updatePermissionUI();

            if (permission !== 'granted') {
                showClientAlert('Izin notifikasi tidak diberikan di browser.', false);
                return;
            }

            // Daftarkan Service Worker
            const swReg = await navigator.serviceWorker.register('/firebase-messaging-sw.js', { scope: '/' });
            await navigator.serviceWorker.ready;

            const messaging = firebase.messaging();

            const VAPID_KEY = "BC9so406q2ySAfwRFSvUqWMkntM3pgaQ-W0TpCo6NInrOkJsiryrqDTElPxH5Iva6iHrcz61LlALMaY7ETKCtHE";
            
            // Di Firebase v9/v10 Compat, serviceWorkerRegistration dimasukkan di dalam options getToken
            const token = await messaging.getToken({ 
                vapidKey: VAPID_KEY,
                serviceWorkerRegistration: swReg
            });

            if (!token) {
                showClientAlert('Gagal mengambil token dari Google Firebase.', false);
                return;
            }

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch('/fcm/token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ token: token })
            });

            const data = await res.json();
            if (data.status === 'ok') {
                showClientAlert('Perangkat berhasil tersambung kembali ke notifikasi Elvith!', true);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showClientAlert('Server menolak penyimpanan token: ' + JSON.stringify(data), false);
            }
        } catch (err) {
            console.error(err);
            showClientAlert('Terjadi kesalahan: ' + err.message, false);
        } finally {
            if (btnText) btnText.innerText = 'Sambungkan Perangkat Ini';
        }
    }

    document.addEventListener('DOMContentLoaded', updatePermissionUI);
    document.addEventListener('livewire:navigated', updatePermissionUI);
</script>