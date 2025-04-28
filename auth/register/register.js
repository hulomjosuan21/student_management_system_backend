$(document).ready(function () {
  const toast = $("#toastContainer");
  const toastMessage = $("#toastMessage");

  $("#verifyForm").on("submit", function (e) {
    e.preventDefault();

    toastMessage.text("Verifyng user...");
    toast.removeClass("hidden");

    let urlParams = new URLSearchParams(window.location.search);
    let user_id = urlParams.get("user_id");

    let formData = new FormData(this);

    if (user_id) {
      formData.append("user_id", user_id);
    }

    $.ajax({
      url: "../../api/verify.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (typeof response === "string") {
          response = JSON.parse(response);
        }
        toastMessage.text("You verified successfully!");

        if (response.payload.role === "admin") {
          setTimeout(() => {
            window.location.href = "../../admin/dashboard";
          }, 500);
        } else {
          setTimeout(() => {
            window.location.href = "/hulom_final_sia";
          }, 500);
        }
      },
      error: function (xhr) {
        let message = "Something went wrong. Please try again.";

        try {
          let response = JSON.parse(xhr.responseText);
          if (response.message) {
            message = response.message;
          }
        } catch {}

        toastMessage.text(message);
      },
    });

    setTimeout(() => {
      toast.addClass("hidden");
    }, 3000);
  });

  $("#registerForm").on("submit", function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    toastMessage.text("Registering...");
    toast.removeClass("hidden");

    $.ajax({
      url: "../../api/create_user.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (typeof response === "string") {
          response = JSON.parse(response);
        }

        toastMessage.text("You registered successfully!");

        setTimeout(() => {
          if (response.redirect) {
            window.location.href = response.redirect;
          }
        }, 500);
      },
      error: function (xhr, status, error) {
        toastMessage.text(error);
      },
    });

    setTimeout(() => {
      toast.addClass("hidden");
    }, 3000);
  });
});
