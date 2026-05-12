<?php
require_once "../db_connect.php";

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = $_SESSION['customer_id'];

    $stmt = $conn->prepare("SELECT p.Name AS product_name, c.Quantity, p.Price FROM shopping_cart c JOIN products p ON c.Product_ID = p.Product_ID WHERE c.Customer_ID = ?");
    $stmt->execute([$customerId]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($cartItems);
}
?>
