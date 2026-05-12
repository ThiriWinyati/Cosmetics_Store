<?php
require_once "../db_connect.php";

if (!isset($_SESSION)) {
    session_start();
}

// Get the customer ID from the session
$customerId = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : null;

if ($customerId) {
    // Fetch the latest chat messages
    $sql = "SELECT *, 
            CASE
                WHEN admin_id IS NOT NULL THEN 'admin'
                ELSE 'customer'
            END AS sender_type
            FROM chat_messages
            WHERE customer_id = ? 
            ORDER BY timestamp ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$customerId]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($messages as &$message) {
        $message['formatted_time'] = date('H:i', strtotime($message['timestamp']));
        $message['formatted_date'] = date('Y-m-d', strtotime($message['timestamp']));
        if ($message['sender_type'] == 'admin') {
            $message['display_name'] = 'Admin';
        } else {
            $message['display_name'] = 'You';
        }
    }

    echo json_encode($messages);
} else {
    echo json_encode([]);
}
