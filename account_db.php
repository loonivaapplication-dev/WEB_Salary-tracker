<?php
declare(strict_types=1);

session_start([
    'use_strict_mode' => 1,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'cookie_secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);

function db(): PDO
{
    static $pdo;
    return $pdo ??= new PDO(
        getenv('SALARY_DB_DSN') ?: 'mysql:host=localhost;dbname=salary_calculator;charset=utf8mb4',
        getenv('SALARY_DB_USER') ?: 'root',
        getenv('SALARY_DB_PASSWORD') ?: '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
}

function redirect(string $page): never
{
    header('Location: ' . $page, true, 303);
    exit;
}

function require_login(): int
{
    if (!isset($_SESSION['user_id'])) {
        redirect('login.php');
    }
    return (int) $_SESSION['user_id'];
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function field(string $key): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? $value : '';
}

function csrf_token(): string
{
    return $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
}

function verify_csrf(): void
{
    if (!hash_equals(csrf_token(), field('csrf_token'))) {
        http_response_code(403);
        exit('This form has expired. Reload the page and try again.');
    }
}

function end_session(): void
{
    $_SESSION = [];
    $cookie = session_get_cookie_params();
    unset($cookie['lifetime']);
    setcookie(session_name(), '', ['expires' => time() - 3600] + $cookie);
    session_destroy();
}

function account_error(string $name, string $email): string
{
    if ($name === '' || mb_strlen($name) > 100) {
        return 'Enter a name of 1 to 100 characters.';
    }
    if (strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Enter a valid email address.';
    }
    return '';
}
