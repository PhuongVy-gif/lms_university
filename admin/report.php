<?php
session_start();
require_once __DIR__ . '/../config/database.php';
if (!isset($conn)) {
    $conn = new mysqli('localhost', 'root', '', 'lms_university');
    if ($conn->connect_error) {
  die('Database connection failed: ' . $conn->connect_error);
    }
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Xử lý filter ngày (giữ nguyên giao diện)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Real statistics from database
$months = [];
$revenues = [];
$orders_count = [];
$month_labels = [];
for ($i = 11; $i >= 0; $i--) {
  $month = date('Y-m', strtotime("-$i months"));
  $months[] = $month;
  $month_labels[] = date('M Y', strtotime("-$i months"));
  $sql = "SELECT COUNT(*) as cnt FROM enrollments WHERE DATE_FORMAT(enrollment_date, '%Y-%m') = '$month'";
  $res = $conn->query($sql);
  $row = $res ? $res->fetch_assoc() : ['cnt'=>0];
  $orders_count[] = (int)$row['cnt'];
}

// Top 5 courses by enrollment
$top_courses = [];
$sql = "SELECT c.title as name, COUNT(e.id) as qty FROM courses c JOIN enrollments e ON c.id = e.course_id GROUP BY c.id ORDER BY qty DESC LIMIT 5";
$res = $conn->query($sql);
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $top_courses[] = ['name' => $row['name'], 'qty' => (int)$row['qty']];
  }
}

// Real enrollments list (latest 20)
$registrations = [];
$sql = "SELECT e.id, u.first_name, u.last_name, c.title as course, e.status, e.enrollment_date FROM enrollments e JOIN users u ON e.student_id = u.id JOIN courses c ON e.course_id = c.id ORDER BY e.enrollment_date DESC LIMIT 20";
$res = $conn->query($sql);
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $registrations[] = [
      'id' => $row['id'],
      'student' => $row['first_name'] . ' ' . $row['last_name'],
      'course' => $row['course'],
      'status' => $row['status'],
      'registration_date' => $row['enrollment_date'],
    ];
  }
}

