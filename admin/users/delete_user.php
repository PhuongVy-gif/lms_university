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
if (!isset($_GET['id'])) {
    header('Location: manage-user.php');
    exit();
}
$id = intval($_GET['id']);
$msg = '';
$msg = '';
if (isset($_POST['delete_user'])) {
    if (mysqli_query($conn, "DELETE FROM users WHERE id = $id")) {
        header('Location: manage-user.php?deleted=1');
        exit();
    } else {
  $msg = '<div class="alert alert-danger">Error: ' . mysqli_error($conn) . '</div>';
    }
}
// Lấy user theo id mới
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $id"));
if (!$user) {
    header('Location: manage-user.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Delete User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../header_admin.php'; ?>
<div class="container-fluid">
  <div class="row">
    <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
  <h1 class="fw-bold mb-4 text-center" style="font-size:2.4rem">Delete User</h1>
      <div class="row justify-content-center">
        <div class="col-12 col-md-12 col-lg-8">
          <div class="add-teacher-form bg-white p-4 rounded-3 shadow-sm mx-auto" style="max-width: 600px;">
            <?php echo $msg; ?>
            <form method="post" action="" autocomplete="off">
              <p>Are you sure you want to delete user <strong><?php echo htmlspecialchars($user['username']); ?></strong> (ID: <?php echo $user['id']; ?>)?</p>
              <div class="d-flex gap-2">
                <button type="submit" name="delete_user" class="btn btn-danger px-4 fw-semibold">Delete</button>
                <a href="manage-user.php" class="btn btn-secondary px-4 fw-semibold">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
