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
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $address = htmlspecialchars($_POST['address']);

    $profile_picture = $customer['Profile_Picture'];

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
            echo "<script>alert('There was an error uploading your profile picture.');</script>";
        } else {
            $uploadDir = __DIR__ . '/../uploads/profile_pictures/';
            $dbUploadPath = '../uploads/profile_pictures/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $allowedMimeTypes = [
                'image/jpeg' => ['jpg', 'jpeg'],
                'image/png' => ['png'],
                'image/gif' => ['gif'],
                'image/webp' => ['webp'],
            ];

            $fileType = mime_content_type($_FILES['profile_picture']['tmp_name']);
            $fileExtension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));

            if (!isset($allowedMimeTypes[$fileType]) || !in_array($fileExtension, $allowedMimeTypes[$fileType], true)) {
                echo "<script>alert('Only JPG, PNG, GIF, and WEBP image files are allowed.');</script>";
            } else {
                $fileName = 'customer_' . $customer_id . '_' . bin2hex(random_bytes(8)) . '.' . $fileExtension;
                $filePath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $filePath)) {
                    $profile_picture = $dbUploadPath . $fileName;
                } else {
                    echo "<script>alert('Could not save your profile picture. Please try again.');</script>";
                }
            }
        }
    }

    $updateQuery = "UPDATE customers 
                    SET Name = :name, 
                        Email = :email, 
                        Phone = :phone, 
                        Address = :address, 
                        Profile_Picture = :profile_picture 
                    WHERE Customer_ID = :customer_id";

    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':name', $name);
    $updateStmt->bindParam(':email', $email);
    $updateStmt->bindParam(':phone', $phone);
    $updateStmt->bindParam(':address', $address);
    $updateStmt->bindParam(':profile_picture', $profile_picture);
    $updateStmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);

    if ($updateStmt->execute()) {
        echo "<script>alert('Profile updated successfully.');</script>";
        echo "<script>window.location.href = '/Customer/userProfile.php';</script>";
        exit();
    } else {
        echo "<script>alert('An error occurred while updating the profile.');</script>";
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
    <style>
        /* Custom Styles for Edit Profile */
        .edit-profile-form-container {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 25px;
            background-color: #ffffff;
        }

        .edit-profile-form-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .edit-profile-title {
            font-weight: 600;
            font-size: 2rem;
        }

        .edit-profile-field {
            margin-bottom: 15px;
        }

        .edit-profile-label {
            font-weight: 500;
            margin-bottom: 5px;
        }

        .edit-profile-input-text,
        .edit-profile-textarea {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .edit-profile-btn-update {
            width: 100%;
            margin-top: 20px;
        }

        .edit-profile-card {
            padding: 20px;
        }

        .edit-profile-btn-container {
            text-align: center;
        }

        .edit-profile-alert {
            color: red;
        }

        /* Profile image circle */
        .profile-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
        }

        .rounded-circle {
            border-radius: 50%;
        }

        /* Centering profile image */
        .profile-image-container {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
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
