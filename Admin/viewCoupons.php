<?php
session_start();
require_once "../db_connect.php";
require_once "admin_auth.php";

$isAdmin = admin_is_logged_in();

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

        .coupon-page-shell {
            padding-top: 28px;
        }

        .coupon-page-header {
            position: sticky;
            top: calc(var(--admin-topbar-height, 72px) + 10px);
            z-index: 35;
            padding: 14px 0 18px;
            margin-bottom: 18px;
            background: transparent;
            border: 0;
            box-shadow: none;
        }

        .coupon-page-header .admin-page-title,
        .coupon-page-header .admin-page-subtitle {
            text-align: center;
        }

        .coupon-page-header .admin-page-title {
            font-size: clamp(1.7rem, 2.4vw, 2.35rem);
        }

        .coupon-page-search {
            margin-top: 18px;
        }

        .coupon-page-search .input-group {
            flex-wrap: nowrap;
            margin-bottom: 0;
        }

        .coupon-page-search .admin-search-input {
            min-width: 0;
        }

        .coupon-page-actions {
            display: flex;
            justify-content: center;
            margin-top: 12px;
        }

        #viewCouponsTable {
            min-width: 1060px;
        }

        #viewCouponsTable td {
            vertical-align: middle;
        }

        #viewCouponsTable .coupon-code {
            font-weight: 800;
            color: #d97cb3;
            letter-spacing: 0.02em;
        }

        #viewCouponsTable .coupon-date {
            white-space: nowrap;
        }

        #viewCouponsTable .coupon-amount {
            white-space: nowrap;
            font-weight: 700;
        }

        .action-buttons {
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .coupon-page-header {
                top: calc(var(--admin-topbar-height, 72px) + 6px);
            }

            .coupon-page-actions .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>

    <div id="main-content">
        <div class="admin-page-shell coupon-page-shell">
            <section class="coupon-page-header">
                <div>
                    <h2 class="admin-page-title">View Coupons</h2>
                    <p class="admin-page-subtitle">Manage promotional codes, validity windows, discounts, and minimum purchase amounts.</p>
                </div>

                <div class="admin-toolbar coupon-page-search">
                    <form method="POST" action="viewCoupons.php" class="w-100">
                        <div class="input-group">
                            <input type="text" name="searchTerm" class="form-control admin-search-input" placeholder="Search for coupons..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                            <button type="submit" class="admin-search-button px-4">Search</button>
                        </div>
                    </form>
                </div>

                <div class="coupon-page-actions">
                <?php if ($isAdmin): ?>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#insertCouponModal">
                    <i class="fa fa-plus"></i> Insert Coupon
                </button>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary" disabled title="Admin login required">
                        <i class="fa fa-lock"></i> Insert locked
                    </button>
                <?php endif; ?>
                </div>
            </section>

            <?php if (!$isAdmin): ?>
                <div class="alert alert-warning admin-preview-alert">
                    <i class="fa fa-lock"></i>
                    Coupon editing and deletion are locked in portfolio preview mode.
                </div>
            <?php endif; ?>

            <!-- Insert Coupon Modal -->
            <?php if ($isAdmin): ?>
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
            <?php endif; ?>

            <!-- Coupons Table -->
            <div class="admin-table-card">
                <div class="admin-table-scroll">
                <table class="table table-hover admin-data-table" id="viewCouponsTable">
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
                                <td><span class="admin-row-title">#<?php echo htmlspecialchars($coupon['Coupon_ID']); ?></span></td>
                                <td><span class="coupon-code"><?php echo htmlspecialchars($coupon['Coupon_Code']); ?></span></td>
                                <td><?php echo htmlspecialchars($coupon['Discount_Percentage']); ?>%</td>
                                <td class="coupon-date"><?php echo htmlspecialchars($coupon['Valid_From']); ?></td>
                                <td class="coupon-date"><?php echo htmlspecialchars($coupon['Valid_To']); ?></td>
                                <td class="coupon-amount"><?php echo htmlspecialchars($coupon['Minimum_Purchase_Amount']); ?></td>
                                <td class="action-buttons">
                                    <?php if ($isAdmin): ?>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editCouponModal<?php echo $coupon['Coupon_ID']; ?>">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteCouponModal<?php echo $coupon['Coupon_ID']; ?>">
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

                            <!-- Edit Modal -->
                            <?php if ($isAdmin): ?>
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
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
