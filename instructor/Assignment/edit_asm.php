<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    header('Location: ../login.php');
    exit();
}
// Connect to database
$conn = new mysqli('localhost', 'root', '', 'lms_university');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo '<div class="alert alert-danger">Invalid ID.</div>';
    exit();
}
// Get assignment info
$sql = "SELECT * FROM assignments WHERE id = $id";
$result = $conn->query($sql);
$assignment = $result ? $result->fetch_assoc() : null;
if (!$assignment) {
    echo '<div class="alert alert-danger">Assignment not found.</div>';
    exit();
}
// Get list of courses
$instructor_id = $_SESSION['user_id'];
$courses = [];
$q = $conn->query("SELECT id, title FROM courses WHERE instructor_id = $instructor_id");
if ($q) {
    while ($row = $q->fetch_assoc()) {
        $courses[] = $row;
    }
}
// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = intval($_POST['course_id']);
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $due_date = $conn->real_escape_string($_POST['due_date']);
    $max_points = isset($_POST['max_points']) ? floatval($_POST['max_points']) : 100.00;
    $sql = "UPDATE assignments SET course_id=$course_id, title='$title', description='$description', due_date='$due_date', max_points=$max_points WHERE id=$id";
    if ($conn->query($sql)) {
        header('Location: assignments.php');
        exit();
    } else {
    echo '<div class="alert alert-danger">Update failed: ' . $conn->error . '</div>';
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Assignment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<?php include '../header_instructor.php'; ?>
<div style="margin-left:220px">
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo '<div class="alert alert-danger">Invalid ID.</div>';
    exit();
}
// Get assignment info
$sql = "SELECT * FROM assignments WHERE id = $id";
$result = $conn->query($sql);
$assignment = $result ? $result->fetch_assoc() : null;
if (!$assignment) {
    echo '<div class="alert alert-danger">Assignment not found.</div>';
    exit();
}
// Get list of courses
$instructor_id = $_SESSION['user_id'];
$courses = [];
$q = $conn->query("SELECT id, title FROM courses WHERE instructor_id = $instructor_id");
if ($q) {
    while ($row = $q->fetch_assoc()) {
        $courses[] = $row;
    }
}
// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = intval($_POST['course_id']);
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $due_date = $conn->real_escape_string($_POST['due_date']);
    $max_points = isset($_POST['max_points']) ? floatval($_POST['max_points']) : 100.00;
    $sql = "UPDATE assignments SET course_id=$course_id, title='$title', description='$description', due_date='$due_date', max_points=$max_points WHERE id=$id";
    if ($conn->query($sql)) {
        header('Location: assignments.php');
        exit();
    } else {
        echo '<div class="alert alert-danger">Update failed: ' . $conn->error . '</div>';
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Assignment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<?php include '../header_instructor.php'; ?>
<div style="margin-left:220px">
<div class="container mt-5">
    <h2 class="mb-4 fw-bold text-center">Edit Assignment</h2>
    <form method="post" class="bg-white p-4 rounded-3 shadow-sm mx-auto" style="max-width:600px;">
        <div class="mb-3">
            <label for="course_id" class="form-label fw-semibold">Course</label>
            <select class="form-select" id="course_id" name="course_id" required>
                <?php foreach ($courses as $c) { ?>
                    <option value="<?php echo $c['id']; ?>" <?php if($assignment['course_id']==$c['id']) echo 'selected'; ?>><?php echo htmlspecialchars($c['title']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="title" class="form-label fw-semibold">Assignment Title</label>
            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($assignment['title']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label fw-semibold">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($assignment['description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="due_date" class="form-label fw-semibold">Due Date</label>
            <input type="datetime-local" class="form-control" id="due_date" name="due_date" value="<?php echo date('Y-m-d\\TH:i', strtotime($assignment['due_date'])); ?>" required>
        </div>
        <div class="mb-3">
            <label for="max_points" class="form-label fw-semibold">Max Points</label>
            <input type="number" class="form-control" id="max_points" name="max_points" min="1" max="1000" step="0.01" value="<?php echo htmlspecialchars($assignment['max_points']); ?>" required>
        </div>
    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save</button>
    <a href="assignments.php" class="btn btn-secondary px-4 fw-semibold">Back</a>
    </form>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
