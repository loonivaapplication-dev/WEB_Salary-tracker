<?php
// Salary Tracker Project
// Member 4: Shova Bohara
// Administrator Management

session_start();

// Database connection
$pdo = new PDO(
    "mysql:host=localhost;dbname=salary_calculator;charset=utf8mb4",
    "root",
    "",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Check if user is logged in
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

// Check if user is administrator
function require_admin() {
    global $pdo;

    require_login();

    $stmt = $pdo->prepare(
        "SELECT role, is_active FROM Users WHERE user_id = ?"
    );

    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || $user['role'] !== 'admin' || $user['is_active'] != 1) {
        die("Access denied. Administrator only.");
    }
}

// Safe HTML output
function h($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
?>