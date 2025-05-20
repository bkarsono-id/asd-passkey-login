const publicKey = webpush.public_key;
let swObject = null;

/**
 * Converts a base64 string to a Uint8Array, used for push notification keys.
 *
 * @param {string} base64String The base64 encoded string.
 * @returns {Uint8Array}
 */

const urlBase64ToUint8Array = (base64String) => {
  const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
  const base64 = (base64String + padding).replace(/\-/g, "+").replace(/_/g, "/");

  const rawData = atob(base64);
  const outputArray = new Uint8Array(rawData.length);

  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
};

/**
 * Registers the service worker and initializes the push notification UI.
 *
 * @returns {void}
 */
if ("serviceWorker" in navigator && "PushManager" in window) {
  window.addEventListener("load", () => {
    navigator.serviceWorker
      .register(webpush.ajax_public_url + "js/asd-service-worker.js")
      .then((registration) => {
        registration.update();
        swObject = registration;
        showUI();
        console.log("Service Worker Registered Successfully:", registration);
      })
      .catch((error) => {
        console.log("Service Worker Registered Failed:", error);
      });
  });
}

/**
 * Shows the appropriate UI dialog based on the user's subscription status.
 *
 * @returns {Promise<void>}
 */
const showUI = async () => {
  swObject.pushManager.getSubscription().then((subscription) => {
    isSubscribed = !(subscription === null);
    if (isSubscribed) {
      unSubcribeDialog();
      console.log("User IS subscribed.");
    } else {
      subcribeDialog();
      console.log("User is NOT subscribed.");
    }
  });
};

/**
 * Handles the subscribe dialog and user subscription process.
 *
 * @returns {void}
 */
const subcribeDialog = () => {
  showModalSubscribe();
  const btnSubcribe = document.getElementById("asd-subscribe-button");
  if (btnSubcribe) {
    const txtSubcribe = document.getElementById("asd-subscribe-text");
    btnSubcribe.addEventListener("click", async function () {
      btnSubcribe.disabled = true;
      btnSubcribe.textContent = "Loading...";
      txtSubcribe.textContent =
        "Please wait a moment while subscribing. Allow notifications if they are currently blocked.";
      try {
        const subscription = await swObject.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: urlBase64ToUint8Array(publicKey),
        });
        const response = await updateSubcriberOnServer(subscription);
        if (!response.success) {
          throw new Error(response.data.message || "Failed to update subscriber.");
        } else {
          if (Notification.permission === "granted") {
            btnSubcribe.disabled = true;
            btnSubcribe.textContent = "Done";
            txtSubcribe.textContent = "Subscription success.";
          } else if (Notification.permission !== "denied") {
            Notification.requestPermission().then((permission) => {
              if (permission === "granted") {
                const notification = new Notification("Hi there, welcome!");
              }
            });
          } else {
            btnSubcribe.disabled = false;
            btnSubcribe.textContent = "Try again";
            txtSubcribe.textContent = "Notification is still blocked. Please allow it.";
          }
          setTimeout(() => {
            Swal.close();
          }, 2000);
        }
      } catch (error) {
        console.error("Failed to subscribe the user:", error);
        btnSubcribe.disabled = false;
        btnSubcribe.textContent = "Try again";
        txtSubcribe.textContent =
          error.message + " Please allow notification." || "An error occurred. Please try again.";
      }
    });
  }
};

/**
 * Handles the unsubscribe dialog and user unsubscription process.
 *
 * @returns {void}
 */
const unSubcribeDialog = () => {
  showModalUnSubscribe();
  const btnUnSubcribe = document.getElementById("asd-unsubscribe-button");
  if (btnUnSubcribe) {
    const txtUnSubcribe = document.getElementById("asd-unsubscribe-text");
    btnUnSubcribe.addEventListener("click", async function () {
      btnUnSubcribe.disabled = true;
      btnUnSubcribe.textContent = "Loading...";
      txtUnSubcribe.textContent = "Please wait a moment while unsubscribing.";
      /* unsubscribe user */
      swObject.pushManager.getSubscription().then(function (subscription) {
        if (subscription) {
          return subscription.unsubscribe().then(async function () {
            const response = await updateUnSubcriberOnServer(subscription);
            if (!response.success) {
              btnUnSubcribe.disabled = false;
              btnUnSubcribe.textContent = "Try again";
              txtUnSubcribe.textContent = response.data.message;
              return;
            }
            btnUnSubcribe.disabled = true;
            btnUnSubcribe.textContent = "Done";
            txtUnSubcribe.textContent = "Unsubcription success.";
            setTimeout(() => {
              Swal.close();
            }, 2000);
          });
        }
      });
    });
  }
  return;
};

