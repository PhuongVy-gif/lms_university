<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    header('Location: ../login.php');
    exit();
}
$conn = new mysqli('localhost', 'root', '', 'lms_university');
if ($conn->connect_error) {
    die('Kết nối CSDL thất bại: ' . $conn->connect_error);
}
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    $sql = "DELETE FROM courses WHERE id = $id";
    $conn->query($sql);
}
$conn->close();
header('Location: my-courses.php');
exit();
