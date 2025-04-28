$(document).ready(function () {
  function checkSession() {
    $.get("../../api/check_session.php", function (response) {
      if (typeof response === "string") {
        response = JSON.parse(response);
      }

      console.log(JSON.stringify(response.payload, null, 2));
      if (response.payload.role !== "admin") {
        window.location.href = "/hulom_final_sia/auth/login";
      }
    }).fail(function () {
      window.location.href = "/hulom_final_sia/auth/login";
    });
  }
  checkSession();

  $("#logoutButton").click(function () {
    const confirmation = confirm("Are you sure you want to logout?");
    if (confirmation) {
      logout("../../api/logout.php");
    }
  });
});
