<?php
// settings.php - User Settings & Profile Update
$pageTitle = "Account Settings";
require_once __DIR__ . '/includes/header.php';

if (!isLoggedIn()) {
    $_SESSION['flash_error'] = "Please log in to manage your settings.";
    header('Location: login.php');
    exit;
}

$user = currentUser();

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $newPass  = $_POST['new_password'] ?? '';

    if (empty($fullName)) {
        $_SESSION['flash_error'] = "Full name cannot be empty.";
    } else {
        if (!empty($newPass)) {
            if (strlen($newPass) < 6) {
                $_SESSION['flash_error'] = "New password must be at least 6 characters.";
            } else {
                $hash = password_hash($newPass, PASSWORD_DEFAULT);
                $upd = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, password = ? WHERE user_id = ?");
                $upd->execute([$fullName, $phone, $hash, $user['id']]);
                $_SESSION['full_name'] = $fullName;
                $_SESSION['flash_success'] = "Profile and password updated successfully!";
            }
        } else {
            $upd = $pdo->prepare("UPDATE users SET full_name = ?, phone = ? WHERE user_id = ?");
            $upd->execute([$fullName, $phone, $user['id']]);
            $_SESSION['full_name'] = $fullName;
            $_SESSION['flash_success'] = "Profile details updated successfully!";
        }
    }
    header('Location: settings.php');
    exit;
}

// Fetch current user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user['id']]);
$userData = $stmt->fetch();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h3 class="fw-bold mb-1"><i class="bi bi-gear-fill me-2 text-warning"></i>Account Settings</h3>
                <p class="text-muted small mb-4">Update your profile details and security settings</p>

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

                <form action="settings.php" method="POST">
                    <div class="mb-3">
                        <label for="full_name" class="form-label fw-semibold">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($userData['full_name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address (Read-only)</label>
                        <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($userData['email']) ?>" readonly>
                        <div class="form-text">University email cannot be changed.</div>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label fw-semibold">Contact Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($userData['phone'] ?? '') ?>" placeholder="07X XXXXXXX">
                    </div>

                    <hr class="my-4">

                    <h5 class="fw-bold mb-3">Change Password</h5>
                    <div class="mb-4">
                        <label for="new_password" class="form-label fw-semibold">New Password (Optional)</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" minlength="6" placeholder="Leave blank to keep existing password">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="profile.php" class="btn btn-outline-secondary">Back to Profile</a>
                        <button type="submit" class="btn btn-warning fw-semibold">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>