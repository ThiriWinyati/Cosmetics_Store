<?php
session_start();
require_once "../db_connect.php";
require_once "admin_auth.php";

$isAdmin = isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true;

if (isset($_POST['insertBrand']) || isset($_POST['editBrand']) || isset($_GET['deleteBrandId'])) {
    admin_require_login('viewBrands.php');
}

// Fetch all brands or search brands
$searchTerm = $_POST['searchTerm'] ?? '';
try {
    if ($searchTerm) {
        $brandsQuery = "SELECT * FROM brands WHERE brand_name LIKE :searchTerm";
        $brandsStmt = $conn->prepare($brandsQuery);
        $brandsStmt->execute(['searchTerm' => '%' . $searchTerm . '%']);
    } else {
        $brandsQuery = "SELECT * FROM brands";
        $brandsStmt = $conn->prepare($brandsQuery);
        $brandsStmt->execute();
    }
    $brands = $brandsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching brands: " . $e->getMessage());
}

// Insert new brand
if (isset($_POST['insertBrand'])) {
    $brandName = $_POST['brandName'];

    try {
        $insertBrandQuery = "INSERT INTO brands (brand_name) VALUES (:brandName)";
        $insertBrandStmt = $conn->prepare($insertBrandQuery);
        $insertBrandStmt->bindParam(':brandName', $brandName);
        $insertBrandStmt->execute();

        echo "<script>alert('Brand inserted successfully!');</script>";
        echo "<script>window.location.href = 'viewBrands.php';</script>";
    } catch (PDOException $e) {
        die("Error inserting brand: " . $e->getMessage());
    }
}

// Edit brand
if (isset($_POST['editBrand'])) {
    $brandId = $_POST['brandId'];
    $brandName = $_POST['brandName'];

    try {
        $editBrandQuery = "UPDATE brands SET brand_name = :brandName WHERE brand_id = :brandId";
        $editBrandStmt = $conn->prepare($editBrandQuery);
        $editBrandStmt->bindParam(':brandName', $brandName);
        $editBrandStmt->bindParam(':brandId', $brandId);
        $editBrandStmt->execute();

        echo "<script>alert('Brand edited successfully!');</script>";
        echo "<script>window.location.href = 'viewBrands.php';</script>";
    } catch (PDOException $e) {
        die("Error editing brand: " . $e->getMessage());
    }
}

// Delete brand
if (isset($_GET['deleteBrandId'])) {
    $brandId = $_GET['deleteBrandId'];

    try {
        $deleteBrandQuery = "DELETE FROM brands WHERE brand_id = :brandId";
        $deleteBrandStmt = $conn->prepare($deleteBrandQuery);
        $deleteBrandStmt->bindParam(':brandId', $brandId);
        $deleteBrandStmt->execute();

        echo "<script>alert('Brand deleted successfully!');</script>";
        echo "<script>window.location.href = 'viewBrands.php';</script>";
    } catch (PDOException $e) {
        die("Error deleting brand: " . $e->getMessage());
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
    <script src="../Admin/admin_Javascript/sidebar.js"></script>
    <link rel="icon" href="path/to/favicon.ico">
    <title>View Brands</title>
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
            max-height: 400px;
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
            padding: 0.5rem;
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

        .modal-content {
            background-color: #fff !important;
            /* Ensure the modal has a white background */
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

    <?php include 'sidebar_nav.php'; ?>


    <div class="container mt-4">
        <a href="viewBrands.php" class="text-decoration-none">
            <h2 class="text-center mb-4">View Brands</h2>
        </a>
        <form method="POST" action="viewBrands.php" class="mb-3">
            <div class="input-group">
                <input type="text" name="searchTerm" class="form-control" placeholder="Search for brands..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                <button type="submit" class="btn btn-dark">Search</button>
            </div>
        </form>
        <div class="text-end mb-3">
            <?php if ($isAdmin): ?>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#insertBrandModal">
                    <i class="fa fa-plus"></i> Insert New Brand
                </button>
            <?php else: ?>
                <button type="button" class="btn btn-secondary" disabled title="Admin login required">
                    <i class="fa fa-lock"></i> Insert locked
                </button>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <table class="table table-hover" id="viewBrandsTable">
                <thead>
                    <tr>
                        <th>Brand ID</th>
                        <th>Brand Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($brands)): ?>
                        <?php foreach ($brands as $brand): ?>
                            <?php
                            $brandID = htmlspecialchars($brand['brand_id']);
                            $brandName = htmlspecialchars($brand['brand_name']);
                            ?>

                            <tr>
                                <td><?php echo $brandID; ?></td>
                                <td><?php echo $brandName; ?></td>
                                <td>
                                    <?php if ($isAdmin): ?>
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editBrandModal<?php echo $brandID; ?>">
                                            <i class="fa fa-pencil-alt"></i> Edit
                                        </button>

                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteBrandModal<?php echo $brandID; ?>">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled title="Admin login required">
                                            <i class="fa fa-lock"></i> Edit locked
                                        </button>

                                        <button class="btn btn-secondary btn-sm" disabled title="Admin login required">
                                            <i class="fa fa-lock"></i> Delete locked
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <?php if ($isAdmin): ?>
                                <!-- Edit Modal -->
                                <div class="modal fade" id="editBrandModal<?php echo $brandID; ?>" tabindex="-1" aria-labelledby="editBrandModalLabel<?php echo $brandID; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editBrandModalLabel<?php echo $brandID; ?>">Edit Brand</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body">
                                                <form method="POST" action="viewBrands.php">
                                                    <input type="hidden" name="brandId" value="<?php echo $brandID; ?>">

                                                    <div class="mb-3">
                                                        <label class="form-label">Brand Name</label>
                                                        <input type="text" class="form-control" name="brandName" value="<?php echo $brandName; ?>" required>
                                                    </div>

                                                    <button type="submit" name="editBrand" class="btn btn-primary">Save Changes</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteBrandModal<?php echo $brandID; ?>" tabindex="-1" aria-labelledby="deleteBrandModalLabel<?php echo $brandID; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteBrandModalLabel<?php echo $brandID; ?>">Delete Brand</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body">
                                                Are you sure you want to delete this brand?
                                            </div>

                                            <div class="modal-footer">
                                                <form action="viewBrands.php" method="GET">
                                                    <input type="hidden" name="deleteBrandId" value="<?php echo $brandID; ?>">

                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No brands found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($isAdmin): ?>
            <!-- Insert Brand Modal -->
            <div class="modal fade" id="insertBrandModal" tabindex="-1" aria-labelledby="insertBrandModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="insertBrandModalLabel">Insert New Brand</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <form method="POST" action="viewBrands.php">
                                <div class="mb-3">
                                    <label for="brandName" class="form-label">Brand Name</label>
                                    <input type="text" class="form-control" id="brandName" name="brandName" required>
                                </div>

                                <button type="submit" name="insertBrand" class="btn btn-primary">Insert Brand</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
</body>

</html>
