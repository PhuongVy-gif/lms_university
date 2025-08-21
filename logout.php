<?php
session_start();
// Cancel all sessions
session_unset();
session_destroy();
// Redirect to login.php or specified page
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'login.php';
header('Location: ' . $redirect);
exit();
