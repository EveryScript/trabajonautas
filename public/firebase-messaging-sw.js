importScripts(
    "https://www.gstatic.com/firebasejs/11.8.1/firebase-app-compat.js"
);
importScripts(
    "https://www.gstatic.com/firebasejs/11.8.1/firebase-messaging-compat.js"
);

firebase.initializeApp({
    apiKey: "AIzaSyAvwNRxrt1CRWOO5Q09uFLRpbUf00GVfzA",
    authDomain: "every-script-cloud.firebaseapp.com",
    projectId: "every-script-cloud",
    storageBucket: "every-script-cloud.firebasestorage.app",
    messagingSenderId: "702129503021",
    appId: "1:702129503021:web:afe3b44ed73bea558331a4",
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function (payload) {
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
    };
    self.registration.showNotification(notificationTitle, notificationOptions);
});
