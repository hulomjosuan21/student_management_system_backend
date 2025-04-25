$(document).ready(function () {
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

    document.getElementById("digitalClock").textContent = fullDateTime;
  }

  setInterval(updateClock, 1000);
  updateClock();
});
