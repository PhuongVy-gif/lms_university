<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    header('Location: ../login.php');
    exit();
}
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    $conn = new mysqli('localhost', 'root', '', 'lms_university');
    if ($conn->connect_error) {
        die('Kết nối CSDL thất bại: ' . $conn->connect_error);
    }
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id=? AND role='student'");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
header('Location: manage_student.php');
exit();
