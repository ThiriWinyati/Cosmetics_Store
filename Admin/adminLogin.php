<?php
require_once "../db_connect.php";

if (!isset($_SESSION)) {
    session_start();
}

function isPasswordStrong($password)
{
    // Check if the password is at least 8 characters long
    if (strlen($password) < 8) {
        return false;
    }

    // Check if the password contains at least one uppercase letter, one lowercase letter, one digit, and one special character
    if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W]/', $password)) {
        return false;
    }

    return true;
}

if (isset($_POST['admin_login']) && $_SERVER['REQUEST_METHOD'] == "POST") {
    $adminName = $_POST["admin_name"];
    $adminPassword = $_POST["admin_password"];

    if (isPasswordStrong($adminPassword)) {
        try {
            $sql = "SELECT Admin_User_ID, password FROM admin_users WHERE name = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$adminName]);
            $info = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($info) {
                $password_hash = $info['password'];
                if (password_verify($adminPassword, $password_hash)) {
                    $_SESSION['admin_id'] = $info['Admin_User_ID'];
                    $_SESSION['adminLoginSuccess'] = "Login Success";
                    $_SESSION['isLoggedIn'] = true;
                    header("Location: adminHome.php");
                } else {
                    $password_err = "Username or Password is incorrect";
                }
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    } else {
        $password_err = "Password must be at least 8 characters long and contain at least one uppercase letter,
                        one lowercase letter, one digit, and one special character.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../Admin/admin_css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .login-container {
            display: flex;
            width: 80%;
            max-width: 900px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-form {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-form h4 {
            margin-bottom: 20px;
            color: #d97cb3;
        }

        .login-form .form-label {
            font-weight: bold;
        }

        .login-form .form-control {
            height: calc(2.5em + 0.75rem + 2px);
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            border-radius: 5px;
            border: 1px solid #ced4da;
            margin-bottom: 15px;
        }

        .login-form .btn-primary {
            font-size: 1rem;
            padding: 0.75rem;
            border-radius: 5px;
            border: none;
            background-color: #d97cb3;
            transition: background-color 0.3s ease;
        }

        .login-form .btn-primary:hover {
            background-color: #c2185b;
        }

        .login-form .alert {
            margin-bottom: 20px;
        }

        .animation-container {
            flex: 1;
            background: linear-gradient(135deg, #f8d7da, #f1c4c9);
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .animation-container::before,
        .animation-container::after {
            content: '';
            position: absolute;
            width: 100px;
            height: 100px;
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .animation-container::before {
            top: 20%;
            left: 30%;
            animation-delay: 0s;
        }

        .animation-container::after {
            bottom: 20%;
            right: 30%;
            animation-delay: 3s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="animation-container">
            <dotlottie-player src="https://lottie.host/b8caa454-c050-4e88-98f3-b389553b679d/QGTvMy9yWR.lottie" background="transparent" speed="1" style="width: 300px; height: 300px" loop autoplay></dotlottie-player>
        </div>
        <div class="login-form">
            <div class="text-center mb-4">
                <h4>Admin Login</h4>
                <?php
                if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
                    echo "<div class='alert alert-success'>You logged out of the system. Please login again if you want to.</div>";
                }
                ?>
            </div>
            <form action="" method="POST">
                <?php if (isset($password_err)) {
                    echo "<p class='alert alert-danger'>$password_err</p>";
                } ?>
                <div class="mb-3">
                    <label for="admin_username" class="form-label">Username</label>
                    <input type="text" name="admin_name" id="admin_username" class="form-control" placeholder="Enter your username" required>
                </div>
                <div class="mb-3">
                    <label for="admin_password" class="form-label">Password</label>
                    <input type="password" name="admin_password" id="admin_password" class="form-control" placeholder="Enter your password" required>
                </div>
                <button type="submit" name="admin_login" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</body>

</html>