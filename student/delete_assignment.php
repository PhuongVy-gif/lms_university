<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login.php');
    exit();
}
require_once(__DIR__ . '/../config/database.php');
if (!isset($conn)) {
    $conn = new mysqli('localhost', 'root', '', 'lms_university');
    if ($conn->connect_error) {
        die('Database connection failed: ' . $conn->connect_error);
    }
}
$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignment_id'])) {
    $assignment_id = intval($_POST['assignment_id']);
    // Get file name to delete
    $sql = "SELECT file_path FROM assignment_submissions WHERE assignment_id=? AND student_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $assignment_id, $user_id);
    $stmt->execute();
    $stmt->bind_result($file_path);
    $stmt->fetch();
    $stmt->close();
    if ($file_path && file_exists(__DIR__ . '/../uploads/' . $file_path)) {
        @unlink(__DIR__ . '/../uploads/' . $file_path);
    }
    // Remove file_path from DB
    $sql = "UPDATE assignment_submissions SET file_path=NULL, submitted_at=NULL WHERE assignment_id=? AND student_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $assignment_id, $user_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header('Location: assignment_student.php?upload=success');
    exit();
}
header('Location: assignment_student.php');
exit();
