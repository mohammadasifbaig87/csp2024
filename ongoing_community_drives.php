<?php
session_start();
include 'database.php'; // Include your database connection file

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
$logged_in_user_id = $row['id']; // Get the ID of the logged-in user

// Get the current date
$current_date = date('Y-m-d');

// Fetch ongoing community drives excluding the logged-in user's drives
$query = "SELECT user_name, phone, address, drive_type, drive_date 
          FROM community_drives 
          WHERE drive_date >= '$current_date' AND user_id != '$logged_in_user_id'"; // Ongoing drives of other users

$result = mysqli_query($conn, $query);

// Check for errors in the query
if (!$result) {
    echo "Error fetching ongoing community drives: " . mysqli_error($conn);
    exit();
}

// Initialize an array to store ongoing drives
$ongoing_drives = [];

// Fetch drives and store them in the array
while ($drive = mysqli_fetch_assoc($result)) {
    $ongoing_drives[] = $drive;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ongoing Community Drives</title>
    <link rel="stylesheet" href="drive.css"> <!-- Include your CSS file -->
</head>
<body>
    <header>
        <h1>Ongoing Community Drives</h1>
        <nav>
            <ul class="header-buttons">
                <li><a class="nav-button" href="index.php">Home</a></li>
                <li><a class="nav-button" href="dashboard.php">Dashboard</a></li>
                <li><a class="nav-button" href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section>
        <?php if (empty($ongoing_drives)): ?>
            <p>No ongoing community drives from other users at this time.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Type</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ongoing_drives as $drive): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($drive['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($drive['phone']); ?></td>
                            <td><?php echo htmlspecialchars($drive['address']); ?></td>
                            <td><?php echo htmlspecialchars($drive['drive_type']); ?></td>
                            <td><?php echo htmlspecialchars($drive['drive_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</body>
</html>
