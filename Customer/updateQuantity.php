<?php
session_start();
require_once "db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $index = $_POST['index']; // Index of the item in the cart
    $quantity = $_POST['quantity']; // New quantity

    // Update the quantity in the session
    if (isset($_SESSION['cart'][$index])) {
        $_SESSION['cart'][$index]['quantity'] = $quantity;

        // Update the quantity in the database (if needed)
        $productId = $_SESSION['cart'][$index]['product_id'];
        $customerId = $_SESSION['customer_id'];

        $stmt = $conn->prepare("UPDATE shopping_cart SET Quantity = ? WHERE Product_ID = ? AND Customer_ID = ?");
        $stmt->execute([$quantity, $productId, $customerId]);

        echo json_encode(['success' => true , 'message' => 'Quantity updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found in the cart.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>