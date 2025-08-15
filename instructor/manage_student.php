<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    header('Location: ../login.php');
    exit();
}
$conn = new mysqli('localhost', 'root', '', 'lms_university');
if ($conn->connect_error) {
    die('Kết nối CSDL thất bại: ' . $conn->connect_error);
}
// Lấy danh sách sinh viên
$sql = "SELECT user_id, username, email, role FROM users WHERE role = 'student'";
$result = $conn->query($sql);
$students = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sinh viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/13c47b287c.js" crossorigin="anonymous"></script>
</head>

<body style="background:#f6f8fa;">
<?php include 'header_instructor.php'; ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-2 d-none d-md-block px-0">
      <?php include 'left_teacher.php'; ?>
    </div>
    <div class="col-md-10 ms-sm-auto px-4 py-4">
      <h2 class="fw-bold mb-2">Quản lý sinh viên</h2>
      <h4 class="fw-bold mb-4">Danh sách sinh viên</h4>
      <a href="add_student.php" class="btn btn-success mb-3"><i class="fa-solid fa-user-plus me-2"></i>Thêm sinh viên</a>
      <table class="table table-bordered bg-white shadow-sm">
          <thead class="table-light">
              <tr>
                  <th>STT</th>
                  <th>ID</th>
                  <th>Tên đăng nhập</th>
                  <th>Email</th>
                  <th>Sửa</th>
                  <th>Xóa</th>
              </tr>
          </thead>
          <tbody>
              <?php $i=1; foreach ($students as $student): ?>
              <tr>
                  <td><?php echo $i++; ?></td>
                  <td><?php echo $student['user_id']; ?></td>
                  <td><?php echo htmlspecialchars($student['username']); ?></td>
                  <td><?php echo htmlspecialchars($student['email']); ?></td>
                  <td><a href="edit_student.php?id=<?php echo $student['user_id']; ?>" class="btn btn-primary btn-sm">Sửa</a></td>
                  <td><a href="delete.php?id=<?php echo $student['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa sinh viên này?');">Xóa</a></td>
              </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
