
<?php
session_start();
require_once "db.php";

$user_id = $_SESSION['user_id'];

$sql = "SELECT
        YEAR(work_date) AS year,
        WEEK(work_date, 1) AS week_number,
        MIN(work_date) AS start_date,
        MAX(work_date) AS end_date,
        SUM(hours_worked) AS total_hours,
        SUM(total_earnings) AS total_earnings,
        COUNT(*) AS total_records
        FROM Salary_Records
        WHERE user_id = ?
        GROUP BY YEAR(work_date), WEEK(work_date, 1)
        ORDER BY year DESC, week_number DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$weeks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Weekly Earnings Summary</title>
</head>
<body>

<h1>Weekly Earnings Summary</h1>

<table border="1" cellpadding="8">
    <tr>
        <th>Week</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Total Hours</th>
        <th>Total Records</th>
        <th>Total Earnings</th>
    </tr>

    <?php foreach ($weeks as $week): ?>
    <tr>
        <td>
            Week <?php echo htmlspecialchars($week['week_number']); ?>,
            <?php echo htmlspecialchars($week['year']); ?>
        </td>
        <td><?php echo htmlspecialchars($week['start_date']); ?></td>
        <td><?php echo htmlspecialchars($week['end_date']); ?></td>
        <td><?php echo number_format($week['total_hours'], 2); ?></td>
        <td><?php echo $week['total_records']; ?></td>
        <td><?php echo number_format($week['total_earnings'], 2); ?> KRW</td>
    </tr>
    <?php endforeach; ?>

</table>

<?php if (count($weeks) == 0): ?>
    <p>No weekly records found.</p>
<?php endif; ?>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
