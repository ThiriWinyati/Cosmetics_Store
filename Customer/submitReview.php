<?php
// submit_review.php
session_start();
$customer_id = $_SESSION['customer_id'];
$product_id = $_POST['product_id'];
$review_text = $_POST['review_text'];
$rating = $_POST['rating'];

// Database connection
$pdo = new PDO("mysql:host=localhost;dbname=cosmetics_store", "root", "");

// Insert the review into the database
$insert_stmt = $pdo->prepare("INSERT INTO reviews (Product_ID, Customer_ID, Rating, Review_Text)
                              VALUES (:product_id, :customer_id, :rating, :review_text)");
$insert_stmt->execute([
    'product_id' => $product_id,
    'customer_id' => $customer_id,
    'rating' => $rating,
    'review_text' => $review_text,
    'review_date' => $review_date
]);

// Redirect back to the product details page
header("Location: viewDetails.php?product_id=$product_id");
exit();
