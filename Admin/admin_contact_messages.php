<?php
require_once "../db_connect.php";

// Fetch all contact messages
$sql = "SELECT * FROM contactMessages";
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
        .main-contact-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .message-card {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .message-body {
            padding: 20px;
        }

        .message-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .message-text {
            font-size: 16px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <?php include 'sidebar_nav.php'; ?>

    <div class="col-md-9">
        <!-- Main Content for Contact Messages -->
        <div class="main-contact-container mt-4 align-items-center">
            <h2 class="mb-4 text-center">Contact Messages</h2>
            <?php foreach ($messagesList as $message): ?>
                <div class="message-card align-items-center">
                    <div class="message-body">
                        <h5 class="message-title"><?php echo htmlspecialchars($message['name']); ?></h5>
                        <p class="message-text"><strong>Email:</strong> <?php echo htmlspecialchars($message['email']); ?></p>
                        <p class="message-text"><strong>Subject:</strong> <?php echo htmlspecialchars($message['subject']); ?></p>
                        <p class="message-text"><?php echo htmlspecialchars($message['message']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>