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
    <title>Navbar</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="blog.php">Blog</a>
                    </li>
                </ul>
            </div>

            <div class="navbar-brand mx-auto d-flex align-items-center">
                <a href="user_homeIndex.php" class="d-flex align-items-center text-decoration-none color-black">
                    <img src="../images/logo.png" alt="">
                    <h5 class="ms-2 mb-0">Charm & Grace</h5>
                </a>
            </div>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Wishlist Button and Dropdown -->
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-bs-toggle="dropdown">
                            <div class="wishlist-icon position-relative">
                                <i class="fa fa-heart"></i>
                                <?php if (count($wishlistItems) > 0): ?>
                                    <span class="wishlist-quantity position-absolute top-0 start-100 translate-middle">
                                        <?php echo count($wishlistItems); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" id="wishlistDropdown">
                            <h6 class="dropdown-header">Your Wishlist</h6>
                            <?php if (!empty($wishlistItems)): ?>
                                <?php foreach ($wishlistItems as $item): ?>
                                    <div class="dropdown-item d-flex justify-content-between">
                                        <span><?php echo htmlspecialchars($item['Name']); ?></span>
                                        <span>$<?php echo number_format($item['Price'], 2); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="dropdown-item">Wishlist is empty</div>
                            <?php endif; ?>
                            <div class="dropdown-divider"></div>
                            <a href="wishlist.php" class="dropdown-item text-center">View Wishlist</a>
                        </div>
                    </li>

                    <!-- Cart Button and Dropdown -->
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-bs-toggle="dropdown">
                            <button id="cart" type="button" class="btn btn-outline-dark position-relative">
                                <i class="fa fa-shopping-cart me-2 position-relative">
                                    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                                        <?php $totalQuantity = array_sum(array_column($_SESSION['cart'], 'quantity')); ?>
                                        <span class="cart-quantity position-absolute top-0 start-100 translate-middle">
                                            <?php echo $totalQuantity; ?>
                                        </span>
                                    <?php endif; ?>
                                </i>
                                <span>My Cart</span>
                                <span class="cart-total d-block text-center mt-1">
                                    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                                        <?php $totalAmount = 0; ?>
                                        <?php foreach ($_SESSION['cart'] as $item): ?>
                                            <?php $totalAmount += $item['price'] * $item['quantity']; ?>
                                        <?php endforeach; ?>
                                        Total: $<?php echo number_format($totalAmount, 2); ?>
                                    <?php else: ?>
                                        Total: $0.00
                                    <?php endif; ?>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" id="cartDropdown">
                                <h6 class="dropdown-header">Your Cart</h6>
                                <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                                    <?php foreach ($_SESSION['cart'] as $item): ?>
                                        <div class="dropdown-item d-flex justify-content-between">
                                            <span><?php echo htmlspecialchars($item['product_name']); ?> x <?php echo $item['quantity']; ?></span>
                                            <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="dropdown-item">Cart is empty</div>
                                <?php endif; ?>
                                <a href="cart.php" class="dropdown-item text-center" style="justify-content:center;">View Cart</a>
                            </div>
                        </a>
                    </li>

                    <li class="nav-item">
                        <div class="dropdown">
                            <button id="account" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-user-circle-o"></i>
                                <span class="navbar-text me-3">
                                    <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true): ?>
                                        <?php echo 'Welcome, ' . $_SESSION['cname'] . '!'; ?>
                                    <?php else: ?>
                                        Welcome, Guest!
                                    <?php endif; ?>
                                    <i class="fas fa-caret-down"></i>
                                </span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="account">
                                <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true): ?>
                                    <li><a class="dropdown-item" href="userProfile.php">My Profile</a></li>
                                    <li><a class="dropdown-item" href="orderHistory.php">Order History</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?php echo isset($_SESSION['is_logged_in']) ? 'user_logout.php' : 'user_login.php'; ?>">
                                        <?php echo isset($_SESSION['is_logged_in']) ? 'Logout' : 'Login'; ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>


        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>