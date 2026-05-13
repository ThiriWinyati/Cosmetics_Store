<?php
session_start();
require_once "../db_connect.php";
require_once "admin_auth.php";

$isAdmin = isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true;

function maskName($name)
{
    if (empty($name)) {
        return 'N/A';
    }

    $firstLetter = mb_substr($name, 0, 1);
    return $firstLetter . str_repeat('*', max(mb_strlen($name) - 1, 3));
}

function maskEmail($email)
{
    if (empty($email) || strpos($email, '@') === false) {
        return 'N/A';
    }

    [$localPart, $domain] = explode('@', $email, 2);

    $firstLetter = mb_substr($localPart, 0, 1);
    $maskedLocal = $firstLetter . str_repeat('*', max(mb_strlen($localPart) - 1, 3));

    return $maskedLocal . '@' . $domain;
}

if (isset($_GET['delete_order_id'])) {
    admin_require_login('orderManage.php');
}

// Fetch all orders or search orders
$searchTerm = $_POST['searchTerm'] ?? '';
try {
    if ($searchTerm) {
        $ordersQuery = "SELECT o.*, 
                           c.Name as Customer_Name, 
                           c.Email as Customer_Email, 
                           smm.Shipping_Method, 
                           smm.DeliveryTime, 
                           pm.Method_Name AS Payment_Method_Name, 
                           c.Signup_time, 
                           o.cupon_id,
                           co.Coupon_Code, 
                           GROUP_CONCAT(p.Name ORDER BY oi.Order_Item_ID ASC) AS Product_Names,
                           GROUP_CONCAT(p.Price ORDER BY oi.Order_Item_ID ASC) AS Product_Prices,
                           GROUP_CONCAT(oi.Quantity ORDER BY oi.Order_Item_ID ASC) AS Quantities
                    FROM orders o
                    JOIN customers c ON o.Customer_ID = c.Customer_ID
                    JOIN shipping sm ON o.shipping_id = sm.Shipping_ID
                    JOIN shippingmethods smm ON sm.Shipping_Method_ID = smm.Shipping_Method_ID
                    JOIN payment_methods pm ON o.Payment_Method_ID = pm.Payment_Method_ID
                    LEFT JOIN order_items oi ON o.Order_ID = oi.Order_ID
                    LEFT JOIN products p ON oi.Product_ID = p.Product_ID
                    LEFT JOIN coupons co ON o.cupon_id = co.Coupon_ID  
                    WHERE o.Status = 'Pending' 
                    AND (c.Name LIKE :searchTerm OR c.Email LIKE :searchTerm OR o.Order_ID LIKE :searchTerm)
                    GROUP BY o.Order_ID
                    ORDER BY o.Order_ID ASC";
        $ordersStmt = $conn->prepare($ordersQuery);
        $ordersStmt->execute(['searchTerm' => "%$searchTerm%"]);
    } else {
        $ordersQuery = "SELECT o.*, 
                           c.Name as Customer_Name, 
                           c.Email as Customer_Email, 
                           smm.Shipping_Method, 
                           smm.DeliveryTime, 
                           pm.Method_Name AS Payment_Method_Name, 
                           c.Signup_time, 
                           o.cupon_id,
                           co.Coupon_Code,  -- Add coupon code to the query
                           GROUP_CONCAT(p.Name ORDER BY oi.Order_Item_ID ASC) AS Product_Names,
                           GROUP_CONCAT(p.Price ORDER BY oi.Order_Item_ID ASC) AS Product_Prices,
                           GROUP_CONCAT(oi.Quantity ORDER BY oi.Order_Item_ID ASC) AS Quantities
                    FROM orders o
                    JOIN customers c ON o.Customer_ID = c.Customer_ID
                    JOIN shipping sm ON o.shipping_id = sm.Shipping_ID
                    JOIN shippingmethods smm ON sm.Shipping_Method_ID = smm.Shipping_Method_ID
                    JOIN payment_methods pm ON o.Payment_Method_ID = pm.Payment_Method_ID
                    LEFT JOIN order_items oi ON o.Order_ID = oi.Order_ID
                    LEFT JOIN products p ON oi.Product_ID = p.Product_ID
                    LEFT JOIN coupons co ON o.cupon_id = co.Coupon_ID  -- Join the coupons table
                    WHERE o.Status = 'Pending'  -- Include only pending orders
                    GROUP BY o.Order_ID
                    ORDER BY o.Order_ID ASC";
        $ordersStmt = $conn->prepare($ordersQuery);
        $ordersStmt->execute();
    }
    $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching orders: " . $e->getMessage());
}

