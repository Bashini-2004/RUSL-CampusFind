<?php
// register_process.php - Process User Registration
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$name     = trim($_POST['name'] ?? '');
$email    = strtolower(trim($_POST['email'] ?? ''));
$phone    = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($name) || empty($email) || empty($password)) {
    $_SESSION['flash_error'] = "All required fields must be completed.";
    header('Location: register.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash_error'] = "Please provide a valid email address.";
    header('Location: register.php');
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['flash_error'] = "Password must be at least 6 characters.";
    header('Location: register.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $_SESSION['flash_error'] = "An account with this email address already exists.";
        header('Location: register.php');
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $insertStmt = $pdo->prepare("INSERT INTO users (full_name, email, password, phone, role) VALUES (?, ?, ?, ?, 'student')");
    $insertStmt->execute([$name, $email, $hashedPassword, $phone]);
    $newUserId = $pdo->lastInsertId();

    $_SESSION['user_id']   = $newUserId;
    $_SESSION['full_name'] = $name;
    $_SESSION['email']     = $email;
    $_SESSION['role']      = 'student';

    $_SESSION['flash_success'] = "Welcome to CampusFind, {$name}! Your account has been created.";
    header('Location: myitems.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['flash_error'] = "System error during registration: " . $e->getMessage();
    header('Location: register.php');
    exit;
}
