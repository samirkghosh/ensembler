// Initialize BroadcastChannel
const channel = new BroadcastChannel('tab-focus-channel');

// Save the original title and favicon to restore later
const originalTitle = document.title;
const originalFavicon = $('#favicon').attr('href');

// Listen for messages
channel.onmessage = (event) => {
if (event.data === 'focus-tab') {
    notifyUser();
}
};

// Function to change tab title, favicon, and send notification
function notifyUser() {
if (document.hidden) {
    document.title = "🔔 Incoming Call! - " + originalTitle;
    changeFavicon('incoming-call-favicon.ico');
    changeTitleBarColor('#ff0000');

    // Check for notification permission
    if (Notification.permission === "granted") {
        new Notification("Incoming Call", {
            body: "Please switch to the main tab.",
        });
    } else if (Notification.permission !== "denied") {
        Notification.requestPermission().then((permission) => {
            if (permission === "granted") {
                new Notification("Incoming Call", {
                    body: "Please switch to the main tab.",
                });
            }
        });
    }

    // Focus the tab
    window.focus();
} else {
    alert("You have an incoming call. Please switch to this tab.");
}
}
