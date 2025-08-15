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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignment_id']) && isset($_FILES['file'])) {
    $assignment_id = intval($_POST['assignment_id']);
    $file = $_FILES['file'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed = ['pdf','doc','docx','zip','rar'];
        if (in_array(strtolower($ext), $allowed)) {
            $new_name = 'assignment_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            $dest = '../uploads/' . $new_name;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                // Remove old file if exists
                $sql = "SELECT file_path FROM assignment_submissions WHERE assignment_id=? AND student_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ii', $assignment_id, $user_id);
                $stmt->execute();
                $stmt->bind_result($old_file);
                $stmt->fetch();
                $stmt->close();
                if ($old_file && file_exists(__DIR__ . '/../uploads/' . $old_file)) {
                    @unlink(__DIR__ . '/../uploads/' . $old_file);
                }
                // Update file_path in DB
                $sql = "UPDATE assignment_submissions SET file_path=?, submitted_at=NOW() WHERE assignment_id=? AND student_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('sii', $new_name, $assignment_id, $user_id);
                $stmt->execute();
                $stmt->close();
                $conn->close();
                header('Location: assignment_student.php?upload=success');
                exit();
            }
        }
    }
    header('Location: assignment_student.php?upload=fail');
    exit();
}
header('Location: assignment_student.php');
exit();
