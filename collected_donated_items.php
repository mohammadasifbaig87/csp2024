<?php
session_start();
include 'database.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

// Get the logged-in user's ID
$user_name = $_SESSION['user'];
$query = "SELECT id FROM users WHERE name='$user_name'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$user_id = $row['id']; // Get the ID of the logged-in user

// Initialize an array to store collected items
$collected_items = [];

// Fetch collected items for the logged-in user
$query = "SELECT di.*, u.name AS donor_name 
          FROM donated_items di 
          JOIN users u ON di.user_id = u.id 
          WHERE di.is_collected = TRUE 
          AND di.collection_user_id = '$user_id'"; // Assuming there's a collection_user_id to link the collection to the user

$result = mysqli_query($conn, $query);

if ($result) {
    while ($item = mysqli_fetch_assoc($result)) {
        $collected_items[] = $item;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collected Items</title>
    <link rel="stylesheet" href="collected_items.css"> <!-- Link to the new CSS file -->
</head>
<body>
    <header>
        <h1>Collected Items</h1>
        <div class="header-buttons">
            <button onclick="window.location.href='index.php';" class="home-button">Home</button>
            <button onclick="window.location.href='logout.php';" class="logout-button">Logout</button>
            <button onclick="window.location.href='dashboard.php';" class="dashboard-button">Dashboard</button> <!-- Dashboard Button -->
        </div>
    </header>

    <section>
        <?php if (empty($collected_items)): ?>
            <p><center>No items collected from other users at this time.</center></p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Item Number</th>
                        <th>Item Name</th>
                        <th>Image</th> <!-- Added column for images -->
                        <th>Donor Name</th>
                        <th>Donation Date</th>
                        <th>Collection Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($collected_items as $index => $item): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td> <!-- Dynamic item number -->
                            <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                            <td>
                                <?php if (!empty($item['image_path'])): ?> <!-- Check if image path is available -->
                                    <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>" style="width: 100px; height: auto;"/> <!-- Display image with a fixed width -->
                                <?php else: ?>
                                    No Image Available
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['donor_name']); ?></td> <!-- Added donor name -->
                            <td><?php echo htmlspecialchars($item['donation_date']); ?></td>
                            <td><?php echo htmlspecialchars($item['collection_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</body>
</html>
