<?php
// Shared setup: database connection, login check, helpers.
session_start();

$pdo = new PDO(
    "mysql:host=localhost;dbname=salary_calculator;charset=utf8mb4",
    "root",   // change to your MySQL username
    "",       // change to your MySQL password
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Member 1's login should set $_SESSION['user_id']
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

// Safely print text in HTML
function h($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Hours worked = (end - start) minus break. Returns null if invalid.
function calc_hours($start, $end, $break) {
    $minutes = (strtotime($end) - strtotime($start)) / 60 - $break;
    if ($minutes <= 0 || $break < 0) {
        return null;
    }
    return round($minutes / 60, 2);
}
// ===== Member 3: earnings summaries and Salary_Records =====

// Latest saved hourly rate (same rule as calculator.php). 0 if none saved.
function get_hourly_rate($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT hourly_rate FROM Wage_Settings WHERE user_id = ? ORDER BY wage_id DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $rate = $stmt->fetchColumn();
    return $rate ? (float) $rate : 0.0;
}

// Total hours / shifts / earnings between two dates (inclusive).
function summarize($pdo, $user_id, $start, $end) {
    $stmt = $pdo->prepare(
        "SELECT COALESCE(SUM(total_hours), 0) AS hours, COUNT(*) AS shifts
         FROM Work_Records
         WHERE user_id = ? AND work_date BETWEEN ? AND ?"
    );
    $stmt->execute([$user_id, $start, $end]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $hours = (float) $row['hours'];
    $rate  = get_hourly_rate($pdo, $user_id);
    return [
        'hours'  => $hours,
        'shifts' => (int) $row['shifts'],
        'rate'   => $rate,
        'salary' => round($hours * $rate, 2),
    ];
}

// Individual work records in the period (for the detail table).
function period_records($pdo, $user_id, $start, $end) {
    $stmt = $pdo->prepare(
        "SELECT * FROM Work_Records
         WHERE user_id = ? AND work_date BETWEEN ? AND ?
         ORDER BY work_date, start_time"
    );
    $stmt->execute([$user_id, $start, $end]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Save (or refresh) one Salary_Records row. Same user + type + start date = update.
function save_salary_record($pdo, $user_id, $type, $start, $end, $s) {
    $stmt = $pdo->prepare(
        "INSERT INTO Salary_Records
            (user_id, period_type, period_start, period_end, total_hours, hourly_rate, total_salary)
         VALUES (?, ?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
            period_end = VALUES(period_end), total_hours = VALUES(total_hours),
            hourly_rate = VALUES(hourly_rate), total_salary = VALUES(total_salary)"
    );
    $stmt->execute([$user_id, $type, $start, $end, $s['hours'], $s['rate'], $s['salary']]);
}

// Keep saved snapshots in sync: if a weekly/monthly record already exists for the
// period containing $date, recalculate it. Call after adding/editing/deleting a work record.
function refresh_saved_records($pdo, $user_id, $date) {
    $periods = [
        'weekly'  => [date('Y-m-d', strtotime('monday this week', strtotime($date))), null],
        'monthly' => [date('Y-m-01', strtotime($date)), null],
    ];
    $periods['weekly'][1]  = date('Y-m-d', strtotime($periods['weekly'][0] . ' +6 days'));
    $periods['monthly'][1] = date('Y-m-t', strtotime($date));

    foreach ($periods as $type => [$start, $end]) {
        $check = $pdo->prepare(
            "SELECT 1 FROM Salary_Records WHERE user_id = ? AND period_type = ? AND period_start = ?"
        );
        $check->execute([$user_id, $type, $start]);
        if ($check->fetchColumn()) {
            $s = summarize($pdo, $user_id, $start, $end);
            if ($s['rate'] > 0) {
                save_salary_record($pdo, $user_id, $type, $start, $end, $s);
            }
        }
    }
}
