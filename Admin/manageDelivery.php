<?php
session_start();
require_once "../db_connect.php";
require_once "admin_auth.php";

$isAdmin = admin_is_logged_in();

function maskDeliveryValue($value, $visiblePrefix = 1)
{
    $value = trim((string) $value);
    if ($value === '') {
        return 'N/A';
    }

    return mb_substr($value, 0, $visiblePrefix) . str_repeat('*', max(mb_strlen($value) - $visiblePrefix, 3));
}

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

if (isset($_GET['shipping_id'], $_GET['action'])) {
    admin_require_login('manageDelivery.php');
}

// Fetch delivery records or filter by status
$status = $_GET['status'] ?? '';
$searchTerm = $_POST['searchTerm'] ?? '';
try {
    if ($searchTerm) {
        $deliveryQuery = "SELECT s.Shipping_ID, s.Order_ID, s.Shipping_Status, s.Shipping_Date, sm.Shipping_Method, c.Name AS Customer_Name,
                          GROUP_CONCAT(DISTINCT p.Name ORDER BY p.Name SEPARATOR ', ') AS Product_Names
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
                          GROUP BY s.Shipping_ID, s.Order_ID, s.Shipping_Status, s.Shipping_Date, sm.Shipping_Method, c.Name
                          ORDER BY s.Shipping_ID ASC";
        $deliveryStmt = $conn->prepare($deliveryQuery);
        $deliveryStmt->execute(['status' => '%' . $status . '%', 'searchTerm' => '%' . $searchTerm . '%']);
    } else {
        $deliveryQuery = "SELECT s.Shipping_ID, s.Order_ID, s.Shipping_Status, s.Shipping_Date, sm.Shipping_Method, c.Name AS Customer_Name,
                          GROUP_CONCAT(DISTINCT p.Name ORDER BY p.Name SEPARATOR ', ') AS Product_Names
                          FROM shipping s
                          JOIN order_items oi ON s.Order_ID = oi.Order_ID
                          JOIN products p ON oi.Product_ID = p.Product_ID
                          JOIN orders o ON s.Order_ID = o.Order_ID
                          JOIN customers c ON o.Customer_ID = c.Customer_ID
                          JOIN shippingmethods sm ON s.Shipping_Method_ID = sm.Shipping_Method_ID
                          WHERE s.Shipping_Status LIKE :status
                          GROUP BY s.Shipping_ID, s.Order_ID, s.Shipping_Status, s.Shipping_Date, sm.Shipping_Method, c.Name
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
    <style>
        .delivery-page-header {
            position: sticky;
            top: calc(var(--admin-topbar-height, 72px) + 12px);
            z-index: 20;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid #f0d5e3;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 22px;
            box-shadow: 0 12px 28px rgba(38, 38, 48, 0.08);
            backdrop-filter: blur(12px);
        }

        .delivery-page-title {
            color: #d97cb3;
            font-weight: 800;
            margin: 0;
        }

        .delivery-page-subtitle {
            color: #777;
            margin: 6px 0 0;
        }

        .delivery-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-top: 16px;
        }

        .delivery-filter-actions,
        .delivery-search {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .delivery-search {
            flex: 1;
            min-width: min(100%, 360px);
        }

        .delivery-search .form-control {
            min-width: 0;
        }

        .delivery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 360px), 1fr));
            gap: 18px;
        }

        .delivery-card {
            background: #ffffff;
            border: 1px solid #ead8e3;
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 12px 28px rgba(38, 38, 48, 0.08);
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .delivery-card:hover {
            transform: translateY(-3px);
            border-color: #d97cb3;
            box-shadow: 0 18px 34px rgba(38, 38, 48, 0.14);
        }

        .delivery-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .delivery-id {
            color: #d97cb3;
            font-weight: 800;
            margin: 0;
        }

        .delivery-status {
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 0.82rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .delivery-status.delivered {
            background: rgba(25, 135, 84, 0.14);
            color: #167448;
        }

        .delivery-status.processing {
            background: rgba(255, 193, 7, 0.2);
            color: #8a6200;
        }

        .delivery-status.cancelled {
            background: rgba(220, 53, 69, 0.14);
            color: #b02a37;
        }

        .delivery-details {
            display: grid;
            gap: 10px;
        }

        .delivery-detail {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            border-bottom: 1px solid #f4e3ec;
            padding-bottom: 8px;
        }

        .delivery-detail span {
            color: #777;
        }

        .delivery-detail strong {
            color: #222;
            text-align: right;
        }

        .delivery-products {
            margin-top: 12px;
            color: #555;
            line-height: 1.5;
        }

        .delivery-card-actions {
            display: flex;
            gap: 10px;
            margin-top: 16px;
            flex-wrap: wrap;
        }

        .delivery-card-actions .btn {
            flex: 1;
            min-width: 120px;
        }

        .delivery-locked-note {
            color: #777;
            font-size: 0.84rem;
            margin-top: 10px;
        }

        html[data-theme="dark"] .delivery-page-header,
        html[data-theme="dark"] .delivery-card {
            background: rgba(31, 31, 39, 0.96);
            border-color: #343442;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.38);
        }

        html[data-theme="dark"] .delivery-page-subtitle,
        html[data-theme="dark"] .delivery-detail span,
        html[data-theme="dark"] .delivery-products,
        html[data-theme="dark"] .delivery-locked-note {
            color: #b8bcc6;
        }

        html[data-theme="dark"] .delivery-detail {
            border-color: #343442;
        }

        html[data-theme="dark"] .delivery-detail strong {
            color: #f4f4f5;
        }

        html[data-theme="dark"] .delivery-status.delivered {
            color: #75d59e;
        }

        html[data-theme="dark"] .delivery-status.processing {
            color: #ffd35f;
        }

        html[data-theme="dark"] .delivery-status.cancelled {
            color: #ff8b99;
        }

        @media (max-width: 768px) {
            .delivery-page-header {
                top: calc(var(--admin-topbar-height, 72px) + 8px);
            }

            .delivery-toolbar,
            .delivery-search {
                flex-direction: column;
                align-items: stretch;
            }

            .delivery-filter-actions {
                width: 100%;
            }

            .delivery-filter-actions .btn {
                flex: 1;
            }
        }
    </style>
</head>

<body>

    <?php include 'sidebar_nav.php'; ?>


    <!-- Main Content -->
    <div id="main-content">
        <div class="container mt-4">
            <section class="delivery-page-header">
                <div>
                    <h2 class="delivery-page-title">Manage Deliveries</h2>
                    <p class="delivery-page-subtitle">
                        <?php echo $isAdmin ? 'Review delivery progress and update shipping outcomes.' : 'Read-only portfolio preview. Customer and order details are protected.'; ?>
                    </p>
                </div>

                <div class="delivery-toolbar">
                    <div class="delivery-filter-actions">
                        <a href="manageDelivery.php" class="btn btn-outline-primary">All</a>
                        <a href="manageDelivery.php?status=Delivered" class="btn btn-success">Delivered</a>
                        <a href="manageDelivery.php?status=Processing" class="btn btn-warning">Processing</a>
                    </div>

                    <form method="POST" action="manageDelivery.php" class="delivery-search">
                        <input type="text" name="searchTerm" class="form-control" placeholder="<?php echo $isAdmin ? 'Search shipment, order, customer, method...' : 'Search shipment status or method...'; ?>" value="<?php echo htmlspecialchars($searchTerm); ?>">
                        <button type="submit" class="btn btn-dark">Search</button>
                    </form>
                </div>
            </section>

            <div class="delivery-grid">
                <?php foreach ($deliveries as $record): ?>
                    <?php
                    $shippingId = htmlspecialchars($record['Shipping_ID']);
                    $rawStatus = $record['Shipping_Status'] ?? 'Processing';
                    $statusClass = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $rawStatus));
                    $orderId = $isAdmin ? htmlspecialchars($record['Order_ID']) : 'Locked';
                    $customerName = $isAdmin ? htmlspecialchars($record['Customer_Name']) : htmlspecialchars(maskDeliveryValue($record['Customer_Name'] ?? 'Customer'));
                    $products = $isAdmin ? htmlspecialchars($record['Product_Names'] ?? 'No products listed') : 'Login required to view product details';
                    $shippingDate = !empty($record['Shipping_Date']) ? date('M j, Y g:i A', strtotime($record['Shipping_Date'])) : 'Not scheduled';
                    ?>
                    <div class="delivery-card" data-bs-toggle="modal" data-bs-target="#managedeliveryModal<?php echo $shippingId; ?>">
                        <div class="delivery-card-top">
                            <h5 class="delivery-id">Shipment #<?php echo $shippingId; ?></h5>
                            <span class="delivery-status <?php echo htmlspecialchars($statusClass); ?>"><?php echo htmlspecialchars($rawStatus); ?></span>
                        </div>

                        <div class="delivery-details">
                            <div class="delivery-detail">
                                <span>Order</span>
                                <strong><?php echo $orderId; ?></strong>
                            </div>
                            <div class="delivery-detail">
                                <span>Customer</span>
                                <strong><?php echo $customerName; ?></strong>
                            </div>
                            <div class="delivery-detail">
                                <span>Method</span>
                                <strong><?php echo htmlspecialchars($record['Shipping_Method']); ?></strong>
                            </div>
                            <div class="delivery-detail">
                                <span>Ship Date</span>
                                <strong><?php echo htmlspecialchars($shippingDate); ?></strong>
                            </div>
                        </div>

                        <div class="delivery-products">
                            <strong>Products:</strong> <?php echo $products; ?>
                        </div>

                        <div class="delivery-card-actions" onclick="event.stopPropagation();">
                            <?php if ($isAdmin): ?>
                                <a href="?shipping_id=<?php echo $shippingId; ?>&action=accept" class="btn btn-success">Mark Delivered</a>
                                <a href="?shipping_id=<?php echo $shippingId; ?>&action=cancel" class="btn btn-outline-danger">Cancel</a>
                            <?php else: ?>
                                <a href="adminLogin.php?return_to=manageDelivery.php" class="btn btn-secondary">
                                    <i class="fa fa-lock"></i> Login to Update
                                </a>
                            <?php endif; ?>
                        </div>

                        <?php if (!$isAdmin): ?>
                            <div class="delivery-locked-note">Order ID, products, and full customer name are hidden for visitors.</div>
                        <?php endif; ?>
                    </div>

                    <!-- Modal for Delivery Details -->
                    <div class="modal fade managedelivery-modal" id="managedeliveryModal<?php echo $shippingId; ?>" tabindex="-1" aria-labelledby="managedeliveryModalLabel<?php echo $shippingId; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="managedeliveryModalLabel<?php echo $shippingId; ?>">Delivery Details for Shipment #<?php echo $shippingId; ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Order ID:</strong> <?php echo $orderId; ?></p>
                                    <p><strong>Shipping Status:</strong> <?php echo htmlspecialchars($record['Shipping_Status']); ?></p>
                                    <p><strong>Shipping Date:</strong> <?php echo htmlspecialchars($shippingDate); ?></p>
                                    <p><strong>Shipping Method:</strong> <?php echo htmlspecialchars($record['Shipping_Method']); ?></p>
                                    <p><strong>Customer Name:</strong> <?php echo $customerName; ?></p>
                                    <p><strong>Products:</strong> <?php echo $products; ?></p>
                                </div>
                                <div class="modal-footer">
                                    <?php if ($isAdmin): ?>
                                        <a href="?shipping_id=<?php echo $shippingId; ?>&action=accept" class="btn btn-success">Mark Delivered</a>
                                        <a href="?shipping_id=<?php echo $shippingId; ?>&action=cancel" class="btn btn-danger">Cancel Delivery</a>
                                    <?php else: ?>
                                        <a href="adminLogin.php?return_to=manageDelivery.php" class="btn btn-secondary">
                                            <i class="fa fa-lock"></i> Login Required
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($deliveries)): ?>
                    <div class="delivery-card">
                        <h5 class="delivery-id">No deliveries found</h5>
                        <p class="delivery-products mb-0">Try another status filter or search keyword.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
