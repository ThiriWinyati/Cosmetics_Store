<?php
session_start();
require_once "../db_connect.php";

// Check if the user is logged in as an admin
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    echo "<script>alert('Please log in as an admin.');</script>";
    echo "<script>window.location.href = 'adminLogin.php';</script>";
    exit();
}

// Fetch all categories or search categories
$searchTerm = $_POST['searchTerm'] ?? '';
try {
    if ($searchTerm) {
        $categoriesQuery = "SELECT * FROM categories WHERE Category_Name LIKE :searchTerm";
        $categoriesStmt = $conn->prepare($categoriesQuery);
        $categoriesStmt->execute(['searchTerm' => '%' . $searchTerm . '%']);
    } else {
        $categoriesQuery = "SELECT * FROM categories";
        $categoriesStmt = $conn->prepare($categoriesQuery);
        $categoriesStmt->execute();
    }
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching categories: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Admin/admin_css/style.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../Admin/admin_Javascript/sidebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" href="path/to/favicon.ico">
    <title>View Categories</title>
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
            /* Reduced height */
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
            /* Reduced padding */
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
    </style>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>

    <div class="container" style="overflow-x:auto;">
        <a href="viewCategory.php" class="text-decoration-none">
            <h2 class="text-center mb-4">View Categories</h2>
        </a>
        <form method="POST" action="viewCategory.php" class="mb-3">
            <div class="input-group">
                <input type="text" name="searchTerm" class="form-control" placeholder="Search for categories..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                <button type="submit" class="btn btn-dark">Search</button>
            </div>
        </form>
        <div class="text-end mb-3">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#insertModal">
                <i class="fa fa-plus"></i> Insert New Category
            </button>
        </div>

        <div class="table-container">
            <table class="table table-hover" id="viewCategoriesTable">
                <thead>
                    <tr>
                        <th>Category ID</th>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($category['Category_ID']); ?></td>
                            <td><?php echo htmlspecialchars($category['Category_Name']); ?></td>
                            <td>
                                <!-- Edit Button -->
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $category['Category_ID']; ?>">
                                    <i class="fa fa-pencil-alt"></i> Edit
                                </button>

                                <!-- Delete Button -->
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $category['Category_ID']; ?>">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>

                        <!-- Insert Modal -->
                        <div class="modal fade" id="insertModal" tabindex="-1" aria-labelledby="insertModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="insertModalLabel">Insert New Category</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="insertCategory.php" method="POST">
                                            <div class="mb-3">
                                                <label for="category_name" class="form-label">Category Name:</label>
                                                <input type="text" class="form-control" id="category_name" name="category_name">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Insert Category</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?php echo $category['Category_ID']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Edit Category</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="editCategory.php" method="POST">
                                            <input type="hidden" name="category_id" value="<?php echo $category['Category_ID']; ?>">
                                            <div class="mb-3">
                                                <label for="category_name" class="form-label">Category Name:</label>
                                                <input type="text" class="form-control" id="category_name" name="category_name" value="<?php echo htmlspecialchars($category['Category_Name']); ?>">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteModal<?php echo $category['Category_ID']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel">Delete Category</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete this category?
                                    </div>
                                    <div class="modal-footer">
                                        <form action="deleteCategory.php" method="POST">
                                            <input type="hidden" name="category_id" value="<?php echo $category['Category_ID']; ?>">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>


    </div>
</body>

</html>