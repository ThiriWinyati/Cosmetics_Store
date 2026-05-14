<?php
session_start();
require_once "../db_connect.php";
require_once "admin_auth.php";

$isAdmin = isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true;

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

        .category-page-shell {
            padding-top: 28px;
        }

        .category-page-header {
            position: relative;
            top: auto;
            z-index: 35;
            padding: 14px 0 20px;
            margin-bottom: 22px;
            background: #ffffff;
            border: 0;
            box-shadow: none;
        }

        .category-page-header .admin-page-title,
        .category-page-header .admin-page-subtitle {
            text-align: center;
        }

        .category-page-header .admin-page-title {
            font-size: clamp(1.7rem, 2.4vw, 2.35rem);
        }

        .category-page-search {
            margin-top: 18px;
        }

        .category-page-search .input-group {
            flex-wrap: nowrap;
            margin-bottom: 0;
        }

        .category-page-search .admin-search-input {
            min-width: 0;
        }

        .category-page-actions {
            display: flex;
            justify-content: center;
            margin-top: 16px;
            margin-bottom: 0;
        }

        #viewCategoriesTable {
            min-width: 720px;
        }

        #viewCategoriesTable td {
            vertical-align: middle;
        }

        @media (max-width: 768px) {
            .category-page-header {
                top: auto;
            }

            .category-page-actions .btn {
                width: 100%;
            }
        }

        html[data-theme="dark"] .category-page-header {
            background: #111113;
        }
    </style>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>

    <div class="admin-page-shell category-page-shell">
        <section class="category-page-header">
            <div>
                <h2 class="admin-page-title">View Categories</h2>
                <p class="admin-page-subtitle">Browse and organize the product categories used across the shop.</p>
            </div>

            <div class="admin-toolbar category-page-search">
                <form method="POST" action="viewCategory.php" class="w-100">
                    <div class="input-group">
                        <input type="text" name="searchTerm" class="form-control admin-search-input" placeholder="Search for categories..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                        <button type="submit" class="admin-search-button px-4">Search</button>
                    </div>
                </form>
            </div>

        </section>

        <div class="admin-table-action-row category-page-actions">
            <?php if ($isAdmin): ?>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#insertModal">
                    <i class="fa fa-plus"></i> Insert New Category
                </button>
            <?php else: ?>
                <button type="button" class="btn btn-secondary" disabled title="Admin login required">
                    <i class="fa fa-lock"></i> Insert locked
                </button>
            <?php endif; ?>
        </div>

        <div class="admin-table-card">
            <div class="admin-table-scroll">
            <table class="table table-hover admin-data-table" id="viewCategoriesTable">
                <thead>
                    <tr>
                        <th>Category ID</th>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <?php
                            $categoryID = htmlspecialchars($category['Category_ID']);
                            $categoryName = htmlspecialchars($category['Category_Name']);
                            ?>

                            <tr>
                                <td><?php echo $categoryID; ?></td>
                                <td><?php echo $categoryName; ?></td>
                                <td>
                                    <?php if ($isAdmin): ?>
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $categoryID; ?>">
                                            <i class="fa fa-pencil-alt"></i> Edit
                                        </button>

                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $categoryID; ?>">
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
                                <div class="modal fade" id="editModal<?php echo $categoryID; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $categoryID; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel<?php echo $categoryID; ?>">Edit Category</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body">
                                                <form action="editCategory.php" method="POST">
                                                    <input type="hidden" name="category_id" value="<?php echo $categoryID; ?>">

                                                    <div class="mb-3">
                                                        <label class="form-label">Category Name:</label>
                                                        <input type="text" class="form-control" name="category_name" value="<?php echo $categoryName; ?>" required>
                                                    </div>

                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal<?php echo $categoryID; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $categoryID; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel<?php echo $categoryID; ?>">Delete Category</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body">
                                                Are you sure you want to delete this category?
                                            </div>

                                            <div class="modal-footer">
                                                <form action="deleteCategory.php" method="POST">
                                                    <input type="hidden" name="category_id" value="<?php echo $categoryID; ?>">

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
                            <td colspan="3" class="text-center">No categories found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>


    </div>
</body>

</html>
