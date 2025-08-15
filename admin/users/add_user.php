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
$msg = '';
if (isset($_POST['add_user'])) {
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $role = mysqli_real_escape_string($conn, $_POST['role']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
  $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
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
  $profile_image_sql = mysqli_real_escape_string($conn, $profile_image);
  $sql = "INSERT INTO users (username, email, role, password, first_name, last_name, profile_image) VALUES ('$username', '$email', '$role', '$password', '$first_name', '$last_name', '$profile_image_sql')";
  if (mysqli_query($conn, $sql)) {
    $msg = '<div class="alert alert-success">User added successfully!</div>';
  } else {
    $msg = '<div class="alert alert-danger">Error: ' . mysqli_error($conn) . '</div>';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add user</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../header_admin.php'; ?>
<div class="container-fluid">
  <div class="row">
    <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
      <h1 class="fw-bold mb-4 text-center" style="font-size:2.4rem">Add user</h1>
      <div class="row justify-content-center">
        <div class="col-12 col-md-12 col-lg-12">
          <form method="post" action="" enctype="multipart/form-data" autocomplete="off" class="add-teacher-form bg-white p-4 rounded-3 shadow-sm mx-auto mt-4" style="max-width:900px;">
            <?php echo $msg; ?>
            <div class="mb-3">
              <label for="username" class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
              <input type="text" class="form-control bg-light" id="username" name="username" required placeholder="Username">
            </div>
            <div class="mb-3">
              <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
              <input type="email" class="form-control bg-light" id="email" name="email" required placeholder="Email">
            </div>
            <div class="mb-3">
              <label for="first_name" class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control bg-light" id="first_name" name="first_name" required placeholder="First Name">
            </div>
            <div class="mb-3">
              <label for="last_name" class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control bg-light" id="last_name" name="last_name" required placeholder="Last Name">
            </div>
            <div class="mb-3">
              <label for="profile_image" class="form-label fw-semibold">Profile Image</label>
              <input type="file" class="form-control bg-light" id="profile_image" name="profile_image_file" accept="image/*">
            </div>
            <div class="mb-3">
              <label for="role" class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
              <select class="form-select bg-light" id="role" name="role" required>
                <option value="student">Student</option>
                <option value="instructor">Instructor</option>
                <option value="admin">Admin</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
              <input type="password" class="form-control bg-light" id="password" name="password" required placeholder="Password">
            </div>
            <div class="d-flex gap-2 mt-3">
              <button type="submit" name="add_user" class="btn btn-primary px-4 fw-semibold">Add</button>
              <button type="reset" class="btn btn-warning px-4 fw-semibold">Reset</button>
              <a href="manage-user.php" class="btn btn-secondary px-4 fw-semibold">Back</a>
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
