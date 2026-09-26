<?php
// register.php - User Registration
$pageTitle = "Register";
require_once __DIR__ . '/includes/header.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <div class="text-center mb-4">
                    <img src="image/logo.png" alt="CampusFind Logo" class="brand-logo mb-3" style="width: 70px; height: 70px; object-fit: contain;">
                    <h3 class="fw-bold">Create an Account</h3>
                    <p class="text-muted small">Join CampusFind to report, track, and recover items</p>
                </div>

                <?php if (isset($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger py-2 small" role="alert">
                        <?= htmlspecialchars($_SESSION['flash_error']) ?>
                    </div>
                    <?php unset($_SESSION['flash_error']); ?>
                <?php endif; ?>

                <form action="register_process.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Full Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="e.g. Supun Perera">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">University Email *</label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="name@rusl.ac.lk">
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label fw-semibold">Contact Phone (Optional)</label>
                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="07X XXXXXXX">
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Password *</label>
                        <input type="password" class="form-control" id="password" name="password" minlength="6" required placeholder="At least 6 characters">
                    </div>

                    <button type="submit" class="btn btn-warning w-100 py-2 fw-bold shadow-sm mb-3">
                        Register Account
                    </button>

                    <p class="text-center text-muted small mb-0">
                        Already have an account? <a href="login.php" class="text-decoration-none fw-semibold">Login here</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>