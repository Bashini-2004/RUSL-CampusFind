<?php
// report.php - Report Lost or Found Item
$pageTitle = "Report an Item";
require_once __DIR__ . '/includes/header.php';
$user = currentUser();
?>

<div class="container py-5">
    <div class="report-form-card">
        <div class="text-center mb-4">
            <img src="image/logo.png" alt="CampusFind Logo" class="brand-logo mb-3" style="width: 70px; height: 70px; object-fit: contain;">
            <h2 class="fw-bold">Report Lost or Found Item</h2>
            <p class="text-muted">Please provide accurate details about the item so we can connect it with its owner.</p>
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

        <form action="report_process.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            
            <div class="mb-4 text-center">
                <label class="form-label d-block fw-semibold mb-2">What would you like to report? *</label>
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="reportType" id="lostRadio" value="lost" checked>
                    <label class="btn btn-outline-danger px-4 py-2" for="lostRadio">
                        <i class="bi bi-search me-1"></i> I lost an item
                    </label>

                    <input type="radio" class="btn-check" name="reportType" id="foundRadio" value="found">
                    <label class="btn btn-outline-success px-4 py-2" for="foundRadio">
                        <i class="bi bi-box2-heart me-1"></i> I found an item
                    </label>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="item_name" class="form-label fw-semibold">Item Name *</label>
                    <input type="text" class="form-control" id="item_name" name="item_name" required placeholder="e.g. Student ID, Samsung Galaxy, Black Backpack">
                </div>

                <div class="col-md-6">
                    <label for="category" class="form-label fw-semibold">Category *</label>
                    <select class="form-select" id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="Electronics">Electronics</option>
                        <option value="ID Cards">ID Cards</option>
                        <option value="Bags">Bags</option>
                        <option value="Books">Books</option>
                        <option value="Clothing">Clothing</option>
                        <option value="Accessories">Accessories</option>
                        <option value="Stationery">Stationery</option>
                        <option value="Keys">Keys</option>
                        <option value="Others">Others</option>
                    </select>
                </div>

                <div class="col-12">
                    <label for="description" class="form-label fw-semibold">Description *</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required placeholder="Provide distinguishing details (e.g. color, brand, stickers, unique scratches, content inside)"></textarea>
                </div>

                <div class="col-md-4">
                    <label for="date" class="form-label fw-semibold">Date *</label>
                    <input type="date" class="form-control" id="date" name="date" max="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="col-md-4">
                    <label for="location" class="form-label fw-semibold">Location *</label>
                    <select class="form-select" id="location" name="location" required>
                        <option value="">Where was it lost/found?</option>
                        <option value="Library">Library</option>
                        <option value="Cafeteria">Cafeteria</option>
                        <option value="Lecture Hall">Lecture Hall</option>
                        <option value="Playground">Playground</option>
                        <option value="Parking Area">Parking Area</option>
                        <option value="ICT Building">ICT Building</option>
                        <option value="Main Building">Main Building</option>
                        <option value="Hostel">Hostel</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="time" class="form-label fw-semibold">Approx. Time</label>
                    <input type="time" class="form-control" id="time" name="time">
                </div>

                <div class="col-md-6">
                    <label for="image" class="form-label fw-semibold">Upload Photo</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/png, image/jpeg, image/jpg, image/webp">
                    <div class="form-text">JPG, PNG, or WEBP (Max 5MB). Optional but recommended.</div>
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold">Contact Email *</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required placeholder="Enter university email">
                </div>
            </div>

            <div class="alert alert-light border mt-4 mb-4 small text-muted">
                <i class="bi bi-shield-check text-success me-1"></i>
                <strong>Note:</strong> Your contact details will only be used to facilitate returning the item securely.
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-warning btn-lg px-5 fw-bold shadow-sm">
                    <i class="bi bi-send-fill me-2"></i> Submit Report
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>