$(document).ready(function () {
  function checkSession() {
    $.get("../api/check_session.php", function (response) {
      if (typeof response === "string") {
        response = JSON.parse(response);
      }

      console.log(JSON.stringify(response.payload, null, 2));
    }).fail(function () {
      window.location.href = "/hulom_final_sia/auth/login";
    });
  }
  checkSession();
});
