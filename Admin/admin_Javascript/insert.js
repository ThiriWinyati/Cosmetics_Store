$(document).ready(function () {
  // Define allowed categories (IDs where shades are allowed)
  const allowedCategories = [7, 8, 9]; // Replace with your allowed category IDs

  // Category change event listener
  $("#category").on("change", function () {
    const selectedCategory = $(this).val();

    // Check if the selected category is in the allowed list
    if (allowedCategories.includes(parseInt(selectedCategory))) {
      // Show shade name input
      $(".shade-name").closest(".shade-input-container").show();
    } else {
      // Hide shade name input
      $(".shade-name").closest(".shade-input-container").hide();
    }
  });

  // Dynamically add shade input fields based on the count entered
  $("#shadeCount").on("input", function () {
    let count = $(this).val();
    $("#shadeInputs").empty();
    for (let i = 0; i < count; i++) {
      $("#shadeInputs").append(
        `<div class="shade-input-container d-flex mb-2">
                    <input type="text" class="form-control shade-name" name="shade_names[]" placeholder="Shade Name" style="width: 30%; margin-right:10px;">
                    <input type="text" class="form-control shade-quantity" ms-3" name="shade_quantities[]" placeholder="Quantity" required style="width: 30%; margin-right:10px;">
                    <input type="file" class="form-control shade-image ms-3" name="shade_images[]" required>
                </div>`
      );
    }

    // Hide shade name input if the selected category is not allowed
    const selectedCategory = $("#category").val();
    if (!allowedCategories.includes(parseInt(selectedCategory))) {
      $(".shade-name").closest(".shade-input-container").hide();
    }
  });
});
