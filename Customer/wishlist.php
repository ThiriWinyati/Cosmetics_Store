<?php
session_start();
require_once "../db_connect.php";

// Ensure the user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    echo "<script>alert('Please log in to view your wishlist.');</script>";
    echo "<script>window.location.href = 'user_login.php';</script>";
    exit();
}

// Remove item from the wishlist
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_favourites_id'])) {
    $favouritesIdToRemove = intval($_POST['remove_favourites_id']);

    try {
        $stmt = $conn->prepare("DELETE FROM favourites WHERE FavouritesID = :favouritesIdToRemove AND Customer_ID = :customer_id");
        $stmt->bindParam(':favouritesIdToRemove', $favouritesIdToRemove, PDO::PARAM_INT);
        $stmt->bindParam(':customer_id', $_SESSION['customer_id'], PDO::PARAM_INT);
        $stmt->execute();

        header("Location: wishlist.php");
        exit();
    } catch (PDOException $e) {
        echo "Error removing item: " . $e->getMessage();
    }
}

// Fetch unique wishlist items (only one record per product)
$query = "SELECT 
            f.FavouritesID, 
            p.Name AS Product_Name, 
            p.Price, 
            COALESCE(MIN(pi.Image_Path), p.Image_Path) AS Image_Path,
            p.Product_ID, 
            p.Brand_ID
          FROM favourites f
          JOIN products p ON f.Product_ID = p.Product_ID
          LEFT JOIN product_images pi ON p.Product_ID = pi.Product_ID
          WHERE f.Customer_ID = ?
          GROUP BY 
            f.FavouritesID, 
            p.Name, 
            p.Price, 
            p.Image_Path,
            p.Product_ID, 
            p.Brand_ID,
            f.DateAdded
          ORDER BY f.DateAdded ASC";

$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['customer_id']]);

// Populate the session wishlist with images and prevent duplicates
$_SESSION['wishlist'] = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $_SESSION['wishlist'][] = [
        'favourites_id' => $row['FavouritesID'],
        'product_name' => $row['Product_Name'],
        'price' => $row['Price'],
        'image_path' => $row['Image_Path'],
        'product_id' => $row['Product_ID'],
        'brand_id' => $row['Brand_ID'],
    ];
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
    <link rel="stylesheet" href="../Customer/customer_css/style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Wishlist - Cosmetics Shop</title>
</head>

<body class="wishlist-page">
    <?php include 'navbar.php'; ?>

    <main class="container wishlist-page-container">
        <nav class="wishlist-breadcrumb" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="user_homeIndex.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Wishlist</li>
            </ol>
        </nav>

        <section class="wishlist-hero">
            <span>Saved Favorites</span>
            <h1>Your Wishlist</h1>
            <p>Keep your favorite Charm & Grace picks close and move them to cart when you are ready.</p>
        </section>

        <?php if (empty($_SESSION['wishlist'])): ?>
            <section class="wishlist-empty-state">
                <i class="fa fa-heart-o" aria-hidden="true"></i>
                <h2>Your wishlist is empty</h2>
                <p>Start saving products you love and they will appear here.</p>
                <a href="products.php" class="wishlist-primary-link">Browse Products</a>
            </section>
        <?php else: ?>
            <section class="wishlist-grid" aria-label="Wishlist products">
                <?php foreach ($_SESSION['wishlist'] as $item): ?>
                    <article class="wishlist-product-card">
                        <form method="POST" action="wishlist.php" class="wishlist-remove-form">
                            <input type="hidden" name="remove_favourites_id" value="<?php echo $item['favourites_id']; ?>">
                            <button type="submit" class="wishlist-remove-btn" aria-label="Remove <?php echo htmlspecialchars($item['product_name']); ?> from wishlist" onclick="return confirm('Are you sure you want to remove this item from your wishlist?')">
                                <i class="fa fa-times"></i>
                            </button>
                        </form>

                        <a href="viewDetails.php?id=<?php echo $item['product_id']; ?>" class="wishlist-image-link">
                            <img src="<?php echo !empty($item['image_path']) ? htmlspecialchars($item['image_path']) : '../images/default-image.jpg'; ?>"
                                alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                        </a>

                        <div class="wishlist-product-info">
                            <h2><?php echo htmlspecialchars($item['product_name']); ?></h2>
                            <p>$<?php echo number_format($item['price'], 2); ?></p>
                        </div>

                        <div class="wishlist-actions">
                            <a href="viewDetails.php?id=<?php echo $item['product_id']; ?>" class="wishlist-secondary-link">
                                <i class="fa fa-eye"></i> View Details
                            </a>
                            <form method="POST" action="add_to_cart.php">
                                <input type="hidden" name="add_to_cart" value="1">
                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($item['product_name']); ?>">
                                <input type="hidden" name="price" value="<?php echo htmlspecialchars($item['price']); ?>">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="image_path" value="<?php echo htmlspecialchars($item['image_path']); ?>">
                                <button type="submit" class="wishlist-primary-btn">
                                    <i class="fa fa-cart-plus"></i> Add to Cart
                                </button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>

            <div class="wishlist-footer-action">
                <a href="products.php" class="wishlist-primary-link">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>

</html>
