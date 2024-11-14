<?php
session_start();
include 'database.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

// Get the current user's ID
$user_name = $_SESSION['user'];
$query = "SELECT id FROM users WHERE name='$user_name'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$user_id = $row['id'];

// Retrieve items donated by the logged-in user, including donation_date and image_path
$query = "SELECT item_name, donation_date, is_collected, collection_date, image_path FROM donated_items WHERE user_id='$user_id'";
$items = mysqli_query($conn, $query);

// Initialize counters for pie chart
$collected_count = 0;
$pending_count = 0;

// Count items for the pie chart
while ($item = mysqli_fetch_assoc($items)) {
    if ($item['is_collected']) {
        $collected_count++;
    } else {
        $pending_count++;
    }
}

// Reset the item result for table display
mysqli_data_seek($items, 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Donated Items</title>
    <link rel="stylesheet" href="items.css"> <!-- Link to the new CSS file -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
</head>
<body>
    <header>
        <h1>My Donated Items</h1>
        <div class="header-buttons">
            <button onclick="window.location.href='index.php';" class="nav-button">Home</button>
            <button onclick="window.location.href='dashboard.php';" class="nav-button">Dashboard</button>
            <button onclick="window.location.href='logout.php';" class="nav-button">Logout</button>
        </div>
    </header>

    <section>
        <?php if (mysqli_num_rows($items) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Image</th> <!-- New column for item images -->
                        <th>Name</th>
                        <th>Donated Date</th>
                        <th>Status</th>
                        <th>Collected Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = mysqli_fetch_assoc($items)): ?>
                        <tr>
                            <td>
                                <?php if (!empty($item['image_path'])): ?>
                                    <img src="http://localhost/CSP/<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>" class="item-image">
                                <?php else: ?>
                                    <span>No Image</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['donation_date']); ?></td>
                            <td><?php echo $item['is_collected'] ? 'Collected' : 'Pending'; ?></td>
                            <td>
                                <?php 
                                    echo $item['is_collected'] && $item['collection_date'] ? 
                                         htmlspecialchars($item['collection_date']) : 
                                         'N/A'; 
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You haven't donated any items yet.</p>
        <?php endif; ?>
        
        <h2>Status of Donated Items</h2>
        <div class="chart-container">
            <canvas id="statusChart"></canvas> <!-- Reduced size for the pie chart -->
        </div>
        <script>
            const ctx = document.getElementById('statusChart').getContext('2d');
            const statusChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Collected', 'Pending'],
                    datasets: [{
                        data: [<?php echo $collected_count; ?>, <?php echo $pending_count; ?>],
                        backgroundColor: ['#4caf50', '#ff9800'], // Colors for segments
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Collection Status'
                        }
                    }
                }
            });
        </script>
    </section>
</body>
</html>
