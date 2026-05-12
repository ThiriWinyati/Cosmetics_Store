function deleteReview(reviewId) {
  if (confirm("Are you sure you want to delete this review?")) {
    $.ajax({
      url: "deleteReview.php",
      type: "POST",
      data: { review_id: reviewId },
      success: function (response) {
        alert("Review deleted successfully.");
        location.reload(); // Refresh the page to see the changes
      },
      error: function () {
        alert("Error deleting review. Please try again.");
      },
    });
  }
}
