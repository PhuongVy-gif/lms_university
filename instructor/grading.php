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
$instructor_id = $_SESSION['user_id'];
// Get the list of student submissions for assignments in the instructor's courses
$sql = "SELECT s.id as submission_id, s.assignment_id, s.student_id, s.file_path, s.submitted_at, s.grade, s.feedback, s.graded_by, s.graded_at, a.title as assignment_title, u.username as student_name, g.username as grader_name FROM assignment_submissions s JOIN assignments a ON s.assignment_id = a.id JOIN courses c ON a.course_id = c.id JOIN users u ON s.student_id = u.id LEFT JOIN users g ON s.graded_by = g.id WHERE c.instructor_id = $instructor_id ORDER BY s.submitted_at DESC";
$result = $conn->query($sql);
$submissions = [];
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $submissions[] = $row;
  }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grading Submissions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
    <script src="https://kit.fontawesome.com/13c47b287c.js" crossorigin="anonymous"></script>
</head>
<body style="background:#f6f8fa;">
<?php include '../instructor/header_instructor.php'; ?>

<div class="px-4 pt-2 pb-4" style="margin-left:6cm;">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <h2 class="fw-bold mb-2 text-primary"><i class="fa-solid fa-clipboard-check me-2"></i>Grade Submissions</h2>
      <div class="text-muted" style="font-size:1.1rem;">View and grade student submissions for assignments in your courses.</div>
    </div>
  </div>
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white fw-bold text-primary" style="font-size:1.1rem;"><i class="fa-solid fa-list-ul me-2"></i>Submission List</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Student</th>
              <th>Assignment</th>
              <th>Submission File</th>
              <th>Submitted At</th>
              <th>Grade</th>
              <th>Feedback</th>
              <th>Grader</th>
              <th>Grade</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; foreach ($submissions as $sub): ?>
            <tr>
              <td class="fw-bold text-secondary"><?php echo $i++; ?></td>
              <td class="fw-bold text-primary"><?php echo htmlspecialchars($sub['student_name']); ?></td>
              <td><?php echo htmlspecialchars($sub['assignment_title']); ?></td>
              <td>
                <a href="../uploads/<?php echo htmlspecialchars($sub['file_path']); ?>" target="_blank" class="btn btn-info btn-sm"><i class="fa-solid fa-file-arrow-down"></i> View File</a>
              </td>
              <td><span class="badge bg-warning text-dark px-3 py-2" style="font-size:1rem;"><i class="fa-solid fa-calendar-day me-1"></i><?php echo htmlspecialchars($sub['submitted_at']); ?></span></td>
              <td>
                <?php echo !empty($sub['grade']) ? '<span class="badge bg-success">'.htmlspecialchars($sub['grade']).'</span>' : '<span class="text-muted">-</span>'; ?>
              </td>
              <td>
                <?php echo !empty($sub['feedback']) ? htmlspecialchars($sub['feedback']) : '<span class="text-muted">-</span>'; ?>
              </td>
              <td>
                <?php echo !empty($sub['grader_name']) ? htmlspecialchars($sub['grader_name']) : '<span class="text-muted">-</span>'; ?>
              </td>
              <td>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" 
                  data-bs-toggle="modal" data-bs-target="#gradeModal"
                  data-submission_id="<?php echo $sub['submission_id']; ?>"
                  data-student_name="<?php echo htmlspecialchars($sub['student_name'], ENT_QUOTES); ?>"
                  data-grade="<?php echo is_null($sub['grade']) ? '' : $sub['grade']; ?>"
                  data-feedback="<?php echo htmlspecialchars($sub['feedback'], ENT_QUOTES); ?>"
                >
                  <i class="fa-solid fa-pen-to-square"></i> Grade
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Modal for grading (only one modal) -->
            <div class="modal fade" id="gradeModal" tabindex="-1" aria-labelledby="gradeModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="gradeModalLabel">Grade Submission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form method="post" action="grade_submission.php">
                    <div class="modal-body">
                      <input type="hidden" name="submission_id" id="modalSubmissionId">
                      <div class="mb-3">
                        <label class="form-label">Student</label>
                        <input type="text" class="form-control" id="modalStudentName" readonly>
                      </div>
                      <div class="mb-3">
                        <label for="modalGradeInput" class="form-label">Grade (0-10)</label>
                        <input type="number" name="grade" min="0" max="10" class="form-control" id="modalGradeInput" required>
                      </div>
                      <div class="mb-3">
                        <label for="modalFeedbackInput" class="form-label">Feedback</label>
                        <textarea name="feedback" class="form-control" id="modalFeedbackInput" rows="2"></textarea>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Save Grade</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          </div>
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
          <script>
          // When the modal opens, transfer data from the button to the modal
          var gradeModal = document.getElementById('gradeModal');
          gradeModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var submissionId = button.getAttribute('data-submission_id');
            var studentName = button.getAttribute('data-student_name');
            var grade = button.getAttribute('data-grade');
            var feedback = button.getAttribute('data-feedback');
            document.getElementById('modalSubmissionId').value = submissionId;
            document.getElementById('modalStudentName').value = studentName;
            document.getElementById('modalGradeInput').value = grade;
            document.getElementById('modalFeedbackInput').value = feedback;
          });

          // Show success notification when the grading form is submitted
          document.querySelector('#gradeModal form').addEventListener('submit', function(e) {
            e.preventDefault();
            var form = this;
            var formData = new FormData(form);
            var submissionId = formData.get('submission_id');
            var grade = formData.get('grade');
            var feedback = formData.get('feedback');
            fetch(form.action, {
              method: 'POST',
              body: formData
            })
            .then(response => response.ok ? response.text() : Promise.reject())
            .then(() => {
              // Update grade and feedback in the table
              var gradeCell = document.querySelector('button[data-submission_id="'+submissionId+'"]')
                .closest('tr').querySelectorAll('td')[5];
              var feedbackCell = document.querySelector('button[data-submission_id="'+submissionId+'"]')
                .closest('tr').querySelectorAll('td')[6];
              gradeCell.innerHTML = '<span class="badge bg-success">'+grade+'</span>';
              feedbackCell.textContent = feedback ? feedback : '-';

              var alert = document.createElement('div');
              alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
              alert.style.zIndex = 2000;
              alert.innerHTML = 'Grade saved successfully!<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
              document.body.appendChild(alert);
              var modal = bootstrap.Modal.getInstance(gradeModal);
              modal.hide();
              setTimeout(function(){
                alert.classList.remove('show');
                alert.classList.add('hide');
                setTimeout(function(){ alert.remove(); }, 500);
              }, 1200);
            })
            .catch(() => {
              alert('An error occurred while saving the grade!');
            });
          });
          </script>
          </body>
          </html>
