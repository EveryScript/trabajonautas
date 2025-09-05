importScripts(
    "https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"
);
importScripts(
    "https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js"
);

firebase.initializeApp({
    apiKey: "AIzaSyBKn9I--sruLzQFQVgAayPMZuTt6tKU8A8",
    authDomain: "trabajonautas-notifications.firebaseapp.com",
    projectId: "trabajonautas-notifications",
    storageBucket: "trabajonautas-notifications.firebasestorage.app",
    messagingSenderId: "888362496290",
    appId: "1:888362496290:web:04394616572da440fef57b",
    measurementId: "G-FTYMBGYPTP",
});

const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage(function (payload) {
    const title = payload.data.title || "Trabajonautas";
    const body = payload.data.body || "";
    const icon = payload.data.icon || "storage/img/tbn-icon.ico";
    const clickAction = payload.data.click_action || "https://trabajonautas.com";
    self.registration.showNotification(title, {
        body: body,
        icon: icon,
        data: {
            url: clickAction,
        },
    });
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    const url = event.notification.data && event.notification.data.url
        ? event.notification.data.url
        : 'https://trabajonautas.com';

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