<?php
session_start();
include 'database.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

// Retrieve the logged-in user's ID
$user_name = $_SESSION['user'];
$query = "SELECT id FROM users WHERE name='$user_name'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching user ID: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);
$user_id = $row['id']; // Get the ID of the logged-in user

// Initialize an array to store items
$items = [];

// Fetch donated items excluding those donated by the logged-in user and collected items
$query = "SELECT * FROM donated_items WHERE user_id != '$user_id' AND is_collected = FALSE";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching donated items: " . mysqli_error($conn));
}

while ($item = mysqli_fetch_assoc($result)) {
    $items[] = $item;
}

// Handle item collection
if (isset($_GET['collect'])) {
    $item_id = intval($_GET['collect']);
    
    // Check if the item ID is valid and exists in the database
    $check_item_query = "SELECT * FROM donated_items WHERE id='$item_id' AND user_id != '$user_id'";
    $check_result = mysqli_query($conn, $check_item_query);
    
    if (mysqli_num_rows($check_result) === 0) {
        echo "<p>Error: Invalid item ID or item already collected.</p>";
        exit();
    }
    
    // Update the item to mark it as collected
    $update_query = "UPDATE donated_items SET is_collected = TRUE, collection_date = CURDATE(), collection_user_id = '$user_id' WHERE id='$item_id' AND user_id != '$user_id'";
    
    if (mysqli_query($conn, $update_query)) {
        // Redirect after successful collection
        header("Location: view_donated_items.php"); // Redirect to refresh the items
        exit();
    } else {
        echo "<p>Error collecting item: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donated Items</title>
    <link rel="stylesheet" href="view.css">
</head>
<body>
    <header>
        <h1><center>Available Donated Items</center></h1>
        <div class="header-buttons">
            <button onclick="window.location.href='index.php';" class="home-button">Home Page</button>
            <button onclick="window.location.href='logout.php';" class="logout-button">Logout</button>
            <button onclick="window.location.href='dashboard.php';" class="dashboard-button">Dashboard</button> <!-- Dashboard Button -->
        </div>
    </header>

    <section>
        <?php if (empty($items)): ?>
            <p><center>No available donated items at this time.</center></p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Description</th>
                        <th>Door No</th>
                        <th>Area</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Pincode</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <?php if (!empty($item['image_path'])): ?>
                                    <img src="http://localhost/CSP/<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>" class="item-image">
                                <?php else: ?>
                                    <p>No Image Available</p>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($item['description']); ?></td>
                            <td><?php echo htmlspecialchars($item['door_no']); ?></td>
                            <td><?php echo htmlspecialchars($item['area']); ?></td>
                            <td><?php echo htmlspecialchars($item['city']); ?></td>
                            <td><?php echo htmlspecialchars($item['state']); ?></td>
                            <td><?php echo htmlspecialchars($item['pincode']); ?></td>
                            <td>
                                <a href="view_donated_items.php?collect=<?php echo $item['id']; ?>" onclick="return confirm('Are you sure you want to collect this item?');">Collect</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</body>
</html>
