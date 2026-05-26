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
            background: #fbf7f8;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        .access-shell {
            min-height: calc(100vh - var(--customer-navbar-height, 84px));
            display: flex;
            align-items: center;
            position: relative;
            padding: clamp(28px, 5vw, 64px) 0;
            isolation: isolate;
        }

        .access-shell::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(251, 247, 248, 0.98) 0%, rgba(251, 247, 248, 0.9) 38%, rgba(251, 247, 248, 0.58) 68%, rgba(251, 247, 248, 0.22) 100%),
                url('/images/main_poster.jpg') center right / cover no-repeat;
            z-index: -2;
        }

        .access-shell::after {
            content: "";
            position: absolute;
            inset: auto 0 0;
            height: 42%;
            background: linear-gradient(0deg, rgba(251, 247, 248, 0.94), transparent);
            z-index: -1;
            pointer-events: none;
        }

        .access-intro {
            max-width: 570px;
        }

        .access-kicker {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-height: 34px;
            padding: 8px 12px;
            color: #982c61;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(220, 100, 150, 0.22);
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            box-shadow: 0 10px 30px rgba(36, 24, 33, 0.08);
        }

        .access-title {
            margin: 18px 0 18px;
            color: #241821;
            font-size: clamp(2.35rem, 5.6vw, 5.4rem);
            font-weight: 800;
            line-height: 0.98;
        }

        .access-copy {
            max-width: 510px;
            color: #594650;
            font-size: 1.06rem;
            line-height: 1.7;
        }

        .access-showcase {
            display: grid;
            grid-template-columns: 1fr 0.74fr;
            gap: 14px;
            align-items: stretch;
            margin-top: 28px;
            max-width: 500px;
        }

        .access-image {
            min-height: 250px;
            border-radius: 8px;
            background-position: center;
            background-size: cover;
            box-shadow: 0 22px 46px rgba(36, 24, 33, 0.16);
            overflow: hidden;
        }

        .access-image.primary {
            background-image: url('/images/poster3.webp');
        }

        .access-image.secondary {
            display: grid;
            align-content: end;
            min-height: 250px;
            padding: 18px;
            color: #ffffff;
            background:
                linear-gradient(180deg, rgba(36, 24, 33, 0.02), rgba(36, 24, 33, 0.72)),
                url('/images/lipsticks.webp') center / cover no-repeat;
        }

        .access-image.secondary span {
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .access-image.secondary strong {
            display: block;
            margin-top: 6px;
            font-size: 1.45rem;
            line-height: 1.05;
        }

        .access-panel-wrap {
            position: relative;
            padding: clamp(16px, 2.6vw, 26px);
            background: rgba(255, 255, 255, 0.76);
            border: 1px solid rgba(255, 255, 255, 0.64);
            border-radius: 8px;
            box-shadow: 0 26px 70px rgba(36, 24, 33, 0.16);
            backdrop-filter: blur(18px);
        }

        .access-panel-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
        }

        .access-panel-head h2 {
            margin: 0;
            color: #241821;
            font-size: 1.1rem;
            font-weight: 800;
        }

        .access-panel-head p {
            margin: 4px 0 0;
            color: #786a72;
            font-size: 0.92rem;
        }

        .access-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex: 0 0 auto;
            padding: 7px 10px;
            color: #22614f;
            background: #eaf7f1;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .access-panel {
            display: grid;
            gap: 12px;
        }

        .access-card {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 16px;
            min-height: 118px;
            padding: 18px;
            color: #241821;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.74);
            border: 1px solid rgba(220, 100, 150, 0.16);
            border-radius: 8px;
            box-shadow: 0 12px 28px rgba(49, 27, 42, 0.06);
            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .access-card:hover,
        .access-card:focus {
            color: #241821;
            background: #ffffff;
            border-color: rgba(220, 100, 150, 0.46);
            box-shadow: 0 18px 40px rgba(49, 27, 42, 0.13);
            transform: translateX(4px);
        }

        .access-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            color: #ffffff;
            background: linear-gradient(135deg, #dc6496, #b83b75);
            border-radius: 8px;
            font-size: 1.18rem;
            flex: 0 0 auto;
        }

        .access-card.admin .access-icon {
            background: linear-gradient(135deg, #241821, #5e4a56);
        }

        .access-card h3 {
            margin: 0 0 6px;
            font-size: 1.06rem;
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

        .access-note {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 16px;
            padding: 13px 14px;
            color: #665861;
            background: rgba(255, 255, 255, 0.58);
            border: 1px solid rgba(36, 24, 33, 0.08);
            border-radius: 8px;
            font-size: 0.88rem;
        }

        html[data-theme="dark"] body {
            background: #121212;
        }

        html[data-theme="dark"] .access-shell::before {
            background:
                linear-gradient(90deg, rgba(18, 18, 18, 0.98) 0%, rgba(18, 18, 18, 0.88) 40%, rgba(18, 18, 18, 0.58) 72%, rgba(18, 18, 18, 0.24) 100%),
                url('/images/main_poster.jpg') center right / cover no-repeat;
        }

        html[data-theme="dark"] .access-shell::after {
            background: linear-gradient(0deg, rgba(18, 18, 18, 0.95), transparent);
        }

        html[data-theme="dark"] .access-title,
        html[data-theme="dark"] .access-panel-head h2,
        html[data-theme="dark"] .access-card,
        html[data-theme="dark"] .access-card:hover,
        html[data-theme="dark"] .access-card:focus {
            color: #f5f5f5;
        }

        html[data-theme="dark"] .access-copy,
        html[data-theme="dark"] .access-panel-head p,
        html[data-theme="dark"] .access-card p,
        html[data-theme="dark"] .access-note {
            color: #c4c4c4;
        }

        html[data-theme="dark"] .access-kicker,
        html[data-theme="dark"] .access-panel-wrap,
        html[data-theme="dark"] .access-card {
            background: rgba(28, 28, 31, 0.82);
            border-color: #37373d;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.32);
        }

        html[data-theme="dark"] .access-card:hover,
        html[data-theme="dark"] .access-card:focus {
            background: rgba(39, 39, 43, 0.96);
            border-color: rgba(255, 134, 186, 0.45);
        }

        html[data-theme="dark"] .access-card.admin .access-arrow {
            color: #ff86ba;
        }

        html[data-theme="dark"] .access-note {
            background: rgba(28, 28, 31, 0.7);
            border-color: #37373d;
        }

        @media (max-width: 991px) {
            .access-shell {
                align-items: flex-start;
            }

            .access-intro {
                margin-bottom: 28px;
            }

            .access-shell::before {
                background:
                    linear-gradient(180deg, rgba(251, 247, 248, 0.95) 0%, rgba(251, 247, 248, 0.88) 48%, rgba(251, 247, 248, 0.98) 100%),
                    url('/images/main_poster.jpg') center top / cover no-repeat;
            }

            html[data-theme="dark"] .access-shell::before {
                background:
                    linear-gradient(180deg, rgba(18, 18, 18, 0.95) 0%, rgba(18, 18, 18, 0.88) 48%, rgba(18, 18, 18, 0.98) 100%),
                    url('/images/main_poster.jpg') center top / cover no-repeat;
            }
        }

        @media (max-width: 767px) {
            .access-showcase {
                grid-template-columns: 1fr;
            }

            .access-image {
                min-height: 180px;
            }

            .access-image.secondary {
                min-height: 160px;
            }

            .access-panel-head {
                display: block;
            }

            .access-status {
                margin-top: 12px;
            }
        }

        @media (max-width: 575px) {
            .access-shell {
                padding-top: 24px;
            }

            .access-card {
                grid-template-columns: 1fr auto;
                min-height: auto;
                transform: none;
            }

            .access-card:hover,
            .access-card:focus {
                transform: none;
            }

            .access-icon {
                grid-row: 1;
                width: 46px;
                height: 46px;
                grid-column: 1;
            }

            .access-card div:not(.access-icon) {
                grid-column: 1 / -1;
            }

            .access-arrow {
                grid-column: 2;
                grid-row: 1;
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
                        <div class="access-showcase" aria-hidden="true">
                            <div class="access-image primary"></div>
                            <div class="access-image secondary">
                                <span>Beauty Storefront</span>
                                <strong>Customer and admin experiences.</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="access-panel-wrap">
                        <div class="access-panel-head">
                            <div>
                                <h2>Select a demo path</h2>
                                <p>Pick the role you want to present first.</p>
                            </div>
                            <span class="access-status"><i class="fa-solid fa-circle-check"></i> Live Demo Ready</span>
                        </div>

                        <div class="access-panel" aria-label="Account access options">
                            <a class="access-card" href="/Customer/user_login.php">
                                <span class="access-icon"><i class="fa-solid fa-right-to-bracket"></i></span>
                                <div>
                                    <h3>Customer Login</h3>
                                    <p>Use an existing customer account to shop, save wishlist items, and view orders.</p>
                                </div>
                                <span class="access-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                            </a>

                            <a class="access-card" href="/Customer/user_signup.php">
                                <span class="access-icon"><i class="fa-solid fa-user-plus"></i></span>
                                <div>
                                    <h3>Customer Sign Up</h3>
                                    <p>Create a customer account for checkout, profile, wishlist, and order history features.</p>
                                </div>
                                <span class="access-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                            </a>

                            <a class="access-card admin" href="/Admin/adminLogin.php">
                                <span class="access-icon"><i class="fa-solid fa-chart-line"></i></span>
                                <div>
                                    <h3>Admin Dashboard Login</h3>
                                    <p>Sign in as an admin to demonstrate product, order, customer, and dashboard tools.</p>
                                </div>
                                <span class="access-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                            </a>
                        </div>

                        <div class="access-note">
                            <i class="fa-solid fa-lock"></i>
                            <span>Customer and admin areas stay separated, which makes the portfolio walkthrough easier to understand.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
