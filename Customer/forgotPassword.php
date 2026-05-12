<?php
session_start();
require_once "../db_connect.php";

function isPasswordStrong($password)
{
    if (strlen($password) < 8) {
        return false;
    }
    $digitCount = 0;
    $capitalCount = 0;
    $specCount = 0;
    foreach (str_split($password) as $char) {
        if (is_numeric($char)) $digitCount++;
        elseif (ctype_upper($char)) $capitalCount++;
        elseif (ctype_punct($char)) $specCount++;
    }
    return $digitCount >= 1 && $capitalCount >= 1 && $specCount >= 1;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $new_password = htmlspecialchars($_POST['new_password']);
    $confirm_password = htmlspecialchars($_POST['confirm_password']);

    if ($new_password !== $confirm_password) {
        $passwordError = "Passwords do not match. Please try again.";
    } elseif (!isPasswordStrong($new_password)) {
        $passwordError = "Password must contain at least 8 characters, including at least one digit, one uppercase letter, and one special character.";
    } else {
        $query = "SELECT Customer_ID FROM customers WHERE Name = :name AND Email = :email";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $updateQuery = "UPDATE customers SET Password = :password WHERE Customer_ID = :customer_id";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bindParam(':password', $hashed_password);
            $updateStmt->bindParam(':customer_id', $user['Customer_ID'], PDO::PARAM_INT);
            if ($updateStmt->execute()) {
                echo "<script>alert('Password updated successfully.'); window.location.href = 'user_login.php';</script>";
            } else {
                $passwordError = "An error occurred while updating the password.";
            }
        } else {
            $passwordError = "No customer found with the provided name and email.";
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
    <link rel="stylesheet" href="../Customer/customer_css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .forgot-password-container {
            display: flex;
            align-items: center;
            min-height: 100vh;
        }

        .forgot-password-form {
            background-color: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
            width: 100%;
            max-width: 400px;
        }

        .forgot-password-form h2 {
            font-weight: bold;
            color: #333;
            margin-bottom: 1rem;
        }

        .forgot-password-btn {
            width: 100%;
            background-color: #e91e63 !important;
            border-color: #e91e63 !important;
            color: #fff !important;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 5px;
        }

        .forgot-password-btn:hover {
            background-color: #c2185b;
            border-color: #c2185b;
        }


        .forgot-password-image {
            background: url('../images/reset-password-bg.jpg') no-repeat center center;
            background-size: cover;
            border-radius: 10px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <title>Forgot Password - Charm & Grace</title>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container forgot-password-container">
        <div class="row w-100 align-items-center">

            <!-- Forgot Password Form -->
            <div class="col-lg-6">
                <div class="forgot-password-form mx-auto">
                    <h2>Reset Password</h2>
                    <form method="POST" action="forgotPassword.php">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        </div>
                        <?php
                        if (isset($passwordError)) {
                            echo "<div class='alert alert-danger'>$passwordError</div>";
                            unset($passwordError);
                        }
                        ?>
                        <button type="submit" class="btn forgot-password-btn">Reset Password</button>
                        <div class="mt-3 text-center">
                            <a href="user_login.php" class="text-muted">Back to Login</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Forgot Password Illustration -->
            <div class="col-lg-6 forgot-password-image d-none d-lg-block">
                <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
                <dotlottie-player src="https://lottie.host/f9a1f483-25e0-45c5-bf65-b094dae815a2/hwlDZt7Qsw.lottie" background="transparent" speed="1" style="width: 400px; height: 400px" loop autoplay></dotlottie-player>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>