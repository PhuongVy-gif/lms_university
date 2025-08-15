<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
  header('Location: ../login.php');
  exit();
}
$conn = new mysqli('localhost', 'root', '', 'lms_university');
if ($conn->connect_error) {
  die('Database connection failed: ' . $conn->connect_error);
}
// Handle delete assignment
$assignment_delete_msg = '';
if (isset($_POST['delete_assignment_id'])) {
  $delete_id = intval($_POST['delete_assignment_id']);
  // Get file path to delete file
  $file_q = $conn->query("SELECT update_file FROM assignments WHERE id = $delete_id");
  if ($file_q && $file_q->num_rows > 0) {
    $file_row = $file_q->fetch_assoc();
    $file_path = $file_row['update_file'];
    if (!empty($file_path) && file_exists(__DIR__ . '/../../uploads/' . $file_path)) {
      @unlink(__DIR__ . '/../../uploads/' . $file_path);
    }
  }
  $conn->query("DELETE FROM assignments WHERE id = $delete_id");
  $assignment_delete_msg = '<div class="alert alert-success">Assignment deleted successfully!</div>';
}
// Handle add/edit assignment (modal Save button)
$assignment_msg = '';
if (isset($_POST['save_assignment'])) {
  $assignment_id = isset($_POST['assignment_id']) ? intval($_POST['assignment_id']) : 0;
  $assignment_title = mysqli_real_escape_string($conn, $_POST['assignment_title']);
  $assignment_desc = mysqli_real_escape_string($conn, $_POST['assignment_desc']);
  $assignment_course = intval($_POST['assignment_course']);
  $assignment_due = !empty($_POST['assignment_due']) ? mysqli_real_escape_string($conn, $_POST['assignment_due']) : null;
  $assignment_points = isset($_POST['assignment_points']) ? floatval($_POST['assignment_points']) : 100.00;
  $created_by = $_SESSION['user_id'];
  $update_file = '';
  $file_uploaded = false;
  if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['assignment_file'];
    $file_name_orig = basename($file['name']);
    $file_tmp = $file['tmp_name'];
    $ext = pathinfo($file_name_orig, PATHINFO_EXTENSION);
    $allowed = ['pdf','doc','docx','zip','rar','ppt','pptx','xls','xlsx','txt'];
    if (in_array(strtolower($ext), $allowed)) {
      $update_file = 'assignment_' . time() . '_' . rand(1000,9999) . '.' . $ext;
      $dest = '../../uploads/' . $update_file;
      if (move_uploaded_file($file_tmp, $dest)) {
        $file_uploaded = true;
      } else {
        $assignment_msg = '<div class="alert alert-danger">File upload failed.</div>';
      }
    } else {
      $assignment_msg = '<div class="alert alert-warning">Invalid file type. Allowed: PDF, DOC, DOCX, ZIP, RAR, PPT, PPTX, XLS, XLSX, TXT.</div>';
    }
  }
  if (empty($assignment_msg)) {
    if ($assignment_id > 0) {
      // Edit existing
      $set = "title='$assignment_title', description='$assignment_desc', course_id=$assignment_course, due_date=" . ($assignment_due ? "'$assignment_due'" : 'NULL') . ", max_points=$assignment_points";
      if ($file_uploaded) {
        $set .= ", update_file='$update_file'";
      }
      $sql = "UPDATE assignments SET $set WHERE id=$assignment_id";
      if ($conn->query($sql)) {
        $assignment_msg = '<div class="alert alert-success">Assignment updated successfully!</div>';
      } else {
        $assignment_msg = '<div class="alert alert-danger">Database error: ' . htmlspecialchars($conn->error) . '</div>';
      }
    } else {
      // Add new
      $file_col = $file_uploaded ? ', update_file' : '';
      $file_val = $file_uploaded ? ', ?' : '';
      $query = "INSERT INTO assignments (course_id, title, description, due_date, max_points, created_by$file_col) VALUES (?, ?, ?, ?, ?, ?$file_val)";
      $stmt = $conn->prepare($query);
      if ($stmt) {
        if ($file_uploaded) {
          // Corrected bind_param types: update_file is string, created_by is int
          $stmt->bind_param('isssdis', $assignment_course, $assignment_title, $assignment_desc, $assignment_due, $assignment_points, $created_by, $update_file);
        } else {
          $stmt->bind_param('isssdi', $assignment_course, $assignment_title, $assignment_desc, $assignment_due, $assignment_points, $created_by);
        }
        if ($stmt->execute()) {
          $assignment_msg = '<div class="alert alert-success">Assignment added successfully!</div>';
        } else {
          $assignment_msg = '<div class="alert alert-danger">Database error: ' . htmlspecialchars($stmt->error) . '</div>';
        }
        $stmt->close();
      } else {
        $assignment_msg = '<div class="alert alert-danger">Prepare failed: ' . htmlspecialchars($conn->error) . '</div>';
      }
    }
  }
}

