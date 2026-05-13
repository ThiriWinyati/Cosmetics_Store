<?php
session_start();
require_once "../db_connect.php";
require_once "admin_auth.php";

admin_require_login('viewCategory.php');

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
