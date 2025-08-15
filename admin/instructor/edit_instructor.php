<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
global $pdo;
$db = $pdo;
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
// Lấy id instructor
$instructor_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($instructor_id <= 0) {
    header('Location: manage-instructor.php');
    exit();
}
// Lấy thông tin instructor
$sql = "SELECT id, username, email, first_name, last_name, profile_image FROM users WHERE id = ? AND role = 'instructor' LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->execute([$instructor_id]);
$instructor = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$instructor) {
    header('Location: manage-instructor.php');
    exit();
}
$success = false;
$error = '';
// Xử lý cập nhật
if (isset($_POST['edit_instructor'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $profile_image = $instructor['profile_image'] ?? '';
    if (isset($_FILES['profile_image_file']) && $_FILES['profile_image_file']['error'] === UPLOAD_ERR_OK) {
        $img_tmp = $_FILES['profile_image_file']['tmp_name'];
        $img_name = basename($_FILES['profile_image_file']['name']);
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (in_array($img_ext, $allowed)) {
            $new_img_name = 'profile_' . time() . '_' . rand(1000,9999) . '.' . $img_ext;
            $dest = __DIR__ . '/../../uploads/' . $new_img_name;
            if (move_uploaded_file($img_tmp, $dest)) {
                if (!empty($profile_image) && file_exists(__DIR__ . '/../../uploads/' . $profile_image)) {
                    @unlink(__DIR__ . '/../../uploads/' . $profile_image);
                }
                $profile_image = $new_img_name;
            }
        }
    }
    $sql_update = "UPDATE users SET first_name = ?, last_name = ?, email = ?, profile_image = ? WHERE id = ? AND role = 'instructor'";
    $stmt_update = $db->prepare($sql_update);
    if ($stmt_update->execute([$first_name, $last_name, $email, $profile_image, $instructor_id])) {
        $success = true;
        // Lấy lại dữ liệu mới nhất
        $stmt = $db->prepare($sql);
        $stmt->execute([$instructor_id]);
        $instructor = $stmt->fetch(PDO::FETCH_ASSOC);
        // Chuyển hướng sau khi cập nhật thành công
        header('Location: manage-instructor.php?updated=1');
        exit();
    } else {
        $error = 'Failed to update instructor!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Instructor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../header_admin.php'; ?>
<div class="container-fluid">
  <div class="row">
    <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
      <h1 class="fw-bold mb-4 text-center" style="font-size:2.4rem">Edit Instructor</h1>
      <?php if ($success) { ?>
        <div class="alert alert-success">Update successful! <a href='manage-instructor.php'>Back to list</a></div>
      <?php } elseif ($error) { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php } ?>
      <div class="row justify-content-center">
        <div class="col-12 col-md-12 col-lg-10">
          <form method="post" action="" enctype="multipart/form-data" autocomplete="off" class="add-instructor-form bg-white p-4 rounded-3 shadow-sm mx-auto mt-4" style="max-width: 1000px;">
            <div class="mb-3">
              <label class="form-label fw-semibold">Username</label>
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($instructor['username']); ?>" disabled>
            </div>
            <div class="mb-3">
              <label for="first_name" class="form-label fw-semibold">First Name</label>
              <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($instructor['first_name']); ?>" required>
            </div>
            <div class="mb-3">
              <label for="last_name" class="form-label fw-semibold">Last Name</label>
              <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($instructor['last_name']); ?>" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label fw-semibold">Email</label>
              <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($instructor['email']); ?>" required>
            </div>
            <div class="mb-3">
              <label for="profile_image" class="form-label fw-semibold">Profile Image</label>
              <?php if (!empty($instructor['profile_image'])): ?>
                <div class="mb-2">
                  <img src="../../uploads/<?php echo htmlspecialchars($instructor['profile_image']); ?>" alt="Profile Image" style="max-width:100px;max-height:100px;">
                </div>
              <?php endif; ?>
              <input type="file" class="form-control bg-light" id="profile_image" name="profile_image_file" accept="image/*">
            </div>
            <div class="d-flex gap-2">
              <button type="submit" name="edit_instructor" class="btn btn-warning px-4 fw-semibold">Update</button>
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
