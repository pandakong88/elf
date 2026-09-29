{{-- FCM Push Notification Init — hanya di-render untuk user yang sudah login --}}
@auth
<script type="module">
    import { initializeApp }  from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
    import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging.js";

    const firebaseConfig = {
        apiKey:            "{{ config('services.firebase.api_key') }}",
        authDomain:        "{{ config('services.firebase.project_id') }}.firebaseapp.com",
        projectId:         "{{ config('services.firebase.project_id') }}",
        storageBucket:     "{{ config('services.firebase.project_id') }}.firebasestorage.app",
        messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
        appId:             "{{ config('services.firebase.app_id') }}",
    };

    const VAPID_KEY = "{{ config('services.firebase.vapid_key') }}";

    const app       = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);

    async function initFcm() {
        try {
            // Register Firebase background SW
            const swReg = await navigator.serviceWorker.register('/firebase-messaging-sw.js', { scope: '/' });

            // Request notification permission
            const permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                console.log('[FCM] Notification permission denied');
                return;
            }

            // Get FCM token
            const token = await getToken(messaging, {
                vapidKey:            VAPID_KEY,
                serviceWorkerRegistration: swReg,
            });

            if (!token) {
                console.warn('[FCM] Could not get token — try again later');
                return;
            }

            // Save token to backend
            await fetch('/fcm/token', {
                method:  'POST',
                headers: {
                    'Content-Type':     'application/json',
                    'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
                    'Accept':           'application/json',
                },
                body: JSON.stringify({ token }),
            });

            console.log('[FCM] Token saved successfully');

            // Handle foreground messages (saat tab sedang aktif/terbuka)
            onMessage(messaging, (payload) => {
                console.log('[FCM] Foreground message:', payload);

                const title   = payload.notification?.title ?? 'Elvith';
                const body    = payload.notification?.body  ?? '';
                const clickUrl = payload.fcmOptions?.link ?? payload.data?.url ?? '/';

                // Show browser notification even when tab is open
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
            console.warn('[FCM] Init error:', err);
        }
    }

    // Init after page load
    if ('serviceWorker' in navigator && 'Notification' in window) {
        window.addEventListener('load', initFcm);
    }
</script>
@endauth