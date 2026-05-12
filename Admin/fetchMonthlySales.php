<?php
require_once "../db_connect.php";

if (isset($_GET['year'])) {
    $year = $_GET['year'];

    try {
        $conn = new PDO("mysql:host=localhost;dbname=cosmetics_store", 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $monthlySalesQuery = $conn->prepare("SELECT DATE_FORMAT(Order_Date, '%Y-%m') as month, SUM(Total_Price) as total 
                                             FROM orders 
                                             WHERE DATE_FORMAT(Order_Date, '%Y') = :year 
                                             GROUP BY DATE_FORMAT(Order_Date, '%Y-%m')");
        $monthlySalesQuery->execute(['year' => $year]);
        $monthlySalesData = $monthlySalesQuery->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($monthlySalesData);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Year parameter is missing']);
}
