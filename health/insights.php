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
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();
$userfetch = $userrow->fetch_assoc();
$pid = $userfetch["pid"];

// Get latest health data for this patient
$query = $database->query("SELECT * FROM health_data WHERE pid = '$pid' ORDER BY id DESC LIMIT 1");
$data = $query->fetch_assoc();

if (!$data) {
    echo "<h3>No health data found. Please submit your health form first.</h3>";
    exit();
}

$sugar = $data['sugar_level'];
$sleep = $data['sleep_hours'];
$bp = isset($data['bp_systolic']) ? $data['bp_systolic'] : 0;
$oxygen = isset($data['oxygen_level']) ? $data['oxygen_level'] : (isset($data['spo2']) ? $data['spo2'] : 0);

// Call Python script
$command = escapeshellcmd("python ../python/ai_health_model.py $sugar $sleep $bp $oxygen");
$output = shell_exec($command);

// Check if output is valid JSON and not empty
$result = null;
if ($output) {
    $result = json_decode($output, true);
}

echo "<h3>AI Risk Alert: " . (isset($result['risk']) ? htmlspecialchars($result['risk']) : "N/A") . "</h3>";
echo "<h4>Health Tips:</h4><ul>";
if (isset($result['tips']) && is_array($result['tips']) && count($result['tips']) > 0) {
    foreach ($result['tips'] as $tip) {
        echo "<li>" . htmlspecialchars($tip) . "</li>";
    }
} else {
    echo "<li>No tips available.</li>";
}
echo "</ul>";
?>
