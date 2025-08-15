<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
require_once __DIR__ . '/../config/database.php';
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Debug: check user_id
// echo '<pre>User ID: ' . $user_id . '</pre>';

// Get the list of enrolled courses (same as dashboard)
$my_courses = [];
$sql = "SELECT c.*, u.first_name AS instructor_first_name, u.last_name AS instructor_last_name, e.enrollment_date, e.final_grade 
        FROM courses c 
        JOIN enrollments e ON c.id = e.course_id 
        JOIN users u ON c.instructor_id = u.id 
        WHERE e.student_id = ? AND e.status = 'enrolled'";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $my_courses[] = $row;
        }
    }
    $stmt->close();
}
$conn->close();
// Debug: check returned data
// echo '<pre>'; print_r($my_courses); echo '</pre>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/13c47b287c.js" crossorigin="anonymous"></script>
    <style>
        .course-card { min-height: 120px; }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<div class="d-flex">
    <div class="container py-4">
    <h2 class="fw-bold mb-4"><i class="fa-solid fa-book me-2"></i>My Courses</h2>
    <?php if (count($my_courses) == 0): ?>
        <div class="text-center py-4">
            <i class="fas fa-book fa-3x text-primary mb-3"></i>
            <p class="text-muted fs-5">You are not enrolled in any courses yet.</p>
            <a href="courses.php" class="btn btn-primary">Browse Courses</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($my_courses as $course): ?>
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-primary"><?php echo htmlspecialchars($course['title']); ?></h5>
                            <p class="card-text text-muted mb-2">
                                <span class="fw-bold">Code:</span> <?php echo htmlspecialchars($course['course_code']); ?><br>
                                <span class="fw-bold">Instructor:</span> <?php echo htmlspecialchars($course['instructor_first_name'] . ' ' . $course['instructor_last_name']); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    Enrolled: <?php echo isset($course['enrollment_date']) ? htmlspecialchars($course['enrollment_date']) : 'N/A'; ?>
                                </small>
                                <a href="course_view.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    View Course
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
