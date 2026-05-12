<?php
session_start();

require_once "../db_connect.php";

// Database credentials
$servername = 'localhost';
$username = 'root';
$password = '';
$database = 'cosmetics_store';

// Create connection
try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Ensure the user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    echo "<script>alert('Please log in to view your receipt.');</script>";
    echo "<script>window.location.href = 'user_login.php';</script>";
    exit();
}

// Fetch the order details
if (!isset($_GET['order_id'])) {
    echo "<script>alert('Invalid order ID.');</script>";
    echo "<script>window.location.href = 'user_homeIndex.php';</script>";
    exit();
}

$orderID = $_GET['order_id'];

// Fetch order information
try {
    $orderQuery = "SELECT o.*, c.Name, c.Email, smm.Shipping_Method, smm.DeliveryTime, smm.Cost, pm.Method_Name AS Payment_Method_Name,
    cu.Coupon_Code, cu.Discount_Percentage AS Coupon_Discount
FROM orders o
JOIN customers c ON o.Customer_ID = c.Customer_ID
JOIN shipping sm ON o.shipping_id = sm.Shipping_ID
JOIN shippingmethods smm ON sm.Shipping_Method_ID = smm.Shipping_Method_ID
JOIN payment_methods pm ON o.Payment_Method_ID = pm.Payment_Method_ID
LEFT JOIN coupons cu ON o.cupon_id = cu.Coupon_ID
WHERE o.Order_ID = ?";

    $orderStmt = $conn->prepare($orderQuery);
    $orderStmt->execute([$orderID]);
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "<script>alert('Order not found.');</script>";
        echo "<script>window.location.href = 'user_homeIndex.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    // die("Error fetching order details: " . $e->getMessage());
}

