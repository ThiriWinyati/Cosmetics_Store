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
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar">
            <nav class="nav flex-column">
                <a href="../Admin/adminHome.php" class="nav-link">
                    <i class="fa fa-home"></i> Home
                </a>
                <a href="../Admin/adminDashboard.php" class="nav-link">
                    <i class="fa fa-tachometer-alt"></i> Dashboard
                </a>

                <!-- Product Catalog -->
                <a href="#" class="nav-link" onclick="toggleDropdown('productsDropdown')">
                    <i class="fa fa-cogs"></i> Management <i class="fa fa-caret-down ms-auto"></i>
                </a>
                <div id="productsDropdown" class="dropdown-container">
                    <a href="viewProduct.php" class="nav-link"><i class="fa fa-box"></i> Product</a>
                    <a href="viewCategory.php" class="nav-link"><i class="fa fa-th-large"></i> Category</a>
                    <a href="viewBrands.php" class="nav-link"><i class="fa fa-tags"></i> Brand</a>
                    <a href="deliveryMethods.php" class="nav-link"><i class="fa fa-ship"></i> Shipping</a>

                </div>

                <!-- Customer Domain -->
                <a href="#" class="nav-link" onclick="toggleDropdown('customerDomainDropdown')">
                    <i class="fa fa-users"></i> Customer Domain <i class="fa fa-caret-down ms-auto"></i>
                </a>
                <div id="customerDomainDropdown" class="dropdown-container">
                    <a href="viewCustomer.php" class="nav-link"><i class="fa fa-user"></i> Customer</a>
                    <a href="admin_chat.php" class="nav-link"><i class="fas fa-comments"></i> Chats</a>
                    <a href="admin_contact_messages.php" class="nav-link"><i class="fa fa-comment"></i>Contact Messages</a>
                    <a href="viewReviews.php" class="nav-link"><i class="fa fa-star"></i> Reviews</a>
                </div>

                <!-- Manage Orders -->
                <a href="#" class="nav-link" onclick="toggleDropdown('ordersDropdown')">
                    <i class="fa fa-box-open"></i> Orders <i class="fa fa-caret-down ms-auto"></i>
                </a>
                <div id="ordersDropdown" class="dropdown-container">
                    <a href="viewOrders.php" class="nav-link"><i class="fa fa-list-alt"></i> Orders</a>
                    <a href="manageDelivery.php" class="nav-link"><i class="fa fa-truck"></i> Delivery</a>
                    <a href="viewPaymentMethods.php" class="nav-link"><i class="fa fa-credit-card"></i> Payment</a>
                </div>

                <!-- Special Offers -->
                <a href="viewCoupons.php" class="nav-link">
                    <i class="fa fa-gift"></i> Special Offers
                </a>


            </nav>

            <!-- Logout -->
            <div class="logout">
                <a href="adminLogout.php" class="nav-link">
                    <i class="fa fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light d-flex align-items-center sticky-top">
            <!-- Sidebar Toggle Button -->
            <button id="openNav" class="btn btn-outline-primary me-3" onclick="toggleSidebar()">&#9776;</button>
            <img src="../images/logo.png" alt="Logo" style="width: 50px; height: auto; object-fit: contain;">
            <a href="../Admin/adminHome.php" style="text-decoration: none">
                <h5 class="ms-2 mb-0 brand-name">Charm & Grace</h5>
            </a>
            <!-- Logo and Brand Name -->
            <div class="d-flex align-items-center">

            </div>


        </nav>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Function to toggle dropdown and set active state
                function toggleDropdown(id) {
                    const dropdown = document.getElementById(id);
                    dropdown.classList.toggle('show');
                    const link = dropdown.previousElementSibling;
                    link.classList.toggle('active');
                    saveActiveState();
                }

                // Function to save active state to localStorage
                function saveActiveState() {
                    const activeLinks = document.querySelectorAll('.nav-link.active');
                    const activeDropdowns = document.querySelectorAll('.dropdown-container.show');
                    const activeState = {
                        links: Array.from(activeLinks).map(link => link.getAttribute('href')),
                        dropdowns: Array.from(activeDropdowns).map(dropdown => dropdown.id)
                    };
                    localStorage.setItem('sidebarActiveState', JSON.stringify(activeState));
                }

                // Function to load active state from localStorage
                function loadActiveState() {
                    const activeState = JSON.parse(localStorage.getItem('sidebarActiveState'));
                    if (activeState) {
                        activeState.links.forEach(href => {
                            const link = document.querySelector(`.nav-link[href="${href}"]`);
                            if (link) {
                                link.classList.add('active');
                            }
                        });
                        activeState.dropdowns.forEach(id => {
                            const dropdown = document.getElementById(id);
                            if (dropdown) {
                                dropdown.classList.add('show');
                            }
                        });
                    }
                }

                // Add click event listeners to nav links
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.addEventListener('click', function() {
                        document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
                        this.classList.add('active');
                        saveActiveState();
                    });
                });

                // Add click event listeners to dropdown toggles
                document.querySelectorAll('.nav-link[onclick]').forEach(link => {
                    link.addEventListener('click', function() {
                        const dropdownId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
                        toggleDropdown(dropdownId);
                    });
                });

                // Load active state on page load
                loadActiveState();
            });
        </script>
</body>

</html>