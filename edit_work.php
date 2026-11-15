<?php
require 'db.php';
require_login();
$user_id = $_SESSION['user_id'];
$error = "";

// Works for both GET (?id=) and POST (hidden work_id)
$work_id = (int) ($_POST['work_id'] ?? $_GET['id'] ?? 0);

// Only load a record that belongs to the logged-in student
$stmt = $pdo->prepare("SELECT * FROM Work_Records WHERE work_id = ? AND user_id = ?");
$stmt->execute([$work_id, $user_id]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$record) {
    die("Record not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date  = $_POST['work_date'];
    $start = $_POST['start_time'];
    $end   = $_POST['end_time'];
    $break = (int) $_POST['break_minutes'];
    $hours = calc_hours($start, $end, $break);

    if ($hours === null) {
        $error = "End time must be after start time (after subtracting the break).";
    } else {
        $stmt = $pdo->prepare(
            "UPDATE Work_Records
             SET work_date = ?, start_time = ?, end_time = ?, break_minutes = ?, total_hours = ?
             WHERE work_id = ? AND user_id = ?"
        );
        $stmt->execute([$date, $start, $end, $break, $hours, $work_id, $user_id]);
        header("Location: add_work.php?msg=" . urlencode("Record updated."));
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit Work Record</title><link rel="stylesheet" href="style.css"></head>
<body>
<h1>Edit Work Record</h1>
<p><a href="add_work.php">Back</a></p>

<?php if ($error): ?><div class="error"><?= h($error) ?></div><?php endif; ?>

<form method="post">
    <input type="hidden" name="work_id" value="<?= (int)$record['work_id'] ?>">

    <label>Date</label>
    <input type="date" name="work_date" value="<?= h($record['work_date']) ?>" required>

    <label>Start Time</label>
    <input type="time" name="start_time" value="<?= h(substr($record['start_time'], 0, 5)) ?>" required>

    <label>End Time</label>
    <input type="time" name="end_time" value="<?= h(substr($record['end_time'], 0, 5)) ?>" required>

    <label>Unpaid Break (minutes)</label>
    <input type="number" name="break_minutes" min="0" value="<?= h($record['break_minutes']) ?>" required>

    <button type="submit">Update</button>
</form>
</body>
</html>