const profileBaseUrl = "http://localhost/hulom_final_sia/profiles/";

$(document).ready(function () {
  function checkSession() {
    $.get("./api/check_session.php", function (response) {
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

      getAllTaskBy(current_user.user_id, "pending", populatePendingTable);
      if (response.payload.role !== "user") {
        window.location.href = "./auth/login";
      }
    }).fail(function () {
      window.location.href = "./auth/login";
    });
  }
  checkSession();

  async function getAllTaskBy(user_id, task_status, populate) {
    try {
      const response = await $.get(
        `./api/get_user_task.php?user_id=${user_id}&task_status=${task_status}`
      );

      const data =
        typeof response === "string" ? JSON.parse(response) : response;

      console.log(`Response data ${JSON.stringify(data.payload, null, 2)}`);
      populate(data.payload);
    } catch (error) {
      let message = "Something went wrong. Please try again.";
      if (error?.responseText) {
        try {
          const response = JSON.parse(error.responseText);
          if (response.message) message = response.message;
        } catch {}
      }
      console.error("Error:", message);
    }
  }

  async function populatePendingTable(taskList) {
    try {
      const tbody = $("#pending-table-body");
      const template = $("#pending-row-template");
      tbody.empty();

      if (!taskList || taskList.length === 0) {
        tbody.append(`
        <tr>
          <td colspan="4" class="text-center text-gray-500 py-4">
            No pending assigned task found.
          </td>
        </tr>
      `);
        return;
      }

      taskList.forEach((task) => {
        const newRow = template.contents().clone();

        newRow.find(".row-title").text(task.title);
        newRow.find(".row-description").text(task.description);
        newRow.find(".row-deadline").text(task.due_date);

        const statusBadge = newRow.find(".row-status .badge");
        const isComplete = task.task_status == "complete";
        statusBadge
          .text(isComplete ? "Complete" : "Pending")
          .removeClass("badge-success badge-error")
          .addClass(isComplete ? "badge-success" : "badge-error");

        newRow.find(".row-action .btn").click(function () {
          console.log(`Assisnged Task id ${task.assigned_task_id}`);
        });

        tbody.append(newRow);
      });
    } catch (error) {
      console.error("Error populating assigned students:", error);
    }
  }

  $("#logoutBtn").click(function () {
    logout("./api/logout.php");
  });

  $("#avatar").click(function () {
    $("#popover-1").toggle();
  });
});
