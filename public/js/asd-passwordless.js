// Toastr configuration
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

function showSuccessMessage(message) {
  toastr.success(message, "Success");
}

function showErrorMessage(message) {
  toastr.error(message, "Error");
}
function isMobile() {
  return /Mobi|Android|iPhone|iPad|iPod|Opera Mini|IEMobile|WPDesktop/.test(navigator.userAgent);
}
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
function swalConfirm(title = "Confirmation", msg = "Please wait a moment.") {
  return Swal.fire({
    title: title,
    html: msg,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes",
  });
}

function cleanMesageBox() {
  errorMessage.style.display = "none";
  infoMessage.style.display = "none";
}
function infoMessageBox(message = "") {
  cleanMesageBox();
  infoMessage.textContent = message;
  infoMessage.style.display = "block";
}
function errorMessageBox(message = "") {
  cleanMesageBox();
  errorMessage.textContent = message;
  errorMessage.style.display = "block";
}
function spinnerOn() {
  spinnerText.style.display = "inline-block";
  spinnerText.textContent = "Loading..";
  buttonText.style.display = "none";
}
function spinnerOff() {
  spinnerText.style.display = "none";

  buttonText.style.display = "inline-block";
  buttonText.textContent = "Login via Passkey";
}
