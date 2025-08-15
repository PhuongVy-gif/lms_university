<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
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
// Get instructor's courses
$courses = [];
$q = $conn->query("SELECT id, title FROM courses WHERE instructor_id = $instructor_id");
if ($q) {
    while ($row = $q->fetch_assoc()) {
        $courses[] = $row;
    }
}
if (isset($_POST['add_assignment'])) {
    $course_id = intval($_POST['course_id']);
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $due_date = $conn->real_escape_string($_POST['due_date']);
    $max_points = isset($_POST['max_points']) ? floatval($_POST['max_points']) : 100.00;
    $sql = "INSERT INTO assignments (course_id, title, description, due_date, max_points, created_by) VALUES ($course_id, '$title', '$description', '$due_date', $max_points, $instructor_id)";
    if ($conn->query($sql)) {
        $success = true;
    } else {
    $error = 'Failed to add assignment!';
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Assignment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<?php include '../header_instructor.php'; ?>
<div style="margin-left:220px">
<div class="container mt-5">
    <h2 class="mb-4 fw-bold text-center">Add New Assignment</h2>
    <?php if ($success) { ?>
        <div class="alert alert-success">Assignment added successfully!</div>
    <?php } elseif ($error) { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } ?>
    <form method="post" action="" class="bg-white p-4 rounded-3 shadow-sm mx-auto" style="max-width:600px;">
        <div class="mb-3">
            <label for="course_id" class="form-label fw-semibold">Course</label>
            <select class="form-select" id="course_id" name="course_id" required>
                <option value="">-- Select course --</option>
                <?php foreach ($courses as $c) { ?>
                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['title']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="title" class="form-label fw-semibold">Assignment Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label fw-semibold">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label for="due_date" class="form-label fw-semibold">Due Date</label>
            <input type="datetime-local" class="form-control" id="due_date" name="due_date" required>
        </div>
        <div class="mb-3">
            <label for="max_points" class="form-label fw-semibold">Max Points</label>
            <input type="number" class="form-control" id="max_points" name="max_points" min="1" max="1000" step="0.01" value="100" required>
        </div>
        <button type="submit" name="add_assignment" class="btn btn-success px-4 fw-semibold">Add Assignment</button>
        <a href="assignments.php" class="btn btn-secondary px-4 fw-semibold">Back</a>
    </form>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
