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

// Fetch the product ID from the URL
$productID = $_GET['id'] ?? null;

// Fetch shades for the product with images and quantities
$shadesQuery = "
    SELECT s.*, pi.image_path, s.Quantity AS stock_quantity
    FROM shades s 
    LEFT JOIN product_images pi ON s.shade_id = pi.shade_id 
    WHERE s.product_id = ?
";
$stmt = $conn->prepare($shadesQuery);
$stmt->execute([$productID]);
$shades = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

// Fetch the number of items in the wishlist
if (isset($_SESSION['customer_id'])) {
    $wishlistQuery = "SELECT COUNT(*) AS wishlist_count FROM favourites WHERE Customer_ID = ?";
    $stmt = $conn->prepare($wishlistQuery);
    $stmt->execute([$_SESSION['customer_id']]);
    $wishlistCount = $stmt->fetch(PDO::FETCH_ASSOC)['wishlist_count'];
} else {
    $wishlistCount = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
        echo "<script>alert('Please log in to add products to the cart.');</script>";
        echo "<script>window.location.href = 'user_login.php';</script>";
        exit();
    }

    $productId = $_POST['product_id']; // Product ID from the form
    $quantity = $_POST['quantity']; // Quantity from the form
    $customerId = $_SESSION['customer_id']; // Customer ID from the session
    $selectedShade = $_POST['selected_shade'] ?? null; // Selected shade from the form

    if (!$selectedShade) {
        echo "<script>alert('Please select a shade.');</script>";
        exit();
    }

    try {
        // Check if the product with the same shade is already in the cart
        $stmt = $conn->prepare("SELECT Quantity FROM shopping_cart WHERE Customer_ID = ? AND Product_ID = ? AND shade_id = ?");
        $stmt->execute([$customerId, $productId, $selectedShade]);
        $existingProduct = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingProduct) {
            // Update quantity if the product with the same shade is already in the cart
            $newQuantity = $existingProduct['Quantity'] + $quantity;
            $stmt = $conn->prepare("UPDATE shopping_cart SET Quantity = ? WHERE Customer_ID = ? AND Product_ID = ? AND shade_id = ?");
            $stmt->execute([$newQuantity, $customerId, $productId, $selectedShade]);
        } else {
            // Insert new product with shade into the cart
            $stmt = $conn->prepare("INSERT INTO shopping_cart (Customer_ID, Product_ID, Quantity, shade_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$customerId, $productId, $quantity, $selectedShade]);
        }

        // Update the cart session
        $stmt = $conn->prepare("
            SELECT 
                p.Name AS product_name, 
                sc.Quantity AS quantity, 
                p.Price AS price,
                sc.shade_id,
                s.shade_name,
                spi.image_path AS shade_image_path
            FROM shopping_cart sc 
            JOIN products p ON sc.Product_ID = p.Product_ID 
            LEFT JOIN shades s ON sc.shade_id = s.shade_id 
            LEFT JOIN product_images spi ON s.shade_id = spi.shade_id
            WHERE sc.Customer_ID = ?
        ");
        $stmt->execute([$_SESSION['customer_id']]);
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $_SESSION['cart'] = $cartItems;

        echo "<script>alert('Product added to cart successfully!');</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_wishlist'])) {
    if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
        echo "<script>alert('Please log in to add products to the wishlist.');</script>";
        echo "<script>window.location.href = 'user_login.php';</script>";
        exit();
    }

    $productId = $_POST['product_id']; // Product ID from the form
    $customerId = $_SESSION['customer_id']; // Customer ID from the session

    try {
        // Check if the product is already in the wishlist
        $stmt = $conn->prepare("SELECT * FROM favourites WHERE Customer_ID = ? AND Product_ID = ?");
        $stmt->execute([$customerId, $productId]);
        $existingProduct = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existingProduct) {
            // Insert new product into the wishlist
            $stmt = $conn->prepare("INSERT INTO favourites (Customer_ID, Product_ID) VALUES (?, ?)");
            $stmt->execute([$customerId, $productId]);
        }

        // echo "<script>alert('Product added to wishlist successfully!');</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

$orderDelivered = false;

if (isset($_SESSION['customer_id'])) {
    // Check if the customer's order for this product has been delivered
    $orderDeliveredQuery = "
        SELECT o.Order_ID 
        FROM orders o 
        JOIN order_items oi ON o.Order_ID = oi.Order_ID
        JOIN shipping s ON o.Order_ID = s.Order_ID
        WHERE o.Customer_ID = ? AND oi.Product_ID = ? AND s.Shipping_Status = 'Delivered'
    ";
    $stmt = $conn->prepare($orderDeliveredQuery);
    $stmt->execute([$_SESSION['customer_id'], $productID]);
    $orderDelivered = $stmt->rowCount() > 0; // If the order is delivered, this will be true
}

// Fetch the customer details
if (isset($_SESSION['customer_id'])) {
    $query = "SELECT * FROM customers WHERE Customer_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['customer_id']]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (isset($_SESSION['customer_id'])) {
        // Check if the customer's order for this product has been delivered
        $orderDeliveredQuery = "
            SELECT o.Order_ID 
            FROM orders o 
            JOIN order_items oi ON o.Order_ID = oi.Order_ID
            JOIN shipping s ON o.Order_ID = s.Order_ID
            WHERE o.Customer_ID = ? AND oi.Product_ID = ? AND s.Shipping_Status = 'Delivered'
        ";
        $stmt = $conn->prepare($orderDeliveredQuery);
        $stmt->execute([$_SESSION['customer_id'], $productID]);
        $orderDelivered = $stmt->rowCount() > 0; // If the order is delivered, this will be true

        if ($orderDelivered) {
            $rating = $_POST['rating'];
            $reviewText = $_POST['review_text'];

            $reviewQuery = "INSERT INTO reviews (Customer_ID, Product_ID, Rating, Review_Text, Review_Date) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($reviewQuery);
            $stmt->execute([$_SESSION['customer_id'], $productID, $rating, $reviewText]);

            echo "<script>alert('Review submitted successfully!');</script>";
        } else {
            echo "<script>alert('You must place an order and your order must be delivered before submitting a review.');</script>";
        }
    } else {
        echo "<script>alert('Please log in to submit a review.');</script>";
        echo "<script>window.location.href = 'user_login.php';</script>";
        exit();
    }
}

