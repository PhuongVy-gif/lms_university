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
$course_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($course_id > 0) {
    $sql = "DELETE FROM courses WHERE id = $course_id";
    if (mysqli_query($conn, $sql)) {
        header('Location: manage-courses.php?deleted=1');
        exit();
    } else {
  $error = 'Failed to delete course!';
    }
} else {
    header('Location: manage-courses.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Delete Course</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../header_admin.php'; ?>
<div class="container-fluid">
  <div class="row">
    <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
      <h1 class="fw-bold mb-4 text-center" style="font-size:2.4rem">Delete Course</h1>
      <?php if (isset($error)) { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php } ?>
      <a href="manage-courses.php" class="btn btn-secondary">Back to list</a>
    </main>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
