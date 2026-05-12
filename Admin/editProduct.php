<?php
require_once "../db_connect.php";

if (!isset($_SESSION)) {
    session_start();
}

try {
    // to get categories
    $sql = "SELECT * FROM categories";
    $stmt = $conn->query($sql);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // to get brands
    $sql = "SELECT * FROM brands";
    $stmt = $conn->query($sql);
    $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // to get admins
    $sql = "SELECT * FROM admin_users";
    $stmt = $conn->query($sql);
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}


if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    $product = getProductInfo($productId);
}

function getProductInfo($productId)
{
    global $conn;

    // Query for product details
    $sql = "SELECT 
                    p.Product_ID, p.Name AS productName, c.Category_ID AS categories, 
                    p.Price, a.Admin_User_ID AS admin_users, 
                    b.brand_id AS brands, p.Description, p.is_latest, p.is_popular, p.Image_Path
                FROM products p
                JOIN categories c ON p.Category_ID = c.Category_ID
                JOIN admin_users a ON p.Admin_User_ID = a.Admin_User_ID
                JOIN brands b ON p.brand_id = b.brand_id
                WHERE p.Product_ID=?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Query for product images and associated shades
    $imageSql = "SELECT pi.image_path, s.shade_name, pi.shade_id, s.Quantity
                     FROM product_images pi
                     JOIN shades s ON pi.shade_id = s.shade_id
                     WHERE pi.Product_ID = ?";

    try {
        $imageStmt = $conn->prepare($imageSql);
        $imageStmt->execute([$productId]);
        $images = $imageStmt->fetchAll(PDO::FETCH_ASSOC);

        // Structure the images and shades together
        $product['images'] = [];
        foreach ($images as $image) {
            $product['images'][] = [
                'image_path' => $image['image_path'],
                'shade_name' => $image['shade_name'],
                'shade_id' => $image['shade_id'],
                'quantity' => $image['Quantity']
            ];
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    return $product;
}

if (isset($_POST['update'])) {
    $productId = $_POST['Product_ID'];
    $productName = trim($_POST['productName']);
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $admin = $_POST['admin'];
    $brand = $_POST['brand'];
    $is_latest = $_POST['is_latest']; // Get the is_latest value
    $is_popular = $_POST['is_popular']; // Get the is_popular value
    $productImage = $_FILES['product_image']; // New product image

    // Check if shades are provided
    $shades = isset($_POST['shade_names']) ? $_POST['shade_names'] : [];
    $shadeQuantities = isset($_POST['shade_quantities']) ? $_POST['shade_quantities'] : []; // New shade quantities

    // Image upload logic
    $imagePaths = [];
    if (isset($_FILES['shade_images'])) {
        $uploadedImages = $_FILES['shade_images'];
        foreach ($uploadedImages['name'] as $index => $image) {
            $imageFilename = basename($image);
            $imageUploadPath = "../uploads/products/" . $imageFilename;

            if (move_uploaded_file($uploadedImages['tmp_name'][$index], $imageUploadPath)) {
                $imagePaths[$index] = $imageUploadPath; // Store the image path in the array
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

    // Start transaction for multiple updates
    $conn->beginTransaction();

    try {
        // Update product details
        $sql = "UPDATE products SET Name=?, Category_ID=?, Price=?, 
                     Admin_User_ID=?, brand_id=?, Description=?, is_latest=?, is_popular=?, Image_Path=? 
                    WHERE Product_ID=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$productName, $category, $price, $admin, $brand, $description, $is_latest, $is_popular, $productImagePath, $productId]);

        // Update existing shades and their quantities
        if (isset($_POST['existing_shade_ids'])) {
            foreach ($_POST['existing_shade_ids'] as $index => $shadeId) {
                $shadeQuantity = $_POST['existing_shade_quantities'][$index];
                $updateShadeSql = "UPDATE shades SET Quantity = ? WHERE shade_id = ?";
                $updateShadeStmt = $conn->prepare($updateShadeSql);
                $updateShadeStmt->execute([$shadeQuantity, $shadeId]);
            }
        }

        // Insert new shades and their images
        foreach ($shades as $index => $shadeName) {
            // Insert new shade
            $insertShadeSql = "INSERT INTO shades (product_id, shade_name, Quantity) VALUES (?, ?, ?)";
            $insertShadeStmt = $conn->prepare($insertShadeSql);
            $insertShadeStmt->execute([$productId, $shadeName, $shadeQuantities[$index]]);

            // Get the last inserted shade_id
            $shadeId = $conn->lastInsertId();

            // Insert new image if provided
            if (isset($imagePaths[$index])) {
                $insertImageSql = "INSERT INTO product_images (product_id, image_path, shade_id) VALUES (?, ?, ?)";
                $insertImageStmt = $conn->prepare($insertImageSql);
                $insertImageStmt->execute([$productId, $imagePaths[$index], $shadeId]);
            }
        }

        // Handle image and shade deletion
        if (isset($_POST['delete_images'])) {
            foreach ($_POST['delete_images'] as $image) {
                $filePath = "../uploads/" . $image;

                // Fetch the shade_id associated with the image
                $fetchShadeIdSql = "SELECT shade_id FROM product_images WHERE image_path=?";
                $fetchShadeIdStmt = $conn->prepare($fetchShadeIdSql);
                $fetchShadeIdStmt->execute([$image]);
                $shadeId = $fetchShadeIdStmt->fetchColumn();

                // Delete the shade from the shades table
                if ($shadeId) {
                    $deleteShadeSql = "DELETE FROM shades WHERE shade_id=?";
                    $deleteShadeStmt = $conn->prepare($deleteShadeSql);
                    $deleteShadeStmt->execute([$shadeId]);
                }

                // Delete the image file from the server
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                // Delete the image record from the product_images table
                $deleteImageSql = "DELETE FROM product_images WHERE image_path=?";
                $deleteImageStmt = $conn->prepare($deleteImageSql);
                $deleteImageStmt->execute([$image]);
            }
        }

        // Commit the transaction
        $conn->commit();

        $_SESSION['updateProductSuccess'] = "Product with ID $productId has been updated successfully";
        header("Location:viewProduct.php");
        exit();
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}


?>


<!DOCTYPE html>
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
    <title>Edit Products Page</title>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>

    <div id="main-content">
        <div class="container mt-2">

            <!-- Edit Products View -->
            <div class="container">

                <div class="mb-2">
                    <a href="../Admin/viewProduct.php" class="btn btn-secondary">Go back</a>
                </div>

                <h2 class="text-center mb-4">Edit Product</h2>
                <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
                    <input type="hidden" name="Product_ID" value="<?php if (isset($product['Product_ID'])) echo $product['Product_ID']; ?>">

                    <div class="mb-3">
                        <label for="productName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" name="productName"
                            value="<?php if (isset($product['productName'])) echo $product['productName'] ?>">
                    </div>

                    <!-- Product Image -->
                    <div class="mb-3">
                        <label for="productImage" class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="productImage" name="product_image">
                        <?php if (isset($product['Image_Path']) && !empty($product['Image_Path'])): ?>
                            <img src="<?php echo $product['Image_Path']; ?>" alt="Product Image" style="width: 100px; height: auto; margin-top: 10px;">
                        <?php endif; ?>
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <?php
                        if (isset($product['categories'])) {
                            foreach ($categories as $category) {
                                if ($category['Category_ID'] == $product['categories']) {
                                    echo "You have selected: " . $category['Category_Name'];
                                    break; // Stop the loop once the matching category is found
                                }
                            }
                        }
                        ?>
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" name="category">
                            <option value="" disabled>Choose Category</option>
                            <?php
                            if (isset($categories)) {
                                foreach ($categories as $category) {
                                    // Check if this category is the selected one
                                    $selected = ($category['Category_ID'] == $product['categories']) ? "selected" : "";
                                    echo "<option value='$category[Category_ID]' $selected>$category[Category_Name]</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="text" class="form-control" name="price"
                            value="<?php if (isset($product['Price'])) echo $product['Price'] ?>">
                    </div>


                    <div class="mb-3">
                        <?php
                        if (isset($product['brands'])) {
                            foreach ($brands as $brand) {
                                if ($brand['brand_id'] == $product['brands']) {
                                    echo "You have selected: " . $brand['brand_name'];
                                    break; // Stop the loop once the matching category is found
                                }
                            }
                        }
                        ?>
                        <label for="brand" class="form-label">Brand</label>
                        <select class="form-select" name="brand">
                            <option value="" disabled>Choose Brand</option>
                            <?php
                            if (isset($brands)) {
                                foreach ($brands as $brand) {
                                    // Check if this category is the selected one
                                    $selected = ($brand['brand_id'] == $product['brands']) ? "selected" : "";
                                    echo "<option value='$brand[brand_id]' $selected>$brand[brand_name]</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Is Latest Product -->
                    <div class="mb-3">
                        <label for="isLatest" class="form-label">Is Latest Product?</label>
                        <select class="form-select" name="is_latest">
                            <option value="1" <?php if (isset($product['is_latest']) && $product['is_latest'] == 1) echo 'selected'; ?>>Yes</option>
                            <option value="0" <?php if (isset($product['is_latest']) && $product['is_latest'] == 0) echo 'selected'; ?>>No</option>
                        </select>
                    </div>

                    <!-- Is Popular Product -->
                    <div class="mb-3">
                        <label for="isPopular" class="form-label">Is Popular Product?</label>
                        <select class="form-select" name="is_popular">
                            <option value="1" <?php if (isset($product['is_popular']) && $product['is_popular'] == 1) echo 'selected'; ?>>Yes</option>
                            <option value="0" <?php if (isset($product['is_popular']) && $product['is_popular'] == 0) echo 'selected'; ?>>No</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="currentImages" class="form-label">Current Shades & Images</label>
                        <div id="currentImages">
                            <?php if (isset($product['images']) && !empty($product['images'])): ?>
                                <?php foreach ($product['images'] as $image): ?>
                                    <div class="d-flex align-items-center mb-2">
                                        <input type="checkbox" name="delete_images[]" value="<?php echo $image['image_path']; ?>"> Delete
                                        <img src="../uploads/<?php echo $image['image_path']; ?>" alt="Product Image" style="width: 100px; height: auto; margin-right: 10px;">
                                        <input type="text" class="form-control" name="shades[]" value="<?php echo $image['shade_name']; ?>" readonly style="width: 30%; margin-right:10px;">
                                        <input type="number" class="form-control" name="existing_shade_quantities[]" value="<?php echo $image['quantity']; ?>" placeholder="Quantity" style="width: 30%; margin-right:10px;">
                                        <input type="hidden" name="existing_shade_ids[]" value="<?php echo $image['shade_id']; ?>">
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No images available.</p>
                            <?php endif; ?>
                        </div>
                    </div>



                    <!-- Number of Shades -->
                    <div class="mb-3">
                        <label for="shadeCount" class="form-label">Number of Shades</label>
                        <input type="number" class="form-control" id="shadeCount" name="shade_count" min="1" max="10">
                    </div>

                    <!-- Dynamic Shade Inputs -->
                    <div class="mb-3">
                        <label class="form-label">Shade Details</label>
                        <div id="shadeInputs"></div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" class="form-control" name="description"
                            value="<?php if (isset($product['Description'])) echo $product['Description'] ?>">
                    </div>

                    <div class="mb-3">
                        <?php
                        if (isset($product['admin_users'])) {
                            foreach ($admins as $admin) {
                                if ($admin['Admin_User_ID'] == $product['admin_users']) {
                                    echo "You have selected: " . $admin['Name'];
                                    break; // Stop the loop once the matching category is found
                                }
                            }
                        }
                        ?>
                        <label for="admin" class="form-label">Admin</label>
                        <select class="form-select" name="admin">
                            <option value="" disabled>Choose Admin</option>
                            <?php
                            if (isset($admins)) {
                                foreach ($admins as $admin) {
                                    // Check if this category is the selected one
                                    $selected = ($admin['Admin_User_ID'] == $product['admin_users']) ? "selected" : "";
                                    echo "<option value='$admin[Admin_User_ID]' $selected>$admin[Name]</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" name="update">Save Changes</button>

                </form>

            </div>
        </div>
    </div>
    </div>
</body>

</html>