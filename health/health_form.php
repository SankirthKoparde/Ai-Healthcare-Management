<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] == "" || $_SESSION['usertype'] != 'p') {
    header("Location: ../login.php");
    exit();
}

// Fetch patient id if needed
include("../connection.php");
$useremail = $_SESSION['user'];
$sqlmain = "SELECT * FROM patient WHERE pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();
$userfetch = $userrow->fetch_assoc();
$pid = $userfetch["pid"];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Enter Health Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="mb-4">Enter Your Health Data</h3>
    <form action="save_health.php" method="POST">
        <div class="mb-3">
            <label>Blood Pressure (e.g. 120/80)</label>
            <input type="text" name="bp" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Sugar Level (mg/dL)</label>
            <input type="text" name="sugar_level" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Sleep Hours</label>
            <input type="number" step="0.1" name="sleep_hours" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>SpO₂ (%)</label>
            <input type="number" step="0.1" name="spo2" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Save Data</button>
        <a href="health_graph.php" class="btn btn-success">View Health Graph</a>
    </form>
</div>
</body>
</html>
