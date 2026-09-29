// Firebase Messaging Service Worker (Background Push Notifications)
importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey:            "AIzaSyCEiBx6quKPkuFpKpA2nvFvapVfylTGvDA",
    authDomain:        "elvith.firebaseapp.com",
    projectId:         "elvith",
    storageBucket:     "elvith.firebasestorage.app",
    messagingSenderId: "577760254277",
    appId:             "1:577760254277:web:da29e5e357811ca86d462c"
});

const messaging = firebase.messaging();

// Firebase SDK otomatis menampilkan notifikasi dari payload.webpush.notification.
// Di onBackgroundMessage kita TIDAK memanggil self.registration.showNotification lagi
// agar tidak memunculkan notifikasi ganda/dobel di layar Android.
messaging.onBackgroundMessage(function(payload) {
    console.log('[FCM SW] Background payload diterima:', payload);
});

// Handle notification click — arahkan ke tab verifikasi transfer
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    const url = event.notification.data?.url || '/keuangan/billing?tab=transfers';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            for (const client of clientList) {
                if (client.url.includes('keuangan/billing') && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});