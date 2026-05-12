<?php
session_start();
require_once "../db_connect.php";

$servername = 'localhost';
$username = 'root';
$password = '';
$database = 'cosmetics_store';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
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

// Fetch all delivery methods or search delivery methods
$searchTerm = $_POST['searchTerm'] ?? '';
try {
    if ($searchTerm) {
        $deliveryMethodsQuery = "SELECT * FROM shippingmethods WHERE Shipping_Method LIKE :searchTerm";
        $deliveryMethodsStmt = $conn->prepare($deliveryMethodsQuery);
        $deliveryMethodsStmt->execute(['searchTerm' => '%' . $searchTerm . '%']);
    } else {
        $deliveryMethodsQuery = "SELECT * FROM shippingmethods";
        $deliveryMethodsStmt = $conn->prepare($deliveryMethodsQuery);
        $deliveryMethodsStmt->execute();
    }
    $deliveryMethods = $deliveryMethodsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching delivery methods: " . $e->getMessage());
}

// Edit delivery method
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editDeliveryMethod'])) {
    $shippingMethodID = $_POST['Shipping_Method_ID'];
    $shippingMethod = $_POST['Shipping_Method'];
    $deliveryTime = $_POST['DeliveryTime'];
    $cost = $_POST['Cost'];

    try {
        $editQuery = "UPDATE shippingmethods 
                      SET Shipping_Method = :shippingMethod, 
                          DeliveryTime = :deliveryTime, 
                          Cost = :cost 
                      WHERE Shipping_Method_ID = :id";
        $editStmt = $conn->prepare($editQuery);
        $editStmt->bindParam(':shippingMethod', $shippingMethod);
        $editStmt->bindParam(':deliveryTime', $deliveryTime);
        $editStmt->bindParam(':cost', $cost);
        $editStmt->bindParam(':id', $shippingMethodID);
        $editStmt->execute();

        echo "<script>alert('Delivery method updated successfully!');</script>";
        echo "<script>window.location.href = 'deliveryMethods.php';</script>";
    } catch (PDOException $e) {
        die("Error updating delivery method: " . $e->getMessage());
    }
}

