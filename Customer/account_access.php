<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    header("Location: /Customer/userProfile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Access - Charm & Grace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../Customer/customer_css/style.css">
    <style>
        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(220, 100, 150, 0.18), transparent 34%),
                linear-gradient(135deg, #fff7fa 0%, #ffffff 48%, #f8f9fb 100%);
            font-family: 'Poppins', sans-serif;
        }

        .access-shell {
            min-height: calc(100vh - var(--customer-navbar-height, 84px));
            display: flex;
            align-items: center;
            padding: clamp(32px, 6vw, 72px) 0;
        }

        .access-intro {
            max-width: 520px;
        }

        .access-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #b83b75;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .access-title {
            margin: 14px 0 16px;
            color: #241821;
            font-size: clamp(2.25rem, 5vw, 4.75rem);
            font-weight: 800;
            line-height: 0.98;
        }

        .access-copy {
            color: #62555f;
            font-size: 1.06rem;
            line-height: 1.7;
        }

        .access-panel {
            display: grid;
            gap: 16px;
        }

        .access-card {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 18px;
            min-height: 132px;
            padding: 22px;
            color: #241821;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.86);
            border: 1px solid rgba(220, 100, 150, 0.18);
            border-radius: 8px;
            box-shadow: 0 18px 40px rgba(49, 27, 42, 0.08);
            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .access-card:hover,
        .access-card:focus {
            color: #241821;
            border-color: rgba(220, 100, 150, 0.45);
            box-shadow: 0 22px 48px rgba(49, 27, 42, 0.14);
            transform: translateY(-3px);
        }

        .access-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 54px;
            height: 54px;
            color: #ffffff;
            background: #dc6496;
            border-radius: 50%;
            font-size: 1.25rem;
            flex: 0 0 auto;
        }

        .access-card.admin .access-icon {
            background: #241821;
        }

        .access-card h2 {
            margin: 0 0 6px;
            font-size: 1.12rem;
            font-weight: 800;
        }

        .access-card p {
            margin: 0;
            color: #6d6068;
            font-size: 0.95rem;
            line-height: 1.45;
        }

        .access-arrow {
            color: #dc6496;
            font-size: 1.1rem;
        }

        .access-card.admin .access-arrow {
            color: #241821;
        }

        html[data-theme="dark"] body {
            background:
                radial-gradient(circle at top left, rgba(255, 134, 186, 0.16), transparent 34%),
                linear-gradient(135deg, #121212 0%, #1c1c1f 52%, #17171a 100%);
        }

        html[data-theme="dark"] .access-title,
        html[data-theme="dark"] .access-card,
        html[data-theme="dark"] .access-card:hover,
        html[data-theme="dark"] .access-card:focus {
            color: #f5f5f5;
        }

        html[data-theme="dark"] .access-copy,
        html[data-theme="dark"] .access-card p {
            color: #c4c4c4;
        }

        html[data-theme="dark"] .access-card {
            background: rgba(28, 28, 31, 0.9);
            border-color: #37373d;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.32);
        }

        html[data-theme="dark"] .access-card:hover,
        html[data-theme="dark"] .access-card:focus {
            border-color: rgba(255, 134, 186, 0.45);
        }

        @media (max-width: 991px) {
            .access-shell {
                align-items: flex-start;
            }

            .access-intro {
                margin-bottom: 28px;
            }
        }

        @media (max-width: 575px) {
            .access-card {
                grid-template-columns: 1fr auto;
                min-height: auto;
            }

            .access-icon {
                grid-row: 1;
                width: 46px;
                height: 46px;
            }

            .access-card div:not(.access-icon) {
                grid-column: 1 / -1;
            }
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <main class="access-shell">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-5">
                    <div class="access-intro">
                        <span class="access-kicker"><i class="fa-solid fa-wand-magic-sparkles"></i> Portfolio Demo</span>
                        <h1 class="access-title">Choose your Charm & Grace access.</h1>
                        <p class="access-copy">Enter the storefront as a customer, create a new customer account, or open the admin dashboard login for the management side of the project.</p>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="access-panel" aria-label="Account access options">
                        <a class="access-card" href="/Customer/user_login.php">
                            <span class="access-icon"><i class="fa-solid fa-right-to-bracket"></i></span>
                            <div>
                                <h2>Customer Login</h2>
                                <p>Use an existing customer account to shop, save wishlist items, and view orders.</p>
                            </div>
                            <span class="access-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                        </a>

                        <a class="access-card" href="/Customer/user_signup.php">
                            <span class="access-icon"><i class="fa-solid fa-user-plus"></i></span>
                            <div>
                                <h2>Customer Sign Up</h2>
                                <p>Create a customer account for checkout, profile, wishlist, and order history features.</p>
                            </div>
                            <span class="access-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                        </a>

                        <a class="access-card admin" href="/Admin/adminLogin.php">
                            <span class="access-icon"><i class="fa-solid fa-chart-line"></i></span>
                            <div>
                                <h2>Admin Dashboard Login</h2>
                                <p>Sign in as an admin to demonstrate product, order, customer, and dashboard tools.</p>
                            </div>
                            <span class="access-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
