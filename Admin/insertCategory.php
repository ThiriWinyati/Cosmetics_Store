<?php
session_start();
require_once "../db_connect.php";
require_once "admin_auth.php";

admin_require_login('viewCategory.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categoryName = $_POST['category_name'];

    try {
        $insertQuery = "INSERT INTO categories (Category_Name) VALUES (:category_name)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bindParam(':category_name', $categoryName);
        $insertStmt->execute();

        echo "<script>alert('Category inserted successfully.');</script>";
        echo "<script>window.location.href = 'viewCategory.php';</script>";
    } catch (PDOException $e) {
        die("Error inserting category: " . $e->getMessage());
    }
}
