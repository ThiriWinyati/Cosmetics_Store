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

// Check if the user is logged in as an admin
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    echo "<script>alert('Please log in as an admin.');</script>";
    echo "<script>window.location.href = 'adminLogin.php';</script>";
    exit();
}

// Fetch all customers or search customers
$searchTerm = $_POST['searchTerm'] ?? '';
try {
    if ($searchTerm) {
        $customersQuery = "SELECT * FROM customers WHERE Name LIKE :searchTerm OR Email LIKE :searchTerm";
        $customersStmt = $conn->prepare($customersQuery);
        $customersStmt->execute(['searchTerm' => '%' . $searchTerm . '%']);
    } else {
        $customersQuery = "SELECT * FROM customers";
        $customersStmt = $conn->prepare($customersQuery);
        $customersStmt->execute();
    }
    $customers = $customersStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching customers: " . $e->getMessage());
}


// Edit customer
if (isset($_POST['editCustomer'])) {
    $customerId = $_POST['customerId'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    try {
        $editCustomerQuery = "UPDATE customers SET Name = :name, Email = :email WHERE Customer_ID = :customerId";
        $editCustomerStmt = $conn->prepare($editCustomerQuery);
        $editCustomerStmt->bindParam(':name', $name);
        $editCustomerStmt->bindParam(':email', $email);
        $editCustomerStmt->bindParam(':customerId', $customerId);
        $editCustomerStmt->execute();

        echo "<script>alert('Customer updated successfully!');</script>";
        echo "<script>window.location.href = 'viewCustomer.php';</script>";
    } catch (PDOException $e) {
        die("Error updating customer: " . $e->getMessage());
    }
}

// Delete customer
if (isset($_GET['deleteCustomerId'])) {
    $customerId = $_GET['deleteCustomerId'];

    try {
        $deleteCustomerQuery = "DELETE FROM customers WHERE Customer_ID = :customerId";
        $deleteCustomerStmt = $conn->prepare($deleteCustomerQuery);
        $deleteCustomerStmt->bindParam(':customerId', $customerId);
        $deleteCustomerStmt->execute();

        echo "<script>alert('Customer deleted successfully!');</script>";
        echo "<script>window.location.href = 'viewCustomer.php';</script>";
    } catch (PDOException $e) {
        die("Error deleting customer: " . $e->getMessage());
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
    <title>View Customers</title>
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

        .profile-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }

        .rounded-circle {
            border-radius: 50%;
        }
    </style>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>


    <div class="container mt-4">
        <a href="viewCustomer.php" class="text-decoration-none">
            <h2 class="text-center">View Customers</h2>
        </a>

        <form method="POST" action="viewCustomer.php" class="mb-3">
            <div class="input-group">
                <input type="text" name="searchTerm" class="form-control" placeholder="Search for customers..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                <button type="submit" class="btn btn-dark">Search</button>
            </div>
        </form>

        <div class="table-container">
            <table class="table table-hover" id="viewCustomersTable">
                <thead>
                    <tr>
                        <th>Profile Picture</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td>
                                <?php if (!empty($customer['Profile_Picture'])): ?>
                                    <img src="<?= htmlspecialchars($customer['Profile_Picture']); ?>" alt="Profile Picture" class="rounded-circle profile-image">
                                <?php else: ?>
                                    <i class="fa fa-user-circle fa-2x" aria-hidden="true"></i>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($customer['Name']); ?></td>
                            <td><?php echo htmlspecialchars($customer['Email']); ?></td>
                            <td>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewCustomerModal<?php echo $customer['Customer_ID']; ?>">
                                    <i class="fa fa-eye"></i> View
                                </button>
                            </td>
                        </tr>

                        <!-- View Customer Modal -->
                        <div class="modal fade" id="viewCustomerModal<?php echo $customer['Customer_ID']; ?>" tabindex="-1" aria-labelledby="viewCustomerModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="viewCustomerModalLabel">Customer Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Customer ID:</strong> <?php echo htmlspecialchars($customer['Customer_ID']); ?></p>
                                        <p><strong>Name:</strong> <?php echo htmlspecialchars($customer['Name']); ?></p>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['Email']); ?></p>
                                        <p><strong>Signup Time:</strong> <?php echo htmlspecialchars($customer['Signup_time']); ?></p>
                                        <p><strong>Profile Picture:</strong></p>
                                        <?php if (!empty($customer['Profile_Picture'])): ?>
                                            <img src="<?= htmlspecialchars($customer['Profile_Picture']); ?>" alt="Profile Picture" class="rounded-circle profile-image">
                                        <?php else: ?>
                                            <i class="fa fa-user-circle fa-5x" aria-hidden="true"></i>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>
    </div>

    </div>

</body>

</html>