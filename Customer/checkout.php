<?php
session_start();
require_once "../db_connect.php";

// Database credentials
$server = getenv('DB_HOST');
$user = getenv('DB_USER');
$password = getenv('DB_PASS');
$database = getenv('DB_NAME');
$port = getenv('DB_PORT') ?: 3306;

// Create connection
try {
    $conn = new PDO(
        "mysql:host=$server;port=$port;dbname=$database;charset=utf8mb4",
        $user,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Ensure the user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    echo "<script>alert('Please log in to proceed with checkout.');</script>";
    echo "<script>window.location.href = 'user_login.php';</script>";
    exit();
}

// Fetch the customer details
$query = "SELECT * FROM customers WHERE Customer_ID = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['customer_id']]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    echo "<script>alert('Customer not found.');</script>";
    exit();
}

// Fetch the shipping methods
$shippingQuery = "SELECT * FROM shippingmethods";
$shippingStmt = $conn->prepare($shippingQuery);
$shippingStmt->execute();
$shippingMethods = $shippingStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch the payment methods
$paymentQuery = "SELECT * FROM payment_methods";
$paymentStmt = $conn->prepare($paymentQuery);
$paymentStmt->execute();
$paymentMethods = $paymentStmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate the total amount for the products
$productTotalAmount = 0;
foreach ($_SESSION['cart'] as $item) {
    $productTotalAmount += $item['price'] * $item['quantity'];
}

$newTotalAmount = $productTotalAmount; // Initial total is the product total amount
$couponApplied = false;
$discount = 0;
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_coupon'])) {
    $couponCode = trim($_POST['coupon_code']);

    // Fetch coupon details from the database
    $stmt = $conn->prepare(
        "SELECT discount_percentage, minimum_purchase_amount, coupon_id 
         FROM coupons 
         WHERE Coupon_Code = ? AND valid_to >= NOW()"
    );
    $stmt->execute([$couponCode]);
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($coupon) {
        if (isset($coupon['discount_percentage']) && isset($coupon['minimum_purchase_amount'])) {
            $discountPercentage = $coupon['discount_percentage'];
            $minimumPurchaseAmount = $coupon['minimum_purchase_amount'];

            // Check if the total price of the products meets the minimum purchase amount
            if ($productTotalAmount >= $minimumPurchaseAmount) {
                $discount = $productTotalAmount * ($discountPercentage / 100);
                $couponApplied = true;
                $couponID = $coupon['coupon_id'];

                $_SESSION['coupon'] = [
                    'coupon_id' => $couponID,
                    'coupon_code' => $couponCode,
                    'discount' => $discount,
                    'new_total_amount' => $newTotalAmount
                ];
            } else {
                $error_message = "The total price of the products does not meet the minimum purchase amount required by the coupon.";
            }
        } else {
            $error_message = "The coupon discount percentage or minimum purchase amount is missing.";
        }
    } else {
        $error_message = "Invalid or expired coupon.";
    }
}

// Include the shipping cost after applying the coupon discount
// Get the shipping cost
$shippingCost = 0;
if (isset($_POST['shipping_method'])) {
    $shippingMethodID = $_POST['shipping_method'];
    $shippingQuery = "SELECT Cost FROM shippingmethods WHERE Shipping_Method_ID = ?";
    $stmt = $conn->prepare($shippingQuery);
    $stmt->execute([$shippingMethodID]);
    $shippingMethod = $stmt->fetch(PDO::FETCH_ASSOC);
    $shippingCost = $shippingMethod['Cost'];
}

