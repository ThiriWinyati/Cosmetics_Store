<?php
session_start();
require_once "../db_connect.php";

// Check if the user is logged in as an admin
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    echo "<script>alert('Please log in as an admin.');</script>";
    echo "<script>window.location.href = 'adminLogin.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categoryId = $_POST['category_id'];

    try {
        $deleteQuery = "DELETE FROM categories WHERE Category_ID = :category_id";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bindParam(':category_id', $categoryId);
        $deleteStmt->execute();

        echo "<script>alert('Category deleted successfully.');</script>";
        echo "<script>window.location.href = 'viewCategory.php';</script>";
    } catch (PDOException $e) {
        die("Error deleting category: " . $e->getMessage());
    }
}
