<?php
session_start();
require_once "../db_connect.php";

// Database credentials
$server = getenv('DB_HOST');
$user = getenv('DB_USER');
$password = getenv('DB_PASS');
$database = getenv('DB_NAME');
$port = getenv('DB_PORT') ?: 3306;

// Create connection
try {
    $conn = new PDO(
        "mysql:host=$server;port=$port;dbname=$database;charset=utf8mb4",
        $user,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    // If not logged in, redirect to login page
    echo "<script>alert('Please log in as an admin.');</script>";
    echo "<script>window.location.href = 'adminLogin.php';</script>";
    exit();
}

// Handle deletion
if (isset($_GET['id'])) {
    $shippingMethodID = $_GET['id'];

    try {
        $deleteQuery = "DELETE FROM shippingmethods WHERE Shipping_Method_ID = :id";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bindParam(':id', $shippingMethodID);
        $deleteStmt->execute();

        echo "<script>alert('Delivery method deleted successfully!');</script>";
        echo "<script>window.location.href = 'deliveryMethods.php';</script>";
    } catch (PDOException $e) {
        die("Error deleting delivery method: " . $e->getMessage());
    }
} else {
    echo "<script>alert('Invalid request.');</script>";
    echo "<script>window.location.href = 'deliveryMethods.php';</script>";
}
