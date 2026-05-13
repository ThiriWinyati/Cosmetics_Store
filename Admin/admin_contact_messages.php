<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../db_connect.php";
require_once "admin_auth.php";

$isAdmin = admin_is_logged_in();

function maskText($text)
{
    if (empty($text)) {
        return 'N/A';
    }

    $firstLetter = mb_substr($text, 0, 1);
    return $firstLetter . str_repeat('*', max(mb_strlen($text) - 1, 3));
}

function maskEmail($email)
{
    if (empty($email) || strpos($email, '@') === false) {
        return 'N/A';
    }

    [$localPart, $domain] = explode('@', $email, 2);

    $firstLetter = mb_substr($localPart, 0, 1);
    $maskedLocal = $firstLetter . str_repeat('*', max(mb_strlen($localPart) - 1, 3));

    return $maskedLocal . '@' . $domain;
}

function maskMessage($message)
{
    if (empty($message)) {
        return 'N/A';
    }

    return mb_substr($message, 0, 1) . '********';
}

$isAdmin = admin_is_logged_in();

// Fetch all contact messages
$sql = "SELECT * FROM contactmessages";
$stmt = $conn->prepare($sql);
$stmt->execute();
$messagesList = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalMessages = count($messagesList);
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../Admin/admin_Javascript/sidebar.js"></script>
    <link rel="icon" href="path/to/favicon.ico">
    <title>Admin Contact Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .alert {
            max-width: 100%;
            margin-bottom: 24px;
            border-radius: 12px;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>

    <div class="admin-page-shell">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Contact Messages</h2>
                <p class="admin-page-subtitle">Review customer questions, subjects, and contact details from the storefront form.</p>
            </div>
        </div>

        <div class="admin-summary-grid">
            <div class="admin-stat-card">
                <span class="admin-stat-label">Messages</span>
                <span class="admin-stat-value"><?php echo $totalMessages; ?></span>
            </div>
            <div class="admin-stat-card">
                <span class="admin-stat-label">Access Mode</span>
                <span class="admin-stat-value"><?php echo $isAdmin ? 'Admin' : 'Preview'; ?></span>
            </div>
        </div>

            <?php if (!$isAdmin): ?>
                <div class="alert alert-warning admin-preview-alert">
                    <i class="fa fa-lock"></i>
                    Customer contact details are hidden in portfolio preview mode.
                </div>
            <?php endif; ?>

            <div class="admin-message-grid">
                <?php foreach ($messagesList as $message): ?>
                    <article class="admin-message-card">
                        <div class="admin-message-card-header">
                            <h5 class="admin-message-name">
                            <?php
                            echo $isAdmin
                                ? htmlspecialchars($message['name'])
                                : htmlspecialchars(maskText($message['name']));
                            ?>
                            </h5>
                            <p class="admin-message-subject">
                                <?php
                                echo $isAdmin
                                    ? htmlspecialchars($message['subject'])
                                    : htmlspecialchars(maskText($message['subject']));
                                ?>
                            </p>
                        </div>

                        <div class="admin-message-card-body">
                            <p class="admin-muted-text mb-3">
                                <i class="fa fa-envelope"></i>
                                <?php
                                echo $isAdmin
                                    ? htmlspecialchars($message['email'])
                                    : htmlspecialchars(maskEmail($message['email']));
                                ?>
                            </p>

                            <p class="admin-message-text mb-0">
                                <?php
                                echo $isAdmin
                                    ? nl2br(htmlspecialchars($message['message']))
                                    : htmlspecialchars(maskMessage($message['message']));
                                ?>
                            </p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
    </div>
</body>

</html>
