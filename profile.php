
<?php
session_start();
require_once('config/database.php');
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
include 'user/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <img src="https://ui-avatars.com/api/?name=<?=urlencode($user['username'])?>&background=667eea&color=fff&size=100" class="rounded-circle shadow" alt="Avatar">
                    </div>
                    <h3 class="mb-2"> <?= htmlspecialchars($user['username']) ?> </h3>
                    <p class="text-muted mb-4"><i class="fa-solid fa-envelope me-2"></i><?= htmlspecialchars($user['email']) ?></p>
                    <a href="logout.php" class="btn btn-danger px-4">Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'user/footer.php'; ?>
