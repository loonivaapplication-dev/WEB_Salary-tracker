<?php
require __DIR__ . '/account_db.php';

if (isset($_SESSION['user_id'])) {
    redirect('profile.php');
}

$email = $error = '';
$notice = $_SESSION['notice'] ?? '';
unset($_SESSION['notice']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = strtolower(trim(field('email')));
    $password = field('password');
    $stmt = db()->prepare('SELECT user_id, password_hash FROM Users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && strlen($password) <= 72 && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        unset($_SESSION['csrf_token']);
        $_SESSION['user_id'] = (int) $user['user_id'];
        redirect('profile.php');
    }
    $error = 'Invalid email or password.';
}

$title = 'Sign in';
require __DIR__ . '/header.php';
?>
<form method="post">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
    <label for="email">Email address</label>
    <input id="email" type="email" name="email" value="<?= h($email) ?>" autocomplete="username" maxlength="254" required>
    <label for="password">Password</label>
    <input id="password" type="password" name="password" autocomplete="current-password" required>
    <button type="submit">Sign in</button>
</form>
<p>New to Salary Tracker? <a href="register.php">Create an account</a></p>
</main>
</body>
</html>
