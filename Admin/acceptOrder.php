<?php
session_start();
require_once "../db_connect.php";

// Database credentials
$servername = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$database = getenv('DB_NAME');
$port = getenv('DB_PORT') ?: 3306;

// Create connection
try {
    $conn = new PDO(
        "mysql:host=$servername;port=$port;dbname=$database;charset=utf8mb4",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if the user is an admin
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    // If not logged in, redirect to login page
    echo "<script>alert('Please log in as an admin.');</script>";
    echo "<script>window.location.href = 'adminLogin.php';</script>";
    exit();
}

// Check if order_id is passed
if (isset($_GET['order_id'])) {
    $orderID = $_GET['order_id'];

    try {
        // Step 1: Update order status to 'Accepted'
        $updateStatusQuery = "UPDATE orders SET Status = 'Accepted' WHERE Order_ID = ?";
        $updateStatusStmt = $conn->prepare($updateStatusQuery);
        $updateStatusStmt->execute([$orderID]);

        // Step 2: Update shipping status to 'Pending' for the existing order
        $updateShippingStatusQuery = "UPDATE shipping SET Shipping_Status = 'Processing', Shipping_Date = NOW() WHERE Order_ID = ?";
        $updateShippingStatusStmt = $conn->prepare($updateShippingStatusQuery);
        $updateShippingStatusStmt->execute([$orderID]);

        echo "<script>alert('Order accepted successfully and shipping status updated to Processing.');</script>";

        // Redirect to order management page
        echo "<script>window.location.href = 'orderManage.php';</script>";
    } catch (PDOException $e) {
        die("Error updating order status or shipping status: " . $e->getMessage());
    }
} else {
    echo "<script>alert('No order selected.');</script>";
    echo "<script>window.location.href = 'orderManage.php';</script>";
}
