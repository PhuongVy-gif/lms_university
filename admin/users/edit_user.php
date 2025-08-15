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
$user = null;
$user_result = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
if ($user_result && mysqli_num_rows($user_result) > 0) {
  $user = mysqli_fetch_assoc($user_result);
} else {
  header('Location: manage-user.php');
  exit();
}

if (isset($_POST['edit_user'])) {
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $role = mysqli_real_escape_string($conn, $_POST['role']);
  $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
  $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
  $profile_image = $_POST['profile_image_old'] ?? '';
  // Handle file upload
  if (isset($_FILES['profile_image_file']) && $_FILES['profile_image_file']['error'] === UPLOAD_ERR_OK) {
    $img_tmp = $_FILES['profile_image_file']['tmp_name'];
    $img_name = basename($_FILES['profile_image_file']['name']);
    $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp'];
    if (in_array($img_ext, $allowed)) {
      $new_img_name = 'profile_' . time() . '_' . rand(1000,9999) . '.' . $img_ext;
      $dest = __DIR__ . '/../../uploads/' . $new_img_name;
      if (move_uploaded_file($img_tmp, $dest)) {
        // Remove old image if exists
        if (!empty($profile_image) && file_exists(__DIR__ . '/../../uploads/' . $profile_image)) {
          @unlink(__DIR__ . '/../../uploads/' . $profile_image);
        }
        $profile_image = $new_img_name;
      }
    }
  }
  $profile_image_sql = mysqli_real_escape_string($conn, $profile_image);
  $update_fields = [];
  $update_fields[] = "username='$username'";
  $update_fields[] = "email='$email'";
  $update_fields[] = "role='$role'";
  $update_fields[] = "first_name='$first_name'";
  $update_fields[] = "last_name='$last_name'";
  $update_fields[] = "profile_image='$profile_image_sql'";
  if (!empty($_POST['password'])) {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $update_fields[] = "password='$password'";
  }
  $update_sql = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE id = $id";
  if (mysqli_query($conn, $update_sql)) {
    $msg = '<div class="alert alert-success">User updated successfully!</div>';
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $id"));
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
  <title>Edit User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../header_admin.php'; ?>
<div class="container-fluid">
  <div class="row">
    <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
  <h1 class="fw-bold mb-4 text-center" style="font-size:2.4rem">Edit User</h1>
      <div class="row justify-content-center">
        <div class="col-12 col-md-12 col-lg-12">
          <form method="post" action="" enctype="multipart/form-data" autocomplete="off" class="add-teacher-form bg-white p-4 rounded-3 shadow-sm mx-auto mt-4" style="max-width:900px;">
            <?php echo $msg; ?>
            <div class="mb-3">
              <label for="username" class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
              <input type="text" class="form-control bg-light" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required placeholder="Username">
            </div>
            <div class="mb-3">
              <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
              <input type="email" class="form-control bg-light" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required placeholder="Email">
            </div>
            <div class="mb-3">
              <label for="first_name" class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control bg-light" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required placeholder="First Name">
            </div>
            <div class="mb-3">
              <label for="last_name" class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control bg-light" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required placeholder="Last Name">
            </div>
            <div class="mb-3">
              <label for="profile_image" class="form-label fw-semibold">Profile Image</label>
              <?php if (!empty($user['profile_image'])): ?>
                <div class="mb-2">
                  <img src="../../uploads/<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Image" style="max-width:100px;max-height:100px;">
                </div>
              <?php endif; ?>
              <input type="file" class="form-control bg-light" id="profile_image" name="profile_image_file" accept="image/*">
              <input type="hidden" name="profile_image_old" value="<?php echo htmlspecialchars($user['profile_image'] ?? ''); ?>">
            </div>
            <div class="mb-3">
              <label for="role" class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
              <select class="form-select bg-light" id="role" name="role" required>
                <option value="student" <?php if($user['role']=='student') echo 'selected'; ?>>Student</option>
                <option value="instructor" <?php if($user['role']=='instructor') echo 'selected'; ?>>Instructor</option>
                <option value="admin" <?php if($user['role']=='admin') echo 'selected'; ?>>Admin</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label fw-semibold">New Password (leave blank if unchanged)</label>
              <input type="password" class="form-control bg-light" id="password" name="password" placeholder="New Password">
            </div>
            <div class="d-flex gap-2 mt-3">
              <button type="submit" name="edit_user" class="btn btn-warning px-4 fw-semibold">Update</button>
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