// Fetch product details
$productQuery = "
    SELECT 
        p.Product_ID, 
        p.Name AS productName, 
        c.Category_Name AS category,
        c.Category_ID,
        p.Price, 
        a.Name AS admin_user, 
        b.brand_name AS brand, 
        p.Description,
        p.Image_Path
    FROM products p
    LEFT JOIN categories c ON p.Category_ID = c.Category_ID
    LEFT JOIN admin_users a ON p.Admin_User_ID = a.Admin_User_ID
    LEFT JOIN brands b ON p.brand_id = b.brand_id
    WHERE p.Product_ID = ?
";
$stmt = $conn->prepare($productQuery);
$stmt->execute([$productID]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}

$imageSql = "SELECT image_path FROM product_images WHERE Product_ID = ?";
$imageStmt = $conn->prepare($imageSql);
$imageStmt->execute([$productID]);
$product['images'] = $imageStmt->fetchAll(PDO::FETCH_COLUMN);

// Add the main product image to the beginning of the images array
if (!empty($product['Image_Path'])) {
    array_unshift($product['images'], $product['Image_Path']);
}

// SQL query to calculate the average rating for a specific product
$avgRatingQuery = "SELECT AVG(Rating) AS avg_rating FROM reviews WHERE Product_ID = ?";
$stmt = $conn->prepare($avgRatingQuery);
$stmt->execute([$productID]);
$avgRatingResult = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the average rating is available, then round it to 1 decimal place
$avgRating = $avgRatingResult['avg_rating'] ? round($avgRatingResult['avg_rating'], 1) : 0;

// Generate stars based on average rating
$fullStars = floor($avgRating);
$emptyStars = 5 - $fullStars;

