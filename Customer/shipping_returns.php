<?php
session_start();
require_once "../db_connect.php";

// Fetch shipping methods from the database
try {
    $stmt = $conn->query("SELECT Shipping_Method_ID, Shipping_Method, DeliveryTime, Cost FROM shippingmethods");
    $shippingMethods = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching shipping methods: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Customer/customer_css/style.css">
    <title>Shipping & Returns - Cosmetics Shop</title>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="user_homeIndex.php" style="color: black; text-decoration:none;">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Shipping & Returns</li>
            </ol>
        </nav>

        <h3 class="text-center mb-4">Shipping & Returns</h3>

        <div class="card shadow-lg mb-4">
            <div class="card-body">
                <h4>Shipping Methods</h4>
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th>Delivery Time</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($shippingMethods as $method) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($method['Shipping_Method']); ?></td>
                                <td><?php echo htmlspecialchars($method['DeliveryTime']); ?></td>
                                <td>$<?php echo number_format($method['Cost'], 2); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-lg mb-4">
            <div class="card-body">
                <h4>Returns Policy</h4>
                <p>We accept returns within 30 days of purchase. Products must be unopened and in their original packaging. Please contact our customer service for further instructions on how to proceed with a return.</p>
                <p>For any damaged or incorrect items received, please notify us within 7 days of receipt, and we will arrange for a replacement or refund.</p>
            </div>
        </div>

        <div class="text-center">
            <a href="contact.php" class="btn btn-primary">Contact Customer Service</a>
        </div>

    </div>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>