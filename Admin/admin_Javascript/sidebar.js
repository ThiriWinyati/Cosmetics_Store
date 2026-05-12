function w3_open() {
  document.getElementById("main").style.marginLeft = "25%";
  document.getElementById("mySidebar").style.width = "25%";
  document.getElementById("mySidebar").style.display = "block";
  document.getElementById("openNav").style.display = "none";
}
function w3_close() {
  document.getElementById("main").style.marginLeft = "0%";
  document.getElementById("mySidebar").style.display = "none";
  document.getElementById("openNav").style.display = "inline-block";
}

// Toggle sidebar functionality
function toggleSidebar() {
  const sidebar = document.querySelector(".sidebar");
  const main = document.querySelector("#main");

  sidebar.classList.toggle("active"); // Toggle sidebar visibility
  main.classList.toggle("active"); // Adjust main content
}

// Add event listener to the toggle button
document
  .querySelector(".toggle-button")
  .addEventListener("click", toggleSidebar);

// Add event listener to the toggle button
document
  .querySelector(".toggle-button")
  .addEventListener("click", toggleSidebar);

function w3_close() {
  const sidebar = document.querySelector(".sidebar");
  const main = document.querySelector("#main");

  sidebar.classList.remove("active");
  main.classList.remove("active");
}

// Toggle dropdown visibility
function toggleDropdown(id) {
  const dropdown = document.getElementById(id);
  dropdown.style.display =
    dropdown.style.display === "block" ? "none" : "block";
}
