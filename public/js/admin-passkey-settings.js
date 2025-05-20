document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("passkeySettingsForm");
  if (form) {
    form.addEventListener("submit", async function (event) {
      event.preventDefault();
      const formData = new FormData(form);
      formData.append("action", "asd_passkey_settings");
      formData.append("_wpnonce", asd_ajax.ajax_nonce);
      try {
        SwalHoldModal("Save settings.");
        const response = await fetch(asd_ajax.ajax_url, {
          method: "POST",
          body: formData,
        });
        const result = await response.json();
        if (result.success) {
          setTimeout(() => {
            Swal.update({
              icon: "success",
              title: "Success",
              html: result.data.message,
              showConfirmButton: false,
            });
          }, 1000);
          setTimeout(() => {
            Swal.close();
            window.location.reload(true);
          }, 2000);
        } else {
          errorModal("Error: " + (result.data.message || "Failed to save settings."));
        }
      } catch (error) {
        errorModal("AJAX request failed. Please try again.");
        console.error("Error:", error);
      }
    });
  }
  /* smtp */
  const formSMTP = document.getElementById("passkeySMTPForm");
  if (formSMTP) {
    formSMTP.addEventListener("submit", async function (event) {
      event.preventDefault();
      const formData = new FormData(formSMTP);
      formData.append("action", "asd_passkey_smtp_settings");
      formData.append("_wpnonce", asd_ajax.ajax_smtp_nonce);
      try {
        SwalHoldModal("Save SMTP settings.");
        const response = await fetch(asd_ajax.ajax_url, {
          method: "POST",
          body: formData,
        });
        const result = await response.json();
        if (result.success) {
          setTimeout(() => {
            Swal.update({
              icon: "success",
              title: "Success",
              html: result.data.message,
              showConfirmButton: false,
            });
          }, 1000);
          setTimeout(() => {
            Swal.close();
          }, 2000);
        } else {
          errorModal("Error: " + (result.data.message || "Failed to save settings."));
        }
      } catch (error) {
        errorModal("AJAX request failed. Please try again.");
        console.error("Error:", error);
      }
    });
  }
  /* smtp test */
  const btnSMTPTest = document.getElementById("test-smtp");
  const formSMTPTest = document.getElementById("passkeySMTPForm");
  if (btnSMTPTest && formSMTPTest) {
    btnSMTPTest.addEventListener("click", async function (event) {
      event.preventDefault();
      const formData = new FormData(formSMTPTest);
      formData.append("action", "asd_passkey_smtp_test");
      formData.append("_wpnonce", asd_ajax.ajax_smtp_test_nonce);
      try {
        SwalHoldModal("Test SMTP settings.");
        const response = await fetch(asd_ajax.ajax_url, {
          method: "POST",
          body: formData,
        });
        const result = await response.json();
        if (result.success) {
          setTimeout(() => {
            Swal.update({
              icon: "success",
              title: "Success",
              html: result.data.message,
              showConfirmButton: false,
            });
          }, 1000);
          setTimeout(() => {
            Swal.close();
          }, 2000);
        } else {
          errorModal("Error: " + (result.data.message || "Failed to save settings."));
        }
      } catch (error) {
        errorModal("AJAX request failed. Please try again.");
        console.error("Error:", error);
      }
    });
  }
  /* sync package */

  const btnSyncPackage = document.getElementById("btnSyncPackage");
  if (btnSyncPackage) {
    btnSyncPackage.addEventListener("click", async function (event) {
      try {
        SwalHoldModal("Please wait a moment while sync package.");
        const response = await fetch(asd_ajax.ajax_url, {
          method: "POST",
          body: new URLSearchParams({
            action: "asd_sync_package",
            _wpnonce: asd_ajax.ajax_sync_nonce,
          }),
        });

        const result = await response.json();
        if (result.success) {
          document.getElementById("asd_package_name").value = result.data.package;
          /* smtp */
          document.getElementById("asd_p4ssk3y_smtp_host").value = result.data.smtp.smtp_host;
          document.getElementById("asd_p4ssk3y_smtp_port").value = result.data.smtp.smtp_port;
          document.getElementById("asd_p4ssk3y_smtp_user").value = result.data.smtp.smtp_user;
          document.getElementById("asd_p4ssk3y_smtp_password").value = result.data.smtp.smtp_password;

          setTimeout(() => {
            Swal.update({
              icon: "success",
              title: "Success",
              html: "Sync package succesfull.",
              showConfirmButton: false,
            });
          }, 1000);
          setTimeout(() => {
            Swal.close();
          }, 2000);
        } else {
          errorModal("Error: " + (result.data.message || "Failed to save settings."));
        }
      } catch (error) {
        console.log(error);
        errorModal("AJAX request failed. Please try again.");
        console.error("Error:", error);
      }
    });
  }
  /** client id */

  const fedcmSelect = document.getElementById("asd_p4ssk3y_woo_login_fedcm_form");
  const clientId = document.getElementById("asd_google_client_id");
  const idp = document.getElementById("asd_p4ssk3y_woo_idp_provider");
  if (fedcmSelect && clientId) {
    fedcmSelect.addEventListener("change", function (event) {
      const selectedValue = event.target.value;
      controlBox(selectedValue, idp.value);
    });
    idp.addEventListener("change", function (event) {
      const idp = event.target.value;
      controlBox(fedcmSelect.value, idp);
    });
  }

  const controlBox = (selectedValue, idp) => {
    if (selectedValue === "disabled") {
      clientId.readOnly = true;
    } else if (idp === "alcia") {
      clientId.readOnly = true;
    } else {
      clientId.readOnly = false;
    }
  };

  /* web push */
  const formWebPush = document.getElementById("passkeyWebPushForm");
  if (formWebPush) {
    formWebPush.addEventListener("submit", async function (event) {
      event.preventDefault();
      const formData = new FormData(formWebPush);
      formData.append("action", "asd_push_notification_settings");
      formData.append("_wpnonce", asd_ajax.ajax_webpush_nonce);
      try {
        SwalHoldModal("Push Notification Settings.");
        const response = await fetch(asd_ajax.ajax_url, {
          method: "POST",
          body: formData,
        });
        const result = await response.json();
        if (result.success) {
          setTimeout(() => {
            Swal.update({
              icon: "success",
              title: "Success",
              html: result.data.message,
              showConfirmButton: false,
            });
          }, 1000);
          setTimeout(() => {
            Swal.close();
            window.location.reload(true);
          }, 2000);
        } else {
          errorModal("Error: " + (result.data.message || "Failed to save settings."));
        }
      } catch (error) {
        errorModal("AJAX request failed. Please try again.");
        console.error("Error:", error);
      }
    });
  }

  const btnCreatePublicKey = document.getElementById("btnCreatePublicKey");
  if (btnCreatePublicKey) {
    btnCreatePublicKey.addEventListener("click", async function (event) {
      try {
        SwalHoldModal("Please wait a moment while creating public key.");
        const response = await fetch(asd_ajax.ajax_url, {
          method: "POST",
          body: new URLSearchParams({
            action: "asd_push_notification_publickey",
            _wpnonce: asd_ajax.ajax_webpush_publickey_nonce,
          }),
        });

        const result = await response.json();
        if (result.success) {
          setTimeout(() => {
            Swal.update({
              icon: "success",
              title: "Success",
              html: "Public Key Created Successfull. Page Reloading..",
              showConfirmButton: false,
            });
            window.location.reload(true);
          }, 1000);
          setTimeout(() => {
            Swal.close();
          }, 2000);
        } else {
          errorModal("Error: " + (result.data.details || "Failed to save settings."));
        }
      } catch (error) {
        console.log(error);
        errorModal("AJAX request failed. Please try again.");
        console.error("Error:", error);
      }
    });
  }

  /* icon media uploader */
  let mediaUploader;
  const asd_p4ssk3y_button_icon_url = document.getElementById("asd_p4ssk3y_button_icon_url");
  if (asd_p4ssk3y_button_icon_url) {
    asd_p4ssk3y_button_icon_url.addEventListener("click", async function (e) {
      e.preventDefault();
      if (mediaUploader) {
        mediaUploader.open();
        return;
      }

      mediaUploader = wp.media({
        title: "Choose Icon",
        button: {
          text: "Use this icon",
        },
        multiple: false,
      });

      mediaUploader.on("select", function () {
        const attachment = mediaUploader.state().get("selection").first().toJSON();
        document.getElementById("asd_p4ssk3y_icon_url").value = attachment.url;
      });

      mediaUploader.open();
    });
  }
  let mediaUploader2;
  const asd_p4ssk3y_button_badge_url = document.getElementById("asd_p4ssk3y_button_badge_url");
  if (asd_p4ssk3y_button_badge_url) {
    asd_p4ssk3y_button_badge_url.addEventListener("click", async function (e) {
      e.preventDefault();

      if (mediaUploader2) {
        mediaUploader2.open();
        return;
      }

      mediaUploader2 = wp.media({
        title: "Choose Icon",
        button: {
          text: "Use this icon",
        },
        multiple: false,
      });

      mediaUploader2.on("select", function () {
        const attachment = mediaUploader2.state().get("selection").first().toJSON();
        document.getElementById("asd_p4ssk3y_badge_url").value = attachment.url;
      });
      mediaUploader2.open();
    });
  }
});
