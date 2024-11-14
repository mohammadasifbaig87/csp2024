<?php
session_start();
include 'database.php'; // Include your database connection

// Initialize variables for the pie chart
$total_items = 0;
$collected_items = 0;

// Fetch data from the donated_items table to calculate totals
$query = "SELECT COUNT(*) as total, SUM(CASE WHEN is_collected THEN 1 ELSE 0 END) as collected FROM donated_items";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if ($data) {
    $total_items = $data['total'];
    $collected_items = $data['collected'];
}

$available_items = $total_items - $collected_items;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Website</title>
    <link rel="stylesheet" href="index.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header>
        <h1>Welcome to Useful Items Donation</h1>
        <nav>
            <ul>
                <?php if (isset($_SESSION['user'])): ?>
                    <li><button onclick="location.href='dashboard.php'">Dashboard</button></li>
                    <li><button onclick="location.href='about.html'">About Us</button></li>
                    <li><button onclick="location.href='logout.php'">Logout</button></li>
                <?php else: ?>
                    <li><button onclick="location.href='login.html'">Login</button></li>
                    <li><button onclick="location.href='register.html'">Register</button></li>
                    <li><button onclick="location.href='about.html'">About Us</button></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <section>
        <h2>Donate Your Unwanted Useful Items!</h2>
        <p>Helping those in need by donating items that are still useful but no longer needed.</p>
        
        <h3>Donation Item Status</h3>
        <div class="chart-container">
            <canvas id="statusChart"></canvas>
        </div>
    </section>

    <script>
        const ctx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Collected', 'Available'],
                datasets: [{
                    data: [<?php echo $collected_items; ?>, <?php echo $available_items; ?>],
                    backgroundColor: ['#4caf50', '#ff9800'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Total Donation Item Status'
                    }
                }
            }
        });
    </script>
</body>
</html>
