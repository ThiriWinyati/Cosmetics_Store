<?php
session_start();
require_once "../db_connect.php";
require_once "admin_auth.php";

$isAdmin = admin_is_logged_in();

function maskOrderName($name)
{
    if (empty($name)) {
        return 'Locked';
    }

    return mb_substr($name, 0, 1) . str_repeat('*', max(mb_strlen($name) - 1, 3));
}

function maskOrderEmail($email)
{
    if (empty($email) || strpos($email, '@') === false) {
        return 'Locked';
    }

    [$localPart, $domain] = explode('@', $email, 2);
    return mb_substr($localPart, 0, 1) . str_repeat('*', max(mb_strlen($localPart) - 1, 3)) . '@' . $domain;
}

// Fetch all orders or search orders
$searchTerm = $_POST['searchTerm'] ?? '';
try {
    if ($searchTerm) {
        $ordersQuery = "SELECT o.*, 
                           c.Name as Customer_Name, 
                           c.Email as Customer_Email, 
                           sm.Shipping_Method, 
                           sm.DeliveryTime, 
                           pm.Method_Name AS Payment_Method_Name, 
                           c.Signup_time, 
                           o.cupon_id,
                           co.Coupon_Code, 
                           GROUP_CONCAT(p.Name ORDER BY oi.Order_Item_ID ASC) AS Product_Names,
                           GROUP_CONCAT(p.Price ORDER BY oi.Order_Item_ID ASC) AS Product_Prices,
                           GROUP_CONCAT(oi.Quantity ORDER BY oi.Order_Item_ID ASC) AS Quantities
                    FROM orders o
                    JOIN customers c ON o.Customer_ID = c.Customer_ID
                    LEFT JOIN shippingmethods sm ON o.shipping_id = sm.Shipping_Method_ID
                    LEFT JOIN payment_methods pm ON o.Payment_Method_ID = pm.Payment_Method_ID
                    LEFT JOIN order_items oi ON o.Order_ID = oi.Order_ID
                    LEFT JOIN products p ON oi.Product_ID = p.Product_ID
                    LEFT JOIN coupons co ON o.cupon_id = co.Coupon_ID  
                    WHERE o.Order_ID LIKE :searchTerm 
                       OR c.Name LIKE :searchTerm 
                       OR c.Email LIKE :searchTerm 
                       OR sm.Shipping_Method LIKE :searchTerm 
                       OR pm.Method_Name LIKE :searchTerm 
                       OR o.Status LIKE :searchTerm 
                       OR p.Name LIKE :searchTerm 
                       OR oi.Quantity LIKE :searchTerm 
                       OR co.Coupon_Code LIKE :searchTerm
                    GROUP BY o.Order_ID
                    ORDER BY o.Order_ID ASC";
        $ordersStmt = $conn->prepare($ordersQuery);
        $ordersStmt->execute(['searchTerm' => '%' . $searchTerm . '%']);
    } else {
        $ordersQuery = "SELECT o.*, 
                           c.Name as Customer_Name, 
                           c.Email as Customer_Email, 
                           sm.Shipping_Method, 
                           sm.DeliveryTime, 
                           pm.Method_Name AS Payment_Method_Name, 
                           c.Signup_time, 
                           o.cupon_id,
                           co.Coupon_Code,  
                           GROUP_CONCAT(p.Name ORDER BY oi.Order_Item_ID ASC) AS Product_Names,
                           GROUP_CONCAT(p.Price ORDER BY oi.Order_Item_ID ASC) AS Product_Prices,
                           GROUP_CONCAT(oi.Quantity ORDER BY oi.Order_Item_ID ASC) AS Quantities
                    FROM orders o
                    JOIN customers c ON o.Customer_ID = c.Customer_ID
                    LEFT JOIN shippingmethods sm ON o.shipping_id = sm.Shipping_Method_ID
                    LEFT JOIN payment_methods pm ON o.Payment_Method_ID = pm.Payment_Method_ID
                    LEFT JOIN order_items oi ON o.Order_ID = oi.Order_ID
                    LEFT JOIN products p ON oi.Product_ID = p.Product_ID
                    LEFT JOIN coupons co ON o.cupon_id = co.Coupon_ID  
                    GROUP BY o.Order_ID
                    ORDER BY o.Order_ID ASC";
        $ordersStmt = $conn->prepare($ordersQuery);
        $ordersStmt->execute();
    }
    $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching orders: " . $e->getMessage());
}

