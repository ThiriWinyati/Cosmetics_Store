<?php



session_start();
require_once "../db_connect.php"; // Ensure database connection

// Ensure the user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    echo "<script>alert('Please log in to remove items from your cart.');</script>";
    echo "<script>window.location.href = 'user_login.php';</script>";
    exit();
}

// Check if Cart_ID is received
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $cartIdToRemove = intval($_POST['cart_id']); // Get the Cart_ID from the form


    // Remove product from session
    $productFoundInSession = false;
    foreach ($_SESSION['cart'] as $index => $item) {
        if ($item['cart_id'] == $cartIdToRemove) {
            // Item found in session, remove it
            unset($_SESSION['cart'][$index]); // Remove from session
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index the session array
            $productFoundInSession = true;
            break;
        }
    }

    if ($productFoundInSession) {
        try {
            // Remove the product from the database
            $stmt = $conn->prepare("DELETE FROM shopping_cart WHERE Cart_ID = ?");
            $stmt->execute([$cartIdToRemove]);

            // After successful deletion, redirect to cart page
            echo "<script>alert('Product removed successfully.'); window.location.href = 'cart.php';</script>";
        } catch (PDOException $e) {
            // Handle any errors with the database connection or query execution
            echo "<script>alert('Error removing product from cart: " . $e->getMessage() . "'); window.location.href = 'cart.php';</script>";
        }
    } else {
        echo "<script>alert('Product not found in session.'); window.location.href = 'cart.php';</script>";
    }
} else {
    echo "<script>alert('No product selected for removal.'); window.location.href = 'cart.php';</script>";
}
?>
