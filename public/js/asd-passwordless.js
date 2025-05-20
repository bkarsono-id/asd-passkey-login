if (typeof toastr !== "undefined") {
  toastr.options = {
    closeButton: true,
    debug: false,
    newestOnTop: true,
    progressBar: true,
    positionClass: "toast-top-center",
    preventDuplicates: true,
    onclick: null,
    showDuration: "300",
    hideDuration: "1000",
    timeOut: "5000",
    extendedTimeOut: "1000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut",
  };
}
/**
 * Displays a success toast message.
 *
 * @param {string} message The message to display.
 * @returns {void}
 */
function showSuccessMessage(message) {
  toastr.success(message, "Success");
}

/**
 * Displays an error toast message.
 *
 * @param {string} message The message to display.
 * @returns {void}
 */
function showErrorMessage(message) {
  toastr.error(message, "Error");
}

/**
 * Checks if the current device is a mobile device.
 *
 * @returns {boolean}
 */
function isMobile() {
  return /Mobi|Android|iPhone|iPad|iPod|Opera Mini|IEMobile|WPDesktop/.test(navigator.userAgent);
}

/**
 * Shows a SweetAlert modal indicating a process is in progress.
 *
 * @param {string} [msg] The message to display.
 * @returns {void}
 */
function SwalHoldModal(
  msg = "Please hold on for a moment while we check for biometric data. The authenticator will be displayed shortly."
) {
  Swal.fire({
    title: "In Progress",
    html: msg,
    icon: "info",
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    showCloseButton: false,
    willOpen: () => {
      Swal.showLoading();
    },
  });
}

/**
 * Shows a SweetAlert modal for success feedback.
 *
 * @param {string} [msg] The message to display.
 * @param {string} [icon] The icon to display.
 * @returns {void}
 */
function successModal(msg = "Success.", icon = "success") {
  setTimeout(() => {
    Swal.update({
      icon: "success",
      title: "Success",
      html: msg,
      showConfirmButton: false,
    });
  }, 1000);
  setTimeout(() => {
    Swal.close();
  }, 2000);
}

/**
 * Shows a SweetAlert modal for error feedback.
 *
 * @param {string|Array} msg The error message(s) to display.
 * @returns {void}
 */
function errorModal(msg) {
  if (Array.isArray(msg)) {
    const errorList = $("<ul></ul>");
    $.each(msg, function (key, value) {
      const $li = $("<li></li>").text(value);
      errorList.append($li);
    });
    msg = errorList.prop("outerHTML");
  }
  if (Swal.isVisible()) {
    setTimeout(() => {
      Swal.hideLoading();
      Swal.update({
        title: "Oops...",
        html: msg,
        icon: "error",
        showConfirmButton: true,
      });
    }, 1000);
  } else {
    setTimeout(() => {
      Swal.hideLoading();
      Swal.fire({
        icon: "error",
        title: "Oops...",
        html: msg,
        willOpen: () => {
          Swal.hideLoading();
        },
        showClass: {
          popup: "animate__animated animate__fadeIn",
        },
        hideClass: {
          popup: "animate__animated animate__fadeOut",
        },
      });
    }, 1000);
  }
}

/**
 * Shows a SweetAlert loader modal.
 *
 * @param {string} [msg] The message to display.
 * @param {string} [icon] The icon to display.
 * @returns {void}
 */
function swalLoader(msg = "Please wait a moment.", icon = "info") {
  if (Swal.isVisible()) {
    setTimeout(() => {
      Swal.update({
        title: "In Progress",
        html: msg,
        icon: icon,
      });
    }, 1000);
  } else {
    Swal.fire({
      title: "In Progress",
      html: msg,
      icon: icon,
      allowOutsideClick: false,
      allowEscapeKey: false,
      showConfirmButton: false,
      willOpen: () => {
        Swal.showLoading();
      },
      showClass: {
        popup: "animate__animated animate__fadeIn",
      },
      hideClass: {
        popup: "animate__animated animate__fadeOut",
      },
    });
  }
}

/**
 * Shows a SweetAlert confirmation modal.
 *
 * @param {string} [title] The title of the modal.
 * @param {string} [msg] The message to display.
 * @returns {Promise<SweetAlertResult>}
 */

function swalConfirm(title = "Confirmation", msg = "Please wait a moment.") {
  return Swal.fire({
    title: title,
    html: msg,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes",
  });
}

/**
 * Hides all message boxes.
 *
 * @returns {void}
 */
function cleanMesageBox() {
  errorMessage.style.display = "none";
  infoMessage.style.display = "none";
}

/**
 * Displays an info message box.
 *
 * @param {string} [message] The message to display.
 * @returns {void}
 */
function infoMessageBox(message = "") {
  cleanMesageBox();
  infoMessage.textContent = message;
  infoMessage.style.display = "block";
}

/**
 * Displays an error message box.
 *
 * @param {string} [message] The message to display.
 * @returns {void}
 */
function errorMessageBox(message = "") {
  cleanMesageBox();
  errorMessage.textContent = message;
  errorMessage.style.display = "block";
}

/**
 * Shows the loading spinner and hides the button text.
 *
 * @returns {void}
 */
function spinnerOn() {
  spinnerText.style.display = "inline-block";
  spinnerText.textContent = "Loading..";
  buttonText.style.display = "none";
}

/**
 * Hides the loading spinner and shows the button text.
 *
 * @returns {void}
 */
function spinnerOff() {
  spinnerText.style.display = "none";

  buttonText.style.display = "inline-block";
  buttonText.textContent = "Login via Passkey";
}
