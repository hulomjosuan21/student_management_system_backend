const profileBaseUrl = "http://localhost/hulom_final_sia/profiles/";

function calculateAge(birthdate) {
  const birthDate = new Date(birthdate);
  const today = new Date();
  let age = today.getFullYear() - birthDate.getFullYear();
  const monthDifference = today.getMonth() - birthDate.getMonth();
  if (
    monthDifference < 0 ||
    (monthDifference === 0 && today.getDate() < birthDate.getDate())
  ) {
    age--;
  }
  return age;
}

$(document).ready(function () {
  function checkSession() {
    $.get("../../api/check_session.php", function (response) {
      if (typeof response === "string") {
        response = JSON.parse(response);
      }

      const current_user = response.payload;
      console.log(JSON.stringify(current_user, null, 2));

      const profileUrl = current_user.profile_url
        ? `${profileBaseUrl}${current_user.profile_url}`
        : `${profileBaseUrl}default-image.png`;

      $("#nav-avatar-image").attr("src", profileUrl);
      $("#nav-profile-name").text(current_user.first_name);

      if (response.payload.role !== "admin") {
        window.location.href = "/hulom_final_sia/auth/login";
      }
    }).fail(function () {
      window.location.href = "/hulom_final_sia/auth/login";
    });
  }
  checkSession();

  async function initializeDashboard() {
    try {
      const response = await $.get("../../api/dashboard_analytics.php");

      const data =
        typeof response === "string" ? JSON.parse(response) : response;

      $("#total-user").text(data.payload.user_count);
      $("#total-verified").text(data.payload.verified_user_count);
      $("#count-session").text(data.payload.session_count);
      const systemHealth = data.payload.system_health;

      if (systemHealth.disk_usage === "Normal") {
        $("#system-health").html(
          `<i class="fa-regular fa-circle-check text-green-300"></i> ${systemHealth.disk_usage}`
        );
      } else {
        $("#system-health").html(
          `<i class="fa-solid fa-triangle-exclamation text-red-300"></i> ${systemHealth.disk_usage}`
        );
      }
    } catch (error) {
      $("#system-health").html(
        `<i class="fa-solid fa-triangle-exclamation text-red-300"></i> ${error.message}`
      );
    }
  }
  initializeDashboard();

  const rowTemplate = $("#row-template");
  const tableBody = $("#table-body");

  function fetchUsers(search = "", sortBy = "user_id", order = "DESC") {
    $.get(
      `../../api/get_all_user.php?search=${encodeURIComponent(
        search
      )}&sort_by=${sortBy}&order=${order}`,
      function (response) {
        const users = response.payload;
        tableBody.empty();
        $("#total-students").text(users.length);

        users.forEach((user) => {
          const clone = $(rowTemplate.get(0).content).clone();

          clone.find(".row-index").text(user.user_id);
          const fullName = `${user.first_name} ${user.last_name}`;
          clone.find(".row-fullname").text(fullName);

          const avatarImg = clone.find(".row-avatar");
          const profileUrl = user.profile_url
            ? `${profileBaseUrl}${user.profile_url}`
            : `${profileBaseUrl}default-image.png`;
          avatarImg.attr("src", profileUrl);

          clone.find(".row-email").text(user.email);
          clone.find(".row-phone").text(user.phone_number);
          clone.find(".row-gender").text(user.gender);
          clone.find(".row-course").text(user.course);
          clone.find(".row-address").text(user.address);
          clone.find(".row-age").text(calculateAge(user.birthdate));

          const statusBadge = clone.find(".row-status .badge");
          const isVerified = user.is_verified === 1 || user.is_verified === "1";
          statusBadge
            .text(isVerified ? "Verified" : "Not Verified")
            .removeClass("badge-success badge-error")
            .addClass(isVerified ? "badge-success" : "badge-error");

          tableBody.append(clone);
        });
      }
    );
  }

  fetchUsers();

  $("#logoutButton").click(function () {
    const confirmation = confirm("Are you sure you want to logout?");
    if (confirmation) {
      logout("../../api/logout.php");
    }
  });

  $("#searchForm").on("submit", function (e) {
    e.preventDefault();
    const search = $("input[name='search']").val();
    const selectedSort = $("#sortSelect").val();

    const [sortBy, order] = selectedSort
      ? selectedSort.split("_")
      : ["first_name", "ASC"];

    fetchUsers(search, sortBy, order);
  });
});
