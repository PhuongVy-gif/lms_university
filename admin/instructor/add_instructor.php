<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
global $pdo;
$db = $pdo;
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
// Lấy danh sách user có role instructor để chọn khi thêm mới
$stmt = $db->prepare("SELECT id, username, email FROM users WHERE role = 'instructor'");
$stmt->execute();
$user_teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Xử lý thêm giảng viên
$success = false;
$error = '';
if (isset($_POST['add_instructor'])) {
    // Lấy dữ liệu từ form
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $profile_image = '';
    if (isset($_FILES['profile_image_file']) && $_FILES['profile_image_file']['error'] === UPLOAD_ERR_OK) {
        $img_tmp = $_FILES['profile_image_file']['tmp_name'];
        $img_name = basename($_FILES['profile_image_file']['name']);
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (in_array($img_ext, $allowed)) {
            $new_img_name = 'profile_' . time() . '_' . rand(1000,9999) . '.' . $img_ext;
            $dest = __DIR__ . '/../../uploads/' . $new_img_name;
            if (move_uploaded_file($img_tmp, $dest)) {
                $profile_image = $new_img_name;
            }
        }
    }
    // Thêm mới instructor vào bảng users
    $sql = "INSERT INTO users (username, email, password, first_name, last_name, role, profile_image) VALUES (?, ?, ?, ?, ?, 'instructor', ?)";
    $stmt = $db->prepare($sql);
    if ($stmt->execute([$username, $email, $password, $first_name, $last_name, $profile_image])) {
        $success = true;
    } else {
        $error = 'Failed to add instructor!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Instructor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../header_admin.php'; ?>
<div class="container-fluid">
  <div class="row">
    <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
      <h1 class="fw-bold mb-4 text-center" style="font-size:2.4rem">Add Instructor</h1>
      <?php if ($success) { ?>
        <div class="alert alert-success">Instructor added successfully! <a href='manage-instructor.php'>Back to list</a></div>
      <?php } elseif ($error) { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php } ?>
      <div class="row justify-content-center">
        <div class="col-12 col-md-12 col-lg-10">
          <form method="post" action="" autocomplete="off" enctype="multipart/form-data" class="add-instructor-form bg-white p-4 rounded-3 shadow-sm mx-auto mt-4" style="max-width: 1000px;">
            <div class="mb-3">
              <label for="username" class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
              <label for="first_name" class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            <div class="mb-3">
              <label for="last_name" class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            <div class="mb-3">
              <label for="profile_image" class="form-label fw-semibold">Profile Image</label>
              <input type="file" class="form-control bg-light" id="profile_image" name="profile_image_file" accept="image/*">
            </div>
            <div class="d-flex gap-2">
              <button type="submit" name="add_instructor" class="btn btn-primary px-4 fw-semibold">Add</button>
              <button type="reset" class="btn btn-warning px-4 fw-semibold">Reset</button>
              <a href="manage-instructor.php" class="btn btn-secondary px-4 fw-semibold">Back</a>
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
