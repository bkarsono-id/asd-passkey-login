self.addEventListener("install", (event) => {
  self.skipWaiting();
});
self.addEventListener("activate", (event) => {
  event.waitUntil(self.clients.claim());
  /* test */
});
self.addEventListener("notificationclick", function (event) {
  let url = new URL(self.location.origin);
  const targetUrl = event.notification.data?.url || "https://passwordless.alciasolusidigital.com";

  event.notification.close();
  event.waitUntil(clients.openWindow(targetUrl));
});
self.addEventListener("push", (event) => {
  let data = {};
  try {
    data = event.data.json();
  } catch (e) {
    data = { title: "Notification", body: event.data.text(), url: "" };
  }

  const options = {
    body: data.body || "You have a new notification",
    icon: "/image/icons/icon-192x192.png",
    badge: "/image/icons/icon-192x192.png",
    data: {
      url: data.url || "/",
    },
  };

  event.waitUntil(self.registration.showNotification(data.title || "Notification", options));
});
