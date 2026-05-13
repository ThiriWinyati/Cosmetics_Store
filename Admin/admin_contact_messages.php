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
        .contact-page-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
            padding: 30px 20px;
        }

        .main-contact-container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
        }

        .main-contact-container h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .message-card {
            width: 100%;
            margin-bottom: 24px;
            border: 1px solid #343442;
            border-radius: 16px;
            background: #1f1f27;
            color: #f5f5f5;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        .message-body {
            padding: 24px;
        }

        .message-body h5 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 14px;
            color: #ffffff;
        }

        .message-body p {
            font-size: 1rem;
            margin-bottom: 12px;
            line-height: 1.6;
            color: #e8e8e8;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .message-body strong {
            color: #ffffff;
        }

        .alert {
            max-width: 100%;
            margin-bottom: 24px;
            border-radius: 12px;
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .contact-page-wrapper {
                padding: 20px 12px;
            }

            .main-contact-container {
                max-width: 100%;
            }

            .message-body {
                padding: 18px;
            }

            .message-body h5 {
                font-size: 1.05rem;
            }

            .message-body p {
                font-size: 0.95rem;
            }
        }

        @media (max-width: 576px) {
            .contact-page-wrapper {
                padding: 15px 10px;
            }

            .message-card {
                border-radius: 12px;
            }

            .message-body {
                padding: 16px;
            }

            .message-body h5 {
                font-size: 1rem;
            }

            .message-body p {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>

    <div class="contact-page-wrapper">
        <div class="main-contact-container mt-4">
            <h2 class="mb-4 text-center">Contact Messages</h2>

            <?php if (!$isAdmin): ?>
                <div class="alert alert-warning text-center">
                    <i class="fa fa-lock"></i>
                    Customer contact details are hidden in portfolio preview mode.
                </div>
            <?php endif; ?>

            <?php foreach ($messagesList as $message): ?>
                <div class="message-card">
                    <div class="message-body">
                        <h5>
                            <?php
                            echo $isAdmin
                                ? htmlspecialchars($message['name'])
                                : htmlspecialchars(maskText($message['name']));
                            ?>
                        </h5>

                        <p>
                            <strong>Email:</strong>
                            <?php
                            echo $isAdmin
                                ? htmlspecialchars($message['email'])
                                : htmlspecialchars(maskEmail($message['email']));
                            ?>
                        </p>

                        <p>
                            <strong>Subject:</strong>
                            <?php
                            echo $isAdmin
                                ? htmlspecialchars($message['subject'])
                                : htmlspecialchars(maskText($message['subject']));
                            ?>
                        </p>

                        <p>
                            <?php
                            echo $isAdmin
                                ? nl2br(htmlspecialchars($message['message']))
                                : htmlspecialchars(maskMessage($message['message']));
                            ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>