{{-- FCM Push Notification Init — hanya di-render untuk user yang sudah login --}}
@auth
<script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js"></script>

<script>
window.elvithFcm = {
    messaging: null,
    isInitialized: false,

    async requestPermissionAndRegister() {
        console.log('[FCM] Meminta izin notifikasi browser secara manual...');
        try {
            if (!('Notification' in window)) {
                alert('Browser Anda tidak mendukung notifikasi Web Push.');
                return false;
            }

            const permission = await Notification.requestPermission();
            console.log('[FCM] Permission result:', permission);

            if (permission === 'granted') {
                return await this.registerToken();
            } else if (permission === 'denied') {
                alert('Izin notifikasi diblokir di setelan browser. Buka ikon gembok di address bar untuk mengubah izin ke "Izinkan".');
                return false;
            } else {
                return false;
            }
        } catch (err) {
            console.error('[FCM] Gagal request permission:', err);
            return false;
        }
    },

    async registerToken() {
        try {
            if (!('serviceWorker' in navigator)) return false;

            const swReg = await navigator.serviceWorker.register('/firebase-messaging-sw.js', { scope: '/' });
            this.messaging.useServiceWorker(swReg);

            const VAPID_KEY = "BC9so406q2ySAfwRFSvUqWMkntM3pgaQ-W0TpCo6NInrOkJsiryrqDTElPxH5Iva6iHrcz61LlALMaY7ETKCtHE";
            const token = await this.messaging.getToken({ vapidKey: VAPID_KEY });

            if (!token) return false;

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

            console.log('[FCM] Token tersimpan ke backend');
            return true;
        } catch (e) {
            console.error('[FCM] Error register token:', e);
            return false;
        }
    }
};

(function() {
    const firebaseConfig = {
        apiKey:            "AIzaSyCEiBx6quKPkuFpKpA2nvFvapVfylTGvDA",
        authDomain:        "elvith.firebaseapp.com",
        projectId:         "elvith",
        storageBucket:     "elvith.firebasestorage.app",
        messagingSenderId: "577760254277",
        appId:             "1:577760254277:web:da29e5e357811ca86d462c",
    };

    if (!firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
    }

    if (!('serviceWorker' in navigator) || !('Notification' in window)) {
        return;
    }

    window.elvithFcm.messaging = firebase.messaging();
    window.elvithFcm.isInitialized = true;

    // Jika izin sudah diberikan sebelumnya, daftarkan otomatis di background
    if (Notification.permission === 'granted') {
        window.elvithFcm.registerToken();
    }
})();
</script>
@endauth