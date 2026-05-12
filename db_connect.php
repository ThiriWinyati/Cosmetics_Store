<?php
$server = getenv('DB_HOST');
$user = getenv('DB_USER');
$password = getenv('DB_PASS');
$database = getenv('DB_NAME');
$port = getenv('DB_PORT');

if (!$server || !$user || !$password || !$database || !$port) {
    die("Database environment variables are missing. Please check Render Environment settings.");
}

// PDO connection
try {
    $conn = new PDO(
        "mysql:host=$server;port=$port;dbname=$database;charset=utf8mb4",
        $user,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// MySQLi connection
$mysqli = new mysqli($server, $user, $password, $database, (int)$port);

if ($mysqli->connect_error) {
    die("Database connection error: " . $mysqli->connect_error);
}
?>