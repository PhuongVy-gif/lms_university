<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    header('Location: ../login.php');
    exit();
}
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    } else {
        $conn = new mysqli('localhost', 'root', '', 'lms_university');
        if ($conn->connect_error) {
            die('Kết nối CSDL thất bại: ' . $conn->connect_error);
        }
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'student')");
        $stmt->bind_param('sss', $username, $email, $hashed);
        if ($stmt->execute()) {
            header('Location: manage_student.php');
            exit();
        } else {
            $error = "Thêm sinh viên thất bại.";
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sinh viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body style="background:#f6f8fa;">
<div class="container py-4">
    <h2 class="fw-bold mb-4">Thêm sinh viên</h2>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
    <form method="post" class="bg-white p-4 rounded shadow-sm" style="max-width:500px;">
        <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Thêm sinh viên</button>
        <a href="manage_student.php" class="btn btn-secondary ms-2">Quay lại</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
