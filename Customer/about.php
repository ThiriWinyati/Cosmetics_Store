<?php
require_once "../db_connect.php";

// Database credentials
$servername = 'localhost';
$username = 'root';
$password = '';
$database = 'cosmetics_store';

// Create connection
try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../Customer/customer_css/style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="icon" href="path/to/favicon.ico">
    <title>About Us: Charm & Grace</title>
</head>

<body>

    <?php include 'navbar.php' ?>

    <div class="container about-section mt-5">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="user_homeIndex.php" style="color: black; text-decoration:none;">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">About</li>
            </ol>
        </nav>
        <div class="row">
            <h1>About Us</h1>
            <div class="row align-items-center mb-5">
                <div class="col-md-6">
                    <p>Welcome to Charm & Grace, your premier online destination for all your beauty needs. We are a team of passionate individuals who are dedicated to providing you with the best possible shopping experience.</p>
                </div>
                <div class="col-md-6">
                    <img src="../images/about.jpg" alt="Cosmetics Store" class="img-fluid">
                </div>
            </div>

            <div class="row align-items-center mb-5">
                <div class="col-md-6 order-md-2">
                    <p>Founded in 2022, Charm & Grace started as a small boutique and has grown into a beloved online store. Our team is committed to curating a collection of high-quality cosmetics and beauty products that reflect the latest trends.</p>
                </div>
                <div class="col-md-6 order-md-1">
                    <img src="../images/our_story.jpg" alt="Our Story" class="img-fluid">
                </div>
            </div>

            <div class="row align-items-center mb-5">
                <div class="col-md-6">
                    <p>We invite you to explore our collection and join our community of beauty lovers. Follow us on social media for the latest updates, promotions, and beauty tips!</p>
                </div>
                <div class="col-md-6">
                    <img src="../images/community.jpg" alt="Join Us" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>

</html>