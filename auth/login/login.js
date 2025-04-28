$(document).ready(function () {
  const toast = $("#toastContainer");
  const toastMessage = $("#toastMessage");

  $("#loginForm").on("submit", function (e) {
    e.preventDefault();

    toastMessage.text("Logging user...");
    toast.removeClass("hidden");

    let formData = new FormData(this);

    $.ajax({
      url: "../../api/login_user.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        toastMessage.text("You login successfully!");
        if (typeof response === "string") {
          response = JSON.parse(response);
        }

        // console.log(JSON.stringify(response.payload, null, 2));
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
});
