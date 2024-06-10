<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $price_per_liter = $_POST['price_per_liter'];
    $total_cost = $_POST['total_cost'];
    $kilometers = $_POST['kilometers'];

    $price_per_liter = floatval($price_per_liter);
    $total_cost = floatval($total_cost);
    $kilometers = intval($kilometers);

    $query = "INSERT INTO gas_records (user_id, price_per_liter, total_cost, kilometers)
              VALUES ($user_id, $price_per_liter, $total_cost, $kilometers)";
    $conn->query($query);
}

$query = "SELECT * FROM gas_records WHERE user_id = $user_id";
$result = $conn->query($query);

$total_liters = 0;
$total_kilometers = 0;
$data_points = [];

foreach ($records as $record) {
    $liters = $record['total_cost'] / $record['price_per_liter'];
    $total_liters += $liters;
    $total_kilometers += $record['kilometers'];
    $data_points[] = [
        'price_per_liter' => $record['price_per_liter'],
        'total_cost' => $record['total_cost'],
        'kilometers' => $record['kilometers'],
        'liters' => $liters
    ];
}

$average_consumption = $total_kilometers > 0 ? ($total_liters / $total_kilometers) * 100 : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Dashboard</h1>
    <form method="post" action="">
        <label for="price_per_liter">Price per Liter:</label>
        <input type="text" name="price_per_liter" required><br>
        <label for="total_cost">Total Cost:</label>
        <input type="text" name="total_cost" required><br>
        <label for="kilometers">Kilometers:</label>
        <input type="text" name="kilometers" required><br>
        <button type="submit">Add Record</button>
    </form>
    <h2>Your Records</h2>
    <table>
        <tr>
            <th>Price per Liter</th>
            <th>Total Cost</th>
            <th>Kilometers</th>
            <th>Liters</th>
        </tr>
        <?php foreach ($records as $record): ?>
        <tr>
            <td><?php echo htmlspecialchars($record['price_per_liter']); ?></td>
            <td><?php echo htmlspecialchars($record['total_cost']); ?></td>
            <td><?php echo htmlspecialchars($record['kilometers']); ?></td>
            <td><?php echo htmlspecialchars($record['total_cost'] / $record['price_per_liter']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <h2>Average Consumption: <?php echo htmlspecialchars($average_consumption); ?> liters/100km</h2>
    <h2>Consumption Chart</h2>
    <canvas id="consumptionChart" width="400" height="200"></canvas>
    <a href="logout.php">Logout</a>

    <script>
        var ctx = document.getElementById('consumptionChart').getContext('2d');
        var dataPoints = <?php echo json_encode($data_points); ?>;
        var labels = dataPoints.map(function(point, index) {
            return 'Record ' + (index + 1);
        });
        var litersData = dataPoints.map(function(point) {
            return point.liters;
        });
        var kilometersData = dataPoints.map(function(point) {
            return point.kilometers;
        });

        var consumptionChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Liters',
                    data: litersData,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }, {
                    label: 'Kilometers',
                    data: kilometersData,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>