// Get list of assignments
$instructor_id = $_SESSION['user_id'];
$sql = "SELECT a.id as assignment_id, a.title, a.description, a.due_date, a.max_points, a.update_file, c.title as course_title, c.id as course_id FROM assignments a JOIN courses c ON a.course_id = c.id WHERE c.instructor_id = $instructor_id ORDER BY a.due_date DESC";
$result = $conn->query($sql);
$assignments = [];
$course_ids = [];
if ($result) {
  while ($row = $result->fetch_assoc()) {
  $assignments[] = $row;
  $course_ids[$row['course_id']] = $row['course_title'];
  }
}

$upload_msg = '';
// Handle delete course material
if (isset($_POST['delete_material_id'])) {
  $delete_id = intval($_POST['delete_material_id']);
  // Get file path to delete file
  $file_q = $conn->query("SELECT file_path FROM course_materials WHERE id = $delete_id");
  if ($file_q && $file_q->num_rows > 0) {
    $file_row = $file_q->fetch_assoc();
    $file_path = $file_row['file_path'];
    if (!empty($file_path) && file_exists(__DIR__ . '/../../uploads/' . $file_path)) {
      @unlink(__DIR__ . '/../../uploads/' . $file_path);
    }
  }
  $conn->query("DELETE FROM course_materials WHERE id = $delete_id");
  $upload_msg = '<div class="alert alert-success">Material deleted successfully!</div>';
}

// Handle add/edit course material (modal Save button)
$upload_msg = '';
if (isset($_POST['save_material'])) {
  $material_id = isset($_POST['material_id']) ? intval($_POST['material_id']) : 0;
  $material_title = mysqli_real_escape_string($conn, $_POST['material_title']);
  $material_desc = mysqli_real_escape_string($conn, $_POST['material_desc']);
  $material_course = intval($_POST['material_course']);
  $material_due = !empty($_POST['material_due']) ? mysqli_real_escape_string($conn, $_POST['material_due']) : null;
  $uploaded_by = $_SESSION['user_id'];
  $file_uploaded = false;
  $file_name = $file_type = '';
  $file_size = 0;
  if (isset($_FILES['material_file']) && $_FILES['material_file']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['material_file'];
    $file_name_orig = basename($file['name']);
    $file_tmp = $file['tmp_name'];
    $file_type = $file['type'];
    $file_size = $file['size'];
    $ext = pathinfo($file_name_orig, PATHINFO_EXTENSION);
    $allowed = ['pdf','doc','docx','zip','rar','ppt','pptx','xls','xlsx','txt'];
    if (in_array(strtolower($ext), $allowed)) {
      $file_name = 'material_' . time() . '_' . rand(1000,9999) . '.' . $ext;
      $dest = '../../uploads/' . $file_name;
      if (move_uploaded_file($file_tmp, $dest)) {
        $file_uploaded = true;
      } else {
        $upload_msg = '<div class="alert alert-danger">File upload failed.</div>';
      }
    } else {
      $upload_msg = '<div class="alert alert-warning">Invalid file type. Allowed: PDF, DOC, DOCX, ZIP, RAR, PPT, PPTX, XLS, XLSX, TXT.</div>';
    }
  }
  if (empty($upload_msg)) {
    if ($material_id > 0) {
      // Edit existing
      $set = "title='$material_title', description='$material_desc', course_id=$material_course";
      if ($material_due !== null) {
        $set .= ", due_date='$material_due'";
      }
      if ($file_uploaded) {
        $set .= ", file_path='$file_name', file_type='$file_type', file_size=$file_size";
      }
      $sql = "UPDATE course_materials SET $set WHERE id=$material_id";
      if ($conn->query($sql)) {
        $upload_msg = '<div class="alert alert-success">Material updated successfully!</div>';
      } else {
        $upload_msg = '<div class="alert alert-danger">Database error: ' . htmlspecialchars($conn->error) . '</div>';
      }
    } else {
      // Add new
      if ($file_uploaded) {
        $stmt = $conn->prepare("INSERT INTO course_materials (course_id, title, description, file_path, file_type, file_size, uploaded_by, due_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
          $stmt->bind_param('issssiss', $material_course, $material_title, $material_desc, $file_name, $file_type, $file_size, $uploaded_by, $material_due);
          if ($stmt->execute()) {
            $upload_msg = '<div class="alert alert-success">Material uploaded successfully!</div>';
          } else {
            $upload_msg = '<div class="alert alert-danger">Database error: ' . htmlspecialchars($stmt->error) . '</div>';
          }
          $stmt->close();
        } else {
          $upload_msg = '<div class="alert alert-danger">Prepare failed: ' . htmlspecialchars($conn->error) . '</div>';
        }
      } else {
        $upload_msg = '<div class="alert alert-warning">File is required for new material.</div>';
      }
    }
  }
}

