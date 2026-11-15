<?php
// Shared setup: database connection, login check, helpers.
session_start();

$pdo = new PDO(
    "mysql:host=localhost;dbname=salary_calculator;charset=utf8mb4",
    "root",   // change to your MySQL username
    "",       // change to your MySQL password
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Member 1's login should set $_SESSION['user_id']
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

// Safely print text in HTML
function h($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Hours worked = (end - start) minus break. Returns null if invalid.
function calc_hours($start, $end, $break) {
    $minutes = (strtotime($end) - strtotime($start)) / 60 - $break;
    if ($minutes <= 0 || $break < 0) {
        return null;
    }
    return round($minutes / 60, 2);
}