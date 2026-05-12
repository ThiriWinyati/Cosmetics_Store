<?php
session_start();
require_once "../db_connect.php";

// Ensure the user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    echo "<script>alert('Please log in to update your cart.');</script>";
    echo "<script>window.location.href = 'user_login.php';</script>";
    exit();
}

// Update cart quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $productId = intval($_POST['product_id']);
    $newQuantity = intval($_POST['quantity']);

    if ($newQuantity > 0) {
        // Update the quantity in the shopping_cart table using Cart_ID
        try {
            $stmt = $conn->prepare("UPDATE shopping_cart SET Quantity = ? WHERE Cart_ID = ? AND Product_ID = ?");
            $stmt->execute([$newQuantity, $_SESSION['cart'][$productId]['cart_id'], $productId]);
            echo "Quantity updated successfully!";
        } catch (PDOException $e) {
            echo "Error updating cart: " . $e->getMessage();
        }
    } else {
        echo "Invalid quantity.";
    }
}
?>
