<?php
// search.php - Dynamic Search & Filter Items
$pageTitle = "Search Lost & Found Items";
require_once __DIR__ . '/includes/header.php';

$searchQuery = trim($_GET['q'] ?? '');
$category    = trim($_GET['category'] ?? '');
$location    = trim($_GET['location'] ?? '');
$type        = trim($_GET['type'] ?? 'all');

$sql = "SELECT * FROM items WHERE status = 'active'";
$params = [];

if (!empty($searchQuery)) {
    $sql .= " AND (item_name LIKE ? OR description LIKE ?)";
    $params[] = "%{$searchQuery}%";
    $params[] = "%{$searchQuery}%";
}

if (!empty($category) && $category !== 'All Categories') {
    $sql .= " AND category = ?";
    $params[] = $category;
}

if (!empty($location) && $location !== 'All Locations') {
    $sql .= " AND location = ?";
    $params[] = $location;
}

if ($type === 'lost') {
    $sql .= " AND report_type = 'lost'";
} elseif ($type === 'found') {
    $sql .= " AND report_type = 'found'";
}

$sql .= " ORDER BY event_date DESC, item_id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

$categories = ['All Categories', 'Electronics', 'ID Cards', 'Bags', 'Books', 'Clothing', 'Accessories', 'Stationery', 'Keys', 'Others'];
$locations  = ['All Locations', 'Library', 'Cafeteria', 'Lecture Hall', 'Playground', 'Parking Area', 'ICT Building', 'Main Building', 'Hostel', 'Other'];
?>

<section class="container py-5">
    <div class="text-center mb-4">
        <img src="image/logo.png" alt="CampusFind Logo" class="brand-logo mb-3" style="width: 70px; height: 70px; object-fit: contain;">
        <h1 class="fw-bold">Search Lost & Found Items</h1>
        <p class="text-muted">Find your lost belongings or see if an item you found has been reported missing.</p>
    </div>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> <?= htmlspecialchars($_SESSION['flash_success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
        <form action="search.php" method="GET" class="row g-3">
            <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">

            <div class="col-lg-4 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control" name="q" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Search by item name or keyword...">
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <select class="form-select" name="category">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= ($category === $cat) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-lg-3 col-md-6">
                <select class="form-select" name="location">
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?= htmlspecialchars($loc) ?>" <?= ($location === $loc) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($loc) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-lg-2 col-md-6">
                <button type="submit" class="btn btn-warning w-100 fw-bold">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div class="btn-group" role="group">
            <a href="search.php?<?= http_build_query(array_merge($_GET, ['type' => 'all'])) ?>" 
               class="btn <?= ($type === 'all') ? 'btn-dark' : 'btn-outline-dark' ?> px-3">
                All Items
            </a>
            <a href="search.php?<?= http_build_query(array_merge($_GET, ['type' => 'lost'])) ?>" 
               class="btn <?= ($type === 'lost') ? 'btn-danger' : 'btn-outline-danger' ?> px-3">
                Lost Items
            </a>
            <a href="search.php?<?= http_build_query(array_merge($_GET, ['type' => 'found'])) ?>" 
               class="btn <?= ($type === 'found') ? 'btn-success' : 'btn-outline-success' ?> px-3">
                Found Items
            </a>
        </div>

        <span class="text-muted small">
            Found <strong><?= count($items) ?></strong> <?= count($items) === 1 ? 'item' : 'items' ?>
        </span>
    </div>

    <div class="row g-4">
        <?php if (!empty($items)): ?>
            <?php foreach ($items as $item): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card item-card shadow-sm h-100">
                        <div class="item-img-container">
                            <img src="<?= getItemImageUrl($item['image_path'] ?? '', $item['item_name']) ?>" alt="<?= htmlspecialchars($item['item_name']) ?>">
                            <span class="badge <?= ($item['report_type'] === 'lost') ? 'bg-danger' : 'bg-success' ?> item-type-badge">
                                <?= ucfirst(htmlspecialchars($item['report_type'])) ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($item['category']) ?></span>
                                <small class="text-muted"><i class="bi bi-clock me-1"></i><?= !empty($item['event_time']) ? htmlspecialchars(date('h:i A', strtotime($item['event_time']))) : 'N/A' ?></small>
                            </div>
                            <h5 class="card-title"><?= htmlspecialchars($item['item_name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($item['description']) ?></p>
                            
                            <div class="item-meta">
                                <div><i class="bi bi-geo-alt text-primary me-1"></i> <strong>Location:</strong> <?= htmlspecialchars($item['location']) ?></div>
                                <div><i class="bi bi-calendar3 me-1"></i> <strong>Date:</strong> <?= date('d M Y', strtotime($item['event_date'])) ?></div>
                            </div>

                            <div class="mt-3 pt-2 border-top">
                                <a href="mailto:<?= htmlspecialchars($item['contact_email']) ?>?subject=Regarding your <?= urlencode($item['report_type']) ?> item: <?= urlencode($item['item_name']) ?>" 
                                   class="btn btn-outline-dark btn-sm w-100 fw-semibold">
                                    <i class="bi bi-envelope me-1"></i> Contact <?= ($item['report_type'] === 'lost') ? 'Owner' : 'Finder' ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-inbox display-4 text-muted mb-3 d-block"></i>
                <h4 class="text-muted">No items matched your search criteria</h4>
                <p class="text-secondary small">Try clearing your filters or search keywords.</p>
                <a href="search.php" class="btn btn-outline-warning">Clear All Filters</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>