<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';
requireAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Baladiya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .sidebar { min-height: 100vh; background: #2c3e50; }
        .sidebar .nav-link { color: #ecf0f1; padding: 15px 20px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #34495e; color: #fff; }
        .main-content { padding: 20px; }
        .card-stats { border-left: 4px solid; }
        .card-stats.citizens { border-color: #3498db; }
        .card-stats.documents { border-color: #2ecc71; }
        .card-stats.requests { border-color: #f39c12; }
        .card-stats.reports { border-color: #e74c3c; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar p-0">
            <div class="p-3 text-white text-center">
                <h4><i class="bi bi-building"></i> CMS Baladiya</h4>
                <small><?php echo sanitize($_SESSION['user']); ?></small>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' && basename(dirname($_SERVER['PHP_SELF'])) == 'dashboard' ? 'active' : ''; ?>" href="../dashboard/index.php">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
                <a class="nav-link <?php echo basename(dirname($_SERVER['PHP_SELF'])) == 'citizens' ? 'active' : ''; ?>" href="../citizens/index.php">
                    <i class="bi bi-people me-2"></i> Citizens
                </a>
                <a class="nav-link <?php echo basename(dirname($_SERVER['PHP_SELF'])) == 'documents' ? 'active' : ''; ?>" href="../documents/index.php">
                    <i class="bi bi-file-earmark-text me-2"></i> Documents
                </a>
                <a class="nav-link <?php echo basename(dirname($_SERVER['PHP_SELF'])) == 'requests' ? 'active' : ''; ?>" href="../requests/index.php">
                    <i class="bi bi-inbox me-2"></i> Requests
                </a>
                <a class="nav-link <?php echo basename(dirname($_SERVER['PHP_SELF'])) == 'reports' ? 'active' : ''; ?>" href="../reports/index.php">
                    <i class="bi bi-graph-up me-2"></i> Reports
                </a>
                <?php if (isAdmin()): ?>
                <a class="nav-link <?php echo basename(dirname($_SERVER['PHP_SELF'])) == 'users' ? 'active' : ''; ?>" href="../users/index.php">
                    <i class="bi bi-shield-lock me-2"></i> Users
                </a>
                <?php endif; ?>
                <a class="nav-link text-danger" href="../auth/logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-10 main-content">
            <?php echo showFlash(); ?>