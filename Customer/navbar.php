<?php
require_once "../db_connect.php";

if (session_status() === PHP_SESSION_NONE) {
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
$cartItems = $_SESSION['cart'] ?? [];
$totalQuantity = 0;
$totalAmount = 0;

if (!empty($cartItems)) {
    foreach ($cartItems as $item) {
        $totalQuantity += $item['quantity'] ?? 0;
        $totalAmount += ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
    }
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
    <nav class="navbar navbar-expand-lg navbar-light bg-white customer-navbar">
        <div class="container-fluid">

            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="/Customer/user_homeIndex.php">
                <img src="/images/logo.png" alt="Charm & Grace Logo">
                <h5 class="ms-2 mb-0">Charm & Grace</h5>
            </a>

            <!-- Collapsible Navbar Content -->
            <div class="collapse navbar-collapse" id="mainNavbar">

                <!-- Left Links -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/Customer/about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Customer/contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Customer/products.php">Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Customer/blog.php">Blog</a>
                    </li>
                </ul>

                <!-- Right Icons -->
                <ul class="navbar-nav ms-auto align-items-lg-center">

                    <!-- Wishlist -->
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link" data-bs-toggle="dropdown" aria-expanded="false">
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
                                        <span><?php echo htmlspecialchars($item['Name'] ?? 'Product'); ?></span>
                                        <span>$<?php echo number_format((float)($item['Price'] ?? 0), 2); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="dropdown-item">Wishlist is empty</div>
                            <?php endif; ?>

                            <div class="dropdown-divider"></div>
                            <a href="/Customer/wishlist.php" class="dropdown-item text-center">View Wishlist</a>
                        </div>
                    </li>

                    <!-- Cart -->
                    <li class="nav-item dropdown">
                        <button id="cart" type="button" class="btn btn-outline-dark position-relative dropdown-toggle"
                            data-bs-toggle="dropdown" aria-expanded="false">

                            <i class="fa fa-shopping-cart me-2"></i>

                            <?php if ($totalQuantity > 0): ?>
                                <span class="cart-quantity position-absolute top-0 start-100 translate-middle">
                                    <?php echo $totalQuantity; ?>
                                </span>
                            <?php endif; ?>

                            <span>My Cart</span>

                            <span class="cart-total d-block text-center mt-1">
                                Total: $<?php echo number_format($totalAmount, 2); ?>
                            </span>
                        </button>

                        <div class="dropdown-menu dropdown-menu-end" id="cartDropdown">
                            <h6 class="dropdown-header">Your Cart</h6>

                            <?php if (!empty($cartItems)): ?>
                                <?php foreach ($cartItems as $item): ?>
                                    <div class="dropdown-item d-flex justify-content-between">
                                        <span>
                                            <?php echo htmlspecialchars($item['product_name'] ?? 'Product'); ?>
                                            x <?php echo htmlspecialchars($item['quantity'] ?? 0); ?>
                                        </span>
                                        <span>
                                            $<?php echo number_format((float)(($item['price'] ?? 0) * ($item['quantity'] ?? 0)), 2); ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="dropdown-item">Cart is empty</div>
                            <?php endif; ?>

                            <div class="dropdown-divider"></div>
                            <a href="/Customer/cart.php" class="dropdown-item text-center">View Cart</a>
                        </div>
                    </li>

                    <!-- Account -->
                    <li class="nav-item dropdown">
                        <button id="account" type="button" class="btn btn-outline-dark dropdown-toggle"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-user-circle-o"></i>

                            <span>
                                <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true): ?>
                                    Welcome, <?php echo htmlspecialchars($_SESSION['cname'] ?? 'Customer'); ?>!
                                <?php else: ?>
                                    Welcome, Guest!
                                <?php endif; ?>
                            </span>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="account">
                            <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true): ?>
                                <li><a class="dropdown-item" href="/Customer/userProfile.php">My Profile</a></li>
                                <li><a class="dropdown-item" href="/Customer/orderHistory.php">Order History</a></li>
                                <li><a class="dropdown-item" href="/Customer/user_logout.php">Logout</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="/Customer/user_login.php">Login</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>

                </ul>
            </div>
        </div>
    </nav>                     
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const navbar = document.querySelector('.customer-navbar');
            const toggler = navbar?.querySelector('.navbar-toggler');
            const menu = navbar?.querySelector('#mainNavbar');

            if (!toggler || !menu || typeof bootstrap === 'undefined') {
                return;
            }

            toggler.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();

                const collapse = bootstrap.Collapse.getOrCreateInstance(menu, {
                    toggle: false
                });

                if (menu.classList.contains('show')) {
                    collapse.hide();
                    toggler.setAttribute('aria-expanded', 'false');
                } else {
                    collapse.show();
                    toggler.setAttribute('aria-expanded', 'true');
                }
            }, true);
        });
    </script>
</body>

</html>
