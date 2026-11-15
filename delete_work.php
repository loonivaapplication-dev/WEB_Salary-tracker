<?php
require 'db.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $work_id = (int) $_POST['work_id'];

    // user_id check means students can only delete their own records
    $stmt = $pdo->prepare("DELETE FROM Work_Records WHERE work_id = ? AND user_id = ?");
    $stmt->execute([$work_id, $_SESSION['user_id']]);

    $msg = $stmt->rowCount() ? "Record deleted." : "Record not found.";
    header("Location: add_work.php?msg=" . urlencode($msg));
    exit;
}

header("Location: add_work.php");