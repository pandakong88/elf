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

// Universal push listener (fallback standar browser jika SDK background delay)
self.addEventListener('push', function(event) {
    let title = 'Elvith Notifikasi';
    let body = 'Ada update baru dari sistem Elvith.';
    let clickUrl = '/keuangan/billing';
    let icon = '/icons/icon-192x192.png';

    if (event.data) {
        try {
            const data = event.data.json();
            title = data.notification?.title || data.data?.title || title;
            body = data.notification?.body || data.data?.body || body;
            clickUrl = data.fcmOptions?.link || data.data?.url || clickUrl;
        } catch (e) {
            body = event.data.text() || body;
        }
    }

    const options = {
        body: body,
        icon: icon,
        badge: '/icons/icon-72x72.png',
        vibrate: [200, 100, 200],
        requireInteraction: true,
        tag: 'elvith-notif-' + Date.now(),
        data: { url: clickUrl }
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Handle notification click
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    const url = event.notification.data?.url || '/';

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