<?php
session_start();
require_once "../db_connect.php";

// Database credentials
$servername = 'localhost';
$username = 'root';
$password = '';
$database = 'cosmetics_store';

// Create connection
try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
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
    $paymentMethodID = $_GET['id'];

    try {
        $deleteQuery = "DELETE FROM payment_methods WHERE Payment_Method_ID = :id";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bindParam(':id', $paymentMethodID);
        $deleteStmt->execute();

        echo "<script>alert('Payment method deleted successfully!');</script>";
        echo "<script>window.location.href = 'viewPaymentMethods.php';</script>";
    } catch (PDOException $e) {
        die("Error deleting payment method: " . $e->getMessage());
    }
} else {
    echo "<script>alert('Invalid request.');</script>";
    echo "<script>window.location.href = 'viewPaymentMethods.php';</script>";
}
