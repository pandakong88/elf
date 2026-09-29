{{-- FCM Push Notification Init — hanya di-render untuk user yang sudah login --}}
@auth
<script type="module">
    import { initializeApp }  from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
    import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging.js";

    const firebaseConfig = {
        apiKey:            "AIzaSyCEiBx6quKPkuFpKpA2nvFvapVfylTGvDA",
        authDomain:        "elvith.firebaseapp.com",
        projectId:         "elvith",
        storageBucket:     "elvith.firebasestorage.app",
        messagingSenderId: "577760254277",
        appId:             "1:577760254277:web:da29e5e357811ca86d462c",
    };

    const VAPID_KEY = "BC9so406q2ySAfwRFSvUqWMkntM3pgaQ-W0TpCo6NInrOkJsiryrqDTElPxH5Iva6iHrcz61LlALMaY7ETKCtHE";

    const app       = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);

    async function initFcm() {
        console.log('[FCM] Memulai inisialisasi FCM...');
        try {
            if (!('serviceWorker' in navigator)) {
                console.warn('[FCM] Browser tidak mendukung ServiceWorker');
                return;
            }

            // Register Firebase background SW
            const swReg = await navigator.serviceWorker.register('/firebase-messaging-sw.js', { scope: '/' });
            console.log('[FCM] ServiceWorker registered:', swReg.scope);

            // Request notification permission
            const permission = await Notification.requestPermission();
            console.log('[FCM] Permission status:', permission);
            if (permission !== 'granted') {
                console.warn('[FCM] Izin notifikasi belum diberikan atau ditolak');
                return;
            }

            // Get FCM token
            console.log('[FCM] Meminta token ke Google Firebase...');
            const token = await getToken(messaging, {
                vapidKey: VAPID_KEY,
                serviceWorkerRegistration: swReg,
            });

            if (!token) {
                console.warn('[FCM] Tidak berhasil mendapatkan token');
                return;
            }

            console.log('[FCM] Token berhasil didapat:', token.substring(0, 20) + '...');

            // Save token to backend
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch('/fcm/token', {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept':       'application/json',
                },
                body: JSON.stringify({ token }),
            });

            const data = await res.json();
            console.log('[FCM] Respon simpan token backend:', data);

            // Foreground message handler
            onMessage(messaging, (payload) => {
                console.log('[FCM] Foreground push diterima:', payload);
                const title   = payload.notification?.title ?? 'Elvith Notifikasi';
                const body    = payload.notification?.body  ?? '';
                const clickUrl = payload.fcmOptions?.link ?? payload.data?.url ?? '/';

                if (Notification.permission === 'granted') {
                    const notif = new Notification(title, {
                        body:  body,
                        icon:  '/icons/icon-192x192.png',
                        badge: '/icons/icon-72x72.png',
                        tag:   'elvith-fg-' + Date.now(),
                        requireInteraction: true,
                    });
                    notif.onclick = () => {
                        window.focus();
                        window.location.href = clickUrl;
                    };
                }
            });

        } catch (err) {
            console.error('[FCM] Init error detail:', err);
        }
    }

    // Jalankan segera saat DOM siap
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFcm);
    } else {
        initFcm();
    }
</script>
@endauth