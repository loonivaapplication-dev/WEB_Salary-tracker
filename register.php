<?php
require __DIR__ . '/account_db.php';

if (isset($_SESSION['user_id'])) {
    redirect('profile.php');
}

$name = $email = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim(field('name'));
    $email = strtolower(trim(field('email')));
    $password = field('password');
    $error = account_error($name, $email);

    if ($error === '' && (mb_strlen($password) < 8 || strlen($password) > 72 || str_contains($password, "\0"))) {
        $error = 'Use at least 8 characters and no more than 72 bytes for your password.';
    }
    if ($error === '' && $password !== field('confirm_password')) {
        $error = 'Passwords do not match.';
    }
    if ($error === '') {
        try {
            $stmt = db()->prepare('INSERT INTO Users (name, email, password_hash) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            $_SESSION['notice'] = 'Account created. You can now sign in.';
            redirect('login.php');
        } catch (PDOException $exception) {
            if ($exception->getCode() !== '23000') {
                throw $exception;
            }
            $error = 'An account already uses this email address.';
        }
    }
}

$title = 'Create a student account';
require __DIR__ . '/header.php';
?>
<form method="post">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
    <label for="name">Full name</label>
    <input id="name" name="name" value="<?= h($name) ?>" autocomplete="name" maxlength="100" required>
    <label for="email">Email address</label>
    <input id="email" type="email" name="email" value="<?= h($email) ?>" autocomplete="email" maxlength="254" required>
    <label for="password">Password</label>
    <input id="password" type="password" name="password" autocomplete="new-password" minlength="8" required>
    <label for="confirm_password">Confirm password</label>
    <input id="confirm_password" type="password" name="confirm_password" autocomplete="new-password" required>
    <button type="submit">Create account</button>
</form>
<p>Already registered? <a href="login.php">Sign in</a></p>
</main>
</body>
</html>
