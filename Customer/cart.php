<?php
session_start();
require_once "../db_connect.php";

// Ensure the user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    echo "<script>alert('Please log in to view your cart.');</script>";
    echo "<script>window.location.href = 'user_login.php';</script>";
    exit();
}

// Fetch shipping methods
$shippingMethods = [];
try {
    $shippingQuery = "SELECT * FROM shippingmethods";
    $stmt = $conn->prepare($shippingQuery);
    $stmt->execute();
    $shippingMethods = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching shipping methods: " . $e->getMessage();
}

// Update cart logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantity'])) {
    foreach ($_POST['quantity'] as $index => $newQuantity) {
        $cartId = intval($_POST['cart_id'][$index]);
        $newQuantity = intval($newQuantity);

        // Fetch the available stock quantity for the product
        $stmt = $conn->prepare("SELECT s.Quantity FROM shopping_cart sc JOIN shades s ON sc.shade_id = s.shade_id WHERE sc.Cart_ID = ?");
        $stmt->execute([$cartId]);
        $stockQuantity = $stmt->fetchColumn();

        if ($newQuantity > 0 && $newQuantity <= $stockQuantity) {
            try {
                // Update the quantity in the shopping_cart table using Cart_ID
                $stmt = $conn->prepare("UPDATE shopping_cart SET Quantity = ? WHERE Cart_ID = ?");
                $stmt->execute([$newQuantity, $cartId]);

                // Update the session cart to reflect the new quantity
                $_SESSION['cart'][$index]['quantity'] = $newQuantity;
            } catch (PDOException $e) {
                echo "Error updating cart: " . $e->getMessage();
            }
        } else {
            echo "<script>alert('Invalid quantity or not enough stock available.');</script>";
        }
    }

    // Redirect back to cart page to reflect the updates
    header("Location: cart.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_cart_id'])) {
    $cartIdToRemove = intval($_POST['remove_cart_id']);

    try {
        // Remove from database
        $stmt = $conn->prepare("DELETE FROM shopping_cart WHERE Cart_ID = :cartIdToRemove");
        $stmt->bindParam(':cartIdToRemove', $cartIdToRemove, PDO::PARAM_INT);
        $stmt->execute();

        // Remove from session and reindex the array
        $_SESSION['cart'] = array_values(array_filter($_SESSION['cart'], function ($item) use ($cartIdToRemove) {
            return $item['cart_id'] !== $cartIdToRemove;
        }));

        // Redirect to refresh the page after removal
        header("Location: cart.php");
        exit();
    } catch (PDOException $e) {
        echo "Error removing item: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_all'])) {
    try {
        // Remove all items from the database
        $stmt = $conn->prepare("DELETE FROM shopping_cart WHERE Customer_ID = ?");
        $stmt->execute([$_SESSION['customer_id']]);

        // Clear the session cart
        $_SESSION['cart'] = [];

        // Redirect to refresh the page after clearing the cart
        header("Location: cart.php");
        exit();
    } catch (PDOException $e) {
        echo "Error clearing cart: " . $e->getMessage();
    }
}

// Fetch cart items again with image paths and stock quantities
$query = "SELECT sc.Cart_ID, p.Name AS Product_Name, p.Price, sc.Quantity, pi.image_path AS product_image_path, spi.image_path AS shade_image_path, p.Product_ID, p.Brand_ID, s.shade_name, s.Quantity AS stock_quantity
          FROM shopping_cart sc
          JOIN products p ON sc.Product_ID = p.Product_ID
          LEFT JOIN product_images pi ON p.Product_ID = pi.Product_ID AND pi.shade_id IS NULL
          LEFT JOIN shades s ON sc.shade_id = s.shade_id
          LEFT JOIN product_images spi ON s.shade_id = spi.shade_id
          WHERE sc.Customer_ID = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['customer_id']]);

