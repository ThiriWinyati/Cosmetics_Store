<?php
session_start();
require_once "../db_connect.php";

// Ensure the user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

// Fetch cart items from the session
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cartItems = $_SESSION['cart'];
    $response = [];

    foreach ($cartItems as $item) {
        $response[] = [
            'product_name' => htmlspecialchars($item['product_name']),
            'quantity' => $item['quantity'],
            'price' => number_format($item['price'] * $item['quantity'], 2),
            'image_path' => htmlspecialchars($item['image_path']),
        ];
    }

    echo json_encode($response);
} else {
    echo json_encode(['message' => 'Cart is empty']);
}
