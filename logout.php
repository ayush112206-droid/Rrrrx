<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
// Only destroy if it was a manual login (not auto)
if (isset($_SESSION['login_type']) && $_SESSION['login_type'] !== 'auto') {
    session_destroy();
}
header('Location: dashboard.php');
exit;
