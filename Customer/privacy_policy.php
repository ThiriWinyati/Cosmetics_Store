<?php
require_once "../db_connect.php";

// Database credentials
$server = getenv('DB_HOST');
$user = getenv('DB_USER');
$password = getenv('DB_PASS');
$database = getenv('DB_NAME');
$port = getenv('DB_PORT') ?: 3306;

// Create connection
try {
    $conn = new PDO(
        "mysql:host=$server;port=$port;dbname=$database;charset=utf8mb4",
        $user,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if (!isset($_SESSION)) {
    session_start();
}

// Fetch wishlist items
if (isset($_SESSION['customer_id'])) {
    $wishlistQuery = "SELECT p.Name, p.Price, p.Product_ID 
                    FROM favourites f 
                    JOIN products p ON f.Product_ID = p.Product_ID 
                    WHERE f.Customer_ID = ?";
    $stmt = $conn->prepare($wishlistQuery);
    $stmt->execute([$_SESSION['customer_id']]);
    $wishlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $wishlistItems = [];
}

// Fetch cart items
if (isset($_SESSION['cart'])) {
    $cartItems = $_SESSION['cart'];
} else {
    $cartItems = [];
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .privacy-container {
            margin-top: 20px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h3 {
            text-align: center;
            margin-bottom: 20px;
        }

        p {
            margin-bottom: 10px;
        }

        ul {
            margin-left: 20px;
            list-style-type: disc;
        }

        .btn-back {
            display: block;
            margin: 20px auto;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="user_homeIndex.php" style="color: black; text-decoration:none;">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Privacy Policy</li>
            </ol>
        </nav>
        <div class="privacy-container">
            <h3>Privacy Policy</h3>
            <p>Your privacy is critically important to us. This Privacy Policy outlines how we collect, use, and protect your personal information when you use our e-commerce services.</p>

            <h4>1. Information We Collect</h4>
            <ul>
                <li>We collect personal information that you provide directly, such as your name, email, phone number, and address when you create an account or make a purchase.</li>
                <li>We may collect non-personal information such as your IP address, browser type, and browsing behavior on our site.</li>
            </ul>

            <h4>2. How We Use Your Information</h4>
            <ul>
                <li>To process your orders and deliver products and services you purchase from us.</li>
                <li>To improve our website, customer service, and overall shopping experience.</li>
                <li>To send periodic emails regarding your order or other products and services.</li>
            </ul>

            <h4>3. Sharing Your Information</h4>
            <ul>
                <li>We do not sell, trade, or rent your personal information to others.</li>
                <li>We may share your information with trusted third-party service providers who assist us in operating our website, conducting our business, or servicing you, provided that these parties agree to keep this information confidential.</li>
            </ul>

            <h4>4. Security of Your Information</h4>
            <ul>
                <li>We implement security measures to protect your personal information from unauthorized access, alteration, disclosure, or destruction.</li>
                <li>However, no method of transmission over the Internet or electronic storage is 100% secure. Therefore, we cannot guarantee absolute security.</li>
            </ul>

            <h4>5. Your Rights</h4>
            <ul>
                <li>You have the right to access, correct, or delete your personal information at any time.</li>
                <li>You can also request that we restrict the processing of your information or object to our use of your information.</li>
            </ul>

            <h4>6. Changes to This Privacy Policy</h4>
            <ul>
                <li>We reserve the right to update or change our Privacy Policy at any time. We will notify you of any changes by posting the new Privacy Policy on our website.</li>
                <li>It is your responsibility to review this Privacy Policy periodically for any updates or changes.</li>
            </ul>

            <p>By using our site, you consent to our Privacy Policy.</p>

            <div class="btn-back">
                <a href="user_homeIndex.php" class="btn btn-primary">Back to Home</a>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>

</html>