<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    header('Location: ../login.php');
    exit();
}
$conn = new mysqli('localhost', 'root', '', 'lms_university');
if ($conn->connect_error) {
    die('Database connection failed:' . $conn->connect_error);
}
$error = "";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: manage_student.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    if (empty($username) || empty($email)) {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, email=? WHERE user_id=? AND role='student'");
        $stmt->bind_param('ssi', $username, $email, $id);
        if ($stmt->execute()) {
            header('Location: manage_student.php');
            exit();
        } else {
            $error = "Update failed.";
        }
        $stmt->close();
    }
}
$stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id=? AND role='student'");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body style="background:#f6f8fa;">
<div class="container py-4">
    <h2 class="fw-bold mb-4">Edit student information</h2>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
    <form method="post" class="bg-white p-4 rounded shadow-sm" style="max-width:500px;">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="manage_student.php" class="btn btn-secondary ms-2">cancel</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
