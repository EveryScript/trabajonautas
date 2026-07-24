// Esperar a que el DOM esté listo
document.addEventListener("DOMContentLoaded", function () {
    const firebaseConfig = window.firebaseConfig || {};

    if (
        !firebaseConfig.apiKey
        || !firebaseConfig.projectId
        || !firebaseConfig.messagingSenderId
        || !firebaseConfig.appId
    ) {
        console.warn("Firebase notifications disabled: missing web configuration.");
        return;
    }

    if (!firebase.apps.length) firebase.initializeApp(firebaseConfig);

    window.messaging = firebase.messaging();

    // Request permission for notifications
    Notification.requestPermission().then((permission) => {
        if (permission === "granted") {
            console.log("Notification browser activated!");
        }
    });

    // Foreground message handler
    window.messaging.onMessage((payload) => {
        console.info("Mensaje de Firebase recibido en primer plano.");

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
