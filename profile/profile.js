const profileBaseUrl = "http://localhost/hulom_final_sia/profiles/";
$(document).ready(function () {
  let currentUserId = null;

  function checkSession() {
    $.get("../api/check_session.php", function (response) {
      try {
        if (typeof response === "string") {
          response = JSON.parse(response);
        }

        if (response.payload) {
          // console.log(JSON.stringify(response.payload, null, 2));
          setUserProfileUI(response.payload);
          currentUserId = response.payload.user_id;
        } else {
          window.location.href = "/hulom_final_sia/auth/login";
        }
      } catch (e) {
        console.error("Failed to parse JSON:", e);
        window.location.href = "/hulom_final_sia/auth/login";
      }
    }).fail(function () {
      console.error("AJAX request failed");
      window.location.href = "/hulom_final_sia/auth/login";
    });
  }

  function setUserProfileUI(user) {
    const profileImage =
      profileBaseUrl + (user.profile_url || "default-image.png");

    $("img[alt='Profile Image']").attr("src", profileImage);

    $(".profile-name").text(user.first_name + " " + user.last_name);

    $(".profile-role").html(
      `${capitalize(user.role)} | ${
        user.is_verified ? "Verified ✅" : "Unverified ❌ "
      }`
    );

    $(".email").html("<span class='font-semibold'>Email:</span> " + user.email);
    $(".phone").html(
      "<span class='font-semibold'>Phone:</span> " + user.phone_number
    );
    $(".gender").html(
      "<span class='font-semibold'>Gender:</span> " + user.gender
    );
    $(".course").html(
      "<span class='font-semibold'>Course:</span> " + user.course
    );
    $(".address").html(
      "<span class='font-semibold'>Address:</span> " + user.address
    );
    $(".birthdate").html(
      "<span class='font-semibold'>Birthdate:</span> " + user.birthdate
    );
    $(".created-at").html(
      "<span class='font-semibold'>User Since:</span> " +
        user.created_at.split(" ")[0]
    );
  }

  function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  }

  checkSession();

  $("#editProfileForm").on("submit", async function (e) {
    try {
      e.preventDefault();
      const formData = new FormData(this);
      formData.append("user_id", currentUserId);

      for (const [key, value] of formData.entries()) {
        console.log(key, value);
      }

      const response = await $.ajax({
        url: "../api/update_user.php",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
      });

      const data =
        typeof response === "string" ? JSON.parse(response) : response;

      console.log(JSON.stringify(data.payload, null, 2));
      setUserProfileUI(data.payload);
      document.getElementById("editProfileModal").close();
    } catch (error) {
      console.error("Error in form submission:", error);
    }
  });

  $("#deleteAccountButton").click(function () {
    if (window.confirm("Are you sure you want to delete your account?")) {
      console.log("Delete account button clicked");
    }
  });

  $("#logoutButton").click(function () {
    const confirmation = confirm("Are you sure you want to logout?");
    if (confirmation) {
      logout("../api/logout.php");
    }
  });
});
