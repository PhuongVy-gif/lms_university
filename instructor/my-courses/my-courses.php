<?php
session_start();

// Check if user is instructor, otherwise redirect
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    header('Location: ../login.php');
    exit();
}

// Connect to database
$conn = new mysqli('localhost', 'root', '', 'lms_university');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Get instructor info
$instructor_id = $_SESSION['user_id'];

// Get courses taught by this instructor
$sql = "SELECT * FROM courses WHERE instructor_id = $instructor_id";
$result = mysqli_query($conn, $sql);
if (!$result) {
    echo '<div class="alert alert-danger">Query error: ' . mysqli_error($conn) . '</div>';
    $result = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://kit.fontawesome.com/13c47b287c.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }
        .card {
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
        }
        .btn-custom {
            background-color: #28a745;
            color: white;
        }
        .btn-custom:hover {
            background-color: #218838;
        }
        .table {
            margin-top: 20px;
        }
        .table th {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
<?php include '../../instructor/header_instructor.php'; ?>

<!-- header và sidebar đã được include ở trên, phần giao diện bị xóa theo yêu cầu -->
<div style="margin-left:220px">
<div class="px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-2 text-primary"><i class="fa-solid fa-book-open me-2"></i>My Courses</h2>
            <div class="text-muted" style="font-size:1.1rem;">View, add, edit, or delete the courses you are responsible for.</div>
        </div>
        <a href="add_my_courses.php" class="btn btn-success btn-lg px-4 py-2 shadow-sm"><i class="fa-solid fa-plus me-2"></i>Add Course</a>
    </div>
    <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white fw-bold text-primary" style="font-size:1.2rem;"><i class="fa-solid fa-list-ul me-2"></i>Course List</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Course Name</th>
                            <th>Description</th>
                            <th>Course Code</th>
                            <th>Credits</th>
                            <th>Semester</th>
                            <th>Year</th>
                            <th>Max Students</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result) { while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td class="fw-bold text-secondary"><?php echo $row['id']; ?></td>
                            <td>
                                <?php if (!empty($row['images'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['images']); ?>" alt="Course Image" style="width:60px;height:40px;object-fit:cover;border-radius:6px;">
                                <?php else: ?>
                                    <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold text-primary"><?php echo htmlspecialchars($row['title']); ?></td>
                            <td style="max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                <?php echo htmlspecialchars($row['description']); ?>
                            </td>
                            <td><span class="badge bg-info text-dark px-2 py-2" style="font-size:1rem;"><?php echo htmlspecialchars($row['course_code']); ?></span></td>
                            <td><span class="badge bg-success text-white px-2 py-2" style="font-size:1rem;"><?php echo (int)$row['credits']; ?></span></td>
                            <td><?php echo htmlspecialchars($row['semester']); ?></td>
                            <td><?php echo htmlspecialchars($row['year']); ?></td>
                            <td><span class="badge bg-secondary text-white px-2 py-2" style="font-size:1rem;"><?php echo (int)$row['max_students']; ?></span></td>
                            <td>
                                <span class="badge <?php echo $row['status'] == 'active' ? 'bg-success' : 'bg-secondary'; ?> px-2 py-2" style="font-size:1rem;">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_my_courses.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                <a href="delete_my_courses.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="return confirm('Are you sure you want to delete this course?');"><i class="fa-solid fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
