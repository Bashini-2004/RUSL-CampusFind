<?php
// login.php - User Login
$pageTitle = "Login";
require_once __DIR__ . '/includes/header.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <div class="text-center mb-4">
                    <img src="image/logo.png" alt="CampusFind Logo" class="brand-logo mb-3" style="width: 70px; height: 70px; object-fit: contain;">
                    <h3 class="fw-bold">Sign In</h3>
                    <p class="text-muted small">Access your reported items and campus alerts</p>
                </div>

                <?php if (isset($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger py-2 small" role="alert">
                        <?= htmlspecialchars($_SESSION['flash_error']) ?>
                    </div>
                    <?php unset($_SESSION['flash_error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success py-2 small" role="alert">
                        <?= htmlspecialchars($_SESSION['flash_success']) ?>
                    </div>
                    <?php unset($_SESSION['flash_success']); ?>
                <?php endif; ?>

                <form action="login_process.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="name@rusl.ac.lk">
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                    </div>

                    <button type="submit" class="btn btn-warning w-100 py-2 fw-bold shadow-sm mb-3">
                        Log In
                    </button>

                    <p class="text-center text-muted small mb-0">
                        Don't have an account? <a href="register.php" class="text-decoration-none fw-semibold">Register here</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>