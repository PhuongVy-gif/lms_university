<?php
session_start();
require_once __DIR__ . '/config/database.php';
$error = "";
// Always use global $pdo from config/database.php
global $pdo;

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['profile_image'] = $user['profile_image'];
                $_SESSION['login_success'] = true;
            $role = strtolower(trim($user['role']));
            if ($role === 'admin') {
                header("Location: admin/dashboard.php");
                exit();
            } elseif ($role === 'instructor') {
                header("Location: instructor/dashboard.php");
                exit();
            } elseif ($role === 'student') {
                header("Location: student/dashboard.php");
                exit();
            } else {
                $error = "Unknown user role.";
            }
            } else {
                $error = "Incorrect email or password.";
            }
        } catch (PDOException $e) {
            $error = "Query failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LMS Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 25px;
        }
        .login-header i {
            font-size: 2.5rem;
            color: #667eea;
        }
        .btn-login {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            border: none;
        }
        .btn-login:hover {
            background: linear-gradient(to right, #5c6bdc, #6f3ea0);
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-header">
        <i class="fas fa-user-lock"></i>
        <h3>Login to LMS</h3>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="login.php">
        <div class="mb-3">
            <label class="form-label">Email address</label>
            <input type="email" name="email" class="form-control" required placeholder="you@example.com">
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Enter password">
        </div>



        <button type="submit" class="btn btn-login w-100">Login</button>
    </form>

    <div class="text-center mt-3">
        <p>Don’t have an account? <a href="register.php">Register here</a>.</p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
