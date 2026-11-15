<?php
require 'db.php';
require_login();
$user_id = $_SESSION['user_id'];

$error = "";
$result = null;

// Load saved hourly rate (if any) to pre-fill the form
$stmt = $pdo->prepare("SELECT hourly_rate FROM Wage_Settings WHERE user_id = ? ORDER BY wage_id DESC LIMIT 1");
$stmt->execute([$user_id]);
$saved = $stmt->fetch(PDO::FETCH_ASSOC);
$rate_value = $saved ? $saved['hourly_rate'] : "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rate  = (float) $_POST['hourly_rate'];
    $hours = (float) $_POST['hours'];
    $type  = $_POST['type'];                 // daily or weekly
    $days  = (int) $_POST['days_per_week'];
    $rate_value = $_POST['hourly_rate'];

    if ($rate <= 0 || $hours <= 0) {
        $error = "Hourly wage and hours must be greater than 0.";
    } elseif ($type === 'daily' && ($days < 1 || $days > 7)) {
        $error = "Days per week must be between 1 and 7.";
    } else {
        $weekly_hours = ($type === 'daily') ? $hours * $days : $hours;
        $weekly  = $weekly_hours * $rate;
        $monthly = $weekly * 4.33;           // average weeks per month
        $result = ['weekly_hours' => $weekly_hours, 'weekly' => $weekly, 'monthly' => $monthly];

        // Save the hourly rate if the student ticked the box
        if (isset($_POST['save_rate'])) {
            if ($saved) {
                $stmt = $pdo->prepare("UPDATE Wage_Settings SET hourly_rate = ? WHERE user_id = ?");
                $stmt->execute([$rate, $user_id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO Wage_Settings (user_id, hourly_rate) VALUES (?, ?)");
                $stmt->execute([$user_id, $rate]);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Salary Calculator</title><link rel="stylesheet" href="style.css"></head>
<body>
<h1>Salary Calculator</h1>
<p><a href="add_work.php">Working Hours</a></p>

<?php if ($error): ?><div class="error"><?= h($error) ?></div><?php endif; ?>

<form method="post">
    <label>Hourly Wage</label>
    <input type="number" name="hourly_rate" step="0.01" min="0.01" value="<?= h($rate_value) ?>" required>

    <label>Hours Worked</label>
    <input type="number" name="hours" step="0.25" min="0.25" required>

    <label>Hours entered are</label>
    <select name="type">
        <option value="daily">Per day</option>
        <option value="weekly">Per week</option>
    </select>

    <label>Days worked per week (only for "Per day")</label>
    <input type="number" name="days_per_week" min="1" max="7" value="5">

    <label><input type="checkbox" name="save_rate" style="width:auto"> Save this hourly wage</label>

    <button type="submit">Calculate</button>
</form>

<?php if ($result): ?>
<div class="result">
    <h2>Estimated Earnings</h2>
    <p>Weekly hours: <strong><?= number_format($result['weekly_hours'], 2) ?></strong></p>
    <p>Weekly earnings: <strong><?= number_format($result['weekly'], 2) ?></strong></p>
    <p>Monthly earnings (approx.): <strong><?= number_format($result['monthly'], 2) ?></strong></p>
</div>
<?php endif; ?>
</body>
</html>