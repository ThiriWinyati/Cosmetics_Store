$(document).ready(function () {
  $("#category").on("change", function () {
    var selectedCategory = $(this).val();
    var categoriesToHideShades = [10, 11, 12, 13, 14, 15, 17]; // IDs of categories where shades are not required

    if (categoriesToHideShades.includes(parseInt(selectedCategory))) {
      $("#shadeCountDiv").hide();
      $("#shadeInputsDiv").hide();

      // Show image inputs
      $("#imageCountDiv").show();
      $("#imageInputsDiv").show();
    } else {
      $("#shadeCountDiv").show();
      $("#shadeInputsDiv").show();

      // Hide image inputs
      $("#imageCountDiv").hide();
      $("#imageInputsDiv").hide();
    }
  });
});