// Fetch uploaded materials for instructor's courses
$materials = [];
if (!empty($course_ids)) {
  $ids = implode(',', array_keys($course_ids));
  $mat_result = $conn->query("SELECT m.*, c.title as course_title FROM course_materials m JOIN courses c ON m.course_id = c.id WHERE m.course_id IN ($ids) ORDER BY m.upload_date DESC");
  if ($mat_result) {
    while ($row = $mat_result->fetch_assoc()) {
      $materials[] = $row;
    }
  }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Assignment Management</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/13c47b287c.js" crossorigin="anonymous"></script>
</head>
<body style="background:#f6f8fa;">
<?php include '../header_instructor.php'; ?>

<!-- header đã được include ở trên, phần giao diện sidebar bị xóa theo yêu cầu -->
<div style="margin-left:220px">
<div class="px-4 py-4">
  <?php if (!empty($assignment_delete_msg)) echo $assignment_delete_msg; ?>
  <?php if (!empty($assignment_msg)) echo $assignment_msg; ?>
      <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
          <h2 class="fw-bold mb-2 text-primary"><i class="fa-solid fa-book-open me-2"></i>Assignment Management</h2>
          <div class="text-muted" style="font-size:1.1rem;">View, add, edit, and delete assignments for your courses.</div>
        </div>
      </div>


      <!-- Modal for Add/Edit Assignment -->
      <div class="modal fade" id="assignmentModal" tabindex="-1" aria-labelledby="assignmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="post" id="assignmentForm" enctype="multipart/form-data">
              <div class="modal-header">
                <h5 class="modal-title" id="assignmentModalLabel">Add/Edit Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="assignment_id" id="assignment_id">
                <div class="mb-3">
                  <label class="form-label">Course</label>
                  <select name="assignment_course" id="assignment_course" class="form-select" required>
                    <option value="">Select Course</option>
                    <?php foreach ($course_ids as $cid => $ctitle): ?>
                      <option value="<?php echo $cid; ?>"><?php echo htmlspecialchars($ctitle); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Title</label>
                  <input type="text" name="assignment_title" id="assignment_title" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Description</label>
                  <input type="text" name="assignment_desc" id="assignment_desc" class="form-control">
                </div>
                <div class="mb-3">
                  <label class="form-label">Due Date</label>
                  <input type="datetime-local" name="assignment_due" id="assignment_due" class="form-control">
                </div>
                <div class="mb-3">
                  <label class="form-label">Max Points</label>
                  <input type="number" name="assignment_points" id="assignment_points" class="form-control" min="0" max="1000" step="0.01" value="100.00">
                </div>
                <div class="mb-3">
                  <label class="form-label">Update File</label>
                  <input type="file" name="assignment_file" id="assignment_file" class="form-control">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="save_assignment" class="btn btn-primary">Save</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <script>
      // Fill modal for edit assignment
      function editAssignment(id, course, title, desc, due, points) {
          document.getElementById('assignment_id').value = id;
          document.getElementById('assignment_course').value = course;
          document.getElementById('assignment_title').value = title;
          document.getElementById('assignment_desc').value = desc;
          // Format due date to 'YYYY-MM-DDTHH:MM' for datetime-local input
          if (due && due.length > 0) {
            let dt = due.replace(' ', 'T');
            // If seconds exist, remove them
            if (/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/.test(dt)) {
              dt = dt.substring(0, 16);
            }
            document.getElementById('assignment_due').value = dt;
          } else {
            document.getElementById('assignment_due').value = '';
          }
          document.getElementById('assignment_points').value = points;
          var modal = new bootstrap.Modal(document.getElementById('assignmentModal'));
          modal.show();
      }
      function addAssignment() {
        document.getElementById('assignmentForm').reset();
        document.getElementById('assignment_id').value = '';
        document.getElementById('assignment_points').value = '100.00';
        var modal = new bootstrap.Modal(document.getElementById('assignmentModal'));
        modal.show();
      }
      </script>

      <div class="d-flex justify-content-end mb-2">
        <button class="btn btn-success" onclick="addAssignment()"><i class="fa fa-plus"></i> Add Assignment</button>
      </div>

      <!-- List of Assignments -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white fw-bold text-primary" style="font-size:1.1rem;"><i class="fa-solid fa-list-ul me-2"></i>Assignments</div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th class="text-center">#</th>
                  <th>Title</th>
                  <th>Description</th>
                  <th>Course</th>
                  <th>Due Date</th>
                  <th>Max Points</th>
                  <th>Update File</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php $ai=1; foreach ($assignments as $a): ?>
                <tr>
                  <td class="text-center fw-bold text-secondary"><?php echo $ai++; ?></td>
                  <td class="fw-bold text-primary"><?php echo htmlspecialchars($a['title']); ?></td>
                  <td><?php echo htmlspecialchars($a['description']); ?></td>
                  <td><span class="badge bg-info text-dark px-3 py-2"><?php echo htmlspecialchars($a['course_title']); ?></span></td>
                  <td><?php echo isset($a['due_date']) ? htmlspecialchars($a['due_date']) : '-'; ?></td>
                  <td><?php echo htmlspecialchars($a['max_points']); ?></td>
                  <td>
                    <?php if (!empty($a['update_file'])): ?>
                      <a href="../../uploads/<?php echo htmlspecialchars($a['update_file']); ?>" class="btn btn-outline-primary btn-sm" download>Download</a>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </td>
                  <td class="action-btns">
                    <div class="d-flex align-items-center">
                      <button class="btn btn-primary btn-sm" onclick="editAssignment('<?php echo $a['assignment_id']; ?>','<?php echo $a['course_id']; ?>','<?php echo htmlspecialchars(addslashes($a['title'])); ?>','<?php echo htmlspecialchars(addslashes($a['description'])); ?>','<?php echo isset($a['due_date']) ? $a['due_date'] : ''; ?>','<?php echo $a['max_points']; ?>')"><i class="fa fa-edit"></i></button>
                      <form method="post" action="" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this assignment?');" class="ms-2 mb-0">
                        <input type="hidden" name="delete_assignment_id" value="<?php echo $a['assignment_id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                      </form>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal fade" id="materialModal" tabindex="-1" aria-labelledby="materialModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="post" enctype="multipart/form-data" id="materialForm">
              <div class="modal-header">
                <h5 class="modal-title" id="materialModalLabel">Add/Edit Course Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="material_id" id="material_id">
                <div class="mb-3">
                  <label class="form-label">Course</label>
                  <select name="material_course" id="material_course" class="form-select" required>
                    <option value="">Select Course</option>
                    <?php foreach ($course_ids as $cid => $ctitle): ?>
                      <option value="<?php echo $cid; ?>"><?php echo htmlspecialchars($ctitle); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Title</label>
                  <input type="text" name="material_title" id="material_title" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Description</label>
                  <input type="text" name="material_desc" id="material_desc" class="form-control">
                </div>
                <div class="mb-3">
                  <label class="form-label">Due Date</label>
                  <input type="datetime-local" name="material_due" id="material_due" class="form-control">
                </div>
                <div class="mb-3">
                  <label class="form-label">File</label>
                  <input type="file" name="material_file" id="material_file" class="form-control">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="save_material" class="btn btn-primary">Save</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <script>
      // Fill modal for edit
      function editMaterial(id, course, title, desc, due) {
          document.getElementById('material_id').value = id;
          document.getElementById('material_course').value = course;
          document.getElementById('material_title').value = title;
          document.getElementById('material_desc').value = desc;
          // Format due date to 'YYYY-MM-DDTHH:MM' for datetime-local input
          if (due && due.length > 0) {
            let dt = due.replace(' ', 'T');
            if (/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/.test(dt)) {
              dt = dt.substring(0, 16);
            }
            document.getElementById('material_due').value = dt;
          } else {
            document.getElementById('material_due').value = '';
          }
          document.getElementById('material_file').required = false;
          var modal = new bootstrap.Modal(document.getElementById('materialModal'));
          modal.show();
      }
      function addMaterial() {
        document.getElementById('materialForm').reset();
        document.getElementById('material_id').value = '';
        document.getElementById('material_file').required = true;
        var modal = new bootstrap.Modal(document.getElementById('materialModal'));
        modal.show();
      }
      </script>

     

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
