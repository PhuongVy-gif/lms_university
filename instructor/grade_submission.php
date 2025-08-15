<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
	http_response_code(403);
	exit('Unauthorized');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$submission_id = isset($_POST['submission_id']) ? intval($_POST['submission_id']) : 0;
	$grade = isset($_POST['grade']) ? floatval($_POST['grade']) : null;
	$feedback = isset($_POST['feedback']) ? trim($_POST['feedback']) : '';
	$grader_id = $_SESSION['user_id'];
	$graded_at = date('Y-m-d H:i:s');
	$conn = new mysqli('localhost', 'root', '', 'lms_university');
	if ($conn->connect_error) {
		http_response_code(500);
		exit('DB error');
	}
	$stmt = $conn->prepare("UPDATE assignment_submissions SET grade=?, feedback=?, graded_by=?, graded_at=? WHERE id=?");
	if ($stmt) {
		$stmt->bind_param('dsisi', $grade, $feedback, $grader_id, $graded_at, $submission_id);
		$stmt->execute();
		$stmt->close();
		$conn->close();
		http_response_code(200);
		exit('OK');
	} else {
		$conn->close();
		http_response_code(500);
		exit('Query error');
	}
}
http_response_code(400);
exit('Invalid request');
