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

function getCustomerProfilePictureSrc($path)
{
    $path = trim((string) $path);
    if ($path === '') {
        return null;
    }

    if (preg_match('/^(https?:)?\/\//i', $path) || strpos($path, 'data:') === 0) {
        return $path;
    }

    $path = str_replace('\\', '/', $path);
    $uploadsPosition = strpos($path, 'uploads/');

    if ($uploadsPosition !== false) {
        $path = substr($path, $uploadsPosition);
    }

    $path = ltrim($path, '/');

    if (strpos($path, '../') === 0) {
        $src = $path;
    } elseif (strpos($path, 'uploads/') === 0) {
        $src = '../' . $path;
    } else {
        $src = '../uploads/profile_pictures/' . basename($path);
    }

    $filePath = realpath(__DIR__ . '/' . $src);
    $uploadsRoot = realpath(__DIR__ . '/../uploads');

    if ($filePath === false || $uploadsRoot === false || strpos($filePath, $uploadsRoot) !== 0) {
        return null;
    }

    return $src;
}

$profile_picture = getCustomerProfilePictureSrc($customer['Profile_Picture'] ?? '');
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
    <link rel="stylesheet" href="../Customer/customer_css/style.css?v=20260514-cart-profile">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="icon" href="path/to/favicon.ico">
    <title>User Profile - Charm & Grace</title>
</head>

<body class="customer-profile-page">
    <?php include 'navbar.php'; ?>

    <div class="container profile-page-container">
        <div class="profile-header text-center">
            <h3 class="header-title">Your Profile</h3>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="profile-card">
                    <div class="profile-summary">
                        <div class="profile-avatar-wrap">
                            <?php if ($profile_picture): ?>
                                <img src="<?= htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="rounded-circle profile-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                <i class="fa fa-user-circle profile-default-icon" aria-hidden="true" style="display: none;"></i>
                            <?php else: ?>
                                <i class="fa fa-user-circle profile-default-icon" aria-hidden="true"></i>
                            <?php endif; ?>
                        </div>

                        <div class="profile-intro">
                            <span class="profile-eyebrow">Charm & Grace Account</span>
                            <h4><?= htmlspecialchars($customer['Name']); ?></h4>
                            <p><?= htmlspecialchars($customer['Email']); ?></p>
                        </div>
                    </div>

                    <div class="profile-content-grid">
                        <div class="profile-info">
                            <h4>Profile Information</h4>
                            <div class="profile-detail-list">
                                <div class="profile-detail-item">
                                    <span>Name</span>
                                    <strong><?= htmlspecialchars($customer['Name']); ?></strong>
                                </div>
                                <div class="profile-detail-item">
                                    <span>Email</span>
                                    <strong><?= htmlspecialchars($customer['Email']); ?></strong>
                                </div>
                                <div class="profile-detail-item">
                                    <span>Phone</span>
                                    <strong><?= htmlspecialchars($customer['Phone']); ?></strong>
                                </div>
                                <div class="profile-detail-item profile-detail-wide">
                                    <span>Address</span>
                                    <strong><?= htmlspecialchars($customer['Address']); ?></strong>
                                </div>
                            </div>
                        </div>

                        <div class="profile-actions-panel">
                            <h4>Quick Actions</h4>
                            <div class="profile-actions-grid">
                                <a href="/Customer/editProfile.php" class="profile-action-card">
                                    <i class="fa-solid fa-user-pen"></i>
                                    <span>Edit Profile</span>
                                </a>
                                <a href="/Customer/orderHistory.php" class="profile-action-card">
                                    <i class="fa-solid fa-receipt"></i>
                                    <span>Order History</span>
                                </a>
                                <a href="/Customer/wishlist.php" class="profile-action-card">
                                    <i class="fa-solid fa-heart"></i>
                                    <span>Wishlist</span>
                                </a>
                                <a href="/Customer/cart.php" class="profile-action-card">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                    <span>My Cart</span>
                                </a>
                                <a href="/Customer/user_logout.php" class="profile-action-card profile-action-danger">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> -->
</body>

</html>
