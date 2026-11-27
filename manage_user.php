<?php
require_once 'db.php';

require_login();

// Check administrator permission
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admin only.");
}

// Get all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Manage Users</h1>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($users as $user): ?>

    <tr>
        <td><?php echo h($user['id']); ?></td>

        <td><?php echo h($user['name']); ?></td>

        <td><?php echo h($user['email']); ?></td>

        <td><?php echo h($user['role']); ?></td>

        <td>
            <a href="edit_user.php?id=<?php echo $user['id']; ?>">
                Edit
            </a>

            |

            <a href="delete_user.php?id=<?php echo $user['id']; ?>"
               onclick="return confirm('Are you sure you want to delete this user?');">
                Delete
            </a>
        </td>
    </tr>

    <?php endforeach; ?>

</table>

<br>

<a href="admin.php">Back to Admin Dashboard</a>

</body>
</html>