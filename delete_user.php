<?php
require_once 'db.php';

require_login();

// Check administrator permission
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admin only.");
}

// Get user ID from URL
$id = $_GET['id'] ?? null;

if (!$id) {
    die("User ID is missing.");
}

// Prevent admin from deleting their own account
if ($id == $_SESSION['user_id']) {
    die("You cannot delete your own account.");
}

// Check if user exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
$stmt->execute([$id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Delete user from database
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

// Return to manage users page
header("Location: manage_users.php");
exit;
?>