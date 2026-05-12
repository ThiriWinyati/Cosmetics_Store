<?php
require_once "../db_connect.php";

if (!isset($_SESSION)) {
    session_start(); 
}

function isPasswordStrong($password)
{
    if (strlen($password) < 8) {
        return false;
    }
    return isPasswordStrong($password);
}

if (isset($_POST['login']) && $_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    if (strlen($password) > 7) {
        try {
            $sql = "SELECT Customer_ID, name, password FROM customers WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$email]);
            $info = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($info) {
                $name = $info['name'];
                $password_hash = $info['password'];
                if (password_verify($password, $password_hash)) {
                    $_SESSION['customer_id'] = $info['Customer_ID'];
                    $_SESSION['cname'] = $info['name'];
                    $_SESSION['cLoginSuccess'] = "Login Success";
                    $_SESSION['is_logged_in'] = true;
                    header("Location: user_homeIndex.php");
                } else {
                    $password_err = "Email or Password is incorrect";
                }
            } else {
                $password_err = "Email or Password is incorrect";
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    } else {
        $password_err = "Email or Password is incorrect";
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

        .signin-container {
            display: flex;
            align-items: center;
            min-height: 100vh;
            background-color: #fefefe;
        }

        .signin-form {
            background-color: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
            width: 100%;
            max-width: 400px;
        }

        .signin-form h2 {
            font-weight: bold;
            color: #333;
            margin-bottom: 1rem;
        }

        .signin-btn {
            width: 100%;
            background-color: #e91e63;
            border-color: #e91e63;
        }

        .signin-btn:hover {
            background-color: #c2185b;
            border-color: #c2185b;
        }

        .signin-image {
            background: url('../images/cosmetics-bg.jpg') no-repeat center center;
            background-size: cover;
            border-radius: 10px;
        }

        .forgot-password-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #e91e63;
            text-decoration: none;
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
    <title>Log In - Charm & Grace</title>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container signin-container">
        <div class="row w-100 align-items-center">

            <!-- Login Form -->
            <div class="col-lg-6">
                <div class="signin-form mx-auto">
                    <h2>Log In</h2>
                    <form action="user_login.php" method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            <?php if (isset($password_err)) {
                                echo "<div class='alert alert-danger mt-2'>$password_err</div>";
                                unset($password_err);
                            } ?>
                        </div>
                        <button type="submit" class="btn btn-primary signin-btn" name="login">Log In</button>
                    </form>
                    <a href="/Customer/forgotPassword.php" class="forgot-password-link">Forgot Password?</a>
                    <p class="mt-3 text-center">Don't have an account? <a href="/Customer/user_signup.php" style="color: #e91e63;">Sign up here</a></p>
                </div>
            </div>

            <!-- Login Illustration -->
            <div class="col-lg-6 signin-image d-none d-lg-block">
                <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
                <dotlottie-player src="https://lottie.host/20a9dee6-3b27-434f-b87d-058f975d3b5e/81XQchm37q.lottie" background="transparent" speed="1" style="width: 400px; height: 400px" loop autoplay></dotlottie-player>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>