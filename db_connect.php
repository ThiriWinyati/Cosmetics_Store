<?php
$server = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$database = getenv('DB_NAME') ?: 'cosmetics_store';
$port = getenv('DB_PORT') ?: 3306;

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
    die("Database connection error. Check server logs.");
}
?>