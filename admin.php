<?php
require_once 'db.php';

require_login();

// Check administrator role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admin only.");
}

// Count total users
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$total_users = $stmt->fetchColumn();

// Count administrators
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
$total_admins = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Administrator Dashboard</h1>

<p>Total Users: <?php echo $total_users; ?></p>

<p>Total Administrators: <?php echo $total_admins; ?></p>

<a href="manage_users.php">Manage Users</a>

<br><br>

<a href="dashboard.php">Back to Dashboard</a>

<br><br>

<a href="logout.php">Logout</a>

</body>
</html>