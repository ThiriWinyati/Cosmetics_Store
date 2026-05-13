<?php
require_once "../db_connect.php";
require_once "admin_auth.php";

$isAdmin = admin_is_logged_in();

$customers = [];

// Only allow real admin to use chat actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    admin_require_login('admin_chat.php');

    $message = $_POST['message'];

    if (!isset($_SESSION['chat_messages'])) {
        $_SESSION['chat_messages'] = [];
    }

    $_SESSION['chat_messages'][] = $message;
}

$adminId = $_SESSION['admin_id'] ?? null;

// Only fetch customer chats if real admin is logged in
if ($isAdmin) {
    try {
        $sql = "SELECT cm.customer_id, c.Name AS customer_name, COUNT(cm.message) AS new_messages
                FROM chat_messages cm
                LEFT JOIN customers c ON cm.customer_id = c.Customer_ID
                WHERE cm.admin_read = 0
                GROUP BY cm.customer_id, c.Name";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching chat customers: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Charm & Grace: Admin Chat</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Admin/admin_css/style.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../Admin/admin_Javascript/sidebar.js"></script>

    <link rel="icon" href="path/to/favicon.ico">

    <style>
        .admin-chat-wrapper {
            width: 100%;
            max-width: 1150px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .chat-title {
            text-align: center;
            margin-bottom: 28px;
            font-weight: 700;
        }

        .customer-panel,
        .chat-panel {
            background: #ffffff;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            border: 1px solid #eeeeee;
        }

        .customer-panel h5,
        .chat-panel h5 {
            color: #d97cb3;
            font-weight: 700;
            margin-bottom: 14px;
        }

        #customer-list .list-group-item {
            border-radius: 10px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: 0.2s ease;
        }

        #customer-list .list-group-item:hover {
            background: #fce8f2;
        }

        #chat-box {
            height: 320px;
            overflow-y: auto;
            border-radius: 14px;
            border: 1px solid #dddddd;
            padding: 16px;
            background: #ffffff;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        #admin-message {
            border-radius: 14px;
            margin-top: 14px;
        }

        #send-message {
            border-radius: 12px;
            background-color: #d97cb3;
            border-color: #d97cb3;
            padding: 8px 22px;
        }

        #send-message:hover {
            background-color: #c2185b;
            border-color: #c2185b;
        }

        .locked-chat-wrapper {
            min-height: 60vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .locked-chat-card {
            width: 100%;
            max-width: 550px;
            text-align: center;
            padding: 42px 32px;
            border-radius: 20px;
            background: #ffffff;
            color: #222;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            border: 1px solid #eeeeee;
        }

        .locked-chat-card i {
            color: #d97cb3;
            margin-bottom: 18px;
        }

        .locked-chat-card h3 {
            font-weight: 700;
            margin-bottom: 12px;
        }

        .locked-chat-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 22px;
        }

        .locked-chat-card .btn-primary {
            background-color: #d97cb3;
            border-color: #d97cb3;
            border-radius: 12px;
            padding: 9px 22px;
        }

        .locked-chat-card .btn-primary:hover {
            background-color: #c2185b;
            border-color: #c2185b;
        }

        body.dark-mode .customer-panel,
        body.dark-mode .chat-panel,
        .dark-mode .customer-panel,
        .dark-mode .chat-panel {
            background: #1f1f27;
            color: #f5f5f5;
            border-color: #343442;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        }

        body.dark-mode #chat-box,
        .dark-mode #chat-box {
            background: #15151b;
            color: #f5f5f5;
            border-color: #343442;
        }

        body.dark-mode #customer-list .list-group-item,
        .dark-mode #customer-list .list-group-item {
            background: #2a2a35;
            color: #f5f5f5;
            border-color: #343442;
        }

        body.dark-mode #customer-list .list-group-item:hover,
        .dark-mode #customer-list .list-group-item:hover {
            background: #353545;
        }

        body.dark-mode .locked-chat-card,
        .dark-mode .locked-chat-card {
            background: #1f1f27;
            color: #f5f5f5;
            border-color: #343442;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        }

        body.dark-mode .locked-chat-card p,
        .dark-mode .locked-chat-card p {
            color: #d6d6d6;
        }

        @media (max-width: 768px) {
            .admin-chat-wrapper {
                padding: 20px 12px;
            }

            .customer-panel {
                margin-bottom: 20px;
            }

            .locked-chat-card {
                padding: 32px 22px;
            }
        }
    </style>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>

    <?php if ($isAdmin): ?>

        <div class="admin-chat-wrapper">
            <h3 class="chat-title">Admin Chat Interface</h3>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="customer-panel">
                        <h5>Customers</h5>

                        <ul id="customer-list" class="list-group">
                            <?php if (!empty($customers)): ?>
                                <?php foreach ($customers as $customer): ?>
                                    <li class="list-group-item customer" data-customer-id="<?php echo htmlspecialchars($customer['customer_id']); ?>">
                                        <?php echo "Customer: " . htmlspecialchars($customer['customer_name'] ?? 'Unknown'); ?>
                                        <span class="badge bg-primary float-end">
                                            <?php echo htmlspecialchars($customer['new_messages']); ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item text-center">
                                    No unread customer chats.
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="chat-panel">
                        <h5>Chat with Customer</h5>

                        <div id="chat-box">
                            <p class="text-muted text-center mt-5">
                                Select a customer to view messages.
                            </p>
                        </div>

                        <textarea id="admin-message" class="form-control" rows="3" placeholder="Type your message"></textarea>
                        <button id="send-message" class="btn btn-primary mt-2">Send</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            let selectedCustomerId = null;

            $(document).ready(function() {
                const storedCustomerId = localStorage.getItem('selectedCustomerId');

                if (storedCustomerId) {
                    selectedCustomerId = storedCustomerId;
                    loadMessages();
                }
            });

            $(document).on('click', '.customer', function() {
                selectedCustomerId = $(this).data('customer-id');
                localStorage.setItem('selectedCustomerId', selectedCustomerId);
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
                            $('#admin-message').val('');
                            loadMessages();
                        }
                    });
                } else {
                    alert('Please select a customer and type a message.');
                }
            });

            function loadMessages() {
                if (selectedCustomerId) {
                    $.post('update_admin_read.php', {
                        customer_id: selectedCustomerId
                    }, function() {
                        $.post('admin_load_messages.php', {
                            customer_id: selectedCustomerId
                        }, function(data) {
                            $('#chat-box').html(data);
                        });
                    });
                }
            }
        </script>

    <?php else: ?>

        <div class="locked-chat-wrapper">
            <div class="locked-chat-card">
                <i class="fa fa-lock fa-3x"></i>
                <h3>Admin Chat Locked</h3>
                <p>
                    Customer chat messages are hidden in portfolio preview mode.
                    Only logged-in admins can view and reply to customer conversations.
                </p>
                <a href="adminLogin.php" class="btn btn-primary">
                    Login as Admin
                </a>
            </div>
        </div>

    <?php endif; ?>

</body>

</html>