<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS - Learning Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/13c47b287c.js" crossorigin="anonymous"></script>
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<!-- Student Header/Navbar at the very top -->
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
.student-avatar {
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
.navbar .ms-1, .navbar .ms-2, .navbar .ms-3 {
  margin-left: 8px !important;
}
.navbar .me-1, .navbar .me-2, .navbar .me-3 {
  margin-right: 8px !important;
}
.navbar .user-info {
  display: flex;
  align-items: center;
  gap: 8px;
}
</style>
<nav class="navbar navbar-expand-lg navbar-light sticky-top" style="z-index:1050; margin-bottom:0; margin-top:0; margin-left:-10px; width:calc(100% - 20px);">
  <div class="container-fluid" style="padding-left:8px; padding-right:8px; min-height:48px; margin-top:0;">
    <a class="navbar-brand d-flex align-items-center mb-0" href="/student/dashboard.php">
      <i class="fas fa-user-graduate fa-lg text-primary me-2"></i>
      <span class="fw-bold">LMS Student</span>
    </a>
    <ul class="navbar-nav align-items-center">
      <li class="nav-item">
        <a class="nav-link fw-semibold" href="/student/dashboard.php">Dashboard</a>
      </li>
      <li class="nav-item">
        <a class="nav-link fw-semibold" href="/student/courses.php">Courses</a>
      </li>
      <li class="nav-item">
        <a class="nav-link fw-semibold" href="/student/contact.php">Contact</a>
      </li>
    </ul>
    <div class="flex-grow-1"></div>
    <form class="d-none d-md-flex mb-0" method="get" action="/student/courses.php" style="max-width:320px; margin-left:auto;">
      <input class="form-control form-control-sm" type="search" name="q" placeholder="Search courses..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" style="flex:1;">
      <button class="btn btn-primary btn-sm ms-2" type="submit"><i class="fas fa-search"></i></button>
    </form>
    <div class="user-info ms-3">
      <span class="d-none d-md-inline text-secondary small" style="font-size:1rem;">
        <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Student'; ?>
      </span>
      <img src="../assets/images/student.jpg" class="student-avatar" alt="avatar">
      <a href="/logout.php" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt"></i></a>
    </div>
  </div>
</nav>
<?php /* --- Sidebar for student dashboard --- */ ?>
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
<nav class="sidebar d-none d-md-block p-0" style="width:220px; min-width:220px; max-width:220px; position:fixed; top:0; left:0; height:100vh;">
  <div class="position-sticky">
    <ul class="nav flex-column">
      <li class="nav-item"><a class="nav-link" href="/student/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li class="nav-title mt-3 mb-1 text-uppercase text-muted small" style="letter-spacing:1px; font-size:0.92rem;">Courses</li>
      <li class="nav-item"><a class="nav-link" href="/student/my_courses.php"><i class="fas fa-book"></i> My Courses</a></li>
      <li class="nav-item"><a class="nav-link" href="/student/assignment_student.php"><i class="fas fa-list-check"></i> Assignments</a></li>
      <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-question-circle"></i> Quizzes</a></li>
      <li class="nav-item"><a class="nav-link" href="/student/assignment_student.php"><i class="fas fa-chart-line"></i> Grades</a></li>
      <li class="nav-title mt-3 mb-1 text-uppercase text-muted small" style="letter-spacing:1px; font-size:0.92rem;">Communication</li>
      <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-comments"></i> Forum</a></li>
      <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-envelope"></i> Messages</a></li>
      <li class="nav-title mt-3 mb-1 text-uppercase text-muted small" style="letter-spacing:1px; font-size:0.92rem;">Account</li>
      <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-user"></i> Profile</a></li>
      <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-cog"></i> Settings</a></li>
      <li class="nav-item"><a class="nav-link" href="/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </div>
</nav>
<!-- End student header/sidebar -->
<div class="main-layout" style="margin-left:90px; width:calc(100% - 90px); position:relative;">
  <div class="dashboard-content" style="padding: 24px 18px 18px 18px;">
    <!-- Dashboard content will be rendered here -->
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>