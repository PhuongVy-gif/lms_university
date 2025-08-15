
<?php
session_start();
// Kết nối cơ sở dữ liệu
require_once __DIR__ . '/../../config/database.php';
if (!isset($conn)) {
    // Nếu file database.php không tạo $conn thì tạo mới ở đây
    $conn = new mysqli('localhost', 'root', '', 'lms_university');
    if ($conn->connect_error) {
        die('Kết nối CSDL thất bại: ' . $conn->connect_error);
    }
}
// Kiểm tra nếu không phải admin thì chuyển hướng
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Lấy danh sách instructor để map instructor_id sang tên
$instructors = [];
$q_instructors = mysqli_query($conn, "SELECT id, username FROM users WHERE role='instructor'");
if ($q_instructors) {
  while ($t = mysqli_fetch_assoc($q_instructors)) {
    $instructors[$t['id']] = $t['username'];
  }
}
// Lấy danh sách khóa học
$result = mysqli_query($conn, "SELECT * FROM courses");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../header_admin.php'; ?>
<div class="container-fluid">
  <div class="row">
    <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">Manage Courses</h1>
      </div>
      <div class="mb-4">
        <h2 class="fw-bold mb-3">Course List</h2>
        <a href="add_courses.php" class="btn btn-success mb-3">Add Course</a>
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th style="width:50px">No.</th>
                <th style="width:80px">ID</th>
                <th style="width:180px">Course Name</th>
                <th>Description</th>
                <th style="width:110px">Image</th>
                <th style="width:80px">Code</th>
                <th style="width:80px">Credits</th>
                <th style="width:100px">Semester</th>
                <th style="width:80px">Year</th>
                <th style="width:120px">Instructor</th>
                <th style="width:90px">Edit</th>
                <th style="width:90px">Delete</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $i=1;
              if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) { ?>
              <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td>
                  <?php if (!empty($row['images'])): ?>
                    <img src="../assets/images/<?php echo htmlspecialchars($row['images']); ?>" alt="course image" style="width:60px; height:40px; object-fit:cover; border-radius:6px;">
                  <?php else: ?>
                    <span class="text-muted">None</span>
                  <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['course_code']); ?></td>
                <td><?php echo htmlspecialchars($row['credits']); ?></td>
                <td><?php echo htmlspecialchars($row['semester']); ?></td>
                <td><?php echo htmlspecialchars($row['year']); ?></td>
                <td><?php echo isset($instructors[$row['instructor_id']]) ? htmlspecialchars($instructors[$row['instructor_id']]) : '<span class="text-muted">Unassigned</span>'; ?></td>
                <td><a href="edit_courses.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a></td>
                <td><a href="delete_courses.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this course?');">Delete</a></td>
              </tr>
              <?php }} else { ?>
                <tr><td colspan="7" class="text-center">No data available</td></tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
