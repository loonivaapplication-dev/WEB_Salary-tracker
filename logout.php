<?php
require __DIR__ . '/account_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit;
}

verify_csrf();
end_session();
redirect('login.php');
