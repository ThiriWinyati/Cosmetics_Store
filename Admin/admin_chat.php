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

// Only fetch customer chat histories if real admin is logged in
if ($isAdmin) {
    try {
        $sql = "SELECT 
                    cm.customer_id,
                    c.Name AS customer_name,
                    MAX(cm.timestamp) AS last_timestamp,
                    SUBSTRING_INDEX(GROUP_CONCAT(cm.message ORDER BY cm.timestamp DESC SEPARATOR '|||'), '|||', 1) AS last_message,
                    SUM(CASE WHEN cm.admin_read = 0 AND cm.admin_id IS NULL THEN 1 ELSE 0 END) AS new_messages
                FROM chat_messages cm
                LEFT JOIN customers c ON cm.customer_id = c.Customer_ID
                GROUP BY cm.customer_id, c.Name
                ORDER BY last_timestamp DESC";

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
            max-width: 1280px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .chat-title {
            text-align: center;
            margin-bottom: 28px;
            font-weight: 700;
        }

        .chat-layout {
            display: grid;
            grid-template-columns: minmax(280px, 360px) minmax(0, 1fr);
            gap: 22px;
            align-items: stretch;
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

        .customer-panel {
            height: min(72vh, 720px);
            display: flex;
            flex-direction: column;
            padding: 16px;
        }

        .chat-history-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px;
        }

        .chat-history-heading h5 {
            margin-bottom: 0;
        }

        .chat-history-count {
            color: #7a7f89;
            font-size: 0.85rem;
            font-weight: 700;
        }

        #customer-list {
            overflow-y: auto;
            padding-right: 4px;
        }

        #customer-list .list-group-item {
            border-radius: 14px;
            margin-bottom: 10px;
            border: 1px solid #f0d6e4;
            padding: 12px;
            cursor: pointer;
            transition: 0.2s ease;
        }

        #customer-list .list-group-item:hover {
            background: #fce8f2;
        }

        #customer-list .list-group-item.active {
            background: #fff0f7;
            border-color: #d97cb3;
            color: #222222;
        }

        .chat-history-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
        }

        .chat-history-name {
            display: block;
            font-weight: 800;
            color: #222222;
        }

        .chat-history-time {
            white-space: nowrap;
            color: #8b9099;
            font-size: 0.78rem;
        }

        .chat-history-preview {
            margin: 6px 0 0;
            color: #6c757d;
            font-size: 0.88rem;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .chat-history-badge {
            margin-top: 8px;
        }

        .chat-panel {
            min-height: min(72vh, 720px);
            display: flex;
            flex-direction: column;
        }

        .active-chat-name {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 600;
            margin-left: 6px;
        }

        #chat-box {
            flex: 1;
            min-height: 420px;
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
        html[data-theme="dark"] .customer-panel,
        html[data-theme="dark"] .chat-panel,
        .dark-mode .customer-panel,
        .dark-mode .chat-panel {
            background: #1f1f27;
            color: #f5f5f5;
            border-color: #343442;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        }

        body.dark-mode #chat-box,
        html[data-theme="dark"] #chat-box,
        .dark-mode #chat-box {
            background: #15151b;
            color: #f5f5f5;
            border-color: #343442;
        }

        body.dark-mode #customer-list .list-group-item,
        html[data-theme="dark"] #customer-list .list-group-item,
        .dark-mode #customer-list .list-group-item {
            background: #2a2a35;
            color: #f5f5f5;
            border-color: #343442;
        }

        body.dark-mode #customer-list .list-group-item:hover,
        html[data-theme="dark"] #customer-list .list-group-item:hover,
        .dark-mode #customer-list .list-group-item:hover {
            background: #353545;
        }

        html[data-theme="dark"] #customer-list .list-group-item.active {
            background: #352530;
            border-color: #ff7fbd;
            color: #f5f5f5;
        }

        html[data-theme="dark"] .chat-history-name {
            color: #f5f5f5;
        }

        html[data-theme="dark"] .chat-history-preview,
        html[data-theme="dark"] .chat-history-time,
        html[data-theme="dark"] .active-chat-name,
        html[data-theme="dark"] .chat-history-count {
            color: #b8bcc6;
        }

        body.dark-mode .locked-chat-card,
        html[data-theme="dark"] .locked-chat-card,
        .dark-mode .locked-chat-card {
            background: #1f1f27;
            color: #f5f5f5;
            border-color: #343442;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        }

        body.dark-mode .locked-chat-card p,
        html[data-theme="dark"] .locked-chat-card p,
        .dark-mode .locked-chat-card p {
            color: #d6d6d6;
        }

        @media (max-width: 768px) {
            .admin-chat-wrapper {
                padding: 20px 12px;
            }

            .chat-layout {
                grid-template-columns: 1fr;
            }

            .customer-panel {
                margin-bottom: 20px;
                height: auto;
                max-height: 320px;
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

            <div class="chat-layout">
                <aside class="customer-panel">
                    <div class="chat-history-heading">
                        <h5>Chat History</h5>
                        <span class="chat-history-count"><?php echo count($customers); ?> chats</span>
                    </div>

                    <ul id="customer-list" class="list-group">
                        <?php if (!empty($customers)): ?>
                            <?php foreach ($customers as $customer): ?>
                                <?php
                                    $customerName = htmlspecialchars($customer['customer_name'] ?? 'Unknown customer');
                                    $lastMessage = htmlspecialchars($customer['last_message'] ?? 'No messages yet.');
                                    $lastTime = !empty($customer['last_timestamp']) ? date('M j, H:i', strtotime($customer['last_timestamp'])) : '';
                                    $unreadCount = (int)($customer['new_messages'] ?? 0);
                                ?>
                                <li class="list-group-item customer"
                                    data-customer-id="<?php echo htmlspecialchars($customer['customer_id']); ?>"
                                    data-customer-name="<?php echo $customerName; ?>">
                                    <div class="chat-history-top">
                                        <span class="chat-history-name"><?php echo $customerName; ?></span>
                                        <span class="chat-history-time"><?php echo htmlspecialchars($lastTime); ?></span>
                                    </div>
                                    <p class="chat-history-preview"><?php echo $lastMessage; ?></p>
                                    <?php if ($unreadCount > 0): ?>
                                        <span class="badge bg-primary chat-history-badge">
                                            <?php echo $unreadCount; ?> new
                                        </span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item text-center">
                                No chat history yet.
                            </li>
                        <?php endif; ?>
                    </ul>
                </aside>

                <div class="chat-panel">
                    <h5>
                        Chat with Customer
                        <span id="active-chat-name" class="active-chat-name">No customer selected</span>
                    </h5>

                    <div id="chat-box">
                        <p class="text-muted text-center mt-5">
                            Select a customer from chat history to view messages.
                        </p>
                    </div>

                    <textarea id="admin-message" class="form-control" rows="3" placeholder="Type your message"></textarea>
                    <button id="send-message" class="btn btn-primary mt-2">Send</button>
                </div>
            </div>
        </div>

        <script>
            let selectedCustomerId = null;

            $(document).ready(function() {
                const storedCustomerId = localStorage.getItem('selectedCustomerId');

                if (storedCustomerId && $(`.customer[data-customer-id="${storedCustomerId}"]`).length) {
                    selectedCustomerId = storedCustomerId;
                    setActiveCustomer($(`.customer[data-customer-id="${storedCustomerId}"]`));
                    loadMessages();
                } else if ($('.customer').length) {
                    setActiveCustomer($('.customer').first());
                    selectedCustomerId = $('.customer').first().data('customer-id');
                    localStorage.setItem('selectedCustomerId', selectedCustomerId);
                    loadMessages();
                }
            });

            $(document).on('click', '.customer', function() {
                selectedCustomerId = $(this).data('customer-id');
                localStorage.setItem('selectedCustomerId', selectedCustomerId);
                setActiveCustomer($(this));
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
                            $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
                        });
                    });
                }
            }

            function setActiveCustomer(item) {
                $('.customer').removeClass('active');
                item.addClass('active');
                $('#active-chat-name').text(item.data('customer-name') || 'Selected customer');
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
