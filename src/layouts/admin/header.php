<?php
session_start();
// Use __DIR__ to safely locate the auth file regardless of XAMPP folder depth
require_once __DIR__ . '/../../includes/auth_admin.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SatayTech Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">
        <img src="../assets/sataytech_logo.png" width="30" height="30" alt="Logo"> SatayTech
    </a>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="products.php">Inventory</a></li>
        <li class="nav-item"><a class="nav-link" href="add_product.php">Add Stock</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
         <li class="nav-item"><a class="nav-link btn btn-danger text-white btn-sm" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<main class="container mt-4">