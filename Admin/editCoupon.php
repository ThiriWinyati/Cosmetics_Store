<?php
session_start();
require_once "../db_connect.php";
require_once "admin_auth.php";

admin_require_login('viewCoupons.php');

// Update the coupon details
if (isset($_POST['coupon_id'])) {
    $couponID = $_POST['coupon_id'];
    $couponCode = $_POST['coupon_code'];
    $discountPercentage = $_POST['discount_percentage'];
    $validFrom = $_POST['valid_from'];
    $validTo = $_POST['valid_to'];
    $minimumPurchaseAmount = $_POST['minimum_purchase_amount'];

    try {
        $updateCouponQuery = "UPDATE coupons SET Coupon_Code = :coupon_code, Discount_Percentage = :discount_percentage, Valid_From = :valid_from, Valid_To = :valid_to, Minimum_Purchase_Amount = :minimum_purchase_amount WHERE Coupon_ID = :coupon_id";
        $updateCouponStmt = $conn->prepare($updateCouponQuery);
        $updateCouponStmt->bindParam(':coupon_code', $couponCode, PDO::PARAM_STR);
        $updateCouponStmt->bindParam(':discount_percentage', $discountPercentage, PDO::PARAM_INT);
        $updateCouponStmt->bindParam(':valid_from', $validFrom, PDO::PARAM_STR);
        $updateCouponStmt->bindParam(':valid_to', $validTo, PDO::PARAM_STR);
        $updateCouponStmt->bindParam(':minimum_purchase_amount', $minimumPurchaseAmount, PDO::PARAM_INT);
        $updateCouponStmt->bindParam(':coupon_id', $couponID, PDO::PARAM_INT);
        $updateCouponStmt->execute();

        echo "<script>alert('Coupon updated successfully.');</script>";
        echo "<script>window.location.href = 'viewCoupons.php';</script>";
    } catch (PDOException $e) {
        die("Error updating coupon: " . $e->getMessage());
    }
}
