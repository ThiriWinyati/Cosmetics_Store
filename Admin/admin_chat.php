<?php
require_once "../db_connect.php";
require_once "admin_auth.php";

// Clear previous chat messages when a new session starts
if (!isset($_SESSION['customer_id'])) {
    $_SESSION['chat_messages'] = [];
}

// Function to add a new message to the chat
function addMessage($message)
{
    $_SESSION['chat_messages'][] = $message;
}

// Example usage
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    admin_require_login('admin_chat.php');
    $message = $_POST['message'];
    addMessage($message);
}

$adminId = $_SESSION['admin_id'] ?? null;

// Fetch distinct customer chats
$sql = "SELECT cm.customer_id, c.name AS customer_name, COUNT(cm.message) AS new_messages
        FROM chat_messages cm
        LEFT JOIN customers c ON cm.customer_id = c.Customer_ID
        WHERE cm.admin_read = 0
        GROUP BY cm.customer_id, c.name";
$stmt = $conn->prepare($sql);
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../Admin/admin_Javascript/sidebar.js"></script>
    <link rel="icon" href="path/to/favicon.ico">
    <title>Charm & Grace: Admin Chart</title>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>

    <div class="container mt-5">
        <h3 class="text-center">Admin Chat Interface</h3>
        <div class="row">
            <div class="col-md-4">
                <h5>Customers</h5>
                <ul id="customer-list" class="list-group">
                    <?php foreach ($customers as $customer): ?>
                        <li class="list-group-item customer" data-customer-id="<?= $customer['customer_id'] ?>">
                            <?= "Customer: " . htmlspecialchars($customer['customer_name']) ?> (ID: <?= $customer['customer_id'] ?>)
                            <span class="badge bg-primary float-end"><?= $customer['new_messages'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="col-md-8">
                <h5>Chat with Customer</h5>
                <div id="chat-box" class="border p-3 mb-3" style="height: 300px; overflow-y: scroll; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    <!-- Messages will be loaded here -->
                    <?php
                    if (isset($_SESSION['chat_messages'])) {
                        foreach ($_SESSION['chat_messages'] as $msg) {
                            echo "<div class='message'>{$msg}</div>";
                        }
                    }
                    ?>
                </div>
                <textarea id="admin-message" class="form-control" rows="3" placeholder="Type your message" style="border-radius: 10px;"></textarea>
                <button id="send-message" class="btn btn-primary mt-2" style="border-radius: 10px;">Send</button>
            </div>
        </div>
    </div>

    <script>
        let selectedCustomerId;

        $(document).ready(function() {
            // Check if there is a stored customer ID in local storage
            const storedCustomerId = localStorage.getItem('selectedCustomerId');
            if (storedCustomerId) {
                selectedCustomerId = storedCustomerId;
                loadMessages(); // Load messages for the stored customer ID
            }
        });

        $(document).on('click', '.customer', function() {
            selectedCustomerId = $(this).data('customer-id');
            localStorage.setItem('selectedCustomerId', selectedCustomerId); // Store the selected customer ID
            loadMessages();
        });

        $('#send-message').click(function() {
            const message = $('#admin-message').val();
            if (message.trim() && selectedCustomerId) {
                const data = JSON.stringify({
                    customer_id: selectedCustomerId,
                    message: message
                });
                $.ajax({
                    type: 'POST',
                    url: 'send_admin_message.php',
                    data: data,
                    contentType: 'application/json',
                    success: function(response) {
                        console.log(response); // Check if the message is sent successfully
                        $('#admin-message').val('');
                        loadMessages();
                    }
                });
            }
        });


        function loadMessages() {
            if (selectedCustomerId) {
                // Update admin_read status to 1
                $.post('update_admin_read.php', {
                    customer_id: selectedCustomerId
                }, function() {
                    // After updating, load the messages
                    $.post('admin_load_messages.php', {
                        customer_id: selectedCustomerId
                    }, function(data) {
                        $('#chat-box').html(data);
                    });
                });
            }
        }
    </script>
</body>

</html>
