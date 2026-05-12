<?php
require_once "../db_connect.php";

if (!isset($_SESSION)) {
    session_start();
}

// Get the incoming message from the request
$data = json_decode(file_get_contents('php://input'), true);
$message = $data['message'];

// Get the customer ID from the session
$customerId = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : null;

// For simplicity, we'll set admin_id as null (or a specific admin ID if needed)
$adminId = null;

if ($customerId && $message) {
    // Insert the message into the chat_messages table
    $sql = "INSERT INTO chat_messages (customer_id, admin_id, message, timestamp) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$customerId, $adminId, $message]);

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
