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
        // Remove from the favourites table in the database
        $stmt = $conn->prepare("DELETE FROM favourites WHERE FavouritesID = :favouritesIdToRemove");
        $stmt->bindParam(':favouritesIdToRemove', $favouritesIdToRemove, PDO::PARAM_INT);
        $stmt->execute();

        // Remove from session and reindex the array
        $_SESSION['wishlist'] = array_values(array_filter($_SESSION['wishlist'], function ($item) use ($favouritesIdToRemove) {
            return $item['favourites_id'] !== $favouritesIdToRemove;
        }));

        // Redirect to refresh the page after removal
        header("Location: wishlist.php");
        exit();
    } catch (PDOException $e) {
        echo "Error removing item: " . $e->getMessage();
    }
}

// Fetch unique wishlist items (only one record per product)
$query = "SELECT f.FavouritesID, p.Name AS Product_Name, p.Price, 
                 pi.Image_Path AS Image_Path, p.Product_ID, p.Brand_ID
          FROM favourites f
          JOIN products p ON f.Product_ID = p.Product_ID
          LEFT JOIN product_images pi ON p.Product_ID = pi.Product_ID
          WHERE f.Customer_ID = ?
          GROUP BY f.FavouritesID, p.Name, p.Price, p.Product_ID, p.Brand_ID
          ORDER BY f.DateAdded ASC"; // Sort by date added or any other preference

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
    <style>
        .wishlist-container {
            margin-top: 50px;
        }

        .wishlist-table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
            border-radius: 12px;
            overflow: hidden;
        }

        .wishlist-table th,
        .wishlist-table td {
            padding: 0.75rem;
            vertical-align: top;
            text-align: center;
        }

        .wishlist-table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
            background-color: transparent;
            color: #fff;
        }

        .wishlist-table tbody+tbody {
            border-top: 2px solid #dee2e6;
        }

        .wishlist-table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .wishlist-table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .wishlist-table img {
            border-radius: 10px;
            object-fit: cover;
            height: 100px;
            width: 100px;
        }

        .wishlist-table-actions .btn {
            width: 50%;
            padding: 5px;
            border-radius: 10px;
            font-weight: bold;
            transition: all 0.3s ease;
            margin-bottom: 5px;
            font-size: 0.8rem;
        }

        .wishlist-table-actions .btn-primary {
            background-color: white;
            border: 1px solid black;
            color: black;
        }

        .wishlist-table-actions .btn-primary:hover {
            background-color: black;
            border: none;
            color: white;
        }

        .wishlist-table-actions .btn-success {
            background-color: white;
            border: 1px solid black;
            color: black;
        }

        .wishlist-table-actions .btn-success:hover {
            background-color: black;
            border: none;
            color: white;
        }

        .wishlist-table-actions .btn-danger {
            background-color: transparent;
            border: none;
            color: black;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="user_homeIndex.php" style="color: black; text-decoration:none;">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Wishlist</li>
            </ol>
        </nav>

        <div class="card shadow-lg">
            <div class="card-body">
                <h3 class="text-center mb-4">Your Wishlist</h3>

                <?php if (empty($_SESSION['wishlist'])): ?>
                    <p class="text-center">Your wishlist is empty. Start adding items!</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table wishlist-table wishlist-table-hover">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th> </th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_SESSION['wishlist'] as $index => $item): ?>
                                    <tr>
                                        <td><img src="<?php echo !empty($item['image_path']) ? htmlspecialchars($item['image_path']) : 'default-image.jpg'; ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>"></td>
                                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                                        <td class="wishlist-table-actions">
                                            <a href="viewDetails.php?id=<?php echo $item['product_id']; ?>" class="btn btn-primary">
                                                <i class="fa fa-eye"></i> View Details
                                            </a>
                                            <form method="POST" action="add_to_cart.php" style="display:inline;">
                                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fa fa-cart-plus"></i> Add to Cart
                                                </button>
                                            </form>

                                        </td>
                                        <td class="wishlist-table-actions">
                                            <form method="POST" action="wishlist.php" style="display:inline;">
                                                <input type="hidden" name="remove_favourites_id" value="<?php echo $item['favourites_id']; ?>">
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to remove this item from your wishlist?')">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="text-center mt-3">
                    <a href="products.php" class="btn btn-primary">Continue Shopping</a>
                </div>
            </div>
        </div>

        <?php include 'footer.php'; ?>
</body>

</html>