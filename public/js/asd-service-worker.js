self.addEventListener("install", (event) => {
  self.skipWaiting();
});
self.addEventListener("activate", (event) => {
  event.waitUntil(self.clients.claim());
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
    data = {
      title: "Notification",
      body: event.data.text(),
      url: "",
      push_icon_url: "",
      push_badge_url: "",
      push_interaction: "silent",
    };
  }

  const options = {
    body: data.body || "You have a new notification",
    icon: data.push_icon_url,
    badge: data.push_badge_url,
    requireInteraction: data.push_interaction,
    data: {
      url: data.url || "/",
    },
  };
  console.log(options);
  event.waitUntil(self.registration.showNotification(data.title || "Notification", options));
});
