// Firebase Messaging Service Worker (Background Push Notifications)
// Terpisah dari service-worker.js PWA — ini khusus untuk FCM background messages

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

// Handle background messages (saat tab ditutup / browser di background)
messaging.onBackgroundMessage(function(payload) {
    console.log('[FCM SW] Background message received:', payload);

    const notifTitle = payload.notification?.title ?? 'Elvith Notifikasi';
    const notifBody  = payload.notification?.body  ?? '';
    const clickUrl   = payload.fcmOptions?.link ?? payload.data?.url ?? '/';

    const options = {
        body:             notifBody,
        icon:             '/icons/icon-192x192.png',
        badge:            '/icons/icon-72x72.png',
        requireInteraction: true,
        tag:              'elvith-' + (payload.data?.type ?? 'notif'),
        data:             { url: clickUrl },
    };

    self.registration.showNotification(notifTitle, options);
});

// Handle notification click — buka tab/fokus ke URL yang ditentukan
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    const url = event.notification.data?.url ?? '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            for (const client of clientList) {
                if (client.url === url && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});