<?php
// Start a session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // If not admin, redirect them to the Admin Login page
    // Note: This assumes this file is included by pages inside /public/admin/
    header("Location: index.php?error=unauthorized");
    exit;
}
