<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
if (!isset($conn)) {
    $conn = new mysqli('localhost', 'root', '', 'lms_university');
    if ($conn->connect_error) {
        die('Kết nối CSDL thất bại: ' . $conn->connect_error);
    }
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
$course_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($course_id <= 0) {
    header('Location: manage-courses.php');
    exit();
}
$sql = "SELECT * FROM courses WHERE id = $course_id LIMIT 1";
$result = mysqli_query($conn, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: manage-courses.php');
    exit();
}
$course = mysqli_fetch_assoc($result);
// Lấy danh sách giảng viên (role = 'instructor')
$instructors = [];
$q = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'specialty'");
$has_specialty = ($q && mysqli_num_rows($q) > 0);
if ($has_specialty) {
    $q_instructors = mysqli_query($conn, "SELECT id, username, email, specialty FROM users WHERE role='instructor'");
} else {
    $q_instructors = mysqli_query($conn, "SELECT id, username, email FROM users WHERE role='instructor'");
}
if ($q_instructors) {
    while ($t = mysqli_fetch_assoc($q_instructors)) {
        $instructors[] = $t;
    }
}
$success = false;
$error = '';
if (isset($_POST['edit_course'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $course_code = mysqli_real_escape_string($conn, $_POST['course_code']);
    $credits = intval($_POST['credits']);
    $semester = mysqli_real_escape_string($conn, $_POST['semester']);
    $year = intval($_POST['year']);
    $max_students = intval($_POST['max_students']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $instructor_id = intval($_POST['instructor_id']);
    $image_sql = '';
    if (isset($_FILES['images']) && $_FILES['images']['error'] == UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['images']['name'], PATHINFO_EXTENSION);
        $image_name = uniqid('course_', true) . '.' . $ext;
        move_uploaded_file($_FILES['images']['tmp_name'], __DIR__ . '/../../assets/images/' . $image_name);
        $image_sql = ", images='$image_name'";
    }
    $sql_update = "UPDATE courses SET title='$title', description='$description', course_code='$course_code', credits=$credits, semester='$semester', year=$year, max_students=$max_students, status='$status', instructor_id=$instructor_id$image_sql WHERE id=$course_id";
    if (mysqli_query($conn, $sql_update)) {
        $success = true;
        $result = mysqli_query($conn, $sql);
        $course = mysqli_fetch_assoc($result);
    } else {
        $error = 'Cập nhật khóa học thất bại!';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa khóa học</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../header_admin.php'; ?>
<div class="container-fluid">
  <div class="row">
    <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
   
        <h1 class="fw-bold mb-4 text-center" style="font-size:2.4rem">Edit Course</h1>
        <?php if ($success) { ?>
          <div class="alert alert-success">Update successful! <a href='manage-courses.php'>Back to list</a></div>
        <?php } elseif ($error) { ?>
          <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>
      <div class="row justify-content-center">
        <div class="col-12 col-md-12 col-lg-10">
          <form method="post" action="" enctype="multipart/form-data" autocomplete="off" class="add-course-form bg-white p-4 rounded-3 shadow-sm mx-auto mt-4" style="max-width: 1000px;">
            <div class="mb-3">
                <label for="title" class="form-label fw-semibold">Course Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="title" name="title" placeholder="Tên khóa học" value="<?php echo htmlspecialchars($course['title']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label fw-semibold">Description</label>
              <textarea class="form-control" id="description" name="description" placeholder="Mô tả khóa học" rows="3" required><?php echo htmlspecialchars($course['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="course_code" class="form-label fw-semibold">Course Code <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="course_code" name="course_code" placeholder="Mã khóa học" value="<?php echo htmlspecialchars($course['course_code']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="credits" class="form-label fw-semibold">Credits</label>
              <input type="number" class="form-control" id="credits" name="credits" min="1" max="10" value="<?php echo htmlspecialchars($course['credits']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="semester" class="form-label fw-semibold">Semester</label>
              <input type="text" class="form-control" id="semester" name="semester" placeholder="Học kỳ" value="<?php echo htmlspecialchars($course['semester']); ?>">
            </div>
            <div class="mb-3">
                <label for="year" class="form-label fw-semibold">Year</label>
              <input type="number" class="form-control" id="year" name="year" min="2000" max="2100" value="<?php echo htmlspecialchars($course['year']); ?>">
            </div>
            <div class="mb-3">
                <label for="max_students" class="form-label fw-semibold">Max Students</label>
              <input type="number" class="form-control" id="max_students" name="max_students" min="1" max="500" value="<?php echo htmlspecialchars($course['max_students']); ?>">
            </div>
            <div class="mb-3">
                <label for="status" class="form-label fw-semibold">Status</label>
              <select class="form-select" id="status" name="status">
                  <option value="active" <?php if ($course['status'] == 'active') echo 'selected'; ?>>Active</option>
                  <option value="inactive" <?php if ($course['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
              </select>
            </div>
            <div class="mb-3">
                <label for="instructor_id" class="form-label fw-semibold">Instructor</label>
              <select class="form-select" id="instructor_id" name="instructor_id" required>
                  <option value="">-- Select instructor --</option>
                <?php foreach ($instructors as $t) { ?>
                  <option value="<?php echo $t['id']; ?>" <?php if ($course['instructor_id'] == $t['id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($t['username'] . ' (' . $t['email'] . ')'); ?>
                    <?php if (isset($t['specialty'])) echo ' - ' . htmlspecialchars($t['specialty']); ?>
                  </option>
                <?php } ?>
              </select>
            </div>
            <div class="mb-3">
                <label for="images" class="form-label fw-semibold">Course Image</label>
              <?php if (!empty($course['images'])): ?>
                <div class="mb-2">
                  <img src="../assets/images/<?php echo htmlspecialchars($course['images']); ?>" alt="course image" style="width:80px; height:60px; object-fit:cover; border-radius:6px;">
                </div>
              <?php endif; ?>
              <input type="file" class="form-control" id="images" name="images" accept="image/*">
                <small class="text-muted">Leave blank if you do not want to change the image</small>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" name="edit_course" class="btn btn-warning px-4 fw-semibold">Update</button>
                <a href="manage-courses.php" class="btn btn-secondary px-4 fw-semibold">Back</a>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
