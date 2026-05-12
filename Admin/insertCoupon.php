<?php
session_start();
require_once "../db_connect.php";

// Check if the user is logged in as an admin
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    echo "<script>alert('Please log in as an admin.');</script>";
    echo "<script>window.location.href = 'adminLogin.php';</script>";
    exit();
}

// Insert coupon into the database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $coupon_code = $_POST['coupon_code'];
    $discount_percentage = $_POST['discount_percentage'];
    $valid_from = $_POST['valid_from'];
    $valid_to = $_POST['valid_to'];
    $minimum_purchase_amount = $_POST['minimum_purchase_amount'];

    try {
        $query = "INSERT INTO coupons (Coupon_Code, Discount_Percentage, Valid_From, Valid_To, Minimum_Purchase_Amount) 
                  VALUES (:coupon_code, :discount_percentage, :valid_from, :valid_to, :minimum_purchase_amount)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':coupon_code', $coupon_code);
        $stmt->bindParam(':discount_percentage', $discount_percentage);
        $stmt->bindParam(':valid_from', $valid_from);
        $stmt->bindParam(':valid_to', $valid_to);
        $stmt->bindParam(':minimum_purchase_amount', $minimum_purchase_amount);
        $stmt->execute();
        echo "<script>alert('Coupon inserted successfully.'); window.location.href='viewCoupons.php';</script>";
    } catch (PDOException $e) {
        die("Error inserting coupon: " . $e->getMessage());
    }
}
