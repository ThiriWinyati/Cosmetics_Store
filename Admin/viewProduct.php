<?php
session_start();
require_once "../db_connect.php";
require_once "admin_auth.php";

$isAdmin = isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true;

try {
    //to get categories
    $sql = "select * from categories";
    $stmt = $conn->query($sql);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //to get brands
    $sql = "select * from brands";
    $stmt = $conn->query($sql);
    $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //to get admins
    $sql = "select * from admin_users";
    $stmt = $conn->query($sql);
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['search'])) {
    admin_require_login('viewProduct.php');
}

// Fetch products
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $searchTerm = $_POST['searchTerm'];
    $productsQuery = "SELECT 
        p.Product_ID, 
        p.Name, 
        c.Category_Name AS categories, 
        p.Price, 
        a.Name AS admin_users, 
        b.brand_name AS brands, 
        p.Description, 
        p.created_at,
        p.is_latest AS is_latest_column, 
        p.is_popular AS is_popular_column, 
        GROUP_CONCAT(DISTINCT pi.image_path) AS images,
        GROUP_CONCAT(DISTINCT ps.shade_name) AS shades,
        GROUP_CONCAT(DISTINCT ps.Quantity ORDER BY ps.shade_name ASC SEPARATOR ', ') AS quantities
    FROM products p
    LEFT JOIN categories c ON p.Category_ID = c.Category_ID
    LEFT JOIN admin_users a ON p.Admin_User_ID = a.Admin_User_ID
    LEFT JOIN brands b ON p.brand_id = b.brand_id
    LEFT JOIN product_images pi ON pi.Product_ID = p.Product_ID
    LEFT JOIN shades ps ON ps.product_id = p.Product_ID
    WHERE p.Name LIKE :searchTerm
       OR c.Category_Name LIKE :searchTerm
       OR b.brand_name LIKE :searchTerm
       OR p.Description LIKE :searchTerm
       OR a.Name LIKE :searchTerm
       OR ps.shade_name LIKE :searchTerm
    GROUP BY p.Product_ID
    ORDER BY p.Product_ID";
    $productsStmt = $conn->prepare($productsQuery);
    $productsStmt->execute(['searchTerm' => '%' . $searchTerm . '%']);
    $products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    try {
        $productsQuery = "SELECT 
        p.Product_ID, 
        p.Name, 
        c.Category_Name AS categories, 
        p.Price, 
        a.Name AS admin_users, 
        b.brand_name AS brands, 
        p.Description, 
        p.created_at,
        p.is_latest AS is_latest_column,  
        p.is_popular AS is_popular_column, 
        GROUP_CONCAT(DISTINCT pi.image_path) AS images,
        GROUP_CONCAT(DISTINCT ps.shade_name) AS shades,
        GROUP_CONCAT(DISTINCT ps.Quantity ORDER BY ps.shade_name ASC SEPARATOR ', ') AS quantities
    FROM products p
    LEFT JOIN categories c ON p.Category_ID = c.Category_ID
    LEFT JOIN admin_users a ON p.Admin_User_ID = a.Admin_User_ID
    LEFT JOIN brands b ON p.brand_id = b.brand_id
    LEFT JOIN product_images pi ON pi.Product_ID = p.Product_ID
    LEFT JOIN shades ps ON ps.product_id = p.Product_ID
    GROUP BY p.Product_ID
    ORDER BY p.Product_ID;";
        $productsStmt = $conn->prepare($productsQuery);
        $productsStmt->execute();
        $products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching products: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['search'])) {
    // Product Details
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $admin = $_POST['admin'];
    $brand = $_POST['brand'];
    $shade = $_POST['shade_names'];
    $is_latest = isset($_POST['is_latest']) ? 1 : 0;
    $uploadedImages = $_FILES['shade_images'];

    // Image Uploads
    $imagePaths = [];
    if (isset($_FILES['shade_images'])) {
        $uploadedImages = $_FILES['shade_images'];

        for ($i = 0; $i < count($uploadedImages['name']); $i++) {
            $imageFilename = basename($uploadedImages['name'][$i]);
            $imageUploadPath = "../uploads/products/" . $imageFilename;

            if (move_uploaded_file($uploadedImages['tmp_name'][$i], $imageUploadPath)) {
                $imagePaths[] = $imageUploadPath;
            } else {
                echo "Failed to upload image: " . $imageFilename;
            }
        }
    }

    try {
        // Insert product details into the products table
        $sql = "INSERT INTO products (Name, Category_ID, Price, Admin_User_ID, Brand_ID, Description, is_latest)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $status = $stmt->execute([$name, $category, $price, $admin, $brand, $description, $is_latest]);
        // Get the last inserted product ID
        $Product_ID = $conn->lastInsertId();

        // Insert shades into shades table and get shade_id for each shade
        $shade_ids = []; // Array to store shade_id for each shade
        foreach ($shade as $shade_name) {
            $sql1 = "INSERT INTO shades (product_id, shade_name) VALUES (?, ?)";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->execute([$Product_ID, $shade_name]);

            // Get the last inserted shade_id for the current shade
            $shade_id = $conn->lastInsertId();
            $shade_ids[] = $shade_id;  // Store the shade_id for later use
        }

        // Insert images into product_images table, linking them with the correct shade_id
        $sql2 = "INSERT INTO product_images (product_id, image_path, shade_id) VALUES (?, ?, ?)";
        $stmt2 = $conn->prepare($sql2);

        $imageIndex = 0; // Initialize image index for shades
        foreach ($imagePaths as $path) {
            // Use the corresponding shade_id for each image (based on the order)
            $stmt2->execute([$Product_ID, $path, $shade_ids[$imageIndex]]);
            $imageIndex++;
        }

        if ($status) {
            $_SESSION['insertProductSuccess'] = "Product with ID $productId has been inserted successfully";
            header("Location:viewProduct.php");
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}




$totalProducts = count($products ?? []);
$latestProducts = 0;
$popularProducts = 0;

foreach ($products ?? [] as $productSummary) {
    if (($productSummary['is_latest_column'] ?? 0) == 1) {
        $latestProducts++;
    }

    if (($productSummary['is_popular_column'] ?? 0) == 1) {
        $popularProducts++;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../Admin/admin_css/style.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../Admin/admin_Javascript/sidebar.js"></script>
    <script src="../Admin/admin_Javascript/insert.js"></script>

    <link rel="icon" href="path/to/favicon.ico">
    <title>Products</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .container {
            margin: 0 auto;
            padding: 20px;
        }

        h2 {
            color: #d97cb3;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #d97cb3;
            border-color: #d97cb3;
            color: #fff;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #c2185b;
            border-color: #c2185b;
        }

        .btn-primary a {
            color: #fff;
            text-decoration: none;
        }

        .btn-primary a:hover {
            color: #fff;
            text-decoration: none;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 10px;
        }

        .input-group .btn-primary {
            border-radius: 8px;
            padding: 10px 20px;
        }

        .table-container {
            max-height: 500px;
            overflow-y: auto;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
            border-radius: 12px;
            overflow: hidden;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
            text-align: center;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
            background-color: rgb(191, 132, 166);
            color: #fff;
        }

        .table tbody+tbody {
            border-top: 2px solid #dee2e6;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .btn-link {
            color: #d97cb3;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .btn-link:hover {
            color: #c2185b;
            text-decoration: underline;
        }

        .btn-link:focus,
        .btn-link:active {
            color: #c2185b;
            text-decoration: underline;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            filter: invert(1);
        }

        .carousel-control-prev-icon:hover,
        .carousel-control-next-icon:hover {
            filter: invert(0.5);
        }

        .table th.shade-column,
        .table td.shade-column {
            width: 50px;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }

        .product-page-shell {
            padding-top: 28px;
        }

        .product-page-header {
            position: relative;
            top: auto;
            z-index: 35;
            padding: 14px 0 20px;
            margin-bottom: 22px;
            background: #ffffff;
            border: 0;
            box-shadow: none;
        }

        .product-page-header .admin-page-title,
        .product-page-header .admin-page-subtitle {
            text-align: center;
        }

        .product-page-header .admin-page-title {
            font-size: clamp(1.7rem, 2.4vw, 2.35rem);
        }

        .product-summary-grid {
            grid-template-columns: repeat(3, minmax(160px, 1fr));
            margin-top: 18px;
        }

        .product-stat-card {
            min-height: 108px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 8px;
            overflow: visible;
        }

        .product-stat-card .admin-stat-label,
        .product-stat-card .admin-stat-value {
            line-height: 1.15;
        }

        .product-page-actions {
            display: flex;
            justify-content: center;
            margin-top: 16px;
            margin-bottom: 0;
        }

        .product-page-search {
            margin-top: 14px;
        }

        .product-page-search .input-group {
            flex-wrap: nowrap;
        }

        .product-page-search .admin-search-input {
            min-width: 0;
        }

        #viewProductsTable {
            min-width: 1180px;
        }

        #viewProductsTable td {
            padding-top: 18px;
            padding-bottom: 18px;
        }

        #viewProductsTable .shade-list-cell {
            max-width: 360px;
            white-space: normal;
            line-height: 1.45;
        }

        #viewProductsTable .shade-list-content {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        @media (max-width: 992px) {
            .product-summary-grid {
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .product-page-header {
                top: auto;
            }

            .product-page-actions .btn {
                width: 100%;
            }
        }

        html[data-theme="dark"] .product-page-header {
            background: #111113;
        }
    </style>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>


    <div class="admin-page-shell product-page-shell">
        <section class="product-page-header">
            <div>
                <h2 class="admin-page-title">View Products</h2>
                <p class="admin-page-subtitle">Manage product catalog visibility, stock by shade, brands, and product flags.</p>
            </div>

            <div class="admin-summary-grid product-summary-grid">
                <div class="admin-stat-card product-stat-card">
                    <span class="admin-stat-label">Visible Products</span>
                    <span class="admin-stat-value"><?php echo $totalProducts; ?></span>
                </div>
                <div class="admin-stat-card product-stat-card">
                    <span class="admin-stat-label">Latest</span>
                    <span class="admin-stat-value"><?php echo $latestProducts; ?></span>
                </div>
                <div class="admin-stat-card product-stat-card">
                    <span class="admin-stat-label">Popular</span>
                    <span class="admin-stat-value"><?php echo $popularProducts; ?></span>
                </div>
            </div>

            <?php if (!$isAdmin): ?>
                <div class="alert alert-warning admin-preview-alert">
                    <i class="fa fa-lock"></i>
                    Product editing and deletion are locked in portfolio preview mode.
                </div>
            <?php endif; ?>

            <div class="admin-toolbar product-page-search">
                <form method="POST" action="viewProduct.php" class="w-100">
                    <div class="input-group">
                        <input type="text" name="searchTerm" class="form-control admin-search-input" placeholder="Search products, brands, categories, or shades..." value="<?php echo htmlspecialchars($_POST['searchTerm'] ?? ''); ?>" required>
                        <button type="submit" name="search" class="admin-search-button px-4">Search</button>
                    </div>
                </form>
            </div>

        </section>

        <div class="admin-table-action-row product-page-actions">
            <?php if ($isAdmin): ?>
                <a href="insertProduct.php" class="btn btn-outline-primary text-decoration-none">
                    <i class="fa fa-plus"></i> Insert Product
                </a>
            <?php else: ?>
                <button type="button" class="btn btn-secondary" disabled title="Admin login required">
                    <i class="fa fa-lock"></i> Insert locked
                </button>
            <?php endif; ?>
        </div>

        <div class="admin-table-card">
            <div class="admin-table-scroll">
            <table class="table table-hover admin-data-table" id="viewProductsTable">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Brand</th>
                        <th class="shade-column">Shades</th>
                        <th>Quantities</th>
                        <th>Latest</th>
                        <th>Popular</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="products">
                    <?php
                        if (isset($products) && !empty($products)) {
                            foreach ($products as $product) {
                                $productID = htmlspecialchars($product['Product_ID']);
                                $productName = htmlspecialchars($product['Name'] ?? '');
                                $categoryName = htmlspecialchars($product['categories'] ?? '');
                                $price = htmlspecialchars($product['Price'] ?? '');
                                $brandName = htmlspecialchars($product['brands'] ?? '');
                                $shades = htmlspecialchars($product['shades'] ?? '');
                                $quantities = htmlspecialchars($product['quantities'] ?? '');
                                $isLatest = ($product['is_latest_column'] == 1) ? 'Yes' : 'No';
                                $isPopular = ($product['is_popular_column'] == 1) ? 'Yes' : 'No';

                                echo "
                                <tr>
                                    <td><span class='admin-row-title'>#{$productID}</span></td>
                                    <td>
                                        <span class='admin-row-title'>{$productName}</span>
                                        <div class='admin-muted-text'>{$brandName}</div>
                                    </td>
                                    <td>{$categoryName}</td>
                                    <td>$ {$price}</td>
                                    <td>{$brandName}</td>
                                    <td class='shade-list-cell'><div class='shade-list-content'>{$shades}</div></td>
                                    <td>{$quantities}</td>
                                    <td><span class='admin-status-badge " . (($product['is_latest_column'] == 1) ? "status-delivered" : "status-locked") . "'>{$isLatest}</span></td>
                                    <td><span class='admin-status-badge " . (($product['is_popular_column'] == 1) ? "status-delivered" : "status-locked") . "'>{$isPopular}</span></td>
                                    <td><span class='admin-action-group'>";

                                if ($isAdmin) {
                                    echo "
                                        <a href='editProduct.php?id={$productID}' class='btn btn-warning btn-sm admin-icon-btn text-decoration-none custom-edit'>
                                            <i class='fa fa-pencil-alt'></i> Edit
                                        </a>

                                        <button class='btn btn-danger btn-sm admin-icon-btn custom-delete' data-bs-toggle='modal' data-bs-target='#deleteProductModal{$productID}'>
                                            <i class='fa fa-trash'></i> Delete
                                        </button>";
                                } else {
                                    echo "
                                        <button class='btn btn-secondary btn-sm' disabled title='Admin login required'>
                                            <i class='fa fa-lock'></i> Edit locked
                                        </button>

                                        <button class='btn btn-secondary btn-sm' disabled title='Admin login required'>
                                            <i class='fa fa-lock'></i> Delete locked
                                        </button>";
                                }

                                echo "
                                        </span>
                                    </td>
                                </tr>";

                                if ($isAdmin) {
                                    echo "
                                    <div class='modal fade' id='deleteProductModal{$productID}' tabindex='-1' aria-labelledby='deleteProductModalLabel{$productID}' aria-hidden='true'>
                                        <div class='modal-dialog'>
                                            <div class='modal-content'>
                                                <div class='modal-header'>
                                                    <h5 class='modal-title' id='deleteProductModalLabel{$productID}'>Delete Product</h5>
                                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                </div>

                                                <div class='modal-body'>
                                                    Are you sure you want to delete this product?
                                                </div>

                                                <div class='modal-footer'>
                                                    <form action='deleteProduct.php' method='GET'>
                                                        <input type='hidden' name='id' value='{$productID}'>
                                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                                        <button type='submit' class='btn btn-danger'>Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>";
                                }
                            }
                        } else {
                            echo "
                            <tr>
                                <td colspan='10' class='text-center'>No products found.</td>
                            </tr>";
                        }
                        ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</body>

</html>
