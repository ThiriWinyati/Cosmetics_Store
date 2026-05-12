<?php
session_start();
require_once "../db_connect.php";

// Ensure the user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    echo "<script>alert('Please log in to add products to the cart.');</script>";
    echo "<script>window.location.href = 'user_login.php';</script>";
    exit();
}

// Handle adding a product to the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $customerId = $_SESSION['customer_id'];

    try {
        // Step 1: Check if the product already exists in the shopping cart
        $stmt = $conn->prepare("SELECT Cart_ID, Quantity FROM shopping_cart WHERE Customer_ID = ? AND Product_ID = ?");
        $stmt->execute([$customerId, $productId]);
        $existingCartItem = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingCartItem) {
            // If product exists, update the quantity
            $newQuantity = $existingCartItem['Quantity'] + $quantity;
            $updateStmt = $conn->prepare("UPDATE shopping_cart SET Quantity = ? WHERE Cart_ID = ?");
            $updateStmt->execute([$newQuantity, $existingCartItem['Cart_ID']]);

            // Update the session cart as well
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['cart_id'] == $existingCartItem['Cart_ID']) {
                    $item['quantity'] = $newQuantity;
                    break;
                }
            }

            echo "<script>alert('Quantity updated successfully!');</script>";
        } else {
            // Step 2: If the product doesn't exist, insert a new row in the shopping cart
            $stmt = $conn->prepare("INSERT INTO shopping_cart (Customer_ID, Product_ID, Quantity) VALUES (?, ?, ?)");
            $stmt->execute([$customerId, $productId, $quantity]);

            // Add the new item to the session cart
            $_SESSION['cart'][] = [
                'cart_id' => $conn->lastInsertId(),
                'product_name' => $_POST['product_name'],
                'price' => $_POST['price'],
                'quantity' => $quantity,
                'image_path' => $_POST['image_path'],
            ];

            echo "<script>alert('Product added to cart successfully!');</script>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Redirect back to the previous page or cart page
header("Location: cart.php");
exit();
