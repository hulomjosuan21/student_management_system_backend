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
            ? `../../profiles/${user.profile_url}`
            : "https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp";
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

  $("#searchForm").on("submit", function (e) {
    e.preventDefault();
    const search = $("input[name='search']").val();
    const selectedSort = $("#sortSelect").val();

    const [sortBy, order] = selectedSort
      ? selectedSort.split("_")
      : ["first_name", "ASC"];

    fetchUsers(search, sortBy, order);
  });

  $("#avatar").click(function () {
    $("#popover-1").toggle();
  });
});
