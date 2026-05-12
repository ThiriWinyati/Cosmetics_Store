<?php
require_once "../db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = $_POST['customer_id'];

    // Update admin_read to 1 for all unread messages for the selected customer
    $updateSql = "UPDATE chat_messages SET admin_read = 1 WHERE customer_id = ? AND admin_read = 0";
    $stmt = $conn->prepare($updateSql);
    $stmt->execute([$customerId]);
}
