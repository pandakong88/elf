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

// Handle background messages
messaging.onBackgroundMessage(function(payload) {
    const title = payload.notification?.title || payload.data?.title || 'Elvith Notifikasi';
    const body  = payload.notification?.body  || payload.data?.body  || '';
    const clickUrl = payload.fcmOptions?.link || payload.data?.url   || '/keuangan/billing';
    const tag   = payload.data?.submission_code ? ('sub-' + payload.data.submission_code) : 'elvith-transfer-single';

    const options = {
        body: body,
        icon: '/icons/icon-192x192.png',
        badge: '/icons/icon-72x72.png',
        vibrate: [200, 100, 200],
        requireInteraction: true,
        tag: tag, // Tag stabil agar notifikasi menumpuk rapi / tidak menduplikasi
        data: { url: clickUrl }
    };

    return self.registration.showNotification(title, options);
});

// Handle notification click
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    const url = event.notification.data?.url || '/keuangan/billing';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            for (const client of clientList) {
                if (client.url.includes(url) && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});