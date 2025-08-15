<?php
// manage-courses.php - Quản lý khóa học cho giáo viên, có nút Diễn đàn thảo luận
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: ../login.php');
    exit();
}
include '../config/database.php';
if (!isset($conn) || !$conn) {
    $conn = new mysqli('localhost', 'root', '', 'lms_university');
    if ($conn->connect_error) {
        die('Kết nối CSDL thất bại: ' . $conn->connect_error);
    }
}
$user_id = $_SESSION['user_id'];
// Lấy danh sách khóa học của giáo viên
$sql = "SELECT c.*, COUNT(r.id) as student_count FROM courses c LEFT JOIN course_registrations r ON c.course_id = r.course_id WHERE c.teacher_id = $user_id GROUP BY c.course_id ORDER BY c.course_id DESC";
$result = mysqli_query($conn, $sql);
$courses = [];
while ($row = mysqli_fetch_assoc($result)) {
    $courses[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khóa học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/13c47b287c.js" crossorigin="anonymous"></script>
</head>
<body style="background:#f6f8fa;">
<?php include 'header_instructor.php'; ?>
<div class="d-flex">
    <div class="container py-4">
    <h2 class="fw-bold mb-3">Quản lý khóa học của tôi</h2>
    <table class="table table-bordered bg-white shadow-sm">
        <thead class="table-light">
            <tr>
                <th>STT</th>
                <th>Tiêu đề</th>
                <th>Mô tả</th>
                <th>Số sinh viên</th>
                <th>Diễn đàn</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($courses)): ?>
                <tr><td colspan="5" class="text-center text-muted">Bạn chưa có khóa học nào.</td></tr>
            <?php else: $i=1; foreach ($courses as $c): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo htmlspecialchars($c['title']); ?></td>
                <td><?php echo htmlspecialchars($c['description']); ?></td>
                <td><?php echo $c['student_count']; ?></td>
                <td>
                    <a href="../user/course_forum.php?course_id=<?php echo $c['course_id']; ?>" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-comments"></i> Diễn đàn</a>
                </td>
            </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
