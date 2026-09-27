<?php
// index.php - Dynamic Home Page
$pageTitle = "Home";
require_once __DIR__ . '/includes/header.php';

// Fetch 3 most recent lost items
$lostStmt = $pdo->prepare("SELECT * FROM items WHERE report_type = 'lost' AND status = 'active' ORDER BY event_date DESC, item_id DESC LIMIT 3");
$lostStmt->execute();
$recentLost = $lostStmt->fetchAll();

// Fetch 3 most recent found items
$foundStmt = $pdo->prepare("SELECT * FROM items WHERE report_type = 'found' AND status = 'active' ORDER BY event_date DESC, item_id DESC LIMIT 3");
$foundStmt->execute();
$recentFound = $foundStmt->fetchAll();

/**
 * Returns the image URL if a real uploaded/item file exists, or NULL.
 * Logo and default placeholder images are explicitly ignored.
 */
function resolveItemImage(array $item): ?string
{
    // Files that are system images, not real item photos
    $ignored = ['logo.png', 'default_item.png'];

    $filename = !empty($item['image_path']) ? basename($item['image_path']) : '';

    if ($filename === '' || in_array($filename, $ignored, true)) {
        return null;
    }

    if (file_exists(__DIR__ . '/uploads/' . $filename) && !is_dir(__DIR__ . '/uploads/' . $filename)) {
        return 'uploads/' . htmlspecialchars($filename);
    }

    if (file_exists(__DIR__ . '/image/' . $filename) && !is_dir(__DIR__ . '/image/' . $filename)) {
        return 'image/' . htmlspecialchars($filename);
    }

    return null; // file not found on disk
}

/**
 * Format an event date safely; returns a fallback string instead of
 * silently printing a bogus date if strtotime() fails.
 */
function formatEventDate(?string $date): string
{
    if (empty($date)) {
        return 'Date unknown';
    }
    $timestamp = strtotime($date);
    return $timestamp !== false ? date('d M Y', $timestamp) : 'Date unknown';
}

/**
 * Render a single item card. $type is 'lost' or 'found'.
 */
function renderItemCard(array $item, string $type): void
{
    $imgSrc     = resolveItemImage($item);
    $badgeClass = $type === 'lost' ? 'bg-danger' : 'bg-success';
    $badgeLabel = $type === 'lost' ? 'Lost' : 'Found';
    $dateLabel  = $type === 'lost' ? 'Date Lost' : 'Date Found';
    ?>
    <div class="col-md-4">
        <div class="card item-card shadow-sm h-100">
            <div class="item-img-container">
                <?php if ($imgSrc !== null): ?>
                    <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($item['item_name']) ?>">
                <?php else: ?>
                    <div class="item-img-placeholder">
                        <i class="bi bi-image placeholder-icon"></i>
                        <span class="placeholder-name"><?= htmlspecialchars($item['item_name']) ?></span>
                    </div>
                <?php endif; ?>
                <span class="badge <?= $badgeClass ?> item-type-badge"><?= $badgeLabel ?></span>
            </div>
            <div class="card-body d-flex flex-column">
                <span class="category-border-box align-self-start">Category: <strong><?= htmlspecialchars($item['category']) ?></strong></span>
                <h5 class="card-title fw-bold text-truncate"><?= htmlspecialchars($item['item_name']) ?></h5>
                <p class="card-text text-muted small flex-grow-1"><?= htmlspecialchars(substr($item['description'], 0, 90)) ?>...</p>
                <div class="border-top pt-2 mt-2 small text-secondary">
                    <div><strong>Location:</strong> <?= htmlspecialchars($item['location']) ?></div>
                    <div><strong><?= $dateLabel ?>:</strong> <?= formatEventDate($item['event_date']) ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>

<!-- ================= HERO SECTION ================= -->
<section class="hero py-5">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold text-dark mb-2">Lost Something?</h1>
                <h2 class="display-6 fw-semibold mb-3"><b>We're here to help!</b></h2>
                <p class="lead text-secondary mb-4">
                    Search for lost items or report items you've found across Rajarata University campus.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="search.php" class="btn btn-warning btn-lg px-4 fw-semibold shadow-sm">
                        Search Items
                    </a>
                    <a href="report.php" class="btn btn-outline-dark btn-lg px-4 fw-semibold">
                        Report Item
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <?php if (file_exists(__DIR__ . '/image/logo.png')): ?>
                    <img src="image/logo.png" class="img-fluid rounded-4 shadow-sm" alt="CampusFind Logo" style="max-height: 280px; object-fit: contain;">
                <?php else: ?>
                    <div class="p-5 bg-white rounded-4 shadow-sm border text-center d-inline-block" style="min-width: 280px;">
                        <div class="display-1 text-warning mb-2"><i class="bi bi-geo-alt-fill"></i></div>
                        <h2 class="fw-bold mb-1">CampusFind</h2>
                        <p class="text-muted small mb-0">Rajarata University of Sri Lanka</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ================= RECENT LOST ITEMS ================= -->
<section class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Recent Lost Items</h2>
        </div>
        <a href="search.php?type=lost" class="btn btn-secondary btn-sm px-3">View All</a>
    </div>

    <div class="row g-4">
        <?php if (!empty($recentLost)): ?>
            <?php foreach ($recentLost as $item): ?>
                <?php renderItemCard($item, 'lost'); ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-4 text-muted">
                No active lost items reported yet.
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ================= RECENT FOUND ITEMS ================= -->
<section class="container py-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Recent Found Items</h2>
        </div>
        <a href="search.php?type=found" class="btn btn-secondary btn-sm px-3">View All</a>
    </div>

    <div class="row g-4">
        <?php if (!empty($recentFound)): ?>
            <?php foreach ($recentFound as $item): ?>
                <?php renderItemCard($item, 'found'); ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-4 text-muted">
                No active found items reported yet.
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>