<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    header('Location: ../login.php');
    exit();
}
$conn = new mysqli('localhost', 'root', '', 'lms_university');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
$instructor_id = $_SESSION['user_id'];
$error = '';
$success = false;
if (isset($_POST['add_course'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $images = mysqli_real_escape_string($conn, $_POST['images']);
    $course_code = mysqli_real_escape_string($conn, $_POST['course_code']);
    $credits = intval($_POST['credits']);
    $semester = mysqli_real_escape_string($conn, $_POST['semester']);
    $year = intval($_POST['year']);
    $max_students = intval($_POST['max_students']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $sql = "INSERT INTO courses (title, description, images, instructor_id, course_code, credits, semester, year, max_students, status) VALUES ('$title', '$description', '$images', $instructor_id, '$course_code', $credits, '$semester', $year, $max_students, '$status')";
    if (mysqli_query($conn, $sql)) {
        $success = true;
    } else {
        $error = 'Failed to add course! The course code may already exist or the data is invalid.';
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
</head>
<body>
<?php include '../header_instructor.php'; ?>
<div class="container mt-5">
    <h2 class="mb-4 fw-bold text-center">Add New Course</h2>
    <?php if ($success) { ?>
        <div class="alert alert-success">Course added successfully!</div>
    <?php } elseif ($error) { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } ?>
    <form method="post" action="" class="bg-white p-4 rounded-3 shadow-sm mx-auto" style="max-width:600px;">
        <div class="mb-3">
            <label for="title" class="form-label fw-semibold">Course Name</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label fw-semibold">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label for="images" class="form-label fw-semibold">Image Path</label>
            <input type="text" class="form-control" id="images" name="images" placeholder="assets/images/html.jpg" required>
        </div>
        <div class="mb-3">
            <label for="course_code" class="form-label fw-semibold">Course Code</label>
            <input type="text" class="form-control" id="course_code" name="course_code" required>
        </div>
        <div class="mb-3">
            <label for="credits" class="form-label fw-semibold">Credits</label>
            <input type="number" class="form-control" id="credits" name="credits" min="1" max="10" value="3" required>
        </div>
        <div class="mb-3">
            <label for="semester" class="form-label fw-semibold">Semester</label>
            <input type="text" class="form-control" id="semester" name="semester" placeholder="Spring/Fall" required>
        </div>
        <div class="mb-3">
            <label for="year" class="form-label fw-semibold">Year</label>
            <input type="number" class="form-control" id="year" name="year" min="2000" max="2100" value="<?php echo date('Y'); ?>" required>
        </div>
        <div class="mb-3">
            <label for="max_students" class="form-label fw-semibold">Max Students</label>
            <input type="number" class="form-control" id="max_students" name="max_students" min="1" max="500" value="50" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label fw-semibold">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <button type="submit" name="add_course" class="btn btn-success px-4 fw-semibold">Add Course</button>
        <a href="my-courses.php" class="btn btn-secondary px-4 fw-semibold">Back</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
