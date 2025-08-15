<?php
session_start();
require_once __DIR__ . '/config/database.php';
$message = '';
// Ensure $pdo is defined and connected
if (!isset($pdo) || !$pdo) {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=lms_university;charset=utf8', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }
}

if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = 'student';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Kiểm tra email đã tồn tại chưa
    $stmt = $pdo->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $message = "Email already exists. Please use a different email.";
    } else {
        // Thêm người dùng mới
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, first_name, last_name) VALUES (?, ?, ?, ?, ?, ?)");
        try {
            $stmt->execute([$username, $email, $hashed_password, $role, $first_name, $last_name]);
            // Tự động đăng nhập sau khi đăng ký
            $user_id = $pdo->lastInsertId();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['email'] = $email;
            header("Location: student/dashboard.php");
            exit();
        } catch (PDOException $e) {
            $message = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="register-page">
<div class="register-container">
        <div class="register-header">
            <i class="fas fa-user-plus"></i>
            <h2> Register</h2>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" required class="form-control" placeholder="Username">
                </div>
                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" required class="form-control" placeholder="First name">
                </div>
                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" required class="form-control" placeholder="Last name">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" required class="form-control" placeholder="example@email.com">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" required class="form-control" placeholder="Choose a password">
                </div>
                <button type="submit" class="btn btn-register w-100">Register</button>
                <p class="mt-3 text-center">Already have an account? <a href="login.php" class="login-link">Login here</a></p>
            </form>
        </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
