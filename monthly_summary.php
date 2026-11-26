
<?php
session_start();
require_once "db.php";

$user_id = $_SESSION['user_id'];

$sql = "SELECT 
        YEAR(work_date) AS year,
        MONTH(work_date) AS month,
        SUM(hours_worked) AS total_hours,
        SUM(total_earnings) AS total_earnings,
        COUNT(*) AS total_records
        FROM Salary_Records
        WHERE user_id = ?
        GROUP BY YEAR(work_date), MONTH(work_date)
        ORDER BY year DESC, month DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$months = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Monthly Earnings Summary</title>
</head>
<body>

<h1>Monthly Earnings Summary</h1>

<table border="1" cellpadding="8">
    <tr>
        <th>Month</th>
        <th>Total Hours</th>
        <th>Total Records</th>
        <th>Total Earnings</th>
    </tr>

    <?php foreach ($months as $month): ?>
    <tr>
        <td>
            <?php echo date("F", mktime(0, 0, 0, $month['month'], 1)); ?>
            <?php echo htmlspecialchars($month['year']); ?>
        </td>
        <td><?php echo number_format($month['total_hours'], 2); ?></td>
        <td><?php echo $month['total_records']; ?></td>
        <td><?php echo number_format($month['total_earnings'], 2); ?> KRW</td>
    </tr>
    <?php endforeach; ?>

</table>

<?php if (count($months) == 0): ?>
    <p>No monthly records found.</p>
<?php endif; ?>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
