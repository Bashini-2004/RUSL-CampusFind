<?php
// admin.php - Administrator Management Dashboard
$pageTitle = "Admin Dashboard";
require_once __DIR__ . '/includes/header.php';

if (!isAdmin()) {
    $_SESSION['flash_error'] = "Access denied. Administrator privileges required.";
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $itemId = (int)($_POST['item_id'] ?? 0);

    if ($action === 'delete' && $itemId > 0) {
        $del = $pdo->prepare("DELETE FROM items WHERE item_id = ?");
        $del->execute([$itemId]);
        $_SESSION['flash_success'] = "Item #{$itemId} has been removed by administrator.";
        header('Location: admin.php');
        exit;
    }

    if ($action === 'toggle_status' && $itemId > 0) {
        $newStatus = ($_POST['new_status'] === 'claimed') ? 'claimed' : 'active';
        $upd = $pdo->prepare("UPDATE items SET status = ? WHERE item_id = ?");
        $upd->execute([$newStatus, $itemId]);
        $_SESSION['flash_success'] = "Item #{$itemId} status updated to " . ucfirst($newStatus);
        header('Location: admin.php');
        exit;
    }
}

$totalItems    = $pdo->query("SELECT COUNT(*) FROM items")->fetchColumn();
$activeLost    = $pdo->query("SELECT COUNT(*) FROM items WHERE report_type = 'lost' AND status = 'active'")->fetchColumn();
$activeFound   = $pdo->query("SELECT COUNT(*) FROM items WHERE report_type = 'found' AND status = 'active'")->fetchColumn();
$resolvedItems = $pdo->query("SELECT COUNT(*) FROM items WHERE status = 'claimed'")->fetchColumn();
$totalUsers    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

$stmt = $pdo->query("
    SELECT i.*, u.full_name as reporter_name 
    FROM items i 
    LEFT JOIN users u ON i.user_id = u.user_id 
    ORDER BY i.created_at DESC
");
$allItems = $stmt->fetchAll();

$msgStmt = $pdo->query("SELECT * FROM contact_messages ORDER BY submitted_at DESC LIMIT 5");
$recentMsgs = $msgStmt->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <img src="image/logo.png" alt="CampusFind Logo" width="45" height="45" class="me-3 rounded-2" style="object-fit: contain;">
            <div>
                <h2 class="fw-bold mb-0"><i class="bi bi-shield-lock-fill text-danger me-2"></i>Admin Dashboard</h2>
                <p class="text-muted mb-0 small">Manage university lost & found reports, user activities, and inquiries</p>
            </div>
        </div>
        <span class="badge bg-danger px-3 py-2 fs-6">Administrator Mode</span>
    </div>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash_success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <div class="row g-3 mb-5">
        <div class="col-md-4 col-lg-2">
            <div class="card shadow-sm border-0 p-3 text-center rounded-3">
                <span class="text-muted small">Total Reports</span>
                <h3 class="fw-bold my-1"><?= $totalItems ?></h3>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card shadow-sm border-0 p-3 text-center rounded-3 border-start border-danger border-4">
                <span class="text-muted small">Active Lost</span>
                <h3 class="fw-bold text-danger my-1"><?= $activeLost ?></h3>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card shadow-sm border-0 p-3 text-center rounded-3 border-start border-success border-4">
                <span class="text-muted small">Active Found</span>
                <h3 class="fw-bold text-success my-1"><?= $activeFound ?></h3>
            </div>
        </div>
        <div class="col-md-4 col-lg-3">
            <div class="card shadow-sm border-0 p-3 text-center rounded-3">
                <span class="text-muted small">Recovered / Resolved</span>
                <h3 class="fw-bold text-primary my-1"><?= $resolvedItems ?></h3>
            </div>
        </div>
        <div class="col-md-4 col-lg-3">
            <div class="card shadow-sm border-0 p-3 text-center rounded-3">
                <span class="text-muted small">Registered Users</span>
                <h3 class="fw-bold text-dark my-1"><?= $totalUsers ?></h3>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-5">
        <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-list-check me-2"></i>All Item Reports</h5>
            <span class="badge bg-warning text-dark"><?= count($allItems) ?> Reports Total</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th style="width: 50px;">Photo</th>
                        <th>Item Details</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Date</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($allItems)): ?>
                        <?php foreach ($allItems as $item): ?>
                            <tr>
                                <td class="fw-bold text-muted">#<?= $item['item_id'] ?></td>
                                <td>
                                    <span class="badge <?= ($item['report_type'] === 'lost') ? 'bg-danger' : 'bg-success' ?>">
                                        <?= strtoupper($item['report_type']) ?>
                                    </span>
                                </td>
                                <td>
                                    <img src="<?= getItemImageUrl($item['image_path'] ?? '', $item['item_name']) ?>" alt="Thumbnail" class="rounded-3 border" style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($item['item_name']) ?></div>
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;">
                                        <?= htmlspecialchars($item['description']) ?>
                                    </small>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($item['category']) ?></span></td>
                                <td><?= htmlspecialchars($item['location']) ?></td>
                                <td><?= date('d M Y', strtotime($item['event_date'])) ?></td>
                                <td><small class="text-truncate d-block" style="max-width: 140px;"><?= htmlspecialchars($item['contact_email']) ?></small></td>
                                <td>
                                    <span class="badge <?= ($item['status'] === 'active') ? 'bg-primary' : 'bg-secondary' ?>">
                                        <?= ucfirst($item['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                                            <input type="hidden" name="new_status" value="<?= ($item['status'] === 'active') ? 'claimed' : 'active' ?>">
                                            <button type="submit" class="btn btn-outline-secondary btn-sm" title="Toggle Active/Claimed">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        </form>

                                        <form method="POST" class="d-inline" onsubmit="return confirm('Permanently delete item report #<?= $item['item_id'] ?>?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Report">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="10" class="text-center py-4 text-muted">No records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-light py-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-chat-left-dots-fill me-2 text-warning"></i>Recent Contact Inquiries</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentMsgs)): ?>
                        <?php foreach ($recentMsgs as $msg): ?>
                            <tr>
                                <td><small class="text-muted"><?= date('d M Y, h:i A', strtotime($msg['submitted_at'])) ?></small></td>
                                <td class="fw-semibold"><?= htmlspecialchars($msg['name']) ?></td>
                                <td><a href="mailto:<?= htmlspecialchars($msg['email']) ?>"><?= htmlspecialchars($msg['email']) ?></a></td>
                                <td><?= htmlspecialchars($msg['message']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">No contact messages received yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>