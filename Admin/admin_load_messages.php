<?php
require_once "../db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['customer_id'])) {
        die("Missing required data.");
    }

    $customerId = $_POST['customer_id'];

    // Fetch chat messages for the selected customer along with the admin's name
    $sql = "SELECT cm.message, cm.timestamp, cm.admin_id, cm.customer_id, a.name AS admin_name
            FROM chat_messages cm
            LEFT JOIN admin_users a ON cm.admin_id = a.Admin_User_ID
            WHERE cm.customer_id = ?
            ORDER BY cm.timestamp ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$customerId]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $lastDate = null; // Variable to track the last displayed date

    foreach ($messages as $message) {
        $messageDate = date('Y-m-d', strtotime($message['timestamp']));
        $messageTime = date('H:i', strtotime($message['timestamp']));

        // Check if the date has changed
        if ($messageDate !== $lastDate) {
            echo '<div class="date-header text-center" style="margin: 10px 0; font-weight: bold;">' . htmlspecialchars($messageDate) . '</div>';
            $lastDate = $messageDate; // Update the last displayed date
        }

        if ($message['admin_id'] != null) {
            // Admin message
            echo '<div class="d-flex justify-content-end mb-3">
                    <div class="bg-primary text-white p-2 rounded">
                        <strong>' . htmlspecialchars($message['admin_name']) . '</strong><br>
                        ' . htmlspecialchars($message['message']) . '<br>
                        <small>' . $messageTime . '</small>
                    </div>
                  </div>';
        } else {
            // Customer message
            echo '<div class="d-flex justify-content-start mb-3">
                    <div class="bg-light p-2 rounded">
                        <strong>Customer</strong><br>
                        ' . htmlspecialchars($message['message']) . '<br>
                        <small>' . $messageTime . '</small>
                    </div>
                  </div>';
        }
    }
}
