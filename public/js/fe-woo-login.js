const loginViaPasskey = async () => {
  try {
    spinnerOn();
    infoMessageBox(
      "Please hold on for a moment while we check for biometric data. The authenticator will be displayed shortly."
    );
    const config = {
      apiKey: asd_ajax.api_key,
      apiUrl: asd_ajax.api_url,
    };
    JwtEAuth.config(config);
    const result = await JwtEAuth.userLoginMediation();
    if (result.status === "success") {
      try {
        const jwtToken = result.token;
        infoMessageBox("Biometric valid, checking token...");
        const response = await fetch(asd_ajax.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams({
            action: "asd_woo_passkey_login",
            token: jwtToken,
            _wpnonce: asd_ajax.ajax_woo_login_nonce,
          }),
        });

        const bcresult = await response.json();
        if (bcresult.success) {
          infoMessageBox(
            "Biometric is valid, wait a moment while redirecting..."
          );
          setTimeout(() => {
            window.location.href = bcresult.data.redirect;
          }, 2000);
        } else {
          errorMessageBox(
            bcresult.data.message || "Login failed. Please try again."
          );
          spinnerOff();
        }
      } catch (error) {
        errorMessageBox(error);
        spinnerOff();
      }
    } else if (result.status === "error") {
      errorMessageBox(result.msg);
      spinnerOff();
    }
  } catch (error) {
    console.error("Error during passkey login:", error);
  }
};
const oAuthGoogleHandle = async (callback) => {
  infoMessageBox("Checking token...");
  const response = await fetch(asd_ajax.ajax_url, {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: new URLSearchParams({
      action: "asd_google_check_token",
      token: callback.credential,
      _wpnonce: asd_ajax.ajax_nonce,
    }),
  });
  const bcresult = await response.json();
  if (bcresult.success) {
    infoMessageBox("Credential is valid, wait a moment while redirecting...");
    setTimeout(() => {
      window.location.href = bcresult.data.redirect;
    }, 2000);
  } else {
    errorMessageBox(bcresult.data.message || "Login failed. Please try again.");
    spinnerOff();
  }
};
document.addEventListener("DOMContentLoaded", function () {
  const box = document.getElementById("asd-passkey-login-wrapper");
  const submit = document.querySelector(".submit");
  if (box && submit) {
    submit.insertAdjacentElement("afterend", box);
  }
  if (box) {
    box.style.display = "block";
  }
  const viapasskey = document.getElementById("login-via-passkey");
  if (viapasskey) {
    viapasskey.addEventListener("click", async function (event) {
      event.preventDefault();
      await loginViaPasskey();
    });
  }

  if ("credentials" in navigator && navigator.credentials.preventSilentAccess) {
    google.accounts.id.initialize({
      client_id: asd_ajax.google_client_id,
      callback: oAuthGoogleHandle,
      cancel_on_tap_outside: false,
    });
    google.accounts.id.prompt();
  } else {
    console.log("FedCM is not supported.");
  }
});
