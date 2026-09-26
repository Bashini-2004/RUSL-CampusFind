<?php
// contact.php - Contact CampusFind Team
$pageTitle = "Contact Us";
require_once __DIR__ . '/includes/header.php';
$user = currentUser();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <div class="text-center mb-4">
                    <img src="image/logo.png" alt="CampusFind Logo" class="brand-logo mb-3" style="width: 70px; height: 70px; object-fit: contain;">
                    <h2 class="fw-bold">Contact Us</h2>
                    <p class="text-muted">Have inquiries, need assistance with security, or suggestions for CampusFind?</p>
                </div>

                <?php if (isset($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['flash_success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['flash_success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['flash_error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['flash_error']); ?>
                <?php endif; ?>

                <form action="contact_process.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Your Name *</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name'] !== 'Guest' ? $user['name'] : '') ?>" required placeholder="Enter your full name">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Your Email *</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required placeholder="Enter university email">
                    </div>

                    <div class="mb-4">
                        <label for="message" class="form-label fw-semibold">Message *</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required placeholder="Describe your question or issue in detail..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-warning w-100 py-2 fw-bold shadow-sm">
                        <i class="bi bi-send-fill me-1"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>