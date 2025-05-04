const profileBaseUrl = "http://localhost/hulom_final_sia/profiles/";
const submittedFileBaseUrl = "http://localhost/hulom_final_sia/attachment/";
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
      getAllTaskBy(current_user.user_id, "complete", populateCompleteTable);
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

  var assigned_task_id_to_submmit = null;

  async function populatePendingTable(taskList) {
    try {
      const tbody = $("#pending-table-body");
      const template = $("#pending-row-template");
      tbody.empty();

      if (!taskList || taskList.length === 0) {
        tbody.append(`
        <tr>
          <td colspan="5" class="text-center text-gray-500 py-4">
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
        newRow.find(".row-deadline").text(formatDueDateWithAMPM(task.due_date));

        const statusBadge = newRow.find(".row-status .badge");
        const dueDate = new Date(task.due_date);
        const now = new Date();
        const isLate = now > dueDate;

        statusBadge
          .text(isLate ? "Late" : "Pending")
          .removeClass("badge-success badge-error badge-warning")
          .addClass(isLate ? "badge-warning" : "badge-error");

        newRow.find(".row-action .btn").click(function () {
          const dueDate = new Date(task.due_date);
          const now = new Date();

          if (now >= dueDate) {
            const proceed = confirm(
              "The due date has already passed. Do you still want to submit?"
            );
            if (!proceed) return;
          }
          document.getElementById("attachFileModal").showModal();
          assigned_task_id_to_submmit = task.assigned_task_id;
        });

        tbody.append(newRow);
      });
    } catch (error) {
      console.error("Error populating assigned students:", error);
    }
  }

  async function populateCompleteTable(taskList) {
    try {
      const tbody = $("#complete-table-body");
      const template = $("#complete-row-template");
      tbody.empty();

      if (!taskList || taskList.length === 0) {
        tbody.append(`
        <tr>
          <td colspan="5" class="text-center text-gray-500 py-4">
            No completed assigned task found.
          </td>
        </tr>
      `);
        return;
      }

      taskList.forEach((task) => {
        const newRow = template.contents().clone();

        newRow.find(".row-title").text(task.title);
        newRow.find(".row-description").text(task.description);
        newRow.find(".row-deadline").text(formatDueDateWithAMPM(task.due_date));

        const statusBadge = newRow.find(".row-status .badge");
        const isComplete = task.task_status == "complete";
        statusBadge
          .text(isComplete ? "Completed" : "Pending")
          .removeClass("badge-success badge-error")
          .addClass(isComplete ? "badge-success" : "badge-error");

        newRow.find(".row-action .btn").click(function () {
          populateTaskDialog(task);
          document.getElementById("viewDialog").showModal();
        });

        tbody.append(newRow);
      });
    } catch (error) {
      console.error("Error populating assigned students:", error);
    }
  }

  function isValidUrl(str) {
    try {
      new URL(str);
      return true;
    } catch (_) {
      return false;
    }
  }

  function populateTaskDialog(task) {
    $("#dialog-title").text(task.title);
    $("#dialog-description").text(task.description);
    $("#dialog-due-date").text(formatDueDateWithAMPM(task.due_date));

    const dialogStatus = $("#dialog-status");
    dialogStatus
      .text(
        task.task_status.charAt(0).toUpperCase() + task.task_status.slice(1)
      )
      .removeClass("badge-success badge-error")
      .addClass(
        task.task_status == "complete" ? "badge-success" : "badge-error"
      );

    const attachmentDisplay = $("#dialog-attachment");
    if (task.attachment_url) {
      const href = isValidUrl(task.attachment_url)
        ? task.attachment_url
        : submittedFileBaseUrl + task.attachment_url;
      attachmentDisplay.html(
        `<a href="${href}" target="_blank" class="link text-primary">View Attachment</a>`
      );
    } else {
      attachmentDisplay.text("No attachment");
    }
  }

  $("#attachFileDialog").on("submit", async function (e) {
    e.preventDefault();
    if (!assigned_task_id_to_submmit) {
      return;
    }
    const formData = new FormData(this);
    formData.append("assigned_task_id", assigned_task_id_to_submmit);
    formData.append("task_status", "submitted");
    try {
      await $.ajax({
        url: "./api/update_assigned_task_status.php",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
      });

      checkSession();
    } catch (error) {
      console.error(
        "❌ Error:",
        error.responseJSON?.message || error.statusText
      );
    } finally {
      document.getElementById("attachFileModal").close();
    }
  });

  $("#logoutBtn").click(function () {
    logout("./api/logout.php");
  });

  $("#avatar").click(function () {
    $("#popover-1").toggle();
  });
});
