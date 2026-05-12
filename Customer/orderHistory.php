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
$orderQuery = "SELECT o.Order_ID, o.Order_Date, o.Status AS OrderStatus, o.Total_Price, o.Shipping_Address, o.Phone, 
                      o.Cupon_ID, o.Shipping_ID, o.Payment_Method_ID, 
                      s.Shipping_Status, s.Shipping_Date, s.Shipping_Method_ID,
                      GROUP_CONCAT(DISTINCT oi.Product_ID ORDER BY oi.Order_Item_ID ASC) AS Product_IDs,
                      GROUP_CONCAT(DISTINCT oi.Quantity ORDER BY oi.Order_Item_ID ASC) AS Quantities,
                      GROUP_CONCAT(DISTINCT oi.Unit_Price ORDER BY oi.Order_Item_ID ASC) AS Unit_Prices,
                      GROUP_CONCAT(DISTINCT oi.Subtotal ORDER BY oi.Order_Item_ID ASC) AS Subtotals,
                      GROUP_CONCAT(DISTINCT p.Name ORDER BY oi.Order_Item_ID ASC) AS Product_Names
               FROM orders o
               LEFT JOIN (SELECT * FROM shipping ORDER BY Shipping_Date DESC) s ON o.Order_ID = s.Order_ID
               LEFT JOIN order_items oi ON o.Order_ID = oi.Order_ID
               LEFT JOIN products p ON oi.Product_ID = p.Product_ID
               WHERE o.Customer_ID = ?
               GROUP BY o.Order_ID
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
    <link rel="stylesheet" href="../Customer/customer_css/style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Order History - Charm & Grace</title>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="user_homeIndex.php" style="color: black; text-decoration:none;">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Order History</li>
            </ol>
        </nav>

        <h2 class="text-center mb-4">Your Order History</h2>

        <?php if (empty($orders)): ?>
            <p class="text-center">You have no previous orders.</p>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Order ID: #<?php echo $order['Order_ID']; ?> | Status: <?php echo $order['OrderStatus']; ?></h5>
                        <p>Order Date: <?php echo date("F j, Y, g:i a", strtotime($order['Order_Date'])); ?></p>
                        <p><strong>Shipping Status:</strong> <?php echo $order['Shipping_Status'] ?? 'Not available'; ?></p>
                    </div>
                    <div class="card-body">
                        <p><strong>Total Price:</strong> $<?php echo number_format($order['Total_Price'], 2); ?></p>
                        <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['Shipping_Address']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['Phone']); ?></p>

                        <h6>Ordered Products:</h6>
                        <table class="table table-bordered">
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
                                        <td><?php echo htmlspecialchars($productNames[$i]); ?></td>
                                        <td><?php echo $quantities[$i]; ?></td>
                                        <td>$<?php echo number_format($unitPrices[$i], 2); ?></td>
                                        <td>$<?php echo number_format($subtotals[$i], 2); ?></td>
                                        <td>
                                            <a href="viewDetails.php?id=<?php echo $productIDs[$i]; ?>" class="btn btn-info">View Product</a>
                                        </td>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>