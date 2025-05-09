$(document).ready(function () {
  const digitalClock = $("#digitalClock");

  function updateClock() {
    const now = new Date();

    let hours = now.getHours();
    const minutes = now.getMinutes().toString().padStart(2, "0");
    const seconds = now.getSeconds().toString().padStart(2, "0");
    const amPm = hours >= 12 ? "PM" : "AM";
    hours = hours % 12 || 12;
    hours = hours.toString().padStart(2, "0");
    const time = `${hours}:${minutes} ${amPm}`;

    const options = { month: "long", day: "numeric", year: "numeric" };
    const formattedDate = now.toLocaleDateString("en-US", options);

    const fullDateTime = `${formattedDate} ${time}`;

    digitalClock.text(fullDateTime);
  }

  if (digitalClock) {
    setInterval(updateClock, 1000);
    updateClock();
  }
});

function logout(route) {
  $.get(route, function (response) {
    if (typeof response === "string") {
      response = JSON.parse(response);
    }

    if (response.status == 200) {
      window.location.href = "/hulom_final_sia/auth/login";
    } else {
      alert("Logout failed. Please try again.");
    }
  }).fail(function () {
    alert("An error occurred during logout.");
  });
}

$("#avatar").click(function () {
  $("#popover-1").toggle();
});

function formDataToObject(formData) {
  let formObject = {};
  formData.forEach(function (value, key) {
    if (formObject[key]) {
      if (!Array.isArray(formObject[key])) {
        formObject[key] = [formObject[key]];
      }
      formObject[key].push(value);
    } else {
      formObject[key] = value;
    }
  });

  return formObject;
}

function formatDueDateWithAMPM(dueDate) {
  let dateObj = new Date(dueDate);

  let datePart = dateObj.toISOString().slice(0, 10);

  let hours = dateObj.getHours();
  let minutes = dateObj.getMinutes();
  let ampm = hours >= 12 ? "PM" : "AM";
  hours = hours % 12;
  hours = hours ? hours : 12;
  minutes = minutes < 10 ? "0" + minutes : minutes;

  let timePart = hours + ":" + minutes + " " + ampm;

  return datePart + " " + timePart;
}
