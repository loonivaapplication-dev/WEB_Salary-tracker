<?php 
require 'dp.php';
require_login();
$user_id = $_SESSION['user_id'];


$error = "";
$result = null;

//Load saved houry rate (if any) to pre-fill the form
$stmt = $pdo->prepare("Select hourly_rate FROM Wage_Settings Where user_id =
? ORDER BY wage_id DESC LIMIT 1");
$stmt-> execute([$user_id]);
$saved = $stmt-> fetch(PDO:: FETCH_ASSOC);
$rate_value = $saved ? $saved['hourly rate']: "";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $rate = (float) $_POST['hourly_rate'];
    $hours = (float) $_POST['hours'];
    $type = $_POST['type'];    //daily or weekly
    $days = (int) $_POST['days_per_week'];
    $rate_value = $_POST['hourly_rate'];

    if($rate <= 0 || $hours <= 0){
        $error = "Hourly wage and hours must be greater than 0.";
    } elseif ($type ==='daily' && ($days <1 || $days > 7)) {
        $error ="Days per week must b between 1 and 7.";
    } else {
        $weekly_hours = ($type ==='daily') ? $hours * $days : $hours;
        $weekly = $weekly_hours * $rate;
        $monthly = $weekly * 4.33;  //avg weeks per month
        $result = ['weekly_hours' => $weekly_hours, 'weekly'=> $weekly,
        'monthly'=> $monthly];

    }
}

?>