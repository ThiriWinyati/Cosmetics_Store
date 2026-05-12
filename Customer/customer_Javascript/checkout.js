function updateTotalPrice() {
  var shippingCost = parseFloat(
    document
      .getElementById("shipping_method")
      .selectedOptions[0].getAttribute("data-cost")
  );
  var totalPrice = parseFloat(document.getElementById("original_total").value);
  var discount = parseFloat(document.getElementById("discount").value) || 0;
  totalPrice -= discount;
  totalPrice += shippingCost;
  document.getElementById("final_total").textContent =
    "Total Amount: $" + totalPrice.toFixed(2);
  document.getElementById("total_amount").value = totalPrice;
}

function handleCouponApplication() {
  updateTotalPrice();
}

function toggleAddressField() {
  var shippingMethodId = document.getElementById("shipping_method").value;
  var addressField = document.getElementById("address_field");
  var shippingAddressVisible = document.querySelector(
    "input[name='shipping_address_visible']"
  );
  var shippingAddressHidden = document.getElementById(
    "shipping_address_hidden"
  );

  if (shippingMethodId === "7") {
    addressField.style.display = "none";
    shippingAddressHidden.value = ""; // Clear the hidden field for "Pickup"
  } else {
    addressField.style.display = "block";
    // Sync the visible field value with the hidden field
    shippingAddressVisible.addEventListener("input", function () {
      shippingAddressHidden.value = shippingAddressVisible.value;
    });
  }
}

document
  .getElementById("shipping_method")
  .addEventListener("change", updateTotalPrice); // Add this line to update total price on shipping method change
// Call toggleAddressField() when the page loads

window.onload = function () {
  // Sync the visible and hidden shipping address fields
  var shippingAddressVisible = document.querySelector(
    "input[name='shipping_address_visible']"
  );
  var shippingAddressHidden = document.getElementById(
    "shipping_address_hidden"
  );
  if (shippingAddressVisible && shippingAddressHidden) {
    shippingAddressHidden.value = shippingAddressVisible.value;
  }

  // Call toggleAddressField() to update visibility
  toggleAddressField();

  // Update the total price when the page loads
  updateTotalPrice();
};

// Add this code snippet to the existing checkout.js file
document
  .getElementById("shipping_method")
  .addEventListener("change", updateTotalPrice);

function handlePaymentMethod() {
  var paymentMethodId = document.getElementById("payment_method").value;
  var paymentDetailsSection = document.getElementById(
    "payment_details_section"
  );
  document.getElementById("card_details").style.display = "none";
  document.getElementById("paypal_details").style.display = "none";
  document.getElementById("mobile_payment_details").style.display = "none";

  if (paymentMethodId == "1") {
    paymentDetailsSection.style.display = "block";
    document.getElementById("card_details").style.display = "block";
  } else if (paymentMethodId == "2") {
    paymentDetailsSection.style.display = "block";
    document.getElementById("paypal_details").style.display = "block";
  } else if (["4", "5", "6"].includes(paymentMethodId)) {
    paymentDetailsSection.style.display = "block";
    document.getElementById("mobile_payment_details").style.display = "block";
  } else {
    paymentDetailsSection.style.display = "none";
  }
}

function handleFormSubmission() {
  // Sync the shipping address value before submitting the form
  var shippingAddressVisible = document.querySelector(
    "input[name='shipping_address_visible']"
  );
  var shippingAddressHidden = document.getElementById(
    "shipping_address_hidden"
  );
  if (shippingAddressVisible && shippingAddressHidden) {
    shippingAddressHidden.value = shippingAddressVisible.value;
  }
  return true; // Allow the form to submit
}
