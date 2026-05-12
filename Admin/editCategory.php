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
    $categoryName = $_POST['category_name'];

    try {
        $updateQuery = "UPDATE categories SET Category_Name = :category_name WHERE Category_ID = :category_id";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':category_name', $categoryName);
        $updateStmt->bindParam(':category_id', $categoryId);
        $updateStmt->execute();

        echo "<script>alert('Category updated successfully.');</script>";
        echo "<script>window.location.href = 'viewCategory.php';</script>";
    } catch (PDOException $e) {
        die("Error updating category: " . $e->getMessage());
    }
}