// Calculate the total amount
$newTotalAmount = $productTotalAmount - $discount;
$totalAmount = $newTotalAmount + $shippingCost;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_order'])) {
    try {
        // Ensure the shipping method exists
        $shippingMethodID = $_POST['shipping_method'];
        $shippingQuery = "SELECT * FROM shippingmethods WHERE Shipping_Method_ID = ?";
        $stmt = $conn->prepare($shippingQuery);
        $stmt->execute([$shippingMethodID]);
        $shippingMethod = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$shippingMethod) {
            throw new Exception("Invalid shipping method.");
        }

        // Correctly use the newTotalAmount for the final price
        $stmt = $conn->prepare(
            "INSERT INTO orders (Customer_ID, Order_Date, Status, Shipping_Address, Total_Price, cupon_id, Payment_Method_ID, Phone) 
             VALUES (?, NOW(), 'Pending', ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $_SESSION['customer_id'],
            $_POST['shipping_address'],
            $newTotalAmount,
            isset($_SESSION['coupon']['coupon_id']) ? $_SESSION['coupon']['coupon_id'] : null,
            $_POST['payment_method'],
            $_POST['phone']
        ]);

        $orderID = $conn->lastInsertId();

        // Insert a new shipping record with the order ID
        $shippingInsertQuery = "INSERT INTO shipping (Order_ID, Shipping_Status, Shipping_Date, Shipping_Method_ID) VALUES (?, 'Pending', NOW(), ?)";
        $stmt = $conn->prepare($shippingInsertQuery);
        $stmt->execute([$orderID, $shippingMethodID]);
        $shippingID = $conn->lastInsertId();

        // Update the shipping_id in the orders table
        $updateOrderQuery = "UPDATE orders SET shipping_id = ? WHERE Order_ID = ?";
        $updateStmt = $conn->prepare($updateOrderQuery);
        $updateStmt->execute([$shippingID, $orderID]);

        // Update the customer's phone number and address in the customers table
        if (isset($_POST['phone']) && isset($_POST['shipping_address'])) {
            $phone = $_POST['phone'];
            $shippingAddress = $_POST['shipping_address'];

            // Update the customer's phone and address
            $updateCustomerQuery = "UPDATE customers SET Phone = ?, Address = ? WHERE Customer_ID = ?";
            $updateStmt = $conn->prepare($updateCustomerQuery);
            $updateStmt->execute([$phone, $shippingAddress, $_SESSION['customer_id']]);
        }

        // Insert items into the order_items table
        foreach ($_SESSION['cart'] as $item) {
            // Fetch the shade_id based on the product_id and shade_name
            $stmt = $conn->prepare(
                "SELECT shade_id FROM shades WHERE product_id = ? AND shade_name = ?"
            );
            $stmt->execute([$item['product_id'], $item['shade_name']]);
            $shade = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if the shade exists
            if ($shade) {
                $shadeID = $shade['shade_id'];
            } else {
                $shadeID = null;
            }

            $stmt = $conn->prepare(
                "INSERT INTO order_items (Order_ID, Product_ID, Quantity, Unit_Price, Subtotal, Brand_ID, shade_id) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $orderID,
                $item['product_id'],
                $item['quantity'],
                $item['price'],
                $item['price'] * $item['quantity'],
                $item['brand_id'],
                $shadeID
            ]);
        }

        // Store order details in the session for displaying on the receipt page
        $_SESSION['order_details'] = [
            'order_id' => $orderID,
            'products' => $_SESSION['cart'],
            'shipping_method' => $_POST['shipping_method'],
            'shipping_cost' => isset($shippingCost) ? $shippingCost : 0, // Add shipping cost
            'coupon_code' => isset($_SESSION['coupon']['coupon_code']) ? $_SESSION['coupon']['coupon_code'] : null,
            'coupon_discount' => $discount,
            // 'shade_name' => $_POST['shade_name'],
            'total_amount' => $totalAmount,  // Correct total price
        ];
        // Unset cart and coupon
        unset($_SESSION['cart']);
        unset($_SESSION['coupon']);

        // Redirect to receipt page with order ID
        $redirectURL = "receipt.php?order_id=" . urlencode($orderID);
        echo "<script>window.location.href = '$redirectURL';</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

