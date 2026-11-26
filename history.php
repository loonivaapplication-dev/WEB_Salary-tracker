
<?php
session_start();
require_once "db.php";

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM Salary_Records
        WHERE user_id = ?
        ORDER BY work_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$records = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Salary History</title>
</head>
<body>

<h1>Salary History</h1>

<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Work Date</th>
        <th>Hours Worked</th>
        <th>Hourly Wage</th>
        <th>Total Earnings</th>
    </tr>

    <?php foreach ($records as $record): ?>
    <tr>
        <td><?php echo htmlspecialchars($record['id']); ?></td>
        <td><?php echo htmlspecialchars($record['work_date']); ?></td>
        <td><?php echo htmlspecialchars($record['hours_worked']); ?></td>
        <td><?php echo number_format($record['hourly_wage'], 2); ?> KRW</td>
        <td><?php echo number_format($record['total_earnings'], 2); ?> KRW</td>
    </tr>
    <?php endforeach; ?>

</table>

<?php if (count($records) == 0): ?>
    <p>No salary records found.</p>
<?php endif; ?>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
