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

// Fetch the delivery method to edit
if (isset($_GET['id'])) {
    $shippingMethodID = $_GET['id'];

    try {
        $fetchQuery = "SELECT * FROM shippingmethods WHERE Shipping_Method_ID = :id";
        $fetchStmt = $conn->prepare($fetchQuery);
        $fetchStmt->bindParam(':id', $shippingMethodID);
        $fetchStmt->execute();
        $method = $fetchStmt->fetch(PDO::FETCH_ASSOC);

        if (!$method) {
            die("Delivery method not found.");
        }
    } catch (PDOException $e) {
        die("Error fetching delivery method: " . $e->getMessage());
    }
}

// Handle form submission for editing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shippingMethodID = $_POST['shippingMethodID'];
    $shippingMethod = $_POST['shippingMethod'];
    $deliveryTime = $_POST['deliveryTime'];
    $cost = $_POST['cost'];

    try {
        $updateQuery = "UPDATE shippingmethods 
                        SET Shipping_Method = :shippingMethod, 
                            DeliveryTime = :deliveryTime, 
                            Cost = :cost 
                        WHERE Shipping_Method_ID = :id";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':shippingMethod', $shippingMethod);
        $updateStmt->bindParam(':deliveryTime', $deliveryTime);
        $updateStmt->bindParam(':cost', $cost);
        $updateStmt->bindParam(':id', $shippingMethodID);
        $updateStmt->execute();

        echo "<script>alert('Delivery method updated successfully !');</script>";
        echo "<script>window.location.href = 'deliveryMethods.php';</script>";
    } catch (PDOException $e) {
        die("Error updating delivery method: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <title>Edit Delivery Method</title>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>

    <!-- Main Content -->
    <div id="main-content">
        <div class="container mt-4">
            <h2 class="text-center">Edit Delivery Method</h2>
            <form method="POST" action="">
                <input type="hidden" name="shippingMethodID" value="<?php echo htmlspecialchars($method['Shipping_Method_ID']); ?>">
                <div class="mb-3">
                    <label for="shippingMethod" class="form-label">Shipping Method</label>
                    <input type="text" class="form-control" id="shippingMethod" name="shippingMethod" value="<?php echo htmlspecialchars($method['Shipping_Method']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="deliveryTime" class="form-label">Delivery Time</label>
                    <input type="text" class="form-control" id="deliveryTime" name="deliveryTime" value="<?php echo htmlspecialchars($method['DeliveryTime']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="cost" class="form-label">Cost</label>
                    <input type="number" class="form-control" id="cost" name="cost" value="<?php echo htmlspecialchars($method['Cost']); ?>" step="0.01" required>
                </div>
                <button type="submit" class="btn btn-success">Update Delivery Method</button>
                <a href="deliveryMethods.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../Admin/admin_Javascript/sidebar.js"></script>
</body>

</html>