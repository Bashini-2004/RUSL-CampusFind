<?php
// report_process.php - Process Item Report Submission
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: report.php');
    exit;
}

// Require login before processing anything else
if (!isLoggedIn()) {
    $_SESSION['flash_error'] = "You must be logged in to submit a report.";
    header('Location: login.php');
    exit;
}

$user = currentUser();
if (!$user || empty($user['id'])) {
    $_SESSION['flash_error'] = "Your session has expired. Please log in again.";
    header('Location: login.php');
    exit;
}
$userId = $user['id'];

$reportType   = strtolower(trim($_POST['reportType'] ?? 'lost'));
$itemName     = trim($_POST['item_name'] ?? '');
$category     = trim($_POST['category'] ?? '');
$description  = trim($_POST['description'] ?? '');
$location     = trim($_POST['location'] ?? '');
$eventDate    = trim($_POST['date'] ?? '');
$eventTime    = trim($_POST['time'] ?? '');
$contactEmail = strtolower(trim($_POST['email'] ?? ''));

// Validate report type
if (!in_array($reportType, ['lost', 'found'], true)) {
    $reportType = 'lost';
}

// Basic field validation
if (empty($itemName) || empty($category) || empty($description) || empty($location) || empty($eventDate) || empty($contactEmail)) {
    $_SESSION['flash_error'] = "Please fill in all required fields marked with *.";
    header('Location: report.php');
    exit;
}

// Validate email
if (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash_error'] = "Please provide a valid contact email address.";
    header('Location: report.php');
    exit;
}

// Format event time (if empty, store NULL)
$eventTimeVal = !empty($eventTime) ? $eventTime : null;

// Handle File Upload
$imagePath = 'default_item.png';

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName    = $_FILES['image']['name'];
    $fileSize    = $_FILES['image']['size'];
    $fileType    = $_FILES['image']['type'];

    $maxFileSize = 5 * 1024 * 1024; // 5 MB
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    $allowedMimes      = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/webp'];

    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if ($fileSize > $maxFileSize) {
        $_SESSION['flash_error'] = "Uploaded image exceeds the 5MB file size limit.";
        header('Location: report.php');
        exit;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $fileTmpPath);
    finfo_close($finfo);

    if (in_array($fileExt, $allowedExtensions, true) && in_array($mimeType, $allowedMimes, true)) {
        $uploadsDir = __DIR__ . '/uploads';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }

        $newFileName = uniqid('item_', true) . '.' . $fileExt;
        $destPath    = $uploadsDir . '/' . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $imagePath = $newFileName;
        }
    } else {
        $_SESSION['flash_error'] = "Invalid file type. Please upload a valid image file (JPG, PNG, or WEBP).";
        header('Location: report.php');
        exit;
    }
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO items (user_id, report_type, item_name, category, description, location, event_date, event_time, image_path, contact_email, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
    ");

    $stmt->execute([
        $userId,
        $reportType,
        $itemName,
        $category,
        $description,
        $location,
        $eventDate,
        $eventTimeVal,
        $imagePath,
        $contactEmail
    ]);

    $typeLabel = ucfirst($reportType);
    $_SESSION['flash_success'] = "{$typeLabel} item report has been published successfully!";

    header('Location: myitems.php');
    exit;

} catch (PDOException $e) {
    // Log the real error server-side; never expose raw DB messages to the user
    error_log('report_process.php DB error: ' . $e->getMessage());
    $_SESSION['flash_error'] = "Something went wrong while saving your report. Please try again.";
    header('Location: report.php');
    exit;
}