// Populate the session cart with images and prevent duplicates
$_SESSION['cart'] = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $productExists = false;

    // Check if the product already exists in the cart (based on Cart_ID)
    foreach ($_SESSION['cart'] as $cartItem) {
        if ($cartItem['cart_id'] == $row['Cart_ID']) {
            // If product exists, just update the quantity
            $cartItem['quantity'] += $row['Quantity'];
            $productExists = true;
            break;
        }
    }

    // If product doesn't exist, add it to the cart
    if (!$productExists) {
        $_SESSION['cart'][] = [
            'cart_id' => $row['Cart_ID'],
            'product_name' => $row['Product_Name'],
            'price' => $row['Price'],
            'quantity' => $row['Quantity'],
            'image_path' => $row['shade_image_path'] ?: $row['product_image_path'],
            'product_id' => $row['Product_ID'],
            'brand_id' => $row['Brand_ID'],
            'shade_name' => $row['shade_name'],
            'stock_quantity' => $row['stock_quantity'],
        ];
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Customer/customer_css/style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Cart - Cosmetics Shop</title>
    <style>
        .cart-table {
            background-color: rgba(255, 255, 255, 0.8);
            border-collapse: collapse;
            width: 100%;
        }

        .cart-table th,
        .cart-table td {
            border: none;
            padding: 15px;
            text-align: left;
        }

        .cart-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .cart-table tbody tr:nth-child(even) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .total-cart {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            margin-top: 20px;
            max-width: 400px;
            margin-left: auto;
        }

        .total-cart h4 {
            font-weight: bold;
            margin-bottom: 20px;
        }

        .total-cart .total-row img {}

        .total-cart .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .total-cart .total-row span {
            display: inline-block;
            width: 100px;
            text-align: right;
        }

        .total-cart .checkout-btn {
            margin-top: 10px;
            width: 100%;
            padding: 10px;
            background-color: black;
            border-radius: 10px;
            border: none;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .total-cart .checkout-btn:hover {
            background-color: white;
            border: 1px solid black;
        }

        .total-cart .continue-shopping-btn {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border-radius: 10px;
            border: none;
            color: white;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        .total-cart .continue-shopping-btn:hover {
            background-color: #0056b3;
            color: white;
        }

        .btn-quantity {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border-radius: 50%;
        }

        .quantity-input {
            width: 50px;
            text-align: center;
        }

        .clear-all-btn {
            width: 100%;
            padding: 10px;
            background-color: black;
            border-radius: 10px;
            border: none;
            color: white;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s ease, color 0.3s ease, border 0.3s ease;
        }

        .clear-all-btn:hover {
            background-color: white;
            color: black;
            border: 1px solid black;
        }

        .faded-line {
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <?php include 'navbar.php' ?>



    <div class="container mt-5">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="user_homeIndex.php" style="color: black; text-decoration:none;">Home</a></li>
                <li class="breadcrumb-item"><a href="products.php" style="color: black; text-decoration:none;">Shop</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cart</li>
            </ol>
        </nav>
        <h3 class="text-center mb-4">Your Shopping Cart</h3>
        <div class="card shadow-lg">
            <div class="card-body" style="max-width: 100%; overflow-x: auto;">

                <form method="POST" action="cart.php" id="cart-form">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th></th>
                                <th>Shade</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalAmount = 0;
                            foreach ($_SESSION['cart'] as $index => $item) {
                                $total = $item['price'] * $item['quantity'];
                                $totalAmount += $total;
                            ?>
                                <tr id="cart-row-<?php echo $index; ?>">
                                    <td>
                                        <?php if (!empty($item['image_path'])): ?>
                                            <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="img-fluid" style="border-radius: 10px; object-fit: cover; height: 100px; width: 100px;">
                                        <?php else: ?>
                                            <img src="default-image.jpg" alt="Default Image" class="img-fluid" style="max-width: 100px;">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['shade_name'] ?? 'N/A'); ?></td>

                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <button class="btn btn-quantity btn-outline-secondary" type="button" onclick="updateQuantity(<?php echo $index; ?>, -1)">-</button>
                                            <input type="number" name="quantity[<?php echo $index; ?>]" class="form-control quantity-input mx-2" value="<?php echo $item['quantity']; ?>" id="quantity-<?php echo $index; ?>" readonly>
                                            <button class="btn btn-quantity btn-outline-secondary" type="button" onclick="updateQuantity(<?php echo $index; ?>, 1)">+</button>
                                        </div>
                                        <small class="text-muted">Stock: <?php echo $item['stock_quantity']; ?></small>
                                    </td>

                                    <td>$<?php echo number_format($total, 2); ?></td>

                                    <td>
                                        <button type="button" class="btn btn-danger" onclick="removeFromCart(<?php echo $item['cart_id']; ?>)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>

                                </tr>

                                <input type="hidden" name="cart_id[<?php echo $index; ?>]" value="<?php echo $item['cart_id']; ?>">
                            <?php } ?>
                        </tbody>
                    </table>
                </form>

                <form method="POST" action="cart.php" class="mt-3">
                    <button type="submit" name="clear_all" class="btn clear-all-btn">Clear All</button>
                </form>

                <div class="faded-line"></div>

                <div class="total-cart">
                    <h4>Cart Total</h4>
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span id="subtotal">$<?php echo number_format($totalAmount, 2); ?></span>
                    </div>
                    <div class="total-row">
                        <span>Shipping:</span>
                        <span id="shipping-cost">$<?php echo number_format($shippingMethods[0]['Cost'], 2); ?></span>
                    </div>
                    <div class="total-row">
                        <span>Total:</span>
                        <span id="total">$<?php echo number_format($totalAmount + $shippingMethods[0]['Cost'], 2); ?></span>
                    </div>
                    <select id="shipping_method" class="form-select mt-3" onchange="updateTotal()">
                        <?php foreach ($shippingMethods as $method): ?>
                            <option value="<?php echo $method['Cost']; ?>" data-id="<?php echo $method['Shipping_Method_ID']; ?>"><?php echo $method['Shipping_Method']; ?> - $<?php echo number_format($method['Cost'], 2); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <form method="POST" action="checkout.php">
                        <input type="hidden" name="selected_shipping_method" id="selected_shipping_method" value="<?php echo $shippingMethods[0]['Shipping_Method_ID']; ?>">
                        <button type="submit" class="btn checkout-btn">Proceed to Checkout</button>
                    </form>
                    <a href="products.php" class="btn continue-shopping-btn">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        function updateQuantity(index, change) {
            const quantityInput = document.getElementById('quantity-' + index);
            let currentQuantity = parseInt(quantityInput.value);
            let newQuantity = currentQuantity + change;
            const maxQuantity = <?php echo json_encode(array_column($_SESSION['cart'], 'stock_quantity')); ?>[index];

            if (newQuantity > 0 && newQuantity <= maxQuantity) {
                quantityInput.value = newQuantity;
                document.getElementById('cart-form').submit();
            } else {
                alert('Invalid quantity or not enough stock available.');
            }
        }

        function updateTotal() {
            const subtotal = parseFloat(document.getElementById('subtotal').innerText.replace('$', ''));
            const shipping = parseFloat(document.getElementById('shipping_method').value);
            const total = subtotal + shipping;
            document.getElementById('shipping-cost').innerText = `$${shipping.toFixed(2)}`;
            document.getElementById('total').innerText = `$${total.toFixed(2)}`;
            document.getElementById('selected_shipping_method').value = document.getElementById('shipping_method').selectedOptions[0].getAttribute('data-id');
        }

        function removeFromCart(cartId) {
            if (confirm('Are you sure you want to remove this item from the cart?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'cart.php';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remove_cart_id';
                input.value = cartId;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>


</body>

</html>