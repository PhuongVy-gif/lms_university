<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
global $pdo;
$db = $pdo;
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
// Lấy danh sách giảng viên từ bảng users (role = 'instructor')
$sql = "SELECT id, username, email, first_name, last_name, profile_image FROM users WHERE role = 'instructor'";
$stmt = $db->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Instructors</title>
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
        <h1 class="h3 mb-0 fw-bold">Manage Instructors</h1>
      </div>
      <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1) { ?>
        <div class="alert alert-success">Instructor deleted successfully!</div>
      <?php } ?>
      <div class="mb-4">
        <h2 class="fw-bold mb-3">Instructor List</h2>
        <a href="add_instructor.php" class="btn btn-success mb-3">Add Instructor</a>
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th style="width:50px">No.</th>
                <th style="width:80px">ID</th>
                <th style="width:160px">Username</th>
                <th style="width:180px">Email</th>
                <th style="width:120px">Full Name</th>
                <th style="width:110px">Profile Image</th>
                <th style="width:90px">Edit</th>
                <th style="width:90px">Delete</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $i=1;
              if ($result && count($result) > 0) {
                foreach ($result as $row) { ?>
              <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo isset($row['username']) ? htmlspecialchars($row['username']) : ''; ?></td>
                <td><?php echo isset($row['email']) ? htmlspecialchars($row['email']) : ''; ?></td>
                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                <td>
                  <?php if (!empty($row['profile_image'])): ?>
                    <img src="../assets/images/<?php echo htmlspecialchars($row['profile_image']); ?>" alt="avatar" style="width:40px; height:40px; object-fit:cover; border-radius:50%;">
                  <?php else: ?>
                    <span class="text-muted">None</span>
                  <?php endif; ?>
                </td>
                <td><a href="edit_instructor.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a></td>
                <td><a href="delete_instructor.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this instructor?');">Delete</a></td>
              </tr>
              <?php }} else { ?>
                <tr><td colspan="9" class="text-center">No data available</td></tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Form sửa giảng viên sẽ chuyển sang edit_instructor.php -->
    </main>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
