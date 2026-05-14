<?php
session_start();
require_once "../db_connect.php";

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    echo "<script>alert('Please log in to edit your profile.');</script>";
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    $profile_picture = $customer['Profile_Picture'] ?? null;
    $uploadError = null;

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
            $uploadError = 'There was an error uploading your profile picture.';
        } else {
            $uploadDir = __DIR__ . '/../uploads/profile_pictures/';
            $dbUploadPath = '../uploads/profile_pictures/';

            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
                $uploadError = 'Could not create the profile picture upload folder.';
            } elseif (!is_writable($uploadDir)) {
                $uploadError = 'The profile picture upload folder is not writable.';
            } else {
                $allowedMimeTypes = [
                    'image/jpeg' => ['jpg', 'jpeg'],
                    'image/png' => ['png'],
                    'image/gif' => ['gif'],
                    'image/webp' => ['webp'],
                ];

                $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
                $fileType = $fileInfo ? finfo_file($fileInfo, $_FILES['profile_picture']['tmp_name']) : false;
                if ($fileInfo) {
                    finfo_close($fileInfo);
                }

                $fileExtension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));

                if (!isset($allowedMimeTypes[$fileType]) || !in_array($fileExtension, $allowedMimeTypes[$fileType], true)) {
                    $uploadError = 'Only JPG, PNG, GIF, and WEBP image files are allowed.';
                } elseif ($_FILES['profile_picture']['size'] > 5 * 1024 * 1024) {
                    $uploadError = 'Profile picture must be 5MB or smaller.';
                } else {
                    $safeExtension = $allowedMimeTypes[$fileType][0];
                    $fileName = 'customer_' . $customer_id . '_' . bin2hex(random_bytes(8)) . '.' . $safeExtension;
                    $filePath = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $filePath)) {
                        $profile_picture = $dbUploadPath . $fileName;
                    } else {
                        $uploadError = 'Could not save your profile picture. Please try again.';
                    }
                }
            }
        }
    }

    if ($uploadError !== null) {
        echo "<script>alert(" . json_encode($uploadError) . ");</script>";
    } else {
        $updateQuery = "UPDATE customers 
                        SET Name = :name, 
                            Email = :email, 
                            Phone = :phone, 
                            Address = :address, 
                            Profile_Picture = :profile_picture 
                        WHERE Customer_ID = :customer_id";

        $updateStmt = $conn->prepare($updateQuery);

        $updated = $updateStmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':address' => $address,
            ':profile_picture' => $profile_picture,
            ':customer_id' => $customer_id,
        ]);

        if ($updated) {
            echo "<script>alert('Profile updated successfully.');</script>";
            echo "<script>window.location.href = '/Customer/userProfile.php';</script>";
            exit();
        } else {
            echo "<script>alert('An error occurred while updating the profile.');</script>";
        }
    }
}
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
    <title>Edit Profile - Charm & Grace</title>
</head>

<body class="edit-profile-page">

    <?php include 'navbar.php'; ?>

    <div class="container edit-profile-page-container">
        <div class="edit-profile-form-container">
            <!-- Profile Picture Section -->
            <div class="profile-image-container">
                <?php if ($customer['Profile_Picture']): ?>
                    <img src="<?= htmlspecialchars($customer['Profile_Picture']); ?>" class="rounded-circle profile-image" alt="Profile Picture">
                <?php else: ?>
                    <i class="fa fa-user-circle fa-5x" aria-hidden="true"></i>
                <?php endif; ?>
            </div>

            <div class="edit-profile-form-header">
                <h1 class="edit-profile-title">Edit Your Profile</h1>
            </div>

            <!-- Display current profile information in the form -->
            <form method="POST" action="editProfile.php" enctype="multipart/form-data">
                <div class="edit-profile-field">
                    <label for="name" class="edit-profile-label">Name</label>
                    <input type="text" id="name" name="name" class="edit-profile-input-text"
                        value="<?= htmlspecialchars($customer['Name']); ?>" required>
                </div>

                <div class="edit-profile-field">
                    <label for="email" class="edit-profile-label">Email</label>
                    <input type="email" id="email" name="email" class="edit-profile-input-text"
                        value="<?= htmlspecialchars($customer['Email']); ?>" required>
                </div>

                <div class="edit-profile-field">
                    <label for="phone" class="edit-profile-label">Phone</label>
                    <input type="text" id="phone" name="phone" class="edit-profile-input-text"
                        value="<?= htmlspecialchars($customer['Phone']); ?>" required>
                </div>

                <div class="edit-profile-field">
                    <label for="address" class="edit-profile-label">Address</label>
                    <textarea id="address" name="address" class="edit-profile-textarea" rows="4" required><?= htmlspecialchars($customer['Address']); ?></textarea>
                </div>

                <div class="edit-profile-field">
                    <label for="profile_picture" class="edit-profile-label">Profile Picture</label>
                    <input type="file" id="profile_picture" name="profile_picture" class="form-control">
                </div>

                <!-- Update Profile Button -->
                <div class="edit-profile-btn-container">
                    <button type="submit" class="btn btn-primary edit-profile-btn-update">Update Profile</button>
                </div>

                <!-- Forgot Password Link -->
                <div class="edit-profile-btn-container mt-3">
                    <a href="forgotPassword.php" class="btn btn-link">Forgot Password?</a>
                </div>
            </form>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> -->
</body>

</html>
