<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
if (!isset($conn)) {
    $conn = new mysqli('localhost', 'root', '', 'lms_university');
    if ($conn->connect_error) {
  die('Database connection failed: ' . $conn->connect_error);
    }
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../login.php');
    exit();
}
$success = false;
$error = '';
// Get instructor list
$instructor_query = mysqli_query($conn, "SELECT id, username, email FROM users WHERE role = 'instructor'");
$instructors = [];
if ($instructor_query) {
  while ($row = mysqli_fetch_assoc($instructor_query)) {
    $instructors[] = $row;
  }
}
if (isset($_POST['add_course'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $course_code = mysqli_real_escape_string($conn, $_POST['course_code']);
    $credits = intval($_POST['credits']);
    $semester = mysqli_real_escape_string($conn, $_POST['semester']);
    $year = intval($_POST['year']);
    $max_students = intval($_POST['max_students']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $instructor_id = intval($_POST['instructor_id']);
    $image_name = '';
  if (isset($_FILES['images']) && $_FILES['images']['error'] == UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['images']['name'], PATHINFO_EXTENSION);
    $image_name = uniqid('course_', true) . '.' . $ext;
    move_uploaded_file($_FILES['images']['tmp_name'], __DIR__ . '/../../assets/images/' . $image_name);
  }
  $sql = "INSERT INTO courses (title, description, instructor_id, course_code, credits, semester, year, max_students, status, images) VALUES ('$title', '$description', $instructor_id, '$course_code', $credits, '$semester', $year, $max_students, '$status', '$image_name')";
  if (mysqli_query($conn, $sql)) {
    $success = true;
  } else {
    $error = 'Failed to add course!';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Course</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../header_admin.php'; ?>
<div class="container-fluid">
  <div class="row">
    <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
      <h1 class="fw-bold mb-4 text-center" style="font-size:2.4rem">Add Course</h1>
      <?php if ($success) { ?>
        <div class="alert alert-success">Course added successfully! <a href='manage-courses.php'>Back to list</a></div>
      <?php } elseif ($error) { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php } ?>
      <div class="row justify-content-center">
        <div class="col-12 col-md-12 col-lg-10">
          <form method="post" action="" enctype="multipart/form-data" autocomplete="off" class="add-instructor-form bg-white p-4 rounded-3 shadow-sm mx-auto mt-4" style="max-width: 1000px;">
            <div class="mb-3">
              <label for="title" class="form-label fw-semibold">Course Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="title" name="title" placeholder="Course name" required>
            </div>
            <div class="mb-3">
              <label for="description" class="form-label fw-semibold">Description</label>
              <textarea class="form-control" id="description" name="description" placeholder="Course description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
              <label for="course_code" class="form-label fw-semibold">Course Code <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="course_code" name="course_code" placeholder="Course code" required>
            </div>
            <div class="mb-3">
              <label for="credits" class="form-label fw-semibold">Credits</label>
              <input type="number" class="form-control" id="credits" name="credits" value="3" min="1" required>
            </div>
            <div class="mb-3">
              <label for="semester" class="form-label fw-semibold">Semester</label>
              <input type="text" class="form-control" id="semester" name="semester" placeholder="Semester">
            </div>
            <div class="mb-3">
              <label for="year" class="form-label fw-semibold">Year</label>
              <input type="number" class="form-control" id="year" name="year" value="<?php echo date('Y'); ?>">
            </div>
            <div class="mb-3">
              <label for="max_students" class="form-label fw-semibold">Max Students</label>
              <input type="number" class="form-control" id="max_students" name="max_students" value="50" min="1">
            </div>
            <div class="mb-3">
              <label for="status" class="form-label fw-semibold">Status</label>
              <select class="form-select" id="status" name="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="instructor_id" class="form-label fw-semibold">Instructor</label>
              <select class="form-select" id="instructor_id" name="instructor_id" required>
                <option value="">-- Select instructor --</option>
                <?php foreach ($instructors as $t) { ?>
                  <option value="<?php echo $t['id']; ?>">
                    <?php echo htmlspecialchars($t['username'] . ' (' . $t['email'] . ')'); ?>
                  </option>
                <?php } ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="images" class="form-label fw-semibold">Course Image</label>
              <input type="file" class="form-control" id="images" name="images" accept="image/*">
            </div>
            <div class="d-flex gap-2">
              <button type="submit" name="add_course" class="btn btn-primary px-4 fw-semibold">Add</button>
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
