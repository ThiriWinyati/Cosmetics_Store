<?php
session_start();
require_once "../db_connect.php";

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    echo "<script>alert('Please log in to view your profile.');</script>";
    echo "<script>window.location.href = '/Customer/user_login.php';</script>";
    exit();
}

$customer_id = $_SESSION['customer_id'];

$query = "SELECT Customer_ID, Name, Email, Phone, Address, Profile_Picture 
          FROM customers 
          WHERE Customer_ID = :customer_id";

$stmt = $conn->prepare($query);
$stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
$stmt->execute();

$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    echo "<script>alert('User not found.');</script>";
    echo "<script>window.location.href = '/Customer/user_login.php';</script>";
    exit();
}

$profile_picture = !empty($customer['Profile_Picture']) ? $customer['Profile_Picture'] : null;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../Customer/customer_css/style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="icon" href="path/to/favicon.ico">
    <title>User Profile - Charm & Grace</title>
    <style>
        .profile-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 25px;
            background-color: #ffffff;
        }

        .profile-card h4 {
            font-size: 1.25rem;
            font-weight: 500;
        }

        .profile-card .btn-update {
            width: 200px;
            margin-top: 20px;
        }

        .profile-info p {
            font-size: 1rem;
            line-height: 1.6;
        }

        .profile-header {
            margin-bottom: 30px;
        }

        .header-title {
            font-weight: 600;
            font-size: 2rem;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
        }

        .rounded-circle {
            border-radius: 50%;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <div class="profile-header text-center">
            <h3 class="header-title">Your Profile</h3>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 col-sm-12">
                <div class="profile-card text-center">
                    <?php if ($profile_picture): ?>
                        <!-- Display profile image if available -->
                        <img src="<?= htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="rounded-circle profile-image">
                    <?php else: ?>
                        <!-- Display default user icon if no profile picture -->
                        <i class="fa fa-user-circle fa-5x" aria-hidden="true"></i>
                    <?php endif; ?>

                    <div class="profile-info mt-3">
                        <h4 class="mb-3">Profile Information</h4>
                        <p><strong>Name:</strong> <?= htmlspecialchars($customer['Name']); ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($customer['Email']); ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($customer['Phone']); ?></p>
                        <p><strong>Address:</strong> <?= htmlspecialchars($customer['Address']); ?></p>
                    </div>

                    <div class="text-center">
                        <a href="/Customer/editProfile.php" class="btn btn-primary btn-update">Edit Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> -->
</body>

</html>
