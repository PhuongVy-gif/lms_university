<?php
session_start();
// Hủy tất cả session
session_unset();
session_destroy();
// Chuyển hướng về trang login.php hoặc trang chỉ định
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'login.php';
header('Location: ' . $redirect);
exit();
