{{-- FCM Push Notification Init — hanya di-render untuk user yang sudah login --}}
@auth
<script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js"></script>

<script>
(function() {
    console.log('[FCM] Memulai inisialisasi FCM via Compat SDK...');

    const firebaseConfig = {
        apiKey:            "AIzaSyCEiBx6quKPkuFpKpA2nvFvapVfylTGvDA",
        authDomain:        "elvith.firebaseapp.com",
        projectId:         "elvith",
        storageBucket:     "elvith.firebasestorage.app",
        messagingSenderId: "577760254277",
        appId:             "1:577760254277:web:da29e5e357811ca86d462c",
    };

    const VAPID_KEY = "BC9so406q2ySAfwRFSvUqWMkntM3pgaQ-W0TpCo6NInrOkJsiryrqDTElPxH5Iva6iHrcz61LlALMaY7ETKCtHE";

    if (!firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
    }

    if (!('serviceWorker' in navigator) || !('Notification' in window)) {
        console.warn('[FCM] Browser tidak mendukung ServiceWorker atau Notification');
        return;
    }

    const messaging = firebase.messaging();

    async function registerAndSaveToken() {
        try {
            const swReg = await navigator.serviceWorker.register('/firebase-messaging-sw.js', { scope: '/' });
            console.log('[FCM] ServiceWorker registered scope:', swReg.scope);

            // Gunakan SW registration untuk messaging
            messaging.useServiceWorker(swReg);

            const permission = await Notification.requestPermission();
            console.log('[FCM] Permission status:', permission);

            if (permission !== 'granted') {
                console.warn('[FCM] Izin notifikasi tidak diberikan');
                return;
            }

            console.log('[FCM] Mengambil device token dari Google...');
            const token = await messaging.getToken({ vapidKey: VAPID_KEY });

            if (!token) {
                console.warn('[FCM] Token kosong');
                return;
            }

            console.log('[FCM] Token didapat:', token.substring(0, 25) + '...');

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const response = await fetch('/fcm/token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ token: token })
            });

            const result = await response.json();
            console.log('[FCM] Sukses simpan token ke database:', result);

            // Handler pesan masuk saat tab web aktif
            messaging.onMessage(function(payload) {
                console.log('[FCM] Pesan foreground:', payload);
                const title = payload.notification?.title || 'Notifikasi Elvith';
                const body = payload.notification?.body || '';
                const clickUrl = payload.fcmOptions?.link || payload.data?.url || '/keuangan/billing';

                if (Notification.permission === 'granted') {
                    const notif = new Notification(title, {
                        body: body,
                        icon: '/icons/icon-192x192.png',
                        badge: '/icons/icon-72x72.png',
                        requireInteraction: true
                    });
                    notif.onclick = function() {
                        window.focus();
                        window.location.href = clickUrl;
                    };
                }
            });

        } catch (error) {
            console.error('[FCM] Terjadi kendala saat registrasi token:', error);
        }
    }

    registerAndSaveToken();
})();
</script>
@endauth