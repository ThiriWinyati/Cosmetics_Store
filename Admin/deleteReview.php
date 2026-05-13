<?php
session_start();
require_once "../db_connect.php";
require_once "admin_auth.php";

admin_require_login('viewReviews.php');

// Check if review_id is provided
if (isset($_POST['review_id'])) {
    $review_id = $_POST['review_id'];

    // Delete the review from the database
    try {
        $deleteQuery = "DELETE FROM reviews WHERE Review_ID = :review_id";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bindParam(':review_id', $review_id, PDO::PARAM_INT);
        $deleteStmt->execute();

        // Redirect back to the reviews page
        echo "<script>alert('Review deleted successfully.');</script>";
        echo "<script>window.location.href = 'viewReviews.php';</script>";
    } catch (PDOException $e) {
        die("Error deleting review: " . $e->getMessage());
    }
} else {
    echo "<script>alert('Invalid request.');</script>";
    echo "<script>window.location.href = 'viewReviews.php';</script>";
}
