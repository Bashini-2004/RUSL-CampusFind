<?php
// contact_process.php - Process Contact Inquiries
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit;
}

$name    = trim($_POST['name'] ?? '');
$email   = strtolower(trim($_POST['email'] ?? ''));
$message = trim($_POST['message'] ?? '');

if (empty($name) || empty($email) || empty($message)) {
    $_SESSION['flash_error'] = "Please fill in all required fields.";
    header('Location: contact.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash_error'] = "Please provide a valid email address.";
    header('Location: contact.php');
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $message]);

    $_SESSION['flash_success'] = "Thank you! Your message has been received by the CampusFind team.";
    header('Location: contact.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['flash_error'] = "Could not save your message: " . $e->getMessage();
    header('Location: contact.php');
    exit;
}
