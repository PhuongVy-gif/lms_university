<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_POST['assignment_id'])) {
    header('Location: assignment_student.php');
    exit();
}
require_once(__DIR__ . '/../config/database.php');
if (!isset($conn)) {
    $conn = new mysqli('localhost', 'root', '', 'lms_university');
    if ($conn->connect_error) {
        die('Kết nối CSDL thất bại: ' . $conn->connect_error);
    }
}
$user_id = $_SESSION['user_id'];
$assignment_id = intval($_POST['assignment_id']);
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $file_name = basename($_FILES['file']['name']);
    $target_dir = __DIR__ . '/../uploads/';
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    $target_file = $target_dir . time() . '_' . $file_name;
    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
        $db_file = basename($target_file);
        // Record submission
        $stmt = $conn->prepare("REPLACE INTO assignment_submissions (assignment_id, student_id, file_path, submitted_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param('iis', $assignment_id, $user_id, $db_file);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        header('Location: assignment_student.php?upload=success');
        exit();
    }
}
$conn->close();
header('Location: assignment_student.php?upload=fail');
exit();
