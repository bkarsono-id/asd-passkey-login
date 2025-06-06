/**
 * Handles the submission of the WooCommerce create passkey form.
 * Sends AJAX requests to register a passkey, handles responses, manages UI feedback,
 * and performs revocation if registration fails.
 *
 * @param {Event} event The form submit event.
 * @returns {Promise<void>}
 */
document.addEventListener("DOMContentLoaded", function () {
  let newnonce;
  const form = document.getElementById("createPasskeyForm");
  form.addEventListener("submit", async function (event) {
    event.preventDefault();
    SwalHoldModal();
    const formData = new FormData(form);
    formData.append("action", "asd_woo_passkey_register");
    formData.append("_wpnonce", asd_ajax.ajax_nonce_register);
    const wpnonce = formData.get("_wpnonce");
    const displayName = formData.get("displayName");
    const authType = formData.get("authenticator_type");
    try {
      const authResponse = await fetch(asd_ajax.ajax_url, {
        method: "POST",
        body: formData,
      });
      const authData = await authResponse.json();
      if (!authData.success) {
        errorModal(authData.data.message);
        return;
      }
    } catch (error) {
      console.log(error);
      errorModal("An error occurred during the process.");
    }

    const user = {
      userId: users.userId,
      userName: users.userName,
      userDisplayName: displayName,
      userEmail: users.userEmail,
    };
    const authenticator = {
      authenticator: authType,
    };
    const config = {
      apiKey: asd_ajax.api_key,
      apiUrl: asd_ajax.api_url,
    };
    const result = await JwtEAuth.config(config).userRegister(user, authenticator);
    if (result.status == "error") {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        html: result.msg,
        showConfirmButton: false,
        timer: 3500,
      });
      return 0;
    }
    const transactionID = result.code;
    swalLoader("Saving passkey data in progress.");
    const flagResponse = await fetch(asd_ajax.ajax_url, {
      method: "POST",
      body: new URLSearchParams({
        action: "asd_woo_passkey_flagging",
        token: result.token,
        _wpnonce: asd_ajax.ajax_nonce_flagging,
        authenticator: authType,
      }),
    });

    const dataResponse = await flagResponse.json();
    if (dataResponse.success) {
      setTimeout(() => {
        Swal.update({
          icon: "success",
          title: "Success",
          html: dataResponse.data.message,
          showConfirmButton: false,
        });
      }, 1000);
      setTimeout(() => {
        Swal.close();
      }, 2000);
    } else {
      /** trying revoke */
      const user = {
        userId: users.userId,
        userName: users.userName,
        userDisplayName: displayName,
        userEmail: users.userEmail,
        transactionID: transactionID,
      };
      const authenticator = {
        authenticator: authType,
      };
      const wp_msg = dataResponse.data.message;
      swalLoader("Trying revoke registration. Please wait a moment.", "error");
      const revokeResult = await JwtEAuth.revokeRegistration(user, authenticator);
      if (revokeResult.status === "success") {
        setTimeout(() => {
          Swal.hideLoading();
          Swal.update({
            icon: "error",
            title: "Registration Failed",
            html: wp_msg + ", " + revokeResult.msg,
            showConfirmButton: true,
          });
        }, 1000);
      } else {
        errorModal("Revoke failed");
      }
    }
  });
});
