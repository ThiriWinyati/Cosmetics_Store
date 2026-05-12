<?php
require_once "../db_connect.php";

if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $startDate = $_GET['start_date'];
    $endDate = $_GET['end_date'];

    try {

        $dailySalesQuery = $conn->prepare("SELECT DATE_FORMAT(Order_Date, '%Y-%m-%d') as date, SUM(Total_Price) as total 
                                           FROM orders 
                                           WHERE DATE_FORMAT(Order_Date, '%Y-%m-%d') BETWEEN :start_date AND :end_date 
                                           GROUP BY DATE_FORMAT(Order_Date, '%Y-%m-%d')");
        $dailySalesQuery->execute(['start_date' => $startDate, 'end_date' => $endDate]);
        $dailySalesData = $dailySalesQuery->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($dailySalesData);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Start date or end date parameter is missing']);
}
