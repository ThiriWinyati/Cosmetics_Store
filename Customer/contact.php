<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../db_connect.php";

$showModal = false;

// Handle form submission for email
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    $customerId = $_SESSION['customer_id'] ?? null;

    try {
        $sql = "INSERT INTO contactmessages (name, email, subject, message, customer_id) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $email, $subject, $message, $customerId]);

        $showModal = true;
    } catch (PDOException $e) {
        echo "Contact form error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../Customer/customer_css/style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="icon" href="path/to/favicon.ico">
    <title>Contact Us - Cosmetics Shop</title>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5 cosmetics-contact-section">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="user_homeIndex.php" style="color: black; text-decoration:none;">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact</li>
            </ol>
        </nav>
        <div class="row">

            <div class="col-md-6 cosmetics-contact-form-container">
                <h1>Contact Us</h1>
                <!-- Add the message here -->
                <p> You have two choices for contacting our store: Leave an Email or Live Chat.
                </p>

                <div class="contact-options mb-4">
                    <button class="btn btn-primary" onclick="showEmailForm()">Leave an Email</button>
                    <button class="btn btn-secondary" onclick="showChat()">Live Chat</button>
                </div>

                <div id="email-form" class="contact-form">
                    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
                    <dotlottie-player src="https://lottie.host/26981103-abf8-4c74-bbbc-d21dd6b48f39/wstWwALLx3.lottie" background="transparent" speed="1" style="width: 200px; height: 200px; margin-left: 150px;" loop autoplay></dotlottie-player>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">


                        <label for="name">Name:</label><br>
                        <input type="text" id="name" name="name" required><br><br>

                        <label for="email">Email:</label><br>
                        <input type="email" id="email" name="email" required><br><br>

                        <label for="subject">Subject:</label><br>
                        <input type="text" id="subject" name="subject" required><br><br>

                        <label for="message">Message:</label><br>
                        <textarea id="message" name="message" rows="4" cols="50" required></textarea><br><br>

                        <input type="submit" value="Submit">
                    </form>
                </div>

                <div id="chat-box" class="chat-box">
                    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
                    <dotlottie-player src="https://lottie.host/6d1fc1d2-d7ad-408d-aea7-9a5ec8750064/BfFh09CGTI.lottie" background="transparent" speed="1" style="width: 300px; height: 300px; margin-left: 150px" loop autoplay></dotlottie-player>
                    <h5>Live Chat with our Team</h5>
                    <p>Please wait for a while. We'll reach out to you soon!</p>
                    <div id="chat-container" style="border: 1px solid #ccc; height: 300px; overflow-y: auto; padding: 10px; margin-bottom: 10px;"></div>
                    <input type="text" id="chat-input" placeholder="Type your message..." onkeypress="checkEnter(event)" class="form-control">
                    <button class="btn btn-primary mt-3" onclick="sendMessage()">Send</button>
                </div>
            </div>

            <!-- Right Column: Additional Information -->
            <div class="col-md-6 cosmetics-contact-info-container scrollable-right">
                <h2>Visit Us</h2>
                <p>We'd love to hear from you! Visit our store or reach out to us using the details below.</p>

                <div class="mt-4">
                    <h4>Store Location</h4>
                    <p><i class="fa fa-map-marker me-2"></i> Junction City Tower,
                        Corner of Bogyoke Aung San Road and 27th Street,
                        Pabedan Township,
                        Yangon, Myanmar
                    </p>
                    <p><i class="fa fa-clock-o me-2"></i> Mon-Sat: 9AM - 5PM</p>
                    <div class="map-container">
                        <iframe
                            title="Charm & Grace store location map"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d30546.318844178164!2d96.1079719347656!3d16.86155010005379!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30c1948e78bf147f%3A0x15e50b9976b35317!2z4YCZ4YCb4YCZ4YC64YC44YCA4YCv4YCU4YC64YC4IOGAmeGAvOGAreGAr-GAt-GAlOGAmuGAuiwg4YCb4YCU4YC64YCA4YCv4YCU4YC6!5e0!3m2!1smy!2smm!4v1736781138549!5m2!1smy!2smm"
                            width="600"
                            height="450"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>

                <div class="mt-4">
                    <h4>Contact Information</h4>
                    <p><i class="fa fa-phone me-2"></i> +959 123 456 789</p>
                    <p><i class="fa fa-envelope me-2"></i> charmandgrace@gmail.com</p>
                </div>

                <div class="mt-4">
                    <h4>Follow Us</h4>
                    <a href="#" class="text-dark me-3"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-dark me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-dark me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-dark"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Thank You Modal -->
    <div class="modal fade" id="thankYouModal" tabindex="-1" aria-labelledby="thankYouModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="thankYouModalLabel">Thank You!</h5>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    Thank you for contacting us. We will get back to you shortly.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        function showEmailForm() {
            document.getElementById('email-form').style.display = 'block';
            document.getElementById('chat-box').style.display = 'none';
        }

        function showChat() {
            document.getElementById('chat-box').style.display = 'block';
            document.getElementById('email-form').style.display = 'none';
        }

        function checkEnter(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        }

        window.onload = function() {
            showEmailForm();
            <?php if (isset($showModal) && $showModal) { ?>
                var thankYouModal = new bootstrap.Modal(document.getElementById('thankYouModal'));
                thankYouModal.show();
            <?php } ?>
        }

        function sendMessage() {
            const input = document.getElementById('chat-input');
            const message = input.value.trim();
            if (message) {
                fetch('send_message.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            message
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        input.value = '';
                        loadMessages();
                        var thankYouModal = new bootstrap.Modal(document.getElementById('thankYouModal'));
                        thankYouModal.show();
                    });
            }
        }

        function loadMessages() {
            fetch('load_messages.php')
                .then(response => response.json())
                .then(messages => {
                    const chatContainer = document.getElementById('chat-container');
                    chatContainer.innerHTML = '';
                    let lastDate = '';
                    messages.forEach(message => {
                        const messageDate = message.formatted_date;
                        if (messageDate !== lastDate) {
                            const dateDiv = document.createElement('div');
                            dateDiv.className = 'date-divider';
                            dateDiv.textContent = messageDate;
                            chatContainer.appendChild(dateDiv);
                            lastDate = messageDate;
                        }

                        const messageWrapper = document.createElement('div');
                        messageWrapper.className = message.sender_type === 'admin' ? 'message-wrapper admin' : 'message-wrapper customer';

                        const senderDiv = document.createElement('div');
                        senderDiv.className = 'sender';
                        senderDiv.textContent = message.display_name;

                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'message';
                        messageDiv.textContent = message.message;

                        const timeDiv = document.createElement('div');
                        timeDiv.className = 'time';
                        timeDiv.textContent = message.formatted_time;

                        messageWrapper.appendChild(senderDiv);
                        messageWrapper.appendChild(messageDiv);
                        messageWrapper.appendChild(timeDiv);

                        chatContainer.appendChild(messageWrapper);
                    });
                });
        }

        // Call loadMessages initially and at intervals
        loadMessages();
        setInterval(loadMessages, 5000);
    </script>
</body>

</html>
