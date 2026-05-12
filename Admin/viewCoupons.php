<?php
session_start();
require_once "../db_connect.php";

// Check if the user is logged in as an admin
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    echo "<script>alert('Please log in as an admin.');</script>";
    echo "<script>window.location.href = 'adminLogin.php';</script>";
    exit();
}

// Fetch all coupons or search coupons
$searchTerm = $_POST['searchTerm'] ?? '';
try {
    if ($searchTerm) {
        $couponsQuery = "SELECT * FROM coupons WHERE Coupon_Code LIKE :searchTerm";
        $couponsStmt = $conn->prepare($couponsQuery);
        $couponsStmt->execute(['searchTerm' => '%' . $searchTerm . '%']);
    } else {
        $couponsQuery = "SELECT * FROM coupons";
        $couponsStmt = $conn->prepare($couponsQuery);
        $couponsStmt->execute();
    }
    $coupons = $couponsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching coupons: " . $e->getMessage());
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
    <title>View Coupons</title>
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

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 5px;
        }
    </style>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>

    <!-- Main Content -->
    <div id="main-content">
        <div class="container mt-4">
            <a href="viewCoupons.php" class="text-decoration-none">
                <h2 class="text-center">View Coupons</h2>
            </a>

            <!-- Search bar -->
            <form method="POST" action="viewCoupons.php" class="mb-3">
                <div class="input-group">
                    <input type="text" name="searchTerm" class="form-control" placeholder="Search for coupons..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                    <button type="submit" class="btn btn-dark">Search</button>
                </div>
            </form>

            <div class="text-end mb-3">
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#insertCouponModal">
                    <i class="fa fa-plus"></i> Insert Coupon
                </button>
            </div>

            <!-- Insert Coupon Modal -->
            <div class="modal fade" id="insertCouponModal" tabindex="-1" aria-labelledby="insertCouponModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="insertCouponModalLabel">Insert New Coupon</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="insertCoupon.php" method="POST">
                                <div class="mb-3">
                                    <label for="coupon_code" class="form-label">Coupon Code:</label>
                                    <input type="text" class="form-control" id="coupon_code" name="coupon_code" required>
                                </div>
                                <div class="mb-3">
                                    <label for="discount_percentage" class="form-label">Discount Percentage:</label>
                                    <input type="number" class="form-control" id="discount_percentage" name="discount_percentage" required>
                                </div>
                                <div class="mb-3">
                                    <label for="valid_from" class="form-label">Valid From:</label>
                                    <input type="date" class="form-control" id="valid_from" name="valid_from" required>
                                </div>
                                <div class="mb-3">
                                    <label for="valid_to" class="form-label">Valid To:</label>
                                    <input type="date" class="form-control" id="valid_to" name="valid_to" required>
                                </div>
                                <div class="mb-3">
                                    <label for="minimum_purchase_amount" class="form-label">Minimum Purchase Amount:</label>
                                    <input type="number" class="form-control" id="minimum_purchase_amount" name="minimum_purchase_amount" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Insert Coupon</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coupons Table -->
            <div class="table-container">
                <table class="table table-hover" id="viewCouponsTable">
                    <thead>
                        <tr>
                            <th>Coupon ID</th>
                            <th>Coupon Code</th>
                            <th>Discount Percentage</th>
                            <th>Valid From</th>
                            <th>Valid To</th>
                            <th>Minimum Purchase Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($coupons as $coupon): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($coupon['Coupon_ID']); ?></td>
                                <td><?php echo htmlspecialchars($coupon['Coupon_Code']); ?></td>
                                <td><?php echo htmlspecialchars($coupon['Discount_Percentage']); ?>%</td>
                                <td><?php echo htmlspecialchars($coupon['Valid_From']); ?></td>
                                <td><?php echo htmlspecialchars($coupon['Valid_To']); ?></td>
                                <td><?php echo htmlspecialchars($coupon['Minimum_Purchase_Amount']); ?></td>
                                <td class="action-buttons">
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editCouponModal<?php echo $coupon['Coupon_ID']; ?>">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteCouponModal<?php echo $coupon['Coupon_ID']; ?>">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editCouponModal<?php echo $coupon['Coupon_ID']; ?>" tabindex="-1" aria-labelledby="editCouponModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editCouponModalLabel">Edit Coupon</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="editCoupon.php">
                                                <input type="hidden" name="coupon_id" value="<?php echo $coupon['Coupon_ID']; ?>">
                                                <div class="mb-3">
                                                    <label for="coupon_code" class="form-label">Coupon Code</label>
                                                    <input type="text" class="form-control" id="coupon_code" name="coupon_code" value="<?php echo htmlspecialchars($coupon['Coupon_Code']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="discount_percentage" class="form-label">Discount Percentage</label>
                                                    <input type="number" class="form-control" id="discount_percentage" name="discount_percentage" value="<?php echo htmlspecialchars($coupon['Discount_Percentage']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="valid_from" class="form-label">Valid From</label>
                                                    <input type="date" class="form-control" id="valid_from" name="valid_from" value="<?php echo htmlspecialchars($coupon['Valid_From']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="valid_to" class="form-label">Valid To</label>
                                                    <input type="date" class="form-control" id="valid_to" name="valid_to" value="<?php echo htmlspecialchars($coupon['Valid_To']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="minimum_purchase_amount" class="form-label">Minimum Purchase Amount</label>
                                                    <input type="number" class="form-control" id="minimum_purchase_amount" name="minimum_purchase_amount" value="<?php echo htmlspecialchars($coupon['Minimum_Purchase_Amount']); ?>" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteCouponModal<?php echo $coupon['Coupon_ID']; ?>" tabindex="-1" aria-labelledby="deleteCouponModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteCouponModalLabel">Delete Coupon</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete this coupon?
                                        </div>
                                        <div class="modal-footer">
                                            <form method="POST" action="deleteCoupon.php">
                                                <input type="hidden" name="coupon_id" value="<?php echo $coupon['Coupon_ID']; ?>">
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
    </div>

    <script>
        // Search function
        document.getElementById('searchButton').addEventListener('click', function() {
            var input = document.getElementById('searchInput').value.toLowerCase();
            var rows = document.querySelectorAll('#viewCouponsTable tbody tr');

            rows.forEach(function(row) {
                var couponCode = row.cells[1].textContent.toLowerCase();
                if (couponCode.includes(input)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>