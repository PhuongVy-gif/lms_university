<?php
// Always define global $pdo and $conn for all modules
global $pdo, $conn;
$DB_HOST = 'localhost';
$DB_NAME = 'lms_university';
$DB_USER = 'root';
$DB_PASS = '';

// PDO connection
try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('PDO connection failed: ' . $e->getMessage());
}

// mysqli connection
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die('MySQLi connection failed: ' . $conn->connect_error);
}

// Session configuration
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function formatDate($date) {
    return date('M j, Y g:i A', strtotime($date));
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>