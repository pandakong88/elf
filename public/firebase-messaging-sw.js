// Firebase Messaging Service Worker (Background Push Notifications) v2
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

// Update service worker immediately
self.addEventListener('install', function(event) {
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    event.waitUntil(clients.claim());
});

// Firebase background message listener (silent - let SDK render webpush payload)
messaging.onBackgroundMessage(function(payload) {
    console.log('[FCM SW v2] Background payload received');
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