<?php
require_once 'db.php';

require_login();

// Check administrator permission
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admin only.");
}

// Get user ID
$id = $_GET['id'] ?? null;

if (!$id) {
    die("User ID is missing.");
}

// Get user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Update user information
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare(
        "UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?"
    );

    $stmt->execute([$name, $email, $role, $id]);

    header("Location: manage_users.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Edit User</h1>

<form method="POST">

    <label>Name:</label><br>

    <input type="text"
           name="name"
           value="<?php echo h($user['name']); ?>"
           required>

    <br><br>

    <label>Email:</label><br>

    <input type="email"
           name="email"
           value="<?php echo h($user['email']); ?>"
           required>

    <br><br>

    <label>Role:</label><br>

    <select name="role">

        <option value="user"
            <?php if ($user['role'] == 'user') echo 'selected'; ?>>
            User
        </option>

        <option value="admin"
            <?php if ($user['role'] == 'admin') echo 'selected'; ?>>
            Admin
        </option>

    </select>

    <br><br>

    <button type="submit">Update User</button>

</form>

<br>

<a href="manage_users.php">Back</a>

</body>
</html>