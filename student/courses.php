<?php
session_start();
include 'header.php';
include(__DIR__ . '/../config/database.php');
if (!isset($conn) || !$conn) {
    $conn = new mysqli('localhost', 'root', '', 'lms_university');
    if ($conn->connect_error) {
        die('Database connection failed: ' . $conn->connect_error);
    }
}
$user_id = $_SESSION['user_id'] ?? 0;
// Handle registration POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_course_id']) && $user_id) {
    $course_id = intval($_POST['register_course_id']);
    // Check if already registered
    $check = $conn->query("SELECT * FROM enrollments WHERE student_id=$user_id AND course_id=$course_id");
    if ($check && $check->num_rows == 0) {
        $conn->query("INSERT INTO enrollments (student_id, course_id, status) VALUES ($user_id, $course_id, 'enrolled')");
    }
}
// Get registration status for all courses of this user
$reg_status = [];
if ($user_id) {
    $result = $conn->query("SELECT course_id, status FROM enrollments WHERE student_id=$user_id");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $reg_status[$row['course_id']] = $row['status'];
        }
    } else {
    echo '<div class="alert alert-danger">Course registration query error: ' . htmlspecialchars($conn->error) . '</div>';
    }
}
// Get courses and instructors (new schema) with search
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($search !== '') {
    $search_sql = $conn->real_escape_string($search);
    $sql = "SELECT c.*, u.username as instructor_name FROM courses c LEFT JOIN users u ON c.instructor_id = u.id WHERE c.title LIKE '%$search_sql%' ORDER BY c.id DESC";
} else {
    $sql = "SELECT c.*, u.username as instructor_name FROM courses c LEFT JOIN users u ON c.instructor_id = u.id ORDER BY c.id DESC";
}
$result = $conn->query($sql);
$courses = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Ensure column name is 'images' (not 'image')
        if (empty($row['images'])) {
            $row['images'] = 'default.png'; // fallback if no image
        }
        $courses[] = $row;
    }
}
// Get approved courses for student (new schema)
$my_courses = [];
if ($user_id) {
    $sql_my = "SELECT c.* FROM courses c JOIN enrollments e ON c.id = e.course_id WHERE e.student_id = $user_id AND e.status = 'enrolled'";
    $result_my = $conn->query($sql_my);
    if ($result_my) {
        while ($row = $result_my->fetch_assoc()) {
            $my_courses[] = $row;
        }
    }
}
mysqli_close($conn);
?>
<div class="container my-4">
    <?php if (isset($_POST['register_course_id'])): ?>
        <div class="alert alert-info">Course registration successful!</div>
    <?php endif; ?>
    <h2 class="fw-bold mb-2" style="font-size:2rem;">All Courses</h2>
    <div class="row flex-nowrap mt-4" style="overflow-x:auto;">
        <!-- Sidebar filter -->
        <div class="col-12 col-lg-3 mb-4" style="min-width:280px;max-width:350px;">
            <div class="card p-3 mb-3">
                <div class="fw-bold mb-2">My Courses</div>
                <?php if (count($my_courses) > 0): ?>
                    <ul class="list-group mb-2">
                        <?php foreach ($my_courses as $mc): ?>
                            <li class="list-group-item d-flex align-items-center gap-2">
                                <i class="fa-solid fa-book text-primary"></i>
                                <span><?php echo htmlspecialchars($mc['title']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-muted small">You have no approved courses yet.</div>
                <?php endif; ?>
            </div>
            <div class="card p-3 mb-3">
                <div class="fw-bold mb-2">Rating</div>
                <div class="form-check mb-1">
                    <input class="form-check-input" type="radio" name="rating" id="r45">
                    <label class="form-check-label" for="r45">
                        <span class="text-warning">★</span> 4.5 and up
                    </label>
                </div>
                <div class="form-check mb-1">
                    <input class="form-check-input" type="radio" name="rating" id="r40">
                    <label class="form-check-label" for="r40">
                        <span class="text-warning">★</span> 4.0 and up
                    </label>
                </div>
                <div class="form-check mb-1">
                    <input class="form-check-input" type="radio" name="rating" id="r35">
                    <label class="form-check-label" for="r35">
                        <span class="text-warning">★</span> 3.5 and up
                    </label>
                </div>
                <div class="form-check mb-1">
                    <input class="form-check-input" type="radio" name="rating" id="r30">
                    <label class="form-check-label" for="r30">
                        <span class="text-warning">★</span> 3.0 and up
                    </label>
                </div>
            </div>
            <div class="card p-3">
                <div class="fw-bold mb-2">Video Duration</div>
                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" id="v1">
                    <label class="form-check-label" for="v1">0-1 hour</label>
                </div>
                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" id="v2">
                    <label class="form-check-label" for="v2">1-3 hours</label>
                </div>
                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" id="v3">
                    <label class="form-check-label" for="v3">3-6 hours</label>
                </div>
                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" id="v4">
                    <label class="form-check-label" for="v4">6-17 hours</label>
                </div>
            </div>
        </div>
    <!-- Main content -->
    <div class="col-12 col-lg-9" style="min-width:320px;">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
                <div class="fw-semibold" style="font-size:1.1rem; color:#666;">
                    <?php echo count($courses); ?> results
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold">Sort by</span>
                    <select class="form-select form-select-sm" style="width:auto;">
                        <option>Most Popular</option>
                        <option>Newest</option>
                        <option>Price: Low to High</option>
                        <option>Price: High to Low</option>
                    </select>
                </div>
            </div>
<?php if (empty($courses)): ?>
    <div class="alert alert-warning">No courses found.</div>
<?php endif; ?>
<?php foreach ($courses as $course): ?>
            <div class="card mb-4 shadow-sm border-0">
                <div class="row g-0 align-items-center">
                    <div class="col-md-4 col-lg-3">
                        <img src="../assets/images/<?php echo htmlspecialchars($course['images']); ?>" class="img-fluid rounded-3" style="object-fit:cover; width:100%; height:140px;" alt="<?php echo htmlspecialchars($course['title']); ?>">
                    </div>
                    <div class="col-md-8 col-lg-9">
                        <div class="card-body py-3 px-4">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold" style="font-size:1.1rem; color:#222;"><?php echo htmlspecialchars($course['title']); ?></span>
                                <?php if (!empty($course['is_best_seller'])): ?>
                                    <span class="badge bg-info text-dark ms-2">Best Seller</span>
                                <?php endif; ?>
                            </div>
                            <div class="mb-1 text-muted" style="font-size:0.98rem;">
                                <?php echo htmlspecialchars($course['description']); ?>
                            </div>
                            <div class="mb-2 small">
                                <span class="fw-semibold">Instructor:</span> <?php echo htmlspecialchars($course['instructor_name']); ?>
                            </div>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <span class="fw-bold text-warning" style="font-size:1.1rem;">4.8</span>
                                <span class="text-warning">★ ★ ★ ★ ★</span>
                                <span class="text-muted small">(552)</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <?php
                                $cid = $course['id'];
                                if (!isset($reg_status[$cid])) {
                                ?>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="register_course_id" value="<?php echo $cid; ?>">
                                        <button type="submit" class="btn btn-primary px-4">Register</button>
                                    </form>
                                <?php } elseif ($reg_status[$cid] == 'enrolled') { ?>
                                    <button class="btn btn-success px-4" disabled>Enrolled</button>
                                <?php } elseif ($reg_status[$cid] == 'completed') { ?>
                                    <button class="btn btn-secondary px-4" disabled>Completed</button>
                                <?php } elseif ($reg_status[$cid] == 'dropped') { ?>
                                    <button class="btn btn-danger px-4" disabled>Dropped</button>
                                <?php } ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
<?php endforeach; ?>
        </div>
    </div>
</div>