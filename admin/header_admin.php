<style>
.navbar {
  min-height: 48px !important;
  padding-top: 2px !important;
  padding-bottom: 2px !important;
}
.navbar-brand span {
  font-size: 1.1rem !important;
}
.admin-avatar {
  width: 32px !important;
  height: 32px !important;
  border-radius: 50%;
  object-fit: cover;
}
</style>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top" style="margin-left:220px; width:calc(100% - 220px)">
  <div class="container-fluid py-1">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <i class="fas fa-graduation-cap fa-lg text-primary me-2"></i>
      <span class="fw-bold">LMS Admin</span>
    </a>
    <div class="ms-auto d-flex align-items-center gap-2">
      <form class="d-none d-md-flex" method="get" action="dashboard.php" style="margin-bottom:0;">
        <input class="form-control form-control-sm me-2" type="search" name="q" placeholder="Search..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" style="height:28px; font-size:0.95rem;">
      </form>
      <span class="d-none d-md-inline text-secondary small me-1" style="font-size:0.95rem;">
         <?php 
           echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin'; 
         ?>
      </span>
      <img src="../assets/images/admin.jpg" class="admin-avatar" alt="avatar">
  <a href="/logout.php" class="btn btn-outline-danger btn-sm ms-1"><i class="fas fa-sign-out-alt"></i></a>
    </div>
  </div>
</nav>
<?php
// Sidebar for admin dashboard
?>
<style>
.sidebar {
  background: #f8f9fa;
  min-height: 100vh;
  color: #222;
  border-right: 1px solid #e3e6ef;
}
.sidebar .nav-link {
  color: #222 !important;
  font-weight: 500;
  border-radius: 8px;
  margin-bottom: 4px;
  padding: 9px 16px;
  transition: background 0.2s, color 0.2s;
  display: flex;
  align-items: center;
  font-size: 1rem;
}
.sidebar .nav-link.active, .sidebar .nav-link:hover {
  background: #e3e6ef;
  color: #007bff !important;
}
.sidebar .nav-link i {
  color: #007bff;
  font-size: 1.15rem;
  margin-right: 10px;
}
.sidebar .nav-title {
  margin-left: 8px;
  margin-bottom: 2px;
}
.sidebar .nav-link:last-child {
  margin-top: 18px;
  color: #ff4d4f !important;
}
@media (max-width: 991px) {
  .sidebar {
    min-height: auto;
    padding-bottom: 24px;
  }
}
</style>
<?php
$base_url = '';
?>
<nav class="sidebar d-none d-md-block p-0" style="width:220px; min-width:220px; max-width:220px; position:fixed; top:0; left:0; height:100vh;">
  <div class="position-sticky">
    <ul class="nav flex-column">
      <li class="nav-item"><a class="nav-link" href="/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="nav-title mt-3 mb-1 text-uppercase text-muted small" style="letter-spacing:1px; font-size:0.92rem;">User Management</li>
    <li class="nav-item"><a class="nav-link" href="/admin/users/manage-user.php"><i class="fas fa-users"></i> Manage Users</a></li>
    <li class="nav-item"><a class="nav-link" href="/admin/instructor/manage-instructor.php"><i class="fas fa-chalkboard-teacher"></i> Manage Instructors</a></li>
    <li class="nav-title mt-3 mb-1 text-uppercase text-muted small" style="letter-spacing:1px; font-size:0.92rem;">Course Management</li>
    <li class="nav-item"><a class="nav-link" href="/admin/courses/manage-courses.php"><i class="fas fa-book"></i> Manage Courses</a></li>
    <li class="nav-title mt-3 mb-1 text-uppercase text-muted small" style="letter-spacing:1px; font-size:0.92rem;">System</li>
    <li class="nav-item"><a class="nav-link" href="/admin/report.php"><i class="fas fa-chart-bar"></i> Reports & Statistics</a></li>
    <li class="nav-item"><a class="nav-link" href="/admin/settings.php"><i class="fas fa-cog"></i> Settings</a></li>
  <li class="nav-item"><a class="nav-link" href="/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </div>
</nav>
