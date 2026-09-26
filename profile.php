<?php
// profile.php - User Profile Page
$pageTitle = "My Profile";
require_once __DIR__ . '/includes/header.php';

if (!isLoggedIn()) {
    $_SESSION['flash_error'] = "Please log in to view your profile.";
    header('Location: login.php');
    exit;
}

$user = currentUser();

// Fetch fresh details from database
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user['id']]);
$userData = $stmt->fetch();

// Count items reported by this user
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM items WHERE user_id = ? OR contact_email = ?");
$countStmt->execute([$user['id'], $userData['email']]);
$reportCount = $countStmt->fetchColumn();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="bg-dark text-white text-center py-4">
                    <div class="display-3 mb-2">👤</div>
                    <h3 class="fw-bold mb-0"><?= htmlspecialchars($userData['full_name']) ?></h3>
                    <span class="badge bg-warning text-dark mt-2 text-uppercase"><?= htmlspecialchars($userData['role']) ?></span>
                </div>
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Profile Information</h5>
                    
                    <div class="mb-3">
                        <label class="text-muted small d-block">University Email</label>
                        <span class="fw-semibold fs-6"><?= htmlspecialchars($userData['email']) ?></span>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small d-block">Phone Number</label>
                        <span class="fw-semibold fs-6"><?= !empty($userData['phone']) ? htmlspecialchars($userData['phone']) : 'Not provided' ?></span>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small d-block">Items Reported</label>
                        <span class="badge bg-primary fs-6"><?= $reportCount ?> items</span>
                    </div>

                    <div class="mb-4">
                        <label class="text-muted small d-block">Member Since</label>
                        <span class="text-secondary"><?= date('d F Y', strtotime($userData['created_at'])) ?></span>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="myitems.php" class="btn btn-outline-dark flex-grow-1">
                            <i class="bi bi-box-seam me-1"></i> My Items
                        </a>
                        <a href="settings.php" class="btn btn-warning flex-grow-1 fw-semibold">
                            <i class="bi bi-gear me-1"></i> Edit Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
