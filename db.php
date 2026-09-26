<?php
// config/db.php - Database Connection & Session Setup
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_host = '127.0.0.1';
$db_name = 'campusfind_db';
$db_user = 'root';
$db_pass = ''; // Default XAMPP password is empty

try {
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("<div style='font-family:Segoe UI, sans-serif;padding:30px;background:#fff3cd;color:#856404;border:1px solid #ffeeba;border-radius:10px;max-width:700px;margin:50px auto;box-shadow:0 8px 24px rgba(0,0,0,0.08);'>
            <h2 style='margin-top:0;color:#d9534f;'>⚠️ Database Setup Required</h2>
            <p><strong>Could not connect to MySQL:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
            <hr style='border:0;border-top:1px solid #ffeeba;margin:20px 0;'>
            <h4 style='margin-bottom:10px;'>Quick Setup Guide:</h4>
            <ol style='line-height:1.8;padding-left:20px;'>
                <li>Open <strong>XAMPP Control Panel</strong> and click <strong>Start</strong> next to <strong>Apache</strong> and <strong>MySQL</strong>.</li>
                <li>Open your browser and navigate to <a href='http://localhost/phpmyadmin' target='_blank' style='color:#0d6efd;'>http://localhost/phpmyadmin</a>.</li>
                <li>Click the <strong>Import</strong> tab at the top.</li>
                <li>Click <strong>Choose File</strong>, select <code>schema.sql</code> inside this project folder, and click <strong>Go / Import</strong>.</li>
                <li>Refresh this page, and CampusFind will be ready to use!</li>
            </ol>
         </div>");
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function currentUser() {
    return [
        'id'    => $_SESSION['user_id'] ?? null,
        'name'  => $_SESSION['full_name'] ?? 'Guest',
        'email' => $_SESSION['email'] ?? '',
        'role'  => $_SESSION['role'] ?? 'guest',
    ];
}

/**
 * Helper to resolve item image path across uploads/, image/ or placeholder
 */
function getItemImageUrl($imagePath, $itemName = 'Item') {
    if (!empty($imagePath)) {
        $uploadsFile = __DIR__ . '/../uploads/' . $imagePath;
        if (file_exists($uploadsFile) && !is_dir($uploadsFile)) {
            return 'uploads/' . htmlspecialchars($imagePath);
        }

        $imageFile = __DIR__ . '/../image/' . $imagePath;
        if (file_exists($imageFile) && !is_dir($imageFile)) {
            return 'image/' . htmlspecialchars($imagePath);
        }
    }
    if (file_exists(__DIR__ . '/../image/default_item.png')) {
        return 'image/default_item.png';
    }
    if (file_exists(__DIR__ . '/../image/logo.png')) {
        return 'image/logo.png';
    }
    return 'https://placehold.co/400x300/e2e8f0/475569?text=' . urlencode($itemName);
}
