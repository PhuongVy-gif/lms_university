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
if ($id <= 0) {
    echo '<div class="alert alert-danger">ID không hợp lệ.</div>';
    exit();
}
// Lấy thông tin khóa học
$sql = "SELECT * FROM courses WHERE id = $id";
$result = $conn->query($sql);
$course = $result ? $result->fetch_assoc() : null;
if (!$course) {
    echo '<div class="alert alert-danger">Không tìm thấy khóa học.</div>';
    exit();
}
// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $images = $conn->real_escape_string($_POST['images']);
    $course_code = $conn->real_escape_string($_POST['course_code']);
    $credits = intval($_POST['credits']);
    $semester = $conn->real_escape_string($_POST['semester']);
    $year = intval($_POST['year']);
    $max_students = intval($_POST['max_students']);
    $status = $conn->real_escape_string($_POST['status']);
    $sql = "UPDATE courses SET title='$title', description='$description', images='$images', course_code='$course_code', credits=$credits, semester='$semester', year=$year, max_students=$max_students, status='$status' WHERE id=$id";
    if ($conn->query($sql)) {
        header('Location: my-courses.php');
        exit();
    } else {
        echo '<div class="alert alert-danger">Cập nhật thất bại: ' . $conn->error . '</div>';
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../../instructor/header_instructor.php'; ?>
<div style="margin-left:220px">
<div class="container mt-5">
    <h2>Edit Course</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($course['title']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" required><?php echo htmlspecialchars($course['description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Image URL</label>
            <input type="text" name="images" class="form-control" value="<?php echo htmlspecialchars($course['images']); ?>" required>
            <small class="text-muted">Nhập đường dẫn hình ảnh (ví dụ: assets/images/html.jpg)</small>
        </div>
        <div class="mb-3">
            <label class="form-label">Course Code</label>
            <input type="text" name="course_code" class="form-control" value="<?php echo htmlspecialchars($course['course_code']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Credits</label>
            <input type="number" name="credits" class="form-control" value="<?php echo (int)$course['credits']; ?>" min="1" max="10" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Semester</label>
            <input type="text" name="semester" class="form-control" value="<?php echo htmlspecialchars($course['semester']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Year</label>
            <input type="number" name="year" class="form-control" value="<?php echo htmlspecialchars($course['year']); ?>" min="2000" max="2100">
        </div>
        <div class="mb-3">
            <label class="form-label">Max Students</label>
            <input type="number" name="max_students" class="form-control" value="<?php echo (int)$course['max_students']; ?>" min="1" max="500">
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="active" <?php if($course['status']==='active') echo 'selected'; ?>>Active</option>
                <option value="inactive" <?php if($course['status']==='inactive') echo 'selected'; ?>>Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="my-courses.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</div>
</body>
</html>
