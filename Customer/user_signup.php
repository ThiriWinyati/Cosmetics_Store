<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../db_connect.php";

if (!isset($_SESSION)) {
    session_start();
}

function isPasswordStrong($password)
{
    if (strlen($password) < 8) {
        return false;
    }

    $digitCount = 0;
    $capitalCount = 0;
    $specCount = 0;

    foreach (str_split($password) as $char) {
        if (is_numeric($char)) {
            $digitCount++;
        } elseif (ctype_upper($char)) {
            $capitalCount++;
        } elseif (ctype_punct($char)) {
            $specCount++;
        }
    }

    return $digitCount >= 1 && $capitalCount >= 1 && $specCount >= 1;
}

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['signUp'])) {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];

    if ($password !== $confirmPassword) {
        $passwordError = "Passwords do not match.";
    } elseif (!isPasswordStrong($password)) {
        $passwordError = "Password must be at least 8 characters and contain one digit, one capital letter, and one special character.";
    } else {
        try {
            $checkSql = "SELECT Customer_ID FROM customers WHERE Email = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->execute([$email]);

            if ($checkStmt->rowCount() > 0) {
                $passwordError = "This email is already registered. Please log in instead.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                $sql = "INSERT INTO customers (Name, Email, Password) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $status = $stmt->execute([$name, $email, $hashedPassword]);

                if ($status) {
                    $_SESSION['signUpSuccess'] = "Sign up successful. Please log in.";
                    header("Location: /Customer/user_login.php");
                    exit();
                }
            }
        } catch (PDOException $e) {
            echo "Signup error: " . $e->getMessage();
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

        .signup-container {
            display: flex;
            align-items: center;
            min-height: 100vh;
            background-color: #fefefe;
        }

        .signup-form {
            background-color: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }

        .signup-form h2 {
            font-weight: bold;
            color: #333;
            margin-bottom: 1rem;
        }

        .signup-btn {
            width: 100%;
            background-color: #e91e63;
            border-color: #e91e63;
        }

        .signup-btn:hover {
            background-color: #c2185b;
            border-color: #c2185b;
        }

        .signup-image {
            background: url('../images/cosmetics-bg.jpg') no-repeat center center;
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
    <title>Sign Up - Charm & Grace</title>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container signup-container">
        <div class="row w-100 align-items-center">
            <!-- Signup Illustration -->
            <div class="col-lg-6 signup-image d-none d-lg-block">
                <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
                <dotlottie-player src="https://lottie.host/1ee1b4cc-7a80-4288-9870-f97af424697e/8RbLGsvkiy.lottie" background="transparent" speed="1" style="width: 500px; height: 500px" loop autoplay></dotlottie-player>
            </div>

            <!-- Signup Form -->
            <div class="col-lg-6">
                <div class="signup-form mx-auto">
                    <h2>Create Your Account</h2>
                    <form action="user_signup.php" method="post">
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullName" name="name" placeholder="Enter your full name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email address" required>
                        </div>
                        <div class="mb-3">
                            <?php
                            if (isset($passwordError)) {
                                echo "<div class='alert alert-danger'>$passwordError</div>";
                                unset($passwordError);
                            }
                            ?>
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Create your password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                        </div>
                        <button type="submit" class="btn btn-primary signup-btn" name="signUp">Sign Up</button>
                    </form>
                    <p class="mt-3 text-center">By creating an account, you agree to our <a href="/Customer/terms_conditions.php" style="color: #e91e63;">Terms & Conditions</a>.</p>
                    <p class="text-center mt-3">Already have an account? <a href="/Customer/user_login.php" style="color: #e91e63;">Log in here</a></p>
                </div>
            </div>


        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>