// Fetch related products 
$relatedProductsQuery = "
    SELECT p.Product_ID, p.Name, p.Price, b.brand_name AS brand, p.Image_Path, GROUP_CONCAT(pi.image_path) AS images
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.brand_id
    LEFT JOIN product_images pi ON p.Product_ID = pi.Product_ID
    WHERE p.Category_ID = ?
    AND p.Product_ID != ?
    GROUP BY p.Product_ID
    LIMIT 4
";
$stmt = $conn->prepare($relatedProductsQuery);
$stmt->execute([$product['Category_ID'], $productID]);
$relatedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch reviews for the product
$reviewsQuery = "
    SELECT r.*, c.Name AS customer_name 
    FROM reviews r 
    JOIN customers c ON r.Customer_ID = c.Customer_ID 
    WHERE r.Product_ID = ?
    ORDER BY r.Review_Date DESC
";
$stmt = $conn->prepare($reviewsQuery);
$stmt->execute([$productID]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pagination logic for reviews
$reviewsPerPage = 2;
$totalReviews = count($reviews);
$totalReviewPages = ceil($totalReviews / $reviewsPerPage);
$currentReviewPage = isset($_GET['review_page']) ? (int)$_GET['review_page'] : 1;
$startReviewIndex = ($currentReviewPage - 1) * $reviewsPerPage;
$paginatedReviews = array_slice($reviews, $startReviewIndex, $reviewsPerPage);
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
    <link rel="stylesheet" href="../Customer/customer_css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../Customer/customer_Javascript/imageThumbnail.js"></script>
    <script src="../Customer/customer_Javascript/addToCart.js"></script>
    <script src="../Customer/customer_Javascript/cart.js"></script>
    <link rel="icon" href="path/to/favicon.ico">
    <title>Details Page</title>

    <style>
        input[type=number]::-webkit-outer-spin-button,
        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        .review-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
        }

        .review {
            background-color: #ffffff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        .review .rating i {
            color: gold;
        }

        .review .customer-name {
            font-weight: bold;
            font-size: 1.1em;
        }

        .review .review-date {
            color: #6c757d;
            font-size: 0.9em;
        }

        .review .review-text {
            margin-top: 10px;
        }

        .review-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .review-form .form-label {
            font-weight: bold;
        }

        .review-form .stars input[type="radio"] {
            display: none;
        }

        .review-form .stars label {
            font-size: 1.5em;
            color: #ddd;
            cursor: pointer;
        }

        .review-form .stars input[type="radio"]:checked~label {
            color: gold;
        }

        .review-form .stars label:hover,
        .review-form .stars label:hover~label {
            color: gold;
        }

        .shade-tab {
            display: inline-block;
            margin: 5px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .shade-tab:hover {
            background-color: #f0f0f0;
        }

        .shade-tab input[type="radio"] {
            display: none;
        }

        .shade-tab .shade-box {
            width: 30px;
            height: 30px;
            border: 1px solid #ccc;
            margin-right: 5px;
            display: inline-block;
            vertical-align: middle;
        }

        .shade-tab.active {
            border-color: #000;
            background-color: #e0e0e0;
        }

        .divider {
            border-top: 1px solid #ccc;
            margin: 20px 0;
        }

        .thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .thumbnail.active {
            border-color: #000;
        }

        .stock-out {
            color: red;
            font-weight: bold;
        }

        .carousel-item img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        .thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .thumbnail.active {
            border-color: #000;
        }

        .shade-tab.active {
            border: 2px solid #000;
            background-color: #f0f0f0;
        }

        .card1:hover .main-image {
            display: none;
        }

        .card1:hover .card-carousel {
            display: block;
        }

        .card-carousel {
            display: none;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>" class="right d-flex" enctype="multipart/form-data">

        <div class="container mt-5">
            <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="user_homeIndex.php" style="color: black; text-decoration:none;">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Details</li>
                </ol>
            </nav>
            <div class="row">
                <!-- Main Image Carousel -->
                <div class="col-md-6 mt-3">
                    <div id="mainImageCarousel" class="carousel slide">
                        <div class="carousel-inner">
                            <?php foreach ($product['images'] as $index => $image) { ?>
                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <img src="<?php echo $image; ?>" class="d-block w-100 h-80 img-fluid product-image" alt="Product Image">
                                </div>
                            <?php } ?>
                        </div>
                        <!-- Carousel Controls -->
                        <button class="carousel-control-prev" type="button" data-bs-target="#mainImageCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#mainImageCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>

                    <!-- Thumbnails -->
                    <div class="row mt-3">
                        <div class="col-12 d-flex justify-content-start flex-wrap">
                            <?php foreach ($product['images'] as $index => $image) { ?>
                                <img src="<?php echo $image; ?>"
                                    class="thumbnail me-2 <?php echo $index === 0 ? 'active' : ''; ?>"
                                    alt="Thumbnail"
                                    onclick="jumpToSlide(<?php echo $index; ?>)">
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <!-- Product Details -->
                <div class="col-md-6 mt-3">
                    <h2 class="product-title"><?php echo htmlspecialchars($product['productName']); ?></h2>

                    <!-- Display stars -->

                    <strong>
                        <?php
                        // Display full stars with gold color and bigger size
                        for ($i = 0; $i < $fullStars; $i++) {
                            echo "<i class='fas fa-star' style='color: gold;'></i>"; // Full gold star, large size
                        }
                        // Display empty stars with gold color and bigger size
                        for ($i = 0; $i < $emptyStars; $i++) {
                            echo "<i class='far fa-star' style='color: gold;'></i>"; // Empty gold star, large size
                        }
                        ?>
                    </strong>

                    <p class="text-muted">Brand: <span class="brand-name"><?php echo htmlspecialchars($product['brand']); ?></span></p>
                    <h4 class="text-success">$<span class="product-price"><?php echo htmlspecialchars($product['Price']); ?></span></h4>
                    <!-- <p class="text-muted">Stock: <span class="stock-quantity"><?php echo htmlspecialchars($product['Stock_Quantity']); ?></span></p>

                    <?php if ($product['Stock_Quantity'] <= 0): ?>
                        <p class="stock-out">Stock Out</p>
                    <?php endif; ?> -->

                    <div class="divider"></div>

                    <form method="post" action="" class="mt-3">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['Product_ID']); ?>">
                        <div class="mt-4">
                            <h5>Available Shades</h5>
                            <div class="shade-tabs">
                                <?php if (!empty($shades)): ?>
                                    <?php foreach ($shades as $shade): ?>
                                        <label class="shade-tab">
                                            <input type="radio" name="selected_shade" value="<?php echo htmlspecialchars($shade['shade_id']); ?>"
                                                data-image="<?php echo htmlspecialchars($shade['image_path']); ?>" data-stock="<?php echo htmlspecialchars($shade['stock_quantity']); ?>"
                                                onclick="updateMainImage(this)">
                                            <div class="shade-box" style="background-image: url('<?php echo htmlspecialchars($shade['image_path']); ?>');
                                             background-size: cover; background-position: center;">
                                            </div>
                                            <span><?php echo htmlspecialchars($shade['shade_name']); ?> (<?php echo htmlspecialchars($shade['stock_quantity']); ?> in stock)</span>
                                        </label>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>No shades available for this product.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="divider"></div>

                        <div id="stock-out-message" class="stock-out" style="display: none;">Stock Out</div>

                        <div id="add-to-cart-section">
                            <div class="d-flex align-items-center mb-2">
                                <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(-1)">-</button>
                                <input type="number" name="quantity" value="1" min="1" class="form-control mx-2" style="width: 50px;" id="quantity">
                                <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(1)">+</button>
                            </div>
                            <button type="submit" name="add_to_cart" class="btn btn-dark w-50">Add to Cart</button>
                        </div>
                    </form>

                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $productID; ?>" class="mt-2">
                        <input type="hidden" name="product_id" value="<?php echo $product['Product_ID']; ?>">
                        <button type="submit" name="add_to_wishlist" class="btn btn-outline-dark w-50">
                            <i class="fa fa-heart" aria-hidden="true"></i> Save for Later
                        </button>
                    </form>

                </div>
            </div>

            <!-- Tabs for Description and Reviews -->
            <div class="mt-5">
                <ul class="nav nav-tabs" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="true">Description</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">Reviews</button>
                    </li>
                </ul>
                <div class="tab-content" id="productTabsContent">
                    <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                        <div class="mt-3">
                            <h4>Description</h4>
                            <p class="product-description"><?php echo nl2br(htmlspecialchars($product['Description'])); ?></p>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                        <div class="mt-3">
                            <h4>Customer Reviews</h4>
                            <?php if ($orderDelivered): ?>
                                <div class="review-form">
                                    <p class="text-center">You can now leave your honest review!</p>
                                    <form method="POST" action="viewDetails.php?id=<?php echo $productID; ?>">
                                        <div class="mb-3">
                                            <label for="rating" class="form-label">Rating</label>
                                            <div class="stars">
                                                <input type="radio" id="star5" name="rating" value="5">
                                                <label for="star5">&#9733;</label>
                                                <input type="radio" id="star4" name="rating" value="4">
                                                <label for="star4">&#9733;</label>
                                                <input type="radio" id="star3" name="rating" value="3">
                                                <label for="star3">&#9733;</label>
                                                <input type="radio" id="star2" name="rating" value="2">
                                                <label for="star2">&#9733;</label>
                                                <input type="radio" id="star1" name="rating" value="1">
                                                <label for="star1">&#9733;</label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="review_text" class="form-label">Review</label>
                                            <textarea id="review_text" name="review_text" class="form-control" rows="4" required></textarea>
                                        </div>

                                        <button type="submit" name="submit_review" class="btn btn-dark">Submit Review</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center">You must place an order and your order must be delivered before submitting a review. You can still read and react to existing reviews below.</p>
                            <?php endif; ?>

                            <div class="review-section mt-5">
                                <?php foreach ($paginatedReviews as $review): ?>
                                    <div class="review">
                                        <div class="d-flex justify-content-between">
                                            <span class="customer-name"><?php echo htmlspecialchars($review['customer_name']); ?></span>
                                            <span class="review-date"><?php echo htmlspecialchars($review['Review_Date']); ?></span>
                                        </div>
                                        <div class="rating">
                                            <?php for ($i = 0; $i < $review['Rating']; $i++): ?>
                                                <i class="fa fa-star"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <p class="review-text"><?php echo nl2br(html_entity_decode($review['Review_Text'])); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Pagination for Reviews -->
                            <nav aria-label="Review pagination">
                                <ul class="pagination justify-content-center mt-4">
                                    <?php if ($currentReviewPage > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?id=<?php echo $productID; ?>&review_page=<?php echo $currentReviewPage - 1; ?>" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php for ($i = 1; $i <= $totalReviewPages; $i++): ?>
                                        <li class="page-item <?php echo $i === $currentReviewPage ? 'active' : ''; ?>">
                                            <a class="page-link" href="?id=<?php echo $productID; ?>&review_page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <?php if ($currentReviewPage < $totalReviewPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?id=<?php echo $productID; ?>&review_page=<?php echo $currentReviewPage + 1; ?>" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </form>

    <!-- You May Also Like Section -->
    <div class="container mt-5">
        <h4 class="text-center mb-4">You May Also Like</h4>
        <div class="row justify-content-center g-4">
            <?php if (!empty($relatedProducts)): ?>
                <?php foreach ($relatedProducts as $relatedProduct): ?>
                    <?php
                    $imageArray = explode(',', $relatedProduct['images']);
                    $relatedProductID = $relatedProduct['Product_ID'];
                    // Fetch average rating
                    $avgRatingQuery = "SELECT AVG(Rating) AS avg_rating, COUNT(Rating) AS total_reviews FROM reviews WHERE Product_ID = ?";
                    $stmt = $conn->prepare($avgRatingQuery);
                    $stmt->execute([$relatedProductID]);
                    $avgRatingResult = $stmt->fetch(PDO::FETCH_ASSOC);
                    $avgRating = $avgRatingResult['avg_rating'] ? round($avgRatingResult['avg_rating'], 1) : 0;
                    $totalReviews = $avgRatingResult['total_reviews'];

                    // Add the main product image to the beginning of the images array
                    if (!empty($relatedProduct['Image_Path'])) {
                        array_unshift($imageArray, $relatedProduct['Image_Path']);
                    }
                    ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4 d-flex align-items-stretch">
                        <div class="card1 border-0 shadow-sm rounded position-relative" style="width: 250px; height: 350px;">
                            <div class="card-image-wrapper" style="position: relative;">
                                <div class="image-buttons">
                                    <form method="post" action="viewDetails.php?id=<?php echo $relatedProduct['Product_ID']; ?>">
                                        <input type="hidden" name="product_id" value="<?php echo $relatedProduct['Product_ID']; ?>">
                                        <button name="add_to_wishlist" class="btn btn-light btn-circle shadow"><i class="fas fa-heart"></i></button>
                                    </form>
                                    <a href="viewDetails.php?id=<?php echo $relatedProduct['Product_ID']; ?>" style="text-decoration: none;">
                                        <button class="btn btn-light btn-circle shadow"><i class="fas fa-eye"></i></button>
                                    </a>
                                </div>
                                <img src="<?php echo $relatedProduct['Image_Path']; ?>" class="d-block w-100 rounded-top product-image main-image" alt="<?php echo htmlspecialchars($relatedProduct['Name']); ?>" style="height: 200px; object-fit: contain;">
                                <div id="carousel-<?php echo $relatedProduct['Product_ID']; ?>" class="carousel slide card-carousel">
                                    <div class="carousel-inner">
                                        <?php foreach ($imageArray as $index => $image): ?>
                                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                                <img src="<?php echo $image; ?>" class="d-block w-100 rounded-top" alt="<?php echo htmlspecialchars($relatedProduct['Name']); ?>" style="height: 200px; object-fit: contain;">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $relatedProduct['Product_ID']; ?>" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $relatedProduct['Product_ID']; ?>" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body text-center">
                                <span class="card-title fw-bold text-truncate"><?php echo htmlspecialchars($relatedProduct['Name']); ?></span>
                                <div class="mt-2">
                                    <span class="text-warning">
                                        <?php for ($i = 0; $i < 5; $i++): ?>
                                            <i class="fa<?php echo $i < $avgRating ? 's' : 'r'; ?> fa-star"></i>
                                        <?php endfor; ?>
                                    </span>
                                    <p class="card-text text-muted"><?php echo $avgRating; ?> stars (<?php echo $totalReviews; ?> reviews)</p>
                                    <span class="text">$<?php echo htmlspecialchars($relatedProduct['Price']); ?></span>

                                </div>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted text-center">No related products found.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script>
        function updateQuantity(change) {
            var quantityInput = document.getElementById('quantity');
            var currentQuantity = parseInt(quantityInput.value);
            var newQuantity = currentQuantity + change;
            if (newQuantity > 0) {
                quantityInput.value = newQuantity;
            }
        }

        function jumpToSlide(index) {
            $('#mainImageCarousel').carousel(index);
            $('.thumbnail').removeClass('active');
            $('.thumbnail').eq(index).addClass('active');
        }

        function updateMainImage(radio) {
            var imagePath = radio.getAttribute('data-image');
            var stockQuantity = parseInt(radio.getAttribute('data-stock'));
            var mainImage = document.querySelector('#mainImageCarousel .carousel-item.active img');
            mainImage.src = imagePath;

            // Remove active class from all shade tabs
            document.querySelectorAll('.shade-tab').forEach(function(tab) {
                tab.classList.remove('active');
            });

            // Add active class to the selected shade tab
            radio.parentElement.classList.add('active');

            // Show or hide the "Stock Out" message and "Add to Cart" section
            var stockOutMessage = document.getElementById('stock-out-message');
            var addToCartSection = document.getElementById('add-to-cart-section');
            if (stockQuantity <= 0) {
                stockOutMessage.style.display = 'block';
                addToCartSection.style.display = 'none';
            } else {
                stockOutMessage.style.display = 'none';
                addToCartSection.style.display = 'block';
            }
        }
    </script>

</body>

</html>