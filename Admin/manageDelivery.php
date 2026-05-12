<?php
session_start();
require_once "../db_connect.php";

$server = getenv('DB_HOST');
$user = getenv('DB_USER');
$password = getenv('DB_PASS');
$database = getenv('DB_NAME');
$port = getenv('DB_PORT') ?: 3306;

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

if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    // If not logged in, redirect to login page
    echo "<script>alert('Please log in as an admin.');</script>";
    echo "<script>window.location.href = 'adminLogin.php';</script>";
    exit();
}

// Fetch delivery records or filter by status
$status = $_GET['status'] ?? '';
$searchTerm = $_POST['searchTerm'] ?? '';
try {
    if ($searchTerm) {
        $deliveryQuery = "SELECT s.Shipping_ID, s.Order_ID, s.Shipping_Status, s.Shipping_Date, sm.Shipping_Method, p.Name AS Product_Name, c.Name AS Customer_Name
                          FROM shipping s
                          JOIN order_items oi ON s.Order_ID = oi.Order_ID
                          JOIN products p ON oi.Product_ID = p.Product_ID
                          JOIN orders o ON s.Order_ID = o.Order_ID
                          JOIN customers c ON o.Customer_ID = c.Customer_ID
                          JOIN shippingmethods sm ON s.Shipping_Method_ID = sm.Shipping_Method_ID
                          WHERE s.Shipping_Status LIKE :status 
                          AND (s.Shipping_ID LIKE :searchTerm 
                          OR s.Order_ID LIKE :searchTerm 
                          OR s.Shipping_Status LIKE :searchTerm
                          OR s.Shipping_Date LIKE :searchTerm 
                          OR sm.Shipping_Method LIKE :searchTerm 
                          OR c.Name LIKE :searchTerm)
                          ORDER BY s.Shipping_ID ASC";
        $deliveryStmt = $conn->prepare($deliveryQuery);
        $deliveryStmt->execute(['status' => '%' . $status . '%', 'searchTerm' => '%' . $searchTerm . '%']);
    } else {
        $deliveryQuery = "SELECT s.Shipping_ID, s.Order_ID, s.Shipping_Status, s.Shipping_Date, sm.Shipping_Method, p.Name AS Product_Name, c.Name AS Customer_Name
                          FROM shipping s
                          JOIN order_items oi ON s.Order_ID = oi.Order_ID
                          JOIN products p ON oi.Product_ID = p.Product_ID
                          JOIN orders o ON s.Order_ID = o.Order_ID
                          JOIN customers c ON o.Customer_ID = c.Customer_ID
                          JOIN shippingmethods sm ON s.Shipping_Method_ID = sm.Shipping_Method_ID
                          WHERE s.Shipping_Status LIKE :status
                          ORDER BY s.Shipping_ID ASC";
        $deliveryStmt = $conn->prepare($deliveryQuery);
        $deliveryStmt->execute(['status' => '%' . $status . '%']);
    }
    $deliveries = $deliveryStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching deliveries: " . $e->getMessage());
}

// Check if shipping ID is passed
if (isset($_GET['shipping_id'])) {
    $shippingID = $_GET['shipping_id'];

    // Check if action is passed
    if (isset($_GET['action'])) {
        $action = $_GET['action'];

        if ($action == 'accept') {
            try {
                // Update shipping status to Delivered
                $updateStatusQuery = "UPDATE shipping SET Shipping_Status = 'Delivered' WHERE Shipping_ID = ?";
                $updateStatusStmt = $conn->prepare($updateStatusQuery);
                $updateStatusStmt->execute([$shippingID]);

                echo "<script>alert('Shipping status updated to Delivered.');</script>";
                echo "<script>window.location.href = 'manageDelivery.php';</script>";
            } catch (PDOException $e) {
                die("Error updating shipping status: " . $e->getMessage());
            }
        } elseif ($action == 'cancel') {
            try {
                // Update shipping status to Cancelled
                $updateStatusQuery = "UPDATE shipping SET Shipping_Status = 'Cancelled' WHERE Shipping_ID = ?";
                $updateStatusStmt = $conn->prepare($updateStatusQuery);
                $updateStatusStmt->execute([$shippingID]);

                echo "<script>alert('Shipping status updated to Cancelled.');</script>";
                echo "<script>window.location.href = 'manageDelivery.php';</script>";
            } catch (PDOException $e) {
                die("Error updating shipping status: " . $e->getMessage());
            }
        }
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
    <title>Manage Delivery</title>
</head>

<body>

    <?php include 'sidebar_nav.php'; ?>


    <!-- Main Content -->
    <div id="main-content">
        <!-- Main Content -->
        <div class="container mt-4">
            <a href="manageDelivery.php" class="text-decoration-none">
                <h2 class="text-center">Manage Deliveries</h2>
            </a>
            <div class="text-center mb-3">
                <a href="manageDelivery.php?status=Delivered" class="btn btn-success">Delivered Orders</a>
                <a href="manageDelivery.php?status=Processing" class="btn btn-warning">Processing Orders</a>
            </div>
            <form method="POST" action="manageDelivery.php" class="mb-3">
                <div class="input-group">
                    <input type="text" name="searchTerm" class="form-control" placeholder="Search for deliveries..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                    <button type="submit" class="btn btn-dark">Search</button>
                </div>
            </form>
            <!-- Delivery Records List -->
            <div class="list-group">
                <?php foreach ($deliveries as $record): ?>
                    <div class="managedelivery-card list-group-item" data-bs-toggle="modal" data-bs-target="#managedeliveryModal<?php echo $record['Shipping_ID']; ?>">
                        <h5 class="mb-1">Shipping ID: <?php echo htmlspecialchars($record['Shipping_ID']); ?></h5>
                        <p class="mb-1">Order ID: <?php echo htmlspecialchars($record['Order_ID']); ?></p>
                        <p class="mb-1">Shipping Status: <?php echo htmlspecialchars($record['Shipping_Status']); ?></p>
                        <p class="mb-1">Shipping Date: <?php echo htmlspecialchars($record['Shipping_Date']); ?></p>
                        <p class="mb-1">Shipping Method: <?php echo htmlspecialchars($record['Shipping_Method']); ?></p>
                        <p class="mb-1">Customer Name: <?php echo htmlspecialchars($record['Customer_Name']); ?></p>
                    </div>

                    <!-- Modal for Delivery Details -->
                    <div class="modal fade managedelivery-modal" id="managedeliveryModal<?php echo $record['Shipping_ID']; ?>" tabindex="-1" aria-labelledby="managedeliveryModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="managedeliveryModalLabel">Delivery Details for Shipping ID: <?php echo htmlspecialchars($record['Shipping_ID']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class=modal-body>
                                    <p><strong>Order ID:</strong> <?php echo htmlspecialchars($record['Order_ID']); ?></p>
                                    <p><strong>Shipping Status:</strong> <?php echo htmlspecialchars($record['Shipping_Status']); ?></p>
                                    <p><strong>Shipping Date:</strong> <?php echo htmlspecialchars($record['Shipping_Date']); ?></p>
                                    <p><strong>Shipping Method:</strong> <?php echo htmlspecialchars($record['Shipping_Method']); ?></p>
                                    <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($record['Customer_Name']); ?></p>
                                </div>
                                <div class=modal-footer>
                                    <a href="?shipping_id=<?php echo $record['Shipping_ID']; ?>&action=accept" class="btn btn-success">Accept</a>
                                    <a href="?shipping_id=<?php echo $record['Shipping_ID']; ?>&action=cancel" class="btn btn-danger">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>