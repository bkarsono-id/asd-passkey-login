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
});
