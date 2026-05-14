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

$defaultShippingMethod = $shippingMethods[0] ?? [
    'Shipping_Method_ID' => '',
    'Shipping_Method' => 'Shipping unavailable',
    'Cost' => 0,
];

// Update cart logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantity'])) {
    foreach ($_POST['quantity'] as $index => $newQuantity) {
        $cartId = intval($_POST['cart_id'][$index]);
        $newQuantity = intval($newQuantity);

        // Shade-specific stock may be empty for products added without a selected shade.
        $stmt = $conn->prepare("SELECT s.Quantity FROM shopping_cart sc LEFT JOIN shades s ON sc.shade_id = s.shade_id WHERE sc.Cart_ID = ?");
        $stmt->execute([$cartId]);
        $stockQuantity = $stmt->fetchColumn();

        if ($newQuantity > 0 && ($stockQuantity === false || $stockQuantity === null || $newQuantity <= (int)$stockQuantity)) {
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
        $_SESSION['cart'] = array_values(array_filter($_SESSION['cart'] ?? [], function ($item) use ($cartIdToRemove) {
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
    <link rel="stylesheet" href="../Customer/customer_css/style.css?v=20260514-cart-profile">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Cart - Cosmetics Shop</title>
</head>

<body class="cart-page">

    <?php include 'navbar.php' ?>
    <main class="container cart-page-container">
        <nav class="cart-breadcrumb" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="user_homeIndex.php">Home</a></li>
                <li class="breadcrumb-item"><a href="products.php">Shop</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cart</li>
            </ol>
        </nav>
        <section class="cart-hero">
            <span>Shopping Bag</span>
            <h1>Your Shopping Cart</h1>
            <p>Review quantities, shipping, and your order total before checkout.</p>
        </section>

        <?php
        $totalAmount = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }
        ?>

        <?php if (empty($_SESSION['cart'])): ?>
            <section class="cart-empty-state">
                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                <h2>Your cart is empty</h2>
                <p>Add products you love and they will appear here.</p>
                <a href="products.php" class="cart-primary-link">Start Shopping</a>
            </section>
        <?php else: ?>
            <div class="cart-layout">
                <section class="cart-items-panel">
                    <form method="POST" action="cart.php" id="cart-form">
                        <div class="cart-items-list">
                            <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                                <?php $total = $item['price'] * $item['quantity']; ?>
                                <article class="cart-item-card" id="cart-row-<?php echo $index; ?>">
                                    <div class="cart-item-image">
                                        <?php if (!empty($item['image_path'])): ?>
                                            <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                        <?php else: ?>
                                            <img src="../images/default-image.jpg" alt="Default Image">
                                        <?php endif; ?>
                                    </div>

                                    <div class="cart-item-info">
                                        <h2><?php echo htmlspecialchars($item['product_name']); ?></h2>
                                        <p>Shade: <?php echo htmlspecialchars($item['shade_name'] ?? 'N/A'); ?></p>
                                        <span>$<?php echo number_format($item['price'], 2); ?></span>
                                    </div>

                                    <div class="cart-item-quantity">
                                        <div class="cart-quantity-controls">
                                            <button class="btn btn-quantity" type="button" onclick="updateQuantity(<?php echo $index; ?>, -1)" aria-label="Decrease quantity">-</button>
                                            <input type="number" name="quantity[<?php echo $index; ?>]" class="form-control quantity-input" value="<?php echo $item['quantity']; ?>" id="quantity-<?php echo $index; ?>" readonly>
                                            <button class="btn btn-quantity" type="button" onclick="updateQuantity(<?php echo $index; ?>, 1)" aria-label="Increase quantity">+</button>
                                        </div>
                                        <small>Stock: <?php echo $item['stock_quantity'] ?? 'N/A'; ?></small>
                                    </div>

                                    <div class="cart-item-total">
                                        <span>Item Total</span>
                                        <strong>$<?php echo number_format($total, 2); ?></strong>
                                    </div>

                                    <button type="button" class="cart-remove-btn" onclick="removeFromCart(<?php echo $item['cart_id']; ?>)" aria-label="Remove <?php echo htmlspecialchars($item['product_name']); ?>">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </article>
                                <input type="hidden" name="cart_id[<?php echo $index; ?>]" value="<?php echo $item['cart_id']; ?>">
                            <?php endforeach; ?>
                        </div>
                    </form>

                    <form method="POST" action="cart.php" class="cart-clear-form">
                        <button type="submit" name="clear_all" class="cart-clear-btn">Clear All</button>
                    </form>
                </section>

                <aside class="total-cart cart-summary-panel">
                    <h4>Cart Total</h4>
                    <div class="total-row">
                        <span>Subtotal</span>
                        <strong id="subtotal">$<?php echo number_format($totalAmount, 2); ?></strong>
                    </div>
                    <div class="total-row">
                        <span>Shipping</span>
                        <strong id="shipping-cost">$<?php echo number_format($defaultShippingMethod['Cost'], 2); ?></strong>
                    </div>
                    <div class="total-row total-row-grand">
                        <span>Total</span>
                        <strong id="total">$<?php echo number_format($totalAmount + $defaultShippingMethod['Cost'], 2); ?></strong>
                    </div>

                    <label for="shipping_method" class="cart-summary-label">Shipping Method</label>
                    <select id="shipping_method" class="form-select" onchange="updateTotal()">
                        <?php foreach ($shippingMethods as $method): ?>
                            <option value="<?php echo $method['Cost']; ?>" data-id="<?php echo $method['Shipping_Method_ID']; ?>"><?php echo $method['Shipping_Method']; ?> - $<?php echo number_format($method['Cost'], 2); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <form method="POST" action="checkout.php">
                        <input type="hidden" name="selected_shipping_method" id="selected_shipping_method" value="<?php echo $defaultShippingMethod['Shipping_Method_ID']; ?>">
                        <button type="submit" class="btn checkout-btn" <?php echo empty($shippingMethods) ? 'disabled' : ''; ?>>Proceed to Checkout</button>
                    </form>
                    <a href="products.php" class="btn continue-shopping-btn">Continue Shopping</a>
                </aside>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>

    <script>
        function updateQuantity(index, change) {
            const quantityInput = document.getElementById('quantity-' + index);
            let currentQuantity = parseInt(quantityInput.value);
            let newQuantity = currentQuantity + change;
            const maxQuantity = <?php echo json_encode(array_column($_SESSION['cart'], 'stock_quantity')); ?>[index];

            if (newQuantity > 0 && (maxQuantity === null || maxQuantity === '' || newQuantity <= maxQuantity)) {
                quantityInput.value = newQuantity;
                document.getElementById('cart-form').submit();
            } else {
                alert('Invalid quantity or not enough stock available.');
            }
        }

        function updateTotal() {
            const subtotal = parseFloat(document.getElementById('subtotal').innerText.replace('$', ''));
            const shippingMethod = document.getElementById('shipping_method');
            if (!shippingMethod || !shippingMethod.selectedOptions.length) {
                return;
            }
            const shipping = parseFloat(shippingMethod.value) || 0;
            const total = subtotal + shipping;
            document.getElementById('shipping-cost').innerText = `$${shipping.toFixed(2)}`;
            document.getElementById('total').innerText = `$${total.toFixed(2)}`;
            document.getElementById('selected_shipping_method').value = shippingMethod.selectedOptions[0].getAttribute('data-id') || '';
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
