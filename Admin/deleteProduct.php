<?php
require_once "../db_connect.php";

if (isset($_SESSION)) {
    session_start();
}

if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Delete related records in shopping_cart
    $sqlShoppingCart = "DELETE FROM shopping_cart WHERE Product_ID = ?";
    $stmtShoppingCart = $conn->prepare($sqlShoppingCart);
    $stmtShoppingCart->execute([$productId]);

    // Delete related records in reviews
    $sqlReviews = "DELETE FROM reviews WHERE Product_ID = ?";
    $stmtReviews = $conn->prepare($sqlReviews);
    $stmtReviews->execute([$productId]);

    // Delete related records in order_items
    $sqlOrderItems = "DELETE FROM order_items WHERE Product_ID = ?";
    $stmtOrderItems = $conn->prepare($sqlOrderItems);
    $stmtOrderItems->execute([$productId]);

    // Now delete the product
    $sqlProduct = "DELETE FROM products WHERE Product_ID = ?";
    $stmtProduct = $conn->prepare($sqlProduct);
    $status = $stmtProduct->execute([$productId]);

    if ($status) {
        $_SESSION['deleteSuccess'] = "Product with ID $productId has been deleted successfully";
        header("Location: viewProduct.php");
        exit();
    } else {
        $_SESSION['deleteError'] = "Failed to delete product with ID $productId";
        header("Location: viewProduct.php");
        exit();
    }
}
