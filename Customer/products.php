<?php

session_start();

// Database connection
$server = getenv('DB_HOST');
$user = getenv('DB_USER');
$password = getenv('DB_PASS');
$database = getenv('DB_NAME');
$port = getenv('DB_PORT') ?: 3306;

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

// Fetch wishlist items
if (isset($_SESSION['customer_id'])) {
    $wishlistQuery = "SELECT p.Name, p.Price, p.Product_ID, MIN(pi.Image_Path) AS Image_Path
                      FROM favourites f 
                      JOIN products p ON f.Product_ID = p.Product_ID 
                      LEFT JOIN product_images pi ON p.Product_ID = pi.Product_ID 
                      WHERE f.Customer_ID = ?
                      GROUP BY p.Product_ID, p.Name, p.Price";
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

// Initialize variables
$categories = [];
$brands = [];
$products = [];

// Fetch categories
$categoryQuery = "SELECT Category_ID, Category_Name FROM categories";
$stmt = $conn->query($categoryQuery);

if ($stmt && $stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $categories[] = $row;
    }
} else {
    $categories = []; // Ensure $categories is always defined
}

// Fetch brands
$brandQuery = "SELECT Brand_ID, Brand_Name FROM brands";
$stmt = $conn->query($brandQuery);

if ($stmt && $stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $brands[] = $row;
    }
} else {
    $brands = []; // Ensure $brands is always defined
}

// Handle sorting
$sortOption = $_POST['sort'] ?? 'random'; // Default to 'random'
$filterQuery = "SELECT p.Product_ID, p.Name, p.Price, p.Image_Path,
                GROUP_CONCAT(i.image_path SEPARATOR ',') AS image_paths,
                COUNT(oi.Product_ID) AS order_count
                FROM products p 
                INNER JOIN product_images i ON p.Product_ID = i.Product_ID 
                LEFT JOIN order_items oi ON p.Product_ID = oi.Product_ID
                WHERE p.Product_ID IN (
                    SELECT Product_ID 
                    FROM products 
                    WHERE 1=1";

// Check if categories are selected, if not, do not filter by category
if (!empty($_POST['categories']) && $_POST['categories'][0] != '') {
    $categoriesFilter = implode(',', array_map('intval', $_POST['categories']));
    $filterQuery .= " AND Category_ID IN ($categoriesFilter)";
}

// Check if brands are selected, if not, do not filter by brand
if (!empty($_POST['brands']) && $_POST['brands'][0] != '') {
    $brandsFilter = implode(',', array_map('intval', $_POST['brands']));
    $filterQuery .= " AND Brand_ID IN ($brandsFilter)";
}

// Check if search term is provided
if (!empty($_POST['search'])) {
    $searchTerm = htmlspecialchars($_POST['search']);

    // Split the search term into individual words
    $searchWords = explode(' ', $searchTerm);

    // Initialize the search condition
    $searchCondition = '';

    // Loop through each word and add it to the search condition
    foreach ($searchWords as $word) {
        if (!empty($word)) {
            if (!empty($searchCondition)) {
                $searchCondition .= " OR ";
            }
            $searchCondition .= "(p.Name LIKE '%$word%' OR p.Description LIKE '%$word%')";
        }
    }

    // Add the search condition to the filter query
    if (!empty($searchCondition)) {
        $filterQuery .= " AND ($searchCondition)";
    }
}

// Price Range Filters
if (!empty($_POST['priceMin'])) {
    $priceMin = floatval($_POST['priceMin']);
    $filterQuery .= " AND Price >= $priceMin";
}

if (!empty($_POST['priceMax'])) {
    $priceMax = floatval($_POST['priceMax']);
    $filterQuery .= " AND Price <= $priceMax";
}

// Check if filter is set in the query string
$filter = $_GET['filter'] ?? null;
if ($filter === 'new' || $sortOption === 'latest') {
    $filterQuery .= " AND p.is_latest = 1"; // Show only latest products
} elseif ($filter === 'popular' || $sortOption === 'popular') {
    $filterQuery .= " AND p.is_popular = 1"; // Show only popular products
}

$filterQuery .= ") GROUP BY p.Product_ID";

// Apply sorting
if ($sortOption === 'popular') {
    $filterQuery .= " ORDER BY order_count DESC";
} elseif ($sortOption === 'random') {
    $filterQuery .= " ORDER BY RAND()";
} else {
    $filterQuery .= " ORDER BY p.created_at DESC";
}

// Fetch filtered products
$stmt = $conn->query($filterQuery);

if ($stmt && $stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $products[] = $row;
    }
} else {
    $noProductsFound = true;
}

// Pagination logic
$productsPerPage = 12;
$totalProducts = count($products);
$totalPages = ceil($totalProducts / $productsPerPage);
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$startIndex = ($currentPage - 1) * $productsPerPage;
$paginatedProducts = array_slice($products, $startIndex, $productsPerPage);


