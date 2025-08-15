<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login.php');
    exit();
}
require_once(__DIR__ . '/../config/database.php');
// Use $conn from config/database.php, or fallback to direct connection
if (!isset($conn)) {
    $conn = new mysqli('localhost', 'root', '', 'lms_university');
    if ($conn->connect_error) {
        die('Database connection failed: ' . $conn->connect_error);
    }
}
$user_id = $_SESSION['user_id'];
$assignments = [];
$sql = "SELECT a.id AS assignment_id, a.title, a.description, a.due_date, c.title as course_title, s.grade, s.feedback, s.file_path
    FROM assignments a
    JOIN courses c ON a.course_id = c.id
    LEFT JOIN assignment_submissions s ON a.id = s.assignment_id AND s.student_id = ?
    WHERE a.course_id IN (SELECT course_id FROM enrollments WHERE student_id = ? AND status = 'enrolled')
    ORDER BY a.due_date DESC";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('ii', $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $assignments[] = $row;
    }
    $stmt->close();
} else {
    die('Query error: ' . $conn->error);
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Assignments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/13c47b287c.js" crossorigin="anonymous"></script>
</head>
<body style="background:#f6f8fa;">
<?php include 'header.php'; ?>
<div class="d-flex">
    <div class="container py-4">
    <?php if (isset($_GET['upload']) && $_GET['upload'] === 'success'): ?>
        <div class="alert alert-success">Assignment submitted successfully!</div>
    <?php endif; ?>
    <h2 class="fw-bold mb-2">My Assignments</h2>
    <div class="mb-3"><span class="fs-5 fw-semibold">Assignment List</span></div>
    <!-- Pending assignments list -->
    <?php
    $pending = [];
    $now = date('Y-m-d H:i:s');
    foreach ($assignments as $a) {
        if (!$a['file_path']) {
            $due = strtotime($a['due_date']);
            $days_left = ($due - strtotime($now)) / 86400;
            $status = $days_left <= 2 && $days_left > 0 ? 'Due Soon' : 'Not Submitted';
            $pending[] = [
                'title' => $a['title'],
                'due_date' => $a['due_date'],
                'course_title' => $a['course_title'],
                'status' => $status
            ];
        }
    }
    ?>
    <div class="mb-4">
        <h5 class="fw-bold text-danger mb-2">Pending Assignments</h5>
        <?php if (empty($pending)): ?>
            <div class="text-muted">No pending assignments.</div>
        <?php else: ?>
            <ul class="list-group">
            <?php foreach ($pending as $p): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold"><i class="fa-regular fa-clock me-1"></i><?php echo htmlspecialchars($p['title']); ?></span>
                        <span class="ms-3"><i class="fa-regular fa-calendar me-1"></i><?php echo date('d/m/Y H:i', strtotime($p['due_date'])); ?></span>
                        <span class="ms-3"><i class="fa-solid fa-book me-1"></i><?php echo htmlspecialchars($p['course_title']); ?></span>
                    </div>
                    <span class="badge <?php echo $p['status']==='Due Soon' ? 'bg-warning text-dark' : 'bg-secondary'; ?>"> <?php echo $p['status']; ?> </span>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <table class="table table-bordered bg-white shadow-sm">
        <thead class="table-light">
            <tr>
                <th>No.</th>
                <th>Title</th>
                <th>Description</th>
                <th>Course</th>
                <th>Due Date</th>
                <th>Submitted File</th>
                <th>Grade</th>
                <th>Feedback</th>
                <th>Status</th>
                <th>Upload/Submit</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($assignments)): ?>
                <tr><td colspan="10" class="text-center text-muted">No assignments for your enrolled courses.</td></tr>
            <?php else: $i=1; foreach ($assignments as $a): ?>
            <?php
                $now = date('Y-m-d H:i:s');
                $is_overdue = (!$a['file_path'] && $now > $a['due_date']);
                // FSM: Not Submitted, Submitted, Graded
                if (!$a['file_path']) {
                    $status = $is_overdue ? 'Overdue' : 'Not Submitted';
                } elseif (!is_null($a['grade'])) {
                    $status = 'Graded';
                } else {
                    $status = ($now > $a['due_date']) ? 'Submitted Late' : 'Submitted';
                }
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td>
                    <span class="fw-bold text-primary"><?php echo htmlspecialchars($a['title']); ?></span>
                    <button type="button" class="btn btn-link btn-sm p-0 ms-2" data-bs-toggle="collapse" data-bs-target="#desc<?php echo $a['assignment_id']; ?>" aria-expanded="false" aria-controls="desc<?php echo $a['assignment_id']; ?>">Details</button>
                    <div class="collapse mt-2" id="desc<?php echo $a['assignment_id']; ?>">
                        <div class="card card-body bg-light border-0 p-2">
                            <?php echo nl2br(htmlspecialchars($a['description'])); ?>
                        </div>
                    </div>
                </td>
                <td><?php echo htmlspecialchars($a['description']); ?></td>
                <td><?php echo htmlspecialchars($a['course_title']); ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($a['due_date'])); ?></td>
                <td>
                    <?php if ($a['file_path']): ?>
                        <a href="../uploads/<?php echo htmlspecialchars($a['file_path']); ?>" target="_blank" class="btn btn-outline-primary btn-sm">View File</a>
                    <?php else: ?>
                        <span class="text-warning">Not Submitted</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo is_null($a['grade']) ? '<span class="text-warning">Not Graded</span>' : '<span class="fw-bold">'.$a['grade'].'</span>'; ?>
                </td>
                <td>
                    <?php echo isset($a['feedback']) && $a['feedback'] !== null && $a['feedback'] !== '' ? htmlspecialchars($a['feedback']) : '<span class="text-muted">-</span>'; ?>
                </td>
                <td>
                    <?php if ($status == 'Not Submitted'): ?>
                        <span class="badge bg-secondary">Not Submitted</span>
                    <?php elseif ($status == 'Submitted' || $status == 'Submitted Late'): ?>
                        <span class="badge bg-success">Submitted</span>
                        <?php if (is_null($a['grade'])): ?>
                    
                        <?php endif; ?>
                    <?php elseif ($status == 'Graded'): ?>
                        <span class="badge bg-primary">Graded</span>
                    <?php elseif ($status == 'Overdue'): ?>
                        <span class="badge bg-danger">Overdue</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!$a['file_path'] && !$is_overdue): ?>
                        <form method="post" action="submit_assignment.php" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
                            <input type="hidden" name="assignment_id" value="<?php echo $a['assignment_id']; ?>">
                            <input type="file" name="file" required accept=".pdf,.doc,.docx,.zip,.rar" class="form-control form-control-sm" style="width:180px;">
                            <button type="submit" class="btn btn-success btn-sm">Upload/Submit</button>
                        </form>
                    <?php elseif (!$a['file_path'] && $is_overdue): ?>
                        <span class="text-danger fw-bold">Late Submission</span>
                    <?php elseif ($a['file_path'] && is_null($a['grade'])): ?>
                        <div class="d-flex gap-2 align-items-center">
                            <!-- Edit button: allows re-uploading file only if not overdue -->
                            <?php if (!$is_overdue): ?>
                            <form method="post" action="edit_assignment.php" enctype="multipart/form-data" class="d-flex gap-2 align-items-center mb-0">
                                <input type="hidden" name="assignment_id" value="<?php echo $a['assignment_id']; ?>">
                                <input type="file" name="file" required accept=".pdf,.doc,.docx,.zip,.rar" class="form-control form-control-sm" style="width:140px;">
                                <button type="submit" class="btn btn-warning btn-sm">Edit</button>
                            </form>
                            <?php endif; ?>
                            <!-- Withdraw button: allows removing submission only if not overdue -->
                            <?php if (!$is_overdue): ?>
                            <form method="post" action="delete_assignment.php" class="mb-0">
                                <input type="hidden" name="assignment_id" value="<?php echo $a['assignment_id']; ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                            </form>
                            <?php endif; ?>
                            <?php if ($is_overdue): ?>
                                <span class="text-danger fw-bold ms-2">Late Submission</span>
                            <?php endif; ?>
                        </div>
                    <?php elseif ($a['file_path']): ?>
                        <span class="text-success">Submitted</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