// Fetch order items
try {
    $itemsQuery = "SELECT oi.*, p.Name as Product_Name, p.Price, s.shade_name 
                   FROM order_items oi
                   JOIN products p ON oi.Product_ID = p.Product_ID
                   LEFT JOIN shades s ON oi.shade_id = s.shade_id
                   WHERE oi.Order_ID = ?";
    $itemsStmt = $conn->prepare($itemsQuery);
    $itemsStmt->execute([$orderID]);
    $orderItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

    // var_dump($orderItems);

    if (empty($orderItems)) {
        echo "<script>alert('No items found for this order.');</script>";
        echo "<script>window.location.href = 'user_homeIndex.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    // die("Error fetching order items: " . $e->getMessage());
}



// Calculate the total amount for the products
$totalAmount = 0;
foreach ($orderItems as $item) {
    $itemTotal = $item['Quantity'] * $item['Price'];
    $totalAmount += $itemTotal;
}

// Add shipping cost to the total amount if applicable
$shippingCost = 0;
if ($order['Shipping_Method'] != 'Pick Up at Store') {
    $shippingCost = $order['Cost']; // Shipping cost from the shippingmethods table
    // $totalAmount += floatval($shippingCost); // Add shipping cost to the total amount for products
    // echo var_dump($totalAmount);
}
// echo var_dump($totalAmount);


// Calculate the total amount before discount
$totalAmountBeforeDiscount = $totalAmount; 
// Initialize discount variables
$couponApplied = false;
$couponCode = '';
$couponDiscount = 0;
$discountAmount = 0; // Initialize discount amount

if (!empty($order['Coupon_Code'])) {
    $couponApplied = true;
    $couponCode = $order['Coupon_Code'];
    $couponDiscount = $order['Coupon_Discount'];

    // Calculate the discount amount as a percentage of the total amount before discount
    $discountAmount = ($totalAmountBeforeDiscount * ($couponDiscount / 100)); // Calculate the discount

    // Calculate the final total after applying the discount
    $totalAmount = $totalAmountBeforeDiscount - $discountAmount; // Subtract discount from the amount before discount
    $totalAmount += floatval($shippingCost);
} else {
    $totalAmount = $totalAmountBeforeDiscount; // If no coupon, the total remains the same
    $totalAmount += floatval($shippingCost);
}

// Delete cart session
unset($_SESSION['cart']);

// Delete shopping cart data
try {
    $deleteQuery = "DELETE FROM shopping_cart WHERE Customer_ID = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->execute([$_SESSION['customer_id']]);
} catch (PDOException $e) {
}

// Decrease stock quantity
try {
    foreach ($orderItems as $item) {
        $shadeId = $item['shade_id'];
        $quantity = $item['Quantity'];

        // Retrieve current stock quantity
        $stockQuery = "SELECT Quantity FROM shades WHERE shade_id = ?";
        $stockStmt = $conn->prepare($stockQuery);
        $stockStmt->execute([$shadeId]);
        $stockQuantity = $stockStmt->fetchColumn();

        // Decrease stock quantity
        $newStockQuantity = $stockQuantity - $quantity;
        $updateQuery = "UPDATE shades SET Quantity = ? WHERE shade_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->execute([$newStockQuantity, $shadeId]);
    }
} catch (PDOException $e) {
    // die("Error decreasing stock quantity: " . $e->getMessage());
}

// After fetching the order and order items
try {
    // Assuming Shipping_ID is auto-increment, we do not need to manually generate it.
    $shippingStatus = 'Processing'; // Set an initial status for the shipping
    $shippingDate = date('Y-m-d'); // Set the current date as the shipping date

    // Fetch the Shipping_Method_ID from the shipping table
    $shippingMethodQuery = "SELECT Shipping_Method_ID FROM shipping WHERE Order_ID = ?";
    $shippingMethodStmt = $conn->prepare($shippingMethodQuery);
    $shippingMethodStmt->execute([$orderID]);
    $shippingMethod = $shippingMethodStmt->fetch(PDO::FETCH_ASSOC);

    if ($shippingMethod) {
        $shippingMethodID = $shippingMethod['Shipping_Method_ID'];

        // Check if the Shipping_Method_ID exists in the shippingmethods table
        $checkShippingMethodQuery = "SELECT COUNT(*) FROM shippingmethods WHERE Shipping_Method_ID = ?";
        $checkShippingMethodStmt = $conn->prepare($checkShippingMethodQuery);
        $checkShippingMethodStmt->execute([$shippingMethodID]);
        $shippingMethodExists = $checkShippingMethodStmt->fetchColumn();

        if ($shippingMethodExists > 0) {
            // Update the shipping status to 'Processing' if the Shipping_Method_ID exists
            $updateShippingQuery = "UPDATE shipping SET Shipping_Status = ?, Shipping_Date = ? WHERE Order_ID = ?";
            $updateShippingStmt = $conn->prepare($updateShippingQuery);
            $updateShippingStmt->execute([$shippingStatus, $shippingDate, $orderID]);
        } else {
            // echo "<script>alert('Invalid shipping method.');</script>";
            echo "<script>window.location.href = 'user_homeIndex.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Shipping method not found for this order.');</script>";
        echo "<script>window.location.href = 'user_homeIndex.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    // die("Error updating shipping table: " . $e->getMessage());
}


// Unset cart and coupon
unset($_SESSION['order_details']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt - Charm & Grace</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Raleway:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Customer/customer_css/style.css">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: 'Raleway', sans-serif;
            margin: 0;
            padding: 0;
        }

        .receipt-container-luxury {
            max-width: 850px;
            margin: 50px auto;
            padding: 40px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.1);
            border: 1px solid #e6e6e6;
        }

        .receipt-header-luxury {
            text-align: center;
            margin-bottom: 50px;
        }

        .receipt-header-luxury h2 {
            font-size: 36px;
            color: #b38b6d;
            font-family: 'Playfair Display', serif;
            margin-bottom: 15px;
        }

        .receipt-header-luxury p {
            font-size: 18px;
            color: #8a8a8a;
        }

        .receipt-details-luxury {
            margin-bottom: 30px;
            font-size: 18px;
            line-height: 1.6;
            color: #555;
        }

        .receipt-details-luxury table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .receipt-details-luxury th,
        .receipt-details-luxury td {
            padding: 15px;
            border: 1px solid #e6e6e6;
            text-align: left;
            font-size: 16px;
        }

        .receipt-summary-luxury {
            margin-top: 30px;
            font-size: 20px;
            font-weight: bold;
            color: #333;
            text-align: center;
        }

        .customer-details {
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .customer-details h4 {
            font-size: 24px;
            font-family: 'Playfair Display', serif;
            color: #b38b6d;
            margin-bottom: 15px;
        }

        .customer-details p {
            font-size: 16px;
            color: #555;
        }

        .table th {
            background-color: #b38b6d;
            color: white;
            text-align: center;
        }

        .text-primary {
            color: #b38b6d !important;
        }

        .btn-primary {
            background-color: #b38b6d;
            border-color: #b38b6d;
        }

        .btn-primary:hover {
            background-color: #9c7f58;
            border-color: #9c7f58;
        }

        .receipt-footer-luxury {
            margin-top: 30px;
            text-align: center;
            font-size: 16px;
            color: #8a8a8a;
        }
    </style>
</head>

<body>

    <div class="receipt-container-luxury">
        <div class="receipt-header-luxury">
            <h2>Order Receipt</h2>
            <p>Thank you for shopping with Charm & Grace!</p>
            <?php if ($order['Shipping_Method'] == 'Pick Up at Store'): ?>
                <p>Please pick up your order at our store.</p>
            <?php else: ?>
                <p>Your order will arrive in <?php echo htmlspecialchars($order['DeliveryTime']); ?>.</p>
            <?php endif; ?>
        </div>

        <div class="receipt-details-luxury">
            <h4>Order Details</h4>
            <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['Order_ID']); ?></p>
            <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['Name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['Email']); ?></p>
            <p><strong>Shipping Method:</strong> <?php echo htmlspecialchars($order['Shipping_Method']); ?></p>
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['Payment_Method_Name']); ?></p>
            <p><strong>Order Date:</strong> <?php echo date("F j, Y", strtotime($order['Order_Date'])); ?></p>

            <?php
            $shippingCost = 0; // Default shipping cost
            if ($order['Shipping_Method'] != 'Pick Up at Store') {
                $shippingCost = $order['Cost']; // Set shipping cost if method is not "Pick Up at Store"
                echo "<p><strong>Shipping Address:</strong> " . htmlspecialchars($order['Shipping_Address']) . "</p>";
                //echo ";
            }
            ?>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Shade</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalAmount = 0;
                foreach ($orderItems as $item) {
                    $itemTotal = ($item['Quantity'] * $item['Price']);
                    $totalAmount += $itemTotal;
                    echo "<tr>
                        <td>" . htmlspecialchars($item['Product_Name']) . "</td>
                        <td>" . htmlspecialchars($item['shade_name'] ?? 'N/A') . "</td>
                        <td>" . htmlspecialchars($item['Quantity']) . "</td>
                        <td>$" . number_format($item['Price'], 2) . "</td>
                        <td>$" . number_format($itemTotal, 2) . "</td>
                      </tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Update total amount -->
        <div class="receipt-summary-luxury">
            <?php if ($couponApplied): ?>
                <p><strong>Total Amount Before Discount: $<?php echo number_format($totalAmountBeforeDiscount, 2); ?></strong></p>
                <p><strong>Coupon Applied: <?php echo htmlspecialchars($couponCode); ?></strong></p>
                <p><strong>Discount: $<?php echo number_format($discountAmount, 2); ?></strong></p>
                <p><strong>Shipping Cost:</strong> $ <?php echo number_format($shippingCost, 2); ?> </strong></p>
                <p><strong>New Total Amount: $<?php echo number_format($totalAmount - $discountAmount + $shippingCost, 2); ?></strong></p>
            <?php else: ?>
                <p><strong>Shipping Cost:</strong> $ <?php echo number_format($shippingCost, 2); ?> </strong></p>

                <p><strong>Total Amount: $<?php echo number_format($totalAmount - $discountAmount + $shippingCost, 2); ?></strong></p>
            <?php endif; ?>
        </div>

        <div class="text-center">

            <a href="user_homeIndex.php" class="btn btn-primary">Back to Home</a>
        </div>

        <div class="receipt-footer-luxury">
            <p>&copy; 2025 Charm & Grace. All rights reserved.</p>
        </div>
    </div>

</body>

</html>