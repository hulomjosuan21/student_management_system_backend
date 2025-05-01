$(document).ready(function () {
  const toast = $("#toastContainer");
  const toastMessage = $("#toastMessage");
  const submittedFileBaseUrl = "http://localhost/hulom_final_sia/attachment/";
  const profileBaseUrl = "http://localhost/hulom_final_sia/profiles/";
  let current_user = null;
  const to_assign_user = [];

  const selectedUserIds = [];
  // session sa user na naka login
  function checkSession() {
    $.get("../../api/check_session.php", function (response) {
      if (typeof response === "string") response = JSON.parse(response);
      current_user = response.payload;

      if (current_user.role !== "admin") {
        return (window.location.href = "/hulom_final_sia/auth/login");
      }

      const profileUrl = current_user.profile_url
        ? `${profileBaseUrl}${current_user.profile_url}`
        : `${profileBaseUrl}default-image.png`;

      $("#nav-avatar-image").attr("src", profileUrl);
      $("#nav-profile-name").text(current_user.first_name);

      populateUserDropdown(to_assign_user);
      getAllTasks();
      fetchAndAppendUsers("user", to_assign_user);
    }).fail(() => {
      window.location.href = "/hulom_final_sia/auth/login";
    });
  }

  checkSession();

  // pag add sa mga students sa select
  function populateUserDropdown(users) {
    users.forEach((user) => {
      $("#user-select").append(
        `<option value="${user.assign_to}">${user.name}</option>`
      );
    });
  }

  function fetchAndAppendUsers(role, userList, callback) {
    $.get(`../../api/get_all_user_by_role.php?role=${role}`)
      .done((response) => {
        if (typeof response === "string") response = JSON.parse(response);
        response.payload.forEach((user) => {
          const fullName = `${user.first_name} ${user.last_name}`;
          const alreadyExists = userList.some(
            (u) => u.assign_to === user.user_id
          );

          if (!alreadyExists) {
            userList.push({ assign_to: user.user_id, name: fullName });
            $("#user-select").append(
              `<option value="${user.user_id}">${fullName}</option>`
            );
          }
        });
        if (callback) callback();
      })
      .fail(() => console.error("Failed to fetch users."));
  }

  // get all task created by the current user
  function getAllTasks() {
    $.get(`../../api/get_all_tasks.php?created_by=${current_user.user_id}`)
      .done((response) => populateTable(response.payload))
      .fail(() => console.error("Failed to fetch tasks."));
  }

  async function populateTable(tasks) {
    try {
      const tableBody = $("#table-body");
      const template = $("#task-template");
      tableBody.empty();

      if (tasks.length === 0) {
        tableBody.append(`
      <tr>
        <td colspan="5" class="text-center text-gray-500 py-4">
          No tasks created yet.
        </td>
      </tr>
    `);
        return;
      }

      tasks.forEach((task) => {
        const newRow = template.contents().clone();
        newRow.find(".row-id").text(task.task_id);
        newRow.find(".row-title").text(task.title);
        newRow.find(".row-description").text(task.description);
        newRow.find(".row-due_date").text(formatDueDateWithAMPM(task.due_date));

        newRow.find(".btn-warning").on("click", () => {
          $("#taskEditInput").val(task.title);
          $("#taskEditDescriptionInput").val(task.description);
          $("#taskEditDueDate").val(task.due_date);
        });

        newRow.find(".btn-error").on("click", () => {
          if (window.confirm("Are you sure you want to delete this task?")) {
            deleteTask(task.task_id);
            getAllTasks();
          }
        });

        newRow.find(".btn-info").on("click", () => {
          $("#showSubmit")[0].showModal();
          showSubmitStudent(task.task_id);
        });

        tableBody.append(newRow);
      });
    } catch (error) {
      console.error("Error populating table:", error);
    }
  }

  async function showSubmitStudent(task_id) {
    try {
      const response = await $.get(
        `../../api/check_student_submit.php?task_id=${task_id}`
      );

      const data =
        typeof response === "string" ? JSON.parse(response) : response;

      populateAssignedStudentTable(data.payload);
      return data;
    } catch (error) {
      console.error("Error fetching data:", error);
    }
  }

  async function populateAssignedStudentTable(studentList) {
    try {
      const tbody = $("#assigned-student-tbody");
      const template = $("#assigned-student-template");
      tbody.empty();

      if (!studentList || studentList.length === 0) {
        tbody.append(`
        <tr>
          <td colspan="4" class="text-center text-gray-500 py-4">
            No assigned students found.
          </td>
        </tr>
      `);
        return;
      }

      studentList.forEach((student) => {
        const newRow = template.contents().clone();

        newRow.find(".row-id").text(student.assigned_task_id);
        newRow
          .find(".row-name")
          .text(`${student.first_name} ${student.last_name}`);
        newRow
          .find(".row-url")
          .html(
            `<a href="${`${submittedFileBaseUrl}${student.attachment_url}`}" target="_blank" class="link text-primary">View</a>`
          );

        const statusBadge = newRow.find(".row-status .badge");
        const isVerified = student.task_status === "complete";

        statusBadge
          .text(isVerified ? "Complete" : "Pending")
          .removeClass("badge-success badge-error")
          .addClass(isVerified ? "badge-success" : "badge-error");

        const task_status =
          student.task_status != "complete" ? "complete" : "pending";

        newRow.find(".btn-success").on("click", () => {
          updateAssignedTaskStatus(student.assigned_task_id, task_status);
          showSubmitStudent(student.task_id);
        });

        tbody.append(newRow);
      });
    } catch (error) {
      console.error("Error populating assigned students:", error);
    }
  }

  async function updateAssignedTaskStatus(assignedTaskId, taskStatus) {
    try {
      const response = await $.ajax({
        url: "../../api/update_assigned_task_status.php",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({
          assigned_task_id: assignedTaskId,
          task_status: taskStatus,
        }),
      });

      const data =
        typeof response === "string" ? JSON.parse(response) : response;

      console.log(data.message);
    } catch (error) {
      console.error("Error populating assigned students:", error);
    }
  }

  async function deleteTask(taskId) {
    try {
      const response = await $.ajax({
        url: "../../api/delete_task.php",
        type: "DELETE",
        contentType: "application/json",
        data: JSON.stringify({ task_id: taskId }),
      });

      const parsedResponse =
        typeof response === "string" ? JSON.parse(response) : response;
      console.log(parsedResponse.message);
    } catch (error) {
      throw error;
    }
  }

  // lista sa mga users na naka assign sa task
  function addUserTag(userId, userName) {
    if (selectedUserIds.includes(userId)) return;

    selectedUserIds.push(userId);

    const template = document.getElementById("user-tag-template");
    const clone = template.content.cloneNode(true);
    const span = clone.querySelector("span");
    const nameEl = clone.querySelector(".user-name");
    const button = clone.querySelector("button");

    nameEl.textContent = userName;
    button.dataset.user = userId;

    button.addEventListener("click", () => removeUserTag(userId));
    document.getElementById("tag-container").appendChild(clone);

    const optionToRemove = document.querySelector(
      `#user-select option[value="${userId}"]`
    );
    if (optionToRemove) optionToRemove.remove();

    document.getElementById("user-select").value = "";
  }

  function clearUserTags() {
    $("#tag-container").empty();

    selectedUserIds.length = 0;

    $("#user-select")
      .empty()
      .append('<option disabled selected value="">Pick a user</option>');

    to_assign_user.forEach((user) => {
      $("#user-select").append(
        `<option value="${user.assign_to}">${user.name}</option>`
      );
    });
  }

  function removeUserTag(userId) {
    const index = selectedUserIds.indexOf(userId);
    if (index > -1) {
      selectedUserIds.splice(index, 1);
      const user = to_assign_user.find((u) => u.assign_to === userId);
      if (user) {
        $("#user-select").append(
          `<option value="${user.assign_to}">${user.name}</option>`
        );
      }
      $(`#tag-container button[data-user="${userId}"]`).parent().remove();
    }
  }

  $("#user-select").on("change", function () {
    const selectedId = parseInt($(this).val());
    const selectedUser = to_assign_user.find((u) => u.assign_to === selectedId);
    if (selectedUser) {
      addUserTag(selectedUser.assign_to, selectedUser.name);
    }
  });

  $("#addTaskForm").on("submit", async function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append("created_by", current_user.user_id);

    toastMessage.text(`Assigning new task ${formData.get("title")}...`);
    toast.removeClass("hidden");

    try {
      const response = await $.ajax({
        url: "../../api/create_task.php",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
      });

      const parsedResponse =
        typeof response === "string" ? JSON.parse(response) : response;
      toastMessage.text("Task assigned successfully!");
      const created_by = current_user.user_id;
      const task_id = parsedResponse.payload.task_id;

      await assignTask(created_by, task_id);
      getAllTasks();
      this.reset();
      clearUserTags();
    } catch (error) {
      let message = "Something went wrong. Please try again.";
      if (error?.responseText) {
        try {
          const response = JSON.parse(error.responseText);
          if (response.message) message = response.message;
        } catch {}
      }
      console.error("Error:", message);
      toastMessage.text(message);
    }

    setTimeout(() => {
      toast.addClass("hidden");
    }, 2000);
  });

  async function assignTask(created_by, task_id) {
    const selectedUsers = to_assign_user.filter((u) =>
      selectedUserIds.includes(u.assign_to)
    );

    const assignPromises = selectedUsers.map((assigned_user) => {
      const new_assigned_task = {
        created_by: created_by,
        task_id: task_id,
        assign_to: assigned_user.assign_to,
      };

      return $.ajax({
        url: "../../api/assign_task.php",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify(new_assigned_task),
      });
    });

    try {
      await Promise.all(assignPromises);
    } catch (error) {
      throw error;
    }
  }

  $("#edit_modal").on("submit", function (e) {
    e.preventDefault();
    console.log("Edit form submitted");
  });

  $("#logoutButton").click(function () {
    const confirmation = confirm("Are you sure you want to logout?");
    if (confirmation) logout("../../api/logout.php");
  });
  $("#avatar").click(function () {
    $("#popover-1").toggle();
  });
});
