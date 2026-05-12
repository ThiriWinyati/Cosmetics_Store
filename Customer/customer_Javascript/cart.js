// Use AJAX to dynamically update the cart dropdown
$("#cart").on("click", function () {
  $.ajax({
    url: "get_cart.php", // Create a new PHP file to return cart data
    type: "GET",
    success: function (response) {
      $("#cartDropdown").html(response);
    },
  });
});

// Increase Quantity
document.querySelectorAll(".increase").forEach((button) => {
  button.addEventListener("click", function () {
    let index = this.getAttribute("data-index");
    let quantityInput = document.querySelectorAll(".quantity-input")[index];
    let newQuantity = parseInt(quantityInput.value) + 1;
    quantityInput.value = newQuantity;

    // AJAX to update cart in session and database
    updateCart(index, newQuantity);
  });
});

// Decrease Quantity
document.querySelectorAll(".decrease").forEach((button) => {
  button.addEventListener("click", function () {
    let index = this.getAttribute("data-index");
    let quantityInput = document.querySelectorAll(".quantity-input")[index];
    let newQuantity = parseInt(quantityInput.value) - 1;
    if (newQuantity > 0) {
      quantityInput.value = newQuantity;

      // AJAX to update cart in session and database
      updateCart(index, newQuantity);
    }
  });
});

// Remove Item from Cart
document.querySelectorAll(".remove-btn").forEach((button) => {
  button.addEventListener("click", function () {
    let index = this.getAttribute("data-index");

    // AJAX to remove item from cart in session and database
    removeFromCart(index);
  });
});

// Update Cart in Session and Database
function updateCart(index, quantity) {
  $.ajax({
    url: "update_cart.php", // PHP file to update cart
    type: "POST",
    data: { index: index, quantity: quantity },
    success: function (response) {
      // Optionally, you can refresh the dropdown or the entire cart
      location.reload();
    },
  });
}

// Remove Item from Cart in Session and Database
function removeFromCart(index) {
  $.ajax({
    url: "remove_from_cart.php", // PHP file to remove item from cart
    type: "POST",
    data: { index: index },
    success: function (response) {
      // Optionally, you can refresh the dropdown or the entire cart
      location.reload();
    },
  });
}