// Check if the add to wishlist button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_wishlist'])) {
    // Get the product ID from the form
    $productID = $_POST['product_id'];

    // Check if the product ID exists in the products table
    $productQuery = "SELECT * FROM products WHERE Product_ID = ?";
    $stmt = $conn->prepare($productQuery);
    $stmt->execute([$productID]);
    $productExists = $stmt->rowCount() > 0;

    if ($productExists) {
        // Check if the customer is logged in
        if (isset($_SESSION['customer_id'])) {
            // Check if the product is already in the wishlist
            $wishlistQuery = "SELECT * FROM favourites WHERE Customer_ID = ? AND Product_ID = ?";
            $stmt = $conn->prepare($wishlistQuery);
            $stmt->execute([$_SESSION['customer_id'], $productID]);
            $wishlistExists = $stmt->rowCount() > 0;

            if (!$wishlistExists) {
                // Add the product to the wishlist
                $addWishlistQuery = "INSERT INTO favourites (Customer_ID, Product_ID) VALUES (?, ?)";
                $stmt = $conn->prepare($addWishlistQuery);
                $stmt->execute([$_SESSION['customer_id'], $productID]);
                echo "<script>alert('Product added to wishlist successfully!');</script>";
            } else {
                echo "<script>alert('Product is already in your wishlist.');</script>";
            }
        } else {
            echo "<script>alert('Please log in to add products to your wishlist.');</script>";
            echo "<script>window.location.href = 'user_login.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Invalid product ID.');</script>";
    }

    // Update the wishlist dropdown content
    $wishlistQuery = "SELECT p.Name, p.Price, p.Product_ID, MIN(pi.Image_Path) AS Image_Path
                    FROM favourites f 
                    JOIN products p ON f.Product_ID = p.Product_ID 
                    LEFT JOIN product_images pi ON p.Product_ID = pi.Product_ID 
                    WHERE f.Customer_ID = ?
                    GROUP BY p.Product_ID, p.Name, p.Price";
    $stmt = $conn->prepare($wishlistQuery);
    $customerId = $_SESSION['customer_id'] ?? null;
    $stmt->execute([$customerId]);
    $wishlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Update the wishlist dropdown content
    echo "<script>
    var wishlistDropdown = document.getElementById('wishlistDropdown');
    wishlistDropdown.innerHTML = '';
    wishlistDropdown.innerHTML += '<h6 class=\"dropdown-header\">Your Wishlist</h6>';
    if (" . count($wishlistItems) . " > 0) {
      " . implode('', array_map(function ($item) {
        return "wishlistDropdown.innerHTML += '<div class=\"dropdown-item d-flex justify-content-between\"><span>" . htmlspecialchars($item['Name']) .
            "</span><span>$" . number_format($item['Price'], 2) . "</span></div>';";
    }, $wishlistItems)) . "
    } else {
      wishlistDropdown.innerHTML += '<div class=\"dropdown-item\">Wishlist is empty</div>';
    }
    wishlistDropdown.innerHTML += '<div class=\"dropdown-divider\"></div><a href=\"wishlist.php\" class=\"dropdown-item text-center\">View Wishlist</a>';
  </script>";

    // Update the wishlist quantity
    echo "<script>
    var wishlistQuantity = document.querySelector('.wishlist-quantity');
    wishlistQuantity.textContent = " . count($wishlistItems) . ";
  </script>";
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
    <title>Products - Cosmetics Shop</title>
    <style>
        .products-display {
            /* min-height: 100vh; */
            display: grid;
            grid-template-rows: auto 1fr auto;
            /* margin-bottom: 100px; */
            flex: 1;
        }

        .pagination-container {
            margin-top: auto;
        }

        .pagination .page-item .page-link {
            color: #000;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }

        .card1 {
            background-color: white;
        }

        @media (max-width: 768px) {
            .filter-section {
                margin-bottom: 20px;
            }

            .products-display {
                margin-top: 20px;
            }

            .pagination-container {
                margin-top: 20px;
            }
        }
    </style>
</head>

<body>
    <?php include 'navbar.php' ?>

    <div class="container mt-3 mb-5">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="user_homeIndex.php" style="color: black; text-decoration:none;">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Shop</li>
            </ol>
        </nav>
        <div class="row">
            <!-- Search Bar -->
            <div class="col-12">
                <form method="POST" action="">
                    <div class="mb-3">
                        <div class="d-flex">
                            <input type="text" name="search" value="<?= htmlspecialchars($_POST['search'] ?? ''); ?>" class="form-control me-2 shop-search-input" placeholder="Search by product name" style="flex-grow: 1;">
                            <button type="submit" name="search_button" class="btn btn-secondary">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <button type="button" class="shop-filter-toggle" aria-controls="shopFilterPanel" aria-expanded="false" aria-label="Open product filters">
                <i class="fas fa-sliders-h" aria-hidden="true"></i>
            </button>
            <div class="shop-filter-backdrop" data-filter-close aria-hidden="true"></div>

            <!-- Sidebar for Filters -->
            <div class="col-md-3 shop-filter-column">
                <div class="filter-section mb-5" id="shopFilterPanel" aria-label="Product filters">
                    <div class="shop-filter-header">
                        <h5>Filter Products</h5>
                        <button type="button" class="shop-filter-close" data-filter-close aria-label="Close product filters">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                    <form method="POST" action="">
                        <!-- Show Dropdown -->
                        <div class="mb-3">
                            <label for="sort" class="form-label">Show by:</label>
                            <select name="sort" id="sort" class="form-select" onchange="this.form.submit()">
                                <option value="latest" <?= $sortOption === 'latest' ? 'selected' : ''; ?>>Latest</option>
                                <option value="popular" <?= $sortOption === 'popular' ? 'selected' : ''; ?>>Popular</option>
                                <option value="random" <?= $sortOption === 'random' ? 'selected' : ''; ?>>Random</option>
                            </select>
                        </div>
                        <!-- Categories Filter -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="categories" class="form-label">Categories:</label>
                                <button type="button" class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#categoriesCollapse" style="max-height: 200px;">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                            <div id="categoriesCollapse" class="product-collapse show">
                                <div class="form-check-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="categories[]" value="" id="allCategories" <?= empty($_POST['categories']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="allCategories">All</label>
                                    </div>
                                    <?php foreach ($categories as $category): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="categories[]" value="<?= htmlspecialchars($category['Category_ID']); ?>"
                                                id="category<?= htmlspecialchars($category['Category_ID']); ?>"
                                                <?= in_array($category['Category_ID'], $_POST['categories'] ?? []) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="category<?= htmlspecialchars($category['Category_ID']); ?>">
                                                <?= htmlspecialchars($category['Category_Name']); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <!-- Brands Filter -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="brands" class="form-label">Brands:</label>
                                <button type="button" class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#brandsCollapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                            <div id="brandsCollapse" class="product-collapse show">
                                <div class="form-check-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="brands[]" value="" id="allBrands" <?= empty($_POST['brands']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="allBrands">All</label>
                                    </div>
                                    <?php foreach ($brands as $brand): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="brands[]" value="<?= htmlspecialchars($brand['Brand_ID']); ?>" id="brand<?= htmlspecialchars($brand['Brand_ID']); ?>" <?= in_array($brand['Brand_ID'], $_POST['brands'] ?? []) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="brand<?= htmlspecialchars($brand['Brand_ID']); ?>">
                                                <?= htmlspecialchars($brand['Brand_Name']); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <!-- Price Range Filter -->
                        <div class="mb-3">
                            <label for="priceMin" class="form-label">Price Min:</label>
                            <input type="number" name="priceMin" value="<?= htmlspecialchars($_POST['priceMin'] ?? ''); ?>" class="form-control" step="0.01">
                        </div>

                        <div class="mb-3">
                            <label for="priceMax" class="form-label">Price Max:</label>
                            <input type="number" name="priceMax" value="<?= htmlspecialchars($_POST['priceMax'] ?? ''); ?>" class="form-control" step="0.01">
                        </div>
                        <!-- Apply Filters Button -->
                        <button type="submit" name="apply_filters" class="btn btn-primary" onclick="document.getElementsByName('search')[0].value = '';">Apply Filters</button>
                    </form>
                </div>
            </div>

            <!-- Products Display -->
            <div class="col-md-9 products-display display-flex">
                <h3 class="mb-4">Products</h3>
                <div class="row">
                    <?php if (!empty($paginatedProducts)): ?>
                        <div class="row justify-content-center g-4 shop-products-grid">
                            <?php foreach ($paginatedProducts as $product): ?>
                                <?php
                                $imageArray = !empty($product['image_paths']) ? explode(',', $product['image_paths']) : [];
                                $productID = $product['Product_ID'];
                                // Fetch average rating
                                $avgRatingQuery = "SELECT AVG(Rating) AS avg_rating, COUNT(Rating) AS total_reviews FROM reviews WHERE Product_ID = ?";
                                $stmt = $conn->prepare($avgRatingQuery);
                                $stmt->execute([$productID]);
                                $avgRatingResult = $stmt->fetch(PDO::FETCH_ASSOC);
                                $avgRating = $avgRatingResult['avg_rating'] ? round($avgRatingResult['avg_rating'], 1) : 0;
                                $totalReviews = $avgRatingResult['total_reviews'];

                                // Fetch shades
                                $shadesQuery = "SELECT shade_name FROM shades WHERE product_id = ?";
                                $stmt = $conn->prepare($shadesQuery);
                                $stmt->execute([$productID]);
                                $shades = $stmt->fetchAll(PDO::FETCH_COLUMN);

                                // Add the main product image to the beginning of the images array
                                if (!empty($product['Image_Path'])) {
                                    array_unshift($imageArray, $product['Image_Path']);
                                }
                                ?>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4 d-flex align-items-stretch">
                                    <div class="card1 border-0 shadow-sm rounded position-relative" style="width: 100%; height: 350px;">
                                        <div class="card-image-wrapper" style="position: relative;">
                                            <div class="image-buttons">
                                                <form method="post" action="products.php">
                                                    <input type="hidden" name="product_id" value="<?php echo $product['Product_ID']; ?>">
                                                    <button name="add_to_wishlist" class="btn btn-light btn-circle shadow"><i class="fas fa-heart"></i></button>
                                                </form>
                                                <a href="viewDetails.php?id=<?php echo $product['Product_ID']; ?>" style="text-decoration: none;">
                                                    <button class="btn btn-light btn-circle shadow"><i class="fas fa-eye"></i></button>
                                                </a>
                                            </div>
                                            <img src="<?php echo $product['Image_Path']; ?>" class="d-block w-100 rounded-top product-image main-image" alt="<?php echo $product['Name']; ?>" style="height: 200px; object-fit: contain;">
                                            <div id="carousel-<?php echo $product['Product_ID']; ?>" class="carousel slide card-carousel" data-bs-ride="carousel">
                                                <div class="carousel-inner">
                                                    <?php foreach ($imageArray as $index => $image): ?>
                                                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                                            <img src="<?php echo $image; ?>" class="d-block w-100 rounded-top" alt="<?php echo $product['Name']; ?>" style="height: 200px; object-fit: contain;">
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $product['Product_ID']; ?>" data-bs-slide="prev">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                    <span class="visually-hidden">Previous</span>
                                                </button>
                                                <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $product['Product_ID']; ?>" data-bs-slide="next">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                    <span class="visually-hidden">Next</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body text-center">
                                            <span class="card-title fw-bold text-truncate"><?php echo $product['Name']; ?></span>
                                            <div class="mt-2">
                                                <span class="text-warning">
                                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                                        <i class="fa<?php echo $i < $avgRating ? 's' : 'r'; ?> fa-star"></i>
                                                    <?php endfor; ?>
                                                </span>
                                                <p class="card-text text-muted"><?php echo $avgRating; ?> stars (<?php echo $totalReviews; ?> reviews)</p>
                                                <span class="text">$<?php echo $product['Price']; ?></span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="mt-4">No products found for the selected filters.</p>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <div class="pagination-container mt-3">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-center mt-4 mb-5">
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $currentPage - 1; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $currentPage ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $currentPage + 1; ?>" aria-label="Next">
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

    <?php include 'footer.php'; ?>




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoryToggle = document.querySelector('[data-bs-target="#categoriesCollapse"]');
            const brandToggle = document.querySelector('[data-bs-target="#brandsCollapse"]');
            const filterToggle = document.querySelector('.shop-filter-toggle');
            const filterPanel = document.querySelector('#shopFilterPanel');
            const filterBackdrop = document.querySelector('.shop-filter-backdrop');
            const filterCloseButtons = document.querySelectorAll('[data-filter-close]');

            categoryToggle.addEventListener('click', function() {
                const icon = categoryToggle.querySelector('i');
                icon.classList.toggle('fa-plus');
                icon.classList.toggle('fa-minus');
            });

            brandToggle.addEventListener('click', function() {
                const icon = brandToggle.querySelector('i');
                icon.classList.toggle('fa-plus');
                icon.classList.toggle('fa-minus');
            });

            function closeFilters() {
                document.body.classList.remove('shop-filter-open');
                filterToggle?.setAttribute('aria-expanded', 'false');
            }

            function openFilters() {
                document.body.classList.add('shop-filter-open');
                filterToggle?.setAttribute('aria-expanded', 'true');
                filterPanel?.querySelector('select, input, button')?.focus({ preventScroll: true });
            }

            filterToggle?.addEventListener('click', function() {
                if (document.body.classList.contains('shop-filter-open')) {
                    closeFilters();
                } else {
                    openFilters();
                }
            });

            filterCloseButtons.forEach(function(button) {
                button.addEventListener('click', closeFilters);
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeFilters();
                }
            });

            window.addEventListener('resize', function() {
                if (window.matchMedia('(min-width: 992px)').matches) {
                    closeFilters();
                }
            });
        });
    </script>

    <style>
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


</body>

</html>
