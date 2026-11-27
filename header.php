<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($title) ?> | Salary Tracker</title>
    <link rel="stylesheet" href="account.css">
</head>
<body>
<main>
    <p class="brand">Salary Tracker</p>
    <h1><?= h($title) ?></h1>
    <?php if (!empty($error)): ?>
        <p class="error" role="alert"><?= h($error) ?></p>
    <?php endif; ?>
    <?php if (!empty($notice)): ?>
        <p class="notice" role="status"><?= h($notice) ?></p>
    <?php endif; ?>