// Delete order
if (isset($_GET['delete_order_id'])) {
    $deleteOrderID = $_GET['delete_order_id'];

    try {
        // Delete order items
        $deleteItemsQuery = "DELETE FROM order_items WHERE Order_ID = ?";
        $deleteItemsStmt = $conn->prepare($deleteItemsQuery);
        $deleteItemsStmt->execute([$deleteOrderID]);

        // Delete the order
        $deleteOrderQuery = "DELETE FROM orders WHERE Order_ID = ?";
        $deleteOrderStmt = $conn->prepare($deleteOrderQuery);
        $deleteOrderStmt->execute([$deleteOrderID]);

        echo "<script>alert('Order deleted successfully.');</script>";
        echo "<script>window.location.href = 'orderManage.php';</script>";
    } catch (PDOException $e) {
        die("Error deleting order: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <title>Manage Orders</title>

    <style>
        .orders-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
            margin-top: 25px;
        }

        .order-card {
            background: #1f1f27;
            color: #f5f5f5;
            border: 1px solid #343442;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .order-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.45);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #3a3a48;
            padding-bottom: 14px;
            margin-bottom: 16px;
        }

        .order-header h5 {
            color: #d97cb3;
            font-weight: 700;
            margin: 0;
        }

        .order-status {
            background: rgba(217, 124, 179, 0.15);
            color: #ff9ed1;
            border: 1px solid rgba(217, 124, 179, 0.4);
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .order-details p,
        .order-products p {
            margin-bottom: 10px;
            color: #dddddd;
        }

        .order-details strong,
        .order-products strong {
            color: #ffffff;
        }

        .order-products {
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px solid #3a3a48;
        }

        .order-products > p:last-of-type {
            color: #d97cb3;
            font-weight: 700;
            margin-top: 12px;
        }

        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            background: #2a2a35;
            border: 1px solid #3b3b4a;
            border-radius: 12px;
            padding: 10px 12px;
            margin-bottom: 8px;
            color: #eeeeee;
        }

        .order-footer {
            display: flex;
            gap: 12px;
            margin-top: 22px;
            flex-wrap: wrap;
        }

        .order-footer button,
        .order-footer .locked-btn {
            flex: 1;
            min-width: 130px;
            border: none;
            border-radius: 999px;
            padding: 10px 18px;
            font-weight: 700;
            transition: 0.25s ease;
        }

        .accept-btn {
            background: #d97cb3;
            color: white;
        }

        .accept-btn:hover {
            background: #c2185b;
        }

        .delete-btn {
            background: #ff4d6d;
            color: white;
        }

        .delete-btn:hover {
            background: #d93655;
        }

        .locked-btn {
            background: #3a3a45;
            color: #bdbdbd;
            cursor: not-allowed;
        }

        @media (max-width: 576px) {
            .orders-container {
                grid-template-columns: 1fr;
            }

            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .order-footer {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>



    <div class="container">
        <a href="orderManage.php" class="text-decoration-none">
            <h2 class="text-center">Manage Orders</h2>
        </a>
        <p class="text-center">Here you can view and manage all customer orders with their details.</p>

        <div class="text-start mb-3">
            <a href="viewOrders.php" class="btn btn-outline-primary">
                <i class="fa fa-arrow-left"></i> Back to Orders
            </a>
        </div>

        <form method="POST" action="orderManage.php" class="mb-3">
            <div class="input-group">
                <input type="text" name="searchTerm" class="form-control" placeholder="<?php echo $isAdmin ? 'Search by customer name, email, or order ID' : 'Search by order ID' ?>" value="<?php echo htmlspecialchars($searchTerm ?? ''); ?>">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>

        <div class="orders-container">
            <?php
            foreach ($orders as $order) {
                $orderID = htmlspecialchars($order['Order_ID'] ?? '');
                $orderDate = !empty($order['Order_Date']) ? date("F j, Y", strtotime($order['Order_Date'])) : 'Not available';
                $totalAmount = number_format((float)($order['Total_Price'] ?? 0), 2);

                $rawCustomerName = $order['Customer_Name'] ?? 'Unknown customer';
                $rawCustomerEmail = $order['Customer_Email'] ?? 'No email';

                $customerName = $isAdmin
                    ? htmlspecialchars($rawCustomerName)
                    : htmlspecialchars(maskName($rawCustomerName));

                $customerEmail = $isAdmin
                    ? htmlspecialchars($rawCustomerEmail)
                    : htmlspecialchars(maskEmail($rawCustomerEmail));
                $shippingMethod = htmlspecialchars($order['Shipping_Method'] ?? 'Not selected');
                $paymentMethod = htmlspecialchars($order['Payment_Method_Name'] ?? 'Not selected');
                $orderStatus = htmlspecialchars($order['Status'] ?? 'Pending');
                $couponApplied = !empty($order['cupon_id']) ? 'Yes' : 'No';

                // Get product details safely
                $productNames = !empty($order['Product_Names']) ? explode(',', $order['Product_Names']) : [];
                $productPrices = !empty($order['Product_Prices']) ? explode(',', $order['Product_Prices']) : [];
                $quantities = !empty($order['Quantities']) ? explode(',', $order['Quantities']) : [];

                echo "
            <div class='order-card'>
                <div class='order-header'>
                    <h5>Order #{$orderID}</h5>
                    <div class='order-status'>Status: {$orderStatus}</div>
                </div>
        
                <div class='order-details'>
                    <p><strong>Customer Name:</strong> {$customerName}</p>
                    <p><strong>Customer Email:</strong> {$customerEmail}</p>
                    <p><strong>Order Date:</strong> {$orderDate}</p>
                    <p><strong>Shipping Method:</strong> {$shippingMethod}</p>
                    <p><strong>Payment Method:</strong> {$paymentMethod}</p>

                </div>
        
                <div class='order-products'>
                    ";


                if (!empty($order['Coupon_Code'])) {
                    echo "<p><strong>Coupon Applied:</strong> {$couponApplied}</p>";
                    $couponCode = htmlspecialchars($order['Coupon_Code'] ?? '');
                    echo "<p><strong>Coupon Code:</strong> {$couponCode}</p>";
                } else {
                    echo "<p><strong>Coupon Applied:</strong> No</p>";
                }
                echo "<p>Products</p>";

                /// Loop through products and display them
                if (!empty($productNames)) {
                    for ($i = 0; $i < count($productNames); $i++) {
                        $productName = htmlspecialchars($productNames[$i] ?? 'Unknown product');

                        $productPrice = isset($productPrices[$i]) && is_numeric($productPrices[$i])
                            ? (float)$productPrices[$i]
                            : 0;

                        $quantity = isset($quantities[$i]) && is_numeric($quantities[$i])
                            ? (int)$quantities[$i]
                            : 0;

                        $subtotal = $productPrice * $quantity;
                        $subtotalFormatted = number_format($subtotal, 2);

                        echo "
                            <div class='product-item'>
                                <span>{$productName} x {$quantity}</span>
                                <span>\${$subtotalFormatted}</span>
                            </div>";
                    }
                } else {
                    echo "<p>No products found for this order.</p>";
                }

                echo "
                </div>
        
                if ($isAdmin) {
                    echo "
                    <div class='order-footer'>
                        <button class='accept-btn' onclick='window.location.href=\"acceptOrder.php?order_id={$orderID}\"'>
                            Accept Order
                        </button>

                        <button class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this order?\") ? window.location.href=\"orderManage.php?delete_order_id={$orderID}\" : false;'>
                            Delete Order
                        </button>
                    </div>";
                } else {
                    echo "
                    <div class='order-footer'>
                        <button class='locked-btn' disabled>
                            <i class='fa fa-lock'></i> Accept Locked
                        </button>

                        <button class='locked-btn' disabled>
                            <i class='fa fa-lock'></i> Delete Locked
                        </button>
                    </div>";
                }

                echo "
            </div>";

            }

            ?>
        </div>
    </div>



</body>

</html>
