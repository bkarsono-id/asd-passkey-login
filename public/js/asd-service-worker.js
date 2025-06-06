/**
 * Handles the 'install' event for the service worker.
 * Forces the waiting service worker to become the active service worker.
 *
 * @param {ExtendableEvent} event
 */

self.addEventListener("install", (event) => {
  self.skipWaiting();
});

/**
 * Handles the 'activate' event for the service worker.
 * Claims control of all clients as soon as the service worker becomes active.
 *
 * @param {ExtendableEvent} event
 */

self.addEventListener("activate", (event) => {
  event.waitUntil(self.clients.claim());
});

/**
 * Handles notification click events.
 * Opens the target URL specified in the notification data or a default URL.
 *
 * @param {NotificationEvent} event
 */
self.addEventListener("notificationclick", function (event) {
  let url = new URL(self.location.origin);
  const targetUrl = event.notification.data?.url || "https://passwordless.alciasolusidigital.com";

  event.notification.close();
  event.waitUntil(clients.openWindow(targetUrl));
});

/**
 * Handles push events.
 * Displays a notification using the data received from the push event.
 *
 * @param {PushEvent} event
 */
self.addEventListener("push", (event) => {
  let data = {};
  let options = {};
  try {
    data = event.data.json();
  } catch (e) {
    data = {
      title: "Notification",
      body: event.data.text(),
      url: "https://passwordless.alciasolusidigital.com",
      push_icon_url: "",
      push_badge_url: "",
      push_interaction: false,
    };
  }
  if (data.vibrate === true) {
    options = {
      body: data.body || "You have a new notification",
      icon: data.push_icon_url,
      badge: data.push_badge_url,
      requireInteraction: data.push_interaction,
      silent: data.silent,
      vibrate: [100, 50, 100],
      data: {
        url: data.url || "/",
      },
    };
  } else {
    options = {
      body: data.body || "You have a new notification",
      icon: data.push_icon_url,
      badge: data.push_badge_url,
      requireInteraction: data.push_interaction,
      silent: data.silent,
      data: {
        url: data.url || "/",
      },
    };
  }
  event.waitUntil(self.registration.showNotification(data.title || "Notification", options));
});
