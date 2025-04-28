$(document).ready(function () {
  function checkSession() {
    $.get("./api/check_session.php", function (response) {
      if (typeof response === "string") {
        response = JSON.parse(response);
      }

      console.log(JSON.stringify(response.payload, null, 2));
      if (response.payload.role !== "user") {
        window.location.href = "./auth/login";
      }
    }).fail(function () {
      window.location.href = "./auth/login";
    });
  }
  checkSession();

  $("#logoutBtn").click(function () {
    logout("./api/logout.php");
  });
});
