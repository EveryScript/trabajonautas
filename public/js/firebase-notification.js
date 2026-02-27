// Esperar a que el DOM esté listo
document.addEventListener("DOMContentLoaded", function () {
    console.log("firebase notifications foreground loaded!");

    // Configurations
    const firebaseConfig = {
        apiKey: "AIzaSyBKn9I--sruLzQFQVgAayPMZuTt6tKU8A8",
        authDomain: "trabajonautas-notifications.firebaseapp.com",
        projectId: "trabajonautas-notifications",
        storageBucket: "trabajonautas-notifications.firebasestorage.app",
        messagingSenderId: "888362496290",
        appId: "1:888362496290:web:04394616572da440fef57b",
    };

    // Initialize Firebase
    if (!firebase.apps.length) firebase.initializeApp(firebaseConfig);

    const messaging = firebase.messaging();

    // Request permission for notifications
    Notification.requestPermission().then((permission) => {
        if (permission === "granted") {
            console.log("Notification browser activated!");
        }
    });

    // Foreground message handler
    messaging.onMessage((payload) => {
        console.log("¡Mensaje en primer plano!", payload);

        const title = payload.data.title || "Nueva Notificación";
        const body = payload.data.body || "";
        const icon = payload.data.icon || "storage/img/tbn-icon.ico";

        const options = {
            body: body,
            icon: icon,
        };

        const notification = new Notification(title, options);

        notification.onclick = function (event) {
            event.preventDefault();
            window.focus();
            window.location.href = event.target.data.click_action;
            notification.close();
        };
    });
});
