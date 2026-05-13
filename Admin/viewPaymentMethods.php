<?php
session_start();
require_once "../db_connect.php";
require_once "admin_auth.php";

$isAdmin = isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_login('viewPaymentMethods.php');
}

// Fetch payment methods
try {
    $paymentMethodsQuery = "SELECT Payment_Method_ID, Method_Name
                             FROM payment_methods
                             ORDER BY Payment_Method_ID ASC";
    $paymentMethodsStmt = $conn->prepare($paymentMethodsQuery);
    $paymentMethodsStmt->execute();
    $paymentMethods = $paymentMethodsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching payment methods: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['insertPaymentMethod'])) {
        $methodName = $_POST['methodName'];

        try {
            $insertQuery = "INSERT INTO payment_methods (Method_Name) VALUES (:methodName)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bindParam(':methodName', $methodName);
            $insertStmt->execute();

            echo "<script>alert('Payment method inserted successfully!');</script>";
            echo "<script>window.location.href = 'viewPaymentMethods.php';</script>";
        } catch (PDOException $e) {
            die("Error inserting payment method: " . $e->getMessage());
        }
    } elseif (isset($_POST['editPaymentMethod'])) {
        $paymentMethodID = $_POST['Payment_Method_ID'];
        $methodName = $_POST['methodName'];

        try {
            $updateQuery = "UPDATE payment_methods SET Method_Name = :methodName WHERE Payment_Method_ID = :id";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bindParam(':methodName', $methodName);
            $updateStmt->bindParam(':id', $paymentMethodID);
            $updateStmt->execute();

            echo "<script>alert('Payment method updated successfully!');</script>";
            echo "<script>window.location.href = 'viewPaymentMethods.php';</script>";
        } catch (PDOException $e) {
            die("Error updating payment method: " . $e->getMessage());
        }
    } elseif (isset($_POST['deletePaymentMethod'])) {
        $paymentMethodID = $_POST['Payment_Method_ID'];

        try {
            $deleteQuery = "DELETE FROM payment_methods WHERE Payment_Method_ID = :id";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->bindParam(':id', $paymentMethodID);
            $deleteStmt->execute();

            echo "<script>alert('Payment method deleted successfully!');</script>";
            echo "<script>window.location.href = 'viewPaymentMethods.php';</script>";
        } catch (PDOException $e) {
            die("Error deleting payment method: " . $e->getMessage());
        }
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
    <title>Payment Methods</title>
    <style>
        .container {

            padding: 20px;
        }

        h2 {
            color: #d97cb3;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .table-container {
            max-height: 500px;
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
            padding: 0.75rem;
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



    <!-- Main Content -->
    <div id="main-content">
        <div class="container mt-4">
                <a href="viewPaymentMethods.php" style="text-decoration:none;">
                    <h2>Payment Methods</h2>
                </a> 


                <!-- Search bar -->
                <div class="input-group mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search Payment Methods">
                    <button class="btn btn-dark" type="button" id="searchButton">Search</button>
                </div>

                <div class="text-end mb-3">
                    <?php if ($isAdmin): ?>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#insertPaymentMethodModal">
                            <i class="fa fa-plus"></i> Insert Payment Method
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-secondary" disabled title="Admin login required">
                            <i class="fa fa-lock"></i> Insert locked
                        </button>
                    <?php endif; ?>
                </div>


                <!-- Add a modal for inserting payment method -->
                <?php if ($isAdmin): ?>
                    <div class="modal fade" id="insertPaymentMethodModal" tabindex="-1" aria-labelledby="insertPaymentMethodModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="insertPaymentMethodModalLabel">Insert Payment Method</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="">
                                        <div class="mb-3">
                                            <label for="methodName" class="form-label">Payment Method Name</label>
                                            <input type="text" class="form-control" id="methodName" name="methodName" required>
                                        </div>
                                        <button type="submit" name="insertPaymentMethod" class="btn btn-primary">
                                            Add Payment Method
                                        </button>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="table-container">
                    <table class="table table-hover" id="viewPaymentMethodsTable">
                        <thead>
                            <tr>
                                <th>Payment Method ID</th>
                                <th>Method Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach ($paymentMethods as $method) {
                                    $paymentMethodID = htmlspecialchars($method['Payment_Method_ID']);
                                    $methodName = htmlspecialchars($method['Method_Name']);

                                    echo "
                                    <tr>
                                        <td>{$paymentMethodID}</td>
                                        <td>{$methodName}</td>
                                        <td>";

                                    if ($isAdmin) {
                                        echo "
                                            <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editPaymentMethodModal{$paymentMethodID}'>
                                                <i class='fa fa-edit'></i> Edit
                                            </button>

                                            <button class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#deletePaymentMethodModal{$paymentMethodID}'>
                                                <i class='fa fa-trash'></i> Delete
                                            </button>";
                                    } else {
                                        echo "
                                            <button class='btn btn-secondary btn-sm' disabled title='Admin login required'>
                                                <i class='fa fa-lock'></i> Edit locked
                                            </button>

                                            <button class='btn btn-secondary btn-sm' disabled title='Admin login required'>
                                                <i class='fa fa-lock'></i> Delete locked
                                            </button>";
                                    }

                                    echo "
                                        </td>
                                    </tr>";

                                    if ($isAdmin) {
                                        // Edit Modal
                                        echo "
                                        <div class='modal fade' id='editPaymentMethodModal{$paymentMethodID}' tabindex='-1' aria-labelledby='editPaymentMethodModalLabel{$paymentMethodID}' aria-hidden='true'>
                                            <div class='modal-dialog'>
                                                <div class='modal-content'>
                                                    <div class='modal-header'>
                                                        <h5 class='modal-title' id='editPaymentMethodModalLabel{$paymentMethodID}'>Edit Payment Method</h5>
                                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                    </div>

                                                    <div class='modal-body'>
                                                        <form method='POST' action=''>
                                                            <input type='hidden' name='Payment_Method_ID' value='{$paymentMethodID}'>

                                                            <div class='mb-3'>
                                                                <label class='form-label'>Payment Method Name</label>
                                                                <input type='text' class='form-control' name='methodName' value='{$methodName}' required>
                                                            </div>

                                                            <button type='submit' name='editPaymentMethod' class='btn btn-primary'>Save Changes</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>";

                                        // Delete Modal
                                        echo "
                                        <div class='modal fade' id='deletePaymentMethodModal{$paymentMethodID}' tabindex='-1' aria-labelledby='deletePaymentMethodModalLabel{$paymentMethodID}' aria-hidden='true'>
                                            <div class='modal-dialog'>
                                                <div class='modal-content'>
                                                    <div class='modal-header'>
                                                        <h5 class='modal-title' id='deletePaymentMethodModalLabel{$paymentMethodID}'>Delete Payment Method</h5>
                                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                    </div>

                                                    <div class='modal-body'>
                                                        Are you sure you want to delete this payment method?
                                                    </div>

                                                    <div class='modal-footer'>
                                                        <form method='POST' action=''>
                                                            <input type='hidden' name='Payment_Method_ID' value='{$paymentMethodID}'>

                                                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                                            <button type='submit' name='deletePaymentMethod' class='btn btn-danger'>Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>";
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../backEnd/backend_Javascript/sidebar.js"></script>
    <script>
        // Search function
        document.getElementById('searchButton').addEventListener('click', function() {
            var input = document.getElementById('searchInput').value.toLowerCase();
            var rows = document.querySelectorAll('#viewPaymentMethodsTable tbody tr');

            rows.forEach(function(row) {
                var methodName = row.cells[1].textContent.toLowerCase();
                if (methodName.includes(input)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>
