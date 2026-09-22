<?php
// config.php
// Database connection and session setup — edit DB credentials below.

session_start();

$DB_HOST = 'localhost';
$DB_NAME = 'mess_management';
$DB_USER = 'root';      // <-- change if necessary
$DB_PASS = '';          // <-- change if necessary

try {
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    // Friendly error for development. In production, log it instead.
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}

// Helper: ensure warden logged in
function ensure_logged_in() {
    if (!isset($_SESSION['warden_id'])) {
        header('Location: login.php');
        exit;
    }
}
?>
