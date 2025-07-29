<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] == "" || $_SESSION['usertype'] != 'p') {
    header("Location: ../login.php");
    exit();
}

// Use the correct connection file and variable
include("../connection.php");

// Get patient id from session user email
$useremail = $_SESSION['user'];
$sqlmain = "SELECT * FROM patient WHERE pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();
$userfetch = $userrow->fetch_assoc();
$pid = $userfetch["pid"];

// Now use $database for queries
$result = $database->query("SELECT * FROM health_data WHERE pid='$pid' ORDER BY recorded_at DESC LIMIT 10");

$dates = $bp = $sugar = $sleep = $spo2 = [];
while ($row = mysqli_fetch_assoc($result)) {
    $dates[] = $row['recorded_at'];
    $bp[] = intval(explode("/", $row['bp'])[0]);  // systolic for chart
    $sugar[] = floatval($row['sugar_level']);
    $sleep[] = floatval($row['sleep_hours']);
    $spo2[] = floatval($row['spo2']);
}

// Simple suggestion
$sleep_suggestion = (count($sleep) != 0 && array_sum($sleep)/count($sleep) < 6) ? 
    "Try to get at least 6–8 hours of sleep daily." : 
    "Great! You're getting good sleep.";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Health Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h3>Health Graphical Report</h3>
    <canvas id="healthChart"></canvas>
    <div class="alert alert-info mt-4">
        <strong>AI Suggestion:</strong> <?= $sleep_suggestion ?>
    </div>
    <a href="health_form.php" class="btn btn-secondary mt-3">Back</a>
</div>

<script>
    const ctx = document.getElementById('healthChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($dates) ?>,
            datasets: [
                {
                    label: 'Blood Pressure (systolic)',
                    data: <?= json_encode($bp) ?>,
                    borderColor: 'rgba(255,99,132,1)',
                    fill: false
                },
                {
                    label: 'Sugar Level',
                    data: <?= json_encode($sugar) ?>,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    fill: false
                },
                {
                    label: 'Sleep Hours',
                    data: <?= json_encode($sleep) ?>,
                    borderColor: 'rgba(255, 206, 86, 1)',
                    fill: false
                },
                {
                    label: 'SpO₂ (%)',
                    data: <?= json_encode($spo2) ?>,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    fill: false
                }
            ]
        }
    });
</script>
</body>
</html>
