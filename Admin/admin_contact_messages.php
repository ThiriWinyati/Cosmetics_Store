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
        .contact-message-shell {
            padding-top: 28px;
        }

        .contact-message-header {
            position: relative;
            top: calc(var(--admin-topbar-height, 72px) + 10px);
            z-index: 35;
            padding: 14px 0 20px;
            margin-bottom: 22px;
            background: #ffffff;
            border: 0;
            box-shadow: none;
        }

        .contact-message-header .admin-page-title,
        .contact-message-header .admin-page-subtitle {
            text-align: center;
        }

        .contact-message-header .admin-page-title {
            font-size: clamp(1.7rem, 2.4vw, 2.35rem);
        }

        .contact-message-stats {
            max-width: 720px;
            margin: 18px auto 0;
            grid-template-columns: repeat(2, minmax(180px, 1fr));
        }

        .contact-message-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 22px;
            align-items: stretch;
        }

        .contact-message-card {
            min-height: 300px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid #eee0e8;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 14px 34px rgba(38, 38, 48, 0.08);
        }

        .contact-message-card-header {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 18px;
            border-bottom: 1px solid #f2e5ed;
            background: #fff7fb;
        }

        .contact-message-avatar {
            width: 48px;
            height: 48px;
            flex: 0 0 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #f8d7ea;
            color: #c65091;
            font-weight: 800;
            text-transform: uppercase;
        }

        .contact-message-heading {
            min-width: 0;
        }

        .contact-message-name {
            margin: 0;
            color: #d97cb3;
            font-size: 1.05rem;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .contact-message-subject {
            margin: 6px 0 0;
            color: #25252c;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .contact-message-body {
            flex: 1;
            padding: 18px;
        }

        .contact-message-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
            color: #6c757d;
            font-size: 0.9rem;
            overflow-wrap: anywhere;
        }

        .contact-message-text {
            color: #42424a;
            line-height: 1.65;
            overflow-wrap: anywhere;
        }

        .contact-message-footer {
            padding: 14px 18px;
            border-top: 1px solid #f2e5ed;
            color: #777;
            font-size: 0.86rem;
            background: #fffbfd;
        }

        .alert {
            max-width: 100%;
            margin-bottom: 24px;
            border-radius: 12px;
            text-align: center;
        }

        html[data-theme="dark"] .contact-message-card {
            border-color: #34343d;
            background: #1f1f26;
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.34);
        }

        html[data-theme="dark"] .contact-message-card-header,
        html[data-theme="dark"] .contact-message-footer {
            border-color: #34343d;
            background: #282832;
        }

        html[data-theme="dark"] .contact-message-avatar {
            background: #3a2834;
            color: #f178b6;
        }

        html[data-theme="dark"] .contact-message-subject,
        html[data-theme="dark"] .contact-message-text {
            color: #f5f5f7;
        }

        html[data-theme="dark"] .contact-message-meta,
        html[data-theme="dark"] .contact-message-footer {
            color: #b8bcc6;
        }

        @media (max-width: 768px) {
            .contact-message-header {
                top: auto;
            }

            .contact-message-stats,
            .contact-message-grid {
                grid-template-columns: 1fr;
            }
        }

        html[data-theme="dark"] .contact-message-header {
            background: #111113;
        }
    </style>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>

    <div class="admin-page-shell contact-message-shell">
        <section class="contact-message-header">
            <div>
                <h2 class="admin-page-title">Contact Messages</h2>
                <p class="admin-page-subtitle">Review customer questions, subjects, and contact details from the storefront form.</p>
            </div>

            <div class="admin-summary-grid contact-message-stats">
                <div class="admin-stat-card">
                    <span class="admin-stat-label">Messages</span>
                    <span class="admin-stat-value"><?php echo $totalMessages; ?></span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">Access Mode</span>
                    <span class="admin-stat-value"><?php echo $isAdmin ? 'Admin' : 'Preview'; ?></span>
                </div>
            </div>
        </section>

            <?php if (!$isAdmin): ?>
                <div class="alert alert-warning admin-preview-alert">
                    <i class="fa fa-lock"></i>
                    Customer contact details are hidden in portfolio preview mode.
                </div>
            <?php endif; ?>

            <div class="contact-message-grid">
                <?php foreach ($messagesList as $message): ?>
                    <?php
                    $displayName = $isAdmin
                        ? htmlspecialchars($message['name'])
                        : htmlspecialchars(maskText($message['name']));
                    $avatarLetter = htmlspecialchars(mb_substr($displayName, 0, 1));
                    ?>
                    <article class="contact-message-card">
                        <div class="contact-message-card-header">
                            <span class="contact-message-avatar"><?php echo $avatarLetter; ?></span>

                            <div class="contact-message-heading">
                                <h5 class="contact-message-name"><?php echo $displayName; ?></h5>
                                <p class="contact-message-subject">
                                <?php
                                echo $isAdmin
                                    ? htmlspecialchars($message['subject'])
                                    : htmlspecialchars(maskText($message['subject']));
                                ?>
                                </p>
                            </div>
                        </div>

                        <div class="contact-message-body">
                            <p class="contact-message-meta">
                                <i class="fa fa-envelope"></i>
                                <?php
                                echo $isAdmin
                                    ? htmlspecialchars($message['email'])
                                    : htmlspecialchars(maskEmail($message['email']));
                                ?>
                            </p>

                            <p class="contact-message-text mb-0">
                                <?php
                                echo $isAdmin
                                    ? nl2br(htmlspecialchars($message['message']))
                                    : htmlspecialchars(maskMessage($message['message']));
                                ?>
                            </p>
                        </div>

                        <div class="contact-message-footer">
                            <i class="fa fa-inbox"></i>
                            <?php echo $isAdmin ? 'Customer message is visible to admins.' : 'Full message is locked in preview mode.'; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
    </div>
</body>

</html>
