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
        <label for="liters">Liters:</label>
        <input type="text" name="liters" required><br>
        <label for="kilometers">Kilometers:</label>
        <input type="text" name="kilometers" required><br>
        <button type="submit">Add Record</button>
    </form>
    <h2>Your Records</h2>
    <table>
        <tr>
            <th>Liters</th>
            <th>Kilometers</th>
        </tr>
        <?php foreach ($records as $record): ?>
        <tr>
            <td><?php echo htmlspecialchars($record['liters']); ?></td>
            <td><?php echo htmlspecialchars($record['kilometers']); ?></td>
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
