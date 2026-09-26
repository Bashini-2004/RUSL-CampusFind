<?php
// myitems.php - User's Reported Items Dashboard
$pageTitle = "My Reported Items";
require_once __DIR__ . '/includes/header.php';

if (!isLoggedIn()) {
    $_SESSION['flash_error'] = "Please log in to manage your reported items.";
    header('Location: login.php');
    exit;
}

$user = currentUser();

if (isset($_POST['action']) && $_POST['action'] === 'resolve' && isset($_POST['item_id'])) {
    $itemId = (int)$_POST['item_id'];
    $updateStmt = $pdo->prepare("UPDATE items SET status = 'claimed' WHERE item_id = ? AND (user_id = ? OR contact_email = ?)");
    $updateStmt->execute([$itemId, $user['id'], $user['email']]);
    $_SESSION['flash_success'] = "Item status updated to Resolved / Claimed!";
    header('Location: myitems.php');
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['item_id'])) {
    $itemId = (int)$_POST['item_id'];
    $delStmt = $pdo->prepare("DELETE FROM items WHERE item_id = ? AND (user_id = ? OR contact_email = ?)");
    $delStmt->execute([$itemId, $user['id'], $user['email']]);
    $_SESSION['flash_success'] = "Report deleted successfully.";
    header('Location: myitems.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT * FROM items 
    WHERE user_id = ? OR contact_email = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$user['id'], $user['email']]);
$myItems = $stmt->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div class="d-flex align-items-center">
            <img src="image/logo.png" alt="CampusFind Logo" width="40" height="40" class="me-3 rounded-2" style="object-fit: contain;">
            <div>
                <h2 class="fw-bold mb-0">My Reported Items</h2>
                <p class="text-muted mb-0 small">Manage all items you have reported as lost or found</p>
            </div>
        </div>
        <a href="report.php" class="btn btn-warning fw-semibold">
            <i class="bi bi-plus-lg me-1"></i> Report New Item
        </a>
    </div>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash_success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="min-width: 90px;">Type</th>
                        <th style="width: 60px;">Photo</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($myItems)): ?>
                        <?php foreach ($myItems as $item): ?>
                            <tr>
                                <td>
                                    <span class="badge <?= ($item['report_type'] === 'lost') ? 'bg-danger' : 'bg-success' ?> px-2 py-1">
                                        <?= ucfirst(htmlspecialchars($item['report_type'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <img src="<?= getItemImageUrl($item['image_path'] ?? '', $item['item_name']) ?>" alt="Thumbnail" class="rounded-3 border" style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($item['item_name']) ?></div>
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;">
                                        <?= htmlspecialchars($item['description']) ?>
                                    </small>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($item['category']) ?></span></td>
                                <td><?= htmlspecialchars($item['location']) ?></td>
                                <td><?= date('d M Y', strtotime($item['event_date'])) ?></td>
                                <td>
                                    <?php if ($item['status'] === 'active'): ?>
                                        <span class="badge bg-primary">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Claimed / Resolved</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <?php if ($item['status'] === 'active'): ?>
                                            <form method="POST" onsubmit="return confirm('Mark this item as recovered / claimed?');">
                                                <input type="hidden" name="action" value="resolve">
                                                <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                                                <button type="submit" class="btn btn-outline-success btn-sm" title="Mark as resolved">
                                                    <i class="bi bi-check-circle"></i> Resolved
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this report?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete report">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-folder2-open display-5 d-block mb-2"></i>
                                You haven't reported any lost or found items yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>