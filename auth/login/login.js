$(document).ready(function () {
  const toast = $("#toastContainer");
  const toastMessage = $("#toastMessage");

  $("#loginForm").on("submit", function (e) {
    e.preventDefault();

    toastMessage.text("Logging user...");
    toast.removeClass("hidden");

    let formData = new FormData(this);
    for (let [key, value] of formData.entries()) {
      console.log(`${key}: ${value}`);
    }

    $.ajax({
      url: "../../api/login_user.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (typeof response === "string") {
          response = JSON.parse(response);
        }

        toastMessage.text("You login successfully!");

        // setTimeout(() => {
        //   if (response.redirect) {
        //     window.location.href = response.redirect;
        //   }
        // }, 1500);
      },
      error: function (xhr, status, error) {
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
