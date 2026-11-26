
<?php
session_start();
require_once "db.php";

$user_id = $_SESSION['user_id'];

$sql = "SELECT 
        COUNT(*) AS total_records,
        SUM(total_earnings) AS total_earnings,
        SUM(hours_worked) AS total_hours
        FROM Salary_Records
        WHERE user_id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$summary = $stmt->fetch();

$sql = "SELECT * FROM Salary_Records
        WHERE user_id = ?
        ORDER BY work_date DESC
        LIMIT 5";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$records = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Earnings Dashboard</title>
</head>
<body>

<h1>Earnings Dashboard</h1>

<h2>Total Earnings</h2>
<p><?php echo number_format($summary['total_earnings'] ?? 0, 2); ?> KRW</p>

<h2>Total Hours Worked</h2>
<p><?php echo number_format($summary['total_hours'] ?? 0, 2); ?> hours</p>

<h2>Total Work Records</h2>
<p><?php echo $summary['total_records']; ?></p>

<h2>Recent Salary Records</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>Date</th>
        <th>Hours</th>
        <th>Hourly Wage</th>
        <th>Total Earnings</th>
    </tr>

    <?php foreach ($records as $record): ?>
    <tr>
        <td><?php echo htmlspecialchars($record['work_date']); ?></td>
        <td><?php echo htmlspecialchars($record['hours_worked']); ?></td>
        <td><?php echo number_format($record['hourly_wage'], 2); ?> KRW</td>
        <td><?php echo number_format($record['total_earnings'], 2); ?> KRW</td>
    </tr>
    <?php endforeach; ?>

</table>

<a href="history.php">View Full History</a>
<br>
<a href="monthly_summary.php">Monthly Summary</a>
<br>
<a href="weekly_summary.php">Weekly Summary</a>

</body>
</html>
