<?php
session_start();
require_once "../db_connect.php";

// Check if the user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    echo "<script>alert('Please log in to view your order history.');</script>";
    echo "<script>window.location.href = 'user_login.php';</script>";
    exit();
}

$customerID = $_SESSION['customer_id'];

// Fetch previous orders with shipping status
$orderQuery = "SELECT 
                    o.Order_ID, 
                    o.Order_Date, 
                    o.Status AS OrderStatus, 
                    o.Total_Price, 
                    o.Shipping_Address, 
                    o.Phone, 
                    o.Cupon_ID, 
                    o.Shipping_ID, 
                    o.Payment_Method_ID, 
                    MAX(s.Shipping_Status) AS Shipping_Status, 
                    MAX(s.Shipping_Date) AS Shipping_Date, 
                    MAX(s.Shipping_Method_ID) AS Shipping_Method_ID,
                    GROUP_CONCAT(oi.Product_ID ORDER BY oi.Order_Item_ID ASC) AS Product_IDs,
                    GROUP_CONCAT(oi.Quantity ORDER BY oi.Order_Item_ID ASC) AS Quantities,
                    GROUP_CONCAT(oi.Unit_Price ORDER BY oi.Order_Item_ID ASC) AS Unit_Prices,
                    GROUP_CONCAT(oi.Subtotal ORDER BY oi.Order_Item_ID ASC) AS Subtotals,
                    GROUP_CONCAT(p.Name ORDER BY oi.Order_Item_ID ASC) AS Product_Names
               FROM orders o
               LEFT JOIN shipping s ON o.Order_ID = s.Order_ID
               LEFT JOIN order_items oi ON o.Order_ID = oi.Order_ID
               LEFT JOIN products p ON oi.Product_ID = p.Product_ID
               WHERE o.Customer_ID = ?
               GROUP BY 
                    o.Order_ID, 
                    o.Order_Date, 
                    o.Status, 
                    o.Total_Price, 
                    o.Shipping_Address, 
                    o.Phone, 
                    o.Cupon_ID, 
                    o.Shipping_ID, 
                    o.Payment_Method_ID
               ORDER BY o.Order_Date DESC";

$stmt = $conn->prepare($orderQuery);
$stmt->execute([$customerID]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../Customer/customer_css/style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Order History - Charm & Grace</title>
</head>

<body class="order-history-page">
    <?php include 'navbar.php'; ?>

    <div class="container order-history-container">
        <nav class="order-history-breadcrumb" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="user_homeIndex.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Order History</li>
            </ol>
        </nav>

        <div class="order-history-heading">
            <span>Account</span>
            <h2>Your Order History</h2>
            <p>Review your recent orders, delivery status, and purchased products.</p>
        </div>

        <?php if (empty($orders)): ?>
            <div class="order-empty-state">
                <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                <p>You have no previous orders.</p>
                <a href="products.php">Start Shopping</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="card mb-4 order-history-card">
                    <div class="card-header order-history-card-header">
                        <div>
                            <span class="order-card-eyebrow">Order #<?php echo $order['Order_ID']; ?></span>
                            <h5><?php echo date("F j, Y, g:i a", strtotime($order['Order_Date'])); ?></h5>
                        </div>
                        <div class="order-status-group">
                            <span class="order-status-pill"><?php echo htmlspecialchars($order['OrderStatus']); ?></span>
                            <span class="order-status-pill shipping"><?php echo htmlspecialchars($order['Shipping_Status'] ?? 'Not available'); ?></span>
                        </div>
                    </div>
                    <div class="card-body order-history-card-body">
                        <div class="order-summary-grid">
                            <div class="order-summary-item">
                                <span>Total Price</span>
                                <strong>$<?php echo number_format($order['Total_Price'], 2); ?></strong>
                            </div>
                            <div class="order-summary-item">
                                <span>Shipping Address</span>
                                <strong><?php echo htmlspecialchars($order['Shipping_Address']); ?></strong>
                            </div>
                            <div class="order-summary-item">
                                <span>Phone</span>
                                <strong><?php echo htmlspecialchars($order['Phone']); ?></strong>
                            </div>
                        </div>

                        <h6 class="ordered-products-title">Ordered Products</h6>
                        <div class="order-products-table-wrap">
                        <table class="table table-bordered order-products-table">
                            <thead class="table-dark">
                                <tr>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Subtotal</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Get product details from grouped result
                                $productIDs = explode(',', $order['Product_IDs']);
                                $productNames = explode(',', $order['Product_Names']);
                                $quantities = explode(',', $order['Quantities']);
                                $unitPrices = explode(',', $order['Unit_Prices']);
                                $subtotals = explode(',', $order['Subtotals']);

                                for ($i = 0; $i < count($productIDs); $i++): ?>
                                    <tr>
                                        <td data-label="Product Name"><?php echo htmlspecialchars($productNames[$i]); ?></td>
                                        <td data-label="Quantity"><?php echo $quantities[$i]; ?></td>
                                        <td data-label="Unit Price">$<?php echo number_format($unitPrices[$i], 2); ?></td>
                                        <td data-label="Subtotal">$<?php echo number_format($subtotals[$i], 2); ?></td>
                                        <td data-label="Action">
                                            <a href="viewDetails.php?id=<?php echo $productIDs[$i]; ?>" class="btn btn-info order-view-product-btn">View Product</a>
                                        </td>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>
