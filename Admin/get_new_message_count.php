<?php
require_once "../db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = $_POST['customer_id'];

    // Fetch the count of new messages for the specific customer
    $sql = "SELECT COUNT(*) AS new_messages FROM chat_messages WHERE customer_id = ? AND admin_read = 0";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$customerId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo $result['new_messages'];
}
