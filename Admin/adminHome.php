<?php
session_start();
require_once "../db_connect.php";

// Check admin session
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    echo "<script>alert('Please log in as an admin.');</script>";
    echo "<script>window.location.href = 'adminLogin.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../Admin/admin_css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../Admin/admin_Javascript/sidebar.js"></script>
    <link rel="icon" href="path/to/favicon.ico">
    <title>Charm & Grace: Admin Home</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .home-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 20px;
        }

        .welcome-card {
            padding: 20px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .welcome-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .welcome-card h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #d97cb3;
        }

        .welcome-card p {
            font-size: 20px;
            color: #666;
        }

        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .quick-link-card {
            padding: 20px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .quick-link-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .quick-link-card h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #d97cb3;
        }

        .quick-link-card p {
            font-size: 16px;
            color: #666;
        }

        .quick-link-card a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .quick-link-card a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <?php include 'sidebar_nav.php'; ?>

    <div class="container mt-2">
        <div class="home-container">
            <div class="text-center mb-2">
                <img src="../adminHome.gif" alt="Welcome GIF" style="max-width: 50%; height: 200px;">
            </div>
            <div class="welcome-card">
                <h2>Welcome to Charm & Grace Admin Dashboard</h2>
                <p>Manage your store efficiently and effectively.</p>
            </div>

            <div class="quick-links">
                <div class="quick-link-card">
                    <h3>View Products</h3>
                    <p>Manage your product catalog.</p>
                    <a href="viewProduct.php">Go to Products</a>
                </div>
                <div class="quick-link-card">
                    <h3>View Orders</h3>
                    <p>Manage customer orders.</p>
                    <a href="viewOrders.php">Go to Orders</a>
                </div>
                <div class="quick-link-card">
                    <h3>View Customers</h3>
                    <p>Manage customer information.</p>
                    <a href="viewCustomer.php">Go to Customers</a>
                </div>
                <div class="quick-link-card">
                    <h3>View Reviews</h3>
                    <p>Manage product reviews.</p>
                    <a href="viewReviews.php">Go to Reviews</a>
                </div>
            </div>
        </div>
    </div>

</body>

</html>