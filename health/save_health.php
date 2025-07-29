<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] == "" || $_SESSION['usertype'] != 'p') {
    header("Location: ../login.php");
    exit();
}

include("../connection.php");

// Get patient id from session user email
$useremail = $_SESSION['user'];
$sqlmain = "SELECT * FROM patient WHERE pemail=?";
$stmt = $database->prepare($sqlmain);
if (!$stmt) {
    die("Prepare failed: (" . $database->errno . ") " . $database->error);
}
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();
$userfetch = $userrow->fetch_assoc();
$pid = $userfetch["pid"];

// Get form data
$bp = $_POST['bp'];
$sugar_level = $_POST['sugar_level'];
$sleep_hours = $_POST['sleep_hours'];
$spo2 = $_POST['spo2'];

// Insert into health_data table
$sql = "INSERT INTO health_data (pid, bp, sugar_level, sleep_hours, spo2, recorded_at) VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = $database->prepare($sql);
if (!$stmt) {
    die("Prepare failed: (" . $database->errno . ") " . $database->error);
}
$stmt->bind_param("issdd", $pid, $bp, $sugar_level, $sleep_hours, $spo2);
$stmt->execute();

header("Location: health_form.php?success=1");
exit();
?>
