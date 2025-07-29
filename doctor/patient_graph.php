<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user"] == "" || $_SESSION['usertype'] != 'd') {
    header("location: ../login.php");
    exit();
}
include("../connection.php");

// Get all patients for dropdown
$patients = $database->query("SELECT pid, pname FROM patient");

// Get selected pid
$selected_pid = isset($_GET['pid']) ? intval($_GET['pid']) : null;
$health_data = [];
if ($selected_pid) {
    $result = $database->query("SELECT * FROM health_data WHERE pid = $selected_pid ORDER BY recorded_at ASC");
    while ($row = $result->fetch_assoc()) {
        $health_data[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Health Graph</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3>Patient Health Graph & Details</h3>
    <form method="get" class="mb-4">
        <label for="pid" class="form-label">Select Patient:</label>
        <select name="pid" id="pid" class="form-select" style="max-width:300px;display:inline-block;" onchange="this.form.submit()">
            <option value="">-- Select Patient --</option>
            <?php while ($row = $patients->fetch_assoc()): ?>
                <option value="<?php echo $row['pid']; ?>" <?php if ($selected_pid == $row['pid']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($row['pname']) . " (PID: " . $row['pid'] . ")"; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <noscript><button type="submit" class="btn btn-primary btn-sm">View</button></noscript>
    </form>
    <?php if ($selected_pid && count($health_data) > 0): ?>
        <div class="mb-4">
            <h5>Health Data Table</h5>
            <div style="overflow-x:auto;">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>BP</th>
                        <th>Sugar Level</th>
                        <th>Sleep Hours</th>
                        <th>SpO₂</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($health_data as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['recorded_at']); ?></td>
                            <td><?php echo htmlspecialchars($row['bp']); ?></td>
                            <td><?php echo htmlspecialchars($row['sugar_level']); ?></td>
                            <td><?php echo htmlspecialchars($row['sleep_hours']); ?></td>
                            <td><?php echo htmlspecialchars($row['spo2']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>
        <div>
            <h5>Health Trends</h5>
            <canvas id="healthChart" height="100"></canvas>
        </div>
        <script>
        const labels = <?php echo json_encode(array_column($health_data, 'recorded_at')); ?>;
        const bp = <?php echo json_encode(array_map('floatval', array_column($health_data, 'bp'))); ?>;
        const sugar = <?php echo json_encode(array_map('floatval', array_column($health_data, 'sugar_level'))); ?>;
        const sleep = <?php echo json_encode(array_map('floatval', array_column($health_data, 'sleep_hours'))); ?>;
        const spo2 = <?php echo json_encode(array_map('floatval', array_column($health_data, 'spo2'))); ?>;
        new Chart(document.getElementById('healthChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    { label: 'BP', data: bp, borderColor: '#0078d7', fill: false },
                    { label: 'Sugar', data: sugar, borderColor: '#d7263d', fill: false },
                    { label: 'Sleep', data: sleep, borderColor: '#2ecc40', fill: false },
                    { label: 'SpO₂', data: spo2, borderColor: '#ff9800', fill: false }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: { x: { title: { display: true, text: 'Date' } } }
            }
        });
        </script>
    <?php elseif ($selected_pid): ?>
        <div class="alert alert-warning">No health data found for this patient.</div>
    <?php endif; ?>
</div>
</body>
</html>
