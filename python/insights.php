<?php
session_start();

// Fix: Use the correct session variable for patient login
if (isset($_SESSION['user']) && $_SESSION['user'] != "") {
    $username = $_SESSION['user'];
} elseif (isset($_SESSION['username']) && $_SESSION['username'] != "") {
    $username = $_SESSION['username'];
} else {
    echo "User not logged in.";
    exit();
}

$conn = new mysqli("localhost", "root", "", "sql_database_edoc");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Default values
$sugar = $sleep = $bp = $oxygen = "";

// If form submitted, use manual entry
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sugar = $_POST['sugar_level'];
    $sleep = $_POST['sleep_hours'];
    $bp = $_POST['bp_systolic'];
    $oxygen = $_POST['oxygen_level'];
} else {
    // Otherwise, fetch latest health data for the user
    $query = $conn->query("SELECT * FROM health_data WHERE username = '$username' ORDER BY id DESC LIMIT 1");
    if ($query && $data = $query->fetch_assoc()) {
        $sugar = $data['sugar_level'];
        $sleep = $data['sleep_hours'];
        $bp = $data['bp_systolic'];
        $oxygen = $data['oxygen_level'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AI Health Insights</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f8fb;
        }
        .insights-container {
            max-width: 480px;
            margin: 40px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 28px 24px 28px;
        }
        .insights-title {
            font-size: 2rem;
            font-weight: 700;
            color: #0078d7;
            margin-bottom: 18px;
            text-align: center;
        }
        .form-label {
            font-weight: 500;
        }
        .btn-primary {
            background: #0078d7;
            border: none;
        }
        .ai-result {
            margin-top: 30px;
            padding: 18px 20px;
            border-radius: 12px;
            background: #e7f3ff;
            box-shadow: 0 2px 8px rgba(0,120,215,0.08);
        }
        .ai-risk {
            font-size: 1.3rem;
            font-weight: 600;
            color: #d7263d;
        }
        .ai-tips {
            margin-top: 10px;
        }
        .ai-tips li {
            margin-bottom: 6px;
        }
    </style>
</head>
<body>
<div class="insights-container">
    <div class="insights-title">AI Health Insights</div>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Sugar Level (mg/dL):</label>
            <input type="number" step="0.1" name="sugar_level" class="form-control" required value="<?php echo htmlspecialchars($sugar); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Sleep Hours:</label>
            <input type="number" step="0.1" name="sleep_hours" class="form-control" required value="<?php echo htmlspecialchars($sleep); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">BP Systolic:</label>
            <input type="number" step="1" name="bp_systolic" class="form-control" required value="<?php echo htmlspecialchars($bp); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Oxygen Level (%):</label>
            <input type="number" step="0.1" name="oxygen_level" class="form-control" required value="<?php echo htmlspecialchars($oxygen); ?>">
        </div>
        <button type="submit" class="btn btn-primary w-100">Get Insights</button>
    </form>
    <?php
    // Only run AI if we have all values
    if ($sugar !== "" && $sleep !== "" && $bp !== "" && $oxygen !== "") {
        $command = escapeshellcmd("python ../python/ai_health_model.py $sugar $sleep $bp $oxygen");
        $output = shell_exec($command);

        $result = null;
        if ($output && trim($output) !== "") {
            $result = json_decode($output, true);
        }

        echo '<div class="ai-result">';
        if ($result && isset($result['risk'])) {
            echo "<div class='ai-risk'>AI Risk Alert: " . htmlspecialchars($result['risk']) . "</div>";
            echo "<div class='ai-tips'><strong>Health Tips:</strong><ul>";
            if (isset($result['tips']) && is_array($result['tips']) && count($result['tips']) > 0) {
                foreach ($result['tips'] as $tip) {
                    echo "<li>" . htmlspecialchars($tip) . "</li>";
                }
            } else {
                echo "<li>No tips available.</li>";
            }
            echo "</ul></div>";
        } else {
            echo "<div class='ai-risk'>AI Risk Alert: N/A</div>";
            echo "<div class='ai-tips'><strong>Health Tips:</strong><ul><li>No tips available.</li></ul></div>";
            // Show error for debugging
            if ($output === null || trim($output) === "") {
                echo "<div style='color:red;'>No output from Python script. Check if Python is installed and accessible, and the script runs without error.</div>";
            } else {
                echo "<div style='color:red;'>Output from Python script could not be decoded as JSON. Output:<br><pre>" . htmlspecialchars($output) . "</pre></div>";
            }
        }
        echo '</div>';
    }
    ?>
</div>
</body>
</html>
