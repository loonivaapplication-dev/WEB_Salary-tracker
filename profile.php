<?php
require __DIR__ . '/account_db.php';

$userId = require_login();
header('Cache-Control: no-store');
$stmt = db()->prepare('SELECT name, email, password_hash FROM Users WHERE user_id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    end_session();
    redirect('login.php');
}

$name = $user['name'];
$email = $user['email'];
$error = '';
$notice = $_SESSION['notice'] ?? '';
unset($_SESSION['notice']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = field('action');
    $password = field('current_password');

    if ($action === 'update') {
        $name = trim(field('name'));
        $email = strtolower(trim(field('email')));
        $error = account_error($name, $email);
        if ($error === '') {
            try {
                $stmt = db()->prepare('UPDATE Users SET name = ?, email = ? WHERE user_id = ?');
                $stmt->execute([$name, $email, $userId]);
                $_SESSION['notice'] = 'Profile updated.';
                redirect('profile.php');
            } catch (PDOException $exception) {
                if ($exception->getCode() !== '23000') {
                    throw $exception;
                }
                $error = 'An account already uses this email address.';
            }
        }
    } elseif ($action === 'delete') {
        if (field('confirm_delete') !== 'yes' || strlen($password) > 72 || !password_verify($password, $user['password_hash'])) {
            $error = 'Confirm the deletion and enter your current password.';
        } else {
            $stmt = db()->prepare('DELETE FROM Users WHERE user_id = ?');
            $stmt->execute([$userId]);
            end_session();
            redirect('login.php');
        }
    } else {
        http_response_code(400);
        $error = 'Unknown profile action.';
    }
}

$title = 'Your profile';
require __DIR__ . '/header.php';
?>
<form method="post">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="action" value="update">
    <label for="name">Full name</label>
    <input id="name" name="name" value="<?= h($name) ?>" autocomplete="name" maxlength="100" required>
    <label for="email">Email address</label>
    <input id="email" type="email" name="email" value="<?= h($email) ?>" autocomplete="email" maxlength="254" required>
    <button type="submit">Save changes</button>
</form>
<form method="post" action="logout.php">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
    <button class="secondary" type="submit">Sign out</button>
</form>
<details <?= field('action') === 'delete' ? 'open' : '' ?>>
    <summary>Delete account</summary>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <input type="hidden" name="action" value="delete">
        <label for="current_password">Current password</label>
        <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
        <label class="checkbox"><input type="checkbox" name="confirm_delete" value="yes" required> I understand that deleting my account is permanent.</label>
        <button class="danger" type="submit">Permanently delete account</button>
    </form>
</details>
</main>
</body>
</html>
