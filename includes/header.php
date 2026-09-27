<?php
// includes/header.php
require_once __DIR__ . '/../config/db.php';

$currentPage = basename($_SERVER['PHP_SELF']);
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?>CampusFind - RUSL Lost & Found</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- ================= NAVBAR ================= -->
<nav class="navbar navbar-expand-lg shadow-sm sticky-top campus-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="image/logo.png" alt="CampusFind Logo" width="42" height="42" class="me-2 rounded-2" style="object-fit: contain;">
            <span class="fw-bold tracking-tight text-dark">CampusFind</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage === 'index.php') ? 'active' : '' ?>" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage === 'search.php') ? 'active' : '' ?>" href="search.php">Search Items</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage === 'report.php') ? 'active' : '' ?>" href="report.php">Report Items</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage === 'about.php') ? 'active' : '' ?>" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage === 'contact.php') ? 'active' : '' ?>" href="contact.php">Contact</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <?php if (isLoggedIn()): ?>
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="userMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
                            👤 User Account
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userMenuBtn">
                            <li class="px-3 py-2 border-bottom bg-light">
                                <span class="fw-bold d-block text-dark text-truncate" style="max-width: 200px;"><?= htmlspecialchars($user['name']) ?></span>
                                <small class="text-muted text-truncate d-block" style="max-width: 200px;"><?= htmlspecialchars($user['email']) ?></small>
                            </li>
                            <li><a class="dropdown-item py-2" href="profile.php"><i class="bi bi-person me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item py-2" href="myitems.php"><i class="bi bi-box-seam me-2"></i>My Items</a></li>
                            <li><a class="dropdown-item py-2" href="settings.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <?php if (isAdmin()): ?>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li><a class="dropdown-item py-2 text-danger fw-bold" href="admin.php"><i class="bi bi-shield-lock me-2"></i>Admin Dashboard</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li><a class="dropdown-item py-2 text-danger fw-semibold" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-dark me-2">Login</a>
                    <a href="register.php" class="btn btn-warning fw-semibold">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>