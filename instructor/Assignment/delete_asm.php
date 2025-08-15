<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    header('Location: ../login.php');
    exit();
}
if ($conn->connect_error) {
    die('Kết nối CSDL thất bại: ' . $conn->connect_error);
}
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    $sql = "DELETE FROM assignments WHERE assignment_id = $id";
    $conn->query($sql);
}
$conn->close();
header('Location: assignments.php');
exit();

$conn = new mysqli('localhost', 'root', '', 'lms_university');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    $sql = "DELETE FROM assignments WHERE id = $id";
    $conn->query($sql);
}
$conn->close();
header('Location: assignments.php');
exit();
