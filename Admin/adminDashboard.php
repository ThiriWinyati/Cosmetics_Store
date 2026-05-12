<?php
session_start();
require_once "../db_connect.php";

// Check admin session
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    echo "<script>alert('Please log in as an admin.');</script>";
    echo "<script>window.location.href = 'adminLogin.php';</script>";
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=cosmetics_store", 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch total sales
    $totalSalesQuery = "SELECT SUM(Total_Price) as total_sales FROM orders";
    $totalSales = $conn->query($totalSalesQuery)->fetch(PDO::FETCH_ASSOC);

    // Fetch total orders
    $totalOrdersQuery = "SELECT COUNT(Order_ID) as total_orders FROM orders";
    $totalOrders = $conn->query($totalOrdersQuery)->fetch(PDO::FETCH_ASSOC);

    // Fetch total users
    $totalUsersQuery = "SELECT COUNT(Customer_ID) as total_users FROM customers";
    $totalUsers = $conn->query($totalUsersQuery)->fetch(PDO::FETCH_ASSOC);

    // Fetch total users who made purchases
    $totalUsersWithPurchasesQuery = "SELECT COUNT(DISTINCT Customer_ID) as users_with_purchases FROM orders";
    $totalUsersWithPurchases = $conn->query($totalUsersWithPurchasesQuery)->fetch(PDO::FETCH_ASSOC);

    // Fetch average order value
    $avgOrderValueQuery = "SELECT AVG(Total_Price) as avg_order_value FROM orders";
    $avgOrderValue = $conn->query($avgOrderValueQuery)->fetch(PDO::FETCH_ASSOC);


    // Fetch data for bar chart (top products by sales)
    $barData = $conn->query("SELECT p.Name, SUM(oi.Quantity) as total_quantity 
                             FROM order_items oi
                             JOIN products p ON oi.Product_ID = p.Product_ID
                             GROUP BY p.Name 
                             ORDER BY total_quantity DESC 
                             LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

    // Fetch data for donut chart (sales distribution by payment method)
    $donutData = $conn->query("SELECT pm.Method_Name, COUNT(o.Order_ID) as count 
                               FROM orders o
                               JOIN payment_methods pm ON o.Payment_Method_ID = pm.Payment_Method_ID
                               GROUP BY pm.Method_Name")->fetchAll(PDO::FETCH_ASSOC);

    // Fetch data for radar chart (average ratings per product category)
    $radarData = $conn->query("SELECT c.Category_Name, AVG(r.Rating) as avg_rating 
                               FROM reviews r
                               JOIN products p ON r.Product_ID = p.Product_ID
                               JOIN categories c ON p.Category_ID = c.Category_ID
                               GROUP BY c.Category_Name")->fetchAll(PDO::FETCH_ASSOC);

    // Fetch data for total quantity of products by categories
    $categoryQuantityQuery = "
        SELECT c.Category_Name, SUM(s.Quantity) as total_quantity 
        FROM products p
        JOIN categories c ON p.Category_ID = c.Category_ID
        JOIN shades s ON p.Product_ID = s.product_id
        GROUP BY c.Category_Name
    ";
    $categoryQuantityData = $conn->query($categoryQuantityQuery)->fetchAll(PDO::FETCH_ASSOC);

    // Fetch data for top ten customers with total purchase amounts
    $topCustomersQuery = "SELECT c.Name, SUM(o.Total_Price) as total_purchase 
                          FROM orders o
                          JOIN customers c ON o.Customer_ID = c.Customer_ID
                          GROUP BY c.Name
                          ORDER BY total_purchase DESC
                          LIMIT 10";
    $topCustomersData = $conn->query($topCustomersQuery)->fetchAll(PDO::FETCH_ASSOC);

    // Fetch data for top ten products with sales
    $topProductsQuery = "SELECT p.Name, SUM(oi.Quantity) as total_sales 
                         FROM order_items oi
                         JOIN products p ON oi.Product_ID = p.Product_ID
                         GROUP BY p.Name
                         ORDER BY total_sales DESC
                         LIMIT 10";
    $topProductsData = $conn->query($topProductsQuery)->fetchAll(PDO::FETCH_ASSOC);

    // Fetch data for total sales amounts for top 10 products
    $topProductsSalesQuery = "SELECT p.Name, SUM(oi.Subtotal) as total_sales_amount 
                              FROM order_items oi
                              JOIN products p ON oi.Product_ID = p.Product_ID
                              GROUP BY p.Name
                              ORDER BY total_sales_amount DESC
                              LIMIT 10";
    $topProductsSalesData = $conn->query($topProductsSalesQuery)->fetchAll(PDO::FETCH_ASSOC);

    // Fetch data for top ten products with sales quantities
    $topProductsQuantitiesQuery = "SELECT p.Name, SUM(oi.Quantity) as total_quantity 
                                   FROM order_items oi
                                   JOIN products p ON oi.Product_ID = p.Product_ID
                                   GROUP BY p.Name
                                   ORDER BY total_quantity DESC
                                   LIMIT 10";
    $topProductsQuantitiesData = $conn->query($topProductsQuantitiesQuery)->fetchAll(PDO::FETCH_ASSOC);

    // Fetch data for pie chart (stock vs out of stock)
    $stockDataQuery = "
        SELECT 
            SUM(CASE WHEN s.Quantity > 0 THEN 1 ELSE 0 END) AS in_stock,
            SUM(CASE WHEN s.Quantity = 0 THEN 1 ELSE 0 END) AS out_of_stock
        FROM shades s
    ";
    $stockData = $conn->query($stockDataQuery)->fetch(PDO::FETCH_ASSOC);

    // Fetch data for daily sales
    $dailySalesQuery = "SELECT DATE_FORMAT(Order_Date, '%Y-%m-%d') as date, SUM(Total_Price) as total 
                        FROM orders 
                        GROUP BY DATE_FORMAT(Order_Date, '%Y-%m-%d')";
    $dailySalesData = $conn->query($dailySalesQuery)->fetchAll(PDO::FETCH_ASSOC);

    // Fetch data for monthly sales
    $monthlySalesQuery = "SELECT DATE_FORMAT(Order_Date, '%Y-%m') as month, SUM(Total_Price) as total 
                          FROM orders 
                          GROUP BY DATE_FORMAT(Order_Date, '%Y-%m')";
    $monthlySalesData = $conn->query($monthlySalesQuery)->fetchAll(PDO::FETCH_ASSOC);

    // Fetch data for yearly sales
    $yearlySalesQuery = "SELECT DATE_FORMAT(Order_Date, '%Y') as year, SUM(Total_Price) as total 
                         FROM orders 
                         GROUP BY DATE_FORMAT(Order_Date, '%Y')";
    $yearlySalesData = $conn->query($yearlySalesQuery)->fetchAll(PDO::FETCH_ASSOC);

    // Fetch data for coupon usage by category
    $couponUsageQuery = "SELECT c.Category_Name, COUNT(o.Order_ID) as coupon_usage 
                         FROM orders o
                         JOIN order_items oi ON o.Order_ID = oi.Order_ID
                         JOIN products p ON oi.Product_ID = p.Product_ID
                         JOIN categories c ON p.Category_ID = c.Category_ID
                         JOIN coupons co ON o.cupon_id = co.Coupon_ID
                         WHERE o.cupon_id IS NOT NULL
                         GROUP BY c.Category_Name";
    $couponUsageData = $conn->query($couponUsageQuery)->fetchAll(PDO::FETCH_ASSOC);

    // Fetch data for percentage of customers who use coupons
    $couponUsagePercentQuery = "SELECT 
                                SUM(CASE WHEN cupon_id IS NOT NULL THEN 1 ELSE 0 END) AS used_coupon,
                                SUM(CASE WHEN cupon_id IS NULL THEN 1 ELSE 0 END) AS not_used_coupon
                                FROM orders";
    $couponUsagePercentData = $conn->query($couponUsagePercentQuery)->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../Admin/admin_css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../Admin/admin_Javascript/sidebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" href="path/to/favicon.ico">
    <title>Charm & Grace: Dashboard</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .dashboard-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 20px;
        }

        .charts-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .chart-box {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .chart-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        canvas {
            max-width: 100%;
            max-height: 250px;
        }

        .donut-chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .donut-chart-container canvas {
            max-width: 200px;
            max-height: 200px;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
            background-color: #d97cb3;
            color: #fff;
        }

        .table tbody+tbody {
            border-top: 2px solid #dee2e6;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .btn-primary {
            background-color: #d97cb3;
            border-color: #d97cb3;
            color: #fff;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #c2185b;
            border-color: #c2185b;
        }

        .btn-primary:focus,
        .btn-primary:active {
            background-color: #c2185b;
            border-color: #c2185b;
            box-shadow: 0 0 0 0.2rem rgba(194, 24, 91, 0.5);
        }

        .btn-link {
            color: #d97cb3;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .btn-link:hover {
            color: #c2185b;
            text-decoration: underline;
        }

        .btn-link:focus,
        .btn-link:active {
            color: #c2185b;
            text-decoration: underline;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .col {
            flex: 1;
            min-width: 300px;
        }

        .filter-container {
            flex: 1;
            max-width: 300px;
        }

        .chart-container {
            flex: 1;
            max-width: 100%;
            /* Ensure the chart container fits within the box */
        }

        .filter-chart-container {
            display: flex;
            flex-direction: row;
            gap: 20px;
        }

        .filter-container {
            flex: 1;
            max-width: 300px;
        }

        .chart {
            flex: 2;
        }

        .small-pie-chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .small-pie-chart-container canvas {
            max-width: 100px;
            max-height: 100px;
        }
    </style>
</head>

<body>

    <?php include 'sidebar_nav.php'; ?>

    <div class="container mt-2">
        <h2 class="text-center">Admin Dashboard</h2>

        <div class="dashboard-container">
            <div class="row">
                <div class="col">
                    <div class="chart-box">
                        <h4>Daily Sales Records</h4>
                        <div class="filter-chart-container">
                            <div class="filter-container">
                                <div class="mb-3">
                                    <label for="startDateFilter" class="form-label">Start Date:</label>
                                    <input type="date" id="startDateFilter" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="endDateFilter" class="form-label">End Date:</label>
                                    <input type="date" id="endDateFilter" class="form-control">
                                </div>
                                <button id="filterButton" class="btn btn-primary mb-3">Filter</button>
                            </div>
                            <div class="chart">
                                <canvas id="dailySalesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="chart-box">
                        <h4>Monthly Sales Report</h4>
                        <div class="filter-chart-container">
                            <div class="filter-container">
                                <div class="mb-3">
                                    <label for="yearFilter" class="form-label">Year:</label>
                                    <input type="number" id="yearFilter" class="form-control" min="2000" max="2100" step="1">
                                </div>
                                <button id="monthlyFilterButton" class="btn btn-primary mb-3">Filter</button>
                            </div>
                            <div class="chart">
                                <canvas id="monthlySalesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="chart-box">
                        <h4>Yearly Sales Report</h4>
                        <canvas id="yearlySalesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="chart-box">
                        <h4>Total Quantity of Products by Category</h4>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Total Quantity</th>
                                </tr>
                            </thead>
                            <tbody id="categoryQuantityTableBody">
                                <?php foreach ($categoryQuantityData as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['Category_Name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['total_quantity']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col">
                    <div class="chart-box">
                        <h4>Numbers of Products for Stock and Stock-out</h4>
                        <canvas id="stockPieChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="chart-box">
                        <h4>Coupon Usage by Category</h4>
                        <div class="chart-container">
                            <canvas id="couponUsageChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="chart-box">
                        <h4>Percentage of Customers Using Coupons</h4>
                        <canvas id="couponUsagePercentChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="charts-container">
                <div class="chart-box">
                    <canvas id="barChart"></canvas>
                </div>





                <div class="chart-box">
                    <h4>Top 10 Products by Stock Quantity</h4>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Total Quantity</th>
                            </tr>
                        </thead>
                        <tbody id="topProductsQuantitiesTableBody">
                            <?php foreach ($topProductsQuantitiesData as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['Name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['total_quantity']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="chart-box">
                    <canvas id="topProductsSalesChart"></canvas>
                </div>
                <div class="chart-box">
                    <canvas id="topCustomersChart"></canvas>
                </div>
                <div class="chart-box donut-chart-container">
                    <canvas id="donutChart"></canvas>
                </div>

                <div class="chart-box">
                    <canvas id="avgRatingChart"></canvas>
                </div>

            </div>

        </div>

    </div>

    <script>
        function updateCharts(lineData, barData, donutData, radarData, categoryQuantityData, topCustomersData, topProductsData, topProductsSalesData, topProductsQuantitiesData, stockData, avgRatingData, couponUsageData, couponUsagePercentData) {
            lineChart.data.labels = lineData.labels;
            lineChart.data.datasets[0].data = lineData.data.map(value => `$${value.toFixed(2)}`);
            lineChart.update();

            barChart.data.labels = barData.labels;
            barChart.data.datasets[0].data = barData.data.map(value => `$${value.toFixed(2)}`);
            barChart.update();

            topProductsSalesChart.data.labels = topProductsSalesData.labels;
            topProductsSalesChart.data.datasets[0].data = topProductsSalesData.data.map(value => `$${value.toFixed(2)}`);
            topProductsSalesChart.update();

            donutChart.data.labels = donutData.labels;
            donutChart.data.datasets[0].data = donutData.data.map(value => `$${value.toFixed(2)}`);
            donutChart.update();

            radarChart.data.labels = radarData.labels;
            radarChart.data.datasets[0].data = radarData.data.map(value => `$${value.toFixed(2)}`);
            radarChart.update();

            const categoryQuantityTableBody = document.getElementById('categoryQuantityTableBody');
            categoryQuantityTableBody.innerHTML = '';
            categoryQuantityData.labels.forEach((label, index) => {
                const row = document.createElement('tr');
                const categoryCell = document.createElement('td');
                categoryCell.textContent = label;
                const quantityCell = document.createElement('td');
                quantityCell.textContent = categoryQuantityData.data[index];
                row.appendChild(categoryCell);
                row.appendChild(quantityCell);
                categoryQuantityTableBody.appendChild(row);
            });

            topCustomersChart.data.labels = topCustomersData.labels;
            topCustomersChart.data.datasets[0].data = topCustomersData.data.map(value => `$${value.toFixed(2)}`);
            topCustomersChart.update();

            topProductsChart.data.labels = topProductsData.labels;
            topProductsChart.data.datasets[0].data = topProductsData.data.map(value => `$${value.toFixed(2)}`);
            topProductsChart.update();

            const topProductsQuantitiesTableBody = document.getElementById('topProductsQuantitiesTableBody');
            topProductsQuantitiesTableBody.innerHTML = '';
            topProductsQuantitiesData.labels.forEach((label, index) => {
                const row = document.createElement('tr');
                const productCell = document.createElement('td');
                productCell.textContent = label;
                const quantityCell = document.createElement('td');
                quantityCell.textContent = topProductsQuantitiesData.data[index];
                row.appendChild(productCell);
                row.appendChild(quantityCell);
                topProductsQuantitiesTableBody.appendChild(row);
            });

            stockPieChart.data.datasets[0].data = [stockData.in_stock, stockData.out_of_stock];
            stockPieChart.update();

            avgRatingChart.data.labels = avgRatingData.labels;
            avgRatingChart.data.datasets[0].data = avgRatingData.data.map(value => `$${value.toFixed(2)}`);
            avgRatingChart.update();

            couponUsageChart.data.labels = couponUsageData.labels;
            couponUsageChart.data.datasets[0].data = couponUsageData.data.map(value => `$${value.toFixed(2)}`);
            couponUsageChart.update();

            couponUsagePercentChart.data.datasets[0].data = [couponUsagePercentData.used_coupon, couponUsagePercentData.not_used_coupon];
            couponUsagePercentChart.update();
        }



        const barChart = new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($barData, 'Name')); ?>,
                datasets: [{
                    label: 'Top Products by Sales',
                    data: <?php echo json_encode(array_column($barData, 'total_quantity')); ?>,
                    backgroundColor: '#28a745'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });

        const donutChart = new Chart(document.getElementById('donutChart'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($donutData, 'Method_Name')); ?>,
                datasets: [{
                    label: 'Sales by Payment Method',
                    data: <?php echo json_encode(array_column($donutData, 'count')); ?>,
                    backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });

        const topCustomersChart = new Chart(document.getElementById('topCustomersChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($topCustomersData, 'Name')); ?>,
                datasets: [{
                    label: 'Top 10 Customers by Purchase Amount',
                    data: <?php echo json_encode(array_column($topCustomersData, 'total_purchase')); ?>,
                    backgroundColor: '#36a2eb'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });

        const topProductsChart = new Chart(document.getElementById('topProductsChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($topProductsData, 'Name')); ?>,
                datasets: [{
                    label: 'Top 10 Products by Sales Counts',
                    data: <?php echo json_encode(array_column($topProductsData, 'total_sales')); ?>,
                    backgroundColor: '#ffce56'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });

        const topProductsSalesChart = new Chart(document.getElementById('topProductsSalesChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($topProductsSalesData, 'Name')); ?>,
                datasets: [{
                    label: 'Top 10 Products by Sales Amount',
                    data: <?php echo json_encode(array_column($topProductsSalesData, 'total_sales_amount')); ?>,
                    backgroundColor: '#ff9f40'
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });

        const stockPieChart = new Chart(document.getElementById('stockPieChart'), {
            type: 'pie',
            data: {
                labels: ['In Stock', 'Out of Stock'],
                datasets: [{
                    data: [<?php echo $stockData['in_stock']; ?>, <?php echo $stockData['out_of_stock']; ?>],
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.raw !== null) {
                                    label += context.raw;
                                }
                                return label;
                            }
                        }
                    }
                },
                layout: {
                    padding: 40
                },
                elements: {
                    arc: {
                        borderWidth: 1,
                        borderColor: '#fff',
                        hoverBorderColor: '#fff',
                        hoverBorderWidth: 2,
                        borderAlign: 'inner'
                    }
                }
            }
        });

        const avgRatingChart = new Chart(document.getElementById('avgRatingChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($radarData, 'Category_Name')); ?>,
                datasets: [{
                    label: 'Average Rating by Category',
                    data: <?php echo json_encode(array_column($radarData, 'avg_rating')); ?>,
                    backgroundColor: '#ff9f40'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });

        const yearlySalesChart = new Chart(document.getElementById('yearlySalesChart'), {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($yearlySalesData, 'year')); ?>,
                datasets: [{
                    label: 'Yearly Sales',
                    data: <?php echo json_encode(array_column($yearlySalesData, 'total')); ?>,
                    backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff', '#ff9f40']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.raw !== null) {
                                    label += context.raw;
                                }
                                return label;
                            }
                        }
                    }
                },
                layout: {
                    padding: 40
                },
                elements: {
                    arc: {
                        borderWidth: 1,
                        borderColor: '#fff',
                        hoverBorderColor: '#fff',
                        hoverBorderWidth: 2,
                        borderAlign: 'inner'
                    }
                }
            }
        });

        const monthlySalesChart = new Chart(document.getElementById('monthlySalesChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($monthlySalesData, 'month')); ?>,
                datasets: [{
                    label: 'Monthly Sales',
                    data: <?php echo json_encode(array_column($monthlySalesData, 'total')); ?>,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });

        const dailySalesChart = new Chart(document.getElementById('dailySalesChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($dailySalesData, 'date')); ?>,
                datasets: [{
                    label: 'Daily Sales',
                    data: <?php echo json_encode(array_column($dailySalesData, 'total')); ?>,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });

        const couponUsageChart = new Chart(document.getElementById('couponUsageChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($couponUsageData, 'Category_Name')); ?>,
                datasets: [{
                    label: 'Coupon Usage by Category',
                    data: <?php echo json_encode(array_column($couponUsageData, 'coupon_usage')); ?>,
                    backgroundColor: '#36a2eb'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });

        const couponUsagePercentChart = new Chart(document.getElementById('couponUsagePercentChart'), {
            type: 'pie',
            data: {
                labels: ['Used Coupon', 'Did Not Use Coupon'],
                datasets: [{
                    data: [<?php echo $couponUsagePercentData['used_coupon']; ?>, <?php echo $couponUsagePercentData['not_used_coupon']; ?>],
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.raw !== null) {
                                    label += context.raw;
                                }
                                return label;
                            }
                        }
                    }
                },
                layout: {
                    padding: 40
                },
                elements: {
                    arc: {
                        borderWidth: 1,
                        borderColor: '#fff',
                        hoverBorderColor: '#fff',
                        hoverBorderWidth: 2,
                        borderAlign: 'inner'
                    }
                }
            }
        });

        document.getElementById('filterButton').addEventListener('click', function() {
            const startDate = document.getElementById('startDateFilter').value;
            const endDate = document.getElementById('endDateFilter').value;
            fetch(`fetchDailySales.php?start_date=${startDate}&end_date=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    dailySalesChart.data.labels = data.map(row => row.date);
                    dailySalesChart.data.datasets[0].data = data.map(row => row.total);
                    dailySalesChart.update();
                })
                .catch(error => console.error('Error fetching daily sales data:', error));
        });

        document.getElementById('monthlyFilterButton').addEventListener('click', function() {
            const year = document.getElementById('yearFilter').value;
            fetch(`fetchMonthlySales.php?year=${year}`)
                .then(response => response.json())
                .then(data => {
                    monthlySalesChart.data.labels = data.map(row => row.month);
                    monthlySalesChart.data.datasets[0].data = data.map(row => row.total);
                    monthlySalesChart.update();
                })
                .catch(error => console.error('Error fetching monthly sales data:', error));
        });
    </script>
</body>

</html>