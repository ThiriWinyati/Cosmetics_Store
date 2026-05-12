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
    <title>Terms & Conditions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .terms-container {
            margin-top: 20px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
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
                <li class="breadcrumb-item active" aria-current="page">Terms & Conditions</li>
            </ol>
        </nav>
        <div class="terms-container mt-3">

            <h2>Terms & Conditions</h2>
            <p>Welcome to Charm & Grace! By using our services, you agree to comply with the following terms and conditions.</p>

            <h3>1. General Terms</h3>
            <ul>
                <li>By placing an order, you agree to provide accurate and truthful information.</li>
                <li>All prices are displayed in USD and are subject to change without notice.</li>
                <li>We reserve the right to refuse service to anyone for any reason at any time.</li>
            </ul>

            <h3>2. Payment and Billing</h3>
            <ul>
                <li>All payments must be received before the shipment of goods.</li>
                <li>We accept various payment methods including credit/debit cards, PayPal, and other local payment services.</li>
                <li>You agree to provide current, complete, and accurate purchase and account information.</li>
            </ul>

            <h3>3. Shipping and Delivery</h3>
            <ul>
                <li>We aim to dispatch all orders within the specified delivery times mentioned.</li>
                <li>We are not responsible for delays caused by courier services or customs clearance.</li>
                <li>Shipping costs and delivery times vary based on location and shipping method selected.</li>
            </ul>

            <h3>4. Returns and Refunds</h3>
            <ul>
                <li>Returns are accepted within 30 days of purchase, provided the items are unopened and in their original condition.</li>
                <li>Refunds will be processed to the original payment method within a reasonable timeframe.</li>
                <li>Shipping costs for returns are the responsibility of the customer unless the product is defective or incorrect.</li>
            </ul>

            <h3>5. Privacy Policy</h3>
            <ul>
                <li>Your privacy is important to us. We collect and use your information in accordance with our Privacy Policy.</li>
                <li>We implement security measures to protect your personal information from unauthorized access.</li>
            </ul>

            <p>By continuing to use our site, you acknowledge and agree to our Terms & Conditions.</p>

            <div class="btn-back">
                <a href="user_homeIndex.php" class="btn btn-primary">Back to Home</a>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>

</html>