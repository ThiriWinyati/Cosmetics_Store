<?php
session_start();
require_once "../db_connect.php";
require_once "admin_auth.php";

admin_require_login('viewCoupons.php');

// Delete the coupon and related orders
if (isset($_POST['coupon_id'])) {
    $couponID = $_POST['coupon_id'];

    try {
        // Check if the coupon is used in any orders
        $checkCouponUsageQuery = "SELECT COUNT(*) as usage_count FROM orders WHERE cupon_id = :coupon_id";
        $checkCouponUsageStmt = $conn->prepare($checkCouponUsageQuery);
        $checkCouponUsageStmt->bindParam(':coupon_id', $couponID, PDO::PARAM_INT);
        $checkCouponUsageStmt->execute();
        $usageCount = $checkCouponUsageStmt->fetch(PDO::FETCH_ASSOC)['usage_count'];

        if ($usageCount > 0) {
            // Delete related orders
            $deleteOrdersQuery = "DELETE FROM orders WHERE cupon_id = :coupon_id";
            $deleteOrdersStmt = $conn->prepare($deleteOrdersQuery);
            $deleteOrdersStmt->bindParam(':coupon_id', $couponID, PDO::PARAM_INT);
            $deleteOrdersStmt->execute();
        }

        // Delete the coupon
        $deleteCouponQuery = "DELETE FROM coupons WHERE Coupon_ID = :coupon_id";
        $deleteCouponStmt = $conn->prepare($deleteCouponQuery);
        $deleteCouponStmt->bindParam(':coupon_id', $couponID, PDO::PARAM_INT);
        $deleteCouponStmt->execute();

        echo "<script>alert('Coupon and related orders deleted successfully.');</script>";
        echo "<script>window.location.href = 'viewCoupons.php';</script>";
    } catch (PDOException $e) {
        die("Error deleting coupon: " . $e->getMessage());
    }
}