$conn = null;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Customer/customer_css/style.css">
    <script src="../Customer/customer_Javascript/checkout.js"></script>
    <style>
        .checkout-container {
            display: flex;
            gap: 20px;
            margin-top: 50px;
        }

        .checkout-form {
            flex: 2;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .checkout-summary {
            flex: 1;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .checkout-summary table {
            width: 100%;
            border-collapse: collapse;
        }

        .checkout-summary th,
        .checkout-summary td {
            padding: 10px;
            text-align: left;
        }

        .checkout-summary th {
            background-color: #f8f9fa;
        }

        .checkout-summary tbody tr:nth-child(even) {
            background-color: #f1f1f1;
        }

        .checkout-summary img {
            max-width: 50px;
            max-height: 50px;
            border-radius: 5px;
            margin-right: 10px;
        }

        .total-section {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .coupon-section {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .coupon-section .btn-primary {
            padding: 10px 20px;
            font-size: 1rem;
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container checkout-container">
        <div class="checkout-form">
            <h4>Billing Information</h4>
            <p>Name: <?php echo htmlspecialchars($customer['Name']); ?></p>
            <p>Email: <?php echo htmlspecialchars($customer['Email']); ?></p>

            <form method="POST" action="checkout.php" class="mt-4" onsubmit="return handleFormSubmission();">
                <input type="text" name="phone" class="form-control mb-3" placeholder="Enter your phone number" value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : ''; ?>" required>

                <h4>Select Shipping Method</h4>
                <select id="shipping_method" name="shipping_method" class="form-select mb-3" onchange="toggleAddressField(); updateTotalPrice();">
                    <?php foreach ($shippingMethods as $method) { ?>
                        <option value="<?php echo $method['Shipping_Method_ID']; ?>" data-cost="<?php echo $method['Cost']; ?>"
                            <?php echo (isset($_POST['shipping_method']) && $_POST['shipping_method'] == $method['Shipping_Method_ID']) ? 'selected' : ''; ?>>
                            <?php echo $method['Shipping_Method']; ?> - $<?php echo number_format($method['Cost'], 2); ?>
                        </option>
                    <?php } ?>
                </select>

                <input type="hidden" id="selected_shipping_method" name="selected_shipping_method" value="<?php echo isset($_POST['shipping_method']) ? $_POST['shipping_method'] : ''; ?>">

                <div id="address_field" style="display:none;">
                    <h4>Enter Shipping Address</h4>
                    <input type="text" name="shipping_address_visible" class="form-control mb-3" placeholder="Enter your address" value="<?php echo isset($_POST['shipping_address']) ? $_POST['shipping_address'] : ''; ?>">
                </div>

                <input type="hidden" name="shipping_address" id="shipping_address_hidden" value="<?php echo isset($_POST['shipping_address']) ? $_POST['shipping_address'] : ''; ?>">

                <h4>Select Payment Method</h4>
                <select id="payment_method" name="payment_method" class="form-select mb-3" onchange="handlePaymentMethod();" required>
                    <option value="1" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 1) ? 'selected' : ''; ?>>Credit/Debit Card</option>
                    <option value="2" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 2) ? 'selected' : ''; ?>>PayPal</option>
                    <option value="3" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 3) ? 'selected' : ''; ?>>Cash on Delivery</option>
                    <option value="4" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 4) ? 'selected' : ''; ?>>KBZPay</option>
                    <option value="5" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 5) ? 'selected' : ''; ?>>WavePay</option>
                    <option value="6" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 6) ? 'selected' : ''; ?>>AYA Pay</option>
                </select>

                <div id="payment_details_section" style="display:none;">
                    <div id="card_details" style="display:none;">
                        <h4>Credit/Debit Card Details</h4>
                        <input type="text" name="card_number" class="form-control mb-3" placeholder="Enter your card number">
                        <input type="text" name="card_expiry" class="form-control mb-3" placeholder="Enter expiry date (MM/YY)">
                        <input type="text" name="card_cvc" class="form-control mb-3" placeholder="Enter CVC">
                    </div>

                    <div id="paypal_details" style="display:none;">
                        <h4>PayPal Details</h4>
                        <input type="email" name="paypal_email" class="form-control mb-3" placeholder="Enter your PayPal email">
                    </div>

                    <div id="mobile_payment_details" style="display:none;">
                        <h4>Mobile Payment Details</h4>
                        <input type="text" name="phone_number" class="form-control mb-3" placeholder="Enter your phone number">
                        <input type="text" name="otp" class="form-control mb-3" placeholder="Enter OTP received">
                    </div>
                </div>

                <h4>Apply Coupon (Optional)</h4>
                <div class="coupon-section">
                    <input type="text" name="coupon_code" class="form-control" style="width: 80%;" placeholder="Enter coupon code"
                        value="<?php echo isset($_POST['coupon_code']) ? $_POST['coupon_code'] : ''; ?>">

                    <button type="submit" name="apply_coupon" class="btn btn-primary">Apply Coupon</button>
                </div>

                <input type="hidden" id="original_total" value="<?php echo $productTotalAmount; ?>">
                <input type="hidden" id="discount" value="<?php echo $discount; ?>">
                <input type="hidden" id="total_amount" name="total_amount" value="<?php echo $totalAmount; ?>">

                <button type="submit" name="complete_order" class="btn btn-dark btn-lg w-100 mt-3">Complete Order</button>
            </form>

            <a href="cart.php" class="btn btn-outline-secondary btn-lg w-100 mt-3">Back to Cart</a>
        </div>

        <div class="checkout-summary">
            <h4>Your Cart</h4>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Shade</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($_SESSION['cart'] as $item) {
                        $total = $item['price'] * $item['quantity'];
                    ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                <?php echo htmlspecialchars($item['product_name']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['shade_name'] ?? 'N/A'); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>$<?php echo number_format($total, 2); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="total-section">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span class="total-amount">$<?php echo number_format($productTotalAmount, 2); ?></span>
                </div>
                <div class="total-row">
                    <span>Discount:</span>
                    <span class="total-amount">-$<?php echo number_format($discount, 2); ?></span>
                </div>
                <div class="total-row">
                    <span>Shipping:</span>
                    <span class="total-amount" id="shipping-cost">$<?php echo number_format($shippingCost, 2); ?></span>
                </div>
                <div class="total-row">
                    <span>Total:</span>
                    <span class="total-amount" id="total">$<?php echo number_format($totalAmount, 2); ?></span>
                </div>
            </div>

            <?php if (isset($couponApplied) && $couponApplied) { ?>
                <div class="alert alert-success">
                    Coupon applied successfully! Discount: $<?php echo number_format($discount, 2); ?>
                </div>
            <?php } elseif (isset($error_message) && $error_message) { ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php } ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        function toggleAddressField() {
            const shippingMethod = document.getElementById('shipping_method').value;
            const addressField = document.getElementById('address_field');
            const shippingAddressHidden = document.getElementById('shipping_address_hidden');

            // Assuming "Pick Up at Store" has a Shipping_Method_ID of 7
            if (shippingMethod == 7) {
                addressField.style.display = 'none';
                shippingAddressHidden.value = '';
            } else {
                addressField.style.display = 'block';
                shippingAddressHidden.value = document.querySelector('input[name="shipping_address_visible"]').value;
            }
        }

        function handlePaymentMethod() {
            const paymentMethod = document.getElementById('payment_method').value;
            const paymentDetailsSection = document.getElementById('payment_details_section');
            const cardDetails = document.getElementById('card_details');
            const paypalDetails = document.getElementById('paypal_details');
            const mobilePaymentDetails = document.getElementById('mobile_payment_details');

            paymentDetailsSection.style.display = 'block';
            cardDetails.style.display = 'none';
            paypalDetails.style.display = 'none';
            mobilePaymentDetails.style.display = 'none';

            if (paymentMethod == 1) {
                cardDetails.style.display = 'block';
            } else if (paymentMethod == 2) {
                paypalDetails.style.display = 'block';
            } else if (paymentMethod == 4 || paymentMethod == 5 || paymentMethod == 6) {
                mobilePaymentDetails.style.display = 'block';
            }
        }

        function updateTotalPrice() {
            const shippingMethod = document.getElementById('shipping_method');
            const shippingCost = parseFloat(shippingMethod.options[shippingMethod.selectedIndex].getAttribute('data-cost'));
            const subtotal = parseFloat(document.getElementById('original_total').value);
            const discount = parseFloat(document.getElementById('discount').value) || 0;
            const totalAmount = subtotal - discount + shippingCost;

            document.getElementById('shipping-cost').innerText = `$${shippingCost.toFixed(2)}`;
            document.getElementById('total').innerText = `$${totalAmount.toFixed(2)}`;
            document.getElementById('total_amount').value = totalAmount;
            document.getElementById('selected_shipping_method').value = shippingMethod.value;
        }

        function handleFormSubmission() {
            const shippingAddressVisible = document.querySelector('input[name="shipping_address_visible"]');
            const shippingAddressHidden = document.getElementById('shipping_address_hidden');
            if (shippingAddressVisible && shippingAddressHidden) {
                shippingAddressHidden.value = shippingAddressVisible.value;
            }
            return true;
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleAddressField();
            updateTotalPrice();
            handlePaymentMethod();
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>

</html>