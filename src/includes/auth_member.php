<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    // If not member, redirect them to the Member Login page
    header("Location: login.php?error=unauthorized");
    exit;
}
