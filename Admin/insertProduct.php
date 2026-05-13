<?php
require_once "../db_connect.php";
require_once "admin_auth.php";

if (!isset($_SESSION)) {
    session_start();
}

admin_require_login('viewProduct.php');

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

if (isset($_POST['insert'])) {
    // Product Details
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $admin = $_POST['admin'];
    $brand = $_POST['brand'];
    $shades = $_POST['shade_names'];
    $shadeQuantities = $_POST['shade_quantities'];
    $is_latest = $_POST['is_latest'];
    $uploadedImages = $_FILES['shade_images'];
    $productImage = $_FILES['product_image'];

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

    // Upload product image
    $productImagePath = "";
    if (isset($productImage) && $productImage['error'] == 0) {
        $productImageFilename = basename($productImage['name']);
        $productImageUploadPath = "../uploads/products/" . $productImageFilename;

        if (move_uploaded_file($productImage['tmp_name'], $productImageUploadPath)) {
            $productImagePath = $productImageUploadPath;
        } else {
            echo "Failed to upload product image: " . $productImageFilename;
        }
    }

    try {
        // Insert product details into the products table
        $sql = "INSERT INTO products (Name, Category_ID, Price, Admin_User_ID, Brand_ID, Description, is_latest, Image_Path)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $status = $stmt->execute([$name, $category, $price, $admin, $brand, $description, $is_latest, $productImagePath]);
        // Get the last inserted product ID
        $Product_ID = $conn->lastInsertId();

        // Insert shades into shades table and get shade_id for each shade
        $shade_ids = []; // Array to store shade_id for each shade
        foreach ($shades as $index => $shade_name) {
            $shade_quantity = $shadeQuantities[$index]; // Get the corresponding shade quantity
            $sql1 = "INSERT INTO shades (product_id, shade_name, Quantity) VALUES (?, ?, ?)";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->execute([$Product_ID, $shade_name, $shade_quantity]);

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
            $_SESSION['insertProductSuccess'] = "Product with ID $Product_ID has been inserted successfully";
            header("Location:viewProduct.php");
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}



?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <script src="../Admin/admin_Javascript/insert.js"></script>
    <script src="../Admin/admin_Javascript/sidebar.js"></script>
    <link rel="icon" href="path/to/favicon.ico">
    <title>Insert Product</title>
    <style>
        .form-check-input:checked {
            background-color: #d97cb3;
            border-color: #d97cb3;
        }

        .form-check-input:focus {
            border-color: #d97cb3;
            box-shadow: 0 0 0 0.25rem rgba(217, 124, 179, 0.25);
        }

        .form-check-label {
            color: #d97cb3;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>


    <!-- Main Content -->
    <div id="main-content">
        <div class="container mt-2">
            <div class="mb-3">
                <a href="../Admin/viewProduct.php" class="btn btn-secondary">Go back</a>
            </div>

            <h2 class="text-center mb-4">Add New Product</h2>
            <form id="addProductForm" action="insertProduct.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <!-- Product Name -->
                        <div class="mb-3">
                            <label for="productName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="productName" name="name" required>
                        </div>

                        <!-- Product Image -->
                        <div class="mb-3">
                            <label for="productImage" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="productImage" name="product_image" required>
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option selected disabled>Select a category</option>
                                <?php
                                if (isset($categories)) {
                                    foreach ($categories as $category) {
                                        echo "<option value='$category[Category_ID]'>$category[Category_Name]</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Price -->
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                        </div>

                        <!-- Is Latest -->
                        <div class="mb-3">
                            <label for="isLatest" class="form-label">Is Latest Product?</label>
                            <select class="form-select" id="isLatest" name="is_latest" required>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>

                        <!-- Is Popular -->
                        <div class="mb-3">
                            <label for="isPopular" class="form-label">Is Popular Product?</label>
                            <select class="form-select" id="isPopular" name="is_popular" required>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>

                        <!-- Number of Shades -->
                        <div class="mb-3">
                            <label for="shadeCount" class="form-label">Number of Shades</label>
                            <input type="number" class="form-control" id="shadeCount" name="shade_count" min="1" max="100" required>
                        </div>


                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <!-- Brand -->
                        <div class="mb-3">
                            <label for="brand" class="form-label">Brand</label>
                            <select class="form-select" id="brand" name="brand" required>
                                <option selected disabled>Select a brand</option>
                                <?php
                                if (isset($brands)) {
                                    foreach ($brands as $brand) {
                                        echo "<option value='$brand[brand_id]'>$brand[brand_name]</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>


                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Product Description</label>
                            <textarea id="description" name="description" class="form-control" rows="3" placeholder="Enter product description"></textarea>
                        </div>

                        <!-- Admin -->
                        <div class="mb-3">
                            <label for="admin" class="form-label">Admin</label>
                            <select class="form-select" id="admin" name="admin" required>
                                <option selected disabled>Select Admin</option>
                                <?php
                                if (isset($admins)) {
                                    foreach ($admins as $admin) {
                                        echo "<option value='$admin[Admin_User_ID]'>$admin[Name]</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Shade Inputs -->
                <div class="mb-3">
                    <label class="form-label">Shade Details</label>
                    <div id="shadeInputs"></div>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary" name="insert">Add Product</button>
                </div>
            </form>
        </div>
    </div>
    </div>


    <script>
        document.getElementById('shadeCount').addEventListener('input', function() {
            var shadeCount = parseInt(this.value);
            var shadeInputs = document.getElementById('shadeInputs');
            shadeInputs.innerHTML = '';

            for (var i = 0; i < shadeCount; i++) {
                var shadeInput = document.createElement('div');
                shadeInput.className = 'mb-3';

                var shadeLabel = document.createElement('label');
                shadeLabel.className = 'form-label';
                shadeLabel.textContent = 'Shade ' + (i + 1) + ' Name';
                shadeInput.appendChild(shadeLabel);

                var shadeNameInput = document.createElement('input');
                shadeNameInput.type = 'text';
                shadeNameInput.className = 'form-control';
                shadeNameInput.name = 'shade_names[]';
                shadeNameInput.required = true;
                shadeInput.appendChild(shadeNameInput);

                var shadeQuantityLabel = document.createElement('label');
                shadeQuantityLabel.className = 'form-label';
                shadeQuantityLabel.textContent = 'Shade ' + (i + 1) + ' Quantity';
                shadeInput.appendChild(shadeQuantityLabel);

                var shadeQuantityInput = document.createElement('input');
                shadeQuantityInput.type = 'number';
                shadeQuantityInput.className = 'form-control';
                shadeQuantityInput.name = 'shade_quantities[]';
                shadeQuantityInput.required = true;
                shadeInput.appendChild(shadeQuantityInput);

                var shadeImageLabel = document.createElement('label');
                shadeImageLabel.className = 'form-label';
                shadeImageLabel.textContent = 'Shade ' + (i + 1) + ' Image';
                shadeInput.appendChild(shadeImageLabel);

                var shadeImageInput = document.createElement('input');
                shadeImageInput.type = 'file';
                shadeImageInput.className = 'form-control';
                shadeImageInput.name = 'shade_images[]';
                shadeImageInput.required = true;
                shadeInput.appendChild(shadeImageInput);

                shadeInputs.appendChild(shadeInput);
            }
        });
    </script>

</body>

</html>