// Filter orders by status
if (isset($_GET['status'])) {
    $status = $_GET['status'];

    if ($status == 'pending') {
        $ordersQuery = "SELECT o.*, 
                           c.Name as Customer_Name, 
                           c.Email as Customer_Email, 
                           sm.Shipping_Method, 
                           sm.DeliveryTime, 
                           pm.Method_Name AS Payment_Method_Name, 
                           c.Signup_time, 
                           o.cupon_id,
                           co.Coupon_Code,  
                           GROUP_CONCAT(p.Name ORDER BY oi.Order_Item_ID ASC) AS Product_Names,
                           GROUP_CONCAT(p.Price ORDER BY oi.Order_Item_ID ASC) AS Product_Prices,
                           GROUP_CONCAT(oi.Quantity ORDER BY oi.Order_Item_ID ASC) AS Quantities
                    FROM orders o
                    JOIN customers c ON o.Customer_ID = c.Customer_ID
                    LEFT JOIN shippingmethods sm ON o.shipping_id = sm.Shipping_Method_ID
                    LEFT JOIN payment_methods pm ON o.Payment_Method_ID = pm.Payment_Method_ID
                    LEFT JOIN order_items oi ON o.Order_ID = oi.Order_ID
                    LEFT JOIN products p ON oi.Product_ID = p.Product_ID
                    LEFT JOIN coupons co ON o.cupon_id = co.Coupon_ID 
                    WHERE o.Status = 'Pending'
                    GROUP BY o.Order_ID
                    ORDER BY o.Order_ID ASC";

        $ordersStmt = $conn->prepare($ordersQuery);
        $ordersStmt->execute();
        $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($status == 'accepted') {
        $ordersQuery = "SELECT o.*, 
                           c.Name as Customer_Name, 
                           c.Email as Customer_Email, 
                           sm.Shipping_Method, 
                           sm.DeliveryTime, 
                           pm.Method_Name AS Payment_Method_Name, 
                           c.Signup_time, 
                           o.cupon_id,
                           co.Coupon_Code,  
                           GROUP_CONCAT(p.Name ORDER BY oi.Order_Item_ID ASC) AS Product_Names,
                           GROUP_CONCAT(p.Price ORDER BY oi.Order_Item_ID ASC) AS Product_Prices,
                           GROUP_CONCAT(oi.Quantity ORDER BY oi.Order_Item_ID ASC) AS Quantities
                    FROM orders o
                    JOIN customers c ON o.Customer_ID = c.Customer_ID
                    LEFT JOIN shippingmethods sm ON o.shipping_id = sm.Shipping_Method_ID
                    LEFT JOIN payment_methods pm ON o.Payment_Method_ID = pm.Payment_Method_ID
                    LEFT JOIN order_items oi ON o.Order_ID = oi.Order_ID
                    LEFT JOIN products p ON oi.Product_ID = p.Product_ID
                    LEFT JOIN coupons co ON o.cupon_id = co.Coupon_ID 
                    WHERE o.Status = 'Accepted'
                    GROUP BY o.Order_ID
                    ORDER BY o.Order_ID ASC";

        $ordersStmt = $conn->prepare($ordersQuery);
        $ordersStmt->execute();
        $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$totalOrders = count($orders ?? []);
$pendingOrders = 0;
$acceptedOrders = 0;

foreach ($orders ?? [] as $orderSummary) {
    $orderStatus = strtolower($orderSummary['Status'] ?? 'pending');

    if ($orderStatus === 'accepted') {
        $acceptedOrders++;
    } else {
        $pendingOrders++;
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
    <title>View Orders</title>
    <style>
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

        #viewOrdersTable thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            vertical-align: middle;
            background: #2f2f38;
            color: #ffffff;
            border-color: #454550;
            font-weight: 700;
            letter-spacing: 0;
            white-space: nowrap;
            text-align: center;
        }

        #viewOrdersTable thead th:first-child {
            border-top-left-radius: 12px;
        }

        #viewOrdersTable thead th:last-child {
            border-top-right-radius: 12px;
        }

        html[data-theme="dark"] #viewOrdersTable thead th {
            background: #2d2d34 !important;
            color: #ffffff !important;
            border-color: #3a3a42 !important;
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

        .orders-page-shell {
            padding-top: 28px;
        }

        .orders-page-header {
            position: relative;
            top: calc(var(--admin-topbar-height, 72px) + 10px);
            z-index: 35;
            padding: 14px 0 20px;
            margin-bottom: 22px;
            background: #ffffff;
            border: 0;
            box-shadow: none;
        }

        .orders-page-header .admin-page-title,
        .orders-page-header .admin-page-subtitle {
            text-align: center;
        }

        .orders-page-header .admin-page-title {
            font-size: clamp(1.7rem, 2.4vw, 2.35rem);
        }

        .orders-summary-grid {
            grid-template-columns: repeat(3, minmax(160px, 1fr));
            margin-top: 18px;
        }

        .orders-stat-card {
            min-height: 108px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 8px;
            overflow: visible;
        }

        .orders-page-search {
            margin-top: 14px;
        }

        .orders-page-search .input-group {
            flex-wrap: nowrap;
            margin-bottom: 0;
        }

        .orders-page-search .admin-search-input {
            min-width: 0;
        }

        .orders-page-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            margin-top: 16px;
            margin-bottom: 0;
        }

        .orders-page-actions .btn {
            min-width: 160px;
            font-weight: 700;
        }

        #viewOrdersTable {
            min-width: 1260px;
        }

        #viewOrdersTable td {
            padding-top: 18px;
            padding-bottom: 18px;
            vertical-align: middle;
        }

        #viewOrdersTable .order-products-cell {
            max-width: 300px;
            white-space: normal;
            line-height: 1.45;
        }

        #viewOrdersTable .order-products-content {
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        #viewOrdersTable .order-quantities-cell,
        #viewOrdersTable .order-coupon-cell {
            white-space: nowrap;
        }

        @media (max-width: 992px) {
            .orders-summary-grid {
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .orders-page-header {
                top: auto;
            }

            .orders-page-actions .btn {
                width: 100%;
            }
        }

        html[data-theme="dark"] .orders-page-header {
            background: #111113;
        }
    </style>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>

    <div class="admin-page-shell orders-page-shell">
        <section class="orders-page-header">
            <div>
                <h2 class="admin-page-title">View Orders</h2>
                <p class="admin-page-subtitle">Browse order activity, fulfillment status, payment method, and product quantities.</p>
            </div>

            <div class="admin-summary-grid orders-summary-grid">
                <div class="admin-stat-card orders-stat-card">
                    <span class="admin-stat-label">Visible Orders</span>
                    <span class="admin-stat-value"><?php echo $totalOrders; ?></span>
                </div>
                <div class="admin-stat-card orders-stat-card">
                    <span class="admin-stat-label">Pending</span>
                    <span class="admin-stat-value"><?php echo $pendingOrders; ?></span>
                </div>
                <div class="admin-stat-card orders-stat-card">
                    <span class="admin-stat-label">Accepted</span>
                    <span class="admin-stat-value"><?php echo $acceptedOrders; ?></span>
                </div>
            </div>

            <div class="admin-toolbar orders-page-search">
                <form method="POST" action="viewOrders.php" class="w-100">
                    <div class="input-group">
                        <input type="text" name="searchTerm" class="form-control admin-search-input" placeholder="Search orders, customers, products, payment, or shipping..." value="<?php echo htmlspecialchars($searchTerm ?? ''); ?>">
                        <button type="submit" class="admin-search-button px-4">Search</button>
                    </div>
                </form>
            </div>

            <div class="orders-page-actions">
                <a href="viewOrders.php?status=pending" class="btn btn-warning view-orders-btn">Pending Orders</a>
                <a href="viewOrders.php?status=accepted" class="btn btn-success view-orders-btn">Accepted Orders</a>
                <a href="orderManage.php" class="btn btn-primary">Manage Orders</a>
            </div>
        </section>

        <?php if (!$isAdmin): ?>
            <div class="alert alert-warning admin-preview-alert">
                <i class="fa fa-lock"></i>
                Customer names and emails are hidden in portfolio preview mode.
            </div>
        <?php endif; ?>

        <div class="admin-table-card">
            <div class="admin-table-scroll">
            <table class="table table-hover view-orders-table admin-data-table" id="viewOrdersTable">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Email</th>
                        <th>Shipping Method</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Products</th>
                        <th>Quantities</th>
                        <th>Coupon Code</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <?php
                            $orderStatus = strtolower($order['Status'] ?? 'pending');
                            $statusClass = $orderStatus === 'accepted' ? 'status-accepted' : 'status-pending';
                            ?>
                            <tr>
                                <td><span class="admin-row-title">#<?php echo htmlspecialchars($order['Order_ID'] ?? ''); ?></span></td>
                                <td><?php echo htmlspecialchars($isAdmin ? ($order['Customer_Name'] ?? '') : maskOrderName($order['Customer_Name'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars($isAdmin ? ($order['Customer_Email'] ?? '') : maskOrderEmail($order['Customer_Email'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars($order['Shipping_Method'] ?? 'Not selected'); ?></td>
                                <td><?php echo htmlspecialchars($order['Payment_Method_Name'] ?? 'Not selected'); ?></td>
                                <td><span class="admin-status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($order['Status'] ?? 'Pending'); ?></span></td>
                                <td class="order-products-cell"><div class="order-products-content"><?php echo htmlspecialchars($order['Product_Names'] ?? 'No products'); ?></div></td>
                                <td class="order-quantities-cell"><?php echo htmlspecialchars($order['Quantities'] ?? '0'); ?></td>
                                <td class="order-coupon-cell"><?php echo htmlspecialchars($order['Coupon_Code'] ?? 'No coupon'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</body>

</html>
