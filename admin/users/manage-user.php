
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
// Xóa user
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM users WHERE id = $id");
    header('Location: manage-user.php?deleted=1');
    exit();
}
// Lấy danh sách user
$result = mysqli_query($conn, "SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Users</title>
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
        <h1 class="h3 mb-0 fw-bold">Manage Users</h1>
      </div>
      <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1) { ?>
        <div class="alert alert-success">User deleted successfully!</div>
      <?php } ?>
      <div class="mb-4">
        <h2 class="fw-bold mb-3">User List</h2>
        <a href="add_user.php" class="btn btn-success mb-3">Add User</a>
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th style="width:50px">No.</th>
                <th style="width:80px">ID</th>
                <th style="width:160px">Username</th>
                <th style="width:180px">Email</th>
                <th style="width:120px">Role</th>
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
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['role']); ?></td>
                <td><a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a></td>
                <td><a href="manage-user.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a></td>
              </tr>
              <?php }} else { ?>
                <tr><td colspan="7" class="text-center">No data available</td></tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Form thêm/sửa chuyển sang add_user.php, edit_user.php -->
    </main>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
