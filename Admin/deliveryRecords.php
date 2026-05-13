<?php
session_start();
require_once "../db_connect.php";
require_once "admin_auth.php";

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

// Fetch all delivery records
try {
    $deliveryRecordsQuery = "SELECT s.Shipping_ID, s.Order_ID, s.Shipping_Status, s.Shipping_Date, sm.Shipping_Method
                FROM shipping s
                JOIN shippingmethods sm ON s.Shipping_Method_ID = sm.Shipping_Method_ID
                ORDER BY s.Shipping_ID ASC";

    $deliveryRecordsStmt = $conn->prepare($deliveryRecordsQuery);
    $deliveryRecordsStmt->execute();
    $deliveryRecords = $deliveryRecordsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching delivery records: " . $e->getMessage());
}

// Filter delivery records by status
if (isset($_GET['status'])) {
    $status = $_GET['status'];

    if ($status == 'processing') {
        $deliveryRecordsQuery = "SELECT s.Shipping_ID, s.Order_ID, s.Shipping_Status, s.Shipping_Date, sm.Shipping_Method
                    FROM shipping s
                    JOIN shippingmethods sm ON s.Shipping_Method_ID = sm.Shipping_Method_ID
                    WHERE s.Shipping_Status = 'Processing'
                    ORDER BY s.Shipping_ID ASC";

        $deliveryRecordsStmt = $conn->prepare($deliveryRecordsQuery);
        $deliveryRecordsStmt->execute();
        $deliveryRecords = $deliveryRecordsStmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($status == 'delivered') {
        $deliveryRecordsQuery = "SELECT s.Shipping_ID, s.Order_ID, s.Shipping_Status, s.Shipping_Date, sm.Shipping_Method
                    FROM shipping s
                    JOIN shippingmethods sm ON s.Shipping_Method_ID = sm.Shipping_Method_ID
                    WHERE s.Shipping_Status = 'Delivered'
                    ORDER BY s.Shipping_ID ASC";

        $deliveryRecordsStmt = $conn->prepare($deliveryRecordsQuery);
        $deliveryRecordsStmt->execute();
        $deliveryRecords = $deliveryRecordsStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Display delivery records
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
    <title>Delivery Records</title>
</head>

<body>

    <?php include 'sidebar_nav.php'; ?>


    <div id="main-content">
        <div class="container mt-4">
            <h2>Delivery Records</h2>

            <div class="view-orders-status-buttons">
                <a href="deliveryRecords.php?status=processing" class="view-orders-btn">Processing</a>
                <a href="deliveryRecords.php?status=delivered" class="view-orders-btn">Delivered</a>
            </div>


            <table class="table table-bordered view-orders-table">
                <thead>
                    <tr>
                        <th>Shipping ID</th>
                        <th>Order ID</th>
                        <th>Shipping Status</th>
                        <th>Start Shipping Date</th>
                        <th>Shipping Method</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($deliveryRecords as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['Shipping_ID']); ?></td>
                            <td><?php echo htmlspecialchars($record['Order_ID']); ?></td>
                            <td><?php echo htmlspecialchars($record['Shipping_Status']); ?></td>
                            <td><?php echo htmlspecialchars($record['Shipping_Date']); ?></td>
                            <td><?php echo htmlspecialchars($record['Shipping_Method']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>

</body>

</html>
