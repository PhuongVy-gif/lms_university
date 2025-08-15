<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();

if (!hasRole('student')) {
    header('Location: ../login.php');
    exit();
}


$db = $pdo;

// Get enrolled courses

// Get enrolled courses (instructor, not teacher)
$query = "SELECT c.*, u.first_name AS instructor_first_name, u.last_name AS instructor_last_name, e.enrollment_date, e.final_grade 
          FROM courses c 
          JOIN enrollments e ON c.id = e.course_id 
          JOIN users u ON c.instructor_id = u.id 
          WHERE e.student_id = ? AND e.status = 'enrolled'";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$enrolled_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent assignments
$query = "SELECT a.*, c.title as course_title, c.course_code,
          CASE WHEN s.id IS NOT NULL THEN 'submitted' ELSE 'pending' END as status,
          s.grade, s.submitted_at
          FROM assignments a
          JOIN courses c ON a.course_id = c.id
          JOIN enrollments e ON c.id = e.course_id
          LEFT JOIN assignment_submissions s ON a.id = s.assignment_id AND s.student_id = ?
          WHERE e.student_id = ? AND e.status = 'enrolled'
          ORDER BY a.due_date ASC LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$recent_assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Quiz feature is disabled because table quiz_attempts does not exist
$recent_quizzes = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - University LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
    <style>
        body {
            background: #f6f8fa;
        }
        .dashboard-welcome {
            background: linear-gradient(90deg, #2563eb 0%, #1e40af 100%);
            color: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(37,99,235,0.08);
            padding: 1.2rem 1.5rem;
            margin-bottom: 1.2rem;
        }
        .dashboard-stats .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            transition: transform 0.1s;
        }
        .dashboard-stats .card:hover {
            transform: translateY(-4px) scale(1.03);
        }
        .dashboard-stats .icon {
            font-size: 2.5rem;
            opacity: 0.2;
        }
        .dashboard-section {
            margin-bottom: 2rem;
        }
        .dashboard-section .card-header {
            background: #2563eb;
            color: #fff;
            border-radius: 1rem 1rem 0 0;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .dashboard-section .card {
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        .timeline-item {
            margin-bottom: 1.5rem;
        }
        .timeline-marker {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
        .bg-success { background: #22c55e !important; }
        .bg-warning { background: #f59e42 !important; }
        .bg-primary { background: #2563eb !important; }
        .bg-info { background: #0ea5e9 !important; }
        .bg-danger { background: #ef4444 !important; }
        .dashboard-table th, .dashboard-table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
   <?php include __DIR__ . './header.php'; ?>
    
    <div class="container-fluid" style="overflow-x:auto;">
        <div class="row flex-nowrap" style="min-width:900px;">
            <main class="dashboard-content" style="min-width:900px;">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-1 pb-1 mb-2 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-calendar"></i> This week
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Welcome Section -->
                <div class="dashboard-welcome">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-user-graduate fa-2x me-3"></i>
                        <h2 class="mb-0">Welcome back, <?php echo isset($_SESSION['first_name']) ? htmlspecialchars($_SESSION['first_name']) : htmlspecialchars($_SESSION['username']); ?>!</h2>
                    </div>
                    <p class="mb-0 fs-5">You have <strong><?php echo count($enrolled_courses); ?></strong> active courses and <strong><?php echo count(array_filter($recent_assignments, function($a) { return $a['status'] == 'pending'; })); ?></strong> pending assignments.</p>
                </div>

                <!-- Stats Cards -->
                <div class="dashboard-stats row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card h-100 py-3">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-uppercase text-primary fw-bold mb-1">Enrolled Courses</div>
                                    <div class="fs-3 fw-bold text-dark"><?php echo count($enrolled_courses); ?></div>
                                </div>
                                <div class="icon text-primary"><i class="fas fa-book"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card h-100 py-3">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-uppercase text-success fw-bold mb-1">Completed Assignments</div>
                                    <div class="fs-3 fw-bold text-dark"><?php echo count(array_filter($recent_assignments, function($a) { return $a['status'] == 'submitted'; })); ?></div>
                                </div>
                                <div class="icon text-success"><i class="fas fa-clipboard-check"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card h-100 py-3">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-uppercase text-info fw-bold mb-1">Quiz Attempts</div>
                                    <div class="fs-3 fw-bold text-dark"><?php echo count($recent_quizzes); ?></div>
                                </div>
                                <div class="icon text-info"><i class="fas fa-question-circle"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card h-100 py-3">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-uppercase text-warning fw-bold mb-1">Pending Tasks</div>
                                    <div class="fs-3 fw-bold text-dark"><?php echo count(array_filter($recent_assignments, function($a) { return $a['status'] == 'pending'; })); ?></div>
                                </div>
                                <div class="icon text-warning"><i class="fas fa-exclamation-triangle"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="row dashboard-section">
                    <!-- Enrolled Courses -->
                    <div class="col-lg-8 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-book"></i> My Courses
                            </div>
                            <div class="card-body">
                                <?php if (empty($enrolled_courses)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-book fa-3x text-primary mb-3"></i>
                                        <p class="text-muted fs-5">You are not enrolled in any courses yet.</p>
                                        <a href="courses.php" class="btn btn-primary">Browse Courses</a>
                                    </div>
                                <?php else: ?>
                                    <div class="row">
                                        <?php foreach ($enrolled_courses as $course): ?>
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
                                                                Enrolled: <?php echo formatDate($course['enrollment_date']); ?>
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
                    </div>
                    <!-- Recent Activity -->
                    <div class="col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-clock"></i> Recent Activity
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    <?php foreach ($recent_assignments as $assignment): ?>
                                        <div class="timeline-item">
                                            <span class="timeline-marker <?php echo $assignment['status'] == 'submitted' ? 'bg-success' : 'bg-warning'; ?>"></span>
                                            <div class="timeline-content">
                                                <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($assignment['title']); ?></h6>
                                                <p class="text-muted mb-1">Course: <?php echo htmlspecialchars($assignment['course_title']); ?></p>
                                                <small class="text-muted">
                                                    Due: <?php echo formatDate($assignment['due_date']); ?>
                                                    <?php if ($assignment['status'] == 'submitted'): ?>
                                                        <span class="badge bg-success ms-2">Submitted</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning ms-2">Pending</span>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Quiz Results -->
                <?php if (!empty($recent_quizzes)): ?>
                <div class="row dashboard-section">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-chart-line"></i> Recent Quiz Results
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table dashboard-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Quiz</th>
                                                <th>Course</th>
                                                <th>Score</th>
                                                <th>Percentage</th>
                                                <th>Completed</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_quizzes as $quiz): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($quiz['title']); ?></td>
                                                    <td><?php echo htmlspecialchars($quiz['course_title']); ?></td>
                                                    <td><?php echo $quiz['score']; ?>/<?php echo $quiz['total_points']; ?></td>
                                                    <td>
                                                        <?php 
                                                        $percentage = ($quiz['score'] / $quiz['total_points']) * 100;
                                                        $badge_class = $percentage >= 90 ? 'bg-success' : ($percentage >= 70 ? 'bg-warning' : 'bg-danger');
                                                        ?>
                                                        <span class="badge <?php echo $badge_class; ?>">
                                                            <?php echo number_format($percentage, 1); ?>%
                                                        </span>
                                                    </td>
                                                    <td><?php echo formatDate($quiz['completed_at']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>