// Delete delivery method
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteDeliveryMethod'])) {
    $shippingMethodID = $_POST['Shipping_Method_ID'];

    try {
        $deleteQuery = "DELETE FROM shippingmethods WHERE Shipping_Method_ID = :id";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bindParam(':id', $shippingMethodID);
        $deleteStmt->execute();

        echo "<script>alert('Delivery method deleted successfully!');</script>";
        echo "<script>window.location.href = 'deliveryMethods.php';</script>";
    } catch (PDOException $e) {
        die("Error deleting delivery method: " . $e->getMessage());
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
    <link rel="stylesheet" href="../backEnd/backEnd_css/style.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../backEnd/backend_Javascript/sidebar.js"></script>
    <link rel="icon" href="path/to/favicon.ico">
    <title>Delivery Methods</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .container {
            margin: 0 auto;
            padding: 20px;
        }

        h2 {
            color: #d97cb3;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #d97cb3;
            border-color: #d97cb3;
            color: #fff;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #c2185b;
            border-color: #c2185b;
        }

        .btn-primary a {
            color: #fff;
            text-decoration: none;
        }

        .btn-primary a:hover {
            color: #fff;
            text-decoration: none;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 10px;
        }

        .input-group .btn-primary {
            border-radius: 8px;
            padding: 10px 20px;
        }

        .table-container {
            max-height: 400px;
            overflow-y: auto;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
            border-radius: 12px;
            overflow: hidden;
        }

        .table th,
        .table td {
            padding: 0.5rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
            text-align: center;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
            background-color: rgb(191, 132, 166);
            color: #fff;
        }

        .table tbody+tbody {
            border-top: 2px solid #dee2e6;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .btn-link {
            color: #d97cb3;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .btn-link:hover {
            color: #c2185b;
            text-decoration: underline;
        }

        .btn-link:focus,
        .btn-link:active {
            color: #c2185b;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>

    <div class="container mt-4">
        <a href="deliveryMethods.php" class="text-decoration-none">
            <h2 class="text-center">Shipping Methods</h2>
        </a>

        <form method="POST" action="deliveryMethods.php" class="mb-3">
            <div class="input-group">
                <input type="text" name="searchTerm" class="form-control" placeholder="Search for shipping methods..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                <button type="submit" class="btn btn-dark">Search</button>
            </div>
        </form>

        <div class="text-end mb-3">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#insertDeliveryMethodModal">
                <i class="fa fa-plus"></i> Insert Delivery Method
            </button>
        </div>

        <div class="delivery-methods-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Shipping Method ID</th>
                        <th>Shipping Method</th>
                        <th>Delivery Time</th>
                        <th>Cost</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($deliveryMethods as $method) {
                        $shippingMethodID = htmlspecialchars($method['Shipping_Method_ID']);
                        $shippingMethod = htmlspecialchars($method['Shipping_Method']);
                        $deliveryTime = htmlspecialchars($method['DeliveryTime']);
                        $cost = number_format($method['Cost'], 2);

                        echo "
                            <tr>
                                <td>{$shippingMethodID}</td>
                                <td>{$shippingMethod}</td>
                                <td>{$deliveryTime}</td>
                                <td>\${$cost}</td>
                                <td>
                                    <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editDeliveryMethodModal{$shippingMethodID}'>
                                        <i class='fa fa-edit'></i> Edit
                                    </button>
                                    <button class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#deleteDeliveryMethodModal{$shippingMethodID}'>
                                        <i class='fa fa-trash'></i> Delete
                                    </button>
                                </td>
                            </tr>";

                        // Edit Modal
                        echo "<div class='modal fade' id='editDeliveryMethodModal{$shippingMethodID}' tabindex='-1' aria-labelledby='editDeliveryMethodModalLabel' aria-hidden='true'>
                                <div class='modal-dialog'>
                                    <div class='modal-content'>
                                        <div class='modal-header'>
                                            <h5 class='modal-title' id='editDeliveryMethodModalLabel'>Edit Shipping Method</h5>
                                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                        </div>
                                        <div class='modal-body'>
                                            <form method='POST' action='deliveryMethods.php'>
                                                <input type='hidden' name='Shipping_Method_ID' value='{$shippingMethodID}'>
                                                <div class='mb-3'>
                                                    <label for='Shipping_Method' class='form-label'>Shipping Method</label>
                                                    <input type='text' class='form-control' id='Shipping_Method' name='Shipping_Method' value='{$shippingMethod}' required>
                                                </div>
                                                <div class='mb-3'>
                                                    <label for='DeliveryTime' class='form-label'>Shipping Time</label>
                                                    <input type='text' class='form-control' id='DeliveryTime' name='DeliveryTime' value='{$deliveryTime}' required>
                                                </div>
                                                <div class='mb-3'>
                                                    <label for='Cost' class='form-label'>Cost</label>
                                                    <input type='number' class='form-control' id='Cost' name='Cost' step='0.01' value='{$cost}' required>
                                                </div>
                                                <button type='submit' name='editDeliveryMethod' class='btn btn-primary'>Save Changes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>";

                        // Delete Modal
                        echo "<div class='modal fade' id='deleteDeliveryMethodModal{$shippingMethodID}' tabindex='-1' aria-labelledby='deleteDeliveryMethodModalLabel' aria-hidden='true'>
                                <div class='modal-dialog'>
                                    <div class='modal-content'>
                                        <div class='modal-header'>
                                            <h5 class='modal-title' id='deleteDeliveryMethodModalLabel'>Delete Delivery Method</h5>
                                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                        </div>
                                        <div class='modal-body'>
                                            Are you sure you want to delete this delivery method?
                                        </div>
                                        <div class='modal-footer'>
                                            <form method='POST' action='deliveryMethods.php'>
                                                <input type='hidden' name='Shipping_Method_ID' value='{$shippingMethodID}'>
                                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                                <button type='submit' name='deleteDeliveryMethod' class='btn btn-danger'>Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Insert Delivery Method Modal -->
    <div class='modal fade' id='insertDeliveryMethodModal' tabindex='-1' aria-labelledby='insertDeliveryMethodModalLabel' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='insertDeliveryMethodModalLabel'>Insert New Shipping Method</h5>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class='modal-body'>
                    <form method='POST' action='insertDeliveryMethod.php'>
                        <div class='mb-3'>
                            <label for='Shipping_Method' class='form-label'>Shipping Method:</label>
                            <input type='text' class='form-control' id='Shipping_Method' name='Shipping_Method' required>
                        </div>
                        <div class='mb-3'>
                            <label for='DeliveryTime' class='form-label'>Shipping Time:</label>
                            <input type='text' class='form-control' id='DeliveryTime' name='DeliveryTime' required>
                        </div>
                        <div class='mb-3'>
                            <label for='Cost' class='form-label'>Cost:</label>
                            <input type='number' class='form-control' id='Cost' name='Cost' step='0.01' required>
                        </div>
                        <button type='submit' class='btn btn-primary'>Insert Shipping Method</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://
</body>

</html>