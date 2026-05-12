<?php
session_start();
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

if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    // If not logged in, redirect to login page
    echo "<script>alert('Please log in as an admin.');</script>";
    echo "<script>window.location.href = 'adminLogin.php';</script>";
    exit();
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
    </style>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>


    <!-- View Products -->
    <div class="container" style="overflow-x:auto;">
        <a href="viewProduct.php" class="text-decoration-none">
            <h2 class="text-center mb-4">View Products</h2>
        </a>
        <form method="POST" action="viewProduct.php" class="mb-3">
            <div class="input-group">
                <input type="text" name="searchTerm" class="form-control" placeholder="Search for products..." required>
                <button type="submit" name="search" class="btn btn-dark">Search</button>
            </div>
        </form>
        <div class="text-end mb-3">
            <button type="button" class="btn btn-outline-primary">
                <a href="insertProduct.php" style="text-decoration: none;"><i class="fa fa-plus"></i> Insert Product</a>
            </button>
        </div>

        <div class="table-container">
            <table class="table table-hover" id="viewProductsTable">
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
                    if (isset($products) && isset($_SESSION['isLoggedIn'])) {
                        foreach ($products as $product) {
                            echo "<tr>
                                <td>{$product['Product_ID']}</td>
                                <td>{$product['Name']}</td>
                                <td>{$product['categories']}</td>
                                <td>{$product['Price']}</td>
                                <td>{$product['brands']}</td>
                                <td>{$product['shades']}</td>
                                <td>{$product['quantities']}</td>
                            
                                <td>" . ($product['is_latest_column'] == 1 ? 'Yes' : 'No') . "</td>
                                <td>" . ($product['is_popular_column'] == 1 ? 'Yes' : 'No') . "</td> <!-- New column for Is Popular -->
                                <td>
                                    <a href='editProduct.php?id={$product['Product_ID']}' class='btn btn-link text-decoration-none custom-edit'><i class='fa fa-pencil-alt'></i> </a>
                                    <button class='btn btn-link text-decoration-none custom-delete' data-bs-toggle='modal' data-bs-target='#deleteProductModal{$product['Product_ID']}'><i class='fa fa-trash'></i></button>
                                </td>
                                </tr>";

                            // Delete Modal
                            echo "<div class='modal fade' id='deleteProductModal{$product['Product_ID']}' tabindex='-1' aria-labelledby='deleteProductModalLabel' aria-hidden='true'>
                                    <div class='modal-dialog'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title' id='deleteProductModalLabel'>Delete Product</h5>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                            </div>
                                            <div class='modal-body'>
                                                Are you sure you want to delete this product?
                                            </div>
                                            <div class='modal-footer'>
                                                <form action='deleteProduct.php' method='GET'>
                                                    <input type='hidden' name='id' value='{$product['Product_ID']}'>
                                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                                    <button type='submit' class='btn btn-danger'>Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>