/**
 * Sends the subscription object to the server to save the subscriber.
 *
 * @param {PushSubscription} subscription The push subscription object.
 * @returns {Promise<Object>}
 */
const updateUnSubcriberOnServer = async (subscription) => {
  try {
    const response = await fetch(webpush.ajax_url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        action: "asd_save_unsubscriber",
        subcription: JSON.stringify(subscription),
        _wpnonce: webpush.ajax_nonce,
      }),
    });
    return await response.json();
  } catch (error) {
    console.error("Error saving subscriber:", error);
  }
};

/**
 * Sends the subscription object to the server to remove the subscriber.
 *
 * @param {PushSubscription} subscription The push subscription object.
 * @returns {Promise<Object>}
 */
const updateSubcriberOnServer = async (subscription) => {
  try {
    const response = await fetch(webpush.ajax_url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        action: "asd_save_subscriber",
        subcription: JSON.stringify(subscription),
        _wpnonce: webpush.ajax_nonce,
      }),
    });
    return await response.json();
  } catch (error) {
    console.error("Error saving subscriber:", error);
    return;
  }
};

/**
 * Shows the modal dialog for subscribing to notifications.
 *
 * @returns {void}
 */
const showModalSubscribe = () => {
  Swal.fire({
    width: 500,
    position: "top",
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    backdrop: false,
    timer: 15000,
    html: `
      <div style="margin:10px;font-size:16px; display: flex; align-items: center; justify-content: space-between; gap: 10px;">
        <span style="text-align: left;" id="asd-subscribe-text">Get notified about restocks, discounts, and special deals—subscribe now!</span>
          <button id="asd-subscribe-button" style="padding: 10px 15px; background-color: #3085d6; color: white; border: none; border-radius: 4px; cursor: pointer; white-space: nowrap;">
          Subscribe
        </button>
      </div>
    `,
    timerProgressBar: true,
    showClass: {
      popup: `
        animate__animated
        animate__slideInDown
        animate__faster
      `,
      icon: "swal2-icon-show",
    },
    hideClass: {
      popup: `
        animate__animated
        animate__slideOutUp
        animate__faster
      `,
    },
  }).then((result) => {
    if (result.dismiss === Swal.DismissReason.timer) {
      console.log("Dialog was closed by the timer");
    }
  });
};

/**
 * Shows the modal dialog for unsubscribing from notifications.
 *
 * @returns {void}
 */
const showModalUnSubscribe = () => {
  Swal.fire({
    width: 500,
    position: "top",
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    backdrop: false,
    timer: 10000,
    html: `
      <div style="margin:10px;font-size:16px; display: flex; align-items: center; justify-content: space-between; gap: 10px;">
        <span style="text-align: left;" id="asd-unsubscribe-text">Click 'Unsubscribe' to stop receiving notifications from us.</span>
          <button id="asd-unsubscribe-button" style="padding: 5px 15px; background-color:rgb(180, 180, 180); color: white; border: none; border-radius: 4px; cursor: pointer; white-space: nowrap;">
          Unsubscribe
        </button>
      </div>
    `,
    timerProgressBar: true,
    showClass: {
      popup: `
        animate__animated
        animate__slideInDown
        animate__faster
      `,
      icon: "swal2-icon-show",
    },
    hideClass: {
      popup: `
        animate__animated
        animate__slideOutUp
        animate__faster
      `,
    },
  }).then((result) => {
    if (result.dismiss === Swal.DismissReason.timer) {
      console.log("Dialog was closed by the timer");
    }
  });
};