// Status labels and counts
$status_labels = ['Enrolled', 'Completed', 'Dropped'];
$status_counts = [];
// Map status values in DB to display labels
$status_map = [
  'enrolled' => 'Enrolled',
  'completed' => 'Completed',
  'dropped' => 'Dropped'
];
$status_counts = [0, 0, 0];
$sql = "SELECT status, COUNT(*) as cnt FROM enrollments GROUP BY status";
$res = $conn->query($sql);
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $label = $status_map[strtolower($row['status'])] ?? null;
    if ($label) {
      $idx = array_search($label, $status_labels);
      if ($idx !== false) $status_counts[$idx] = (int)$row['cnt'];
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Statistics Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
</head>
<body>
<?php include __DIR__ . './header_admin.php'; ?>

<div class="container-fluid">
 
    <main class="col-12 col-md-10 px-4 py-4" style="margin-left:220px; width:calc(100% - 220px);">
      <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
          <h1 class="fw-bold mb-1" style="font-size:2.2rem">
              <i class="fas fa-chart-bar text-primary me-2"></i> Course Enrollment Report
          </h1>
          <div class="text-muted" style="font-size:1.1rem;">Overview of course registrations and statistics</div>
        </div>
      </div>
  <form class="row g-3 mb-4" method="get">
        <div class="col-auto">
          <label for="start_date" class="col-form-label">From date</label>
        </div>
        <div class="col-auto">
          <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
        </div>
        <div class="col-auto">
          <label for="end_date" class="col-form-label">To date</label>
        </div>
        <div class="col-auto">
          <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
        </div>
  </form>
    <div class="row mb-4">
        <div class="col-lg-6">
          <div class="card shadow-sm border-0 p-3 mb-4">
            <h5 class="fw-bold mb-3">Enrollment Count (Last 12 Months)</h5>
            <canvas id="ordersChart" height="120"></canvas>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card shadow-sm border-0 p-3 mb-4">
            <h5 class="fw-bold mb-3">Top 5 Best-Selling Courses</h5>
            <canvas id="topCourseChart" height="220"></canvas>
          </div>
          <div class="card shadow-sm border-0 p-3 mb-4">
            <h5 class="fw-bold mb-3">Course Participation Ratio (Enrollment Status)</h5>
            <canvas id="statusPieChart" height="180"></canvas>
          </div>
        </div>
  </div>
  <div class="card shadow-sm border-0 p-3 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold mb-0">Recent Course Registrations</h5>
          <div>
            <button id="exportCsv" class="btn btn-outline-success btn-sm me-2"><i class="fa fa-file-csv"></i> Export CSV</button>
            <button id="exportPdf" class="btn btn-outline-danger btn-sm"><i class="fa fa-file-pdf"></i> Export PDF</button>
          </div>
        </div>
        <div class="table-responsive">
          <table id="registrationsTable" class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Student</th>
                <th>Course</th>
                <th>Status</th>
                <th>Registration Date</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($registrations)) {
                foreach($registrations as $r) { ?>
                  <tr>
                    <td><?php echo $r['id']; ?></td>
                    <td><?php echo htmlspecialchars($r['student']); ?></td>
                    <td><?php echo htmlspecialchars($r['course']); ?></td>
                    <td>
          <?php if($r['status']=='processing') echo '<span class="badge bg-warning">Processing</span>';
            elseif($r['status']=='completed') echo '<span class="badge bg-success">Completed</span>';
            elseif($r['status']=='cancelled') echo '<span class="badge bg-danger">Cancelled</span>';
            else echo htmlspecialchars($r['status']); ?>
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($r['registration_date'])); ?></td>
                  </tr>
              <?php }
              } else { ?>
                <tr>
                  <td class="text-center" colspan="5">No data available</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</div>
<script>
// Enrollment Count (Last 12 Months)
const ordersCtx = document.getElementById('ordersChart').getContext('2d');
new Chart(ordersCtx, {
  type: 'bar',
  data: {
    labels: <?php echo json_encode($month_labels); ?>,
    datasets: [{
      label: 'Enrollment Count',
      data: <?php echo json_encode($orders_count); ?>,
      backgroundColor: '#28a745'
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true, stepSize: 1 } }
  }
});
// Top 5 most enrolled courses
const topCourseCtx = document.getElementById('topCourseChart').getContext('2d');
new Chart(topCourseCtx, {
  type: 'bar',
  data: {
    labels: <?php echo json_encode(array_column($top_courses,'name')); ?>,
    datasets: [{
      label: 'Enrollment Count',
      data: <?php echo json_encode(array_column($top_courses,'qty')); ?>,
      backgroundColor: '#fd7e14'
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    indexAxis: 'y',
    scales: { x: { beginAtZero: true, stepSize: 1 } }
  }
});
// Pie chart for enrollment status
const statusPieCtx = document.getElementById('statusPieChart').getContext('2d');
new Chart(statusPieCtx, {
  type: 'pie',
  data: {
    labels: <?php echo json_encode($status_labels); ?>,
    datasets: [{
      data: <?php echo json_encode($status_counts); ?>,
      backgroundColor: ['#ffc107','#28a745','#dc3545']
    }]
  },
  options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

// DataTables + Export
$(document).ready(function() {
  if ($('#registrationsTable tbody tr').length > 0 && $('#registrationsTable tbody tr td').length === 5) {
    var table = $('#registrationsTable').DataTable({
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/en-GB.json'
      },
      dom: 'Bfrtip',
      buttons: [
        {
          extend: 'csvHtml5',
          text: '<i class="fa fa-file-csv"></i> CSV',
          className: 'btn btn-outline-success btn-sm',
          title: 'registrations_report',
          exportOptions: { columns: ':visible' }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fa fa-file-pdf"></i> PDF',
          className: 'btn btn-outline-danger btn-sm',
          title: 'registrations_report',
          orientation: 'landscape',
          pageSize: 'A4',
          exportOptions: { columns: ':visible' }
        }
      ],
      ordering: true,
      order: [[3, 'desc']]
    });
    $('#exportCsv').on('click', function() { table.button('.buttons-csv').trigger(); });
    $('#exportPdf').on('click', function() { table.button('.buttons-pdf').trigger(); });
  }
});
</script>
</body>
</html>
