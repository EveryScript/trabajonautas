importScripts(
    "https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"
);
importScripts(
    "https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js"
);

const params = new URL(self.location.href).searchParams;
const firebaseConfig = {
    apiKey: params.get("apiKey"),
    authDomain: params.get("authDomain"),
    projectId: params.get("projectId"),
    storageBucket: params.get("storageBucket"),
    messagingSenderId: params.get("messagingSenderId"),
    appId: params.get("appId"),
    measurementId: params.get("measurementId"),
};

if (
    firebaseConfig.apiKey
    && firebaseConfig.projectId
    && firebaseConfig.messagingSenderId
    && firebaseConfig.appId
) {
    firebase.initializeApp(firebaseConfig);

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
            data: { url: clickAction },
        });
    });
}

// Handle notification click
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
