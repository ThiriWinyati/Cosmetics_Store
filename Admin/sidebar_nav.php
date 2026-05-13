<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../Admin/admin_css/style.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../Admin/admin_Javascript/sidebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" href="path/to/favicon.ico">
    <title>Sidebar and Navbar</title>
    <script>
        (function () {
            const savedTheme = localStorage.getItem("adminTheme") || "light";
            document.documentElement.setAttribute("data-theme", savedTheme);
        })();
    </script>
</head>

<body>
    <!-- Admin Sidebar -->
    <aside id="adminSidebar" class="admin-sidebar">
        <nav class="admin-sidebar-nav">

            <a href="../Admin/adminHome.php" class="admin-nav-link">
                <i class="fa fa-home"></i>
                <span>Home</span>
            </a>

            <a href="../Admin/adminDashboard.php" class="admin-nav-link">
                <i class="fa fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>

            <button type="button" class="admin-nav-link admin-dropdown-toggle" onclick="toggleDropdown('productsDropdown')">
                <span><i class="fa fa-cogs"></i> Management</span>
                <i class="fa fa-caret-down"></i>
            </button>

            <div id="productsDropdown" class="admin-dropdown-container">
                <a href="viewProduct.php" class="admin-nav-link sub-link"><i class="fa fa-box"></i> Product</a>
                <a href="viewCategory.php" class="admin-nav-link sub-link"><i class="fa fa-th-large"></i> Category</a>
                <a href="viewBrands.php" class="admin-nav-link sub-link"><i class="fa fa-tags"></i> Brand</a>
                <a href="deliveryMethods.php" class="admin-nav-link sub-link"><i class="fa fa-ship"></i> Shipping</a>
            </div>

            <button type="button" class="admin-nav-link admin-dropdown-toggle" onclick="toggleDropdown('customerDomainDropdown')">
                <span><i class="fa fa-users"></i> Customer Domain</span>
                <i class="fa fa-caret-down"></i>
            </button>

            <div id="customerDomainDropdown" class="admin-dropdown-container">
                <a href="viewCustomer.php" class="admin-nav-link sub-link"><i class="fa fa-user"></i> Customer</a>
                <a href="admin_chat.php" class="admin-nav-link sub-link"><i class="fas fa-comments"></i> Chats</a>
                <a href="admin_contact_messages.php" class="admin-nav-link sub-link"><i class="fa fa-comment"></i> Contact Messages</a>
                <a href="viewReviews.php" class="admin-nav-link sub-link"><i class="fa fa-star"></i> Reviews</a>
            </div>

            <button type="button" class="admin-nav-link admin-dropdown-toggle" onclick="toggleDropdown('ordersDropdown')">
                <span><i class="fa fa-box-open"></i> Orders</span>
                <i class="fa fa-caret-down"></i>
            </button>

            <div id="ordersDropdown" class="admin-dropdown-container">
                <a href="viewOrders.php" class="admin-nav-link sub-link"><i class="fa fa-list-alt"></i> Orders</a>
                <a href="manageDelivery.php" class="admin-nav-link sub-link"><i class="fa fa-truck"></i> Delivery</a>
                <a href="viewPaymentMethods.php" class="admin-nav-link sub-link"><i class="fa fa-credit-card"></i> Payment</a>
            </div>

            <a href="viewCoupons.php" class="admin-nav-link">
                <i class="fa fa-gift"></i>
                <span>Special Offers</span>
            </a>

        </nav>

        <div class="admin-logout">
            <a href="adminLogout.php" class="admin-nav-link">
                <i class="fa fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>


    <div id="adminSidebarOverlay" class="admin-sidebar-overlay" onclick="closeSidebar()"></div>

    <div id="adminMain" class="admin-main">

        <nav class="admin-topbar">
            <button id="openNav" class="admin-menu-btn" onclick="toggleSidebar()" type="button">
                <i class="fa fa-bars"></i>
            </button>

            <a href="../Admin/adminHome.php" class="admin-brand">
                <img src="../images/logo.png" alt="Charm & Grace Logo">
                <span>Charm & Grace</span>
            </a>

            <button id="adminThemeToggle" class="admin-theme-toggle" type="button" aria-label="Current theme: light" aria-pressed="false">
                <span class="admin-theme-toggle-track" aria-hidden="true">
                    <span class="admin-theme-toggle-thumb">
                        <i class="fas fa-sun admin-theme-icon-light"></i>
                        <i class="fas fa-moon admin-theme-icon-dark"></i>
                    </span>
                </span>
                <span class="admin-theme-toggle-text">Light</span>
            </button>
        </nav>

        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById("adminSidebar");
                const overlay = document.getElementById("adminSidebarOverlay");
                const main = document.getElementById("adminMain");

                if (window.innerWidth > 768) {
                    sidebar.classList.toggle("collapsed");
                    main.classList.toggle("expanded");
                } else {
                    sidebar.classList.toggle("show");
                    overlay.classList.toggle("show");
                }
            }

            function closeSidebar() {
                document.getElementById("adminSidebar").classList.remove("show");
                document.getElementById("adminSidebarOverlay").classList.remove("show");
            }

            function toggleDropdown(id) {
                const dropdown = document.getElementById(id);
                dropdown.classList.toggle("show");
            }

            document.addEventListener("DOMContentLoaded", function () {
                const currentPage = window.location.pathname.split("/").pop();

                document.querySelectorAll(".admin-nav-link[href]").forEach(link => {
                    const linkPage = link.getAttribute("href").split("/").pop();

                    if (linkPage === currentPage) {
                        link.classList.add("active");

                        const parentDropdown = link.closest(".admin-dropdown-container");
                        if (parentDropdown) {
                            parentDropdown.classList.add("show");
                        }
                    }
                });

                const themeToggle = document.getElementById("adminThemeToggle");

                if (themeToggle) {
                    const themeText = themeToggle.querySelector(".admin-theme-toggle-text");

                    function applyAdminTheme(theme) {
                        document.documentElement.setAttribute("data-theme", theme);
                        localStorage.setItem("adminTheme", theme);
                        window.dispatchEvent(new CustomEvent("admin-theme-change", { detail: { theme } }));

                        const isDark = theme === "dark";
                        themeToggle.setAttribute("aria-pressed", isDark ? "true" : "false");
                        themeToggle.setAttribute("aria-label", `Current theme: ${theme}`);

                        if (themeText) {
                            themeText.textContent = isDark ? "Dark" : "Light";
                        }
                    }

                    applyAdminTheme(localStorage.getItem("adminTheme") || "light");

                    themeToggle.addEventListener("click", function () {
                        const nextTheme = document.documentElement.getAttribute("data-theme") === "dark" ? "light" : "dark";
                        applyAdminTheme(nextTheme);
                    });
                }
            });
        </script>
</body>

</html>
