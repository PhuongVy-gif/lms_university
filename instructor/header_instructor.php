<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    header('Location: ../login.php');
    exit();
}
?>
<script src="https://kit.fontawesome.com/13c47b287c.js" crossorigin="anonymous"></script>
<style>
.navbar {
  min-height: 48px !important;
  padding: 0 !important;
  background: #fff;
  border-bottom: 1px solid #e3e6ef;
  box-shadow: 0 1px 4px rgba(0,0,0,0.03);
  border-radius: 0;
  margin-top: 0 !important;
}
.navbar-brand span {
  font-size: 1.13rem !important;
}
.instructor-avatar {
  width: 34px !important;
  height: 34px !important;
  border-radius: 50%;
  object-fit: cover;
  background: #f1f3f6;
  border: 1.5px solid #e3e6ef;
  box-shadow: 0 1px 2px rgba(0,0,0,0.04);
}
.navbar-nav .nav-link {
  padding-left: 14px !important;
  padding-right: 14px !important;
  font-size: 1.05rem;
}
.navbar .form-control {
  min-width: 210px;
  max-width: 260px;
  font-size: 1rem;
  height: 34px;
  padding: 4px 12px;
  border-radius: 8px;
  background: #f8f9fa;
  border: 1px solid #e3e6ef;
  box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}
.navbar .btn-outline-danger {
  padding: 4px 10px;
  font-size: 1.15rem;
  border-radius: 8px;
  margin-left: 8px;
}
.navbar .navbar-collapse {
  gap: 0 !important;
}
.navbar .navbar-brand {
  margin-right: 16px !important;
}
.navbar .user-info {
  display: flex;
  align-items: center;
  gap: 8px;
}
.sidebar {
  background: #f8f9fa;
  min-height: 100vh;
  color: #222;
  border-right: 1px solid #e3e6ef;
  width: 220px !important;
  min-width: 220px;
  max-width: 220px;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1040;
  padding-top: 48px;
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
  color: #1976d2 !important;
}
.sidebar .nav-link i {
  color: #1976d2;
  font-size: 1.15rem;
  margin-right: 10px;
}
.sidebar .nav-title {
  margin-left: 8px;
  margin-bottom: 2px;
  text-transform: uppercase;
  font-size: 0.92rem;
  color: #888;
  letter-spacing: 1px;
}
@media (max-width: 991px) {
  .sidebar {
    min-height: auto;
    padding-bottom: 24px;
    position: static;
    width: 100% !important;
    max-width: 100%;
    border-right: none;
    border-bottom: 1px solid #e3e6ef;
  }
}
</style>
<nav class="navbar navbar-expand-lg navbar-light sticky-top" style="z-index:1050; margin-bottom:0; margin-top:0; margin-left:6cm;">
  <div class="container-fluid" style="padding-left:8px; padding-right:8px; min-height:48px; margin-top:0;">
    <a class="navbar-brand d-flex align-items-center mb-0" href="/instructor/dashboard.php">
      <i class="fa-solid fa-graduation-cap fa-lg text-primary me-2"></i>
      <span class="fw-bold">LMS Instructor</span>
    </a>
    <ul class="navbar-nav align-items-center">
      <li class="nav-item">
        <a class="nav-link fw-semibold" href="/instructor/dashboard.php">Dashboard</a>
      </li>
      <li class="nav-item">
        <a class="nav-link fw-semibold" href="/instructor/my-courses/my-courses.php">Courses</a>
      </li>
    </ul>
    <div class="flex-grow-1"></div>
    <div class="user-info ms-3">
      <span class="d-none d-md-inline text-secondary small" style="font-size:1rem;">
        <?php echo isset($_SESSION['last_name']) ? htmlspecialchars($_SESSION['last_name']) : 'Instructor'; ?>
      </span>
      <img src="../../assets/images/admin.jpg" class="instructor-avatar" alt="avatar">
      <a href="../../logout.php" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt"></i></a>
    </div>
  </div>
</nav>
<nav class="sidebar d-none d-md-block p-0">
  <div class="position-sticky">
    <ul class="nav flex-column">
      <li class="nav-item"><a class="nav-link" href="/instructor/dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
      <li class="nav-title mt-3 mb-1">Course Management</li>
      <li class="nav-item"><a class="nav-link" href="/instructor/my-courses/my-courses.php"><i class="fa-solid fa-book"></i> My Courses</a></li>
      <li class="nav-item"><a class="nav-link" href="/instructor/Assignment/assignments.php"><i class="fa-solid fa-list-check"></i> Assignments</a></li>
      <li class="nav-item"><a class="nav-link" href="#"><i class="fa-regular fa-circle-question"></i> Quizzes</a></li>
      <li class="nav-item"><a class="nav-link" href="/instructor/grading.php"><i class="fa-solid fa-clipboard-check"></i> Grading</a></li>
      <li class="nav-title mt-3 mb-1">Content Management</li>
      <li class="nav-item"><a class="nav-link" href="#"><i class="fa-regular fa-file-lines"></i> Course Materials</a></li>
      <li class="nav-item"><a class="nav-link" href="/instructor/manage-courses.php"><i class="fa-regular fa-comments"></i> Forum Management</a></li>
      <li class="nav-title mt-3 mb-1">Analytics</li>
      <li class="nav-item"><a class="nav-link" href="#"><i class="fa-regular fa-chart-bar"></i> Course Analytics</a></li>
      <li class="nav-item"><a class="nav-link" href="#"><i class="fa-regular fa-file-alt"></i> Reports</a></li>
      <li class="nav-title mt-3 mb-1">Account</li>
      <li class="nav-item"><a class="nav-link" href="/instructor/profile.php"><i class="fa-regular fa-user"></i> Profile</a></li>
      <li class="nav-item"><a class="nav-link" href="#"><i class="fa-solid fa-gear"></i> Settings</a></li>
      <li class="nav-item"><a class="nav-link" href="../../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </div>
</nav>


