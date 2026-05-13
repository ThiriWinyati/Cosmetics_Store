<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../db_connect.php";
require_once "admin_auth.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Charm & Grace: Admin Home</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../Admin/admin_css/style.css">
    <link rel="icon" href="path/to/favicon.ico">
</head>

<body>

    <?php include 'sidebar_nav.php'; ?>

    <main class="admin-home-wrapper">
        <section class="admin-home-hero">

            <div class="admin-home-gif-wrapper">
                <img src="../adminHome.gif" alt="Welcome GIF" class="admin-home-gif">
            </div>

            <div class="welcome-card">
                <h2>Welcome to Charm & Grace Admin Dashboard</h2>
                <p>Manage your store efficiently and effectively.</p>
            </div>

            <div class="quick-links">
                <div class="quick-link-card">
                    <i class="fa fa-box admin-home-icon"></i>
                    <h3>View Products</h3>
                    <p>Manage your product catalog.</p>
                    <a href="viewProduct.php">Go to Products</a>
                </div>

                <div class="quick-link-card">
                    <i class="fa fa-list-alt admin-home-icon"></i>
                    <h3>View Orders</h3>
                    <p>Manage customer orders.</p>
                    <a href="viewOrders.php">Go to Orders</a>
                </div>

                <div class="quick-link-card">
                    <i class="fa fa-users admin-home-icon"></i>
                    <h3>View Customers</h3>
                    <p>Manage customer information.</p>
                    <a href="viewCustomer.php">Go to Customers</a>
                </div>

                <div class="quick-link-card">
                    <i class="fa fa-star admin-home-icon"></i>
                    <h3>View Reviews</h3>
                    <p>Manage product reviews.</p>
                    <a href="viewReviews.php">Go to Reviews</a>
                </div>
            </div>

        </section>
    </main>

    </div> <!-- closes admin-main from sidebar_nav.php -->

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
