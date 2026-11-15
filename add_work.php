<?php
require 'db.php';
require_login();
$user_id = $_SESSION['user_id'];

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date  = $_POST['work_date'];
    $start = $_POST['start_time'];
    $end   = $_POST['end_time'];
    $break = (int) $_POST['break_minutes'];

    $hours = calc_hours($start, $end, $break);

    if (!$date || !$start || !$end) {
        $error = "Please fill in all fields.";
    } elseif ($hours === null) {
        $error = "End time must be after start time (after subtracting the break).";
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO Work_Records (user_id, work_date, start_time, end_time, break_minutes, total_hours)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$user_id, $date, $start, $end, $break, $hours]);
        $success = "Work record saved ($hours hours).";
    }
}

// Read: this student's records only
$stmt = $pdo->prepare("SELECT * FROM Work_Records WHERE user_id = ? ORDER BY work_date DESC");
$stmt->execute([$user_id]);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head><title>Working Hours</title><link rel="stylesheet" href="style.css"></head>
<body>
<h1>Working Hours</h1>
<p><a href="calculator.php">Salary Calculator</a></p>

<?php if ($error): ?><div class="error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="success"><?= h($success) ?></div><?php endif; ?>
<?php if (isset($_GET['msg'])): ?><div class="success"><?= h($_GET['msg']) ?></div><?php endif; ?>

<form method="post">
    <label>Date</label>
    <input type="date" name="work_date" required>

    <label>Start Time</label>
    <input type="time" name="start_time" required>

    <label>End Time</label>
    <input type="time" name="end_time" required>

    <label>Unpaid Break (minutes)</label>
    <input type="number" name="break_minutes" min="0" value="0" required>

    <button type="submit">Save Work Hours</button>
</form>

<h2>My Records</h2>
<table>
    <tr><th>Date</th><th>Start</th><th>End</th><th>Break</th><th>Hours</th><th>Actions</th></tr>
    <?php foreach ($records as $r): ?>
    <tr>
        <td><?= h($r['work_date']) ?></td>
        <td><?= h(substr($r['start_time'], 0, 5)) ?></td>
        <td><?= h(substr($r['end_time'], 0, 5)) ?></td>
        <td><?= h($r['break_minutes']) ?> min</td>
        <td><?= h($r['total_hours']) ?></td>
        <td>
            <a class="btn" href="edit_work.php?id=<?= (int)$r['work_id'] ?>">Edit</a>
            <form method="post" action="delete_work.php" onsubmit="return confirm('Delete this record?');">
                <input type="hidden" name="work_id" value="<?= (int)$r['work_id'] ?>">
                <button class="btn-red" type="submit">Delete</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$records): ?><tr><td colspan="6">No records yet.</td></tr><?php endif; ?>
</table>
</body>
</html>